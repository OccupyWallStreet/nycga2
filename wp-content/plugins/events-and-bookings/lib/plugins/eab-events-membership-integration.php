<?php
/*
Plugin Name: Membership Integration
Description: Allows Events+ to Integrate with Membership plugin, so that members can be selected to be exempt from payment for paid events. <br /><b>Requires <a href="http://premium.wpmudev.org/project/membership">Membership plugin</a>.</b>
Plugin URI: http://premium.wpmudev.org/project/events-and-booking
Version: 0.25
Author: Hakan Evin
*/

/*
Detail: Adds a field to the Event page so that you can select which membership level(s) will be exempt from payments for a selected paid event.
*/

class Eab_Events_MembershipIntegration {

	/**
	 * Constructor
	 */	
	private function __construct () {

		global $wpdb;
		$this->db = &$wpdb;
		// At the beginning, we assume Membership plugin is not activated.
		// After all plugins loaded we will check this variable
		$this->membership_active = false;
	}

	/**
	 * Run the Addon
	 *
	 */	
	public static function serve () {
		$me = new Eab_Events_MembershipIntegration;
		$me->_add_hooks();
	}

	/**
	 * Hooks to the main plugin Events+
	 *
	 */	
	private function _add_hooks () {
		add_action('admin_enqueue_scripts', array( &$this, 'load_scripts'));
		add_action('plugins_loaded', array( &$this, 'check_membership_plugin'));
		add_action('admin_notices', array($this, 'show_nags'));
		add_action('eab-event_meta-save_meta', array(&$this, 'save_membership_meta'));
		add_filter('eab-event_meta-event_meta_box-after', array( &$this, 'event_meta_box'));
		add_filter('eab-event-show_pay_note',array(&$this,'will_show_pay_note'), 10, 2);
		add_filter('eab-event-payment_status',array(&$this,'status'), 10, 2);
	}

	/**
	 * Load jQuery multiselect
	 */	
	function load_scripts() {
		wp_enqueue_script('jquery-multiselect',plugins_url('events-and-bookings/js/').'jquery.multiselect.min.js',array('jquery','jquery-ui-widget'));
		wp_enqueue_style('jquery-multiselect-css',plugins_url('events-and-bookings/css/').'jquery.multiselect.css');
	}

	/**
	 * Warn admin
	 */	
	function show_nags () {
		if (!$this->membership_active) {
			echo '<div class="error"><p>' .
				__("You'll need <a href='http://premium.wpmudev.org/project/membership'>Membership</a> plugin installed and activated for Membership integration add-on to work", Eab_EventsHub::TEXT_DOMAIN) .
			'</p></div>';
		}
	}

	/**
	 * Check if Membership plugin is active
	 *
	 */	
	function check_membership_plugin() {
		if( ( is_admin() AND class_exists('membershipadmin') ) 
			OR 
			( !is_admin() AND class_exists('membershippublic') ) )
				$this->membership_active = true;
	}

	/**
	 * Check if 'You havent paid for this event' note will be displayed
	 * If user is a member and his level is sufficent, return false, i.e don't show pay note
	 * Otherwise return whatever sent here
	 */	
	function will_show_pay_note( $show_pay_note, $event_id ) {
		if ( $this->membership_active ) {
			global $current_user;
			$meta = get_post_meta( $event_id, 'eab_events_mi', true );
			$member = new M_Membership($current_user->ID);
			if( $meta["sel"] AND $current_user->ID > 0 AND $member->has_levels()) {
				// Load the levels for this member
				$levels = $member->get_level_ids( );
				if ( is_array( $levels ) AND is_array( $meta["level"] ) ) {
					foreach ( $levels as $level ) {
						if ( in_array( $level->level_id, $meta["level"] ) )
							return false; // Yes, user has sufficent level
					}
				}
			}
		}
		return $show_pay_note;
	}

