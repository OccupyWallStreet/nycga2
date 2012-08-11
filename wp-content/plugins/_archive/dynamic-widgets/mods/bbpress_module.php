<?php
/**
 * bbPress Module
 *
 * @version $Id$
 * @copyright 2011 Jacco Drabbe
 */

	class DW_bbPress extends DWModule {
		public static $option = array( 'bbp_profile' => 'bbPress User Profile pages' );
		protected static $question = 'Show widget on bbPress User Profile pages?';
		protected static $type = 'complex';
		
		public static function admin() {
			parent::admin();
			
			if ( self::detect() ) {
				self::mkGUI('simple', self::$option['bbp_profile'], self::$question, FALSE, FALSE, FALSE, 'bbp_profile');
			}
		}
		
		public static function detect() {
			if ( function_exists('bbp_is_single_user') ) {
				return TRUE;
			}
			return FALSE;
		}
	}
?>