<?php
/*
Plugin Name: Manual Payments
Description: Allows users to pay manually (check, wire transfer, etc)
Plugin URI: http://premium.wpmudev.org/project/events-and-booking
Version: 0.27
Author: Hakan Evin
*/

/*
Detail: Adds codes to the front end for the manual payment instructions. These instructions are entered from this setting page, under <b>Manual Payment settings</b>. Also adds codes to the Event page so that you can select that a member paid manually.
*/


class Eab_Events_ManualPayments {

	private $_data;

	/**
	 * Constructor
	 */	
	private function __construct () {
		$this->_data = Eab_Options::get_instance();
	}

	/**
	 * Run the Addon
	 *
	 */	
	public static function serve () {
		$me = new Eab_Events_ManualPayments;
		$me->_add_hooks();
	}

	/**
	 * Hooks to the main plugin Events+
	 *
	 */	
	private function _add_hooks () {
		add_action('eab-settings-after_payment_settings', array(&$this, 'show_settings'));
		add_action('admin_notices', array(&$this, 'show_nags'));
		add_action('wp_ajax_eab_manual_payment', array(&$this, 'do_payment'));
		add_action('wp_ajax_eab_approve_manual_payment', array(&$this, 'approve_payment'));
		add_filter('eab-event-show_pay_note', array(&$this,'will_show_pay_note'), 10, 2);
		add_filter('eab-event-payment_status', array(&$this,'status'), 10, 2);
		add_filter('eab-event-booking_metabox_content', array(&$this,'add_approve_payment'), 10, 2);
		add_filter('eab-settings-before_save', array(&$this,'save_settings'));
		add_filter('eab-event-payment_forms', array(&$this,'add_select_button'), 10, 2);
		add_filter('eab-event-after_payment_forms', array(&$this,'add_instructions'), 10, 2);
	}

	/**
	 * Ajax call when user clicks payment button
	 */	
	function do_payment() {
		check_ajax_referer( 'manual-payment-nonce', 'nonce' );
		$user_id = $_POST["user_id"];
		$event_id = $_POST["event_id"];
		if ( !$user_id OR !$event_id )
			die();
		$payments = maybe_unserialize( stripslashes( Eab_EventModel::get_booking_meta( $event_id, "manual_payment" ) ) );
		if ( !is_array( $payments ) )
			$payments = array();
		else {
			foreach ( $payments as $payment ) { // Make a check
				if ( $payment["id"] == $user_id ) // User has a record before!?
					die();
			}
		}
		array_push( $payments, array( "id"=>$user_id, "stat"=>"pending"));
		$payments = array_filter( array_unique( $payments ) ); // Clear empty records, just in case
		Eab_EventModel::update_booking_meta( $event_id, "manual_payment", serialize( $payments ) );
		die();
	}

