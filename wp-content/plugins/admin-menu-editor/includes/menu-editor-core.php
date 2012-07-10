<?php

//Can't have two different versions of the plugin active at the same time. It would be incredibly buggy.
if (class_exists('WPMenuEditor')){
	trigger_error(
		'Another version of Admin Menu Editor is already active. Please deactivate it before activating this one.', 
		E_USER_ERROR
	);
}

//Load the "framework"
require 'shadow_plugin_framework.php';

if ( !class_exists('WPMenuEditor') ) :

class WPMenuEditor extends MenuEd_ShadowPluginFramework {

	protected $default_wp_menu = null;    //Holds the default WP menu for later use in the editor
	protected $default_wp_submenu = null; //Holds the default WP menu for later use
	private $filtered_wp_menu = null;     //The final, ready-for-display top-level menu and sub-menu.
	private $filtered_wp_submenu = null;

	protected $title_lookups = array(); //A list of page titles indexed by $item['file']. Used to
	                                    //fix the titles of moved plugin pages.
	private $custom_menu = null;        //The current custom menu with defaults merged in
	public $menu_format_version = 4;
    
    private $templates = null; //Template arrays for various menu structures. See the constructor for details.

	//Our personal copy of the request vars, without any "magic quotes".
	private $post = array();
	private $get = array();

	function init(){
		//Determine if the plugin is active network-wide (i.e. either installed in 
		//the /mu-plugins/ directory or activated "network wide" by the super admin.
		if ( $this->is_super_plugin() ){
			$this->sitewide_options = true;
		}
		
		//Set some plugin-specific options
		if ( empty($this->option_name) ){
			$this->option_name = 'ws_menu_editor';
		}
		$this->defaults = array(
			'hide_advanced_settings' => true,
			'menu_format_version' => 0,
			'display_survey_notice' => false,
		);
		$this->serialize_with_json = false; //(Don't) store the options in JSON format

		$this->settings_link = 'options-general.php?page=menu_editor';
		
		$this->magic_hooks = true;
		$this->magic_hook_priority = 99999;
		
        //Build some template arrays
        $this->templates['basic_defaults'] = array(
            'page_title' => '',
			'menu_title' => '',
			'access_level' => 'read',  
			'file' => '',          
			'css_class' => '',
			'hookname' => '',
			'icon_url' => '',
			'position' => 0,
			'separator' => false,
			'custom' => false,
			'open_in' => 'same_window', //'new_window', 'iframe' or 'same_window' (the default)
        );
        
        //Template for a basic top-level menu
        $this->templates['blank_menu'] = array(
			'page_title' => null,
			'menu_title' => null,
			'access_level' => null,  
			'file' => null,          
			'css_class' => null,
			'hookname' => null,
			'icon_url' => null,
			'position' => null,
			'separator' => null,
			'custom' => null,
			'open_in' => null, 
			'defaults' => $this->templates['basic_defaults'],
			'items' => array(),
		);
		//Template for menu items
		$this->templates['blank_item'] = array(
			'menu_title' => null,
			'access_level' => null,
			'file' => null,
			'page_title' => null,
			'position' => null,
            'custom' => null,
			'open_in' => null,
			'defaults' => $this->templates['basic_defaults'],
		 );

		//AJAXify screen options
		add_action( 'wp_ajax_ws_ame_save_screen_options', array(&$this,'ajax_save_screen_options') );

		//AJAXify hints
		add_action('wp_ajax_ws_ame_hide_hint', array($this, 'ajax_hide_hint'));

		//Make sure we have access to the original, un-mangled request data.
		//This is necessary because WordPress will stupidly apply "magic quotes"
		//to the request vars even if this PHP misfeature is disabled.
		add_action('plugins_loaded', array($this, 'capture_request_vars'));

		add_action('admin_enqueue_scripts', array($this, 'enqueue_menu_fix_script'));
	}
	
  /**
   * Activation hook
   * 
   * @return void
   */
	function activate(){
		//If we have no stored settings for this version of the plugin, try importing them
		//from other versions (i.e. the free or the Pro version).
		if ( !$this->load_options() ){
			$this->import_settings();
		}
		
		parent::activate();
	}
	
  /**
   * Import settings from a different version of the plugin.
   * 
   * @return bool True if settings were imported successfully, False otherwise
   */
	function import_settings(){
		$possible_names = array('ws_menu_editor', 'ws_menu_editor_pro');
		foreach($possible_names as $option_name){
			if ( $this->load_options($option_name) ){
				return true;
			}
		}
		
		return false;
	}

  /**
   * Add the JS required by the editor to the page header
   *
   * @return void
   */
	function enqueue_scripts(){
		//jQuery JSON plugin
		wp_enqueue_script('jquery-json', $this->plugin_dir_url.'/js/jquery.json-1.3.js', array('jquery'), '1.3');
		//jQuery sort plugin
		wp_enqueue_script('jquery-sort', $this->plugin_dir_url.'/js/jquery.sort.js', array('jquery'));
		//jQuery UI Droppable
		wp_enqueue_script('jquery-ui-droppable');
		
		//Editor's scipts
        wp_enqueue_script(
			'menu-editor', 
			$this->plugin_dir_url.'/js/menu-editor.js', 
			array('jquery', 'jquery-ui-sortable', 'jquery-ui-dialog', 'jquery-form'), 
			'1.1'
		);

		//The editor will need access to some of the plugin data and WP data.
		wp_localize_script(
			'menu-editor',
			'wsEditorData',
			array(
				'adminAjaxUrl' => admin_url('admin-ajax.php'),
				'showHints' => $this->get_hint_visibility(),
			)
		);
	}
	
  /**
   * Add the editor's CSS file to the page header
   *
   * @return void
   */
	function enqueue_styles(){
		wp_enqueue_style('menu-editor-style', $this->plugin_dir_url . '/css/menu-editor.css', array(), '20120626');
	}