	/**
	 * Modify payment status text if user is a member with sufficent level
	 */		
	function status ( $payment_status, $user_id ) {
		if ( $this->membership_active AND $user_id > 0 ) {
			global $post;
			$meta = get_post_meta( $post->ID, 'eab_events_mi', true );
			$member = new M_Membership($user_id);
			$levels = $member->get_level_ids( );
			if ( is_array( $levels ) AND is_array( $meta["level"] ) ) {
				foreach ( $levels as $level ) {
					if ( in_array( $level->level_id, $meta["level"] ) )
						return "Member"; // Yes, user has sufficent level
				}
			}
		}
		return $payment_status;
	}

	/**
	 * Save post meta
	 *
	 */	
	function _save_meta ($post_id, $REQUEST) {
		if (!isset($REQUEST['eab_events_mi'])) 
			return false;
		update_post_meta($post_id, 'eab_events_mi', $REQUEST['eab_events_mi']);
	}
	function save_membership_meta ($post_id) {
		$this->_save_meta($post_id, $_POST);	
	}

	/**
	 * Add HTML codes to the event meta box
	 *
	 */	
	function event_meta_box( $content ) {
		global $post;
		
		$meta = get_post_meta( $post->ID, 'eab_events_mi', true );
	
		$content .= '<div class="eab_meta_box">';
		$content .= '<input type="hidden" name="incsub_event_membership_meta" value="1" />';
		$content .= '<div class="misc-eab-section">';
		$content .= '<div class="eab_meta_column_box">'.__('Membership Integration', Eab_EventsHub::TEXT_DOMAIN).'</div>';
		if ( $this->membership_active ) {
			$content .= '<label for="incsub_event_membership_select" id="incsub_event_membership_select_label">'.__('Free to members? ', Eab_EventsHub::TEXT_DOMAIN).':</label>&nbsp;';
			$content .= '<select name="eab_events_mi[sel]" id="incsub_event_membership_select" class="incsub_event_paid" >';
			$content .= '<option value="1" ' . ($meta["sel"] ? 'selected="selected"' : '') . '>'.__('Yes', Eab_EventsHub::TEXT_DOMAIN).'</option>';
			$content .= '<option value="0" ' . ($meta["sel"] ? '' : 'selected="selected"') . '>'.__('No', Eab_EventsHub::TEXT_DOMAIN).'</option>';
			$content .= '</select>';
			$content .= '<div class="clear"></div>';
			$content .= '<label for="incsub_event_membership_level" id="incsub_event_membership_level_label">'.__('Free for', Eab_EventsHub::TEXT_DOMAIN).':&nbsp;&nbsp;';
			$content .= '</label>';
			global $membershipadmin;
			$levels = $membershipadmin->get_membership_levels(array('level_id' => 'active'));
			if ( is_array( $levels ) ) {
				$content .= '<select multiple="multiple" name="eab_events_mi[level][]" id="incsub_event_membership_level" class="incsub_event_membership_level" >';
				foreach ( $levels as $level ) {
					if ( $level->level_slug != 'visitors' ) { // Do not include strangers
						if ( is_array( $meta["level"] ) AND in_array( $level->id, $meta["level"] ) )
							$sela = 'selected="selected"';
						else
							$sela = '';
						$content .= '<option value="'.$level->id.'"' . $sela . '>'. $level->level_title . '</option>';
					}
				}
				$content .= '</select>';
				$content .= '<script type="text/javascript">
					jQuery(document).ready(function($){
					   $("#incsub_event_membership_level").multiselect({
						noneSelectedText: "Select levels",
						height: 200,
						minWidth: 200,
						selectedList: 3,
						position:{
							  my: "left bottom",
							  at: "left top"
							}
					   });
					});
				</script>';
			}
			else
				$content .= __('No level was defined yet',Eab_EventsHub::TEXT_DOMAIN);
		}
		else
			$content .= __('Membership plugin is not activated.',Eab_EventsHub::TEXT_DOMAIN);
			
		$content .= '</div>';
		$content .= '</div>';
	
		return $content;
	
	}
}

Eab_Events_MembershipIntegration::serve();