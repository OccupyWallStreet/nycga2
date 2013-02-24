<?php
class EM_ML{
	static public $translatable_options;
	static public $langs;
	static public $wplang;

	function init(){
		self::$translatable_options = apply_filters('em_ml_translatable_options', array(
			//GENERAL TAB
				//event submission forms
				'dbem_events_anonymous_result_success',
				'dbem_events_form_result_success',
				'dbem_events_form_result_success_updated',
			//FORMATTING TAB
				//events
				'dbem_event_list_groupby_format',
				'dbem_event_list_item_format_header',
				'dbem_event_list_item_format',
				'dbem_event_list_item_format_footer',
				'dbem_no_events_message',
				'dbem_list_date_title',
				'dbem_single_event_format',
				//Search Form
				'dbem_serach_form_submit',
				'dbem_search_form_text_label',
				'dbem_search_form_categories_label',
				'dbem_search_form_countries_label',
				'dbem_search_form_regions_label',
				'dbem_search_form_states_label',
				'dbem_search_form_towns_label',
				//Date/Time
				'dbem_date_format',
				'dbem_date_format_js',
				'dbem_dates_Separator',
				'dbem_time_format',
				'dbem_times_Separator',
				'dbem_event_all_day_message',
				//Calendar
				'dbem_small_calendar_event_title_format',
				'dbem_small_calendar_event_title_separator',
				'dbem_full_calendar_event_format',
				'dbem_display_calendar_events_limit_msg',
				'dbem_ical_description_format',
				//Locations
				'dbem_location_list_item_format_header',
				'dbem_location_list_item_format',
				'dbem_location_list_item_format_footer',
				'dbem_no_locations_message',
				'dbem_location_page_title_format',
				'dbem_single_location_format',
				'dbem_location_event_list_item_header_format',
				'dbem_location_event_list_item_format',
				'dbem_location_event_list_item_footer_format',
				'dbem_location_no_events_message',
				//Categories
				'dbem_categories_list_item_format_header',
				'dbem_categories_list_item_format',
				'dbem_categories_list_item_format_footer',
				'dbem_no_categories_message',
				'dbem_category_page_title_format',
				'dbem_category_page_format',
				'dbem_category_event_list_item_header_format',
				'dbem_category_event_list_item_format',
				'dbem_category_event_list_item_footer_format',
				'dbem_category_no_events_message',
				//Tags
				'dbem_tag_page_title_format',
				'dbem_tag_page_format',
				'dbem_tag_event_list_item_header_format',
				'dbem_tag_event_list_item_format',
				'dbem_tag_event_list_item_footer_format',
				'dbem_tag_no_events_message',
				//RSS
				'dbem_rss_main_description',
				'dbem_rss_main_title',
				'dbem_rss_title_format',
				'dbem_rss_description_format',
				//Maps
				'dbem_map_text_format',
				'dbem_location_baloon_format',
			//Bookings
				//Pricing Options
				'dbem_bookings_currency_thousands_sep',
				'dbem_bookings_currency_decimal_point',
				'dbem_bookings_currency_format',
				'dbem_bookings_tax',
				//booking feedback messages
				'dbem_booking_feedback_cancelled',
				'dbem_booking_warning_cancel',
				'dbem_bookings_form_msg_disabled',
				'dbem_bookings_form_msg_closed',
				'dbem_bookings_form_msg_full',
				'dbem_bookings_form_msg_attending',
				'dbem_bookings_form_msg_bookings_link',
				'dbem_booking_feedback',
				'dbem_booking_feedback_pending',
				'dbem_booking_feedback_full',
				'dbem_booking_feedback_error',
				'dbem_booking_feedback_email_exists',
				'dbem_booking_feedback_log_in',
				'dbem_booking_feedback_nomail',
				'dbem_booking_feedback_already_booked',
				'dbem_booking_feedback_min_space',
				'dbem_booking_button_msg_book',
				'dbem_booking_button_msg_booking',
				'dbem_booking_button_msg_booked',
				'dbem_booking_button_msg_error',
				'dbem_booking_button_msg_full',
				'dbem_booking_button_msg_cancel',
				'dbem_booking_button_msg_canceling',
				'dbem_booking_button_msg_cancelled',
				'dbem_booking_button_msg_cancel_error',
				//booking form options
				'dbem_bookings_submit_button',
				//booking email templates
				'dbem_bookings_contact_email_subject',
				'dbem_bookings_contact_email_body',
				'dbem_contactperson_email_cancelled_subject',
				'dbem_contactperson_email_cancelled_body',
				'dbem_bookings_email_confirmed_subject',
				'dbem_bookings_email_confirmed_body',
				'dbem_bookings_email_pending_subject',
				'dbem_bookings_email_pending_body',
				'dbem_bookings_email_rejected_subject',
				'dbem_bookings_email_rejected_body',
				'dbem_bookings_email_cancelled_subject',
				'dbem_bookings_email_cancelled_body',
				//event submission templates
				'dbem_event_submitted_email_subject',
				'dbem_event_submitted_email_body',
				'dbem_event_resubmitted_email_subject',
				'dbem_event_resubmitted_email_body',
				'dbem_event_published_email_subject',
				'dbem_event_approved_email_body',
				'dbem_event_reapproved_email_subject',
				'dbem_event_reapproved_email_body',
			//Pro Stuff
				'em_booking_form_error_required',
				//email reminders
				'dbem_emp_emails_reminder_subject',
				'dbem_emp_emails_reminder_body',
				//payment gateway options (pro, move out asap)
				'dbem_gateway_label',
				//gateways - don't work atm
				'em_paypal_booking_feedback',
				'em_paypal_booking_feedback_free',
				'em_paypal_booking_feedback_thanks',
				'em_offline_option_name',
				'em_offline_booking_feedback',
				'em_offline_option_name',
				'em_offline_button',
				'em_authorize_aim_option_name',
				'em_authorize_aim_booking_feedback',
				'em_authorize_aim_booking_feedback_free'
		));
		self::$langs = apply_filters('em_ml_langs', array());
		self::$wplang = apply_filters('em_ml_wplang',get_locale());
		
		if( !is_admin() ){
			if( count(self::$langs) > 0 && self::$wplang != get_locale() ){
			 	foreach(self::$translatable_options as $option){
			 	    add_filter('pre_option_'.$option, array(&$this, 'pre_option_'.$option), 1,1);
		 		}
			}
		}
	}
	
	function __call($filter_name, $value){
		if( strstr($filter_name, 'pre_option_') !== false ){
		    //we're calling an option to be overriden by the default language
		    $option_name = str_replace('pre_option_','',$filter_name);
		    $translations = get_option($option_name.'_ml');
		    if( !empty($translations[get_locale()]) ){
				return $translations[get_locale()];
		    }
		}
		return $value[0];
	}

	static function is_option_translatable($option){
		return count(self::$langs) > 0 && in_array($option, self::$translatable_options);
	}

	static function get_langs(){
		return self::$langs;
	}

	static function get_option($option, $lang, $return_original = true){
		if( self::is_option_translatable($option) ){
			$option_langs = get_option($option.'_ml', array());
			if( !empty($option_langs[$lang]) ){
				return $option_langs[$lang];
			}
		}
		return $return_original ? get_option($option):'';
	}
}
$EM_ML = new EM_ML();
add_action('plugins_loaded', array(&$EM_ML, 'init'), 100);