  /**
   * Create a configuration page and load the custom menu
   *
   * @return void
   */
	function hook_admin_menu(){
		global $menu, $submenu;
		
		//Menu reset (for emergencies). Executed by accessing http://example.com/wp-admin/?reset_admin_menu=1 
		$reset_requested = isset($this->get['reset_admin_menu']) && $this->get['reset_admin_menu'];
		if ( $reset_requested && $this->current_user_can_edit_menu() ){
			$this->options['custom_menu'] = null;
			$this->save_options();
		}
		
		//The menu editor is only visible to users with the manage_options privilege.
		//Or, if the plugin is installed in mu-plugins, only to the site administrator(s). 
		if ( $this->current_user_can_edit_menu() ){
			$page = add_options_page(
				apply_filters('admin_menu_editor-self_page_title', 'Menu Editor'), 
				apply_filters('admin_menu_editor-self_menu_title', 'Menu Editor'), 
				'manage_options', 
				'menu_editor', 
				array(&$this, 'page_menu_editor')
			);
			//Output our JS & CSS on that page only
			add_action("admin_print_scripts-$page", array(&$this, 'enqueue_scripts'));
			add_action("admin_print_styles-$page", array(&$this, 'enqueue_styles'));
			
			//Make a placeholder for our screen options (hacky)
			add_meta_box("ws-ame-screen-options", "You should never see this", array(&$this, 'noop'), $page);
		}
		
		//WP 3.0 in multisite mode has two separators with the same filename. This plugin 
		//expects all top-level menus to have unique filenames/URLs. 
		$first_separator1 = -1;
		$last_separator1 = -1;
		foreach($menu as $index => $item){
			if ( $item[2] == 'separator1' ){
				$last_separator1 = $index;
				if ( $first_separator1 == -1 ){
					$first_separator1 = $index;
				}
			}
		}
		if ( $first_separator1 != $last_separator1 ){
			$menu[$first_separator1][2] = 'separator0';
		}
		
		//Store the "original" menus for later use in the editor
		$this->default_wp_menu = $menu;
		$this->default_wp_submenu = $submenu;
		
		//Is there a custom menu to use?
		if ( !empty($this->options['custom_menu']) ){
			//Check if we need to upgrade the menu structure
			if ( empty($this->options['menu_format_version']) || ($this->options['menu_format_version'] < $this->menu_format_version) ){
				$this->options['custom_menu'] = $this->upgrade_menu_structure($this->options['custom_menu']);
				$this->options['menu_format_version'] = $this->menu_format_version;
				$this->save_options();
			}
			//Merge in data from the default menu
			$tree = $this->menu_merge($this->options['custom_menu'], $menu, $submenu);
			//Save for later - the editor page will need it
			$this->custom_menu = $tree;
			//Apply the custom menu
			list($menu, $submenu, $this->title_lookups) = $this->tree2wp($tree);
			//Re-filter the menu (silly WP should do that itself, oh well)
			$this->filter_menu();
			$this->filtered_wp_menu = $menu;
			$this->filtered_wp_submenu = $submenu;
		}
	}

	/**
	 * Activate the 'menu_order' filter.
	 *
	 * @return bool
	 */
	function hook_custom_menu_order(){
		return true;
	}

	/**
	 * Override the order of the top-level menu entries.
	 *
	 * @param array $menu_order
	 * @return array
	 */
	function hook_menu_order($menu_order){
		if (empty($this->custom_menu)){
			return $menu_order;
		}
		$custom_menu_order = array();
		foreach($this->filtered_wp_menu as $topmenu){
			$filename = $topmenu[2];
			if ( in_array($filename, $menu_order) ){
				$custom_menu_order[] = $filename;
			}
		}
		return $custom_menu_order;
	}
	
	/**
	 * Determine if the current user may use the menu editor.
	 * 
	 * @return bool
	 */
	function current_user_can_edit_menu(){
		if ( $this->is_super_plugin() ){
			return is_super_admin();
		} else {
			return current_user_can('manage_options');
		}
	}
	
  /**
   * Intercept a handy action to fix the page title for moved plugin pages.
   *
   * @return void
   */
	function hook_admin_xml_ns(){
		global $title;
		if ( empty($title) ){
			$title = $this->get_real_page_title();
		}		
	}
	
	/**
	 * Fix the page title for move plugin pages.
	 * The 'admin_title' filter is only available in WP 3.1+
	 * 
	 * @param string $admin_title The current admin title.
	 * @param string $title The current page title. 
	 * @return string New admin title.
	 */
	function hook_admin_title($admin_title, $title){
		if ( empty($title) ){
			$admin_title = $this->get_real_page_title() . $admin_title;
		}
		return $admin_title;
	}
	
	/**
	 * Get the correct page title for a plugin page that's been moved to a different menu.
	 *  
	 * @return string
	 */
	function get_real_page_title(){
		global $title;
		global $pagenow;
		global $plugin_page;
		
		if ( empty($title) && !empty($plugin_page) && !empty($pagenow) ){
			$file = sprintf('%s?page=%s', $pagenow, $plugin_page);
			if ( isset($this->title_lookups[$file]) ){
				$title = esc_html( strip_tags( $this->title_lookups[$file] ) );
			}
		}
		
		return $title;
	}	
	

  /**
   * Loop over the Dashboard submenus and remove pages for which the current user does not have privs.
   *
   * @return void
   */
	function filter_menu(){
		global $menu, $submenu, $_wp_submenu_nopriv, $_wp_menu_nopriv;
		
		foreach ( array( 'submenu' ) as $sub_loop ) {
			foreach ($$sub_loop as $parent => $sub) {
				foreach ($sub as $index => $data) {
					if ( ! current_user_can($data[1]) ) {
						unset(${$sub_loop}[$parent][$index]);
						$_wp_submenu_nopriv[$parent][$data[2]] = true;
					}
				}

				if ( empty(${$sub_loop}[$parent]) )
					unset(${$sub_loop}[$parent]);
			}
		}
	}

