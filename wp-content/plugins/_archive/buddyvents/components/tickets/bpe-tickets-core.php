<?php
/**
 * @package		WordPress
 * @subpackage	BuddyPress
 * @author		Boris Glumpler
 * @copyright	2011, ShabuShabu Webdesign
 * @link		http://shabushabu.eu
 * @license		http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */

// No direct access is allowed
if( ! defined( 'ABSPATH' ) ) exit;

/**
 * Include all needed files
 * 
 * @package	Tickets
 * @since 	2.1
 */
function bpe_ticket_load_files()
{
	$files = array(
		'models/bpe-tickets',
		'models/bpe-sales',
		'models/bpe-invoices',
		'templatetags/bpe-tickets',
		'templatetags/bpe-sales',
		'templatetags/bpe-invoices',
		'bpe-tickets-ajax',
		'bpe-tickets-filters',
		'bpe-tickets-db',
		'bpe-tickets-extension',
		'bpe-tickets-sales',
		'bpe-tickets-js',
		'bpe-tickets-paypal'
	);
	
	foreach( $files as $file )
		require( EVENT_ABSPATH .'components/tickets/'. $file .'.php' );
}
add_action( 'init', 'bpe_ticket_load_files', 1 );

/**
 * Override our normal attendance button if there are tickets
 * 
 * @package	Tickets
 * @since 	2.0
 * 
 * @param	string	$button
 * @param	object	$event
 * 
 * @uses	bpe_is_member()
 * @uses	bpe_get_event_link()
 * @uses	bp_loggedin_user_id()
 * @uses	bpe_get_option()
 * @uses	apply_filters()
 */
function bpe_ticket_attendance_button( $button, $event )
{
	global $nav_counter;
		
	if( $event->has_tickets )
	{
		$button = '';
		
		if( bpe_is_member( $event ) ) :
			$nav_counter++;
			$button .= '<a class="button confirm" href="'. bpe_get_event_link( $event ) .'not-attending/'. bp_loggedin_user_id() .'/">'. __( 'Remove from event', 'events' ) .'</a>';
		else :
			$nav_counter++;
			$button .= '<a class="button ticketbox" href="'. bpe_get_event_link( $event ) . bpe_get_option( 'tickets_slug' ) .'/">'. __( 'Tickets', 'events' ) .'</a>';
		endif;
	}
	
	return apply_filters( 'bpe_ticket_attendance_button', $button, $event );
}

/**
 * Attach the above filter at the right moment
 * 
 * Attached to the <code>wp</code> action hook
 * 
 * @package	Tickets
 * @since 	2.0
 * 
 * @uses	add_filter()
 */
function bpe_ticket_change_button()
{
	add_filter( 'bpe_get_attendance_button', 'bpe_ticket_attendance_button', 10, 2 );
}
add_action( 'wp', 'bpe_ticket_change_button', 0 );

/**
 * Changes the content of the invite message
 * 
 * Attached to the <code>bpe_new_invite_message</code> filter hook
 * 
 * @package	Tickets
 * @since 	2.0
 */
function bpe_tickets_change_invite_email( $message, $event, $uid, $eventlink )
{
	if( ! $event->has_tickets )
		return $message;
	
	$message = sprintf( __( "Hello %s,\n\nyou have been invited to attend %s:\n%s\n\nPlease follow the above link to purchase a ticket if you would like to attend.", 'events' ),
						bp_core_get_user_displayname( $uid ),
						bpe_get_event_name( $event ),
						$eventlink
					   );

	return $message;
}
add_filter( 'bpe_new_invite_message', 'bpe_tickets_change_invite_email', 10, 4 );

/**
 * Add anonymous event members to attendee count
 * 
 * Attached to the <code>bpe_filter_event_attendee_count</code> filter hook
 * 
 * @package	Tickets
 * @since 	2.0
 * 
 * @param	int		$count
 * @param	int		$event_id
 * @return	int		$count
 * 
 * @uses	bp_get_option()
 */
function bpe_ticket_add_anonymous_members( $count, $event_id )
{
	$attendees = bp_get_option( 'bpe_non_existing_attendees' );
	
	foreach( (array)$attendees as $email => $event_ids )
	{
		if( in_array( $event_id, (array)$event_ids ) )
			$count++;
	}
	
	return $count;
}
add_filter( 'bpe_filter_event_attendee_count', 'bpe_ticket_add_anonymous_members', 10, 2 );

/**
 * Display the content of the ticket box
 * 
 * Attached to the <code>wp</code> action hook
 * 
 * @package	Tickets
 * @since 	2.0
 * 
 * @global 	object	$wp_query 	The current WordPress query variables
 * @todo	Extract inline JS
 * 
 * @uses	Ticket template tags
 */
