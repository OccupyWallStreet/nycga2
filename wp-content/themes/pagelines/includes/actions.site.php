<?php

global $pagelines_template;

// ===================================================================================================
// = Set up Section loading & create pagelines_template global in page (give access to conditionals) =
// ===================================================================================================

/**
 * Build PageLines Template Global (Singleton)
 *
 * Must be built inside the page (wp_head) so conditionals can be used to identify the template
 * in the admin; the template does not need to be identified so it is loaded in the init action
 *
 * @global  object $pagelines_template
 * @since   1.0.0
 */
add_action('pagelines_before_html', 'build_pagelines_template');

/**
 * Build the template in the admin... doesn't need to load in the page
 * @since 1.0.0
 */
add_action('admin_head', 'build_pagelines_template', 5);

add_action('pagelines_before_html', 'build_pagelines_layout', 5);
add_action('admin_head', 'build_pagelines_layout');

/**
 * Optionator
 * Does "just in time" loading of section option in meta; 
 * Will only load section options if the section is present, handles clones
 * @since 1.0.0
 */
add_action('admin_head', array(&$pagelines_template, 'load_section_optionator'));

add_filter( 'pagelines_options_array', 'pagelines_merge_addon_options' );

// Run Before Any HTML
add_action('pagelines_before_html', array(&$pagelines_template, 'run_before_page'));

add_action('wp_print_styles', 'workaround_pagelines_template_styles'); // Used as workaround on WP login page (and other pages with wp_print_styles and no wp_head/pagelines_before_html)

add_action( 'wp_print_styles', 'pagelines_get_childcss', 99);

add_action('pagelines_head', array(&$pagelines_template, 'hook_and_print_sections'));

add_action('wp_footer', array(&$pagelines_template, 'print_template_section_scripts'));

/**
 * Creates a global page ID for reference in editing and meta options (no unset warnings)
 * 
 * @since 1.0.0
 */
add_action('pagelines_before_html', 'pagelines_id_setup', 5);


/**
 * Adds page templates from the child theme.
 * 
 * @since 1.0.0
 */
add_filter('the_sub_templates', 'pagelines_add_page_callback', 10, 2);

/**
 * Adds link to admin bar
 * 
 * @since 1.0.0
 */
add_action( 'admin_bar_menu', 'pagelines_settings_menu_link', 100 );

// ================
// = HEAD ACTIONS =
// ================

/**
 * Add Main PageLines Header Information
 * 
 * @since 1.3.3
 */
add_action('pagelines_head', 'pagelines_head_common');

/**
 *
 * @TODO document
 *
 */
function pagelines_add_google_profile( $contactmethods ) {
	// Add Google Profiles
	$contactmethods['google_profile'] = __( 'Google Profile URL', 'pageines' );
	return $contactmethods;
}
add_filter( 'user_contactmethods', 'pagelines_add_google_profile', 10, 1);

/**
 * ng gallery fix.
 *
 * @return gallery template path
 * 
 */

add_filter( 'ngg_render_template', 'gallery_filter' , 10, 2);


/**
 *
 * @TODO document
 *
 */
function gallery_filter( $a, $template_name) {

	if ( $template_name == 'gallery-plcarousel')
		return sprintf( '%s/carousel/gallery-plcarousel.php', PL_SECTIONS);
	else
		return false;
}

new PageLinesRenderCSS;

add_action( 'template_redirect', 'pl_check_integrations' );

add_action( 'comment_form_before', 'pl_comment_form_js' );
function pl_comment_form_js() {
	if ( get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );
}
add_action( 'wp_enqueue_scripts', 'pagelines_register_js' );
function pagelines_register_js() {
	wp_register_script( 'pagelines-bootstrap-all', PL_JS . '/script.bootstrap.min.js', array( 'jquery' ), '2.0.3', true );
	wp_register_script( 'pagelines-blocks', PL_JS . '/script.blocks.js', array('jquery'), '1.0.1', true );
	wp_register_script( 'pagelines-supersize', PL_JS . '/script.supersize.js', array( 'jquery' ), '3.1.3', false );
}

add_action( 'wp_print_scripts', 'pagelines_print_js' );
function pagelines_print_js() {
	
	wp_enqueue_script( 'pagelines-bootstrap-all' );
	wp_enqueue_script( 'pagelines-blocks' );	
}

add_action( 'wp_enqueue_scripts', 'pagelines_supersize_bg' );

if ( defined( 'PL_LESS_DEV' ) && PL_LESS_DEV )
	PageLinesRenderCSS::flush_version( false );
add_filter( 'generate_rewrite_rules', array( 'PageLinesRenderCSS', 'pagelines_less_rewrite' ) );
add_action( 'wp_loaded', array( 'PageLinesRenderCSS', 'check_rules') );

/**
 * Auto load child less file.
 */
add_action( 'init', 'pagelines_check_child_less' );
function pagelines_check_child_less() {

	$lessfile = sprintf( '%s/style.less', get_stylesheet_directory() );

	if ( is_file( $lessfile ) )
		pagelines_insert_core_less( $lessfile );
}