  /**
   * Encode a menu tree as JSON
   *
   * @param array $tree
   * @return string
   */
	function getMenuAsJS($tree){
		return $this->json_encode($tree);
	}

  /**
   * Convert a WP menu structure to an associative array
   *
   * @param array $item An element of the $menu array
   * @param integer $pos The position (index) of the menu item
   * @return array
   */
	function menu2assoc($item, $pos=0){
		$item = array(
			'menu_title' => $item[0],
			'access_level' => $item[1],
			'file' => $item[2],
			'page_title' => $item[3],
			'css_class' => $item[4],
			'hookname' => (isset($item[5])?$item[5]:''), //ID
			'icon_url' => (isset($item[6])?$item[6]:''),
			'position' => $pos,
		);
		$item['separator'] = strpos($item['css_class'], 'wp-menu-separator') !== false;
		//Flag plugin pages
		$item['is_plugin_page'] = (get_plugin_page_hook($item['file'], '') != null);
        
		return array_merge($this->templates['basic_defaults'], $item);
	}

  /**
   * Convert a WP submenu structure to an associative array
   *
   * @param array $item An element of the $submenu array
   * @param integer $pos The position (index) of that element
   * @param string $parent Parent file that this menu item belongs to.
   * @return array
   */
	function submenu2assoc($item, $pos = 0, $parent = ''){
		$item = array(
			'menu_title' => $item[0],
			'access_level' => $item[1],
			'file' => $item[2],
			'page_title' => (isset($item[3])?$item[3]:''),
			'position' => $pos,
		 );
		//Save the default parent menu
		$item['parent'] = $parent;
		//Flag plugin pages
		$item['is_plugin_page'] = (get_plugin_page_hook($item['file'], $parent) != null);
        
		return array_merge($this->templates['basic_defaults'], $item);
	}

  /**
   * Populate lookup arrays with default values from $menu and $submenu. Used later to merge
   * a custom menu with the native WordPress menu structure somewhat gracefully.
   *
   * @param array $menu
   * @param array $submenu
   * @return array An array with two elements containing menu and submenu defaults.
   */
	function build_lookups($menu, $submenu){
		//Process the top menu
		$menu_defaults = array();
		foreach($menu as $pos => $item){
			$item = $this->menu2assoc($item, $pos);
			$menu_defaults[$item['file']] = $item; //index by filename
		}

		//Process the submenu
		$submenu_defaults = array();
		foreach($submenu as $parent => $items){
			foreach($items as $pos => $item){
				$item = $this->submenu2assoc($item, $pos, $parent);
				//File itself is not guaranteed to be unique, so we use a surrogate ID to identify submenus.
				$uid = $this->unique_submenu_id($item['file'], $parent);
				$submenu_defaults[$uid] = $item;
			}
		}

		return array($menu_defaults, $submenu_defaults);
	}

  /**
   * Merge $menu and $submenu into the $tree. Adds/replaces defaults, inserts new items
   * and marks missing items as such.
   *
   * @param array $tree A menu in plugin's internal form
   * @param array $menu WordPress menu structure
   * @param array $submenu WordPress submenu structure
   * @return array Updated menu tree
   */
	function menu_merge($tree, $menu, $submenu){
		list($menu_defaults, $submenu_defaults) = $this->build_lookups($menu, $submenu);

		//Iterate over all menus and submenus and look up default values
		foreach ($tree as &$topmenu){
			$topfile = $this->get_menu_field($topmenu, 'file');
			//Is this menu present in the default WP menu?
			if (isset($menu_defaults[$topfile])){
				//Yes, load defaults from that item
				$topmenu['defaults'] = $menu_defaults[$topfile];
				//Note that the original item was used
				$menu_defaults[$topfile]['used'] = true;
			} else {
				//Record the menu as missing, unless it's a menu separator
				if ( empty($topmenu['separator']) ){
					$topmenu['missing'] = true;
					//[Nasty] Fill the 'defaults' array for menu's that don't have it.
					//This should never be required - saving a custom menu should set the defaults
					//for all menus it contains automatically.
					if ( empty($topmenu['defaults']) ){   
						$tmp = $topmenu;
						$topmenu['defaults'] = $tmp;
					}
				}
			}

			if (isset($topmenu['items']) && is_array($topmenu['items'])) {
				//Iterate over submenu items
				foreach ($topmenu['items'] as $file => &$item){
					$uid = $this->unique_submenu_id($item, $topfile);
					
					//Is this item present in the default WP menu?
					if (isset($submenu_defaults[$uid])){
						//Yes, load defaults from that item
						$item['defaults'] = $submenu_defaults[$uid];
						$submenu_defaults[$uid]['used'] = true;
					} else {
						//Record as missing
						$item['missing'] = true;
						if ( empty($item['defaults']) ){
							$tmp = $item;
							$item['defaults'] = $tmp;
						}
					}
				}
			}
		}

		//If we don't unset these they will fuck up the next two loops where the same names are used.
		unset($topmenu);
		unset($item);

		//Note : Now we have some items marked as missing, and some items in lookup arrays
		//that are not marked as used. The missing items are handled elsewhere (e.g. tree2wp()),
		//but lets merge in the unused items now.

		//Find and merge unused toplevel menus
		foreach ($menu_defaults as $topfile => $topmenu){
			//Skip used menus and separators
			if ( !empty($topmenu['used']) || !empty($topmenu['separator'])) {
				continue;
			};

			//Found an unused item. Build the tree entry.
			$entry = $this->templates['blank_menu'];
			$entry['defaults'] = $topmenu;
			$entry['items'] = array(); //prepare a place for menu items, if any.
			//Note that this item is unused
			$entry['unused'] = true;
			//Add the new entry to the menu tree
			$tree[$topfile] = $entry;
		}
		unset($topmenu);

		//Find and merge submenu items
		foreach($submenu_defaults as $uid => $item){
			if ( !empty($item['used']) ) continue;
			//Found an unused item. Build an entry and attach it under the default toplevel menu.
			$entry = $this->templates['blank_item'];
			$entry['defaults'] = $item;
			//Note that this item is unused
			$entry['unused'] = true;

			//Check if the toplevel menu exists
			if (isset($tree[$item['parent']])) {
				//Okay, insert the item.
				$tree[$item['parent']]['items'][$item['file']] = $entry;
			} else {
				//Ooops? This should never happen. Some kind of inconsistency?
			}
		}

		//Resort the tree to ensure the found items are in the right spots
		$tree = $this->sort_menu_tree($tree);

		return $tree;
	}
	