function bpe_ticket_box_content()
{
	global $wp_query;
	
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_action_variable( bpe_get_option( 'tickets_slug' ), 1 ) && ! bp_is_action_variable( bpe_get_option( 'step_slug' ), 0 ) )
	{
		$wp_query->is_404 = false;
		status_header( 200 );
		
		?>
        <div>
            <form id="bpe-purchase-tickets" action="<?php echo bpe_get_event_link( bpe_get_displayed_event() ) .'checkout/paypal/' ?>" method="post" class="standard-form">
            
            	<?php wp_nonce_field( 'bpe_purchase_ticket_'. bpe_get_displayed_event( 'id' ) ) ?>
                
            	<?php if( bpe_has_tickets( array( 'available' => true ) ) ) : ?>

            		<h3><?php printf( __( 'Choose your tickets for <i>%s</i>', 'events' ), bpe_get_displayed_event( 'name' ) ) ?></h3>
                    
                    <hr />
                
                    <ul>
                    <?php while( bpe_tickets() ) : bpe_the_ticket(); ?>
                    
                        <li class="single-ticket">
                            <input type="radio" name="ticket_id" class="ticket_id" value="<?php bpe_ticket_id() ?>" /> <?php bpe_ticket_name() ?>
                            <?php if( bpe_get_ticket_currency() && bpe_get_ticket_price() ) : ?>
                                (<?php printf( __( '%s %s / ticket', 'events' ), bpe_get_ticket_currency(), bpe_get_ticket_price() ) ?>)
                            <?php else : ?>
                                (<?php _e( 'free', 'events' ) ?>)
                            <?php endif; ?>
                            <a href="#" class="ticket-info">?</a>
                            <div class="ticket-desc"><?php bpe_ticket_description() ?></div>
                        </li>
                    
                    <?php endwhile; ?>
                    </ul>
                    
                    <hr />

                    <?php while( bpe_tickets() ) : bpe_the_ticket(); ?>
                    
                        <div class="tc-content" id="tc-<?php bpe_ticket_id() ?>">
                            <?php bpe_ticket_quantity_dropdown() ?>&nbsp;<?php _e( 'ticket(s)', 'events' ) ?>
                            <input type="hidden" name="sale[<?php bpe_ticket_id() ?>][price]" id="price-<?php bpe_ticket_id() ?>" value="<?php bpe_ticket_price() ?>" />
                            <input type="hidden" name="sale[<?php bpe_ticket_id() ?>][currency]" id="currency-<?php bpe_ticket_id() ?>" value="<?php bpe_ticket_currency() ?>" />
		                    <hr />
                        </div>
                    
                    <?php endwhile; ?>
                    
                    <strong><?php _e( 'Total', 'events' ) ?></strong>: <span class="ticket_currency"></span> <span class="ticket_price"></span>                    
                    
                    <div id="ticket-xinfo">
                    	<hr />
                        <h4><?php _e( 'Additional Attendee Information', 'events' ) ?></h4>
                    	<div id="ticket-xinfo-inner"></div>
                        <small><?php printf( __( 'Please enter any additonal attendee information (apart from you) above. If possible, please use the email addresses that were used to register on %s', 'events' ), get_bloginfo( 'name' ) ) ?></small>
                    </div>

                    <input type="hidden" name="event_id" value="<?php bpe_ticket_event_id() ?>" />
                  
                    <div class="submit" id="purchase-submit">
                    	<input type="submit" id="checkout" name="checkout" value="<?php _e( 'Checkout', 'events' ) ?>" />
                    </div>
                    
                    <script type="text/javascript">
					function bpe_set_price(value) {
						var quantity = jQuery('select#q-'+ value).val();
						var currency = jQuery('input#currency-'+ value).val();
						var price = jQuery('input#price-'+ value).val();
						
						var total = quantity * parseFloat(price);
						total = String(total.toFixed(2));
						
						jQuery('span.ticket_currency').text(currency);
						jQuery('span.ticket_price').text(total);
						
						return quantity;							
					}
					function bpe_create_name_email_field(value) {
						value--;
						if(value < 1) {
							jQuery('#ticket-xinfo-inner').empty();
							jQuery('#ticket-xinfo').hide();
							return false;
						}
						
						var fields = '';
						for(i=1;i<=value;i++) {
							fields += '<div class="xattendee-wrap">';
							fields += '<label for="xattendee-name'+ i +'"><?php _e( '* Name', 'events' ) ?></label>';
							fields += '<input class="name-field" type="text" id="xattendee-name'+ i +'" name="names[]" value="" />';
							fields += '<label for="xattendee-email'+ i +'"><?php _e( '* Email', 'events' ) ?></label>';
							fields += '<input class="email-field" type="text" id="xattendee-email'+ i +'" name="emails[]" value="" />';
							fields += '</div><hr />';
						}
						i = null;
						
						jQuery('#ticket-xinfo').show();
						jQuery('#ticket-xinfo-inner').empty().html(fields);
					}
					jQuery(document).ready( function() {
						jQuery('.ticket-desc,.tc-content,#purchase-submit,#ticket-xinfo').hide();
						jQuery('.ticket-info').click( function () {
							jQuery(this).siblings('.ticket-desc').toggle();
							jQuery.colorbox.resize({height:'90%'});
							return false;
						})
						jQuery('input[name="ticket_id"]').change(function() {
							var value = jQuery(this).val();
							jQuery(".tc-content").hide();
							jQuery("#purchase-submit,#tc-"+ value).show();
							var q = bpe_set_price(value);
							bpe_create_name_email_field(q);
							jQuery.colorbox.resize({height:'90%'});
						});
						jQuery('select.select_quantity').change(function() {
							var value = jQuery(this).attr('id');
							value = value.split('-');
							value = value[1];
							var q = bpe_set_price(value);
							bpe_create_name_email_field(q);
							jQuery.colorbox.resize({height:'90%'});
						});
						jQuery('#checkout').click( function() {
							jQuery('.ticket-error').remove();
        					var hasError = false;
							var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;							
							jQuery(".name-field").each(function(i) {
								var name = jQuery(this).val();
								if(name == '') {
									jQuery(this).before('<span class="ticket-error"><?php _e( 'Please enter a name.', 'events' ) ?></span>');
									hasError = true;
								}
							});
							jQuery(".email-field").each(function(i) {
								var email = jQuery(this).val();
								if(email == '') {
									jQuery(this).before('<span class="ticket-error"><?php _e( 'Please enter an email address.', 'events' ) ?></span>');
									hasError = true;
								} else if(!emailReg.test(email)) {
									jQuery(this).before('<span class="ticket-error"><?php _e( 'Please enter a valid email address.', 'events' ) ?></span>');
									hasError = true;
								}
							}); 					
							if(hasError == true) { return false; }
						});
					});
					</script>
                
                <?php else :
				
					$next_sale = bpe_get_next_ticket_sale_date( bpe_get_displayed_event( 'id' ) );
					
					if( $next_sale )
						$date = sprintf( __( 'The next sale starts on %s', 'events' ), mysql2date( bpe_get_option( 'date_format' ), $next_sale, true ) );
					?>
                
                	<p><?php printf( __( 'There are no tickets available right now. %s', 'events' ), $date ) ?></p>
                    
                <?php endif; ?>

            </form>
    		</div>
		<?php
		exit;
	}
}
add_action( 'wp', 'bpe_ticket_box_content', 1 );

/**
 * Display tickets for editing
 * 
 * @package	Tickets
 * @since 	2.0
 * 
 * @global 	object	$ticket_template	Ticket data
 * @param	mixed	$t 					Either object or boolean
 * 
 * @uses	bpe_get_displayed_event()
 * @uses	apply_filters()
 * @uses	bpe_get_ticket_id()
 */
