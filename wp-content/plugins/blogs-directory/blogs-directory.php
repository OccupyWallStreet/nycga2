<?php
/*
Plugin Name: Blogs Directory
Plugin URI: http://premium.wpmudev.org/project/blogs-directory
Description: This plugin provides a paginated, fully search-able, avatar inclusive, automatic and rather good looking directory of all of the blogs on your WordPress Multisite or BuddyPress installation.
Author: Ivan Shaovchev, Ulrich Sossou, Andrew Billits, Andrey Shipilov (Incsub), S H Mohanjith (Incsub)
Author URI: http://premium.wpmudev.org
Version: 1.1.9.1
Network: true
WDP ID: 101
*/

/*
Copyright 2007-2011 Incsub (http://incsub.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License (Version 2 - GPLv2) as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

//------------------------------------------------------------------------//
//---Config---------------------------------------------------------------//
//------------------------------------------------------------------------//

if (defined('BLOGS_DIRECTORY_SLUG')) {
	$blogs_directory_base = BLOGS_DIRECTORY_SLUG;
} else {
	$blogs_directory_base = 'hub-directory'; //domain.tld/BASE/ Ex: domain.tld/user/
}

load_plugin_textdomain( 'blogs-directory', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

//------------------------------------------------------------------------//
//---Hook-----------------------------------------------------------------//
//------------------------------------------------------------------------//

if ( $current_blog->domain . $current_blog->path == $current_site->domain . $current_site->path ){
	add_filter('rewrite_rules_array','blogs_directory_rewrite');
	add_filter('the_content', 'blogs_directory_output', 20);
	add_filter('the_title', 'blogs_directory_title_output', 99, 2);
	add_action('admin_footer', 'blogs_directory_page_setup');
	add_action('init', 'blogs_directory_flush_rewrite_rules');
}

add_action('network_admin_menu', 'blogs_directory_admin_page');
add_action('admin_init', 'blogs_directory_save_options');

//------------------------------------------------------------------------//
//---Functions------------------------------------------------------------//
//------------------------------------------------------------------------//



//Network admin menu
function blogs_directory_admin_page() {
        add_submenu_page( 'settings.php',  __( 'Site Directory', 'blogs-directory' ), __( 'Site Directory', 'blogs-directory' ), 'manage_network_options', 'blog-directory-settings', 'blogs_directory_site_admin_options' );
        // $page = add_submenu_page( 'blog-directory', __( 'Settings', 'blogs-directory' ), __( 'Settings', 'blogs-directory' ), 'manage_network_options', 'blog-directory-settings', 'blogs_directory_site_admin_options' );
}


//hide some blogs from result
function blogs_directory_hide_some_blogs( $blog_id ) {
    $blogs_directory_hide_blogs = get_site_option( 'blogs_directory_hide_blogs');

    /*Hide Pro Site blogs */
    if ( isset( $blogs_directory_hide_blogs['pro_site'] ) && 1 == $blogs_directory_hide_blogs['pro_site'] ) {
        global $ProSites_Module_PayToBlog, $psts;
        //don't show unpaid blogs
        if ( is_object( $ProSites_Module_PayToBlog ) && $psts->get_setting( 'ptb_front_disable' ) && !is_pro_site( $blog_id, 1 ) )
            return true;
    }

    /*Hide Private blogs */
    if ( isset( $blogs_directory_hide_blogs['private'] ) && 1 == $blogs_directory_hide_blogs['private'] ) {
        //don't show private blogs
        $privacy = get_blog_option( $blog_id, 'blog_public' );
        if ( is_numeric( $privacy ) && 1 != $privacy )
            return true;
    }

    return false;
}

//update rewrite rules
function blogs_directory_flush_rewrite_rules() {
    global $blogs_directory_base;
    $rules = get_option( 'rewrite_rules' );
    if ( !isset( $rules[$blogs_directory_base . '/([^/]+)/([^/]+)/([^/]+)/([^/]+)/?$'] ) ) {
        flush_rewrite_rules( false );
    }
}

function blogs_directory_page_setup() {
	global $wpdb, $user_ID, $blogs_directory_base;
	if ( get_site_option('blogs_directory_page_setup') != 'complete'.$blogs_directory_base && is_super_admin() ) {
		$page_count = $wpdb->get_var("SELECT COUNT(*) FROM " . $wpdb->posts . " WHERE post_name = '" . $blogs_directory_base . "' AND post_type = 'page'");
		if ( $page_count < 1 ) {
			$wpdb->query( "INSERT INTO " . $wpdb->posts . " ( post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count ) VALUES ( '" . $user_ID . "', '" . current_time( 'mysql' ) . "', '" . current_time( 'mysql' ) . "', '', '" . __('Sites') . "', '', 'publish', 'closed', 'closed', '', '" . $blogs_directory_base . "', '', '', '" . current_time( 'mysql' ) . "', '" . current_time( 'mysql' ) . "', '', 0, '', 0, 'page', '', 0 )" );
		}
		update_site_option('blogs_directory_page_setup', 'complete'.$blogs_directory_base);
	}
}

