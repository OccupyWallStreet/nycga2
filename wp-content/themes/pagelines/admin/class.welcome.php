<?php 
/**
 * PageLines Welcome (class)
 *
 * This generates and returns the Welcome Page of the theme's Global Settings
 * 
 */
class PageLinesWelcome {
	
	/**
     * PHP5 Constructor
     */
	function __contruct(){}

	/**
     * Get Welcome
     *
     * Pull all of the components together and returns them via the 'pagelines_welcome_intro' filter
     *
     * @uses        get_intro
     * @uses        get_plugins_billboard
     *
     * @internal    uses 'pagelines_welcome_finally' filter - text at the end of the welcome page
     *
     * @return      mixed|void
     */
	function get_welcome(){

		$dash = new PageLinesDashboard;
		
		// PageLines Plus
		$args = array(
			'title'			=> __( 'Some Tips To Get You Started', 'pagelines' ),
			'data'			=> $this->welcome_array(), 
			'icon'			=> PL_ADMIN_ICONS . '/light-bulb.png', 
			'excerpt-trim'	=> false
		); 

		$view = $this->get_welcome_billboard();
		
		$view .= $dash->wrap_dashboard_pane('tips', $args);
		
		$view .= $this->getting_started_video();

		$args = array(
			'title'			=> __( 'Core WordPress Graphical/Functional Support', 'pagelines' ),
			'data'			=> $this->get_welcome_plugins(), 
			'icon'			=> PL_ADMIN_ICONS . '/extend-plugins.png', 
			'excerpt-trim'	=> false, 
			'align'			=> 'right', 
			'btn-text'		=> 'Get It', 
			'target'		=> 'new'
		); 

		$view .= $this->get_support_banner();

		$view .= $dash->wrap_dashboard_pane('support-plugins', $args);

		return apply_filters('pagelines_welcome_intro', $view);
	}
	
	function getting_started_video(){
		ob_start();
		?>
		<div class="admin_billboard">
			<div class="admin_billboard_pad fix">
					<h3 class="admin_header_main"><?php _e( 'Video - Build A Site in 10 Minutes', 'pagelines' ); ?></h3>
					<div class='admin_billboard_text'>
						<?php _e( 'In this video <a href="http://bearded-avenger.com/">Nick</a> shows you how to build a beautiful site in 10 minutes with PageLines', 'pagelines' ); ?>
					</div>
			</div>
		</div>
		<iframe src="http://player.vimeo.com/video/44265010" width="100%" height="400" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
		<?php 
		
		return ob_get_clean();
	}
	
	function welcome_array(){
		
		$data = array(
			'story1'	=> array(
				'title'	=> __( 'The First Rule', 'pagelines' ), 
				'text'	=> __( "It's time we introduce you to the first rule.  The first rule of PageLines is that you come first. We truly appreciate your business and support.", 'pagelines' ), 
				'img'	=> PL_ADMIN_ICONS . '/first-rule.png'
			), 
			'story3'	=> array(
				'title'	=> __( 'Drag &amp; Drop Template Setup', 'pagelines' ), 
				'text'	=> sprintf( __( "Check out the <a href='%s'>Template Setup panel</a>! Using drag and drop you can completely control the appearance of your templates. Learn more in the <a href='http://www.pagelines.com/wiki/'>docs</a>.", 'pagelines' ), admin_url(PL_TEMPLATE_SETUP_URL) ), 
				'img'	=> PL_ADMIN_ICONS . '/dash-drag-drop.png'
			),
			'story4'	=> array(
				'title'	=> __( 'Set Up Your Extensions', 'pagelines' ), 
				'text'	=> __( "To maximize PageLines you're gonna need some extensions. Head over to the extensions page to get supported plugins and learn about extensions in the Store and Plus.", 'pagelines' ), 
				'img'	=> PL_ADMIN_ICONS . '/dash-plug.png'
			),
			'spprt'	=> array(
				'title'	=> __( 'Get Fast Support', 'pagelines' ), 
				'text'	=> __( "For help getting started, we offer our customers tons of support including comprehensive <a href='http://www.pagelines.com/wiki/' target='_blank'>docs</a>, and an active, moderated <a href='http://www.pagelines.com/forum/' target='_blank'>forum</a>.", 'pagelines' ), 
				'img'	=> PL_ADMIN_ICONS . '/dash-light-bulb.png'
			),
			'opts'	=> array(
				'title'	=> __( 'Site-Wide Vs. Page-by-Page Options', 'pagelines' ), 
				'text'	=> __( "PageLines is completely set up using a combination of site-wide and page-by-page options. Configure your site wide settings in the 'site options' panel, and setup your page by page options on individual pages, and in the 'page options' panel, which is used to set defaults and manage multiple post pages (like your blog).", 'pagelines' ), 
				'img'	=> PL_ADMIN_ICONS . '/dash-opts.png'
			),
			'widgets'	=> array(
				'title'	=> __( 'Menus and Widgets', 'pagelines' ), 
				'text'	=> __( "PageLines makes use of WordPress functionality to help you manage your site faster and better. Specifically, you'll be using WP menus and widgets so you may want to familiarize yourself with those interfaces as well.", 'pagelines' ), 
				'img'	=> PL_ADMIN_ICONS . '/dash-setting.png'
			),
		);
		
		return $data;
		
	}
	

