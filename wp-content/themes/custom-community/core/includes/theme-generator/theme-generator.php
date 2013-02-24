<?php 
class CC_Theme_Generator{

	var $detect;

	/**
	 * PHP 4 constructor
	 *
	 * @package custom community
	 * @since 1.8.3
	 */
	function custom_community() {
		$this->__construct();
	}

	/**
	 * PHP 5 constructor
	 *
	 * @package Custom Community
	 * @since 1.8.3
	 */	
	function __construct() {
		global $bp;
		
		$this->detect = new TK_WP_Detect();
	
		// load predefined constants first
		add_action( 'bp_head', array( $this, 'load_constants' ), 2 );
		
		//CC_Theme_Generator::load_constants();
		
		// header.php
		add_action( 'bp_before_header', array( $this, 'innerrim_before_header' ), 2 );
		add_action( 'bp_after_header', array( $this, 'innerrim_after_header' ), 2 );
		add_action( 'bp_before_access', array( $this, 'menue_enable_search' ), 2 );
		add_action( 'bp_before_access', array( $this, 'header_logo' ), 2 );
		add_action( 'bp_menu', array( $this, 'bp_menu' ), 2 );
		add_filter( 'wp_page_menu_args', array( $this, 'remove_home_nav_from_fallback'), 100 ); 
		add_action( 'bp_after_header', array( $this, 'slideshow_home' ), 2 );
		add_action( 'favicon', array( $this, 'favicon' ), 2 );
		
		// footer.php
		add_action( 'bp_before_footer', array( $this, 'innerrim_before_footer' ), 2 );
		add_action( 'bp_after_footer', array( $this, 'innerrim_after_footer' ), 2 );
		add_action( 'bp_footer', array( $this, 'footer_content' ), 2 );
		
		// sidebars
		add_action( 'sidebar_left', array( $this, 'sidebar_left' ), 2 );
		add_action( 'sidebar_right', array( $this, 'sidebar_right' ), 2 );
		add_action( 'bp_inside_after_sidebar', array( $this, 'login_sidebar_widget' ), 2 );
		
		// home
		add_action( 'bp_before_blog_home', array( $this, 'default_homepage_last_posts' ), 2 );
		add_filter('body_class',array( $this, 'home_body_class'), 10 );
		
		// Posts lists
		add_filter('body_class',array( $this, 'posts_lists_body_class'), 10 );

		// helper functions
		add_action( 'blog_post_entry', array( $this, 'excerpt_on' ), 2 );
		
		// groups
		add_action( 'bp_before_group_home_content', array( $this, 'before_group_home_content' ), 2 );
		
		// profile
		add_action( 'bp_before_member_home_content', array( $this, 'before_member_home_content' ), 2 );
		
		// custom login
		add_action('login_head', array( $this, 'custom_login'), 2 );
	}
	

	function load_constants(){
		global $cap, $post;

		$component = explode('-',$this->detect->tk_get_page_type());
		
		if($cap->sidebar_position == ''){
			$cap->sidebar_position      = __('right','cc') ;
			$cap->menue_disable_home    = true;
			$cap->enable_slideshow_home = __('home','cc') ;
			$cap->header_text           = __('off','cc') ;
			$cap->preview               = true;
		}	
		
		$sidebar_position = $cap->sidebar_position;
		
		if(!empty($component[2])){
			if($component[2] == 'groups' && !empty($component[3]) && ($cap->bp_groups_sidebars != 'default' && $cap->bp_groups_sidebars != __('default','cc') )) {
				$sidebar_position = $cap->bp_groups_sidebars;
			} elseif($component[2] == 'profile' && !empty($component[3]) && ($cap->bp_profile_sidebars != 'default' && $cap->bp_profile_sidebars != __('default','cc') )) {
				$sidebar_position = $cap->bp_profile_sidebars;
			}
		}
		
		// return if enabled "pro" option, further processing of load constants moves to cc_pro_load_constants() function
		if( defined('is_pro') ) return;

		switch ($sidebar_position) {
			case __('left','cc') : $cap->rightsidebar_width = 0; break;
			case __('right','cc') : $cap->leftsidebar_width = 0; break;
			case __('none','cc') : $cap->leftsidebar_width = 0; $cap->rightsidebar_width = 0; break;
			case __('full-width','cc') : $cap->leftsidebar_width = 0; $cap->rightsidebar_width = 0; break;
		}

		if(isset($post)){
			$tmp = get_post_meta( $post->ID, '_wp_page_template', true );
			if( !is_search() ){
				switch ($tmp) {
					case 'full-width.php': $cap->leftsidebar_width = 0; $cap->rightsidebar_width = 0; break;
				}
			}
		}
	}
	
