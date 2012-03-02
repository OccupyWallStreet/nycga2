<?php
/*
Plugin Name: Revision History
Plugin URI: http://keyes.ie/wordpress/revision-history
Description: Allow any visitor to your site to view the revisions of posts and/or pages.
Version: 0.9.1
Author: John Keyes
Author URI: http://keyes.ie
*/

/*  Copyright 2009  John Keyes
 
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

/*
    Thanks to D'Arcy Norman's http://www.darcynorman.net/wordpress/post-revision-display/
    for a push in the right direction.
*/

add_action('wp', 'check_for_revision');

function check_for_revision($wp) {
	$adjust_title = get_option( 'rh_adjust_title' );
    
	if ( $adjust_title ) {
        $title_class = get_option( 'rh_title_class' );
        $rev_date = rh_revision_str();

        if (valid_revision_id()) {
            $revision = $_GET['revision'];
            $post = get_post($revision);
            if ($post != null) {
                # reset posts so the revision is displayed instead of the original
                global $posts;
                $posts = array($post);
            }
        }
        else {
        	$post = get_post(get_the_ID());
        }

	    if ($title_class) {
	        $post->post_title .= "<span class=\"$title_class\">";
	    }
        $post->post_title .= " (Revision: " . $rev_date . ")";
	    if ($title_class) {
	        $post->post_title .= "</span>";
	    }
    }
}

function rh_get_the_revision_post() {
    if (valid_revision_id()) {
        $revision = $_GET['revision'];
        $post = get_post($revision);
    } else {
    	$post = get_post(get_the_ID());
    }
    return $post;    
}

function rh_revision_str($post = null) {
    if ($post == null) {
        if (valid_revision_id()) {
            $post = rh_get_the_revision_post($revision);
        }
    }
    if ($post != null) {
        $modified = strtotime($post->post_modified_gmt . ' +0000');
        return sprintf('%s at %s', date(get_option('date_format'), $modified), date(get_option('time_format'), $modified));    
    } else {
        return "Latest";
	}
}

function rh_the_revision($before = '', $after = '') {
    echo $before . rh_revision_str() . $after;
}

add_filter('the_content', 'display_post_revisions');

function display_post_revisions($content) {
	$post = get_post(get_the_ID());

	// if we are visiting a revision we need to adjust the post 
	// so we can still get the revision history.
	if ( $post && $post->post_type == "revision" ) {
		$post = get_post($post->post_parent);
	}
    $page_name = 'rh_show_page_revisions';
    $post_name = 'rh_show_post_revisions';
	$page_val = get_option( $page_name );
    $post_val = get_option( $post_name );
    
	if ( $post && (( $post->post_type == "post" && $post_val) ||
	     ($post->post_type == "page" && $page_val)) ) {
 		$revisions = list_post_revisions($post);
 		if ( $revisions ) {
    		$content .= '<div class="revision-history">';
    		$content .= '	<h3>Revision History:</h3>';
		    $content .= $revisions;
    		$content .= '</div>';
    	}
	}
	return $content;
}


function list_post_revisions( $post ) {
	if ( $revisions = wp_get_post_revisions( $post->ID ) ) {
        $autosave_name = 'rh_show_autosaves';
        $show_autosaves = get_option( $autosave_name );
	    $revision_id = (valid_revision_id()) ? $revision_id = $_GET['revision'] : $post->ID;
        $current_revision_date = rh_revision_str($post);
        $current_revision_author = get_author_name($post->post_author);
        $current_revision_url = get_permalink($post);
	    if ($revision_id == $post->ID) {
		    $items .= "<li>$current_revision_date by $current_revision_author (<em>displayed above</em>)</li>";
	    } else {
		    $items .= "<li><a href=\"$current_revision_url\">$current_revision_date</a> by $current_revision_author</li>";
	    }
    	foreach ( $revisions as $revision ) {
    	    $is_autosave = wp_is_post_autosave($revision);
    	    if ( !$show_autosaves && $is_autosave ) {
    	        continue;
    	    }
    	    $rev_date = rh_revision_str($revision);
    		$name = get_author_name( $revision->post_author );
    		$query_string = get_query_string($revision);
    		$items .= "<li>";
    		if ($revision_id == $revision->ID) {
    		    $items .= "$rev_date by $name (<em>displayed above</em>)";
    		} else {
    		    $items .= "<a href=\"$query_string\">$rev_date</a> by $name";
    		}
    		if ($is_autosave) {
    		    $items .= " [autosave]";
    		}
    		$items .= "</li>";
    	}
    	return "<ul class='revision-list'>$items</ul>";
    }
}

function valid_revision_id() {
    return isset($_GET['revision']) && is_numeric($_GET['revision']);
}

function get_query_string($revision) {
    $query_string = "?revision=$revision->ID";
    foreach ($_GET as $key => $value) {
        if ($key != "revision") {
            $query_string.="&$key=$value";
        }
    }
    return $query_string;
}

add_option('rh_show_page_revisions', '0');
add_option('rh_show_post_revisions', '0');
add_option('rh_show_autosaves', '0');
add_option('rh_adjust_title', '0');
add_option('rh_title_class', 'rh-title');

