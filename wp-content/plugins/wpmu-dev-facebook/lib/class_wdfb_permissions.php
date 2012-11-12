<?php
class Wdfb_Permissions {

	//const NEW_USER = 'user_about_me,user_birthday,user_education_history,user_events,user_hometown,user_location,user_relationships,user_religion_politics,user_birthday,user_likes,email';
	const NEW_USER = 'user_about_me,user_birthday,user_events,user_location,user_birthday,user_likes,email';
	const NON_PUBLISHER = 'user_photos,create_event,rsvp_event,read_stream';
	//const PUBLISHER = 'publish_stream,create_note,manage_pages,offline_access';
	const PUBLISHER = 'publish_stream,create_note,manage_pages';
	const EXTRAS = 'user_education_history,user_hometown,user_relationships,user_religion_politics';

	private function __construct () {}

	public static function get_permissions () {
		$id = get_current_user_id();
		if (!$id) return self::get_new_user_permissions();
		if (!current_user_can('edit_theme_options')) return self::get_new_user_permissions();
		if (!current_user_can('publish_posts')) return self::get_non_publisher_permissions();
		else return self::get_publisher_permissions();
	}

	public static function get_new_user_permissions () {
		$data = Wdfb_OptionsRegistry::get_instance();
		$extra_fields = array (
			'gender',
			'hometown',
			'relationship_status',
			'significant_other',
			'political',
			'religion',
			'favorite_teams',
			'quotes',
		);
		$import = false;
		
		if (!defined('BP_VERSION') && $data->get_option('wdfb_connect', 'wordpress_registration_fields')) {
			$wp_fields = $data->get_option('wdfb_connect', 'wordpress_registration_fields');
			if (is_array($wp_fields)) foreach ($wp_fields as $map) {
				if (!isset($map['fb'])) continue;
				if (!in_array($map['fb'], $extra_fields)) continue;
				$import = true;
				break;
			}
		} else if (defined('BP_VERSION')) {
			$model = new Wdfb_Model;
			$fields = $model->get_bp_xprofile_fields();
			if (is_array($fields)) foreach ($fields as $field) {
				$fb_value = $data->get_option('wdfb_connect', 'buddypress_registration_fields_' . $field['id']);
				if (!in_array($fb_value, $extra_fields)) continue;
				$import = true;
				break;
			}
		}
		return $import ? 
			rtrim(join(',', array(
				self::EXTRAS,
				self::NEW_USER,
			)), ',')
			: 
			rtrim(self::NEW_USER, ',');
		;
	}

	public static function get_non_publisher_permissions () {
		return rtrim(join(',', array(
			self::get_new_user_permissions(),
			self::NON_PUBLISHER,
		)), ',');
	}

	public static function get_publisher_permissions () {
		return rtrim(join(',', array(
			self::get_new_user_permissions(),
			self::get_non_publisher_permissions(),
			self::PUBLISHER,
		)), ',');
	}
}