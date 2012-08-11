<?php
/**
 * Pods Module
 *
 * @version $Id$
 * @copyright 2011 Jacco Drabbe
 */

	class DW_Pods extends DWModule {
		protected static $except = 'Except the Pods pages';
		public static $option = array( 'pods' => 'Pods pages' );
		public static $plugin = array( 'pods' => FALSE );
		protected static $question = 'Show widget default on Pods pages?';
		protected static $type = 'complex';

		public static function admin() {
			$wpdb = &$GLOBALS['wpdb'];

			parent::admin();

			if ( self::detect() ) {
				$query = "SELECT id, uri FROM " . $wpdb->prefix . "pod_pages ORDER BY uri";
				$results = $wpdb->get_results($query);

				$list = array();
				foreach ( $results as $row ) {
					$list[$row->id] = $row->uri;
				}

				self::mkGUI(self::$type, self::$option[self::$name], self::$question, FALSE, self::$except, $list);
			}
		}

		public static function detect($update = TRUE) {
			$DW = &$GLOBALS['DW'];
			$DW->pods = FALSE;

			if ( defined('PODS_VERSION_FULL') ) {
				if ( $update ) {
					$DW->pods = TRUE;
				}
				return TRUE;
			}
			return FALSE;
		}

		public static function is_dw_pods_page($id) {
			global $pod_page_exists;

			if ( is_int($id) ) {
				$id = array($id);
			}

			if ( in_array($pod_page_exists['id'], $id) ) {
				return TRUE;
			}
			return FALSE;
		}
	}
?>