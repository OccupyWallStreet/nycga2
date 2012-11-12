<?php

class Eab_Template {
	
	public static function get_archive_content ($post, $content=false) {
		$event = ($post instanceof Eab_EventModel) ? $post : new Eab_EventModel($post);
		if ('incsub_event' != $event->get_type()) return $content;
		
		$start_day = date_i18n('m', $event->get_start_timestamp());
		
		$new_content  = '';
		
		$new_content .= '<div class="event ' . self::get_status_class($event) . '">';
		$new_content .= '<a href="' . get_permalink($event->get_id()) . '" class="wpmudevevents-viewevent">' .
			__('View event', Eab_EventsHub::TEXT_DOMAIN) . 
		'</a>';
		$new_content .= apply_filters('eab-template-archive_after_view_link', '', $event);
		$new_content .= '<div style="clear: both;"></div>';
		$new_content .= '<hr />';
		$new_content .= self::get_event_details($event);
		$new_content .= self::get_rsvp_form($event);
		$new_content .= '</div>';
		$new_content .= '<div style="clear:both"></div>';
		
		return $new_content;
	}
	
	public static function get_single_content ($post, $content=false) {
		global $current_user;
		$event = ($post instanceof Eab_EventModel) ? $post : new Eab_EventModel($post);
		
		if ('incsub_event' != $event->get_type()) return $content;
		
		$start_day = date_i18n('m', $event->get_start_timestamp());
		    
		$new_content  = '';
		$new_content .= '<div class="event ' . self::get_status_class($event) . '" id="wpmudevevents-wrapper"><div id="wpmudevents-single">';
		
		$new_content .= self::get_error_notice();
		
		// Added by Hakan
		$show_pay_note = $event->is_premium() && $event->user_is_coming() && !$event->user_paid();
		$show_pay_note = apply_filters('eab-event-show_pay_note', $show_pay_note, $event->get_id () ); 

		if ( $show_pay_note	) {
			$new_content .= '<div id="wpmudevevents-payment">';
			$new_content .= __('You haven\'t paid for this event', Eab_EventsHub::TEXT_DOMAIN).' ';
			$new_content .= self::get_payment_forms($event);
			$new_content .= '</div>';
		} else if ($event->is_premium() && $event->user_paid()) {
			$new_content .= __('You already paid for this event', Eab_EventsHub::TEXT_DOMAIN);
		}
		
		// Added by Hakan
		$new_content = apply_filters('eab-event-after_payment_forms', $new_content, $event->get_id());
	
		$new_content .= '<div class="eab-needtomove"><div id="event-bread-crumbs" >' . self::get_breadcrumbs($event) . '</div></div>';
		
		$new_content .= '<div id="wpmudevevents-header">';
		$new_content .= self::get_rsvp_form($event);
		$new_content .= self::get_inline_rsvps($event);
		$new_content .= '</div>';
		
		$new_content .= '<hr/>';
		
		$new_content .= '<div class="wpmudevevents-content">';
		
		$new_content .= '<div id="wpmudevevents-contentheader">';
		$new_content .= '<h3>' . __('About this event:', Eab_EventsHub::TEXT_DOMAIN) . '</h3>';
		$new_content .= '<div id="wpmudevevents-user">'. __('Created by ', Eab_EventsHub::TEXT_DOMAIN) . self::get_event_author_link($event) . '</div>';
		$new_content .= '</div>';
		
		$new_content .= '<hr/>';
		
		$new_content .= '<div id="wpmudevevents-contentmeta">' . self::get_event_details($event) . '<div style="clear: both;"></div></div>';
		$new_content .= '<div id="wpmudevevents-contentbody">' . ($content ? $content : $event->get_content()) . '</div>';			
		
		if ($event->has_venue_map()) {
			$new_content .= '<div id="wpmudevevents-map">' . $event->get_venue_location(Eab_EventModel::VENUE_AS_MAP) . '</div>';
		}
		$new_content .= '</div>';
		$new_content .= apply_filters('eab-events-after_single_event', '', $event);
		$new_content .= '</div></div>';
		return $new_content;		
	}

