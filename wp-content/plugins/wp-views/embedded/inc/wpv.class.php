<?php

require_once( WPV_PATH_EMBEDDED . '/common/visual-editor/editor-addon.class.php');
require_once( WPV_PATH_EMBEDDED . '/common/views/promote.php');

require WPV_PATH_EMBEDDED . '/inc/wpv-filter-query.php';

class WP_Views{
    
    function __construct(){
        add_action('init', array($this, 'init'));
        add_action('widgets_init', array($this, 'widgets_init'));

        $this->options = null;
        $this->view_ids = array();
        $this->current_view = null;
        $this->CCK_types = array();
        
        $this->top_current_page = null;
        $this->current_page = array();
        
        $this->post_query = null;
		$this->post_query_stack = array();
        
        $this->view_count = 0;
        $this->set_view_counts = array();
		
		$this->taxonomy_data = array();
		
		$this->parent_taxonomy = 0;
		
		$this->view_shortcode_attributes = array();
        
        $this->widget_view_id = 0;
		
		$this->variables = array();

		$this->rendering_views_form_in_progress = false;
        
        add_filter('icl_cf_translate_state', array($this, 'custom_field_translate_state'), 10, 2);

    }
    

    function __destruct(){
        
    }

    function init(){
		
        $this->wpv_register_type_view();
        
        $this->plugin_localization();
        
        add_action('wp_ajax_wpv_get_type_filter_summary', 'wpv_ajax_get_type_filter_summary');
        add_action('wp_ajax_wpv_get_table_row_ui', array($this, 'ajax_get_table_row_ui'));
        add_action('wp_ajax_wpv_add_custom_field', 'wpv_ajax_add_custom_field');
        add_action('wp_ajax_wpv_add_taxonomy', 'wpv_ajax_add_taxonomy');
        add_action('wp_ajax_wpv_pagination', 'wpv_ajax_pagination');
        add_action('wp_ajax_wpv_views_editor_height', array($this, 'save_editor_height'));
        add_action('wp_ajax_wpv_get_posts_select', 'wpv_get_posts_select');
        add_action('wp_ajax_wpv_dismiss_message', array($this, 'wpv_dismiss_message'));
        add_action('wp_ajax_wpv_get_taxonomy_parents_select', 'wpv_get_taxonomy_parents_select');
        add_action('wp_ajax_wpv_get_taxonomy_term_check', 'wpv_get_taxonomy_term_check');
        add_action('wp_ajax_wpv_get_taxonomy_term_summary', 'wpv_ajax_get_taxonomy_term_summary');
        add_action('wp_ajax_wpv_get_post_relationship_info', 'wpv_ajax_wpv_get_post_relationship_info');
        add_action('wp_ajax_wpv_view_form_popup', 'wpv_ajax_wpv_view_form_popup');
		add_action('wp_ajax_wpv_insert_form_shortcode', 'wp_ajax_wpv_insert_form_shortcode');
		add_action('wp_ajax_wpv_get_show_hidden_custom_fields', array($this, 'wpv_get_show_hidden_custom_fields'));
		add_action('wp_ajax_wpv_format_date', array($this, 'wpv_format_date'));
        
        if(is_admin()){

            if (function_exists('wpv_admin_menu_import_export_hook')) {
                add_action('wp_loaded', 'wpv_admin_menu_import_export_hook');
            }
            
            add_action('admin_menu', array($this, 'admin_menu'));
            add_action('admin_head', array($this, 'settings_box_load'));            
            add_action('admin_footer', array($this, 'hide_view_body_controls'));            
            add_action('save_post', array($this, 'save_view_settings'));
            //add_action('save_post', array($this, 'save_css'));

            global $pagenow;
            
            if($pagenow == 'post.php' || $pagenow == 'post-new.php'){
                add_action('admin_head', array($this,'post_edit_tinymce'));
                add_action('admin_head', array($this,'add_css'));
                add_action('admin_print_scripts', array($this,'add_js'));
                
				add_action('icl_post_languages_options_after', array($this, 'language_options'));
                add_action('admin_head', array($this, 'set_editor_height'));
                
            }
            
            global $wp_version;
            if (version_compare($wp_version, '3.3', '<')) {
                add_filter('contextual_help', array($this, 'admin_plugin_help'), 10, 3);
            }
            
            promote_types_and_views();
            
        } else {

            // Add pagination for the front end.

            wp_enqueue_script( 'views-pagination-script' , WPV_URL_EMBEDDED . '/res/js/wpv-pagination-embedded.js', array('jquery'), WPV_VERSION);
            wp_enqueue_style( 'views-pagination-style', WPV_URL_EMBEDDED . '/res/css/wpv-pagination.css', array(), WPV_VERSION);
            
			wp_enqueue_script( 'date-picker-script' , WPV_URL_EMBEDDED . '/res/js/jquery.ui.datepicker.min.js', array('jquery-ui-core', 'jquery'), WPV_VERSION);
			wp_enqueue_style( 'date-picker-style' , WPV_URL_EMBEDDED . '/res/css/datepicker.css', array(), WPV_VERSION);
			wp_enqueue_script( 'wpv-date-front-end-script' , WPV_URL_EMBEDDED . '/res/js/wpv-date-front-end-control.js', array('jquery'), WPV_VERSION);

            add_action('wp_head', 'wpv_add_front_end_js');
			
        }        
        
         /*shorttags*/
        add_shortcode( 'wpv-view', array($this, 'short_tag_wpv_view') );
        add_shortcode( 'wpv-form-view', array($this, 'short_tag_wpv_view_form') );
        
        add_action('wp_print_styles', array($this, 'add_render_css'));

		add_filter('edit_post_link', array($this, 'edit_post_link'), 10, 2);


        // check for views import.

        global $wpv_theme_import, $wpv_theme_import_xml;
        if (isset($wpv_theme_import) && $wpv_theme_import != '') {
            include $wpv_theme_import;
    
            $dismissed = get_option('views_dismissed_messages', array());
            if (!in_array($timestamp, $dismissed)) {
                if ($timestamp > get_option('views-embedded-import', 0)) {
                    // something new to import.
                    if ($auto_import) {
                        if (!isset($_POST['import'])) {
                            // setup an automatic import
                            $_POST['import'] = __('Import', 'wpv-views');
                            $_POST['wpv-import-nonce'] = wp_create_nonce('wpv-import-nonce');
                            $_POST['views-overwrite'] = 'on';
                            $_POST['view-templates-overwrite'] = 'on';
                            $_POST['import-file'] = $wpv_theme_import_xml;
                        }
                    } else {
                        global $pagenow;
                        if ($pagenow != 'options-general.php' || !isset($_GET['page']) || $_GET['page'] != 'wpv-import-theme') {
                        
                            // add admin message about importing.
                            $link = '<a href=\"' . admin_url('options-general.php') . '?page=wpv-import-theme\">';
                            $text = sprintf(__('You have <strong>Views</strong> import pending. %sClick here to import.%s %sDismiss message.%s',
                                            'wpcf'), $link, '</a>',
                                    '<a onclick=\"jQuery(this).parent().parent().fadeOut();\" href=\"'
                                    . admin_url('admin-ajax.php?action=wpv_dismiss_message&amp;id='
                                            . $timestamp . '&amp;wpv_nonce=' . wp_create_nonce('wpv-dismiss-message')) . '\">', '</a>');
                            $code = 'echo "<div class=\"message updated\"><p>' . $text . '</p></div>";';
                            add_action('admin_notices', create_function('$a=1', $code));
                        }
                        
                    }
                }                
            }
        
        add_action('admin_menu', array($this, 'add_import_menu'));
		}        
    }
    