	/**
	 * header: add div 'innerrim' before header if the header is not set to full width
	 * 
	 * located: header.php - do_action( 'bp_before_header' )
	 *
	 * @package Custom Community
	 * @since 1.8.3
	 */	
	function innerrim_before_header(){
		global $cap;
		
		if ($cap->header_width != "full-width" && $cap->header_width != __("full-width",'cc') ) {
			echo '<div id="innerrim" class="span12">'; 
		}
	}
	
	/**
	 * header: add div 'innerrim' after header if the header is set to full width
	 * 
	 * located: header.php do_action( 'bp_after_header' ) on line 84
	 *
	 * @package Custom Community
	 * @since 1.8.3
	 */	
	function innerrim_after_header(){
		global $cap;
		
		if ($cap->header_width == "full-width" && $cap->header_width == __("full-width",'cc')) {
			echo '<div id="innerrim" class="span12">'; 
		}
	}
	
	/**
	 * header: add a search field in the header
	 * 
	 * located: header.php do_action( 'bp_after_header_nav' )
	 *
	 * @package Custom Community
	 * @since 1.8.3
	 */	
	
	function menue_enable_search(){
		global $cap;

		if(defined('BP_VERSION')){
			if($cap->menue_enable_search){?>
			<div id="search-bar" role="search">
				<div class="padder">
					
						<form action="<?php echo bp_search_form_action() ?>" method="post" id="search-form">
							<input type="text" id="search-terms" name="search-terms" value="<?php echo isset( $_REQUEST['s'] ) ? esc_attr( $_REQUEST['s'] ) : ''; ?>" />
							<?php echo bp_search_form_type_select() ?>

							<input type="submit" name="search-submit" id="search-submit" value="<?php _e( 'Search', 'cc' ) ?>" />

							<?php wp_nonce_field( 'bp_search_form' ) ?>

						</form><!-- #search-form -->

				<?php do_action( 'bp_search_login_bar' ) ?>

				</div><!-- .padder -->
			</div><!-- #search-bar -->
			<?php 
			}
		}
	}
	
	/**
	 * header: add a header logo in the header
	 * 
	 * located: header.php do_action( 'bp_before_access' )
	 *
	 * @package Custom Community
	 * @since 1.8.3
	 */	
	function header_logo(){
		global $cap;	
			if(is_home()): ?>
			<div id="logo">
			<h1><a href="<?php echo site_url() ?>" title="<?php _e( 'Home', 'cc' ) ?>"><?php if(defined('BP_VERSION')){ bp_site_name(); } else { bloginfo('name'); } ?></a></h1>
			<div id="blog-description"><?php bloginfo('description'); ?></div>
			
			<?php if($cap->logo){ ?>
			<a href="<?php echo site_url() ?>" title="<?php _e( 'Home', 'cc' ) ?>"><img src="<?php echo $cap->logo?>" alt="<?php if(defined('BP_VERSION')){ bp_site_name(); } else { bloginfo('name'); } ?>"></img></a>
			<?php } ?>
			</div>
		<?php else: ?>
			<div id="logo">
			<h4><a href="<?php echo site_url() ?>" title="<?php _e( 'Home', 'cc' ) ?>"><?php if(defined('BP_VERSION')){ bp_site_name(); } else { bloginfo('name'); } ?></a></h4>
			<div id="blog-description"><?php bloginfo('description'); ?></div>
			<?php if($cap->logo){ ?>
			<a href="<?php echo site_url() ?>" title="<?php _e( 'Home', 'cc' ) ?>"><img src="<?php echo $cap->logo?>" alt="<?php if(defined('BP_VERSION')){ bp_site_name(); } else { bloginfo('name'); } ?>"></img></a>
			<?php } ?>
			</div>
		<?php endif;
	}
	
