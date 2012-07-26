<?php
/**
 *	WPEC Module
 *  http://getshopped.org/
 *
 * @version $Id: wpec_module.php 523481 2012-03-25 19:49:08Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	class DW_WPSC extends DWModule {
		protected static $except = 'Except the categories';
		public static $option = array( 'wpsc' => 'WPSC Category' );
		public static $plugin = array( 'wpsc' => FALSE );
		protected static $question = 'Show widget default on WPSC categories?';
		protected static $type = 'complex';

		public static function admin() {
			$DW = &$GLOBALS['DW'];

			parent::admin();

			if ( self::detect() ) {
				$list = self::getWPSCcategories();
				self::mkGUI(self::$type, self::$option[self::$name],self::$question ,self::$info, self::$except, $lists);
			}
		}

		public static function detect($update = TRUE) {
			$DW = &$GLOBALS['DW'];
			$DW->wpsc = FALSE;

			if ( defined('WPSC_VERSION') && version_compare(WPSC_VERSION, '3.8', '<') ) {
				if ( $update ) {
					$DW->wpsc = TRUE;
				}
				return TRUE;
			}
			return FALSE;
		}

		public static function detectCategory() {
			$DW = &$GLOBALS['DW'];
			
			if ( self::detect(FALSE) ) {
				$wpsc_query = &$GLOBALS['wpsc_query'];

				if ( $wpsc_query->category > 0 ) {
					$DW->wpsc = TRUE;
					$DW->whereami = 'wpsc';
					$DW->message('WPSC detected, page changed to ' . $DW->whereami . ', category: ' . $wpsc_query->category);
				}
			}
		}

		public static function getWPSCcategories() {
			$wpdb = &$GLOBALS['wpdb'];

			$categories = array();
			$table = WPSC_TABLE_PRODUCT_CATEGORIES;
			$fields = array('id', 'name');
			$query = "SELECT " . implode(', ', $fields) . " FROM " . $table . " WHERE active = '1' ORDER BY name";
			$results = $wpdb->get_results($query);

			foreach ( $results as $myrow ) {
				$categories[$myrow->id] = $myrow->name;
			}

			return $categories;
		}

		public static function is_dw_wpsc_category($id) {
			$wpsc_query = &$GLOBALS['wpsc_query'];
			$category = $wpsc_query->category;

			if ( is_int($id) ) {
				$id = array($id);
			}

			if ( in_array($category, $id) ) {
				return TRUE;
			}
			return FALSE;
		}
	}
?>