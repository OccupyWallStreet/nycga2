<?php
/**
 * 
 *
 *  PageLines Meta Panel Option Handling
 *
 *
 *  @package PageLines Framework
 *  @subpackage Post Types
 *  @since 4.0
 *
 */
class PageLinesMetaPanel {

	var $tabs = array();	// Controller for drawing meta options
	
	/**
	 * PHP5 constructor
	 */
	function __construct( $settings = array() ) {

		global $post; 
		global $pagenow;
					
		/**
		 * Single post pages have post as GET, not $post as object
		 */
		$post = (!isset($post) && isset($_GET['post'])) ? get_post($_GET['post'], 'object') : null;
		
		$this->ptype = PageLinesTemplate::current_admin_post_type();
	
		$this->page_for_posts = ( isset($post) && get_option( 'page_for_posts' ) === $post->ID ) ? true : false;			
	
		$this->blacklist = apply_filters( 'pagelines_meta_blacklist', array( 'banners', 'feature', 'boxes', 'attachment', 'revision', 'nav_menu_item' ));
		
		$defaults = array(
				'id' 		=> 'pagelines-metapanel',
				'name' 		=> $this->get_the_title(),
				'posttype' 	=> $this->get_the_post_types(),
				'location' 	=> 'normal', 
				'priority' 	=> 'low', 
				'hide_tabs'	=> false, 
				'global'	=> false, 
				'handle'	=> 'metatabs',
			);

		$this->settings = wp_parse_args($settings, $defaults); // settings for post type		
	
		$this->register_actions();

		$this->hide_tabs = $this->settings['hide_tabs'];
			
	}


