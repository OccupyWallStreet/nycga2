<?php
/*
 * We keep the configuration in a separate file to ease updating: if you plan
 * to update the plugin simply put this file aside, copy the new files over the
 * old ones and put this config back into place and your settings won't be lost.
 *
 * Compare your original config file with the new one once in a while to make
 * sure that you've got the latest defines and functions in your one too.
 */

#
# WordPress SmoothGallery plugin
# Copyright (C) 2008-2009 Christian Schenk
# 
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
# 
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA
#


# Allows you to use a custom version of SmoothGallery. If the version string is
# empty the stable version will be used.
#
# Note to the 'namespaced' version: This can be used if you use Prototype.js,
# jQuery or any other incompatible framework. Note that there's no guarantee
# that everything works fine with this hack.
# Hint: Maybe you're better off using iFrames.
#
# Currently available versions:
#  SmoothGallery     MooTools
#  - namespaced      namespaced  (old hack)
#  - 2.1beta1        1.2
define('SMOOTHGALLERY_VERSION', '');
define('MOOTOOLS_VERSION', '');
#define('SMOOTHGALLERY_VERSION', '2.1beta1');
#define('MOOTOOLS_VERSION', '1.2');

# We'll add this many pixels to the size of the iFrame. This makes sure that
# there're no scrollbars just because the content of the iFrame is a little bit
# taller than the iFrame itself.
# If you don't like this solution you can set it to a smaller value and use
# some CSS to handle this issue.
define('ADD_PIXELS_TO_IFRAME', 20);


#
# Extras
# 
# Transitions
define('ENABLE_TRANSITIONS', true);

# Generated thumbnails
define('ENABLE_GENERATED_THUMBNAILS', false);

# Recent images box
define('ENABLE_RECENT_IMAGES_BOX', false);
if (ENABLE_RECENT_IMAGES_BOX) require_once('extra/recent_images_box.php');

# Flickr
define('ENABLE_FLICKR', false);
define('FLICKR_APIKEY', '');
define('FLICKR_SECRET', '');
if (ENABLE_FLICKR) require_once('extra/flickr.php');

# PicasaRSS (PHP5 only)
define('ENABLE_PICASARSS', false);
if (ENABLE_PICASARSS) require_once('extra/picasa_rss.php');


/**
 * Expects an integer that references a particular directory in the array
 * $dirs. This is used along with the shortcode's attribute "dir".
 */
function getImageDirectory($dir) {
	# You may add more directories to the array
	#$dirs = array(dirname(__FILE__).'/../../../'.get_option('upload_path')); # won't work with iFrames
	$dirs = array(dirname(__FILE__).'/../../../wp-content/uploads');
	# You don't have to edit below here...
	if (!is_numeric($dir)) return NULL;
	if ($dir > count($dir) - 1) return NULL;
	return $dirs[$dir];
}


/**
 * If you want to integrate the SmoothGallery into your theme, you can
 * implement this method and set the default values for the gallery
 * accordingly.
 *
 * You may want to use "Conditional Tags":
 * -> http://codex.wordpress.org/Conditional_Tags
 *
 * @return mixed false if we don't want to set global parameters for the
 * gallery, otherwise an array with the appropriate options; for the latter
 * have a look at the get_default_smoothgallery_parameters() function at the
 * top of the file utils.php
 */
function insertSmoothGallery() {
	# add all the conditional tags you're using to the array
	if (assert_functions_exist(array('is_page')) === false) return false;

	if (is_page('73')) {
		return array('width' => 96,
		             'height' => 72,
		             'timed' => 'true',
		             'showInfopane' => 'false',
		             'showArrows' => 'false',
		             'embedLinks' => 'false');
	}
	return false;
}


/**
 * Returns true if the given functions exist, otherwise false.
 */
function assert_functions_exist($functions) {
	if (empty($functions) or !is_array($functions)) return false;
	foreach ($functions as $function) {
		if (function_exists($function)) continue;
		return false;
	}
	return true;
}

?>
