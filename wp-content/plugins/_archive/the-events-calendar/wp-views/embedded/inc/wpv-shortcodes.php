<?php

$wpv_shortcodes = array();

$wpv_shortcodes['wpv-post-id'] = array('wpv-post-id', __('ID', 'wpv-views'), 'wpv_shortcode_wpv_post_id');
$wpv_shortcodes['wpv-post-title'] = array('wpv-post-title', __('Title', 'wpv-views'), 'wpv_shortcode_wpv_post_title');
$wpv_shortcodes['wpv-post-link'] = array('wpv-post-link', __('Title with a link', 'wpv-views'), 'wpv_shortcode_wpv_post_link');
$wpv_shortcodes['wpv-post-body'] = array('wpv-post-body', __('Body', 'wpv-views'), 'wpv_shortcode_wpv_post_body');
$wpv_shortcodes['wpv-post-excerpt'] = array('wpv-post-excerpt', __('Excerpt', 'wpv-views'), 'wpv_shortcode_wpv_post_excerpt');
$wpv_shortcodes['wpv-post-author'] = array('wpv-post-author', __('Author', 'wpv-views'), 'wpv_shortcode_wpv_post_author');
$wpv_shortcodes['wpv-post-date'] = array('wpv-post-date', __('Date', 'wpv-views'), 'wpv_shortcode_wpv_post_date');
$wpv_shortcodes['wpv-post-url'] = array('wpv-post-url', __('URL', 'wpv-views'), 'wpv_shortcode_wpv_post_url');
$wpv_shortcodes['wpv-post-featured-image'] = array('wpv-post-featured-image', __('Featured image', 'wpv-views'), 'wpv_shortcode_wpv_post_featured_image');
$wpv_shortcodes['wpv-post-comments-number'] = array('wpv-post-comments-number', __('Comments number', 'wpv-views'), 'wpv_shortcode_wpv_comments_number');
$wpv_shortcodes['wpv-post-edit-link'] = array('wpv-post-edit-link', __('Edit Link', 'wpv-views'), 'wpv_shortcode_wpv_post_edit_link');
$wpv_shortcodes['wpv-post-type'] = array('wpv-post-type', __('Post type', 'wpv-views'), 'wpv_shortcode_wpv_post_type');

// NOTE:  Put all "post" shortcodes before 'wpv-post-field' so they appear in the right order in various popups.
$wpv_shortcodes['wpv-post-field'] = array('wpv-post-field', __('Field', 'wpv-views'), 'wpv_shortcode_wpv_post_field');


$wpv_shortcodes['wpv-comment-title'] = array('wpv-comment-title', __('Comment title', 'wpv-views'), 'wpv_shortcode_wpv_comment_title');
$wpv_shortcodes['wpv-comment-body'] = array('wpv-comment-body', __('Comment body', 'wpv-views'), 'wpv_shortcode_wpv_comment_body');
$wpv_shortcodes['wpv-comment-author'] = array('wpv-comment-author', __('Comment Author', 'wpv-views'), 'wpv_shortcode_wpv_comment_author');
$wpv_shortcodes['wpv-comment-date'] = array('wpv-comment-date', __('Comment Date', 'wpv-views'), 'wpv_shortcode_wpv_comment_date');

$wpv_shortcodes['wpv-taxonomy-title'] = array('wpv-taxonomy-title', __('Taxonomy title', 'wpv-views'), 'wpv_shortcode_wpv_tax_title');
$wpv_shortcodes['wpv-taxonomy-link'] = array('wpv-taxonomy-link', __('Taxonomy title with a link', 'wpv-views'), 'wpv_shortcode_wpv_tax_title_link');
$wpv_shortcodes['wpv-taxonomy-url'] = array('wpv-taxonomy-url', __('Taxonomy URL', 'wpv-views'), 'wpv_shortcode_wpv_tax_url');
$wpv_shortcodes['wpv-taxonomy-slug'] = array('wpv-taxonomy-slug', __('Taxonomy slug', 'wpv-views'), 'wpv_shortcode_wpv_tax_slug');
$wpv_shortcodes['wpv-taxonomy-description'] = array('wpv-taxonomy-description', __('Taxonomy description', 'wpv-views'), 'wpv_shortcode_wpv_tax_description');
$wpv_shortcodes['wpv-taxonomy-post-count'] = array('wpv-taxonomy-post-count', __('Taxonomy post count', 'wpv-views'), 'wpv_shortcode_wpv_tax_items_count');

// $wpv_shortcodes['wpv-control'] = array('wpv-control', __('Filter control', 'wpv-views'), 'wpv_shortcode_wpv_control');


// register the short codes
foreach ($wpv_shortcodes as $shortcode) {
    if (function_exists($shortcode[2])) {
        add_shortcode($shortcode[0], $shortcode[2]);
    }
}

// Init taxonomies shortcode
wpv_post_taxonomies_shortcode();

/*
  Get the short code via name
*/

