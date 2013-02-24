<?php
ignore_user_abort(true);

if ( !empty($_POST) || defined('DOING_AJAX') || defined('DOING_CRON') )
	die();
	
	
 if ( !defined('ABSPATH') ) {
	/** Set up WordPress environment */
	require_once( '/wp-load.php');
}

echo 'Running WPeMatico external WP-Crom';

$args = array( 'post_type' => 'wpematico', 'orderby' => 'ID', 'order' => 'ASC', 'numberposts' => -1 );
$campaigns = get_posts( $args );
foreach( $campaigns as $post ) {
	$campaign = WPeMatico :: get_campaign( $post->ID );
	$activated = $campaign['activated'];
	$cronnextrun = $campaign['cronnextrun'];
	if ( !$activated )
		continue;
	if ( $cronnextrun <= current_time('timestamp') ) {
		echo $post->post_title.' ';
		WPeMatico :: wpematico_dojob( $post->ID );
	}
}
echo " Success !";
