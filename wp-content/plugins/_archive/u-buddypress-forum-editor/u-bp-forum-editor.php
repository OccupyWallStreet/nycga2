<?php
/*
Plugin Name: U BuddyPress Forum Editor
Plugin URI: http://urlless.com/u-buddypress-forum-editor/
Description: This plugin is tinyMCE WYSIWYG HTML editor for BuddyPress Forum.
Author: Taehan Lee
Author URI: http://urlless.com
Version: 1.3
Network: true
*/

class UBPForumEditor {
	
var $id = 'ubpfeditor';
var $ver = '1.3';
var $url, $path;

function UBPForumEditor(){
	$this->url = plugin_dir_url(__FILE__);
	$this->path = plugin_dir_path(__FILE__);
	
	register_activation_hook( __FILE__, array(&$this, 'activation') );
	
	load_plugin_textdomain($this->id, false, dirname(plugin_basename(__FILE__)).'/languages/');
	
	add_action( 'admin_menu', array(&$this, 'admin_menu') );
	add_action( 'network_admin_menu', array(&$this, 'admin_menu') );
	add_action( 'admin_init', array(&$this, 'admin_init') );
	add_action( 'bb_init', array(&$this, 'bb_init') );
}

function bb_init(){
	if( ! $this->is_enable() )
		return false;
	
	$opts = $this->get_option();
	
	wp_enqueue_script('jquery');
	wp_enqueue_style( $this->id.'-editor', $this->url.'inc/editor.css', '', $this->ver);
	wp_enqueue_script($this->id.'-editor', $this->url.'inc/editor.js', '', $this->ver);
	if( !empty($opts->form_validate) ){
		wp_enqueue_script( $this->id.'-form-validate', $this->url.'inc/form-validate.js', array('jquery'), $this->ver);
		wp_localize_script( $this->id.'-form-validate', $this->id.'_form_validate_vars', array(
			'title_error' => __('Error: Please enter a title.', $this->id),
			'content_error' => __('Error: Please enter content.', $this->id),
			'group_id_error' => __('Error: Please select the Group Forum.', $this->id),
		));
	}
	
	if( !empty($opts->include_post_css) )
		wp_enqueue_style( $this->id.'-post', $this->url.'inc/post-content.css', '', $this->ver);
	
	remove_filter( 'bp_get_the_topic_post_content', 'force_balance_tags' );
	remove_filter( 'bp_get_the_topic_post_content', 'bp_forums_filter_kses', 1 );
	add_action( 'wp_footer', array(&$this, 'the_editor'));
	
	// paragraph margin remove
	add_action( 'groups_new_forum_topic', array(&$this, 'add_topic_pmr'), 1, 2);
	add_action( 'groups_edit_forum_topic', array(&$this, 'update_topic_pmr'), 1);
	add_action( 'groups_new_forum_topic_post', array(&$this, 'add_post_pmr'), 1, 2);
	add_action( 'groups_edit_forum_post', array(&$this, 'update_post_pmr'), 1);
	add_filter( 'bp_get_the_topic_post_content', array(&$this, 'add_pmr_sprite') );
	add_action( 'wp_head', array(&$this, 'p_margin_remove'));
}

function is_enable(){
	global $is_iphone;
	
	if( $is_iphone )
		return false;
		
	$opts = $this->get_option();
	
	if( empty($opts->enable) || (empty($opts->enable_topic) AND empty($opts->enable_reply)) ) 
		return false;
	
	return true;
}

function the_editor( ) {
	global $wp_version, $tinymce_version;
	
	$opts = $this->get_option();
	
	$baseurl = includes_url('js/tinymce');
	$mce_locale = ( '' == get_locale() ) ? 'en' : strtolower( substr(get_locale(), 0, 2) ); // only ISO 639-1
	$plugins = array( 'inlinepopups', 'tabfocus', 'paste', 'fullscreen', 'wordpress' );
	if( version_compare($wp_version, "3.2", ">=") )
		$plugins[] = 'media';
	$ext_plugins = $this->get_external_plugins(&$plugins, $mce_locale);
	
	$enable_textareas = array();
	if( !empty($opts->enable_topic) ) array_push($enable_textareas, 'textarea[name=topic_text]');
	if( !empty($opts->enable_reply) ) array_push($enable_textareas, 'textarea[name=reply_text]', 'textarea[name=post_text]');
	$enable_textareas = implode(',', $enable_textareas);
	
	$editor_style = !empty($opts->editor_style) ? $opts->editor_style : $this->url.'inc/editor-content.css';
	$editor_style .= '?ver='.$this->ver.','.$this->url.'inc/editor-content-p.css?ver='.$this->ver;
	
	$allowed_tags_array = array();
	$allowed_tags = $this->allowed_tags();
	foreach( $allowed_tags as $k=>$v)
		$allowed_tags_array[] = $k.'[*]';
	$allowed_tags = join(',', $allowed_tags_array);
	
	$width = absint($opts->width);
	if( $width > 100 ){
		$textarea_width = ($width-6).'px'; // 6 = padding + border-width
		$width = $width.'px';
	}else{
		$textarea_width = ($width-1).'%';
		$width = $width.'%';
	}
	$height = max(100, absint($opts->height)).'px';
	
	$initArray = array (
		'mode' => 'specific_textareas',
		'editor_selector' => 'theEditor',
		'width' => $width,
		'height' => $height,
		'theme' => 'advanced',
		'skin' => $opts->skin,
		'theme_advanced_buttons1' => $opts->buttons1,
		'theme_advanced_buttons2' => $opts->buttons2,
		'theme_advanced_buttons3' => $opts->buttons3,
		'theme_advanced_buttons4' => $opts->buttons4,
		'language' => $mce_locale,
		'content_css' => $editor_style,
		'valid_elements' => $allowed_tags,
		'invalid_elements' => 'script,style,link',
		'theme_advanced_toolbar_location' => 'top',
		'theme_advanced_toolbar_align' => 'left',
		'theme_advanced_statusbar_location' => 'bottom',
		'theme_advanced_resizing' => true,
		'theme_advanced_resize_horizontal' => false,
		'theme_advanced_resizing_use_cookie' => true,
		'theme_advanced_disable' => 'code',
		'dialog_type' => 'modal',
		'relative_urls' => false,
		'remove_script_host' => false,
		'convert_urls' => false,
		'apply_source_formatting' => false,
		'remove_linebreaks' => true,
		'gecko_spellcheck' => true,
		'keep_styles' => false,
		'entities' => '38,amp,60,lt,62,gt',
		'accessibility_focus' => true,
		'tabfocus_elements' => 'major-publishing-actions',
		'media_strict' => false,
		'paste_remove_styles' => true,
		'paste_remove_spans' => true,
		'paste_strip_class_attributes' => 'all',
		'paste_text_use_dialog' => true,
		'wpeditimage_disable_captions' => true,
		'plugins' => implode( ',', $plugins ),
	);
	
	$formats = array('p','code','div','blockquote');
	for($i=1; $i<=6; $i++) if( in_array('h'.$i, $opts->allowed_tags) ) $formats[] = 'h'.$i;
	$formats = apply_filters($this->id.'_formats', join(',', $formats));
	if( !empty($formats) )
		$initArray['theme_advanced_blockformats'] = $formats;
		
	if( $fontsizes = apply_filters($this->id.'_fontsizes', "80%,100%,120%,150%,200%,300%"))
		$initArray['theme_advanced_font_sizes'] = $fontsizes;
		
	if( $fonts = apply_filters($this->id.'_fonts', ''))
		$initArray['theme_advanced_fonts'] = $fonts;
		
	$version = apply_filters('tiny_mce_version', '');
	$version = 'ver=' . $tinymce_version . $version;
	
	$language = $initArray['language'];
	
	if ( 'en' != $language )
		include_once(ABSPATH . WPINC . '/js/tinymce/langs/wp-langs.php');
		
	$mce_options = '';
	foreach ( $initArray as $k => $v ) {
		if ( is_bool($v) ) {
			$val = $v ? 'true' : 'false'; $mce_options .= $k . ':' . $val . ', '; continue;
		} elseif ( !empty($v) && is_string($v) && ( '{' == $v{0} || '[' == $v{0} ) ) {
			$mce_options .= $k . ':' . $v . ', '; continue;
		}
		$mce_options .= $k . ':"' . $v . '", ';
	}
	$mce_options = rtrim( trim($mce_options), '\n\r,' );
	?>

<script type="text/javascript" src="<?php echo $baseurl?>/tiny_mce.js?<?php echo $version?>"></script>
<?php
if ( 'en' != $language && isset($lang) )
	echo "<script type='text/javascript'>\n$lang\n</script>\n";
else
	echo "<script type='text/javascript' src='$baseurl/langs/wp-langs-en.js?$version'></script>\n";
?>

<script type="text/javascript">
jQuery('<?php echo $enable_textareas?>').addClass('theEditor');

// toolbar
jQuery('textarea.theEditor').each(function(){
	jQuery('#ta').val(this.value);
	this.value = this.value.replace(/^(\r|\n)\s(\r|\n)$/gm, '<p>&nbsp;</p>');
	this.value = switchEditors.wpautop(this.value);
	var toolbar = '';
	toolbar += '<div class="<?php echo $this->id?>-toolbar">';
	toolbar += '<a id="edButtonPreview" class="active" onclick="switchEditors.go(\''+this.id+'\', \'tinymce\');"><?php _e('Visual', $this->id)?></a>';
	toolbar += '<a id="edButtonHTML" class="" onclick="switchEditors.go(\''+this.id+'\', \'html\');"><?php _e('HTML', $this->id)?></a>';
	toolbar += '</div>';
	jQuery(this).wrap('<span class="<?php echo $this->id?>-wrap <?php echo $opts->skin?>"></span>').before(toolbar);
	jQuery(this).css('width', '<?php echo $textarea_width?> !important');
});

// overwrite for tinymce.plugins.WordPress
function getUserSetting( name, def ){ 
	if( name=='hidetb' ){
		return '1';
	}else if( name=='editor' ){
		return 'tinymce';
	}else if( typeof getAllUserSettings=='function'){
		var o = getAllUserSettings();
	
		if ( o.hasOwnProperty(name) )
			return o[name];
	
		if ( typeof def != 'undefined' )
			return def;
	}
	return '';
}

tinyMCEPreInit = {
	base : "<?php echo $baseurl; ?>",
	suffix : "",
	query : "<?php echo $version; ?>",
	mceInit : {<?php echo $mce_options; ?>},
	load_ext : function(url,lang){var sl=tinymce.ScriptLoader;sl.markDone(url+'/langs/'+lang+'.js');sl.markDone(url+'/langs/'+lang+'_dlg.js');}
};

<?php if ( $ext_plugins ) echo "$ext_plugins\n"; ?>

(function(){var t=tinyMCEPreInit,sl=tinymce.ScriptLoader,ln=t.mceInit.language,th=t.mceInit.theme,pl=t.mceInit.plugins;sl.markDone(t.base+'/langs/'+ln+'.js');sl.markDone(t.base+'/themes/'+th+'/langs/'+ln+'.js');sl.markDone(t.base+'/themes/'+th+'/langs/'+ln+'_dlg.js');tinymce.each(pl.split(','),function(n){if(n&&n.charAt(0)!='-'){sl.markDone(t.base+'/plugins/'+n+'/langs/'+ln+'.js');sl.markDone(t.base+'/plugins/'+n+'/langs/'+ln+'_dlg.js');}});})();

tinyMCE.init(tinyMCEPreInit.mceInit);

<?php if( !empty($opts->form_validate) ) echo "bp_forum_form_validate.init();\n"?>
</script>
<?php
}


function get_external_plugins($plugins, $mce_locale){
	$opts = $this->get_option();
	
	$defaults = array('simpleimage', 'wpeditimage');
	$customs = preg_replace('/,\s*/',',',$opts->plugins);
	$ext_plugins = array();
	$ret = '';
	
	foreach($defaults as $plugin){
		$ext_plugins[$plugin] = array(
			'url' => $this->url.'inc/tiny_mce/plugins/'.$plugin.'/editor_plugin.js',
			'dir_path' => $this->path.'inc/tiny_mce/plugins/'.$plugin.'/',
		);
	}
	
	if( !empty($customs) AND $opts->plugin_dir){
		$customs = explode(',', $customs);
		foreach($customs as $plugin){
			$ext_plugins[$plugin] = array(
				'url' => WP_PLUGIN_URL.'/'.$opts->plugin_dir.'/plugins/'.$plugin.'/editor_plugin.js',
				'dir_path' => WP_PLUGIN_DIR.'/'.$opts->plugin_dir.'/plugins/'.$plugin.'/',
			);
		}
	}
	
	if( !empty($ext_plugins) ){	
		foreach ( $ext_plugins as $name => $v ) {
			
			if ( is_ssl() ) 
				$v['url'] = str_replace('http://', 'https://', $v['url']);
			
			$plugins[] = '-' . $name;
			
			$plugurl = dirname($v['url']);
			$path = $v['dir_path'] . 'langs/';
			$strings = $str1 = $str2 = '';

			if ( function_exists('realpath') )
				$path = trailingslashit( realpath($path) );

			if ( @is_file($path . $mce_locale . '.js') )
				$strings .= @file_get_contents($path . $mce_locale . '.js') . "\n";

			if ( @is_file($path . $mce_locale . '_dlg.js') )
				$strings .= @file_get_contents($path . $mce_locale . '_dlg.js') . "\n";

			if ( 'en' != $mce_locale && empty($strings) ) {
				if ( @is_file($path . 'en.js') ) {
					$str1 = @file_get_contents($path . 'en.js');
					$strings .= preg_replace( '/([\'"])en\./', '$1' . $mce_locale . '.', $str1, 1 ) . "\n";
				}

				if ( @is_file($path . 'en_dlg.js') ) {
					$str2 = @file_get_contents($path . 'en_dlg.js');
					$strings .= preg_replace( '/([\'"])en\./', '$1' . $mce_locale . '.', $str2, 1 ) . "\n";
				}
			}

			if ( ! empty($strings) )
				$ret .= "\n" . $strings . "\n";
		
			$ret .= 'tinyMCEPreInit.load_ext("' . $plugurl . '", "' . $mce_locale . '");' . "\n";
			$ret .= 'tinymce.PluginManager.load("' . $name . '", "' . $v['url'] . '");' . "\n";
		}
	}
	
	return $ret;
}


function allowed_tags(){
	global $default_allowedtags, $full_allowedtags;
	$opts = $this->get_option();
	$r = array();
	foreach($opts->allowed_tags as $tag)
		$r[$tag] = $full_allowedtags[$tag];
	return $r;
}


// paragraph margin remove
function add_topic_pmr($group_id, $topic) {
	bb_update_topicmeta( $topic->topic_id, $this->id.'_pmr', '1' );
}
function update_topic_pmr($topic_id){
	bb_update_topicmeta( $topic_id, $this->id.'_pmr', '1' );
}
function add_post_pmr($group_id, $post_id) {
	bb_update_postmeta( $post_id, $this->id.'_pmr', '1' );
}
function update_post_pmr($post_id){
	bb_update_postmeta( $post_id, $this->id.'_pmr', '1' );
}
function add_pmr_sprite($content){
	global $topic_template;
	if( $topic_template->current_post==0 ){
		$object_id = $topic_template->topic_id;
		$meta = bb_get_topicmeta( $object_id, $this->id.'_pmr', true);
	}else{
		$object_id = $topic_template->post->post_id;
		$meta = bb_get_postmeta( $object_id, $this->id.'_pmr', true);
	}
	if( !empty($meta) )
		$content .= '<em class="pmr-sprite">sprite</em>';
	return $content;
}
function p_margin_remove(){
	?>
<style>.pmr p { margin: 0; }</style>

<script>
jQuery(function(){
	jQuery('em.pmr-sprite').each(function(){
		var t = jQuery(this);
		var wrap = t.parents('.post-content:eq(0)');
		if( !wrap.length ) wrap = t.parents('.post:eq(0)');
		if( !wrap.length ) wrap = t.parents('.entry-content:eq(0)');
		if( !wrap.length ) wrap = t.parents('.entry:eq(0)');
		if( wrap.length ) wrap.addClass('pmr');
		t.remove();
	});
});
</script>
<?php
}

function get_default_buttons1(){
	return 'formatselect, |, forecolor, |, bold, italic, underline, strikethrough, |, justifyleft, justifycenter, justifyright, | ,removeformat';
}

function get_default_buttons2(){
	return 'undo, redo,|, pastetext, pasteword, |, bullist, numlist, |, outdent, indent, |, link, unlink, charmap, image, |, fullscreen';
}

function get_buttons_list(){
	return 'formatselect, fontselect, fontsizeselect, forecolor, backcolor, bold, italic, underline, strikethrough, justifyleft, justifycenter, justifyright, justifyfull, sub, sup, removeformat, undo, redo, pastetext, pasteword, bullist, numlist, outdent, indent, blockquote, link, unlink, hr, image, media, charmap, fullscreen';
}


function get_option(){
	include('inc/allowed-tags.php');
	
	$default = array (
		'enable' => '',
		'enable_topic' => '1',
		'enable_reply' => '1',
		'form_validate' => '1',
		'buttons1' => $this->get_default_buttons1(),
		'buttons2' => $this->get_default_buttons2(),
		'buttons3' => '',
		'buttons4' => '',
		'plugins' => '', 
		'plugin_dir' => '',
		'width' => 77,
		'height' => 300,
		'skin' => 'wp_theme',
		'editor_style' => '',
		'allowed_tags' => $default_allowedtags,
		'include_post_css' => '1',
	);
	
	$saved = get_option($this->id);
	$option = wp_parse_args($saved, $default);
	
	if( empty($options['allowed_tags']) ) 
		$options['allowed_tags'] = $default_allowedtags;
		
	if( $saved!=$option )
		update_option($this->id, $option);
	
	return (object) $option;
}


/* Back-end
--------------------------------------------------------------------------------------- */

function activation() {
	global $wp_version;
	if (version_compare($wp_version, "3.1", "<")) 
		wp_die("This plugin requires WordPress version 3.1 or higher.");
	
	register_uninstall_hook( __FILE__, 'ubpfeditor_uninstall' );
	
	$this->get_option();
}

function admin_init(){
	register_setting($this->id.'_options', $this->id, array( &$this, 'admin_page_vailidate'));
}

function admin_menu(){
	if( !is_super_admin() ) 
		return false;
	
	add_submenu_page( 
		'bp-general-settings', 
		'U '.__('BuddyPress Forum Editor', $this->id), 
		'U '.__('Forum Editor', $this->id),
		'manage_options', 
		$this->id, 
		array(&$this, 'admin_page') 
	);
}

function admin_page(){
	global $default_allowedtags, $full_allowedtags;
	$opts = $this->get_option();
	$skins = array('default', 'highcontrast', 'o2k7', 'wp_theme');
	?>
	
	<div class="wrap">
		<?php screen_icon("options-general"); ?>
		<h2>U <?php _e('BuddyPress Forum Editor', $this->id);?></h2>
		
		<?php settings_errors( $this->id ) ?>
		
		<form action="<?php echo admin_url('options.php')?>" method="post">
			
			<?php settings_fields($this->id.'_options'); ?>
			
			<table class="form-table">
			<tr>
				<th><strong><?php _e('Enable', $this->id)?></strong></th>
				<td>
					<label><input type="checkbox" name="<?php echo $this->id?>[enable]" id="enable_cb" value="1" <?php checked($opts->enable, '1')?>> 
					<strong><?php _e('Enable', $this->id)?></strong></label>
					
					<div id="enable_scope" style="padding-left:16px">
						<label><input type="checkbox" name="<?php echo $this->id?>[enable_topic]" value="1" <?php checked($opts->enable_topic, '1')?>> <?php _e('Enable Topic editor', $this->id)?></label><br>
						<label><input type="checkbox" name="<?php echo $this->id?>[enable_reply]" value="1" <?php checked($opts->enable_reply, '1')?>> <?php _e('Enable Reply editor', $this->id)?></label>
					</div>
					
					<script>
					jQuery('#enable_cb').filter(function(){
						jQuery(this).click(function(){ if( this.checked ) jQuery('#enable_scope').slideDown('fast'); else jQuery('#enable_scope').hide();});
						if( ! this.checked ) jQuery('#enable_scope').hide();
					});
					</script> 
					
					<p>&nbsp;</p>
				</td>
			</tr>
			<tr>
				<th><?php _e('Editor Size', $this->id)?> *</th>
				<td>
					<?php _e('Width', $this->id)?> :
					<input type="text" name="<?php echo $this->id?>[width]" value="<?php echo $opts->width;?>" size="3" id="editor-width"> <span>px</span> &nbsp;&nbsp;
					<span class="description">0~100 =&gt; %, 101~ =&gt; px</span>
					<br>
					<?php _e('Height', $this->id)?> :
					<input type="text" name="<?php echo $this->id?>[height]" value="<?php echo $opts->height;?>" size="3"> px
				</td>
			</tr>
			<tr>
				<th><?php _e('Editor Skin', $this->id)?></th>
				<td>
					<select name="<?php echo $this->id?>[skin]">
						<?php foreach($skins as $skin){ ?>
						<option value="<?php echo $skin?>" <?php selected($opts->skin, $skin)?>><?php echo $skin?></option>
						<?php } ?>
					</select>
				</td>
			</tr>
			<tr>
				<th><?php _e('Include Post content CSS', $this->id)?></th>
				<td>
					<label><input type="checkbox" name="<?php echo $this->id?>[include_post_css]" value="1" <?php checked($opts->include_post_css, '1')?>> <?php _e('Yes', $this->id)?></label>
					<p class="description"><?php _e('Most template CSS of bp theme is not sufficient to display a post content properly. for example, in bp-default theme, ol, ul, blockquote, etc are shown unexpectedly.', $this->id)?>
					<a href="http://urlless.com/blog/wp-content/uploads/i/ubpf-editor-css-compare.png" target="_blank"><?php _e('Refer to screenshot for details.', $this->id)?></a>
					</p>
					
				</td>
			</tr>
			<tr>
				<th><?php _e('Form Validate', $this->id)?></th>
				<td>
					<label><input type="checkbox" name="<?php echo $this->id?>[form_validate]" value="1" <?php checked($opts->form_validate, '1')?>> <?php _e('Enable', $this->id)?></label>
					<p class="description"><?php _e('Validating whether form fields are filled out before submit post.', $this->id)?></p>
					
				</td>
			</tr>
			<tr>
				<th><?php _e('Buttons group', $this->id)?> 1</th>
				<td>
					<input type="text" name="<?php echo $this->id?>[buttons1]" value="<?php echo $opts->buttons1;?>" class="widefat" >
					<p class="description"><?php _e('Separate buttons with commas. Pipe character( | ) is visual separator.', $this->id)?></p>
				</td>
			</tr>
			<tr>
				<th><?php _e('Buttons group', $this->id)?> 2</th>
				<td>
					<input type="text" name="<?php echo $this->id?>[buttons2]" value="<?php echo $opts->buttons2;?>" class="widefat">
					<p class="description"><?php _e('Separate buttons with commas. Pipe character( | ) is visual separator.', $this->id)?></p>
				</td>
			</tr>
			<tr>
				<th><?php _e('Buttons group', $this->id)?> 3</th>
				<td>
					<input type="text" name="<?php echo $this->id?>[buttons3]" value="<?php echo $opts->buttons3;?>" class="widefat">
					<p class="description"><?php _e('Separate buttons with commas. Pipe character( | ) is visual separator.', $this->id)?></p>
				</td>
			</tr>
			<tr>
				<th><?php _e('Buttons group', $this->id)?> 4</th>
				<td>
					<input type="text" name="<?php echo $this->id?>[buttons4]" value="<?php echo $opts->buttons4;?>" class="widefat">
					<p class="description"><?php _e('Separate buttons with commas. Pipe character( | ) is visual separator.', $this->id)?></p>
				</td>
			</tr>
			<tr>
				<th></th>
				<td>
					<p><strong><?php _e('Available buttons', $this->id)?> :</strong></p>
					<p><code><?php echo $this->get_buttons_list();?></code></p>
					<br>
					
					<p><strong><?php _e('Extend TinyMCE Plugin', $this->id)?></strong> 
						<span style="color:red">(<?php _e('This is not required.', $this->id)?>)</span></p>
					
					<p><?php _e('Plugin directory', $this->id)?>:
						<span class="description"><?php echo WP_PLUGIN_URL?>/</span>
						<input type="text" name="<?php echo $this->id?>[plugin_dir]" value="<?php echo $opts->plugin_dir;?>" ></p>
					
					<p><?php _e('Plugin names', $this->id)?>:
						<input type="text" name="<?php echo $this->id?>[plugins]" value="<?php echo $opts->plugins;?>" >
						<span class="description"><?php _e('Separate plugin name with commas.', $this->id)?></span></p>
					
					<p><a href="http://urlless.com/extending-tinymce-plugin-for-u-buddypress-forum-editor/" target="_blank"><?php _e('How to extend TinyMCE plugin', $this->id)?></a></p>
				</td>
			</tr>
			<tr>
				<th><?php _e('Allowed Tags', $this->id)?></th>
				<td>
					<div class="allowed-tags default-tags">
					<strong><?php _e('Default allowed tags', $this->id)?>:</strong>
					<span><?php foreach($full_allowedtags as $k=>$v){ if(in_array($k, $default_allowedtags)){ ?>
					<label><input type="checkbox" name="<?php echo $this->id?>[allowed_tags][]" value="<?php echo $k?>" <?php checked(in_array($k, $opts->allowed_tags))?>><?php echo $k?></label>
					<?php }} ?></span>
					<br class="clear">
					</div>
					
					<div class="allowed-tags additional-tags">
					<strong><?php _e('Additional tags', $this->id)?>: &nbsp;
						<label><input type="checkbox" id="allow-all-additional-tags"><?php _e('Check all', $this->id)?></label></strong>
					<span><?php foreach($full_allowedtags as $k=>$v){ if(!in_array($k, $default_allowedtags)){ ?>
					<label><input type="checkbox" name="<?php echo $this->id?>[allowed_tags][]" value="<?php echo $k?>" <?php checked(in_array($k, $opts->allowed_tags))?>><?php echo $k?></label> 
					<?php }} ?></span>
					<br class="clear">
					</div>
					
					<p class="description"><?php _e('For instance, if you would embed Youtube, select <code>iframe</code>. and if you would use old embed code(Flash), select <code>object, embed and param</code>.', $this->id)?></p>
					<p class="description"><?php _e('Some tags are never allowed. script, style, link.', $this->id)?></p>
				</td>
			</tr>
			<tr>
				<th><?php _e('Editor Content CSS URL', $this->id)?></th>
				<td>
					<input type="text" name="<?php echo $this->id?>[editor_style]" value="<?php echo $editor_style;?>" class="widefat">
					<p class="description"><?php _e('If you\'d like to customize the Editor\'s content style, enter your own stylesheet file URL.', $this->id)?></p>
					<p class="description"><?php printf(__('If you leave a blank, the %s CSS will be used.', $this->id), '<a href="'.$this->url.'inc/editor-content.css">'.__('defaults', $this->id).'</a>')?></p>
				</td>
			</tr>
			</table>
			
			<p class="submit">
				<input name="submit" type="submit" class="button-primary" value="<?php esc_attr_e(__('Save Changes'))?>" />
			</p>
		</form>
		
		<style>
		.allowed-tags { margin-bottom: 15px;}
		.allowed-tags strong { display: block; margin-bottom: 5px;}
		.allowed-tags strong label { font-weight: normal; }
		.allowed-tags span label { float: left; margin-right: 10px; }
		.allowed-tags label input{ margin-right: 3px; }
		</style>
		
		<script>
		jQuery('#editor-width').keyup(function(){ var unit = jQuery(this).next('span'); if( Number(this.value)>100 ) unit.text('px'); else unit.text('%');}).trigger('keyup');
		jQuery('#allow-all-additional-tags').click(function(){ if( this.checked ){ jQuery('.additional-tags input').attr('checked', 'checked'); }else{ jQuery('.additional-tags input').removeAttr('checked'); }});
		</script>
		
	</div>
	<?php
}

function admin_page_vailidate($input){
	$r = array();
	$r['enable'] = !empty($input['enable']) ? '1' : '';
	$r['enable_topic'] = !empty($input['enable_topic']) ? '1' : '';
	$r['enable_reply'] = !empty($input['enable_reply']) ? '1' : '';
	$r['form_validate'] = !empty($input['form_validate']) ? '1' : '';
	$r['include_post_css'] = !empty($input['include_post_css']) ? '1' : '';
	$r['width'] = absint($input['width']) ? absint($input['width']) : 77;
	$r['height'] = absint($input['height']) ? absint($input['height']) : 300;
	$r['skin'] = $input['skin'];
	$r['buttons1'] = $input['buttons1'];
	$r['buttons2'] = $input['buttons2'];
	$r['buttons3'] = $input['buttons3'];
	$r['buttons4'] = $input['buttons4'];
	$r['plugins'] = $input['plugins'];
	$r['plugin_dir'] = trim($input['plugin_dir'], '/');
	$r['allowed_tags'] = $input['allowed_tags'];
	$r['editor_style'] = $input['editor_style'];
	
	add_settings_error($this->id, 'settings_updated', __('Settings saved.'), 'updated');
	
	return $r;
}

}

$ubpfeditor = new UBPForumEditor;

function ubpfeditor_uninstall(){
	delete_option('ubpfeditor');
}

