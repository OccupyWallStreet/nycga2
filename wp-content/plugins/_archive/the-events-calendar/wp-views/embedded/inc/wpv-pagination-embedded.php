<?php

// Set the default values to display in the View editor.
add_filter('wpv_view_settings', 'wpv_pager_defaults', 10, 2);
function wpv_pager_defaults($view_settings, $view_id=null) {
    $defaults = array(
        'posts_per_page' => 10,
        'pagination' => array(
            0 => 'disable',
            'mode' => 'paged',
            'preload_images' => 1,
            'cache_pages' => 1,
            'preload_pages' => 1,
            'spinner' => 'default',
            'spinner_image' => WPV_URL . '/res/img/ajax-loader.gif',
            'spinner_image_uploaded' => '',
            'callback_next' => '',
            'page_selector_control_type' => 'drop_down',
        ),
        'ajax_pagination' => array(
            0 => 'disable',
            'style' => 'fade',
        ),
        'rollover' => array(
            'posts_per_page' => 1,
            'speed' => 5,
            'effect' => 'fade',
            'preload_images' => 1,
            'include_page_selector' => 0,
            'include_prev_next_page_controls' => 0,
        ),
    );
    $view_settings = wpv_parse_args_recursive($view_settings, $defaults);

    if ($view_settings['pagination']['spinner'] == 'uploaded') {
        $view_settings['pagination']['spinner_image'] = $view_settings['pagination']['spinner_image_uploaded'];
    }

    return $view_settings;
}

add_filter('wpv_view_settings_save', 'wpv_pager_defaults_save', 10, 1);
function wpv_pager_defaults_save($view_settings) {
    // we need to set 0 for the checkboxes that aren't checked and are missing for the $_POST.
    
    $defaults = array(
        'pagination' => array(
            'preload_images' => 0,
            'cache_pages' => 0,
            'preload_pages' => 0,
        ),
        'rollover' => array(
            'preload_images' => 0,
        ),
    );
    $view_settings = wpv_parse_args_recursive($view_settings, $defaults);

    return $view_settings;
}

/**
 * Views-Shortcode: wpv-pagination
 *
 * Description: Display the pagination controls that are within the shortcode.
 * The pagination controls will only be displayed if there are multiple
 * pages to display
 *
 * Parameters:
 * This has no parameters.
 *
 */

add_shortcode('wpv-pagination', 'wpv_pagination_shortcode');

function wpv_pagination_shortcode($atts, $value) {

    extract(
            shortcode_atts(array(), $atts)
    );

    global $WP_Views;

    if ($WP_Views->get_max_pages() > 1.0) {
        // output the pagination.
        return wpv_do_shortcode($value);
    } else {
        // only 1 page so we don't need any pagination controls.
        return '';
    }
}

/**
 * Views-Shortcode: wpv-pager-num-page
 *
 * Description: Display the maximum number of pages found by the Views Query.
 *
 * Parameters:
 * This has no parameters.
 *
 */
add_shortcode('wpv-pager-num-page', 'wpv_pager_num_page_shortcode');

function wpv_pager_num_page_shortcode($atts) {
    extract(
            shortcode_atts(array(), $atts)
    );

    global $WP_Views;

    return sprintf('%1.0f', $WP_Views->get_max_pages());
}

/**
 * Views-Shortcode: wpv-pager-prev-page
 *
 * Description: Display a "Previous" link to move to the previous page.
 * eg. [wpv-pager-prev-page]Previous[/wpv-pager-prev-page]
 *
 * Parameters:
 * This has no parameters.
 *
 */
add_shortcode('wpv-pager-prev-page', 'wpv_pager_prev_page_shortcode');