function wpv_get_shortcode($name) {
    global $wpv_shortcodes;
    
    foreach ($wpv_shortcodes as $shortcode) {
        if ($shortcode[1] == $name) {
            return $shortcode[0];
        }
    }
    
    if ($name == 'Taxonomy View') {
        return 'wpv-view';
    }
    
    return null;
}


/**
 * Views-Shortcode: wpv-post-id
 *
 * Description: Display the current post's ID
 *
 * Parameters:
 * This takes no parameters.
 *
 */

function wpv_shortcode_wpv_post_id($atts){
    $post_id_atts = new WPV_wpcf_switch_post_from_attr_id($atts);
    
    extract(
        shortcode_atts( array(), $atts )
    );
    $out = '';
    
    global $post;
        
    if(!empty($post)){
        $out .= $post->ID;
    }
    
    return $out;
}


/**
 * Views-Shortcode: wpv-post-title
 *
 * Description: Display the current post's title
 *
 * Parameters:
 * This takes no parameters.
 *
 */

function wpv_shortcode_wpv_post_title($atts){
    $post_id_atts = new WPV_wpcf_switch_post_from_attr_id($atts);

    extract(
        shortcode_atts( array(), $atts )
    );
    
    $out = '';
    
    global $post;
        
    if(!empty($post)){
        $out .= apply_filters('the_title', $post->post_title);
    }
    
    return $out;
}


/**
 * Views-Shortcode: wpv-post-link
 *
 * Description: Display the current post's title as a link to the post
 *
 * Parameters:
 * This takes no parameters.
 *
 */

function wpv_shortcode_wpv_post_link($atts){
    $post_id_atts = new WPV_wpcf_switch_post_from_attr_id($atts);

    extract(
        shortcode_atts( array(), $atts )
    );
    
    $out = '';
    
    global $post;
        
    if(!empty($post)){
        $out .= '<a href="' . get_permalink() . '">';
        $out .= apply_filters('the_title', $post->post_title);
        $out .= '</a>';
    }
    
    
    return $out;
}


/**
 * Views-Shortcode: wpv-post-body
 *
 * Description: Display the content of the current post
 *
 * Parameters:
 * 'view_template' => The name of a view template to use when displaying the post content.
 *
 */
function wpv_shortcode_wpv_post_body($atts){
    $post_id_atts = new WPV_wpcf_switch_post_from_attr_id($atts);

    extract(
        shortcode_atts( array(), $atts )
    );
    $old_override = null;
    $out = '';
    
    global $WPV_templates, $post;
    
    if (isset($atts['view_template'])) {
        if (isset($post->view_template_override) && $post->view_template_override != '') {
            $old_override = $post->view_template_override;
        }
        $post->view_template_override = $atts['view_template'];
    }
    
    if(!empty($post) && isset($post->post_type) && $post->post_type != 'view' && $post->post_type != 'view-template'){
        $wpautop_was_removed = $WPV_templates->is_wpautop_removed();
        if ($wpautop_was_removed) {
            $WPV_templates->restore_wpautop('');
        }
        
        // Remove the icl language switcher to stop WPML from add the
        // "This post is avaiable in XXXX" twice.
        global $icl_language_switcher;
        $icl_filter_removed = false;
        if(isset($icl_language_switcher)) {
            $icl_filter_removed = remove_filter('the_content', array($icl_language_switcher, 'post_availability'));
        }
        
        $out .= apply_filters('the_content', $post->post_content);
        
        if ($icl_filter_removed) {
            add_filter('the_content', array($icl_language_switcher, 'post_availability'));
        }
        
        if ($wpautop_was_removed) {
            $WPV_templates->remove_wpautop();
        }
    }
    
    if (isset($post->view_template_override)) {
        if ($old_override) {
            $post->view_template_override = $old_override;
        } else {
            unset($post->view_template_override);
        }
    }
    return $out;
}


/**
 * Views-Shortcode: wpv-post-excerpt
 *
 * Description: Display the excerpt of the current post
 *
 * Parameters:
 * length => the length of the excerpt. Default is 252 or the excerpt length defined by the theme.
 *
 */
function wpv_shortcode_wpv_post_excerpt($atts){
    $post_id_atts = new WPV_wpcf_switch_post_from_attr_id($atts);

    extract(
        shortcode_atts( array('length' => 0), $atts )
    );
    $out = '';
    
    global $WPV_templates, $post;
        
    if(!empty($post) && $post->post_type != 'view' && $post->post_type != 'view-template'){
        
    	// verify if displaying the real excerpt field or part of the content one
    	$display_real_excerpt = false;
        if ( empty($post->post_excerpt) ) {
            $excerpt = $post->post_content;
        } else {
            $excerpt = $post->post_excerpt;
            $display_real_excerpt = true;
        }
        $excerpt = str_replace(']]>', ']]&gt;', $excerpt);
        
        if ($length > 0) {
            $excerpt_length = $length;
        } else {
            $excerpt_length = apply_filters('excerpt_length', 252);
        }
		$excerpt_more = apply_filters('excerpt_more', ' ' . '...');
        
		if($display_real_excerpt) {
        	$excerpt_length = strlen($excerpt); // don't cut manually inserted excerpts
        	$excerpt_more = '';
        }
        
        // evaluate shortcodes before truncating tags
        $excerpt = wpv_do_shortcode($excerpt);
        $excerpt = wp_html_excerpt($excerpt, $excerpt_length) . $excerpt_more;
        
        $wpautop_was_removed = $WPV_templates->is_wpautop_removed();
        if ($wpautop_was_removed) {
            $WPV_templates->restore_wpautop('');
        }

        // Remove the View template excerpt filter. We don't want it applied to this shortcode
        remove_filter('the_excerpt', array($WPV_templates, 'the_excerpt_for_archives'), 1, 1);

        $out .= apply_filters('the_excerpt', $excerpt);

        // restore filter
        add_filter('the_excerpt', array($WPV_templates, 'the_excerpt_for_archives'), 1, 1);
        
        if ($wpautop_was_removed) {
            $WPV_templates->remove_wpautop();
        }
    }
    
    return $out;
}


