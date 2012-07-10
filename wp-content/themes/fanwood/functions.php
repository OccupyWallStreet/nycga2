<?php
/**
 * The functions file is used to initialize everything in the theme.  It controls how the theme is loaded and 
 * sets up the supported features, default actions, and default filters.  If making customizations, users 
 * should create a child theme and make changes to its functions.php file (not this one).  Friends don't let 
 * friends modify parent theme files. ;)
 *
 * Child themes should do their setup on the 'after_setup_theme' hook with a priority of 11 if they want to
 * override parent theme features.  Use a priority of 9 if wanting to run before the parent theme.
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License version 2, as published by the Free Software Foundation.  You may NOT assume 
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * You should have received a copy of the GNU General Public License along with this program; if not, write 
 * to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @package Fanwood
 * @subpackage Functions
 * @version 0.1.6.5
 * @author Tung Do <ttsondo@devpress.com>
 * @copyright Copyright (c) 2012, Tung Do
 * @link http://devpress.com/themes/fanwood
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Load the Hybrid Core framework. */
require_once( trailingslashit ( get_template_directory() ) . 'library/hybrid.php' );
$theme = new Hybrid(); // Part of the framework.

/* Do theme setup on the 'after_setup_theme' hook. */
add_action( 'after_setup_theme', 'fanwood_theme_setup' );

/**
 * Theme setup function.  This function adds support for theme features and defines the default theme
 * actions and filters.
 *
 * @since 0.1.0
 */
function fanwood_theme_setup() {

	/* Get action/filter hook prefix. */
	$prefix = hybrid_get_prefix(); // Part of the framework, cannot be changed or prefixed.
	
	/* Add theme settings */
	if ( is_admin() )
	    require_once( trailingslashit ( get_template_directory() ) . 'admin/functions-admin.php' );
	
	/* Register support for all post formats. */
	add_theme_support( 'post-formats', array(
		'aside',
		'audio',
		'chat',
		'gallery',
		'image',
		'link',
		'quote',
		'status',
		'video'
		) );

	/* Add framework menus. */
	add_theme_support( 'hybrid-core-menus', array( // Add core menus.
		'primary',
		'secondary',
		'subsidiary'
		) );

	/* Register aditional menus */
	add_action( 'init', 'fanwood_register_menus' );

	/* Add framework sidebars */
	add_theme_support( 'hybrid-core-sidebars', array( // Add sidebars or widget areas.
		'primary',
		'secondary',
		'subsidiary',
		'header',
		'before-content',
		'after-content',
		'after-singular'
		) );

	/* Register additional widget areas. */
	add_action( 'widgets_init', 'fanwood_register_sidebars' );

	add_theme_support( 'hybrid-core-widgets' );
	add_theme_support( 'hybrid-core-shortcodes' );
	add_theme_support( 'hybrid-core-drop-downs' ); // Works with registered menus above.
	add_theme_support( 'hybrid-core-template-hierarchy' ); // This is important. Do not remove. */
	
	/* Add aditional layouts */
	add_filter( 'theme_layouts_strings', 'fanwood_theme_layouts' );

	/* Add theme support for framework layout extension. */
	add_theme_support( 'theme-layouts', array( // Add theme layout options.
		'1c',
		'2c-l',
		'2c-r',
		'3c-c',
		'3c-l',
		'3c-r',
		'hl-1c',
		'hl-2c-l',
		'hl-2c-r',
		'hr-1c',
		'hr-2c-l',
		'hr-2c-r'
		) );

	/* Add theme support for other framework extensions */
	add_theme_support( 'post-stylesheets' );
	add_theme_support( 'loop-pagination' );
	add_theme_support( 'get-the-image' );
	add_theme_support( 'breadcrumb-trail' );
	add_theme_support( 'cleaner-gallery' );
	add_theme_support( 'hybrid-core-theme-settings', array( 'footer' ) );
	
	/* Add theme support for plugins */
	add_theme_support( 'bbpress' );

	/* Add theme support for WordPress features. */
	add_theme_support( 'automatic-feed-links' );
	
	/* Load resources into the theme. */
	add_action( 'wp_enqueue_scripts', 'fanwood_resources' );
	
	/* Add theme support for WordPress background feature */
	add_custom_background( 'fanwood_custom_background_callback' );
	
	/* Modify excerpt more */
	add_filter('excerpt_more', 'fanwood_new_excerpt_more');
	
	/* Register new image sizes. */
	add_action( 'init', 'fanwood_register_image_sizes' );
	
	/* Wraps <blockquote> around quote posts. */
	add_filter( 'the_content', 'fanwood_quote_post_content' );

	/* Set content width. */
	hybrid_set_content_width( 520 );
	
	/* Embed width/height defaults. */
	add_filter( 'embed_defaults', 'fanwood_embed_defaults' ); // Set default widths to use when inserting media files
	
	/* Assign specific layouts to pages based on set conditions and disable certain sidebars based on layout choices. */
	add_action( 'template_redirect', 'fanwood_layouts' );
	add_filter( 'sidebars_widgets', 'fanwood_disable_sidebars' );
	
	/* Add custom <body> classes. */
	add_filter( 'body_class', 'fanwood_body_class' );
	
	/* bbPress Functions */
	if ( function_exists( 'is_bbpress' ) ) {
		add_action( 'wp_head', 'fanwood_bbpress_scripts' );
		add_filter( 'bbp_show_lead_topic', '__return_true' );
		add_filter( 'wp_enqueue_scripts', 'fanwood_localize_topic_script' );
		add_action( 'wp_ajax_dim-favorite', 'fanwood_dim_favorite' );
		add_action( 'wp_ajax_dim-subscription', 'fanwood_dim_subscription' );
	}
	
	/* Plugin Layouts */
	if ( function_exists ( 'bp_loaded' ) ) {
		add_filter( 'get_theme_layout', 'fanwood_plugin_layouts' );
	}
	
	/* Jigoshop Functions. */
	if ( function_exists( 'is_jigoshop' ) ) {
		remove_action( 'jigoshop_before_main_content', 'jigoshop_output_content_wrapper', 10);
		remove_action( 'jigoshop_after_main_content', 'jigoshop_output_content_wrapper_end', 10);
	}
	
}

