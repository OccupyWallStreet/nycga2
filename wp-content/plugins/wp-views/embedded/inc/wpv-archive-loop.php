<?php

// Hook into the template redirect and see if it's an archive loop
// Use the select page (that contains a View) to display the loop items.

add_action('template_redirect', 'wpv_archive_redirect');

function wpv_archive_redirect() {
    
    global $WPV_view_archive_loop, $WP_Views, $wp_query;
    
    $options = $WP_Views->get_options();
    
    // See if we have a setting for the home page.
	if ( is_home() && isset($options['view_home-blog-page']) && $options['view_home-blog-page'] > 0) {
        
        $WPV_view_archive_loop->initialize_archive_loop($options['view_home-blog-page']);
    }
    
    // check if it's a post type archive.
    if ( is_post_type_archive() ) {
        $post_type_object = $wp_query->get_queried_object();

        // See if we have a setting for this post type
        if ($post_type_object) {
            $post_type = $post_type_object->name;
            
            if (isset($options['view_cpt_' . $post_type]) && $options['view_cpt_' . $post_type] > 0) {
                $WPV_view_archive_loop->initialize_archive_loop($options['view_cpt_' . $post_type]);
            }
        }
        
    }

    // check taxonomy loops    
    if ( is_archive() ) {
    
        /* Taxonomy archives. */

        if ( is_tax() || is_category() || is_tag() ) {
    
			$term = $wp_query->get_queried_object();
            if ($term && isset($options['view_taxonomy_loop_' . $term->taxonomy]) && $options['view_taxonomy_loop_' . $term->taxonomy] > 0) {
                $WPV_view_archive_loop->initialize_archive_loop($options['view_taxonomy_loop_' . $term->taxonomy]);
            }
        }
    }
    
    if ( is_search()  && isset($options['view_search-page']) && $options['view_search-page'] > 0) {
        $WPV_view_archive_loop->initialize_archive_loop($options['view_search-page']);
    }

    if ( is_author()  && isset($options['view_author-page']) && $options['view_author-page'] > 0) {
        $WPV_view_archive_loop->initialize_archive_loop($options['view_author-page']);
    }

    // See if we have a setting for the Year page.
	if ( is_year() && isset($options['view_year-page']) && $options['view_year-page'] > 0) {
        
        $WPV_view_archive_loop->initialize_archive_loop($options['view_year-page']);
    }
    
    // See if we have a setting for the Month page.
	if ( is_month() && isset($options['view_month-page']) && $options['view_month-page'] > 0) {
        
        $WPV_view_archive_loop->initialize_archive_loop($options['view_month-page']);
    }

    // See if we have a setting for the Day page.
	if ( is_day() && isset($options['view_day-page']) && $options['view_day-page'] > 0) {
        
        $WPV_view_archive_loop->initialize_archive_loop($options['view_day-page']);
    }

}



class WP_Views_archive_loops{
    
    function __construct(){
        add_action('init', array($this, 'init'));
		
		$this->header_started = false;
		
		$this->in_the_loop = false;
		
    }
    

    function __destruct(){
        
    }

    function init(){
        if(is_admin()){
            add_action('admin_print_scripts', array($this,'add_js'));
                
            add_action('save_post', array($this, 'save_view_archive_settings'));
            
            add_action('wp_ajax_wpv_get_archive_post_type_summary', array($this, '_ajax_get_post_type_loop_summary'));
            add_action('wp_ajax_wpv_get_archive_taxonomy_summary', array($this, '_ajax_get_taxonomy_loop_summary'));
            add_action('wp_ajax_wpv_get_archive_view_edit_summary', array($this, '_ajax_get_view_edit_summary'));
            
        }
		
    }
    
    /** function: initialize_archive_loop
     *
     * This will redirect to display the given post_id
     * The post will be displayed using the theme template selected for it
     * When a View is rendered it will use the posts from the current query
     *
     */
    
