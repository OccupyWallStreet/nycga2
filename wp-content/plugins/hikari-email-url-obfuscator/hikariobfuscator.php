<?php
/*
Plugin Name: Hikari Email &amp; URL Obfuscator
Plugin URI: http://Hikari.ws/email-url-obfuscator/
Description: Email and normal links are obfuscated, hiding them from spambots. It automatically encodes each link, then uses JavaScript to decode and show them.
Version: 0.08.10
Author: Hikari
Author URI: http://Hikari.ws
*/

/**!
* I, Hikari, from http://Hikari.WS , and the original author of the Wordpress plugin named
* Hikari Email & URL Obfuscator, please keep this license terms and credit me if you redistribute the plugin
*
* I dedicate Hikari Email & URL Obfuscator to Ju, my beloved frient ^-^
*
*
*   This program is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*
/*****************************************************************************
* © Copyright Hikari (http://wordpress.Hikari.ws), 2010
* If you want to redistribute this script, please leave a link to
* http://hikari.WS
*
* Parts of this code are provided or based on ideas and/or code written by others
* Translations to different languages are provided by users of this script
* IMPORTANT CONTRIBUTIONS TO THIS SCRIPT (listed in alphabetical order):
*
** Debugged Interactive Designs @ http://www.debuggeddesigns.com/open-source-projects/mailto
** Scott Yang @ http://scott.yang.id.au/file/js/rot13.js
** Silvan Mühlemann @ http://techblog.tilllate.com/2008/07/20/ten-methods-to-obfuscate-e-mail-addresses-compared/
*
* Please send a message to the address specified on the page of the script, for credits
*
* Other contributors' (nick)names may be provided in the header of (or inside) the functions
* SPECIAL THANKS to all contributors and translators of this script !
*****************************************************************************/

define('HkMuob_basename',plugin_basename(__FILE__));
define('HkMuob_pluginfile',__FILE__);
define('HkMuob_no_obfuscate_comment','<!-- HkMuob NO OBFUSCATE -->');


require_once 'hikari-tools.php';
require_once 'hikariobfuscator-options.php';
require_once 'hikariobfuscator-core.php';