/**
 * Loads the theme scripts.
 *
 * @since 0.1
 */
function fanwood_resources() {

	wp_enqueue_script( 'jquery-ui-accordion' );
	wp_enqueue_script( 'jquery-ui-tabs' );
	wp_enqueue_script ( 'fanwood-scripts', trailingslashit ( THEME_URI ) . 'js/fanwood.js', array( 'jquery' ), '201200215', true );
	wp_enqueue_script ( 'fanwood-ui', trailingslashit ( THEME_URI ) . 'js/fanwood-ui.js', array( 'jquery', 'jquery-ui-accordion', 'jquery-ui-tabs' ), '20120413', true );
	
	/* for bbPress. */
	
	if ( function_exists ( 'is_bbpress' ) ) {
	
		wp_enqueue_style ( 'fanwood-bbpress', trailingslashit ( THEME_URI ) . 'css/bbpress.css', false, '20120228', 'all' );
	
		if ( function_exists( 'bbp_is_topic' ) ) {
			if ( bbp_is_topic() )
				wp_enqueue_script( 'fanwood-bbpress-topic', trailingslashit( THEME_URI ) . 'js/bbpress-topic.js', array( 'wp-lists' ), false, true );
		}
				
		if( function_exists( 'bbp_is_single_user_edit' ) ) {
			if ( bbp_is_single_user_edit() )
				wp_enqueue_script( 'user-profile' );
		}
	
	}
	
	/* for BuddyPress */
	
	if ( function_exists ( 'bp_is_active' ) ) {

		wp_dequeue_style( 'bp' );
		
		wp_enqueue_style ( 'fanwood-buddypress-admin-bar', trailingslashit ( THEME_URI ) . 'css/buddypress-admin-bar.css', array( 'bp-admin-bar' ), '20120308', 'all' );

		/* Load BuddyPress-specific styles. */
		wp_enqueue_style ( 'fanwood-buddypress', trailingslashit ( THEME_URI ) . 'css/buddypress.css', false, '20120228', 'all' );
	
	}
	
	/* for Jigoshop */
	
	if( function_exists( 'is_jigoshop') ) {
		wp_dequeue_style( 'jigoshop_frontend_styles' );
		wp_enqueue_style ( 'fanwood-jigoshop', trailingslashit ( THEME_URI ) . 'css/jigoshop.css', false, '20120310', 'all' );
	}
	
	/* for Hybrid Tabs */
	
	if( function_exists( 'register_hybrid_tab') ) {
		wp_enqueue_style ( 'fanwood-hybrid-tabs', trailingslashit ( THEME_URI ) . 'css/hybrid-tabs.css', false, '20120312', 'all' );	
	}
	
	/* for Gravity Forms */
	
	if( class_exists( 'RGForms' ) && class_exists( 'RGFormsModel' )) {
		
		wp_enqueue_style( 'fanwood-gravity-forms', trailingslashit (THEME_URI) . 'css/gravity-forms.css', false, '20120312', 'all' );

	}

}