    function wpv_register_type_view() 
    {
      $labels = array(
        'name' => _x('Views', 'post type general name'),
        'singular_name' => _x('View', 'post type singular name'),
        'add_new' => _x('Add New', 'book'),
        'add_new_item' => __('Add New View', 'wpv-views'),
        'edit_item' => __('Edit View', 'wpv-views'),
        'new_item' => __('New View', 'wpv-views'),
        'view_item' => __('View Views', 'wpv-views'),
        'search_items' => __('Search Views', 'wpv-views'),
        'not_found' =>  __('No views found', 'wpv-views'),
        'not_found_in_trash' => __('No views found in Trash', 'wpv-views'), 
        'parent_item_colon' => '',
        'menu_name' => 'Views'
    
      );
      $args = array(
        'labels' => $labels,
        'public' => false,
        'publicly_queryable' => false,
        'show_ui' => false, 
        'show_in_menu' => false, 
        'query_var' => false,
        'rewrite' => false,
        'can_export' => false,
        'capability_type' => 'post',
        'has_archive' => false, 
        'hierarchical' => false,
        'menu_position' => 80,
        'supports' => array('title','editor','author')
      ); 
      register_post_type('view',$args);
    }
    
    function custom_field_translate_state($state, $field_name) {
        switch($field_name) {
            case '_wpv_settings':
            case '_wpv_layout_settings':
            case '_wpv_view_sync':
                return 'ignore';
            
            default:
                return $state;
        }
    }
    
	// Add WPML sync options.
	
	function language_options() {

        // not needed for theme version.
        
	}
    
    function widgets_init(){
        register_widget('WPV_Widget');
        register_widget('WPV_Widget_filter');
    }
    
    function register_CCK_type($type) {
        $this->CCK_types[] = $type;
    }
    
    function can_include_type($type) {
        return !in_array($type, $this->CCK_types);
    }
    
    function admin_menu(){
        // do nothing for theme version.
    }
    
    function add_import_menu() {
        add_options_page(__('Import Views for theme', 'wpv-views'), 'Import Views', 'manage_options', 'wpv-import-theme', array($this, 'import_views_from_theme'));
    }
    
    function import_views_from_theme() {
        
        global $wpv_theme_import_xml;

        global $import_errors, $import_messages;
		if (isset($_POST['import']) && $_POST['import'] == __('Import', 'wpv-views') &&
			wp_verify_nonce($_POST['wpv-import-nonce'], 'wpv-import-nonce') &&
			!$import_errors)
		
			{
			?>
			
			<div class="wrap">
		
				<div id="icon-views" class="icon32"><br /></div>
				<h2><?php _e('Views Import', 'wpv-views') ?></h2>
		
				<br />
				
				<h3><?php _e('Views import complete', 'wpv-views') ?></h3>
				
			</div>
	
			<?php
		} else {
			?>
			
			<div class="wrap">
		
				<div id="icon-views" class="icon32"><br /></div>
				<h2><?php _e('Views Import', 'wpv-views') ?></h2>
		
				<br />
				
				<?php wpv_admin_import_form($wpv_theme_import_xml); ?>
				
			</div>
	
			<?php
		}
    }
    
    function settings_box_load(){
		global $pagenow;
        if ($pagenow == 'options-general.php' && isset($_GET['page']) && $_GET['page'] == 'wpv-import-theme') {
            $this->include_admin_css();
        }
    }
	
	function hide_view_body_controls() {
		// do nothing for embedded version.
	}

    function include_admin_css() {
        $link_tag = '<link rel="stylesheet" href="'. WPV_URL . '/res/css/wpv-views.css?v='.WPV_VERSION.'" type="text/css" media="all" />';
        echo $link_tag;
    }

    /**
     * Output the CSS metabox on the view edit page.
     *
     */
    
    /*
    function css_box($post){
        $css = get_post_meta($post->ID, '_wpv_css', true);
        ?>
            <textarea name="_wpv_css" rows="6" cols="80" style="width:100%"><?php
              if (!empty($css)) {
                echo $css;
              }
            ?></textarea>
        <?php
    }
    */
    
    /**
     * save the view settings.
     * Called from a post_save action
     *
     */
    
    /*
     function save_css($post_id){
        if(isset($_POST['_wpv_css'])){
            update_post_meta($post_id, '_wpv_css', $_POST['_wpv_css']);
        }
    }
    */
    
    /**
     * Output the view query metabox on the view edit page.
     *
     */
    
    function settings_box($post){
        // do nothing in the theme version.
    }
    
    /**
     * save the view settings.
     * Called from a post_save action
     *
     */
    
    function save_view_settings($post_id){
        // do nothing in the theme version.
    }
    
    
    /**
     * Process the view shortcode
     *
     * eg. [wpv-view name='my-view']
     *
     */
    
    function short_tag_wpv_view($atts){
        global $wpdb;
        
        global $wplogger;
        $wplogger->log($atts);
        
        extract(
            shortcode_atts( array(
                'id'    => false,
                'name'  => false
            ), $atts )
        );
        
        if(empty($id) && !empty($name)){
            // lookup by post name first
            $id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_type='view' AND post_name=%s", $name));
            if (!$id) {
                // try the post title
                $id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_type='view' AND post_title=%s", $name));
            }
        }
        
        if(empty($id)){
            return sprintf('<!- %s ->', __('View not found', 'wpv-views'));
        }

		array_push($this->view_shortcode_attributes, $atts);
		
        $out = $this->render_view_ex($id, md5(serialize($atts)));
        
		array_pop($this->view_shortcode_attributes);
        
        return $out;
        
    }
    
    /**
     * Process the view shortcode
     *
     * eg. [wpv-form-view name='my-view' target_id='xx']
     *
     */
    
