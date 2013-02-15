<?php
/**
 * Media-related functions
 *
 * @package WordPress_Plugins
 * @subpackage AdminManagementXtended
 */
 
/*
Copyright 2008 Oliver Schlöbe (email : webmaster@schloebe.de)

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
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* ************************************************ */
/* Adding the columns and data						*/
/* ************************************************ */

/**
 * Adds a new 'Media Order' column to the media management view
 *
 * @since 1.4.0
 * @author scripts@schloebe.de
 *
 * @param array
 * @return array
 */
function ame_column_media_order( $defaults ) {
    $defaults['ame_media_order'] = '<abbr style="cursor:help;" title="' . __('Enhanced by Admin Management Xtended Plugin', 'admin-management-xtended') . ' ' . get_option("ame_version") . '">' . __('Media Order', 'admin-management-xtended') . '</abbr>';
    return $defaults;
}

/**
 * Replaces the media 'Description' column in the media management view
 *
 * @since 1.5.0
 * @author scripts@schloebe.de
 *
 * @param array
 * @return array
 */
function ame_column_media_desc( $defaults ) {
	unset($defaults['desc']);
    $defaults['ame_media_desc'] = '<abbr style="cursor:help;" title="' . __('Enhanced by Admin Management Xtended Plugin', 'admin-management-xtended') . ' ' . get_option("ame_version") . '">' . __('Description') . '</abbr>';
    return $defaults;
}

/**
 * Adds content to the new 'Media Order' column on the media management view
 *
 * @since 1.4.0
 * @author scripts@schloebe.de
 *
 * @param string
 * @param int
 */
function ame_custom_column_media_order( $ame_column_name, $ame_id ) {
	global $wpdb;
    if( $ame_column_name == 'ame_media_order' && current_user_can( 'edit_post', $ame_id ) ) {
    	$q_media_order = get_post( $ame_id );
    	echo '<div style="width:75px;" class="ame_options">';
    	echo '<input type="text" value="' . $q_media_order->menu_order . '" size="3" maxlength="3" style="font-size:1em;" id="ame_postorder' . $ame_id . '" onchange="ame_ajax_order_save(' . $ame_id . ', \'post\');" /> <span id="ame_order_loader' . $ame_id . '" style="display:none;"><img src="' . AME_PLUGINFULLURL . 'img/' . AME_IMGSET . 'loader.gif" border="0" alt="" /></span>';
    	echo '</div>';
    }
}

/**
 * Adds content to the altered media 'Description' column on the media management view
 *
 * @since 1.5.0
 * @author scripts@schloebe.de
 *
 * @param string
 * @param int
 */
function ame_custom_column_media_desc( $ame_column_name, $ame_id ) {
	global $wpdb;
    if( $ame_column_name == 'ame_media_desc' ) {
    	$q_media_desc = get_post( $ame_id );
    	$media_desc = $q_media_desc->post_excerpt;
    	echo '<span id="ame_mediadesc' . $ame_id . '"><span id="ame_mediadesc_text' . $ame_id . '">' . $media_desc . '</span>&nbsp;';
		if( current_user_can( 'edit_post', $ame_id ) ) {
			echo '<a id="mediadesceditlink' . $ame_id . '" href="javascript:void(0);" onclick="ame_ajax_form_mediadesc(' . $ame_id . ');return false;" title="' . __('Edit') . '"><img src="' . AME_PLUGINFULLURL . 'img/' . AME_IMGSET . 'edit_small.gif" border="0" alt="' . __('Edit') . '" title="' . __('Edit') . '" /></a></span>';
		}
    }
}

add_action('manage_media_custom_column', 'ame_custom_column_media_order', 500, 2);
add_filter('manage_media_columns', 'ame_column_media_order', 500, 2);
add_action('manage_media_custom_column', 'ame_custom_column_media_desc', 300, 2);
add_filter('manage_media_columns', 'ame_column_media_desc', 300, 2);
?>