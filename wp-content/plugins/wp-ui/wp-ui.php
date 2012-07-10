<?php
/*
Plugin Name: WP UI - Tabs, accordions and more. 
Plugin URI: http://kav.in/wp-ui-for-wordpress
Description: Easily add Tabs, Accordion, Collapsibles to your posts. With 14 fresh Unique CSS3 styles and multiple jQuery UI custom themes.
Author:	Kavin
Version: 0.8.2
Author URI: http://kav.in

Copyright (c) 2011 Kavin ( http://kav.in/contact )

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/*
Script: WP UI for Wordpress 

About: Version
	0.8.1

About: License 
	The PHP files are licensed under GPLv2. Javascript files as specified in their file header ( Or their *.dev.js ). Included scripts come under their respective licenses.

About: Wordpress versions supported.
	This version is supported from WordPress 3.1 through 3.3.1. 

About: Download
	Download the plugin from wordpress Extend, http://wordpress.org/extend/plugins/wp-ui

About: Support
	Active support forum is available at http://kav.in/forum .

About: Copyright
	Copyright 2012 - Kavin Amuthan

*/



define( 'WPUI_VER', '0.8.2' );

// $opts = get_option( 'wpUI_options');
// echo '<pre>';
// print_r( $opts );
// echo '</pre>';

global $wp_ui, $wpui_bleeding;

$wp_ui = new wpUI;



class wpUI {
	private $plugin_details,
	 		$options,
			$wpuiPosts;
	
	
	public function __construct() {
		$this->wpUI();
	} // END fn __construct.


	public function wpUI() {
		
		// Register the default options on activation.
		register_activation_hook( __FILE__ , array(&$this, 'set_defaults'));

		// Get the options.
		$this->options = get_option('wpUI_options', array());
	
	
		// Bleeding edge - Experimental
		global $wpui_bleeding;
		$wpui_bleeding = ( isset( $this->options[ 'bleeding_edge' ] ) &&
		 					$this->options[ 'bleeding_edge' ] == 'on' ) ?
		 					true : false;
		$this->bleeding = $wpui_bleeding;
			
		
		// Translation.
		add_action('init', array(&$this, 'load_plugin_loc'));

		
		// Custom CSS query.
		add_filter( 'query_vars', array( &$this, 'wpui_add_query') );
		add_action( 'template_redirect', array( &$this, 'wpui_add_queries') );		

		

		// Shortcodes.
		add_shortcode('wptabs',			array(&$this, 'sc_wptabs'));
		add_shortcode('wptabposts',		array(&$this, 'sc_wptabposts'));
		add_shortcode( 'wptabtitle', 	array(&$this, 'sc_wptabtitle'));
		add_shortcode( 'wptabcontent', 	array(&$this, 'sc_wptabcontent'));
		add_shortcode( 'wpspoiler',		array(&$this, 'sc_wpspoiler'));
		add_shortcode( 'wpdialog',		array(&$this, 'sc_wpdialog'));
		add_shortcode( 'wploop',		array(&$this, 'sc_wpui_loop'));
		add_shortcode( 'wpuifeeds',		array(&$this, 'sc_wpuifeeds'));
		add_shortcode( 'wpuicomp',		array( &$this, 'sc_wpuicomp' ) );
		
		
		// Feeds support.
		include_once( ABSPATH . WPINC . '/feed.php' );
		
		/**
		 * Helper functions !!
		 */
		include_once( 'inc/wpui-helpers.php' );


		/**
		 * Load scripts and styles.
		 */
		if ( ! is_admin() ) {
			add_action('wp_enqueue_scripts', array(&$this, 'plugin_viewer_scripts'), 999);
			add_action('wp_print_styles', array(&$this, 'plugin_viewer_styles'), 999 );
		}
			
		
		/**
		 *  Insert the editor buttons and help panels.
		 */
		if ( is_admin() ) include_once( 'inc/wpuimce/wpui_mce.php' );
		
		if ( function_exists( 'gd_info' ) )
			include_once( 'inc/class-imager.php' );


		/**
		 * Posts module. 
		 * @todo move into modules dir.
		 */
		include_once( 'inc/class-wpui-posts.php' );
		$this->wpuiPosts = new wpuiPosts();
		
		/**
		 * 	WP UI options module and the page.
		 */
		if ( is_admin() ) require_once( wpui_dir( 'admin/wpUI-options.php' ));
		
		if ( ! is_admin() ) include_once( wpui_dir( 'inc/wpui-buttons.php' ));


		if ( isset( $this->options[ 'alt_sc' ] ) && $this->options[ 'alt_sc' ] == 'on' )
		{
			// alternative shortcodes.
			add_shortcode( 'tabs', array(&$this, 'sc_wptabs'));
			add_shortcode( 'tabname', array(&$this, 'sc_wptabtitle'));
			add_shortcode( 'tabcont', array(&$this, 'sc_wptabcontent'));
			add_shortcode( 'spoiler', array(&$this, 'sc_wpspoiler'));
			add_shortcode( 'dialog', array(&$this, 'sc_wpdialog'));
		}
		
		
		// Related posts widget. to be removed.
		if ( isset( $this->options[ 'enable_post_widget' ] ) && ( $this->options['enable_post_widget'] == 'on' ) ) {
			add_shortcode( 'wpui_related_posts', array( $this->wpuiPosts, 'insert_related_posts' ) );
			add_filter( 'the_content', array( $this->wpuiPosts, 'put_related_posts' ));
		}
		
		
		$this->plugin_dir = plugin_dir_path( __FILE__ );
		
		if ( isset( $this->options[ 'enable_widgets' ] ) && $this->options[ 'enable_widgets' ] == 'on' ) {
/*			$widVer = ( floatval( get_bloginfo( 'version' ) ) >= 3.3 ) ? '-3.3' : '';*/
			include_once( $this->plugin_dir . 'inc/widgets.php' );			
		}


		/**
		 * Scan and load modules.
		 */
		$this->load_modules();		
		
	} //END method wpUI


