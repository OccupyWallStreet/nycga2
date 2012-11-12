<?php
/**
 * This object is a parent of EM_Bookings_Form, which is extendable by creating a bookings form object and placing the 
 * php file in the wp-content/mu-plugins folder (see http://wpmututorials.com/plugins/basics/what-is-the-mu-plugins-folder/ for more info). 
 * Since mu-plugin files are executed before this, the EM_Bookings_Form will already be defined and won't be definied here.
 * @author marcus
 *
 */
class EM_Bookings_Form_Core extends EM_Object {
	/* Contains functions to handle save and delete requests on the front-end for bookings */
	static $em_form_messages_booking_cancel = array();
	static $em_form_messages_booking_add = array();
	
	function init(){
		add_action('template_redirect', array('EM_Bookings_Form','actions'),100);
		//catch the ajax call
		add_action( 'wp_ajax_add_booking', array('EM_Bookings_Form','ajax_add_booking') );
	}
	
	function ajax_add_booking(){
		/* Check the nonce */
		check_admin_referer( 'add_booking', '_wpnonce_add_booking' );
		if( !empty($_REQUEST['event_id']) && is_numeric($_REQUEST['event_id']) ){
			$EM_Event = new EM_Event($_REQUEST['event_id']);	
			$result = $EM_Event->get_bookings()->add_from_post();
			if($result){
				$return = array('result'=>true, 'message'=>$EM_Event->get_bookings()->feedback_message);
			}else{
				$return = array('result'=>false, 'message'=>implode('<br />', $EM_Event->get_bookings()->errors));
			}
			echo EM_Object::json_encode($return);
			exit();
		}else{
			$return = array('result'=>false, 'message'=>'');
			echo EM_Object::json_encode($return);
			exit();
		}
	}
	
	/**
	 * Check if there's any actions to take for bookings
	 * @return null
	 */
	function actions() {
		global $wpdb;
		global $EM_Event, $EM_Person;
		if( @get_class($EM_Event) == 'EM_Event' ){
			//ADD/EDIT Booking
			if (isset($_POST['action']) && $_POST['action'] == 'add_booking') {
				check_admin_referer( 'em_add_booking', '_wpnonce_em_add_booking' );
				$EM_Event->get_bookings()->add_from_post();
		  	}
		  	//CANCEL Booking
			if (isset($_POST['action']) && $_POST['action'] == 'cancel_booking') {
				self::cancel_booking();
		  	}
		}
	}   
	
	/**
	 * Handles booking cancellations on the front end. makes a few extra checks. 
	 */
	function cancel_booking(){
		global $EM_Event;
		if( is_user_logged_in() ){
			$canceled = 0;
			foreach($EM_Event->get_bookings()->bookings as $EM_Booking){
				if($EM_Booking->person->ID == $EM_Person->ID ){
					$EM_Booking->cancel();
					$canceled++;
				}
			}
			if($canceled > 0){
				self::$em_form_messages_booking_cancel['success'] = __('Booking cancelled', 'dbem');
			}
		}else{
			self::$em_form_messages_booking_cancel['error'] = __('You must log in to cancel your booking.', 'dbem');
		}
	}
	
