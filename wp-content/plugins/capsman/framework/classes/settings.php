<?php
/**
 * Alkivia Settings Manager
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
 * Class to manage settings.
 * It is expected to receive all settings properly filtered.
 *
 * @author		Jordi Canals
 * @package		Alkivia
 * @subpackage	Framework
 * @link		http://wiki.alkivia.org/framework/classes/settings
 */
final class akSettings
{

    /**
     * Settings from alkivia.ini file
     * This settings will be forced and cannot be set on the admin panel.
     *
     * @var array
     */
    private $forced = array();

    /**
     * Global settings for all Alkivia modules.
     * This is a merge from the settings on db and settings on the ini file.
     *
     * @var array
     */
    private $settings = array();

    /**
     * Options retrieved from database and merged with defaults.
     * This is what needs to be saved to database at end.
     *
     * @var array
     */
    private $options = array();

    /**
     * Default settings for all alkivia modules.
     * This options are set at module startup and used if no other setting is found.
     *
     * @var array
     */
    private $defaults = array();

    /**
     * Flags to know if a setting has been updated or not.
     * This is an array of flags, one item per module.
     *
     * @var array
     */
    private $updated = array();

    /**
     * Settings prefix and sufix for database entry.
     */
    const prefix = '';
    const sufix  = '_settings';

    /**
     * Class constructor.
     * Loads settings from ini file.
     *
     * @return akSettings
     */
    public function __construct()
    {
        if ( defined('AK_INI_FILE') && file_exists(AK_INI_FILE) ) {
            $this->forced = parse_ini_file(AK_INI_FILE, true);
        }

        add_action('shutdown', array($this, 'saveOptions'));
    }

    /**
     * Populates module settings array.
     * Merges into settings array, defaults and options retrieved from DB.
     *
     * @param string $module Module name to load.
     * @return void
     */
    private function populateSettings ( $module )
    {
        if ( ! isset($this->defaults[$module]) ) {
            $this->defaults[$module] = array();
        }

        if ( ! isset($this->options[$module]) ) {
            $options = apply_filters('ak_' . $module . '_options', get_option(self::prefix . $module . self::sufix));

            if ( is_array($options) ) {
                $this->options[$module] = $options;
            } else {
                $this->options[$module] = array();
            }
        }

        if ( ! isset($this->forced[$module]) ) {
            $this->forced[$module] = array();
        }

        if ( ! isset($this->updated[$module]) ) {
            $this->updated[$module] = false;
        }

        $this->options[$module] = array_merge(
                                   $this->defaults[$module],
                                   $this->options[$module]);

        $this->settings[$module] = array_merge(
                                   $this->options[$module],
                                   $this->forced[$module]);
    }

    /**
     * Sets default values for a module and fills missing settings with them.
     *
     * @uses apply_filters() Calls the 'ak_<module>_defaults' filter on new defaults.
     * @param string $module	Module name.
     * @param array $options	Default settings array.
     * @return void
     */
    public function setDefaults ( $module, $options )
    {
        if ( ! is_array($options) ) {    // Must be an array of options.
            $options = array();
        }

        $this->defaults[$module] = apply_filters('ak_' . $module . '_defaults', $options);
        $this->populateSettings($module);
    }

    /**
     * Returns an array with default values for a module.
     *
     * @param $module	Module name
     * @return array	Default values for this module.
     */
    public function getDefaults ( $module )
    {
        if ( isset($this->defaults[$module]) && is_array($this->defaults[$module]) ) {
            return $this->defaults[$module];
        } else {
            return array();
        }
    }

    /**
     * Gets a setting for a module.
     * If $option is empty, will return all settings for a module in an array.
     *
     * @param string $module	Module internal name.
     * @param string $option	Setting name.
     * @param mixed $default	Default value if setting not found.
     * @return mixed Returns the setting value or $default if not defined.
     */
    public function getSetting ( $module, $option = '', $default = false )
    {
        if ( ! isset($this->settings[$module]) ) {
            $this->populateSettings($module);
        }

        if ( empty($option) ) {
            return $this->settings[$module];
        } elseif ( isset($this->settings[$module][$option]) ) {
            return $this->settings[$module][$option];
        } else {
            return $default;
        }
    }

    /**
     * Checks if an option is forced in the ini file.
     * It is forced if it was defined on the ini file.
     *
     * @param string $module	Module internal name.
     * @param string $option	Setting name.
     * @return boolean
     */
    public function isForced ( $module, $option )
    {
        if ( isset($this->forced[$module][$option]) ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Adds a new module setting only if it does not exists.
     * Does not save them to database. After adding all new settings, must call akSettings::saveSettings()
     *
     * @param string $module  Module name.
     * @param string $option  Setting name.
     * @param mixed $value    Setting value
     * @return boolean Returns true if settings has been added, false otherwise.
     */
    public function addOption ( $module, $option, $value )
    {
        if ( ! isset($this->options[$module]) ) {
            $this->populateSettings($module);
        }

        if ( isset($this->options[$module][$option]) ) {
            return false;
        } else {
            $this->options[$module][$option] = $value;
        }

        $this->populateSettings($module);
        $this->updated[$module] = true;

        return true;
    }

    /**
     * Updates a module setting. If the setting does not exists, it is created.
     * Does not save them to database. After adding all new settings, must call akSettings::saveSettings()
     *
     * @param string $module  Module name.
     * @param string $option  Setting name.
     * @param mixed $value    Setting value
     * @return void
     */
    public function updateOption ( $module, $option, $value )
    {
        if ( ! isset($this->options[$module]) ) {
            $this->populateSettings($module);
        }

        $this->options[$module][$option] = $value;
        $this->populateSettings($module);
        $this->updated[$module] = true;
    }

    /**
     * Deletes an option from a module.
     * Only if the option existed.
     *
     * @param string $module  Module name.
     * @param string $option  Setting name.
     * @return void
     */
    public function deleteOption ( $module, $option )
    {
        if ( ! isset($this->options[$module]) ) {
            $this->populateSettings($module);
        }

        if ( isset($this->options[$module][$option]) ) {
            unset($this->options[$module][$option]);
        }
        $this->populateSettings($module);
        $this->updated[$module] = true;
    }

    /**
     * Replaces all settings for a module and saves them to database.
     * If settings does not exist, them are created.
     *
     * @uses apply_filters() Calls the 'ak_<module>_replace_options' filter on new options.
     * @param string $module Module name.
     * @param array $settings New module settings.
     * @return boolean Returns true if settings have been replaced.
     */
    public function replaceOptions ( $module, $settings )
    {
        if ( ! is_array($settings) ) {
            return false;
        }

        $this->options[$module] = apply_filters('ak_' . $module . '_replace_options', $settings);
        $this->populateSettings($module);
        $this->updated[$module] = true;

        return $this->saveOptions($module);
    }

    /**
     * Saves settings to database.
     * If a module name is provided saves only this module settings, if not, saves all settings.
     *
     * @param string $module Module name to save.
     * @return boolean Returns true is settings have been saved, false otherwise.
     */
    public function saveOptions ( $module = '' )
    {
        if ( empty($module) ) {
            foreach ( $this->options as $module => $value ) {
                if ( $this->updated[$module] ) {
                    update_option(self::prefix . $module . self::sufix, $value);
                    $this->updated[$module] = false;
                }
            }
            return true;
        } elseif ( isset($this->options[$module]) && $this->updated[$module]) {
            update_option(self::prefix . $module . self::sufix, $this->options[$module]);
            return true;
        } else {
            return false;
        }
    }
}