    function initialize_archive_loop($post_id) {
        global $post, $wp_query;
        if (have_posts()) {
            
            $output_post = get_post($post_id);
            
            if ($output_post) {
                
                // Save the original query.
                
                $this->query = clone $wp_query;
                $this->view_id = $post_id;
                add_action('loop_start', array($this, 'loop_start'), 1, 1);
                add_action('loop_end', array($this, 'loop_end'), 999, 1);
				add_action('get_header', array($this, 'get_header'));
                
            }
        } 
    }
    
    function get_archive_loop_query() {
        if ($this->in_the_loop) {
            return $this->query;
        } else {
            return null;
        }
    }
    
	function get_header($name) {
		$this->header_started = true;
	}
	
    function loop_start($query) {
        if ($this->header_started && ($query->query_vars_hash == $this->query->query_vars_hash || $query->request == $this->query->request)) {
            ob_start();
            $this->post_count = $query->post_count;
            $query->post_count = 1;
            $this->loop_found = true;
        }
    }

    function loop_end($query) {
        if ($this->loop_found) {
            ob_end_clean();
    
            $query->post_count = $this->post_count;
    
            $this->in_the_loop = true;
            echo render_view(array('id' => $this->view_id));
            $this->in_the_loop = false;

            $this->loop_found = false;
        }
        
    }

    // Add setting to the Views Settings page to select which page to use
    // to display our archive loop.
    
    function admin_settings($options) {
        
        global $WP_Views;
        
        // Display controls for Post Type archives.
        
        $loops = $this->_get_post_type_loops();

        ?>
        
        <h3 class="title"><?php _e('Views for Post Type archive loops', 'wpv-views'); ?></h3>
        
        <?php

        $this->_display_post_type_loop_summary($loops, $options);
        $this->_display_post_type_loop_admin($loops, $options);
        
        
        // Display controls for the Taxonomy archive loops

        ?>
        
        <h3 class="title"><?php _e('Views for Taxonomy archive loops', 'wpv-views'); ?></h3>
        
        <?php
        
        
        $this->_display_taxonomy_loop_summary($options);
        $this->_display_taxonomy_loop_admin($options);
        
    }

    function _get_post_type_loops() {
        $loops = array('home-blog-page' => __('Home/Blog', 'wpv-views'),
                       'search-page' => __('Search results', 'wpv-views'),
                       'author-page' => __('Author archives', 'wpv-views'),
                       'year-page' => __('Year archives', 'wpv-views'),
                       'month-page' => __('Month archives', 'wpv-views'),
                       'day-page' => __('Day archives', 'wpv-views'));

        $post_types = get_post_types(array('public'=>true), 'objects');
        foreach($post_types as $post_type) {
			if (!in_array($post_type->name, array('post', 'page', 'attachment'))) {
				$type = 'cpt_' . $post_type->name;
				$name = $post_type->labels->name;
				$loops[$type] = $name;
			}
        }

        return $loops;        
    }
    
    function _ajax_get_post_type_loop_summary() {
        global $WP_Views;
        
		if (wp_verify_nonce($_POST['wpv_post_type_loop_nonce'], 'wpv_post_type_loop_nonce')) {
            
            $loops = $this->_get_post_type_loops();
            
            $options = $WP_Views->get_options();
            $options = $this->submit($options);
            
            $WP_Views->save_options($options);
            
            $this->_display_post_type_loop_summary($loops, $options);
        }
        die();
    }
    
    function _display_post_type_loop_summary($loops, $options) {
        global $WP_Views;
        $views_available = $WP_Views->get_view_titles();
        
        $selected = '';
        
        foreach($loops as $loop => $loop_name) {
            if (isset ($options['view_' . $loop]) && $options['view_' . $loop] > 0) {
				$view_id = $options['view_' . $loop];
				if (function_exists('icl_object_id')) {
					$view_id = icl_object_id($view_id, 'view', true);
				}

                $selected .= '<li type=square style="margin:0;">' . sprintf(__('%s using "%s"', 'wpv-views'), $loop_name, $views_available[$view_id]) . '</li>';
            }
        }

        if ($selected == '') {
            $selected = __('There are no Views being used for Post Type archive loops.', 'wpv-views');
        } else {
            $selected = '<ul style="margin-left:20px">' . $selected . '</ul>';
        }
        
        echo '<div id="wpv-post-type-loop-summary" style="margin-left:20px;">';
        echo $selected;
        ?>
        <input class="button-secondary" type="button" value="<?php echo __('Edit', 'wpv-views'); ?>" name="post_type_loop_edit" onclick="wpv_archive_post_type_loop_edit();"/>
        </div>
        
        <?php
        
    }
    
