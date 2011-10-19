<?php
function bp_groupblog_options_nav() {
  global $bp;
  ?>
  
	  <li id="home-personal-li">
			<a id="home" href="<?php bp_group_permalink() ?>"><?php _e( 'Home', 'buddypress' ); ?></a>
		</li>

    <?php if ( groups_is_user_admin( $bp->loggedin_user->id, bp_get_group_id() ) || groups_is_user_mod( $bp->loggedin_user->id, bp_get_group_id() ) ) : ?> 			
			<li id="admin-personal-li" >
				<a id="admin" href="<?php bp_group_permalink() ?>admin/"><?php _e( 'Admin', 'buddypress' ); ?></a>
			</li>
		<?php endif; ?>

		<?php if ( bp_group_is_visible() ) : ?>	
				
			<?php if ( bp_groupblog_is_blog_enabled ( bp_get_group_id() ) ) : ?>
				<li id="<?php echo BP_GROUPBLOG_SLUG; ?>-personal-li" class="current selected">
					<a id="<?php echo BP_GROUPBLOG_SLUG; ?>" href="<?php bp_group_permalink() ?>blog/"><?php _e( 'Blog', 'buddypress' ); ?></a>
				</li>
		  <?php endif; ?>
			
			<?php if ( bp_is_active( 'forums' ) && ( function_exists( 'bp_forums_is_installed_correctly' ) && bp_group_is_forum_enabled() && !(int) bp_get_option( 'bp-disable-forum-directory' ) ) && bp_forums_is_installed_correctly() ) : ?>
				<li id="<?php echo BP_FORUMS_SLUG; ?>-personal-li" >
					<a id="<?php echo BP_FORUMS_SLUG; ?>" href="<?php bp_group_permalink() ?>forum/"><?php _e( 'Forum', 'buddypress' ); ?></a>
				</li>
			<?php endif; ?>
			
			<li id="<?php echo BP_MEMBERS_SLUG; ?>-personal-li" >
				<a id="<?php echo BP_MEMBERS_SLUG; ?>" href="<?php bp_group_permalink() ?>members/"><?php _e( 'Members', 'buddypress' ); ?> (<?php bp_group_total_members() ?>)</a>
			</li>
			
			<li id="invite-personal-li" >
				<a id="invite" href="<?php bp_group_permalink() ?>send-invites/"><?php _e( 'Send Invites', 'buddypress' ); ?></a>
			</li>
			
		<?php elseif ( !bp_group_is_visible() && bp_get_group_status() != 'hidden' ) : ?>
		
			<li id="request-membership-personal-li" >
				<a id="request-membership" href="<?php bp_group_permalink() ?>request-membership/"><?php _e( 'Request Membership', 'buddypress' ); ?></a>
			</li>
			
		<?php endif; ?>
	
	<?php
}															
?>