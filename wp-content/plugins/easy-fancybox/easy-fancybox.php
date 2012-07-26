<?php
/*
Plugin Name: Easy FancyBox
Plugin URI: http://4visions.nl/en/wordpress-plugins/easy-fancybox/
Description: Easily enable the <a href="http://fancybox.net/">FancyBox jQuery extension</a> on all image, SWF, PDF, YouTube, Dailymotion and Vimeo links. Also supports iFrame and inline content.
Text Domain: easy-fancybox
Version: 1.3.4.9
Author: RavanH
Author URI: http://4visions.nl/
*/

// DEF

define( 'EASY_FANCYBOX_VERSION', '1.3.4.9' );
define( 'FANCYBOX_VERSION', '1.3.4' );
define( 'MOUSEWHEEL_VERSION', '3.0.4' );
define( 'EASING_VERSION', '1.3' );
define( 'METADATA_VERSION', '2.1' );

// Check if easy-fancybox.php is moved one dir up like in WPMU's /mu-plugins/
// or if plugins_url() returns the main plugins dir location as it does on 
// a Debian repository install.
// NOTE: WP_PLUGIN_URL causes problems when installed in /mu-plugins/
if(!stristr(plugins_url('', __FILE__),'/easy-fancybox'))
	define( 'FANCYBOX_SUBDIR', '/easy-fancybox' );
else
	define( 'FANCYBOX_SUBDIR', '' );

/* CHECK FOR NETWORK ACTIVATION
if (function_exists('is_plugin_active_for_network') && is_plugin_active_for_network(plugin_basename( __FILE__ )))
	$no_network_activate = '';
else
	$no_network_activate = '1';
*/
	
require_once(dirname(__FILE__) . FANCYBOX_SUBDIR . '/easy-fancybox-settings.php');

$easy_fancybox_array = easy_fancybox_settings();

// FUNCTIONS //