    function _display_post_type_loop_admin($loops, $options) {
        
        global $WP_Views;
        
        $add_new_view_url = admin_url('post-new.php?post_type=view');
        ?>
        
        <div id="wpv-post-type-loop-edit" style="margin-left:20px;display:none">
            
    		<?php wp_nonce_field('wpv_post_type_loop_nonce', 'wpv_post_type_loop_nonce'); ?>
            
            <table class="widefat" style="width:auto;">
                <thead>
                    <tr>
                        <th><?php _e('Loop'); ?></th>
                        <th><?php _e('Use this View', 'wpv-views'); ?></th>
                    </tr>
                </thead>
                        
                <tbody>
                    
                    <?php
                        foreach($loops as $loop => $loop_name) {
                            echo '<tr><td>' . $loop_name . '</td>';
                            
                            $selected_view = 0;
                            if (isset ($options['view_' . $loop])) {
                                $selected_view = $options['view_' . $loop];
                            }
                            
                            $select = $WP_Views->get_view_select_box($loop, $selected_view, true);
                            
                            echo '<td>' . $select . '&nbsp;&nbsp;<a href="' . $add_new_view_url . '&view_archive=' . $loop. '">' . __('Create a new View for this listing page', 'wpv-views'). '</td>';
                            
                            echo '</tr>';
                        }
                        
                    ?>


                </tbody>
            </table>

        <input class="button-primary" type="button" value="<?php echo __('Save', 'wpv-views'); ?>" name="post_type_loop_save" onclick="wpv_archive_post_type_loop_save();"/>
        <img id="wpv_save_post_type_loop_spinner" src="<?php echo WPV_URL; ?>/res/img/ajax-loader.gif" width="16" height="16" style="display:none" alt="loading" />

        <input class="button-secondary" type="button" value="<?php echo __('Cancel', 'wpv-views'); ?>" name="post_type_loop_cancel" onclick="wpv_archive_post_type_loop_cancel();"/>

        </div>
        
        <?php
    }

    function _ajax_get_taxonomy_loop_summary() {
        global $WP_Views;
        
		if (wp_verify_nonce($_POST['wpv_taxonomy_loop_nonce'], 'wpv_taxonomy_loop_nonce')) {
            $options = $WP_Views->get_options();
            $options = $this->submit($options);
            
            $WP_Views->save_options($options);
            
            $this->_display_taxonomy_loop_summary($options);
        }
        die();
        
        
    }
    
    function _display_taxonomy_loop_summary($options) {

        global $WP_Views;
        $views_available = $WP_Views->get_view_titles();
        
        $selected = '';
        
        $taxonomies = get_taxonomies('', 'objects');
        foreach ($taxonomies as $category_slug => $category) {
            if ($category_slug == 'nav_menu' || $category_slug == 'link_category'
                    || $category_slug == 'post_format') {
                continue;
            }
            $name = $category->name;
            if (isset ($options['view_taxonomy_loop_' . $name ]) && $options['view_taxonomy_loop_' . $name ] > 0) {
				$view_id = $options['view_taxonomy_loop_' . $name];
				if (function_exists('icl_object_id')) {
					$view_id = icl_object_id($view_id, 'view', true);
				}
				
                $selected .= '<li type=square style="margin:0;">' . sprintf(__('%s using "%s"', 'wpv-views'), $category->labels->name, $views_available[$view_id]) . '</li>';
            }
        }

        if ($selected == '') {
            $selected = __('There are no Views being used for Taxonomy archive loops.', 'wpv-views');
        } else {
            $selected = '<ul style="margin-left:20px">' . $selected . '</ul>';
        }
        
        echo '<div id="wpv-taxonomy-loop-summary" style="margin-left:20px;">';
        echo $selected;
        ?>
        <input class="button-secondary" type="button" value="<?php echo __('Edit', 'wpv-views'); ?>" name="wpv-taxonomy-loop-edit" onclick="wpv_archive_taxonomy_loop_edit();"/>
        </div>
        
        <?php
    }
    