    function short_tag_wpv_view_form($atts){
        global $wpdb;
        
        global $wplogger;
        $wplogger->log($atts);
        
        extract(
            shortcode_atts( array(
                'id'    => false,
                'name'  => false,
				'target_id' => ''
            ), $atts )
        );
        
        if(empty($id) && !empty($name)){
            // lookup by post name first
            $id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_type='view' AND post_name=%s", $name));
            if (!$id) {
                // try the post title
                $id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_type='view' AND post_title=%s", $name));
            }
        }
        
        if(empty($id)){
            return sprintf('<!- %s ->', __('View not found', 'wpv-views'));
        }
		
		$this->rendering_views_form_in_progress = true;
		
		$out = '';
		
        $view_settings = $this->get_view_settings($id);
	    if (isset($view_settings['filter_meta_html'])) {

			$url = get_permalink($target_id);
	        $out .= '<form action="' . $url . '" method="GET" class="wpv-filter-form"' . ">\n";
			
			// add hidden inputs for any url parameters.
			// We need these for when the form is submitted.
			$url_query = parse_url($url, PHP_URL_QUERY);
			if ($url_query != '') {
				$query_parts = explode('&', $url_query);
				foreach($query_parts as $param) {
					$item = explode('=', $param);
					if (strpos($item[0], 'wpv_') !== 0) {
						$out .= '<input id="wpv_param_' . $item[0] . '" type="hidden" name="' . $item[0] . '" value="' . $item[1] . '">' . "\n";
					}
				}
			}
			
			$meta_html = $view_settings['filter_meta_html'];
			
            if(preg_match('#\\[wpv-filter-controls\\](.*?)\\[\/wpv-filter-controls\\]#is', $meta_html, $matches)) {
				
				$out .= wpv_do_shortcode($matches[0]);
			
			}
			
			$out .= '</form>';
			
		}
		
		$this->rendering_views_form_in_progress = false;

        
        return $out;
        
    }
	
	function rendering_views_form() {
		return $this->rendering_views_form_in_progress;
	}
    
    function get_current_page() {
        return end($this->current_page);
    }
	
	function get_view_shortcodes_attributes() {
		return end($this->view_shortcode_attributes);
	}

    function get_top_current_page() {
        return $this->top_current_page;
    }

    /**
     * get the current view we are processing.
     *
     */
    
    function get_current_view() {
        return $this->current_view;
    }
    
    /**
     * get the current view count
     *
     */
    
    function get_view_count() {
		if (isset($this->set_view_counts[$this->current_view])) {
			return $this->set_view_counts[$this->current_view];
		} else {
			return $this->view_count;
		}
    }

    function set_view_count($count, $view_id) {
		if ($view_id) {
			$this->set_view_counts[$view_id] = $count;
		} else {
			$this->view_count = $count;
		}
    }
    
    /**
     * get the view settings for the current view
     * 
     * @param integer $view_id View post ID
     * @param $settings Additional forced settings
     */
    
    function get_view_settings($view_id = null, $settings = array()) {
        static $view_settings = array();
        
        if (is_null($view_id)) {
            $view_id = $this->get_current_view();
        }
        
        if (!isset($view_settings[$view_id])) {
            $view_settings[$view_id] = apply_filters('wpv_view_settings', (array)get_post_meta($view_id, '_wpv_settings', true), $view_id);
            if (!empty($settings) && is_array($settings)) {
                $view_settings[$view_id] = wpv_parse_args_recursive($settings, $view_settings[$view_id]);
            }
			
			// PATCH - v1.1 - add the default view mode to normal (not archive)
			if (!isset($view_settings[$view_id]['view-query-mode'])) {
				$view_settings[$view_id]['view-query-mode'] = 'normal';
			}
			
        }

        return $view_settings[$view_id];
    }

    /**
     * get the view layout settings for the current view
     *
     */
    
    function get_view_layout_settings() {
        static $view_layout_settings = array();
        
        if (!isset($view_layout_settings[$this->get_current_view()])) {
            $view_layout_settings[$this->get_current_view()] = (array)get_post_meta($this->get_current_view(), '_wpv_layout_settings', true);
        }
        
        return $view_layout_settings[$this->get_current_view()];
    }

    /**
     * Keep track of the current view and render the view.
     *
     */

    function render_view_ex($id, $hash){
        
        global $post;
		
        if ($this->top_current_page == null) {
            $this->top_current_page = isset($post) ? clone $post : null;
        }

        array_push($this->current_page, isset($post) ? clone $post : null);
        
        array_push($this->view_ids, $this->current_view);
		
        if (function_exists('icl_object_id')) {
            $id = icl_object_id($id, 'view', true);
        }
        $this->current_view = $id;
		
		array_push($this->post_query_stack, $this->post_query);
        
		// save original taxonomy term if any
		$tmp_parent_taxonomy = $this->parent_taxonomy;
		if (isset($this->taxonomy_data['term'])) {
			$this->parent_taxonomy = $this->taxonomy_data['term']->term_id;
		} else {
			$this->parent_taxonomy = 0;
		}
		$tmp_taxonomy_data = $this->taxonomy_data;

        $out =  $this->render_view($id, $hash);
        
        $out = wpv_do_shortcode($out);
        
		$this->taxonomy_data = $tmp_taxonomy_data;
		$this->parent_taxonomy = $tmp_parent_taxonomy;

        $this->current_view = array_pop($this->view_ids);
        if ($this->current_view == null) {
            $this->current_view = $id;
        }
        
        array_pop($this->current_page);
		
		$this->post_query = array_pop($this->post_query_stack);
        
        
        return $out;
    }

    /**
     * Render the view and loops through the found posts
     *
     */
    
