<?php
/*
Plugin Name: Network Latest Posts
Plugin URI: http://en.8elite.com/network-latest-posts
Description: Display the latest posts from the blogs in your network using it as a function, shortcode or widget.
Version: 3.5.4
Author: L'Elite
Author URI: http://laelite.info/
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
/* Comments
 *
 * Network Latest Posts version 3.0 has been totally rewritten,
 * it now uses WordPress hooks to improve the performance,
 * its easiness to maintain and to tweak in case of need.
 *
 * Because the list of variables became huge, I decided to put everything
 * inside an array, that way it looks clean and most of all READABLE from
 * a programmer's point of view, it's also a good programming practice ;).
 * I also provide backwards compatibility for the renamed variables.
 *
 * I'd like to thank Angelo (http://bitfreedom.com/) because his WPMU Recent
 * Posts Widget was the base code in previous versions of this plugin. He
 * inspired Network Latest Posts.
 *
 * I'd also like to thank the people who sent patches and helped improve the
 * functionalities giving ideas and providing invaluable feedback. Network
 * Latest Posts has evolved a lot thanks to them, below those of you who have
 * helped Network Latest Posts become what it is today:
 *
 * -- John Hawkins (9seeds.com)
 * --- Functionalities proposed:
 * ---- Custom Post Type
 * ---- Ignore Blog
 * **** Patches contributor
 *
 * -- Jenny Beaumont
 * --- Functionalities proposed:
 * ---- Taxonomy Filters (categories & tags)
 *
 * -- Tim (trailsherpa.com)
 * -- Functionalities proposed:
 * --- Excerpt Length
 *
 * -- Josh Maxwell
 * **** Spotted minor bug in the full_meta text
 *
 * -- Davo
 * --- Functionalities proposed:
 * ---- Pagination
 *
 * -- Sergeyzimin
 * --- Functionalities proposed:
 * ---- thumbnail_class
 * ---- Strip shorcodes from excerpts
 * **** Bugs spotted and fixed
 * **** Patch for the nlp_custom_excerpt function
 *
 * -- Greggo
 * **** Missing meta-info spotted
 * **** Missing site name Widget
 *
 * -- skepticblogsnet
 * --- Functionalities proposed:
 * ---- Override CSS classes for the wrapper tag
 *
 * -- Ricardoweb
 * --- Bug spotted $post_type should be $custom_post_type
 *
 * -- Jason Willis
 * --- Spotted deprecated functions register_sidebar_widget and register_widget_control
 *
 * -- Owagu
 * --- Bug spotted, custom post types didn't accept multiple values
 *
 * -- cyberdemon8
 * --- Patched for wp_register_sidebar_widget & wp_register_widget_control
 *
 * -- Kobus
 * --- Functionalities proposed:
 * ---- Random posts
 *
 * -- Aircut
 * --- Spotted an issue with Visual Composer plugin, their shortcodes were not being
 *     stripped out from excerpts
 *
 * -- Snapalot
 * --- Spotted an issue when placing NLPosts right before comments section, comments were
 *     added to different posts
 *
 * -- aaronbennett2097
 * --- Functionalities proposed:
 * ----- Thumbnails from Custom Fields
 *
 * -- kkalvaa
 * --- Spotted auto_excerpt bug
 *
 * -- James
 * --- Proposed ignore posts
 *
 * -- Anton Channing
 * --- Spotted blog IDs were missing from element classes
 *
 * -- Julien Dizdar
 * --- Spotted a bug in sorting parameters
 *
 * -- kkalvaa
 * --- Spotted ignored strings by translation files, this problem was due to
 * --- a loading hierarchy problem
 *
 * -- Gerard Bik
 * -- Proposed display post content instead of excerpts
 *
 * -- ThorHammer
 * --- Spotted a warning when NLPosts couldn't find posts.
 *
 * -- Claas Augner
 * **** Patch to correctly format dates for localization.
 *
 * That's it, let the fun begin!
 *
 */
// Requires widget class
require_once dirname( __FILE__ ) . '/network-latest-posts-widget.php';
/* Network Latest Posts Main Function
 *
 * Where the magic happens ;)
 *
 * List of Parameters
 *
 * -- @title              : Widget/Shortcode main title (section title)
 * -- @number_posts       : Number of posts BY blog to retrieve. Ex: 10 means, retrieve 10 posts for each blog found in the network
 * -- @time_frame         : Period of time to retrieve the posts from in days. Ex: 5 means, find all articles posted in the last 5 days
 * -- @title_only         : Display post titles only, if false then excerpts will be shown
 * -- @display_type       : How to display the articles, as an: unordered list (ulist), ordered list (olist) or block elements
 * -- @blog_id            : None, one or many blog IDs to be queried. Ex: 1,2 means, retrieve posts for blogs 1 and 2 only
 * -- @ignore_blog        : It takes the same values as blog_id but in this case this blogs will be ignored. Ex: 1,2 means, display all but 1 and 2
 * -- @thumbnail          : If true then thumbnails will be shown, if active and not found then a placeholder will be used instead
 * -- @thumbnail_wh       : Thumbnails size, width and height in pixels, while using the shortcode or a function this parameter must be passed like: '80x80'
 * -- @thumbnail_class    : Thumbnail class, set a custom class (alignleft, alignright, center, etc)
 * -- @thumbnail_filler   : Placeholder to use if the post's thumbnail couldn't be found, options: placeholder, kittens, puppies (what?.. I can be funny sometimes)
 * -- @thumbnail_custom   : Pull thumbnails from custom fields
 * -- @thumbnail_field    : Specify the custom field for thumbnail URL
 * -- @thumbnail_url      : Custom thumbnail URL
 * -- @custom_post_type   : Specify a custom post type: post, page or something-you-invented
 * -- @category           : Category or categories you want to display. Ex: cats,dogs means, retrieve posts containing the categories cats or dogs
 * -- @tag                : Same as categoy WordPress treats both taxonomies the same way; by the way, you can pass one or many (separated by commas)
 * -- @paginate           : Display results by pages, if used then the parameter posts_per_page must be specified, otherwise pagination won't be displayed
 * -- @posts_per_page     : Set the number of posts to display by page (paginate must be activated)
 * -- @display_content    : When true then post content will be displayed instead of excertps
 * -- @excerpt_length     : Set the excerpt's length in case you think it's too long for your needs Ex: 40 means, 40 words
 * -- @auto_excerpt       : If true then it will generate an excerpt from the post content, it's useful for those who forget to use the Excerpt field in the post edition page
 * -- @excerpt_trail      : Set the type of trail you want to append to the excerpts: text, image. The text will be _more_, the image is inside the plugin's img directory and it's called excerpt_trail.png
 * -- @full_meta          : Display the date and the author of the post, for the date/time each blog time format will be used
 * -- @sort_by_date       : Sorting capabilities, this will take all posts found (regardless their blogs) and sort them in order of recency, putting newest first
 * -- @sort_by_blog       : Sort by blog ID
 * -- @sorting_order      : Specify the sorting order: 'newer' means from newest to oldest posts, 'older' means from oldest to newest. Asc and desc for blog IDs
 * -- @sorting_limit      : Limit the number of posts to display. Ex: 5 means display 5 posts from all those found (even if 20 were found, only 5 will be displayed)
 * -- @post_status        : Specify the status of the posts you want to display: publish, new, pending, draft, auto-draft, future, private, inherit, trash
 * -- @css_style          : Use a custom CSS style instead of the one included by default, useful if you want to customize the front-end display: filename (without extension), this file must be located where your active theme CSS style is located
 * -- @wrapper_list_css   : Custom CSS classes for the list wrapper
 * -- @wrapper_block_css  : Custom CSS classes for the block wrapper
 * -- @instance           : This parameter is intended to differenciate each instance of the widget/shortcode/function you use, it's required in order for the asynchronous pagination links to work
 * -- @random             : Pull random posts (possible values: true or false, false by default)
 * -- @post_ignore        : Post ID(s) to ignore (default null) comma separated values ex: 1 or 1,2,3 > ignore posts ID 1 or 1,2,3 (post ID 1 = Hello World)
 */