	/**
	 * header: add the buddypress dropdown navigation to the menu
	 * 
	 * located: header.php do_action( 'bp_menu' )
	 *
	 * @package Custom Community
	 * @since 1.8.3
	 */	
	function bp_menu(){
		global $cap;	
	
		if(!defined('BP_VERSION')) :
			if($cap->menue_disable_home == true){ ?>
				<ul>
					<li id="nav-home"<?php if ( is_home() ) : ?> class="span2 current-menu-item"<?php endif; ?>>
						<a href="<?php echo site_url() ?>" title="<?php _e( 'Home', 'cc' ) ?>"><?php _e( 'Home', 'cc' ) ?></a>
					</li>
				</ul>
			<?php } ?>
		<?php else : ?>
			<ul>
			<?php if($cap->menue_disable_home == true){ ?>
				<li id="nav-home"<?php if ( is_front_page() ) : ?> class="span2 current-menu-item"<?php endif; ?>>
					<a href="<?php echo site_url() ?>" title="<?php _e( 'Home', 'cc' ) ?>"><?php _e( 'Home', 'cc' ) ?></a>
				</li>
			<?php }?>
				<?php if($cap->menue_enable_community == true){ ?>
				<li id="nav-community"<?php if ( bp_is_page( BP_ACTIVITY_SLUG ) || (bp_is_page( BP_MEMBERS_SLUG ) || bp_is_user()) || (bp_is_page( BP_GROUPS_SLUG ) || bp_is_group()) || bp_is_page( BP_FORUMS_SLUG ) || bp_is_page( BP_BLOGS_SLUG ) )  : ?> class="span2 page_item current-menu-item"<?php endif; ?>>
					<a href="<?php echo site_url() ?>/<?php echo BP_ACTIVITY_SLUG ?>/" title="<?php _e( 'Community', 'cc' ) ?>"><?php _e( 'Community', 'cc' ) ?></a>
					<ul class="children">
						<?php if ( 'activity' != bp_dtheme_page_on_front() && bp_is_active( 'activity' ) ) : ?>
							<li<?php if ( bp_is_page( BP_ACTIVITY_SLUG ) ) : ?> class="selected"<?php endif; ?>>
								<a href="<?php echo site_url() ?>/<?php echo BP_ACTIVITY_SLUG ?>/" title="<?php _e( 'Activity', 'cc' ) ?>"><?php _e( 'Activity', 'cc' ) ?></a>
							</li>
						<?php endif; ?>
		
						<li<?php if ( bp_is_page( BP_MEMBERS_SLUG ) || bp_is_user() ) : ?> class="selected"<?php endif; ?>>
							<a href="<?php echo site_url() ?>/<?php echo BP_MEMBERS_SLUG ?>/" title="<?php _e( 'Members', 'cc' ) ?>"><?php _e( 'Members', 'cc' ) ?></a>
						</li>
		
						<?php if ( bp_is_active( 'groups' ) ) : ?>
							<li<?php if ( bp_is_page( BP_GROUPS_SLUG ) || bp_is_group() ) : ?> class="selected"<?php endif; ?>>
								<a href="<?php echo site_url() ?>/<?php echo BP_GROUPS_SLUG ?>/" title="<?php _e( 'Groups', 'cc' ) ?>"><?php _e( 'Groups', 'cc' ) ?></a>
							</li>
							<?php if ( bp_is_active( 'forums' ) && ( function_exists( 'bp_forums_is_installed_correctly' ) && !(int) bp_get_option( 'bp-disable-forum-directory' ) ) && bp_forums_is_installed_correctly() ) : ?>
								<li<?php if ( bp_is_page( BP_FORUMS_SLUG ) ) : ?> class="selected"<?php endif; ?>>
									<a href="<?php echo site_url() ?>/<?php echo BP_FORUMS_SLUG ?>/" title="<?php _e( 'Forums', 'cc' ) ?>"><?php _e( 'Forums', 'cc' ) ?></a>
								</li>
							<?php endif; ?>
						<?php endif; ?>
		
						<?php if ( bp_is_active( 'blogs' ) && is_multisite() ) : ?>
							<li<?php if ( bp_is_page( BP_BLOGS_SLUG ) ) : ?> class="selected"<?php endif; ?>>
								<a href="<?php echo site_url() ?>/<?php echo BP_BLOGS_SLUG ?>/" title="<?php _e( 'Blogs', 'cc' ) ?>"><?php _e( 'Blogs', 'cc' ) ?></a>
							</li>
						<?php endif; ?>
					</ul>
				</li>
        		<?php do_action( 'bp_nav_items' ); ?>
        		<?php } ?>
			</ul>
		<?php endif;
		}

	
	function remove_home_nav_from_fallback( $args ) {
		$args['show_home'] = false;
		return $args;
	}
	
