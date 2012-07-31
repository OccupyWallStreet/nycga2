<?php get_header() ?>
<div id="profile-wrapper">
<div id="profile-background">

<?php bp_get_userbar() ?>
<?php bp_get_optionsbar() ?>
<div id="main">
<div class="content-header">

</div>

<div id="content">
	<div class="pagination-links">
		<?php bp_messages_pagination() ?>
	</div>
	
	<h2><?php _e("Sent Notices", "buddypress"); ?></h2>
	
	<?php do_action( 'template_notices' ) ?>

	<?php if ( bp_has_message_threads() ) : ?>
		
		<table id="message-threads" class="notices">
		<?php while ( bp_message_threads() ) : bp_message_thread(); ?>
			<tr>
				<td width="1%">
				</td>
				<td width="40%">
					<p><strong><?php bp_message_notice_subject() ?></strong></p>
					<p><?php bp_message_notice_text() ?></p>
				</td>
				<td width="27%">
					<p><?php bp_message_is_active_notice() ?></p>
					<p class="date"><?php _e("Sent:", "buddypress"); ?> <?php bp_message_notice_post_date() ?></p>
				</td>
				<td width="4%">
					<a href="<?php bp_message_activate_deactivate_link() ?>"><?php bp_message_activate_deactivate_text() ?></a> 
					<a href="<?php bp_message_notice_delete_link() ?>" title="<?php _e("Delete Message", "buddypress"); ?>"><?php _e("Delete", "buddypress"); ?></a> 
				</td>
			</tr>
		<?php endwhile; ?>
		</table>
		
	<?php else: ?>
		
		<div id="message" class="info">
			<p><?php _e("You have not sent any notices.", "buddypress"); ?></p>
		</div>	

	<?php endif;?>

</div>
</div> <!-- end #profile bg found in header -->
</div> <!-- end #profile wrapper found in header -->
<div id="profile-background-shade-bottom"></div>
</div><!-- end main found here-->
<?php get_footer() ?>