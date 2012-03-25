<?php

// Register Widgets
function tj_widgets_init() {

	// Blog Sidebar
	register_sidebar( array (
		'name' => __( 'Blog Sidebar', 'themejunkie' ),
		'id' => 'blog-sidebar',
		'description' => __( 'Blog Sidebar', 'themejunkie' ),
		'before_widget' => '<div id="%1$s" class="widget clear %2$s">',
		'after_widget' => "</div>",
		'before_title' => '<h3 class="widget-title"><span>',
		'after_title' => '</span></h3>',
	) );

	// Page Sidebar
	register_sidebar( array (
		'name' => __( 'Page Sidebar', 'themejunkie' ),
		'id' => 'page-sidebar',
		'description' => __( 'Page Sidebar', 'themejunkie' ),
		'before_widget' => '<div id="%1$s" class="widget clear %2$s">',
		'after_widget' => "</div>",
		'before_title' => '<h3 class="widget-title"><span>',
		'after_title' => '</span></h3>',
	) );

	// Portfolio Sidebar
	register_sidebar( array (
		'name' => __( 'Porfolio Sidebar', 'themejunkie' ),
		'id' => 'portfolio-sidebar',
		'description' => __( 'Portfolio Sidebar', 'themejunkie' ),
		'before_widget' => '<div id="%1$s" class="widget clear %2$s">',
		'after_widget' => "</div>",
		'before_title' => '<h3 class="widget-title"><span>',
		'after_title' => '</span></h3>',
	) );		

	// Footer Widgets Area 1
	register_sidebar( array (
		'name' => __( 'Footer Widgets Area 1', 'themejunkie' ),
		'id' => 'footer-widget-area-1',
		'description' => __( 'Footer Widgets Area #1', 'themejunkie' ),
		'before_widget' => '<div id="%1$s" class="widget footer-widget clear %2$s">',
		'after_widget' => "</div>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
	
	// Footer Widgets Area 2
	register_sidebar( array (
		'name' => __( 'Footer Widgets Area 2', 'themejunkie' ),
		'id' => 'footer-widget-area-2',
		'description' => __( 'Footer Widgets Area #2', 'themejunkie' ),
		'before_widget' => '<div id="%1$s" class="widget footer-widget clear %2$s">',
		'after_widget' => "</div>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
	
	// Footer Widgets Area 3
	register_sidebar( array (
		'name' => __( 'Footer Widgets Area 3', 'themejunkie' ),
		'id' => 'footer-widget-area-3',
		'description' => __( 'Footer Widgets Area #3', 'themejunkie' ),
		'before_widget' => '<div id="%1$s" class="widget footer-widget clear %2$s">',
		'after_widget' => "</div>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
	
	// Footer Widgets Area 4
	register_sidebar( array (
		'name' => __( 'Footer Widgets Area 4', 'themejunkie' ),
		'id' => 'footer-widget-area-4',
		'description' => __( 'Footer Widgets Area #4', 'themejunkie' ),
		'before_widget' => '<div id="%1$s" class="widget footer-widget clear %2$s">',
		'after_widget' => "</div>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );	
	
}
add_action( 'init', 'tj_widgets_init' );

?>