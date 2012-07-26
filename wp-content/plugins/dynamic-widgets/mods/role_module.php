<?php
/**
 * Role Module
 *
 * @version $Id: role_module.php 523481 2012-03-25 19:49:08Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	class DW_Role extends DWModule {
		protected static $info = 'Setting options by role is very powerfull. It can override all other options!<br />Users who are not logged in, get the <em>Anonymous</em> role.';
		protected static $except = 'Except for:';
		public static $option = array( 'role' => 'Role' );
		protected static $overrule = TRUE;
		protected static $question = 'Show widget to everybody?';
		protected static $type = 'complex';

		public static function admin() {
			$DW = &$GLOBALS['DW'];

			parent::admin();

		   $list = array();
		   $wp_roles = $GLOBALS['wp_roles'];
		   $roles = array_merge($wp_roles->role_names, array('anonymous' => __('Anonymous') . '|User role'));
		   foreach ( $roles as $rid => $role ) {
		   	$list[esc_attr($rid)] = translate_user_role($role);
		   }

		   self::mkGUI(self::$type, self::$option[self::$name], self::$question, self::$info, self::$except, $list);
		}
	}
?>