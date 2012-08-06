<?php
/*
 Plugin Name: CSS & JavaScript Toolbox
 Plugin URI: http://wipeoutmedia.com/wordpress-plugins/css-javascript-toolbox
 Description: WordPress plugin to easily add custom CSS and JavaScript to individual pages
 Version: 0.8
 Author: Wipeout Media 
 Author URI: http://wipeoutmedia.com/wordpress-plugins/css-javascript-toolbox

 Copyright (c) 2011, Wipeout Media.

 This program is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License
 as published by the Free Software Foundation; either version 2
 of the License, or (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */


/**
* Avoid direct calls to this file where wp core files not present
*/
if (!function_exists ('add_action')) {
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
}

/**
* CJT info.
*/
define('CJTOOLBOX_VERSION', '0.3');
define('CJTOOLBOX_NAME', plugin_basename(dirname(__FILE__)));
define('CJTOOLBOX_TEXT_DOMAIN', CJTOOLBOX_NAME);
define('CJTOOLBOX_DEBUG', FALSE);

/**
* CJT Paths.
*/
define('CJTOOLBOX_PATH', dirname(__FILE__));
define('CJTOOLBOX_INCLUDE_PATH', CJTOOLBOX_PATH . '/includes');
define('CJTOOLBOX_VIEWS_PATH', CJTOOLBOX_PATH . '/views');
define('CJTOOLBOX_VIEWS_SNIPPETS_PATH', CJTOOLBOX_PATH . '/views/snippets');

/**
* CJT URLs.
*/
define('CJTOOLBOX_URL', WP_PLUGIN_URL . '/' . CJTOOLBOX_NAME );
define('CJTOOLBOX_MEDIA_URL', CJTOOLBOX_URL . '/public/media');
define('CJTOOLBOX_CSS_URL', CJTOOLBOX_URL . '/public/css');
define('CJTOOLBOX_JS_URL', CJTOOLBOX_URL . '/public/js');

/**
* Wordpress option name Prefix for modules list cache.
* 
* The directory name will be added to the option name,
* so each modules directory has different cached list.
*/
define('MODULES_LIST_CACHE_VAR_PREFIX', 'cjt_modules_list');