function blogs_directory_site_admin_options() {
	$blogs_directory_sort_by                    = get_site_option('blogs_directory_sort_by', 'alphabetically');
	$blogs_directory_per_page                   = get_site_option('blogs_directory_per_page', '10');
	$blogs_directory_background_color           = get_site_option('blogs_directory_background_color', '#F2F2EA');
	$blogs_directory_alternate_background_color = get_site_option('blogs_directory_alternate_background_color', '#FFFFFF');
    $blogs_directory_border_color               = get_site_option('blogs_directory_border_color', '#CFD0CB');
    $blogs_directory_hide_blogs                 = get_site_option('blogs_directory_hide_blogs');
    $blogs_directory_title_blogs_page           = get_site_option('blogs_directory_title_blogs_page');
	$blogs_directory_show_description           = get_site_option('blogs_directory_show_description');
	?>

    <div class="wrap">

    <?php
    //Display status message
    if ( isset( $_GET['updated'] ) ):
        ?><div id="message" class="updated fade"><p><?php echo urldecode( $_GET['dmsg'] ); ?></p></div><?php
    endif;
    ?>
        <h2><?php _e('Site Directory Settings','blogs-directory') ?></h2>
        <form method="post" name="" >
		    <table class="form-table">
                <tr valign="top">
                    <th width="33%" scope="row"><?php _e('Sort By','blogs-directory') ?></th>
                    <td>
                        <select name="blogs_directory_sort_by" id="blogs_directory_sort_by">
                           <option value="alphabetically" <?php if ( $blogs_directory_sort_by == 'alphabetically' ) { echo 'selected="selected"'; } ?> ><?php _e('Site Name (A-Z)','blogs-directory'); ?></option>
                           <option value="latest" <?php if ( $blogs_directory_sort_by == 'latest' ) { echo 'selected="selected"'; } ?> ><?php _e('Newest','blogs-directory'); ?></option>
                           <option value="last_updated" <?php if ( $blogs_directory_sort_by == 'last_updated' ) { echo 'selected="selected"'; } ?> ><?php _e('Last Updated','blogs-directory'); ?></option>
                        </select>
                    <br /></td>
                </tr>
                <tr valign="top">
                    <th width="33%" scope="row"><?php _e('Listing Per Page','blogs-directory') ?></th>
                    <td>
                    <select name="blogs_directory_per_page" id="blogs_directory_per_page">
                       <option value="5" <?php if ( $blogs_directory_per_page == '5' ) { echo 'selected="selected"'; } ?> ><?php echo '5'; ?></option>
                       <option value="10" <?php if ( $blogs_directory_per_page == '10' ) { echo 'selected="selected"'; } ?> ><?php echo '10'; ?></option>
                       <option value="15" <?php if ( $blogs_directory_per_page == '15' ) { echo 'selected="selected"'; } ?> ><?php echo '15'; ?></option>
                       <option value="20" <?php if ( $blogs_directory_per_page == '20' ) { echo 'selected="selected"'; } ?> ><?php echo '20'; ?></option>
                       <option value="25" <?php if ( $blogs_directory_per_page == '25' ) { echo 'selected="selected"'; } ?> ><?php echo '25'; ?></option>
                       <option value="30" <?php if ( $blogs_directory_per_page == '30' ) { echo 'selected="selected"'; } ?> ><?php echo '30'; ?></option>
                       <option value="35" <?php if ( $blogs_directory_per_page == '35' ) { echo 'selected="selected"'; } ?> ><?php echo '35'; ?></option>
                       <option value="40" <?php if ( $blogs_directory_per_page == '40' ) { echo 'selected="selected"'; } ?> ><?php echo '40'; ?></option>
                       <option value="45" <?php if ( $blogs_directory_per_page == '45' ) { echo 'selected="selected"'; } ?> ><?php echo '45'; ?></option>
                       <option value="50" <?php if ( $blogs_directory_per_page == '50' ) { echo 'selected="selected"'; } ?> ><?php echo '50'; ?></option>
                    </select>
                    <br /></td>
                </tr>
                <tr valign="top">
                    <th width="33%" scope="row"><?php _e('Hide Sites','blogs-directory') ?></th>
                    <td>
                        <input name="blogs_directory_hide_blogs[pro_site]" id="blogs_directory_hide_blogs[pro_site]" type="checkbox" value="1" <?php echo ( isset( $blogs_directory_hide_blogs['pro_site'] ) && '1' == $blogs_directory_hide_blogs['pro_site'] ) ? 'checked' : '' ; ?>  />
                        <label for="blogs_directory_hide_blogs[pro_site]"><?php _e('Pro Site plugin','blogs-directory') ?></label><br />
                        <span class="description"><?php _e('(Hide unpaid blogs.)','blogs-directory') ?></span><br />

                        <input name="blogs_directory_hide_blogs[private]" id="blogs_directory_hide_blogs[private]" type="checkbox" value="1" <?php echo ( isset( $blogs_directory_hide_blogs['private'] ) && '1' == $blogs_directory_hide_blogs['private'] ) ? 'checked' : '' ; ?>  />
                        <label for="blogs_directory_hide_blogs[private]"><?php _e('Private','blogs-directory') ?></label><br />
                        <span class="description"><?php _e('(Hide blogs which block search engines.)','blogs-directory') ?></span><br />
                    </td>
                </tr>
                <tr valign="top">
                    <th width="33%" scope="row"><?php _e('Title of Site page','blogs-directory') ?></th>
                    <td>
                        <input name="blogs_directory_title_blogs_page" type="text" id="blogs_directory_title_blogs_page" value="<?php echo ( isset( $blogs_directory_title_blogs_page ) && '' != $blogs_directory_title_blogs_page ) ? $blogs_directory_title_blogs_page : 'Sites'; ?>" size="20" />
                        <br /><span class="description"><?php _e('Default','blogs-directory') ?>: "Sites"</span>
                    </td>
                </tr>
                <tr valign="top">
                    <th width="33%" scope="row"><?php _e('Display Description','blogs-directory') ?></th>
                    <td>
                        <input name="blogs_directory_show_description" id="blogs_directory_show_description" type="checkbox" value="1" <?php echo ( isset( $blogs_directory_show_description ) && '1' == $blogs_directory_show_description ) ? 'checked' : '' ; ?>  />
                        <label for="blogs_directory_show_description"><?php _e('Show the description for each site on Sites page','blogs-directory') ?></label><br />
                    </td>
                </tr>
                <tr valign="top">
                    <th width="33%" scope="row"><?php _e('Background Color','blogs-directory') ?></th>
                    <td><input name="blogs_directory_background_color" type="text" id="blogs_directory_background_color" value="<?php echo $blogs_directory_background_color; ?>" size="20" />
                    <br /><span class="description"><?php _e('Default','blogs-directory') ?>: #F2F2EA</span></td>
                </tr>
                <tr valign="top">
                    <th width="33%" scope="row"><?php _e('Alternate Background Color','blogs-directory') ?></th>
                    <td><input name="blogs_directory_alternate_background_color" type="text" id="blogs_directory_alternate_background_color" value="<?php echo $blogs_directory_alternate_background_color; ?>" size="20" />
                    <br /><span class="description"><?php _e('Default','blogs-directory') ?>: #FFFFFF</span></td>
                </tr>
                <tr valign="top">
                    <th width="33%" scope="row"><?php _e('Border Color','blogs-directory') ?></th>
                    <td><input name="blogs_directory_border_color" type="text" id="blogs_directory_border_color" value="<?php echo $blogs_directory_border_color; ?>" size="20" />
                    <br /><span class="description"><?php _e('Default','blogs-directory') ?>: #CFD0CB</span></td>
                </tr>
		    </table>
            <p class="submit">
                <input type="submit" class="button-primary" name="save_settings" value="<?php _e('Save Changes','blogs-directory') ?>" />
            </p>
        </form>
    </div>

	<?php
}