    function _display_taxonomy_loop_admin($options) {
        
        global $WP_Views;
        
        $add_new_view_url = admin_url('post-new.php?post_type=view');
        
        ?>
        <div id="wpv-taxonomy-loop-edit" style="margin-left:20px;display:none">
            
            <?php wp_nonce_field('wpv_taxonomy_loop_nonce', 'wpv_taxonomy_loop_nonce'); ?>
            
            <table class="widefat" style="width:auto;">
                <thead>
                    <tr>
                        <th><?php _e('Loop'); ?></th>
                        <th><?php _e('Use this View', 'wpv-views'); ?></th>
                    </tr>
                </thead>
                        
                <tbody>
                    
                    <?php
                    
                        $taxonomies = get_taxonomies('', 'objects');
                        foreach ($taxonomies as $category_slug => $category) {
                            if ($category_slug == 'nav_menu' || $category_slug == 'link_category'
                                    || $category_slug == 'post_format') {
                                continue;
                            }
                            $name = $category->name;
                            ?>
                            <tr>
                                <td><?php echo $category->labels->name; ?></td>
                                <td>
                                    <?php
                                        if (!isset($options['view_taxonomy_loop_' . $name ])) {
                                            $options['view_taxonomy_loop_' . $name ] = '0';
                                        }
                                        $template = $WP_Views->get_view_select_box('taxonomy_loop_'. $name, $options['view_taxonomy_loop_' . $name ], true);
                                        
                                        echo $template . '&nbsp;&nbsp;<a href="' . $add_new_view_url . '&view_archive_taxonomy=' . $name. '">' . __('Create a new View for this listing page', 'wpv-views');

                                    ?>
                                </td>
                            </tr>
                            <?php
                        }
                            
                    
                    ?>
                </tbody>
            </table>


        <input class="button-primary" type="button" value="<?php echo __('Save', 'wpv-views'); ?>" name="post_type_loop_save" onclick="wpv_archive_taxonomy_loop_save();"/>
        <img id="wpv_save_taxonomy_loop_spinner" src="<?php echo WPV_URL; ?>/res/img/ajax-loader.gif" width="16" height="16" style="display:none" alt="loading" />

        <input class="button-secondary" type="button" value="<?php echo __('Cancel', 'wpv-views'); ?>" name="post_type_loop_cancel" onclick="wpv_archive_taxonomy_loop_cancel();"/>
        </div>

        <?php
        
    }

        
    // Save the view settings for the archive loops.
    function submit($options) {
     
        foreach($_POST as $index => $value) {
            if (strpos($index, 'view_') === 0) {
                $options[$index] = $value;
            }
            
            if (strpos($index, 'view_taxonomy_loop_') === 0) {
                $options[$index] = $value;
            }
        }
        
        return $options;
    }
    
    function add_js() {
        global $pagenow, $post;
        
        if(($pagenow == 'post.php' && $post->post_type == 'view') ||
                ($pagenow == 'post-new.php' && isset($_GET['post_type']) && $_GET['post_type'] == 'view') ||
                ($pagenow == 'edit.php' && isset($_GET['page']) && $_GET['page'] == 'views-settings')){
        
            wp_enqueue_script( 'views-archive-loop-script' , WPV_URL . '/res/js/views_archive_loop.js', array('jquery'), WPV_VERSION);
        }
    }
    
    function view_edit_admin($view_id, $view_settings) {
        global $WP_Views;
        $options = $WP_Views->get_options();
        ?>
        
        <div id="wpv-archive-view-mode"<?php if($view_settings['view-query-mode'] != 'archive') {echo ' style="display:none;"';} ?>>
            <?php $this->_display_view_edit_summary($view_id, $options); ?>
            <?php $this->_display_view_edit_edit($view_id); ?>
        </div>
        
        <?php
    }
    