function network_latest_posts( $parameters ) {
    // Global variables
    global $wpdb;
    //global $nlp_time_frame;
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
        'posts_per_page'   => NULL,          // Number of posts per page (paginate must be activated)
        'display_content'  => FALSE,         // Display post content (when false, excerpts will be displayed)
        'excerpt_length'   => NULL,          // Excerpt's length
        'auto_excerpt'     => FALSE,         // Generate excerpt from content
        'excerpt_trail'    => 'text',        // Excerpt's trailing element: text, image
        'full_meta'        => FALSE,         // Display full metadata
        'sort_by_date'     => FALSE,         // Display the latest posts first regardless of the blog they come from
        'sort_by_blog'     => FALSE,         // Sort by Blog ID
        'sorting_order'    => NULL,          // Sort posts from Newest to Oldest or vice versa (newer / older), asc / desc for blog ID
        'sorting_limit'    => NULL,          // Limit the number of sorted posts to display
        'post_status'      => 'publish',     // Post status (publish, new, pending, draft, auto-draft, future, private, inherit, trash)
        'css_style'        => NULL,          // Custom CSS _filename_ (ex: custom_style)
        'wrapper_list_css' => 'nav nav-tabs nav-stacked', // Custom CSS classes for the list wrapper
        'wrapper_block_css'=> 'content',     // Custom CSS classes for the block wrapper
        'instance'         => NULL,          // Instance identifier, used to uniquely differenciate each shortcode or widget used
        'random'           => FALSE,         // Pull random posts (true or false)
        'post_ignore'      => NULL           // Post ID(s) to ignore
    );
    // Parse & merge parameters with the defaults
    $settings = wp_parse_args( $parameters, $defaults );
    // Paranoid mode activated (yes I'm a security freak)
    foreach($settings as $parameter => $value) {
        // Strip everything
        $settings[$parameter] = strip_tags($value);
    }
    // Extract each parameter as its own variable
    extract( $settings, EXTR_SKIP );
    // If no instance was set, make one
    if( empty($instance) ) { $instance = 'default'; }
    // HTML Tags
    $html_tags = nlp_display_type($display_type, $instance, $wrapper_list_css, $wrapper_block_css);
    // If Custom CSS
    if( !empty($css_style) ) {
        // If RTL
        if( is_rtl() ) {
            // Tell WordPress this plugin is switching to RTL mode
            /* Set the text direction to RTL
             * This two variables will tell load-styles.php
             * load the Dashboard in RTL instead of LTR mode
             */
            global $wp_locale, $wp_styles;
            $wp_locale->text_direction = 'rtl';
            $wp_styles->text_direction = 'rtl';
        }
        // File path
        $cssfile = get_stylesheet_directory_uri().'/'.$css_style.'.css';
        // Load styles
        nlp_load_styles($cssfile);
    }
    // Display blog or blogs
    // if the user passes one value
    if( !preg_match("/,/",$blog_id) ) {
        // Always clean this stuff ;) (oh.. told you I'm a paranoid)
        $blog_id = (int)htmlspecialchars($blog_id);
        // Check if it's numeric
        if( is_numeric($blog_id) ) {
            // and put the sql
            $display = " AND blog_id = $blog_id ";
        }
    // if the user passes more than one value separated by commas
    } else {
        // create an array
        $display_arr = explode(",",$blog_id);
        // and repeat the sql for each ID found
        for( $counter=0; $counter < count($display_arr); $counter++){
            // Add AND the first time
            if( $counter == 0 ) {
                $display .= " AND blog_id = ".(int)$display_arr[$counter];
            // Add OR the rest of the time
            } else {
                $display .= " OR blog_id = ".(int)$display_arr[$counter];
            }
        }
    }
    // Ignore blog or blogs
    // if the user passes one value
    if( !preg_match("/,/",$ignore_blog) ) {
        // Always clean this stuff ;)
        $ignore_blog = (int)htmlspecialchars($ignore_blog);
        // Check if it's numeric
        if( is_numeric($ignore_blog) ) {
            // and put the sql
            $ignore = " AND blog_id != $ignore_blog ";
        }
    // if the user passes more than one value separated by commas
    } else {
        // create an array
        $ignore_arr = explode(",",$ignore_blog);
        // and repeat the sql for each ID found
        for( $counter=0; $counter < count($ignore_arr); $counter++){
            $ignore .= " AND blog_id != ".(int)$ignore_arr[$counter];
        }
    }
    // If multiple tags found, set an array
    if( preg_match("/,/",$tag) ) {
        $tag = explode(",",$tag);
    } else {
        if( !empty($tag) ) {
            $tag = str_split($tag,strlen($tag));
        }
    }
    // If multiple categories found, set an array
    if( preg_match("/,/",$category) ) {
        $category = explode(",",$category);
    } else {
        if( !empty($category) ) {
            $category = str_split($category,strlen($category));
        }
    }
    // If multiple post type found, set an array
    if( preg_match("/,/",$custom_post_type) ) {
        $custom_post_type = explode(",",$custom_post_type);
    } else {
        if( !empty($category) ) {
            $custom_post_type = str_split($custom_post_type,strlen($custom_post_type));
        }
    }
    // Paranoid ;)
    $time_frame = (int)$time_frame;
    // Get the list of blogs in order of most recent update, get only public and nonarchived/spam/mature/deleted
    if( $time_frame > 0 ) {
        // By blog ID except those ignored
        if( !empty($blog_id) && $blog_id != NULL ) {
            $blogs = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs WHERE
                public = '1' AND archived = '0' AND mature = '0' AND spam = '0' AND deleted = '0' $display
                    $ignore AND last_updated >= DATE_SUB(CURRENT_DATE(), INTERVAL $time_frame DAY)
                        ORDER BY last_updated DESC");
        // Everything but ignored blogs
        } else {
            $blogs = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs WHERE
                public = '1' AND archived = '0' AND mature = '0' AND spam = '0' AND deleted = '0'
                    $ignore AND last_updated >= DATE_SUB(CURRENT_DATE(), INTERVAL $time_frame DAY)
                        ORDER BY last_updated DESC");
        }
    // Everything written so far
    } else {
        // By blog ID except those ignored
        if( !empty($blog_id) && $blog_id != NULL ) {
            $blogs = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs WHERE
                public = '1' AND archived = '0' AND mature = '0' AND spam = '0' AND deleted = '0' $display
                    $ignore ORDER BY last_updated DESC");
        // Everything but ignored blogs
        } else {
            $blogs = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs WHERE
                public = '1' AND archived = '0' AND mature = '0' AND spam = '0' AND deleted = '0'
                    $ignore ORDER BY last_updated DESC");
        }
    }
    // Ignore one or many posts
    // if the user passes one value
    if( !preg_match("/,/",$post_ignore) ) {
        // Always clean this stuff ;) (oh.. told you I'm a paranoid)
        $post_ignore = array( 0 => (int)htmlspecialchars($post_ignore) );
    // if the user passes more than one value separated by commas
    } else {
        // create an array
        $post_ignore = explode(",",$post_ignore);
    }
    // If it found something
    if( $blogs ) {
        // Count blogs found
        $count_blogs = count($blogs);
        // Dig into each blog
        foreach( $blogs as $blog_key ) {
            // Options: Site URL, Blog Name, Date Format
            ${'blog_url_'.$blog_key} = get_blog_option($blog_key,'siteurl');
            ${'blog_name_'.$blog_key} = get_blog_option($blog_key,'blogname');
            ${'date_format_'.$blog_key} = get_blog_option($blog_key,'date_format');
            // Orderby
            if( $random == 'true' ) { $orderby = 'rand'; } else { $orderby = 'post_date'; }
            // Categories or Tags
            if( !empty($category) && !empty($tag) ) {
                $args = array(
                    'tax_query' => array(
                        'relation' => 'OR',
                        array(
                            'taxonomy' => 'category',
                            'field' => 'slug',
                            'terms' => $category
                        ),
                        array(
                            'taxonomy' => 'post_tag',
                            'field' => 'slug',
                            'terms' => $tag
                        )
                    ),
                    'numberposts' => $number_posts,
                    'post_status' => $post_status,
                    'post_type' => $custom_post_type,
                    'orderby' => $orderby
                );
            }
            // Categories only
            if( !empty($category) && empty($tag) ) {
                $args = array(
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'category',
                            'field' => 'slug',
                            'terms' => $category
                        )
                    ),
                    'numberposts' => $number_posts,
                    'post_status' => $post_status,
                    'post_type' => $custom_post_type,
                    'orderby' => $orderby
                );
            }
            // Tags only
            if( !empty($tag) && empty($category) ) {
                $args = array(
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'post_tag',
                            'field' => 'slug',
                            'terms' => $tag
                        )
                    ),
                    'numberposts' => $number_posts,
                    'post_status' => $post_status,
                    'post_type' => $custom_post_type,
                    'orderby' => $orderby
                );
            }
            // Everything by Default
            if( empty($category) && empty($tag) ) {
                // By default
                $args = array(
                    'numberposts' => $number_posts,
                    'post_status' => $post_status,
                    'post_type' => $custom_post_type,
                    'orderby' => $orderby
                );
            }
            // Switch to the blog
            switch_to_blog($blog_key);
            // Get posts
            ${'posts_'.$blog_key} = get_posts($args);
            // Check if posts with the defined criteria were found
            if( empty(${'posts_'.$blog_key}) ) {
                /* If no posts matching the criteria were found then
                 * move to the next blog
                 */
                next($blogs);
            }
            // Put everything inside an array for sorting purposes
            foreach( ${'posts_'.$blog_key} as $post ) {
                // Access all post data
                setup_postdata($post);
                // Sort by blog ID
                if( $sort_by_blog == 'true' ) {
                    // Ignore Posts
                    if( !in_array( $post->ID, $post_ignore ) ) {
                        // Put inside another array and use blog ID as keys
                        $all_posts[$blog_key.$post->post_modified] = $post;
                    }
                } else {
                    // Ignore Posts
                    if( !in_array( $post->ID, $post_ignore ) ) {
                        // Put everything inside another array using the modified date as
                        // the array keys
                        $all_posts[$post->post_modified] = $post;
                    }
                }
                // The guid is the only value which can differenciate a post from
                // others in the whole network
                $all_permalinks[$post->guid] = get_blog_permalink($blog_key, $post->ID);
                $all_blogkeys[$post->guid] = $blog_key;
            }
            // Back the current blog
            restore_current_blog();
        }
        // If no content was found
        if( empty($all_posts) ) {
            // Nothing to do here, let people know and get out of here
            echo "<div class='alert'><p>".__("Sorry, I couldn't find any recent posts matching your parameters.","trans-nlp")."</p></div>";
            return;
        }
        // Sort by date (regardless blog IDs)
        if( $sort_by_date == 'true' ) {
            // Sorting order (newer / older)
            if( !empty($sorting_order) ) {
                switch( $sorting_order ) {
                    // From newest to oldest
                    case "newer":
                        // Sort the array
                        @krsort($all_posts);
                        // Limit the number of posts
                        if( !empty($sorting_limit) ) {
                            $all_posts = @array_slice($all_posts,0,$sorting_limit,true);
                        }
                        break;
                    // From oldest to newest
                    case "older":
                        // Sort the array
                        @ksort($all_posts);
                        // Limit the number of posts
                        if( !empty($sorting_limit) ) {
                            $all_posts = @array_slice($all_posts,0,$sorting_limit,true);
                        }
                        break;
                    // Newest to oldest by default
                    default:
                        // Sort the array
                        @krsort($all_posts);
                        // Limit the number of posts
                        if( !empty($sorting_limit) ) {
                            $all_posts = @array_slice($all_posts,0,$sorting_limit,true);
                        }
                        break;
                }
            } else {
                // Sort the array
                @krsort($all_posts);
                // Limit the number of posts
                if( !empty($sorting_limit) ) {
                    $all_posts = @array_slice($all_posts,0,$sorting_limit,true);
                }
            }
        }
        // Sort by blog ID
        if( $sort_by_blog == 'true' ) {
            // Sorting order (newer / older)
            if( !empty($sorting_order) ) {
                switch( $sorting_order ) {
                    // Ascendant
                    case "asc":
                        // Sort the array
                        @ksort($all_posts);
                        // Limit the number of posts
                        if( !empty($sorting_limit) ) {
                            $all_posts = @array_slice($all_posts,0,$sorting_limit,true);
                        }
                        break;
                    // Descendant
                    case "desc":
                        // Sort the array
                        @krsort($all_posts);
                        // Limit the number of posts
                        if( !empty($sorting_limit) ) {
                            $all_posts = @array_slice($all_posts,0,$sorting_limit,true);
                        }
                        break;
                    // Newest to oldest by default
                    default:
                        // Sort the array
                        @krsort($all_posts);
                        // Limit the number of posts
                        if( !empty($sorting_limit) ) {
                            $all_posts = @array_slice($all_posts,0,$sorting_limit,true);
                        }
                        break;
                }
            } else {
                // Sort the array
                @ksort($all_posts);
                // Limit the number of posts
                if( !empty($sorting_limit) ) {
                    $all_posts = @array_slice($all_posts,0,$sorting_limit,true);
                }
            }
        }
        // Open content box
        echo $html_tags['content_o'];
        // NLPosts title
        if( !empty($title) ) {
            // Open widget title box
            echo $html_tags['wtitle_o'];
            // Print the title
            echo $title;
            // Close widget title box
            echo $html_tags['wtitle_c'];
        }
        // Open wrapper
        echo $html_tags['wrapper_o'];
        // Paginate results
        if( $paginate && $posts_per_page ) {
            // Page number
            $pag = isset( $_GET['pag'] ) ? abs( (int) $_GET['pag'] ) : 1;
            // Break all posts into pages
            $pages = array_chunk($all_posts, $posts_per_page);
            // Set the page number variable
            add_query_arg('pag','%#%');
            // Print out the posts
            foreach( $pages[$pag-1] as $field ) {
                // Open item box
                $item_o = $html_tags['item_o'];
                $item_o = str_replace("'>"," nlposts-siteid-".$all_blogkeys[$field->guid]."'>", $item_o);
                echo $item_o;
                // Thumbnails
                if( $thumbnail === 'true' ) {
                    // Open thumbnail container
                    echo $html_tags['thumbnail_o'];
                    // Open thumbnail item placeholder
                    echo $html_tags['thumbnail_io'];
                    // Switch to the blog
                    switch_to_blog($all_blogkeys[$field->guid]);
                    // Put the dimensions into an array
                    $thumbnail_size = str_replace('x',',',$thumbnail_wh);
                    $thumbnail_size = explode(',',$thumbnail_size);
                    if( $thumbnail_custom != 'true' && $thumbnail_field == NULL ) {
                        // Get the thumbnail
                        $thumb_html = get_the_post_thumbnail($field->ID,$thumbnail_size,array('class' =>$thumbnail_class, 'alt' => $field->post_title, 'title' => $field->post_title));
                    } else {
                        $thumbnail_custom_field = get_post_meta($field->ID, $thumbnail_field, true);
                        if( !empty( $thumbnail_custom_field ) ) {
                            // Get custom thumbnail
                            $thumb_html = "<img src='".$thumbnail_custom_field."' width='".$thumbnail_size[0]."' height='".$thumbnail_size[1]." alt='".$field->post_title."' title='".$field->post_title."' />";
                        } else {
                            // Get the regular thumbnail
                            $thumb_html = get_the_post_thumbnail($field->ID,$thumbnail_size,array('class' =>$thumbnail_class, 'alt' => $field->post_title, 'title' => $field->post_title));
                        }
                    }
                    // If there is a thumbnail
                    if( !empty($thumb_html) ) {
                        // Display the thumbnail
                        echo "<a href='".$all_permalinks[$field->guid]."'>$thumb_html</a>";
                    // Thumbnail not found
                    } else {
                        // Put a placeholder with the post title
                        switch($thumbnail_filler) {
                            // Placeholder provided by Placehold.it
                            case 'placeholder':
                                echo "<a href='".$all_permalinks[$field->guid]."'><img src='http://placehold.it/".$thumbnail_wh."&text=".$field->post_title."' alt='".$field->post_title."' title='".$field->post_title."' /></a>";
                                break;
                            // Just for fun Kittens thanks to PlaceKitten
                            case 'kittens':
                                echo "<a href='".$all_permalinks[$field->guid]."'><img src='http://placekitten.com/".$thumbnail_size[0]."/".$thumbnail_size[1]."' alt='".$field->post_title."' title='".$field->post_title."' /></a>";
                                break;
                            // More fun Puppies thanks to PlaceDog
                            case 'puppies':
                                echo "<a href='".$all_permalinks[$field->guid]."'><img src='http://placedog.com/".$thumbnail_size[0]."/".$thumbnail_size[1]."' alt='".$field->post_title."' title='".$field->post_title."' /></a>";
                                break;
                            case 'custom':
                                if( !empty( $thumbnail_url ) ) {
                                    echo "<a href='".$all_permalinks[$field->guid]."'><img src='".$thumbnail_url."' alt='".$field->post_title."' title='".$field->post_title."' width='".$thumbnail_size[0]."' height='".$thumbnail_size[1]."' /></a>";
                                } else {
                                    echo "<a href='".$all_permalinks[$field->guid]."'><img src='http://placehold.it/".$thumbnail_wh."&text=".$field->post_title."' alt='".$field->post_title."' title='".$field->post_title."' /></a>";
                                }
                                break;
                            // Boring by default ;)
                            default:
                                echo "<a href='".$all_permalinks[$field->guid]."'><img src='http://placehold.it/".$thumbnail_wh."&text=".$field->post_title."' alt='".$field->post_title."' title='".$field->post_title."' /></a>";
                                break;
                        }
                    }
                    // Back the current blog
                    restore_current_blog();
                    // Open title box
                    echo $html_tags['title_o'];
                    // Print the title
                    echo "<a href='".$all_permalinks[$field->guid]."'>".$field->post_title."</a>";
                    // Close the title box
                    echo $html_tags['title_c'];
                    if( $full_meta === 'true' ) {
                        // Open meta box
                        echo $html_tags['meta_o'];
                        // Set metainfo
                        $author = get_user_by('id',$field->post_author);
                        $format = (string)${'date_format_'.$all_blogkeys[$field->guid]};
                        $datepost = date_i18n($format, strtotime(trim( $field->post_date) ) );
                        $blog_name = '<a href="'.${'blog_url_'.$all_blogkeys[$field->guid]}.'">'.${'blog_name_'.$all_blogkeys[$field->guid]}."</a>";
                        // The network's root (main blog) is called 'blog',
                        // so we have to set this up because the url ignores the root's subdirectory
                        if( $all_blogkeys[$field->guid] == 1 ) {
                            // Author's page for the main blog
                            $author_url = ${'blog_url_'.$all_blogkeys[$field->guid]}.'/blog/author/'.$author->user_login;
                        } else {
                            // Author's page for other blogs
                            $author_url = ${'blog_url_'.$all_blogkeys[$field->guid]}.'/author/'.$author->user_login;
                        }
                        // Print metainfo
                        echo $blog_name . ' - ' . __('Published on','trans-nlp') . ' ' . $datepost . ' ' . __('by','trans-nlp') . ' ' . '<a href="' . $author_url . '">' . $author->display_name . '</a>';
                        // Close meta box
                        echo $html_tags['meta_c'];
                    }
                    // Print the content
                    if( $title_only === 'false' ) {
                        // Open excerpt wrapper
                        echo $html_tags['excerpt_o'];
                        // Display excerpts or content
                        if( $display_content != 'true' ) {
                            // Custom Excerpt
                            if( $auto_excerpt != 'true' ) {
                                // Print out the excerpt
                                echo nlp_custom_excerpt($excerpt_length, $field->post_excerpt, $all_permalinks[$field->guid],$excerpt_trail);
                            // Extract excerpt from content
                            } else {
                                // Get the excerpt
                                echo nlp_custom_excerpt($excerpt_length, $field->post_content, $all_permalinks[$field->guid],$excerpt_trail);
                            }
                        } else {
                            // Display post content
                            echo nl2br( do_shortcode( $field->post_content ) );
                        }
                        // Close excerpt wrapper
                        echo $html_tags['excerpt_c'];
                    }
                    // Close thumbnail item placeholder
                    echo $html_tags['thumbnail_ic'];
                    // Close thumbnail container
                    echo $html_tags['thumbnail_c'];
                } else {
                    // Open title box
                    echo $html_tags['title_o'];
                    // Print the title
                    echo "<a href='".$all_permalinks[$field->guid]."'>".$field->post_title."</a>";
                    // Close the title box
                    echo $html_tags['title_c'];
                    if( $full_meta === 'true' ) {
                        // Open meta box
                        echo $html_tags['meta_o'];
                        // Set metainfo
                        $author = get_user_by('id',$field->post_author);
                        $format = (string)${'date_format_'.$all_blogkeys[$field->guid]};
                        $datepost = date_i18n($format, strtotime(trim( $field->post_date) ) );
                        $blog_name = '<a href="'.${'blog_url_'.$all_blogkeys[$field->guid]}.'">'.${'blog_name_'.$all_blogkeys[$field->guid]}."</a>";
                        // The network's root (main blog) is called 'blog',
                        // so we have to set this up because the url ignores the root's subdirectory
                        if( $all_blogkeys[$field->guid] == 1 ) {
                            // Author's page for the main blog
                            $author_url = ${'blog_url_'.$all_blogkeys[$field->guid]}.'/blog/author/'.$author->user_login;
                        } else {
                            // Author's page for other blogs
                            $author_url = ${'blog_url_'.$all_blogkeys[$field->guid]}.'/author/'.$author->user_login;
                        }
                        // Print metainfo
                        echo $blog_name . ' - ' . __('Published on','trans-nlp') . ' ' . $datepost . ' ' . __('by','trans-nlp') . ' ' . '<a href="' . $author_url . '">' . $author->display_name . '</a>';
                        // Close meta box
                        echo $html_tags['meta_c'];
                    }
                    // Print the content
                    if( $title_only === 'false' ) {
                        // Open excerpt wrapper
                        echo $html_tags['excerpt_o'];
                        // Display excerpts or content
                        if( $display_content != 'true' ) {
                            // Custom Excerpt
                            if( $auto_excerpt != 'true' ) {
                                // Print out the excerpt
                                echo nlp_custom_excerpt($excerpt_length, $field->post_excerpt, $all_permalinks[$field->guid],$excerpt_trail);
                            // Extract excerpt from content
                            } else {
                                // Get the excerpt
                                echo nlp_custom_excerpt($excerpt_length, $field->post_content, $all_permalinks[$field->guid],$excerpt_trail);
                            }
                        } else {
                            // Display post content
                            echo nl2br( do_shortcode( $field->post_content ) );
                        }
                        // Close excerpt wrapper
                        echo $html_tags['excerpt_c'];
                    }
                }
                // Close item box
                echo $html_tags['item_c'];
            }
            // Print out the pagination menu
            for($i=1; $i< count($pages)+1; $i++) {
                // Count the number of pages
                $total += 1;
            }
            // Open pagination wrapper
            echo $html_tags['pagination_o'];
            echo paginate_links( array(
                'base' => add_query_arg( 'pag', '%#%' ),
                'format' => '',
                'prev_text' => __('&laquo;','trans-nlp'),
                'next_text' => __('&raquo;','trans-nlp'),
                'total' => $total,
                'current' => $pag,
                'type' => 'list'
            ));
            // Close pagination wrapper
            echo $html_tags['pagination_c'];
            // Close wrapper
            echo $html_tags['wrapper_c'];
            /*
             * jQuery function
             * Asynchronous pagination links
             */
            echo '
            <script type="text/javascript" charset="utf-8">
                //<![CDATA[
                jQuery(document).ready(function(){
                    jQuery(".nlp-instance-'.$instance.' .pagination a").live("click", function(e){
                        e.preventDefault();
                        var link = jQuery(this).attr("href");
                        jQuery(".nlp-instance-'.$instance.' .nlposts-wrapper").html("<style type=\"text/css\">p.loading { text-align:center;margin:0 auto; padding:20px; }</style><p class=\"loading\"><img src=\"'.plugins_url('/img/loader.gif', __FILE__) .'\" /></p>");
                        jQuery(".nlp-instance-'.$instance.' .nlposts-wrapper").fadeOut("slow",function(){
                            jQuery(".nlp-instance-'.$instance.' .nlposts-wrapper").load(link+" .nlp-instance-'.$instance.' .nlposts-wrapper > *").fadeIn(3000);
                        });

                    });
                });
                //]]>
            </script>';
            // Close content box
            echo $html_tags['content_c'];
        // Without pagination
        } else {
            // Print out the posts
            foreach( $all_posts as $field ) {
                // Open item box
                $item_o = $html_tags['item_o'];
                $item_o = str_replace("'>"," nlposts-siteid-".$all_blogkeys[$field->guid]."'>", $item_o);
                echo $item_o;
                // Thumbnails
                if( $thumbnail === 'true' ) {
                    // Open thumbnail container
                    echo $html_tags['thumbnail_o'];
                    // Open thumbnail item placeholder
                    echo $html_tags['thumbnail_io'];
                    // Switch to the blog
                    switch_to_blog($all_blogkeys[$field->guid]);
                    // Put the dimensions into an array
                    $thumbnail_size = str_replace('x',',',$thumbnail_wh);
                    $thumbnail_size = explode(',',$thumbnail_size);
                    if( $thumbnail_custom != 'true' && $thumbnail_field == NULL ) {
                        // Get the thumbnail
                        $thumb_html = get_the_post_thumbnail($field->ID,$thumbnail_size,array('class' =>$thumbnail_class, 'alt' => $field->post_title, 'title' => $field->post_title));
                    } else {
                        $thumbnail_custom_field = get_post_meta($field->ID, $thumbnail_field, true);
                        if( !empty( $thumbnail_custom_field ) ) {
                            // Get custom thumbnail
                            $thumb_html = "<img src='".$thumbnail_custom_field."' width='".$thumbnail_size[0]."' height='".$thumbnail_size[1]." alt='".$field->post_title."' title='".$field->post_title."' />";
                        } else {
                            // Get the regular thumbnail
                            $thumb_html = get_the_post_thumbnail($field->ID,$thumbnail_size,array('class' =>$thumbnail_class, 'alt' => $field->post_title, 'title' => $field->post_title));
                        }
                    }
                    // If there is a thumbnail
                    if( !empty($thumb_html) ) {
                        // Display the thumbnail
                        echo "<a href='".$all_permalinks[$field->guid]."'>$thumb_html</a>";
                    // Thumbnail not found
                    } else {
                        // Put a placeholder with the post title
                        switch($thumbnail_filler) {
                            // Placeholder provided by Placehold.it
                            case 'placeholder':
                                echo "<a href='".$all_permalinks[$field->guid]."'><img src='http://placehold.it/".$thumbnail_wh."&text=".$field->post_title."' alt='".$field->post_title."' title='".$field->post_title."' /></a>";
                                break;
                            // Just for fun Kittens thanks to PlaceKitten
                            case 'kittens':
                                echo "<a href='".$all_permalinks[$field->guid]."'><img src='http://placekitten.com/".$thumbnail_size[0]."/".$thumbnail_size[1]."' alt='".$field->post_title."' title='".$field->post_title."' /></a>";
                                break;
                            // More fun Puppies thanks to PlaceDog
                            case 'puppies':
                                echo "<a href='".$all_permalinks[$field->guid]."'><img src='http://placedog.com/".$thumbnail_size[0]."/".$thumbnail_size[1]."' alt='".$field->post_title."' title='".$field->post_title."' /></a>";
                                break;
                            case 'custom':
                                if( !empty( $thumbnail_url ) ) {
                                    echo "<a href='".$all_permalinks[$field->guid]."'><img src='".$thumbnail_url."' alt='".$field->post_title."' title='".$field->post_title."' /></a>";
                                } else {
                                    echo "<a href='".$all_permalinks[$field->guid]."'><img src='http://placehold.it/".$thumbnail_wh."&text=".$field->post_title."' alt='".$field->post_title."' title='".$field->post_title."' /></a>";
                                }
                                break;
                            // Boring by default ;)
                            default:
                                echo "<a href='".$all_permalinks[$field->guid]."'><img src='http://placehold.it/".$thumbnail_wh."&text=".$field->post_title."' alt='".$field->post_title."' title='".$field->post_title."' /></a>";
                                break;
                        }
                    }
                    // Back the current blog
                    restore_current_blog();
                    // Open title box
                    echo $html_tags['title_o'];
                    // Print the title
                    echo "<a href='".$all_permalinks[$field->guid]."'>".$field->post_title."</a>";
                    // Close the title box
                    echo $html_tags['title_c'];
                    if( $full_meta === 'true' ) {
                        // Open meta box
                        echo $html_tags['meta_o'];
                        // Set metainfo
                        $author = get_user_by('id',$field->post_author);
                        $format = (string)${'date_format_'.$all_blogkeys[$field->guid]};
                        $datepost = date_i18n($format, strtotime(trim( $field->post_date) ) );
                        $blog_name = '<a href="'.${'blog_url_'.$all_blogkeys[$field->guid]}.'">'.${'blog_name_'.$all_blogkeys[$field->guid]}."</a>";
                        // The network's root (main blog) is called 'blog',
                        // so we have to set this up because the url ignores the root's subdirectory
                        if( $all_blogkeys[$field->guid] == 1 ) {
                            // Author's page for the main blog
                            $author_url = ${'blog_url_'.$all_blogkeys[$field->guid]}.'/blog/author/'.$author->user_login;
                        } else {
                            // Author's page for other blogs
                            $author_url = ${'blog_url_'.$all_blogkeys[$field->guid]}.'/author/'.$author->user_login;
                        }
                        // Print metainfo
                        echo $blog_name . ' - ' . __('Published on','trans-nlp') . ' ' . $datepost . ' ' . __('by','trans-nlp') . ' ' . '<a href="' . $author_url . '">' . $author->display_name . '</a>';
                        // Close meta box
                        echo $html_tags['meta_c'];
                    }
                    // Print the content
                    if( $title_only === 'false' ) {
                        // Open excerpt wrapper
                        echo $html_tags['excerpt_o'];
                        // Display excerpts or content
                        if( $display_content != 'true' ) {
                            // Custom Excerpt
                            if( $auto_excerpt != 'true' ) {
                                // Print out the excerpt
                                echo nlp_custom_excerpt($excerpt_length, $field->post_excerpt, $all_permalinks[$field->guid],$excerpt_trail);
                            // Extract excerpt from content
                            } else {
                                // Get the excerpt
                                echo nlp_custom_excerpt($excerpt_length, $field->post_content, $all_permalinks[$field->guid],$excerpt_trail);
                            }
                        } else {
                            // Display post content
                            echo nl2br( do_shortcode( $field->post_content ) );
                        }
                        // Close excerpt wrapper
                        echo $html_tags['excerpt_c'];
                    }
                    // Close thumbnail item placeholder
                    echo $html_tags['thumbnail_ic'];
                    // Close thumbnail container
                    echo $html_tags['thumbnail_c'];
                } else {
                    // Open title box
                    echo $html_tags['title_o'];
                    // Print the title
                    echo "<a href='".$all_permalinks[$field->guid]."'>".$field->post_title."</a>";
                    // Close the title box
                    echo $html_tags['title_c'];
                    if( $full_meta === 'true' ) {
                        // Open meta box
                        echo $html_tags['meta_o'];
                        // Set metainfo
                        $author = get_user_by('id',$field->post_author);
                        $format = (string)${'date_format_'.$all_blogkeys[$field->guid]};
                        $datepost = date_i18n($format, strtotime(trim( $field->post_date) ) );
                        $blog_name = '<a href="'.${'blog_url_'.$all_blogkeys[$field->guid]}.'">'.${'blog_name_'.$all_blogkeys[$field->guid]}."</a>";
                        // The network's root (main blog) is called 'blog',
                        // so we have to set this up because the url ignores the root's subdirectory
                        if( $all_blogkeys[$field->guid] == 1 ) {
                            // Author's page for the main blog
                            $author_url = ${'blog_url_'.$all_blogkeys[$field->guid]}.'/blog/author/'.$author->user_login;
                        } else {
                            // Author's page for other blogs
                            $author_url = ${'blog_url_'.$all_blogkeys[$field->guid]}.'/author/'.$author->user_login;
                        }
                        // Print metainfo
                        echo $blog_name . ' - ' . __('Published on','trans-nlp') . ' ' . $datepost . ' ' . __('by','trans-nlp') . ' ' . '<a href="' . $author_url . '">' . $author->display_name . '</a>';
                        // Close meta box
                        echo $html_tags['meta_c'];
                    }
                    // Print the content
                    if( $title_only === 'false' ) {
                        // Open excerpt wrapper
                        echo $html_tags['excerpt_o'];
                        // Display excerpts or content
                        if( $display_content != 'true' ) {
                            // Custom Excerpt
                            if( $auto_excerpt != 'true' ) {
                                // Print out the excerpt
                                echo nlp_custom_excerpt($excerpt_length, $field->post_excerpt, $all_permalinks[$field->guid],$excerpt_trail);
                            // Extract excerpt from content
                            } else {
                                // Get the excerpt
                                echo nlp_custom_excerpt($excerpt_length, $field->post_content, $all_permalinks[$field->guid],$excerpt_trail);
                            }
                        } else {
                            // Display post content
                            echo nl2br( do_shortcode( $field->post_content ) );
                        }
                        // Close excerpt wrapper
                        echo $html_tags['excerpt_c'];
                    }
                }
                // Close item box
                echo $html_tags['item_c'];
            }
            // Close wrapper
            echo $html_tags['wrapper_c'];
            // Close content box
            echo $html_tags['content_c'];
        }
    }
    // Reset post data
    wp_reset_postdata();
}

