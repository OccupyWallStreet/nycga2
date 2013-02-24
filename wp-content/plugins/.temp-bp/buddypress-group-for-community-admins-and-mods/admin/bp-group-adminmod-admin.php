<?php
function etivite_bp_group_adminmod_admin_seed() {
	global $bp, $wpdb;	

	$data = get_option( 'etivite_bp_group_adminmod' );
	
	if ( !$data || empty( $data ) || !$data['group']['id'] )
		return 0;

	//loop all groups and seed admin group
	$groupids = $wpdb->get_results( "SELECT id FROM {$bp->groups->table_name}" );
	
	if ( !$groupids )
		return 0;
	
	//holy infinity and beyond...
	remove_action( 'groups_member_before_save', 'etivite_bp_group_adminmod_member_before_save' );
	
	$i = 0;
	foreach( $groupids as $gid ) {

		$modids = groups_get_group_mods( $gid->id );
		if ( $modids ) {
			foreach($modids as $id) {
				if ( !groups_is_user_member( $id->user_id, $data['group']['id'] ) ) {
					$member = new BP_Groups_Member( $id->user_id, $data['group']['id'] );
					$member->is_confirmed  = 1;
					$member->inviter_id    = 0;
					$member->invite_sent   = 0;
					$member->is_admin      = 0;
					$member->user_title    = '';
					$member->date_modified = bp_core_current_time();
					$member->save();
					groups_update_groupmeta( $data['group']['id'], 'total_member_count', (int) groups_get_groupmeta( $data['group']['id'], 'total_member_count') + 1 );
				}
			}
		}
		
		$adminids = groups_get_group_admins( $gid->id );
		if ( $adminids ) {
			foreach($adminids as $id) {
				if ( !groups_is_user_member( $id->user_id, $data['group']['id'] ) ) {
					$member = new BP_Groups_Member( $id->user_id, $data['group']['id'] );
					$member->is_confirmed  = 1;
					$member->inviter_id    = 0;
					$member->invite_sent   = 0;
					$member->is_admin      = 0;
					$member->user_title    = '';
					$member->date_modified = bp_core_current_time();
					$member->save();
					groups_update_groupmeta( $data['group']['id'], 'total_member_count', (int) groups_get_groupmeta( $data['group']['id'], 'total_member_count') + 1 );
				}

			}
		}
	}
	
	add_action( 'groups_member_before_save', 'etivite_bp_group_adminmod_member_before_save' );
	
	return $i;

}

