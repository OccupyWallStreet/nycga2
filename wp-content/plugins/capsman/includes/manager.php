<?php
/**
 * Capability Manager.
 * Plugin to create and manage roles and capabilities.
 *
 * @version		$Rev: 199485 $
 * @author		Jordi Canals
 * @copyright   Copyright (C) 2009, 2010 Jordi Canals
 * @license		GNU General Public License version 2
 * @link		http://alkivia.org
 * @package		Alkivia
 * @subpackage	CapsMan
 *

	Copyright 2009, 2010 Jordi Canals <devel@jcanals.cat>

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	version 2 as published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

include_once ( AK_CLASSES . '/abstract/plugin.php' );

/**
 * Class cmanCapsManager.
 * Sets the main environment for all Capability Manager components.
 *
 * @author		Jordi Canals
 * @package		CapsMan
 * @link		http://alkivia.org
 */
class CapabilityManager extends akPluginAbstract
{
	/**
	 * Array with all capabilities to be managed. (Depends on user caps).
	 * The array keys are the capability, the value is its screen name.
	 * @var array
	 */
	private $capabilities = array();

	/**
	 * Array with roles that can be managed. (Depends on user roles).
	 * The array keys are the role name, the value is its translated name.
	 * @var array
	 */
	private $roles = array();

	/**
	 * Current role we are managing
	 * @var string
	 */
	private $current;

	/**
	 * Maximum level current manager can assign to a user.
	 * @var int
	 */
	private $max_level;

	/**
	 * Creates some filters at module load time.
	 *
	 * @see akModuleAbstract#moduleLoad()
	 *
	 * @return void
	 */
    protected function moduleLoad ()
    {
        // Only roles that a user can administer can be assigned to others.
        add_filter('editable_roles', array($this, 'filterEditRoles'));

        // Users with roles that cannot be managed, are not allowed to be edited.
        add_filter('map_meta_cap', array(&$this, 'filterUserEdit'), 10, 4);
    }

	/**
	 * Sets default settings values.
	 *
	 * @return void
	 */
	protected function defaultOptions ()
	{
		$this->generateSysNames();

		return array(
			'form-rows' => 5,
			'syscaps'   => $this->capabilities
		);
	}

	/**
	 * Activates the plugin and sets the new capability 'Manage Capabilities'
	 *
	 * @return void
	 */
	protected function pluginActivate ()
	{
		$this->setAdminCapability();
	}

	/**
	 * Updates Capability Manager to a new version
	 *
	 * @return void
	 */
	protected function pluginUpdate ( $version )
	{
		$backup = get_option($this->ID . '_backup');
		if ( false === $backup ) {		// No previous backup found. Save it!
			global $wpdb;
			$roles = get_option($wpdb->prefix . 'user_roles');
			update_option($this->ID . '_backup', $roles);
		}
	}

	/**
	 * Adds admin panel menus. (At plugins loading time. This is before plugins_loaded).
	 * User needs to have 'manage_capabilities' to access this menus.
	 * This is set as an action in the parent class constructor.
	 *
	 * @hook action admin_menu
	 * @return void
	 */
	public function adminMenus ()
	{
		// First we check if user is administrator and can 'manage_capabilities'.
		if ( current_user_can('administrator') && ! current_user_can('manage_capabilities') ) {
			$this->setAdminCapability();
		}

		add_users_page( __('Capability Manager', $this->ID),  __('Capabilities', $this->ID), 'manage_capabilities', $this->ID, array($this, 'generalManager'));
		add_management_page(__('Capability Manager', $this->ID),  __('Capability Manager', $this->ID), 'manage_capabilities', $this->ID . '-tool', array($this, 'backupTool'));
	}

	/**
	 * Sets the 'manage_capabilities' cap to the administrator role.
	 *
	 * @return void
	 */
	private function setAdminCapability ()
	{
		$admin = get_role('administrator');
		$admin->add_cap('manage_capabilities');
	}

	/**
	 * Filters roles that can be shown in roles list.
	 * This is mainly used to prevent an user admin to create other users with
	 * higher capabilities.
	 *
	 * @hook 'editable_roles' filter.
	 *
	 * @param $roles List of roles to check.
	 * @return array Restircted roles list
	 */
	function filterEditRoles ( $roles )
	{
	    $this->generateNames();
        $valid = array_keys($this->roles);

        foreach ( $roles as $role => $caps ) {
            if ( ! in_array($role, $valid) ) {
                unset($roles[$role]);
            }
        }

        return $roles;
	}

