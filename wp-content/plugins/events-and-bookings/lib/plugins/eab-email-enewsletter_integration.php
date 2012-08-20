<?php
/*
Plugin Name: Email: E-Newsletter integration
Description: Allows you to automatically send newsletters about your events created with e-Newsletter plugin. <br /><b>Requires <a href="http://premium.wpmudev.org/project/e-newsletter">e-Newsletter plugin</a>.</b>
Plugin URI: http://premium.wpmudev.org/project/events-and-booking
Version: 1.0
Author: Ve Bailovity (Incsub)
*/

class Eab_Email_eNewsletterIntegration {
	
	private $_newsletter;
	
	private function __construct () {}
	
	public static function serve () {
		$me = new Eab_Email_eNewsletterIntegration;
		$me->_add_hooks();
	}
	
	private function _add_hooks () {
		add_action('init', array($this, 'populate_newsletter_object'));
		add_action('admin_notices', array($this, 'show_nags'));
		
		add_filter('eab-event_meta-meta_box_registration', array($this, 'add_meta_box'));
		add_action('eab-event_meta-after_save_meta', array($this, 'save_meta'));
	}
	
	function populate_newsletter_object () {
		global $email_newsletter;
		$this->_newsletter = $email_newsletter;
	}
	
	function show_nags () {
		if (!$this->_newsletter) {
			echo '<div class="error"><p>' .
				__("You'll need <a href='http://premium.wpmudev.org/project/e-newsletter'>e-Newsletter</a> plugin installed and activated for E-Newsletter integration add-on to work", Eab_EventsHub::TEXT_DOMAIN) .
			'</p></div>';
		}
	}
	
	function add_meta_box () {
		if (!$this->_newsletter) return false;
		add_meta_box('eab-email-newsletter', __('e-Newsletter', Eab_EventsHub::TEXT_DOMAIN), array(&$this, 'create_meta_box'), 'incsub_event', 'side', 'low');	
	}
	
	function create_meta_box () {
		$newsletters = $this->_newsletter->get_newsletters();
		
		$ret = '';
		$ret .= __('When I save my event, send this newsletter:', Eab_EventsHub::TEXT_DOMAIN);
		$ret .= ' <select name="eab_event-email-enewsletter" id="eab_event-email-enewsletter">';
		$ret .= '<option value="">' . __('Do not send a newsletter', Eab_EventsHub::TEXT_DOMAIN) . '&nbsp;</option>';
		foreach ($newsletters as $news) {
			$ret .= "<option value='{$news['newsletter_id']}'>{$news['subject']}</option>";
		}
		$ret .= '</select> ';
		
		echo $ret;
	}
	
	function save_meta () {
		if (!isset($_POST['eab_event-email-enewsletter'])) return false;
		$newsletter_id = (int)$_POST['eab_event-email-enewsletter'];
		if (!$newsletter_id) return false;
		
		wp_redirect(
			admin_url('admin.php?page=newsletters&newsletter_action=send_newsletter&newsletter_id=' . $newsletter_id)
		);
		die;
	}
}
	
Eab_Email_eNewsletterIntegration:: serve();
