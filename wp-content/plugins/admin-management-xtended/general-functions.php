<?php
/**
 * General functions used globally
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
/* Localization												 */
/* ************************************************ */

/**
 * Checks if a current locale file used for popout calendar exists
 *
 * @since 0.7
 * @author scripts@schloebe.de
 *
 * @return bool
 */
function ame_locale_exists() {
	$cur_locale = get_locale();
	$ame_date_locale_path = AME_PLUGINFULLURL . 'js/jquery-addons/date_' . $cur_locale . '.js';
	if( file_exists( $ame_date_locale_path ) || !empty( $cur_locale ) ) {
		return true;
	} else {
		return false;
	}
}



/* ************************************************ */
/* Define the Ajax response functions				*/
/* ************************************************ */

/**
 * Returns the given parameter instead of echoing it
 *
 * @since 0.7
 * @author scripts@schloebe.de
 *
 * @return string|int|mixed
 */
function return_function( $output ) {
	return $output;
}


/**
 * SACK response function for saving media description
 *
 * @since 1.5.0
 * @author scripts@schloebe.de
 */
function ame_ajax_save_mediadesc() {
	global $wpdb;
	$postid = intval( $_POST['postid'] );
	$new_mediadesc = $_POST['new_mediadesc'];
	
	$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET post_excerpt = %s WHERE ID = %d", stripslashes( $new_mediadesc ), $postid ) );
	$ame_media_desc = '<span id="ame_mediadesc_text' . $postid . '">' . $new_mediadesc . '</span>';
	$ame_media_desc .= '&nbsp;<a id="mediadesceditlink' . $postid . '" href="javascript:void(0);" onclick="ame_ajax_form_mediadesc(' . $postid . ');return false;" title="' . __('Edit') . '"><img src="' . AME_PLUGINFULLURL . 'img/' . AME_IMGSET . 'edit_small.gif" border="0" alt="' . __('Edit') . '" title="' . __('Edit') . '" /></a>';
	do_action('edit_attachment', $postid);
	die( "jQuery('span#ame_mediadesc" . $postid . "').fadeOut('fast', function() {
		jQuery('span#ame_mediadesc" . $postid . "').html('" . addslashes_gpc( $ame_media_desc ) . "').fadeIn('fast');
	});" );
}

/**
 * SACK response function for saving comment status for a post
 *
 * @since 1.2.0
 * @author scripts@schloebe.de
 */
function ame_ajax_set_commentstatus() {
	global $wpdb;
	$postid = intval($_POST['postid']);
	$q_status = intval($_POST['comment_status']);
	( $q_status == '1' ) ? $status = 'open' : $status = 'closed';
	if( is_string($_POST['posttype']) ) $posttype = $_POST['posttype'];
	$post = get_post( $postid );
	
	if ( $status == 'open' ) {
		$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET comment_status = %s WHERE ID = %d", $status, $postid ) );
		AdminManagementXtended::fireActions( 'post', $postid, $post );
		die( "jQuery('#commentstatus" . $postid . "').html('<a href=\"javascript:void(0);\" onclick=\"ame_ajax_set_commentstatus(" . $postid . ", 0, \'" . $posttype . "\');return false;\"><img src=\"" . AME_PLUGINFULLURL . "img/" . AME_IMGSET . "comments_open.png\" border=\"0\" alt=\"" . __('Toggle comment status open/closed', 'admin-management-xtended') . "\" title=\"" . __('Toggle comment status open/closed', 'admin-management-xtended') . "\" /></a>');jQuery('#" . $posttype . "-" . $postid . " td, #" . $posttype . "-" . $postid . " th').animate( { backgroundColor: '#EAF3FA' }, 300).animate( { backgroundColor: '#F9F9F9' }, 300).animate( { backgroundColor: '#EAF3FA' }, 300).animate( { backgroundColor: '#F9F9F9' }, 300);" );
	} else {
		$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET comment_status = %s WHERE ID = %d", $status, $postid ) );
		AdminManagementXtended::fireActions( 'post', $postid, $post );
		die( "jQuery('#commentstatus" . $postid . "').html('<a href=\"javascript:void(0);\" onclick=\"ame_ajax_set_commentstatus(" . $postid . ", 1, \'" . $posttype . "\');return false;\"><img src=\"" . AME_PLUGINFULLURL . "img/" . AME_IMGSET . "comments_closed.png\" border=\"0\" alt=\"" . __('Toggle comment status open/closed', 'admin-management-xtended') . "\" title=\"" . __('Toggle comment status open/closed', 'admin-management-xtended') . "\" /></a>');jQuery('#" . $posttype . "-" . $postid . " td, #" . $posttype . "-" . $postid . " th').animate( { backgroundColor: '#EAF3FA' }, 300).animate( { backgroundColor: '#F9F9F9' }, 300).animate( { backgroundColor: '#EAF3FA' }, 300).animate( { backgroundColor: '#F9F9F9' }, 300);" );
	}
}

/**
 * SACK response function for saving page order
 *
 * @since 1.1.0
 * @author scripts@schloebe.de
 */
function ame_get_pageorder() {
	global $wpdb, $post;
	$pageorder2 = $_POST['pageordertable2'];
	parse_str( $pageorder2 );
	$orderval = ""; $i = 0;
	foreach( $pageordertable as $value ) {
		//$value = intval( substr( $value, 5 ) );
		$has_parent = get_post( $value );
		if( $value != '0' && empty( $has_parent->post_parent ) ) {
			$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET menu_order = %d WHERE ID = %d AND post_type = 'page'", $i, $value ) );
			$i++;
		}
	}
	
	die( "jQuery(\"#ame_ordersave_loader\").html('');" );
}

/**
 * SACK response function for saving post tags
 *
 * @since 1.3.0
 * @author scripts@schloebe.de
 */
function ame_ajax_save_tags() {
	global $wpdb;
	$postid = intval( $_POST['postid'] );
	$ame_tags = $_POST['new_tags'];
	
	$tagarray = explode(",", trim( $ame_tags ));
	wp_set_post_tags($postid, $tagarray);
	unset($GLOBALS['tag_cache']);
	
	$tags = get_the_tags( $postid );
	$ame_post_tags = ''; $ame_post_tags_plain = '';
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
	$ame_post_tags .= '&nbsp;<a id="tageditlink' . $postid . '" href="javascript:void(0);" onclick="ame_ajax_form_tags(' . $postid . ', \'' . $ame_post_tags_plain . '\');return false;" title="' . __('Edit') . '"><img src="' . AME_PLUGINFULLURL . 'img/' . AME_IMGSET . 'edit_small.gif" border="0" alt="' . __('Edit') . '" title="' . __('Edit') . '" /></a>';
	$post = get_post( $postid );
	do_action('edit_post', $postid, $post);
	do_action('save_post', $postid, $post);
	die( "jQuery('span#ame_tags" . $postid . "').fadeOut('fast', function() {
		jQuery('span#ame_tags" . $postid . "').html('" . addslashes_gpc( $ame_post_tags ) . "').fadeIn('fast');
	});" );
}