function bpe_ticket_quantity_dropdown( $t = false )
{
	global $ticket_template;
	
	$ticket = ( ! $t ) ? $ticket_template->ticket : $t;
	
	// get the minimum tickets available
	$min = ( empty( $ticket->min_tickets ) ) ? 1 : $ticket->min_tickets;
	
	// get the maximum tickets available
	$leftover = bpe_get_displayed_event( 'limit_members' ) - bpe_get_displayed_event( 'attendees' );
	$spots = ( $leftover > 0 && bpe_get_displayed_event( 'limit_members' ) > 0 ) ? $leftover : 50;
	$max = ( $ticket->max_tickets > $spots ) ? $spots : $ticket->max_tickets;
	
	// for free tickets everybody has to get his/her own ticket
	if( $ticket->price == 0.00 )
	{
		$min = 1;
		$max = 1;
	}

	$select = '<select class="select_quantity" id="q-'. bpe_get_ticket_id() .'" name="sale['. bpe_get_ticket_id() .'][quantity]">';
	for( $i = $min; $i <= $max; $i++ )
	{
		$select .= '<option value="'. $i .'">'. $i .'</option>';
	}
	$select .= '</select>';
	
	echo apply_filters( 'bpe_ticket_quantity_dropdown', $select, $ticket, bpe_get_displayed_event() );
}

/**
 * Display tickets for editing
 * 
 * @package	Tickets
 * @since 	2.0
 * 
 * @param 	int		$eid	The current event id
 * 
 * @uses	various ticket template tags
 */
function bpe_edit_tickets( $eid = false )
{
	$counter = 1;
	
	if( ! $eid )
		$eid = bpe_get_displayed_event( 'id' );
		
	$ticket_ids = array();
	
	if( bpe_has_tickets( array( 'event_id' => $eid ) ) ) :
		while ( bpe_tickets() ) : bpe_the_ticket();

		$ticket = array(
			'id' 		  => bpe_get_ticket_id(),
			'name' 		  => bpe_get_ticket_name_raw(),
			'description' => bpe_get_ticket_description_raw(),
			'price' 	  => bpe_get_ticket_price(),
			'currency' 	  => bpe_get_ticket_currency(),
			'quantity' 	  => bpe_get_ticket_quantity(),
			'start_sales' => bpe_get_ticket_start_sales(),
			'end_sales'   => bpe_get_ticket_end_sales(),
			'min_tickets' => bpe_get_ticket_min_tickets(),
			'max_tickets' => bpe_get_ticket_max_tickets()
		);
		
		$ticket_ids[] = bpe_get_ticket_id();
		
		echo bpe_event_ticket_form( $counter, $ticket );
		
		$counter++;
		endwhile;
	endif;
	
	echo '<input type="hidden" name="ticket_ids" value="'. implode( ',', $ticket_ids ) .'" />';
}

/**
 * Display the correct counter
 * 
 * @package	Tickets
 * @since 	2.0
 * 
 * @return 	int
 */
function bpe_ticket_counter()
{
	$cookie = ( isset( $_COOKIE['buddyvents_tickets'] ) ) ? stripslashes( $_COOKIE['buddyvents_tickets'] ) : false;

	if( $cookie )
		return (int)$cookie + 1;

	return 1;	
}

/**
 * Schedule form html
 * 
 * Output can be modified with the <code>bpe_event_ticket_form</code>
 * filter hook. Additional parameters are $key and $ticket
 * 
 * @package	Tickets
 * @since 	2.0
 * 
 * @param	int		$key
 * @param	object	$ticket
 * @param	string	$currency
 * 
 * @uses	apply_filters()
 */
function bpe_event_ticket_form( $key, $ticket = false, $currency = false )
{
	if( ! $currency )
		$currency = $ticket['currency'];
	
	$form = '<fieldset class="event-block" id="event-ticket-'. $key .'">';
		
		if( $ticket['id'] > 0 )
        	$form .= '<input type="hidden" name="ticket['. $key .'][id]" value="'. $ticket['id'] .'" />';
		
        $form .= '<a class="button del-ticket" href="#">X</a>';

		$form .= '<label for="name_ticket-'. $key .'">'. __( '* Name', 'events' ) .'</label>';
		$form .= '<input type="text" id="name_ticket-'. $key .'" name="ticket['. $key .'][name]" value="'. $ticket['name'] .'">';

        $form .= '<label for="ticket_description-'. $key .'">'. __( '* Description', 'events' ) .'</label>';
        $form .= '<textarea id="ticket_description-'. $key .'" name="ticket['. $key .'][description]">'. $ticket['description'] .'</textarea>';

		$form .= '<div class="date-schedule">';
			$form .= '<label for="quantity_ticket-'. $key .'">'. __( 'Quantity', 'events' ) .'</label>';
			$form .= '<input type="text" id="quantity_ticket-'. $key .'" name="ticket['. $key .'][quantity]" value="'. $ticket['quantity'] .'" />';
		$form .= '</div>';
	
		$form .= '<div class="date-schedule">';
			$form .= '<label for="price_ticket-'. $key .'">'. __( 'Price', 'events' ) .'</label>';
			$form .= '<input type="text" id="price_ticket-'. $key .'" name="ticket['. $key .'][price]" value="'. $ticket['price'] .'" /> '. $currency;
		$form .= '</div>';

		$form .= '<div class="clear"></div>';

		$form .= '<div class="date-schedule">';
            $form .= '<label for="start_sales_ticket-'. $key .'">'. __( 'Start Sales', 'events' ) .'</label>';
            $form .= '<input type="text" id="start_sales_ticket-'. $key .'" class="ticket-date" name="ticket['. $key .'][start_sales]" value="'. $ticket['start_sales'] .'" />';
		$form .= '</div>';

		$form .= '<div class="date-schedule">';
            $form .= '<label for="end_sales_ticket-'. $key .'">'. __( 'End Sales', 'events' ) .'</label>';
            $form .= '<input type="text" id="end_sales_ticket-'. $key .'" class="ticket-date" name="ticket['. $key .'][end_sales]" value="'. $ticket['end_sales'] .'" />';
		$form .= '</div>';

		$form .= '<div class="clear"></div>';

		$form .= '<div class="date-schedule">';
			$form .= '<label for="min_tickets_ticket-'. $key .'">'. __( 'Min Tickets', 'events' ) .'</label>';
			$form .= '<input type="text" id="min_tickets_ticket-'. $key .'" name="ticket['. $key .'][min_tickets]" value="'. $ticket['min_tickets'] .'" />';
		$form .= '</div>';
	
		$form .= '<div class="date-schedule">';
			$form .= '<label for="max_tickets_ticket-'. $key .'">'. __( 'Max Tickets', 'events' ) .'</label>';
			$form .= '<input type="text" id="max_tickets_ticket-'. $key .'" name="ticket['. $key .'][max_tickets]" value="'. $ticket['max_tickets'] .'" />';
		$form .= '</div>';
      
	$form .= '</fieldset>';
    
    return apply_filters( 'bpe_event_ticket_form', $form, $key, $ticket );
}