	/**
	 * Returns the booking form for the front-end, displayed when using placeholder #_ADDBOOKINGFORM
	 * @return string
	 */
	function create() {                
		global $em_form_messages_booking_add, $EM_Event;
	 
		$booked_places_options = array();
		for ( $i = 1; $i <= 10; $i++ ) {
			$booking_spaces = (!empty($_POST['booking_spaces']) && $_POST['booking_spaces'] == $i) ? 'selected="selected"':'';
			array_push($booked_places_options, "<option value='$i' $booking_spaces>$i</option>");
		}
		ob_start();
		?>
		<div id="em-booking">
			<a name="em-booking"></a>
			<h3><?php _e('Book now!','dbem') ?></h3>
			
			<?php if( !empty($EM_Event->get_bookings()->feedback_message) && count($EM_Event->get_bookings()->errors) == 0 ) : ?>
				<div class='em-booking-message-success'><?php echo $EM_Event->get_bookings()->feedback_message; ?></div>
			<?php elseif( count($EM_Event->get_bookings()->errors) > 1 ) : ?>
				<div class='em-booking-message-error'><?php echo implode('<br />', $EM_Event->get_bookings()->errors); ?></div>
			<?php elseif( !empty($EM_Event->get_bookings()->feedback_message) ) : ?>
				<div class='em-booking-message'><?php echo $EM_Event->get_bookings()->feedback_message; ?></div>
			<?php endif; ?>
			
			<form id='em-booking-form' name='booking-form' method='post' action=''>
				<?php do_action('em_booking_form_before_tickets'); ?>
				<?php 
					$EM_Tickets = ( get_option('dbem_bookings_tickets_show_unavailable') ) ? $EM_Event->get_bookings()->get_tickets():$EM_Event->get_bookings()->get_tickets();
					if( (count($EM_Tickets->tickets) > 1 || !empty($EM_Tickets->get_first()->price)) && (get_option('dbem_bookings_tickets_show_loggedout') || is_user_logged_in()) ): ?>
					<table class="em-tickets" cellspacing="0" cellpadding="0">
						<tr>
							<td><?php _e('Ticket Type','dbem') ?></td>
							<td><?php _e('Price','dbem') ?></td>
							<td><?php _e('Spaces','dbem') ?></td>
						</tr>
						<?php foreach( $EM_Tickets->tickets as $EM_Ticket ): ?>
							<?php if( $EM_Ticket->is_available() || get_option('dbem_bookings_tickets_show_unavailable') ): ?>
							<tr>
								<td><?php echo $EM_Ticket->output_property('name'); ?></td>
								<td><?php echo $EM_Ticket->output_property('price'); ?></td>
								<td>
									<?php 
										$spaces_options = $EM_Ticket->get_spaces_options();
										if( $spaces_options ){
											echo $spaces_options;
										}else{
											echo "<strong>".__('N/A','dbem')."</strong>";
										}
									?>
								</td>
							</tr>
							<?php endif; ?>
						<?php endforeach; ?>
					</table>
				<?php endif; ?>
				<?php do_action('em_booking_form_after_tickets'); ?>
				<?php if( is_user_logged_in() && count($EM_Tickets->tickets) > 0 ) : ?>
					<?php $EM_Ticket = $EM_Tickets->get_first(); ?>
					<table class='em-booking-form-details'>
						<?php if( is_object($EM_Ticket) && (count($EM_Tickets->tickets) == 1 && empty($EM_Tickets->get_first()->price)) ): ?>
						<tr>
							<th scope='row'><?php _e('Spaces', 'dbem') ?>:</th>
							<td>
								<?php 
									$spaces_options = $EM_Ticket->get_spaces_options(false);
									if( $spaces_options ){
										echo $spaces_options;
									}else{
										echo "<strong>".__('N/A','dbem')."</strong>";
									}
								?>
							</td>
						</tr>
						<?php endif; ?>
						<?php do_action('em_booking_form_before_user_details'); ?>
						<tr><th scope='row'><?php _e('Comment', 'dbem') ?>:</th><td><textarea name='booking_comment'><?php echo !empty($_POST['booking_comment']) ? $_POST['booking_comment']:'' ?></textarea></td></tr>
						<?php do_action('em_booking_form_footer'); ?>
				</table>
				<div class="em-booking-buttons">
					<?php echo apply_filters('em_booking_form_buttons', '<input type="submit" class="em-booking-submit" value="'.__('Send your booking', 'dbem').'" />'); ?>
				 	<input type='hidden' name='action' value='add_booking'/>
				 	<input type='hidden' name='callback' value='em'/>
				 	<input type='hidden' name='event_id' value='<?php echo $EM_Event->id; ?>'/>
				 	<?php echo wp_nonce_field('add_booking','_wpnonce_add_booking'); ?>
				</div>
				<?php elseif( count($EM_Tickets->tickets) == 0 ): ?>
				<div><?php _e('No more tickets available at this time.','dbem'); ?></div>
				<?php else: ?>
				<div><?php echo sprintf(__('You must <a href="%s">register</a> or <a href="%s">log in</a> in order to create and manage your bookings.','dbem'), site_url('wp-login.php?action=register', 'login_post'), site_url('wp-login.php', 'login_post')); ?></div>
				<?php endif; ?>  
			</form>
		</div>
		<?php echo self::get_js(); ?>
		<?php
		return apply_filters('em_bookings_form_create', ob_get_clean());	
	}
	