if (!class_exists('cssJSToolbox')) {
	/**
	* CJT class.
	*/
	class cssJSToolbox {
		/**
		* WOrdpress option name.
		*/
		const BLOCKS_OPTION_NAME = 'cjtoolbox_data';
		
		/**
		* URL to check for premium update.
		*/
		const CHECK_UPDATE_URL = 'http://wipeoutmedia.com/wp-admin/admin-ajax.php?action=cjts_dispatcher&procedure=Updates.getLatestPremiumVersion';
		
		/**
		* Version option name.
		*/
		const DATABASE_VERSION_OPTION_NAME = 'cjtoolbox_db_version';
		
		/**
		* Dir for uploaded images.
		*/
		const IMAGES_UPLOAD_DIR = 'upload';
		
		/**
		* Additional scripts directory name.
		*/
		const ADDITIONAL_SCRIPTS_DIR = 'upload';
		
		/**
		* Blocks used to output the code for
		* the current request.
		* 
		* @see cssJSToolbox::setTargetBlocks.
		* 
		* @var array|null
		*/
		var $blocks = null;
		
		/**
		* Security nonce used in the blocks page.
		* 
		* @var string
		*/
		var $security_nonce = null;
		
		/**
		* CJT options.
		* 
		* @var array
		*/
		var $settings = array();
		
		/**
		* CSS?JS Blocks data.
		* 
		* @var array
		*/
		var $cjdata = array();

		/**
		* Note: self::$instance is used in all HOOKS callbacks instead of $this.
		* This allow other modules to override the methods of this class.
		* 
		* Allow only single instance.
		* 
		* @var mixed
		*/
		public static $instance = null;
		
		/**
		* Modules engine object.
		* 
		* @var CJTModulesEngine
		*/
		public static $modulesEngine = null;
		
		/**
		* Premium upgrade response trasient.
		* 
		* @var array
		*/
		public static $premiumUpgradeTransient = null;
		
		/**
		* Initialize Plugin. 
		* 
		* @return void
		*/
		protected function __construct() {
			// Set hooks pointer.
			self::$instance = $this;
			// Process/Load attached modules.
			if (is_admin()) {
				$this->processSDModules();
			}
			// Start this plugin once all other plugins are fully loaded.
			add_action('plugins_loaded', array(&self::$instance, 'start_plugin'));
			// Activation & Deactivbation.
			register_activation_hook(__FILE__, array(&self::$instance, 'activate_plugin'));
			register_deactivation_hook(__FILE__, array(&self::$instance, 'deactivate_plugin'));
		}
		
		/**
		* Clean up single block data before saving to database.
		* 
		* @param array Block data.
		* @return array Cleaned block data.
		*/
		protected function cleanSingleBlock($block) {
			$fieldsToClean = array(
				'code',
				'links',
			);
			// New lines submitted to server as CRLF but displayed in browser as LF.
			// PHP script and JS work on two different versions of texts.
			// Replace CRLF with LF just as displayed in browsers.
			foreach ($fieldsToClean as $field) {
				$block[$field] = preg_replace("/\x0D\x0A/", "\x0A", $block[$field]);
			}
			return $block;
		}
		
		/**
		* Get CJT Plugin object.
		* 
		* @return cssJSToolbox.
		*/
		public static function getInstance() {
			if (!self::$instance) {
				$instance = new cssJSToolbox();
			}
			return self::$instance;
		}
		
		/**
		* Process CJT Self delete modules only if there is modules
		* available.
		* 
		* Note: Modules can deleted them self after a while.
		* 
		* The main concern is to avoid 
		* CJTModulesEngine or any other modules 
		* is resposible for setting cjt_process_modules value.
		* 
		* @return void.
		*/
		private function processSDModules() {
			$modulesDirectory = 'modules';
			$modulesListOptionName = MODULES_LIST_CACHE_VAR_PREFIX . "-{$modulesDirectory}";
			$processModules = get_option($modulesListOptionName);
			// IF processmodules is not array it means that this is the first time to run after
			// the plugin installed and no list cached list, so give the modules engine the chance to collect the data.
			// IF processModules is array but empty it means that modules deleted themself
			// and no more modules to process.
		  if (!is_array($processModules) || (is_array($processModules) && (!empty($processModules)))) {
		  	// Process/Load modules.
		    require_once CJTOOLBOX_INCLUDE_PATH . '/modules.inc.php';
		    require_once CJTOOLBOX_INCLUDE_PATH . '/modulebase.inc.php';
		    self::$modulesEngine = CJTModulesEngine::getInstance($modulesDirectory);
		    self::$modulesEngine->processAll();
			}
		}

		/**
		* Save blocks data to database.
		* 
		* @param array Save blocks parameters if provided.
		* @return void
		*/
		function saveData($blocks = null) {
			$blocks = isset($blocks) ? $blocks : $this->cjdata;
			update_option(self::BLOCKS_OPTION_NAME, $blocks);
		}
		
		/**
		* Read blocks data from the database.
		* 
		* @return array Blocks data array.
		*/
		function getData() {
			$cjdata = (array) get_option(self::BLOCKS_OPTION_NAME);
			$this->cjdata = apply_filters('cjt_blocks_data', $cjdata);
			// This is a Database Recovery condition.
			// This is not for a well-known bug. All cases has been studied.
			// If under any circumstances the cjdata is empty instead of having
			// a broken Plugin we just recovery by returning a block object.
			// Also this will fix previous version broken Plugins.
			if (empty($this->cjdata)) {
				$this->cjdata[] = array(
					'block_name' => 'Default',
					'location' => 'header',
					'page' => array(),
					'category' => array(),
					'links' => '',
					'scripts' => '',
					'meta' => array(),
				);
			}
			return $this->cjdata;
		}

		/**
		* Check for premium update.
		* 
		* 
		* @return void
		*/
		public function checkPremiumUpdate() {
			// Import Premium Update cron hook.
			require_once 'premium-update-check.php';
			CJTPremiumUpdate::check();
		}
		
		/**
		* Bind Wordpress hooks.
		* 
		* Callback for plugins_loaded.
		*/
		function start_plugin() {
			if (is_admin()) {
				// New installation or check for upgrade.
				// Plugin activation hook is not fired when the Plugin updated since Wordpress 3.1.
				// No worries the code inside will not executed twice.
				$this->checkInstallation();
				// Load Plugin translation.
				load_plugin_textdomain(CJTOOLBOX_TEXT_DOMAIN, null, 'css-javascript-toolbox/langs');
				// Load for admin panel
				add_action('admin_menu', array(&self::$instance, 'add_plugin_menu'));
				// register ajax save function
				add_action('wp_ajax_cjtoolbox_save', array(&self::$instance, 'ajax_save_changes'));
				add_action('wp_ajax_cjtoolbox_save_newcode', array(&self::$instance, 'ajax_save_newcode'));
				add_action('wp_ajax_cjtoolbox_form', array(&self::$instance, 'ajax_show_form'));
				add_action('wp_ajax_cjtoolbox_get_code', array(&self::$instance, 'ajax_get_code'));
				add_action('wp_ajax_cjtoolbox_delete_code', array(&self::$instance, 'ajax_delete_code'));
				add_action('wp_ajax_cjtoolbox_add_block', array(&self::$instance, 'ajax_add_block'));
	      add_action('wp_ajax_cjtoolbox_request_template', array(&self::$instance, 'ajax_request_template'));
				// Get latest update data.
				self::$premiumUpgradeTransient = get_site_transient('cjt_premium_upgrade');
			}
			else {
				// Add the script and style files to header/footer
				add_action('wp_head', array(&self::$instance, 'cjtoolbox_insert_header_code'));
				add_action('wp_print_scripts', array(&self::$instance, 'cjtoolbox_embedded_scripts'), 11);
	      add_action('wp_footer', array(&self::$instance, 'cjtoolbox_insert_footer_code'));
				// Premium update check cron hook.
				add_action('cjt_premium_update_checker', array(&self::$instance, 'checkPremiumUpdate'));
			}
		}

		/**
		* Output css/js codes for header.
		* 
		* Callback for wp_head.
		*/
	  function cjtoolbox_insert_header_code() {
	    $this->insertcode('wp_head');
	  }
	  
		/**
		* Output css/js codes for footer.
		* 
		* Callback for wp_footer.
		*/
	  function cjtoolbox_insert_footer_code() {
	    $this->insertcode('wp_footer');
	  }
	  
	  /**
	  * Enqueue embedded scripts.
	  * Callback for wp_enquque_scripts
	  */
	  public function cjtoolbox_embedded_scripts() {
	  	global $wp_scripts;
	  	if (!is_admin() && $wp_scripts) { // wp_enqueue_scripts used by backend too!!!
	  		// Register additional script that shipped with the Plugin.
	  		$this->registerScripts($wp_scripts);
	  		// This is the first hook in out chan (wp_head, wp_footer, wp_enqueue_scripts).
	  		// We'll use this hook to set target blocks.
	  		$this->setTargetBlocks();
	  		// We've to hooklocations wp_head and wp_footer.
	  		foreach ($this->blocks as $hookLocation => $blocks) {
	  		  foreach ($blocks as $key => $block) {
	  	  		// Get block scripts handlers.
	  	  		$scriptsStrList = $this->getScriptsList($block);
	  	  		$scripts = explode(',', $scriptsStrList);
	  	  		if (!empty($scripts) && ($scripts[0] != '')) {
	  	  			foreach ($scripts as $script) {
	  	  				// If previously enqueued, dequeue and then enquque again.
	  	  				// We'll use the latest block hook location.
	  	  				$wp_scripts->dequeue($script);
	  	  				$isFooter = ($hookLocation == 'wp_footer') ? true : false;
	  	  				wp_enqueue_script($script, null, null, null, $isFooter);
							} // End output scripts.	  	  	
						}
					} // End blocks.
				} // Enc hooks.	  	
			}
		}
		
	  /**
	  * Output code for a specific location.
	  * 
	  * @param string Blocks Hook/Location to output.
	  * @return void
	  */
		function insertcode($hook) {
			// Make sure there is at least one block for the hook.
			if (isset($this->blocks[$hook])){
				// Get blocks code.
				foreach($this->blocks[$hook] as $blockId => $block) {
					echo $block['code'] . "\n";
				}
			}
		}

		/**
		* Set blocks array that should used to output the 
		* css/js codes for the current request.
		* 
		* @return vod
		*/
		protected function setTargetBlocks() {
			global $post;
			// Reset blocks.
			$this->blocks = array();
			// Home page displays a page.
			$check_for = '';
			if (is_front_page()) {
				$check_for = 'frontpage';
			}
			else if (is_single() || is_home()) { // The blog page. It will be either same as front page or will be a page.
				$check_for = 'allposts';
			}
			else if (is_page()) {
				$check_for = $post->ID;
			}

			$this->getData();
			$data = $this->cjdata;
			foreach($data as $key => $block) :
				// Backward compatibility.
				// Catogriez blocks by hook location.
				$hookLocation = $this->getHookLocation($block);
				$page_list = $data[$key]['page'];
				if (is_array($page_list)) {
					if (is_page() && in_array('allpages', $page_list)) {
						$this->blocks[$hookLocation][$key] = $block;
						continue;
					}
					else if(in_array($check_for, $page_list)) {
						$this->blocks[$hookLocation][$key] = $block;
						continue;
					}
				}
				if (is_category()) {
					$this_category = get_query_var('cat');
					$category_list = $data[$key]['category'];
					if (is_array($category_list)) {
						if(in_array($this_category, $category_list)) {
							$this->blocks[$hookLocation][$key] = $block;
							continue;
						}
					}
				}

				$pageURL = 'http';
				if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
				$pageURL .= "://";
				if ($_SERVER["SERVER_PORT"] != "80") {
				$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
				} else {
				$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
				}
				$links = $data[$key]['links'];
				$link_list = explode("\n", $links);
				if (in_array($pageURL, $link_list)) {
					$this->blocks[$hookLocation][$key] = $block;
					continue;
				}
			endforeach;
		}
		
		/**
		* Register additional scripts shipped out with the Plugin.
		* 
		* @param WP_SCRIPTS
		* @return void
		*/
		protected function registerScripts(&$wp_scripts) {
			// Scripts shipped with Plugin.
			$scripts = array(
				'jquery-cycle-all' => array(
					'file' => 'jquery.cycle.all.min.js',
					'ver' => '2.65',
					'dep' => array('jquery'),
					),
				'jquery-easing' => array(
					'file' => 'jquery.easing.js',
					'ver' => '1.3',
					'dep' => array('jquery'),
					),
			);
			// Register scripts.
			foreach ($scripts as $handle => $script) {
				$additionalJSDir = CJTOOLBOX_JS_URL . '/' . self::ADDITIONAL_SCRIPTS_DIR;
				$source = "{$additionalJSDir}/{$script['file']}";
				$wp_scripts->add($handle, $source, $script['dep'], $script['ver']);
			}
		}
		
		/**
		* Add CJT admin page.
		* 
		* Callback for (admin_menu)
		*/
		function add_plugin_menu() {
			$this->hook_manage = add_options_page('CSS & JavaScript Toolbox' ,'CSS & JavaScript Toolbox', '10', 'cjtoolbox', array(&self::$instance, 'admin_display'));
			// register callback to show styles needed for the admin page
			add_action('admin_print_styles-' . $this->hook_manage, array(&self::$instance, 'admin_print_styles'));
			// Load scripts for admin panel working
			add_action('admin_print_scripts-' . $this->hook_manage, array(&self::$instance, 'admin_print_scripts'));
		}

		/**
		* Enqueue admin styles.
		* 
		* Callback for admin_print_styles-[$hook_manage].
		*/
		function admin_print_styles() {
			wp_enqueue_style('thickbox');
			wp_enqueue_style('cjtoolbox', CJTOOLBOX_CSS_URL . '/admin.css', '', CJTOOLBOX_VERSION, 'all');
			wp_enqueue_style('jquery');
		}

		/**
		* Enqueue admin scripts.
		* 
		* Callback for admin_print_scripts-[$hook_manage].
		*/
		function admin_print_scripts() {
			wp_enqueue_script('jquery');
			wp_enqueue_script('common');
			wp_enqueue_script('wp-lists');
			wp_enqueue_script('postbox');
			wp_enqueue_script('thickbox');
			wp_enqueue_script('jquery-ui-tabs');
	    wp_enqueue_script('md5', CJTOOLBOX_JS_URL . '/md5-min.js'); // Md5 used to create from data hashes.
	    wp_enqueue_script('cjt-contenthash', CJTOOLBOX_JS_URL . '/contenthash.js');
	    // Admin Javascript with localization.
	    wp_enqueue_script('cjt-admin', CJTOOLBOX_JS_URL  . '/admin.js');
	    $localization = array(
    		'addBlockFailed' => __('Oops, unable to add CSS & JavaScript Block! Please try again!!!'),
    		'UnableToReadCode' => __('Oops, unable to fetch selected {type} template! Please try again!!!'),
    		'confirmDeleteTemplate' => __('Are you sure? Selected template will be deleted permanently!!!'),
    		'cantDeleteTemplate' => __('Oops, unable to delete selected {type} template! Please try again!!!'),
    		'templateDeleted' => __('Selected {type} template deleted successfully!'),
    		'confirmDeleteBlock' => __('Are you sure you want to delete "{block_name}" block?') . "\n\n" . __('The block is not permanently deleted unless "Save Changes" button is clicked'),
    		'titleFieldMissing' => __('Please enter title for code!'),
    		'codeFieldMissing' => __('Please enter code to save!'),
    		'noChangeMadeCouldNotSaveTemplate' => __('Code template was not saved because there were no changes made.') . "\n\n" . __('Do you wish to finish editing anyway?'),
    		'couldNotSaveTemplate' => __('Could not save template, please try again.'),
    		'templateSavedSuccessful' => __('"{title}" {type} code template has been saved successfully.'),
    		'blockNameMissing' => __('Block name cannot be null, please type a name.'),
    		'blockNameIsInUse' => __('Block name is in use. There is another block with the same name!!!') . "\n\n" . __('Please select another name.'),
	    );
	    wp_localize_script('cjt-admin', 'localization', $localization);
	  }

		/**
		* Blocks management page.
		* 
		* Callback for menu page
		*/
		function admin_display() {
			// Import Wordpress Menu Navigation for displaying posts/pages/categories.
			require CJTOOLBOX_INCLUDE_PATH . '/wpnavmenuwalker.inc.php';
			// Load blocks data from database.
			$this->getData();
			// The idea behind making the blocks sortable is stop 
			// reseting the array ids.
			// To avoid block id duplication we need to do not ever use the same Id again.
			$existsIds = array_keys($this->cjdata);
			$count = max($existsIds) + 1;
			// Prepare blocks for display.
			foreach ($this->cjdata as $i => $block) {
				$blockName = $this->getBlockName($block, $i);
				add_meta_box('cjtoolbox-' . ($i + 1), sprintf(__('CSS & JavaScript Block: %s', CJTOOLBOX_TEXT_DOMAIN), $blockName), array(&$this, 'cjtoolbox_unit'), $this->hook_manage, 'normal', 'core', $i);
			}
			do_action('cjt_admin_display_start', $this->cjdata);
			// Output the admin management page.
			require CJTOOLBOX_VIEWS_PATH . '/manage.html';
			do_action('cjt_admin_display_end', $this->cjdata);
		}
		
		/**
		* Get block hook location header/footer.
		* 
		* Backward compatibility for older version that doesn't support hook location.
		* 
		* @param array Block data.
		* @param string Value to use if the hook location is not available.
		* @return string Hook location name.
		*/
		protected function getHookLocation($block, $default = 'wp_head') {
		  return isset($block['location']) ? $block['location'] : $default;
		}
		
		/**
		* Get block scripts list.
		* 
		* Backward compatibility for older version that doesn't support embedded scripting.
		* 
		* @param array Block data.
		* @return string
		*/
		protected function getScriptsList($block, $default = '') {
			// For old version that doesn't support embedded scripts.
			$scripts = isset($block['scripts']) ? $block['scripts'] : $default;
			return $scripts;
		}
		
		/**
		* Get block name.
		* 
		* Backward compatibility for older version that doesn't support block names.
		* 
		* @param array Block data.
		* @param integer Block id.
		* @return string Block name.
		*/
		protected function getBlockName($block, $blockId, $oldVersionPrefix = '') {
	    $oldNameStyle = ($blockId + 1);
	    if (isset($block['block_name'])) {
	    	$blockName =  $block['block_name'];
			}
			else {
				$blockName = "{$oldVersionPrefix}{$oldNameStyle}";
			}
			return $blockName;
		}
		
		/**
		* Represent single block markup constructor.
		* 
		* @param null Not used.
		* @param integer Block Id.
		* @param boolean Indicate whether the request is Ajax request.
		* @return void
		*/
		function cjtoolbox_unit($data = '', $arg = '', $ajax = false) {
			$boxid = -1; // Because block 1 might have some content...
			if ($arg != '') {
				$boxid = $arg['args'];
			}
			// E_ALL complain.
			// We don't want to use $this->cjdata[$boxid] when the $this->cjdata[$boxid] is not set.
			// Because the previous version do that (views/snippets/block.tmpl) we need to cover.
			if ($ajax) {
				// This won't saved to the database.
				$this->cjdata[$boxid] = array(
					'block_name' => ($boxid + 1),
					'location' => 'header',
					'code' => '',
					'page' => array(),
					'category' => array(),
					'links' => '',
					'scripts' => '',
					'meta' => array(),
				);
			}
	    $currentBlock = $this->cjdata[$boxid];
	    $blocksCount = count($this->cjdata);
	    $blockName = $this->getBlockName($currentBlock, $boxid);
	    require CJTOOLBOX_VIEWS_SNIPPETS_PATH . '/block.tmpl';
		}

		/**
		* Get taxanomy terms checkboxes selection list.
		* 
		* @param string List Id.
		* @param array Selected terms list.
		*/
		function show_taxonomy_with_checkbox($boxid, $taxonomy_selected) {
			$taxonomy_name = 'category';
		  $args = array(
    		  'child_of' => 0,
		      'exclude' => '',
    		  'hide_empty' => false,
		      'hierarchical' => 1,
    		  'include' => '',
		      'include_last_update_time' => false,
    		  'number' => 9999,
    		  'order' => 'ASC',
		      'orderby' => 'name',
    		  'pad_counts' => false,
	    );
		  $terms = get_terms($taxonomy_name, $args);
	    if (!$terms || is_wp_error($terms)) {
			// No items
		      return;
	    }
		  $db_fields = false;
		  if (is_taxonomy_hierarchical($taxonomy_name)) {
    		  $db_fields = array( 'parent' => 'parent', 'id' => 'term_id' );
		  }
			$walker = new cj_Walker_Nav_Menu_Checklist($db_fields, $boxid, 'category', $taxonomy_selected);
			$args['walker'] = $walker;
			echo walk_nav_menu_tree(array_map('wp_setup_nav_menu_item', $terms), 0, (object) $args);
		}
		
		/**
		* Get pages terms checkboxes selection list.
		* 
		* @param string List Id.
		* @param array Selected pages list.
		*/
		function show_pages_with_checkbox($boxid, $pages_selected) {
			$post_type_name = 'page';
			$args = array(
				'order' => 'ASC',
				'orderby' => 'title',
				'posts_per_page' => 9999,
				'post_type' => $post_type_name,
				'suppress_filters' => true,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false
			);
			// @todo transient caching of these results with proper invalidation on updating of a post of this type
			$get_posts = new WP_Query;
			$posts = $get_posts->query($args);
			if (!$get_posts->post_count) {
				// No items
				return;
			}
			$db_fields = false;
			if (is_post_type_hierarchical($post_type_name)) {
				$db_fields = array( 'parent' => 'post_parent', 'id' => 'ID' );
			}
			$walker = new cj_Walker_Nav_Menu_Checklist($db_fields, $boxid, 'page', $pages_selected);
			$post_type_object = get_post_type_object($post_type_name);
			$args['walker'] = $walker;
			$checkbox_items = walk_nav_menu_tree(array_map('wp_setup_nav_menu_item', $posts), 0, (object) $args);
			echo $checkbox_items;
		}
		
		/**
		* Get add new block dialog.
		* 
		* @return void
		*/
		function ajax_add_block() {
			check_ajax_referer('cjtoolbox-admin', 'security');
			// We need the security nonce for the new block.
			$this->security_nonce = $_REQUEST['security'];
			// Import Wordpress Menu Navigation for displaying posts/pages/categories.
			require CJTOOLBOX_INCLUDE_PATH . '/wpnavmenuwalker.inc.php';
			
			$args = array();
			// Load blocks from database.
			$this->getData();		
			$count = (int) $_POST['count'];
			$args['args'] = $count;
			require CJTOOLBOX_VIEWS_SNIPPETS_PATH . '/newblock.html.tmpl';
			die();
		}
	  
	  /**
	  * Request forms templates(e.g edit block name popup, etc..)
	  * 
	  * @param string Views path. Useful for modules to utilize from the method.
	  * @param array Params to make visible to the template file.
	  * @return void
	  */
	  function ajax_request_template($viewsPath = CJTOOLBOX_VIEWS_SNIPPETS_PATH, $param = array()) {
  		check_ajax_referer('cjtoolbox-admin', 'security');
	    $name = $_GET['name'];
	    if (preg_match('/[a-z\_\-]+/', $name)) {
	    	// Make parameters visible to the template.
	    	extract($param);
	    	// Include the file.
    		$templateName = "{$name}.html.tmpl";
    		$pathToTemplate = "{$viewsPath}/{$templateName}";
	      require $pathToTemplate;
	    }
	    die();
	  }
	  
	  /**
	  * Save new code template to database.
	  * 
	  * @return void
	  */
		function ajax_save_newcode() {
			check_ajax_referer('cjtoolbox-popup', 'security');
			// Add new row to cjdata table
			$type = $_POST['type'];
			$title = $_POST['title'];
			// Get RAW data indpendent from magic_quote_gpc, let Wordpress $wpdb->update/insert do the rest.
			$code = filter_input(INPUT_POST, 'code', FILTER_UNSAFE_RAW);
			$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
			$response = $this->add_cjdata($id, $type, $title, $code);
			die($response);
		}

	  /**
	  * Save blocks data.
	  * 
	  * @return void
	  */
		function ajax_save_changes() {
			check_ajax_referer('cjtoolbox-admin', 'security');
			$response = array();
			if($_POST['action'] == 'cjtoolbox_save') {
				// Save data and return 1 on success.
				// Get RAW data indpendent from magic_quote_gpc, let Wordpress $wpdb->update/insert do the escaping.
				$blocks = filter_input(INPUT_POST, 'cjtoolbox', FILTER_UNSAFE_RAW, FILTER_REQUIRE_ARRAY);
				$blocks = apply_filters('cjt_save_data', $blocks);
				// Take a copy from the first block.
				$firstBlock = each($blocks);
				foreach($blocks as $id => $block) {
					if ($block['code'] == '') {
						// Don't store blocks with empty code.
						unset($blocks[$id]);
					}
					else {
						// Clean up block data.
						// Prepare for storing.
						$blocks[$id] = $this->cleanSingleBlock($block);
					}
				}
				// Because we may get all the blocks with empty code and
				// we need to maintain at least one block.
				// If all blocks is empty take the first one.
				if (empty($blocks)) {
					$blocks[$firstBlock['key']] = $firstBlock['value'];
				}
				$this->cjdata = $blocks;
				$this->saveData();
				do_action('cjt_data_saved', $blocks);
				$response['savedIds'] = array_keys($blocks);
				$response['availableCount'] = count($blocks);
			}
			die(json_encode($response)); // Our Response.
		}

		/**
		* Delete selected code template.
		* 
		* @return void
		*/
		function ajax_delete_code() {
			check_ajax_referer('cjtoolbox-admin', 'security');
			$type = $_POST['type'];
			$id = (int) $_POST['id'];
			if ($id <=0 || ($type != 'js' && $type != 'css')) {
			  return __('Invalid Request: Unable to process the request!', CJTOOLBOX_TEXT_DOMAIN);
			}
			$this->delete_cjdata($type, $id);
			die('1');
		}

		/**
		* Get code for a specific template.
		* 
		* @return void
		*/
		function ajax_get_code() {
			check_ajax_referer('cjtoolbox-admin', 'security');
			$type = $_POST['type'];
			$id = (int) $_POST['id'];
			if($id <=0 || ($type != 'js' && $type != 'css')) {
				return __('Invalid Request: Unable to process the request!', CJTOOLBOX_TEXT_DOMAIN);
			}
			$code = $this->get_cjdata($type, $id);
			die($code);
		}

		/**
		* New code popup constructor.
		* 
		* @return void
		*/
		function ajax_show_form() {
			global $wpdb;
			check_ajax_referer('cjtoolbox-admin', 'security');
			$type = '';
			switch($_GET['type']) {
				case 'js':
					$type = 'js';
					break;
				case 'css':
				default:
					$type = 'css';
					break;
			}
			$editId = (int) $_GET['id'];
			if ($editId) {
				$query = "SELECT * 
									FROM {$wpdb->prefix}cjtoolbox_cjdata 
									WHERE id = %d";
				$query = $wpdb->prepare($query, $editId);
				$template = $wpdb->get_row($query, ARRAY_A);
			}
			else {
				// Dummy object for filling the form.
				$template = array(
					'type' => $type,
					'title' => '',
					'code' => '',
				);
			}
			require CJTOOLBOX_VIEWS_SNIPPETS_PATH . "/newcode.html.tmpl";
			die();
		}

		/**
		* Get code template selection list.
		* 
		* @param string Type of template. It could be 'css' or 'js';
		* @param string Unique identified for the block list.
		* @return void
		*/
		function show_dropdown_box($type, $boxid) {
			global $wpdb;
			$query = $wpdb->prepare("SELECT id, title FROM {$wpdb->prefix}cjtoolbox_cjdata WHERE type = '{$type}'");
			$list = $wpdb->get_results($query);
			if(count($list)) {
				echo '<select id="cjtoolbox-'.$type.'-'.$boxid.'" class="cjtoolbox-'.$type.'">';
				foreach($list as $def) {
					echo '<option value="' . $def->id . '">'. $def->title . '</option>';
				}
				echo '</select>';
			}
		}

		/**
		* Add a new code template to database.
		* 
		* @param string Template type.
		* @param string Template title.
		* @param string Template content.
		* @return integer|null Template id when success or null if faild.
		*/
		function add_cjdata($id, $type, $title, $code) {
			global $wpdb;
			$result = array('operation' => '', 'id' => '', 'code' => '');
			// Validate.
			if($type == '' || $title == '' || $code == '') {
			  return false;
			}
			// Update exists record.
			if ($id) {
				$data = array(
					'title' => $title,
					'code' => $code,
				);
				$filter = array(
					'id' => $id,
					'type' => $type,
				);
				$result['id'] = $id;
				$result['operation'] = 'update';
				$result['responseCode'] = $wpdb->update("{$wpdb->prefix}cjtoolbox_cjdata", $data, $filter);
			}
			else {
				$query = $wpdb->prepare("INSERT INTO {$wpdb->prefix}cjtoolbox_cjdata (type,title,code) VALUES ('%s', '%s', '%s')", $type, $title, $code);
				$wpdb->query($query);
				// Get inserted id
				$result['id'] = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}cjtoolbox_cjdata ORDER BY id DESC LIMIT 0,1");		
				$result['operation'] = 'insert';
				$result['responseCode'] = $result['id'];
			}
			return json_encode($result);
		}

		/**
		* Delete template.
		* 
		* @param string Template type css/js.
		* @param integer Id of the template.
		* @return true.
		*/
		function delete_cjdata($type, $id) {
			global $wpdb;
			if($type == '' || $id <= 0) return false;
			$query = $wpdb->prepare("DELETE FROM {$wpdb->prefix}cjtoolbox_cjdata WHERE type = '%s' AND id = '%d' LIMIT 1", $type, $id);
			$wpdb->query($query);
			return true;
		}

		/**
		* Get code for a specific template.
		* 
		* @param string Template type.
		* @param integer Template Id.
		* @return string|null
		*/
		function get_cjdata($type, $id) {
			global $wpdb;
			if($type == '' || $id <= 0) return false;
			$query = $wpdb->prepare("SELECT code FROM {$wpdb->prefix}cjtoolbox_cjdata WHERE type = '%s' AND id = '%d' LIMIT 1", $type, $id);
			$code = $wpdb->get_var($query);
			return $code;
		}

		/**
		* Install/Upgrade CJT Plugin.
		* 
		* return void.
		*/
		function checkInstallation() {
			$installed_db = get_option(self::DATABASE_VERSION_OPTION_NAME);
			if (!$installed_db) { // New installation.
				do_action('cjt_install');
				$this->install();
				add_option(self::DATABASE_VERSION_OPTION_NAME, CJTOOLBOX_VERSION);
				do_action('cjt_installed');
			}
			else if(version_compare(CJTOOLBOX_VERSION, $installed_db) == 1) { // Upgrade version 0.2.
				do_action('cjt_upgrade', $installed_db);
				$this->upgrade();
				update_option(self::DATABASE_VERSION_OPTION_NAME, CJTOOLBOX_VERSION);
				do_action('cjt_upgraded', $installed_db);
			}
		}
		
		/**
		* Activate the Plugin
		* 
		* Callback for register_Activation_hook.
		*/
		public function activate_plugin() {
		  // Schedule Premium Check Update event. 
		  wp_schedule_event(time() + 60, "daily", 'cjt_premium_update_checker');
		}
		
		/**
		* Call back for register_deactivation_hook.
		*/
		public function deactivate_plugin() {
			// Clear previously scheduled event (@see activate_plugin).
			wp_clear_scheduled_hook('cjt_premium_update_checker');
		}
		
		/**
		* Install the Plugin.
		* 
		* @return void
		*/
		public function install() {
			global $wpdb;
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			// Create the table structure
			$sql = "CREATE TABLE `{$wpdb->prefix}cjtoolbox_cjdata` (
					`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
					`type` VARCHAR( 15 ) NOT NULL ,
					`title` TINYTEXT NOT NULL ,
					`code` MEDIUMTEXT NOT NULL ,
					PRIMARY KEY ( `id` , `type` )
					)";
			dbDelta($sql);

			// Add sample code
			$count = $wpdb->get_var("SELECT count(*) FROM {$wpdb->prefix}cjtoolbox_cjdata WHERE type='css'");
			if($count == 0) {
				$wpdb->query("INSERT INTO {$wpdb->prefix}cjtoolbox_cjdata (type,title,code) VALUES ('css','Inline CSS Declaration','<style type=\"text/css\">\n\n</style>')");
				$wpdb->query("INSERT INTO {$wpdb->prefix}cjtoolbox_cjdata (type,title,code) VALUES ('css','External Stylesheet','<link rel=\"stylesheet\" type=\"text/css\" href=\"\"/>')");
			}

			$count = $wpdb->get_var("SELECT count(*) FROM {$wpdb->prefix}cjtoolbox_cjdata WHERE type='js'");
			if($count == 0) {
				$wpdb->query("INSERT INTO {$wpdb->prefix}cjtoolbox_cjdata (type,title,code) VALUES ('js','Inline JavaScript Declaration','<script type=\"text/javascript\">\n\n</script>')");
				$wpdb->query("INSERT INTO {$wpdb->prefix}cjtoolbox_cjdata (type,title,code) VALUES ('js','External JavaScript','<script type=\"text/javascript\" src=\"\"></script>')");
				$wpdb->query("INSERT INTO {$wpdb->prefix}cjtoolbox_cjdata (type,title,code) VALUES ('js','jQuery Code Wrapper','<script type=\"text/javascript\">\n(function(\$) {\n\n\t//PUT YOUR CODE HERE...\n\n})(jQuery);\n</script>')");
			}
			// Add default block.
			$sampleCode  = '<!-- ' . __('CSS & JAVASCRIPT TOOLBOX - INSTRUCTIONS AND DEMO') . " -->\n";
			$sampleCode .= '<!-- ' . __('Feel free to delete all of this text at any time.  For more information, please click \'Hints & Tips\'') . " -->\n\n";
			$sampleCode .= '<!-- ' . __('Write your CSS and JS code here, then apply it by using one of the tabs (Pages, Categories, URL) from the panel on the right') . " -->\n";
			$sampleCode .= '<!-- ' . __('The example JavaScript code shown below will display an alert message box') . " -->\n";
			$sampleCode .= '<!-- ' . __('To see this code in action, lets click the "Front Page" checkbox from the panel on the right') . " -->\n";
			$sampleCode .= '<!-- ' . __('Click the blue \'Save All Changes\' button, then click the Front Page navigation icon to open the page in a new window') . " -->\n";
			$sampleCode .= '<!-- ' . __('Have fun!!!') . " -->\n\n";
			$sampleCode .= "<script type='text/javascript'>\n\talert(\"Thank you for installing CSS & JavaScript Toolbox.\\nIf you find this plugin useful, please let us know at www.WipeoutMedia.com\");\n</script>\n";
			
			$defaultBlock = array(
				'block_name' => 'Default',
				'location' => 'header',
				'code' => $sampleCode,
				'page' => array(),
				'category' => array(),
				'links' => '',
				'scripts' => '',
				'meta' => array(),
			);
	    $this->cjdata = array($defaultBlock);
	    $this->saveData();
		}

		/**
		* Upgrade the plugin from the last version.
		* 
		* @return void
		*/
		public function upgrade() {
			// Add meta array for all blocks.
			$blocks = (array) get_option(self::BLOCKS_OPTION_NAME);
			foreach ($blocks as $id => $block) {
				// Add meta field to the exists blocks.
				// This method should called one time but for any reason 
				// don't overriding exists values.
				if (!array_key_exists('meta', $block)) {
					$blocks[$id]['meta'] = array();
				}
				$blocks[$id] = $this->cleanSingleBlock($block);
			}
			// Save blocks.
			update_option(cssJSToolbox::BLOCKS_OPTION_NAME, $blocks);
		}
		
		/**
		* Print CSSJtoolbox Debug message.
		* 
		* @param mixed $message
		*/
		public static function printDebugMessage($message) {
			if (CJTOOLBOX_DEBUG) {
				echo "{$message}<br />";
			}
		}
		
	}// END Class

	// Let's start the plugin
	cssJSToolbox::getInstance();
}