/**
 * Process ticket edit
 * 
 * @package	Tickets
 * @since 	2.0
 * 
 * @param	array 	$input
 * @param	object	$displayed_event
 * @param	bool	$api
 * @param	bool	$edit
 * 
 * @uses	bpe_add_message()
 * @uses	bp_core_redirect()
 * @uses	bp_get_root_domain()
 * @uses	bp_get_user_meta()
 * @uses	bpe_get_event_user_id()
 * @uses	bpe_add_ticket()
 * @uses	bpe_delete_tickets_by_ids()
 */
function bpe_process_ticket( $input, $displayed_event, $api = false, $edit = false )
{
	$ticket_fail = false;
	
	$tickets = ( isset( $input['ticket'] ) ) ? $input['ticket'] : array();
	
	if( count( $tickets ) > 0 )
	{
		foreach( $tickets as $key => $ticket )
		{
			$start 	= strtotime( $ticket['start_sales'] );
			$end	= strtotime( $ticket['end_sales'] );
			
			if( empty( $ticket['name'] ) || empty( $ticket['description'] ) )
			{
				$ticket_fail = true;
				$message = __( 'Please fill in all fields marked by *.', 'events' );
				$api_message = 'Required data is missing';
				break;
			}
			
			if( $start > $end )
			{
				$ticket_fail = true;
				$message = __( 'Start date has to be before end date.', 'events' );
				$api_message = 'Start date before end date';
				break;
			}
		}
	}

	// check for all required fields
	if( $ticket_fail == true )
	{
		if( ! $api )
		{
			bpe_add_message( $message, 'error' );
			bp_core_redirect( bp_get_root_domain() . $input['_wp_http_referer'] );
		}
		else
			return bpe_api_message( $api_message, 'failed' );
	}

	// process any schedule entries
	$existing_tickets = explode( ',', $input['ticket_ids'] );
	
	$user_id = bpe_get_event_user_id( $displayed_event );
	
	if( ! $user_id )
		$user_id = bp_loggedin_user_id();
	
	$currency = bp_get_user_meta( $user_id, 'bpe_paypal_currency', true );
	
	$tids = array();
	foreach( (array)$tickets as $key => $tick )
	{
		$ticket_id = ( isset( $tick['id'] ) ) ? $tick['id'] : null;

		if( ! is_null( $ticket_id ) )
			$tids[] = $ticket_id;
		
		$start_sales 	= ( empty( $tick['start_sales'] ) ) ? date( 'Y-m-d' ) : $tick['start_sales'];
		$end_sales 		= ( empty( $tick['end_sales'] ) ) ? bpe_get_event_start_date_raw( $displayed_event ) : $tick['end_sales'];
		
		bpe_add_ticket( $ticket_id, bpe_get_event_id( $displayed_event ), $tick['name'], $tick['description'], $tick['price'], $currency, $tick['quantity'], $start_sales, $end_sales, $tick['min_tickets'], $tick['max_tickets'] );
	}

	$delete_tickets = array_diff( (array)$existing_tickets, (array)$tids );

	if( count( $delete_tickets ) > 0 )
		bpe_delete_tickets_by_ids( $delete_tickets );
	
	if( $edit )
		do_action( 'bpe_updated_event_tickets', $displayed_event );
		
	if( ! $edit && ! $api )
		@setcookie( 'buddyvents_tickets', $input['ticket-counter'], time() + 86400, COOKIEPATH );

	if( ! $api ) :
		if( $edit ) :
			bpe_add_message( __( 'Tickets have been updated.', 'events' ) );
		endif;
	else :
		return bpe_api_message( 'Tickets have been updated', 'success' );
	endif;
}

/**
 * Add the paypal email address to events settings
 * 
 * Attached to the <code>bpe_event_settings_action_end</code>
 * 
 * @package	Tickets
 * @since 	2.0
 *
 * @param	int		$user_id
 * 
 * @uses	bp_get_user_meta()
 * @uses	esc_attr()
 * @uses	bpe_get_option()
 * @uses	bpe_country_select()
 */
function bpe_tickets_add_settings( $user_id )
{
	$email = bp_get_user_meta( $user_id, 'bpe_paypal_email', true );
	$currency = bp_get_user_meta( $user_id, 'bpe_paypal_currency', true );
	$address = bp_get_user_meta( $user_id, 'bpe_billing_address', true );
	?>

    <hr />

    <p>
        <label for="paypal-email"><?php _e( 'PayPal Email Address', 'events' ) ?></label>
        <input type="text" name="paypal-email" id="paypal-email" value="<?php echo esc_attr( $email ) ?>" /><br />
        <small><?php _e( 'Enter your primary PayPal email address.', 'events' ) ?></small>
    </p>

    <hr />

    <p>
        <label for="paypal-currency"><?php _e( '* Currency', 'events' ) ?></label>
		<select id="paypal-currency" name="paypal-currency">
			<option value=""></option>
			<?php foreach( (array) bpe_get_option( 'allowed_currencies' ) as $code ) : ?>
				<option<?php if( $currency == $code ) echo ' selected="selected"'; ?> value="<?php echo esc_attr( $code ) ?>"><?php echo bpe_get_translatable_currency( $code ) ?></option>
			<?php endforeach; ?>
			</select> <small><?php _e( 'Mandatory if a PayPal address is filled in.', 'events' ) ?></small>
    </p>
    
    <hr />
    
    <h4><?php _e( 'Billing Address', 'events' ) ?></h4>
    <small><?php _e( 'All fields marked with * are mandatory if a PayPal address is filled in.', 'events' ) ?></small>
    <p>
        <label for="company"><?php _e( 'Company Name', 'events' ) ?></label>
        <input type="text" name="address[company]" id="company" value="<?php echo ( isset( $address['company'] ) ) ? esc_attr( $address['company'] ) : ''; ?>" />
    </p>
    <p>
        <label for="street"><?php _e( '* Street', 'events' ) ?></label>
        <input type="text" name="address[street]" id="street" value="<?php echo ( isset( $address['street'] ) ) ? esc_attr( $address['street'] ) : ''; ?>" />
    </p>
    <p>
        <label for="postcode"><?php _e( '* Postcode', 'events' ) ?></label>
        <input type="text" name="address[postcode]" id="postcode" value="<?php echo ( isset( $address['postcode'] ) ) ? esc_attr( $address['postcode'] ) : ''; ?>" />
    </p>
    <p>
        <label for="city"><?php _e( '* City', 'events' ) ?></label>
        <input type="text" name="address[city]" id="city" value="<?php echo ( isset( $address['city'] ) ) ? esc_attr( $address['city'] ) : ''; ?>" />
    </p>
    <p>
        <label for="country"><?php _e( '* Country', 'events' ) ?></label>
        <select id="country" name="address[country]">
        	<?php bpe_country_select( ( ( isset( $address['country'] ) ) ? $address['country']: '' ) ) ?>
        </select>
    </p>

    <hr />
    <?php	
}
add_action( 'bpe_event_settings_action_end', 'bpe_tickets_add_settings' );


