<?php
/*
    Network Latest Posts Widget
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
// Load main functionalities
include_once dirname( __FILE__ ) . '/network-latest-posts.php';
/* NLposts_Widget Class extending the WP_Widget class
 *
 * This beauty is used to create a multi-instance widget
 * to be used in any widgetized zone of the themes
 */
class NLposts_Widget extends WP_Widget {

    // Default values
    private $defaults = array(
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
        'display_content'  => FALSE,         // Display post content instead of excerpt
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
        'random'           => FALSE,         // Pull random posts (true or false)
        'post_ignore'      => NULL           // Post ID(s) to ignore
    );

    /*
	* Register widget with WordPress
     *
	*/
    public function __construct() {
        parent::__construct(
            'nlposts_widget', // Base ID
            'Network Latest Posts', // Name
            array( 'description' => __( 'Network Latest Posts Widget', 'trans-nlp' ), ) // Args
        );
    }


    /*
     * Front-end display of widget
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments
     * @param array $instance Saved values from database
     */
    public function widget( $args, $instance ) {
        // Process blog_id
        if( !empty($instance['blog_id']) ) {
            // Check it's an array
            if( is_array($instance['blog_id']) ) {
                // If it's null
                if( in_array('null', $instance['blog_id']) ) {
                    // Set it to a real NULL
                    $instance['blog_id'] = NULL;
                // If it's not 'null' then convert to value1,..,valueN
                } else {
                    // Scape the string (trying to minimize injection risks
                    $instance['blog_id'] = implode(',', array_map('mysql_real_escape_string', $instance['blog_id']));
                }
             // If it isn't an array
             } else {
                 // Check if it's null
                 if( $instance['blog_id'] == 'null' ) {
                     // Set it to a real NULL
                     $instance['blog_id'] = NULL;
                 }
             }
        }
        // Process ignore_blog
        if( !empty($instance['ignore_blog']) ) {
            // Check it's an array
            if( is_array($instance['ignore_blog']) ) {
                // If it's null
                if( in_array('null', $instance['ignore_blog']) ) {
                    // Set it to a real NULL
                    $instance['ignore_blog'] = NULL;
                // If it's not 'null' then convert to value1,..,valueN
                } else {
                    // Scape the string (trying to minimize injection risks
                    $instance['ignore_blog'] = implode(',', array_map('mysql_real_escape_string', $instance['ignore_blog']));
                }
             // If it isn't an array
             } else {
                 // Check if it's null
                 if( $instance['ignore_blog'] == 'null' ) {
                     // Set it to a real NULL
                     $instance['ignore_blog'] = NULL;
                 }
             }
        }
        // Duplicate the instances
        $options = $instance;
        if( empty($options['thumbnail_wh']) ) {
            // Set the thumbnail_wh variable putting together width and height
            $options['thumbnail_wh'] = (int)$options['thumbnail_w'].'x'.(int)$options['thumbnail_h'];
        }
        // If we couldn't find anything, set some default values
        if ( is_array( $options ) ) {
            // Parse & merge parameters with the defaults
            $options = wp_parse_args( $options, $this->defaults );
        }
        // Set the instance identifier, so each instance of the widget is treated individually
        $options['instance'] = $this->number;
        // If there are passed arguments, transform them into variables
        if( !empty($args) ) { extract( $args ); }
        // Display the widget
        // Start the output buffer to control the display position
        ob_start();
        // Get the posts
        network_latest_posts($options);
        // Open the aside tag (widget placeholder)
        $output_string = "<aside class='widget nlposts-widget'>";
        // Grab the content
        $output_string .= ob_get_contents();
        // Close the aside tag
        $output_string .= "</aside>";
        // Clean the output buffer
        ob_end_clean();
        // Put the content where we want
        echo $output_string;
    }