	/**
	 * Checks if a user can be edited or not by current administrator.
	 * Returns array('do_not_allow') if user cannot be edited.
	 *
	 * @hook 'map_meta_cap' filter
	 *
	 * @param array $caps Current user capabilities
	 * @param string $cap Capability to check
	 * @param int $user_id Current user ID
	 * @param array $args For our purpose, we receive edited user id at $args[0]
	 * @return array Allowed capabilities.
	 */
	function filterUserEdit ( $caps, $cap, $user_id, $args )
	{
	    if ( 'edit_user' != $cap || $user_id == (int) $args[0] ) {
	        return $caps;
	    }

	    $this->generateNames();
	    $valid = array_keys($this->roles);

        $user = new WP_User( (int) $args[0] );
        foreach ( $user->roles as $role ) {
		    if ( ! in_array($role, $valid) ) {
		        $caps = array('do_not_allow');
		        break;
            }
		}

		return $caps;
	}

	/**
	 * Manages global settings admin.
	 *
	 * @hook add_submenu_page
	 * @return void
	 */
	function generalManager ()
	{
		if ( ! current_user_can('manage_capabilities') && ! current_user_can('administrator') ) {
            // TODO: Implement exceptions.
		    wp_die('<strong>' .__('What do you think you\'re doing?!?', $this->ID) . '</strong>');
		}

		global $wp_roles;
		$this->current = get_option('default_role');	// By default we manage the default role.

		if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
			check_admin_referer('capsman-general-manager');
			$this->processAdminGeneral();
		}

		$this->generateNames();
		$roles = array_keys($this->roles);

		if ( isset($_GET['action']) && 'delete' == $_GET['action']) {
			check_admin_referer('delete-role_' . $_GET['role']);
			$this->adminDeleteRole();
		}

		if ( ! in_array($this->current, $roles) ) {    // Current role has been deleted.
			$this->current = array_shift($roles);
		}