/**
 * SACK response function for getting post categories and compiling them into a category checklist
 *
 * @since 2.3.5
 * @author scripts@schloebe.de
 * @uses wp_category_checklist()
 */
function ame_ajax_get_categories() {
	global $wpdb, $post;
	$ame_id = intval( $_POST['postid'] );
	;
	echo '<div id="categorychoose' . $ame_id . '" class="categorydiv">';
	echo '<div class="button-group">';
	echo '<a href="javascript:void(0);" class="button small" onclick="ame_check_all(' . $ame_id . ', true);">' . __('Check All') . '</a><a href="javascript:void(0);" class="button small" onclick="ame_check_all(' . $ame_id . ', false);">' . __('Uncheck All') . '</a>';
	echo '</div><br />';
	echo '<ul id="categorychecklist" class="list:category categorychecklist form-no-clear" style="height:365px;overflow:auto;">';
	wp_category_checklist( $ame_id, 0, get_option('default_category') );
	echo '</ul>';
	echo '<div style="text-align:center;">';
	echo get_submit_button( __('Save'), 'button button-primary primary large', 'save', false, 'onclick="ame_ajax_save_categories(' . $ame_id . ');return false;"' );
	echo "&nbsp;";
	echo get_submit_button( __('Cancel'), 'button button-secondary secondary', 'cancel', false, 'onclick="tb_remove();"' );
	echo '</div>';
	echo '</div>';
	
	die("");
}

/**
 * SACK response function for saving post categories
 *
 * @since 1.2.0
 * @author scripts@schloebe.de
 */
function ame_ajax_save_categories() {
	global $wpdb, $post;
	$postid = intval( $_POST['postid'] );
	$ame_cats = $_POST['ame_cats'];
	
	$ame_categories = substr( $ame_cats, 0, -1 );
	$catarray = explode(",", $ame_categories);
	wp_set_post_categories($postid, $catarray);
	unset($GLOBALS['category_cache']);
	
	$categories = get_the_category( $postid );
	$ame_post_cats = "";
	if ( !empty( $categories ) ) {
		$out = array();
		foreach ( $categories as $c ) {
			$out[] = '<a href="edit.php?category_name=' . $c->slug . '"> ' . esc_html(sanitize_term_field('name', $c->name, $c->term_id, 'category', 'display')) . '</a>';
		}
		$ame_post_cats = join( ', ', $out );
	} else {
		$ame_post_cats = __('Uncategorized');
	}
	do_action('edit_post', $postid, get_post($postid));
	do_action('save_post', $postid, get_post($postid));
	die( "re_init();jQuery('span#ame_category" . $postid . "').fadeOut('fast', function() {
		jQuery('a#thickboxlink" . $postid . "').show();
		jQuery('span#ame_category" . $postid . "').html('" . addslashes_gpc( $ame_post_cats ) . "').fadeIn('fast');
	});" );
}

/**
 * SACK response function for saving draft post visibility option
 *
 * @since 0.9
 * @author scripts@schloebe.de
 */
function ame_toggle_showinvisposts() {
	global $wpdb;
	$status = intval($_POST['status']);
	
	update_option("ame_toggle_showinvisposts", $status);
	die( "location.reload();" );
}

/**
 * SACK response function for toggling button image sets option
 *
 * @since 1.3.0
 * @author scripts@schloebe.de
 */
function ame_ajax_toggle_imageset() {
	global $wpdb;
	$setid = intval($_POST['setid']);
	
	update_option("ame_imgset", "set" . $setid);
	die( "location.reload();" );
}

/**
 * SACK response function for saving order input option
 *
 * @since 1.0
 * @author scripts@schloebe.de
 */
function ame_toggle_orderoptions() {
	global $wpdb;
	$status = intval($_POST['status']);
	
	update_option("ame_show_orderoptions", $status);
	die( "location.reload();" );
}

/**
 * SACK response function for displaying the slug edit form inline
 *
 * @since 1.0
 * @author scripts@schloebe.de
 */
function ame_slug_edit() {
	global $wpdb;
	$catid = intval($_POST['category_id']);
	if( is_string($_POST['posttype']) ) $posttype = $_POST['posttype'];
	if( $posttype == 'post' ) { $postnumber = '1'; } elseif( $posttype == 'page' ) { $postnumber = '2'; }
	$curpostslug = $wpdb->get_var( $wpdb->prepare( "SELECT post_name FROM $wpdb->posts WHERE ID = %d", $catid ) );
	$cols = intval( $_POST['col_no'] );
	
	$addHTML = "<tr id='alter" . $posttype . "-" . $catid . "' class='author-other status-publish' valign='middle'><td colspan='" . $cols . "' align='center'> <input type='text' value='" . $curpostslug . "' size='50' id='ame_slug" . $catid . "' /> <div class='button-group'><input value='" . __('Save') . "' class='button button-primary primary small' type='button' onclick='ame_ajax_slug_save(" . $catid . ", " . $postnumber . ");' /><input value='" . __('Cancel') . "' class='button button-secondary secondary small' type='button' onclick='ame_edit_cancel(" . $catid . ");' /></div></td></tr>";
	die( "jQuery('#" . $posttype . "-" . $catid . "').after( \"" . $addHTML . "\" ); jQuery('#" . $posttype . "-" . $catid . "').hide();" );
}

/**
 * SACK response function for displaying the author edit form inline
 *
 * @since 1.7.0
 * @author scripts@schloebe.de
 */
