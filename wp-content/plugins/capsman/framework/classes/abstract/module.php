<?php
/**
 * Class for Modules management.
 * Modules are Plugins, Themes and plugin components.
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

/**
 * Abtract class to be used as a theme template.
 * Must be implemented before using this class.
 * There are some special functions that have to be declared in implementations to perform main actions:
 * 		- moduleLoad (Protected) Additional actions to be run at module load time.
 *		- defaultOptions (Protected) Returns the module default options.
 *		- widgetsInit (Public) Runs at 'widgets_init' action.
 *		- wpInit (Public) Runs at 'init' action.
 *		- adminMenus (Public) Runs at 'admin_menus' option, used to set admin menus and page options.
 *
 * @author		Jordi Canals
 * @package		Alkivia
 * @subpackage	Framework
 * @link		http://wiki.alkivia.org/framework/classes/module
 *
 * @uses		akSettings
 */

abstract class akModuleAbstract
{
	/**
	 * Module ID. Is the module internal short name.
	 * Filled in constructor (as a constructor param). Used for translations textdomain.
	 *
	 * @var string
	 */
	public $ID;

	/**
	 * Component parent ID.
	 * Used only for components.
	 *
	 * @var string
	 */
	public $PID = '';

	/**
	 * Module version number.
	 *
	 * @since 0.8
	 *
	 * @var string
	 */
	public $version;

	/**
	 * Module Type using a class constant: self::PLUGIN, self::COMPONENT, seelf::THEME, self::CHILD_THEME
	 * By default is set to 0 (unknown).
	 *
	 * @var int
	 */
	protected $mod_type = 0;

	/**
	 * Full path to module main file.
	 * Main file is 'style.css' for themes and the php file with data header for plugins and components.
	 *
	 * @var string
	 */
	protected $mod_file;

	/**
	 * URL to the module folder.
	 *
	 * @var string
	 */
	protected $mod_url;

	/**
	 * Module data. Readed from the main plugin file header and the readme file.
	 * Filled in loadModuleData(). Called in constructor.
	 * From the filename:
	 * 		- 'ID' - Module internal short name. Taken from main module's file name.
	 * From themes style.css file header:
	 *		- 'Name' - Name of the theme.
	 *		- 'Title' - Long title for the theme. As WP 2.8 is the same as 'Name'.
	 *		- 'URI' - Theme's page URI.
	 *		- 'Description' - Description of theme's features.
	 *		- 'Author' - Author name and link (to author home).
	 *		- 'Version' - Theme Version number.
	 *		- 'Template' - The parent theme (If this is a child theme).
	 *		- 'Tags' - An array of theme tags features.
	 * From plugins file header:
	 *		- 'Name' - Name of the plugin, must be unique.
	 *		- 'Title' - Title of the plugin and the link to the plugin's web site.
	 *		- 'Description' - Description of what the plugin does and/or notes from the author.
	 *		- 'Author' - The author's name
	 *		- 'AuthorURI' - The author's web site address.
	 *		- 'Version' - The plugin's version number.
	 *		- 'PluginURI' - Plugin's web site address.
	 *		- 'TextDomain' - Plugin's text domain for localization.
	 *		- 'DomainPath' - Plugin's relative directory path to .mo files.
	 * From readme.txt file :
	 * 		- 'Contributors' - An array with all contributors nicknames.
	 * 		- 'Tags' - An array with all plugin tags.
	 * 		- 'DonateURI' - The donations page address.
	 *      - 'Requires' - Minimum required WordPress version.
	 *      - 'Tested' - Higher WordPress version this plugin has been tested.
	 *      - 'Stable' - Last stable tag when this was released.
	 *
	 * @var array
	 */
	protected $mod_data;