    function _ajax_get_view_edit_summary() {
        
		if (wp_verify_nonce($_POST['wpv_view_edit_nonce'], 'wpv_view_edit_nonce')) {
            $options = array();
            $view_id = $_POST['wpv-archive-view-id'];
            
            foreach($_POST as $key => $value) {
                if (strpos($key, 'wpv-view-loop-') === 0) {
                    $options['view_' . substr($key, 14)] = $view_id;
                }
    
                if (strpos($key, 'wpv-view-taxonomy-loop-') === 0) {
                    $options['view_taxonomy_loop_' . substr($key, 23)] = $view_id;
                }
            }
            
            $this->_display_view_edit_summary($view_id, $options);
        }
        die();
    }
    
    function _display_view_edit_summary($view_id, $options) {
        global $WP_Views;
        $loops = $this->_get_post_type_loops();

        $options = $this->_view_edit_options($view_id, $options);
        
        ?>
        <div id="wpv-archive-view-mode-summary" style="margin-left:20px;">
            <?php
                
                $selected = '';
                foreach($loops as $loop => $loop_name) {
                    if (isset ($options['view_' . $loop]) && $options['view_' . $loop] == $view_id) {
                        if ($selected != '') {
                            $selected .= ', ';
                        }
                        $selected .= sprintf(__('post type <strong>%s</strong>', 'wpv-views'), $loop_name);
                    }
                }
                $taxonomies = get_taxonomies('', 'objects');
                foreach ($taxonomies as $category_slug => $category) {
                    if ($category_slug == 'nav_menu' || $category_slug == 'link_category'
                            || $category_slug == 'post_format') {
                        continue;
                    }
                    $name = $category->name;
                    if (isset ($options['view_taxonomy_loop_' . $name ]) && $options['view_taxonomy_loop_' . $name ] == $view_id) {
                        if ($selected != '') {
                            $selected .= ', ';
                        }
                        $selected .= sprintf(__('taxonomy <strong>%s</strong>', 'wpv-views'), $category->labels->name);
                    }
                }
                
                if ($selected == '') {
                    $selected = __("This View isn't being used for any archive loops.", 'wpv-views');
                } else {
                    $selected = sprintf(__('This View is being used for these archive loops: %s', 'wpv-views'), $selected);
                }
                echo $selected;
            
            ?>
            <br />
            <input class="button-secondary" type="button" value="<?php echo __('Edit', 'wpv-views'); ?>" name="wpv-archive-view-edit" onclick="wpv_archive_view_edit();"/>
        </div>
        <?php
    }

    function _view_edit_options($view_id, $options) {
        static $js_added = false;

        $title = '';        
        if (isset($_GET['view_archive'])) {
            $options['view_' . $_GET['view_archive']] = $view_id;
            $loops = $this->_get_post_type_loops();
            $title = sprintf(__('%s-archive', 'wpv-views'), $loops[$_GET['view_archive']]);
        }
        
        if (isset($_GET['view_archive_taxonomy'])) {
            $options['view_taxonomy_loop_' . $_GET['view_archive_taxonomy']] = $view_id;
            $taxonomies = get_taxonomies('', 'objects');
            $title = sprintf(__('%s-taxonomy-archive', 'wpv-views'), $taxonomies[$_GET['view_archive_taxonomy']]->labels->name);
        }
        
        if ($title != '' && !$js_added) {
            // add some js to set the post title.
            
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function($){
                    jQuery('#title').val('<?php echo esc_js($title); ?>');
                });
            </script>
            <?php
            $js_added = true;
        }
        