function ame_author_edit() {
	global $wpdb, $current_user;
	$postid = intval($_POST['post_id']);
	$cols = intval( $_POST['col_no'] );
	if( is_string($_POST['posttype']) ) $posttype = $_POST['posttype'];
	if( $posttype == 'post' ) { $typenumber = '1'; } elseif( $posttype == 'page' ) { $typenumber = '2'; }
	if( $typenumber == '1' && !current_user_can('edit_post', $postid) ) {
		die( "alert('" . esc_js( __('You are not allowed to change the post author as this user.') ) . "');" );
		return;
	} elseif( $typenumber == '2' && !current_user_can('edit_page', $postid) ) {
		die( "alert('" . esc_js( __('You are not allowed to change the page author as this user.') ) . "');" );
		return;
	}
	$post = get_post( $postid );
	
	$authors = get_users( array( 'user_id' => $current_user->ID ) ); // TODO: ROLE SYSTEM
 	if ( $post->post_author && !in_array($post->post_author, $authors) )
		$authors[] = $post->post_author;
	if ( $authors && count( $authors ) > 1 ) {
		$output = wp_dropdown_users( array(
			'echo' 				=> 0,
			'who' 				=> 'authors',
			'name' 				=> 'author-' . $postid,
			'selected' 			=> empty($post->ID) ? $current_user->ID : $post->post_author,
			'include_selected' 	=> true
		) );
	} else {
		if( $typenumber == '1' ) {
			die( "alert('" . esc_js( __('You are not allowed to change the post author as this user.') ) . "');" );
			return;
		} elseif( $typenumber == '2' ) {
			die( "alert('" . esc_js( __('You are not allowed to change the page author as this user.') ) . "');" );
			return;
		}
	}
	$output = str_replace("\n", "", $output);
	
	$addHTML = "<tr id='alter" . $posttype . "-" . $postid . "' class='author-other status-publish' valign='middle'><td colspan='" . $cols . "' align='center'>" . $output . " <div class='button-group'><input value='" . __('Save') . "' class='button button-primary primary' type='button' onclick='ame_ajax_author_save(" . $postid . ", " . $typenumber . ");' /> <input value='" . __('Cancel') . "' class='button button-secondary secondary' type='button' onclick='ame_edit_cancel($postid)' /></div></td></tr>";
	die( "jQuery('#" . $posttype . "-" . $postid . "').after( \"" . $addHTML . "\" ); jQuery('#" . $posttype . "-" . $postid . "').hide();" );
}

/**
 * SACK response function for saving page order from direct input
 *
 * @since 1.0
 * @author scripts@schloebe.de
 */
function ame_save_order() {
	global $wpdb;
	$catid = intval( $_POST['category_id'] );
	$neworderid = intval( $_POST['new_orderid'] );
	if( is_string($_POST['posttype']) ) $posttype = $_POST['posttype'];
	
	$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET menu_order = %d WHERE ID = %d", $neworderid, $catid ) );
	die( "jQuery('span#ame_order_loader" . $catid . "').hide(); jQuery('#" . $posttype . "-" . $catid . " td, #" . $posttype . "-" . $catid . " th').animate( { backgroundColor: '#EAF3FA' }, 300).animate( { backgroundColor: '#F9F9F9' }, 300).animate( { backgroundColor: '#EAF3FA' }, 300).animate( { backgroundColor: '#F9F9F9' }, 300);" );
}

/**
 * SACK response function for saving page slug
 *
 * @since 1.0
 * @author scripts@schloebe.de
 */
function ame_save_slug() {
	global $wpdb;
	$catid = intval($_POST['category_id']);
	$new_slug = $_POST['new_slug'];
	if( empty( $new_slug ) || $new_slug == '' ) {
		$postinfo = get_post( $catid, ARRAY_A );
		$new_slug = $postinfo['post_title'];
	}
	$new_slug = sanitize_title( $new_slug );
	if( is_string($_POST['typenumber']) ) $posttype = $_POST['typenumber'];
	$current_page = basename($_SERVER['PHP_SELF'], ".php");
	if( $posttype == '1' ) { $posttype = 'post'; } elseif( $posttype == '2' ) { $posttype = 'page'; }
	
	$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET post_name = %s WHERE ID = %d", $new_slug, $catid ) );
	
	$post = get_post( $catid );
	AdminManagementXtended::fireActions( 'post', $catid, $post );
	die( "jQuery('#" . $posttype . "-" . $catid . "').show(); jQuery('#alter" . $posttype . "-" . $catid . "').hide(); jQuery('#" . $posttype . "-" . $catid . " td, #" . $posttype . "-" . $catid . " th').animate( { backgroundColor: '#EAF3FA' }, 300).animate( { backgroundColor: '#F9F9F9' }, 300).animate( { backgroundColor: '#EAF3FA' }, 300).animate( { backgroundColor: '#F9F9F9' }, 300);" );
}

/**
 * SACK response function for saving post author
 *
 * @since 1.7.0
 * @author scripts@schloebe.de
 */
function ame_save_author() {
	global $wpdb;
	$catid = intval($_POST['category_id']);
	$newauthorid = intval( $_POST['newauthor'] );
	if( is_string($_POST['typenumber']) ) $posttype = $_POST['typenumber'];
	$current_page = basename($_SERVER['PHP_SELF'], ".php");
	if( $posttype == '1' ) { $posttype = 'post'; } elseif( $posttype == '2' ) { $posttype = 'page'; }
	
	$post = get_post( $catid );
	AdminManagementXtended::fireActions( 'post', $catid, $post );
	$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET post_author = %d WHERE ID = %d", $newauthorid, $catid ) );
	die( "jQuery('#" . $posttype . "-" . $catid . "').show(); jQuery('#" . $posttype . "-" . $catid . " td, #" . $posttype . "-" . $catid . " th').animate( { backgroundColor: '#EAF3FA' }, 300).animate( { backgroundColor: '#F9F9F9' }, 300).animate( { backgroundColor: '#EAF3FA' }, 300).animate( { backgroundColor: '#F9F9F9' }, 300); jQuery('#alter" . $posttype . "-" . $catid . "').hide(); jQuery(\"a[href^='edit.php?author=" . $catid . "'], a[href^='edit-pages.php?author=" . $catid . "']\").html('" . $newauthorid . "');" );
}

/**
 * SACK response function for saving post//page title
 *
 * @since 0.7
 * @author scripts@schloebe.de
 */
function ame_save_title() {
	global $wpdb;
	$postid = intval($_POST['category_id']);
	$new_title = $_POST['new_title'];
	if( is_string($_POST['posttype']) ) $posttype = $_POST['posttype'];
	
	$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET post_title = %s WHERE ID = %d", stripslashes( $new_title ), $postid ) );
	
	$new_title = apply_filters( 'the_title', $new_title );
	$post = get_post( $postid );
	AdminManagementXtended::fireActions( 'post', $postid, $post );
	die( "jQuery('a[href*=\'post.php?post=" . $postid . "&action=edit\']').html('" . $new_title . "'); jQuery('#" . $posttype . "-" . $postid . "').show(); jQuery('#alter" . $posttype . "-" . $postid . "').hide(); jQuery('#" . $posttype . "-" . $postid . " td, #" . $posttype . "-" . $postid . " th').animate( { backgroundColor: '#EAF3FA' }, 300).animate( { backgroundColor: '#F9F9F9' }, 300).animate( { backgroundColor: '#EAF3FA' }, 300).animate( { backgroundColor: '#F9F9F9' }, 300);" );
}

