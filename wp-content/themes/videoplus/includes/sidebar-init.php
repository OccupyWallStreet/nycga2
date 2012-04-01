<?php

// Register Widgets
function tj_widgets_init() {

	// Right Sidebar - Home
	register_sidebar( array (
		'name' => __( 'Right Sidebar - Home', 'themejunkie' ),
		'id' => 'right-sidebar-home',
		'description' => __( 'Right sidebar on homepage', 'themejunkie' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => "</div></div>",
		'before_title' => '<h3 class="widget-title"><span>',
		'after_title' => '</span></h3><div class="widget-content">',
	) );

	// Right Sidebar - Pages
	register_sidebar( array (
		'name' => __( 'Right Sidebar - Pages', 'themejunkie' ),
		'id' => 'right-sidebar-pages',
		'description' => __( 'Right sidebar on pages', 'themejunkie' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => "</div></div>",
		'before_title' => '<h3 class="widget-title"><span>',
		'after_title' => '</span></h3><div class="widget-content">',
	) );
	
	// Right Sidebar - Posts
	register_sidebar( array (
		'name' => __( 'Right Sidebar - Posts', 'themejunkie' ),
		'id' => 'right-sidebar-posts',
		'description' => __( 'Right sidebar on posts', 'themejunkie' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => "</div></div>",
		'before_title' => '<h3 class="widget-title"><span>',
		'after_title' => '</span></h3><div class="widget-content">',
	) );	

	// Left Sidebar - Posts
	register_sidebar( array (
		'name' => __( 'Left Sidebar - Posts', 'themejunkie' ),
		'id' => 'left-sidebar-posts',
		'description' => __( 'Left sidebar on posts', 'themejunkie' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => "</div></div>",
		'before_title' => '<h3 class="widget-title"><span>',
		'after_title' => '</span></h3><div class="widget-content">',
	) );

	// Footer Widget Area 1
	register_sidebar( array (
		'name' => __( 'Footer Widget Area 1', 'themejunkie' ),
		'id' => 'footer-widget-area-1',
		'description' => __( 'The bottom widget area', 'themejunkie' ),
		'before_widget' => '<div id="%1$s" class="widget footer-widget %2$s">',
		'after_widget' => "</div>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
	
	// Footer Widget Area 2
	register_sidebar( array (
		'name' => __( 'Footer Widget Area 2', 'themejunkie' ),
		'id' => 'footer-widget-area-2',
		'description' => __( 'The bottom widget area', 'themejunkie' ),
		'before_widget' => '<div id="%1$s" class="widget footer-widget %2$s">',
		'after_widget' => "</div>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
	
	// Footer Widget Area 3
	register_sidebar( array (
		'name' => __( 'Footer Widget Area 3', 'themejunkie' ),
		'id' => 'footer-widget-area-3',
		'description' => __( 'The bottom widget area', 'themejunkie' ),
		'before_widget' => '<div id="%1$s" class="widget footer-widget %2$s">',
		'after_widget' => "</div>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
	
	// Footer Widget Area 4
	register_sidebar( array (
		'name' => __( 'Footer Widget Area 4', 'themejunkie' ),
		'id' => 'footer-widget-area-4',
		'description' => __( 'The bottom widget area', 'themejunkie' ),
		'before_widget' => '<div id="%1$s" class="widget footer-widget %2$s">',
		'after_widget' => "</div>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );	
	
}
add_action( 'init', 'tj_widgets_init' );

?>