	/**
	 * Adds the button to the front end that reveals instructions box
	 */	
	function add_select_button( $content, $event_id ) {
		if ($this->_data->get_option('paypal_email')) $content .= '<br /><br />';
		$content .= '<a class="wpmudevevents-yes-submit" style="float:none !important" href="javascript:void(0)" id="manual_payment_select_'.$event_id.'">'. $this->_data->get_option('manual_payment_select') . '</a>';
		$content .= '<script type="text/javascript">';
		$content .= 'jQuery(document).ready(function($){
						$("#manual_payment_select_'.$event_id.'").click(function() {
							$("#manual_payment_instructions_'.$event_id.'").toggle("slow");
						});
					});';
		$content .= '</script>';
		
		return $content;
	}

	/**
	 * Adds instructions box to the front end
	 */	
	function add_instructions( $content, $event_id ) {
		global $current_user;
		$content .= '<div class="message" id="manual_payment_instructions_'.$event_id.'" style="display:none">';
		$button = '<a class="wpmudevevents-yes-submit" style="float:none !important" href="javascript:void(0)" id="manual_payment_pay_'.$event_id.'">'. $this->_data->get_option('manual_payment_pay') . '</a>';
		$content .= wpautop(str_replace("MANUALPAYMENTBUTTON", $button,$this->_data->get_option('manual_payment_instructions')));
		$content .= '</div>';
		$content .= '<script type="text/javascript">';
		$content .= 'jQuery(document).ready(function($){
						$("#manual_payment_pay_'.$event_id.'").click(function() {
							$.post("'.admin_url('admin-ajax.php').'", {
								"action": "eab_manual_payment",
								"user_id":'.$current_user->ID.',
								"event_id":'.$event_id.',
								"nonce":"'.wp_create_nonce("manual-payment-nonce").'"
							}, function (data) {
								if (data && data.error) {alert(data.error);}
								else {
									$("#manual_payment_pay_'.$event_id.'").css("opacity","0.2");
									alert("'.__('Thank you for the payment!',Eab_EventsHub::TEXT_DOMAIN).'");
								}
							});
							return false;
						});
					});';
		$content .= '</script>';
		return $content;
	}

	/**
	 * Warn admin if Manual Button is not inserted
	 */
	function show_nags() {
		if ( strpos($this->_data->get_option('manual_payment_instructions'), "MANUALPAYMENTBUTTON") === false) {
			echo '<div class="error"><p>' .
				__("You do not have MANUALPAYMENTBUTTON keyword in the Instructions field. 
				This means there will be no button and user will not be able to inform you that he/she made a payment, 
				which in turn means that Manual Payment will be practically useless.", Eab_EventsHub::TEXT_DOMAIN) .
			'</p></div>';
		}
	}
	 
	/**
	 * Add Addon settings to the other admin options to be saved
	 */	
	function save_settings( $options ) {
		$options['manual_payment_select']		= stripslashes($_POST['event_default']['manual_payment_select']);
		$options['manual_payment_pay']			= stripslashes($_POST['event_default']['manual_payment_pay']);
		$options['manual_payment_instructions']	= stripslashes($_POST['event_default']['manual_payment_instructions']);
		
		return $options;
	}
	
	/**
	 * Admin settings
	 *
	 */	
	function show_settings() {
		if (!class_exists('WpmuDev_HelpTooltips')) 
			require_once dirname(__FILE__) . '/lib/class_wd_help_tooltips.php';
		$tips = new WpmuDev_HelpTooltips();
		$tips->set_icon_url(plugins_url('events-and-bookings/img/information.png'));
		?>
		<div id="eab-settings-paypal" class="eab-metabox postbox">
				<h3 class="eab-hndle"><?php _e('Manual Payment settings :', Eab_EventsHub::TEXT_DOMAIN); ?></h3>
				<div class="eab-inside">
					<div class="eab-settings-settings_item">
					    <label for="incsub_event-manual_payment_select" ><?php _e('Select button text', Eab_EventsHub::TEXT_DOMAIN); ?></label>
						<input type="text" size="40" name="event_default[manual_payment_select]" value="<?php print $this->_data->get_option('manual_payment_select'); ?>" />
						<span><?php echo $tips->add_tip(__('This is the text that will appear on Select Manual Payment button.', Eab_EventsHub::TEXT_DOMAIN)); ?></span>
					</div>
					    
					<div class="eab-settings-settings_item">
					    <label for="incsub_event-manual_payment_pay" ><?php _e('Pay button text', Eab_EventsHub::TEXT_DOMAIN); ?></label>
						<input type="text" size="40" name="event_default[manual_payment_pay]" value="<?php print $this->_data->get_option('manual_payment_pay'); ?>" />
						<span><?php echo $tips->add_tip(__('This is the text that will appear on Pay button. User needs to click this button after he made the payment.', Eab_EventsHub::TEXT_DOMAIN)); ?></span>
					</div>
					
					<div class="eab-settings-settings_item">
					    <label for="incsub_event-manual_payment_instructions" ><?php _e('Instructions', Eab_EventsHub::TEXT_DOMAIN); ?>&nbsp;:</label>
						<span><?php echo $tips->add_tip(__('Write the procedure that the user needs to do for a manual payment here. Use MANUALPAYMENTBUTTON to insert the Pay Button to the desired location.', Eab_EventsHub::TEXT_DOMAIN)); ?></span>
						<?php wp_editor( $this->_data->get_option('manual_payment_instructions'), 'manualpaymentsinstructions', array('textarea_name'=>'event_default[manual_payment_instructions]', 'textarea_rows' => 5) ); ?>
					</div>
					    
				</div>
		    </div>
		<?php
	}

	/**
	 * Check if 'You havent paid for this event' note will be displayed
	 * If user is approved to be paid, return false, i.e don't show pay note
	 * Otherwise return whatever sent here
	 */	
	function will_show_pay_note( $show_pay_note, $event_id ) {
		global $current_user;
		$payments = maybe_unserialize( stripslashes( Eab_EventModel::get_booking_meta( $event_id, "manual_payment") ) );
		if ( is_array( $payments ) ) {
			foreach ( $payments as $payment ) {
				if ( $payment["id"] == $current_user->ID AND $payment["stat"] == "paid" )
					return false; // User approved to be paid
			}
		}
		return $show_pay_note;
	}

	/**
	 * Modify payment status text if user is manually selected that he paid
	 */		
	function status( $payment_status, $user_id ) {
		global $post; // This object should be available as we are on Admin side
		$payments = maybe_unserialize( stripslashes( Eab_EventModel::get_booking_meta( $post->ID, "manual_payment") ) );
		if ( is_array( $payments ) ) {
			foreach ( $payments as $payment ) {
				if ( $payment["id"] == $user_id )
					return $payment["stat"]; // User status is either paid or pending
			}
		}
		return $payment_status;
	}

	/**
	 * Approve a payment on the admin side
	 */	
	function approve_payment() {
		$user_id = $_POST["user_id"];
		$event_id = $_POST["event_id"];
		if ( !$user_id OR !$event_id )
			die(json_encode(array("error"=>"User ID or Event ID is missing")));
		$payments = maybe_unserialize( stripslashes( Eab_EventModel::get_booking_meta( $event_id, "manual_payment" ) ) );
		if ( !is_array( $payments ) )
			die(json_encode(array("error"=>"Record could not be found"))); 
		else {
			foreach ( $payments as $key=>$payment ) { 
				if ( $payment["id"] == $user_id ) {
					$payments[$key]["stat"] = "paid";
					$payments = array_filter( array_unique( $payments ) );
					Eab_EventModel::update_booking_meta( $event_id, "manual_payment", serialize( $payments ) );
					die();
				}
			}
		}
		die(json_encode(array("error"=>"Record could not be found")));
	}
	
	/**
	 * Add manual payment link inside the RSVP box
	 */		
	function add_approve_payment( $content, $user_id ) {
		global $post;
		$payments = maybe_unserialize( stripslashes( Eab_EventModel::get_booking_meta( $post->ID, "manual_payment") ) );
		if ( is_array($payments ) ) {
			foreach ( $payments as $payment ) {
				if ( $payment["id"] == $user_id AND $payment["stat"] == 'pending' ) {
					$content .= '<div class="eab-guest-actions" id="div_approve_payment_'.$user_id.'">
					<a id="approve_payment_'.$user_id.'" href="javascript:void(0)" class="eab-guest-manual_payment" >' .
					__('Approve Payment', Eab_EventsHub::TEXT_DOMAIN) .
					'</a></div>';
					$content .= '<script type="text/javascript">';
					$content .= 'jQuery(document).ready(function($){
									$("#approve_payment_'.$user_id.'").click(function() {
										if (confirm("Are you sure to approve this payment?")){
											$.post(ajaxurl, {
												"action": "eab_approve_manual_payment",
												"user_id":'.$user_id.',
												"event_id":'.$post->ID.'
											}, function (data) {
												if (data && data.error) {alert(data.error);}
												else {
													$("#div_approve_payment_'.$user_id.'").parent(".eab-guest").find(".eab-guest-payment_info").html("paid");
													$("#div_approve_payment_'.$user_id.'").remove();
												}
											},
											"json");
											return false;
										}
										else {return false;}
									});
								});';
					$content .= '</script>';
				}
			}
		}
		return $content;
	}
}

Eab_Events_ManualPayments::serve();