function wpv_pager_prev_page_shortcode($atts, $value) {
    extract(
            shortcode_atts(array(), $atts)
    );

    global $WP_Views;

    $page = $WP_Views->get_current_page_number();
    $view_settings = $WP_Views->get_view_settings();
    $display = false;
    if ($view_settings['pagination']['mode'] == 'rollover') {
        $display = true;
    } else if ($page > 1) {
        $display = true;
    }

    if ($display) {

        $page--;

        $value = wpv_do_shortcode($value);

        // TODO remove
//        return '<a href="#" onclick="return wpv_pager_click_' . $WP_Views->get_view_count() . '(\'' . $page. '\')">' . $value . '</a>';
        
        $ajax = $view_settings['ajax_pagination'][0] == 'enable' ? 'true' : 'false';
        $effect = isset($view_settings['ajax_pagination']['style']) ? $view_settings['ajax_pagination']['style'] : 'fade';
        $stop_rollover = 'false';
        if ($view_settings['pagination']['mode'] == 'rollover') {
            $ajax = 'true';
            $effect = $view_settings['rollover']['effect'];
            $stop_rollover = 'true';
            if ($effect == 'slideleft') {
                $effect = 'slideright';
            }
            
            if ($effect == 'slidedown') {
                $effect = 'slideup';
            }
            
        }
        $cache_pages = $view_settings['pagination']['cache_pages'];
        $preload_pages = $view_settings['pagination']['preload_pages'];
        $spinner = $view_settings['pagination']['spinner'];
        $spinner_image = $view_settings['pagination']['spinner_image'];
        $callback_next = $view_settings['pagination']['callback_next'];

        if ($page <= 0) {
            $page = $WP_Views->get_max_pages();
        } else if ($page > $WP_Views->get_max_pages()) {
            $page = 1;
        }

        return '<a href="#" class="wpv-filter-previous-link" onclick="return wpv_pagination_replace_view(' . $WP_Views->get_view_count() . ',' . $page . ', ' . $ajax . ', \'' . $effect . '\', ' . $WP_Views->get_max_pages() . ', ' . $cache_pages . ', ' . $preload_pages . ', \'' . $spinner . '\', \'' . $spinner_image . '\', \'' . $callback_next . '\', ' . $stop_rollover . ')">' . $value . '</a>';
    } else {
        return '';
    }
}

/**
 * Views-Shortcode: wpv-pager-next-page
 *
 * Description: Display a "Next" link to move to the next page.
 * eg. [wpv-pager-next-page]Next[/wpv-pager-next-page]
 *
 * Parameters:
 * This has no parameters.
 *
 */
add_shortcode('wpv-pager-next-page', 'wpv_pager_next_page_shortcode');

function wpv_pager_next_page_shortcode($atts, $value) {
    extract(
            shortcode_atts(array(), $atts)
    );

    global $WP_Views;

    $page = $WP_Views->get_current_page_number();
    $view_settings = $WP_Views->get_view_settings();
    $display = false;
    if ($view_settings['pagination']['mode'] == 'rollover') {
        $display = true;
    } else if ($page < $WP_Views->get_max_pages()) {
        $display = true;
    }

    if ($display) {

        $page++;

        $value = wpv_do_shortcode($value);

        // TODO remove
//        return '<a href="#" onclick="return wpv_pager_click_' . $WP_Views->get_view_count() . '(\'' . $page. '\')">' . $value . '</a>';

        $ajax = $view_settings['ajax_pagination'][0] == 'enable' ? 'true' : 'false';
        $effect = isset($view_settings['ajax_pagination']['style']) ? $view_settings['ajax_pagination']['style'] : 'fade';
        $stop_rollover = 'false';
        if ($view_settings['pagination']['mode'] == 'rollover') {
            $ajax = 'true';
            $effect = $view_settings['rollover']['effect'];
            $stop_rollover = 'true';
            if ($effect == 'slideright') {
                $effect = 'slideleft';
            }

            if ($effect == 'slideup') {
                $effect = 'slidedown';
            }
            
        }
        $cache_pages = $view_settings['pagination']['cache_pages'];
        $preload_pages = $view_settings['pagination']['preload_pages'];
        $spinner = $view_settings['pagination']['spinner'];
        $spinner_image = $view_settings['pagination']['spinner_image'];
        $callback_next = $view_settings['pagination']['callback_next'];
        
        if ($page <= 0) {
            $page = $WP_Views->get_max_pages();
        } else if ($page > $WP_Views->get_max_pages()) {
            $page = 1;
        }
        
        return '<a href="#" class="wpv-filter-next-link" onclick="return wpv_pagination_replace_view(' . $WP_Views->get_view_count() . ',' . $page . ', ' . $ajax . ', \'' . $effect . '\',' . $WP_Views->get_max_pages() . ', ' . $cache_pages . ', ' . $preload_pages . ', \'' . $spinner . '\', \'' . $spinner_image . '\', \'' . $callback_next . '\', ' . $stop_rollover . ')">' . $value . '</a>';
    } else {
        return '';
    }
}