		include ( AK_CMAN_LIB . '/admin.php' );
	}

	/**
	 * Manages backup, restore and resset roles and capabilities
	 *
	 * @hook add_management_page
	 * @return void
	 */
	function backupTool ()
	{
		if ( ! current_user_can('manage_capabilities') && ! current_user_can('administrator') ) {
		    // TODO: Implement exceptions.
			wp_die('<strong>' .__('What do you think you\'re doing?!?', $this->ID) . '</strong>');
		}

		if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
			check_admin_referer('capsman-backup-tool');
			$this->processBackupTool();
		}

		if ( isset($_GET['action']) && 'reset-defaults' == $_GET['action']) {
			check_admin_referer('capsman-reset-defaults');
			$this->backupToolReset();
		}

		include ( AK_CMAN_LIB . '/backup.php' );
	}

	/**
	 * Processes and saves the changes in the general capabilities form.
	 *
	 * @return void
	 */
	private function processAdminGeneral ()
	{
		global $wp_roles;

		if (! isset($_POST['action']) || 'update' != $_POST['action'] ) {
		    // TODO: Implement exceptions. This must be a fatal error.
			ak_admin_error(__('Bad form Received', $this->ID));
			return;
		}

		$post = stripslashes_deep($_POST);
		if ( empty ($post['caps']) ) {
		    $post['caps'] = array();
		}

		$this->saveRoleCapabilities($post['current'], $post['caps'], $post['level']);
		$this->current = $post['current'];

		// Select a new role.
		if ( isset($post['Change']) && __('Change', $this->ID) == $post['Change'] ) {
			$this->current = $post['role'];

		// Create a new role.
		} elseif ( isset($post['Create']) && __('Create', $this->ID) == $post['Create'] ) {
			if ( $newrole = $this->createRole($post['create-name']) ) {
				ak_admin_notify(__('New role created.', $this->ID));
				$this->current = $newrole;
			} else {
				ak_admin_error(__('Error: Failed creating the new role.', $this->ID));
			}

		// Copy current role to a new one.
		} elseif ( isset($post['Copy']) && __('Copy', $this->ID) == $post['Copy'] ) {
			$current = get_role($post['current']);
			if ( $newrole = $this->createRole($post['copy-name'], $current->capabilities) ) {
				ak_admin_notify(__('New role created.', $this->ID));
				$this->current = $newrole;
			} else {
				ak_admin_error(__('Error: Failed creating the new role.', $this->ID));
			}

		// Save role changes. Already saved at start with self::saveRoleCapabilities().
		}elseif ( isset($post['Save']) && __('Save Changes', $this->ID) == $post['Save'] ) {
			ak_admin_notify(__('New capabilities saved.', $this->ID));

		// Create New Capability and adds it to current role.
		} elseif ( isset($post['AddCap']) &&  __('Add to role', $this->ID) == $post['AddCap'] ) {
			$role = get_role($post['current']);

			if ( $newname = $this->createNewName($post['capability-name']) ) {
				$role->add_cap($newname['name']);
				ak_admin_notify(__('New capability added to role.', $this->ID));
			} else {
				ak_admin_error(__('Incorrect capability name.', $this->ID));
			}
		} else {
		    // TODO: Implement exceptions. This must be a fatal error.
		    ak_admin_error(__('Bad form received.', $this->ID));
		}
	}

	/**
	 * Processes backups and restores.
	 *
	 * @return void
	 */
	private function processBackupTool ()
	{
		if ( isset($_POST['Perform']) ) {
			global $wpdb;
			$wp_roles = $wpdb->prefix . 'user_roles';
			$cm_roles = $this->ID . '_backup';

			switch ( $_POST['action'] ) {
				case 'backup':
					$roles = get_option($wp_roles);
					update_option($cm_roles, $roles);
					ak_admin_notify(__('New backup saved.', $this->ID));
					break;
				case 'restore':
					$roles = get_option($cm_roles);
					if ( $roles ) {
						update_option($wp_roles, $roles);
						ak_admin_notify(__('Roles and Capabilities restored from last backup.', $this->ID));
					} else {
						ak_admin_error(__('Restore failed. No backup found.', $this->ID));
					}
					break;
			}
		}
	}

	/**
	 * Deletes a role.
	 * The role comes from the $_GET['role'] var and the nonce has already been checked.
	 * Default WordPress role cannot be deleted and if trying to do it, throws an error.
	 * Users with the deleted role, are moved to the WordPress default role.
	 *
	 * @return void
	 */
	private function adminDeleteRole ()
	{
		global $wpdb;

		$this->current = $_GET['role'];
		$default = get_option('default_role');
		if (  $default == $this->current ) {
			ak_admin_error(sprintf(__('Cannot delete default role. You <a href="%s">have to change it first</a>.', $this->ID), 'options-general.php'));
			return;
		}

		$query = "SELECT ID FROM {$wpdb->usermeta} INNER JOIN {$wpdb->users} "
			. "ON {$wpdb->usermeta}.user_id = {$wpdb->users}.ID "
			. "WHERE meta_key='{$wpdb->prefix}capabilities' AND meta_value LIKE '%{$this->current}%';";

		$users = $wpdb->get_results($query);
		$count = count($users);

		foreach ( $users as $u ) {
			$user = new WP_User($u->ID);
			if ( $user->has_cap($this->current) ) {		// Check again the user has the deleting role
				$user->set_role($default);
			}
		}

		remove_role($this->current);
		unset($this->roles[$this->current]);

		ak_admin_notify(sprintf(__('Role has been deleted. %1$d users moved to default role %2$s.', $this->ID), $count, $this->roles[$default]));
		$this->current = $default;
	}

	/**
	 * Resets roles to WordPress defaults.
	 *
	 * @return void
	 */
	private function backupToolReset ()
	{
		require_once(ABSPATH . 'wp-admin/includes/schema.php');

		if ( ! function_exists('populate_roles') ) {
			ak_admin_error(__('Needed function to create default roles not found!', $this->ID));
			return;
		}

		$roles = array_keys($this->roles);
		foreach ( $roles as $role) {
			remove_role($role);
		}

		populate_roles();
		$this->setAdminCapability();

		ak_admin_notify(__('Roles and Capabilities reset to WordPress defaults', $this->ID));
	}

	/**
	 * Callback function to create names.
	 * Replaces underscores by spaces and uppercases the first letter.
	 *
	 * @access private
	 * @param string $cap Capability name.
	 * @return string	The generated name.
	 */
	function _capNamesCB ( $cap )
	{
		$cap = str_replace('_', ' ', $cap);
		$cap = ucfirst($cap);

		return $cap;
	}

	/**
	 * Generates an array with the system capability names.
	 * The key is the capability and the value the created screen name.
	 *
	 * @uses self::_capNamesCB()
	 * @return void
	 */
	private function generateSysNames ()
	{
		$this->max_level = 10;
		$this->roles = ak_get_roles(true);
		$caps = array();

		foreach ( array_keys($this->roles) as $role ) {
			$role_caps = get_role($role);
			$caps = array_merge($caps, $role_caps->capabilities);
		}

		$keys = array_keys($caps);
		$names = array_map(array($this, '_capNamesCB'), $keys);
		$this->capabilities = array_combine($keys, $names);

		$sys_caps = $this->getOption('syscaps');
		if ( is_array($sys_caps) ) {
			$this->capabilities = array_merge($sys_caps, $this->capabilities);
		}

		asort($this->capabilities);
	}

	/**
	 * Generates an array with the user capability names.
	 * If user has 'administrator' role, system roles are generated.
	 * The key is the capability and the value the created screen name.
	 * A user cannot manage more capabilities that has himself (Except for administrators).
	 *
	 * @uses self::_capNamesCB()
	 * @return void
	 */
	private function generateNames ()
	{
		if ( current_user_can('administrator') ) {
			$this->generateSysNames();
		} else {
		    global $user_ID;
		    $user = new WP_User($user_ID);
		    $this->max_level = ak_caps2level($user->allcaps);

		    $keys = array_keys($user->allcaps);
    		$names = array_map(array($this, '_capNamesCB'), $keys);
	    	$this->capabilities = array_combine($keys, $names);

		    $roles = ak_get_roles(true);
    		unset($roles['administrator']);

	    	foreach ( $user->roles as $role ) {			// Unset the roles from capability list.
		    	unset ( $this->capabilities[$role] );
			    unset ( $roles[$role]);					// User cannot manage his roles.
    		}
	    	asort($this->capabilities);

		    foreach ( array_keys($roles) as $role ) {
			    $r = get_role($role);
    			$level = ak_caps2level($r->capabilities);

	    		if ( $level > $this->max_level ) {
		    		unset($roles[$role]);
			    }
    		}

	    	$this->roles = $roles;
		}
	}

	/**
	 * Creates a new role/capability name from user input name.
	 * Name rules are:
	 * 		- 2-40 charachers lenght.
	 * 		- Only letters, digits, spaces and underscores.
	 * 		- Must to start with a letter.
	 *
	 * @param string $name	Name from user input.
	 * @return array|false An array with the name and display_name, or false if not valid $name.
	 */
	private function createNewName( $name ) {
		// Allow max 40 characters, letters, digits and spaces
		$name = trim(substr($name, 0, 40));
		$pattern = '/^[a-zA-Z][a-zA-Z0-9 _]+$/';

		if ( preg_match($pattern, $name) ) {
			$roles = ak_get_roles();

			$name = strtolower($name);
			$name = str_replace(' ', '_', $name);
			if ( in_array($name, $roles) || array_key_exists($name, $this->capabilities) ) {
				return false;	// Already a role or capability with this name.
			}

			$display = explode('_', $name);
			$display = array_map('ucfirst', $display);
			$display = implode(' ', $display);

			return compact('name', 'display');
		} else {
			return false;
		}
	}

	/**
	 * Creates a new role.
	 *
	 * @param string $name	Role name to create.
	 * @param array $caps	Role capabilities.
	 * @return string|false	Returns the name of the new role created or false if failed.
	 */
	private function createRole( $name, $caps = array() ) {
		$role = $this->createNewName($name);
		if ( ! is_array($role) ) {
			return false;
		}

		$new_role = add_role($role['name'], $role['display'], $caps);
		if ( is_object($new_role) ) {
			return $role['name'];
		} else {
			return false;
		}
	}

	 /**
	  * Saves capability changes to roles.
	  *
	  * @param string $role_name Role name to change its capabilities
	  * @param array $caps New capabilities for the role.
	  * @return void
	  */
	private function saveRoleCapabilities( $role_name, $caps, $level ) {

		$this->generateNames();
		$role = get_role($role_name);

		$old_caps = array_intersect_key($role->capabilities, $this->capabilities);
		$new_caps = ( is_array($caps) ) ? array_map('intval', $caps) : array();
		$new_caps = array_merge($new_caps, ak_level2caps($level));

		// Find caps to add and remove
		$add_caps = array_diff_key($new_caps, $old_caps);
		$del_caps = array_diff_key($old_caps, $new_caps);

		if ( ! current_user_can('administrator') ) {
			unset($add_caps['manage_capabilities']);
			unset($del_caps['manage_capabilities']);
		}

		if ( 'administrator' == $role_name && isset($del_caps['manage_capabilities']) ) {
			unset($del_caps['manage_capabilities']);
			ak_admin_error(__('You cannot remove Manage Capabilities from Administrators', $this->ID));
		}
		// Add new capabilities to role
		foreach ( $add_caps as $cap => $grant ) {
			$role->add_cap($cap);
		}

		// Remove capabilities from role
		foreach ( $del_caps as $cap => $grant) {
			$role->remove_cap($cap);
		}
	}

	protected function pluginDeactivate() {}
    protected function pluginsLoaded() {}
    protected function registerWidgets() {}
    public function wpInit() {}
}
