<?php
/**
 * Author Module
 *
 * @version $Id: author_module.php 523481 2012-03-25 19:49:08Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	class DW_Author extends DWModule {
		protected static $except = 'Except the author(s)';
		public static $option = array( 'author' => 'Author Pages' );
		protected static $question = 'Show widget default on author pages?';
		protected static $type = 'complex';

		public static function admin() {
			parent::admin();
			self::mkGUI();
		}

		public static function getAuthors() {
			global $wpdb;

			if ( function_exists('get_users') ) {
				$authors = get_users( array('who' => 'authors') );
			} else {
				$query = "SELECT " . $wpdb->prefix . "users.ID, " . $wpdb->prefix . "users.display_name
							 FROM " . $wpdb->prefix . "users
							 JOIN " . $wpdb->prefix . "usermeta ON " . $wpdb->prefix . "users.ID = " . $wpdb->prefix . "usermeta.user_id
							 WHERE 1 AND " . $wpdb->prefix . "usermeta.meta_key = '" . $wpdb->prefix . "user_level'
							 	AND " . $wpdb->prefix . "usermeta.meta_value > '0'";
				$authors = $wpdb->get_results($query);
			}

			$list = array();
			foreach ( $authors as $author ) {
				$list[$author->ID] = $author->display_name;
			}

			return $list;
		}

		public static function mkGUI($single = FALSE) {
			$DW = &$GLOBALS['DW'];
			$list = self::getAuthors();

			if ( $single ) {
				self::$opt = $DW->getDWOpt($_GET['id'], 'single-author');

				if ( count($list) > DW_LIST_LIMIT ) {
					$select_style = DW_LIST_STYLE;
				}

				if ( count($list) > 0 ) {
					$DW->dumpOpt(self::$opt);
					echo '<br />';
					_e(self::$except, DW_L10N_DOMAIN);
					echo '<br />';
					echo '<div id="single-author-select" class="condition-select" ' . ( (isset($select_style)) ? $select_style : '' ) . ' />';
					foreach ( $list as $key => $value ) {
						$extra = 'onclick="ci(\'single_author_act_' . $key . '\')"';
						echo '<input type="checkbox" id="single_author_act_' . $key . '" name="single_author_act[]" value="' . $key . '" ' . ( (self::$opt->count > 0 && in_array($key, self::$opt->act)) ? 'checked="checked"' : '' ) . $extra  . ' /> <label for="single_author_act_' . $key . '">' . $value . '</label><br />' . "\n";
					}
					echo '</div>' . "\n";
				}
			} else {
				parent::mkGUI(self::$type, self::$option[self::$name], self::$question, FALSE, self::$except, $list);
			}
		}
	}
?>