    function render_view($view_id, $hash){
        
        global $post;
        global $wplogger;
        
        static $processed_views = array();

        // increment the view count.
        $this->view_count++;
        
        $view = get_post($view_id);
        
        $out = '';
        
        /*
        $css = get_post_meta($view_id, '_wpv_css', true);
        if ($css) {
            $out .= "<style type='text/css'>\n";
            $out .= $css;
            $out .= "\n</style>";
        }
        */

        $view_caller_id = (isset($post) && isset($post->ID)) ? get_the_ID() : 0; // post or widget
        
        if(!isset($processed_views[$view_caller_id][$hash]) || 0 === $view_caller_id){
            
            //$processed_views[$view_caller_id][$hash] = true; // mark view as processed for this post
            
            if(!empty($view)){

                $post_content = $view->post_content;
                
                // apply the layout meta html if we have some.
                $view_layout_settings = $this->get_view_layout_settings();
                
                if (isset($view_layout_settings['layout_meta_html'])) {
                    $post_content = str_replace('[wpv-layout-meta-html]', $view_layout_settings['layout_meta_html'], $post_content);
                }
				
				$view_settings = $this->get_view_settings();
                
                // find the loop
                
                if(preg_match('#\<wpv-loop(.*?)\>(.*)</wpv-loop>#is', $post_content, $matches)) {
                    // get the loop arguments.
                    $args = $matches[1];
                    $exp = array_map('trim', explode(' ', $args));
                    $args = array();
                    foreach($exp as $e){
                        $kv = explode('=', $e);
                        if (sizeof($kv) == 2) {
                            $args[$kv[0]] = trim($kv[1],'\'"');
                        }
                    }
                    if (isset($args['wrap'])) {
                        $args['wrap'] = intval($args['wrap']);
                    }
                    if (isset($args['pad'])) {
                        $args['pad'] = $args['pad'] == 'true';
                    } else {
                        $args['pad'] = false;
                    }
                    
                    $tmpl = $matches[2];
                    $item_indexes = $this->_get_item_indexes($tmpl);
                
					if ($view_settings['query_type'][0] == 'posts') {
						// get the posts using the query settings for this view.
						
						// check for an archive loop
						global $WPV_view_archive_loop;
						$archive_query = null;
						if (isset($WPV_view_archive_loop)) {
							$archive_query = $WPV_view_archive_loop->get_archive_loop_query();
						}
						
						if ($archive_query) {
							$this->post_query = $archive_query;
						} else {
							$this->post_query = wpv_filter_get_posts($view_id);
						}
						$items = $this->post_query->posts;

                        $wplogger->log('Found '. count($items) . ' posts');
                        
                        if ($wplogger->isMsgVisible(WPLOG_DEBUG)) {
                            
                            // simplify the output
                            $out_items = array();
                            foreach($items as $item) {
                                $out_items[] = array('ID' => $item->ID, 'post_title' => $item->post_title);
                            }
                            $wplogger->log($out_items, WPLOG_DEBUG);
                        }
					}

                    // save original post 
                    global $post, $authordata, $id;                                    
                    $tmp_post = isset($post) ? clone $post : null;
                    if ($authordata) {
                        $tmp_authordata = clone $authordata;
                    } else {
                        $tmp_authordata = null;
                    }
                    $tmp_id = $id;
					
					if ($view_settings['query_type'][0] == 'taxonomy') {
						$items = $this->taxonomy_query($view_settings);

                        $wplogger->log($items, WPLOG_DEBUG);

						// taxonomy views can be recursive so remove from
						// the processed array
			            //unset($processed_views[$view_caller_id][$hash]);
						
					}
				
                    $loop = '';
                    for($i = 0; $i < count($items); $i++){
                        
                        $index = $i;
                        if (isset($args['wrap'])) {
                            $index %= $args['wrap'];
                        }

                        $index++; // [wpv-item index=xx] uses base 1
                        $index = strval($index);

                        
						if ($view_settings['query_type'][0] == 'posts') {
							$post = clone $items[$i];
							$authordata = new WP_User($post->post_author);
							$id = $post->ID;
							$temp_variables = $this->variables;
							$this->variables = array();
							do_action('wpv-before-display-post', $post, $view_id);
						}
						if ($view_settings['query_type'][0] == 'taxonomy') {
							$this->taxonomy_data['term'] = $items[$i];
						}
						
                        // first output the "all" index.
                        $loop .= wpv_do_shortcode($item_indexes['all']);
                        
                        // Output each index we find
                        // otherwise output 'other'
                        if (isset($item_indexes[$index])) {
                            $loop .= wpv_do_shortcode($item_indexes[$index]);
                        } elseif (isset($item_indexes['other'])) {
                            $loop .= wpv_do_shortcode($item_indexes['other']);
                        }
						
						if ($view_settings['query_type'][0] == 'posts') {
							do_action('wpv-after-display-post', $post, $view_id);
							$this->variables = $temp_variables;
						}
						
                        
                    }
					
					// see if we should pad the remaining items.
					if (isset($args['wrap']) && isset($args['pad'])) {
						while (($i % $args['wrap']) && $args['pad']) {
	                        $index = $i;
                            $index %= $args['wrap'];
							
							if($index == $args['wrap'] - 1) {
		                        $loop .= wpv_do_shortcode($item_indexes['pad-last']);
							} else {
		                        $loop .= wpv_do_shortcode($item_indexes['pad']);
							}
							
							$i++;
						}
					}
					
                    $out .= str_replace($matches[0], $loop, $post_content);
                    
                    $post = isset($tmp_post) ? clone $tmp_post : null; // restore original $post
                    if ($tmp_authordata) {
                        $authordata = clone $tmp_authordata;
                    } else {
                        $authordata = null;
                    }
                    $id = $tmp_id;
					
                }
            }else{
                $out .= sprintf('<!- %s ->', __('View not found', 'wpv-views'));
            }
            
        }else{
            
            if($processed_views[$view_caller_id][$hash] !== true){
                $out .= $processed_views[$view_caller_id][$hash]; // use output from cache
            }
            
        }
    
        return $out;
    }
    
    /**
     * Get the html for each of the wpv-item index.
     *
     * <wpv-loop wrap=8 pad=true>
     * Output for all items
     * [wpv-item index=1]
     * Output for item 1
     * [wpv-item index=4]
     * Output for item 4
     * [wpv-item index=8]
     * Output for item 8
     * [wpv-item index=others]
     * Output for other items
     * [wpv-item index=pad]
     * Output for when padding is required
     * [wpv-item index=pad-last]
     * Output for the last item when padding is required
     * </wpv-loop>
     *
     * Will return an array with the output for each index
     *
     * eg array('all' => 'Output for all items',
     *          '1' => 'Output for item 1',
     *          '4' => 'Output for item 4',
     *          '8' => 'Output for item 8',
     *          'other' => 'Output for other items',
     *          )
     *
     */
    
    function _get_item_indexes($template) {
        $indexes = array();
        $indexes['all'] = '';
        $indexes['pad'] = '';
        $indexes['pad-last'] = '';
        
        // search for the [wpv-item index=xx] shortcode
        $found = false;
        $last_index = -1;
        
        while(preg_match('#\\[wpv-item index=([^\[]+)\]#is', $template, $matches)) {
            $pos = strpos($template, $matches[0]);
            
            if (!$found) {
                
                // found the first one.
                // use all the stuff before for the all index.
                
                $indexes['all'] = substr($template, 0, $pos);
                $found = true;
            } else 
            
            if ($last_index != -1) {
                // All the stuff before belongs to the previous index
                $indexes[$last_index] = substr($template, 0, $pos);
            }
            
            $template = substr($template, $pos + strlen($matches[0]));
            
            $last_index = $matches[1];
            
        }
        
        if (!$found) {
            $indexes['all'] = $template;
        } else {
            $indexes[$last_index] = $template;
        }
        
        return $indexes;
    }
 
    /**
     * get the current post query
     *
     */
    
    function get_query() {
        return $this->post_query;
    }
    
