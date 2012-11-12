<?php
/**
 * Events export hub.
 */
class Eab_ExporterFactory {

	const EXPORTER_KEY = 'eab_export';
	const DISPOSITION_KEY = 'disposition';

	public static function serve ($args) {
		if (!$args || !is_array($args)) wp_die(__('Invalid argument format.'), Eab_EventsHub::TEXT_DOMAIN);
		$args = wp_parse_args($args, array(
			self::EXPORTER_KEY => 'attendees',
			'format' => 'csv',
			'event_id' => false,
			'disposition' => Eab_Exporter::DISPOSITION_ATTACHMENT,
		));
		$class = 'Eab_Exporter_' . ucfirst(strtolower($args['format']));
		if (!class_exists($class)) wp_die(__('Invalid exporter requested.'), Eab_EventsHub::TEXT_DOMAIN);

		$me = new $class($args);
		$me->export();
	}
}


/**
 * Abstract Exporter interface.
 */
abstract class Eab_Exporter {

	const SCOPE_EVENT = 'event';
	const SCOPE_COLLECTION = 'collection';
	const SCOPE_ATTENDEES = 'attendees';

	const DISPOSITION_INLINE = 'inline';
	const DISPOSITION_ATTACHMENT = 'attachment';
	
	protected $_event_id = false;
	protected $_collection = false;

	private $_scope = false;
	private $_disposition = false;

	function __construct ($args) {
		$this->_scope = $args[Eab_ExporterFactory::EXPORTER_KEY];
		$this->_disposition = $args[Eab_ExporterFactory::DISPOSITION_KEY];
		if ((int)$args['event_id']) $this->_event_id = (int)$args['event_id'];
	}

	public function export () {
		switch ($this->_scope) {
			case self::SCOPE_EVENT:
				$this->send_headers();
				$this->export_event();
				die;
			case self::SCOPE_COLLECTION:
				$this->send_headers();
				$this->export_events_collection();
				die;
			case self::SCOPE_ATTENDEES:
				$this->send_headers();
				$this->export_attendees();
				die;
			default:
				wp_die(__('Invalid scope', Eab_EventsHub::TEXT_DOMAIN));
		}
	}

	public function send_headers () {
		$type = $this->get_mime_type();
		$name = $this->get_file_name();
		switch ($this->_scope) {
			case self::SCOPE_EVENT:
				header("Content-type: {$type}; charset=UTF-8");
				break;
			case self::SCOPE_COLLECTION:
				header("Content-type: {$type}; charset=UTF-8");
				break;
			case self::SCOPE_ATTENDEES:
				header("Content-type: {$type}; charset=UTF-8");
				break;
			default:
				header('Content-type: {$type}; charset=UTF-8');
		}
		if (self::DISPOSITION_ATTACHMENT == $this->_disposition) header("Content-disposition: attachment; filename=\"{$name}\"");
	}

	public function get_file_name () {
		$type = $this->get_file_extension();
		if (!$this->_event_id) return 'Events Collection.' . $type;
		$event = new Eab_EventModel(get_post($this->_event_id));
		return preg_replace('/[^-_a-z0-9 ]/i', '-', $event->get_title()) . '.' . $type;
	}

	abstract function get_mime_type();
	abstract function get_file_extension();
	abstract function export_event();
	abstract function export_events_collection();
	abstract function export_attendees();

	protected function _get_bookings ($status) {
		global $wpdb;
		return $wpdb->get_results($wpdb->prepare("SELECT id,user_id FROM ".Eab_EventsHub::tablename(Eab_EventsHub::BOOKING_TABLE)." WHERE event_id = %d AND status = %s;", $this->_event_id, $status));
	}

	protected function _get_statuses () {
		return array(
			Eab_EventModel::BOOKING_YES => __('Attending', Eab_EventsHub::TEXT_DOMAIN), 
			Eab_EventModel::BOOKING_MAYBE => __('Maybe', Eab_EventsHub::TEXT_DOMAIN), 
			Eab_EventModel::BOOKING_NO => __('No', Eab_EventsHub::TEXT_DOMAIN)
		);
	}
}


/**
 * CSV Exporter implementation.
 */
class Eab_Exporter_Csv extends Eab_Exporter {

	private $_delimiter = ",";

	public function get_mime_type () {
		return 'text/csv';
	}

	public function get_file_extension () {
		return 'csv';
	}

	public function export_event () {
		if (!$this->_event_id) wp_die(__('No event to export', Eab_EventsHub::TEXT_DOMAIN));
	}

	public function export_events_collection () {
		if (!$this->_event_id) wp_die(__('No event to export', Eab_EventsHub::TEXT_DOMAIN));
	}

	public function export_attendees () {
		$event = new Eab_EventModel(get_post($this->_event_id));
		if (!$this->_event_id) wp_die(__('No event to export', Eab_EventsHub::TEXT_DOMAIN));
		$event = new Eab_EventModel(get_post($this->_event_id));
		$attendees = array();

		$statuses = $this->_get_statuses();
		foreach ($statuses as $status => $title) {
			$bookings = $this->_get_bookings($status);
			foreach ($bookings as $booking) {
				$user_data = get_userdata($booking->user_id);
				$payment_status = $ticket_count = __('N/A', Eab_EventsHub::TEXT_DOMAIN);
				if (Eab_EventModel::BOOKING_NO != $status) {
					$ticket_count = $event->get_booking_meta($booking->id, 'booking_ticket_count');
					$ticket_count = $ticket_count ? $ticket_count : 1;
					if ($event->is_premium()) {
						$payment_status = $event->user_paid($booking->user_id) ? __('Yes', Eab_EventsHub::TEXT_DOMAIN) : __('No', Eab_EventsHub::TEXT_DOMAIN);
					}
				}
				$attendees[] = array(
					__('User ID', Eab_EventsHub::TEXT_DOMAIN) => $user_data->id,
					__('User Name', Eab_EventsHub::TEXT_DOMAIN) => $user_data->display_name,
					__('User Email', Eab_EventsHub::TEXT_DOMAIN) => $user_data->user_email,
					__('Attending', Eab_EventsHub::TEXT_DOMAIN) => $title,
					__('Ticket Count', Eab_EventsHub::TEXT_DOMAIN) => $ticket_count,
					__('Payment Status', Eab_EventsHub::TEXT_DOMAIN) => $payment_status,
				);
			}
		}
		$delimiter = apply_filters('eab-exporter-csv-field_delimiter', $this->_delimiter);
		$fp = fopen('php://output', 'w');
		fputcsv($fp, array_keys($attendees[0]), $delimiter);
		foreach ($attendees as $res) fputcsv($fp, $res, $delimiter);
	}

}
