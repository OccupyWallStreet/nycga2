<?php

/*
  Plugin Name: Advanced Post List
  Version: 0.3.b3
  Plugin URI: http://advanced-post-list.wikiforum.net/
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
define('APL_NAME',      'Advanced Post List');
//FIX ALWAYS - Update version number
define('APL_VERSION',   '0.3.b3');
//APL_DIR = C:\xampp\htdocs\wordpress\wp-content\plugins\advanced-post-list/
define('APL_DIR',       plugin_dir_path(__FILE__));
//APL_URL = http://localhost/wordpress/wp-content/plugins/advanced-post-list/
define('APL_URL',       plugin_dir_url(__FILE__));
/*****************************************************************************/
/************************ REQUIRED FILES *************************************/
/*****************************************************************************/
require_once(APL_DIR . 'includes/class/APLCore.php');
require_once(APL_DIR . 'includes/class/APLPresetDbObj.php');
require_once(APL_DIR . 'includes/class/APLPresetObj.php');
require_once(APL_DIR . 'includes/class/APLCallback.php');
require_once(APL_DIR . 'includes/class/APLWidget.php');
require_once(APL_DIR . 'includes/class/APLQuery.php');

/*****************************************************************************/
/************************ LOAD HANDLER ***************************************/
/*****************************************************************************/

//Load Handler
$advanced_post_list = new APLCore(__FILE__);

?>
