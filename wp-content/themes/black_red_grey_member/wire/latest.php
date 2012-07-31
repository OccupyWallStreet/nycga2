<?php get_header() ?>
<div id="profile-wrapper">
<div id="profile-background">

<?php bp_get_userbar() ?>
<?php bp_get_optionsbar() ?>
<div id="main">
<div class="content-header">

</div>

<div id="content">
	<?php do_action( 'template_notices' ) // (error/success feedback) ?>
	
	<div class="left-menu">
		<?php bp_the_avatar() ?>
		
		<div class="button-block">
			<?php if ( function_exists('bp_add_friend_button') ) : ?>
				<?php bp_add_friend_button() ?>
			<?php endif; ?>
			
			<?php if ( function_exists('bp_send_message_button') ) : ?>
				<?php bp_send_message_button() ?>
			<?php endif; ?>
		</div>

		<?php bp_custom_profile_sidebar_boxes() ?>
	</div>

	<div class="main-column">
		<?php bp_get_profile_header() ?>
		
		<?php if ( function_exists('bp_wire_get_post_list') ) : ?>
			<?php bp_wire_get_post_list( bp_current_user_id(), bp_word_or_name( __( "Your Wire", 'buddypress' ), __( "%s's Wire", 'buddypress' ), true, false ), bp_word_or_name( __( "No one has posted to your wire yet.", 'buddypress' ), __( "No one has posted to %s's wire yet.", 'buddypress' ), true, false ), bp_profile_wire_can_post() ) ?>
		<?php endif; ?>
	</div>

</div>
</div> <!-- end #profile bg found in header -->
</div> <!-- end #profile wrapper found in header -->
<div id="profile-background-shade-bottom"></div>
</div><!-- end main found here-->
<?php get_footer() ?>