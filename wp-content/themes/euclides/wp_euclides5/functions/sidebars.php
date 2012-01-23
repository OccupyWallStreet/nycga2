<?php

function ci_widgets_init() {
	register_sidebar(array(
		'name' => __( 'Right Sidebar', CI_DOMAIN),
		'id' => 'sidebar-right',
		'description' => __( 'Sidebar on the right', CI_DOMAIN),
		'before_widget' => '<section class="block sidebar-item %1$s %2$s">',
		'after_widget' => '</section>',
		'before_title' => '<h3>',
		'after_title' => '</h3>',
	));	
}
add_action( 'widgets_init', 'ci_widgets_init' );

?>