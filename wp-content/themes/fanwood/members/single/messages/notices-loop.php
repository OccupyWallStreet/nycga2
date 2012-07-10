<?php

/**
 * BuddyPress - Single Member Notices Loop
 *
 * This template displays all notifications sent to all members of the network.
 *
 * @package BuddyPress
 * @subpackage Theme
 */
 
?>

<?php do_action( 'bp_before_notices_loop' ) ?>

<?php if ( bp_has_message_threads() ) : ?>

	<div class="pagination bp-pagination" id="user-pag">

		<div class="pag-count" id="messages-dir-count">
			<?php bp_messages_pagination_count() ?>
		</div>

		<div class="pagination-links" id="messages-dir-pag">
			<?php bp_messages_pagination() ?>
		</div>

	</div><!-- .pagination -->

	<?php do_action( 'bp_after_notices_pagination' ) ?>
	<?php do_action( 'bp_before_notices' ) ?>
	
	<table id="message-threads" class="notices">
	
		<?php while ( bp_message_threads() ) : bp_message_thread(); ?>
		
		<tr id="notice-<?php bp_message_notice_id() ?>" class="<?php bp_message_css_class(); ?>">
			<td>
				<ul class="item-list dir-list notices">
				
					<li id="notice-<?php bp_message_notice_id() ?>" class="<?php bp_message_css_class(); ?>">

						<div class="item-header">
							<h2><?php bp_message_notice_subject() ?></h2>
							
							<span class="activity">
								<?php bp_message_is_active_notice() ?>
								<?php _e("Sent:", "buddypress"); ?> <?php bp_message_notice_post_date() ?>
							</span>
						</div><!-- .item-header -->
						
						<div class="item-content">
							<?php bp_message_notice_text() ?>
							<?php do_action( 'bp_notices_list_item' ) ?>
						</div><!-- .item-content -->

						<div class="item-action">
							<a class="button" href="<?php bp_message_activate_deactivate_link() ?>" class="confirm"><?php bp_message_activate_deactivate_text() ?></a>
							<a class="button" href="<?php bp_message_notice_delete_link() ?>" class="confirm" title="<?php _e( "Delete Message", "buddypress" ); ?>">x</a>
						</div>
					</li>
					
				</ul>
			</td>
		</tr>
			
		<?php endwhile; ?>
		
	</table><!-- #message-threads -->

	<?php do_action( 'bp_after_notices' ) ?>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'Sorry, no notices were found.', 'buddypress' ); ?></p>
	</div>

<?php endif;?>

<?php do_action( 'bp_after_notices_loop' ) ?>