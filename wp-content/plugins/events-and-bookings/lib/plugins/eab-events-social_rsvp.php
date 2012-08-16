<?php
/*
Plugin Name: Social RSVPs
Description: Automatically posts RSVP status updates for your Facebook and Twitter guests.
Plugin URI: http://premium.wpmudev.org/project/events-and-booking
Version: 1.0
Author: Ve Bailovity (Incsub)
*/

/*
Detail: You will need to enable and configure Facebook/Twitter login for this add-on to work.
*/

class Eab_Events_SocialRsvps {
	
	private $_data;
	
	private function __construct () {
		$this->_data = Eab_Options::get_instance();
	}
	
	public static function serve () {
		$me = new Eab_Events_SocialRsvps;
		$me->_add_hooks();
	}
	
	private function _add_hooks () {
		add_action('admin_notices', array($this, 'show_nags'));

		add_action('eab-javascript-public_data', array($this, 'update_oauth_scope'));
		
		add_action('incsub_event_booking_yes', array($this, 'post_facebook_update'), 10, 2);
		add_action('incsub_event_booking_yes', array($this, 'post_twitter_update'), 10, 2);
	}
	
	function show_nags () {
		$msg = false;
		if (!$this->_data->get_option('accept_api_logins')) {
			$msg = __("You will need to enable and configure Facebook logins for Social RSVPs add-on to work properly", Eab_EventsHub::TEXT_DOMAIN);
		}
		if (!$this->_data->get_option('facebook-app_id')) {
			$msg = __("You will need to configure your Facebook app for Social RSVPs add-on to work properly", Eab_EventsHub::TEXT_DOMAIN);			
		}
		
		if ($msg) {
			echo '<div class="error"><p>' . $msg . '</p></div>';
		}
	}
	
	function update_oauth_scope ($public) {
		$public['fb_scope'] = 'email,publish_stream';
		return $public;
	}
	
	function post_facebook_update ($event_id, $user_id) {
		$fb = get_user_meta($user_id, '_eab_fb', true);
		if (!$fb) return false; // Can't post this
		if (!$fb['id']) return false; // No profile id
		if (!$fb['token']) return false; // No access_token
		
		$event = new Eab_EventModel(get_post($event_id));
		if ($event->get_meta('_eab-social_rsvp-facebook-' . $user_id)) return false; // Already posted
		
		$send = array(
			'caption' => sprintf("I'm going to %s!", $event->get_title()), //substr($event->get_excerpt(), 0, 999),
			'message' => $event->get_title(),
			'link' => get_permalink($event_id),
			'name' => $event->get_title(),
			'description' => $event->get_excerpt(),
			'access_token' => $fb['token'],
		);
		$resp = wp_remote_post('https://graph.facebook.com/' . $fb['id'] . '/feed', array(
			'method' => 'POST',
			'timeout' => 45,
			'redirection' => 5,
			'blocking' => true,
			'body' => $send,
			'sslverify' => false,
		));
		if (200 != $resp['response']['code']) return false;
		if (!isset($resp['body'])) return false;
		$resp = (array)@json_decode($resp['body']);
		if (!$resp) return false;
		
		$event->set_meta('_eab-social_rsvp-facebook-' . $user_id, @$resp['id']);
	}

	function post_twitter_update ($event_id, $user_id) {
		$tw = get_user_meta($user_id, '_eab_tw', true);
		if (!$tw) return false; // Can't post this
		if (!$tw['id']) return false; // No profile id
		if (!$tw['token']) return false; // No access_token
		
		//die(var_export($tw));
		$event = new Eab_EventModel(get_post($event_id));
		if ($event->get_meta('_eab-social_rsvp-twitter-' . $user_id)) return false; // Already posted
		
		if (!class_exists('TwitterOAuth')) include_once EAB_PLUGIN_DIR . 'lib/twitteroauth/twitteroauth.php';
		$twitter = new TwitterOAuth(
			$this->_data->get_option('twitter-app_id'), 
			$this->_data->get_option('twitter-app_secret'),
			$tw['token']['oauth_token'], $tw['token']['oauth_token_secret']
		);
		$send = array(
			'status' => substr(sprintf("I'm going to %s!", $event->get_title()) . ' ' . get_permalink($event_id), 0, 140),
		);
		try {
			$resp = $twitter->post('statuses/update', $send);
		} catch (Exception $e) {
			return false;
		}
		$event->set_meta('_eab-social_rsvp-twitter-' . $user_id, $resp->id);
	}
}

Eab_Events_SocialRsvps::serve();