	/**
	 * Same as $mod_data but for child themes and components.
	 *
	 * From the Component file:
	 * 		- 'File' - FileName of the component (relative to plugin's folder).
	 * From componets file header:
	 * 		- 'File' - The component filename, relative to the plugin folder.
	 * 		- 'Component' - The component short name or ID.
	 * 		- 'Name' - Descriptive name for the component.
	 * 		- 'Description' - A descriptive text about the component.
	 * 		- 'Author' - Component author name
	 * 		- 'URL' - Author homepage URL.
	 * 		- 'Link' - Author anchor to home page.
	 * 		- 'Core' - If this is a core compoment or not.
	 * 		- 'Version' - Component version number.
	 * From readme.txt file:
	 * 		- Same as seen on akModuleAbstract::mod_data.
	 * From child themes file header:
	 * 		- Same as seen on akModuleAbstract::mod_data for themes.
	 *
	 * @var array
	 */
	protected $child_data = array();

	/**
	 * Theme saved data.
	 *		- 'post' - Saves the current post.
	 *		- 'more' - Saves the read more status.
	 *
	 * @var array
	 */
	protected $saved;

	/**
	 * Holds a reference to the global 'settings' object.
	 * This object has been created on the framework loader.
	 *
	 * @var akSettings
	 */
	protected $cfg;

	/**
	 * Flag to see if we are installing (activating for first time) or reactivating the module.
	 *
	 * @var boolean
	 */
	protected $installing = false;

	/**
	 * Flag to see if module needs to be updated.
	 *
	 * @var boolean
	 */
	protected $needs_update = false;

	/** Constant used to define module as plugin. */
	const PLUGIN      = 10;

	/** Constant used to define module as component. */
	const COMPONENT   = 15;

	/** Constant used to define module as theme. */
	const THEME       = 20;

	/** Constant used to define module as child theme. */
	const CHILD_THEME = 25;

	/**
	 * Class constructor.
	 * Calls the implementated method 'startUp' if it exists. This is done at theme's loading time.
	 * Prepares admin menus by setting an action for the implemented method '_adminMenus' if it exists.
	 *
	 * @param string $type Module type. Must be one of 'plugin', 'component', 'theme'. (child themes are detected).
	 * @param string $ID  Theme internal short name (known as theme ID).
	 * @param string $file Module file. Only for plugins and components. (For themes style.css is always used).
	 * @return akTheme
	 */
	public function __construct ( $type = '', $ID = '', $file = '' )
	{
	    $this->cfg =& ak_settings_object();

	    switch ( strtolower($type) ) {
	        case 'plugin' :
	            $this->mod_type = self::PLUGIN;
                $this->mod_file = trim($file);
	            break;
	        case 'component' :
	            $this->mod_type = self::COMPONENT;
                $this->mod_file = trim($file);
	            break;
	        case 'theme' :
	            $this->mod_type = self::THEME;
    	        $this->mod_file = STYLESHEETPATH . '/style.css';
	            break;
	        default:
	            $this->mod_type = 0; // Unknown.
	    }

        $this->loadModuleData($ID);
        if ( $this->isCompatible() ) {
            add_action('init', array($this, 'systemInit'));
            add_action('plugins_loaded', array($this, 'pluginsInit'));
		    add_action('widgets_init', array($this, 'widgetsInit'));

		    if ( ! apply_filters('ak_' . $this->ID . '_disable_admin', $this->getOption('disable-admin-page')) ) {
    		    add_action('admin_menu', array($this, 'adminMenus'));
		    }

            // Load styles
			if ( is_admin() ) {
				add_action('admin_print_styles', array($this, 'adminStyles'));
			} else {
				add_action('wp_print_styles', array($this, 'enqueueStyles'));
			}

            $this->moduleLoad();
        }
	}

	/**
	 * Executes as soon as module class is loaded.
	 *
	 * @return void
	 */
	protected function moduleLoad() {}

	/**
	 * Prepares and returns default module options.
	 *
	 * @return array Options array
	 */
	protected function defaultOptions()
	{
	    return array();
	}

	/**
	 * Fires at 'widgets_init' action hook
	 *.
	 * @return void
	 */
	public function widgetsInit () {}

	/**
	 * Fires at 'init' action hook.
	 *
	 * @return void
	 */
    protected function wpInit () {}

