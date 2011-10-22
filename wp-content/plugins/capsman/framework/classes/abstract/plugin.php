<?php
/**
 * Plugins related functions and classes.
 *
 * @version		$Rev: 203758 $
 * @author		Jordi Canals
 * @copyright   Copyright (C) 2008, 2009, 2010 Jordi Canals
 * @license		GNU General Public License version 2
 * @link		http://alkivia.org
 * @package		Alkivia
 * @subpackage	Framework
 *

	Copyright 2008, 2009, 2010 Jordi Canals <devel@jcanals.cat>

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

require_once ( AK_CLASSES . '/abstract/module.php' );

/**
 * Abtract class to be used as a plugin template.
 * Must be implemented before using this class and it's recommended to prefix the class to prevent collissions.
 * There are some special functions that have to be declared in implementations to perform main actions:
 * 		- pluginActivate (Protected) Actions to run when activating the plugin.
 * 		- pluginDeactivate (Protected) Actions to run when deactivating the plugin.
 * 		- pluginUpdate (Protected) Actions to update the plugin to a new version. (Updating version on DB is done after this).
 * 						Takes plugin running version as a parameter.
 *		- pluginsLoaded (Protected) Runs at 'plugins_loaded' action hook.
 *		- registerWidgets (Protected) Runs at 'widgets_init' action hook.
 *
 * @author		Jordi Canals
 * @package		Alkivia
 * @subpackage	Framework
 * @link		http://wiki.alkivia.org/framework/classes/plugin
 */
abstract class akPluginAbstract extends akModuleAbstract
{
    /**
	 * Holds the installed and active components.
	 * @var array
	 */
	private $components = false;

    /**
	 * Class constructor.
	 * Calls the implementated method 'startUp' if it exists. This is done at plugins loading time.
	 * Prepares admin menus by seting an action for the implemented method '_adminMenus' if it exists.
	 *
	 * @param string $mod_file	Full main plugin's filename (absolute to root).
	 * @param string $ID  Plugin short name (known as plugin ID).
	 * @return spostsPlugin|false	The plugin object or false if not compatible.
	 */
	public function __construct( $mod_file, $ID = '' )
	{
        parent::__construct('plugin', $ID, $mod_file);

		if ( $this->isCompatible() ) {
		    // Activation and deactivation hooks.
			register_activation_hook($this->mod_file, array($this, 'activate'));
			register_deactivation_hook($this->mod_file, array($this, 'deactivate'));

			add_action('plugins_loaded', array($this, 'init'));
		}
	}

	/**
	 * Fires on plugin activation.
	 * @return void
	 */
	protected function pluginActivate () {}

	/**
	 * Fires on plugin deactivation.
	 * @return void
	 */
	protected function pluginDeactivate () {}

	/**
	 * Updates the plugin to a new version.
	 * @param string $version Old plugin version.
	 * @return void
	 */
	protected function pluginUpdate ( $version ) {}

	/**
	 * Fires when plugins have been loaded.
	 * @return void
	 */
	protected function pluginsLoaded () {}

	/**
	 * Fires on Widgets init.
	 * @return void
	 */
	protected function registerWidgets () {}

	/**
	 * Allows to check if plugin is ready to load components on implementations.
	 * Overwrite this method and return true to load components, false to omit components load.
	 *
	 * @return boolean If components can be loaded or not.
	 */
	protected function readyForComponents ()
	{
	    return false;
	}

	/**
	 * Activates the plugin. Only runs on first activation.
	 * Saves the plugin version in DB, and calls the 'pluginActivate' method.
	 *
	 * @uses do_action() Calls 'ak_activate_<modID>_plugin' action hook.
	 * @hook register_activation_hook
	 * @access private
	 * @return void
	 */
	final function activate()
	{
        $this->pluginActivate();

        // Save options and version
		$this->cfg->saveOptions($this->ID);
		add_option($this->ID . '_version', $this->version);

		// Load and activate plugin components.
		$this->components = ak_get_installed_components($this->componentsPath(), true);
        if ( empty($this->components) ) {
            $this->components = false;
        } else {
            foreach ( $this->components as $id => $component ) {
			    if ( $component['Core'] ) {
				    require_once( $component['File'] );
    				do_action('ak_activate_' . $this->ID . '_' . $id);
	    			$this->components[$id]['active'] = 1;
		    	} else {
			    	$this->components[$id]['active'] = 0;
    			}
	    	}
		    update_option($this->ID . '_components', $this->components);
        }

        // Do activated hook.
		do_action('ak_activate_' . $this->ID . '_plugin');
	}