/**
 * Views-Shortcode: wpv-post-author
 *
 * Description: Display the author of the current post
 *
 * Parameters:
 * format => The format of the output.
 *   "name" displays the author's name (Default)
 *   "link" displays the author's name as a link
 *   "url" displays the url for the author
 *   "meta" displays the author meta info
 * 
 * meta => The meta field to display when format="meta"
 *   see http://codex.wordpress.org/Function_Reference/get_the_author_meta
 *
 */

function wpv_shortcode_wpv_post_author($atts) {
    $post_id_atts = new WPV_wpcf_switch_post_from_attr_id($atts);

    extract(
        shortcode_atts( array('format' => 'name', 'meta' => 'nickname'), $atts )
    );

	global $authordata;
    
    $author_url = esc_url( get_author_posts_url( get_the_author_meta( 'ID' )));
    
    switch ($format) {
        case 'link':
            $out = '<a href="' . $author_url . '">' . get_the_author() . '</a>';
            break;
        
        case 'url':
            $out = $author_url;
            break;

        case 'meta':
            $out = get_the_author_meta($meta);
            break;
            
        default:        
            $out = get_the_author();
            break;
            
    }
    
    return $out;
}


/**
 * Views-Shortcode: wpv-post-date
 *
 * Description: Display the date of the current post
 *
 * Parameters:
 * format => Format string for the date. Defaults to F jS, Y
 * 
 * format parameter is the same as here - http://codex.wordpress.org/Formatting_Date_and_Time
 *
 */

function wpv_shortcode_wpv_post_date($atts) {
    $post_id_atts = new WPV_wpcf_switch_post_from_attr_id($atts);

    extract(
        shortcode_atts( array(
            'format' => 'F jS, Y'
        ), $atts )
    );

    $out = apply_filters('the_time', get_the_time( $format ));
    
    return $out;
}


/**
 * Views-Shortcode: wpv-post-url
 *
 * Description: Display the url to the current post
 *
 * Parameters:
 * This takes no parameters.
 *
 */

function wpv_shortcode_wpv_post_url($atts) {
    $post_id_atts = new WPV_wpcf_switch_post_from_attr_id($atts);

    extract(
        shortcode_atts( array(), $atts )
    );

    $out = get_permalink();
    
    return $out;
}

/**
 * Views-Shortcode: wpv-post-type
 *
 * Description: Display the current post type
 *
 * Parameters:
 * 'show' => 'slug', 'singular' or 'plural'. Defaults to 'slug'
 *
 */

function wpv_shortcode_wpv_post_type($atts){
    $post_id_atts = new WPV_wpcf_switch_post_from_attr_id($atts);

    extract(
        shortcode_atts( array('show' => 'slug'), $atts )
    );
    
    $out = '';
    
    global $post;
    
        
    if(!empty($post)){

    	$post_object = get_post_type_object($post->post_type);
        
        switch ($show) {
            case 'single':
                $out = $post_object->labels->singular_name;
                break;
            
            case 'plural':
                $out = $post_object->labels->name;
                break;
            
            default:
                $out = $post->post_type;
                break;
            
        }
        
    }
    
    return $out;
}



/**
 * Views-Shortcode: wpv-post-featured-image
 *
 * Description: Display the featured image of the current post
 *
 * Parameters:
 * 'size' => Image size - thumbnail, medium, large or full - defaults to thumbnail
 * 
 */

function wpv_shortcode_wpv_post_featured_image($atts) {
    $post_id_atts = new WPV_wpcf_switch_post_from_attr_id($atts);

    extract(
        shortcode_atts( array(
            'size'  => 'thumbnail',
            'attr'  => ''
        ), $atts )
    );

    $out = get_the_post_thumbnail( null, $size, $attr );
    $out = apply_filters('wpv-post-featured-image', $out);
    
    return $out;
}

