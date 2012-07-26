<?php
/**
 * Tag Module
 *
 * @version $Id$
 * @copyright 2011 Jacco Drabbe
 */

	class DW_Tag extends DWModule {
		protected static $except = 'Except the tag(s)';
		public static $option = array( 'tag'	=> 'Tag Pages' );
		protected static $question = 'Show widget on tag pages?';
		protected static $type = 'complex';

		public static function admin() {
			parent::admin();

			$list = array();
			$tags = get_tags( array('hide_empty' => FALSE) );
			foreach ( $tags as $t ) {
				$list[$t->term_id] = $t->name;
			}
			
			self::mkGUI(self::$type, self::$option[self::$name], self::$question, FALSE, self::$except, $list);
		}
	}
?>