	/**
     * Get Intro
     *
     * Includes the 'welcome.php' file from Child-Theme's root folder if it exists.
     *
     * @uses    default_headers
     *
     * @return  string
     */
	function get_intro( $o ) {
		
		if ( is_file( get_stylesheet_directory() . '/welcome.php' ) ) {
			
			ob_start();
				include( get_stylesheet_directory() . '/welcome.php' );
			$welcome =  ob_get_clean();	
			
			$a = array();
			
			if ( is_file( get_stylesheet_directory() . '/welcome.png' ) )
				$icon = get_stylesheet_directory_uri() . '/welcome.png';
			else
				$icon =  PL_ADMIN_ICONS . '/welcome.png';
			$a['welcome'] = array(
				'icon'			=> $icon,
				'hide_pagelines_introduction'	=> array(
					'type'			=> 'text_content',
					'flag'			=> 'hide_option',
					'exp'			=> $welcome
				)
			);		
		$o = array_merge( $a, $o );
		}
	return $o;
	}

	/**
     * Get Welcome Billboard
     *
     * Used to produce the content at the top of the theme Welcome page.
     *
     * @uses        CHILD_URL (constant)
     * @internal    uses 'pagelines_welcome_billboard' filter
     *
     * @return      mixed|void
     */
	function get_welcome_billboard(){
		
		ob_start();
		?>
		
		<div class="admin_billboard">
			<div class="admin_billboard_pad fix">
					<h3 class="admin_header_main"><?php _e( 'Getting Started with PageLines', 'pagelines' ); ?></h3>
					<div class='admin_billboard_text'>
						<?php _e( 'Congratulations and Welcome! Here are a few tips to get you started...', 'pagelines' ); ?>
					</div>
			</div>
		</div>
		<?php 
		
		$bill = ob_get_clean();
		
		
		return apply_filters('pagelines_welcome_billboard', $bill);
	}
	

	function get_support_banner(){
		
		ob_start();
		?>

		<div class="admin_billboard">
			<div class="admin_billboard_pad fix">
					<h3 class="admin_header_main"><?php _e( 'Core Plugin Support', 'pagelines' ); ?></h3>
					<div class='admin_billboard_text'>
						<?php _e( 'These common WordPress plugins that have special support within the framework', 'pagelines' ); ?>
					</div>
			</div>
		</div>
		<?php 

		$banner = ob_get_clean();


		return $banner;
	}

	function get_welcome_plugins(){
		$plugins = array(
			'postorder'	=> array(
				'title'			=> __( 'Post Types Order', 'pagelines' ),
				'link'			=> 'http://wordpress.org/extend/plugins/post-types-order/', 
				'text'			=> __( 'Allows you to re-order custom post types like features and boxes.', 'pagelines' ),
				'btn-text'		=> 'Get On WordPress.org'
			),
			'specialrecent'	=> array(
				'title'			=> __( 'Special Recent Posts', 'pagelines' ),
				'link'			=> 'http://wordpress.org/extend/plugins/special-recent-posts/', 
				'text'			=> __( 'A sidebar widget that shows your most recent blog posts and their thumbs.', 'pagelines' ),
				'btn-text'		=> 'Get On WordPress.org'
			),
			'disqus'	=> array(
				'title'			=> __( 'Disqus Comments', 'pagelines' ),
				'link'			=> 'http://wordpress.org/extend/plugins/disqus-comment-system/', 
				'text'			=> __( 'Improve your commenting system.', 'pagelines' ),
				'btn-text'		=> 'Get On WordPress.org'
			),
			'cforms'	=> array(
				'title'			=> __( 'CForms', 'pagelines' ),
				'link'			=> 'http://www.deliciousdays.com/cforms-plugin/', 
				'text'			=> __( 'Advanced contact forms that can be used for creating mailing lists, etc.', 'pagelines' ),
				'btn-text'		=> 'Get On DeliciousDays.com'
			),
			'wp125'	=> array(
				'title'			=> __( 'WP125', 'pagelines' ),
				'link'			=> 'http://wordpress.org/extend/plugins/wp125/', 
				'text'			=> __( 'Used to show 125px by 125px ads or images in your sidebar(Widget).', 'pagelines' ),
				'btn-text'		=> 'Get On WordPress.org'
			),
			'flickrrss'	=> array(
				'title'			=> __( 'FlickrRSS Images', 'pagelines' ),
				'link'			=> 'http://eightface.com/wordpress/flickrrss/', 
				'text'			=> __( 'Shows pictures from your Flickr Account (Widget &amp; Carousel Section).', 'pagelines' ),
				'btn-text'		=> 'Get On EightFace.com'
			),
			'nextgen'	=> array(
				'title'			=> __( 'NextGen-Gallery', 'pagelines' ),
				'link'			=> 'http://wordpress.org/extend/plugins/nextgen-gallery/', 
				'text'			=> __( 'Allows you to create image galleries with special effects (Carousel Section).', 'pagelines' ),
				'btn-text'		=> 'Get On WordPress.org'
			),
			'pagenavi'	=> array(
				'title'			=> __( 'Wp-PageNavi', 'pagelines' ),
				'link'			=> 'http://wordpress.org/extend/plugins/wp-pagenavi/', 
				'text'			=> __( 'Creates advanced <strong>paginated</strong> post navigation.', 'pagelines' ),
				'btn-text'		=> 'Get On WordPress.org'
			)
		);
		
		return apply_filters('pagelines_welcome_plugins', $plugins);
	}


	
}
