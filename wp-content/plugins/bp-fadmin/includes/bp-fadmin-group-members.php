<?php

if ( defined( 'BP_FADMIN_IS_INSTALLED' ) ) {

	function bp_fadmin_setup_nav_groups() {
		global $bp;


		/* Create sub nav item for this component */
		bp_core_new_subnav_item( array(
			'name' => __( 'Group Members', 'bp-fadmin' ),
			'slug' => 'group-members',
			'parent_slug' => $bp->fadmin->slug,
			'parent_url' => $bp->loggedin_user->domain . $bp->fadmin->slug . '/',
			'screen_function' => 'bp_fadmin_screen_groups',
			'position' => 30
		) );

	}
	add_action( 'wp', 'bp_fadmin_setup_nav_groups', 2 );
	add_action( 'admin_menu', 'bp_fadmin_setup_nav_groups', 2 );




	function bp_fadmin_screen_groups() {
		global $bp;
		
		if ( isset( $_POST['save-type'] ) ) {
			bp_fadmin_groups_process_form();
		}
		
		do_action( 'bp_fadmin_screen_groups' );

		add_action( 'bp_template_title', 'bp_fadmin_screen_groups_title' );
		add_action( 'bp_template_content', 'bp_fadmin_screen_groups_content' );

		bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
	}

	function bp_fadmin_screen_groups_title() {
		_e( 'Groups Management', 'bp-fadmin' );
	}

	function bp_fadmin_screen_groups_content() {
		global $bp; ?>

		<h4><?php _e( 'Welcome to Group Member Management', 'bp-fadmin' ); ?></h4>

		<p><?php _e( 'This screen provides summary data for groups you are an admin of and the ability to quickly promote/demote/ban members across all those groups.', 'bp-fadmin' ); ?></p>
		
		<p><?php _e( 'Please note: You will not appear in any of the group lists as you cannot modify your own group status with this interface.', 'bp-fadmin' ); ?></p>
		
		<?php
		if ( bp_has_groups( 'per_page=999' ) ) : ?>

			<ul id="groups-list" class="item-list">
			<?php 
			while ( bp_groups() ) : bp_the_group(); 
			
				if ( groups_is_user_admin( $bp->loggedin_user->id, bp_get_group_id() ) ) {	
					?>
					<li>
						<div class="item">
						
							<div class="item-title"><a href="<?php bp_group_permalink() ?>"><?php bp_group_name() ?></a></div>
							
							<form action="<?php echo $bp->loggedin_user->domain . $bp->fadmin->slug . '/group-members/'; ?>" method="post" name="group-members-options-form">
								
								<input type="hidden" name="group-id" value="<?php bp_group_id(); ?>"/>

								<?php _e( 'Set ticked to', 'bp-fadmin'); ?>: 

									<select name="member-setting">

										<?php 
										echo '
										<option value="blank" selected="selected">--------------------------------</option>
										<option value="member-this">' . __( 'THIS group - Member', 'bp_fadmin' ) . '</option>
										<option value="moderator-this">' . __( 'THIS group - Mod', 'bp_fadmin' ) . '</option>
										<option value="administrator-this">' . __( 'THIS group - Admin', 'bp_fadmin' ) . '</option>
										<option value="banned-this">' . __( 'THIS group - Ban', 'bp_fadmin' ) . '</option>
										<option value="blank">--------------------------------</option>
										<option value="member-all">' . __( 'ALL groups - Member', 'bp_fadmin' ) . '</option>
										<option value="moderator-all">' . __( 'ALL groups - Mod', 'bp_fadmin' ) . '</option>
										<option value="administrator-all">' . __( 'ALL groups - Admin', 'bp_fadmin' ) . '</option>
										<option value="banned-all">' . __( 'ALL groups - Ban', 'bp_fadmin' ) . '</option>
										';
										?>
										
									</select>

									&nbsp;&nbsp;
									
									<button name="save-type" type="submit" value="group"><?php _e( 'Save', 'bp-fadmin'); ?></button>

								</p>
								
								<p>
									<?php
									$args = array(
										 'exclude_admins_mods' => false,
										 'exclude_banned' => false,
										 'per_page ' => 9999,
										 'group_id' => bp_get_group_id()
									);
									
									$alt_row_admins = 1;
									$alt_row_mods = 0;
									$alt_row_members = 1;
									$alt_row_banned= 0;
									$group_admins_output = '';
									$group_mods_output = '';
									$group_members_output = '';
									$group_banned_output = '';
									$group_combined_output = '';
									
									if ( bp_group_has_members( $args ) ) :
									
									while ( bp_group_members() ) : bp_group_the_member();
									
									// skip is this is the current user
									if ( bp_get_group_member_id() == $bp->loggedin_user->id ) {
										continue;
									}
									
									if ( groups_is_user_admin( bp_get_group_member_id(), bp_get_group_id() ) ) {
										// admins
										if ( $alt_row_admins % 2 == 0 ) {
											$alt_tag = 'background-color:#EBEBEB;';
										} else {
											$alt_tag = '';
										}
										
										$group_admins_output .= '<tr style="' . $alt_tag .'"><td style="width:50px;"><input type="checkbox" name="group-user-ids[]" value="' . bp_get_group_member_id() . '" /></td>';
										
										$group_admins_output .= '<td>' . bp_get_group_member_link() . '</td></tr>';
										
										$alt_row_admins++; 
										
									} elseif ( groups_is_user_mod( bp_get_group_member_id(), bp_get_group_id() ) ) {
										// moderators
										if ( $alt_row_mods % 2 == 0 ) {
											$alt_tag = 'background-color:#EBEBEB;';
										} else {
											$alt_tag = '';
										}
										
										$group_mods_output .= '<tr style="' . $alt_tag .'"><td style="width:50px;"><input type="checkbox" name="group-user-ids[]" value="' . bp_get_group_member_id() . '" /></td>';
										
										$group_mods_output .= '<td>' . bp_get_group_member_link() . '</td></tr>';
										$alt_row_mods++; 
									} elseif ( groups_is_user_member( bp_get_group_member_id(), bp_get_group_id() ) ) {
										// members
										if ( $alt_row_members % 2 == 0 ) {
											$alt_tag = 'background-color:#EBEBEB;';
										} else {
											$alt_tag = '';
										}
										
										$group_members_output .= '<tr style="' . $alt_tag .'"><td style="width:50px;"><input type="checkbox" name="group-user-ids[]" value="' . bp_get_group_member_id() . '" /></td>';
										
										$group_members_output .= '<td>' . bp_get_group_member_link() . '</td></tr>';
										
										$alt_row_members++; 
									} elseif ( groups_is_user_banned( bp_get_group_member_id(), bp_get_group_id() ) ) {
										// banned users
										if ( $alt_row_banned % 2 == 0 ) {
											$alt_tag = 'background-color:#EBEBEB;';
										} else {
											$alt_tag = '';
										}
										
										$group_banned_output .= '<tr style="' . $alt_tag .'"><td style="width:50px;"><input type="checkbox" name="group-user-ids[]" value="' . bp_get_group_member_id() . '" /></td>';
										
										$group_banned_output .= '<td>' . bp_get_group_member_link() . '</td></tr>';
										
										$alt_row_banned++; 
									} 
									
									endwhile;
									
									if ( $group_admins_output ) {
										echo '<h5>' . __( 'Admins', 'bp-fadmin' ) . '</h5>';
										echo '<table>' . $group_admins_output . '</table>';
									}
									if ( $group_mods_output ) {
										echo '<h5>' . __( 'Moderators', 'bp-fadmin' ) . '</h5>';
										echo '<table>' . $group_mods_output . '</table>';
									}
									if ( $group_members_output ) {
										echo '<h5>' . __( 'Members', 'bp-fadmin' ) . '</h5>';
										echo '<table>' . $group_members_output . '</table>';
									}
									if ( $group_banned_output ) {
										echo '<h5>' . __( 'Banned', 'bp-fadmin' ) . '</h5>';
										echo '<table>' . $group_banned_output . '</table>';
									}
									
									endif;	
									?>
								</p>
								
							</form>

						</div>

					<div class="clear"></div>

					</li>
					<?php
				}

			endwhile; 
			?>
			</ul>

		<?php else: ?>

			<div id="message" class="info">

				<p><?php _e( 'There were no group members found.', 'bp-fadmin' ) ?></p>

			</div>
			
		<?php endif; 		

	}
	
	function bp_fadmin_groups_process_form() {
		global $bp;
		
		$group_user_ids = $_POST['group-user-ids'];
		$this_group_id = $_POST['group-id'];
		$membership_level = $_POST['member-setting'];
		
		if ( is_array( $group_user_ids ) && $this_group_id && $membership_level != 'blank' ) {
		
			switch ( $membership_level ) {
			
				case 'member-this': 
					foreach ( $group_user_ids as $group_user_id ) {
						// if user is mod or admin, demote
						if ( groups_is_user_mod( $group_user_id, $this_group_id ) || groups_is_user_admin( $group_user_id, $this_group_id ) ) {
							groups_demote_member( $group_user_id, $this_group_id );
						}
						// unban if banned
						if ( groups_is_user_banned( $group_user_id, $this_group_id ) ) {
							groups_unban_member( $group_user_id, $this_group_id );
						}	
					}		
					break;
				
				case 'moderator-this': 
					foreach ( $group_user_ids as $group_user_id ) {
						// if user already mod, ignore
						if ( groups_is_user_mod( $group_user_id, $this_group_id ) ) {
							continue;
						}
						// unban first if needed
						if ( groups_is_user_banned( $group_user_id, $this_group_id ) ) {
							groups_unban_member( $group_user_id, $this_group_id );
						}	
						// if user is admin need to demote, not promote
						if ( groups_is_user_admin( $group_user_id, $this_group_id ) ) {
							groups_demote_member( $group_user_id, $this_group_id );
						}
						// user is member so we can promote them
						groups_promote_member( $group_user_id, $this_group_id, 'mod' );
					}		
					break;
				
				case 'administrator-this': 
					foreach ( $group_user_ids as $group_user_id ) {
						// if user already admin, ignore
						if ( groups_is_user_admin( $group_user_id, $this_group_id ) ) {
							continue;
						}
						// unban first if needed
						if ( groups_is_user_banned( $group_user_id, $this_group_id ) ) {
							groups_unban_member( $group_user_id, $this_group_id );
						}
						// user is member so we can promote them
						groups_promote_member( $group_user_id, $this_group_id, 'admin' );
					}		
					break;
				
				case 'banned-this': 
					foreach ( $group_user_ids as $group_user_id ) {
						// if user is mod or admin, demote
						if ( groups_is_user_mod( $group_user_id, $this_group_id ) || groups_is_user_admin( $group_user_id, $this_group_id ) ) {
							groups_demote_member( $group_user_id, $this_group_id );
						}
						// ban the fudge out of them
						groups_ban_member( $group_user_id, $this_group_id );
					}		
					break;
				
				case 'member-all': 
					if ( bp_has_groups( 'per_page=999' ) ) :
						while ( bp_groups() ) : bp_the_group(); 
							if ( groups_is_user_admin( $bp->loggedin_user->id, bp_get_group_id() ) ) {
								foreach ( $group_user_ids as $group_user_id ) {
									// if user is mod or admin, demote
									if ( groups_is_user_mod( $group_user_id, bp_get_group_id() ) || groups_is_user_admin( $group_user_id, bp_get_group_id() ) ) {
										groups_demote_member( $group_user_id, bp_get_group_id() );
									}
									// unban if banned
									if ( groups_is_user_banned( $group_user_id, bp_get_group_id() ) ) {
										groups_unban_member( $group_user_id, bp_get_group_id() );
									}	
								}		
							}
						endwhile;
					endif;
					break;
									
				case 'moderator-all': 
					if ( bp_has_groups( 'per_page=999' ) ) :
						while ( bp_groups() ) : bp_the_group(); 
							if ( groups_is_user_admin( $bp->loggedin_user->id, bp_get_group_id() ) ) {
								foreach ( $group_user_ids as $group_user_id ) {
									// if user already mod, ignore
									if ( groups_is_user_mod( $group_user_id, bp_get_group_id() ) ) {
										continue;
									}
									// unban first if needed
									if ( groups_is_user_banned( $group_user_id, bp_get_group_id()) ) {
										groups_unban_member( $group_user_id, bp_get_group_id() );
									}	
									// if user is admin need to demote, not promote
									if ( groups_is_user_admin( $group_user_id, bp_get_group_id() ) ) {
										groups_demote_member( $group_user_id, bp_get_group_id() );
									}
									// user is member so we can promote them
									groups_promote_member( $group_user_id, bp_get_group_id(), 'mod' );
								}		
							}
						endwhile;
					endif;
					break;
				
				case 'administrator-all': 
					if ( bp_has_groups( 'per_page=999' ) ) :
						while ( bp_groups() ) : bp_the_group(); 
							if ( groups_is_user_admin( $bp->loggedin_user->id, bp_get_group_id() ) ) {
								foreach ( $group_user_ids as $group_user_id ) {
									// if user already admin, ignore
									if ( groups_is_user_admin( $group_user_id, bp_get_group_id() ) ) {
										continue;
									}
									// unban first if needed
									if ( groups_is_user_banned( $group_user_id, bp_get_group_id() ) ) {
										groups_unban_member( $group_user_id, bp_get_group_id() );
									}
									// user is member so we can promote them
									groups_promote_member( $group_user_id, bp_get_group_id(), 'admin' );
								}		
							}
						endwhile;
					endif;
					break;
				
				case 'banned-all': 
					if ( bp_has_groups( 'per_page=999' ) ) :
						while ( bp_groups() ) : bp_the_group(); 
							if ( groups_is_user_admin( $bp->loggedin_user->id, bp_get_group_id() ) ) {
								foreach ( $group_user_ids as $group_user_id ) {
									// if user is mod or admin, demote
									if ( groups_is_user_mod( $group_user_id, bp_get_group_id() ) || groups_is_user_admin( $group_user_id, bp_get_group_id() ) ) {
										groups_demote_member( $group_user_id, bp_get_group_id() );
									}
									// ban the fudge out of them
									groups_ban_member( $group_user_id, bp_get_group_id() );
								}		
							}
						endwhile;
					endif;
					break;		
										
			}
				
			bp_core_add_message( __( 'Settings saved successfully', 'bp-fadmin' ) );

			bp_core_redirect( $bp->fadmin->slug );
				
		} else {
			
			bp_core_add_message( __( 'Details not saved', 'bp-fadmin' ), 'error' );

			bp_core_redirect( $bp->fadmin->slug );
			
		}
		
	}
	
	function bp_fadmin_register_groups( $fadmin_extensions ) {
	
		$this_extension = new stdClass;
		$this_extension->name = __( 'Group Members', 'bp-fadmin');
		$this_extension->slug = 'group-members';
		$this_extension->description = __( 'Site-wide controls of members within your groups.', 'bp-fadmin');
		
		$fadmin_extensions[] = $this_extension;
		
		return $fadmin_extensions;
	}
	add_filter( 'bp_fadmin_register_extension', 'bp_fadmin_register_groups' );
	
}
?>