	public static function get_inline_rsvps ($post) {
		$event = ($post instanceof Eab_EventModel) ? $post : new Eab_EventModel($post);
		$data = Eab_Options::get_instance();
		
		$content = '';
		if ($event->has_bookings() && $data->get_option('display_attendees') == 1) {
			$content .= '<div id="wpmudevevents-rsvps">';
			$content .= '<a href="' . 
				admin_url('admin-ajax.php?action=eab_list_rsvps&pid=' . $event->get_id()) . 
				'" id="wpmudevevents-load-rsvps" class="hide-if-no-js wpmudevevents-viewrsvps wpmudevevents-loadrsvps">' .
					__('See who has RSVPed', Eab_EventsHub::TEXT_DOMAIN) .
			'</a>';
			$content .= '&nbsp;';
			$content .= '<a href="#" id="wpmudevevents-hide-rsvps" class="hide-if-no-js wpmudevevents-viewrsvps wpmudevevents-hidersvps">' .
				__('Hide who has RSVPed', Eab_EventsHub::TEXT_DOMAIN) .
			'</a>';
			$content .= '</div>';
			$content .= '<div id="wpmudevevents-rsvps-response"></div>';
		}
		
		return $content;
	}
	
	public static function get_rsvps ($post) {
		$data = Eab_Options::get_instance();
		$event = ($post instanceof Eab_EventModel) ? $post : new Eab_EventModel($post);
		$content = '';
		if ($data->get_option('display_attendees') == 1) {
			$content .= '<div class="wpmudevevents-attendees">';
			$content .= '	<div id="event-bookings">';
			$content .= '		<div id="event-booking-yes">';
			$content .= self::get_bookings(Eab_EventModel::BOOKING_YES, $event);
			$content .= '		</div>';
			$content .= '		<div class="clear"></div>';
			$content .= '		<div id="event-booking-maybe">';
			$content .= self::get_bookings(Eab_EventModel::BOOKING_MAYBE, $event);
			$content .= '		</div>';
			$content .= '		<div id="event-booking-no">';
			$content .= self::get_bookings(Eab_EventModel::BOOKING_NO, $event);
			$content .= '		</div>';
			$content .= '	</div>';
			$content .= '</div>';
		}
		return $content;
	}

	public static function get_bookings ($status, $post) {
		global $wpdb;
		$event = ($post instanceof Eab_EventModel) ? $post : new Eab_EventModel($post);
		
		$statuses = array(
			Eab_EventModel::BOOKING_YES => __('Attending', Eab_EventsHub::TEXT_DOMAIN), 
			Eab_EventModel::BOOKING_MAYBE => __('Maybe', Eab_EventsHub::TEXT_DOMAIN), 
			Eab_EventModel::BOOKING_NO => __('No', Eab_EventsHub::TEXT_DOMAIN)
		);
		if (!in_array($status, array_keys($statuses))) return false; // Unknown status
		$status_name = $statuses[$status];
		
		$bookings = $wpdb->get_results($wpdb->prepare("SELECT user_id FROM ".Eab_EventsHub::tablename(Eab_EventsHub::BOOKING_TABLE)." WHERE event_id = %d AND status = %s;", $event->get_id(), $status));
		if (!count($bookings)) return false;
		
		$content = '';		
		$content .= '<h4>'. $status_name . '</h4>';
		$content .= '<ul class="eab-guest-list">';
		foreach ($bookings as $booking) {
			$user_data = get_userdata($booking->user_id);
			$url = defined('BP_VERSION') 
				? bp_core_get_user_domain($booking->user_id) : 
				get_author_posts_url($booking->user_id)
			;
			
			$avatar = '<a href="' . $url . '" title="' . esc_attr($user_data->display_name) . '">' .
				get_avatar($booking->user_id, 32) .
			'</a>';
			$avatar = apply_filters('eab-guest_list-guest_avatar', 
				apply_filters("eab-guest_list-status_{$status}-guest_avatar", $avatar, $booking->user_id, $user_data, $event),
				$booking->user_id, $user_data, $event
			);
			
			$content .= "<li>{$avatar}</li>";
		}
		$content .= '</ul>';
		$content .= '<div class="clear"></div>';
		
		return $content;	
	}