/**
 * Views-Shortcode: wpv-pager-current-page
 *
 * Description: Display the current page number. It can be displayed as a number
 * or as a drop-down list to select another page.
 *
 * Parameters:
 * 'style' => leave empty to display a number.
 * 'style' => 'drop_down' to display a selector to select another page.
 * 'stile' => 'link' to display a series of links to each page
 *
 */
add_shortcode('wpv-pager-current-page', 'wpv_pager_current_page_shortcode');

function wpv_pager_current_page_shortcode($atts) {
    extract(
            shortcode_atts(array(), $atts)
    );

    global $WP_Views;

    $page = $WP_Views->get_current_page_number();

    if (isset($atts['style'])) {
        
        $view_settings = $WP_Views->get_view_settings();
        $cache_pages = $view_settings['pagination']['cache_pages'];
        $preload_pages = $view_settings['pagination']['preload_pages'];
        $spinner = $view_settings['pagination']['spinner'];
        $spinner_image = $view_settings['pagination']['spinner_image'];
        $callback_next = $view_settings['pagination']['callback_next'];
        
        if ($view_settings['pagination']['mode'] == 'paged') {
            $ajax = $view_settings['ajax_pagination'][0] == 'enable' ? 'true' : 'false';
            $effect = isset($view_settings['ajax_pagination']['style']) ? $view_settings['ajax_pagination']['style'] : 'fade';
        }
        
        if ($view_settings['pagination']['mode'] == 'rollover') {
            $ajax = 'true';
            $effect = $view_settings['rollover']['effect'];
            // convert rollover to slide effect if the user clicks on a page.
            
            if ($effect == 'slideleft' || $effect == 'slideright') {
                $effect = 'slideh';
            }
            if ($effect == 'slideup' || $effect == 'slidedown') {
                $effect = 'slidev';
            }
        }

        switch($atts['style']) {
            case 'drop_down':
                $out = '';
                $out .= '<select id="wpv-page-selector-' . $WP_Views->get_view_count() . '" onchange="wpv_pagination_replace_view(' . $WP_Views->get_view_count() . ', jQuery(this).val(), ' . $ajax . ', \'' . $effect . '\',' . $WP_Views->get_max_pages() . ', ' . $cache_pages . ', ' . $preload_pages . ', \'' . $spinner . '\', \'' . $spinner_image . '\', \'' . $callback_next . '\', true);">' . "\n";
        
                $max_page = intval($WP_Views->get_max_pages());
                for ($i = 1; $i < $max_page + 1; $i++) {
                    $is_selected = $i == $page ? ' selected="selected"' : '';
                    $page_number = apply_filters('wpv_pagination_page_number', $i);
                    $out .= '<option value="' . $i . '" ' . $is_selected . '>' . $page_number . "</option>\n";
                }
                $out .= "</select>\n";
        
                return $out;
                    
            case 'link':
                $page_count = intval($WP_Views->get_max_pages());
                // output a series of links to each page.
                
                $out = '<div class="wpv_pagination_links">';
                $out .= '<ul class="wpv_pagination_dots" style="list-style-position:outside; margin: 0; list-style-type: none;">';
                
                for ($i = 1; $i < $page_count + 1; $i++) {
                    $page_title = sprintf(__('Page %s', 'wpv-views'), $i);
                    $page_title = apply_filters('wpv_pagination_page_title', $page_title, $i);
                    $page_number = apply_filters('wpv_pagination_page_number', $i);
                    $link = '<a title="' . $page_title . '" href="#" class="wpv-filter-previous-link" onclick="wpv_pagination_replace_view_links(' . $WP_Views->get_view_count() . ',' . $i . ', ' . $ajax . ', \'' . $effect . '\', ' . $page_count . ', ' . $cache_pages . ', ' . $preload_pages . ', \'' . $spinner . '\', \'' . $spinner_image . '\', \'' . $callback_next . '\', true); return false;">' . $page_number . '</a>';
                    $link_id = 'wpv-page-link-' . $WP_Views->get_view_count() . '-' . $i;
                    if ($i == $page) {
                        $out .= '<li style="list-style-position:outside; list-style-type: none; float: left; margin-right: 5px;" id="' . $link_id . '" class="wpv_page_current">' . $link . '</li>';
                    } else {
                        $out .= '<li style="list-style-position:outside; list-style-type: none; float: left; margin-right: 5px;" id="' . $link_id . '">' . $link . '</li>';
                    }
                }
                $out .= '</ul>';
                $out .= '</div>';
                $out .= '<br />';
                return $out;
                
                

        }
    } else {
        // show the page number.
        return sprintf('%d', $page);
    }
}