    /**
     * Fires at 'admin_menus' action hook.
     *
     * @return void
     */
    public function adminMenus () {}

	/**
	 * Dummy method provided to check additional WP compatibility on inplementations.
	 * This is mostly used on plugins to check for WordPress required version.
	 *
	 * @return boolean
	 */
	public function isCompatible ()
	{
	    return true;
	}

    /**
     * Loads module data.
     * Loads different data for Plugin, Theme or Component.
     *
     * @return void
     */
	abstract protected function loadData ();

    /**
     * Functions to execute at system Init.
     *
	 * @hook action 'init'
	 * @access private
	 *
	 * @return void
	 */
    final function systemInit ()
    {
        switch ( $this->mod_type ) {
            case self::CHILD_THEME :
    		    load_theme_textdomain('akchild', STYLESHEETPATH . '/lang');
            case self::THEME :
                load_theme_textdomain('aktheme', TEMPLATEPATH . '/lang');
                break;
        }

        $this->wpInit();
    }

    /**
     * Functions to execute after loading plugins.
     *
     * @return void
     */
    final function pluginsInit ()
    {
        switch ( $this->mod_type ) {
            case self::PLUGIN :
		        load_plugin_textdomain($this->ID, false, basename(dirname($this->mod_file)) . '/lang');
		        break;
            case self::COMPONENT :
                // TODO: Manage components translations.
                break;
        }

    }

    /**
     * Enqueues additional administration styles.
     * Send the framework admin.css file and additionally any other admin.css file
     * found on the module direcotry.
     *
     * @hook action 'admin_print_styles'
     * @uses apply_filters() Calls the 'ak_framework_style_admin' filter on the framework style url.
     * @uses apply_filters() Calls the 'ak_<Mod_ID>_style_admin' filter on the style url.
     * @access private
     *
     * @return void
     */
    final function adminStyles()
    {
		// FRAMEWORK admin styles.
		$url = apply_filters('ak_framework_style_admin', AK_STYLES_URL . '/admin.css');
		if ( ! empty($url) ) {
   			wp_register_style('ak_framework_admin', $url, false, get_option('ak_framework_version'));
   			wp_enqueue_style('ak_framework_admin');
    	}

        // MODULE admin styles.
        if ( $this->isChildTheme() && file_exists(STYLESHEETPATH . '/admin.css') ) {
		    $url = get_stylesheet_directory_uri() . '/admin.css';
		} elseif ( $this->isTheme() && file_exists(TEMPLATEPATH . '/admin.css') ) {
			$url = get_template_directory_uri() . '/admin.css';
		} elseif ( file_exists(dirname($this->mod_file) . '/admin.css') ) {
		    $url = $this->mod_url . '/admin.css';
		} else {
		    $url = '';
		}

		$url = apply_filters('ak_' . $this->ID . '_style_admin', $url);
		if ( ! empty($url) ) {
   			wp_register_style('ak_' . $this->ID . '_admin', $url, array('ak_framework_admin'), $this->version);
   			wp_enqueue_style('ak_' . $this->ID . '_admin');
    	}
    }

    /**
     * Enqueues additional styles for plugins and components.
     * For themes no styles are enqueued as them are already sent by WP.
     *
     * @hook action 'wp_print_styles'
     * @uses apply_filters() Calls the 'ak_<Mod_ID>_style_url' filter on the style url.
     * @access private
     *
     * @return void
     */
    final function enqueueStyles()
    {
        if ( $this->isTheme() || $this->getOption('disable-module-styles') ) {
            return;
        }

        $url = $this->getOption('style-url', false);
        if ( false === $url ) {
            if ( file_exists(dirname($this->mod_file) . '/style.css') ) {
    		    $url = $this->mod_url . '/style.css';
	    	} else {
		        $url = '';
    		}
        }

		$url = apply_filters('ak_' . $this->ID . '_style_url', $url);
		if ( ! empty($url) ) {
   			wp_register_style('ak_' . $this->ID, $url, false, $this->version);
   			wp_enqueue_style('ak_' . $this->ID);
    	}
    }