	public static function get_user_events ($status, $user_id) {
		global $wpdb;
		
		$statuses = array(
			Eab_EventModel::BOOKING_YES => __('Attending', Eab_EventsHub::TEXT_DOMAIN), 
			Eab_EventModel::BOOKING_MAYBE => __('Maybe', Eab_EventsHub::TEXT_DOMAIN), 
			Eab_EventModel::BOOKING_NO => __('No', Eab_EventsHub::TEXT_DOMAIN)
		);
		if (!in_array($status, array_keys($statuses))) return false; // Unknown status
		$status_name = $statuses[$status];
		
		$bookings = $wpdb->get_col($wpdb->prepare("SELECT event_id FROM ".Eab_EventsHub::tablename(Eab_EventsHub::BOOKING_TABLE)." WHERE user_id = %d AND status = %s;", $user_id, $status));
		if (!count($bookings)) return false;
		
		$ret = '<div class="wpmudevevents-user_bookings wpmudevevents-user_bookings-' . $status . '">';
		foreach ($bookings as $event_id) {
			$event = new Eab_EventModel(get_post($event_id));
			$ret .= '<h4>' . self::get_event_link($event) . '</h4>';
			$ret .= '<div class="wpmudevevents-event-meta">' . 
				self::get_event_dates($event) .
				'<br />' .
				$event->get_venue_location() . 
			'</div>';
		}
		$ret .= '</div>';
		return $ret;
	}

	public static function get_user_organized_events ($user_id) {
		$events = Eab_CollectionFactory::get_user_organized_events($user_id); 
		
		$ret = '<div class="wpmudevevents-user_bookings wpmudevevents-events-user_organized">';
		foreach ($events as $event) {
			if ($event->is_recurring()) continue;
			$ret .= '<h4>' . self::get_event_link($event) . '</h4>';
			$ret .= '<div class="wpmudevevents-event-meta">' . 
				self::get_event_dates($event) .
				'<br />' .
				$event->get_venue_location() . 
			'</div>';
		}
		$ret .= '</div>';
		return $ret;
	}

