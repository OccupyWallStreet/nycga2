<?php
/**
 * Post-related functions
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
/* Some stuff for editing tags inline				*/
/* ************************************************ */

/**
 * Modifies the 'Tags' column header on the post management view
 *
 * @since 1.3.0
 * @author scripts@schloebe.de
 *
 * @param array
 * @return array
 */
function ame_column_tag_actions( $defaults ) {
	unset($defaults['tags']);
	$defaults['ame_tag_actions'] = '<abbr style="cursor:help;" title="' . __('Enhanced by Admin Management Xtended Plugin', 'admin-management-xtended') . ' ' . get_option("ame_version") . '">' . __('Tags') . '</abbr>';
	return $defaults;
}

/**
 * Adds content to the modified 'Tags' column on the post management view
 *
 * @since 1.3.0
 * @author scripts@schloebe.de
 *
 * @param string
 * @param int
 */
function ame_custom_column_tag_actions( $ame_column_name, $ame_id ) {
	global $wpdb, $locale;
	if( $ame_column_name == 'ame_tag_actions' ) {
		$tags = get_the_tags( $ame_id );
		$ame_post_tags = $ame_post_tags_plain = '';
		if ( !empty( $tags ) ) {
			$out = array();
			foreach ( $tags as $c ) {
				$out[] = '<a href="edit.php?tag=' . $c->slug . '"> ' . esc_html(sanitize_term_field('name', $c->name, $c->term_id, 'post_tag', 'display')) . '</a>';
				$out2[] = esc_html(sanitize_term_field('name', $c->name, $c->term_id, 'post_tag', 'display'));
			}
			$ame_post_tags .= join( ', ', $out );
			$ame_post_tags_plain .= join( ', ', $out2 );
		} else {
			$ame_post_tags .= __('No Tags');
			$ame_post_tags_plain .= '';
		}
		echo '<span id="ame_tags' . $ame_id . '">' . $ame_post_tags . '&nbsp;';
		if( current_user_can( 'edit_post', $ame_id ) ) {
			echo '<a id="tageditlink' . $ame_id . '" href="javascript:void(0);" onclick="ame_ajax_form_tags(' . $ame_id . ', \'' . $ame_post_tags_plain . '\');return false;" title="' . __('Edit') . '"><img src="' . AME_PLUGINFULLURL . 'img/' . AME_IMGSET . 'edit_small.gif" border="0" alt="' . __('Edit') . '" title="' . __('Edit') . '" /></a></span>';
		}
	}
}

add_action('manage_posts_custom_column', 'ame_custom_column_tag_actions', 2, 2);
add_filter('manage_posts_columns', 'ame_column_tag_actions', 2, 1);

/* ************************************************ */
/* Some stuff for editing categories inline			*/
/* ************************************************ */

/**
 * Modifies the 'Category' column header on the post management view
 *
 * @since 1.3.0
 * @author scripts@schloebe.de
 *
 * @param array
 * @return array
 */
function ame_column_category_actions( $defaults ) {
	unset($defaults['categories']);
	if( isset($defaults['tags']) && $defaults['tags'] ) {
		$defaults['ame_cat_actions'] = '<abbr style="cursor:help;" title="' . __('Enhanced by Admin Management Xtended Plugin', 'admin-management-xtended') . ' ' . get_option("ame_version") . '">' . __('Categories') . '</abbr>';
	}
	return $defaults;
}

/**
 * Adds content to the modified 'Category' column on the post management view
 *
 * @since 1.2.0
 * @author scripts@schloebe.de
 *
 * @param string
 * @param int
 */
function ame_custom_column_category_actions( $ame_column_name, $ame_id ) {
	global $wpdb, $locale;
	
	if( $ame_column_name == 'ame_cat_actions' ) {
		$categories = get_the_category( $ame_id );
		$ame_post_cats = "";
		if ( !empty( $categories ) ) {
			$out = array();
			foreach ( $categories as $c ) {
				$out[] = "<a href='edit.php?category_name=$c->slug'> " . esc_html(sanitize_term_field('name', $c->name, $c->term_id, 'category', 'display')) . "</a>";
			}
			$ame_post_cats .= join( ', ', $out );
		} else {
			$ame_post_cats .= __('Uncategorized');
		}
		if( current_user_can( 'edit_post', $ame_id ) ) {
		?>
		<div class="categorychoosewrap" id="categorychoosewrap<?php echo $ame_id; ?>" style="width:300px;height:415px;overflow:auto;display:none;"></div>
		<?php
		}
		echo '<span id="ame_category' . $ame_id . '">' . $ame_post_cats . '</span>&nbsp;';
		if( current_user_can( 'edit_post', $ame_id ) ) {
			echo '<a href="#" title="' . __('Edit') . '" onclick="return ame_ajax_get_categories(' . $ame_id . ');"><img src="' . AME_PLUGINFULLURL . 'img/' . AME_IMGSET . 'edit_small.gif" border="0" alt="' . __('Edit') . '" title="' . __('Edit') . '" /></a>';
			echo '<a title="' . __('Categories') . '" class="thickbox" id="thickboxlink' . $ame_id . '" href="#TB_inline?height=425&amp;width=300&amp;inlineId=categorychoosewrap' . $ame_id . '"></a>';
		}
	}
}