/* Shortcode function
 *
 * @atts: attributes passed to the main function
 * return @shortcode
 */
function network_latest_posts_shortcode($atts) {
    if( !empty($atts) ) {
        // Legacy mode due to variable renaming
        // So existent shorcodes don't break ;)
        foreach( $atts as $key => $value ) {
            switch( $key ) {
                case 'number':
                    $atts['number_posts'] = $value;
                    break;
                case 'days':
                    $atts['time_frame'] = $value;
                    break;
                case 'titleonly':
                    $atts['title_only'] = $value;
                    break;
                case 'begin_wrap':
                    $atts['before_wrap'] = $value;
                    break;
                case 'end_wrap':
                    $atts['after_wrap'] = $value;
                    break;
                case 'blogid':
                    $atts['blog_id'] = $value;
                    break;
                case 'cpt':
                    $atts['custom_post_type'] = $value;
                    break;
                case 'cat':
                    $atts['category'] = $value;
                    break;
                default:
                    $atts[$key] = $value;
                    break;
            }
        }
        extract($atts);
    }
    // Start the output buffer to control the display position
    ob_start();
    // Get the posts
    network_latest_posts($atts);
    // Output the content
    $shortcode = ob_get_contents();
    // Clean the output buffer
    ob_end_clean();
    // Put the content where we want
    return $shortcode;
}
// Add the shortcode functionality
add_shortcode('nlposts','network_latest_posts_shortcode');