/**
 * Save any extra events settings
 * 
 * Attached to the <code>bpe_event_settings_save_extra</code>
 * 
 * @package	Tickets
 * @since 	2.0
 * 
 * @param	int		$user_id
 * 
 * @uses	bp_core_add_message()
 * @uses	is_email()
 * @uses	bp_core_redirect()
 * @uses	bp_get_root_domain()
 * @uses	bp_update_user_meta()
 * @uses	add_action()
 */
function bpe_tickets_save_settings( $user_id )
{
	if( ! empty( $_POST['paypal-email'] ) )
	{
		if( ! is_email( $_POST['paypal-email'] ) )
		{
			bp_core_add_message( __( 'Please enter a valid email address.', 'events' ), 'error' );
			bp_core_redirect( bp_get_root_domain() . $_POST['_wp_http_referer'] );
		}
		
		if( empty( $_POST['paypal-currency'] ) )
		{
			bp_core_add_message( __( 'Please choose a currency.', 'events' ), 'error' );
			bp_core_redirect( bp_get_root_domain() . $_POST['_wp_http_referer'] );
		}

		if( empty( $_POST['address']['street'] ) || empty( $_POST['address']['city'] ) || empty( $_POST['address']['postcode'] ) || empty( $_POST['address']['country'] ) )
		{
			bp_core_add_message( __( 'Please fill in all address fields marked with *', 'events' ), 'error' );
			bp_core_redirect( bp_get_root_domain() . $_POST['_wp_http_referer'] );
		}
	}
	
	bp_update_user_meta( $user_id, 'bpe_paypal_email', 	  $_POST['paypal-email'] 	);
	bp_update_user_meta( $user_id, 'bpe_paypal_currency', $_POST['paypal-currency'] );
	bp_update_user_meta( $user_id, 'bpe_billing_address', $_POST['address'] 		);
}
add_action( 'bpe_event_settings_save_extra', 'bpe_tickets_save_settings' );

/**
 * Send the tickets via email
 * 
 * Takes non-existing members into account (sends invitations)
 * and deletes the generated PDF afterwards
 * 
 * @package	Tickets
 * @since 	2.0
 * 
 * @param 	object		$event
 * @param 	object		$ticket
 * @param	object		$sale
 * @param	object		$buyer
 * 
 * @uses	wp_mail()
 * @uses	bpe_tickets_create_pdf()
 */
function bpe_sales_send_tickets( $event = false, $ticket = false, $sale = false, $buyer = false )
{
	global $bp;
	
	if( ! $event || ! $ticket || ! $sale || ! $buyer )
		return false;
	
	$attendees = maybe_unserialize( stripslashes( $sale->attendees ) );
	
	// add any existing members to the event
	$non_existing = array();
	foreach( (array)$attendees as $email => $val )
	{
		$uid = email_exists( $email );
		
		if( $uid )
		{
			$existing_id = bpe_is_user_member_already( bpe_get_event_id( $event ), $uid );
			
			if( ! $existing_id )
				$existing_id = bpe_was_user_member( bpe_get_event_id( $event ), $uid );
			
			bpe_add_member( $existing_id, bpe_get_event_id( $event ), $uid, 1, bp_core_current_time(), 'attendee' );

			do_action( 'bpe_attend_event', $event, $uid ); 
		}
		else
		{
			$non_existing[$email][] = bpe_get_event_id( $event );
			$emails[] = $email;
		}
	}
	
	// save all non-existing attendees
	if( count( $non_existing ) >= 1 )
	{
		$old_non_existing = bp_get_option( 'bpe_non_existing_attendees' );
		
		$non_exist = array_merge_recursive( (array)$old_non_existing, $non_existing );
		
		$ne = array();
		foreach( $non_exist as $user_mail => $event_ids )
			$ne[$user_mail] = array_unique( (array)$event_ids );
		
		bp_update_option( 'bpe_non_existing_attendees', $ne );
	}
	
	// add the actual buyer to the array
	$attendees[$buyer->user_email] = bp_core_get_user_displayname( $buyer->ID );
	
	foreach( (array)$attendees as $email => $name )
	{
		$pdf = bpe_tickets_create_pdf( $event, $ticket, $sale, $email, $name, 'F' );

		// add invite text if user is not registered yet
		$invite_txt = '';
		if( in_array( $email, (array)$emails ) )
		{
			$invite_txt = sprintf( __( "You can sign up to %s and have a look around the online presence of this event. Sign up here:\n%s\n\nTo be automatically added to the event on sign-up you need to use the following email address:\n%s\n\nIf you are already registered with a different email address, log in and enter the above email address on the following page:\n%s\n\n---------\n\n", 'events' ),
									get_bloginfo( 'name' ),
									bp_get_root_domain() .'/'. $bp->pages->register->slug .'/',
									$email,
									bpe_get_event_link( $event ) . bpe_get_option( 'signup_slug' ) .'/'. wp_hash( $email ) .'/'
								);
		}
		
		$email_body = sprintf( __( "Hello %s,\n\nplease find attached your ticket for %s.\n\n---------\n\nSingle Price:\n%s %d\n\nEvent Start:\n%s\n\nEvent End:\n%s\n\nLink:\n%s\n\n---------\n\n%sYour %s Team", 'events' ),
									$name,
									bpe_get_event_name( $event ),
									$sale->currency,
									$sale->single_price,
									bpe_get_event_start_date( $event ) .' '. bpe_get_event_start_time( $event ),
									bpe_get_event_end_date( $event ) .' '. bpe_get_event_end_time( $event ),
									bpe_get_event_link( $event ),
									$invite_txt,
									get_bloginfo( 'name' )
								);
		
		wp_mail( $email, sprintf( __( '[%s] Event Tickets', 'events' ), get_bloginfo( 'name' ) ), $email_body, '', array( $pdf ) );
		
		// remove the pdf
		if( file_exists( $pdf ) ) @unlink( $pdf );
		unset( $pdf );
	}
}