/**
 * This is a fix for when a user sets a custom background color with no custom background image.  What 
 * happens is the theme's background image hides the user-selected background color.  If a user selects a 
 * background image, we'll just use the WordPress custom background callback.
 * 
 * Thanks to Justin Tadlock for the code.
 *
 * @since 0.1
 * @link http://core.trac.wordpress.org/ticket/16919
 */
function fanwood_custom_background_callback() {

	/* Get the background image. */
	$image = get_background_image();

	/* If there's an image, just call the normal WordPress callback. We won't do anything here. */
	if ( !empty( $image ) ) {
		_custom_background_cb();
		return;
	}

	/* Get the background color. */
	$color = get_background_color();

	/* If no background color, return. */
	if ( empty( $color ) )
		return;

	/* Use 'background' instead of 'background-color'. */
	$style = "background: #{$color};";

?>
<style type="text/css">body.custom-background { <?php echo trim( $style ); ?> }</style>
<?php

}

/**
 * Filters the excerpt more.
 *
 * @since 0.1
 */

function fanwood_new_excerpt_more( $more ) {
	return '&#133;';
}

/**
 * Wraps the output of posts with the 'quote' post format with a <blockquote> element if the post content 
 * doesn't already have this element within it.
 *
 * @since 0.1
 * @access private
 * @param string $content The content of the post.
 * @return string $content
 */
function fanwood_quote_post_content( $content ) {

	if ( has_post_format( 'quote' ) ) {
		preg_match( '/<blockquote.*?>/', $content, $matches );

		if ( empty( $matches ) )
			$content = "<blockquote>{$content}</blockquote>";
	}

	return $content;
}

/**
 * Registers additional image size 'fanwood-thumbnail'.
 *
 * @since 0.1
 */
function fanwood_register_image_sizes() {
	add_image_size( 'fanwood-thumbnail', 240, 240, true );
}

/**
 * Overwrites the default widths for embeds.  This is especially useful for making sure videos properly
 * expand the full width on video pages.  This function overwrites what the $content_width variable handles
 * with context-based widths.
 *
 * @since 0.1
 */
function fanwood_embed_defaults( $args ) {

	$args['width'] = 520;

	if ( current_theme_supports( 'theme-layouts' ) ) {

		$layout = theme_layouts_get_layout();

		if ( 'layout-3c-l' == $layout || 'layout-3c-r' == $layout || 'layout-3c-c' == $layout || 'layout-hl-2c-l' == $layout || 'layout-hl-2c-r' == $layout || 'layout-hr-2c-l' == $layout || 'layout-hr-2c-r' == $layout )
		
			$args['width'] = 240;
			
		elseif ( 'layout-1c' == $layout )
		
			$args['width'] = 840;

	}

	return $args;
}

/**
 * Conditional logic deciding the layout of certain pages.
 *
 * @since 0.1
 */
