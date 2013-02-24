<?php
/*
Author: OWS
URL: htp://nycga.net

This is where you can drop your custom functions or
just edit things like thumbnail sizes, header images, 
sidebars, comments, ect.
*/


/************* ACTIVE SIDEBARS ********************/

// Sidebars & Widgetizes Areas
function nycga_widgets_init() {

    register_sidebar(array(
        'id' => 'header1',
        'name' => 'Header Right Sidebar',
        'description' => 'Used in the header.',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4 class="widgettitle">',
        'after_title' => '</h4>',
    ));

    register_sidebar(array(
        'id' => 'homecontent1',
        'name' => 'Homepage Left Content Widget',
        'description' => 'Used only on the homepage page template.',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4 class="widgettitle">',
        'after_title' => '</h4>',
    ));

    register_sidebar(array(
        'id' => 'homecontent2',
        'name' => 'Homepage Right Content Widget',
        'description' => 'Used only on the homepage page template.',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4 class="widgettitle">',
        'after_title' => '</h4>',
    ));

    register_sidebar(array(
        'id' => 'footer1',
        'name' => 'Footer Left Sidebar',
        'description' => 'Used in the footer.',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4 class="widgettitle">',
        'after_title' => '</h4>',
    ));

    register_sidebar(array(
        'id' => 'footer2',
        'name' => 'Footer Middle Sidebar',
        'description' => 'Used in the footer.',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4 class="widgettitle">',
        'after_title' => '</h4>',
    ));

    register_sidebar(array(
        'id' => 'footer3',
        'name' => 'Footer Right Sidebar',
        'description' => 'Used in the footer.',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4 class="widgettitle">',
        'after_title' => '</h4>',
    ));
    
} // don't remove this bracket!
add_action( 'widgets_init', 'nycga_widgets_init' );


?>