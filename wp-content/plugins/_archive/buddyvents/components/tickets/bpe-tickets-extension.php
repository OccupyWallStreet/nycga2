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

class Buddyvents_Ticket_Extension extends Buddyvents_Extension
{
	/**
	 * PHP5 Constructor
	 * 
	 * @package	Tickets
	 * @since 	2.0
	 * 
	 * @uses	bpe_get_option()
	 * @uses	bp_get_user_meta()
	 * @uses	bp_loggedin_user_id()
	 */
	public function __construct()
	{
 		$this->name = __( 'Tickets', 'events' );
		$this->slug = bpe_get_option( 'tickets_slug' );
		
		$this->paypal = bp_get_user_meta( bp_loggedin_user_id(), 'bpe_paypal_email', true );
		$this->currency = bp_get_user_meta( bp_loggedin_user_id(), 'bpe_paypal_currency', true );

		$this->create_step_position = apply_filters( 'bpe_tickets_create_step_position', 8 );

		$this->enable_create_step = ( empty( $this->paypal ) || empty( $this->currency ) ) ? false : true;
		$this->enable_edit_item = ( empty( $this->paypal ) || empty( $this->currency ) ) ? false : true;
		$this->enable_nav_item = false;
	}

	/**
	 * Display create screen
	 * 
	 * @package	Tickets
	 * @since 	2.0
	 */
	public function create_screen()
	{
		if( ! bpe_is_event_creation_step( $this->slug ) )
			return false;
		?>
        <input type="hidden" id="ticket-counter" value="<?php echo bpe_ticket_counter() ?>" />
        <input type="hidden" id="first-cal-day" value="<?php echo esc_attr( ( bpe_get_option( 'week_start' ) == 1 ) ? 1 : 0 ); ?>" />
        <input type="hidden" id="uid" value="<?php echo esc_attr( bp_loggedin_user_id() ) ?>" />
        <input type="hidden" id="end_date" value="<?php echo esc_attr( bpe_get_displayed_event( 'end_date' ) ) ?>" />
        
        <?php if( bpe_get_option( 'commission_percent' ) ) : ?>
        <p><?php printf( __( '<strong>Note:</strong> %s (%s of %s VAT) of all ticket sales will be invoiced to you each month.', 'events' ), bpe_get_option( 'commission_percent' ) .'%', ( ( ! bpe_get_option( 'invoice_tax' ) ) ? __( 'inclusive', 'events' ) : __( 'exclusive', 'events' ) ), bpe_get_option( 'invoice_tax' ) .'%' ) ?></p>
        <?php endif; ?>
        
        <div id="ticket-wrapper"><?php bpe_edit_tickets() ?></div>
        <a class="button add-ticket" href="#"><?php _e( 'Add ticket', 'events' ) ?></a>
        <small><?php _e( 'Add a ticket to your event. These will be automatically sorted by price.', 'events' ) ?></small>
        
        <script type="text/javascript">
        jQuery(document).ready(function() {
			var today = new Date();
			var maxDate = jQuery( "#end_date" ).val();
			maxDate = maxDate.split('-');

            jQuery(".ticket-date").datepicker({
				<?php if( bpe_get_option( 'week_start' ) == 1 ) : ?>firstDay: 1,<?php endif; ?>
				minDate: today,
				maxDate: new Date( maxDate[0], maxDate[1], maxDate[2] ),
				changeMonth: false,
				changeYear: false,
				dateFormat: "yy-mm-dd"
            });
        });
        </script>
		<?php
		wp_nonce_field( 'bpe_add_event_'. $this->slug );
	}

	/**
	 * Process create screen
	 * 
	 * @package	Tickets
	 * @since 	2.0
	 */
	public function create_screen_save()
	{
		check_admin_referer( 'bpe_add_event_'. $this->slug );
		
		bpe_process_ticket( $_POST, bpe_get_displayed_event() );
	}

	/**
	 * Display edit screen
	 * 
	 * @package	Tickets
	 * @since 	2.0
	 */
	public function edit_screen()
	{
		if( ! bpe_is_event_edit_screen( $this->slug ) )
			return false;

		?>
        <input type="hidden" id="ticket-counter" value="<?php echo bpe_ticket_amount( bpe_get_event_id( bpe_get_displayed_event() ) ) ?>" />
        <input type="hidden" id="first-cal-day" value="<?php echo esc_attr( ( bpe_get_option( 'week_start' ) == 1 ) ? 1 : 0 ); ?>" />
        <input type="hidden" id="uid" value="<?php echo esc_attr( bpe_get_event_user_id( bpe_get_displayed_event() ) ) ?>" />
        <input type="hidden" id="end_date" value="<?php echo esc_attr( bpe_get_displayed_event( 'end_date' ) ) ?>" />
        
        <div id="ticket-wrapper"><?php bpe_edit_tickets() ?></div>
        <a class="button add-ticket" href="#"><?php _e( 'Add ticket', 'events' ) ?></a>
        <small><?php _e( 'Add a ticket to your event. These will be automatically sorted by price.', 'events' ) ?></small>
        
        <div class="submit">
            <input type="submit" value="<?php _e( 'Save Changes', 'events' ) ?>" id="edit-event" name="edit-event" />
        </div>

        <script type="text/javascript">
        jQuery(document).ready(function() {
			var today = new Date();
			var maxDate = jQuery( "#end_date" ).val();
			maxDate = maxDate.split('-');

            jQuery(".ticket-date").datepicker({
				<?php if( bpe_get_option( 'week_start' ) == 1 ) : ?>firstDay: 1,<?php endif; ?>
				minDate: today,
				maxDate: new Date( maxDate[0], maxDate[1], maxDate[2] ),
				changeMonth: false,
				changeYear: false,
				dateFormat: "yy-mm-dd"
            });
        });
        </script>
        <?php
		wp_nonce_field( 'bpe_edit_event_'. $this->slug );
	}

	/**
	 * Process edit screen
	 * 
	 * @package	Tickets
	 * @since 	2.0
	 */
 	public function edit_screen_save()
	{
		if( ! isset( $_POST['edit-event'] ) )
			return false;

		check_admin_referer( 'bpe_edit_event_'. $this->slug );

		bpe_process_ticket( $_POST, bpe_get_displayed_event(), false, true );

		if( is_admin() )
			bp_core_redirect( admin_url( 'admin.php?page='. EVENT_FOLDER .'&paged=1&event='. bpe_get_displayed_event( 'id' ) .'&step='. $this->slug ) );
		else
			bp_core_redirect( bpe_get_event_link( bpe_get_displayed_event() ) . bpe_get_option( 'edit_slug' ) .'/'. $this->slug .'/' );
	}
}
bpe_register_event_extension( 'Buddyvents_Ticket_Extension' );
?>