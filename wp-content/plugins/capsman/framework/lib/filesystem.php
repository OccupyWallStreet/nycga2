<?php
/**
 * Functions related to the file system and system settings.
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
 * Creates a subdirectory within the WordPress uploads folder.
 *
 * @uses apply_filters() Calls the 'ak_upload_dir' on the upload folder directory.
 * @param string $name	New subdirectory name.
 * @return void
 */
function ak_create_upload_folder( $name )
{
    if ( ! defined('AK_UPLOAD_DIR') ) {
        $uploads = wp_upload_dir();
        define ( 'AK_UPLOAD_DIR', $upload['basedir'] .'/alkivia' );
    }

    $folder = apply_filters('ak_upload_dir', AK_UPLOAD_DIR . '/' . $name);
	wp_mkdir_p($folder);
}

/**
 * Returns the system max filesize for uploads.
 * This depends on the directives upload_max_filesize, post_max_size and memory_limit. The lowest of them is the limit.
 *
 * @return int	Max system filesize upload (in bytes).
 */
function ak_max_upload()
{

    $file 	= ak_return_bytes(ini_get('upload_max_filesize'));
    $post	= ak_return_bytes(ini_get('post_max_size'));
    $mem	= ak_return_bytes(ini_get('memory_limit'));

    return min($file, $post, $mem);
}

/**
 * Converts a PHP config value to bytes.
 *
 * @param $string	PHP config value, as readed with ini_get()
 * @return int		Value in bytes.
 */
function ak_return_bytes( $value )
{
    $val 	= trim($value);
    $unit	= strtoupper($val[strlen($val)-1]);

    switch ( $unit ) {
        case 'G':			// GigaBytes
            $val *= 1024;
        case 'M':			// MegaBytes
            $val *= 1024;
        case 'K':			// KiloBytes
            $val *= 1024;
		}

    return $val;
}

/**
 * Formats a value in bytes to the bigger unit. (Gb, Mb, Kb).
 *
 * @param int $value	Value to convert.
 * @return string		Formated value.
 */
function ak_return_units( $value, $max = 'G' )
{
    $max = strtoupper($max);
    $unit = 'bytes';

    if ( $value >= 1024 ) {
        $value /= 1024;
        $unit = 'Kb';
    }
    if ( $value >= 1024 && ( 'M' == $max || 'G' == $max ) ){
        $value /= 1024;
        $unit = 'Mb';
    }
    if ( $value >= 1024 && ( 'G' == $max) ) {
        $value /= 1024;
        $unit = 'Gb';
    }

    $val = intval($value) . ' ' . $unit;
    return $val;
}

/**
 * Creates and returns an ordered list for files in a given directori.
 * The function recurses into all directory tree.
 *
 * @param string $directory	Directory where search for files. Absolute path.
 * @param array $args		Options array to select wich files to return. Options:
 * 								- tree: recurses into subdirectories. 0 = No, 1 (default) = Yes
 * 								- extensions: array or comma delimited list of wanted extensions (default all extensions).
 * 								- with_ext: Return filename with extension. 0 = No, 1 (default) = Yes
 * 								- prefix: A filename prefix for returned files (default = No prefix).
 * @param boolean $withexst	We want returned filenames with extension. Defaults true.
 * @return array List of files found.
 */
function ak_dir_content($directory, $args='')
{

    $directory = realpath($directory); // Be sure the directory path is well formed.
    if ( ! is_dir($directory) ) {      // Check if it is a directory.
        return array();                // If not, return an ampty array.
    }

	$defaults = array(
		'tree' => 1,			// recurses into subdirectories (0 = No, 1 = Yes)
		'extensions' => '',		// array or comma delimited list of wanted extensions
		'with_ext' => 1,		// Return filename with extension (0 = No, 1 = Yes)
	    'prefix' => '');        // If we only want files with a custom prefix set the prefix option.

	$options = wp_parse_args($args, $defaults);
	extract($options, EXTR_SKIP);

	if ( ! empty($extensions) && ! is_array($extensions) ) {
		$extensions = explode(',', $extensions);
	}

	$dir_tree = array();			// Directory could be empty.
    $d = dir($directory);
	while ( false !== ( $filename = $d->read() ) ) {
	    if ( $prefix != substr($filename, 0, strlen($prefix)) || '.' == substr($filename, 0, 1) ) {
		    continue;
		}

		if ( is_dir($directory .  DIRECTORY_SEPARATOR . $filename) && $tree ) {
		    $dir_tree[$filename] = ak_dir_content($directory . DIRECTORY_SEPARATOR . $filename, $options);
		} else {
			$fileinfo = pathinfo($directory . DIRECTORY_SEPARATOR . $filename);
			if ( empty($extensions) || in_array($fileinfo['extension'], $extensions) ) {
				if ( ! $with_ext ) {
					$filename = substr($filename, 0, (strlen($fileinfo['extension']) + 1) * -1);
				}
				$dir_tree[] = $filename;
			}
		}
	}
	$d->close();
	asort($dir_tree);

	return $dir_tree;
}

/**
 * Returns a list of templates found in an array of directories
 *
 * @param array|string $folders Array of folders to search in.
 * @param string $prefix Templates prefix filter.
 * @return array Found templates (all found php files).
 */
function ak_get_templates( $folders, $prefix = '' )
{
    // Compatibility with a bug in Sideposts 3.0 and 3.0.1
    if ( strpos($prefix, '&') ) $prefix = '';

    $list = array();
    foreach ( (array) $folders as $folder ) {
        $found = ak_dir_content($folder, "tree=0&extensions=php&with_ext=0&prefix={$prefix}");
        $list = array_merge($found, $list);
    }

    $start = strlen($prefix);
    foreach ( $list as $item ) {
        $name = substr($item, $start);
        $templates[$name] = ucfirst(str_replace('_', ' ', $name));
    }

    return $templates;
}