add_action('manage_posts_custom_column', 'ame_custom_column_category_actions', 1, 2);
add_filter('manage_posts_columns', 'ame_column_category_actions', 1, 1);



/* ************************************************ */
/* Adding the columns and data						*/
/* ************************************************ */

/**
 * Add a new 'Actions' column to the post management view
 *
 * @since 0.7
 * @author scripts@schloebe.de
 *
 * @param array
 * @return array
 */
function ame_column_post_actions( $defaults ) {
	$defaults['ame_post_actions'] = '<abbr style="cursor:help;" title="' . __('Enhanced by Admin Management Xtended Plugin', 'admin-management-xtended') . ' ' . get_option("ame_version") . '">' . __('Actions', 'admin-management-xtended') . '</abbr>' . ame_changeImgSet();
	return $defaults;
}

/**
 * Adds content to the new 'Actions' column on the post management view
 *
 * @since 0.7
 * @author scripts@schloebe.de
 *
 * @param string
 * @param int
 */
function ame_custom_column_post_actions( $ame_column_name, $ame_id ) {
	global $wpdb, $locale;
	if( $ame_column_name == 'ame_post_actions' && current_user_can( 'edit_post', $ame_id ) ) {
    	$post_status = get_post_status($ame_id); $q_post = get_post($ame_id);
    	echo '<div style="width:90px;" class="ame_options">';
		
    	// Visibility icon
    	$visstatus = ( $post_status == 'publish' ) ? 'draft' : 'publish';
    	echo '<div id="visicon' . $ame_id . '" style="padding:1px;float:left;"><a href="javascript:void(0);" onclick="ame_ajax_set_visibility(' . $ame_id . ', \'' . $visstatus . '\', \'post\');return false;"><img src="' . AME_PLUGINFULLURL . 'img/' . AME_IMGSET . $visstatus . '.png" border="0" alt="' . __('Toggle visibility', 'admin-management-xtended') . '" title="' . __('Toggle visibility', 'admin-management-xtended') . '" /></a></div>';
		
    	// Sticky icon
    	$stickyimg = ( is_sticky( $ame_id ) ) ? 'sticky.png' : 'nosticky.png';
    	echo '<div id="stickyicon' . $ame_id . '" style="padding:1px;float:left;"><a href="javascript:void(0);" onclick="ame_ajax_set_sticky(' . $ame_id . ');return false;"><img src="' . AME_PLUGINFULLURL . 'img/' . AME_IMGSET . $stickyimg . '" border="0" alt="' . __('Stick this post to the front page') . '" title="' . __('Stick this post to the front page') . '" /></a></div>';
		
    	// Date icon
    	echo '<div id="date' . $ame_id . '" style="padding:1px;float:left;"><a href="javascript:void(0);" class="date-pick" id="datepicker' . $ame_id . '"><img src="' . AME_PLUGINFULLURL . 'img/' . AME_IMGSET . 'date.png" border="0" alt="' . __('Change Publication Date', 'admin-management-xtended') . '" title="' . __('Change Publication Date', 'admin-management-xtended') . '" /></a></div>';
		
    	// Slug edit icon
    	echo '<div id="slug' . $ame_id . '" style="padding:1px;float:left;"><a href="javascript:void(0);" id="slugedit' . $ame_id . '" onclick="ame_slug_edit(' . $ame_id . ', \'post\');"><img src="' . AME_PLUGINFULLURL . 'img/' . AME_IMGSET . 'slug_edit.png" border="0" alt="' . __('Edit Post Slug', 'admin-management-xtended') . '" title="' . __('Edit Post Slug', 'admin-management-xtended') . '" /></a></div>';
		
    	// Comment open/closed status icon
    	$comment_status = $q_post->comment_status;
    	if( $comment_status == 'open' ) { $c_status = 0; $c_img = '_open'; } else { $c_status = 1; $c_img = '_closed'; }
    	echo '<div id="commentstatus' . $ame_id . '" style="padding:1px;float:left;"><a href="javascript:void(0);" onclick="ame_ajax_set_commentstatus(' . $ame_id . ', ' . $c_status . ', \'post\');return false;"><img src="' . AME_PLUGINFULLURL . 'img/' . AME_IMGSET . 'comments' . $c_img . '.png" border="0" alt="' . __('Toggle comment status open/closed', 'admin-management-xtended') . '" title="' . __('Toggle comment status open/closed', 'admin-management-xtended') . '" /></a></div> ';
		
		// Post revisions
		if( function_exists('wp_list_post_revisions') && wp_get_post_revisions( $ame_id ) ) {
			echo '<div class="amehasrev" id="amerevisionwrap' . $ame_id . '" style="width:300px;height:165px;overflow:auto;display:none;">';
			wp_list_post_revisions( $ame_id );
			echo '</div>';
		}
		
		echo '</div>';
	}
}

add_action('manage_posts_custom_column', 'ame_custom_column_post_actions', 500, 2);
add_filter('manage_posts_columns', 'ame_column_post_actions', 500, 2);

if ( get_option('ame_show_orderoptions') == '1' ) {
	add_action('manage_posts_custom_column', 'ame_custom_column_page_order', 500, 2);
	add_filter('manage_posts_columns', 'ame_column_page_order', 500, 2);
}
?>