/* Limit excerpt length
 * @count: excerpt length
 * @content: excerpt content
 * @permalink: link to the post
 * return customized @excerpt
 */
function nlp_custom_excerpt($count, $content, $permalink, $excerpt_trail){
    if($count == 0 || $count == 'null') { $count = 55; }
    /* Strip shortcodes
     * Due to an incompatibility issue between Visual Composer
     * and WordPress strip_shortcodes hook, I'm stripping
     * shortcodes using regex. (27-09-2012)
     *
     * $content = strip_tags(strip_shortcodes($content));
     *
     * replaced by
     *
     * $content = preg_replace("/\[(.*?)\]/i", '', $content);
     * $content = strip_tags($content);
     */
    $content = preg_replace("/\[(.*?)\]/i", '', $content);
    $content = strip_tags($content);
    // Get the words
    $words = explode(' ', $content, $count + 1);
    // Pop everything
    array_pop($words);
    // Add trailing dots
    array_push($words, '...');
    // Add white spaces
    $content = implode(' ', $words);
    // Add the trail
    switch( $excerpt_trail ) {
        // Text
        case 'text':
            $content = $content.'<a href="'.$permalink.'">'.__('more','trans-nlp').'</a>';
            break;
        // Image
        case 'image':
            $content = $content.'<a href="'.$permalink.'"><img src="'.plugins_url('/img/excerpt_trail.png', __FILE__) .'" alt="'.__('more','trans-nlp').'" title="'.__('more','trans-nlp').'" /></a>';
            break;
        // Text by default
        default:
            $content = $content.'<a href="'.$permalink.'">'.__('more','trans-nlp').'</a>';
            break;
    }
    // Return the excerpt
    return $content;
}