/**
 * SACK response function for saving post/page date
 *
 * @since 0.7
 * @author scripts@schloebe.de
 */
function ame_set_date() {
	global $wpdb;
	$catid = intval(substr($_POST['category_id'], 10, 5));
	$newpostdate = date("Y-m-d H:i:s", strtotime( $_POST['pickedDate'] ));
	$newpostdate_gmt = get_gmt_from_date( $newpostdate );
	if( is_string($_POST['posttype']) ) $posttype = $_POST['posttype'];
	
	$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET post_date = %s WHERE ID = %d", $newpostdate, $catid ) );
	$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET post_date_gmt = %s WHERE ID = %d", $newpostdate_gmt, $catid ) );
	if( strtotime( current_time('mysql') ) < strtotime( $newpostdate ) ) {
		$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET post_status = 'future' WHERE ID = %d", $catid ) );
		$post = get_post( $catid );
		AdminManagementXtended::fireActions( 'post', $catid, $post );
		die( "jQuery('#" . $posttype . "-" . $catid . " abbr').html('" . date(__('Y/m/d'), strtotime( $newpostdate ) ) . "'); jQuery('#" . $posttype . "-" . $catid . "').removeClass('status-publish').addClass('status-future'); jQuery('#" . $posttype . "-" . $catid . " td, #" . $posttype . "-" . $catid . " th').animate( { backgroundColor: '#EAF3FA' }, 300).animate( { backgroundColor: '#F9F9F9' }, 300).animate( { backgroundColor: '#EAF3FA' }, 300).animate( { backgroundColor: '#F9F9F9' }, 300);" );
	} elseif ( strtotime( current_time('mysql') ) > strtotime( $newpostdate ) ) {
		if( $posttype == 'post' && !current_user_can( 'publish_posts' ) ) {
			die( "alert('" . esc_js( __('You are not allowed to edit this post.') ) . "');" );
			return;
		}
		//$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET post_status = 'publish' WHERE ID = %d", $catid ) );
		$post = get_post( $catid );
		AdminManagementXtended::fireActions( 'post', $catid, $post );
		die( "jQuery('#" . $posttype . "-" . $catid . " abbr').html('" . date(__('Y/m/d'), strtotime( $newpostdate ) ) . "'); jQuery('#" . $posttype . "-" . $catid . "').removeClass('status-future').addClass('status-publish'); jQuery('#" . $posttype . "-" . $catid . " td, #" . $posttype . "-" . $catid . " th').animate( { backgroundColor: '#EAF3FA' }, 300).animate( { backgroundColor: '#F9F9F9' }, 300).animate( { backgroundColor: '#EAF3FA' }, 300).animate( { backgroundColor: '#F9F9F9' }, 300);" );
	}
}

/**
 * SACK response function for toggling post/page visibility
 *
 * @since 0.7
 * @author scripts@schloebe.de
 */
function ame_toggle_visibility() {
	global $wpdb;
	$catid = intval($_POST['category_id']);
	if( is_string($_POST['vis_status']) ) $status = $_POST['vis_status'];
	if( is_string($_POST['posttype']) ) $posttype = $_POST['posttype'];
	$post_status = get_post_status( $catid );
	
	if ( $status == 'publish' ) {
		if( $posttype == 'post' && !current_user_can( 'publish_posts' ) ) {
			die( "alert('" . esc_js( __('Sorry, you do not have the right to publish this post.') ) . "');" );
			return;
		}
		if( $posttype == 'post' && $post_status == 'pending' ) {
			$postdate = current_time('mysql'); $postdate_gmt = get_gmt_from_date( $postdate );
			$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET post_date = %s WHERE ID = %d", $postdate, $catid ) );
			$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET post_date_gmt = %s WHERE ID = %d", $postdate_gmt, $catid ) );
		}
		$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET post_status = %s WHERE ID = %d", $status, $catid ) );
		$post = get_post( $catid );
		AdminManagementXtended::fireActions( 'post', $catid, $post );
		die( "jQuery('#visicon" . $catid . "').html('<a href=\"javascript:void(0);\" onclick=\"ame_ajax_set_visibility(" . $catid . ", \'draft\', \'" . $posttype . "\');return false;\"><img src=\"" . AME_PLUGINFULLURL . "img/" . AME_IMGSET . "draft.png\" border=\"0\" alt=\"" . __('Toggle visibility', 'admin-management-xtended') . "\" title=\"" . __('Toggle visibility', 'admin-management-xtended') . "\" /></a>');jQuery('#" . $posttype . "-" . $catid . " td, #" . $posttype . "-" . $catid . " th').animate( { backgroundColor: '#EAF3FA' }, 300).animate( { backgroundColor: '#F9F9F9' }, 300).animate( { backgroundColor: '#EAF3FA' }, 300).animate( { backgroundColor: '#F9F9F9' }, 300);jQuery('#" . $posttype . "-" . $catid . "').removeClass('status-draft').addClass('status-publish');" );
	} else {
		$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET post_status = %s WHERE ID = %d", $status, $catid ) );
		$post = get_post( $catid );
		AdminManagementXtended::fireActions( 'post', $catid, $post );
		die( "jQuery('#visicon$catid').html('<a href=\"javascript:void(0);\" onclick=\"ame_ajax_set_visibility(" . $catid . ", \'publish\', \'" . $posttype . "\');return false;\"><img src=\"" . AME_PLUGINFULLURL . "img/" . AME_IMGSET . "publish.png\" border=\"0\" alt=\"" . __('Toggle visibility', 'admin-management-xtended') . "\" title=\"" . __('Toggle visibility', 'admin-management-xtended') . "\" /></a>');jQuery('#" . $posttype . "-" . $catid . " td, #" . $posttype . "-" . $catid . " th').animate( { backgroundColor: '#EAF3FA' }, 300).animate( { backgroundColor: '#F9F9F9' }, 300).animate( { backgroundColor: '#EAF3FA' }, 300).animate( { backgroundColor: '#F9F9F9' }, 300);jQuery('#" . $posttype . "-" . $catid . "').removeClass('status-publish').addClass('status-draft');" );
	}
}

/**
 * SACK response function for toggling post/page sticky
 *
 * @since 2.3.0
 * @author scripts@schloebe.de
 */