/**
 * Maybe add a user to an event on signup
 * 
 * Attached to the <code>bp_core_activated_user</code> action hook
 * 
 * @package	Tickets
 * @since 	2.0
 * 
 * @param	int		$user_id
 */
function bpe_tickets_add_user_on_signup( $user_id )
{
	$user = new WP_User( $user_id );
	$attendees = bp_get_option( 'bpe_non_existing_attendees' );
	
	$event_ids = $attendees[$user->user_email];
	
	if( $event_ids )
	{
		foreach( (array)$event_ids as $event_id )
		{
			bpe_add_member( null, $event_id, $user_id, 1, bp_core_current_time(), 'attendee' );
			
			$data = bpe_get_events( array( 'ids' => $event_id, 'past' => false, 'future' => false ) );
			do_action( 'bpe_attend_event', $data['events'][0], $user_id ); 
		}
	
		if( isset( $attendees[$user->user_email] ) )
			unset( $attendees[$user->user_email] );
		
		bp_update_option( 'bpe_non_existing_attendees', $attendees );
	}
}
add_action( 'bp_core_activated_user', 'bpe_tickets_add_user_on_signup' );

/**
 * Process a self sign up
 * 
 * Attached to the <code>wp</code> action hook
 * 
 * @package	Tickets
 * @since 	2.0
 */
function bpe_tickets_add_user_self_signup()
{
	if( isset( $_POST['event-signup'] ) )
	{
		check_admin_referer( 'bpe_member_self_signup' );
		
		$user = new WP_User( (int)bp_loggedin_user_id() );
		$attendees = bp_get_option( 'bpe_non_existing_attendees' );
		
		$email = ( isset( $_POST['event_email'] ) ) ? $_POST['event_email'] : false;
		$hash = ( isset( $_POST['hash'] ) ) ? $_POST['hash'] : false;
				
		if( ! is_email( $email ) )
		{
			bp_core_add_message( __( 'Enter a valid email address.', 'events' ), 'error' );
			bp_core_redirect( bp_get_root_domain() . $_POST['_wp_http_referer'] );
		}

		$event_ids = $attendees[$email];

		if( ! $event_ids || ! in_array( bpe_get_displayed_event( 'id' ), $event_ids ) )
		{
			bp_core_add_message( __( 'You are not allowed to sign yourself up for this event.', 'events' ), 'error' );
			bp_core_redirect( bp_get_root_domain() . $_POST['_wp_http_referer'] );
		}

		if( $hash != wp_hash( $email ) )
		{
			bp_core_add_message( __( 'An unidentified error occured. Please try again!', 'events' ), 'error' );
			bp_core_redirect( bp_get_root_domain() . $_POST['_wp_http_referer'] );
		}
		
		bpe_add_member( null, bpe_get_displayed_event( 'id' ), bp_loggedin_user_id(), 1, bp_core_current_time(), 'attendee' );
		do_action( 'bpe_attend_event', bpe_get_displayed_event(), bp_loggedin_user_id() ); 
		
		if( $key = array_search( bpe_get_displayed_event( 'id' ), $event_ids ) )
			unset( $attendees[$user->user_email][$key] );
		
		bp_update_option( 'bpe_non_existing_attendees', $attendees );
		
		bp_core_add_message( __( 'You are attending this event.', 'events' ) );
		bp_core_redirect( bpe_get_event_link( bpe_get_displayed_event() ) );
	}
}
add_action( 'wp', 'bpe_tickets_add_user_self_signup', 0 );

/**
 * Create an actual ticket fo a single buyer
 * 
 * Needs to be called once for every email/name
 * 
 * @package	Tickets
 * @since 	2.0
 * 
 * @param	object		$event
 * @param	object		$ticket
 * @param	object		$sale
 * @param	string		$email
 * @param	string		$name
 * @param	string		$output
 */
