<?php
/**
 * Page-related functions
 *
 * @package WordPress_Plugins
 * @subpackage AdminManagementXtended
 */
 
/*
Copyright 2008-2012 Oliver Schlöbe (email : scripts@schloebe.de)

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
 * Add a new 'Actions' column to the page management view
 *
 * @since 0.7
 * @author scripts@schloebe.de
 *
 * @param array $defaults
 * @return array $defaults
 */
function ame_column_page_actions( $defaults ) {
    $defaults['ame_page_actions'] = '<abbr style="cursor:help;" title="' . __('Enhanced by Admin Management Xtended Plugin', 'admin-management-xtended') . ' ' . get_option("ame_version") . '">' . __('Actions', 'admin-management-xtended') . '</abbr>' . ame_changeImgSet();
    return $defaults;
}

/**
 * Add a new 'Page Order' column to the page management view
 *
 * @since 1.0
 * @author scripts@schloebe.de
 * @author Jeff Cole <upekshapriya@coolcave.co.uk>
 *
 * @param array $defaults
 * @return array $defaults
 */
function ame_column_page_order( $defaults ) {
	$current_page = basename($_SERVER['PHP_SELF'], ".php");
	if( $current_page == 'edit-pages' ) $ame_column_heading = __('Page Order:', 'admin-management-xtended'); else $ame_column_heading = __('Post Order:', 'admin-management-xtended');
	
	$defaults['ame_page_order'] = '<abbr style="cursor:help;" title="' . __('Enhanced by Admin Management Xtended Plugin', 'admin-management-xtended') . ' ' . get_option("ame_version") . '">' . $ame_column_heading . '</abbr>';
    return $defaults;
}

/**
 * Adds content to the new 'Actions' column on the page management view
 *
 * @since 0.7
 * @author scripts@schloebe.de
 *
 * @param string $ame_column_name
 * @param int $ame_id
 */