function fanwood_layouts() {

	if ( current_theme_supports( 'theme-layouts' ) ) {

		$global_layout = hybrid_get_setting( 'fanwood_global_layout' );
		$bbpress_layout = hybrid_get_setting( 'fanwood_bbpress_layout' );
		$jigoshop_layout = hybrid_get_setting( 'fanwood_jigoshop_layout' );
		$layout = theme_layouts_get_layout();

		if ( !is_singular() && $global_layout !== 'layout_default' && function_exists( "fanwood_{$global_layout}" ) ) {
			add_filter( 'get_theme_layout', 'fanwood_' . $global_layout );
		}
		
		if ( is_singular() && $layout == 'layout-default' && $global_layout !== 'layout_default' && function_exists( "fanwood_{$global_layout}" ) ) {
			add_filter( 'get_theme_layout', 'fanwood_' . $global_layout );
		}
		
		if ( function_exists ( 'bbp_loaded' ) ) {
			if ( is_bbpress() && !is_singular() && $bbpress_layout !== 'layout_default' && function_exists( "fanwood_{$bbpress_layout}" ) ) {
				add_filter( 'get_theme_layout', 'fanwood_' . $bbpress_layout );
			}
			elseif ( is_bbpress() && is_singular() && $layout == 'layout-default' && $bbpress_layout !== 'layout_default' && function_exists( "fanwood_{$bbpress_layout}" ) ) {
				add_filter( 'get_theme_layout', 'fanwood_' . $bbpress_layout );
			}
		}
		
		if ( function_exists ( 'is_jigoshop' ) ) {
			if ( is_jigoshop() && !is_singular() && $jigoshop_layout !== 'layout_default' && function_exists( "fanwood_{$jigoshop_layout}" ) ) {
				add_filter( 'get_theme_layout', 'fanwood_' . $jigoshop_layout );
			}
			elseif ( is_jigoshop() && is_singular() && $layout == 'layout-default' && $jigoshop_layout !== 'layout_default' && function_exists( "fanwood_{$jigoshop_layout}" ) ) {
				add_filter( 'get_theme_layout', 'fanwood_' . $jigoshop_layout );
			}
		}

	}
	
}

/**
 * Filters 'get_theme_layout' to set layouts for specific installed plugin pages.
 *
 * @since 0.1
 */

function fanwood_plugin_layouts( $layout ) {

	if ( current_theme_supports( 'theme-layouts' ) ) {
	
		$global_layout = hybrid_get_setting( 'fanwood_global_layout' );
		$buddypress_layout = hybrid_get_setting( 'fanwood_buddypress_layout' );

		if ( function_exists( 'bp_loaded' ) && !bp_is_blog_page() && $layout == 'layout-default' ) {
		
			if ( $buddypress_layout !== 'layout_default' ) {
			
				if ( $buddypress_layout == 'layout_1c' )
					$layout = 'layout-1c';
				elseif ( $buddypress_layout == 'layout_2c_l' )
					$layout = 'layout-2c-l';
				elseif ( $buddypress_layout == 'layout_2c_r' )
					$layout = 'layout-2c-r';
				elseif ( $buddypress_layout == 'layout_3c_c' )
					$layout = 'layout-3c-c';
				elseif ( $buddypress_layout == 'layout_3c_l' )
					$layout = 'layout-3c-l';
				elseif ( $buddypress_layout == 'layout_3c_r' )
					$layout = 'layout-3c-r';
				elseif ( $buddypress_layout == 'layout_hl_1c' )
					$layout = 'layout-hl-1c';
				elseif ( $buddypress_layout == 'layout_hl_2c_l' )
					$layout = 'layout-hl-2c-l';
				elseif ( $buddypress_layout == 'layout_hl_2c_r' )
					$layout = 'layout-hl-2c-r';
				elseif ( $buddypress_layout == 'layout_hr_1c' )
					$layout = 'layout-hr-1c';
				elseif ( $buddypress_layout == 'layout_hr_2c_l' )
					$layout = 'layout-hr-2c-l';
				elseif ( $buddypress_layout == 'layout_hr_2c_r' )
					$layout = 'layout-hr-2c-r';
				
			} elseif ( $buddypress_layout == 'layout_default' ) {
			
				if ( $global_layout == 'layout_1c' )
					$layout = 'layout-1c';
				elseif ( $global_layout == 'layout_2c_l' )
					$layout = 'layout-2c-l';
				elseif ( $global_layout == 'layout_2c_r' )
					$layout = 'layout-2c-r';
				elseif ( $global_layout == 'layout_3c_c' )
					$layout = 'layout-3c-c';
				elseif ( $global_layout == 'layout_3c_l' )
					$layout = 'layout-3c-l';
				elseif ( $global_layout == 'layout_3c_r' )
					$layout = 'layout-3c-r';
				elseif ( $global_layout == 'layout_hl_1c' )
					$layout = 'layout-hl-1c';
				elseif ( $global_layout == 'layout_hl_2c_l' )
					$layout = 'layout-hl-2c-l';
				elseif ( $global_layout == 'layout_hl_2c_r' )
					$layout = 'layout-hl-2c-r';
				elseif ( $global_layout == 'layout_hr_1c' )
					$layout = 'layout-hr-1c';
				elseif ( $global_layout == 'layout_hr_2c_l' )
					$layout = 'layout-hr-2c-l';
				elseif ( $global_layout == 'layout_hr_2c_r' )
					$layout = 'layout-hr-2c-r';
			
			}

		}
		
	}
	
	return $layout;

}

