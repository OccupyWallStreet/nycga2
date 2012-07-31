<?php
/**
 * Installs the database, if it's not already present.
 */
class Wdpv_Installer {

	/**
	 * @access public
	 * @static
	 */
	function check () {
		$is_installed = get_site_option('wdpv', false);
		$is_installed = $is_installed ? $is_installed : get_option('wdpv', false);
		if (!$is_installed) Wdpv_Installer::install();
		if (!isset($is_installed['_db_version']) || 1 > (int)@$is_installed['_db_version']) Wdpv_Installer::upgrade_to_dates();
	}

	/**
	 * @access private
	 * @static
	 */
	function install () {
		$me = new Wdpv_Installer;
		if (!$me->has_database_table()) {
			$me->create_database_table();
		}
		$me->create_default_options();
	}

	/**
	 * @access private
	 * @static
	 */
	function upgrade_to_dates () {
		$me = new Wdpv_Installer;
		if (!$me->table_has_dates()) {
			$me->alter_table_add_dates();
		}
		$opts = get_site_option('wdpv', false);
		$opts['_db_version'] = 1;
		update_site_option('wdpv', $opts);
	}

	/**
	 * @access private
	 */
	function has_database_table () {
		global $wpdb;
		$table = $wpdb->base_prefix . 'wdpv_post_votes';
		return ($wpdb->get_var("show tables like '{$table}'") == $table);
	}

	/**
	 * @access private
	 */
	function table_has_dates () {
		global $wpdb;
		$table = $wpdb->base_prefix . 'wdpv_post_votes';
		return ($wpdb->get_var("show columns from {$table} like 'date'") == 'date');
	}

	/**
	 * @access private
	 */
	function create_database_table () {
		global $wpdb;
		$table = $wpdb->base_prefix . 'wdpv_post_votes';
		$sql = "CREATE TABLE {$table} (
			id INT(10) NOT NULL AUTO_INCREMENT,
			blog_id INT(10) NOT NULL,
			site_id INT(10) NOT NULL,
			post_id INT(10) NOT NULL,
			user_id INT(10) NOT NULL,
			user_ip INT(10) NOT NULL,
			vote INT(1) NOT NULL,
			UNIQUE KEY (id)
		);";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}

	/**
	 * @access private
	 */
	function alter_table_add_dates () {
		global $wpdb;
		$table = $wpdb->base_prefix . 'wdpv_post_votes';
		$sql = "ALTER TABLE {$table}
			ADD COLUMN date DATETIME NOT NULL AFTER vote;";
		$wpdb->query($sql);
	}

	/**
	 * @access private
	 */
	function create_default_options () {
		update_site_option('wdpv', array (
			'allow_voting' => 1,
			'allow_visitor_voting' => 1,
			'use_ip_check' => 1,
			'show_login_link' => 0,
			'voting_position' => 'top',
			'front_page_voting' => 1,
			'voting_appearance' => '',
		));
	}
}