	/**
	 * header: add the top slider to the homepage, all pages, or just on specific pages
	 * 
	 * !!! this function needs to be rewritten !!!
	 * 
	 * located: header.php do_action( 'bp_after_header' )
	 *
	 * @package Custom Community
	 * @since 1.8.3
	 */	
	function slideshow_home(){
		global $cap;
		$cc_page_options=cc_get_page_meta();

		if(defined('BP_VERSION')){ 
			if($cap->enable_slideshow_home == 'all' || $cap->enable_slideshow_home == __('all','cc') 
				|| ($cap->enable_slideshow_home == 'home' || $cap->enable_slideshow_home == __('home','cc') ) && is_home() 
				|| ($cap->enable_slideshow_home  == 'home' || $cap->enable_slideshow_home  == __('home','cc') ) && is_front_page() 
				|| ($cap->enable_slideshow_home == 'home' || $cap->enable_slideshow_home == __('home','cc') ) && bp_is_component_front_page( 'activity' ) 
				|| is_page() && isset($cc_page_options) && isset($cc_page_options['cc_page_slider_on']) && $cc_page_options['cc_page_slider_on'] == 1){
				echo cc_slidertop(); // located under wp/templatetags
			}
		} elseif(($cap->enable_slideshow_home == 'all' || $cap->enable_slideshow_home == __('all','cc') ) 
			|| ($cap->enable_slideshow_home == 'home' || $cap->enable_slideshow_home == __('home','cc') ) 
			&& is_home() 
			|| ($cap->enable_slideshow_home == 'home' || $cap->enable_slideshow_home == __('home','cc') ) 
			&& is_front_page() 
			|| is_page() 
			&& isset($cc_page_options) && $cc_page_options['cc_page_slider_on'] == 1){
			echo cc_slidertop(); // located under wp/templatetags
		}
	}
	
	/**
	 * header: add the favicon icon to the site
	 * 
	 * located: header.php do_action( 'favicon' )
	 *
	 * @package Custom Community
	 * @since 1.8.3
	 */	
	function favicon(){
		global $cap;	
		
		if($cap->favicon != '') {
			echo '<link rel="shortcut icon" href="'.$cap->favicon.'" />';
		}
	}
	

	/**
	 * footer: add div 'innerrim' before footer if the footer is set to full width
	 * 
	 * located: footer.php do_action( 'bp_before_footer' )
	 *
	 * @package Custom Community
	 * @since 1.8.3
	 */	
	function innerrim_before_footer(){
		global $cap;
		
		if ($cap->footer_width == "full-width" || $cap->footer_width == __("full-width",'cc') ) {
			echo '</div><!-- #innerrim -->'; 
		}
	}

	/**
	 * footer: add div 'innerrim' after footer if the footer is not set to full width
	 * 
	 * located: footer.php do_action( 'bp_after_footer' )
	 *
	 * @package Custom Community
	 * @since 1.8.3
	 */	
	function innerrim_after_footer(){
		global $cap;
		
		if ($cap->footer_width != "full-width") {
			echo '</div><!-- #innerrim -->';
		}
	}

	/**
	 * footer: add the sidebars and their default widgets to the footer
	 * 
	 * located: footer.php do_action( 'bp_after_footer' )
	 *
	 * @package Custom Community
	 * @since 1.8.3
	 */	
	function footer_content(){ 
		global $cap;?>
        <div class="row-fluid">
            <?php if( ! dynamic_sidebar( 'footerfullwidth' )) :
                if($cap->preview == true){ ?>
                    <div class="widget gererator span12">
                            <h3 class="widgettitle" ><?php _e('20 widget areas all over the site', 'cc'); ?></h3>
                            <div><p class="widget_content"><?php _e('4 header + 4 footer widget areas (2 full width and 6 columns).','cc') ?> <br>
                            <?php _e('6 widget areas for members + 6 for groups.','cc') ?> 
                            </p></div>

                    </div>
                <?php } ?>	
            <?php endif; ?>

            <?php  if (is_active_sidebar('footerleft') || $cap->preview == true ){ ?>
            <div class="widgetarea cc-widget span4">
                <?php if( ! dynamic_sidebar( 'footerleft' )){ ?>
                    <div class="widget">
                        <h3 class="widgettitle" ><?php _e('Links', 'cc'); ?></h3>
                        <ul>
                            <?php wp_list_bookmarks('title_li=&categorize=0&orderby=id'); ?>
                        </ul>
                    </div>
                <?php } ?>
            </div>
            <?php  } ?>

            <?php if (is_active_sidebar('footercenter') || $cap->preview == true){ ?>
            <div class="<?php if(!is_active_sidebar('footerleft') && $cap->preview != true ) { echo 'footer-left-widget'; } ?> widgetarea cc-widget span4">
                <?php if( ! dynamic_sidebar( 'footercenter' )){ ?>
                    <div class="widget">
                        <h3 class="widgettitle" ><?php _e('Archives', 'cc'); ?></h3>
                        <ul>
                            <?php wp_get_archives( 'type=monthly' ); ?>
                        </ul>
                    </div>				
                <?php } ?>
            </div>
            <?php } ?>

            <?php if (is_active_sidebar('footerright') || $cap->preview == true ){ ?>
            <div class="widgetarea cc-widget cc-widget-right span4">
                <?php if( ! dynamic_sidebar( 'footerright' )){ ?>
                    <div class="widget">
                        <h3 class="widgettitle" ><?php _e('Meta', 'cc'); ?></h3>
                        <ul>
                            <?php wp_register(); ?>
                            <li><?php wp_loginout(); ?></li>
                            <?php wp_meta(); ?>
                        </ul>
                    </div>
                <?php } ?>
            </div>
            <?php } ?>
        </div>
  		<div class="clear"></div>
	  	<br />
		<?php if($cap->disable_credits_footer != false || !defined('is_pro')){ ?>
			<br />
			<div class="credits"><?php printf( __( '%s is proudly powered by <a class="credits" href="http://wordpress.org">WordPress</a> and <a class="credits" href="http://buddypress.org">BuddyPress</a>. ', 'cc' ), bloginfo('name') ); ?>
			<?php _e('Just another <a class="credits" href="http://themekraft.com/all-themes/" target="_blank" title="Wordpress Theme" alt="WordPress Theme">WordPress Theme</a> developed by Themekraft.','cc') ?></div>
		<?php } ?>
		<?php if($cap->my_credits_footer != '' ){ ?>
			<br />
			<div class="credits"><?php echo $cap->my_credits_footer; ?></div>
		<?php } ?>
	<?php 
	}
	