/* HTML tags
 * Styling purposes
 * @display_type: ulist, olist, block, inline
 * return @html_tags
 */
function nlp_display_type($display_type, $instance, $wrapper_list_css, $wrapper_block_css) {
    // Instances
    if( !empty($instance) ) { $nlp_instance = "nlp-instance-$instance"; }
    // Display Types
    switch($display_type) {
        // Unordered list
        case "ulist":
            $html_tags = array(
                'wrapper_o' => "<ul class='nlposts-wrapper nlposts-ulist $wrapper_list_css'>",
                'wrapper_c' => "</ul>",
                'wtitle_o' => "<h2 class='nlposts-ulist-wtitle'>",
                'wtitle_c' => "</h2>",
                'item_o' => "<li class='nlposts-ulist-litem'>",
                'item_c' => "</li>",
                'content_o' => "<div class='nlposts-container nlposts-ulist-container $nlp_instance'>",
                'content_c' => "</div>",
                'meta_o' => "<span class='nlposts-ulist-meta'>",
                'meta_c' => "</span>",
                'thumbnail_o' => "<ul class='nlposts-ulist-thumbnail thumbnails'>",
                'thumbnail_c' => "</ul>",
                'thumbnail_io' => "<li class='nlposts-ulist-thumbnail-litem span3'><div class='thumbnail'>",
                'thumbnail_ic' => "</div></li>",
                'pagination_o' => "<div class='nlposts-ulist-pagination pagination'>",
                'pagination_c' => "</div>",
                'title_o' => "<h3 class='nlposts-ulist-title'>",
                'title_c' => "</h3>",
                'excerpt_o' => "<ul class='nlposts-ulist-excerpt'><li>",
                'excerpt_c' => "</li></ul>"
            );
            break;
        // Ordered list
        case "olist":
            $html_tags = array(
                'wrapper_o' => "<ol class='nlposts-wrapper nlposts-olist $wrapper_list_css'>",
                'wrapper_c' => "</ol>",
                'wtitle_o' => "<h2 class='nlposts-olist-wtitle'>",
                'wtitle_c' => "</h2>",
                'item_o' => "<li class='nlposts-olist-litem'>",
                'item_c' => "</li>",
                'content_o' => "<div class='nlposts-container nlposts-olist-container $nlp_instance'>",
                'content_c' => "</div>",
                'meta_o' => "<span class='nlposts-olist-meta'>",
                'meta_c' => "</span>",
                'thumbnail_o' => "<ul class='nlposts-olist-thumbnail thumbnails'>",
                'thumbnail_c' => "</ul>",
                'thumbnail_io' => "<li class='nlposts-olist-thumbnail-litem span3'>",
                'thumbnail_ic' => "</li>",
                'pagination_o' => "<div class='nlposts-olist-pagination pagination'>",
                'pagination_c' => "</div>",
                'title_o' => "<h3 class='nlposts-olist-title'>",
                'title_c' => "</h3>",
                'excerpt_o' => "<ul class='nlposts-olist-excerpt'><li>",
                'excerpt_c' => "</li></ul>"
            );
            break;
        // Block
        case "block":
            $html_tags = array(
                'wrapper_o' => "<div class='nlposts-wrapper nlposts-block $wrapper_block_css'>",
                'wrapper_c' => "</div>",
                'wtitle_o' => "<h2 class='nlposts-block-wtitle'>",
                'wtitle_c' => "</h2>",
                'item_o' => "<div class='nlposts-block-item'>",
                'item_c' => "</div>",
                'content_o' => "<div class='nlposts-container nlposts-block-container $nlp_instance'>",
                'content_c' => "</div>",
                'meta_o' => "<span class='nlposts-block-meta'>",
                'meta_c' => "</span>",
                'thumbnail_o' => "<ul class='nlposts-block-thumbnail thumbnails'>",
                'thumbnail_c' => "</ul>",
                'thumbnail_io' => "<li class='nlposts-block-thumbnail-litem span3'>",
                'thumbnail_ic' => "</li>",
                'pagination_o' => "<div class='nlposts-block-pagination pagination'>",
                'pagination_c' => "</div>",
                'title_o' => "<h3 class='nlposts-block-title'>",
                'title_c' => "</h3>",
                'excerpt_o' => "<div class='nlposts-block-excerpt'><p>",
                'excerpt_c' => "</p></div>"
            );
            break;
        default:
            // Unordered list
            $html_tags = array(
                'wrapper_o' => "<ul class='nlposts-wrapper nlposts-ulist $wrapper_list_css'>",
                'wrapper_c' => "</ul>",
                'wtitle_o' => "<h2 class='nlposts-ulist-wtitle'>",
                'wtitle_c' => "</h2>",
                'item_o' => "<li class='nlposts-ulist-litem'>",
                'item_c' => "</li>",
                'content_o' => "<div class='nlposts-container nlposts-ulist-container $nlp_instance'>",
                'content_c' => "</div>",
                'meta_o' => "<span class='nlposts-ulist-meta'>",
                'meta_c' => "</span>",
                'thumbnail_o' => "<ul class='nlposts-ulist-thumbnail thumbnails'>",
                'thumbnail_c' => "</ul>",
                'thumbnail_io' => "<li class='nlposts-ulist-thumbnail-litem span3'>",
                'thumbnail_ic' => "</li>",
                'pagination_o' => "<div class='nlposts-ulist-pagination pagination'>",
                'pagination_c' => "</div>",
                'title_o' => "<h3 class='nlposts-ulist-title'>",
                'title_c' => "</h3>",
                'excerpt_o' => "<ul class='nlposts-ulist-excerpt'><li>",
                'excerpt_c' => "</li></ul>"
            );
            break;
    }
    // Return tags
    return $html_tags;
}