/**
* Views-Shortcode: wpv-post-edit-link
*
* Description: Display an edit link for the current post
*
* Parameters:
* label: Optional. What to show in the edit link. ie: 'Edit Video'
*
* @param array $atts An associative array of arributes to be used.
*/
function wpv_shortcode_wpv_post_edit_link($atts){
    $post_id_atts = new WPV_wpcf_switch_post_from_attr_id($atts);

	extract(
		shortcode_atts( array(), $atts )
	);
	
	$out = '';
	global $post;
	
	if(!empty($post) && current_user_can('edit_posts')){
		$out .= '<a href="' . get_edit_post_link( $post->ID ) . '" class="post-edit-link">';
		$out .= (isset($atts['label']))? __('Edit ', 'wpv-views') .$atts['label'] : __('Edit This', 'wpv-views');
		$out .= '</a>';
	}
	return $out;
}




/**
 * Views-Shortcode: wpv-post-field
 *
 * Description: Display a custom field of the current post. This displays
 * the raw data from the field. Use the Types plugin the and the [types] shortcode
 * to display formatted fields.
 *
 * Parameters:
 * 'name' => The name of the custom field to display
 * 'index' => The array index to use if the meta key has multiple values. If index is not set then all values will be output
 * 'separator' => The separator between multiple values. Defaults to ', '
 *
 */

function wpv_shortcode_wpv_post_field($atts) {
    $post_id_atts = new WPV_wpcf_switch_post_from_attr_id($atts);

    extract(
        shortcode_atts( array('index' => '',
                              'name' => '',
                              'separator' => ', '), $atts )
    );
    
    $out = '';
    global $post;
    
    if(!empty($post)){
        $meta = get_post_meta($post->ID, $name);

        $meta = apply_filters('wpv-post-field-meta-' . $name, $meta);
        
        if ($meta) {
            
            // Allow wpv-for-each shortcode to set the index
            $index = apply_filters('wpv-for-each-index', $index);
            
            if ($index !== '') {
                $index = intval($index);
                $out .= $meta[$index];
            } else {
                foreach($meta as $item) {
                    if ($out != '') {
                        $out .= $separator;
                    }
                    $out .= $item;
                }
                
            }
        }
    }
    
    $out = apply_filters('wpv-post-field-' . $name, $out, $meta);
    
    return $out;
}

/**
 * Views-Shortcode: wpv-comments-number
 *
 * Description: Displays the number of comments for the current post
 *
 * Parameters:
 * 'none' => Text if there are no comments. Default - "No Comments"
 * 'one'  => Text if there is only one comment. Default - "1 Comment"
 * 'more' => Text if there is more than one comment. Default "% Comments"
 */

$wpv_comments_defaults = array('none' => __('No Comments', 'wpv-views'),
                              'one' => __('1 Comment', 'wpv-views'),
                              'more' => __('% Comments', 'wpv-views'));

function wpv_shortcode_wpv_comments_number($atts) {
    $post_id_atts = new WPV_wpcf_switch_post_from_attr_id($atts);

    global $wpv_comments_defaults, $post;
    
    extract(
        shortcode_atts( $wpv_comments_defaults, $atts )
    );
    
    ob_start();
    
    wp_count_comments($post->ID);
    comments_number($none, $one, $more);
    
    return ob_get_clean();
}

function wpv_shortcode_wpv_comment_title($atts){
    $post_id_atts = new WPV_wpcf_switch_post_from_attr_id($atts);

    
}

function wpv_shortcode_wpv_comment_body($atts){
    $post_id_atts = new WPV_wpcf_switch_post_from_attr_id($atts);

    
}

function wpv_shortcode_wpv_comment_author($atts){
    $post_id_atts = new WPV_wpcf_switch_post_from_attr_id($atts);

    
}

function wpv_shortcode_wpv_comment_date($atts){
    $post_id_atts = new WPV_wpcf_switch_post_from_attr_id($atts);

    
}

/**
 * Views-Shortcode: wpv-taxonomy-title
 *
 * Description: Display the taxonomy title as a plain text
 *
 * Parameters:
 * This takes no parameters.
 *
 */

function wpv_shortcode_wpv_tax_title($atts){
    
    global $WP_Views;
    
    $term = $WP_Views->get_current_taxonomy_term();
    
    if ($term) {
        return $term->name;
    } else {
        return '';
    }
    
}

/**
 * Views-Shortcode: wpv-taxonomy-link
 *
 * Description: Display the taxonomy title within a link
 *
 * Parameters:
 * This takes no parameters.
 *
 */


function wpv_shortcode_wpv_tax_title_link($atts){
    
    global $WP_Views;
    
    $term = $WP_Views->get_current_taxonomy_term();
    
    if ($term) {
        return '<a href="' . get_term_link($term) . '">' . $term->name . '</a>';
    } else {
        return '';
    }
    
}


/**
 * Views-Shortcode: wpv-taxonomy-slug
 *
 * Description: Display the taxonomy slug 
 *
 * Parameters:
 * This takes no parameters.
 *
 */
function wpv_shortcode_wpv_tax_slug($atts){

	global $WP_Views;

	$term = $WP_Views->get_current_taxonomy_term();

	if ($term) {
		return $term->slug;
	} else {
		return '';
	}

}