  /**
   * Generate an ID that uniquely identifies a given submenu item. 
   *
   * @param string|array $file Menu item in question
   * @param string $parent Parent menu. Optional. If $file is an array, the function will try to get the parent value from $file['defaults'] instead.
   * @return string Unique ID
   */
	function unique_submenu_id($file, $parent = ''){
		if ( is_array($file) ){
			if ( isset($file['defaults']) && isset($file['defaults']['parent']) ){
				$parent = $file['defaults']['parent'];
			}
			$file = $this->get_menu_field($file, 'file');
		}
		
		if ( !empty($parent) ){
			return $parent . '::' . $file;
		} else {
			return $file;
		}
	}

  /**
   * Convert the WP menu structure to the internal representation. All properties set as defaults.
   *
   * @param array $menu
   * @param array $submenu
   * @return array Menu in the internal tree format.
   */
	function wp2tree($menu, $submenu){
		$tree = array();
		$separator_count = 0;
		foreach ($menu as $pos => $item){
			
			$tree_item = $this->templates['blank_menu'];
			$tree_item['defaults'] = $this->menu2assoc($item, $pos);
			$tree_item['separator'] = empty($item[2]) || empty($item[0]) || (strpos($item[4], 'wp-menu-separator') !== false);
			
			if ( empty($tree_item['defaults']['file']) ){
				$tree_item['defaults']['file'] = 'separator_'.$separator_count;
				$separator_count++;
			}
			
			//Attach submenu items
			$parent = $tree_item['defaults']['file'];
			if ( isset($submenu[$parent]) ){
				foreach($submenu[$parent] as $subitem_pos => $subitem){
					$tree_item['items'][$subitem[2]] = array_merge(
						$this->templates['blank_item'],
						array('defaults' => $this->submenu2assoc($subitem, $subitem_pos, $parent))
					);
				}				
			}
			
			$tree[$parent] = $tree_item;
		}

		$tree = $this->sort_menu_tree($tree);

		return $tree;
	}

  /**
   * Set all undefined menu fields to the default value
   *
   * @param array $item Menu item in the plugin's internal form
   * @return array
   */
	function apply_defaults($item){
		foreach($item as $key => $value){
			//Is the field set?
			if ($value === null){
				//Use default, if available
				if (isset($item['defaults']) && isset($item['defaults'][$key])){
					$item[$key] = $item['defaults'][$key];
				}
			}
		}
		return $item;
	}
	
	
  /**
   * Apply custom menu filters to an item of the custom menu.
   *
   * Calls two types of filters :
   * 	'custom_admin_$item_type' with the entire $item passed as the argument. 
   * 	'custom_admin_$item_type-$field' with the value of a single field of $item as the argument.
   *	
   * Used when converting the current custom menu to a WP-format menu. 
   *
   * @param array $item Associative array representing one menu item (either top-level or submenu).
   * @param string $item_type 'menu' or 'submenu'
   * @param mixed $extra Optional extra data to pass to hooks.
   * @return array Filtered menu item.
   */
	function apply_menu_filters($item, $item_type = '', $extra = null){
		if ( empty($item_type) ){
			//Only top-level menus have an icon
			$item_type = isset($item['icon_url'])?'menu':'submenu';
		}
		
		$item = apply_filters("custom_admin_{$item_type}", $item, $extra);
		foreach($item as $field => $value){
			$item[$field] = apply_filters("custom_admin_{$item_type}-$field", $value, $extra);
		}
		
		return $item;
	}
	
  /**
   * Get the value of a menu/submenu field.
   * Will return the corresponding value from the 'defaults' entry of $item if the 
   * specified field is not set in the item itself.
   *
   * @param array $item
   * @param string $field_name
   * @param mixed $default Returned if the requested field is not set and is not listed in $item['defaults']. Defaults to null.  
   * @return mixed Field value.
   */
	function get_menu_field($item, $field_name, $default = null){
		if ( isset($item[$field_name]) && ($item[$field_name] !== null) ){
			return $item[$field_name];
		} else {
			if ( isset($item['defaults']) && isset($item['defaults'][$field_name]) ){
				return $item['defaults'][$field_name];
			} else {
				return $default;
			}
		}
	}

  /**
   * Custom comparison function that compares menu items based on their position in the menu.
   *
   * @param array $a
   * @param array $b
   * @return int
   */
	function compare_position($a, $b){
		if ($a['position']!==null) {
			$p1 = $a['position'];
		} else {
			if ( isset($a['defaults']['position']) ){
				$p1 = $a['defaults']['position'];
			} else {
				$p1 = 0;
			}
		}

		if ($b['position']!==null) {
			$p2 = $b['position'];
		} else {
			if ( isset($b['defaults']['position']) ){
				$p2 = $b['defaults']['position'];
			} else {
				$p2 = 0;
			}
		}

		return $p1 - $p2;
	}
	
  /**
   * Sort the menus and menu items of a given menu according to their positions 
   *
   * @param array $tree A menu structure in the internal format
   * @return array Sorted menu in the internal format
   */
	function sort_menu_tree($tree){
		//Resort the tree to ensure the found items are in the right spots
		uasort($tree, array(&$this, 'compare_position'));
		//Resort all submenus as well
		foreach ($tree as &$topmenu){
			if (!empty($topmenu['items'])){
				uasort($topmenu['items'], array(&$this, 'compare_position'));
			}
		}
		
		return $tree;
	}

