<?php
/**
 * The new contextual help API helper.
 * 
 * Example usage:
 * <code>
 * $help = new WpmuDev_ContextualHelper();
 * $help->add_page(
 *		'dashboard',
 *		array(
 *			array(
 *				'id' => 'myid',
 *				'title' => __('My title', 'textdomain'),
 *				'content' => '<p>' . __('My content lalala', 'textdomain') . '</p>',
 *			),
 *		),
 * 		'<p>My awesome sidebar!</p>',
 *		false // Don't clear existing items.
 *	);
 * $help->initialize();
 * </code>
 */

class WpmuDev_ContextualHelp {
	
	private $_pages = array();
	
	private $_auto_clear_wp_help = false;
	
	public function __construct () {}
	
	/**
	 * Add an array of tabs to a certain page.
	 * 
	 * @param string $screen_id Screen ID to which to bind the help. Can be obtained by something like <code>$screen = get_current_screen(); $screen_id = @$screen->id;</code>
	 * @param array $tabs (optional) Array of tabs to add. Each tab is an associative array, with these keys: id, title, content.
	 * @param string $sidebar (optional) HTML string to be displayed as contextual help sidebar.
	 * @param bool $clear (optional) Clear the existing contextual help content for this page before the new tabs.
	 */
	public function add_page ($screen_id, $tabs=array(), $sidebar='', $clear=false) {
		if (!is_array($tabs)) return false;
		$this->_pages[$screen_id] = array(
			'tabs' => $tabs,
			'sidebar' => $sidebar,
			'clear' => $clear,
		);
	}
	
	/**
	 * Removes a page from instance queue.
	 * 
	 * @param string $screen_id Screen ID to clear.
	 */
	public function remove_page ($screen_id) {
		@$this->_pages[$screen_id] = array();
	}
	
	/**
	 * Adds a contextual tab to be displayed on a page.
	 * 
	 * @param string $screen_id Screen ID to which to bind the tab.
	 * @param array $tab An associative array representing a tab.
	 */
	public function add_tab ($screen_id, $tab=array()) {
		if (!is_array($tab)) return false;
		$this->_pages[] = isset($this->_pages[$screen_id]) ? $this->_pages[$screen_id] : array();
		@$this->_pages[$screen_id]['tabs'][] = $tab;
	}
	
	/**
	 * Removes a tab from instance queue.
	 * 
	 * @param string $screen_id Screen ID to which to bind the tab.
	 * @param string $tab_id The value of "id" key of tab to be removed.
	 */
	public function remove_tab ($screen_id, $tab_id) {
		if (!$tab_id) return false;
		
		$tabs = @$this->_pages[$screen_id]['tabs'];
		if (!$tabs) return false;
		
		$tmp = array();
		foreach ($tabs as $tab) {
			if (@$tab['id'] == $tmp_id) continue;
			$tmp[] = $tab;
		}
		$this->_pages[$screen_id]['tabs'] = $tmp;
	}
	
	/**
	 * Set up automatic clearing of existing help 
	 * prior to adding queued help content.
	 */
	public function clear_wp_help () {
		$this->_auto_clear_wp_help = true;
	}
	
	/**
	 * Sets up queued items as contextual help.
	 */
	public function initialize () {
		$this->_add_hooks();
	}
	

	private function _add_hooks () {
		global $wp_version;
		$version = preg_replace('/-.*$/', '', $wp_version);
		
		if (version_compare($version, '3.3', '>=')) {
	 		add_action('admin_head', array($this, 'add_contextual_help'), 999);
		} else {
			add_filter('contextual_help', array($this, 'add_compatibility_contextual_help'), 1, 1);
		}
	}
	
	/**
	 * Hook hander.
	 * No need to be called manually.
	 */
	public function add_contextual_help () {
		$screen = get_current_screen();
		if (!is_object($screen)) return false;
		
		if (!isset($this->_pages['_global_'])) {
			$screen_id = @$screen->id;
			if (!isset($this->_pages[$screen_id]) || !@$this->_pages[$screen_id] || !@$this->_pages[$screen_id]['tabs']) return false;
			$info = $this->_pages[$screen_id];
		} else {
			$info = $this->_pages['_global_'];
		}
		
		$clear = (@$info['clear'] || $this->_auto_clear_wp_help);
		if ($clear) $screen->remove_help_tabs();
		
		$screen->set_help_sidebar(@$info['sidebar']);
		
		foreach ($info['tabs'] as $tab) {
			$screen->add_help_tab($tab);
		}
	}
	
	/**
	 * Compatibility layer for pre-3.3 installs.
	 */
	public function add_compatibility_contextual_help ($help) {
		if (!isset($this->_pages['_global_'])) {
			$screen = get_current_screen();
			if (!is_object($screen)) return $help;

			$screen_id = @$screen->id;
			if (!@$this->_pages[$screen_id] || !@$this->_pages[$screen_id]['tabs']) return $help;
			$info = $this->_pages[$screen_id];
		} else {
			$info = $this->_pages['_global_'];
		} 
		
		$clear = (@$info['clear'] || $this->_auto_clear_wp_help);
		if ($clear) $help = '';
		
		foreach ($info['tabs'] as $tab) {
			$help .= '<h3>' . $tab['title'] . '</h3>' . $tab['content'];
		}
		
		return $help;
	}
}