/**
 * Filters 'theme_layouts_strings'.
 *
 * @since 0.1.6
 */
function fanwood_theme_layouts( $strings ) {

	/* Set up the layout strings. */
	$strings = array(
		'default' => __( 'Default', 'theme-layouts' ),
		'1c' => __( 'One Column', 'theme-layouts' ),
		'2c-l' => __( 'Two Columns, Left', 'theme-layouts' ),
		'2c-r' => __( 'Two Columns, Right', 'theme-layouts' ),
		'3c-c' => __( 'Three Columns, Center', 'theme-layouts' ),
		'3c-l' => __( 'Three Columns, Left', 'theme-layouts' ),
		'3c-r' => __( 'Three Columns, Right', 'theme-layouts' ),
		'hl-1c' => __( 'Header Left One Column', 'theme-layouts' ),
		'hl-2c-l' => __( 'Header Left Two Columns, Left', 'theme-layouts' ),
		'hl-2c-r' => __( 'Header Left Two Columns, Right', 'theme-layouts' ),
		'hr-1c' => __( 'Header Right One Column', 'theme-layouts' ),
		'hr-2c-l' => __( 'Header Right Two Columns, Left', 'theme-layouts' ),
		'hr-2c-r' => __( 'Header Right Two Columns, Right', 'theme-layouts' )
	);

	return $strings;
}

/**
 * Filters 'get_theme_layout'.
 *
 * @since 0.1
 */
function fanwood_layout_default( $layout ) {return 'layout-default';}
function fanwood_layout_1c( $layout ) {return 'layout-1c';}
function fanwood_layout_2c_l( $layout ) {return 'layout-2c-l';}
function fanwood_layout_2c_r( $layout ) {return 'layout-2c-r';}
function fanwood_layout_3c_c( $layout ) {return 'layout-3c-c';}
function fanwood_layout_3c_l( $layout ) {return 'layout-3c-l';}
function fanwood_layout_3c_r( $layout ) {return 'layout-3c-r';}
function fanwood_layout_hl_1c( $layout ) {return 'layout-hl-1c';}
function fanwood_layout_hl_2c_l( $layout ) {return 'layout-hl-2c-l';}
function fanwood_layout_hl_2c_r( $layout ) {return 'layout-hl-2c-r';}
function fanwood_layout_hr_1c( $layout ) {return 'layout-hr-1c';}
function fanwood_layout_hr_2c_l( $layout ) {return 'layout-hr-2c-l';}
function fanwood_layout_hr_2c_r( $layout ) {return 'layout-hr-2c-r';}

/**
 * Disables sidebars based on layout choices.
 *
 * @since 0.1
 */
function fanwood_disable_sidebars( $sidebars_widgets ) {
	global $wp_query;

	if ( current_theme_supports( 'theme-layouts' ) && !is_admin() ) {

		if ( 'layout-1c' == theme_layouts_get_layout() ) {
			$sidebars_widgets['primary'] = false;
			$sidebars_widgets['secondary'] = false;
			
		}
		elseif ( 'layout-hl-1c' == theme_layouts_get_layout() || 'layout-hr-1c' == theme_layouts_get_layout() ) {
			$sidebars_widgets['primary'] = false;
			$sidebars_widgets['secondary'] = false;
			$sidebars_widgets['after-header'] = false;
			$sidebars_widgets['after-header-2c'] = false;
			$sidebars_widgets['after-header-3c'] = false;
			$sidebars_widgets['after-header-4c'] = false;
			$sidebars_widgets['after-header-5c'] = false;
		}
		elseif ( 'layout-hl-2c-l' == theme_layouts_get_layout() || 'layout-hl-2c-r' == theme_layouts_get_layout() || 'layout-hr-2c-l' == theme_layouts_get_layout() || 'layout-hr-2c-r' == theme_layouts_get_layout() ) {
			$sidebars_widgets['after-header'] = false;
			$sidebars_widgets['after-header-2c'] = false;
			$sidebars_widgets['after-header-3c'] = false;
			$sidebars_widgets['after-header-4c'] = false;
			$sidebars_widgets['after-header-5c'] = false;
		}
		
	}

	return $sidebars_widgets;
}