    /*
     * Sanitize widget form values as they are saved
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        // Set an array
        $instance = array();
        // Get the values
        $instance['title']            = strip_tags($new_instance['title']);
        $instance['number_posts']     = intval($new_instance['number_posts']);
        $instance['time_frame']       = intval($new_instance['time_frame']);
        $instance['title_only']       = strip_tags($new_instance['title_only']);
        $instance['display_type']     = strip_tags($new_instance['display_type']);
        $instance['blog_id']          = $new_instance['blog_id'];
        $instance['ignore_blog']      = $new_instance['ignore_blog'];
        $instance['thumbnail']        = strip_tags($new_instance['thumbnail']);
        $instance['thumbnail_w']      = (int)$new_instance['thumbnail_w'];
        $instance['thumbnail_h']      = (int)$new_instance['thumbnail_h'];
        $instance['thumbnail_class']  = strip_tags($new_instance['thumbnail_class']);
        $instance['thumbnail_filler'] = strip_tags($new_instance['thumbnail_filler']);
        $instance['thumbnail_custom'] = strip_tags($new_instance['thumbnail_custom']);
        $instance['thumbnail_field']  = strip_tags($new_instance['thumbnail_field']);
        $instance['thumbnail_url']  = strip_tags($new_instance['thumbnail_url']);
        $instance['custom_post_type'] = strip_tags($new_instance['custom_post_type']);
        $instance['category']         = strip_tags($new_instance['category']);
        $instance['tag']              = strip_tags($new_instance['tag']);
        $instance['paginate']         = strip_tags($new_instance['paginate']);
        $instance['posts_per_page']   = (int)$new_instance['posts_per_page'];
        $instance['display_content']  = strip_tags($new_instance['display_content']);
        $instance['excerpt_length']   = (int)$new_instance['excerpt_length'];
        $instance['auto_excerpt']     = strip_tags($new_instance['auto_excerpt']);
        $instance['full_meta']        = strip_tags($new_instance['full_meta']);
        $instance['sort_by_date']     = strip_tags($new_instance['sort_by_date']);
        $instance['sort_by_blog']     = strip_tags($new_instance['sort_by_blog']);
        $instance['sorting_order']    = strip_tags($new_instance['sorting_order']);
        $instance['sorting_limit']    = (int)$new_instance['sorting_limit'];
        $instance['post_status']      = strip_tags($new_instance['post_status']);
        $instance['excerpt_trail']    = strip_tags($new_instance['excerpt_trail']);
        $instance['css_style']        = strip_tags($new_instance['css_style']);
        $instance['wrapper_list_css'] = strip_tags($new_instance['wrapper_list_css']);
        $instance['wrapper_block_css']= strip_tags($new_instance['wrapper_block_css']);
        $instance['random']           = strip_tags($new_instance['random']);
        $instance['post_ignore']      = strip_tags($new_instance['post_ignore']);
        // Width by default
        if( $instance['thumbnail_w'] == '0' ) { $instance['thumbnail_w'] = '80'; }
        // Height by default
        if( $instance['thumbnail_h'] == '0' ) { $instance['thumbnail_h'] = '80'; }
        // Return the sanitized values
        return $instance;
    }

    /*
     * Back-end widget form
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {
        // Parse & Merge the passed values with the default ones
        $instance = wp_parse_args( $instance, $this->defaults );
        // Extract elements as variables
        extract( $instance, EXTR_SKIP );
        // Get blog ids
        global $wpdb;
        $blog_ids = $wpdb->get_results("SELECT blog_id FROM $wpdb->blogs WHERE
            public = '1' AND archived = '0' AND mature = '0' AND spam = '0' AND deleted = '0'
                ORDER BY last_updated DESC");
        // Basic HTML Tags
        $br = "<br />";
        $p_o = "<p>";
        $p_c = "<p>";
        // Form fields
        $widget_form = $p_o;
        // title
        $widget_form.= "<label for='".$this->get_field_id('title')."'>" . __('Title','trans-nlp') . "</label>";
        $widget_form.= $br;
        $widget_form.= "<input type='text' id='".$this->get_field_id('title')."' name='".$this->get_field_name('title')."' value='$title' />";
        $widget_form.= $br;
        // number_posts
        $widget_form.= "<label for='".$this->get_field_id('number_posts')."'>" . __('Number of Posts by Blog','trans-nlp') . "</label>";
        $widget_form.= $br;
        $widget_form.= "<input type='text' size='3' id='".$this->get_field_id('number_posts')."' name='".$this->get_field_name('number_posts')."' value='$number_posts' />";
        $widget_form.= $br;
        // post_ignore
        $widget_form.= "<label for='".$this->get_field_id('post_ignore')."'>" . __('Post ID(s) to Ignore','trans-nlp') . "</label>";
        $widget_form.= $br;
        $widget_form.= "<input type='text' id='".$this->get_field_id('post_ignore')."' name='".$this->get_field_name('post_ignore')."' value='$post_ignore' />";
        $widget_form.= $br;
        // time_frame
        $widget_form.= "<label for='".$this->get_field_id('time_frame')."'>" . __('Time Frame in Days','trans-nlp') . "</label>";
        $widget_form.= $br;
        $widget_form.= "<input type='text' size='3' id='".$this->get_field_id('time_frame')."' name='".$this->get_field_name('time_frame')."' value='$time_frame' />";
        $widget_form.= $br;
        // title_only
        $widget_form.= "<label for='".$this->get_field_id('title_only')."'>" . __('Titles Only','trans-nlp') . "</label>";
        $widget_form.= $br;
        $widget_form.= "<select id='".$this->get_field_id('title_only')."' name='".$this->get_field_name('title_only')."'>";
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
        $widget_form.= "<label for='".$this->get_field_id('display_type')."'>" . __('Display Type','trans-nlp') . '</label>';
        $widget_form.= $br;
        $widget_form.= "<select id='".$this->get_field_id('display_type')."' name='".$this->get_field_name('display_type')."'>";
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
            $widget_form.= "<label for='".$this->get_field_id('blog_id')."'>" . __('Display Blog','trans-nlp') . " " . __('or','trans-nlp') . " " . __('Blogs','trans-nlp') . "</label>";
        } else {
            $widget_form.= "<label for='".$this->get_field_id('blog_id')."'>" . __('Display Blog(s)','trans-nlp') . "</label>";
        }
        $widget_form.= $br;
        $widget_form.= "<select id='".$this->get_field_id('blog_id')."' name='".$this->get_field_name('blog_id')."[]' multiple='multiple'>";
        // Get the blog_id string
        if( !is_array($blog_id) ) {
            // Check if there are multiple values
            if( preg_match('/,/',$blog_id) ) {
                // Convert to array
                $blog_id = explode(',',$blog_id);
            } else {
                // Single value
                if( empty($blog_id) ) {
                    // Set array null
                    $blog_id = array('null');
                } else {
                    // Set array with the single value
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
            $widget_form.= "<label for='".$this->get_field_id('ignore_blog')."'>" . __('Ignore Blog','trans-nlp') . " " . __('or','trans-nlp') . " " . __('Blogs','trans-nlp') . "</label>";
        } else {
            $widget_form.= "<label for='".$this->get_field_id('ignore_blog')."'>" . __('Ignore Blog(s)','trans-nlp') . "</label>";
        }
        $widget_form.= $br;
        $widget_form.= "<select id='".$this->get_field_id('ignore_blog')."' name='".$this->get_field_name('ignore_blog')."[]' multiple='multiple'>";
        // Get the ignore_blog string
        if( !is_array($ignore_blog) ) {
            // Check if there are multiple values
            if( preg_match('/,/',$ignore_blog) ) {
                // Convert to array
                $ignore_blog = explode(',',$ignore_blog);
            } else {
                // Single value
                if( empty($ignore_blog) ) {
                    // Set array null
                    $ignore_blog = array('null');
                } else {
                    // Set array with single value
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
        $widget_form.= "<label for='".$this->get_field_id('thumbnail')."'>" . __('Display Thumbnails','trans-nlp') . "</label>";
        $widget_form.= $br;
        $widget_form.= "<select id='".$this->get_field_id('thumbnail')."' name='".$this->get_field_name('thumbnail')."'>";
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
        $widget_form.= "<label for='".$this->get_field_id('thumbnail_w')."'>" . __('Width','trans-nlp') . "</label>";
        $widget_form.= $br;
        $widget_form.= "<input type='text' size='3' id='".$this->get_field_id('thumbnail_w')."' name='".$this->get_field_name('thumbnail_w')."' value='$thumbnail_w' />";
        $widget_form.= $br;
        $widget_form.= "<label for='".$this->get_field_id('thumbnail_h')."'>" . __('Height','trans-nlp') . "</label>";
        $widget_form.= $br;
        $widget_form.= "<input type='text' size='3' id='".$this->get_field_id('thumbnail_h')."' name='".$this->get_field_name('thumbnail_h')."' value='$thumbnail_h' />";
        $widget_form.= "</fieldset>";
        // thumbnail_filler
        $widget_form.= "<label for='".$this->get_field_id('thumbnail_filler')."'>" . __('Thumbnail Replacement','trans-nlp') . '</label>';
        $widget_form.= $br;
        $widget_form.= "<select id='".$this->get_field_id('thumbnail_filler')."' name='".$this->get_field_name('thumbnail_filler')."'>";
        switch( $thumbnail_filler ) {
            // Placeholder
            case 'placeholder':
                $widget_form.= "<option value='placeholder' selected='selected'>" . __('Placeholder','trans-nlp') . "</option>";
                $widget_form.= "<option value='kittens'>" . __('Kittens','trans-nlp') . "</option>";
                $widget_form.= "<option value='puppies'>" . __('Puppies','trans-nlp') . "</option>";
                $widget_form.= "<option value='custom'>" . __('Custom', 'trans-nlp') . "</option>";
                break;
            // Kittens
            case 'kittens':
                $widget_form.= "<option value='placeholder'>" . __('Placeholder','trans-nlp') . "</option>";
                $widget_form.= "<option value='kittens' selected='selected'>" . __('Kittens','trans-nlp') . "</option>";
                $widget_form.= "<option value='puppies'>" . __('Puppies','trans-nlp') . "</option>";
                $widget_form.= "<option value='custom'>" . __('Custom', 'trans-nlp') . "</option>";
                break;
            // Puppies
            case 'puppies':
                $widget_form.= "<option value='placeholder'>" . __('Placeholder','trans-nlp') . "</option>";
                $widget_form.= "<option value='kittens'>" . __('Kittens','trans-nlp') . "</option>";
                $widget_form.= "<option value='puppies' selected='selected'>" . __('Puppies','trans-nlp') . "</option>";
                $widget_form.= "<option value='custom'>" . __('Custom', 'trans-nlp') . "</option>";
                break;
            // Custom
            case 'custom':
                $widget_form.= "<option value='placeholder'>" . __('Placeholder','trans-nlp') . "</option>";
                $widget_form.= "<option value='kittens'>" . __('Kittens','trans-nlp') . "</option>";
                $widget_form.= "<option value='puppies'>" . __('Puppies','trans-nlp') . "</option>";
                $widget_form.= "<option value='custom' selected='selected'>" . __('Custom', 'trans-nlp') . "</option>";
                break;
            // Boring by default ;)
            default:
                $widget_form.= "<option value='placeholder' selected='selected'>" . __('Placeholder','trans-nlp') . "</option>";
                $widget_form.= "<option value='kittens'>" . __('Kittens','trans-nlp') . "</option>";
                $widget_form.= "<option value='puppies'>" . __('Puppies','trans-nlp') . "</option>";
                $widget_form.= "<option value='custom'>" . __('Custom', 'trans-nlp') . "</option>";
                break;
        }
        $widget_form.= "</select>";
        // Custom Thumbnail URL
        $widget_form.= $br;
        $widget_form.= "<label for='".$this->get_field_id('thumbnail_url')."'>" . __('Custom Thumbnail URL','trans-nlp') . "</label>";
        $widget_form.= $br;
        $widget_form.= "<input type='text' id='".$this->get_field_id('thumbnail_url')."' name='".$this->get_field_name('thumbnail_url')."' value='$thumbnail_url' />";
        // thumbnail_class
        $widget_form.= $br;
        $widget_form.= "<label for='".$this->get_field_id('thumbnail_class')."'>" . __('Thumbnail Class','trans-nlp') . "</label>";
        $widget_form.= $br;
        $widget_form.= "<input type='text' id='".$this->get_field_id('thumbnail_class')."' name='".$this->get_field_name('thumbnail_class')."' value='$thumbnail_class' />";
        // thumbnail_custom
        $widget_form.= $br;
        $widget_form.= "<label for='".$this->get_field_id('thumbnail_custom')."'>" . __('Custom Thumbnail','trans-nlp') . "</label>";
        $widget_form.= $br;
        $widget_form.= "<select id='".$this->get_field_id('thumbnail_custom')."' name='".$this->get_field_name('thumbnail_custom')."'>";
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
        $widget_form.= "<label for='".$this->get_field_id('thumbnail_field')."'>" . __('Thumbnail Custom Field','trans-nlp') . "</label>";
        $widget_form.= $br;
        $widget_form.= "<input type='text' id='".$this->get_field_id('thumbnail_field')."' name='".$this->get_field_name('thumbnail_field')."' value='$thumbnail_field' />";
        // custom_post_type
        $widget_form.= $br;
        $widget_form.= "<label for='".$this->get_field_id('custom_post_type')."'>" . __('Custom Post Type','trans-nlp') . "</label>";
        $widget_form.= $br;
        $widget_form.= "<input type='text' id='".$this->get_field_id('custom_post_type')."' name='".$this->get_field_name('custom_post_type')."' value='$custom_post_type' />";
        // category
        $widget_form.= $br;
        if( is_rtl() ) {
            $widget_form.= "<label for='".$this->get_field_id('category')."'>" . __('Category','trans-nlp')  . " " . __('or','trans-nlp') . " " . __('Categories','trans-nlp') . "</label>";
        } else {
            $widget_form.= "<label for='".$this->get_field_id('category')."'>" . __('Category(ies)','trans-nlp') . "</label>";
        }
        $widget_form.= $br;
        $widget_form.= "<input type='text' id='".$this->get_field_id('category')."' name='".$this->get_field_name('category')."' value='$category' />";
        // tag
        $widget_form.= $br;
        if( is_rtl() ) {
            $widget_form.= "<label for='".$this->get_field_id('tag')."'>" . __('Tag','trans-nlp') . " " . __('or','trans-nlp') . " " . __('Tags','trans-nlp') . "</label>";
        } else {
            $widget_form.= "<label for='".$this->get_field_id('tag')."'>" . __('Tag(s)','trans-nlp') . "</label>";
        }
        $widget_form.= $br;
        $widget_form.= "<input type='text' id='".$this->get_field_id('tag')."' name='".$this->get_field_name('tag')."' value='$tag' />";
        // paginate
        $widget_form.= $br;
        $widget_form.= "<label for='".$this->get_field_id('paginate')."'>" . __('Paginate Results','trans-nlp') . "</label>";
        $widget_form.= $br;
        $widget_form.= "<select id='".$this->get_field_id('paginate')."' name='".$this->get_field_name('paginate')."'>";
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
        $widget_form.= "<label for='".$this->get_field_id('posts_per_page')."'>" . __('Posts per Page','trans-nlp') . "</label>";
        $widget_form.= $br;
        $widget_form.= "<input type='text' id='".$this->get_field_id('posts_per_page')."' name='".$this->get_field_name('posts_per_page')."' value='$posts_per_page' />";
        // display_content
        $widget_form.= $br;
        $widget_form.= "<label for='".$this->get_field_id('display_content')."'>" . __('Display Content','trans-nlp') . "</label>";
        $widget_form.= $br;
        $widget_form.= "<select id='".$this->get_field_id('display_content')."' name='".$this->get_field_name('display_content')."'>";
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
        $widget_form.= "<label for='".$this->get_field_id('excerpt_length')."'>" . __('Excerpt Length','trans-nlp') . "</label>";
        $widget_form.= $br;
        $widget_form.= "<input type='text' id='".$this->get_field_id('excerpt_length')."' name='".$this->get_field_name('excerpt_length')."' value='$excerpt_length' />";
        // auto_excerpt
        $widget_form.= $br;
        $widget_form.= "<label for='".$this->get_field_id('auto_excerpt')."'>" . __('Auto-Excerpt','trans-nlp') . "</label>";
        $widget_form.= $br;
        $widget_form.= "<select id='".$this->get_field_id('auto_excerpt')."' name='".$this->get_field_name('auto_excerpt')."'>";
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
        $widget_form.= "<label for='".$this->get_field_id('excerpt_trail')."'>" . __('Excerpt Trail','trans-nlp') . "</label>";
        $widget_form.= $br;
        $widget_form.= "<select id='".$this->get_field_id('excerpt_trail')."' name='".$this->get_field_name('excerpt_trail')."'>";
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
        $widget_form.= "<label for='".$this->get_field_id('sort_by_date')."'>" . __('Sort by Date','trans-nlp') . "</label>";
        $widget_form.= $br;
        $widget_form.= "<select id='".$this->get_field_id('sort_by_date')."' name='".$this->get_field_name('sort_by_date')."'>";
        if( $sort_by_date == 'true' ) {
            $widget_form.= "<option value='true' selected='selected'>" . __('Yes','trans-nlp') . "</option>";
            $widget_form.= "<option value='false'>" . __('No','trans-nlp') . "</option>";
        } else {
            $widget_form.= "<option value='true'>" . __('Yes','trans-nlp') . "</option>";
            $widget_form.= "<option value='false' selected='selected'>" . __('No','trans-nlp') . "</option>";
        }
        $widget_form.= "</select>";
        // sort_by_blog
        $widget_form.= $br;
        $widget_form.= "<label for='".$this->get_field_id('sort_by_blog')."'>" . __('Sort by Blog ID','trans-nlp') . "</label>";
        $widget_form.= $br;
        $widget_form.= "<select id='".$this->get_field_id('sort_by_blog')."' name='".$this->get_field_name('sort_by_blog')."'>";
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
        $widget_form.= "<label for='".$this->get_field_id('sorting_order')."'>" . __('Sorting Order','trans-nlp') . "</label>";
        $widget_form.= $br;
        $widget_form.= "<select id='".$this->get_field_id('sorting_order')."' name='".$this->get_field_name('sorting_order')."'>";
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
            $widget_form.= "<option value='older'>" . __('Oldest to Newest','trans-nlp') . "</option>";
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
            $widget_form.= "<option value='desc'>" . __('Descendant','trans-nlp') . "</option>";
        }
        $widget_form.= "</select>";
        // sorting_limit
        $widget_form.= $br;
        $widget_form.= "<label for='".$this->get_field_id('sorting_limit')."'>" . __('Total Number of Posts','trans-nlp') . "</label>";
        $widget_form.= $br;
        $widget_form.= "<input type='text' id='".$this->get_field_id('sorting_limit')."' name='".$this->get_field_name('sorting_limit')."' value='$sorting_limit' />";
        // post_status
        $widget_form.= $br;
        $widget_form.= "<label for='".$this->get_field_id('post_status')."'>" . __('Post Status','trans-nlp') . "</label>";
        $widget_form.= $br;
        $widget_form.= "<input type='text' id='".$this->get_field_id('post_status')."' name='".$this->get_field_name('post_status')."' value='$post_status' />";
        // full_meta
        $widget_form.= $br;
        $widget_form.= "<label for='".$this->get_field_id('full_meta')."'>" . __('Full Metadata','trans-nlp') . "</label>";
        $widget_form.= $br;
        $widget_form.= "<select id='".$this->get_field_id('full_meta')."' name='".$this->get_field_name('full_meta')."'>";
        if( $full_meta == 'true' ) {
            $widget_form.= "<option value='true' selected='selected'>" . __('Yes','trans-nlp') . "</option>";
            $widget_form.= "<option value='false'>" . __('No','trans-nlp') . "</option>";
        } else {
            $widget_form.= "<option value='true'>" . __('Yes','trans-nlp') . "</option>";
            $widget_form.= "<option value='false' selected='selected'>" . __('No','trans-nlp') . "</option>";
        }
        $widget_form.= "</select>";
        $widget_form.= $br;
        // Pull random posts
        $widget_form.= "<label for='".$this->get_field_id('random')."'>". __('Random Posts','trans-nlp') . "</label>";
        $widget_form.= $br;
        $widget_form.= "<select id='".$this->get_field_id('random')."' name='".$this->get_field_name('random')."'>";
        if( $random == 'true' ) {
            $widget_form.="<option value='true' selected='selected'>Yes</option>";
            $widget_form.="<option value='false'>No</option>";
        } else {
            $widget_form.="<option value='true'>Yes</option>";
            $widget_form.="<option value='false' selected='selected'>No</option>";
        }
        $widget_form.= "</select>";
        // css_style
        $widget_form.= $br;
        $widget_form.= "<label for='".$this->get_field_id('css_style')."'>" . __('Custom CSS Filename','trans-nlp') . "</label>";
        $widget_form.= $br;
        $widget_form.= "<input type='text' id='".$this->get_field_id('css_style')."' name='".$this->get_field_name('css_style')."' value='$css_style' />";
        // wrapper_list_css
        $widget_form.= $br;
        $widget_form.= "<label for='".$this->get_field_id('wrapper_list_css')."'>" . __('Custom CSS Class for the list wrapper','trans-nlp') . "</label>";
        $widget_form.= $br;
        $widget_form.= "<input type='text' id='".$this->get_field_id('wrapper_list_css')."' name='".$this->get_field_name('wrapper_list_css')."' value='$wrapper_list_css' />";
        // wrapper_block_css
        $widget_form.= $br;
        $widget_form.= "<label for='".$this->get_field_id('wrapper_block_css')."'>" . __('Custom CSS Class for the block wrapper','trans-nlp') . "</label>";
        $widget_form.= $br;
        $widget_form.= "<input type='text' id='".$this->get_field_id('wrapper_block_css')."' name='".$this->get_field_name('wrapper_block_css')."' value='$wrapper_block_css' />";
        $widget_form.= $p_c;
        echo $widget_form;
    }

}
?>