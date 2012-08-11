<?php
/**
 * Template Module
 *
 * @version $Id: tpl_module.php 523481 2012-03-25 19:49:08Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	class DW_Tpl extends DWModule {
		protected static $info = 'This options takes precedence above other options like Pages and/or Single Posts.';
		protected static $except = 'Except the templates';
		public static $option = array( 'tpl'	=> 'Templates' );
		protected static $overrule = TRUE;
		protected static $question = 'Show widget on every template?';
		protected static $type = 'complex';

		public static function admin() {
			parent::admin();

			$tpl = get_page_templates();
			if ( count($tpl) > 0 ) {
				$list = array();
				foreach ( $tpl as $tplname => $tplfile ) {
					$list[basename($tplfile)] = $tplname;
				}
				self::mkGUI(self::$type, self::$option[self::$name], self::$question, self::$info, self::$except, $list);
			}
		}
	}
?>