	public static function get_admin_bookings ($status, $post) {
		global $wpdb;
		if (!current_user_can('edit_posts')) return false; // Basic sanity check
		$event = ($post instanceof Eab_EventModel) ? $post : new Eab_EventModel($post);
		
		$statuses = array(
			Eab_EventModel::BOOKING_YES => __('Attending', Eab_EventsHub::TEXT_DOMAIN), 
			Eab_EventModel::BOOKING_MAYBE => __('Maybe', Eab_EventsHub::TEXT_DOMAIN), 
			Eab_EventModel::BOOKING_NO => __('No', Eab_EventsHub::TEXT_DOMAIN)
		);
		if (!in_array($status, array_keys($statuses))) return false; // Unknown status
		$status_name = $statuses[$status];
		
		$bookings = $wpdb->get_results($wpdb->prepare("SELECT id,user_id FROM ".Eab_EventsHub::tablename(Eab_EventsHub::BOOKING_TABLE)." WHERE event_id = %d AND status = %s;", $event->get_id(), $status));
		if (!count($bookings)) return false;
		
		$content = '';		
		$content .= '<h4>'. __($status_name, Eab_EventsHub::TEXT_DOMAIN). '</h4>';
		$content .= '<ul class="eab-guest-list">';
		foreach ($bookings as $booking) {
			$user_data = get_userdata($booking->user_id);
			$content .= '<li>';
			$content .= '<div class="eab-guest">';
			$content .= '<a href="user-edit.php?user_id='.$booking->user_id .'" title="'.$user_data->display_name.'">' .
				get_avatar( $booking->user_id, 32 ) .
				'<br />' .
				$user_data->display_name .
			'</a>';
			if ($event->is_premium()) {
				if ($event->user_paid($booking->user_id)) {
				//if ($event->get_booking_paid($booking->id)) {
					$ticket_count = $event->get_booking_meta($booking->id, 'booking_ticket_count');
					$ticket_count = $ticket_count ? $ticket_count : 1;
					$payment_status = '' .
						'<span class="eab-guest-payment_info-paid">' . 
							__('Paid', Eab_EventsHub::TEXT_DOMAIN) . 
						'</span>' .
						'&nbsp;' .
						sprintf(__('(%s tickets)', Eab_EventsHub::TEXT_DOMAIN), $ticket_count) .
					''; 
				} else {
					$payment_status = '<span class="eab-guest-payment_info-not_paid">' . __('Not paid', Eab_EventsHub::TEXT_DOMAIN) . '</span>';
				}
				// Added by Hakan
				$payment_status = apply_filters('eab-event-payment_status', $payment_status, $booking->user_id ); 
				$content .= "<div class='eab-guest-payment_info'>{$payment_status}</div>";
			}
			if (in_array($status, array(Eab_EventModel::BOOKING_YES, Eab_EventModel::BOOKING_MAYBE))) {
				$content .= '<div class="eab-guest-actions"><a href="#cancel-attendance" class="eab-guest-cancel_attendance" data-eab-user_id="' . $booking->user_id . '" data-eab-event_id="' . $event->get_id() . '">' .
					__('Cancel attendance', Eab_EventsHub::TEXT_DOMAIN) .
				'</a></div>';
			}
			$content .= '<div class="eab-guest-actions"><a href="#delete-attendance" class="eab-guest-delete_attendance" data-eab-user_id="' . $booking->user_id . '" data-eab-event_id="' . $event->get_id() . '">' .
				__('Delete attendance entirely', Eab_EventsHub::TEXT_DOMAIN) .
			'</a></div>';
			$content = apply_filters('eab-event-booking_metabox_content', $content, $booking->user_id );
			$content .= '</div>'; // .eab-guest
			$content .= '</li>';
		}
		$content .= '</ul>';
		$content .= '<div class="clear"></div>';
		
		return $content;
	}

	public static function get_event_author_link ($post) {
		$event = ($post instanceof Eab_EventModel) ? $post : new Eab_EventModel($post);
		$user_id = $event->get_author();
		$url = get_the_author_meta('url', $user_id);
		$author = get_the_author_meta('display_name', $user_id);
		return $url 
			? '<a href="' . $url . '" title="' . 
				esc_attr(sprintf(__("Visit %s&#8217;s website"), $author)) . 
				'" rel="external">' . 
					$author . 
			'</a>'
			: $author
		;
	}
	
	public static function get_breadcrumbs ($post) {
		$event = ($post instanceof Eab_EventModel) ? $post : new Eab_EventModel($post);
		$start = $event->get_start_timestamp();
		$content = '';
		
		$content .= '<a href="' . self::get_root_url() . '" class="parent">' .
			__("Events", Eab_EventsHub::TEXT_DOMAIN) .
		'</a> &gt; ';
		$content .= '<a href="' . self::get_archive_url($start, false) . '" class="parent">' .
				date('Y', $start) .
		'</a> &gt; ';
		$content .= '<a href="' . self::get_archive_url($start, true) . '" class="parent">' . 
				date_i18n('F', $start) .
		'</a> &gt; ';
		$content .= '<span class="current">' . $event->get_title() . '</span>';
		
		return $content;
	}
	