function blogs_directory_save_options() {
    if ( isset( $_REQUEST['page'] ) && 'blog-directory-settings' == $_REQUEST['page'] && isset( $_POST['save_settings'] ) ) {

	    update_site_option( 'blogs_directory_sort_by' , $_POST['blogs_directory_sort_by']);
	    update_site_option( 'blogs_directory_per_page' , $_POST['blogs_directory_per_page']);
	    update_site_option( 'blogs_directory_background_color' , trim( $_POST['blogs_directory_background_color'] ));
	    update_site_option( 'blogs_directory_alternate_background_color' , trim( $_POST['blogs_directory_alternate_background_color'] ));
        update_site_option( 'blogs_directory_border_color' , trim( $_POST['blogs_directory_border_color'] ));
        update_site_option( 'blogs_directory_hide_blogs' , $_POST['blogs_directory_hide_blogs'] );

        $blogs_directory_show_description = ( isset( $_POST['blogs_directory_show_description'] ) ) ? 1 : 0;
        update_site_option( 'blogs_directory_show_description' , $blogs_directory_show_description );

        //set blogs page title
        if ( isset( $_POST['blogs_directory_title_blogs_page'] ) && '' != $_POST['blogs_directory_title_blogs_page'] )
            $blogs_directory_title_blogs_page =  trim( $_POST['blogs_directory_title_blogs_page'] );
	    else
            $blogs_directory_title_blogs_page = 'Sites' ;

        update_site_option( 'blogs_directory_title_blogs_page' , $blogs_directory_title_blogs_page );

        global $wpdb, $blogs_directory_base;
        $page_count = $wpdb->get_var("SELECT COUNT(*) FROM " . $wpdb->posts . " WHERE post_name = '" . $blogs_directory_base . "' AND post_type = 'page'");

        if ( 1 == $page_count ) {
            $wpdb->query( "UPDATE " . $wpdb->posts . " SET post_title = '" . $blogs_directory_title_blogs_page . "' WHERE post_name = '" . $blogs_directory_base . "' AND post_type = 'page'" );
        }

        wp_redirect( add_query_arg( array( 'page' => 'blog-directory-settings', 'updated' => 'true', 'dmsg' => urlencode( __( 'Settings saved.', 'email-newsletter' ) ) ), 'admin.php' ) );
        exit;

    }
}