/**
 * Views-Shortcode: wpv-taxonomy-url
 *
 * Description: Display the taxonomy URL as a plain text (not embedded in a HTML link)
 *
 * Parameters:
 * This takes no parameters.
 *
 */

function wpv_shortcode_wpv_tax_url($atts){
    
    global $WP_Views;
    
    $term = $WP_Views->get_current_taxonomy_term();
    
    if ($term) {
        return get_term_link($term);
    } else {
        return '';
    }
    
}


/**
 * Views-Shortcode: wpv-taxonomy-description
 *
 * Description: Display the taxonomy description text
 *
 * Parameters:
 * This takes no parameters.
 *
 */

function wpv_shortcode_wpv_tax_description($atts){

    global $WP_Views;
    
    $term = $WP_Views->get_current_taxonomy_term();
    
    if ($term) {
        return $term->description;
    } else {
        return '';
    }
    
}


/**
 * Views-Shortcode: wpv-taxonomy-post-count
 *
 * Description: Display the number of posts in a taxonomy
 *
 * Parameters:
 * This takes no parameters.
 *
 */

function wpv_shortcode_wpv_tax_items_count($atts){
    global $WP_Views;
    
    $term = $WP_Views->get_current_taxonomy_term();
    
    if ($term) {
        return $term->count;
    } else {
        return '';
    }
    
}

//@todo - add function for the other shortcodes


/*
 
  Add the short codes to javascript so they can be added to the
  post visual editor toolbar.
  
  $types contains the type of items to add to the toolbar
  'post' add all wpv-post shortcodes
  'view' add available views
  
*/