function bpe_tickets_create_pdf( $event = false, $ticket = false, $sale = false, $email = false, $name = false, $output = 'D' )
{
	if( ! $event || ! $ticket || ! $sale || ! $email || ! $name )
		return false;

	do_action( 'bpe_before_ticket_creation', $event, $ticket, $sale, $email, $name, $output );
	
	$proceed = apply_filters( 'bpe_use_default_ticket', true );
	
	if( ! $proceed )
		return false;

	if( ! class_exists( 'Buddyvents_PDF_Tickets' ) )
		require( EVENT_ABSPATH .'components/tickets/bpe-tickets-pdf.php' );
	
	// put it together
	$pdf = new Buddyvents_PDF_Tickets( $event );

	$pdf->AddPage();
	
	// set the header of the document
	$pdf->SetFont( 'Arial', '', 16 );

	$pdf->Cell( 0, 18, utf8_decode( bpe_get_event_name( $event ) ), 'B', 2, 'L' );
	$pdf->Cell( 0, 12, '', 0, 2, 'L' );
	$pdf->SetFontSize( 8 );
	$pdf->Cell( 250, 8, __( 'From', 'events' ), 0, ( ( ! bpe_is_all_day_event() ) ? 0 : 1 ), 'L' );
	
	if( ! bpe_is_all_day_event() )
		$pdf->Cell( 250, 8, __( 'To', 'events' ), 0, 1, 'L' );
	
	$pdf->SetFontSize( 12 );
	$pdf->Cell( 250, 15, sprintf( __( '%s at %s', 'events' ), bpe_get_event_start_date( $event ), bpe_get_event_start_time( $event ) ), 0, ( ( ! bpe_is_all_day_event() ) ? 0 : 1 ), 'L' );
	
	if( ! bpe_is_all_day_event() )
		$pdf->Cell( 250, 15, sprintf( __( '%s at %s', 'events' ), bpe_get_event_end_date( $event ), bpe_get_event_end_time( $event ) ), 0, 1, 'L' );

	$pdf->Cell( 0, 8, '', 0, 2, 'L' );
	$pdf->SetFontSize( 8 );
	$pdf->Cell( 250, 8, __( 'Name', 'events' ), 0, 0, 'L' );
	$pdf->Cell( 250, 8, __( 'Ticket', 'events' ), 0, 1, 'L' );
	$pdf->SetFontSize( 12 );
	$pdf->Cell( 250, 15, utf8_decode( $name ), 0, 0, 'L' );
	$pdf->Cell( 250, 15, utf8_decode( $ticket->name ), 0, 1, 'L' );
	$pdf->Cell( 0, 8, '', 'B', 2, 'L' );
	$pdf->Cell( 0, 8, '', 0, 2, 'L' );
	$pdf->QRCode( $email );	
	$pdf->SetFontSize( 8 );
	$pdf->Cell( 0, 8, __( 'Location', 'events' ), 0, 1, 'L' );
	$pdf->SetFontSize( 12 );
	$pdf->Cell( 0, 16, utf8_decode( bpe_get_event_location( $event ) ), 0, 1, 'L' );
	$pdf->Cell( 0, 16, '', 0, 2, 'L' );
	$pdf->SetFontSize( 8 );
	$venue = ( bpe_get_event_venue_name( $event ) ) ? __( 'Venue', 'events' ) : '';
	$pdf->Cell( 0, 8, $venue, 0, 1, 'L' );
	$pdf->SetFontSize( 12 );
	$pdf->Cell( 290, 16, utf8_decode( bpe_get_event_venue_name( $event ) ), 0, 0, 'L' );
	$pdf->SetFontSize( 16 );
	$pdf->Cell( 290, 18, $sale->currency .' '. $sale->single_price, 0, 1, 'R' );
	$pdf->Cell( 0, 8, '', 'B', 1, 'L' );
	$pdf->Cell( 0, 6, '', 0, 1, 'L' );
	$pdf->SetFontSize( 8 );
	$pdf->Cell( 290, 10, __( 'Ticket is valid for 1 person only. Please print this ticket.', 'events' ), 0, 0, 'L' );
	$pdf->Cell( 280, 10, wp_hash( $email ), 0, 1, 'R' );

	// some metadata
	$pdf->SetMetaData();
	
	// send to browser or safe temporarily
	return $pdf->Finalize( $output, $name );
}

/**
 * Create data points for a flot chart
 * 
 * @package	Tickets
 * @since 	2.0
 * 
 * @param	array 	$sales
 * @param	string	$month
 * @param	string	$year
 * @return	string
 * 
 * @uses	zeroise()
 * @uses	bpe_sale_get_commission()
 */
function bpe_sales_chart_points( $sales = array(), $month = false, $year = false )
{
	$by_currency = array();
	foreach( $sales as $sale )
		$by_currency[$sale->currency][] = $sale;
	
	$fill_days = false;
	if( ! empty( $month ) && ! empty( $year ) )
	{
		$days = date( 't', mktime( 0, 0, 0, $month, 1, $year ) ); 
		$fill_days = true;
	}
	
	$js_arr = array();
	foreach( $by_currency as $currency => $sales )
	{
		$points = array();
		foreach( (array)$sales as $sale )
		{
			$timestamp = strtotime( $sale->sale_date .' UTC' ) * 1000;
			$points[] = array( $timestamp, bpe_sale_get_commission( $sale ) );
		}
		
		if( $fill_days )
		{
			// fill up days without sale with one point
			for( $day = 1; $day <= $days; $day++ )
			{
				$current = $year .'-'. $month .'-'. zeroise( $day, 2 );
				
				$has_data = false;
				foreach( (array)$points as $point )
				{
					$date = date( 'Y-m-d', ( $point[0] / 1000 ) );

					if( $current == $date )
					{
						$datapoints[] = $point;
						$has_data = true;
					}
				}

				// stop once we hit the current date
				if( $current > date( 'Y-m-d' ) )
					break;
				
				if( $has_data )
					continue;
				else
				{
					$timestamp = strtotime( $current .' UTC' ) * 1000;
					$datapoints[] = array( $timestamp, 0 );
				}
			}

		}
		else
			$datapoints = $points;
			
		$js = "'". strtolower( $currency ) ."':{\n";
			$js .= "label:'". $currency ."',\n";
			$js .= "data:". json_encode( $datapoints ) .",\n";
		$js .= "}\n";
		
		$js_arr[] = $js;
	}
	
	if( count( $js_arr ) <= 0 )
	{
		if( date( 'Ym' ) == ( $year . $month ) )
			$days_month = date( 'd' );
		else
			$days_month = date( 't', mktime( 0, 0, 0, $month, 1, $year ) );
			
		for( $i = 1; $i <= $days_month; $i++ )
		{
			$timestamp = strtotime( $year .'-'. $month .'-'. zeroise( $i, 2 ) .' UTC' ) * 1000;
			$datapoints[] = array( $timestamp, 0 );
		}
		
		$js = "'". strtolower( __( 'Sales', 'events' ) ) ."':{\n";
			$js .= "label:'". __( 'Sales', 'events' ) ."',\n";
			$js .= "data:". json_encode( $datapoints ) .",\n";
		$js .= "}\n";
		
		$js_arr[] = $js;
	}
	
	echo join( ', ', (array)$js_arr );
}

/**
 * Create database invoices from an array of sale ids
 * 
 * @package	Tickets
 * @since 	2.0
 */
function bpe_tickets_create_invoices( $sale_ids = array(), $month = false )
{
	if( ! $month )
		return false;
		
	// get all sales
	$sales = bpe_get_sales( array( 'ids' => $sale_ids ) );

	// sort by user
	$user_sales = array();
	foreach( (array)$sales['sales'] as $sale )
	{
		// make sure we don't charge sales twice or process non-completed sales
		if( $sale->status != 'completed' || $sale->requested == 1 )
			continue;
		
		// take currencies into account
		// normally only one currency per user, but edge cases might have more
		$user_sales[$sale->seller_id][$sale->currency][] = $sale;
	}
	
	$invoice_counter = 0;

	// enter invoices into database
	foreach( (array)$user_sales as $user_id => $user_sales )
	{
		foreach( (array)$user_sales as $currency => $total_sales )
		{
			$uids = array();
			foreach( $total_sales as $usale )
				$uids[] = $usale->id;

			if( is_array( $uids ) )
			{
				if( bpe_add_invoice( null, $user_id, maybe_serialize( $uids ), $month, '0000-00-00 00:00:00', 0, 0 ) )
				{
					bpe_sales_set_requested( $uids, 1 );
					$invoice_counter++;
				}
			}
		}
		
	}
	
	if( $invoice_counter >= 1 )
		return true;
		
	return false;
}

