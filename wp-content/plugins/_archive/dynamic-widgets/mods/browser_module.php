<?php
/**
 * Browser Module
 *
 * @version $Id: useragent_module.php 402236 2011-06-28 20:46:55Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	class DW_Browser extends DWModule {
		protected static $except = 'Except the browser(s)';
		protected static $info = 'Browser detection is never 100% accurate.';
		public static $option = array( 'browser'	=>'Browser' );
		protected static $overrule = TRUE;
		protected static $question = 'Show widget with all browsers?';
		protected static $type = 'complex';

		public static function admin() {
			parent::admin();

			$list = array(
				'gecko'		=> 'Firefox' . ' ' . __('(and other Gecko based)', DW_L10N_DOMAIN),
				'msie'   	=> 'Internet Explorer',
				'msie6'		=> 'Internet Explorer 6',
				'opera'  	=> 'Opera',
				'ns'     	=> 'Netscape 4',
				'safari' 	=> 'Safari',
				'chrome' 	=> 'Chrome',
				'undef'  	=> __('Other / Unknown / Not detected', DW_L10N_DOMAIN)
			);
			self::mkGUI(self::$type, self::$option[self::$name], self::$question, self::$info, self::$except, $list);
		}
	}
?>