	/**
	 * Test stuff.
	 */
	public function test_ground() {}

	
	/**
	 * 	Load the wpUI text domain.
	 */
	public function load_plugin_loc() {
		load_plugin_textdomain( 'wp-ui', false, '/wp-ui/languages/' );		
	}

	public function plugin_viewer_scripts() {
		if ( ! $this->get_conditionals() ) return;
		
		$plugin_url = wpui_url();		
		
		$js_dir = $plugin_url . '/js/';
		
		if ( ! is_admin()
			&& ( isset( $this->options['jquery_disabled'] ) ||
			$this->options['jquery_disabled'] == 'off' ) ) {
			
			/**
			 * In case of conflicts, alternate between the below jQuery 
			 * includes and work one out.
			 */			
			// wp_deregister_script( 'jquery' );
			
			// // These are local jQuery and jQuery UI, from the plugin dir.
			// wp_enqueue_script( 'jquery', $js_dir . 'jquery.min.js' );
			// wp_enqueue_script( 'jquery-ui', $js_dir . 'jquery-ui.min.js' );
			
			// // Load from Google CDN.
			// wp_enqueue_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js', false, '1.6.1');
			// wp_enqueue_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js', false, '1.4.2');
			
			// Let's use Wordpress bundled jQuery.
			// wp_enqueue_script( 'jquery' );
			
			// Will be gradually moved to wordpress bundled UI ( >= 3.3)
			wp_deregister_script( 'jquery-ui' );
			wp_register_script('jquery-ui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js', array( 'jquery' ), '1.8.16' );

			wp_register_script('jquery-easing', $plugin_url . '/js/jquery.easing.1.3.js', array( 'jquery', 'jquery-ui') );			

		}
		
		/**
		 * On demand loading. New feature, use with caution.
		 */
		if ( isset( $this->options[ 'load_scripts_on_demand' ] ) &&
		 	$this->options[ 'load_scripts_on_demand' ] == 'on' ) {
			wp_enqueue_script( 'wp-ui-async', wpui_url( 'js/async.js' ), array( 'jquery', 'jquery-ui' ) );
			wp_localize_script( 'wp-ui-async', 'wpUIOpts', $this->get_script_options());
			return;
		}
		
		wp_enqueue_script( 'wp-ui-min', $plugin_url . 'js/wp-ui.js', array( 'jquery', 'jquery-ui'), WPUI_VER );
		if ( $this->bleeding ) {
			wp_enqueue_script( 'wp-ui-alt', $plugin_url . 'js/qtabs.js', array( 'jquery', 'jquery-ui'), WPUI_VER );
		}
		wp_localize_script( 'wp-ui-min', 'wpUIOpts', $this->get_script_options());
		
	}
	
	/**
	 * Check if scripts/styles can be loaded.
	 * 
	 * @uses eval 
	 * @return boolean
	 */
	private function get_conditionals() {
		$script_needed = true;
		if ( isset( $this->options ) &&
		 	! empty( $this->options[ 'script_conditionals' ] ) ) {
			$scrcon = $this->options[ 'script_conditionals' ];			
			$script_needed = ( stripos( $scrcon , 'return') !== FALSE ) ?
			 						$scrcon :
		 							eval( 'return ' . $scrcon . ';');
		}
		return $script_needed;
	}


