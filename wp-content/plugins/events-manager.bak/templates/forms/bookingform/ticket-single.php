<?php 
/* @var $EM_Ticket EM_Ticket */
/* @var $EM_Event EM_Event */
global $allowedposttags;
?>
<?php if(!empty($EM_Ticket->description)) :?><p class="ticket-desc"><?php echo wp_kses($EM_Ticket->description,$allowedposttags); ?></p><?php endif; ?>
<?php if( !$EM_Event->is_free() ): ?>
	<p>
		<label><?php _e('Price','dbem') ?></label><strong><?php echo $EM_Ticket->get_price(true); ?></strong>
	</p>
<?php endif; ?>
<?php if( $EM_Ticket->get_available_spaces() > 1 && ($EM_Ticket->max > 1 || empty($EM_Ticket->max)) ): ?>				
	<p>
		<label for='em_tickets'><?php _e('Spaces', 'dbem') ?></label>
		<?php 
			$spaces_options = $EM_Ticket->get_spaces_options(false);
			if( $spaces_options ){
				echo $spaces_options;
			}else{
				echo "<strong>".__('N/A','dbem')."</strong>";
			}
		?>
	</p>
<?php else: ?>
	<input type="hidden" name="em_tickets[<?php echo $EM_Ticket->id ?>][spaces]" value="1" class="em-ticket-select" />
<?php endif; ?>