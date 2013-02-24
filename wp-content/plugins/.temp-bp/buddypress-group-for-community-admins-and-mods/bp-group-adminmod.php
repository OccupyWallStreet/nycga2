<?php
if ( !defined( 'ABSPATH' ) ) exit;

//for each member save - check if member is_mod/admin flag status changed
function etivite_bp_group_adminmod_member_before_save( $member ) {
	global $wpdb, $bp;
	
	//no data - no bother
	$data = get_option( 'etivite_bp_group_adminmod' );
	if ( !$data || empty( $data ) || !$data['group']['id'] )
		return;
		
	$is_member = groups_is_user_member( $member->user_id, $data['group']['id'] );

	//holy infinity and beyond...
	remove_action( 'groups_member_before_save', 'etivite_bp_group_adminmod_member_before_save' );

	//check if member is_mod/admin and add to group if not a member
	if ( ( $member->is_mod || $member->is_admin ) && !$is_member ) {
		$new_member = new BP_Groups_Member( $member->user_id, $data['group']['id'] );
		$new_member->is_confirmed  = 1;
		$new_member->inviter_id    = 0;
		$new_member->invite_sent   = 0;
		$new_member->is_admin      = 0;
		$new_member->user_title    = '';
		$new_member->date_modified = bp_core_current_time();
		$new_member->save();
		groups_update_groupmeta( $data['group']['id'], 'total_member_count', (int) groups_get_groupmeta( $data['group']['id'], 'total_member_count') + 1 );
	}
	
	//check if member is admin/mod of any groups. If not, remove from adminmod group - boone!
	$is_admin_or_mod_anywhere_else = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$bp->groups->table_name_members} WHERE user_id = %d AND ( is_admin = 1 OR is_mod = 1 ) AND group_id != %d", $member->user_id, $member->group_id ) );

	if ( !$is_admin_or_mod_anywhere_else && !$member->is_admin && !$member->is_mod ) {
		$member = new BP_Groups_Member( $member->user_id, $data['group']['id'] );
		$member->remove();
		groups_update_groupmeta( $data['group']['id'], 'total_member_count', (int) groups_get_groupmeta( $data['group']['id'], 'total_member_count') - 1 );
	}
	
	add_action( 'groups_member_before_save', 'etivite_bp_group_adminmod_member_before_save' );

}
add_action( 'groups_member_before_save', 'etivite_bp_group_adminmod_member_before_save' );

function etivite_bp_group_adminmod_remove_member( $user_id, $group_id ) {
	global $bp;

	//admins can't remove self - so check if mod of a group
	if ( !groups_is_user_mod( $user_id, $group_id ) )
		return;

	$data = get_option( 'etivite_bp_group_adminmod' );
	if ( !$data || empty( $data ) || !$data['group']['id'] )
		return;
		
	//make sure a member of adminmod group
	if ( !groups_is_user_member( $user_id, $data['group']['id'] ) )
		return;

	$member = new BP_Groups_Member( $user_id, $data['group']['id'] );
	$member->remove();
}
add_action( 'groups_remove_member', 'etivite_bp_group_adminmod_remove_member' );

?>