/* Init function
 * Plugin initialization
 */

function network_latest_posts_init() {
    global $wp_locale;
    // Check for the required API functions
    if ( !function_exists('register_sidebar_widget') || !function_exists('register_widget_control') )
        return;
    // Register functions
    wp_register_sidebar_widget('nlposts-sb-widget',__("Network Latest Posts",'trans-nlp'),"network_latest_posts_widget");
    wp_register_widget_control('nlposts-control',__("Network Latest Posts",'trans-nlp'),"network_latest_posts_control");
    wp_register_style('nlpcss-form', plugins_url('/css/form_style.css', __FILE__));
    wp_enqueue_style('nlpcss-form');
    register_uninstall_hook(__FILE__, 'network_latest_posts_uninstall');
    // Load plugins
    wp_enqueue_script('jquery');
}
/* 
 * Load Languages
 */
function nlp_load_languages() {
    // Set the textdomain for translation purposes
    load_plugin_textdomain('trans-nlp', false, basename( dirname( __FILE__ ) ) . '/languages');
}
// Load CSS Styles
function nlp_load_styles($css_style) {
    if( !empty($css_style) ) {
        // Unload default style
        wp_deregister_style('nlpcss');
        // Load custom style
        wp_register_style('nlp-custom',$css_style);
        wp_enqueue_style('nlp-custom');
    } else {
        // Unload custom style
        wp_deregister_style('nlp-custom');
        // Load default style
        wp_register_style( 'nlpcss', plugins_url('/css/default_style.css', __FILE__) );
        wp_enqueue_style( 'nlpcss' );
    }
    return;
}