function easy_fancybox() {
	global $easy_fancybox_array;
	
	echo '
<!-- Easy FancyBox ' . EASY_FANCYBOX_VERSION . ' using FancyBox ' . FANCYBOX_VERSION . ' - RavanH (http://4visions.nl/en/wordpress-plugins/easy-fancybox/) -->';

	// check for any enabled sections
	$do_fancybox = false;
	foreach ($easy_fancybox_array['Global']['options']['Enable']['options'] as $value) {
		// anything enabled?
		if ( '1' == get_option($value['id'],$value['default']) ) {
			$do_fancybox = true;
			break;
		}
	}
	// and break off when none are active
	if (!$do_fancybox) {
		echo '
<!-- No sections enabled under Settings > Media > FancyBox -->

';
		return;
	}
	
	// begin output FancyBox settings
	echo '
<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){
var fb_timeout = null;';

	/*
	 * Global settings routine
	 */
	$more=0;
	echo '
var fb_opts = {';
	foreach ($easy_fancybox_array['Global']['options'] as $globals) {
		foreach ($globals['options'] as $_key => $_value) {
			$parm = ($_value['id']) ? get_option($_value['id'], $_value['default']) : $_value['default'];
			$parm = ('checkbox'==$_value['input'] && ''==$parm) ? '0' : $parm;
			if(!$_value['hide'] && $parm!='') {
				$quote = (is_numeric($parm) || $_value['noquotes']) ? '' : '\'';
				if ($more>0)
					echo ',';
				echo ' \''.$_key.'\' : ';
				if ('checkbox'==$_value['input'])
					echo ( '1' == $parm ) ? 'true' : 'false';
				else
					echo $quote.$parm.$quote;
				$more++;
			} else {
				$$_key = $parm;
			}
		}
	}
	echo ' };';
	
	foreach ($easy_fancybox_array as $key => $value) {
		// check if not enabled or hide=true then skip
		if ( $value['hide'] || !get_option($easy_fancybox_array['Global']['options']['Enable']['options'][$key]['id'], $easy_fancybox_array['Global']['options']['Enable']['options'][$key]['default']) )
			continue;

		echo '
/* ' . $key . ' */';
		/*
		 * Auto-detection routines (2x)
		 */
		$autoAttribute = get_option( $value['options']['autoAttribute']['id'], $value['options']['autoAttribute']['default'] );
		// update from previous version:
		if($attributeLimit == '.not(\':empty\')')
			$attributeLimit = ':not(:empty)';
		elseif($attributeLimit == '.has(\'img\')')
			$attributeLimit = ':has(img)';
		
		if(!empty($autoAttribute)) {
			if(is_numeric($autoAttribute)) {
				echo '
$(\'a['.$value['options']['autoAttribute']['selector'].']:not(.nofancybox)'.$attributeLimit.'\')';
				if ($value['options']['autoAttribute']['href-replace'])
					echo '.attr(\'href\', function(index, attr){'.$value['options']['autoAttribute']['href-replace'].'})';
				echo '.addClass(\''.$value['options']['class']['default'].'\');';
			} else {
				// set selectors
				$file_types = array_filter( explode( ' ', str_replace( ',', ' ', $autoAttribute ) ) );
				$more=0;
				echo '
var fb_'.$key.'_select = \'';
				foreach ($file_types as $type) {
					if ($more>0)
						echo ',';
					echo 'a['.$value['options']['autoAttribute']['selector'].'".'.$type.'"]:not(.nofancybox)'.$attributeLimit.',a['.$value['options']['autoAttribute']['selector'].'".'.strtoupper($type).'"]:not(.nofancybox)'.$attributeLimit;
					$more++;
				}
				echo '\';';

				// class and rel depending on settings
				if( '1' == get_option($value['options']['autoAttributeLimit']['id'],$value['options']['autoAttributeLimit']['default']) ) {
					// add class
					echo '
var fb_'.$key.'_sections = jQuery(\''.get_option($value['options']['autoSelector']['id'],$value['options']['autoSelector']['default']).'\');
fb_'.$key.'_sections.each(function() { jQuery(this).find(fb_'.$key.'_select).addClass(\''.$value['options']['class']['default'].'\')';
					// and set rel
					switch( get_option($value['options']['autoGallery']['id'],$value['options']['autoGallery']['default']) ) {
						case '':
						default :
							echo '; });';
							break;
						case '1':
							echo '.attr(\'rel\', \'gallery-\' + fb_'.$key.'_sections.index(this)); });';
							break;
						case '2':
							echo '.attr(\'rel\', \'gallery\'); });';
					}
				} else {
					// add class
					echo '
$(fb_'.$key.'_select).addClass(\''.$value['options']['class']['default'].'\')';
					// set rel
					switch( get_option($value['options']['autoGallery']['id'],$value['options']['autoGallery']['default']) ) {
						case '':
						default :
							echo ';';
							break;
						case '1':
							echo ';
var fb_'.$key.'_sections = jQuery(\''.get_option($value['options']['autoSelector']['id'],$value['options']['autoSelector']['default']).'\');
fb_'.$key.'_sections.each(function() { jQuery(this).find(fb_'.$key.'_select).attr(\'rel\', \'gallery-\' + fb_'.$key.'_sections.index(this)); });';
							break;
						case '2':
							echo '.attr(\'rel\', \'gallery\');';
					}
				}
				
			}
		}
		
		$autoAttributeAlt = get_option( $value['options']['autoAttributeAlt']['id'], $value['options']['autoAttributeAlt']['default'] );
		if(!empty($autoAttributeAlt) && is_numeric($autoAttributeAlt)) {
			echo '
$(\'a['.$value['options']['autoAttributeAlt']['selector'].']:not(.nofancybox)'.$attributeLimit.'\')';
			if ($value['options']['autoAttributeAlt']['href-replace'])
				echo '.attr(\'href\', function(index, attr){'.$value['options']['autoAttributeAlt']['href-replace']. '})';
			echo '.addClass(\''.$value['options']['class']['default'].'\');';
		}
		
		/*
		 * Append .fancybox() function
		 */
		$trigger='';
		if( $key == $autoClick )
			$trigger = '.filter(\':first\').trigger(\'click\')';

		echo '
$(\'';
		$tags = array_filter( explode( ',' , $value['options']['tag']['default'] ));
		$more=0;
		foreach ($tags as $_tag) {
			if ($more>0)
				echo ',';
			echo $_tag.'.'.$value['options']['class']['default'];
			$more++;
		}
		echo '\').fancybox( $.extend({}, fb_opts, {';
		$more=0;
		foreach ($value['options'] as $_key => $_values) {
			$parm = ($_values['id']) ? get_option($_values['id'], $_values['default']) : $_values['default'];
			$parm = ('checkbox'==$_values['input'] && ''==$parm) ? '0' : $parm;
			if(!$_values['hide'] && $parm!='') {
				$quote = (is_numeric($parm) || $_values['noquotes']) ? '' : '\'';
				if ($more>0)
					echo ',';
				echo ' \''.$_key.'\' : ';
				if ('checkbox'==$_values['input'])
					echo ( '1' == $parm ) ? 'true' : 'false';
				else
					echo $quote.$parm.$quote;
				$more++;
			}
		}
		echo ' }) )'.$trigger.';';

	}

	switch( $autoClick ) {
		case '':
		default :
			break;
		case '1':
			echo '
/* Auto-click */ 
$(\'#fancybox-auto\').trigger(\'click\');';
			break;
		case '99':
			echo '
/* Auto-load */ 
$(\'a[class*="fancybox"]\').filter(\':first\').trigger(\'click\');';
			break;
	}
	echo '
});
/* ]]> */
</script>
<style type="text/css">.fancybox-hidden{display:none}';

	if ('1' == $overlaySpotlight)
		echo '#fancybox-overlay{background-image:url("'. plugins_url(FANCYBOX_SUBDIR.'/light-mask.png', __FILE__) . '");background-position:50% -3%;background-repeat:no-repeat;-o-background-size:100%;-webkit-background-size:100%;-moz-background-size:100%;-khtml-background-size:100%;background-size:100%;position:fixed}';
	if ('' != $backgroundColor)
		echo '#fancybox-outer{background-color:'.$backgroundColor.'}';
	if ('' != $paddingColor)
		echo '#fancybox-content{border-color:'.$paddingColor.'}';
	if ('' != $textColor)
		echo '#fancybox-content{color:'.$textColor.'}';
	if ('' != $frameOpacity) {
		$frameOpacity_percent = (int)$frameOpacity*100;
		echo '#fancybox-outer{filter:alpha(opacity='.$frameOpacity_percent.');-moz-opacity:'.$frameOpacity.';opacity:'.$frameOpacity.'}';
	}
echo '</style>
';

}

// FancyBox Media Settings Section on Settings > Media admin page
function easy_fancybox_settings_section() {
	echo '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=ravanhagen%40gmail%2ecom&item_name=Easy%20FancyBox&item_number=&no_shipping=0&tax=0&bn=PP%2dDonationsBF&charset=UTF%2d8&lc=us" title="'.__('Donate to Easy FancyBox plugin development with PayPal - it\'s fast, free and secure!','easy-fancybox').'"><img src="https://www.paypal.com/en_US/i/btn/x-click-but7.gif" style="border:none;float:right;margin:0 0 10px 10px" alt="'.__('Donate to Easy FancyBox plugin development with PayPal - it\'s fast, free and secure!','easy-fancybox').'" width="72" height="29" /></a><p>'.__('The options in this section are provided by the plugin <strong><a href="http://4visions.nl/en/wordpress-plugins/easy-fancybox/">Easy FancyBox</a></strong> and determine the <strong>Media Lightbox</strong> overlay appearance and behaviour controlled by <strong><a href="http://fancybox.net/">FancyBox</a></strong>.','easy-fancybox').' '.__('First enable each sub-section that you need. Then save and come back to adjust its specific settings.','easy-fancybox').'</p><p>'.__('Note: Each additional sub-section and features like <em>Auto-detection</em>, <em>Elastic transitions</em> and all <em>Easing effects</em> (except Swing) will have some extra impact on client-side page speed. Enable only those sub-sections and options that you actually need on your site.','easy-fancybox').' '.__('Some setting like Transition options are unavailable for SWF video, PDF and iFrame content to ensure browser compatibility and readability.','easy-fancybox').'</p>';
}

// FancyBox Media Settings Fields
function easy_fancybox_settings_fields($args){
	switch($args['input']) {
		case 'multiple':
		case 'deep':
			foreach ($args['options'] as $options)
				easy_fancybox_settings_fields($options);
			echo $args['description'];
			break;
		case 'select':
			if( !empty($args['label_for']) )
				echo '<label for="'.$args['label_for'].'">'.$args['title'].'</label> ';
			else
				echo $args['title'];
			echo '
			<select name="'.$args['id'].'" id="'.$args['id'].'">';
			foreach ($args['options'] as $optionkey => $optionvalue) {
				$selected = (get_option($args['id'], $args['default']) == $optionkey) ? ' selected="selected"' : '';
				echo '
				<option value="'.esc_attr($optionkey).'"'.$selected.'>'.$optionvalue.'</option>';
			}
			echo '
			</select> ';
			if( empty($args['label_for']) )
				echo '<label for="'.$args['id'].'">'.$args['description'].'</label> ';
			else
				echo $args['description'];
			break;
		case 'checkbox':
			if( !empty($args['label_for']) )
				echo '<label for="'.$args['label_for'].'">'.$args['title'].'</label> ';
			else
				echo $args['title'];
			$value = esc_attr( get_option($args['id'], $args['default']) );
			if ($value == "1")
				$checked = ' checked="checked"';
			else
				$checked = '';
			if ($args['default'] == "1")
				$default = __('Checked','easy-fancybox');
			else
				$default = __('Unchecked','easy-fancybox');
			if( empty($args['label_for']) )
				echo '
			<label><input type="checkbox" name="'.$args['id'].'" id="'.$args['id'].'" value="1" '.$checked.'/> '.$args['description'].'</label><br />';
			else
				echo '
			<input type="checkbox" name="'.$args['id'].'" id="'.$args['id'].'" value="1" '.$checked.'/> '.$args['description'].'<br />';
			break;
		case 'text':
			if( !empty($args['label_for']) )
				echo '<label for="'.$args['label_for'].'">'.$args['title'].'</label> ';
			else
				echo $args['title'];
			echo '
			<input type="text" name="'.$args['id'].'" id="'.$args['id'].'" value="'.esc_attr( get_option($args['id'], $args['default']) ).'" class="'.$args['class'].'"/> ';
			if( empty($args['label_for']) )
				echo '<label for="'.$args['id'].'">'.$args['description'].'</label> ';
			else
				echo $args['description'];
			break;
		default:
			echo $args['description'];
	}
}


function easy_fancybox_register_settings($args){
	global $easy_fancybox_array;
	foreach ($args as $key => $value) {
		// check to see if the section is enabled, else skip to next
		if ( array_key_exists($key, $easy_fancybox_array['Global']['options']['Enable']['options']) && !get_option($easy_fancybox_array['Global']['options']['Enable']['options'][$key]['id'], $easy_fancybox_array['Global']['options']['Enable']['options'][$key]['default']) )
			continue;
			
		switch($value['input']) {
			case 'deep':
				// go deeper and loop back on itself 
				easy_fancybox_register_settings($value['options']);
				break;
			case 'multiple':
				add_settings_field( 'fancybox_'.$key, $value['title'], 'easy_fancybox_settings_fields', 'media', 'fancybox_section', $value);
				foreach ($value['options'] as $_value)
					if ($_value['id']) register_setting( 'media', $_value['id'] );	
				break;
			default:
				if ($value['id']) register_setting( 'media', 'fancybox_'.$key );
		}
	}
}

function easy_fancybox_admin_init(){
	load_plugin_textdomain('easy-fancybox', false, dirname(plugin_basename( __FILE__ )));

	add_settings_section('fancybox_section', __('FancyBox','easy-fancybox'), 'easy_fancybox_settings_section', 'media');

	global $easy_fancybox_array;
	easy_fancybox_register_settings($easy_fancybox_array);
}

function easy_fancybox_enqueue_scripts() {
	global $easy_fancybox_array;
	
	// check for any enabled sections plus the need for easing script
	$do_fancybox = false;
	$easing = false;

	foreach ($easy_fancybox_array['Global']['options']['Enable']['options'] as $value) {
		// anything enabled?
		if ( '1' == get_option($value['id'],$value['default']) ) {
			$do_fancybox = true;
			break;
		}
	}
	
	// break off if there is no need for any script files
	if (!$do_fancybox) 
		return;

	// ENQUEUE
	// first get rid of previously registered variants of jquery.fancybox by other plugins or theme
	wp_deregister_script('jquery.fancybox');
	wp_deregister_script('fancybox');
	wp_deregister_script('jquery-fancybox');
	// register main fancybox script
	wp_enqueue_script('jquery.fancybox', plugins_url(FANCYBOX_SUBDIR.'/fancybox/jquery.fancybox-'.FANCYBOX_VERSION.'.pack.js', __FILE__), array('jquery'), FANCYBOX_VERSION);
	
	// easing in IMG settings?
	if ( ( 'elastic' == get_option($easy_fancybox_array['IMG']['options']['transitionIn']['id'],$easy_fancybox_array['IMG']['options']['transitionIn']['default']) || 
		'elastic' == get_option($easy_fancybox_array['IMG']['options']['transitionOut']['id'],$easy_fancybox_array['IMG']['options']['transitionOut']['default']) ) 
		&& 
		( '' != get_option($easy_fancybox_array['IMG']['options']['easingIn']['id'],$easy_fancybox_array['IMG']['options']['easingIn']['default']) || 
		'' != get_option($easy_fancybox_array['IMG']['options']['easingOut']['id'],$easy_fancybox_array['IMG']['options']['easingOut']['default']) ) ) {
		// first get rid of previously registered variants of jquery.easing by other plugins or theme
		wp_deregister_script('jquery.easing');
		wp_deregister_script('jqueryeasing');
		wp_deregister_script('jquery-easing');
		wp_deregister_script('easing');
		// then register our version
		wp_enqueue_script('jquery.easing', plugins_url(FANCYBOX_SUBDIR.'/fancybox/jquery.easing-'.EASING_VERSION.'.pack.js', __FILE__), array('jquery'), EASING_VERSION, true);
	}
	
	// first get rid of previously registered variants of jquery.mousewheel (by other plugins)
	wp_deregister_script('jquery.mousewheel');
	wp_deregister_script('jquerymousewheel');
	wp_deregister_script('jquery-mousewheel');
	wp_deregister_script('mousewheel');
	// then register our version
	wp_enqueue_script('jquery.mousewheel', plugins_url(FANCYBOX_SUBDIR.'/fancybox/jquery.mousewheel-'.MOUSEWHEEL_VERSION.'.pack.js', __FILE__), array('jquery'), MOUSEWHEEL_VERSION, true);
	
	// first get rid of previously registered variants of jquery.metadata (by other plugins)
	wp_deregister_script('jquery.metadata');
	wp_deregister_script('jquerymetadata');
	wp_deregister_script('jquery-metadata');
	wp_deregister_script('metadata');
	// then register our version
	wp_enqueue_script('jquery.metadata',plugins_url(FANCYBOX_SUBDIR.'/jquery.metadata.js', __FILE__), array('jquery'), METADATA_VERSION, true);
}
	
function easy_fancybox_enqueue_styles() {
	// register style
	wp_enqueue_style('easy-fancybox.css', plugins_url(FANCYBOX_SUBDIR.'/easy-fancybox.css.php', __FILE__), false, FANCYBOX_VERSION, 'screen');
}

// Hack to fix missing wmode in (auto)embed code based on Crantea Mihaita's work-around on
// http://www.mehigh.biz/wordpress/adding-wmode-transparent-to-wordpress-3-media-embeds.html
// + own hack for dailymotion iframe embed...
if(!function_exists('add_video_wmode_opaque')) {
 function add_video_wmode_opaque($html, $url, $attr) {
	if (strpos($html, "<embed src=" ) !== false) {
		$html = str_replace('</param><embed', '</param><param name="wmode" value="opaque"></param><embed wmode="opaque" ', $html);
		return $html;
	} elseif(strpos($html, "<iframe src=\"http://player..vimeo.com/video/" ) !== false) {
		$html = str_replace('" width', '?theme=none&wmode=opaque" width', $html);
		return $html;
	} else {
		return $html;
	}
 }
}

// HOOKS //

add_filter('embed_oembed_html', 'add_video_wmode_opaque', 10, 3);
add_action('wp_print_styles', 'easy_fancybox_enqueue_styles', 999);
add_action('wp_enqueue_scripts', 'easy_fancybox_enqueue_scripts', 999);
add_action('wp_head', 'easy_fancybox', 999);

add_action('admin_init','easy_fancybox_admin_init');