/**
 * Create PDF invoices
 * $client and $event are only needed for test PDF
 * 
 * @package	Tickets
 * @since 	2.0
 */
function bpe_tickets_produce_invoice( $invoice = false, $output = 'F', $client = false, $event = false )
{
	global $bpe;
	
	if( ! $invoice )
		return false;

	do_action( 'bpe_before_invoice_creation', $invoice, $output );
	
	$proceed = apply_filters( 'bpe_use_default_invoice', true );
	
	if( ! $proceed )
		return false;
		
	require( EVENT_ABSPATH .'components/tickets/bpe-tickets-invoice.php' );
	
	$pdf = new Buddyvents_PDF_Invoices();
	$pdf->AliasNbPages();
	
	$pdf->SetDisplayMode( 'default' );
	$pdf->SetLeftMargin( 20 );

	$pdf->invoice_date = date( bpe_get_option( 'date_format' ) );
	$pdf->invoice_number = zeroise( $invoice->id, 10 );
	$pdf->client_number = zeroise( $invoice->user_id, 10 );
	
	if( ! $client )
		$client = bp_get_user_meta( $invoice->user_id, 'bpe_billing_address', true );

	$pdf->client_name = bp_core_get_user_displayname( $invoice->user_id );
	$pdf->client_company = $client['company'];
	$pdf->client_street = $client['street'];
	$pdf->client_postcode = $client['postcode'];
	$pdf->client_city = $client['city'];
	$pdf->client_country = $client['country'];

	$pdf->currency = $invoice->datasets[0]->currency;

	$pdf->AddPage();
	$pdf->SetFont( 'Helvetica', '',10 );
	
	$pos = 1;
	$commission = 0;
	foreach( (array)$invoice->datasets as $sale )
	{
		if( ! $event )
			$event = bpe_ticket_sale_get_event( $sale->id );

		$description = sprintf( __( "%s ticket sale commission for %s on %s", 'events' ), $sale->commission .'%', bpe_get_event_name( $event ), mysql2date( bpe_get_option( 'date_format' ), $sale->sale_date ) );
		$com = bpe_sale_get_commission( $sale );
		$pdf->SetProductLine( $pos, $description, $sale->single_price, $sale->quantity, $com );
		$commission += $com;
		$pos++;
	}

	$pdf->SetEnd( $commission );
	
	$pdf->SetAuthor( bp_core_get_user_displayname( $invoice->user_id ) );
	$pdf->SetCreator( sprintf( 'Buddyvents %s', Buddyvents::VERSION ) );
	$pdf->SetTitle( sprintf( __( 'Invoice Nr: %s', 'events' ), $pdf->invoice_number ) );
	$pdf->SetSubject( __( 'Invoice', 'events' ) );
	$pdf->SetKeywords( 'invoice,bill,receipt' );
	
	$date = ( isset( $invoice->sent_date ) ) ? mysql2date( 'Ymd', $invoice->sent_date ) : date( 'Ymd' );
	$file = sanitize_file_name( $invoice->id .'-'. $date . $invoice->user_id .'-'. str_replace( '/', '', $invoice->month ) .'.pdf' );

	if( $output == 'F' )
		$file = EVENT_ABSPATH .'components/tickets/pdf-cache/'. $file;

	$pdf->Output( $file, $output );

	return $file;
}

/**
 * Print invoice out to screen
 * 
 * @package	Tickets
 * @since 	2.0
 */
function bpe_create_invoice_for_member()
{
	global $bp;
	
	if( bp_is_current_component( bpe_get_base( 'slug' ) ) && bp_is_current_action( bpe_get_option( 'invoice_slug' ) ) && bp_is_action_variable( 'view', 0 ) && is_numeric( $bp->action_variables[1] ) )
	{
		$invoice_id = (int)bp_action_variable( 1 );
		
		check_admin_referer( 'bpe_invoice_member-'. $invoice_id );
		
		$data = bpe_get_invoices( array( 'ids' => array( $invoice_id ) ) );
		$invoice = $data['invoices'][0];
		
		if( bp_displayed_user_id() != $invoice->user_id )
			bp_core_redirect( bp_get_root_domain() );
		
		bpe_tickets_produce_invoice( $invoice, 'D' );
	}
}
add_action( 'wp', 'bpe_create_invoice_for_member', 0 );

/**
 * Remove any tickets for an event
 * 
 * @package	Tickets
 * @since 	2.0
 */
function bpe_delete_tickets_on_event_deletion( $event )
{
	bpe_delete_tickets_by_event( bpe_get_event_id( $event ) );
}
add_action( 'bpe_delete_event_action', 'bpe_delete_tickets_on_event_deletion' );

/**
 * Add all schedules from the parent event
 * 
 * @package Tickets
 * @since 	2.0
 */
function bpe_add_recurrent_tickets( $old_event_id, $new_event_id )
{
	if( bpe_get_option( 'enable_tickets' ) == false || empty( $old_event_id ) || empty( $new_event_id ) )
		return false;
	
	$old_event = new Buddyvents_Events( $old_event_id );
	$old_tickets = bpe_get_tickets( array( 'event_id' => $old_event_id ) );

	foreach( $old_tickets as $ticket )
	{
		$start_sales = ( empty( $ticket->start_sales ) ) ? '' : bpe_get_schedule_timestamp( bpe_get_event_recurrent( $old_event ), strtotime( $ticket->start_sales 	) );
		$end_sales	 = ( empty( $ticket->end_sales	 ) ) ? '' : bpe_get_schedule_timestamp( bpe_get_event_recurrent( $old_event ), strtotime( $ticket->end_sales	) );
		
		bpe_add_ticket( null, $new_event_id, $ticket->name, $ticket->description, $ticket->price, $ticket->currency, $ticket->quantity, $start_sales, $end_sales, $ticket->min_tickets, $ticket->max_tickets );
	}
}
add_action( 'bpe_add_to_recurrent_via_component', 'bpe_add_recurrent_tickets', 10, 2 );
?>