	public static function get_payment_forms ($post) {
		global $blog_id, $current_user;
		$event = ($post instanceof Eab_EventModel) ? $post : new Eab_EventModel($post);
		
		$booking_id = $event->get_user_booking_id();
		$data = Eab_Options::get_instance();
		
		$content = '';
		
		$content .= $data->get_option('paypal_sandbox') 
			? '<form action="https://sandbox.paypal.com/cgi-bin/webscr" method="post">'
			: '<form action="https://www.paypal.com/cgi-bin/webscr" method="post">'
		;
		$content .= '<input type="hidden" name="business" value="' . $data->get_option('paypal_email') . '" />';
		$content .= '<input type="hidden" name="item_name" value="' . esc_attr($event->get_title()) . '" />';
		$content .= '<input type="hidden" name="item_number" value="' . $event->get_id() . '" />';
		$content .= '<input type="hidden" name="notify_url" value="' . 
			admin_url('admin-ajax.php?action=eab_paypal_ipn&blog_id=' . $blog_id . '&booking_id=' . $booking_id) .
		'" />';
		$content .= '<input type="hidden" name="amount" value="' . $event->get_price()  .'" />';
		$content .= '<input type="hidden" name="return" value="' . get_permalink($event->get_id()) . '" />';
		$content .= '<input type="hidden" name="currency_code" value="' . $data->get_option('currency') . '">';
		$content .= '<input type="hidden" name="cmd" value="_xclick" />';
		
		// Add multiple tickets
		$extra_attributes = apply_filters('eab-payment-paypal_tickets-extra_attributes', $extra_attributes, $event->get_id(), $booking_id);
		$content .= '' .// '<a href="#buy-tickets" class="eab-buy_tickets-trigger" style="display:none">' . __('Buy tickets', Eab_EventsHub::TEXT_DOMAIN) . '</a>' . 
			sprintf(
				//'<p class="eab-buy_tickets-target">' . __('I want to buy %s ticket(s)', Eab_EventsHub::TEXT_DOMAIN) . '</p>', 
				'<p>' . __('I want to buy %s ticket(s)', Eab_EventsHub::TEXT_DOMAIN) . '</p>', 
				'<input type="number" size="2" name="quantity" value="1" min="1" ' . $extra_attributes . ' />'
			)
		;
		
		$content .= '<input type="image" name="submit" border="0" src="https://www.paypal.com/en_US/i/btn/btn_paynow_SM.gif" alt="PayPal - The safer, easier way to pay online" />';
		$content .= '<img alt="" border="0" width="1" height="1" src="https://www.paypal.com/en_US/i/scr/pixel.gif" />';
		$content .= '</form>';
		// Added by Hakan
		$content = apply_filters('eab-event-payment_forms', $content, $event->get_id());
		
		return $content;
	}

	public function get_root_url () {
		global $blog_id;
		$data = Eab_Options::get_instance();
		return get_site_url($blog_id, $data->get_option('slug'));
	}

	public static function get_archive_url ($timestamp=false, $full=false) {
		global $blog_id;
		$data = Eab_Options::get_instance();
		$timestamp = $timestamp ? $timestamp : time();
		$format = $full ? 'Y/m' : 'Y';
		return get_site_url(
			$blog_id, 
			$data->get_option('slug') . '/' . date($format, $timestamp) . '/'
		);
	} 

	public static function get_archive_url_next ($timestamp=false, $full=false) {
		return self::get_archive_url($timestamp + (32*86400), $full);
	} 
	public static function get_archive_url_next_year ($timestamp=false, $full=false) {
		return self::get_archive_url($timestamp + (366*86400), $full);
	} 
	public static function get_archive_url_prev ($timestamp=false, $full=false) {
		return self::get_archive_url($timestamp - (28*86400), $full);
	} 
	public static function get_archive_url_prev_year ($timestamp=false, $full=false) {
		return self::get_archive_url($timestamp - (366*86400), $full);
	} 