function blogs_directory_rewrite($wp_rewrite){
	global $blogs_directory_base;
    $blogs_directory_rules = array(
        $blogs_directory_base . '/([^/]+)/([^/]+)/([^/]+)/([^/]+)/?$'   => 'index.php?pagename=' . $blogs_directory_base,
        $blogs_directory_base . '/([^/]+)/([^/]+)/([^/]+)/?$'           => 'index.php?pagename=' . $blogs_directory_base,
        $blogs_directory_base . '/([^/]+)/([^/]+)/?$'                   => 'index.php?pagename=' . $blogs_directory_base,
        $blogs_directory_base . '/([^/]+)/?$'                           => 'index.php?pagename=' . $blogs_directory_base
    );
    $wp_rewrite = $blogs_directory_rules + $wp_rewrite;
	return $wp_rewrite;
}

function blogs_directory_url_parse(){
	global $wpdb, $current_site, $blogs_directory_base;
	$blogs_directory_url = $_SERVER['REQUEST_URI'];
	if ( $current_site->path != '/' ) {
		$blogs_directory_url = str_replace('/' . $current_site->path . '/', '', $blogs_directory_url);
		$blogs_directory_url = str_replace($current_site->path . '/', '', $blogs_directory_url);
		$blogs_directory_url = str_replace($current_site->path, '', $blogs_directory_url);
	}
	$blogs_directory_url = ltrim($blogs_directory_url, "/");
	$blogs_directory_url = rtrim($blogs_directory_url, "/");
	$blogs_directory_url = ltrim($blogs_directory_url, $blogs_directory_base);
	$blogs_directory_url = ltrim($blogs_directory_url, "/");

	$blogs_directory_1 = $blogs_directory_2 = $blogs_directory_3 = $blogs_directory_4 = '';
	if( !empty( $blogs_directory_url ) ) {
		$blogs_directory_array = explode("/", $blogs_directory_url);
		for( $i = 1, $j = count( $blogs_directory_array ); $i <= $j ; $i++ ) {
			$blogs_directory_var = "blogs_directory_$i";
			${$blogs_directory_var} = $blogs_directory_array[$i-1];
		}
	}

	$page_type = '';
	$page_subtype = '';
	$page = '';
	$blog = '';
	$phrase = '';
	if ( empty( $blogs_directory_1 ) || is_numeric( $blogs_directory_1 ) ) {
		//landing
		$page_type = 'landing';
		$page = $blogs_directory_1;
		if ( empty( $page ) ) {
			$page = 1;
		}
	} else if ( $blogs_directory_1 == 'search' ) {
		//search
		$page_type = 'search';
		$phrase = isset( $_POST['phrase'] ) ? $_POST['phrase'] : '';
		if ( empty( $phrase ) ) {
			$phrase = $blogs_directory_2;
			$page = $blogs_directory_3;
			if ( empty( $page ) ) {
				$page = 1;
			}
		} else {
			$page = $blogs_directory_3;
			if ( empty( $page ) ) {
				$page = 1;
			}
		}
		$phrase = urldecode( $phrase );
	}

	$blogs_directory['page_type'] = $page_type;
	$blogs_directory['page'] = $page;
	$blogs_directory['phrase'] = $phrase;

	return $blogs_directory;
}

//------------------------------------------------------------------------//
//---Output Functions-----------------------------------------------------//
//------------------------------------------------------------------------//

function blogs_directory_title_output($title, $post_ID = '') {
	global $wpdb, $current_site, $post, $blogs_directory_base;

	if ( in_the_loop() && !empty( $post ) && $post->post_name == $blogs_directory_base && $post_ID == $post->ID) {
		$blogs_directory = blogs_directory_url_parse();
		if ( $blogs_directory['page_type'] == 'landing' ) {
			if ( $blogs_directory['page'] > 1 ) {
				$title = '<a href="http://' . $current_site->domain . $current_site->path . $blogs_directory_base . '/">' . $post->post_title . '</a> &raquo; ' . '<a href="http://' . $current_site->domain . $current_site->path . $blogs_directory_base . '/' . $blogs_directory['page'] . '/">' . $blogs_directory['page'] . '</a>';
			} else {
				$title = '<a href="http://' . $current_site->domain . $current_site->path . $blogs_directory_base . '/">' . $post->post_title . '</a>';
			}
		} else if ( $blogs_directory['page_type'] == 'search' ) {
			if ( $blogs_directory['page'] > 1 ) {
				$title = '<a href="http://' . $current_site->domain . $current_site->path . $blogs_directory_base . '/">' . $post->post_title . '</a> &raquo; <a href="http://' . $current_site->domain . $current_site->path . $blogs_directory_base . '/search/">' . __('Search','blogs-directory') . '</a> &raquo; ' . '<a href="http://' . $current_site->domain . $current_site->path . $blogs_directory_base . '/search/' . urlencode($blogs_directory['phrase']) .  '/' . $blogs_directory['page'] . '/">' . $blogs_directory['page'] . '</a>';
			} else {
				$title = '<a href="http://' . $current_site->domain . $current_site->path . $blogs_directory_base . '/">' . $post->post_title . '</a> &raquo; <a href="http://' . $current_site->domain . $current_site->path . $blogs_directory_base . '/search/">' . __('Search','blogs-directory') . '</a>';
			}
		}
	}
	return $title;
}