function etivite_bp_group_adminmod_admin() {
	global $bp;

	if ( isset( $_POST['creategroup'] ) && check_admin_referer('etivite_bp_group_adminmod_admin_create') ) {
			
		$new = Array();

		$groupname = $_POST['group-name'];
		if ( empty( $groupname ) || !$groupname )
			$groupname = 'Admins and Mods';
		
		$groupdesc = $_POST['group-desc'];
		if ( empty( $groupdesc ) || !$groupdesc )
			$groupdesc = 'A group for community Adminstrators and Moderators';
	
		remove_action( 'groups_created_group', 'etivite_bp_autojoin_admins_created_group', 1, 1);
	
		if ( $newgid = groups_create_group( array( 'name' => $groupname, 'description' => $groupdesc, 'slug' => groups_check_slug( sanitize_title( esc_attr( $groupname ) ) ), 'date_created' => bp_core_current_time(), 'status' => 'private' ) ) ) {
			
			groups_update_groupmeta( $newgid, 'total_member_count', 1 );
			groups_update_groupmeta( $newgid, 'invite_status', 'admins' );
		
			if ( bp_is_active( 'forums' ) && '' == groups_get_groupmeta( $newgid, 'forum_id' ) && bp_forums_is_installed_correctly() )
					groups_new_group_forum( $newgid, $groupname, $groupdesc );
		
			$new['group']['id'] = $newgid;
		
			update_option( 'etivite_bp_group_adminmod', $new );	
			
			$imported = etivite_bp_group_adminmod_admin_seed();

			$updatedcreate = true;
				
		} else {
			$updatedcreate = false;
		}
	}
	
	if ( isset( $_POST['deletegroup'] ) && check_admin_referer('etivite_bp_group_adminmod_admin_delete') ) {
	
		$ddata = maybe_unserialize( get_option( 'etivite_bp_group_adminmod' ) );
		
		groups_delete_group( $ddata['group']['id'] );
		
		delete_option( 'etivite_bp_group_adminmod' );
		
		$ddate = null;
		
	}
	
	// Get the proper URL for submitting the settings form. (Settings API workaround) - boone
	$url_base = function_exists( 'is_network_admin' ) && is_network_admin() ? network_admin_url( 'admin.php?page=bp-groupadminmod-settings' ) : admin_url( 'admin.php?page=bp-groupadminmod-settings' );
	
	$data = maybe_unserialize( get_option( 'etivite_bp_group_adminmod' ) );
?>

	<div class="wrap">
		<h2><?php _e( 'Group for Community Admins and Mods', 'bp-groupforadminmod' ); ?></h2>

		<?php 
		if ( isset($updated) ) : echo "<div id='message' class='updated fade'><p>" . __( 'Settings updated.', 'bp-groupforadminmod' ) . "</p></div>"; endif;
		if ( isset($updatedcreate) ) : echo "<div id='message' class='updated fade'><p>" . __( 'New group created.', 'bp-groupforadminmod' ) . "</p></div>"; endif;
		?>
		
		<?php if ( !$data || !$data['group']['id'] || empty( $data['group']['id'] ) ) { ?>
			<form action="<?php echo $url_base ?>" name="group-adminmod-form" id="group-adminmod-form" method="post">
	
				<h4><?php _e( 'Create Admin/Mod Group', 'bp-groupforadminmod' ); ?></h4>
				
				<p class="description">Please create a group for all community group adminstrators and moderators. This process will also seed the group with existing members. All future members will be automatically added/removed depending on group admin/mod status.</p>
				
				<table>
					<tr>
						<td><label for="group-name"><?php _e( 'Group Name (required)', 'buddypress' ); ?></label></td>
						<td><input type="text" name="group-name" id="group-name" aria-required="true" value="" /></td>
					</tr>
					<tr>
						<td><label for="group-desc"><?php _e( 'Group Description (required)', 'buddypress' ) ?></label></td>
						<td><textarea name="group-desc" id="group-desc" aria-required="true"></textarea></td>
					</tr>
				</table>
	
				<?php wp_nonce_field( 'etivite_bp_group_adminmod_admin_create' ); ?>
				
				<p class="submit"><input type="submit" name="creategroup" value="Create Group"/></p>
				
			</form>
		<?php } else { ?>
			<form action="<?php echo $url_base ?>" name="group-adminmod-form" id="group-adminmod-form" method="post">
				<h4><?php _e( 'Admin/Mod Group Details', 'bp-groupforadminmod' ); ?></h4>
				<?php
				$adminmodgroup = groups_get_group( array( 'group_id' => $data['group']['id'] ) );
				echo 'Admin Mod Group: <a href="'. bp_get_group_permalink( $adminmodgroup ) .'">'. bp_get_group_name( $adminmodgroup ) .'</a>';		
				?>
				<?php wp_nonce_field( 'etivite_bp_group_adminmod_admin_delete' ); ?>
				
				<p class="submit"><input style="color:red" id="delete_group" type="submit" name="deletegroup" value="Delete Group"/></p>
				
			</form>
		<?php } ?>
		
		<h3>About:</h3>
		<div id="plugin-about" style="margin-left:15px;">
			
			<p>
				<a href="http://etivite.com/wordpress-plugins/buddypress-group-for-community-admins-and-mods/">BuddyPress Group for Adminstrators and Moderators - About Page</a>
			</p>
		
			<div class="plugin-author">
				<strong>Author:</strong> <a href="http://profiles.wordpress.org/users/etivite/"><img style="height: 24px; width: 24px;" class="photo avatar avatar-24" src="http://www.gravatar.com/avatar/9411db5fee0d772ddb8c5d16a92e44e0?s=24&amp;d=monsterid&amp;r=g" alt=""> rich @etivite</a><br/>
				<a href="http://twitter.com/etivite">@etivite</a>
			</div>
		
			<p>
			<a href="http://etivite.com">Author's site</a><br/>
			<a href="http://etivite.com/api-hooks/">Developer Hook and Filter API Reference</a><br/>
			<a href="http://etivite.com/wordpress-plugins/">WordPress Plugins</a><br/>
			</p>
		</div>
		
		<script type="text/javascript"> jQuery(document).ready( function() { jQuery("#delete_group").click( function() { if ( confirm( '<?php _e( 'Are you sure?', 'buddypress' ) ?>' ) ) return true; else return false; }); });</script>
		
	</div>
<?php
}

?>
