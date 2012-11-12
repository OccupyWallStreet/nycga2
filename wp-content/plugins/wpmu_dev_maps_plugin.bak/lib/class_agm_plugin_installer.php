<?php

/**
 * Handles plugin installation.
 */
class AgmPluginInstaller {
	var $wpdb;

	var $defaults = array (
		'height' => 300,
		'width' => 300,
		'map_type' => 'ROADMAP',
		'image_size' => 'small',
		'image_limit' => 10,
		'map_alignment' => 'left',
		'zoom' => 1,
		'units' => 'METRIC',
	);

	/**
	 * PHP4 compatibility constructor.
	 */
	function AgmPluginInstaller () {
		$this->__construct();
	}

	function __construct() {
		global $wpdb;
		$this->wpdb = $wpdb;
		$this->model = new AgmMapModel();
	}

	/**
	 * Entry method.
	 *
	 * Handles Plugin installation.
	 *
	 * @access public
	 * @static
	 */
	function install () {
		$me = new AgmPluginInstaller();
		if (!$me->has_database_table()) {
			$me->create_database_table();
			$me->set_default_options();
		}
	}

	/**
	 * Performs a quick check for plugin install state.
	 * Also updates plugin options as needed. This handles minor updates
	 * (i.e. no database changes).
	 *
	 * @access public
	 * @static
	 */
	function check () {
		$is_installed = get_option('agm_google_maps', false);
		if ($is_installed) return AgmPluginInstaller::check_and_update_options($is_installed);
		else AgmPluginInstaller::install();
	}

	/**
	 * Checks to see if we already have a table.
	 *
	 * @access private
	 * @return bool True if we do, false if we need to create it.
	 */
	function has_database_table () {
		$table = $this->model->get_table_name();
		return ($this->wpdb->get_var("show tables like '{$table}'") == $table);
	}

	/**
	 * Actually creates the database table.
	 *
	 * @access private
	 */
	function create_database_table () {
		$table = $this->model->get_table_name();
		$sql = "CREATE TABLE {$table} (
			id INT(10) NOT NULL AUTO_INCREMENT,
			title VARCHAR(50) NOT NULL,
			post_ids TEXT NOT NULL,
			markers TEXT NOT NULL,
			options TEXT NOT NULL,
			UNIQUE KEY id (id)
		)";
		// Setup charset and collation
		if (!empty($this->wpdb->charset)) {
			$sql .= " DEFAULT CHARACTER SET {$this->wpdb->charset}";
		}
		if (!empty($this->wpdb->collate)) {
			$sql .= " COLLATE {$this->wpdb->collate}";
		}
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}

	/**
	 * (Re)sets Plugin options to defaults.
	 *
	 * @access private
	 */
	function set_default_options () {
		update_option('agm_google_maps', $this->defaults);
	}

	/**
	 * Checks for new plugin options and adds them as needed.
	 *
	 * @access private
	 * @static
	 */
	function check_and_update_options ($opts) {
		$me = new AgmPluginInstaller;
		$res = array_merge($me->defaults, $opts);
		update_option('agm_google_maps', $res);
	}

}