function add_short_codes_to_js($types, $editor, $call_back = null){
    
    global $wpv_shortcodes, $wpdb, $WP_Views;

    $cf_keys = $WP_Views->get_meta_keys();
    
    // Find the field sub menus so we can group sub strings.
    $sub_fields = array();
    $last_field = '';
    foreach ($cf_keys as $field) {
        $parts = explode('_', str_replace('-', '_', $field));
        $start = $parts[0];
        if ($start == '') {
            // starts with an underscore.
            if (isset($parts[1])) {
                $start = $parts[1];
            }
        }
        
        if ($start == $last_field) {
            // found a duplicate
            
            if ($parts[0] == '') {
                $start = '_' . $start;
            }
            if (!in_array($start, $sub_fields)) {
                $sub_fields[] = $start;
            }
        } else {
            $last_field = $start;
        }
        
    }
    
    $index = 0;
    foreach($wpv_shortcodes as $shortcode) {
        
        if (in_array('post', $types) && strpos($shortcode[0], 'wpv-post-') === 0 && function_exists($shortcode[2])) {
            
            if ($shortcode[0] == 'wpv-post-field') {
                
                // we need to output the custom fields to a sub menu.

                // first we output the view templates.
                if (in_array('body-view-templates', $types)) {
                    // we need to add the available views.
                    $view_template_available = $wpdb->get_results("SELECT ID, post_name FROM {$wpdb->posts} WHERE post_type='view-template' AND post_status in ('publish')");
                    foreach($view_template_available as $view_template) {
                        if ($call_back) {
                            call_user_func($call_back, $index, $view_template->post_name, '', __('View template', 'wpv-views'), 'wpv-post-body view_template="' . $view_template->post_name . '"');
                        } else {
                            $editor->add_insert_shortcode_menu($view_template->post_name, 'wpv-post-body view_template="' . $view_template->post_name . '"', __('View template', 'wpv-views'));
                        }
            
                        $index += 1;
                    }
                    
                }
                
                foreach ($cf_keys as $cf_key) {
                    
                    if ($WP_Views->can_include_type($cf_key)) {
                        // add to the javascript array (text, function, sub-menu)
                        $function_name = 'wpv_field_' . $index;
                        $menu = $shortcode[1];
                        $parts = explode('_', str_replace('-', '_', $cf_key));
                        $start = $parts[0];
                        if ($start == '') {
                            // starts with an underscore.
                            if (isset($parts[1])) {
                                $start = '_' . $parts[1];
                            }
                        }
                        if (in_array($start, $sub_fields)) {
                            $menu .= '-!-' . $start;
                        }
                        
                        if ($call_back) {
                            call_user_func($call_back, $index, $cf_key, $function_name, $menu, $shortcode[0]);
                        } else {
                            $name = ' name="' . $cf_key . '"';
                            
                            $editor->add_insert_shortcode_menu($cf_key, $shortcode[0] . $name, $menu);
                            }
                        $index += 1;
                    }
                }
                
            } else {
                if ($call_back) {
                    call_user_func($call_back, $index, $shortcode[1], $shortcode[1], "", $shortcode[0]);
                } else {
                    
                    if ($shortcode[0] == 'wpv-post-body') {
                        $editor->add_insert_shortcode_menu($shortcode[1], $shortcode[0] . ' view_template="None"', __('Basic', 'wpv-views'));
                    } elseif ($shortcode[0] == 'wpv-post-comments-number') {
                        global $wpv_comments_defaults;
                        $editor->add_insert_shortcode_menu($shortcode[1], $shortcode[0] . ' none="' . $wpv_comments_defaults['none'] . '" one="' . $wpv_comments_defaults['one'] . '" more="' . $wpv_comments_defaults['more'] . '"', __('Basic', 'wpv-views'));
                    } else {
                        // JS callback
                        if (isset($shortcode[3])) {
                            $editor->add_insert_shortcode_menu($shortcode[1], $shortcode[0], __('Basic', 'wpv-views'), $shortcode[3]);
                        } else {
                            $editor->add_insert_shortcode_menu($shortcode[1], $shortcode[0], __('Basic', 'wpv-views'));
                        }
                    }
                }
                $index += 1;
            }
            
        }
        
        if($call_back == 'add-basics') {
        	if (isset($shortcode[3])) {
                      $editor->add_insert_shortcode_menu($shortcode[1], $shortcode[0], __('Basic', 'wpv-views'), $shortcode[3]);
                 } else {
                      $editor->add_insert_shortcode_menu($shortcode[1], $shortcode[0], __('Basic', 'wpv-views'));
                 }
        }
        
        if (in_array('taxonomy', $types) && strpos($shortcode[0], 'wpv-taxonomy-') === 0 && function_exists($shortcode[2])) {
            if ($call_back) {
                call_user_func($call_back, $index, $shortcode[1], $shortcode[1], "", $shortcode[0]);
            }
            $index += 1;
        }
        
    }
    
    
    if (in_array('view', $types)) {
        // we need to add the available views.
        $view_available = $wpdb->get_results("SELECT ID, post_title FROM {$wpdb->posts} WHERE post_type='view' AND post_status in ('publish')");
        foreach($view_available as $view) {

            if (!$WP_Views->is_archive_view($view->ID)) {
                $editor->add_insert_shortcode_menu($view->post_title, 'wpv-view name="' . $view->post_title . '"', __('View', 'wpv-views'));

                $index += 1;
            }
        }
    }
    
    if (in_array('view-form', $types)) {
        // we need to add the available views.
        $view_available = $wpdb->get_results("SELECT ID, post_title FROM {$wpdb->posts} WHERE post_type='view' AND post_status in ('publish')");
        foreach($view_available as $view) {
            
            if ($WP_Views->does_view_have_form_controls($view->ID) && !$WP_Views->is_archive_view($view->ID)) {
                $editor->add_insert_shortcode_menu($view->post_title,
                                                    'wpv-form-view name="' . $view->post_title . '"',
                                                    __('View Form', 'wpv-views'),
                                                    'wpv_insert_view_form_popup(' . $view->ID . ')');

                $index += 1;
                }
        }
    }
    
    if (in_array('taxonomy-view', $types)) {
        // we need to add the available Taxonomy views.
        $view_available = $wpdb->get_results("SELECT ID, post_title FROM {$wpdb->posts} WHERE post_type='view' AND post_status in ('publish')");
        foreach($view_available as $view) {

            $view_settings = get_post_meta($view->ID, '_wpv_settings', true);
			if (isset($view_settings['query_type'][0]) && $view_settings['query_type'][0] == 'taxonomy') {
            
                if ($call_back) {
                    call_user_func($call_back, $index, $view->post_title, '', __('Taxonomy View', 'wpv-views'), 'wpv-view name="' . $view->post_title . '"');
                }

                $index += 1;
            }
        }
    }
    
    if (in_array('post-view', $types)) {
        // we need to add the available Post views.
        $view_available = $wpdb->get_results("SELECT ID, post_title FROM {$wpdb->posts} WHERE post_type='view' AND post_status in ('publish')");
        foreach($view_available as $view) {

            $view_settings = get_post_meta($view->ID, '_wpv_settings', true);
			if (isset($view_settings['query_type'][0]) && $view_settings['query_type'][0] == 'posts') {
            
                if ($call_back) {
                    call_user_func($call_back, $index, $view->post_title, '', __('Post View', 'wpv-views'), 'wpv-view name="' . $view->post_title . '"');
                }

                $index += 1;
            }
        }
    }
    
    if (in_array('wpml', $types)) {
        global $sitepress;
        
        if (isset($sitepress)) {
        
            $editor->add_insert_shortcode_menu('WPML lang switcher', 'wpml-lang-switcher', 'WPML');
            $index += 1;
            
            global $icl_language_switcher;
            if (isset($icl_language_switcher)) {

                $editor->add_insert_shortcode_menu('WPML lang footer', 'wpml-lang-footer', 'WPML');
                $index += 1;
                
            }
            
            global $iclCMSNavigation;
            if (isset($iclCMSNavigation)) {
                
                //$editor->add_insert_shortcode_menu('WPML breadcrumbs', 'wpml-breadcrumbs', 'WPML');
                //$index += 1;

                $editor->add_insert_shortcode_menu('WPML sidebar', 'wpml-sidebar', 'WPML');
                $index += 1;
                
            }
        }
        
        if (defined('WPSEO_VERSION')) {
        
            $editor->add_insert_shortcode_menu('Yoast SEO breadcrumbs', 'yoast-breadcrumbs', 'Yoast SEO');
            $index += 1;
        }
    }
    
    
    
    return $index;
}
    