  /**
   * Convert internal menu representation to the form used by WP.
   * 
   * Note : While this function doesn't cause any side effects of its own, 
   * it executes several filters that may modify global state. Specifically,
   * IFrame-handling callbacks in 'extras.php' may insert items into the 
   * global $menu and $submenu arrays.
   *
   * @param array $tree
   * @return array $menu and $submenu
   */
	function tree2wp($tree){
		$menu = array();
		$submenu = array();
		$title_lookup = array();
		
		//Sort the menu by position
		uasort($tree, array(&$this, 'compare_position'));

		//Prepare the top menu
		$first_nonseparator_found = false;
		foreach ($tree as $topmenu){
			
			//Skip missing menus, unless they're user-created and thus might point to a non-standard file
			$custom = $this->get_menu_field($topmenu, 'custom', false); 
			if ( !empty($topmenu['missing']) && !$custom ) {
				continue;
			};
			
			//Skip leading menu separators. Fixes a superfluous separator showing up
			//in WP 3.0 (multisite mode) when there's a custom menu and the current user
			//can't access its first item ("Super Admin").
			if ( !empty($topmenu['separator']) && !$first_nonseparator_found ) continue;
			
			$first_nonseparator_found = true;
			
			//Apply defaults & filters
			$topmenu = $this->apply_defaults($topmenu);
			$topmenu = $this->apply_menu_filters($topmenu, 'menu');
			
			//Skip hidden entries
			if (!empty($topmenu['hidden'])) continue;
			
			//Build the menu structure that WP expects
			$menu[] = array(
					$topmenu['menu_title'],
					$topmenu['access_level'],
					$topmenu['file'],
					$topmenu['page_title'],
					$topmenu['css_class'],
					$topmenu['hookname'], //ID
					$topmenu['icon_url']
				);
				
			//Prepare the submenu of this menu
			if( !empty($topmenu['items']) ){
				$items = $topmenu['items'];
				//Sort by position
				uasort($items, array(&$this, 'compare_position'));
				
				foreach ($items as $item) {
					
					//Skip missing items, unless they're user-created
					$custom = $this->get_menu_field($item, 'custom', false);
					if ( !empty($item['missing']) && !$custom ) continue;
					
					//Special case : plugin pages that have been moved to a different menu.
					//If the file field hasn't already been modified, we'll need to adjust it
					//to point to the old parent. This is required because WP identifies 
					//plugin pages using *both* the plugin file and the parent file.
					if ( $this->get_menu_field($item, 'is_plugin_page', false) && ($item['file'] === null) ){
						$default_parent = '';
						if ( isset($item['defaults']) && isset($item['defaults']['parent'])){
							$default_parent = $item['defaults']['parent'];
						}
						if ( $topmenu['file'] != $default_parent ){
							$item['file'] = $default_parent . '?page=' . $item['defaults']['file'];
						}
					}
					
					$item = $this->apply_defaults($item);
					$item = $this->apply_menu_filters($item, 'submenu', $topmenu['file']);
					
					//Skip hidden items
					if (!empty($item['hidden'])) {
						continue;
					}
					
					$submenu[$topmenu['file']][] = array(
						$item['menu_title'],
						$item['access_level'],
						$item['file'],
						$item['page_title'],
					);
					 
					//Make a note of the page's correct title so we can fix it later
					//if necessary.
					$title_lookup[$item['file']] = $item['menu_title']; 
				}
			}
		}
		return array($menu, $submenu, $title_lookup);
	}
	
  /**
   * Upgrade a menu tree to the currently used structure
   * Does nothing if the menu is already up to date.
   *
   * @param array $tree
   * @return array
   */
	function upgrade_menu_structure($tree){
		
		//Append new fields, if any
		foreach($tree as &$menu){
			$menu = array_merge($this->templates['blank_menu'], $menu);
            $menu['defaults'] = array_merge($this->templates['basic_defaults'], $menu['defaults']);
            
			foreach($menu['items'] as $item_file => $item){
				$item = array_merge($this->templates['blank_item'], $item);
                $item['defaults'] = array_merge($this->templates['basic_defaults'], $item['defaults']);
				$menu['items'][$item_file] = $item;
			}
		}
		
		return $tree;
	}
	