   	/**
   	 * Checks if current module is a Plugin.
   	 * @return boolean
   	 */
   	final public function isPlugin()
   	{
        return ( self::PLUGIN == $this->mod_type ) ? true : false;
   	}

   	/**
   	 * Checks if current module is a Component.
   	 * @return boolean
   	 */
   	final public function isComponent()
   	{
        return ( self::COMPONENT == $this->mod_type ) ? true : false;
   	}

   	/**
   	 * Checks if current module is a Theme (or child)
   	 * @return boolean
   	 */
   	final public function isTheme()
   	{
        return ( self::THEME == $this->mod_type || self::CHILD_THEME == $this->mod_type ) ? true : false;
   	}

   	/**
   	 * Checks if current module is a child theme.
   	 * @return boolean
   	 */
   	final public function isChildTheme()
   	{
        return ( self::CHILD_THEME == $this->mod_type ) ? true : false;
   	}

  	/**
   	 * Returns a module option.
   	 * If no specific option is requested, returns all options.
   	 * If requested a non existent settings, returns $default.
   	 *
   	 * @param $name	Name for the option to return.
   	 * @param $default Default value to use if the option does not exists.
   	 * @return mixed	The option's value or an array with all options.
   	 */
   	final public function getOption ( $name = '', $default = false )
   	{
   		return $this->cfg->getSetting($this->ID, $name, $default);
   	}

   	/**
   	 * Updates a module option.
   	 *
   	 * @param string $name Option Name
   	 * @param mixed $value Option value
   	 * @return void
   	 */
   	final public function updateOption ( $name, $value )
   	{
   	    $this->cfg->updateOption($this->ID, $name, $value );
   	}

   	/**
   	 * Deletes a module option.
   	 *
   	 * @param string $name Option Name
   	 * @return void
   	 */
   	final public function deleteOption( $name )
   	{
        $this->cfg->deleteOption($this->ID, $name);
   	}

    /**
     * Merges new options into module settings.
     * Replaces exsisting ones and adds new ones.
     *
     * @since 0.7
     *
     * @param array $options New options to merge into settings.
     * @return void
     */
   	final protected function mergeOptions ( $options )
   	{
   	    $current = $this->cfg->getSetting($this->ID);
   	    $new_opt = array_merge($current, $options);
        $this->cfg->replaceOptions($this->ID, $new_opt);
   	}

   	/**
   	 * Replaces ALL module options by new ones.
   	 *
   	 * @param array $options Array with all options pairs (name=>value)
   	 * @return void
   	 */
   	final public function setNewOptions ( $options )
   	{
   	    $this->cfg->replaceOptions($this->ID, $options);
   	}

    /**
     * Returns current module default options.
     *
     * @since 0.7
     *
     * @return array Default module options.
     */
    final protected function getDefaults ()
    {
        $this->cfg->getDefaults($this->ID);
    }

    /**
     * Replaces current module defaults by new ones.
     *
     * @since 0.7
     *
     * @param array $options New default options-
     * @return void
     */
    final protected function setDefaults ( $options )
    {
        $this->cfg->setDefaults($this->ID, $options);
    }

    /**
     * Merges new options into module defaults.
     * Replaces exsisting ones and adds new ones.
     *
     * @since 0.7
     *
     * @param array $options New options to merge into defaults.
     * @return void
     */
    final protected function mergeDefaults ( $options )
    {
        $defaults = $this->cfg->getDefaults($this->ID);
        $new_def  = array_merge($defaults, $options);
        $this->cfg->setDefaults($this->ID, $new_def);
    }

   	/**
	 * Returns module data.
	 * This data is loaded from the main module file.
	 *
	 * @see akModuleAbstract::$mod_data
	 * @return mixed The parameter requested or an array wil all data.
	 */
	final public function getModData ( $name = '' )
	{
		if ( empty($name) ) {
			return $this->mod_data;
		} elseif ( isset( $this->mod_data[$name]) ) {
			return $this->mod_data[$name];
		} else {
			return false;
		}
	}