/* Load Widget
 * using create_function to support PHP versions < 5.3
 */
add_action( 'widgets_init', create_function( '', '
    /* Check RTL
     * This function cannot be called from the network_latest_posts_init function
     * due to a loading hierarchy issue, if used there it will not
     * recognize the is_rtl() WordPress function
     */
    if( is_rtl() ) {
        // Deregister the LTR style
        wp_deregister_style("nlpcss");
        // Register the RTL style
        wp_register_style( "nlpcss-rtl", plugins_url("/css/default_style-rtl.css", __FILE__) );
        // Load the style
        wp_enqueue_style( "nlpcss-rtl" );
        // Tell WordPress this plugin is switching to RTL mode
        global $wp_locale, $wp_styles;
        /* Set the text direction to RTL
         * This two variables will tell load-styles.php
         * load the Dashboard in RTL instead of LTR mode
         */
        $wp_locale->text_direction = "rtl";
        $wp_styles->text_direction = "rtl";
    }
    // Load the class
    return register_widget( "NLposts_Widget" );
' ) );


/* Uninstall function
 * Provides uninstall capabilities
 */
function network_latest_posts_uninstall() {
    // Delete widget options
    delete_option('widget_nlposts_widget');
    // Delete the shortcode hook
    remove_shortcode('nlposts');
}

/*
 * TinyMCE Shortcode Plugin
 * Add a NLPosts button to the TinyMCE editor
 * this will simplify the way it is used
 */
// TinyMCE button settings
function nlp_shortcode_button() {
    if ( current_user_can('edit_posts') && current_user_can('edit_pages') ) {
        add_filter('mce_external_plugins', 'nlp_shortcode_plugin');
        add_filter('mce_buttons', 'nlp_register_button');
    }
}
// Hook the button into the TinyMCE editor
function nlp_register_button($buttons) {
    array_push($buttons, "|" , "nlposts");
    return $buttons;
}
// Load the TinyMCE NLposts shortcode plugin
function nlp_shortcode_plugin($plugin_array) {
   $plugin_array['nlposts'] = plugin_dir_url(__FILE__) .'js/nlp_tinymce_button.js';
   return $plugin_array;
}
// Hook the shortcode button into TinyMCE
add_action('init', 'nlp_shortcode_button');
// Load styles
add_action('wp_head','nlp_load_styles',10,1);
// Run this stuff
add_action("admin_enqueue_scripts","network_latest_posts_init");
// Languages
add_action('plugins_loaded', 'nlp_load_languages');
?>