  /**
   * Output the menu editor page
   *
   * @return void
   */
	function page_menu_editor(){
		global $menu, $submenu;
		global $wp_roles;
		
		if ( !$this->current_user_can_edit_menu() ){
			die("Access denied");
		}
		
		$action = isset($this->post['action']) ? $this->post['action'] : (isset($this->get['action']) ? $this->get['action'] : '');
		do_action('admin_menu_editor_header', $action);
		
		//Handle form submissions
		if (isset($this->post['data'])){
			check_admin_referer('menu-editor-form');

			//Try to decode a menu tree encoded as JSON
			$data = $this->json_decode($this->post['data'], true);
			if (!$data || (count($data) < 2) ){
				$fixed = stripslashes($this->post['data']);
				$data = $this->json_decode( $fixed, true );
			}

			$url = remove_query_arg('noheader');
			if ($data){
				//Ensure the user doesn't change the required capability to something they themselves don't have.
				if ( isset($data['options-general.php']['items']['menu_editor']) ){
					$item = $data['options-general.php']['items']['menu_editor'];
					if ( !empty($item['access_level']) && !current_user_can($item['access_level']) ){
						$item['access_level'] = null;
						$data['options-general.php']['items']['menu_editor'] = $item;
					}
				}

				//Save the custom menu
				$this->options['custom_menu'] = $data;
				$this->save_options();
				//Redirect back to the editor and display the success message
				wp_redirect( add_query_arg('message', 1, $url) );
			} else {
				//Or redirect & display the error message
				wp_redirect( add_query_arg('message', 2, $url) );
			}
			die();
		}

		//Kindly remind the user to give me money
		if ( !apply_filters('admin_menu_editor_is_pro', false) ){
			$this->print_upgrade_notice();
		}
		
		//Handle the survey notice
		if ( isset($this->get['hide_survey_notice']) && !empty($this->get['hide_survey_notice']) ) {
			$this->options['display_survey_notice'] = false;
			$this->save_options();
		}
				
		if ( $this->options['display_survey_notice'] ) {
			$survey_url = 'https://docs.google.com/spreadsheet/viewform?formkey=dDVLOFM4V0JodUVTbWdUMkJtb2ZtZGc6MQ';
			$hide_url = add_query_arg('hide_survey_notice', 1);
			printf(
				'<div class="updated">
					<p><strong>Help improve this plugin - take the Admin Menu Editor user survey!</strong></p>
					<p><a href="%s" target="_blank" title="Opens in a new window">Take the survey</a></p>
					<p><a href="%s">Hide this notice</a></p>
				</div>',
				esc_attr($survey_url),
				esc_attr($hide_url)
			);
		}
?>
<div class="wrap">
<h2>
	<?php echo apply_filters('admin_menu_editor-self_page_title', 'Menu Editor'); ?>
</h2>

<?php
	
	if ( !empty($this->get['message']) ){
		if ( intval($this->get['message']) == 1 ){
			echo '<div id="message" class="updated fade"><p><strong>Settings saved.</strong></p></div>';
		} elseif ( intval($this->get['message']) == 2 ) {
			echo '<div id="message" class="error"><p><strong>Failed to decode input! The menu wasn\'t modified.</strong></p></div>';
		}
	}
	
	//Build a tree struct. for the default menu
	$default_menu = $this->wp2tree($this->default_wp_menu, $this->default_wp_submenu);
	
	//Is there a custom menu?
	if (!empty($this->custom_menu)){
		$custom_menu = $this->custom_menu;
	} else {
		//Start out with the default menu if there is no user-created one
		$custom_menu = $default_menu;
	}
	
	//Encode both menus as JSON
	$default_menu_js = $this->getMenuAsJS($default_menu);
	$custom_menu_js = $this->getMenuAsJS($custom_menu);

	$plugin_url = $this->plugin_dir_url;
	$images_url = $this->plugin_dir_url . '/images';
	
	//Create a list of all known capabilities and roles. Used for the dropdown list on the access field.
	$all_capabilities = $this->get_all_capabilities();
	//"level_X" capabilities are deprecated so we don't want people using them.
	//This would look better with array_filter() and an anonymous function  as a callback.
	for($level = 0; $level <= 10; $level++){
		$cap = 'level_' . $level;
		if ( isset($all_capabilities[$cap]) ){
			unset($all_capabilities[$cap]);
		}
	}
	$all_capabilities = array_keys($all_capabilities);
	natcasesort($all_capabilities);
	
	$all_roles = $this->get_all_roles();
	//Multi-site installs also get the virtual "Super Admin" role
	if ( is_multisite() ){
		$all_roles['super_admin'] = 'Super Admin';
	}
	asort($all_roles);
?>
<div id='ws_menu_editor'>
	<div class='ws_main_container'>
		<div class='ws_toolbar'>
			<div class="ws_button_container">		
				<a id='ws_cut_menu' class='ws_button' href='javascript:void(0)' title='Cut'><img src='<?php echo $images_url; ?>/cut.png' alt="Cut" /></a>
				<a id='ws_copy_menu' class='ws_button' href='javascript:void(0)' title='Copy'><img src='<?php echo $images_url; ?>/page_white_copy.png' alt="Copy" /></a>
				<a id='ws_paste_menu' class='ws_button' href='javascript:void(0)' title='Paste'><img src='<?php echo $images_url; ?>/page_white_paste.png' alt="Paste" /></a>
				
				<div class="ws_separator">&nbsp;</div>
				
				<a id='ws_new_menu' class='ws_button' href='javascript:void(0)' title='New menu'><img src='<?php echo $images_url; ?>/page_white_add.png' alt="New menu" /></a>
				<a id='ws_hide_menu' class='ws_button' href='javascript:void(0)' title='Show/Hide'><img src='<?php echo $images_url; ?>/plugin_disabled.png' alt="Show/Hide" /></a>
				<a id='ws_delete_menu' class='ws_button' href='javascript:void(0)' title='Delete menu'><img src='<?php echo $images_url; ?>/page_white_delete.png' alt="Delete menu" /></a>
				
				<div class="ws_separator">&nbsp;</div>
				
				<a id='ws_new_separator' class='ws_button' href='javascript:void(0)' title='New separator'><img src='<?php echo $images_url; ?>/separator_add.png' alt="New separator" /></a>
			</div>
		</div>
		
		<div id='ws_menu_box' class="ws_box">
		</div> 
	</div>
	<div class='ws_main_container'>
		<div class='ws_toolbar'>
			<div class="ws_button_container">
				<a id='ws_cut_item' class='ws_button' href='javascript:void(0)' title='Cut'><img src='<?php echo $images_url; ?>/cut.png' alt="Cut" /></a>
				<a id='ws_copy_item' class='ws_button' href='javascript:void(0)' title='Copy'><img src='<?php echo $images_url; ?>/page_white_copy.png' alt="Copy" /></a>
				<a id='ws_paste_item' class='ws_button' href='javascript:void(0)' title='Paste'><img src='<?php echo $images_url; ?>/page_white_paste.png' alt="Paste" /></a>
				
				<div class="ws_separator">&nbsp;</div>
				
				<a id='ws_new_item' class='ws_button' href='javascript:void(0)' title='New menu item'><img src='<?php echo $images_url; ?>/page_white_add.png' alt="New menu item" /></a>
				<a id='ws_hide_item' class='ws_button' href='javascript:void(0)' title='Show/Hide'><img src='<?php echo $images_url; ?>/plugin_disabled.png' alt="Show/Hide" /></a>
				<a id='ws_delete_item' class='ws_button' href='javascript:void(0)' title='Delete menu item'><img src='<?php echo $images_url; ?>/page_white_delete.png' alt="Delete menu item" /></a>
				
				<div class="ws_separator">&nbsp;</div>
				
				<a id='ws_sort_ascending' class='ws_button' href='javascript:void(0)' title='Sort ascending'>
					<img src='<?php echo $images_url; ?>/sort_ascending.png' alt="Sort ascending" />
				</a>
				<a id='ws_sort_descending' class='ws_button' href='javascript:void(0)' title='Sort descending'>
					<img src='<?php echo $images_url; ?>/sort_descending.png' alt="Sort descending" />
				</a>
			</div>
		</div>
		
		<div id='ws_submenu_box' class="ws_box">
		</div>
	</div>
</div>

<div class="ws_main_container" id="ws_editor_sidebar">
<form method="post" action="<?php echo admin_url('options-general.php?page=menu_editor&noheader=1'); ?>" id='ws_main_form' name='ws_main_form'>
	<?php wp_nonce_field('menu-editor-form'); ?>
	<input type="hidden" name="data" id="ws_data" value="">
	<input type="button" id='ws_save_menu' class="button-primary ws_main_button" value="Save Changes" />
</form>
	
	<input type="button" id='ws_reset_menu' value="Undo changes" class="button ws_main_button" />
	<input type="button" id='ws_load_menu' value="Load default menu" class="button ws_main_button" />
	
	<?php
		do_action('admin_menu_editor_sidebar');
	?>
</div>

	<?php
	$show_hints = $this->get_hint_visibility();
	$hint_id = 'ws_sidebar_pro_ad';
	$show_pro_benefits = !apply_filters('admin_menu_editor_is_pro', false) && (!isset($show_hints[$hint_id]) || $show_hints[$hint_id]);
	if ( $show_pro_benefits ):
	?>
		<div class="clear"></div>

		<div class="ws_hint" id="<?php echo esc_attr($hint_id); ?>">
			<div class="ws_hint_close" title="Close">x</div>
			<div class="ws_hint_content">
				<strong>Upgrade to Pro:</strong>
				<ul>
					<li>Menu export & import.</li>
					<li>Per-role menu permissions.</li>
					<li>Drag items between menu levels.</li>
				</ul>
				<a href="http://w-shadow.com/admin-menu-editor-pro/upgrade-to-pro/?utm_source=Admin%2BMenu%2BEditor%2Bfree&utm_medium=text_link&utm_content=sidebar_link&utm_campaign=Plugins" target="_blank">Learn more</a>
			</div>
		</div>
	<?php
	endif;
	?>

</div>

<?php
	//Create a pop-up capability selector
	$capSelector = array('<select id="ws_cap_selector" class="ws_dropdown" size="10">');
	
	$capSelector[] = '<optgroup label="Roles">';
 	foreach($all_roles as $role_id => $role_name){
 		$capSelector[] = sprintf(
		 	'<option value="%s">%s</option>',
		 	esc_attr($role_id),
		 	$role_name
	 	);
 	}
 	$capSelector[] = '</optgroup>';
	
 	$capSelector[] = '<optgroup label="Capabilities">';
 	foreach($all_capabilities as $cap){
 		$capSelector[] = sprintf(
		 	'<option value="%s">%s</option>',
		 	esc_attr($cap),
		 	$cap
	 	);
 	}
 	$capSelector[] = '</optgroup>';
 	$capSelector[] = '</select>';
 	
 	echo implode("\n", $capSelector);

 	//Create a pop-up page selector
 	$pageSelector = array('<select id="ws_page_selector" class="ws_dropdown" size="10">');
 	foreach($default_menu as $toplevel){
 		if ( $toplevel['separator'] ) continue;
 		
 		$top_title = strip_tags( preg_replace('@<span[^>]*>.*</span>@i', '', $this->get_menu_field($toplevel, 'menu_title')) );
 		
 		if ( empty($toplevel['items'])) {
 			//This menu has no items, so it can only link to itself
 			$pageSelector[] = sprintf(
			 	'<option value="%s">%s -&gt; %s</option>',
			 	esc_attr($this->get_menu_field($toplevel, 'file')),
			 	$top_title,
			 	$top_title
		 	);
		} else {
			//When a menu has some items, it's own URL is ignored by WP and the first item is used instead.
		 	foreach($toplevel['items'] as $subitem){
		 		$sub_title = strip_tags( preg_replace('@<span[^>]*>.*</span>@i', '', $this->get_menu_field($subitem, 'menu_title')) );
		 		
		 		$pageSelector[] = sprintf(
				 	'<option value="%s">%s -&gt; %s</option>',
				 	esc_attr($this->get_menu_field($subitem, 'file')),
				 	$top_title,
				 	$sub_title		 	
			 	);
		 	}
	 	}
 	}
 	
 	$pageSelector[] = '</select>';
 	echo implode("\n", $pageSelector);
?>

<span id="ws-ame-screen-meta-contents" style="display:none;">
<label for="ws-hide-advanced-settings">
	<input type="checkbox" id="ws-hide-advanced-settings"<?php 
		if ( $this->options['hide_advanced_settings'] ){
			echo ' checked="checked"';
		}
	?> /> Hide advanced options
</label>
</span>

<script type='text/javascript'>

var defaultMenu = <?php echo $default_menu_js; ?>;
var customMenu = <?php echo $custom_menu_js; ?>;

var imagesUrl = "<?php echo esc_js($images_url); ?>";

var adminAjaxUrl = "<?php echo esc_js(admin_url('admin-ajax.php')); ?>";

var hideAdvancedSettings = <?php echo $this->options['hide_advanced_settings']?'true':'false'; ?>;
var hideAdvancedSettingsNonce = '<?php echo esc_js(wp_create_nonce('ws_ame_save_screen_options'));  ?>';

var captionShowAdvanced = 'Show advanced options';
var captionHideAdvanced = 'Hide advanced options';

window.wsMenuEditorPro = false; //Will be overwritten if extras are loaded

</script>

		<?php
		
		//Let the Pro version script output it's extra HTML & scripts.
		do_action('admin_menu_editor_footer');
	}
	