/** ====== Hybrid Core 1.3.0 functionality. ====== **/

/**
 * Fix for Hybrid Core until version 1.3.0 is released.  This adds the '.custom-background' class to the <body> 
 * element for the WordPress custom background feature.
 *
 * @since 0.1.0
 * @todo Remove once theme is upgraded to Hybrid Core 1.3.0.
 * @link http://core.trac.wordpress.org/ticket/18698
 */
function fanwood_body_class( $classes ) {

	if ( get_background_image() || get_background_color() )
		$classes[] = 'custom-background';

	if ( is_tax( 'post_format' ) )
		$classes = array_map( 'fanwood_clean_post_format_slug', $classes );

	return $classes;
}

/**
 * Put some scripts in the header, like AJAX url for wp-lists
 *
 * @since bbPress (r2652)
 *
 * @uses bbp_is_topic() To check if it's the topic page
 * @uses admin_url() To get the admin url
 * @uses bbp_is_user_profile_edit() To check if it's the profile edit page
 */
function fanwood_bbpress_scripts () {

	if ( bbp_is_topic() ) : ?>

	<script type='text/javascript'>
		/* <![CDATA[ */
		var ajaxurl = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
		/* ]]> */
	</script>

	<?php elseif ( bbp_is_single_user_edit() ) : ?>

	<script type="text/javascript" charset="utf-8">
		if ( window.location.hash == '#password' ) {
			document.getElementById('pass1').focus();
		}
	</script>

	<?php endif;

}

/**
* Load localizations for topic script
*
* These localizations require information that may not be loaded even by init.
*
* @since bbPress (r2652)
*
* @uses bbp_is_single_topic() To check if it's the topic page
* @uses is_user_logged_in() To check if user is logged in
* @uses bbp_get_current_user_id() To get the current user id
* @uses bbp_get_topic_id() To get the topic id
* @uses bbp_get_favorites_permalink() To get the favorites permalink
* @uses bbp_is_user_favorite() To check if the topic is in user's favorites
* @uses bbp_is_subscriptions_active() To check if the subscriptions are active
* @uses bbp_is_user_subscribed() To check if the user is subscribed to topic
* @uses bbp_get_topic_permalink() To get the topic permalink
* @uses wp_localize_script() To localize the script
*/
function fanwood_localize_topic_script() {

	// Bail if not viewing a single topic
	if ( !bbp_is_single_topic() )
		return;

	$user_id = bbp_get_current_user_id();

	$localizations = array(
		'currentUserId' => $user_id,
		'topicId'       => bbp_get_topic_id(),
	);

	// Favorites
	if ( bbp_is_favorites_active() ) {
		$localizations['favoritesActive'] = 1;
		$localizations['favoritesLink']   = bbp_get_favorites_permalink( $user_id );
		$localizations['isFav']           = (int) bbp_is_user_favorite( $user_id );
		$localizations['favLinkYes']      = __( 'favorites',                                         'bbpress' );
		$localizations['favLinkNo']       = __( '?',                                                 'bbpress' );
		$localizations['favYes']          = __( 'This topic is one of your %favLinkYes% [%favDel%]', 'bbpress' );
		$localizations['favNo']           = __( '%favAdd% (%favLinkNo%)',                            'bbpress' );
		$localizations['favDel']          = __( '&times;',                                           'bbpress' );
		$localizations['favAdd']          = __( 'Add this topic to your favorites',                  'bbpress' );
	} else {
		$localizations['favoritesActive'] = 0;
	}

	// Subscriptions
	if ( bbp_is_subscriptions_active() ) {
		$localizations['subsActive']   = 1;
		$localizations['isSubscribed'] = (int) bbp_is_user_subscribed( $user_id );
		$localizations['subsSub']      = __( 'Subscribe',   'bbpress' );
		$localizations['subsUns']      = __( 'Unsubscribe', 'bbpress' );
		$localizations['subsLink']     = bbp_get_topic_permalink();
	} else {
		$localizations['subsActive'] = 0;
	}

	wp_localize_script( 'bbp_topic', 'bbpTopicJS', $localizations );
}

