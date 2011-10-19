<?php
/*
 * Common functions for dynamic content widget
 * @since 0.1
 * 
 * Copyright (C) 2011  Dikhoff Software
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 */


function dcw_get_ID_by_slug($page_slug) {
	global $wpdb;
	$querystr = "SELECT wposts.*
		FROM $wpdb->posts wposts
		WHERE wposts.post_status = 'publish'
		AND wposts.post_name = '$page_slug' 
		AND (wposts.post_type = 'post'
		OR wposts.post_type = 'page') 
	";
	$pageposts = $wpdb->get_results($querystr, OBJECT);
	
	$page = $pageposts[0];

	if ($page) {
		return $page->ID;
	} else {
		return null;
	}
}

function dcw_get_subtemplate_description( $file ) {
	global $st_file_descriptions;

	if ( isset( $st_file_descriptions[basename( $file )] ) ) {
		return $st_file_descriptions[basename( $file )];
	}
	elseif ( file_exists( $file ) && is_file( $file ) ) {
		$template_data = implode( '', file( $file ) );
		if ( preg_match( '|Subtemplate:(.*)$|mi', $template_data, $name )||
		     preg_match( '|Subtemplate Name:(.*)$|mi', $template_data, $name ))
			return _cleanup_header_comment($name[1]);
	}

	return null;
}

function dcw_get_template_sysname($file) {
	$bname = basename($file);
	$parts = split("\.", $bname);
	return $parts[0];	
}


function dcw_get_subtemplates() {
	global $dcw_subtemplates;
	if (isset($dcw_subtemplates)) {
		return $dcw_subtemplates;
	}
	
	$st_subtemplates = Array();
	
	$themes = get_themes();
	$theme = get_current_theme();

	foreach ( $themes[$theme]['Template Files'] as $template_file ) {
		$desc = dcw_get_subtemplate_description($template_file);
		if ($desc) {		
		    $st_subtemplates[dcw_get_template_sysname($template_file)] = $desc;
		}
	} 
	return $st_subtemplates;
}


function dcw_write_subtemplates($widget, $instance) {
	$subtemplates = dcw_get_subtemplates();
	$sid = $widget->get_field_id( 'subtemplate' );
?>
<p>
<label for="<?php echo $sid; ?>"><?php _e('Subtemplate:', 'subtemplate'); ?></label>
	<select id="<?php echo $sid; ?>" name="<?php echo $widget->get_field_name( 'subtemplate' ); ?>">
		<option value="">None</option>
<?php 	
		foreach ( $subtemplates as $file => $desc ) {
			$sel = '';
			if ($instance['subtemplate'] == $file) { 
				$sel = ' selected="selected"';
			}
			echo '<option value="'. $file .'"' . $sel . ' >' . $desc . '</option>';
		}
?> 
	</select>
</p>
<?php
}
	

function _dcw_find_content_id($field, $q) {
	global $wpdb;

	$dbquery = $wpdb->prepare("
			SELECT ID, post_title, post_name FROM $wpdb->posts
			WHERE post_status = 'publish' 
			AND post_type NOT IN ('nav_menu_item', 'revision')
			AND $field = '%s'", $q);
	
	$results = $wpdb->get_results($dbquery);
	return $results;
}

function dcw_find_content_id($q) {
	$parts = split(':', $q);
	if (is_numeric($parts[0])) {
		$results = _dcw_find_content_id("ID", $parts[0]);
		if (sizeof($results) > 0) {
			return $results;
		}
	}
	
	$results = _dcw_find_content_id("post_title", $q);
	if (sizeof($results) > 0) {
		return $results;
	}
	
	$results = _dcw_find_content_id("post_name", $q);
	if (sizeof($results) > 0) {
		return $results;
	}	
}


?>