    /**
     * Add the view button to the toolbar of required edit pages.
     *
     * Also force the view editor to be in HTML mode.
     *
     */
    
    function post_edit_tinymce() {
        global $post;
        
        if ($post->post_type != 'view-template') {
            $this->editor_addon = new Editor_addon('wpv-views',
                                                   __('Insert Views Shortcode', 'wpv-views'),
                                                   WPV_URL . '/res/js/views_editor_plugin.js',
                                                   WPV_URL . '/res/img/bw_icon16.png');
        }
        
        if ($post->post_type == 'view') {
                
            add_short_codes_to_js(array('post'), $this->editor_addon);
        }
        
        if ($post->post_type != 'view' && $post->post_type != 'view-template') {
            
            // add tool bar to other edit pages so they can insert the view shortcodes.
            add_short_codes_to_js(array('view', 'view-form', 'wpml'), $this->editor_addon, 'add-basics');
        }
        
    }
    
    /**
     * Get all the views that have been created.
     *
     */
    
    function get_views(){
        $views = get_posts(array(
            'post_type'         => 'view',
            'post_status'       => 'publish',
			'numberposts'		=> -1
        ));        
        return $views;
    }

	function get_view_titles() {
        global $wpdb;
        
        static $views_available = null;
		
		if ($views_available === null) {

			$views_available = array();			
			$views = $wpdb->get_results("SELECT ID, post_title, post_name FROM {$wpdb->posts} WHERE post_type='view'");
			foreach ($views as $view) {
				$views_available[$view->ID] = $view->post_title;
			}
		}
		return $views_available;
	}
        

    /**
     * Add css required when editing a view
     *
     */
    
    function add_css() {
        global $post;
        if ($post->post_type == 'view') {
            add_thickbox();
        }
    }
    
    /**
     * Add the css required when rendering a view to the front end
     *
     */
    
    function add_render_css() {
        wp_enqueue_style('wpv_render_css', WPV_URL . '/res/css/wpv-views-sorting.css');
    }
    
    /**
     * Add the javascript files when editing a "view" post type.
     *
     */
      
    function add_js() {
        global $post;
        if ($post->post_type == 'view') {
            wpv_filter_add_js();
            add_views_layout_js();
            
        }
    }
    
    
    /**
     * Called when adding a filter to the view query
     *
     * This function will return the html elements for the type of
     * query that is being added
     *
     */
 
    function ajax_get_table_row_ui() {
        
        if (wp_verify_nonce($_POST['wpv_nonce'], 'wpv_get_table_row_ui_nonce')) {
            $type = apply_filters('wpv_get_table_row_ui_type', $_POST['type_data']);
            
            $checkboxes = array();
            if (isset($_POST['checkboxes'])) {
                $checkboxes = $_POST['checkboxes'];
            }
            
            echo call_user_func('wpv_get_table_row_ui_' . $type, $_POST['row'], $_POST['type_data'], $checkboxes, array());
        }
        
        die();
    }
 
    /**
     * get all the meta keys used in all the posts
     *
     * returns an array
     */
    
