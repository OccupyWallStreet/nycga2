<?php

/*
  Plugin Name: Advanced Post List
  Version: 0.2.0
  Plugin URI: http://code.google.com/p/wordpress-advanced-post-list/
  Description: Create highly customizable post lists with dynamic features. 
  Author: JoKeR

  == Copyright ==
  Advanced Post List by JoKeR (email: jokerbr313@gmail.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
/**
 * @package advanced-post-list
 * @since 0.1.0
 * @version 0.2.0 - Added more require functions for additional pages.
 * @link http://code.google.com/p/wordpress-advanced-post-list/ Advanced Post List Homepage
 */
/*****************************************************************************/
/************************ Compatability Checks *******************************/
/*****************************************************************************/
//Check wordpress version and if it exists (called directly)
global $wp_version;

if ( isset($wp_version) )
{
  if (version_compare($wp_version, "2.0.2", "<"))
  {
    $exit_msg = "This plugin requires Wordpress 2.0.2 or higher to operate. <a href='http://codex.wordpress.org/Upgrading_WordPress'>Please update!</a>";
    exit($exit_msg);
  }
}
else
{
  $exit_msg = "You are attempting to access this plugin directly.";
  exit($exit_msg);
  echo "You are attempting to access this plugin directly.";
}

/*****************************************************************************/
/************************ CONSTANTS ******************************************/
/*****************************************************************************/
//Define constant varibles
define('APL_NAME', 'Advanced Post List');
define('APL_DIR', plugin_dir_path(__FILE__));
define('APL_URL', plugin_dir_url(__FILE__));
//FIX ALWAYS - Update version number
define('APL_VERSION', '0.2.0');

/*****************************************************************************/
/************************ REQUIRED FILES *************************************/
/*****************************************************************************/
require_once(APL_DIR . 'includes/Class/APLCore.php');
require_once(APL_DIR . 'includes/Class/APLPresetDbObj.php');
require_once(APL_DIR . 'includes/Class/APLPresetObj.php');
require_once(APL_DIR . 'includes/Class/APLCallback.php');
require_once(APL_DIR . 'includes/APLWidget.php');
//require_once(APL_DIR . 'includes/Class/APLExport.php');
//require_once(APL_DIR . 'includes/Class/APLImport.php');

/*****************************************************************************/
/************************ LOAD HANDLER ***************************************/
/*****************************************************************************/
//Load Handler
$advanced_post_list = new APLCore(__FILE__);

?>