	public static function get_event_link ($post) {
		$event = ($post instanceof Eab_EventModel) ? $post : new Eab_EventModel($post);
		return '<a class="wpmudevevents-event_link" href="' . get_permalink($event->get_id()) . '">' . $event->get_title() . '</a>';
	}

	public static function get_error_notice() {
		if (!isset( $_GET['eab_success_msg'] ) && !isset( $_GET['eab_error_msg'] )) return;
		
		$content = isset($_GET['eab_success_msg']) 
			? '<div id="eab-success-notice" class="message success">'. __(stripslashes($_GET['eab_success_msg']), Eab_EventsHub::TEXT_DOMAIN).'</div>'
			: ''
		;
		
		$content .= isset($_GET['eab_error_msg'])
		 	? '<div id="eab-error-notice" class="message error">'.__(stripslashes($_GET['eab_error_msg']), Eab_EventsHub::TEXT_DOMAIN).'</div>'
		 	: ''
		 ;	
		return $content;
	}
	
	public static function get_rsvp_form ($post) {
		global $current_user;
		$event = ($post instanceof Eab_EventModel) ? $post : new Eab_EventModel($post);
		    
		$content = '';
		$content .= '<div class="wpmudevevents-buttons">';
		
		if ($event->is_open()) {
			if (is_user_logged_in()) {
				$booking_id = $event->get_user_booking_id();
				$booking_status = $event->get_user_booking_status();
				$default_class = $booking_status ? 'ncurrent' : '';

				$content .= '<form action="" method="post" id="eab_booking_form">';
				$content .= '<input type="hidden" name="event_id" value="' . $event->get_id() . '" />';
				$content .= '<input type="hidden" name="user_id" value="' . $booking_id . '" />';
				$content .= '<input class="' .
					(($booking_id && $booking_status == 'no') ? 'current wpmudevevents-no-submit' : 'wpmudevevents-no-submit ' . $default_class) .
					'" type="submit" name="action_no" value="' . __('No', Eab_EventsHub::TEXT_DOMAIN) .
				'" />';
				$content .= '<input class="' . (($booking_id && $booking_status == 'maybe') ? 'current wpmudevevents-maybe-submit' : 'wpmudevevents-maybe-submit ' . $default_class) .
					'" type="submit" name="action_maybe" value="' . __('Maybe', Eab_EventsHub::TEXT_DOMAIN) . 
				'" />';
				$content .= '<input class="' . (($booking_id && $booking_status == 'yes') ? 'current wpmudevevents-yes-submit' : 'wpmudevevents-yes-submit ' . $default_class) .
					'" type="submit" name="action_yes" value="' . __('I\'m attending', Eab_EventsHub::TEXT_DOMAIN) .
				'" />';
				$content .= '</form>';
			} else {
				$content .= '<input type="hidden" name="event_id" value="' . $event->get_id() . '" />';
				$content .= '<a class="wpmudevevents-no-submit" href="' .
					wp_login_url(get_permalink($event->get_id())) .
				'&eab=n" >'.__('No', Eab_EventsHub::TEXT_DOMAIN).'</a>';
				$content .= '<a class="wpmudevevents-maybe-submit" href="' .
					wp_login_url(get_permalink($event->get_id())) .
				'&eab=m" >'.__('Maybe', Eab_EventsHub::TEXT_DOMAIN).'</a>';
				$content .= '<a class="wpmudevevents-yes-submit" href="' .
					wp_login_url(get_permalink($event->get_id())) .
				'&eab=y" >'.__('I\'m Attending', Eab_EventsHub::TEXT_DOMAIN).'</a>';
			}
		}
		
		$content .= '</div>';
		
		$content = apply_filters('eab-rsvps-rsvp_form', $content);
		
		return $content;
	}
	