function ame_toggle_sticky() {
	global $wpdb;
	$postid = intval($_POST['post_id']);
	$post = get_post( $postid );
	
	if ( is_sticky( $postid ) ) {
		unstick_post( $postid );
		AdminManagementXtended::fireActions( 'post', $postid, $post );
		die( "jQuery('#stickyicon" . $postid . "').html('<a href=\"javascript:void(0);\" onclick=\"ame_ajax_set_sticky(" . $postid . ");return false;\"><img src=\"" . AME_PLUGINFULLURL . "img/" . AME_IMGSET . "nosticky.png\" border=\"0\" alt=\"" . __('Stick this post to the front page') . "\" title=\"" . __('Stick this post to the front page') . "\" /></a>');jQuery('#post-" . $postid . " td, #post-" . $postid . " th').animate( { backgroundColor: '#EAF3FA' }, 300).animate( { backgroundColor: '#F9F9F9' }, 300).animate( { backgroundColor: '#EAF3FA' }, 300).animate( { backgroundColor: '#F9F9F9' }, 300);jQuery('#post-" . $postid . "');" );
	} else {
		stick_post( $postid );
		AdminManagementXtended::fireActions( 'post', $postid, $post );
		die( "jQuery('#stickyicon" . $postid . "').html('<a href=\"javascript:void(0);\" onclick=\"ame_ajax_set_sticky(" . $postid . ");return false;\"><img src=\"" . AME_PLUGINFULLURL . "img/" . AME_IMGSET . "sticky.png\" border=\"0\" alt=\"" . __('Stick this post to the front page') . "\" title=\"" . __('Stick this post to the front page') . "\" /></a>');jQuery('#post-" . $postid . " td, #post-" . $postid . " th').animate( { backgroundColor: '#EAF3FA' }, 300).animate( { backgroundColor: '#F9F9F9' }, 300).animate( { backgroundColor: '#EAF3FA' }, 300).animate( { backgroundColor: '#F9F9F9' }, 300);jQuery('#post-" . $postid . "');" );
	}
}

/**
 * SACK response function for toggling page exclusion status
 *
 * @since 2.1.0
 * @author scripts@schloebe.de
 * @link http://plugins.trac.wordpress.org/browser/exclude-pages/trunk/exclude_pages.php#L162
 */
function ame_toggle_excludestatus() {
	$pageid = intval($_POST['pageid']);
	$statusid = intval($_POST['statusid']);
	$excluded_ids = ep_get_excluded_ids();
	if( $statusid == 1 ) {
		array_push( $excluded_ids, $pageid );
		$excluded_ids = array_unique( $excluded_ids );
	} else {
		$index = array_search( $pageid, $excluded_ids );
		if ( $index !== false ) unset( $excluded_ids[$index] );
	}
	$excluded_ids_str = implode( ',', $excluded_ids );
	ep_set_option( EP_OPTION_NAME, $excluded_ids_str, "Comma separated list of post and page IDs to exclude when returning pages from the get_pages function." );
	
	if( $statusid == 0 ) { $e_status = 1; $e_img = '_off'; } else { $e_status = 0; $e_img = ''; }
	die( "jQuery('#excludepagewrap$pageid').html('<a tip=\"" . __('Plugin: Exclude Pages - Exclude page from navigation', 'admin-management-xtended') . "\" href=\"javascript:void(0);\" onclick=\"ame_ajax_set_excludestatus(\'" . $pageid . "\', \'" . $e_status . "\');return false;\"><img src=\"" . AME_PLUGINFULLURL . "img/" . AME_IMGSET . "excludepages" . $e_img . ".gif\" border=\"0\" alt=\"" . __('Plugin: Exclude Pages - Exclude page from navigation', 'admin-management-xtended') . "\" title=\"" . __('Plugin: Exclude Pages - Exclude page from navigation', 'admin-management-xtended') . "\" /></a>');jQuery('#page-" . $pageid . " td, #page-" . $pageid . " th').animate( { backgroundColor: '#EAF3FA' }, 300).animate( { backgroundColor: '#F9F9F9' }, 300).animate( { backgroundColor: '#EAF3FA' }, 300).animate( { backgroundColor: '#F9F9F9' }, 300);" );
}

if( function_exists('add_action') ) {
	add_action('wp_ajax_ame_toggle_visibility', 'ame_toggle_visibility' );
	add_action('wp_ajax_ame_set_date', 'ame_set_date' );
	add_action('wp_ajax_ame_save_title', 'ame_save_title' );
	add_action('wp_ajax_ame_save_slug', 'ame_save_slug' );
	add_action('wp_ajax_ame_slug_edit', 'ame_slug_edit' );
	add_action('wp_ajax_ame_save_order', 'ame_save_order' );
	add_action('wp_ajax_ame_toggle_orderoptions', 'ame_toggle_orderoptions' );
	add_action('wp_ajax_ame_toggle_showinvisposts', 'ame_toggle_showinvisposts' );
	add_action('wp_ajax_ame_get_pageorder', 'ame_get_pageorder' );
	add_action('wp_ajax_ame_ajax_save_categories', 'ame_ajax_save_categories' );
	add_action('wp_ajax_ame_ajax_get_categories', 'ame_ajax_get_categories' );
	add_action('wp_ajax_ame_ajax_set_commentstatus', 'ame_ajax_set_commentstatus' );
	add_action('wp_ajax_ame_ajax_save_tags', 'ame_ajax_save_tags' );
	add_action('wp_ajax_ame_ajax_toggle_imageset', 'ame_ajax_toggle_imageset' );
	add_action('wp_ajax_ame_ajax_save_mediadesc', 'ame_ajax_save_mediadesc' );
	add_action('wp_ajax_ame_author_edit', 'ame_author_edit' );
	add_action('wp_ajax_ame_save_author', 'ame_save_author' );
	add_action('wp_ajax_ame_toggle_excludestatus', 'ame_toggle_excludestatus' );
	add_action('wp_ajax_ame_toggle_sticky', 'ame_toggle_sticky' );
}



/* ************************************************ */
/* Write JS into our admin header					*/
/* ************************************************ */

/**
 * Writes the javascript stuff into page header needed for the JS popout calendar
 *
 * @since 0.7
 * @author scripts@schloebe.de
 * @author Jeff Cole <upekshapriya@coolcave.co.uk>
 */