// Hook for adding admin menus
add_action('admin_menu', 'add_revision_history');

// action function for above hook
function add_revision_history() {
    // Add a new submenu under Settings:
    add_options_page('Revision History', 'Revision History', 'administrator', 'show_revhis_settings', 'add_revision_history_options_page');
}

function add_revision_history_options_page() {
    // variables for the field and option names
    $page_name = 'rh_show_page_revisions';
    $post_name = 'rh_show_post_revisions';
    $autosaves_name = 'rh_show_autosaves';
    $adjust_title_name = 'rh_adjust_title';
    $title_class_name = 'rh_title_class';
    $hidden_field_name = 'submit_hidden';

    // get the current values
    $page_val = get_option( $page_name );
    $post_val = get_option( $post_name );
    $autosaves_val = get_option( $autosaves_name );
    $adjust_title_val = get_option( $adjust_title_name );
    $title_class_val = get_option( $title_class_name );

    // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if( $_POST[ $hidden_field_name ] == 'Y' ) {
        // get the values from the POST
        $new_page_val = ($_POST[ $page_name ] == "on") ? "1" : "0";
        $new_post_val = ($_POST[ $post_name ] == "on") ? "1" : "0";
        $new_autosaves_val = ($_POST[ $autosaves_name ] == "on") ? "1" : "0";
        $new_adjust_title_val = ($_POST[ $adjust_title_name ] == "on") ? "1" : "0";
        $new_title_class_val = ($_POST[ $title_class_name ] == "") ? "" : $_POST[ $title_class_name ];

        // save the new values
        if ( $new_page_val != $page_val ) {
            update_option( $page_name, $new_page_val );
            $page_val = $new_page_val;
        }
        if ( $new_post_val != $post_val ) {
            update_option( $post_name, $new_post_val );
            $post_val = $new_post_val;
        }
        if ( $new_autosaves_val != $autosaves_val ) {
            update_option( $autosaves_name, $new_autosaves_val );
            $autosaves_val = $new_autosaves_val;
        }
        if ( $new_adjust_title_val != $adjust_title_val ) {
            update_option( $adjust_title_name, $new_adjust_title_val );
            $adjust_title_val = $new_adjust_title_val;
        }
        if ( $new_title_class_val != $title_class_val ) {
            update_option( $title_class_name, $new_title_class_val );
            $title_class_val = $new_title_class_val;
        }
        // Feedback that we've updated the options
?>
<div class="updated"><p><strong><?php _e('Options saved.', 'mt_trans_domain' ); ?></strong></p></div>
<?php
    } // END CHECKING POST
?>
<div class="wrap">
    <?php echo "<h2>" . __( 'Revision History Settings', 'mt_trans_domain' ) . "</h2>"; ?>

    <form name="show_revision_history" method="post" action="">
        <input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th scope="row">
                        <span>Page revisions</span>
                    </th>
                    <td>
                        <input id="rh_show_on_pages" type="checkbox" name="<?php echo $page_name; ?>" 
                        <?php checked('1', $page_val); ?> />
                        <label for="rh_show_on_pages"><?php _e("Show revision history on pages.", 'mt_trans_domain' ); ?></label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <span>Post revisions</span>
                    </th>
                    <td>
                        <input id="rh_show_on_posts" type="checkbox" name="<?php echo $post_name; ?>" 
                            <?php checked('1', $post_val); ?> />
                        <label for="rh_show_on_posts"><?php _e("Show revision history on posts.", 'mt_trans_domain' ); ?></label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <span>Autosave revisions</span>
                    </th>
                    <td>
                        <input id="rh_show_autosaves" type="checkbox" name="<?php echo $autosaves_name; ?>" 
                            <?php checked('1', $autosaves_val); ?> />
                        <label for="rh_show_autosaves"><?php _e("Show autosaves.", 'mt_trans_domain' ); ?></label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <span>Modify post title</span>
                    </th>
                    <td>
                        <input id="rh_adjust_title" type="checkbox" name="<?php echo $adjust_title_name; ?>" 
                            <?php checked('1', $adjust_title_val); ?> />
                        <label for="rh_adjust_title"><?php _e("Show revision date in post title of revisions.", 'mt_trans_domain' ); ?></label>
                        <span class="description">&nbsp;If you need more control use <code>rh_the_revision</code> in your theme.</span>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="rh_title_class"><?php _e("Title Class", 'mt_trans_domain' ); ?></label>
                    </th>
                    <td>
                        <input id="rh_title_class" type="text" name="<?php echo $title_class_name; ?>" value="<?php echo $title_class_val; ?>" class="regular-text code"/>
                        <span class="description">A child span is added to the post header, with the specified class.</span>
                    </td>
                </tr>
            </tbody>
        </table>
        <p class="submit">
            <input type="submit" name="Submit" value="<?php _e('Update Options', 'mt_trans_domain' ) ?>" />
        </p>
    </form>
</div>
<?php
} // END add_revision_history_options_page


?>
