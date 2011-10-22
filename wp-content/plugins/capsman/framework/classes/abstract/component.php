<?php
/**
 * Class Component.
 * Manages the main functionality for all components.
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
 * Abtract class to be used as a component template.
 * There are some special functions thay can declared in implementations to perform main actions:
 * 		- componentActivate: (Protected) Actions to run when activating the component.
 * 		- componentDeactivate: (Hook, must be public) Actions to run when deactivating the component.
 * 		- componentUpdate: (Protected) Actions to update the component to a new version. (Updating version on DB is done after this).
 * 		- componentsLoaded: (Protected) Actions to run when components initialization is performed (In plugins loaded).
 * 		- registerWidgets: (Protected) Actions to init plugin widgets (In widgets_init).
 *
 * @since 		0.7
 * @author		Jordi Canals
 * @package		AOC
 * @subpackage	Library
 * @link		http://alkivia.org
 */
abstract class akComponentAbstract extends akModuleAbstract
{
    /**
     * Parent plugin slug.
     * This is used on menus and form actions.
     *
     * @var string
     */
    protected $slug;

	/**
	 * Class constructor.
	 * Calls the implementated method 'startUp' if it exists. This is done at plugins loading time.
	 * Prepares admin menus by seting an action for the implemented method '_adminMenus' if it exists.
	 *
	 * @param string $file The main file to run the component.
	 * @return aocComponent The component object.
	 */
	final function __construct ( $file )
	{
	    parent::__construct('component', '', $file);
        $this->slug = ak_get_object($this->PID)->getSlug();

		// Activating and deactivating hooks.
		add_action('ak_activate_' . $this->ID, array($this, 'activate'));
		add_action('ak_deactivate_'  . $this->ID, array($this, 'componentDeactivate'));

		add_action('ak_' . $this->PID . '_components_init', array($this, 'init'));
		add_action('ak_' . $this->PID . '_widgets_init', array($this, 'widgetsInit'));
	}

	/**
	 * Fires on plugin activation.
	 * @return void
	 */
	protected function componentActivate () {}

	/**
	 * Fires on plugin deactivation.
	 * @return void
	 */
	public function componentDeactivate () {}

	/**
	 * Updates the plugin to a new version.
	 * @param string $version Old component version.
	 * @return void
	 */
	protected function componentUpdate ( $version ) {}

	/**
	 * Fires when plugins have been loaded.
	 * @return void
	 */
    protected function componentsLoaded () {}

	/**
	 * Fires on Widgets init.
	 * @return void
	 */
	protected function registerWidgets () {}

	/**
	 * Activates the Component.
	 * Sets the plugin version in the component settings to be saved to DB.
	 *
	 * @hook action aoc_activate_$component
	 * @access private
	 * @return void
	 */
	final function activate()
	{
        $this->componentActivate();
        add_option($this->ID . '_version', $this->version);
	}

	/**
	 * Inits the component.
	 * Here whe call the 'update' and 'init' functions. This is done after the components are loaded.
	 * Also the component version is updated here.
	 *
	 * @hook action aoc_components_init
	 * @access private
	 * @return void
	 */
	final function init ()
	{
		if ( $this->needs_update ) {
			$this->componentUpdate( $this->getOption('version') );
            update_option($this->ID . '_version', $this->version);
		}

		// Call the custom init for the component.
		$this->componentsLoaded();
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
		} else {
			add_action('admin_notices', array($this, 'noSidebarWarning'));
		}
	}

	/**
	 * Loads component data.
	 * As it is a child component, data is loaded into akModuleAbstract::child_data
	 *
	 * @return void
	 */
	final protected function loadData()
	{
	    if ( empty( $this->child_data) ) {
			$component_data = ak_component_data($this->mod_file, true);
			$readme_data = ak_module_readme_data($this->mod_file);
			$this->child_data = array_merge($readme_data, $component_data);

		    $this->PID = $this->child_data['Parent'];
	    	$this->mod_data = ak_get_object($this->PID)->getModData();

	    	if ( empty( $this->child_data['Version']) ) {
	    	    $this->version = $this->mod_data['Version'];
	    	} else {
	    	    $this->version = $this->child_data['Version'];
	    	}
	    }
	}
}