	/**
	 * header: add the sidebar and their default widgets to the left sidebar
	 * 
	 * located: header.php do_action( 'sidebar_left' )
	 *
	 * @package Custom Community
	 * @since 1.8.3
	 */	
	function sidebar_left(){
		global $cap, $post;
		
        if(isset($post)){
            $tmp = get_post_meta( $post->ID, '_wp_page_template', true );
            if( $tmp == 'full-width.php'|| $tmp == 'tpl-search-full-width.php' || $tmp == 'right-sidebar.php' || $tmp == '_pro/tpl-right-sidebar.php')
                return;

                if( $tmp == 'left-and-right-sidebar.php' || $tmp == 'left-sidebar.php' || 
                    $tmp == '_pro/tpl-left-and-right-sidebar.php' || $tmp == '_pro/tpl-search-right-and-left-sidebar.php' ||
                    $tmp == '_pro/tpl-left-sidebar.php' || $tmp == '_pro/tpl-search-left-sidebar.php' ){
                    locate_template( array( 'sidebar-left.php' ), true );
                    return;		
                }
        }
		$component = explode('-',$this->detect->tk_get_page_type());
        if(!empty($component[2])){	
		
			if($component[2] == 'groups' && !empty($component[3])) {
				if($cap->bp_groups_sidebars == 'left' || $cap->bp_groups_sidebars == __('left','cc')  
					|| $cap->bp_groups_sidebars == 'left and right'  || $cap->bp_groups_sidebars == __('left and right','cc') ){
					locate_template( array( 'groups/single/group-sidebar-left.php' ), true );
				} elseif(($cap->bp_groups_sidebars == "default" || $cap->bp_groups_sidebars == __("default",'cc') ) 
					&& ($cap->sidebar_position == "left" || $cap->sidebar_position == __("left",'cc') ) 
					|| ($cap->sidebar_position == "left and right" || $cap->sidebar_position == __("left and right",'cc') ) 
					&& ($cap->bp_groups_sidebars == "default" || $cap->bp_groups_sidebars == __("default",'cc') )){
					locate_template( array( 'sidebar-left.php' ), true );
				}
			} elseif($component[2] == 'profile' && !empty($component[3])) {
			
				if($cap->bp_profile_sidebars == 'left' || $cap->bp_profile_sidebars == __('left','cc') 
					|| $cap->bp_profile_sidebars == 'left and right' || $cap->bp_profile_sidebars == __('left and right','cc')  ){
					locate_template( array( 'members/single/member-sidebar-left.php' ), true );
				} elseif( ($cap->bp_profile_sidebars == "default" || $cap->bp_profile_sidebars == __("default",'cc') ) 
					&& ($cap->sidebar_position == "left" || $cap->sidebar_position == __("left",'cc') ) 
					|| ($cap->sidebar_position == "left and right" || $cap->sidebar_position == __("left and right",'cc') ) 
					&& ($cap->bp_profile_sidebars == "default" || $cap->bp_profile_sidebars == __("default",'cc') )){
					locate_template( array( 'sidebar-left.php' ), true );
				}
			} else if($cap->sidebar_position == "left" || $cap->sidebar_position == __("left",'cc') 
				|| $cap->sidebar_position == "left and right" || $cap->sidebar_position == __("left and right",'cc') ){
				locate_template( array( 'sidebar-left.php' ), true );
			}  
		} elseif(empty($component[2]) && !is_archive()){
			if($cap->sidebar_position == "left" || $cap->sidebar_position == __("left",'cc')  
				|| $cap->sidebar_position == "left and right" || $cap->sidebar_position == __("left and right",'cc') ){
				locate_template( array( 'sidebar-left.php' ), true );
			}    
	  	} else {
             if($cap->archive_template == __('left', 'cc') || $cap->archive_template == __("left and right",'cc')){
                locate_template( array( 'sidebar-left.php' ), true );
            }
        }
	}