function blogs_directory_output($content) {
	global $wpdb, $current_site, $post, $blogs_directory_base;
	$bg_color = '';
	if ( $post->post_name == $blogs_directory_base ) {
		$blogs_directory_sort_by                    = get_site_option('blogs_directory_sort_by', 'alphabetically');
		$blogs_directory_per_page                   = get_site_option('blogs_directory_per_page', '10');
		$blogs_directory_background_color           = get_site_option('blogs_directory_background_color', '#F2F2EA');
		$blogs_directory_alternate_background_color = get_site_option('blogs_directory_alternate_background_color', '#FFFFFF');
		$blogs_directory_border_color               = get_site_option('blogs_directory_border_color', '#CFD0CB');
		$blogs_directory                            = blogs_directory_url_parse();
		$blogs_directory_title_blogs_page           = get_site_option('blogs_directory_title_blogs_page');
		$blogs_directory_show_description           = get_site_option('blogs_directory_show_description');
		$blogs_directory_hide_blogs 		    = get_site_option( 'blogs_directory_hide_blogs');
		
		if ( $blogs_directory['page_type'] == 'landing' ) {
			$search_form_content = blogs_directory_search_form_output('', $blogs_directory['phrase']);
			$navigation_content = blogs_directory_landing_navigation_output('', $blogs_directory_per_page, $blogs_directory['page']);
			//EDITS BEGIN
			//$content .= $search_form_content;
			//$content .= '<br />';
			$content .= '<div style="float:left; width:100%">';
			$content .= '<h1>' . $blogs_directory_title_blogs_page . '</h1>';
			$content .= '<table border="0" border="0" cellpadding="2px" cellspacing="2px" width="100%" bgcolor="" class="blogs_directory_table">';
				//=================================//
				$avatar_default = get_option('avatar_default');
				$tic_toc = 'toc';
				//=================================//
				if ($blogs_directory['page'] == 1){
					$start = 0;
				} else {
					$math = $blogs_directory['page'] - 1;
					$math = $blogs_directory_per_page * $math;
					$start = $math;
				}

				$query = "SELECT * FROM " . $wpdb->base_prefix . "blogs WHERE spam = 0 AND deleted = 0 AND archived = '0' AND blog_id != 1";
				if ( isset( $blogs_directory_hide_blogs['private'] ) && 1 == $blogs_directory_hide_blogs['private'] ) {
					$query .= " AND public = 1";
				}
				if ( $blogs_directory_sort_by == 'alphabetically' ) {
					if ( is_subdomain_install() ) {
						$query .= " ORDER BY domain ASC";
					} else {
						$query .= " ORDER BY path ASC";
					}
				} else if ( $blogs_directory_sort_by == 'latest' ) {
					$query .= " ORDER BY blog_id DESC";
				} else {
					$query .= " ORDER BY last_updated DESC";
				}
				$query .= " LIMIT " . intval( $start ) . ", " . intval( $blogs_directory_per_page );
				$blogs = $wpdb->get_results( $query, ARRAY_A );
				$blogs = apply_filters( 'blogs_directory_blogs_list', $blogs );
				if ( count($blogs) > 0 ) {
					//=================================//
					foreach ($blogs as $blog){

                        //Hide some blogs
                        if ( blogs_directory_hide_some_blogs( $blog['blog_id'] ) )
                            continue;

						//=============================//
						$blog_title         = get_blog_option( $blog['blog_id'], 'blogname', $blog['domain'] . $blog['path'] );

						if ($tic_toc == 'toc'){
							$tic_toc = 'tic';
						} else {
							$tic_toc = 'toc';
						}
						if ($tic_toc == 'tic'){
							$bg_color = $blogs_directory_alternate_background_color;
						} else {
							$bg_color = $blogs_directory_background_color;
						}
						//=============================//
						$content .= '<tr>';
							if ( function_exists('get_blog_avatar') ) {
								$content .= '<td style="background-color:' . $bg_color . '; padding-top:10px;" valign="top" width="10%"><center><a style="text-decoration:none;" href="http://' . $blog['domain'] . $blog['path'] . '">' . get_blog_avatar($blog['blog_id'], 32, $avatar_default) . '</a></center></td>';
							} else {
								$content .= '<td style="background-color:' . $bg_color . '; padding-top:10px;" valign="top" width="10%"></td>';
							}
							$content .= '<td style="background-color:' . $bg_color . ';" width="90%">';
							$content .= '<a style="text-decoration:none; font-size:1.5em; margin-left:20px;" href="http://' . $blog['domain'] . $blog['path'] . '">' . $blog_title . '</a><br />';

                            //show description for blog
                            if ( 1 == $blogs_directory_show_description ) {
                                $blogdescription    = get_blog_option( $blog['blog_id'], 'blogdescription', $blog['domain'] . $blog['path'] );
                                $content .= '<span class="blogs_dir_search_blog_description" style="font-size: 12px; color: #9D88B0" >' . $blogdescription . '</span>';
                            }

							$content .= '</td>';
						$content .= '</tr>';
					}
					//=================================//
				}
			$content .= '</table>';
			$content .= '</div>';
			$content .= $navigation_content;
		} else if ( $blogs_directory['page_type'] == 'search' ) {
			//=====================================//
			if ($blogs_directory['page'] == 1){
				$start = 0;
			} else {
				$math = $blogs_directory['page'] - 1;
				$math = $blogs_directory_per_page * $math;
				$start = $math;
			}


            //get all blogs
            $query      = "SELECT * FROM " . $wpdb->base_prefix . "blogs";
	    if ( isset( $blogs_directory_hide_blogs['private'] ) && 1 == $blogs_directory_hide_blogs['private'] ) {
		$query .= " WHERE public = 1";
	    }
	    if ( $blogs_directory_sort_by == 'alphabetically' ) {
		if ( is_subdomain_install() ) {
			$query .= " ORDER BY domain ASC";
		} else {
			$query .= " ORDER BY path ASC";
		}
	    } else if ( $blogs_directory_sort_by == 'latest' ) {
		$query .= " ORDER BY blog_id DESC";
	    } else {
		$query .= " ORDER BY last_updated DESC";
	    }
            $temp_blogs = $wpdb->get_results( $query, ARRAY_A );
	    
	    $blogs = array();
	    
            //search by
            if ( !empty( $temp_blogs ) ) {
                foreach ( $temp_blogs as $blog ) {

                    //Hide some blogs
                    if ( blogs_directory_hide_some_blogs( $blog['blog_id'] ) )
                        continue;
		
                    if ( $current_site->id != $blog['blog_id'] ) {
			$search_arr = explode( ' ', $blogs_directory['phrase'] );
			
			$query      = "SELECT option_name FROM {$wpdb->base_prefix}{$blog['blog_id']}_options WHERE option_name IN ('blogname', 'blogdescription') AND option_value LIKE '%".join("%' AND option_value LIKE '%", $search_arr)."%'; ";
			$found_words = $wpdb->get_results( $query, ARRAY_A );
			
			if (count($found_words) == 0)
				continue;
			
                        $found_word_name = 0;
			$found_word_description = 0;
			
			foreach ($found_words as $found_word) {
				if ($found_word['option_name'] == 'blogname') {
					$found_word_name++;
				} else if ($found_word['option_name'] == 'blogdescription') {
					$found_word_description++;
				}
			}
			
                        $blogname           = get_blog_option( $blog['blog_id'], 'blogname', $blog['domain'] . $blog['path'] );
                        $blogdescription    = get_blog_option( $blog['blog_id'], 'blogdescription', $blog['domain'] . $blog['path'] );
                        $percent            = $found_word_name + $found_word_description;
			
                        if ( 0 < $percent ) {
                            $blog['blogname']           = $blogname;
                            $blog['blogdescription']    = $blogdescription;
                            $blog['percent']            = $percent;
                            $blogs[]                    = $blog;
                        }
		    }
                }

                //sort blogs by percent
                if ( 1 < count( $blogs ) ) {
                    $fn = create_function( '$a, $b', '
                        if( $a["percent"] == $b["percent"] ) return 0;
                        return ( $a["percent"] > $b["percent"] ) ? -1 : 1;
                    ');
		    
                    usort( $blogs, $fn );
                }
            }

			//=====================================//
			$search_form_content = blogs_directory_search_form_output('', $blogs_directory['phrase']);
			if ( !empty( $blogs ) ) {
				if ( count( $blogs ) < $blogs_directory_per_page ) {
					$next = 'no';
				} else {
					$next = 'yes';
				}
				$navigation_content = blogs_directory_search_navigation_output('', $blogs_directory_per_page, $blogs_directory['page'], $blogs_directory['phrase'], $next);
			}
			//$content .= $search_form_content;
			//$content .= '<br />';
			$content .= '<div style="float:left; width:100%">';
			$content .= '<table border="0" border="0" cellpadding="2px" cellspacing="2px" width="100%" bgcolor="" class="blogs_directory_search_table">';
				$content .= '<tr>';
					$content .= '<th style="background-color:' . $blogs_directory_background_color . '; border-bottom-style:solid; border-bottom-color:' . $blogs_directory_border_color . '; border-bottom-width:1px; font-size:12px;" width="10%"> </td>';
					$content .= '<th style="background-color:' . $blogs_directory_background_color . '; border-bottom-style:solid; border-bottom-color:' . $blogs_directory_border_color . '; border-bottom-width:1px; font-size:12px;" width="90%"><center><strong>' .  $blogs_directory_title_blogs_page . '</strong></center></td>';
				$content .= '</tr>';
				//=================================//
				$avatar_default = get_option('avatar_default');
				$tic_toc = 'toc';
				//=================================//
				if ( !empty( $blogs ) ) {
					foreach ($blogs as $blog){
						//=============================//
						if ($tic_toc == 'toc'){
							$tic_toc = 'tic';
						} else {
							$tic_toc = 'toc';
						}
						if ($tic_toc == 'tic'){
							$bg_color = $blogs_directory_alternate_background_color;
						} else {
							$bg_color = $blogs_directory_background_color;
						}
						//=============================//
						$content .= '<tr>';
							if ( function_exists('get_blog_avatar') ) {
								$content .= '<td style="background-color:' . $bg_color . '; padding-top:10px;" valign="top" width="10%"><center><a style="text-decoration:none;" href="http://' . $blog['domain'] . $blog['path'] . '">' . get_blog_avatar($blog['blog_id'], 32, $avatar_default) . '</a></center></td>';
							} else {
								$content .= '<td style="background-color:' . $bg_color . '; padding-top:10px;" valign="top" width="10%"></td>';
							}
							$content .= '<td style="background-color:' . $bg_color . ';" width="90%">';
                            $content .= '<a style="text-decoration:none; font-size:1.5em; margin-left:20px;" href="http://' . $blog['domain'] . $blog['path'] . '">' . $blog['blogname'] . '</a><br />';
							$content .= '<span class="blogs_dir_search_blog_description" style="font-size: 12px; color: #9D88B0" >' . $blog['blogdescription'] . '</span>';
							$content .= '</td>';
						$content .= '</tr>';
					}
				} else {
					$content .= '<tr>';
						$content .= '<td style="background-color:' . $bg_color . '; padding-top:10px;" valign="top" width="10%"></td>';
						$content .= '<td style="background-color:' . $bg_color . ';" width="90%">' . __('No results...','blogs-directory') . '</td>';
					$content .= '</tr>';
				}
				//=================================//
			$content .= '</table>';
			$content .= '</div>';
			if ( !empty( $blogs ) ) {
				$content .= $navigation_content;
			}
		} else {
			$content = __('Invalid page.','blogs-directory');
		}
	}
	return $content;
}

function blogs_directory_search_form_output($content, $phrase) {
	global $wpdb, $current_site, $blogs_directory_base;
	if ( !empty( $phrase ) ) {
		$content .= '<form action="' . $current_site->path . $blogs_directory_base . '/search/' . urlencode( $phrase ) . '/" method="post">';
	} else {
		$content .= '<form action="' . $current_site->path . $blogs_directory_base . '/search/" method="post">';
	}
		$content .= '<table border="0" border="0" cellpadding="2px" cellspacing="2px" width="100%" bgcolor=""  class="blogs_directory_search_table">';
		$content .= '<tr>';
		    $content .= '<td style="font-size:12px; text-align:left;" width="80%">';
				$content .= '<input name="phrase" style="width: 100%;" type="text" value="' . $phrase . '">';
			$content .= '</td>';
			$content .= '<td style="font-size:12px; text-align:right;" width="20%">';
				$content .= '<input name="Submit" value="' . __('Search','blogs-directory') . '" type="submit">';
			$content .= '</td>';
		$content .= '</tr>';
		$content .= '</table>';
	$content .= '</form>';
	return $content;
}

function blogs_directory_search_navigation_output($content, $per_page, $page, $phrase, $next){
	global $wpdb, $current_site, $blogs_directory_base;
	if ( is_subdomain_install() ) {
		$blog_count = $wpdb->get_var("SELECT COUNT(*) FROM " . $wpdb->base_prefix . "blogs WHERE ( domain LIKE '%" . $phrase . "%' ) AND spam != 1 AND deleted != 1 AND blog_id != 1");
	} else {
		$blog_count = $wpdb->get_var("SELECT COUNT(*) FROM " . $wpdb->base_prefix . "blogs WHERE ( path LIKE '%" . $phrase . "%' ) AND spam != 1 AND deleted != 1 AND blog_id != 1");
	}
	$blog_count = apply_filters( 'blogs_directory_blogs_count', $blog_count - 1 );

	//generate page div
	//============================================================================//
	$total_pages = blogs_directory_roundup($blog_count / $per_page, 0);
	$content .= '<table border="0" border="0" cellpadding="2px" cellspacing="2px" width="100%" bgcolor="" class="blogs_directory_nav_table">';
	$content .= '<tr>';
	$showing_low = ($page * $per_page) - ($per_page - 1);
	if ($total_pages == $page){
		$showing_high = $blog_count;
	} else {
		$showing_high = $page * $per_page;
	}

    $content .= '<td style="font-size:12px; text-align:left;" width="50%">';
	if ($blog_count > $per_page){
	//============================================================================//
		if ($page == '' || $page == '1'){
			//$content .= __('Previous','blogs-directory');
		} else {
		$previous_page = $page - 1;
		$content .= '<a style="text-decoration:none;" href="http://' . $current_site->domain . $current_site->path . $blogs_directory_base . '/search/' . urlencode( $phrase ) . '/' . $previous_page . '/">&laquo; ' . __('Previous','blogs-directory') . '</a>';
		}
	//============================================================================//
	}
	$content .= '</td>';
    $content .= '<td style="font-size:12px; text-align:right;" width="50%">';
	if ($blog_count > $per_page){
	//============================================================================//
		if ( $next != 'no' ) {
			if ($page == $total_pages){
				//$content .= __('Next','blogs-directory');
			} else {
				if ($total_pages == 1){
					//$content .= __('Next','blogs-directory');
				} else {
					$next_page = $page + 1;
				$content .= '<a style="text-decoration:none;" href="http://' . $current_site->domain . $current_site->path . $blogs_directory_base . '/search/' . urlencode( $phrase ) . '/' . $next_page . '/">' . __('Next','blogs-directory') . ' &raquo;</a>';
				}
			}
		}
	//============================================================================//
	}
    $content .= '</td>';
	$content .= '</tr>';
    $content .= '</table>';
	return $content;
}

function blogs_directory_landing_navigation_output($content, $per_page, $page){
	global $wpdb, $current_site, $blogs_directory_base;
	$blog_count = $wpdb->get_var("SELECT COUNT(*) FROM " . $wpdb->base_prefix . "blogs WHERE spam != 1 AND deleted != 1 AND blog_id != 1");
	$blog_count = apply_filters( 'blogs_directory_blogs_count', $blog_count );

	//generate page div
	//============================================================================//
	$total_pages = blogs_directory_roundup($blog_count / $per_page, 0);
	$content .= '<table border="0" border="0" cellpadding="2px" cellspacing="2px" width="100%" bgcolor="" class="blogs_directory_nav_table">';
	$content .= '<tr>';
	$showing_low = ($page * $per_page) - ($per_page - 1);
	if ($total_pages == $page){
		//last page...
		//$showing_high = $blog_count - (($total_pages - 1) * $per_page);
		$showing_high = $blog_count;
	} else {
		$showing_high = $page * $per_page;
	}

    $content .= '<td style="font-size:12px; text-align:left;" width="50%">';
	if ($blog_count > $per_page){
	//============================================================================//
		if ($page == '' || $page == '1'){
			//$content .= __('Previous','blogs-directory');
		} else {
		$previous_page = $page - 1;
		$content .= '<a style="text-decoration:none;" href="http://' . $current_site->domain . $current_site->path . $blogs_directory_base . '/' . $previous_page . '/">&laquo; ' . __('Previous','blogs-directory') . '</a>';
		}
	//============================================================================//
	}
	$content .= '</td>';
    $content .= '<td style="font-size:12px; text-align:right;" width="50%">';
	if ($blog_count > $per_page){
	//============================================================================//
		if ($page == $total_pages){
			//$content .= __('Next','blogs-directory');
		} else {
			if ($total_pages == 1){
				//$content .= __('Next','blogs-directory');
			} else {
				$next_page = $page + 1;
			$content .= '<a style="text-decoration:none;" href="http://' . $current_site->domain . $current_site->path . $blogs_directory_base . '/' . $next_page . '/">' . __('Next','blogs-directory') . ' &raquo;</a>';
			}
		}
	//============================================================================//
	}
    $content .= '</td>';
	$content .= '</tr>';
    $content .= '</table>';
	return $content;
}

//------------------------------------------------------------------------//
//---Page Output Functions------------------------------------------------//
//------------------------------------------------------------------------//

//------------------------------------------------------------------------//
//---Support Functions----------------------------------------------------//
//------------------------------------------------------------------------//

function blogs_directory_roundup($value, $dp){
    return ceil($value*pow(10, $dp))/pow(10, $dp);
}

/* Update Notifications Notice */
if ( !function_exists( 'wdp_un_check' ) ):
function wdp_un_check() {
    if ( !class_exists('WPMUDEV_Update_Notifications') && current_user_can('edit_users') )
        echo '<div class="error fade"><p>' . __('Please install the latest version of <a href="http://premium.wpmudev.org/project/update-notifications/" title="Download Now &raquo;">our free Update Notifications plugin</a> which helps you stay up-to-date with the most stable, secure versions of WPMU DEV themes and plugins. <a href="http://premium.wpmudev.org/wpmu-dev/update-notifications-plugin-information/">More information &raquo;</a>', 'wpmudev') . '</a></p></div>';
}
add_action( 'admin_notices', 'wdp_un_check', 5 );
add_action( 'network_admin_notices', 'wdp_un_check', 5 );
endif;

?>
