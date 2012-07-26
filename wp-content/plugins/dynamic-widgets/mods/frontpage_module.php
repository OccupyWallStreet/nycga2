<?php
/**
 * Front Page Module
 *
 * @version $Id: frontpage_module.php 523481 2012-03-25 19:49:08Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	class DW_Front_page extends DWModule {
		protected static $except = 'Except for:';
		protected static $info = 'This option only applies when your front page is set to display your latest posts (See Settings &gt; Reading).<br />When a static page is set, you can use the options for the static pages below.';
		public static $option = array( 'front-page' => 'Front Page' );
		protected static $question = 'Show widget on the front page?';
		protected static $type = 'complex';

		public static function admin() {
			parent::admin();
			
			$list = array( 1 => __('First page') );

			if ( get_option('show_on_front') == 'page' ) {
				self::$option = array( 'front-page' => 'Posts Page' );
				self::$question = 'Show widget on the posts page?';
			}
			self::mkGUI(self::$type, self::$option[self::$name], self::$question, NULL, self::$except, $list);
		}
	}
?>