/**
 * Add or remove a topic from a user's favorites
 *
 * @since bbPress (r2652)
 *
 * @uses bbp_get_current_user_id() To get the current user id
 * @uses current_user_can() To check if the current user can edit the user
 * @uses bbp_get_topic() To get the topic
 * @uses check_ajax_referer() To verify the nonce & check the referer
 * @uses bbp_is_user_favorite() To check if the topic is user's favorite
 * @uses bbp_remove_user_favorite() To remove the topic from user's favorites
 * @uses bbp_add_user_favorite() To add the topic from user's favorites
 */
function fanwood_dim_favorite () {
	$user_id = bbp_get_current_user_id();
	$id      = intval( $_POST['id'] );

	if ( !current_user_can( 'edit_user', $user_id ) )
		die( '-1' );

	if ( !$topic = bbp_get_topic( $id ) )
		die( '0' );

	check_ajax_referer( "toggle-favorite_$topic->ID" );

	if ( bbp_is_user_favorite( $user_id, $topic->ID ) ) {
		if ( bbp_remove_user_favorite( $user_id, $topic->ID ) )
			die( '1' );
	} else {
		if ( bbp_add_user_favorite( $user_id, $topic->ID ) )
			die( '1' );
	}

	die( '0' );
}

/**
 * Subscribe/Unsubscribe a user from a topic
 *
 * @since bbPress (r2668)
 *
 * @uses bbp_is_subscriptions_active() To check if the subscriptions are active
 * @uses bbp_get_current_user_id() To get the current user id
 * @uses current_user_can() To check if the current user can edit the user
 * @uses bbp_get_topic() To get the topic
 * @uses check_ajax_referer() To verify the nonce & check the referer
 * @uses bbp_is_user_subscribed() To check if the topic is in user's
 *                                 subscriptions
 * @uses bbp_remove_user_subscriptions() To remove the topic from user's
 *                                        subscriptions
 * @uses bbp_add_user_subscriptions() To add the topic from user's subscriptions
 */
function fanwood_dim_subscription () {
	if ( !bbp_is_subscriptions_active() )
		return;

	$user_id = bbp_get_current_user_id();
	$id = intval( $_POST['id'] );

	if ( !current_user_can( 'edit_user', $user_id ) )
		die( '-1' );

	if ( !$topic = bbp_get_topic( $id ) )
		die( '0' );

	check_ajax_referer( "toggle-subscription_$topic->ID" );

	if ( bbp_is_user_subscribed( $user_id, $topic->ID ) ) {
		if ( bbp_remove_user_subscription( $user_id, $topic->ID ) )
			die( '1' );
	} else {
		if ( bbp_add_user_subscription( $user_id, $topic->ID ) )
			die( '1' );
	}

	die( '0' );
}

/**
 * Registers additional menus.
 *
 * @since 0.1.6
 * @uses register_nav_menu() Registers a nav menu with WordPress.
 * @link http://codex.wordpress.org/Function_Reference/register_nav_menu
 */
function fanwood_register_menus() {

	register_nav_menu( 'header-primary', _x( 'Header Primary', 'nav menu location', 'fanwood' ) );
	register_nav_menu( 'header-secondary', _x( 'Header Secondary', 'nav menu location', 'fanwood' ) );
	register_nav_menu( 'header-horizontal', _x( 'Header Horizontal', 'nav menu location', 'fanwood' ) );
	register_nav_menu( 'footer', _x( 'Footer', 'nav menu location', 'fanwood' ) );

}

/**
 * Register additional sidebars.
 *
 * @since 0.1.6
 */