	/**
	*
	* @TODO document
	*
	*/
	function register_actions(){

		$privelidge = (ploption('hide_controls_meta')) ? ploption('hide_controls_meta') : 'publish_posts';

		if ( !current_user_can( $privelidge ) )
			return;	
			
		// Adds the box
			add_action( 'admin_menu',  array(&$this, 'add_metapanel_box') );
		
	
		// Saves the options.
			add_action( 'post_updated', array(&$this, 'save_meta_options') );
		
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function add_metapanel_box(){
	
		
		
		foreach( $this->settings['posttype'] as $post_type){
			
			if( $this->settings['global'] ){
				$obj = get_post_type_object($post_type);
				
				if( !is_object($obj) || !$obj->public )
					continue;
			}
			
			add_meta_box($this->settings['id'], $this->settings['name'], 'pagelines_metapanel_callback', $post_type, $this->settings['location'], $this->settings['priority'], array( $this ));
		}
			
		
		
	}
	
	

	/**
	*
	* @TODO document
	*
	*/
	function get_the_post_types(){
		
		// if not in this array, then show the 
		$post_id = ( isset( $_GET['post'] ) ) ? $_GET['post'] : ( isset($_POST['post_ID']) ? $_POST['post_ID'] : null );


		if( isset( $post_id ) && !in_array( get_post_type( $post_id ), $this->blacklist ) )
			$pt = array( 'post', 'page', get_post_type( $post_id ) );
		else 
			$pt = array( 'post', 'page' );
	
		
		return $pt;
	}
	
	
	

	/**
	*
	* @TODO document
	*
	*/
	function get_edit_type(){
		global $post;
		
		if(!isset($this->ptype))
			$this->ptype = PageLinesTemplate::current_admin_post_type();
			
		if($this->ptype == 'post' || $this->ptype == 'page'){
			
			$current_template = (isset($post)) ? get_post_meta($post->ID, '_wp_page_template', true) : false;
	
			$this->page_templates = array_flip( get_page_templates() );
		
			if(  $this->ptype == 'page' && $current_template && $current_template != 'default') {

				if(isset($this->page_templates[$current_template]))
					$slug = $this->page_templates[$current_template];
		
			}elseif(  $this->ptype == 'page' )
				$slug = 'Default Page';
			elseif( $this->ptype == 'post' )
				$slug = 'Single Post';
			elseif( $this->page_for_posts )
				$slug = 'Blog Page';
			else 
				$slug = '';
		
			
		} elseif( $this->ptype )
			$slug = $this->ptype;
		elseif(isset($_GET['page']) && $_GET['page'] == PL_SPECIAL_OPTS_SLUG)
			$slug = 'Special Meta';
		else
			$slug = 'Default';

		if(isset($slug))
			$this->edit_slug = $slug;
		
		return ( isset( $slug ) ) ? $slug : '';
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function get_the_title(){
			global $post;
			$this->base_name = __( 'PageLines Meta Settings', 'pagelines' );
			$name = $this->base_name;
			return $name;
	}
	
	/**
	 * Register a new tab for the meta panel
	 * This will look at Clone values and draw cloned tabs for cloned sections
	 *
	 * @since 2.0.b4
	 */
	function register_tab( $o = array(), $option_array = array(), $location = 'bottom') {
		
		$d = array(
				'id' 		=> '',
				'name' 		=> '',
				'icon' 		=> '',
				'clone_id' 	=> 1, 
				'active'	=> true
			);

		$o = wp_parse_args($o, $d);
		

		$tab_id = $o['id'].$o['clone_id'];

		
		if( isset($o['clone_id']) && $o['clone_id'] != 1 ){
			
			$name = sprintf('%s (#%s)', $o['name'], $o['clone_id']);
			
			/**
			 * For cloned tab, unset keys and change to new val w/ key
			 */
			foreach($option_array as $key => $opt){
				
				if($opt['type'] == 'text_content' || $opt['type'] == 'text_content_reverse'){
					unset( $option_array[$key] );
					continue;

				}
				
				$newkey = join( '_', array($key, $o['clone_id']) );
				
			
				
				if(isset($opt['title']))
					$opt['title'] = sprintf('%s (#%s)', $opt['title'], $o['clone_id']);
				
				$new = $option_array[$newkey] = $opt;
				
				unset( $option_array[$key] );
			
				/**
				 * For multi options, keys will need to be changed too.
				 */
				if( pagelines_is_multi_option( $key, $opt ) && isset($option_array[$newkey]['selectvalues']) && is_array($option_array[$newkey]['selectvalues']) ){
					foreach($option_array[$newkey]['selectvalues'] as $skey => $sopt){
	

						$snewkey = join( '_', array($skey, $o['clone_id']) );
						
						$option_array[$newkey]['selectvalues'][$snewkey] = $sopt;
	
						unset( $option_array[$newkey]['selectvalues'][$skey] );

					}
				}
				
			
			}
			
			
		} else 
			$name = $o['name'];
		
		
		if($location == 'top'){
			
			$top[ $tab_id ] = new stdClass;
			
			$top[ $tab_id ]->options = $option_array;
			$top[ $tab_id ]->icon = $o['icon'];
			$top[ $tab_id ]->active = $o['active'];
			$top[ $tab_id ]->clone_id = $o['clone_id'];
			$top[ $tab_id ]->name = $name;
			

			$this->tabs = array_merge($top, $this->tabs);
			
		} else {
			
			$this->tabs[ $tab_id ] = new stdClass;
			
			$this->tabs[ $tab_id ]->options = $option_array;
			$this->tabs[ $tab_id ]->icon = $o['icon'];
			$this->tabs[ $tab_id ]->active = $o['active'];
			$this->tabs[ $tab_id ]->clone_id = $o['clone_id'];
			$this->tabs[ $tab_id ]->name = $name;
		}
		
	}

	

	/**
	*
	* @TODO document
	*
	*/
	function draw_panel(){ 
		global $post_ID;  
		global $pagelines_template;
	
		// if page doesn't support settings
		if ( $this->page_for_posts ){
			$this->non_meta_template(); 
			return;
		}
		
		$set = array(
				'handle'	=> $this->settings['handle'],
				'title' 	=> $this->settings['name'],
				'tag' 		=> ui_key($this->get_edit_type()),
				'type'		=> 'meta',
				'stext' 	=> __('Save Meta Settings','pagelines'),
				'tabs' 		=> $this->tabs, 
				'hidetabs'	=> $this->hide_tabs, 
				'post_ID'	=> $post_ID, 
				'post_type'	=> $this->settings['posttype'],
			);
			
		$panel = new PLPanel();
		
		$panel->the_panel($set);
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function posts_metapanel( $type, $mode = 'meta' ){
		
		
		$option_engine = new OptEngine( PAGELINES_SPECIAL );
		
		$handle = 'postsTabs'.$type;
		
		// Zero Out Tabs
		$this->tabs = array();
		
		do_global_meta_options( $mode );
		
	 	$special_template = new PageLinesTemplate( $type );	
		
		$special_template->load_section_optionator( $mode, $type );

		ob_start(); ?>

	<script type="text/javascript"> 
		jQuery(document).ready(function() { 
			<?php printf('var %1$s = jQuery("#%1$s").tabs({cookie: {  name: "htabs-%2$s" }, fx: { opacity: "toggle", duration: 150 }});', $handle, $type ); ?> 
		});
	</script>
	
		<div id="<?php echo $handle;?>" class="plist-nav fix">
			
			
			<ul class="fix plist">
				<?php if(count($this->tabs) != 1): ?>
					<lh class="hlist-header">Select Settings Panel</lh>
					<?php foreach($this->tabs as $tab => $t): ?>
						<li>
							<a class="<?php echo $tab;?>  metapanel-tab <?php if(!$t->active && $type != 'default' ) echo 'inactive-tab';?>" href="#<?php echo $tab;?>">
								<span class="metatab_pad fix">
									<span class="metatab_icon" style="background: transparent url(<?php echo $t->icon; ?>) no-repeat 0 0;display: block;">
										<?php 
											if(!$t->active && $type != 'default' ) 
												printf('<span class="tab_inactive">inactive</span>');
											
											echo substr($t->name, 0, 17); 
											 ?>
									</span>
								</span>
							</a>
						</li>
					<?php endforeach;?>
				<?php else: ?>
					<lh class="hlist-header"><?php echo ucfirst($mode);?> Settings</lh>
				<?php endif;?>
			</ul>
			
			<?php foreach($this->tabs as $tab => $t): ?>
				<div id="<?php echo $tab;?>" class="posts_tab_content">
					<div class="posts_tab_content_pad">
						<div class="metatab_title" style="background: url(<?php echo $t->icon; ?>) no-repeat 10px 13px;" >
							<?php 
						
								echo $t->name;
					
								if(!$t->active && $type != 'default') 
									echo OptEngine::superlink(__( 'Inactive On Template', 'pagelines' ), 'black', 'right', admin_url('admin.php?page=pagelines_templates'));
								elseif($type == 'default')
									echo OptEngine::superlink(__( 'Sitewide Defaults', 'pagelines' ), null, 'right');
							 ?>
						</div>
						<?php 
						foreach($t->options as $oid => $o){
							$o['special'] = $type;
							$o['scontrol'] = $mode;
							$o['clone_id'] = (isset($t->clone_id)) ? $t->clone_id : 1;
							
							$option_engine->option_engine($oid, $o);
						}
						?>
					</div>
				</div>
		
			<?php endforeach;?>
		</div>
		<?php

		return ob_get_clean();
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function non_meta_template(){?>
		<div class="metapanel_banner">
			<p>
				<strong>Note:</strong> Individual page settings do not work on the blog page (<em>use the settings panel</em>).
			</p>
		</div>
		
	<?php }



	/**
	 * Save Meta Options
	 * 
	 * Use tabs array to save options... 
	 * Need to identify if the option is being set to empty or has never been set
	 * *Section Control* gets its own saving schema
	 */
	function save_meta_options( $postID ){
	
		// Make sure we are saving on the correct post type...
	
		// Current post type is passed in $_POST
		$current_post_type = ( isset( $_POST['post_type'] ) ) ? $_POST['post_type'] : false;
	
		$post_type_save = ( in_array( $current_post_type, $this->settings['posttype'] ) ) ? true : false;

		if((isset($_POST['update']) || isset($_POST['save']) || isset($_POST['publish'])) && $post_type_save){
			
				
			
			$page_template = (isset($_POST['page_template'])) ? $_POST['page_template'] : null;
			$save_template = $this->get_save_template_type($_POST['post_type'], $page_template);
			$template_type = new PageLinesTemplate($save_template);
			$template_type->load_section_optionator( );
			
			
			// Loop through tabs
			foreach($this->tabs as $tab => $t){
				// Loop through tab options
				foreach($t->options as $oid => $o){
						
					
						
					if($oid == 'section_control')
						$this->save_sc( $postID );
					elseif($oid == 'page_background_image')
						$this->save_bg( $oid, $postID );
					elseif($o['type'] == 'text_content' || $o['type'] == 'text_content_reverse'){
						
						$option_value =  isset( $_POST[$oid] ) ? $_POST[ $oid ] : null;
						
						plupop($oid, $option_value);
						plupop($oid, $option_value, array('setting' => PAGELINES_SPECIAL));
						
					}elseif($o['type'] == 'check' && (bool) pldefault($oid)){

							
						$reverse = $oid."_reverse";
						
						$option_value =  isset( $_POST[$reverse] ) ? $_POST[ $reverse ] : null;
					
						if( !empty($option_value) || get_post_meta($postID, $reverse)  ){
						
							update_post_meta($postID, $reverse, $option_value );
						
						}
						
						
					} else {
						
					
						
						// Note: If the value is null, then test to see if the option is already set to something
						// create and overwrite the option to null in that case (i.e. it is being set to empty)
						if(isset($o['selectvalues']) && pagelines_is_multi_option($oid, $o) ){
							
							foreach($o['selectvalues'] as $sid =>$s ){
								$option_value =  isset($_POST[$sid]) ? $_POST[$sid] : null;
								
								if(!empty($option_value) || get_post_meta($postID, $sid))
									update_post_meta($postID, $sid, $option_value );
							}
							
						} else {
								
							$option_value =  isset( $_POST[$oid] ) ? $_POST[ $oid ] : null;
							
							
							if( !empty($option_value) || get_post_meta($postID, $oid)  ){
							
								update_post_meta($postID, $oid, $option_value );
							
							}
							
							
						}
						
						
					}
				}
			}
		}
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function get_save_template_type( $post_type = null, $template = 'default'){
		
		if( $post_type == 'post' ){
			return 'single';
		} elseif( $post_type == 'page' ){
			$page_filename = str_replace('.php', '', $template);
			$template_name = str_replace('page.', '', $page_filename);
			return $template_name;
		} elseif( isset($post_type) )
			return $post_type;
		else 
			return 'default';
		
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function save_bg ( $oid, $postID ) {
		$bg = OptEngine::_background_image_array();
		
		foreach($bg as $k => $i){
			$bgid = $oid.$k;
		
			$option_value =  isset($_POST[$bgid]) ? $_POST[$bgid] : null;

			if(!empty($option_value) || get_post_meta($postID, $bgid))
				update_post_meta($postID, $bgid, $option_value );
		}
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function save_sc( $postID ){
		global $pagelines_template;

		global $post; 

		$save_template = new PageLinesTemplate();

	
		foreach( $save_template->map as $hook => $h ){
	
			if(isset($h['sections'])){
				foreach($h['sections'] as $key => $section_slug)
					$this->save_section_control($postID,  $section_slug, $hook );					
				
			} elseif (isset($h['templates'])){
				foreach($h['templates'] as $template => $t){
					
					if( isset($t['sections']) && !empty($t['sections'])){
						foreach($t['sections'] as $key => $section_slug){

							$template_slug = $hook.'-'.$template;
							$this->save_section_control($postID,  $section_slug, $template_slug );				
						}
					}
					
				}
			}
			
		}
	
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function save_section_control($postID,  $sid, $template_slug ){
		
		
		$check_name_hide = meta_option_name( array('hide', $template_slug, $sid) );

		$this->save_meta($postID, $check_name_hide);
		
		$check_name_show = meta_option_name( array('show', $template_slug, $sid) );
	
		$this->save_meta($postID, $check_name_show);
		
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function save_meta($postID, $name){
		
		$option_value =  isset($_POST[ $name ]) ? $_POST[ $name ] : null;
	
		if(!empty($option_value) || get_post_meta($postID, $name))
			update_post_meta($postID, $name, $option_value );
	}



	
}
/////// END OF MetaOptions CLASS ////////


/**
*
* @TODO do
*
*/
function pagelines_metapanel_callback($post, $object){

	$object['args'][0]->draw_panel();
	
}


/**
*
* @TODO do
*
*/
function register_metatab($settings, $option_array, $section = '', $location = 'bottom'){
	
	global $metapanel_options;
	
	foreach($option_array as $key => $opt)
		$option_array[$key]['section'] = $section;
	
	$metapanel_options->register_tab($settings, $option_array, $location);
	
}


/**
*
* @TODO do
*
*/
function add_global_meta_options( $meta_array = array(), $location = 'bottom'){
	global $global_meta_options;

	if($location == 'top')
		$global_meta_options = array_merge($meta_array, $global_meta_options);
	else
		$global_meta_options = array_merge($global_meta_options, $meta_array);
	
}

/**
*
* @TODO do
*
*/
function do_global_meta_options( $mode = '' ){
	
	global $global_meta_options;
	
	$metatab_settings = array(
			'id' 	=> 'general_page_meta',
			'name' 	=> __( 'Page Setup', 'pagelines' ),
			'icon' 	=>  PL_ADMIN_ICONS . '/ileaf.png'
		);

	
	if($mode == 'integration')
		unset($global_meta_options['_pagelines_layout_mode']);

	if($mode == 'default')
		return; 
		
	register_metatab($metatab_settings,  $global_meta_options, '', 'top');
}

/**
 *
 *  Returns Extension Array Config
 *
 */
function special_page_settings_array(  ){

	global $metapanel_options;
	
	$d = array(
		
		'site_defaults' => array(
			'metapanel' => $metapanel_options->posts_metapanel( 'default', 'default' ),
			'icon'		=> PL_ADMIN_ICONS.'/equalizer.png'
		),
		'blog_page' => array(
			'metapanel' => $metapanel_options->posts_metapanel( 'posts' ),
			'icon'		=> PL_ADMIN_ICONS.'/blog.png'
		),		
		'archive_page' => array(
			'metapanel' => $metapanel_options->posts_metapanel( 'archive' ),
			'icon'		=> PL_ADMIN_ICONS.'/archives.png', 
			'version'	=> 'pro'
		),
		'category_page' => array(
			'metapanel' => $metapanel_options->posts_metapanel( 'category' ),
			'icon'		=> PL_ADMIN_ICONS.'/category.png', 
			'version'	=> 'pro'
		),
		'search_results' => array(
			'metapanel' => $metapanel_options->posts_metapanel('search'),
			'icon'		=> PL_ADMIN_ICONS.'/search.png',
			'version'	=> 'pro'
		),
		'tag_listing' => array(
			'metapanel' => $metapanel_options->posts_metapanel('tag'),
			'icon'		=> PL_ADMIN_ICONS.'/tag.png', 
			'version'	=> 'pro'
		),
		'author_posts' => array(
			'metapanel' => $metapanel_options->posts_metapanel('author'),
			'icon'		=> PL_ADMIN_ICONS.'/author.png', 
			'version'	=> 'pro'
		),
		'404_page' => array(
			'metapanel' => $metapanel_options->posts_metapanel('404_page'),
			'icon'		=> PL_ADMIN_ICONS.'/404.png', 
			'version'	=> 'pro'
		),
	);
	
	$ints = handle_integrations_meta();
	
	$d = array_merge($d, $ints);

	return apply_filters('postsmeta_settings_array', $d); 
}

/**
*
* @TODO do
*
*/
function get_global_meta_options(){
	$opts = array(
		
		'_pagelines_layout_mode' => array(
			'type' 			=> 'graphic_selector',
			'sprite'		=> PL_ADMIN_IMAGES.'/sprite-layouts.png', 
			'height'		=> '50px', 
			'width'			=> '50px', 
			'selectvalues'	=> array(
				'fullwidth'				=> array( 'name' => __( 'Fullwidth layout', 'pagelines' ), 'version' => 'pro', 'offset' => '0px 0px'),
				'one-sidebar-right' 	=> array( 'name' => __( 'One sidebar on right', 'pagelines' ), 'offset' => '0px -50px'),
				'one-sidebar-left'		=> array( 'name' => __( 'One sidebar on left', 'pagelines' ), 'offset' => '0px -100px'),
				'two-sidebar-right' 	=> array( 'name' => __( 'Two sidebars on right', 'pagelines' ), 'version' => 'pro', 'offset' => '0px -150px' ),
				'two-sidebar-left' 		=> array( 'name' => __( 'Two sidebars on left', 'pagelines' ), 'version' => 'pro', 'offset' => '0px -200px' ),
				'two-sidebar-center' 	=> array( 'name' => __( 'Two sidebars, one on each side', 'pagelines' ), 'version' => 'pro', 'offset' => '0px -250px' ),
			),
			'title' 		=> __( 'Individual Page Content Layout', 'pagelines' ),
			'inputlabel'	=> __( 'Select Page Layout', 'pagelines' ),	
			'layout' 		=> 'interface',						
			'shortexp' 		=> __( 'Select the layout that will be used on this page', 'pagelines' ),
			'exp' 			=> '',
		),
		
		'section_control' => array(
			'type' 			=> 'section_control',
			'title' 		=> __( 'Individual Page Section Control', 'pagelines' ),
			'layout' 		=> 'interface',				
			'shortexp' 		=> __( 'Control which sections appear on this specific page', 'pagelines' ),
			'exp' 			=> '',
		),
		
		'page_background_image' => array(
			'title' 	=> 'Page Background Image',						
			'shortexp' 	=> 'Setup A Background Image For This Page',
			'exp' 		=> 'Use this option to apply a background image to this page. This option will only be applied to the current page.<br/><br/><strong>Positioning</strong> Use percentages to position the images, 0% corresponds to the "top" or "left" side, 50% to center, etc..',
			'type' 		=> 'background_image',
			'selectors'	=> cssgroup('page_background_image'), 
			'disabled_mode'	=> 'color_control'
		),
		'disable_mobile_view' => array(
			'default' 	=> false,
			'type' 		=> 'check',
			'title' 	=> __( 'Disable Mobile Optimized View', 'pagelines' ),
			'inputlabel'	=> __( 'Disable Mobile View', 'pagelines' ),				
			'shortexp' 	=> __( 'Make it so mobile devices will see the full site, not the mobile optimized one.', 'pagelines' ),
			'exp' 		=> __( 'By default PageLines accommodates mobile devices resolution and shows a mobile optimized view. Check this option to make it so users see your full site.', 'pagelines' ),
		),
		
	);
	
	return apply_filters('global_meta_options', $opts);
}