	public static function get_event_details ($post) {
		$content = '';
		$data = Eab_Options::get_instance();
		$event = ($post instanceof Eab_EventModel) ? $post : new Eab_EventModel($post);
		
		$content .= '<div class="wpmudevevents-date">' . self::get_event_dates($event) . '</div>';

		if ($event->has_venue()) {
			$venue = $event->get_venue_location(Eab_EventModel::VENUE_AS_ADDRESS);
			$content .= "<div class='wpmudevevents-location'>{$venue}</div>";
		}
		if ($event->is_premium()) {
			$currency = $data->get_option('currency');
			$amount = number_format($event->get_price(), 2);
			$content .= "<div class='wpmudevevents-price'>{$currency} {$amount}</div>";
		}
		$data = apply_filters('eab-events-after_event_details', '', $event);
		if ($data) {
			$content .= '<div class="wpmudevevents-additional_details">' . $data . '</div>';
		}
		
		return $content;
	}

	public static function get_event_dates ($post) {
		$content = '';
		$event = ($post instanceof Eab_EventModel) ? $post : new Eab_EventModel($post);
		
		$start_dates = $event->get_start_dates();
		if (!$start_dates) return $content;
		foreach ($start_dates as $key => $start) {
			$start = $event->get_start_timestamp($key);
			$end = $event->get_end_timestamp($key);		
			
			$end_date_str = (date('Y-m-d', $start) != date('Y-m-d', $end)) 
				? date_i18n(get_option('date_format'), $end) : ''
			;
			
			$content .= $key ? __(' and ', Eab_EventsHub::TEXT_DOMAIN) : '';

			$start_string = $event->has_no_start_time($key)
				? sprintf(__('<span class="wpmudevevents-date_format-start">%s</span>', Eab_EventsHub::TEXT_DOMAIN), date_i18n(get_option('date_format'), $start))
				: sprintf(__('%s <span class="wpmudevevents-date_format-start"> - %s</span>', Eab_EventsHub::TEXT_DOMAIN), date_i18n(get_option('date_format'), $start), date_i18n(get_option('time_format'), $start))
			;
			$end_string = $event->has_no_end_time($key)
				? sprintf(__('<span class="wpmudevevents-date_format-end"> - %s</span><br />', Eab_EventsHub::TEXT_DOMAIN), '<span class="wpmudevevents-date_format-end_date">' . $end_date_str . '</span>')
				: sprintf(__('<span class="wpmudevevents-date_format-end"> - %s</span><br />', Eab_EventsHub::TEXT_DOMAIN), '<span class="wpmudevevents-date_format-end_date">' . $end_date_str . '</span> <span class="wpmudevevents-date_format-end_time">' . date_i18n(get_option('time_format'), $end) . '</span>')
			;
			$content .= apply_filters('eab-events-event_date_string', "{$start_string} {$end_string}", $event->get_id(), $start, $end);
			/*
			$content .= apply_filters('eab-events-event_date_string', sprintf(
				__('On %s <span class="wpmudevevents-date_format-start">from %s</span> <span class="wpmudevevents-date_format-end">to %s</span><br />', Eab_EventsHub::TEXT_DOMAIN),
				'<span class="wpmudevevents-date_format-start_date">' . date_i18n(get_option('date_format'), $start) . '</span>',
				'<span class="wpmudevevents-date_format-start_time">' . date_i18n(get_option('time_format'), $start) . '</span>',
				'<span class="wpmudevevents-date_format-end_date">' . $end_date_str . '</span> <span class="wpmudevevents-date_format-end_time">' . date_i18n(get_option('time_format'), $end) . '</span>'
			), $event->get_id(), $start, $end);
			*/
		}
		return $content;
	}

	public static function get_status_class ($post) {
		$event = ($post instanceof Eab_EventModel) ? $post : new Eab_EventModel($post);
		$status = $event->get_status();
		return sanitize_html_class($status);
	}
	
	
}