function fanwood_register_sidebars() {

	$subsidiary_2 = array(
		'id' => 'subsidiary-2c',
		'name' => _x( 'Subsidiary 2 Columns', 'sidebar', 'fanwood' ),
		'description' => __( 'A 2-column widget area loaded before the footer of the site.', 'fanwood' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s widget-%2$s"><div class="widget-wrap widget-inside">',
		'after_widget' => '</div></div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>'
	);
	
	$subsidiary_3 = array(
		'id' => 'subsidiary-3c',
		'name' => _x( 'Subsidiary 3 Columns', 'sidebar', 'fanwood' ),
		'description' => __( 'A 3-column widget area loaded before the footer of the site.', 'fanwood' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s widget-%2$s"><div class="widget-wrap widget-inside">',
		'after_widget' => '</div></div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>'
	);
	
	$subsidiary_4 = array(
		'id' => 'subsidiary-4c',
		'name' => _x( 'Subsidiary 4 Columns', 'sidebar', 'fanwood' ),
		'description' => __( 'A 4-column widget area loaded before the footer of the site.', 'fanwood' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s widget-%2$s"><div class="widget-wrap widget-inside">',
		'after_widget' => '</div></div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>'
	);
	
	$subsidiary_5 = array(
		'id' => 'subsidiary-5c',
		'name' => _x( 'Subsidiary 5 Columns', 'sidebar', 'fanwood' ),
		'description' => __( 'A 5-column widget area loaded before the footer of the site.', 'fanwood' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s widget-%2$s"><div class="widget-wrap widget-inside">',
		'after_widget' => '</div></div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>'
	);
	
	$after_header = array(
		'id' => 'after-header',
		'name' => _x( 'After Header', 'sidebar', 'fanwood' ),
		'description' => __( 'A 1-column widget area loaded after the header of the site.', 'fanwood' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s widget-%2$s"><div class="widget-wrap widget-inside">',
		'after_widget' => '</div></div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>'
	);
	
	$after_header_2 = array(
		'id' => 'after-header-2c',
		'name' => _x( 'After Header 2 Columns', 'sidebar', 'fanwood' ),
		'description' => __( 'A 2-column widget area loaded after the header of the site.', 'fanwood' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s widget-%2$s"><div class="widget-wrap widget-inside">',
		'after_widget' => '</div></div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>'
	);
	
	$after_header_3 = array(
		'id' => 'after-header-3c',
		'name' => _x( 'After Header 3 Columns', 'sidebar', 'fanwood' ),
		'description' => __( 'A 3-column widget area loaded after the header of the site.', 'fanwood' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s widget-%2$s"><div class="widget-wrap widget-inside">',
		'after_widget' => '</div></div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>'
	);
	
	$after_header_4 = array(
		'id' => 'after-header-4c',
		'name' => _x( 'After Header 4 Columns', 'sidebar', 'fanwood' ),
		'description' => __( 'A 4-column widget area loaded after the header of the site.', 'fanwood' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s widget-%2$s"><div class="widget-wrap widget-inside">',
		'after_widget' => '</div></div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>'
	);
	
	$after_header_5 = array(
		'id' => 'after-header-5c',
		'name' => _x( 'After Header 5 Columns', 'sidebar', 'fanwood' ),
		'description' => __( 'A 5-column widget area loaded after the header of the site.', 'fanwood' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s widget-%2$s"><div class="widget-wrap widget-inside">',
		'after_widget' => '</div></div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>'
	);
	
	$entry = array(
		'id' => 'entry',
		'name' => _x( 'Entry', 'sidebar', 'fanwood' ),
		'description' => __( 'Loaded directly before the entry content texts.', 'fanwood' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s widget-%2$s"><div class="widget-wrap widget-inside">',
		'after_widget' => '</div></div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>'
	);
	
	$widgets_template = array(
		'id' => 'widgets-template',
		'name' => _x( 'Widgets Template', 'sidebar', 'fanwood' ),
		'description' => __( 'Used on widgets only page template.', 'fanwood' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s widget-%2$s"><div class="widget-wrap widget-inside">',
		'after_widget' => '</div></div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>'
	);

	register_sidebar( $subsidiary_2 );
	register_sidebar( $subsidiary_3 );
	register_sidebar( $subsidiary_4 );
	register_sidebar( $subsidiary_5 );
	register_sidebar( $after_header );
	register_sidebar( $after_header_2 );
	register_sidebar( $after_header_3 );
	register_sidebar( $after_header_4 );
	register_sidebar( $after_header_5 );
	register_sidebar( $entry );
	register_sidebar( $widgets_template );

}

?>