  /**
   * Retrieve a list of all known capabilities of all roles
   *
   * @return array Associative array with capability names as keys
   */
	function get_all_capabilities(){
		/** @var WP_Roles $wp_roles */
		global $wp_roles;
		
		$capabilities = array();
		
		if ( !isset($wp_roles) || !isset($wp_roles->roles) ){
			return $capabilities;
		}
		
		//Iterate over all known roles and collect their capabilities
		foreach($wp_roles->roles as $role){
			if ( !empty($role['capabilities']) && is_array($role['capabilities']) ){ //Being defensive here
				$capabilities = array_merge($capabilities, $role['capabilities']);
			}
		}
		
		//Add multisite-specific capabilities (not listed in any roles in WP 3.0)
		$multisite_caps = array(
			'manage_sites' => 1,  
			'manage_network' => 1, 
			'manage_network_users' => 1, 
			'manage_network_themes' => 1, 
			'manage_network_options' => 1, 
			'manage_network_plugins' => 1, 
		);
		$capabilities = array_merge($capabilities, $multisite_caps);
		
		return $capabilities;
	}
	
  /**
   * Retrieve a list of all known roles
   *
   * @return array Associative array with role IDs as keys and role display names as values
   */
	function get_all_roles(){
		/** @var WP_Roles $wp_roles */
		global $wp_roles;
		$roles = array();
		
		if ( !isset($wp_roles) || !isset($wp_roles->roles) ){
			return $roles;
		}
		
		foreach($wp_roles->roles as $role_id => $role){
			$roles[$role_id] = $role['name'];
		}
		
		return $roles;
	}