function wpv_pagination_js() {
    static $js_rendered = false;
    if ($js_rendered == false) {

        $ajax_url = site_url();
        if (substr($ajax_url, strlen($ajax_url) - 1, 1) != '/') {
            $ajax_url .= '/';
        }

        $permalink_structure = get_option('permalink_structure');

        if ($permalink_structure != '') {
            $ajax_url .= 'wpv-ajax-pagination/';
        } else {
            $ajax_url = plugins_url('wpv-ajax-pagination-default.php', __FILE__);
        }
        
        ?>
        <script type="text/javascript">
        
            var wpv_admin_ajax_url = "<?php echo admin_url('admin-ajax.php'); ?>";
            var wpv_ajax_pagination_url = "<?php echo $ajax_url; ?>";

                        
        </script>
        <?php
        $js_rendered = true;
    }
}

function wpv_pagination_rollover_shortcode() {
    global $WP_Views;
    $view_settings = $WP_Views->get_view_settings();
    $view_settings['rollover']['count'] = $WP_Views->get_max_pages();
    wpv_pagination_rollover_add_slide($WP_Views->get_view_count(),
            $view_settings);
    add_action('wp_footer', 'wpv_pagination_rollover_js');
}

function wpv_pagination_rollover_add_slide($id, $settings = array()) {
    static $rollovers = array();
    if ($id == 'get') {
        return $rollovers;
    }
    $rollovers[$id] = $settings;
}

function wpv_pagination_rollover_js() {
    $rollovers = wpv_pagination_rollover_add_slide('get');
    if (!empty($rollovers)) {
        global $WP_Views;
        $out = '';
        wpv_pagination_js();

        ?>
        <script type="text/javascript">
            jQuery(document).ready(function(){
        <?php
        foreach ($rollovers as $id => $rollover) {
            $out .= 'jQuery("#wpv-view-layout-' . $id . '").wpvRollover({id: ' . $id
                    . ', effect: "' . $rollover['rollover']['effect']
                    . '", speed: ' . $rollover['rollover']['speed']
                    . ', page: 1, count: ' . $rollover['rollover']['count']
                    . ', cache_pages:' . $rollover['pagination']['cache_pages']
                    . ', preload_pages:' . $rollover['pagination']['preload_pages']
                    . ', spinner:"' . $rollover['pagination']['spinner'] . '"'
                    . ', spinner_image:"' . $rollover['pagination']['spinner_image'] . '"'
                    . ', callback_next:"' . $rollover['pagination']['callback_next'] . '"'
                    . '});' . "\r\n";
        }
        echo $out;

        ?>
                });
                        
        </script>

        <?php
    }
}