	/**
	 * footer: add the sidebar and their default widgets to the right sidebar
	 * 
	 * located: footer.php do_action( 'sidebar_left' )
	 *
	 * @package Custom Community
	 * @since 1.8.3
	 */	
	function sidebar_right(){
		global $cap, $post;
        
        if(isset($post)){
            $tmp = get_post_meta( $post->ID, '_wp_page_template', true );

            if( $tmp == 'full-width.php' || $tmp =='tpl-search-full-width.php' || $tmp == 'left-sidebar.php' || $tmp == '_pro/tpl-left-sidebar.php')
                return;
            if( $tmp == 'left-and-right-sidebar.php' || $tmp == 'right-sidebar.php' 
                || $tmp == '_pro/tpl-left-and-right-sidebar.php' || $tmp == '_pro/tpl-search-right-and-left-sidebar.php'
                || $tmp == '_pro/tpl-right-sidebar.php' || $tmp == '_pro/tpl-search-right-sidebar.php'){
                locate_template( array( 'sidebar.php' ), true );
                return;		
            }
        }

        $component = explode('-',$this->detect->tk_get_page_type());
		if(!empty($component[2])){	
			if($component[2] == 'groups' && !empty($component[3])) {
				if($cap->bp_groups_sidebars == 'right' || $cap->bp_groups_sidebars == __('right','cc')  || $cap->bp_groups_sidebars == 'left and right' || $cap->bp_groups_sidebars == __('left and right','cc')  ){
					locate_template( array( 'groups/single/group-sidebar-right.php' ), true );
				} elseif(($cap->bp_groups_sidebars == "default" || $cap->bp_groups_sidebars == __("default",'cc') ) 
					&& ($cap->sidebar_position == "right" || $cap->sidebar_position == __("right",'cc') ) 
					|| ($cap->sidebar_position == "left and right" || $cap->sidebar_position == __("left and right",'cc') ) 
					&& ($cap->bp_groups_sidebars == "default" || $cap->bp_groups_sidebars == __("default",'cc') )){
					locate_template( array( 'sidebar.php' ), true );
				}
			} elseif($component[2] == 'profile' && !empty($component[3])) {
				if($cap->bp_profile_sidebars == 'right' || $cap->bp_profile_sidebars == __('right','cc')  
					|| $cap->bp_profile_sidebars == 'left and right' || $cap->bp_profile_sidebars == __('left and right','cc')  ){
					locate_template( array( 'members/single/member-sidebar-right.php' ), true );
				} elseif( ($cap->bp_profile_sidebars == "default" || $cap->bp_profile_sidebars == __("default",'cc') ) 
					&& ($cap->sidebar_position == "right" || $cap->sidebar_position == __("right",'cc') ) 
					|| ($cap->sidebar_position == "left and right" || $cap->sidebar_position == __("left and right",'cc') ) 
					&& ($cap->bp_profile_sidebars == "default" || $cap->bp_profile_sidebars == __("default",'cc') )){
					locate_template( array( 'sidebar.php' ), true );
				}
			} else if($cap->sidebar_position == "right" || $cap->sidebar_position == __("right",'cc')  
				|| $cap->sidebar_position == "left and right" || $cap->sidebar_position == __("left and right",'cc') ){
				locate_template( array( 'sidebar.php' ), true );
			}     
		} elseif(empty($component[2]) && !is_archive()) {
			if($cap->sidebar_position == "right" || $cap->sidebar_position == __("right",'cc')  
                || $cap->sidebar_position == "left and right" || $cap->sidebar_position == __("left and right",'cc') ){
				locate_template( array( 'sidebar.php' ), true );
			}    
  		} else {
            if($cap->archive_template == __("right",'cc') || $cap->archive_template == __("left and right",'cc')){
                locate_template( array( 'sidebar.php' ), true );
            }
        }
		
	}
	