function ame_custom_column_page_actions( $ame_column_name, $ame_id ) {
	global $wpdb, $locale;
    if( $ame_column_name == 'ame_page_actions' ) {
		$post_status = get_post_status( $ame_id ); $q_post = get_post($ame_id);
    	echo '<div style="width:105px;" class="ame_options">';
		
    	// Visibility icon
    	$visstatus = ( $post_status == 'publish' ) ? 'draft' : 'publish';
    	echo '<div id="visicon' . $ame_id . '" style="padding:1px;float:left;"><a href="javascript:void(0);" onclick="ame_ajax_set_visibility(' . $ame_id . ', \'' . $visstatus . '\', \'post\');return false;"><img src="' . AME_PLUGINFULLURL . 'img/' . AME_IMGSET . $visstatus . '.png" border="0" alt="' . __('Toggle visibility', 'admin-management-xtended') . '" title="' . __('Toggle visibility', 'admin-management-xtended') . '" /></a></div>';
		
    	// Date icon
    	echo '<div id="date' . $ame_id . '" style="padding:1px;float:left;"><a href="javascript:void(0);" class="date-pick" id="datepicker' . $ame_id . '"><img src="' . AME_PLUGINFULLURL . 'img/' . AME_IMGSET . 'date.png" border="0" alt="' . __('Change Publication Date', 'admin-management-xtended') . '" title="' . __('Change Publication Date', 'admin-management-xtended') . '" /></a></div> ';
		
		// Slug edit icon
    	echo '<div id="slug' . $ame_id . '" style="padding:1px;float:left;"><a href="javascript:void(0);" id="slugedit' . $ame_id . '" onclick="ame_slug_edit(' . $ame_id . ', \'post\');"><img src="' . AME_PLUGINFULLURL . 'img/' . AME_IMGSET . 'slug_edit.png" border="0" alt="' . __('Edit Page Slug', 'admin-management-xtended') . '" title="' . __('Edit Page Slug', 'admin-management-xtended') . '" /></a></div>';
		
    	// Comment open/closed status icon
    	$comment_status = $q_post->comment_status;
    	if( $comment_status == 'open' ) { $c_status = 0; $c_img = '_open'; } else { $c_status = 1; $c_img = '_closed'; }
    	echo '<div id="commentstatus' . $ame_id . '" style="padding:1px;float:left;"><a tip="' . __('Toggle comment status open/closed', 'admin-management-xtended') . '" href="javascript:void(0);" onclick="ame_ajax_set_commentstatus(' . $ame_id . ', ' . $c_status . ', \'post\');return false;"><img src="' . AME_PLUGINFULLURL . 'img/' . AME_IMGSET . 'comments' . $c_img . '.png" border="0" alt="' . __('Toggle comment status open/closed', 'admin-management-xtended') . '" title="' . __('Toggle comment status open/closed', 'admin-management-xtended') . '" /></a></div> ';
		
		// Plugin: Exclude Pages
		if( is_plugin_active( 'exclude-pages/exclude_pages.php' ) ) {
			$excluded_pages = ep_get_excluded_ids();
    		if( in_array( $ame_id, $excluded_pages ) ) { $e_status = 0; $e_img = ''; } else { $e_status = 1; $e_img = '_off'; }
			echo '<div id="excludepagewrap' . $ame_id . '" style="padding:1px;float:left;"><a tip="' . __('Plugin: Exclude Pages - Exclude page from navigation', 'admin-management-xtended') . '" href="javascript:void(0);" onclick="ame_ajax_set_excludestatus(' . $ame_id . ', ' . $e_status . ');return false;"><img src="' . AME_PLUGINFULLURL . 'img/' . AME_IMGSET . 'excludepages' . $e_img . '.gif" border="0" alt="' . __('Plugin: Exclude Pages - Exclude page from navigation', 'admin-management-xtended') . '" title="' . __('Plugin: Exclude Pages - Exclude page from navigation', 'admin-management-xtended') . '" /></a></div>';
		}
		
		// Post revisions
		if( function_exists('wp_list_post_revisions') && wp_get_post_revisions( $ame_id ) ) {
			echo '<div class="amehasrev" id="amerevisionwrap' . $ame_id . '" style="width:300px;height:165px;overflow:auto;display:none;">';
			wp_list_post_revisions( $ame_id );
			echo '</div>';
		}
    	echo '</div>';
    }
}

/**
 * Adds content to the new 'Page Order' column on the page management view
 * Dikla added $q_post_order->post_type to save order js function line 129.
 * @since 1.0
 * @author Dikla Shwartz <dikla@opentech.co.il>
 * @author scripts@schloebe.de
 *
 * @param string $ame_column_name
 * @param int $ame_id
 */
function ame_custom_column_page_order( $ame_column_name, $ame_id ) {
	global $wpdb;
    if( $ame_column_name == 'ame_page_order' ) {
    	$q_post_order = get_post( $ame_id );
    	echo '<div style="width:75px;" class="ame_options">';
    	echo '<input type="text" value="' . $q_post_order->menu_order . '" size="3" maxlength="3" style="font-size:1em;" id="ame_' . $q_post_order->post_type . 'order' . $ame_id . '" onchange="ame_ajax_order_save(' . $ame_id . ', \'' . $q_post_order->post_type . '\');" /> <span id="ame_order_loader' . $ame_id . '" style="display:none;"><img src="' . AME_PLUGINFULLURL . 'img/' . AME_IMGSET . 'loader.gif" border="0" alt="" /></span>';
    	echo '</div>';
    }
}

add_action('manage_pages_custom_column', 'ame_custom_column_page_actions', 500, 2);
add_filter('manage_pages_columns', 'ame_column_page_actions', 500, 2);
if ( get_option('ame_show_orderoptions') == '1' ) {
	add_action('manage_pages_custom_column', 'ame_custom_column_page_order', 500, 2);
	add_filter('manage_pages_columns', 'ame_column_page_order', 500, 2);
}
?>