	/**
	 * Script options
	 * 
	 * @todo remove.
	 * @return array options for javascript
	 */
	public function get_script_options() {
		$wpui_opts = array(
			'wpUrl'           =>	get_bloginfo('url'),
			'pluginUrl'       =>	plugins_url('/wp-ui/'),
			'enableTabs'      =>	isset($this->options['enable_tabs']) ? $this->options['enable_tabs'] : '',
			'enableAccordion' =>	isset($this->options['enable_accordion']) ? $this->options['enable_accordion'] : '',
			'enableSpoilers'  =>	isset($this->options['enable_spoilers']) ?	$this->options['enable_spoilers'] : '' ,	
			'enableDialogs'	  =>	isset($this->options['enable_dialogs']) ?	$this->options['enable_dialogs'] : '' ,	
			'enablePagination' =>	isset($this->options['enable_pagination']) ?	$this->options['enable_pagination'] : '' ,
			'tabsEffect'      =>	isset($this->options['tabsfx']) ? $this->options['tabsfx'] : '',
			'effectSpeed'     =>	isset($this->options['fx_speed']) ? $this->options['fx_speed'] : '',
			'accordEffect'    =>	isset($this->options['tabsfx']) ? $this->options['tabsfx'] : '',
			'alwaysRotate'    =>	isset($this->options['tabs_rotate']) ? $this->options['tabs_rotate'] : '',
			'tabsEvent'  	  =>	isset($this->options['tabs_event']) ? $this->options['tabs_event'] : '',
			'collapsibleTabs'  =>	isset($this->options['collapsible_tabs']) ? $this->options['collapsible_tabs'] : '',
			'accordEvent'  	  =>	isset($this->options['accord_event']) ? $this->options['accord_event'] : '',
			'topNav'          =>	isset($this->options['topnav']) ? $this->options['topnav'] : '',
			'accordAutoHeight'=>	isset($this->options['accord_autoheight']) ? $this->options['accord_autoheight'] : '',
			'accordCollapsible'=>	isset($this->options['accord_collapsible']) ? $this->options['accord_collapsible'] : '',
			'accordEasing'		=>	isset( $this->options['accord_easing'] ) ? $this->options['accord_easing'] : '',
			'mouseWheelTabs'	=>	isset( $this->options['mouse_wheel_tabs'] ) ? $this->options['mouse_wheel_tabs'] : '',
			'bottomNav'       =>	isset($this->options['bottomnav']) ? $this->options['bottomnav'] : '',
			'tabPrevText'     =>	isset($this->options['tab_nav_prev_text']) ? $this->options['tab_nav_prev_text'] : '',
			'tabNextText'     =>	isset($this->options['tab_nav_next_text']) ? $this->options['tab_nav_next_text'] : '',
			'spoilerShowText' =>	isset($this->options['spoiler_show_text']) ? $this->options['spoiler_show_text'] : '',
			'spoilerHideText' =>	isset($this->options['spoiler_hide_text']) ? $this->options['spoiler_hide_text'] : '',
			"cookies"			=>	isset( $this->options['use_cookies'] ) ? $this->options['use_cookies'] : '',
			"hashChange"		=> isset( $this->options['linking_history'] ) ? $this->options['linking_history'] : '',
			"docWriteFix"		=> isset( $this->options['docwrite_fix'] ) ? $this->options['docwrite_fix'] : '',
			'bleeding'			=>	isset( $this->options[ 'bleeding_edge' ] ) ?  $this->options[ 'bleeding_edge' ]  : 'off'
		);
		return $wpui_opts;
	}

	
	/**
	 * 	Output the plugin styles.
	 */
	public function plugin_viewer_styles() {
		if ( ! $this->get_conditionals() ) return;

		global $is_IE;
		$plugin_url = plugins_url('/wp-ui/');

		$wpuiCss3List = wpui_get_css3_styles_list();
		$jqui_c = wpui_get_custom_themes_list();
		$jqui_cs = wpui_get_custom_themes_list( true );
		
		
		/**
		 * 	Look if it's a css3 style, or try to load a jQuery theme.
		 */		
		if ( in_array( $this->options[ 'tab_scheme' ] , $wpuiCss3List ) )	{
			wp_enqueue_style('wp-ui', $plugin_url . 'wp-ui.css');
			wp_enqueue_style($this->options['tab_scheme'], $plugin_url . 'css/' . $this->options['tab_scheme'] . '.css');
			
		} elseif( $jqui_c && in_array( $this->options[ 'tab_scheme' ] , $jqui_c ) ) { 
			wp_enqueue_style( 'jquery-ui-wp-fix', $plugin_url . 'css/jquery-ui-wp-fix.css' );
			
			wp_enqueue_style( $this->options[ 'tab_scheme' ], $jqui_cs[ $this->options[ 'tab_scheme' ] ] );
			
		} else {
			// Sets the standard font size for jQuery UI themes,
			// to ensure compat with variety of wordpress themes. 
			wp_enqueue_style( 'jquery-ui-wp-fix', $plugin_url . 'css/jquery-ui-wp-fix.css' );
			
			// Load the jQuery UI theme from the Google CDN.
			wp_enqueue_style( 'jquery-ui-css-' . $this->options['tab_scheme'], 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.11/themes/' . $this->options['tab_scheme'] . '/jquery.ui.all.css');
		} 

		/**
		 * 	Load multiple styles - once that are selected on admin.
		 */
		if ( isset( $this->options[ 'load_all_styles' ] ) && 
			isset( $this->options[ 'selected_styles' ] ) && 
			$this->options[ 'load_all_styles'] == 'on' ) {
			if ( $this->options['selected_styles' ] != '' ) {
				$selStylesArr = json_decode( $this->options[ 'selected_styles' ], true );
				if ( is_array( $selStylesArr ) ) {
					$selQuery = implode( "|" , $selStylesArr );
					wp_enqueue_style( 'wpui-multiple' , $plugin_url . 'css/css.php?styles=' . $selQuery );
				}
			} else {
				wp_enqueue_style( 'wp-ui-all' , $plugin_url . 'css/wpui-all.css');
			}
		}
		// if ( $is_IE && $this->options['enable_ie_grad'] )
		// wp_enqueue_style( 'wp-tabs-css-bundled-all-IE' , $plugin_url . 'css/wpui-all-ie.css');	


		/**
		 * 	Load jQuery UI custom themes.
		 */
		if ( isset( $this->options['jqui_custom_themes'] ) && $this->options['jqui_custom_themes'] != '' ) {
			$jquithms = json_decode( $this->options[ 'jqui_custom_themes'] , true );
			foreach( $jquithms as $key=>$val ) {
				if ( $key !== $this->options[ 'tab_scheme' ] )
					wp_enqueue_style( $key, $val );		
			}
		}
			
		// Try a jQuery UI theme.
		// wp_enqueue_style( 'jquery-ui-css-flick' , 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.11/themes/flick/jquery.ui.all.css');
			
		/**
		 *	Load the additional CSS, if any has been input on the options page.		
		 */
		if ( $this->options['custom_css'] != '' )
			wp_enqueue_style( 'wpui-custom-css', get_bloginfo( 'url' ) . '/?wpui-css=css');
		
	} // END method plugin_viewer_styles()
	


	/**
	 * 	Set the defaults on plugin activation.
	 */
	function set_defaults() {
		// First install.
		if ( ! $this->options ) {
			$defaults = get_wpui_default_options();
			update_option( 'wpUI_options', $defaults );
		} else {
			// Append the new options.
			$oldopts = get_option( 'wpUI_options' );
			$newdefs = get_wpui_default_options();
			foreach ( $newdefs as $def=>$val ) {
				// Options not set.
				if ( ! isset( $oldopts[ $def ] ) ) {
					if ( is_array( $newdefs[ $def ] ) )
						$oldopts[ $def ] = array();
					$oldopts[ $def ] = $newdefs[ $def ];					
				}
			}
/*			$oldopts = array_merge( $oldopts, $newdefs );*/
			update_option( 'wpUI_options', $oldopts );
			$this->clean_up_post_meta();
		} // End if ( !this->options )
	} // END set defaults.

	/**
	 * Clean the old post meta, as the demand loading in done with jQuery.
	 * 
	 * @since 0.8
	 * @todo - remove in > 0.8.1
	 * 
	 */
	function clean_up_post_meta()
	{
		$get_posts = get_posts('numberposts=-1&post_type=post&post_status=any');
		
		foreach( $get_posts as $gps ) {
			delete_post_meta( $gps->ID, 'wp-ui-load' );
			delete_post_meta( $gps->ID, '_wp-ui-load' );
		}
		
	} // END function clean_up_post_meta


	/**
	 * 
	 * 	Add the shortcodes.
	 * 
	 */

	/**
	 * 	[wptabs] shortcode.	
	 */
	function sc_wptabs( $atts, $content = null) {
		extract(shortcode_atts(array(
			"type"			=>	'tabs',
			'style'			=>	$this->options['tab_scheme'],
			'effect'		=>	$this->options['tabsfx'],
			'speed'			=>	'600',
			// Tabs only options below
			'rotate'		=>	'', 
			'position'		=>	'top',
			'cat'			=>	'',
			'category_name'	=>	'',
			'mode'			=>	'horizontal',
			'listwidth'		=>	'',
			// Accordion only options below
			'active'		=>	false,
			'background'	=>	'true',
			'engine'		=>	'ui'
		), $atts));
		
		$output  = '';

		$scheme = $style;
		
		static $wpui_tabs_id = 0;
		$wpui_tabs_id++;
		
		$jqui_cust = isset( $this->options[ 'jqui_custom_themes' ] ) ? json_decode( $this->options[ 'jqui_custom_themes' ] , true ) : array();	
		
					
		if ( stristr( $style, 'wpui-' ) && ! isset( $jqui_cust[ $scheme ] ) ) {
			$style .= ' wpui-styles';
		} else {
			$style .= ' jqui-styles';
		}
		
		if ( $mode == 'vertical' ) {
			$style .= ' wpui-tabs-vertical';
		}
		
		if ( $listwidth != '' )
			$style .= ' listwidth-' . $listwidth;

		if ( $background == 'false' )
			$style .= ' wpui-no-background';
	
		// Default : tabs. Change type for accordion.
		// $class  = ($type == 'accordion') ? 'wp-accordion' : 'wp-tabs';
		
		if ( $type == 'accordion' ) {
			$class = 'wp-accordion';
			if ( $active && $active > 0 ) {
				$class .= ' acc-active-' . ( $active - 1 );
			}			
		} else {
			$class = ( $this->bleeding && $engine == 'wp-ui' ) ? 'ktabs' : 'wp-tabs';
		}
				
		$class .= ' ' . $style;
		$class .= ( $rotate == '' ) ? '' : ' tab-rotate-' . $rotate;
		$class .= ( $position == 'bottom' ) ? ' tabs-bottom' : '';

		$id = ( ( $type == 'accordion' ) ? 'wp-accordion-' : 'wp-tabs-' ) . $wpui_tabs_id;
		
		$output .= '<div id="' . $id . '" class="' . $class . '">' . do_shortcode($content) . '</div><!-- end div.wp-tabs -->';

		if ( $engine == 'wp-ui' ) {
			$output .= '<script type="text/javascript">';
			$output .= 'thisisIT' . $wpui_tabs_id . '  = setInterval( function() { ';
			$output .= "if ( typeof(jQuery.fn.ktabs) == 'function' ) {";			
			$output .= 'jQuery( "#wp-tabs-' . $wpui_tabs_id . '" ).ktabs({ elements : { header : ".wp-tab-title", content : ".wp-tab-content"}, animateSelect : false, mode : "' . $mode . '" ';
			$output .= ( $mode == 'vertical' ) ? ', buffer : { height : 40 } ' : '';
			$output .= ' });';
			$output .= 'clearInterval( thisisIT'  . $wpui_tabs_id .  ' ); }';
			$output .= '}, 300 );';
			$output .= '</script>';
		}
		
		return $output;
	} // END function sc_wptabs.

	
	/**
	 * Get posts with a custom loop.
	 */
	function sc_wpui_loop( $atts, $content=null )
	{
		extract( shortcode_atts( array(
			'get'				=>	'',
			'cat'				=>	'',
			'category_name'		=>	'',
			'tag'				=>	'',
			'number'			=>	'4',
			'exclude'			=>	'',
			'elength'			=>	$this->options['excerpt_length'],
			'before_post'		=>	'',
			'after_post'		=>	'',
			'num_per_page'		=>	FALSE,
			'template'			=>	'1'	
		), $atts ));
		
		if ( ( ! $cat || $cat == '' ) && ( ! $tag || $tag == '' ) && ( ! $get || $get == '' ) )
			return;
		
		if ( $cat != '' ) $tag = '';		

		// $wquery . '&number=' . $number . '&length=' . $elength
		$custom_loop = $this->wpuiPosts->wpui_get_posts( array( 
									'cat'		=>	$cat,
									'tag'		=>	$tag,
									'get'		=>	$get,
									'number'	=>	$number,
									'exclude'	=>	$exclude,
									'length'	=>	$elength								
								));
	
		$output = ''; 
		
		if ( ! $custom_loop ) {
			return "Please verify <code>[<span>wploop</span>]</code> arguments.";
		}
		
		if ( $num_per_page ) {
			$output .= '<div class="wpui-pages-holder" />';
			$output .= '<div class="wpui-page wpui-page-1">';		
			$num_page = 1;
		}
		
		$ptempl = ( isset( $this->options[ 'post_template_' . $template ] ) ) ?
					$this->options[ 'post_template_' . $template ] :
					$this->options[ 'post_template_1' ];


		$wpui_total_posts = count( $custom_loop );
		foreach( $custom_loop as $index=>$item ) {			
			$posts_passed = $index + 1;

			$tmpl = $this->wpuiPosts->replace_tags( $ptempl, $item );
			$output .= $before_post . $tmpl . $after_post;
		
			if( $num_per_page 
				&& ( ( $posts_passed % $num_per_page ) == 0 ) 
				&& ( $posts_passed != ( $wpui_total_posts ) )
				) {
				$num_page++;
				$output .= '</div><!-- end div.wpui-page -->';
				$output .= '<div class="wpui-page wpui-page-' . $num_page . '">';

			}			
		} // END foreach.
		
		if ( $num_page ) {
			$output .= '</div><!-- end wpui-page -->';
			$output .= '</div><!-- end wpui-pages-holder -->';
		}
		return $output;		
	} // END function sc_wpui_loop


	/**
	 *	Output tab sets of posts.
	 * 	[wptabposts]
	 * 
	 * @since 0.7
	 * @param $atts, $content
	 * @return shortcode handler.
	 */
	function sc_wptabposts( $atts, $content = null )
	{
		extract( shortcode_atts( array(
			'style'				=>	$this->options[ 'tab_scheme' ],
			'type'				=>	'tabs',
			'mode'				=>	'',
			'listwidth'			=>	'',
			'tab_names'			=>	'title',
			'effect'			=>	$this->options['tabsfx'],
			'speed'				=>	'600',
			'get'				=>	'',
			'cat'				=>	'',
			'category_name'		=>	'',
			'tag'				=>	'',
			'post_type'			=>	'',
			'post_status'		=>	'publish',
			'number'			=>	'4',
			'page'				=>	'',
			'exclude'			=>	'',
			'rotate'			=>	'',
			'elength'			=>	$this->options['excerpt_length'],
			'before_post'		=>	'',
			'after_post'		=>	'',
			'template'			=>	'1'
		), $atts ));
		
		
		if ( ( ! $cat || $cat == '' ) && ( ! $tag || $tag == '' ) && ( ! $get || $get == '' ) && ( $post_type == '' ))
			return;
		
		if ( $cat != '' ) $tag = '';		

		$my_posts = $this->wpuiPosts->wpui_get_posts( array( 
									'cat'			=>	$cat,
									'tag'			=>	$tag,
									'get'			=>	$get,
									'post_type'		=>	$post_type,
									'post_status'	=>	$post_status,
									'number'		=>	$number,
									'exclude'		=>	$exclude,
									'length'		=>	$elength								
								));

	
		$tab_names_arr = preg_split( '/\s?,\s?/i', $tab_names );
		
		$output = ''; 
		
		$each_tabs = '';
		
		if ( ! $my_posts ) {
			return "Please verify <code>[<span>wptabposts</span>]</code> arguments.";
		}
		
		$ptempl = ( isset( $this->options[ 'post_template_' . $template ] ) ) ?
					$this->options[ 'post_template_' . $template ] :
					$this->options[ 'post_template_1' ];
		
		foreach( $my_posts as $index=>$item ) {
			$tabs_count = $index + 1;
			if ( $tab_names == 'title' ) {
				$tab_name =  $item[ 'title' ];
			} elseif ( isset( $tab_names_arr ) && ( count( $tab_names_arr ) > 1 ) ) { 
				$tab_name = $tab_names_arr[ $index ];
			} else {
				$tab_name = $tabs_count;
			}
			
			$tmpl = $this->wpuiPosts->replace_tags( $ptempl , $item );
			$tab_content = $before_post . $tmpl . $after_post;
			$each_tabs .= do_shortcode( '[wptabtitle] ' . $tab_name . ' [/wptabtitle] [wptabcontent] ' . $tab_content  . ' [/wptabcontent]' . "\n" );
		} // END foreach.
		
		$wptabsargs = '';
		
		if ( $type != '' )
			$wptabsargs .= ' type="' . $type . '"';
		if ( $mode != '' )
			$wptabsargs .= ' mode="' . $mode . '"';
		
		if ( $listwidth != '' )
			$style .= ' listwidth-' . $listwidth;
		$wptabsargs .= ' style="' . $style . '"';
		if ( $rotate && $rotate != '' )
			$wptabsargs .= ' rotate="' . $rotate . '"';
		
		if ( $listwidth != '' )
			$wptabsargs .= ' listwidth-' . $listwidth;
			
		$output .= do_shortcode( '[wptabs' . $wptabsargs . '] ' . $each_tabs . ' [/wptabs]' );
		
		return $output;
		
	} // END function wptabposts

	
	
	/**
	 * 	[wptabtitle]
	 */
	function sc_wptabtitle( $atts, $content = null ) {
		extract(shortcode_atts(array(
			'header'	=>	'h3',
			'hclass'	=>	'wp-tab-title',
			'label'		=>	'text',
			'image_size'=>	'24,24',	
			'load'		=>	'',
			'post'		=>	'',
			'page'		=>	'',
			'cat'		=>	'',
			'category_name' => '',
			'tag'		=>	'',
			'tag_name'		=>	'',
			'number'	=>	'4',
			'exclude'	=>	'',
			'tag'		=>	'',
			'feed'		=>	'',
			'hide'		=>	"false",
			'elength'	=>	$this->options['excerpt_length'],
			'before_post'	=>	'',
			'after_post'	=>	'',
			'template'	=>	'1'
		), $atts));
		
		$tmpl = ( isset( $this->options[ 'post_template_' . $template ] ) ) ?
					$this->options[ 'post_template_' . $template ] :
					$this->options[ 'post_template_1' ];		
		
		if ( $hide == "true" ) $hclass .= ' wpui-hidden-tab';
		
		if( $post != '' ) {
			// No ajax load if a post is specified.
			$load = '';	
			$post_cont = $this->wpuiPosts->wpui_get_post( $post, $elength );
			
			if ( ! is_array( $post_cont ) ) :
				$post_content = $post_cont;
			else :
			$post_content = $before_post . $this->wpuiPosts->replace_tags( $tmpl, $post_cont ) . $after_post;
			endif;
			$output  = '<' . $header . ' class="' . $hclass . '">';
			$output .= do_shortcode( __( $content ) ) . '</' . $header . '>';
			$output .= do_shortcode( '[wptabcontent]' .  $post_content . '[/wptabcontent]');

		} elseif( $page != '' ) { 
			$load = '';
			$post_cont = $this->wpuiPosts->wpui_get_post( $page, $elength, 'page' );
			if ( ! is_array( $post_cont ) ) :
				$post_content = $post_cont;
			else :			
			$post_cont[ 'excerpt' ] = $post_cont[ 'content' ];
			$post_content = $before_post . $this->wpuiPosts->replace_tags( $tmpl, $post_cont ) . $after_post;
			$post_cont[ 'excerpt' ] = $post_cont[ 'content' ];
			endif;
			
			$output  = '<' . $header . ' class="' . $hclass . '">';
			$output .= do_shortcode( $content ) . '</' . $header . '>';
			$output .= do_shortcode( ' [wptabcontent] ' .  $post_content . ' [/wptabcontent] ');			
			
		} elseif( $cat != '' || $category_name != '' || $tag != '' || $tag_name != '' ) {
			$load = '';
			
			$get_cat_posts = $this->wpuiPosts->wpui_get_posts( array( 
									'cat'		=>	$cat,
									'category_name' => $category_name,
									'tag'		=>	$tag,
									'tag_name'	=>	$tag_name,
									'number'	=>	$number,
									'exclude'	=>	$exclude,
									'length'	=>	$elength								
									));
			
			// echo '<pre>';
			// print_r($get_cat_posts);
			// echo '</pre>';
			
			$posts_group = '';
			
			foreach( $get_cat_posts as $index=>$values ) {
				$posts_group .= $this->wpuiPosts->replace_tags( $tmpl, $values );	
			}
			
			$output = '<' . $header . ' class="' . $hclass . '">';
			$output .= do_shortcode( __( $content ) ) . '</' . $header . '>';
			$output .= do_shortcode( '[wptabcontent]' . $posts_group . '[/wptabcontent]' );			
			
		} elseif( $feed != '' ) { 
			$get_feeds = $this->wpuiPosts->wpui_get_feeds( array( 
							'url'		=>	$feed,
							'number'	=>	$number	
						));
			$feeds_list = '';
			foreach( $get_feeds as $index=>$item ) {
				$feeds_list .= $this->wpuiPosts->replace_tags( $tmpl, $item );
			}
			
			$output = '<' . $header . ' class="' . $hclass . '">';
			$output .= do_shortcode( __( $content ) ) . '</' . $header . '>';
			$output .= do_shortcode( '[wptabcontent]' . $feeds_list . '[/wptabcontent]' );			
			
		} elseif ( $load != '' ) {
			$output  = '<' . $header . ' class="' . $hclass . '">';
			$output .= '<a class="wp-tab-load" href="' . $load . '">';
			$output .= do_shortcode($content);
			$output .= '</a>';
			$output .= '</' . $header . '>';
		} else {	
			$output = '<' . $header . ' class="' . $hclass . '">' . do_shortcode( __( $content ) ) . '</' . $header . '>';
		}
		
		return $output;
	} // END function sc_wptabtitle

	
	/**
	 * 	[wptabcontent]
	 */
	function sc_wptabcontent( $atts, $content = null ) {
		extract( shortcode_atts( array( 
				'class'	=>	''
			), $atts));
			return '<div class="wp-tab-content"><div class="wp-tab-content-wrapper">' . do_shortcode($content) . '</div></div><!-- end div.wp-tab-content -->';
			
	} // END function sc_wptabcontent


	/**
	 * 	Spoilers/Collapsibles/Sliders. 
	 * 
	 * 	[wpspoiler name="NAME"]
	 */
	function sc_wpspoiler( $atts, $content = null ) {
		extract( shortcode_atts( array( 
				'name'		=>	'Show Content',
				'style'		=>	$this->options['tab_scheme'],
				'fade'		=>	'true',
				'slide'		=>	'true',
				'speed'		=>	false,
				'closebtn'	=>	false,
				'showText'	=>	'Click to show',
				'hideText'	=>	'Click to hide',
				'open'		=>	'false',
				'post'		=>	'',
				'page'		=>	'',
				'elength'	=>	$this->options['excerpt_length'],
				'before_post'	=>	'',
				'after_post'	=>	'',
				'template'	=>	'2',
				'background'=>	'true'
			), $atts));
			
			static $wpui_spoiler_id = 0;
			$wpui_spoiler_id++;

			$scheme = $style;
			
			$style = ( $background != 'true' ) ? $background : $style;
			
			$h3class  = '';
			$h3class .= ( $fade == 'true' ) ? ' fade-true' : ' fade-false'; 
			$h3class .= ( $slide == 'true' ) ? ' slide-true' : ' slide-false';
			$h3class .= ( $open == 'true' ) ? ' open-true' : ' open-false';

			$jqui_cust = isset( $this->options[ 'jqui_custom_themes' ] ) ? @json_decode( $this->options[ 'jqui_custom_themes' ] , true ) : array();	

			if ( stristr( $style, 'wpui-' ) && ! isset( $jqui_cust[ $scheme ] ) ) {
				$style .= ' wpui-styles';
			} else {
				$style .= ' jqui-styles';
			}
			
			$h3class .= ( $speed ) ? ' speed-' . $speed : '';
			
			if ( $post != '' || $page != '' ) {
				$typew = ( $page != '' ) ? 'page' : 'post';
				$piod = ( $page != '' ) ? $page : $post;

				$content = '';
				
				$post_content = $this->wpuiPosts->wpui_get_post( $piod, $elength, $typew );
				
				$tmpl = ( isset($this->options[ 'post_template_' . $template ]) ) ?
							$this->options[ 'post_template_' . $template ] :
							$this->options[ 'post_template_2' ];
				$content = $before_post . $this->wpuiPosts->replace_tags( $tmpl, $post_content ) . $after_post;
				$name = $post_content[ 'title' ];		
			}
			
			$out_content = do_shortcode( $content );
			if ( $closebtn )
				$out_content .= '<a class="close-spoiler ui-button ui-corner-all" href="#">' . $closebtn . '</a>';
			
/*			return '<div class="wp-spoiler ' . $style . '"><h3 class="ui-collapsible-header' . $h3class . '">' .$name . '</h3><div class="ui-collapsible-content"><div class="ui-collapsible-wrapper">'  . $out_content . '</div></div></div><!-- end div.wp-spoiler -->';			*/
			return '<div id="wp-spoiler-' . $wpui_spoiler_id . '" class="wp-spoiler ' . $style . '">  <h3 class="wp-spoiler-title' . $h3class . '">' .$name . '</h3><div class="wp-spoiler-content">'  . $out_content . '</div>  </div><!-- end div.wp-spoiler -->';
	} // END function sc_wptabcontent


	/**
	 * 	Dialogs
	 * 	
	 * 	[wpdialog]Stuff you wanna say[/wpdialog]
	 */
	function sc_wpdialog( $atts, $content = null ) {
		extract( shortcode_atts( array(
			'style'			=>	$this->options['tab_scheme'],
			'auto_open'		=>	"true",
			'openlabel'		=>	"Show Information",
			'opener'		=>	'button',
			'title'			=>	'Information',
			'height'		=>	'auto',
			'width'			=>	$this->options[ 'dialog_width' ],
			'show'			=>	'slide',
			'hide'			=>	'fade',
			'modal'			=>	'false',
			'closeOnEscape'	=>	'true',
			'resizable'		=>	'true',
			'draggable'		=>	'true',
			'position'		=>	'center',
			'zIndex'		=>	false,
			'button'		=>	false,
			'post'			=>	'',
			'elength'		=>	'more',
			'before_post'	=>	'',
			'after_post'	=>	'',
			'template'		=>	'2'
		), $atts ) );
		
		static $dia_inst = 0;
		$dia_inst++;
		// $args = '';
		$args = array();
		$scheme = $style;
		$sel_post = $post;
		unset( $post );
		
		global $post;	
	
		$args[ 'dialogClass' ] = $style;
		$args[ 'width' ] = $width;
		$args[ 'height' ] = $height;
		$args[ 'autoOpen' ] = ($auto_open == 'true' ) ? true : false;
		$args[ 'show' ] = $show;
		$args[ 'hide' ] = $hide;
		$args[ 'modal' ] = ($modal == 'true' ) ? true : false;
		$args[ 'resizable' ] = ($resizable == 'true' ) ? true : false;
		$args[ 'draggable' ] = ($draggable == 'true' ) ? true : false;
		$args[ 'closeOnEscape' ] = ($closeOnEscape == 'true' ) ? true : false;
		$args[ 'position' ] = explode( ' ', $position );
		if ( $zIndex ) $args[ 'zIndex' ] = $zIndex;
		
		$buttonz = get_post_meta( $post->ID, 'wpui_dialog_' . $dia_inst . '_button', true );
		
		$tmpl = ( isset($this->options[ 'post_template_' . $template ]) ) ?
					$this->options[ 'post_template_' . $template ] :
					$this->options[ 'post_template_1' ];
					
		if ( $sel_post != '' ) {
			$get_post = $this->wpuiPosts->wpui_get_post( $sel_post , $elength );
			$title = $get_post[ 'title' ];
			$out_content = $before_post . $this->wpuiPosts->replace_tags( $tmpl, $get_post ) . $after_post;
		} else {
			$out_content = $content;
		}
		
		$output = '';

		if ( $auto_open == "false" ) {
			
			if ( $opener == 'button' ) {
				$output .= do_shortcode( '[wpui_button class="wpui-open-dialog" primary="ui-icon-newwin" url="#" rel="wp-dialog-' . $dia_inst . '" label="' . $openlabel . '"]' );
			} else {
				$output .= '<a href="#" class="wpui-open-dialog dialog-opener-' . $dia_inst . '" rel="wp-dialog-' . $dia_inst . '">' . $openlabel . '</a>';
			}

		} 
		$output .= '<div id="wp-dialog-' . $dia_inst . '" class="wp-dialog wp-dialog-' . $dia_inst . ' ' . $style . '" title="' . $title . '">';
		
		$output .= do_shortcode( $out_content ) . '</div><!-- end .wp-dialog -->';

		$output .= '<script type="text/javascript">' . "\n";
		$output .= 'thiArgs' .  $dia_inst . ' = JSON.parse(\'' . json_encode( $args ) . '\');' . "\n";
		if ( $buttonz ) {
			$output .= 'thiArgs' . $dia_inst . '.buttons = [' . $buttonz . '];';
		}

		$output .= 'jQuery(function() {' . "\n";
		$output .= 'jQuery( "#wp-dialog-' . $dia_inst . '" ).dialog( thiArgs' .  $dia_inst . ' );  });' . "\n";
		$output .= 'jQuery(".wpui-open-dialog" ).live( "click", function() {' . "\n";
		$output .= 'var tisRel = jQuery( this ).attr( "rel" );' . "\n";
		$output .= 'jQuery( "#" + tisRel ).dialog( "open" );' . "\n";
		$output .= 'return false;'. "\n";
		$output .= '});' . "\n";
		$output .= '</script>';
		return $output;
	} // END method sc_wpdialog	


	
	function sc_wpuifeeds( $atts, $content = null ) {
		extract( shortcode_atts( array( 
				'url'			=>	'',
				'number'		=>	3,
				'style'			=>	$this->options[ 'tab_scheme' ],
				'type'			=>	'tabs',
				'mode'			=>	'',
				'listwidth'		=>	'',
				'tab_names'		=>	'title',
				'effect'		=>	$this->options['tabsfx'],
				'speed'			=>	'600',
				'number'		=>	'4',
				'rotate'		=>	'',
				'elength'		=>	$this->options['excerpt_length'],
				'before_post'	=>	'',
				'after_post'	=>	'',
				'template'		=>	'1'
			), $atts));
			
			if ( ! $url )
				return __( 'WP-UI feeds shortcodes needs a valid RSS URL to work.' , 'wp-ui' );
				
			$results = $this->wpuiPosts->wpui_get_feeds( array(
				'url'		=>	$url,
				'elength'	=>	$elength,
				'number'	=>	$number				
			));

		if ( ! $results ) return false;

		$tab_names_arr = preg_split( '/\s?,\s?/i', $tab_names );

		$output = '';

		$output_s = '';

		$tmpl = ( isset( $this->options[ 'post_template_' . $template ] ) ) ?
					$this->options[ 'post_template_' . $template ] :
					$this->options[ 'post_template_1' ];
		
		foreach( $results as $index=>$item ) {
			$tab_num = $index+ 1;
			
			if ( $tab_names == 'title' ) {
				$tab_name = $item[ 'title' ];
			} elseif ( isset( $tab_names_arr ) && count( $tab_names_arr ) > 1 ) {
				$tab_name = $tab_names_arr[ $index ];
			} else {
				$tab_name = $tab_num;
			}
				
			$tabs_content = $this->wpuiPosts->replace_tags( $tmpl , $item );
			$output_s .= do_shortcode( '[wptabtitle]' . $tab_name. '[/wptabtitle] [wptabcontent] ' . $tabs_content . 	' [/wptabcontent]' );
			
		}		
		
		$wptabsargs = '';
		
		if ( $type != '' )
			$wptabsargs .= ' type="' . $type . '"';
		if ( $mode != '' )
			$wptabsargs .= ' mode="' . $mode . '"';
		
		if ( $listwidth != '' )
			$style .= ' listwidth-' . $listwidth;
		$wptabsargs .= ' style="' . $style . '"';
		if ( $rotate && $rotate != '' )
			$wptabsargs .= ' rotate="' . $rotate . '"';
		
		if ( $listwidth != '' )
			$style .= ' listwidth-' . $listwidth;
			
		$output .= do_shortcode( '[wptabs' . $wptabsargs . '] ' . $output_s  . ' [/wptabs]' );
		
		return $output;
			
	} // END function sc_wptabcontent


	/**
	 * 	Add the wpui-query GET var 
	 */
	function wpui_add_query( $query_vars )
	{
		$query_vars[] = 'wpui-css';
		$query_vars[] = 'wpui-image';
		return $query_vars;
	} // END function wpui_add_query


	/**
	 * 	Queue the custom css.
	 * 
	 * 	@since 0.5
	 * 	@uses get_query_var
	 */
	function wpui_add_queries()
	{
		$query = get_query_var( 'wpui-css' );
		if ( 'css' == $query ) {
			// include_once( 'css/css.php');
			header( 'Content-type: text/css' );
			header( 'Cache-Control: must-revalidate' );
			$offset = 72000;
			header( "Expires: " . gmdate( "D, d M Y H:i:s", time() + 72000) . " GMT");
			$opts = get_option( 'wpUi_options' );
			echo $opts['custom_css'];
			exit; // Dont remove.
		}
		
	} // END function wpui_add_queries


	
	function assess_needed_scripts( $posts ) {
		if ( empty( $posts ) ) return $posts;
		$sc_pat = get_shortcode_regex();
		foreach( $posts as $post ) {
			if ( get_post_meta( $post->ID, 'wp-ui-load', true ) )
					delete_post_meta( $post->ID, 'wp-ui-load' );
			$comp = array();
			// preg_match( '/' . $sc_pat . '/s', $post->post_content, $matches );
			if ( stripos( $post->post_content, '[wptabs' ) !== false ||
			 	stripos( $post->post_content, '[wptabposts' ) !== false ||
				stripos( $post->post_content, '[wpuifeeds' ) !== false  ) {
					$comp[] = 'tabs';
				if(stripos ( $post->post_content, 'type="accordion"')  !== false)
					$comp[] = 'acc';			
			}
			if ( stripos( $post->post_content, '[wpspoiler') !== false )
				$comp[] = 'spoiler';
			if ( stripos( $post->post_content, '[wpdialog' ) !== false )
				$comp[] = 'dialog';
			if ( stripos( $post->post_content, '[wploop' ) !== false )
				$comp[] = 'pager';
			if ( count( $comp ) ) {
				$comp[] = 'init';
				$post->post_wpui = $comp;
				// add_post_meta( $post->ID, 'wp-ui-load', $comp, true ) or update_post_meta( $post->ID, 'wp-ui-load', $comp );
			}
			
			
		}
		return $posts;
	}
	
	function sc_wpuicomp($atts, $content = null ) {
		extract( shortcode_atts( array( 
			'debug'	=>	'false',
			'set'	=>	''
			), $atts));
		if ( $debug == 'true' ) {
			global $post;
			if ( isset( $post->post_wpui ) )
			$post_meta = $post->post_wpui;

		}
		if ( $set != '' ) {
			$setA = explode( ',', $set );
			if ( !is_array( $setA) && empty( $setA ) ) 
				return 'Invalid Set argument : Example <code>[wpuicomp set="tabs,spoiler,dialog"]</code> will load tabs, spoiler and dialog scripts. If you just want to view the arguments, input <code>debug="true"</code>';
			else {
				global $post;
				$post->post_wpui = $setA;
				// add_post_meta( $post->ID , '_wp-ui-load', $setA , true);
			}
		}		
	}
	
	private function do_edit() {
		$cond = false;
		if (
		( in_array( basename( $_SERVER['PHP_SELF'] ), array( 'post-new.php', 'page-new.php', 'post.php', 'page.php' ) ) ) && 
		( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) 
		) $cond = true;
		return $cond;	
	}	
	
	
	private function load_modules() {
		if ( ! is_dir ( wpui_dir( 'modules' ) ) ) return false;
		if ( $mod_dir = opendir( wpui_dir( 'modules' ) ) ) {
			while ( false != ( $module = readdir( $mod_dir ) ) ) {
				if ( 'php' == substr( $module, -3 ) ) {
					@include_once( wpui_dir( 'modules/' . $module ) );
				}
			} // end while.			
			
		} // end if mod_dir.
	}
	
	
} // end class WP_UI



if ( function_exists( 'shortcode_unautop' ) ) {
	add_filter( 'the_editor_content', 'shortcode_unautop' );
	add_filter( 'the_content', 'shortcode_unautop' );
}

add_filter( 'widget_text', 'do_shortcode');



?>