        return $options;        
    }
    
    function _display_view_edit_edit($view_id) {
        global $WP_Views;
        $options = $WP_Views->get_options();
        $loops = $this->_get_post_type_loops();
        
        $options = $this->_view_edit_options($view_id, $options);

        ?>
        <div id="wpv-archive-view-mode-edit" style="background:<?php echo WPV_EDIT_BACKGROUND;?>;margin-left:20px;display:none;">
        
            <?php wp_nonce_field('wpv_view_edit_nonce', 'wpv_view_edit_nonce'); ?>
            
            <div style="margin-left:10px;margin-top:10px;margin-bottom:10px;">
                <input type="hidden" value="<?php echo $view_id; ?>" name="wpv-archive-view-id">
                <br />
                <?php _e('Use this View for these archive loops:', 'wpv-views');?>
                <br />
                <table class="widefat" style="width:auto;margin-top:10px;">
                    <thead>
                        <tr>
                            <th><?php _e('Post type loops', 'wpv-views'); echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'; ?></th>
                            <th><?php _e('Taxonomy loops', 'wpv-views'); ?></th>
                        </tr>
                    </thead>
                            
                    <tbody>
                        <tr>
                            <td>
                                <ul>
                                    <?php
                                        foreach($loops as $loop => $loop_name) {
                                            $checked = (isset ($options['view_' . $loop]) && $options['view_' . $loop] == $view_id) ? ' checked="checked"' : '';
                                            echo '<li>';
                                            echo '<label><input type="checkbox"' . $checked . ' name="wpv-view-loop-' . $loop . '" />' . $loop_name . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>';
                                            echo '</li>';
                                        }
                                    
                                    ?>
                                </ul>
                            </td>
                            <td>
                                <ul>
                                    <?php
                                        $taxonomies = get_taxonomies('', 'objects');
                                        foreach ($taxonomies as $category_slug => $category) {
                                            if ($category_slug == 'nav_menu' || $category_slug == 'link_category'
                                                    || $category_slug == 'post_format') {
                                                continue;
                                            }
                                            $name = $category->name;
                                            $checked = (isset ($options['view_taxonomy_loop_' . $name ]) && $options['view_taxonomy_loop_' . $name ] == $view_id) ? ' checked="checked"' : '';
                                            echo '<li>';
                                            echo '<label><input type="checkbox"' . $checked . ' name="wpv-view-taxonomy-loop-' . $name . '" />' . $category->labels->name . '</label>';
                                            echo '</li>';
                                        }
                                    ?>
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <br />
                <input class="button-primary" type="button" value="<?php echo __('OK', 'wpv-views'); ?>" name="wpv-archive-view-ok" onclick="wpv_archive_view_ok();"/>
                <img id="wpv_archive_view_loop_spinner" src="<?php echo WPV_URL; ?>/res/img/ajax-loader.gif" width="16" height="16" style="display:none" alt="loading" />
        
                <input class="button-secondary" type="button" value="<?php echo __('Cancel', 'wpv-views'); ?>" name="wpv-archive-view-cancel" onclick="wpv_archive_view_cancel();"/>
                <br />
            </div>
        </div>
        <?php
    }
    
    function save_view_archive_settings($post_id){
        global $wpdb, $WP_Views;
        
        list($post_type, $post_status) = $wpdb->get_row("SELECT post_type, post_status FROM {$wpdb->posts} WHERE ID = " . $post_id, ARRAY_N);
        
        if ($post_type == 'view') {

            $found = false;
            $options = $WP_Views->get_options();

			// clear existing ones
			$loops = $this->_get_post_type_loops();
			foreach ($loops as $type => $name) {
				if (isset($options['view_' . $type]) && $options['view_' . $type] == $post_id) {
					unset($options['view_' . $type]);
                    $found = true;
				}
			}
			$taxonomies = get_taxonomies('', 'objects');
			foreach ($taxonomies as $category_slug => $category) {
				if (isset($options['view_taxonomy_loop_' . $category_slug]) && $options['view_taxonomy_loop_' . $category_slug] == $post_id) {
					unset($options['view_taxonomy_loop_' . $category_slug]);
                    $found = true;
				}
			}				
            
            foreach($_POST as $key => $value) {
                if (strpos($key, 'wpv-view-loop-') === 0) {
                    $options['view_' . substr($key, 14)] = $post_id;
                    $found = true;
                }
    
                if (strpos($key, 'wpv-view-taxonomy-loop-') === 0) {
                    $options['view_taxonomy_loop_' . substr($key, 23)] = $post_id;
                    $found = true;
                }
            }
            
            if ($found) {
                $WP_Views->save_options($options);
            }
            
        }
    }

}

$WPV_view_archive_loop = new WP_Views_archive_loops;