	/**
	 * Create a virtual 'super_admin' capability that only super admins have.
	 * This function accomplishes that by by filtering 'user_has_cap' calls.
	 * 
	 * @param array $allcaps All capabilities belonging to the current user, cap => true/false.
	 * @param array $required_caps The required capabilities.
	 * @param array $args The capability passed to current_user_can, the current user's ID, and other args.
	 * @return array Filtered version of $allcaps
	 */
	function hook_user_has_cap($allcaps, $required_caps, $args){
		//Be careful not to overwrite a super_admin cap added by other plugins 
		//For example, Advanced Access Manager also adds this capability. 
		if ( in_array('super_admin', $required_caps) && !isset($allcaps['super_admin']) ){
			$allcaps['super_admin'] = is_multisite() && is_super_admin($args[1]);
		}
		return $allcaps;
	}

  /**
   * Output the "Upgrade to Pro" message
   *
   * @return void
   */
	function print_upgrade_notice(){
		?>
		<script type="text/javascript">
		(function($){
			$('#screen-meta-links').append(
				'<div id="ws-pro-version-notice">' +
					'<a href="http://w-shadow.com/admin-menu-editor-pro/?utm_source=Admin%2BMenu%2BEditor%2Bfree&utm_medium=text_link&utm_content=top_upgrade_link&utm_campaign=Plugins" id="ws-pro-version-notice-link" class="show-settings" target="_blank" title="View Pro version details">Upgrade to Pro</a>' +
				'</div>'
			);
		})(jQuery);
		</script>
		<?php
	}
	
	/**
	 * AJAX callback for saving screen options (whether to show or to hide advanced menu options).
	 * 
	 * Handles the 'ws_ame_save_screen_options' action. The new option value 
	 * is read from $_POST['hide_advanced_settings'].
	 * 
	 * @return void
	 */
	function ajax_save_screen_options(){
		if (!current_user_can('manage_options') || !check_ajax_referer('ws_ame_save_screen_options', false, false)){
			die( $this->json_encode( array(
				'error' => "You're not allowed to do that!" 
			 )));
		}
		
		$this->options['hide_advanced_settings'] = !empty($this->post['hide_advanced_settings']);
		$this->save_options();
		die('1');
	}

	public function ajax_hide_hint() {
		if ( !isset($this->post['hint']) || !$this->current_user_can_edit_menu() ){
			die("You're not allowed to do that!");
		}

		$show_hints = $this->get_hint_visibility();
		$show_hints[strval($this->post['hint'])] = false;
		$this->set_hint_visibility($show_hints);

		die("OK");
	}

	private function get_hint_visibility() {
		$user = wp_get_current_user();
		$show_hints = get_user_meta($user->ID, 'ame_show_hints', true);
		if ( !is_array($show_hints) ) {
			$show_hints = array();
		}

        $defaults = array(
            'ws_sidebar_pro_ad' => true,
            //'ws_whats_new_120' => true, //Set upon activation, default not needed.
            'ws_hint_menu_permissions' => true,
        );

		return array_merge($defaults, $show_hints);
	}

	private function set_hint_visibility($show_hints) {
		$user = wp_get_current_user();
		update_user_meta($user->ID, 'ame_show_hints', $show_hints);
	}

	/**
	 * Enqueue a script that fixes a bug where pages moved to a different menu
	 * would not be highlighted properly when the user visits them.
	 */
	public function enqueue_menu_fix_script() {
		wp_enqueue_script(
			'ame-menu-fix',
			$this->plugin_dir_url . '/js/menu-highlight-fix.js',
			array('jquery'),
			'20120519',
			true
		);
	}
	
	/**
	 * A callback for the stub meta box added to the plugin's page. Does nothing.  
	 * 
	 * @return void
	 */
	function noop(){
		//nihil
	}

	/**
	 * Capture $_GET and $_POST in $this->get and $this->post.
	 * Slashes added by "magic quotes" will be stripped.
	 *
	 * @return void
	 */
	function capture_request_vars(){
		$this->post = $_POST;
		$this->get = $_GET;

		if ( function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc() ) {
			$this->post = stripslashes_deep($this->post);
			$this->get = stripslashes_deep($this->get);
		}
	}

} //class

endif;

?>