<?php

function my_bp_groups_forum_first_tab() {
	global $bp;
	
	$bp->bp_options_nav['groups']['forum']['position'] = '10';
	$bp->bp_options_nav['groups']['group-events']['position'] = '20';
	$bp->bp_options_nav['groups']['members']['position'] = '30';
	$bp->bp_options_nav['groups']['docs']['position'] = '40';
	$bp->bp_options_nav['groups']['blog']['position'] = '50';
	$bp->bp_options_nav['groups']['send-invites']['position'] = '60';
	$bp->bp_options_nav['groups']['home']['position'] = '60';
	$bp->bp_options_nav['groups']['group-events']['name'] = 'Events';
	$bp->bp_options_nav['groups']['home']['name'] = 'Activity';
	$bp->bp_options_nav['groups']['send-invites']['name'] = 'Invite';
	$bp->bp_options_nav['groups']['notifications']['name'] = 'messaging';
}
add_action('wp', 'my_bp_groups_forum_first_tab');



//Make forum default tab
function redirect_to_forum() {
	global $bp;
	
	$path = clean_url( $_SERVER['REQUEST_URI'] );
	
	$path = apply_filters( 'bp_uri', $path );
	
	if ( bp_is_group_home() && strpos( $path, $bp->bp_options_nav['groups']['home']['slug'] ) === false )
		bp_core_redirect( $path . $bp->bp_options_nav['groups']['forum']['slug'] . '/' );
}
add_action( 'wp', 'redirect_to_forum' );

?>
