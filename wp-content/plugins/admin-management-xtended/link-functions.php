<?php
/**
 * Link-related functions
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
 * Adds a new 'Link Visibility' column to the link management panel
 *
 * @since 1.8.0
 * @author scripts@schloebe.de
 *
 * @param array
 * @return array
 */
function ame_column_link_visibility( $defaults ) {
	unset( $defaults['visible'] );
    $defaults['ame_link_visibility'] = '<abbr style="cursor:help;" title="' . __('Enhanced by Admin Management Xtended Plugin', 'admin-management-xtended') . ' ' . get_option("ame_version") . '">' . __('Visible') . '</abbr>';
    return $defaults;
}

/**
 * Adds content to the new 'Link Visibility' column in the link management panel
 *
 * @since 1.8.0
 * @author scripts@schloebe.de
 *
 * @param string
 * @param int
 */
function ame_custom_column_link_visibility( $ame_column_name, $ame_id ) {
	global $wpdb;
    if( $ame_column_name == 'ame_link_visibility' && current_user_can( 'manage_links', $ame_id ) ) {
    	$link = get_bookmark( $ame_id );
    	$visible = ($link->link_visible == 'Y') ? __('Yes') : __('No');
		echo '<span id="ame_linkvis' . $ame_id . '">' . $visible . '</span>&nbsp;<a id="ame_linkvislink' . $ame_id . '" href="javascript:void(0);" onclick="ame_ajax_set_linkvisibility(' . $ame_id . ');return false;" title="' . __('Edit') . '"><img src="' . AME_PLUGINFULLURL . 'img/' . AME_IMGSET . 'toggle.gif" border="0" alt="' . __('Toggle visibility', 'admin-management-xtended') . '" title="' . __('Toggle visibility', 'admin-management-xtended') . '" /></a><br />';
    }
}

add_action('manage_link_custom_column', 'ame_custom_column_link_visibility', 3, 2);
add_filter('manage_link-manager_columns', 'ame_column_link_visibility', 3, 2);

/**
 * Adds a new 'Link Categories' column to the link management panel
 *
 * @since 1.8.0
 * @author scripts@schloebe.de
 *
 * @param array
 * @return array
 */
function ame_column_link_categories( $defaults ) {
	unset( $defaults['categories'] );
    $defaults['ame_link_categories'] = '<abbr style="cursor:help;" title="' . __('Enhanced by Admin Management Xtended Plugin', 'admin-management-xtended') . ' ' . get_option("ame_version") . '">' . __('Categories') . '</abbr>';
    return $defaults;
}

/**
 * Adds content to the new 'Link Categories' column in the link management panel
 *
 * @since 1.8.0
 * @author scripts@schloebe.de
 *
 * @param string
 * @param int
 */
function ame_custom_column_link_categories( $ame_column_name, $ame_id ) {
	global $wpdb;
    if( $ame_column_name == 'ame_link_categories' && current_user_can( 'manage_links', $ame_id ) ) {
    	$link = get_bookmark( $ame_id );
    	$cat_names = array();
		foreach ($link->link_category as $category) {
			$cat = get_term($category, 'link_category', OBJECT, 'display');
			if ( is_wp_error( $cat ) )
				echo $cat->get_error_message();
			$cat_name = $cat->name;
			if ( $ame_id != $category )
				$cat_name = "<a href='link-manager.php?cat_id=$category'>$cat_name</a>";
			$cat_names[] = $cat_name;
		}
		$ame_link_cats = implode(', ', $cat_names);
		echo '<span id="ame_linkcategory' . $ame_id . '">' . $ame_link_cats . '</span>&nbsp;';
		echo '<a class="thickbox" id="thickboxlink' . $ame_id . '" href="#TB_inline?height=205&amp;width=300&amp;inlineId=linkcategorychoosewrap' . $ame_id . '&amp;modal=true" title="' . __('Edit') . '"><img src="' . AME_PLUGINFULLURL . 'img/' . AME_IMGSET . 'edit_small.gif" border="0" alt="' . __('Edit') . '" title="' . __('Edit') . '" /></a>';
		?>
		<div id="linkcategorychoosewrap<?php echo $ame_id; ?>" style="width:300px;height:165px;overflow:auto;display:none;">
		<div id="linkcategorychoose<?php echo $ame_id; ?>" class="categorydiv">
			<ul id="categorychecklist" class="list:category categorychecklist form-no-clear" style="height:165px;overflow:auto;">
				<?php wp_link_category_checklist( $link->link_id ); ?>
			</ul>
			<div style="text-align:center;"><input type="button" value="<?php _e('Save') ?>" class="button-primary" onclick="ame_ajax_save_linkcategories(<?php echo $ame_id; ?>);return false;" />&nbsp;<input type="button" value="<?php _e('Cancel') ?>" class="button" onclick="tb_remove();" /></div>
		</div>
		</div>
		<?php
    }
}

