<?php
/**
 * Class for Themes management.
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
 * Abtract class to be used as a theme template.
 * Must be implemented before using this class.
 * There are some special functions that have to be declared in implementations to perform main actions:
 *		- themeInit (Protected) Runs as soon as theme has been loaded.
 *		- themeInstall (Protected) Runs at theme install time and fires on the 'init' action hook.
 *		- themeUpdate (Proetected) Runs at theme update and fires on the 'init' action hook.
 *		- themeSideBars (Protected) Runs at theme load time and used to register the theme sidebars.
 *
 * @author		Jordi Canals
 * @package		Alkivia
 * @subpackage	Framework
 * @link		http://wiki.alkivia.org/framework/classes/theme
 *
 * @uses		akSettings
 */

abstract class akThemeAbstract extends akModuleAbstract
{
	/**
	 * Class constructor.
	 * Calls the implementated method 'startUp' if it exists. This is done at theme's loading time.
	 * Prepares admin menus by setting an action for the implemented method '_adminMenus' if it exists.
	 *
	 * @uses do_action() Calls the 'ak_theme_loaded' action hook.
	 * @param string $ID  Theme internal short name (known as theme ID).
	 * @return akTheme
	 */
	public function __construct ( $ID = '' )
	{
        parent::__construct('theme', $ID);

        if ( $this->installing ) {
            $this->install();
        }

        if ( function_exists('register_sidebars') ) {
		    $this->themeSideBars();
		}

        $this->configureTheme();
		do_action('ak_theme_loaded');
	}

	/**
	 * Inits the theme at WordPress 'init' action hook
	 * @return void
	 */
    protected function themeInit () {}

    /**
     * Installs the theme.
     * @return void
     */
	protected function themeInstall () {}

	/**
	 * Performs additional actions to update the theme.
	 * @param string $version Theme current installed version.
	 * @return void
	 */
	protected function themeUpdate ( $version ) {}

	/**
	 * Registers and sets theme sidebars.
	 * @return void
	 */
    protected function themeSideBars () {}

	/**
	 * Configure the theme based on theme settings.
	 *
	 * @uses do_action() Calls the 'ak_theme_options_set' action hook.
	 * @return void
	 */
    final private function configureTheme()
    {
        // Set metatags
		add_action('wp_head', array($this, 'metaTags') );

		// Set the theme favicon
        if ( ! $this->getOption('disable-favicon') ) {
            add_action('wp_head', array($this, 'favicon'));
            add_action('admin_head', array($this, 'favicon'));
        }

        // Enable self ping.
        if ( ! $this->getOption('enable-selfping') ) {
    		add_action('pre_ping', array($this, 'disableSelfPing'));
        }

        do_action('ak_theme_options_set');
    }

	/**
	 * Installs the theme.
	 * Saves the theme version in DB, and calls the 'install' method.
	 *
	 * @uses do_action() Calls the 'ak_theme_installed' action hook.
	 * @return void
	 */
    final private function install ()
    {
		// If there is an additional function to perform on installation.
		$this->themeInstall();

		// Save options and version
		$this->cfg->saveOptions($this->ID);
		add_option($this->ID . '_version', $this->version);

		do_action('ak_theme_installed');
    }

	/**
	 * Init the theme (In action 'init')
	 * Here whe call the 'themeUpdate' and 'themeInit' methods. This is done after the plugins are loaded.
	 * Also the theme version and settings are updated here.
	 *
	 * @uses do_action() Calls the 'ak_theme_updated' action hook.
	 * @hook action 'init'
	 * @access private
	 * @return void
	 */
	final function wpInit ()
	{
		// Check if the module needs to be updated.
		if ( $this->needs_update ) {
			$version = get_option($this->ID . '_version');
			$this->themeUpdate($version);

			$this->cfg->saveOptions($this->ID);
			update_option($this->ID . '_version', $this->version);

			do_action('ak_theme_updated');
		}

		// Call the custom init for the theme when system is loaded.
		$this->themeInit();
	}

    /**
	 * Inits the widgets (In action 'widgets_init')
	 * In own themes standard sidebar always will be present (No check needed).
	 *
	 * @hook action 'widgets_init'
	 * @access private
	 * @return void
	 */
	final function widgetsInit ()
	{
		do_action('ak_theme_widgets_init');
	}

	/**
	 * Disables self pings.
	 * This will disable sending pings to our own blog.
	 *
	 * @author	Michael D. Adams
	 * @link 	http://blogwaffe.com/2006/10/04/421/
	 * @version	0.2
	 * @hook action 'pre_ping'
	 * @param array $links	Link list of URLs to ping.
	 */
    final function disableSelfPing( &$links )
    {
    	$home = get_option( 'home' );
	    foreach ( $links as $l => $link ) {
		    if ( 0 === strpos( $link, $home ) ) {
			    unset($links[$l]);
		    }
	    }
    }

	/**
	 * Sets the favicon for the theme.
	 *
	 * @uses apply_filters() Calls apply_filters with the 'ak_theme_favicon' hook and the favicon url as content.
	 * @hook actions 'wp_head' and 'admin_head'
	 * @access private
	 * @return void
	 */
	final function favicon ()
	{
	    $file = '/images/favicon.ico';
	    $favicon = $this->getOption('favicon-url');

	    if ( false === $favicon ) {
	        if ( TEMPLATEPATH !== STYLESHEETPATH && file_exists(STYLESHEETPATH . $file) ) {
                $favicon = get_stylesheet_directory_uri() . $file;
            } elseif ( file_exists(TEMPLATEPATH . $file) ) {
                $favicon = get_template_directory_uri() . $file;
            } else {
                $favicon = '';
            }
	    }

        $favicon = apply_filters('ak_theme_favicon', $favicon);
        if ( ! empty($favicon) ) {
            echo '<link rel="shortcut icon" href="' . $favicon . '" />' . PHP_EOL;
        }
	}

	/**
	 * Adds meta names for parent and child themes to head.
	 *
	 * @hook action 'wp_head'
	 * @access private
	 * @return void
	 */
	final function metaTags()
	{
	    echo '<meta name="theme" content="'
	        . $this->getModData('Name') . ' ' . $this->getModData('Version') . '" />' . PHP_EOL;

	    if ( $this->isChildTheme() ) {
	    echo '<meta name="child_theme" content="'
	        . $this->getChildData('Name') . ' ' . $this->getChildData('Version') . '" />' . PHP_EOL;
	    }
	}

    /**
     * Loads theme (and child) data.
     *
     * @return void
     */
	final protected function loadData()
	{
		$readme_data = ak_module_readme_data(TEMPLATEPATH . '/readme.txt');
	    if ( empty($this->mod_data) ) {
		    $theme_data = get_theme_data(TEMPLATEPATH . '/style.css');
			$this->mod_data = array_merge($readme_data, $theme_data);
		}

		if ( TEMPLATEPATH !== STYLESHEETPATH && empty($this->child_data) ) {
		    $this->mod_type = self::CHILD_THEME;
            $child_data = get_theme_data(STYLESHEETPATH . '/style.css');
			$child_readme_data = ak_module_readme_data(STYLESHEETPATH . '/readme.txt');
			$this->child_data = array_merge($readme_data, $child_readme_data, $child_data);
		}

		$this->version = $this->mod_data['Version'];
	}
}