	function get_js(){
		ob_start();
		?>		
		<script type="text/javascript">
			jQuery(document).ready( function($){
				$('#em-booking-form').ajaxForm({
					url: EM.ajaxurl,
					dataType: 'jsonp',
					beforeSubmit: function(formData, jqForm, options) {
						$('.em-booking-message').remove();
						$('#em-booking-form').append('<div id="em-loading"></div>');
					},
					success : function(response, statusText, xhr, $form) {
						$('#em-loading').remove();
						if(response.result){
							$('<div class="em-booking-message-success em-booking-message">'+response.message+'</div>').insertBefore('#em-booking-form');
						}else{
							$('<div class="em-booking-message-error em-booking-message">'+response.message+'</div>').insertBefore('#em-booking-form');
							
						}
					}
				});								
			});
		</script>
		<?php 
		return apply_filters('em_bookings_form_get_js', ob_get_clean());
	}
	
	/**
	 * Booking removal in front end, called by placeholder #_REMOVEBOOKINGFORM
	 * @return string
	 */
	function cancel() {   
		global $em_form_messages_booking_cancel, $EM_Event;	
		$destination = "?".$_SERVER['QUERY_STRING'];
		ob_start();
		?>
		<div id="em-booking-delete">
			<a name="em-booking-delete"></a>
			<h3><?php _e('Cancel your booking', 'dbem') ?></h3>
			
			<?php if( is_user_logged_in() ): ?>
				<?php if( !empty(self::$em_form_messages_booking_cancel['success']) ) : ?>
				<div class='em-booking-message-success'><?php echo self::$em_form_messages_booking_cancel['success'] ?></div>
				<?php elseif( !empty(self::$em_form_messages_booking_cancel['error']) ) : ?>
				<div class='em-booking-message-error'><?php echo self::$em_form_messages_booking_cancel['error'] ?></div>
				<?php elseif( !empty(self::$em_form_messages_booking_cancel['message']) ) : ?>
				<div class='em-booking-message'><?php echo self::$em_form_messages_booking_add['message'] ?></div>
				<?php endif; ?>
				
				<form name='booking-delete-form' method='post' action='<?php echo $destination ?>#em-booking-delete'>
					<input type='hidden' name='em_action' value='delete_booking'/>
					<input type='hidden' name='event_id' value='<?php echo $EM_Event->id; ?>'/>
					<input type='submit' value='<?php _e('Cancel your booking', 'dbem') ?>'/>
				</form>
			<?php else: ?>
				<p>Please <a href="<?php echo site_url('wp-login.php', 'login_post'); ?>">log in</a> to manage your bookings.</p>
			<?php endif; ?>
		</div>
		<?php
		return ob_get_clean();	
	}
	
	/* AJAX update posting */
	function bp_dtheme_post_update() {
		global $bp;
	
		/* Check the nonce */
		check_admin_referer( 'post_update', '_wpnonce_post_update' );
	
		if ( !is_user_logged_in() ) {
			echo '-1';
			return false;
		}
		add_action( 'wp_ajax_post_update', 'bp_dtheme_post_update' );	
	}
}
if( !class_exists('EM_Bookings_Form') ){
	class EM_Bookings_Form extends EM_Bookings_Form_Core {
		
	}
}
EM_Bookings_Form::init();
?>