	/**
	 * Deactivates the plugin.
	 *
	 * @uses do_action() Calls 'ak_deactivate_<modID>_plugin' action hook.
	 * @hook register_deactivation_hook
	 * @access private
	 * @return void
	 */
	final function deactivate()
	{
	    $this->pluginDeactivate();
		do_action('ak_deactivate_' . $this->ID . '_plugin');
	}

	/**
	 * Init the plugin (In action 'plugins_loaded')
	 * Here whe call the 'pluginUpdate' and 'pluginsLoaded' methods.
	 * Also the plugin version and settings are updated here.
	 *
	 * @hook action plugins_loaded
	 * @uses do_action() Calls the 'ak_<modID>_updated' action hook.
	 *
	 * @access private
	 * @return void
	 */
	final function init()
	{
		// First, check if the plugin needs to be updated.
		if ( $this->needs_update ) {
			$version = get_option($this->ID . '_version');
			$this->pluginUpdate($version);

			$this->cfg->saveOptions($this->ID);
			update_option($this->ID . '_version', $this->version);

			$this->searchNewComponents();
			do_action('ak_' . $this->ID . '_updated');
		}

		$this->pluginsLoaded();
		$this->loadComponents();
	}

	/**
	 * Loads plugin components if found.
	 *
	 * @return void
	 */
	final function loadComponents()
	{
        if ( ! $this->readyForComponents() ) {
            return;
        }

        $this->components = get_option($this->ID . '_components');
		if ( ! is_array($this->components) ) {
		    return;
		}

		foreach ( $this->components as $component ) {
			if ( $component['active'] ) {
				require_once ( $component['File']);
			}
		}

		do_action('ak_' . $this->ID . '_components_init');
	}

	/**
	 * Reloads and updates installed components.
	 * If a new core component is found, it will be activated automatically.
	 *
	 * @return void
	 */
	private function searchNewComponents ()
	{
		$components	= ak_get_installed_components($this->componentsPath(), true);
		if ( empty($components) ) {
		    $this->components = false;
		    return;
		}

		$installed	= array();
		$core		= array();
		$optional	= array();

		// Sort components by core and optional. Then by name.
		foreach ( $components as $id => $component ) {
			if ( $component['Core'] ) {
				$core[$id] = $component;
			} else {
				$optional[$id] = $component;
			}
		}
		ksort($core); ksort($optional);	// Sort components by ID.
		$components = array_merge($core, $optional);

		// Now, activate new core components, and set activation for optional.
		$this->components = get_option($this->ID . '_components');
		foreach ( $components as $id => $component ) {
			$installed[$id] = $component;
			if ( $component['Core'] ) {
				$installed[$id]['active'] = 1;
				if ( ! isset($this->components[$id]) || ! $this->components[$id]['active'] ) {
					require_once( $component['File']);
					do_action('ak_activate_' . $this->ID . '_' . $id);
				}
			} else {
				if ( isset($this->components[$id]['active']) ) {
					$installed[$id]['active'] = $this->components[$id]['active'];
				} else {
                    $installed[$id]['active'] = 0;
				}
			}
		}

		$this->components = $installed;
		update_option($this->ID . '_components', $this->components);
	}