add_action('manage_link_custom_column', 'ame_custom_column_link_categories', 2, 2);
add_filter('manage_link-manager_columns', 'ame_column_link_categories', 2, 2);

/**
 * SACK response function for toggling link visibility
 *
 * @since 1.8.0
 * @author scripts@schloebe.de
 */
function ame_toggle_linkvisibility() {
	global $wpdb;
	$posttype = 'link';
	$linkid = intval( $_POST['link_id'] );
	$link = get_bookmark( $linkid );
	$status = ($link->link_visible == 'Y') ? 'N' : 'Y';
	$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->links SET link_visible = %s WHERE link_id = %d", $status, $linkid ) );
	do_action( 'edit_link', $linkid );
	$visible = ($link->link_visible == 'Y') ? __('No') : __('Yes');
	die( "jQuery('span#ame_linkvis" . $linkid . "').text('" . addslashes_gpc( $visible ) . "');jQuery('#" . $posttype . "-" . $linkid . " td, #" . $posttype . "-" . $linkid . " th').animate( { backgroundColor: '#EAF3FA' }, 300).animate( { backgroundColor: '#F9F9F9' }, 300).animate( { backgroundColor: '#EAF3FA' }, 300).animate( { backgroundColor: '#F9F9F9' }, 300);" );
}

/**
 * SACK response function for saving link categories
 *
 * @since 1.8.0
 * @author scripts@schloebe.de
 */
function ame_ajax_save_linkcategories() {
	global $wpdb;
	$linkid = (int) $_POST['linkid'];
	$ame_linkcats = $_POST['ame_linkcats'];
	
	$ame_linkcategories = substr( $ame_linkcats, 0, -1 );
	$catarray = explode(",", $ame_linkcategories);
	wp_set_link_cats( $linkid, $catarray );
	do_action( 'edit_link', $linkid );
	unset($GLOBALS['category_cache']);
	
	$link = wp_get_link_cats( $linkid );
    $cat_names = array();
	foreach ($link as $category) {
		$cat = get_term($category, 'link_category', OBJECT, 'display');
		if ( is_wp_error( $cat ) )
			echo $cat->get_error_message();
		$cat_name = $cat->name;
		if ( $linkid != $category )
			$cat_name = "<a href='link-manager.php?cat_id=$category'>$cat_name</a>";
		$cat_names[] = $cat_name;
	}
	$ame_link_cats = implode(', ', $cat_names);
	die( "re_init();jQuery('span#ame_linkcategory" . $linkid . "').fadeOut('fast', function() {
		jQuery('a#thickboxlink" . $linkid . "').show();
		jQuery('span#ame_linkcategory" . $linkid . "').html('" . addslashes_gpc( $ame_link_cats ) . "').fadeIn('fast');
	});" );
}

if( function_exists('add_action') ) {
	add_action('wp_ajax_ame_toggle_linkvisibility', 'ame_toggle_linkvisibility' );
	add_action('wp_ajax_ame_ajax_save_linkcategories', 'ame_ajax_save_linkcategories' );
}
?>