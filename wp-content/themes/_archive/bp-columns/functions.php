<?php
add_action( 'widgets_init', 'bpcol_register_sidebars' );

function bpcol_register_sidebars() {
register_sidebar(
	array(
		'id' => 'left sidebar',
		'name' => 'Left Sidebar',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widgettitle">',
		'after_title' => '</h3>'
	)
);
}
global $content_width;
$content_width = 500;
?>