function wpv_post_taxonomies_shortcode() {
    add_shortcode('wpv-post-taxonomy', 'wpv_post_taxonomies_shortcode_render');
    add_filter('editor_addon_menus_wpv-views', 'wpv_post_taxonomies_editor_addon_menus_wpv_views_filter', 11);
}

/**
 * Views-Shortcode: wpv-post-taxonomy
 *
 * Description: Display the taxonomy for the current post. 
 *
 * Parameters:
 * 'type' => The name of the taxonomy to be displayed
 * 'separator' => Separator to use when there are multiple taxonomy terms for the post. The default is a comma.
 * 'format' => 'link', 'text' or 'url'. Defaults to 'link'
 * 'show' => 'name', 'description', 'slug' or 'count'. Defaults to 'name'
 *
 */


function wpv_post_taxonomies_shortcode_render($atts) {
    $post_id_atts = new WPV_wpcf_switch_post_from_attr_id($atts);

    extract(
        shortcode_atts( array('format' => 'link',
                              'show' => 'name'), $atts )
    );

    global $wplogger;
    
    $out = '';
    if (empty($atts['type'])) {
        return $out;
    }
    $types = explode(',', @strval($atts['type']));
    if (empty($types)) {
        return $out;
    }
    
    global $post;
    $separator = !empty($atts['separator']) ? @strval($atts['separator']) : ', ';
    $out_terms = array();
    foreach ($types as $taxonomy_slug) {
        $terms = get_the_terms($post->ID, $taxonomy_slug);
        if ( $terms && !is_wp_error( $terms )) {
            foreach ($terms as $term) {
                $text = $term->name;
                switch ($show) {
                    case 'description':
                        if ($term->description != '') {
                            $text = $term->description;
                        }
                        break;
                    
                    case 'count':
                        $text = $term->count;
                        break;
                    
                    case 'slug':
                        $text = $term->slug;
                        break;
                }

                $term_link = get_term_link($term->slug, $taxonomy_slug);
                if (is_wp_error($term_link)) {
                    $wplogger->log('Term invalid - term_slug = ' . $term->slug . ' - taxonomy_slug = ' . $taxonomy_slug, WPLOG_DEBUG);
                }
                
                if ($format == 'text') {
                    $out_terms[] = $text;
                } else if ($format == 'url') {
                    $out_terms[] = $term_link;
                } else {
                    $out_terms[] = '<a href="' . $term_link . '">' . $text . '</a>';
                }
            }
        }
    }
    if (!empty($out_terms)) {
        $out = implode($separator, $out_terms);
    }

    return $out;
}

function wpv_post_taxonomies_editor_addon_menus_wpv_views_filter($items) {
    $taxonomies = get_taxonomies('', 'objects');
    $add = array();
    foreach ($taxonomies as $taxonomy_slug => $taxonomy) {
        if ($taxonomy_slug == 'nav_menu' || $taxonomy_slug == 'link_category'
                || $taxonomy_slug == 'post_format') {
            continue;
        }
        $add[__('Taxonomy', 'wpv-views')][$taxonomy->label] = array($taxonomy->label, 'wpv-post-taxonomy type="' . $taxonomy_slug . '" separator=", " format="link" show="name"', __('Category', 'wpv-views'), '');
    }

    $part_one = array_slice($items, 0, 1);
    $part_two = array_slice($items, 1);
    $items = $part_one + $add + $part_two;
    return $items;
}

function wpv_do_shortcode($content) {

  $content = apply_filters('wpv-pre-do-shortcode', $content);
  
  // HACK HACK HACK
  // fix up a problem where shortcodes are not handled
  // correctly by WP when there a next to each other
  
  $content = str_replace('][', ']###SPACE###[', $content);
  $content = str_replace(']###SPACE###[/', '][/', $content);
  $content = do_shortcode($content);
  $content = str_replace('###SPACE###', '', $content);
  
  return $content;
}

add_shortcode('wpv-filter-order', 'wpv_filter_shortcode_order');
function wpv_filter_shortcode_order($atts){
    extract(
        shortcode_atts( array(), $atts )
    );
    
    global $WP_Views;
    $view_settings = $WP_Views->get_view_settings();
    
    $view_settings = wpv_filter_get_order_arg($view_settings, $view_settings);
    $order_selected = $view_settings['order'];
    
    $orders = array('DESC', 'ASC');
    return wpv_filter_show_user_interface('wpv_order', $orders, $order_selected, $atts['style']);
}

add_shortcode('wpv-filter-types-select', 'wpv_filter_shortcode_types');
function wpv_filter_shortcode_types($atts){
    extract(
        shortcode_atts( array(), $atts )
    );
    
    global $WP_Views;
    $view_settings = $WP_Views->get_view_settings();
    
    $view_settings = wpv_filter_get_post_types_arg($view_settings, $view_settings);
    $post_types_selected = $view_settings['post_type'];
    
    $post_types = get_post_types(array('public'=>true));
    return wpv_filter_show_user_interface('wpv_post_type', $post_types, $post_types_selected, $atts['style']);
}
    