// add a filter so we can set the correct language in WPML during pagination
add_filter('icl_current_language', 'wpv_ajax_pagination_lang');

function wpv_ajax_pagination_lang($lang) {
    if (isset($_POST['action']) && $_POST['action'] == 'wpv_get_page' && isset($_POST['lang'])) {
        $lang = $_POST['lang'];
    }

    return $lang;
}

// Gets the new page for a view.

function wpv_ajax_get_page($post_data) {
    global $WP_Views;
    
    // Fix a problem with WPML using cookie language when DOING_AJAX is set.
    $cookie_lang = null;
    if (isset($_COOKIE['_icl_current_language']) && isset($post_data['lang'])) {
        $cookie_lang = $_COOKIE['_icl_current_language'];
        $_COOKIE['_icl_current_language'] = $post_data['lang'];
    }
    
    // Switch WPML to the correct language.
    if (isset($post_data['lang'])) {
        global $sitepress;
        if (method_exists($sitepress, 'switch_lang')) {
            $sitepress->switch_lang($post_data['lang']);
        }
    }

    $post_id = $post_data['post_id'];

    $_GET['wpv_paged'] = $post_data['page'];
    $_GET['wpv_view_count'] = $post_data['view_number'];
    if (isset($post_data['wpv_column_sort_id']) && $post_data['wpv_column_sort_id'] != 'undefined') {
        $_GET['wpv_column_sort_id'] = $post_data['wpv_column_sort_id'];
    }
    if (isset($post_data['wpv_column_sort_dir']) && $post_data['wpv_column_sort_dir'] != 'undefined') {
        $_GET['wpv_column_sort_dir'] = $post_data['wpv_column_sort_dir'];
    }
    
    if (isset($post_data['get_params'])) {
        foreach($post_data['get_params'] as $key => $param) {
            $_GET[$key] = $param;
        }
    }

    global $post, $authordata, $id;

    $view_data = unserialize(base64_decode($post_data['view_hash']));
    $post = get_post($post_id);
    $authordata = new WP_User($post->post_author);
    $id = $post->ID;

    if ($post_data['wpv_view_widget_id'] == 0) {
        // set the view count so we return the right view number after rendering.
        $WP_Views->set_view_count(intval($post_data['view_number']) - 1, null);

        echo $WP_Views->short_tag_wpv_view($view_data);
        //echo wpv_do_shortcode($post->post_content);
    } else {
        
        // set the view count so we return the right view number after rendering.
        $WP_Views->set_view_count(intval($post_data['view_number']), $post_data['wpv_view_widget_id']);
        
        $widget = new WPV_Widget();
        
        $args = array('before_widget' => '',
                     'before_title' => '',
                     'after_title' => '',
                     'after_widget' => '');
        
        $widget->widget($args, array('title' => '',
                                     'view' => $post_data['wpv_view_widget_id']));
        
        echo $WP_Views->get_max_pages();
    }

    if ($cookie_lang) {
        // reset language cookie.
        $_COOKIE['_icl_current_language'] = $cookie_lang;
    }
}


add_action('template_redirect', 'wpv_pagination_router');

function wpv_pagination_router() {
    global $wp_query;
    $bits =explode("/",$_SERVER['REQUEST_URI']);
    for ($i = 0; $i < count($bits) - 1; $i++) {
    
        if ($bits[$i] == 'wpv-ajax-pagination') {
            // get the post data. It's hex encoded json
            $post_data = $bits[$i + 1];
            $post_data = pack('H*', $post_data);
            
            $post_data = json_decode($post_data, true);
            
            header('HTTP/1.0 200 OK');
            header( 'Content-Type: text/css' );
            echo '<html><body>';
    
            wpv_ajax_get_page($post_data);
            
            echo '</body></html>';
            
            $wp_query->is_404 = false;
            exit;
        }
    }
}