	/**
	 * footer: add the buddypress default login widget to the right sidebar
	 * 
	 * located: footer.php do_action( 'bp_inside_after_sidebar' )
	 *
	 * @package Custom Community
	 * @since 1.8.3
	 */	
	function login_sidebar_widget(){
		global $cap;
	
		if(defined('BP_VERSION')) { if(($cap->login_sidebar != 'off' && $cap->login_sidebar != __('off','cc') ) || $cap->login_sidebar == false){ cc_login_widget();}}
	
	}
	

	/**
	 * homepage: add the latest 3 posts to the default homepage in mouse-over magazine style
	 * 
	 * located: index.php do_action( 'bp_before_blog_home' )
	 *
	 * @package Custom Community
	 * @since 1.8.3
	 */	
	function default_homepage_last_posts(){
		global $cap;
		
		if( $cap->preview == true  
			|| $cap->default_homepage_last_posts == 'show' 
			|| $cap->default_homepage_last_posts == __('show','cc') ) {
			$args = array(
				'amount' => '3',
		 	);
				
			echo '<div class="default-homepage-last-posts">'.cc_list_posts($args).'</div>'; 
		}
	}
	

	/**
	 * check if to use content or excerpt and the excerpt length
	 * 
	 * located: multiple places
	 * 
	 * @package Custom Community
	 * @since 1.8.3
	 */	
	function excerpt_on(){
		global $cap;
	
		if($cap->excerpt_on != 'content'){
			add_filter('excerpt_length', 'cc_excerpt_length');
			the_excerpt();
		} else {
			the_content( __( 'Read the rest of this entry &rarr;', 'cc' ) ); 
		}
	}
	

	/**
	 * groups home: add the sidebars and their default widgets to the groups header
	 * 
	 * located: grous/home.php do_action( 'bp_before_group_home_content' )
	 *
	 * @package Custom Community
	 * @since 1.8.3
	 */	
	function before_group_home_content(){
		global $cap;
		if( $cap->bp_groups_header == false 
			|| $cap->bp_groups_header == 'on' 
			|| $cap->bp_groups_header == __('on','cc') ):?>
			<div id="item-header">
                <div class="row-fluid">
                    <?php if( ! dynamic_sidebar( 'groupheader' )) : ?>
                     <?php locate_template( array( 'groups/single/group-header.php' ), true ) ?>
                    <?php endif; ?>

                    <?php if (is_active_sidebar('groupheaderleft') ){ ?>
                        <div class="widgetarea cc-widget span4">
                        <?php dynamic_sidebar( 'groupheaderleft' )?>
                        </div>
                    <?php } ?>
                    <?php if (is_active_sidebar('groupheadercenter') ){ ?>
                        <div class="<?php if(!is_active_sidebar('groupheaderleft')) { echo 'group-header-left'; } ?> widgetarea cc-widget span4">
                        <?php dynamic_sidebar( 'groupheadercenter' ) ?>
                        </div>
                    <?php } ?>
                    <?php if (is_active_sidebar('groupheaderright') ){ ?>
                        <div class="widgetarea cc-widget cc-widget-right span4">
                        <?php dynamic_sidebar( 'groupheaderright' ) ?>
                        </div>
                    <?php } ?>
                </div>
			</div>
		<?php else:?>
			<div id="item-header">
				<h2><a href="<?php bp_group_permalink() ?>" title="<?php bp_group_name() ?>"><?php bp_group_name() ?></a></h2>
			</div>
		<?php endif;?>
		<?php if($cap->bp_default_navigation == true){?>
			<div id="item-nav">
				<div class="item-list-tabs no-ajax" id="object-nav">
					<ul>
						<?php bp_get_options_nav() ?>
			
						<?php do_action( 'bp_group_options_nav' ) ?>
					</ul>
				</div>
			</div><!-- #item-nav -->
		<?php }
	}	

