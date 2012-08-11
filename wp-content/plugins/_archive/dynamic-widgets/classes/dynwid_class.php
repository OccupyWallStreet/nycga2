<?php
/**
 * dynWid class
 *
 * @version $Id$
 * @copyright 2011 Jacco Drabbe
 */

	class dynWid {
		private $dbtable;
		public  $dwoptions = array();
		public  $dynwid_list;
		public  $enabled;
		private $firstmessage = TRUE;
		public  $listmade = FALSE;
		public  $overrule_maintype = array();
		private $registered_sidebars;
		public  $registered_widget_controls;
		public  $registered_widgets;
		public  $removelist = array();
		public  $sidebars;
		public  $template;
		public  $plugin_url;
		public  $useragent;
		public  $userrole;
		public  $whereami;
		private $wpdb;

		/**
		 * dynWid::__construct() Master class
		 *
		 */
		public function __construct() {
			if ( is_user_logged_in() ) {
				$this->userrole = $GLOBALS['current_user']->roles;
			} else {
				$this->userrole = array('anonymous');
			}

			$this->registered_sidebars = $GLOBALS['wp_registered_sidebars'];
			$this->registered_widget_controls = &$GLOBALS['wp_registered_widget_controls'];
			$this->registered_widgets = &$GLOBALS['wp_registered_widgets'];
			$this->sidebars = wp_get_sidebars_widgets();
			$this->useragent = $this->getBrowser();

			// DB init
			$this->wpdb = $GLOBALS['wpdb'];
			$this->dbtable = $this->wpdb->prefix . DW_DB_TABLE;
			$query = "SHOW TABLES LIKE '" . $this->dbtable . "'";
			$result = $this->wpdb->get_var($query);

			if ( is_null($result) ) {
				$this->enabled = FALSE;
			} else {
				$this->enabled = TRUE;
			}
		}

		/**
		 * dynWid::__get() Overload get
		 *
		 * @param string $name
		 * @return mixed
		 */
		public function __get($name) {
			return $this->$name;
		}

		/**
		 * dynWid::__isset() Overload isset
		 *
		 * @param mixed $name
		 * @return boolean
		 */
		public function __isset($name) {
			if ( isset($this->$name) ) {
				return TRUE;
			}
			return FALSE;
		}

		/**
		 * dynWid::__set() Overload set
		 *
		 * @param string $name
		 * @param mixed $value
		 */
		public function __set($name, $value) {
			$this->$name = $value;
		}

		/**
		 * dynWid::addChilds() Save child options
		 *
		 * @param string $widget_id ID of the widget
		 * @param string $maintype Name of module
		 * @param string $default Default module setting
		 * @param array $act Parent options
		 * @param array $childs Options
		 */
		public function addChilds($widget_id, $maintype, $default, $act, $childs) {
			$child_act = array();
			foreach ( $childs as $opt ) {
				if ( in_array($opt, $act) ) {
					$childs_act[ ] = $opt;
				}
			}
			$this->addMultiOption($widget_id, $maintype, $default, $childs_act);
		}

		/**
		 * dynWid::addDate() Saves date options
		 *
		 * @param string $widget_id ID of the widget
		 * @param array $dates Dates
		 * @return
		 */
		public function addDate($widget_id, $dates) {
			$query = "INSERT INTO " . $this->dbtable . "
                    (widget_id, maintype, name, value)
                  VALUES
                    ('" . $widget_id . "', 'date', 'default', '0')";
			$this->wpdb->query($query);

			foreach ( $dates as $name => $date ) {
				$query = "INSERT INTO " . $this->dbtable . "
                    (widget_id, maintype, name, value)
                  VALUES
                    ('" . $this->wpdb->escape($widget_id) . "', 'date', '" . $this->wpdb->escape($name) . "', '" . $this->wpdb->escape($date) . "')";
				$this->wpdb->query($query);
			}
		}

		/**
		 * dynWid::addMultiOption() Save multi (complex) options
		 *
		 * @param string $widget_id ID of the widget
		 * @param string $maintype Name of the module
		 * @param string $default Default setting
		 * @param array $act Options
		 */
		public function addMultiOption($widget_id, $maintype, $default, $act) {
			$insert = TRUE;

			if ( $default == 'no' ) {
				$opt_default = '0';
				$opt_act = '1';
			} else {
				$opt_default = '1';
				$opt_act = '0';
			}

			// Check single-post or single-option coming from post or tag screen
			if ( $maintype == 'single-post' || $maintype == 'single-tag' ) {
				$query = "SELECT COUNT(1) AS total FROM " . $this->dbtable . " WHERE widget_id = '" . $widget_id . "' AND maintype = '" . $maintype . "' AND name = 'default'";
				$count = $this->wpdb->get_var($this->wpdb->prepare($query));
				if ( $count > 0 ) {
					$insert = FALSE;
				}
			}

			if ( $insert ) {
				$query = "INSERT INTO " . $this->dbtable . "
                      (widget_id, maintype, name, value)
                    VALUES
                      ('" . $this->wpdb->escape($widget_id) . "', '" . $this->wpdb->escape($maintype) . "', 'default', '" . $this->wpdb->escape($opt_default) . "')";
				$this->wpdb->query($query);
			}
			foreach ( $act as $option ) {
				$query = "INSERT INTO " . $this->dbtable . "
                      (widget_id, maintype, name, value)
                    VALUES
                      ('" . $this->wpdb->escape($widget_id) . "', '" . $this->wpdb->escape($maintype) . "', '" . $this->wpdb->escape($option) . "', '" . $this->wpdb->escape($opt_act) . "')";
				$this->wpdb->query($query);
			}
		}

		/**
		 * dynWid::addSingleOption() Save single (simple) options
		 *
		 * @param string $widget_id ID of the widget
		 * @param string $maintype Name of the module
		 * @param integer $value Default setting
		 */
		public function addSingleOption($widget_id, $maintype, $value = '0') {
			$query = "INSERT INTO " . $this->dbtable . "
                    (widget_id, maintype, value)
                  VALUES
                    ('" . $this->wpdb->escape($widget_id) . "', '" . $this->wpdb->escape($maintype) . "', '" . $this->wpdb->escape($value) . "')";
			$this->wpdb->query($query);
		}

		/**
		 * dynWid::checkWPhead() Checks for wp_head()
		 *
		 * @return integer
		 */
		public function checkWPhead() {
			$ct = current_theme_info();
			$headerfile = $ct->template_dir . '/header.php';
			if ( file_exists($headerfile) ) {
				$buffer = file_get_contents($headerfile);
				if ( strpos($buffer, 'wp_head()') ) {
					// wp_head() found
					return 1;
				} else {
					// wp_head() not found
					return 0;
				}
			} else {
				// wp_head() unable to determine
				return 2;
			}
		}

		/**
		 * dynWid::createList() Creates full list of options
		 *
		 */
		private function createList() {
			$this->dynwid_list = array();

			foreach ( $this->sidebars as $sidebar_id => $widgets ) {
				if ( count($widgets) > 0 ) {
					foreach ( $widgets as $widget_id ) {
						if ( $this->hasOptions($widget_id) ) {
							$this->dynwid_list[ ] = $widget_id;
						}
					} // END foreach widgets
				}
			} // END foreach sidebars
		}

		/**
		 * dynWid::deleteOption() Removes option
		 *
		 * @param string $widget_id ID of widget
		 * @param string $maintype Name of module
		 * @param string $name Name of option
		 */
		public function deleteOption($widget_id, $maintype, $name = '') {
			$query = "DELETE FROM " . $this->dbtable . " WHERE widget_id = '" . $widget_id . "' AND maintype = '" . $maintype ."'";
			if (! empty($name) ) {
				$query .= " AND name = '" . $name . "'";
			}
			$this->wpdb->query($query);
		}

		/**
		 * dynWid::detectPage() Page detection
		 *
		 * @return string
		 */
		public function detectPage() {
			if ( is_front_page() && get_option('show_on_front') == 'posts' ) {
				return 'front-page';
			} else if ( is_home() && get_option('show_on_front') == 'page' ) {
				return 'front-page';
			} else if ( is_attachment() ) {
				return 'attachment';					// must be before is_single(), otherwise detects as 'single'
			} else if ( is_single() ) {
				return 'single';
			} else if ( is_page() ) {
				return 'page';
			} else if ( is_author() ) {
				return 'author';
			} else if ( is_category() ) {
				return 'category';
			} else if ( is_tag() ) {
				return 'tag';
			} else if ( function_exists('is_post_type_archive') && is_post_type_archive() ) {
				return 'cp_archive';				// must be before is_archive(), otherwise detects as 'archive' in WP 3.1.0
			} else if ( function_exists('is_tax') && is_tax() ) {
				return 'tax_archive';
			} else if ( is_archive() && ! is_category() && ! is_author() && ! is_tag() ) {
				return 'archive';
			} else if ( function_exists('bbp_is_single_user') && (bbp_is_single_user() || bbp_is_single_user_edit()) ) {	// must be before is_404(), otherwise bbPress profile page is detected as 'e404'.
				return 'bbp_profile';
			} else if ( is_404() ) {
				return 'e404';
			} else if ( is_search() ) {
				return 'search';
			} else if ( function_exists('is_pod_page') && is_pod_page() ) {
				return 'pods';
			} else {
				return 'undef';
			}
		}

		/**
		 * dynWid::dump() Dump file creation
		 *
		 */
		public function dump() {
			echo "wp version: " . $GLOBALS['wp_version'] . "\n";
			echo "wp_head: " . $this->checkWPhead() . "\n";
			echo "dw version: " . DW_VERSION . "\n";
			echo "php version: " . PHP_VERSION . "\n";
			echo "\n";
			echo "front: " . get_option('show_on_front') . "\n";
			if ( get_option('show_on_front') == 'page' ) {
				echo "front page: " . get_option('page_on_front') . "\n";
				echo "posts page: " . get_option('page_for_posts') . "\n";
			}

			echo "\n";
			echo "list: \n";
			$list = array();
			$this->createList();
			foreach ( $this->dynwid_list as $widget_id ) {
				$list[$widget_id] = strip_tags($this->getName($widget_id));
			}
			print_r($list);

			echo "wp_registered_widgets: \n";
			print_r($this->registered_widgets);

			echo "options: \n";
			print_r( $this->getOpt('%', NULL) );

			echo "\n";
			echo serialize($this->getOpt('%', NULL));
		}

		/**
		 * dynWid::dumpOpt() Debug dump option
		 *
		 * @param object $opt
		 */
		public function dumpOpt($opt) {
			if ( DW_DEBUG && count($opt) > 0 ) {
				var_dump($opt);
			}
		}

		// replacement for createList() to make the worker faster
		/**
		 * dynWid::dwList() Option list creation
		 *
		 * @param string $whereami Page
		 */
		public function dwList($whereami) {
			$this->dynwid_list = array();
			if ( $whereami == 'home' ) {
				$whereami = 'page';
			}

			$query = "SELECT DISTINCT widget_id FROM " . $this->dbtable . "
                  WHERE  maintype LIKE '" . $whereami . "%'";

			if ( count($this->overrule_maintype) > 0 ) {
				$query .= " OR maintype IN ";
				$q = array();
				foreach ( $this->overrule_maintype as $omt ) {
					$q[ ] = "'" . $omt . "'";
				}
				$query .= "(" . implode(', ', $q) . ")";
			}

			$results = $this->wpdb->get_results($query);
			foreach ( $results as $myrow ) {
				$this->dynwid_list[ ] = $myrow->widget_id;
			}
		}

		/**
		 * dynWid::getBrowser() Browser detection
		 *
		 * @return string
		 */
		private function getBrowser() {
			global $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome;

			if ( $is_gecko ) {
				return 'gecko';
			} else if ( $is_IE ) {
				if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.') !== FALSE ) {
					return 'msie6';
				} else {
					return 'msie';
				}
			} else if ( $is_opera ) {
				return 'opera';
			} else if ( $is_NS4 ) {
				return 'ns';
			} else if ( $is_safari ) {
				return 'safari';
			} else if ( $is_chrome ) {
				return 'chrome';
			} else {
				return 'undef';
			}
		}

		/**
		 * dynWid::getDWOpt() Gets SQL object used in DWOpts
		 *
		 * @param string $widget_id ID of widget
		 * @param string $maintype Name of module
		 * @return object
		 */
		public function getDWOpt($widget_id, $maintype) {
			if ( $maintype == 'home' ) {
				$maintype = 'page';
			}
			$query = "SELECT widget_id, maintype, name, value FROM " . $this->dbtable . "
                 WHERE widget_id LIKE '" . $widget_id . "'
                   AND maintype LIKE '" . $maintype . "%'
                 ORDER BY maintype, name";
			$results = new DWOpts($this->wpdb->get_results($query), $maintype);
			return $results;
		}

		/**
		 * dynWid::getModuleName() Full registration of the modules
		 *
		 */
		public function getModuleName() {
			$dwoptions = array();
			// I NEED PHP > 5.3!!

			DWModule::registerOption(DW_Archive::$option);
			DWModule::registerOption(DW_Attachment::$option);
			DWModule::registerOption(DW_Author::$option);
			DWModule::registerOption(DW_bbPress::$option);
			DWModule::registerOption(DW_BP::$option);
			DWModule::registerOption(DW_Browser::$option);
			DWModule::registerOption(DW_Category::$option);
			// DWModule::registerOption(DW_CustomPost::$option);
			DW_CustomPost::registerOption();
			DWModule::registerOption(DW_Date::$option);
			DWModule::registerOption(DW_E404::$option);
			DWModule::registerOption(DW_Front_page::$option);
			DWModule::registerOption(DW_Page::$option);
			DWModule::registerOption(DW_Pods::$option);
			DWModule::registerOption(DW_QT::$option);
			DWModule::registerOption(DW_Role::$option);
			DWModule::registerOption(DW_Search::$option);
			DWModule::registerOption(DW_Single::$option);
			DWModule::registerOption(DW_Tag::$option);
			DWModule::registerOption(DW_Tpl::$option);
			DWModule::registerOption(DW_WPSC::$option);
			DWModule::registerOption(DW_WPML::$option);
		}

		/**
		 * dynWid::getName() Gets the lookup name
		 *
		 * @return string
		 */
		public function getName($id, $type = 'W') {
			switch ( $type ) {
				case 'S':
					$lookup = $this->registered_sidebars;
					break;

				default:
					$lookup = $this->registered_widgets;
					// end default
			}

			if ( isset($lookup[$id]['name']) ) {
				$name = $lookup[$id]['name'];

				if ( $type == 'W' && isset($lookup[$id]['params'][0]['number']) ) {
					// Retrieve optional set title
					$number = $lookup[$id]['params'][0]['number'];
					$option_name = $lookup[$id]['callback'][0]->option_name;
					$option = get_option($option_name);
					if (! empty($option[$number]['title']) ) {
						$name .= ': <span class="in-widget-title">' . $option[$number]['title'] . '</span>';
					}
				}
			} else {
				$name = NULL;
			}

			return $name;
		}

		/**
		 * dynWid::getOpt() Get SQL object of Opt
		 *
		 * @param string $widget_id ID of the widget
		 * @param string $maintype Name of the module
		 * @param boolean $admin Admin page
		 * @return object
		 */
		public function getOpt($widget_id, $maintype, $admin = TRUE) {
			$opt = array();

			if ( $admin ) {
				$query = "SELECT widget_id, maintype, name, value FROM " . $this->dbtable . "
                  WHERE widget_id LIKE '" . $widget_id . "'
                    AND maintype LIKE '" . $maintype . "%'
                  ORDER BY maintype, name";

			} else {
				if ( $maintype == 'home' ) {
					$maintype = 'page';
				}
				$query = "SELECT widget_id, maintype, name, value FROM " . $this->dbtable . "
                  WHERE widget_id LIKE '" . $widget_id . "'
                    AND (maintype LIKE '" . $maintype . "%'";

				if ( count($this->overrule_maintype) > 0 ) {
					$query .= " OR maintype IN (";
					$q = array();
					foreach ( $this->overrule_maintype as $omt ) {
						$q[ ] = "'" . $omt . "'";
					}
					$query .= implode(', ', $q);
					$query .= ")";
				}

				$query .= ") ORDER BY maintype, name";
			}
			$this->message('Q: ' . $query);

			$results = $this->wpdb->get_results($query);
			return $results;
		}

		/**
		 * dynWid::getPostCatParents() Gets parents from post category
		 *
		 * @param array $post_category Categories
		 * @return array
		 */
		public function getPostCatParents($post_category) {
			// Getting all parents from the categories this post is in
			$parents = array();
			foreach ( $post_category as $id ) {
				$tp = $this->getTaxParents('category', array(), $id);
				// Now checking if the parent is already known
				foreach ( $tp as $p ) {
					if (! in_array($p, $parents) ) {
						$parents[ ] = $p;
					}
				}
			}

			return $parents;
		}

		/**
		 * dynWid::getParents() Gets parents from posts or pages
		 *
		 * @param string $type Type
		 * @param array $arr
		 * @param integer $id Child ID
		 * @return
		 */
		public function getParents($type, $arr, $id) {
			if ( $type == 'page' ) {
				$obj = get_page($id);
			} else {
				$obj = get_post($id);
			}

			if ( $obj->post_parent > 0 ) {
				$arr[ ] = $obj->post_parent;
				$a = &$arr;
				$a = $this->getParents($type, $a, $obj->post_parent);
			}

			return $arr;
		}

		/**
		 * dynWid::getTaxParents() Get parents for Taxonomy
		 *
		 * @param string $tax_name Taxonomy name
		 * @param array $arr
		 * @param integer $id Child ID
		 * @return
		 */
		public function getTaxParents($tax_name, $arr, $id) {
			$obj = get_term_by('id', $id, $tax_name);
			if ( $obj->parent > 0 ) {
				$arr[ ] = $obj->parent;
				$a = &$arr;
				$a = $this->getTaxParents($tax_name, $a, $obj->parent);
			}
			return $arr;
		}

		/**
		 * dynWid::hasOptions() Checks if a widget has options set
		 *
		 * @param string $widget_id ID of the widget
		 * @return boolean
		 */
		public function hasOptions($widget_id) {
			$query = "SELECT COUNT(1) AS total FROM " . $this->dbtable . "
                  WHERE widget_id = '" . $widget_id . "' AND
                        maintype != 'individual'";
			$count = $this->wpdb->get_var($this->wpdb->prepare($query));

			if ( $count > 0 ) {
				return TRUE;
			} else {
				return FALSE;
			}
		}

		/**
		 * dynWid::housekeeping() Housekeeping
		 *
		 */
		public function housekeeping() {
			$widgets = array_keys($this->registered_widgets);

			$query = "SELECT DISTINCT widget_id FROM " . $this->dbtable;
			$results = $this->wpdb->get_results($query);
			foreach ( $results as $myrow ) {
				if (! in_array($myrow->widget_id, $widgets) ) {
					$this->resetOptions($myrow->widget_id);
				}
			}
		}

		/**
		 * dynWid::loadModules() Full load of all modules
		 *
		 */
		public function loadModules() {
			$dh = opendir(DW_MODULES);
			while ( ($file = readdir($dh)) !== FALSE) {
				if ( $file != '.' && $file != '..' && substr(strrchr($file, '_'), 1) == 'module.php' ) {
					include_once(DW_MODULES . $file);
				}
			}
		}
		
		/**
		 * dynWid::log() Write text to debug log
		 *
		 */		
		public function log($text) {
			if ( WP_DEBUG && DW_DEBUG ) {
				error_log($text);
			}
		}

		/**
		 * dynWid::message() Debug message
		 *
		 * @param string $text
		 */
		public function message($text) {
			if ( DW_DEBUG ) {
				if ( $this->firstmessage ) {
					echo "\n";
					$this->firstmessage = FALSE;
				}
				echo '<!-- ' . $text . ' //-->';
				echo "\n";
			}
		}

		/**
		 * dynWid::registerOverrulers() Overrule module regsitering
		 *
		 */
		public function registerOverrulers() {
			include_once(DW_MODULES . 'browser_module.php');
			include_once(DW_MODULES . 'date_module.php');
			include_once(DW_MODULES . 'role_module.php');
			include_once(DW_MODULES . 'tpl_module.php');
			DW_Browser::checkOverrule('DW_Browser');
			DW_Date::checkOverrule('DW_Date');
			DW_Role::checkOverrule('DW_Role');
			DW_Tpl::checkOverrule('DW_Tpl');

			// WPML Plugin Support
			include_once(DW_MODULES . 'wpml_module.php');
			DW_WPML::detectLanguage();

			// QT Plugin Support
			include_once(DW_MODULES . 'qt_module.php');
			DW_QT::detectLanguage();
		}

		/**
		 * dynWid::resetOptions() Full reset (remove) of options
		 *
		 * @param string $widget_id ID of the widget
		 */
		public function resetOptions($widget_id) {
			$query = "DELETE FROM " . $this->dbtable . " WHERE widget_id = '" . $widget_id . "'";
			$this->wpdb->query($query);
		}
	}
?>