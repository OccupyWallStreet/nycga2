<?php
/**
 * Functions for objects management.
 *
 * @version		$Rev: 198515 $
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

if ( ! isset($GLOBALS['_akv']) )
{
    // Create the global $_akv. This holds all objects and settings.
    $GLOBALS['_akv'] = array();
}

/**
 * Creates and stores an object in the $_akv global.
 * Can be called at the same time we create the object: ak_store_object( 'obj_name', new objectName() );
 *
 * @param string $name	Internal object name.
 * @param object $object The object reference to store in the global.
 * @return object The newly stored object reference.
 */
function & ak_create_object ( $name, $object )
{
    $GLOBALS['_akv'][$name] =& $object;
    return $object;
}

/**
 * Gets an object stored in the $_akv global.
 *
 * @param string $name Object name.
 * @return object|false	Returns the requested object reference. If not found, or not an object, returns false.
 */
function & ak_get_object ( $name )
{
    if ( is_object($GLOBALS['_akv'][$name]) ) {
        return $GLOBALS['_akv'][$name];
    } else {
        return false;
    }
}

/**
 * Checks if an object exists in the $_akv global.
 *
 * @param string $name Object name to check.
 * @return boolean	If the object exists or not.
 */
function ak_object_exists ( $name )
{
    global $_akv;

    if ( isset($_akv[$name]) && is_object($_akv[$name]) ) {
        return true;
    } else {
        return false;
    }
}

/**
 * Returns the 'settings' object reference.
 *
 * @return object|false Returns the object reference, or false if not found.
 */
function & ak_settings_object ()
{
    if ( ak_object_exists('settings') ) {
        return ak_get_object('settings');
    } else {
        return ak_create_object('settings', new akSettings());
    }
}

/**
 * Returns an object option/setting.
 *
 * @param string $object Object name to get options from.
 * @param string $option Option name to return.
 * @param mixed $default Default value if option not found.
 * @return mixed The option value.
 */
function ak_get_option ( $object, $option = '', $default = false )
{
    if ( is_object($GLOBALS['_akv'][$object]) && method_exists($GLOBALS['_akv'][$object], 'getOption') ) {
        return $GLOBALS['_akv'][$object]->getOption($option, $default);
    } else {
        return $default;
    }
}