	/**
	 * members home: add the sidebars and their default widgets to the members header
	 * 
	 * located: members/home.php do_action( 'bp_before_member_home_content' )
	 *
	 * @package Custom Community
	 * @since 1.8.3
	 */	
	function before_member_home_content(){
		global $cap;

		if($cap->bp_profile_header == false || $cap->bp_profile_header == 'on' || $cap->bp_profile_header == __('on','cc') ): ?>
			<div id="item-header">
				<?php if( ! dynamic_sidebar( 'memberheader' )) : ?>
					<?php locate_template( array( 'members/single/member-header.php' ), true ) ?>
				<?php endif; ?>
				
				<div class="clear"></div>
				
				<?php if (is_active_sidebar('memberheaderleft') ){ ?>
					<div class="widgetarea cc-widget span4">
					<?php dynamic_sidebar( 'memberheaderleft' )?>
					</div>
				<?php } ?>
				<?php if (is_active_sidebar('memberheadercenter') ){ ?>
					<div class="<?php if(!is_active_sidebar('memberheaderleft')) { echo 'style="group-header-left'; } ?> widgetarea cc-widget span4">
					<?php dynamic_sidebar( 'memberheadercenter' ) ?>
					</div>
				<?php } ?>
				<?php if (is_active_sidebar('memberheaderright') ){ ?>
					<div class="widgetarea cc-widget cc-widget-right span4">
					<?php dynamic_sidebar( 'memberheaderright' ) ?>
					</div>
				<?php } ?>
			</div>
		<?php else:?>
			<div id="item-header">
				<h2 class="fn"><a href="<?php bp_user_link() ?>"><?php bp_displayed_user_fullname() ?></a> <span class="highlight">@<?php bp_displayed_user_username() ?> <span>?</span></span></h2>
			</div>
		<?php endif;?>
			
		<?php if($cap->bp_default_navigation == true){?>
		<div id="item-nav">
			<div class="item-list-tabs no-ajax" id="object-nav">
				<ul>
					<?php bp_get_displayed_user_nav() ?>
		
					<?php do_action( 'bp_member_options_nav' ) ?>
				</ul>
			</div>
		</div><!-- #item-nav -->
		<?php }
	}
	

	/**
	 * login page: overwrite the login css by adding it to the login_head
	 * 
	 * located: login.php do_action( 'login_head' )
	 *
	 * @package Custom Community
	 * @since 1.8.3
	 */
	function custom_login() { 
		global $cap; ?> 
		<style type="text/css">
		
		.login h1 a {
			<?php if($cap->bg_loginpage_img){ ?>
				background-image: url('<?php echo $cap->bg_loginpage_img; ?>') !important;
				background-repeat: no-repeat;
				height:<?php echo $cap->login_logo_height; ?>px;
			<?php } ?>
			clear: both;
		}
		
		<?php if($cap->bg_loginpage_body_img || $cap->bg_loginpage_body_color){ ?>
			body, body.login {
				<?php if($cap->bg_loginpage_body_img){ ?>
 					background-image: url('<?php echo $cap->bg_loginpage_body_img; ?>');
				<?php } ?>
				<?php if($cap->bg_loginpage_body_color){ ?>
					background-color: #<?php echo $cap->bg_loginpage_body_color; ?>;
				<?php } ?>
			}
		<?php } ?>
		
		<?php if($cap->bg_loginpage_body_color){ ?>
			body {
				color:#<?php echo $cap->bg_loginpage_body_color; ?>;
			}
		<?php } ?>
		#login{
		    margin: auto;
    		padding-top: 30px;
		}
		.login #nav a {
			color:#777 !important;
		}
		.login #nav a:hover {
			color:#777 !important;
		}
		.updated, .login #login_error, .login .message {
			background: none;
			color:#777;
			border-color:#888;
		}
		#lostpasswordform {
			border-color:#999;
		}
		<?php if($cap->bg_loginpage_backtoblog_fade_1 && $cap->bg_loginpage_backtoblog_fade_2){ ?>
			#backtoblog {
				background: -moz-linear-gradient(center bottom , #<?php echo $cap->bg_loginpage_backtoblog_fade_1; ?>, #<?php echo $cap->bg_loginpage_backtoblog_fade_2; ?>) repeat scroll 0 0 transparent;
			}
		<?php } ?>
		</style>
	<?php 
	}
	
	/**
	 * check if the class 'home' exists in the body_class if buddypress is activated.
	 * if not, add class 'home' or 'bubble' if cc is deactivated 
	 * 
	 * do_action( 'body_class' )
	 *
	 * @package Custom Community
	 * @since 1.8.3
	 */	
	function home_body_class($classes){
	
		if(defined('BP_VERSION')){
			if( !in_array( 'home', $classes ) ){
				if ( is_front_page() )
				$classes[] = 'home';
			}
		}
		
		if(is_home()){
			$classes[] = 'bubble';
		}
		
		return $classes;
	
	}

	/**
	 * Will add bubble class
	 * 
	 * do_action( 'body_class' )
	 *
	 * @package Custom Community
	 * @since 1.9
	 */
	function posts_lists_body_class($classes){
		if(is_archive()){
			$classes[] = 'bubble';
		}
		
		return $classes;
	}
}