	/**
	 * Checks if a component is installed and active.
	 *
	 * @return boolean	If the component is active or not.
	 */
	public function activeComponent ( $name )
	{
	    if ( ! is_array($this->components) ) {
	        return false;
	    }

	    $name = strtolower($name);
		if ( isset($this->components[$name]) && $this->components[$name]['active'] ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Inits the widgets (In action 'widgets_init')
	 * Before loading the widgets, we check that standard sidebar is present.
	 *
	 * @hook action 'widgets_init'
	 * @return void
	 */
	final function widgetsInit()
	{
		if ( class_exists('WP_Widget') && function_exists('register_widget') && function_exists('unregister_widget') ) {
			$this->registerWidgets();
			do_action('ak_' . $this->ID . '_widgets_init');
		} else {
			add_action('admin_notices', array($this, 'noSidebarWarning'));
		}
	}

	/**
	 * Checks if the plugin is compatible with the current WordPress version.
	 * If it's not compatible, sets an admin warning.
	 *
	 * @return boolean	Plugin is compatible with this WordPress version or not.
	 */
	final public function isCompatible()
	{
		global $wp_version;

		if ( version_compare($wp_version, $this->mod_data['Requires'] , '>=') ) {
			return true;
		} elseif ( ! has_action('admin_notices', array($this, 'noCompatibleWarning')) ) {
			add_action('admin_notices', array($this, 'noCompatibleWarning'));
		}

		return false;
	}

	/**
	 * Shows a warning message when the plugin is not compatible with current WordPress version.
	 * This is used by calling the action 'admin_notices' in isCompatible()
	 *
	 * @hook action admin_notices
	 * @access private
	 * @return void
	 */
	final function noCompatibleWarning()
	{
		$this->loadTranslations(); // We have not loaded translations yet.

		echo '<div class="error"><p><strong>' . __('Warning:', 'akfw') . '</strong> '
			. sprintf(__('The active plugin %s is not compatible with your WordPress version.', 'akfw'),
				'&laquo;' . $this->mod_data['Name'] . ' ' . $this->version . '&raquo;')
			. '</p><p>' . sprintf(__('WordPress %s is required to run this plugin.', 'akfw'), $this->mod_data['Requires'])
			. '</p></div>';
	}

	/**
	 * Shows an admin warning when not using the WordPress standard sidebar.
	 * This is done by calling the action 'admin_notices' in isStandardSidebar()
	 *
	 * @hook action admin_notices
	 * @access private
	 * @return void
	 */
	final function noSidebarWarning()
	{
		$this->loadTranslations(); // We have not loaded translations yet.

		echo '<div class="error"><p><strong>' . __('Warning:', $this->ID) . '</strong> '
			. __('Standard sidebar functions are not present.', $this->ID) . '</p><p>'
			. sprintf(__('It is required to use the standard sidebar to run %s', $this->ID),
				'&laquo;' . $this->mod_data['Name'] . ' ' . $this->version . '&raquo;')
			. '</p></div>';
	}

    /**
     * Loads plugins data.
     *
     * @return void
     */
	final protected function loadData()
	{
		if ( empty($this->mod_data) ) {
			if ( ! function_exists('get_plugin_data') ) {
				require_once ( ABSPATH . 'wp-admin/includes/plugin.php' );
			}

			$plugin_data = get_plugin_data($this->mod_file);
			$readme_data = ak_module_readme_data($this->mod_file);
			$this->mod_data = array_merge($readme_data, $plugin_data);

			$this->version = $this->mod_data['Version'];
		}
	}

	/**
	 * Returns the path to plugin components.
	 *
	 * @uses apply_filters() Applies the ak_<plugin>_components_path filter on default path.
	 * @return string Path to components directory.
	 */
	final protected function componentsPath ()
	{
	    $path = dirname($this->mod_file) . '/components';
	    return apply_filters('ak_' . $this->ID . '_components_path', $path);
	}

	/**
	 * Form part to activate/deactivate plugin components.
	 * To be included on other configuration or settings form.
	 * String for component name and description have to be included on plugin text_domain.
	 *
	 * @return void
	 */
	final protected function componentActivationForm ()
	{
	    if ( $this->getOption('disable-components-activation') ) {
	        return;
	    }

        $this->searchNewComponents();
        ?>
		<dl>
			<dt><?php _e('Activate Components', 'akfw'); ?></dt>
			<dd>
				<?php wp_nonce_field('ak-component-activation', '_aknonce', false); ?>
				<table width="100%" class="form-table">
				<?php foreach ( $this->components as $c) :
					if ( ! $c['Core'] ) : ?>
					<tr>
						<th scope="row"><?php _e($c['Name'], $this->ID) ?>:</th>
						<td>
							<input type="radio" name="components[<?php echo $c['Component']; ?>]" value="1" <?php checked(1, $c['active']); ?> /> <?php _e('Yes', 'akfw'); ?> &nbsp;&nbsp;
							<input type="radio" name="components[<?php echo $c['Component']; ?>]" value="0" <?php checked(0, $c['active']); ?> /> <?php _e('No', 'akfw'); ?> &nbsp;&nbsp;
							<span class="setting-description"><?php _e($c['Description'], $this->ID); ?></span>
						</td>
					</tr>
					<?php endif;
				endforeach; ?>
				</table>
			</dd>
		</dl>
		<?php
	}

	/**
	 * Saves data from componets activation form.
	 * Activates or deactivates components as requested by user.
	 *
	 * @return void
	 */
	final protected function saveActivationForm ()
	{
	    if ( $this->getOption('disable-components-activation') ) {
	        return;
	    }

	    check_admin_referer('ak-component-activation', '_aknonce');
		if ( isset($_POST['action']) && 'update' == $_POST['action'] ) {
			$post = stripslashes_deep($_POST['components'] );
			$this->components = get_option($this->ID . '_components');
			$this->searchNewComponents();

			foreach ( $post as $name => $activate ) {
				if ( $activate && ! $this->components[$name]['active'] ) {
					require_once( $this->components[$name]['File']);
					do_action('ak_activate_' . $this->ID . '_' . $name);
				} elseif ( ! $activate && $this->components[$name]['active'] ) {
					require_once( $this->components[$name]['File']);
					do_action('ak_deactivate_' . $this->ID . '_' . $name);
				}
				$this->components[$name]['active'] = $activate;
			}
			update_option($this->ID . '_components', $this->components);
		} else {
			wp_die('Bad form received.', $this->ID);
		}
	}
}
