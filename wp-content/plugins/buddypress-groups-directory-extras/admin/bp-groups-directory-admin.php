<?php 

function etivite_bp_groups_directory_admin() {
	global $bp;

	/* If the form has been submitted and the admin referrer checks out, save the settings */
	if ( isset( $_POST['submit'] ) && check_admin_referer('etivite_bp_groups_directory_admin') ) {
	
		if( isset($_POST['gd_activity'] ) && !empty($_POST['gd_activity']) && (int)$_POST['gd_activity'] == 1 ) {

			$s = (int)$_POST['gd_activity_items'];
			
			$enabled = true;
			if ( !$s || $s < 1 ) {
				$s = 0;
				$enabled = false;
			}

			$new['activity'] = array( 'enabled' => $enabled, 'count' => $s);
		} else {
			$new['activity'] = array( 'enabled' => false, 'count' => 0 );
		}
		
		if( isset($_POST['gd_forum_link'] ) && !empty($_POST['gd_forum_link']) && (int)$_POST['gd_forum_link'] == 1 ) {
			$new['forum_link'] = true;
		} else {
			$new['forum_link'] = false;
		}
		
		update_option( 'etivite_bp_groupsdirectory', $new);
		
		$updated = true;
	}
	
	$data = maybe_unserialize( get_option( 'etivite_bp_groupsdirectory' ) );
	
	// Get the proper URL for submitting the settings form. (Settings API workaround) - boone
	$url_base = function_exists( 'is_network_admin' ) && is_network_admin() ? network_admin_url( 'admin.php?page=bp-groups-directory-settings' ) : admin_url( 'admin.php?page=bp-groups-directory-settings' );
	
?>	
	<div class="wrap">
		<h2><?php _e( 'Groups Directory Extras', 'bp-groups-directory' ); ?></h2>

		<?php if ( isset($updated) ) : echo "<div id='message' class='updated fade'><p>" . __( 'Settings Updated.', 'bp-groups-directory' ) . "</p></div>"; endif; ?>

		<form action="<?php echo $url_base; ?>" name="bp-groups-directory-settings-form" id="bp-groups-directory-settings-form" method="post">
				
			<h4><?php _e( 'Items:', 'bp-groups-directory' ); ?></h4>

			<table class="form-table">
				<tr>
					<th><label for="gd_forum_link"><?php _e('Display forum link?','bp-groups-directory') ?></label></th>
					<td><input type="checkbox" name="gd_forum_link" id="gd_forum_link" value="1"<?php if ( $data['forum_link'] ) { ?> checked="checked"<?php } ?> /></td>
				</tr>
				<tr>
					<th><label for="gd_activity"><?php _e('Display latest activity?','bp-groups-directory') ?></label></th>
					<td><input type="checkbox" name="gd_activity" id="gd_activity" value="1"<?php if ( $data['activity']['enabled'] ) { ?> checked="checked"<?php } ?> /> # of Items: <input type="text" value="<?php echo $data['activity']['count']; ?>" name="gd_activity_items" id="gd_activity_items" /> </td>
				</tr>				
			</table>
			
			<?php wp_nonce_field( 'etivite_bp_groups_directory_admin' ); ?>
			
			<p class="submit"><input type="submit" name="submit" value="Save Settings"/></p>

		</form>
		
		<h3>About:</h3>
		<div id="plugin-about" style="margin-left:15px;">
			
			<p>
			<a href="http://etivite.com/wordpress-plugins/buddypress-groups-directory-extras/">BuddyPress Groups Directory Extras - About Page</a><br/> 
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
		
	</div>
<?php
}

?>
