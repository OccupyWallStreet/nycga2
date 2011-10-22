<?php
/**
 * Modules related functions.
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
 * Parse the plugin readme.txt file to retrieve plugin's metadata.
 *
 * The metadata of the plugin's readme searches for the following in the readme.txt
 * header. All metadata must be on its own line. The below is formatted for printing.
 *
 * <code>
 * Contributors: contributors nicknames, comma delimited
 * Donate link: Link to plugin donate page
 * Tags: Plugin tags, comma delimited
 * Requires at least: Minimum WordPress version required
 * Tested up to: Higher WordPress version the plugin has been tested.
 * Stable tag: Latest stable tag in repository.
 * </code>
 *
 * Readme data returned array cointains the following:
 * 		- 'Contributors' - An array with all contributors nicknames.
 * 		- 'Tags' - An array with all plugin tags.
 * 		- 'DonateURI' - The donations page address.
 * 		- 'HelpURI' - Link to the forum page.
 * 		- 'DocsURI' - Link to module documentation.
 *      - 'Required' - Minimum required WordPress version.
 *      - 'Tested' - Higher WordPress version this plugin has been tested.
 *      - 'Stable' - Last stable tag when this was released.
 *
 * The first 8kiB of the file will be pulled in and if the readme data is not
 * within that first 8kiB, then the plugin author should correct their plugin
 * and move the plugin data headers to the top.
 *
 * The readme file is assumed to have permissions to allow for scripts to read
 * the file. This is not checked however and the file is only opened for
 * reading.
 *
 * @param string $mod_file Path to the plugin file (not the readme file)
 * @return array See above for description.
 */
function ak_module_readme_data( $mod_file )
{
	$file = dirname($mod_file) . '/readme.txt';

	if ( is_readable($file) ) {
    	$fp = fopen($file, 'r');	// Open just for reading.
	    $data = fread( $fp, 8192 );	// Pull the first 8kiB of the file in.
    	fclose($fp);				// Close the file.

	    preg_match( '|Contributors:(.*)$|mi', $data, $contributors );
    	preg_match( '|Donate link:(.*)$|mi', $data, $uri );
    	preg_match( '|Help link:(.*)$|mi', $data, $help ); // Not WP Standard
    	preg_match( '|Docs link:(.*)$|mi', $data, $docs ); // Not WP Standard
    	preg_match( '|Tags:(.*)|mi', $data, $tags );
    	preg_match( '|Requires at least:(.*)$|mi', $data, $required );
    	preg_match( '|Tested up to:(.*)$|mi', $data, $tested );
    	preg_match( '|Stable tag:(.*)$|mi', $data, $stable );

	    foreach ( array( 'contributors', 'uri', 'help', 'docs', 'tags', 'required', 'tested', 'stable' ) as $field ) {
    		if ( !empty( ${$field} ) ) {
	    		${$field} = trim(${$field}[1]);
    		} else {
	    		${$field} = '';
    		}
	    }

    	$readme_data = array(
	    	'Contributors' => array_map('trim', explode(',', $contributors)),
			'Tags' => array_map('trim', explode(',', $tags)),
    		'DonateURI' => trim($uri),
    	    'HelpURI' => $help,
    	    'DocsURI' => $docs,
	    	'Requires' => trim($required),
			'Tested' => trim($tested),
			'Stable' => trim($stable) );
	} else {
	    $readme_data = array();
	}

	return $readme_data;
}

/**
 * Reads a component file header, and returns component data.
 * Returned data is:
 * 		- 'File' - The component filename, relative to the plugin folder.
 * 		- 'Component' - The component short name or ID.
 * 		- 'Name' - Descriptive name for the component.
 * 		- 'Description' - A descriptive text about the component.
 * 		- 'Author' - Component author name
 * 		- 'URL' - Author homepage URL.
 * 		- 'Link' - Author anchor to home page.
 * 		- 'Core' - If this is a core compoment or not.
 *
 * @since 0.7
 *
 * @param string $file	File name to read the header
 * @param $is_core	If will return data for core components or not.
 * @return array Component data, see above.
 */
function ak_component_data ( $file, $is_core = false )
{
	$fp = fopen($file, 'r');	// Open just for reading.
	$data = fread( $fp, 8192 );	// Pull the first 8kiB of the file in.
	fclose($fp);				// Close the file.

	preg_match( '|Module Component:(.*)$|mi', $data, $component );
	if ( empty($component) && $is_core ) {
		preg_match( '|Core Component:(.*)$|mi', $data, $component );
		$core = 1;
	} else {
		$core = 0;
	}
	preg_match( '|Parent ID:(.*)$|mi', $data, $parent );
	preg_match( '|Component Name:(.*)$|mi', $data, $name );
	preg_match( '|Description:(.*)|mi', $data, $description );
	preg_match( '|Version:(.*)|mi', $data, $version );
	preg_match( '|Author:(.*)|mi', $data, $author );
	preg_match( '|Link:(.*)|mi', $data, $url );

	foreach ( array( 'component', 'parent', 'name', 'description', 'version', 'author', 'url' ) as $field ) {
		if ( ! empty( ${$field} ) ) {
			${$field} = trim(${$field}[1]);
		} else {
			${$field} = '';
		}
	}

	if ( empty($component) ) {
		$data = false;
	} else {
		$data = array(
			'Component' => str_replace(' ', '_', strtolower($component)),
			'File' => $file,
			'Parent' => $parent,
			'Name' => $name,
			'Description' => $description,
		    'Version' => $version,
			'Author' => $author,
			'URL' => $url,
			'Link' => "<a href='{$url}' target='_blank'>{$author}</a>",
			'Core' => $core);
	}

	return $data;
}

/**
 * Gets information about all optional installed components.
 * The function is recursive to find files in all directory levels.
 *
 * TODO: Path must be provided as AOC_PATH is only for community plugin.
 * @since 0.7
 *
 * @param string $path Absolute path where to search for components.
 * @param boolean $core If we want to include the core components or not.
 * @param array $files An array with filenames to seach information in. If empty will search on $path.
 * @return array Array with all found components information.
 */
function ak_get_installed_components( $path, $core = false, $files = array() )
{
	if ( empty($files) ) {
		$files = ak_dir_content($path, 'extensions=php');
	}

	$components = array();
	foreach ( $files as $subdir => $file ) {
		if ( is_array($file) ) {
			$newdir = $path .'/'. $subdir;
			$data = ak_get_installed_components( $newdir, $core, $file );
			if ( is_array($data) ) {
				$components = array_merge($components, $data);
			}
		} else {
			$data = ak_component_data($path . '/' . $file, $core);
			if ( is_array($data) ) {
				$components[$data['Component']] = $data;
			}
		}
	}

	return $components;
}
