<div class="profile-right-nav">

	<div class="avatar-photograph">
		<div class="avatar-photo photosize-<?php if (defined('BP_AVATAR_FULL_WIDTH')) { echo BP_AVATAR_FULL_WIDTH; } ?>"><div class="photo"><a href="<?php bp_user_link() ?>"><?php bp_displayed_user_avatar( 'type=full' ) ?></a></div></div>
		<?php
		
 		$social_icons = get_option('dev_network_allow_social_icons');
 		
 		if ($social_icons == "yes") { ?>
		
		<div class="avatar-social-links">
			<ul>
				<?php if ($socialLink = bp_get_profile_field_data( 'field=linkedin' )) { ?>
				<li><a href="<?php echo $socialLink; ?>" target="_blank"><img src="<?php bloginfo('template_directory'); ?>/_inc/images/icons/social-icons/linkedin-32x32.png" alt="" /></a></li>
				<?php } ?>
				<?php if ($socialLink = bp_get_profile_field_data( 'field=facebook' )) { ?>
				<li><a href="<?php echo $socialLink; ?>" target="_blank"><img src="<?php bloginfo('template_directory'); ?>/_inc/images/icons/social-icons/facebook-32x32.png" alt="" /></a></li>
				<?php } ?>
				<?php if ($socialLink = bp_get_profile_field_data( 'field=twitter' )) { ?>
				<li><a href="<?php echo $socialLink; ?>" target="_blank"><img src="<?php bloginfo('template_directory'); ?>/_inc/images/icons/social-icons/twitter-32x32.png" alt="" /></a></li>
				<?php } ?>
				<?php if ($socialLink = bp_get_profile_field_data( 'field=foursquare' )) { ?>
				<li><a href="<?php echo $socialLink; ?>" target="_blank"><img src="<?php bloginfo('template_directory'); ?>/_inc/images/icons/social-icons/foursquare-32x32.png" alt="" /></a></li>
				<?php } ?>
				<?php if ($socialLink = bp_get_profile_field_data( 'field=youtube' )) { ?>
				<li><a href="<?php echo $socialLink; ?>" target="_blank"><img src="<?php bloginfo('template_directory'); ?>/_inc/images/icons/social-icons/youtube-32x32.png" alt="" /></a></li>
				<?php } ?>
			</ul>
			<div class="clear"></div>
		</div>
		<?php } ?>
	</div>

	<div class="avatar-action-links">
		<div id="item-buttons">

			<?php do_action( 'bp_member_header_actions' ); ?>

		</div>
	</div>
	
	<?php if (function_exists('bp_get_profile_field_data')) { ?>
	<?php
	 /***
	  * If you'd like to show specific profile fields here use:
	  * bp_profile_field_data( 'field=About Me' ); -- Pass the name of the field
	  */
	  
	  $userBio = bp_get_profile_field_data( 'field=bio' );
	  
	  if ($userBio) {
	?>
	<div class="avatar-summary">
		<h3><?php bp_displayed_user_fullname() ?><br/><?php _e( 'In A Nutshell', 'network' ) ?></h3>
		<p><?php echo $userBio; ?></p>
	</div>
	<?php } } ?>
			<?php if ( function_exists( 'bp_message_get_notices' ) ) : ?>
				<?php bp_message_get_notices(); /* Site wide notices to all users */ ?>
			<?php endif; ?>
</div> <!-- profile-right-nav -->