    function get_meta_keys($include_hidden = false) {
        global $wpdb;
        static $cf_keys = null;
        
        if ($cf_keys == null) {
            // get the custom field keys
            $cf_keys_limit = 10000; // jic
            $cf_keys = $wpdb->get_col( "
                SELECT meta_key
                FROM $wpdb->postmeta
                GROUP BY meta_key
                ORDER BY meta_key
                LIMIT $cf_keys_limit" );
    
            if (function_exists('wpcf_get_post_meta_field_names')) {
                $types_fields = wpcf_get_post_meta_field_names();
                foreach($types_fields as $field) {
                    if (!in_array($field, $cf_keys)) {
                        $cf_keys[] = $field;
                    }
                }
            }
            
            // exclude these keys.        
            $cf_keys_exceptions = array('_edit_last', '_edit_lock', '_wp_page_template', '_wp_attachment_metadata', '_icl_translator_note', '_alp_processed',
                                        '_icl_translation', '_thumbnail_id', '_views_template', '_wpml_media_duplicate', '_wpml_media_featured',
                                        '_top_nav_excluded', '_cms_nav_minihome',
                                        'wpml_media_duplicate_of', 'wpml_media_lang', 'wpml_media_processed',
                                        '_wpv_settings', '_wpv_layout_settings', '_wpv_view_sync',
										'_wpv_view_template_fields', '_wpv_view_template_mode' );
            
            $cf_keys = array_diff($cf_keys, $cf_keys_exceptions);
            
			if (!$include_hidden) {
				
				$options = $this->get_options();
				if (isset($options['wpv_show_hidden_fields'])) {
					$include_these_hidden = explode(',', $options['wpv_show_hidden_fields']);
				} else {
					$include_these_hidden = array();
				}
				// exclude hidden fields (starting with an underscore)
				foreach ($cf_keys as $index => $field) {
					if (strpos($field, '_') === 0) {
						if (!in_array($field, $include_these_hidden)) {
							unset($cf_keys[$index]);
						}
					}
				}
			}
			
            if ( $cf_keys ) {
                natcasesort($cf_keys);
            }
            
            
        }
        
        return $cf_keys;
    }
 
	/**
	 * If the post has a view
	 * add an view edit link to post.
	 */
	
	function edit_post_link($link, $post_id) {
        
        // do nothing for theme version.

		return $link;
	}
    
    /**
     * Saves View editor height
     */
    function save_editor_height() {
        if (isset($_POST['height'])) {
            $type = isset($_POST['type']) ? $_POST['type'] : 'view';
            setcookie('wpv_views_editor_height_' . strval($type), intval($_POST['height']), time() + 60*60*24*30, COOKIEPATH, COOKIE_DOMAIN);
        }
    }
    
    /**
     * Sets View editor height
     */
    function set_editor_height() {
        $post_type = get_post_type();
        if (in_array($post_type, array('view', 'view-template'))) {
            add_action('admin_footer', array($this, 'editor_height_js'));
        }
    }
    
    function editor_height_js() {
        echo '
<script type="text/javascript">
//<![CDATA[
function wpv_views_editor_resize_init() {
        jQuery("#editorcontainer").resizable({
            handles: "s",
            alsoResize: "#content",
            stop: function(event, ui) { 
                jQuery.post(ajaxurl, {
                    action: "wpv_views_editor_height",
                    height: jQuery(this).height(),
                    type: "' . get_post_type() . '"
                });
                jQuery(this).css("width", "100%").find("#content").css("width", "100%");
            }';
        if (isset($_COOKIE['wpv_views_editor_height_' . get_post_type()])) {
            $height = intval($_COOKIE['wpv_views_editor_height_' . get_post_type()]);
            if ($height < 200) {
                $height = 200;
            }
            echo ',
                    create: function(event, ui) {
                        jQuery("#editorcontainer, #content").css("height", "' . $height . 'px").height(' . $height . ');
                    }';
        }
        echo '
        });
    }';
echo '
jQuery(document).ready(function(){
    var timeoutWpvViewsEditorResize = window.setTimeout("wpv_views_editor_resize_init()", 1000);
});

//]]>
</script>
';
    }
       
    
    function get_options() {
        if ($this->options !== null) {
            return $this->options;
        }
        $this->options = get_option('wpv_options');
        if (!$this->options) {
            $this->options = array();
        }
        
        return $this->options;
    }
    
    function save_options($options) {
        update_option('wpv_options', $options);
        $this->options = $options;
    }
    
    /**
    * Adds help on admin pages.
    * 
    * @param type $contextual_help
    * @param type $screen_id
    * @param type $screen
    * @return type 
    */
    function admin_plugin_help($contextual_help, $screen_id, $screen) {
        return $contextual_help;
    }
    
    function is_embedded() {
        return true;
    }

    function convert_ids_to_names_in_settings($settings) {
        global $wpdb;
        
        if (isset($settings['post_type'])) {
            $settings['post_types'] = $settings['post_type'];
            unset($settings['post_type']);
            foreach($settings['post_types'] as $key => $value) {
                $settings['post_types']['post_type-' . $key] = $value;
                unset($settings['post_types'][$key]);
            }
            $settings['post_types']['__key'] = 'post_type';
        }
        
        if (isset($settings['post_status'])) {
            $settings['post_statuses'] = $settings['post_status'];
            unset($settings['post_status']);
            foreach($settings['post_statuses'] as $key => $value) {
                $settings['post_statuses']['post_status-' . $key] = $value;
                unset($settings['post_statuses'][$key]);
            }
            $settings['post_statuses']['__key'] = 'post_status';
        }
        
        if (isset($settings['parent_id']) && $settings['parent_id'] != '') {
            $parent_name = $wpdb->get_var("SELECT post_name FROM {$wpdb->posts} WHERE ID = " . $settings['parent_id']);
            $settings['parent_id'] = $parent_name;
        }
        
        if (isset($settings['post_relationship_id']) && $settings['post_relationship_id'] != '') {
            $parent_name = $wpdb->get_var("SELECT post_name FROM {$wpdb->posts} WHERE ID = " . $settings['post_relationship_id']);
            $settings['post_relationship_id'] = $parent_name;
        }
        
        if (isset($settings['post_relationship_mode'][0])) {
            $settings['post_relationship_mode'] = $settings['post_relationship_mode'][0];
        }
        
        if (isset($settings['parent_mode'])) {
            $settings['parent_mode'] = $settings['parent_mode'][0];
        }
        
	    $taxonomies = get_taxonomies('', 'objects');
		foreach ($taxonomies as $category_slug => $category) {
			$save_name = ( $category->name == 'category' ) ? 'post_category' : 'tax_input_' . $category->name;

            if (isset($settings[$save_name])) {
                foreach($settings[$save_name] as $key => $id) {
                    $term = get_term_by('id', $id, $category->name);
                    if ($term) {
                        $settings[$save_name]['cat-' . $key] = $term->name;
                    }
                    unset($settings[$save_name][$key]);
                }
                $settings[$save_name]['__key'] = 'cat';
            }
            // Use this to check attribute-url-format
            $attribute_url_format = 'taxonomy-' . $category->name . '-attribute-url-format';
            if (isset($settings[$attribute_url_format][0])) {
                $settings[$attribute_url_format] = $settings[$attribute_url_format][0];
            }
        }
        
        if(isset($settings['pagination'][0])) {
            $settings['pagination']['state'] = $settings['pagination'][0];
            unset($settings['pagination'][0]);
        }
        
        if(isset($settings['ajax_pagination'][0])) {
            $settings['ajax_pagination']['state'] = $settings['ajax_pagination'][0];
            unset($settings['ajax_pagination'][0]);
        }

        if(isset($settings['query_type'][0])) {
            $settings['query_type']['state'] = $settings['query_type'][0];
            unset($settings['query_type'][0]);
        }

        if (isset($settings['taxonomy_type'])) {
            $settings['taxonomy_types'] = $settings['taxonomy_type'];
            unset($settings['taxonomy_type']);
            foreach($settings['taxonomy_types'] as $key => $value) {
                $settings['taxonomy_types']['taxonomy_type-' . $key] = $value;
                unset($settings['taxonomy_types'][$key]);
            }
            $settings['taxonomy_types']['__key'] = 'taxonomy_type';
        }
        


        // Fix the taxonomy_terms keys
        if (isset($settings['taxonomy_terms'])) {
            foreach($settings['taxonomy_terms'] as $key => $value) {
                $settings['taxonomy_terms']['taxonomy_term-' . $key] = $value;
                unset($settings['taxonomy_terms'][$key]);
            }
            $settings['taxonomy_terms']['__key'] = 'taxonomy_term';
        }

		// fix filter control settings so arrays get output correctly.
		$filter_control_settings = array('filter_controls_enable',
										 'filter_controls_param',
										 'filter_controls_mode',
										 'filter_controls_field_name',
										 'filter_controls_label',
										 'filter_controls_type',
										 'filter_controls_values'
										 );
		
		foreach($filter_control_settings as $filter_control) {
			if (isset($settings[$filter_control])) {
				$settings[$filter_control]['__key'] = $filter_control;
			}							
		}
	
        return $settings;
    }

    function convert_ids_to_names_in_layout_settings($settings) {
        global $wpdb;
        
		if (isset($settings['fields'])) {
			foreach($settings['fields'] as $key => $value) {
				if (substr($key, 0, 5) == 'name_') {
					if (substr($value, 0, 13) == 'wpv-post-body') {
						// use the view template name instead of the id
						$parts = explode(' ', $value);
						if (sizeof($parts) == 2) {
							$view_template_id = $parts[1];
							$view_template_name = $wpdb->get_var("SELECT post_name FROM {$wpdb->posts} WHERE ID = " . $view_template_id);
							if ($view_template_name) {
								$settings['fields'][$key] = 'wpv-post-body ' . $view_template_name;
							}
						}
					}
				}
			}
			
        }
        return $settings;
    }

    function convert_names_to_ids_in_settings($settings) {
        global $wpdb;
        
        if (isset($settings['post_types'])) {
            $settings['post_type'] = $settings['post_types'];
            unset($settings['post_types']);
            if(is_array($settings['post_type']['post_type'])) {
                $settings['post_type'] = $settings['post_type']['post_type'];
            } else {
                $settings['post_type'][0] = $settings['post_type']['post_type'];
                unset($settings['post_type']['post_type']);
            }
        }
        
        if (isset($settings['post_statuses'])) {
            $settings['post_status'] = $settings['post_statuses'];
            unset($settings['post_statuses']);
            if(is_array($settings['post_status']['post_status'])) {
                $settings['post_status'] = $settings['post_status']['post_status'];
            } else {
                $settings['post_status'][0] = $settings['post_status']['post_status'];
                unset($settings['post_status']['post_status']);
            }
        }
        
        if (isset($settings['parent_id']) && $settings['parent_id'] != '') {
            $parent_id = $wpdb->get_var("SELECT ID FROM {$wpdb->posts} WHERE post_name = '{$settings['parent_id']}'");
            $settings['parent_id'] = $parent_id;
        }
 
        if (isset($settings['parent_mode'])) {
            $settings['parent_mode'] = array($settings['parent_mode']);
        }
        
        if (isset($settings['post_relationship_mode'])) {
            $settings['post_relationship_mode'] = array($settings['post_relationship_mode']);
        }
        
        if (isset($settings['post_relationship_id'])) {
            $parent_id = $wpdb->get_var("SELECT ID FROM {$wpdb->posts} WHERE post_name = '{$settings['post_relationship_id']}'");
            $settings['post_relationship_id'] = $parent_id;
        }
        
	    $taxonomies = get_taxonomies('', 'objects');
		foreach ($taxonomies as $category_slug => $category) {
			$save_name = ( $category->name == 'category' ) ? 'post_category' : 'tax_input_' . $category->name;

            if (isset($settings[$save_name])) {
                if(is_array($settings[$save_name]['cat'])) {
                    $settings[$save_name] = $settings[$save_name]['cat'];
                } else {
                    $settings[$save_name][0] = $settings[$save_name]['cat'];;
                    unset($settings[$save_name]['cat']);
                }
                
                foreach($settings[$save_name] as $key => $name) {
                    $term = get_term_by('name', $name, $category->name);
                    if ($term) {
                        $settings[$save_name][$key] = $term->term_id;
                    }
                }
            }
            
            // Use this to check attribute-url-format
            $attribute_url_format = 'taxonomy-' . $category->name . '-attribute-url-format';
            if (isset($settings[$attribute_url_format])) {
                $settings[$attribute_url_format] = array($settings[$attribute_url_format]);
            }
        
        }        

        if(isset($settings['pagination']['state'])) {
            $settings['pagination'][0] = $settings['pagination']['state'];
            unset($settings['pagination']['state']);
        }
        
        if(isset($settings['ajax_pagination']['state'])) {
            $settings['ajax_pagination'][0] = $settings['ajax_pagination']['state'];
            unset($settings['ajax_pagination']['state']);
        }

        if(isset($settings['query_type']['state'])) {
            $settings['query_type'][0] = $settings['query_type']['state'];
            unset($settings['query_type']['state']);
        } else {
            $settings['query_type'][0] = 'posts';
        }
        

        if (isset($settings['taxonomy_types'])) {
            $settings['taxonomy_type'] = $settings['taxonomy_types'];
            unset($settings['taxonomy_types']);
            if(is_array($settings['taxonomy_type']['taxonomy_type'])) {
                $settings['taxonomy_type'] = $settings['taxonomy_type']['taxonomy_type'];
            } else {
                $settings['taxonomy_type'][0] = $settings['taxonomy_type']['taxonomy_type'];
                unset($settings['taxonomy_type']['taxonomy_type']);
            }
        }
        
        if (isset($settings['taxonomy_terms'])) {
            if(is_array($settings['taxonomy_terms']['taxonomy_term'])) {
                $settings['taxonomy_terms'] = $settings['taxonomy_terms']['taxonomy_term'];
            } else {
                $settings['taxonomy_terms'][0] = $settings['taxonomy_terms']['taxonomy_term'];
                unset($settings['taxonomy_terms']['taxonomy_term']);
            }
        }
        
		// fix filter control settings
		$filter_control_settings = array('filter_controls_enable',
										 'filter_controls_param',
										 'filter_controls_mode',
										 'filter_controls_field_name',
										 'filter_controls_label',
										 'filter_controls_type',
										 'filter_controls_values'
										 );
		
		foreach($filter_control_settings as $filter_control) {
			if (isset($settings[$filter_control][$filter_control])) {
				$settings[$filter_control] = $settings[$filter_control][$filter_control];
			}							
		}
        
        return $settings;
    }

    function convert_names_to_ids_in_layout_settings($settings) {
        global $wpdb;
        
        if (isset($settings['fields'])) {
            foreach($settings['fields'] as $key => $value) {
                if (substr($key, 0, 5) == 'name_') {
                    if (substr($value, 0, 13) == 'wpv-post-body') {
                        // use the view template id instead of the name
                        $parts = explode(' ', $value);
                        if (sizeof($parts) == 2) {
                            $view_template_name = $parts[1];
                            $view_template_id = $wpdb->get_var("SELECT ID FROM {$wpdb->posts} WHERE post_type = 'view-template' AND post_name = '{$view_template_name}'");
                            if ($view_template_id) {
                                $settings['fields'][$key] = 'wpv-post-body ' . $view_template_id;
                            }
                        }
                    }
                    if (!isset($settings['fields']['suffix_' + substr($key, 5)])) {
                        $settings['fields']['suffix_' . substr($key, 5)] = '';
                    }
                    if (!isset($settings['fields']['prefix_' + substr($key, 5)])) {
                        $settings['fields']['prefix_' . substr($key, 5)] = '';
                    }
                }
                
            }
        }
        return $settings;
    }
    
    function wpv_dismiss_message() {
        if (wp_verify_nonce($_REQUEST['wpv_nonce'], 'wpv-dismiss-message')) {
            $dismissed = get_option('views_dismissed_messages', array());
            $dismissed[] = $_REQUEST['id'];
            update_option('views_dismissed_messages', $dismissed);
        }
        wp_redirect(admin_url());
    }

    // Localization
    function plugin_localization(){
        $locale = get_locale();
        load_textdomain( 'wpv-views', WPV_PATH_EMBEDDED . '/locale/views-' . $locale . '.mo');
    }
	
	function get_current_taxonomy_term() {
		if (isset($this->taxonomy_data['term'])) {
			return $this->taxonomy_data['term'];
		} else {
			return null;
		}
	}

	function taxonomy_query($view_settings) {
		$items = get_taxonomy_query($view_settings);
		
		$this->taxonomy_data['item_count'] = sizeof($items);
		
		if ($view_settings['pagination'][0] == 'disable') {
			$this->taxonomy_data['max_num_pages'] = 1;
		} else {
			// calculate the number of pages.
			$posts_per_page = $view_settings['posts_per_page'];
			if (isset($view_settings['pagination']['mode']) && $view_settings['pagination']['mode'] == 'rollover') {
				$posts_per_page = $view_settings['rollover']['posts_per_page'];
			}
		
			$this->taxonomy_data['max_num_pages'] = ceil($this->taxonomy_data['item_count'] / $posts_per_page);
			
			if ($this->taxonomy_data['item_count'] > $posts_per_page) {
				// return the paged result
				
				$page = 1;
				if (isset($_GET['wpv_paged'])) {
					$page = $_GET['wpv_paged'];
				}
				
				$this->taxonomy_data['page_number'] = $page;
				
				// only return 1 page of items.
				$items = array_slice($items, ($page - 1) * $posts_per_page, $posts_per_page);
				
			}			
		}
		return $items;
	}
	
	function get_current_page_number() {
		if ($this->post_query) {
			return intval($this->post_query->query_vars['paged']);
		} else {
			// Taxonomy query
			if (isset($this->taxonomy_data['page_number'])) {
				return $this->taxonomy_data['page_number'];
			}
		}
		
		return 1;
	}
	
	function get_max_pages() {
		if ($this->post_query) {
			return $this->post_query->max_num_pages;
		} else {
			// Taxonomy query
			if (isset($this->taxonomy_data['max_num_pages'])) {
				return $this->taxonomy_data['max_num_pages'];
			}
		}
		
		return 1;
	}
	
	function get_taxonomy_found_count() {
		if (isset($this->taxonomy_data['item_count'])) {
			return $this->taxonomy_data['item_count'];
		} else {
			return 0;
		}
	}
	
	function get_parent_view_taxonomy() {
		return $this->parent_taxonomy;
	}

	function get_taxonomy_view_select_box($row, $view_selected) {
		global $wpdb;
		
        $views_available = $wpdb->get_results("SELECT ID, post_title FROM {$wpdb->posts} WHERE post_type='view' AND post_status in ('publish')");

        $taxonomy_view = '';
		if ($row === '') {
			$taxonomy_view .= '<select class="taxonomy_view_select" name="taxonomy_view" id="taxonomy_view">';
		} else {
			$taxonomy_view .= '<select class="taxonomy_view_select" name="taxonomy_view_' . $row . '" id="taxonomy_view_' . $row . '">';
		}

        foreach($views_available as $view) {
            if ($view_selected == $view->ID)
                $selected = ' selected="selected"';
            else
                $selected = '';
            
            $view_settings = get_post_meta($view->ID, '_wpv_settings', true);
			$title = $view->post_title . ' - ' . __('Post View', 'wpv-views');
			if (isset($view_settings['query_type'][0]) && $view_settings['query_type'][0] == 'taxonomy') {
				$title = $view->post_title . ' - ' . __('Taxonomy View', 'wpv-views');
			}
			
            $taxonomy_view .= '<option value="' . $view->ID . '"' . $selected . '>' . $title . '</option>';
        }
        $taxonomy_view .= '</select>';
        
        return $taxonomy_view;
	}
    
    function set_widget_view_id($id) {
        $this->widget_view_id = $id;
    }
    
    function get_widget_view_id() {
        return $this->widget_view_id;
    }
	
	function set_variable($name, $value) {
		$this->variables[$name] = $value;
	}
	
	function get_variable($name) {
        if (strpos($name, '$') === 0) {
			$name = substr($name, 1);
			
			if (isset($this->variables[$name])) {
				return $this->variables[$name];
			}
		}
		return null;
	}
	
	function get_view_shortcode_params($view_id) {
		$settings = $this->get_view_settings($view_id);
		
		$params = wpv_get_custom_field_view_params($settings);
		$params = array_merge($params, wpv_get_taxonomy_view_params($settings));
		
		return $params;
	}
	
	function hide_view_template_author() {
		
	}
	
	/**
	 * See if a view has any enbabled from controls
	 *
	 */
	
	function does_view_have_form_controls($view_id) {
		$view_settings = $this->get_view_settings($view_id);
		
		if (isset($view_settings['filter_controls_enable']) && is_array($view_settings['filter_controls_enable'])) {
			foreach($view_settings['filter_controls_enable'] as $enable) {
				if ($enable) {
					return true;
				}
			}
		}
		
		return false;
	}

	/**
	 *	See if a view is used for an archive
	 *
	 */
	
	function is_archive_view($view_id) {
		$view_settings = $this->get_view_settings($view_id);

		return $view_settings['view-query-mode'] == 'archive';
	}
	
	function wpv_format_date() {

		$date_format = $_POST['date-format'];
		if ($date_format == '') {
			$date_format = get_option('date_format');
		}
		
		$date = $_POST['date'];
		$date = mktime(0, 0, 0, substr($date, 2, 2), substr($date, 0, 2), substr($date, 4, 4));
		
		echo json_encode(array('display' => date($date_format, intval($date)),
							   'timestamp' => $date));
		die();
		
		
	}
	
}
/**
 * render_view
 *
 * Renders a view and returns the result
 *
 * $args is an array. You can pass one of these keys:
 *
 * 'name' => The View post_name
 * 'title' => The View post_title
 * 'id' => The View post ID
 *
 *	Example:  <?php echo render_view(array('title' => 'Top pages')); ?>
 * 
 */

function render_view($args) {

    global $wpdb, $WP_Views;
    
    $id = 0;
    
    if (isset($args['id'])) {
        $id = $args['id'];
    } elseif (isset($args['name'])) {
        $id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_type='view' AND post_name=%s", $args['name']));
    } elseif (isset($args['title'])) {
        $id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_type='view' AND post_title=%s", $args['title']));
    }
    
    if ($id) {

		$args['id'] = $id;
		array_push($WP_Views->view_shortcode_attributes, $args);
		
        $out = $WP_Views->render_view_ex($id, md5(serialize($args)));
        
		array_pop($WP_Views->view_shortcode_attributes);
        
        return $out;
    } else {
        return '';
    }
}

function wpv_views_plugin_activate() {
    add_option('wpv_views_plugin_do_activation_redirect', true);
}

function wpv_views_plugin_deactivate() {
    delete_option('wpv_views_plugin_do_activation_redirect', true);
}

function wpv_views_plugin_redirect() {
    if (get_option('wpv_views_plugin_do_activation_redirect', false)) {
        delete_option('wpv_views_plugin_do_activation_redirect');
        wp_redirect(admin_url() . 'edit.php?post_type=view&page='. WPV_FOLDER .'/menu/help.php');
        exit;
    }
}

function wpv_views_plugin_action_links($links, $file) {
    $this_plugin = basename(WPV_PATH) . '/wp-views.php';
    if($file == $this_plugin) {
        $links[] = '<a href="admin.php?page='.basename(WPV_PATH).'/menu/help.php">' . __('Getting started', 'wpv-views') . '</a>';
    }
    return $links;
}