/**
 * Add a shortcode for the search input from the user
 *
 */

add_shortcode('wpv-filter-search-box', 'wpv_filter_search_box');
function wpv_filter_search_box($atts){
    extract(
        shortcode_atts( array(), $atts )
    );

    global $WP_Views;
    $view_settings = $WP_Views->get_view_settings();

    if ($view_settings['query_type'][0] == 'posts') {
        if ($view_settings && isset($view_settings['post_search_value'])) {
            $value = 'value="' . $view_settings['post_search_value'] . '"';
        } else {
            $value = '';
        }
        if (isset($_GET['wpv_post_search'])) {
            $value = 'value="' . $_GET['wpv_post_search'] . '"';
        }
    
        return '<input type="text" name="wpv_post_search" ' . $value . '/>';
    }        

    if ($view_settings['query_type'][0] == 'taxonomy') {
        if ($view_settings && isset($view_settings['taxonomy_search_value'])) {
            $value = 'value="' . $view_settings['taxonomy_search_value'] . '"';
        } else {
            $value = '';
        }
        if (isset($_GET['wpv_taxonomy_search'])) {
            $value = 'value="' . $_GET['wpv_taxonomy_search'] . '"';
        }
    
        return '<input type="text" name="wpv_taxonomy_search" ' . $value . '/>';
    }        
}


$wpv_for_each_index = array(); // global for storing the current wpv-for-each index

/**
 * Views-Shortcode: wpv-for-each
 *
 * Description: Iterate through multple items in a post meta field and output the enclosed text for each item
 *
 * Parameters:
 * 'field' => The name of post meta field.
 *
 * Example usage:
 *
 * Output the field values as an ordered list
 * <ol>[wpv-for-each field="my-field"]<li>[wpv-post-field name="my-field]</li>[/wpv-for-each]<ol>
 *
 **/

add_shortcode('wpv-for-each', 'wpv_for_each_shortcode');
function wpv_for_each_shortcode($atts, $value){
    extract(
        shortcode_atts( array('field' => ''), $atts )
    );
    
    if ($field == '') {
        return wpv_do_shortcode($value);
    }
    
    $out = '';
    
    global $post, $wpv_for_each_index;
    
    if(!empty($post)){
        $meta = get_post_meta($post->ID, $field);
        
        if (!$meta) {
            return $value;
        }
        
        // iterate through the items and set the for-each index
        $wpv_for_each_index[] = 0;
        for($i = 0; $i < count($meta); $i++) {
        
            // set the for-each index and output
            $wpv_for_each_index[count($wpv_for_each_index) - 1] = $i;
            
            $out .= wpv_do_shortcode($value);        
        }
        
        array_pop($wpv_for_each_index);

    }
    
    return $out;

}

// set the for-each index
add_filter('wpv-for-each-index', 'wpv_for_each_index');
function wpv_for_each_index($index) {
    global $wpv_for_each_index;
    
    if (count($wpv_for_each_index) > 0) {
        return end($wpv_for_each_index);
    }
    
    return $index;
}


// WPML shortcodes to add to Views.

add_shortcode('wpml-lang-switcher', 'wpv_wpml_lang_switcher');
function wpv_wpml_lang_switcher($atts, $value){
    ob_start();
    
    do_action('icl_language_selector');
    
    $result = ob_get_clean();
    
    return $result;
}

add_shortcode('wpml-lang-footer', 'wpv_wpml_lang_footer');
function wpv_wpml_lang_footer($atts, $value){
    
    global $icl_language_switcher;
    
    if (isset($icl_language_switcher)) {
        ob_start();
        $icl_language_switcher->language_selector_footer_style();
        $icl_language_switcher->language_selector_footer();
        
        $result = ob_get_clean();
        return $result;
    }
    
    return '';
    
}

/*
add_shortcode('wpml-breadcrumbs', 'wpv_wpml_breadcrumbs');
function wpv_wpml_breadcrumbs($atts, $value){
    ob_start();
    
    global $iclCMSNavigation;
    if (isset($iclCMSNavigation)) {
        $iclCMSNavigation->cms_navigation_breadcrumb('');
    }
    
    $result = ob_get_clean();
    
    return $result;
}
*/

add_shortcode('wpml-sidebar', 'wpv_wpml_sidebar');
function wpv_wpml_sidebar($atts, $value){
    ob_start();
    
    do_action('icl_navigation_sidebar', '');
    
    $result = ob_get_clean();
    
    return $result;
}
        
add_shortcode('yoast-breadcrumbs', 'wpv_yoast_breadcrumbs');
function wpv_yoast_breadcrumbs($atts, $value){
    
    if ( function_exists('yoast_breadcrumb') ) {
        return yoast_breadcrumb("","",false);
    }
    
    return '';
}