function ame_js_jquery_datepicker_header() {
	$current_page = basename($_SERVER['PHP_SELF'], ".php");
	$posttype = "";
	if( $current_page == 'edit' && isset($_GET['post_type']) && $_GET['post_type'] == 'page' ) {
		$posttype = "post";
	} elseif( $current_page == 'edit' ) {
		$posttype = "post";
	} elseif ( $current_page == 'edit-pages' ) {
		$posttype = "page";
	}
	if( ($current_page == 'edit-pages' || ( $current_page == 'edit' && isset($_GET['post_type']) && $_GET['post_type'] == 'page' )) && get_option('ame_show_orderoptions') == '2' ) {
		echo "<script type=\"text/javascript\">
//<![CDATA[
jQuery(document).ready(function() {
	jQuery(\".widefat th#title, .widefat th.column-title\").before('<th id=\"ame_sort\">&nbsp;</th>');
	jQuery(\".widefat:first td.post-title\").each(function() {
		jQuery(this).before('<td class=\"ame_sort\" title=\"" . __('Change page order', 'admin-management-xtended') . "\">&nbsp;</td>');
	});
	jQuery(\".widefat\").attr(\"id\", \"pageordertable\");
	jQuery(\"#pageordertable > thead > tr\").attr(\"id\", \"page-0\");
	jQuery(\"tr:has('a:contains('—')')\").addClass('nodrop').addClass('nodrag');
    jQuery(\"#pageordertable\").tableDnD({
    	dragHandle: \"ame_sort\",
    	scrollAmount: \"30\",
    	onDragClass: \"ondragrow\",
    	onDragStart: function(table, row) {
    		//jQuery(\"tr[class*=\'nodrop\']\").addClass('cannotdrop');
    		jQuery(\"tr[class*=\'nodrop\'] a\").css( { opacity: 0.3 }, 600);
    	},
    	onDrop: function(table, row) {
    		//jQuery(\"tr[class*=\'cannotdrop\']\").show();
    		jQuery(\"tr[class*=\'nodrop\'] a\").css( { opacity: 1.0 }, 600);
    		jQuery(\"tr[class*=\'cannotdrop\']\").removeClass('cannotdrop');
    		jQuery(\"#ame_ordersave_loader\").html(\"<img src='" . AME_PLUGINFULLURL . "img/" . AME_IMGSET . "loader2.gif' border='0' alt='' align='absmiddle' /> | \");
    		ame_ajax_get_pageorder( jQuery.tableDnD.serialize() );
    	}
    });
});
//]]>
</script>
\n";
	}
	if( $current_page == 'edit' && !isset( $_GET['page'] ) ) {
	echo "<script type=\"text/javascript\" charset=\"utf-8\">
//<![CDATA[
function ame_setupSuggest( ame_suggestid ) {
	jQuery('#ame-new-tags' + ame_suggestid).suggest( '" . get_bloginfo('wpurl') . "/wp-admin/admin-ajax.php?action=ajax-tag-search&tax=post_tag', { delay: 500, minchars: 2, multiple: true, multipleSep: \", \" } );
}
//]]>
</script>\n";
	}
	echo "<link rel='stylesheet' href='" . AME_PLUGINFULLURL . "css/datePicker.css' type='text/css' />\n";
	echo "<script type=\"text/javascript\" charset=\"utf-8\">
//<![CDATA[
Date.firstDayOfWeek = 1;
Date.format = 'yyyy-mm-dd';\n";
echo "jQuery.dpText = {
\tTEXT_PREV_YEAR		:	'" . __('Previous Year', 'admin-management-xtended') . "',
\tTEXT_PREV_MONTH		:	'" . __('Previous Month', 'admin-management-xtended') . "',
\tTEXT_NEXT_YEAR		:	'" . __('Next Year', 'admin-management-xtended') . "',
\tTEXT_NEXT_MONTH		:	'" . __('Next Month', 'admin-management-xtended') . "',
\tTEXT_CLOSE		:	'" . __('Close', 'admin-management-xtended') . "',
\tTEXT_CHOOSE_DATE	:	'" . __('Pick Date', 'admin-management-xtended') . "'
};\n";
	echo "jQuery(function() {
	jQuery('.date-pick')
		.datePicker({startDate:'2000-01-01', createButton:false, displayClose:true})
		.dpSetPosition(jQuery.dpConst.POS_TOP, jQuery.dpConst.POS_RIGHT)
		.live(
			'click',
			function() {
				jQuery(this).dpDisplay();
				this.blur();
				return false;
			}
		)
		.live(
			'dateSelected',
			function(e, selectedDate) {
				var cat_id = this.id;
				var ame_hour = jQuery('#ame_hour').val();
				var ame_minute = jQuery('#ame_minutes').val();
				( ame_hour > 23) ? ame_hour = '12' : ame_hour = ame_hour;
				( ame_minute > 59) ? ame_minute = '00' : ame_minute = ame_minute;
				var selDate = selectedDate.getFullYear() + '-' + (Number(selectedDate.getMonth())+1) + '-' + selectedDate.getDate() + ' ' + ame_hour + ':' + ame_minute + ':' + '00';
				ame_ajax_set_postdate( cat_id, selDate, posttype='" . $posttype . "' );
				//alert( selDate );
			}
		);
});
//]]>
</script>\n";
if( $current_page == 'edit-pages' || $current_page == 'edit' ) {
	if( $current_page == 'edit-pages' || ( $current_page == 'edit' && isset($_GET['post_type']) && $_GET['post_type'] == 'page' ) ) $ame_column_heading = __('Edit Page Order:', 'admin-management-xtended'); else $ame_column_heading = __('Edit Post Order:', 'admin-management-xtended');
	
	if ( get_option('ame_show_orderoptions') == '0' ) {
		$dnd_text = ($current_page != 'edit' || ( $current_page == 'edit' && $_GET['post_type'] == 'page' )) ? " <a class='page-numbers' href='javascript:void(0);' onclick='ame_ajax_toggle_orderoptions(2)'>" . __('Drag & Drop', 'admin-management-xtended') . "</a>" : "";
		echo "<script type=\"text/javascript\" charset=\"utf-8\">
jQuery(document).ready(function() {
  jQuery(\"div.wrap div[class*='tablenav']:first\").prepend(\"<div class='tablenav-pages'>&nbsp;&nbsp;|&nbsp;<span id='ame_order2_loader' class='displaying-num'>" . $ame_column_heading . "</span> <span class='page-numbers current'>" . __('Off', 'admin-management-xtended') . "</span> <a class='page-numbers' href='javascript:void(0);' onclick='ame_ajax_toggle_orderoptions(1)'>" . __('Direct input', 'admin-management-xtended') . "</a>$dnd_text</div>\");
});
</script>\n";
	} elseif ( get_option('ame_show_orderoptions') == '1' ) {
		$dnd_text = ($current_page != 'edit' || ( $current_page == 'edit' && isset($_GET['post_type']) && $_GET['post_type'] == 'page' )) ? " <a class='page-numbers' href='javascript:void(0);' onclick='ame_ajax_toggle_orderoptions(2)'>" . __('Drag & Drop', 'admin-management-xtended') . "</a>" : "";
		echo "<script type=\"text/javascript\" charset=\"utf-8\">
jQuery(document).ready(function() {
   jQuery(\"div.wrap div[class*='tablenav']:first\").prepend(\"<div class='tablenav-pages'>&nbsp;&nbsp;|&nbsp;<span id='ame_order2_loader' class='displaying-num'>" . $ame_column_heading . "</span> <a class='page-numbers' href='javascript:void(0);' onclick='ame_ajax_toggle_orderoptions(0)'>" . __('Off', 'admin-management-xtended') . "</a> <span class='page-numbers current'>" . __('Direct input', 'admin-management-xtended') . "</span>$dnd_text</div>\");
});
</script>\n";
	} elseif ( get_option('ame_show_orderoptions') == '2' ) {
		$dnd_text = ($current_page != 'edit' || ( $current_page == 'edit' && isset($_GET['post_type']) && $_GET['post_type'] == 'page' )) ? " <span class='page-numbers current'>" . __("Drag & Drop <a href='http://wordpress.org/extend/plugins/admin-management-xtended/other_notes/' target='_blank'>[?]</a>", 'admin-management-xtended') . "</span>" : "";
		echo "<script type=\"text/javascript\" charset=\"utf-8\">
jQuery(document).ready(function() {
   jQuery(\"div.wrap div[class*='tablenav']:first\").prepend(\"<div class='tablenav-pages'>&nbsp;&nbsp;|&nbsp;<span id='ame_ordersave_loader'></span> <span id='ame_order2_loader' class='displaying-num'>" . $ame_column_heading . "</span> <a class='page-numbers' href='javascript:void(0);' onclick='ame_ajax_toggle_orderoptions(0)'>" . __('Off', 'admin-management-xtended') . "</a> <a class='page-numbers' href='javascript:void(0);' onclick='ame_ajax_toggle_orderoptions(1)'>" . __('Direct input', 'admin-management-xtended') . "</a>$dnd_text</div>\");
});
</script>\n";
	}
}
if( $current_page == 'edit' && isset($_GET['post_type']) && $_GET['post_type'] != 'page' ) {
	if ( get_option('ame_toggle_showinvisposts') == '1' && !isset( $_GET['page'] ) ) {
		echo "<script type=\"text/javascript\" charset=\"utf-8\">
jQuery(document).ready(function() {
   jQuery(\"div.wrap div[class='tablenav']:last div[class='tablenav-pages']\").after(\"<div class='alignleft' style='margin-right:5px;'><input type='button' value='" . __('Hide invisible Posts', 'admin-management-xtended') . "' class='button-secondary' onclick='ame_ajax_toggle_showinvisposts(0)' id='ame_toggle_showinvisposts' /></div>\");
});
</script>\n";
	} elseif ( get_option('ame_toggle_showinvisposts') == '0' && !isset( $_GET['page'] ) ) {
		echo "<script type=\"text/javascript\" charset=\"utf-8\">
jQuery(document).ready(function() {
   jQuery(\"div.wrap div[class='tablenav']:last div[class='tablenav-pages']\").after(\"<div class='alignleft' style='margin-right:5px;'><input type='button' value='" . __('Show invisible Posts', 'admin-management-xtended') . "' class='button-secondary' onclick='ame_ajax_toggle_showinvisposts(1)' id='ame_toggle_showinvisposts' /></div>\");
});
</script>\n";
	}
}
if ( get_option('ame_toggle_showinvisposts') == '0' && !isset( $_GET['page'] ) ) {
	if( !isset( $_GET['post_status'] ) ) {
		echo '<script type="text/javascript" charset="utf-8">
jQuery(document).ready(function() {
   jQuery(".wrap .widefat tr[class*=\'status-draft\']").hide();
   jQuery(".wrap .widefat tr[class*=\'status-future\']").hide();
});
</script>' . "\n";
	}
}
}

/**
 * Writes javascript stuff into page header needed for the plugin and calls for the SACK library
 *
 * @since 0.7
 * @author scripts@schloebe.de
 */
function ame_js_admin_header() {
	wp_print_scripts( array( 'sack' ));
	$posttype = 'post'; $revisionL10n = __("Post Revisions");
	
	$current_page = basename($_SERVER['PHP_SELF'], ".php");
	if( $current_page == 'edit' && isset($_GET['post_type']) && $_GET['post_type'] == 'page' ) { $posttype = 'post'; } elseif( $current_page == 'edit' ) { $posttype = 'post'; } elseif( $current_page == 'link-manager' ) { $posttype = 'link'; }
	if( $current_page == 'edit' && isset($_GET['post_type']) && $_GET['post_type'] == 'page' ) { $revisionL10n = __("Page Revisions"); } elseif( $current_page == 'edit' ) { $revisionL10n = __("Post Revisions"); } elseif( $current_page == 'edit-pages' ) { $revisionL10n = __("Page Revisions"); }
?>
<?php if( !isset( $_GET['page'] ) ) { ?>
<script type="text/javascript">
//<![CDATA[
ameAjaxL10n = {
	blogUrl: "<?php bloginfo( 'wpurl' ); ?>", pluginUrl: "<?php echo AME_PLUGINFULLURL; ?>", requestUrl: "<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php", imgUrl: "<?php echo AME_PLUGINFULLURL; ?>img/<?php echo AME_IMGSET ?>", Edit: "<?php _e("Edit"); ?>", Post: "<?php _e("Post"); ?>", Save: "<?php _e("Save"); ?>", Cancel: "<?php _e("Cancel"); ?>", postType: "<?php echo $posttype; ?>", pleaseWait: "<?php _e("Please wait..."); ?>", slugEmpty: "<?php _e("Slug may not be empty!"); ?>", Revisions: "<?php echo $revisionL10n; ?>", Time: "<?php _e("Insert time"); ?>"
}
//]]>
</script>
<?php
}
}

/**
 * Writes the css stuff into page header needed for the plugin
 *
 * @since 1.2.0
 * @author scripts@schloebe.de
 */
function ame_css_admin_header() {
	$current_page = basename($_SERVER['PHP_SELF'], ".php");
	echo '<link rel="stylesheet" type="text/css" href="' . AME_PLUGINFULLURL . 'css/styles.css?ver=' . AME_VERSION . '" />' . "\n";
	echo '
<style type="text/css">
#TB_window #TB_title {
	font-weight: 700;
	color: #D7D7D7;
	background-color: #222;
}

table.widefat td.ame_handleDrag {
	background: url(' . AME_PLUGINFULLURL . 'img/' . AME_IMGSET . 'draghandle.gif) center no-repeat;
}
';
if( version_compare($GLOBALS['wp_version'], '3.5', '<') ) {
echo '
.button-group {
	position: relative;
	display: inline-block;
	white-space: nowrap;
	font-size: 0;
	vertical-align: middle;
}';
}
echo '
</style>' . "\n";
if ( $current_page == 'edit' && !isset( $_GET['page'] ) ) {
	echo '<script type="text/javascript">
jQuery(document).ready(function() {
	ame_roll_through_title_rows();
	ame_roll_through_author_rows();
	ame_roll_through_revision_rows();
});
</script>' . "\n";
} elseif ( $current_page == 'edit-pages' || ( $current_page == 'edit' && isset($_GET['post_type']) && $_GET['post_type'] == 'page' ) && !isset( $_GET['page'] ) ) {
	echo '<script type="text/javascript">
jQuery(document).ready(function() {
	ame_roll_through_title_rows();
	ame_roll_through_author_rows();
	ame_roll_through_revision_rows();
	
	jQuery(".widefat tr").hover(function() {
          jQuery(this).find("td.ame_sort").addClass("ame_handleDrag");
    }, function() {
          jQuery(this).find("td.ame_sort").removeClass("ame_handleDrag");
    });
});
</script>' . "\n";
}
}

/**
 * Returns the output for the 'change image set' link
 *
 * @since 1.3.0
 * @author scripts@schloebe.de
 *
 * @return string
 */
function ame_changeImgSet() {
	if( get_option("ame_imgset") == 'set1' ) { $imgset = '2'; } elseif( get_option("ame_imgset") == 'set2' ) { $imgset = '1'; }
	return ' <a href="javascript:void(0);" onclick="ame_ajax_toggle_imageset(' . $imgset . ');return false;"><img src="' . AME_PLUGINFULLURL . 'img/' . AME_IMGSET . 'changeimgset.gif" border="0" alt="' . __('Change image set', 'admin-management-xtended') . '" title="' . __('Change image set', 'admin-management-xtended') . '" /></a>';
}

$current_page = basename($_SERVER['PHP_SELF'], ".php");
if( function_exists('add_action') ) {
	
	if( ($current_page == 'edit' || $current_page == 'edit-pages') && !isset( $_GET['page'] ) ) {
		function ame_enqueue_stuff_edit() {
			wp_enqueue_style( array('thickbox') );
			wp_enqueue_script( array('thickbox') );
			wp_enqueue_script( 'date', AME_PLUGINFULLURL . "js/jquery-addons/date.js", array('jquery'), AME_VERSION );
			wp_enqueue_script( 'datePicker', AME_PLUGINFULLURL . "js/jquery-addons/jquery.datePicker.js", array('jquery'), AME_VERSION );
			wp_enqueue_script( 'ame_gui-modificators', AME_PLUGINFULLURL . "js/gui-modificators.js", array('sack'), AME_VERSION );
			wp_enqueue_script( 'ame_miscscripts', AME_PLUGINFULLURL . "js/functions.js", array('sack'), AME_VERSION );
		}
		
		add_action('admin_head', 'ame_css_admin_header' );
		add_action('admin_print_scripts', 'ame_js_admin_header' );
		add_action('admin_head', 'ame_js_jquery_datepicker_header' );
		add_action('admin_enqueue_scripts', 'ame_enqueue_stuff_edit' );
		if( ame_locale_exists() === true ) {
			function ame_enqueue_script_localdate() {
				$cur_locale = get_locale();
				wp_enqueue_script( 'localdate', AME_PLUGINFULLURL . "js/jquery-addons/date_" . $cur_locale . ".js", array('jquery'), AME_VERSION );
			}
			add_action('admin_enqueue_scripts', 'ame_enqueue_script_localdate' );
		}
		if( ( $current_page == 'edit-pages' || ( $current_page == 'edit' && isset($_GET['post_type']) && $_GET['post_type'] == 'page' ) ) && get_option('ame_show_orderoptions') == '2' && !isset( $_GET['page'] ) ) {
			function ame_enqueue_script_tablednd() {
				wp_enqueue_script( 'tablednd', AME_PLUGINFULLURL . "js/jquery-addons/jquery.tablednd.js", array('jquery'), AME_VERSION );
			}
			add_action('admin_enqueue_scripts', 'ame_enqueue_script_tablednd' );
		}
	}
	/**
 	* @since 1.8.0
 	*/
	if( $current_page == 'link-manager' ) {
		function ame_enqueue_stuff_linkmanager() {
			wp_enqueue_style( array('thickbox') );
			wp_enqueue_script( array('thickbox') );
			wp_enqueue_script( 'ame_gui-modificators', AME_PLUGINFULLURL . "js/gui-modificators.js", array('sack'), AME_VERSION );
			wp_enqueue_script( 'ame_miscscripts', AME_PLUGINFULLURL . "js/functions.js", array('sack'), AME_VERSION );
		}
		
		add_action('admin_print_scripts', 'ame_js_admin_header' );
		add_action('admin_head', 'ame_css_admin_header' );
		add_action('admin_enqueue_scripts', 'ame_enqueue_stuff_linkmanager' );
	}
	if( $current_page == 'upload' ) {
		function ame_enqueue_stuff_upload() {
			wp_enqueue_script( 'ame_gui-modificators', AME_PLUGINFULLURL . "js/gui-modificators.js", array('sack'), AME_VERSION );
			wp_enqueue_script( 'ame_miscscripts', AME_PLUGINFULLURL . "js/functions.js", array('sack'), AME_VERSION );
		}
		
		add_action('admin_print_scripts', 'ame_js_admin_header' );
		add_action('admin_enqueue_scripts', 'ame_enqueue_stuff_upload' );
	}
	if( $current_page == 'edit' && isset($_GET['post_type']) && $_GET['post_type'] != 'page' && !isset( $_GET['page'] ) ) {
		function ame_enqueue_stuff_editpost() {
			wp_enqueue_script( array('suggest') );
		}
		add_action('admin_enqueue_scripts', 'ame_enqueue_stuff_editpost' );
	}
	/**
 	* @since 2.3.0
 	*/
	if( $current_page == 'edit-tags' ) {
		function ame_enqueue_stuff_edittags() {
			wp_enqueue_script( 'ame_gui-modificators', AME_PLUGINFULLURL . "js/gui-modificators.js", array('sack'), AME_VERSION );
		}
		
		add_action('admin_print_scripts', 'ame_js_admin_header' );
		add_action('admin_enqueue_scripts', 'ame_enqueue_stuff_edittags' );
	}
}
?>