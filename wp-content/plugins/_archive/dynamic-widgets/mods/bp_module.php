<?php
/**
 * BP module
 * http://buddypress.org/
 *
 * @version $Id: bp_module.php 523481 2012-03-25 19:49:08Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	class DW_BP extends DWModule {
		protected static $except = 'Except on the components pages';
		public static $option = array( 'bp'	=> 'BuddyPress', 'bp-group'	=> 'BuddyPress Groups' );
		public static $plugin = array( 'bp' => FALSE, 'bp_groups' => FALSE );
		protected static $question = 'Show widget default on BuddyPress pages?';
		protected static $type = 'complex';

		public static function admin() {
			$DW = &$GLOBALS['DW'];

			parent::admin();

			if ( self::detect() ) {
				$list = self::getBPcomponents();

				// BP components
				self::mkGUI(self::$type, self::$option['bp'], self::$question, self::$info, self::$except, $list, 'bp');

				// BP Groups
				if ( $DW->bp_groups ) {
					self::$question = 'Show widget default on BuddyPress Group pages?';
					self::GUIHeader(self::$option['bp-group'], self::$question, NULL, NULL, $DW->getDWOpt($_GET['id'], 'bp-group'));
					self::GUIOption('bp-group', $DW->getDWOpt($_GET['id'], 'bp-group'));

					echo '<table border="0" cellspacing="0" cellpadding="0">';
					echo '<tr><td valign="top">';

					self::$except = 'Except in the groups';
					$list = self::getBPgroups();
					self::GUIComplex(self::$except, $list, NULL, 'bp-group');

					echo '</td><td style="width:10px"></td><td valign="top">';

					self::$except = 'Except in the group pages';
					$list = array(
										'forum_index' 	=> __('Forum Index', DW_L10N_DOMAIN),
										'forum_topic' 	=> __('Forum Topics', DW_L10N_DOMAIN),
										'members_index'	=> __('Members Index', DW_L10N_DOMAIN)
									);
					self::GUIComplex(self::$except, $list, NULL, 'bp_group');

					echo '</td></tr></table>';
					self::GUIFooter();
				}
			}
		}

		public static function detect($update = TRUE) {
			$DW = &$GLOBALS['DW'];
			$DW->bp = FALSE;

			if ( defined('BP_VERSION') ) {
				if ( $update ) {
					$DW->bp = TRUE;
				}
				return TRUE;
			}
			return FALSE;
		}

		public static function detectComponent() {
			$bp = &$GLOBALS['bp'];
			$DW = &$GLOBALS['DW'];

			if ( self::detect(FALSE) ) {
				/*
				   Array of BP components needed as a workaround for certain themes claiming an invalid BP component,
				   confusing DW by detecting BP, when it should be Page.
				*/
				$components = self::getBPcomponents(FALSE);
				$bp_components = array_keys($components);

				if (! empty($bp->current_component) && in_array($bp->current_component, $bp_components) ) {
					if ( $bp->current_component == 'groups' && ! empty($bp->current_item) ) {
						$DW->bp_groups = TRUE;
						$DW->whereami = 'bp-group';
						$DW->message('BP detected, component: ' . $bp->current_component . '; Group: ' . $bp->current_item . ', Page changed to bp-group');
					} else {
						$DW->bp = TRUE;
						$DW->whereami = 'bp';
						$DW->message('BP detected, component: ' . $bp->current_component . ', Page changed to bp');
					}
				}
			}
		}

		protected static function getBPcomponents($update = TRUE) {
			$bp = &$GLOBALS['bp'];
			$DW = &$GLOBALS['DW'];
			$components = array();

			foreach ( $bp->active_components as $key => $value ) {
				if ( version_compare(BP_VERSION, '1.5', '<') ) {
					$c = &$value;
				} else {
					$c = &$key;
				}
				
				if ( $c == 'groups' ) {
					$components[$c] = ucfirst($c) . ' (only main page)';
					$DW->bp_groups = TRUE;
				} else {
					$components[$c] = ucfirst($c);
				}
			}

			asort($components);
			return $components;
		}

		protected static function getBPgroups() {
			$bp = &$GLOBALS['bp'];
			$wpdb = &$GLOBALS['wpdb'];

			$groups = array();
			$table = $bp->groups->table_name;
			$fields = array('slug', 'name');
			$query = "SELECT " . implode(', ', $fields) . " FROM " . $table . " ORDER BY name";
			$results = $wpdb->get_results($query);

			foreach ( $results as $myrow ) {
				$groups[$myrow->slug] = $myrow->name;
			}

			return $groups;
		}

		public static function is_dw_bp_component($id) {
			$bp = &$GLOBALS['bp'];

			$component = $bp->current_component;
			if ( in_array($component, $id) ) {
				return TRUE;
			}
			return FALSE;
		}

		public static function is_dw_bp_group($id) {
			$bp = &$GLOBALS['bp'];

			$group = $bp->current_item;
			
			// Check if there is an hierarchy in the groups (Plugin: BP Group Hierarchy)
			if ( strpos($group, '/') !== FALSE ) {
				$group = substr( strrchr($group, '/'), 1 );
			}
			
			if ( in_array($group, $id) ) {
				return TRUE;
			}
			return FALSE;
		}

		public static function is_dw_bp_group_forum($id) {
			$bp = &$GLOBALS['bp'];

			if ( $bp->current_action == 'forum' ) {
				if ( count($bp->action_variables) > 0 && in_array('forum_topic', $id) ) {
					return TRUE;
				} else if ( count($bp->action_variables) == 0 && in_array('forum_index', $id) ) {
					return TRUE;
				}
			}
			return FALSE;
		}

		public static function is_dw_bp_group_members($id) {
			$bp = &$GLOBALS['bp'];

			if ( $bp->current_action == 'members' && in_array('members_index', $id) ) {
				return TRUE;
			}
			return FALSE;
		}
	}
?>