<?php
/*
    Network Latest Posts Shortcode Form
    Version 3.5
    Author L'Elite
    Author URI http://laelite.info/
 */
/*  Copyright 2012  L'Elite (email : opensource@laelite.info)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
// Retrieve the WordPress root path
function nlp_config_path()
{
    $base = dirname(__FILE__);
    $path = false;
    // Check multiple levels, until find the config file
    if (@file_exists(dirname(dirname($base))."/wp-config.php")){
        $path = dirname(dirname($base));
    } elseif (@file_exists(dirname(dirname(dirname($base)))."/wp-config.php")) {
        $path = dirname(dirname(dirname($base)));
    } elseif (@file_exists(dirname(dirname(dirname(dirname($base))))."/wp-config.php")) {
        $path = dirname(dirname(dirname(dirname($base))));
    } elseif (@file_exists(dirname(dirname(dirname(dirname(dirname($base)))))."/wp-config.php")) {
        $path = dirname(dirname(dirname(dirname(dirname($base)))));
    } else {
        $path = false;
    }
    // Get the path
    if ($path != false){
        $path = str_replace("\\", "/", $path);
    }
    // Return the path
    return $path;
}
$wp_root_path = nlp_config_path();
// Load WordPress functions & NLposts_Widget class
require_once("$wp_root_path/wp-load.php");
require_once("../network-latest-posts-widget.php");
//$thumbnail_w = '80';
//$thumbnail_h = '80';
// Widget object
$widget_obj = new NLposts_Widget();
// Default values
$defaults = array(
    'title'            => NULL,          // Widget title
    'number_posts'     => 10,            // Number of posts to be displayed
    'time_frame'       => 0,             // Time frame to look for posts in days
    'title_only'       => TRUE,          // Display the post title only
    'display_type'     => 'ulist',       // Display content as a: olist (ordered), ulist (unordered), block
    'blog_id'          => NULL,          // ID(s) of the blog(s) you want to display the latest posts
    'ignore_blog'      => NULL,          // ID(s) of the blog(s) you want to ignore
    'thumbnail'        => FALSE,         // Display the thumbnail
    'thumbnail_wh'     => '80x80',       // Thumbnail Width & Height in pixels
    'thumbnail_class'  => NULL,          // Thumbnail CSS class
    'thumbnail_filler' => 'placeholder', // Replacement image for posts without thumbnail (placeholder, kittens, puppies)
    'thumbnail_custom' => FALSE,         // Pull thumbnails from custom fields
    'thumbnail_field'  => NULL,          // Custom field containing image url
    'thumbnail_url'    => NULL,          // Custom thumbnail URL
    'custom_post_type' => 'post',        // Type of posts to display
    'category'         => NULL,          // Category(ies) to display
    'tag'              => NULL,          // Tag(s) to display
    'paginate'         => FALSE,         // Paginate results
    'posts_per_page'   => NULL,          // Number of posts per page (paginate needs to be active)
    'display_content'  => FALSE,         // Display post content instead of excerpts (false by default)
    'excerpt_length'   => NULL,          // Excerpt's length
    'auto_excerpt'     => FALSE,         // Generate excerpt from content
    'excerpt_trail'    => 'text',        // Excerpt's trailing element: text, image
    'full_meta'        => FALSE,         // Display full metadata
    'sort_by_date'     => FALSE,         // Display the latest posts first regardless of the blog they come from
    'sort_by_blog'     => FALSE,         // Sort by blog ID
    'sorting_order'    => NULL,          // Sort posts from Newest to Oldest or vice versa (newer / older), asc/desc for blog ID
    'sorting_limit'    => NULL,          // Limit the number of sorted posts to display
    'post_status'      => 'publish',     // Post status (publish, new, pending, draft, auto-draft, future, private, inherit, trash)
    'css_style'        => NULL,          // Custom CSS _filename_ (ex: custom_style)
    'wrapper_list_css' => 'nav nav-tabs nav-stacked', // Custom CSS classes for the list wrapper
    'wrapper_block_css'=> 'content',     // Custom CSS classes for the block wrapper
    'instance'         => NULL,          // Instance identifier, used to uniquely differenciate each shortcode used
    'random'           => FALSE,         // Pull random posts
    'post_ignore'      => NULL
);
// Set an array
$settings = array();
// Parse & merge the settings with the default values
$settings = wp_parse_args( $settings, $defaults );
// Extract elements as variables
extract( $settings );
$thumbnail_size = str_replace('x',',',$thumbnail_wh);
$thumbnail_size = explode(',',$thumbnail_size);
$thumbnail_w = $thumbnail_size[0];
$thumbnail_h = $thumbnail_size[1];
// Get blog ids
global $wpdb;
$blog_ids = $wpdb->get_results("SELECT blog_id FROM $wpdb->blogs WHERE
    public = '1' AND archived = '0' AND mature = '0' AND spam = '0' AND deleted = '0'
        ORDER BY last_updated DESC");
// Basic HTML Tags
$br = "<br />";
$p_o = "<p>";
$p_c = "<p>";
$widget_form = "<form id='nlposts_shortcode' name='nlposts_shortcode' method='POST' action=''>";
$widget_form.= $p_o;
// title
$widget_form.= "<label for='title'>" . __('Title','trans-nlp') . "</label>";
$widget_form.= $br;
$widget_form.= "<input type='text' id='title' name='title' value='$title' />";
$widget_form.= $br;
// Instance
$widget_form.= "<label for='instance'>" . __('Instance Identifier','trans-nlp') . "</label>";
$widget_form.= $br;
$widget_form.= "<input type='text' id='instance' name='instance' value='$instance' />";
$widget_form.= $br;
// number_posts
$widget_form.= "<label for='number_posts'>" . __('Number of Posts by Blog','trans-nlp') . "</label>";
$widget_form.= $br;
$widget_form.= "<input type='text' size='3' id='number_posts' name='number_posts' value='$number_posts' />";
$widget_form.= $br;
// post_ignore
$widget_form.= "<label for='post_ignore'>" . __('Post ID(s) to Ignore','trans-nlp') . "</label>";
$widget_form.= $br;
$widget_form.= "<input type='text' id='post_ignore' name='post_ignore' value='$post_ignore' />";
$widget_form.= $br;
// time_frame
$widget_form.= "<label for='time_frame'>" . __('Time Frame in Days','trans-nlp') . "</label>";
$widget_form.= $br;
$widget_form.= "<input type='text' size='3' id='time_frame' name='time_frame' value='$time_frame' />";
$widget_form.= $br;
// title_only
$widget_form.= "<label for='title_only'>" . __('Titles Only','trans-nlp') . "</label>";
$widget_form.= $br;
$widget_form.= "<select id='title_only' name='title_only'>";
if( $title_only == 'true' ) {
    $widget_form.= "<option value='true' selected='selected'>" . __('Yes','trans-nlp') . "</option>";
    $widget_form.= "<option value='false'>" . __('No','trans-nlp') . "</option>";
} else {
    $widget_form.= "<option value='true'>" . __('Yes','trans-nlp') . "</option>";
    $widget_form.= "<option value='false' selected='selected'>" . __('No','trans-nlp') . "</option>";
}
$widget_form.= "</select>";
$widget_form.= $br;
// display_type
$widget_form.= "<label for='display_type'>" . __('Display Type','trans-nlp') . '</label>';
$widget_form.= $br;
$widget_form.= "<select id='display_type' name='display_type'>";
switch( $display_type ) {
    // Unordered list
    case 'ulist':
        $widget_form.= "<option value='ulist' selected='selected'>" . __('Unordered List','trans-nlp') . "</option>";
        $widget_form.= "<option value='olist'>" . __('Ordered List','trans-nlp') . "</option>";
        $widget_form.= "<option value='block'>" . __('Blocks','trans-nlp') . "</option>";
        break;
    // Ordered list
    case 'olist':
        $widget_form.= "<option value='ulist'>" . __('Unordered List','trans-nlp') . "</option>";
        $widget_form.= "<option value='olist' selected='selected'>" . __('Ordered List','trans-nlp') . "</option>";
        $widget_form.= "<option value='block'>" . __('Blocks','trans-nlp') . "</option>";
        break;
    // Block
    case 'block':
        $widget_form.= "<option value='ulist'>" . __('Unordered List','trans-nlp') . "</option>";
        $widget_form.= "<option value='olist'>" . __('Ordered List','trans-nlp') . "</option>";
        $widget_form.= "<option value='block' selected='selected'>" . __('Blocks','trans-nlp') . "</option>";
        break;
    // Unordered list by default
    default:
        $widget_form.= "<option value='ulist' selected='selected'>" . __('Unordered List','trans-nlp') . "</option>";
        $widget_form.= "<option value='olist'>" . __('Ordered List','trans-nlp') . "</option>";
        $widget_form.= "<option value='block'>" . __('Blocks','trans-nlp') . "</option>";
        break;
}
$widget_form.= "</select>";
// blog_id
$widget_form.= $br;
if( is_rtl() ) {
    $widget_form.= "<label for='blog_id'>" . __('Display Blog','trans-nlp') . " " . __('or','trans-nlp') . " " . __('Blogs','trans-nlp') . "</label>";
} else {
    $widget_form.= "<label for='blog_id'>" . __('Display Blog(s)','trans-nlp') . "</label>";
}
$widget_form.= $br;
$widget_form.= "<select id='blog_id' name='blog_id' multiple='multiple'>";
// Get the blog_id string
if( !is_array($blog_id) ) {
    // Check for multiple values
    if( preg_match('/,/',$blog_id) ) {
        // Set an array
        $blog_id = explode(',',$blog_id);
    } else {
        // Single value
        if( empty($blog_id) ) {
            // Set an empty array
            $blog_id = array('null');
        } else {
            // Set an array
            $blog_id = array($blog_id);
        }
    }
}
if( empty($blog_id) || $blog_id == 'null' || in_array('null',$blog_id) ) {
    $widget_form.= "<option value='null' selected='selected'>" . __('Display All','trans-nlp') . "</option>";
} else {
    $widget_form.= "<option value='null'>" . __('Display All','trans-nlp') . "</option>";
}
// Display the list of blogs
foreach ($blog_ids as $single_id) {
    $blog_details = get_blog_details($single_id->blog_id);
    if( !empty($blog_id) && in_array($single_id->blog_id,$blog_id) ) {
        $widget_form.= "<option value='$single_id->blog_id' selected='selected'>". $blog_details->blogname ." (ID $single_id->blog_id)</option>";
    } else {
        $widget_form.= "<option value='$single_id->blog_id'>". $blog_details->blogname ." (ID $single_id->blog_id)</option>";
    }
}
$widget_form.= "</select>";
// ignore_blog
$widget_form.= $br;
if( is_rtl() ) {
    $widget_form.= "<label for='ignore_blog'>" . __('Ignore Blog','trans-nlp') . " " . __('or','trans-nlp') . " " . __('Blogs','trans-nlp') . "</label>";
} else {
    $widget_form.= "<label for='ignore_blog'>" . __('Ignore Blog(s)','trans-nlp') . "</label>";
}
$widget_form.= $br;
$widget_form.= "<select id='ignore_blog' name='ignore_blog' multiple='multiple'>";
// Get the ignore_blog string
if( !is_array($ignore_blog) ) {
    // Check for multiple values
    if( preg_match('/,/',$ignore_blog) ) {
        // Set an array
        $ignore_blog = explode(',',$ignore_blog);
    } else {
        // Single value
        if( empty($ignore_blog) ) {
            // Set an empty array
            $ignore_blog = array('null');
        } else {
            // Set an array
            $ignore_blog = array($ignore_blog);
        }
    }
}
if( empty($ignore_blog) || $ignore_blog == 'null' || in_array('null',$ignore_blog) ) {
    $widget_form.= "<option value='null' selected='selected'>" . __('Nothing to Ignore','trans-nlp') . "</option>";
} else {
    $widget_form.= "<option value='null'>" . __('Nothing to Ignore','trans-nlp') . "</option>";
}
// Display the list of blogs
foreach ($blog_ids as $ignore_id) {
    $blog_details = get_blog_details($ignore_id->blog_id);
    if( !empty($ignore_blog) && in_array($ignore_id->blog_id,$ignore_blog) ) {
        $widget_form.= "<option value='$ignore_id->blog_id' selected='selected'>". $blog_details->blogname ." (ID $ignore_id->blog_id)</option>";
    } else {
        $widget_form.= "<option value='$ignore_id->blog_id'>". $blog_details->blogname ." (ID $ignore_id->blog_id)</option>";
    }
}
$widget_form.= "</select>";
// thumbnail
$widget_form.= $br;
$widget_form.= "<label for='thumbnail'>" . __('Display Thumbnails','trans-nlp') . "</label>";
$widget_form.= $br;
$widget_form.= "<select id='thumbnail' name='thumbnail'>";
if( $thumbnail == 'true' ) {
    $widget_form.= "<option value='true' selected='selected'>" . __('Show','trans-nlp') . "</option>";
    $widget_form.= "<option value='false'>" . __('Hide','trans-nlp') . "</option>";
} else {
    $widget_form.= "<option value='true'>" . __('Show','trans-nlp') . "</option>";
    $widget_form.= "<option value='false' selected='selected'>" . __('Hide','trans-nlp') . "</option>";
}
$widget_form.= "</select>";
$widget_form.= $br;
$widget_form.= "<fieldset>";
$widget_form.= "<legend>" . __('Thumbnail Size','trans-nlp') . "</legend>";
$widget_form.= "<label for='thumbnail_w'>" . __('Width','trans-nlp') . "</label>";
$widget_form.= $br;
$widget_form.= "<input type='text' size='3' id='thumbnail_w' name='thumbnail_w' value='$thumbnail_w' />";
$widget_form.= $br;
$widget_form.= "<label for='thumbnail_h'>" . __('Height','trans-nlp') . "</label>";
$widget_form.= $br;
$widget_form.= "<input type='text' size='3' id='thumbnail_h' name='thumbnail_h' value='$thumbnail_h' />";
$widget_form.= "</fieldset>";
// thumbnail_filler
$widget_form.= "<label for='thumbnail_filler'>" . __('Thumbnail Replacement','trans-nlp') . '</label>';
$widget_form.= $br;
$widget_form.= "<select id='thumbnail_filler' name='thumbnail_filler'>";
switch( $thumbnail_filler ) {
    case 'placeholder':
        $widget_form.= "<option value='placeholder' selected='selected'>" . __('Placeholder','trans-nlp') . "</option>";
        $widget_form.= "<option value='kittens'>" . __('Kittens','trans-nlp') . "</option>";
        $widget_form.= "<option value='puppies'>" . __('Puppies','trans-nlp') . "</option>";
        $widget_form.= "<option value='custom'>" . __('Custom','trans-nlp') . "</option>";
        break;
    case 'kittens':
        $widget_form.= "<option value='placeholder'>" . __('Placeholder','trans-nlp') . "</option>";
        $widget_form.= "<option value='kittens' selected='selected'>" . __('Kittens','trans-nlp') . "</option>";
        $widget_form.= "<option value='puppies'>" . __('Puppies','trans-nlp') . "</option>";
        $widget_form.= "<option value='custom'>" . __('Custom','trans-nlp') . "</option>";
        break;
    case 'puppies':
        $widget_form.= "<option value='placeholder'>" . __('Placeholder','trans-nlp') . "</option>";
        $widget_form.= "<option value='kittens'>" . __('Kittens','trans-nlp') . "</option>";
        $widget_form.= "<option value='puppies' selected='selected'>" . __('Puppies','trans-nlp') . "</option>";
        $widget_form.= "<option value='custom'>" . __('Custom','trans-nlp') . "</option>";
        break;
    case 'custom':
        $widget_form.= "<option value='placeholder'>" . __('Placeholder','trans-nlp') . "</option>";
        $widget_form.= "<option value='kittens'>" . __('Kittens','trans-nlp') . "</option>";
        $widget_form.= "<option value='puppies'>" . __('Puppies','trans-nlp') . "</option>";
        $widget_form.= "<option value='custom' selected='selected'>" . __('Custom','trans-nlp') . "</option>";
        break;
    default:
        $widget_form.= "<option value='placeholder' selected='selected'>" . __('Placeholder','trans-nlp') . "</option>";
        $widget_form.= "<option value='kittens'>" . __('Kittens','trans-nlp') . "</option>";
        $widget_form.= "<option value='puppies'>" . __('Puppies','trans-nlp') . "</option>";
        $widget_form.= "<option value='custom'>" . __('Custom','trans-nlp') . "</option>";
        break;
}
$widget_form.= "</select>";
// thumbnail_url
$widget_form.= $br;
$widget_form.= "<label for='thumbnail_url'>" . __('Thumbnail Class','trans-nlp') . "</label>";
$widget_form.= $br;
$widget_form.= "<input type='text' id='thumbnail_url' name='thumbnail_url' value='$thumbnail_url' />";
// thumbnail_class
$widget_form.= $br;
$widget_form.= "<label for='thumbnail_class'>" . __('Thumbnail Class','trans-nlp') . "</label>";
$widget_form.= $br;
$widget_form.= "<input type='text' id='thumbnail_class' name='thumbnail_class' value='$thumbnail_class' />";
// thumbnail_custom
$widget_form.= $br;
$widget_form.= "<label for='thumbnail_custom'>" . __('Custom Thumbnails','trans-nlp') . "</label>";
$widget_form.= $br;
$widget_form.= "<select id='thumbnail_custom' name='thumbnail_custom'>";
if( $thumbnail_custom == 'true' ) {
    $widget_form.= "<option value='true' selected='selected'>" . __('Yes','trans-nlp') . "</option>";
    $widget_form.= "<option value='false'>" . __('No','trans-nlp') . "</option>";
} else {
    $widget_form.= "<option value='true'>" . __('Yes','trans-nlp') . "</option>";
    $widget_form.= "<option value='false' selected='selected'>" . __('No','trans-nlp') . "</option>";
}
$widget_form.= "</select>";
// thumbnail_field
$widget_form.= $br;
$widget_form.= "<label for='thumbnail_field'>" . __('Thumbnail Custom Field','trans-nlp') . "</label>";
$widget_form.= $br;
$widget_form.= "<input type='text' id='thumbnail_field' name='thumbnail_field' value='$thumbnail_field' />";
// custom_post_type
$widget_form.= $br;
$widget_form.= "<label for='custom_post_type'>" . __('Custom Post Type','trans-nlp') . "</label>";
$widget_form.= $br;
$widget_form.= "<input type='text' id='custom_post_type' name='custom_post_type' value='$custom_post_type' />";
// category
$widget_form.= $br;
if( is_rtl() ) {
    $widget_form.= "<label for='category'>" . __('Category','trans-nlp')  . " " . __('or','trans-nlp') . " " . __('Categories','trans-nlp') . "</label>";
} else {
    $widget_form.= "<label for='category'>" . __('Category(ies)','trans-nlp') . "</label>";
}
$widget_form.= $br;
$widget_form.= "<input type='text' id='category' name='category' value='$category' />";
// tag
$widget_form.= $br;
if( is_rtl() ) {
    $widget_form.= "<label for='tag'>" . __('Tag','trans-nlp') . " " . __('or','trans-nlp') . " " . __('Tags','trans-nlp') . "</label>";
} else {
    $widget_form.= "<label for='tag'>" . __('Tag(s)','trans-nlp') . "</label>";
}
$widget_form.= $br;
$widget_form.= "<input type='text' id='tag' name='tag' value='$tag' />";
// paginate
$widget_form.= $br;
$widget_form.= "<label for='paginate'>" . __('Paginate Results','trans-nlp') . "</label>";
$widget_form.= $br;
$widget_form.= "<select id='paginate' name='paginate'>";
if( $paginate == 'true' ) {
    $widget_form.= "<option value='true' selected='selected'>" . __('Yes','trans-nlp') . "</option>";
    $widget_form.= "<option value='false'>" . __('No','trans-nlp') . "</option>";
} else {
    $widget_form.= "<option value='true'>" . __('Yes','trans-nlp') . "</option>";
    $widget_form.= "<option value='false' selected='selected'>" . __('No','trans-nlp') . "</option>";
}
$widget_form.= "</select>";
// posts_per_page
$widget_form.= $br;
$widget_form.= "<label for='posts_per_page'>" . __('Posts per Page','trans-nlp') . "</label>";
$widget_form.= $br;
$widget_form.= "<input type='text' id='posts_per_page' name='posts_per_page' value='$posts_per_page' />";
// display_content
$widget_form.= $br;
$widget_form.= "<label for='display_content'>" . __('Display Content','trans-nlp') . "</label>";
$widget_form.= $br;
$widget_form.= "<select id='display_content' name='display_content'>";
if( $display_content == 'true' ) {
    $widget_form.= "<option value='true' selected='selected'>" . __('Yes','trans-nlp') . "</option>";
    $widget_form.= "<option value='false'>" . __('No','trans-nlp') . "</option>";
} else {
    $widget_form.= "<option value='true'>" . __('Yes','trans-nlp') . "</option>";
    $widget_form.= "<option value='false' selected='selected'>" . __('No','trans-nlp') . "</option>";
}
$widget_form.= "</select>";
$widget_form.= $br;
// excerpt_length
$widget_form.= $br;
$widget_form.= "<label for='excerpt_length'>" . __('Excerpt Length','trans-nlp') . "</label>";
$widget_form.= $br;
$widget_form.= "<input type='text' id='excerpt_length' name='excerpt_length' value='$excerpt_length' />";
// auto_excerpt
$widget_form.= $br;
$widget_form.= "<label for='auto_excerpt'>" . __('Auto-Excerpt','trans-nlp') . "</label>";
$widget_form.= $br;
$widget_form.= "<select id='auto_excerpt' name='auto_excerpt'>";
if( $auto_excerpt == 'true' ) {
    $widget_form.= "<option value='true' selected='selected'>" . __('Yes','trans-nlp') . "</option>";
    $widget_form.= "<option value='false'>" . __('No','trans-nlp') . "</option>";
} else {
    $widget_form.= "<option value='true'>" . __('Yes','trans-nlp') . "</option>";
    $widget_form.= "<option value='false' selected='selected'>" . __('No','trans-nlp') . "</option>";
}
$widget_form.= "</select>";
// excerpt_trail
$widget_form.= $br;
$widget_form.= "<label for='excerpt_trail'>" . __('Excerpt Trail','trans-nlp') . "</label>";
$widget_form.= $br;
$widget_form.= "<select id='excerpt_trail' name='excerpt_trail'>";
if( $excerpt_trail == 'text' || empty($excerpt_trail) ) {
    $widget_form.= "<option value='text' selected='selected'>" . __('Text','trans-nlp') . "</option>";
    $widget_form.= "<option value='image'>" . __('Image','trans-nlp') . "</option>";
} else {
    $widget_form.= "<option value='text'>" . __('Text','trans-nlp') . "</option>";
    $widget_form.= "<option value='image' selected='selected'>" . __('Image','trans-nlp') . "</option>";
}
$widget_form.= "</select>";
// sort_by_date
$widget_form.= $br;
$widget_form.= "<label for='sort_by_date'>" . __('Sort by Date','trans-nlp') . "</label>";
$widget_form.= $br;
$widget_form.= "<select id='sort_by_date' name='sort_by_date'>";
if( $sort_by_date == 'true' ) {
    $widget_form.= "<option value='true' selected='selected'>" . __('Yes','trans-nlp') . "</option>";
    $widget_form.= "<option value='false'>" . __('No','trans-nlp') . "</option>";
} else {
    $widget_form.= "<option value='true'>" . __('Yes','trans-nlp') . "</option>";
    $widget_form.= "<option value='false' selected='selected'>" . __('No','trans-nlp') . "</option>";
}
$widget_form.= "</select>";
// sort_by_date
$widget_form.= $br;
$widget_form.= "<label for='sort_by_blog'>" . __('Sort by Blog ID','trans-nlp') . "</label>";
$widget_form.= $br;
$widget_form.= "<select id='sort_by_blog' name='sort_by_blog'>";
if( $sort_by_blog == 'true' ) {
    $widget_form.= "<option value='true' selected='selected'>" . __('Yes','trans-nlp') . "</option>";
    $widget_form.= "<option value='false'>" . __('No','trans-nlp') . "</option>";
} else {
    $widget_form.= "<option value='true'>" . __('Yes','trans-nlp') . "</option>";
    $widget_form.= "<option value='false' selected='selected'>" . __('No','trans-nlp') . "</option>";
}
$widget_form.= "</select>";
// sorting_order
$widget_form.= $br;
$widget_form.= "<label for='sorting_order'>" . __('Sorting Order','trans-nlp') . "</label>";
$widget_form.= $br;
$widget_form.= "<select id='sorting_order' name='sorting_order'>";
if( $sort_by_date == 'true' ) {
    if( $sorting_order == 'newer' || empty($sorting_order) ) {
        $widget_form.= "<option value='newer' selected='selected'>" . __('Newest to Oldest','trans-nlp') . "</option>";
        $widget_form.= "<option value='older'>" . __('Oldest to Newest','trans-nlp') . "</option>";
    } else {
        $widget_form.= "<option value='newer'>" . __('Newest to Oldest','trans-nlp') . "</option>";
        $widget_form.= "<option value='older' selected='selected'>" . __('Oldest to Newest','trans-nlp') . "</option>";
    }
} else {
    $widget_form.= "<option value='newer'>" . __('Newest to Oldest','trans-nlp') . "</option>";
    $widget_form.= "<option value='older' selected='selected'>" . __('Oldest to Newest','trans-nlp') . "</option>";
}
if( $sort_by_blog == 'true' ) {
    if( $sorting_order == 'asc' || empty($sorting_order) ) {
        $widget_form.= "<option value='asc' selected='selected'>" . __('Ascendant','trans-nlp') . "</option>";
        $widget_form.= "<option value='desc'>" . __('Descendant','trans-nlp') . "</option>";
    } else {
        $widget_form.= "<option value='asc'>" . __('Ascendant','trans-nlp') . "</option>";
        $widget_form.= "<option value='desc' selected='selected'>" . __('Descendant','trans-nlp') . "</option>";
    }
} else {
    $widget_form.= "<option value='asc'>" . __('Ascendant','trans-nlp') . "</option>";
    $widget_form.= "<option value='desc' selected='selected'>" . __('Descendant','trans-nlp') . "</option>";
}
$widget_form.= "</select>";
// sorting_limit
$widget_form.= $br;
$widget_form.= "<label for='sorting_limit'>" . __('Total Number of Posts','trans-nlp') . "</label>";
$widget_form.= $br;
$widget_form.= "<input type='text' id='sorting_limit' name='sorting_limit' value='$sorting_limit' />";
// post_status
$widget_form.= $br;
$widget_form.= "<label for='post_status'>" . __('Post Status','trans-nlp') . "</label>";
$widget_form.= $br;
$widget_form.= "<input type='text' id='post_status' name='post_status' value='$post_status' />";
// full_meta
$widget_form.= $br;
$widget_form.= "<label for='full_meta'>" . __('Full Metadata','trans-nlp') . "</label>";
$widget_form.= $br;
$widget_form.= "<select id='full_meta' name='full_meta'>";
if( $full_meta == 'true' ) {
    $widget_form.= "<option value='true' selected='selected'>" . __('Yes','trans-nlp') . "</option>";
    $widget_form.= "<option value='false'>" . __('No','trans-nlp') . "</option>";
} else {
    $widget_form.= "<option value='true'>" . __('Yes','trans-nlp') . "</option>";
    $widget_form.= "<option value='false' selected='selected'>" . __('No','trans-nlp') . "</option>";
}
$widget_form.= "</select>";
// css_style
$widget_form.= $br;
$widget_form.= "<label for='css_style'>" . __('Custom CSS Filename','trans-nlp') . "</label>";
$widget_form.= $br;
$widget_form.= "<input type='text' id='css_style' name='css_style' value='$css_style' />";
// wrapper_list_css
$widget_form.= $br;
$widget_form.= "<label for='wrapper_list_css'>" . __('Custom CSS Class for the list wrapper','trans-nlp') . "</label>";
$widget_form.= $br;
$widget_form.= "<input type='text' id='wrapper_list_css' name='wrapper_list_css' value='$wrapper_list_css' />";
// wrapper_block_css
$widget_form.= $br;
$widget_form.= "<label for='wrapper_block_css'>" . __('Custom CSS Class for the block wrapper','trans-nlp') . "</label>";
$widget_form.= $br;
$widget_form.= "<input type='text' id='wrapper_block_css' name='wrapper_block_css' value='$wrapper_block_css' />";
// Random posts
$widget_form.= $br;
$widget_form.= "<label for='random'>" . __('Random Posts','trans-nlp') . "</label>";
$widget_form.= $br;
$widget_form.= "<select id='random' name='random'>";
if( $random == 'true' ) {
    $widget_form.= "<option value='true' selected='selected'>" . __('Yes','trans-nlp') . "</option>";
    $widget_form.= "<option value='false'>" . __('No','trans-nlp') . "</option>";
} else {
    $widget_form.= "<option value='true'>" . __('Yes','trans-nlp') . "</option>";
    $widget_form.= "<option value='false' selected='selected'>" . __('No','trans-nlp') . "</option>";
}
$widget_form.= "</select>";
$widget_form.= $br;
$widget_form.= "<input type='button' id='nlposts_shortcode_submit' value='".__('Insert Shortcode','trans-nlp')."' />";
$widget_form.= $p_c;
$widget_form.= "</form>";
echo $widget_form;
?>
<script type="text/javascript" charset="utf-8">
    //<![CDATA[
    jQuery('#nlposts_shortcode_submit').click(function(){
        // Count words
        function nlp_countWords(s) {
            return s.split(/[ \t\r\n]/).length;
        }
        // Get the form fields
        var values = {};
        jQuery('#TB_ajaxContent form :input').each(function(index,field) {
            name = '#TB_ajaxContent form #'+field.id;
            values[jQuery(name).attr('id')] = jQuery(name).val();
        });
        // Default values
        var defaults = new Array();
        defaults['title'] = null;
        defaults['number_posts'] = '10';
        defaults['time_frame'] = '0';
        defaults['title_only'] = 'true';
        defaults['display_type'] = 'ulist';
        defaults['blog_id'] = null;
        defaults['ignore_blog'] = null;
        defaults['thumbnail'] = 'false';
        defaults['thumbnail_wh'] = '80x80';
        defaults['thumbnail_class'] = null;
        defaults['thumbnail_filler'] = 'placeholder';
        defaults['thumbnail_custom'] = 'false';
        defaults['thumbnail_field'] = null;
        defaults['custom_post_type'] = 'post';
        defaults['category'] = null;
        defaults['tag'] = null;
        defaults['paginate'] = 'false';
        defaults['posts_per_page'] = null;
        defaults['display_content'] = 'false';
        defaults['excerpt_length'] = null;
        defaults['auto_excerpt'] = 'false';
        defaults['full_meta'] = 'false';
        defaults['sort_by_date'] = 'false';
        defaults['sort_by_blog'] = 'false';
        defaults['sorting_order'] = 'desc';
        defaults['sorting_limit'] = null;
        defaults['post_status'] = 'publish';
        defaults['excerpt_trail'] = 'text';
        defaults['css_style'] = null;
        defaults['wrapper_list_css'] = 'nav nav-tabs nav-stacked';
        defaults['wrapper_block_css'] = 'content';
        defaults['instance'] = null;
        defaults['random'] = 'false';
        defaults['post_ignore'] = null;
        // Set the thumbnail size
        if( values.thumbnail_w && values.thumbnail_h ) {
            var thumbnail_wh = values.thumbnail_w+'x'+values.thumbnail_h;
            values['thumbnail_wh'] = thumbnail_wh;
            values['thumbnail_w'] = 'null';
            values['thumbnail_h'] = 'null';
        }
        // Clear the submit button so the shortcode doesn't take its value
        values['nlposts_shortcode_submit'] = null;
        // Build the shortcode
        var nlp_shortcode = '[nlposts';
        // Get the settings and values
        for( settings in values ) {
            // If they're not empty or null
            if( values[settings] && values[settings] != 'null' ) {
                // And they're not the default values
                if( values[settings] != defaults[settings] ) {
                    // Count words
                    if( nlp_countWords(String(values[settings])) > 1 ) {
                        // If more than 1 or a big single string, add quotes to the key=value
                        nlp_shortcode += ' '+settings +'="'+ values[settings]+'"';
                    } else {
                        // Otherwise, add the key=value
                        nlp_shortcode += ' '+settings +'='+ values[settings];
                    }
                }
            }
        }
        // Close the shortcode
        nlp_shortcode += ']';
        // insert the shortcode into the active editor
        tinyMCE.activeEditor.execCommand('mceInsertContent', 0, nlp_shortcode);
        // close Thickbox
        tb_remove();
    });
    //]]>
</script>