   	/**
	 * Returns child module data.
	 * This data is loaded from the child module file.
	 *
	 * @see akModuleAbstract::$child_data
	 * @return mixed The parameter requested or an array wil all data.
	 */
	final public function getChildData ( $name = '' )
	{
		if ( empty($name) ) {
			return $this->child_data;
		} elseif ( isset( $this->child_data[$name]) ) {
			return $this->child_data[$name];
		} else {
			return false;
		}
	}

	/**
	 * Checks if an option can be maneged on settings page.
	 * Looks at the alkivia.ini file and if set there, the option will be disabled.
	 *
	 * @param string|array $options Options names.
	 * @param boolean $show_notice Show a notice if disabled.
	 * @return boolean If administration is allowed or not.
	 */
	final public function allowAdmin( $options, $show_notice = true )
	{
	    foreach ( (array) $options as $option ) {
	        if ( ! $this->cfg->isForced($this->ID, $option) ) {
                return true;
	        }
	    }

	    if ( $show_notice ) {
	        echo '<em>' . __('Option blocked by administrator.', 'akfw') . '</em>';
	    }
	    return false;
	}

	/**
	 * Loads module data and settings.
	 * Data is loaded from the module file headers. Settings from Database and alkivia.ini.
	 *
	 * @return void
	 */
	final private function loadModuleData ( $id )
	{
	    $this->loadData();
	    switch ( $this->mod_type ) {
	        case self::PLUGIN :
                $this->mod_url = WP_PLUGIN_URL . '/' . basename(dirname($this->mod_file));
	            $this->ID = ( empty($id) ) ? strtolower(basename($this->mod_file, '.php')) : trim($id) ;
		        break;
	        case self::THEME :
	        case self::CHILD_THEME :
    	        $this->mod_url = get_stylesheet_directory_uri();
	            $this->ID = ( empty($id) ) ? strtolower(basename(TEMPLATEPATH)) : trim($id) ;
	            break;
	        case self::COMPONENT :
                $this->mod_url  = ak_get_object($this->PID)->getUrl() . 'components/' . basename(dirname($this->mod_file));
                $this->ID = $this->PID . '_' . $this->child_data['Component'];
                break;
	    }
   		$this->cfg->setDefaults($this->ID, $this->defaultOptions());

   		$old_version = get_option($this->ID . '_version');
		if ( false === $old_version ) {
			$this->installing = true;
		} elseif ( version_compare($old_version, $this->version, 'ne') ) {
			$this->needs_update = true;
		}
	}

	/**
	 * Saves the current post state.
	 * Used if we are looping a new query to reset previous state.
	 *
	 * @return void
	 */
	final public function savePost()
	{
		global $post, $more;

		$this->saved['post'] = $post;
		$this->saved['more'] = $more;
	}

	/**
	 * Restores the current post state.
	 * Saved in savePost()
	 *
	 * @return void
	 */
	final public function restorePost()
	{
		global $post, $more;

		$more = $this->saved['more'];
		$post = $this->saved['post'];
		if ( $post ) {
		    setup_postdata($post);
		}
	}

	/**
	 * Returns the URL to the module folder.
	 *
	 * @return string Absolute URL to the module folder.
	 */
	final public function getURL()
	{
	    return trailingslashit($this->mod_url);
	}

	/**
	 * Returns the absolute path to module direcotory.
	 *
	 * @since 0.7
	 *
	 * @return string Full absolute path to module directory.
	 */
	final public function getPath()
	{
        return trailingslashit(dirname($this->mod_file));
	}

	/**
     * Returns the basename for the plugin folder.
     *
     * @since 0.7
     *
     * @return string Plugin folder name (Relative to wp-content/plugins or wp-content/themes.
     */
	final public function getSlug()
	{
	    $folder = basename(dirname($this->mod_file));
	    return $folder;
	}
}
