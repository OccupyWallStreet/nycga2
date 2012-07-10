<?php
if ( !defined( 'ABSPATH' ) ) exit;

function etivite_bp_activity_atgroups_find_group_mentions( $content ) {
	$pattern = '/[@][\(]+([A-Za-z0-9-_\.@]+)[\)]/';
	preg_match_all( $pattern, $content, $usernames );

	// Make sure there's only one instance of each username
	if ( !$usernames = array_unique( $usernames[1] ) )
		return false;

	return $usernames;
}

function etivite_bp_activity_atgroups_at_name_filter( $content, $activity_id = 0 ) {
	$groupnames = etivite_bp_activity_atgroups_find_group_mentions( $content );

	foreach( (array)$groupnames as $groupname ) {
		
		$group_id = BP_Groups_Group::group_exists( $groupname );

		if ( empty( $group_id ) )
			continue;

		$content = preg_replace( '/[@][\(]'. $groupname .'\b[\)]/', "<a href='" .  bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $groupname . '/' . "' rel='nofollow'>@($groupname)</a>", $content );
	}
	
	return $content;
}


function etivite_bp_activity_atgroups_at_name_filter_updates( $activity ) {
	// Only run this function once for a given activity item
	remove_filter( 'bp_activity_after_save', 'etivite_bp_activity_atgroups_at_name_filter_updates' );

	// Run the content through the linking filter, making sure to increment mention count
	$activity->content = etivite_bp_activity_atgroups_at_name_filter( $activity->content, $activity->id );

	// Resave the activity with the new content
	$activity->save();
}



function etivite_bp_activity_atgroups_postit_filter_updates( $activity ) {
	global $bp;
	
	//bail if activity already exists
	if ( $activity->id )
		return;
	
	//only care about activity updates
	if ( $activity->type != 'activity_update' )
		return;
		
	//discard if already a group update
	if ( !empty( $activity->item_id ) )
		return;
	
	// Only run this function once for a given activity item
	remove_filter( 'bp_activity_before_save', 'etivite_bp_activity_atgroups_postit_filter_updates' );

	$content = $activity->content;

	$pattern = '/[=][\(]+([A-Za-z0-9-_\.@]+)[\)]/';
	preg_match_all( $pattern, $content, $groupnames );

	//no match, no care
	if ( !$groupnames[1] )
		return;

	//Make sure there's only one instance of each username
	if ( !$groupnames = array_unique( $groupnames[1] ) )
		return;
	
	//find the pushto group - log the first valid instance
	foreach( (array)$groupnames as $groupname ) {
		
		$group_id = BP_Groups_Group::group_exists( $groupname );

		if ( empty( $group_id ) )
			continue;

		//if match - push it for later
		if ( empty( $first_group_id ) )
			$first_group_id = $group_id;
			
		$activity->content = preg_replace( '/[=][\(]'. $groupname .'\b[\)]/', "<a href='" .  bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $groupname . '/' . "' rel='nofollow'>@($groupname)</a>", $content );
	}

	//mimic the stream and update functions from core from this point on...
	if ( empty( $activity->content ) || !strlen( trim( $activity->content ) ) || empty( $activity->user_id ) || empty( $first_group_id ) )
		return;

	//failsafe is something went fubar
	if ( !$bp->groups->current_group = new BP_Groups_Group( $first_group_id ) )
		return;

	// Be sure the user is a member of the group before posting.
	if ( !is_super_admin() && !groups_is_user_member( $user_id, $first_group_id ) )
		return;
		
	// If the group is not public, hide the activity sitewide.
	if ( isset( $bp->groups->current_group->status ) && 'public' == $bp->groups->current_group->status )
		$hide_sitewide = false;
	else
		$hide_sitewide = true;

	$activity_action  = sprintf( __( '%1$s posted an update in the group %2$s', 'buddypress'), bp_core_get_userlink( $activity->user_id ), '<a href="' . bp_get_group_permalink( $bp->groups->current_group ) . '">' . esc_attr( $bp->groups->current_group->name ) . '</a>' );
	
	$activity->action  = apply_filters( 'groups_activity_new_update_action',  $activity_action  );
	$activity->content = apply_filters( 'groups_activity_new_update_content', $activity->content );
	$activity->item_id = $first_group_id;
	$activity->component = $bp->groups->id;
	$activity->primary_link = '';
	$activity->secondary_item_id = false;
	$activity->recorded_time = bp_core_current_time();
	$activity->hide_sitewide = $hide_sitewide;
	
	//make it believe
	groups_update_groupmeta( $first_group_id, 'last_activity', bp_core_current_time() );

}

?>
