<?php

require WPV_PATH_EMBEDDED . '/inc/wpv.class.php';

class WP_Views_plugin extends WP_Views {
    
    function init() {
        add_filter( 'custom_menu_order', array($this, 'enable_custom_menu_order' ));
        add_filter( 'menu_order', array($this, 'custom_menu_order' ));

        global $wp_version;
        if (version_compare($wp_version, '3.3', '>=')) {
            add_action('admin_head-edit.php', array($this, 'admin_add_help'));
            add_action('admin_head-post.php', array($this, 'admin_add_help'));
            add_action('admin_head-post-new.php', array($this, 'admin_add_help'));
        }
        parent::init();
		
        add_action('wp_ajax_wpv_get_types_field_name', array($this, 'wpv_ajax_wpv_get_types_field_name'));
        add_action('wp_ajax_wpv_get_taxonomy_name', array($this, 'wpv_ajax_wpv_get_taxonomy_name'));
		
        if(is_admin()){
            add_action('admin_print_scripts', array($this,'add_views_settings_js'));
		}
		
		
    }

    function enable_custom_menu_order($menu_ord) {
        return true;
    }
    
    function custom_menu_order( $menu_ord ) {
        $types_index = array_search('wpcf', $menu_ord);
        $views_index = array_search('edit.php?post_type=view', $menu_ord);
        
        if ($types_index !== false && $views_index !== false) {
            // put the types menu above the views menu.
            unset($menu_ord[$types_index]);
            $menu_ord = array_values($menu_ord);
            array_splice($menu_ord, $views_index, 0, 'wpcf');
        }
        
        return $menu_ord;
    }
    
    function is_embedded() {
        return false;
    }
    
    function wpv_register_type_view() 
    {
      $labels = array(
        'name' => _x('Views', 'post type general name'),
        'singular_name' => _x('View', 'post type singular name'),
        'add_new' => _x('Add New View', 'book'),
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
        'show_ui' => true, 
        'show_in_menu' => false, 
        'query_var' => false,
        'rewrite' => false,
        'capability_type' => 'post',
        'can_export' => false,
        'has_archive' => false, 
        'hierarchical' => false,
        //'menu_position' => 80,
        'menu_icon' => WPV_URL .'/res/img/views-18.png',
        'supports' => array('title','editor','author')
      ); 
      register_post_type('view',$args);
    }

    function admin_menu(){

		add_utility_page(__('Views', 'wpv-views'), __('Views', 'wpv-views'), 'manage_options', 'edit.php?post_type=view', '', WPV_URL .'/res/img/views-18.png');

        // remove the default menus and then add a Help menu
        remove_submenu_page('edit.php?post_type=view', 'edit.php?post_type=view');
        remove_submenu_page('edit.php?post_type=view', 'post-new.php?post_type=view');
        
                // Add the default menus after the Help menu
        add_submenu_page('edit.php?post_type=view', __('Views', 'wpv-views'), __('Views', 'wpv-views'), 'manage_options', 'edit.php?post_type=view');
        add_submenu_page('edit.php?post_type=view', __('New View', 'wpv-views'), __('New View', 'wpv-views'), 'manage_options', 'post-new.php?post_type=view');

        // Add the view template menus.        
        add_submenu_page('edit.php?post_type=view', __('View Templates', 'wpv-views'), __('View Templates', 'wpv-views'), 'manage_options', 'edit.php?post_type=view-template');
        add_submenu_page('edit.php?post_type=view', __('New View Template', 'wpv-views'), __('New View Template', 'wpv-views'), 'manage_options', 'post-new.php?post_type=view-template');

        // add settings menu.        
        add_submenu_page('edit.php?post_type=view', __('Settings', 'wpv-views'), __('Settings', 'wpv-views'), 'manage_options', 'views-settings',
                    array($this, 'views_settings_admin'));
        
        // Add import export menu.
        if (function_exists('wpv_admin_menu_import_export')) {
            add_submenu_page('edit.php?post_type=view', __('Import/Export', 'wpv-views'), __('Import/Export', 'wpv-views'), 'manage_options', 'views-import-export',
                    'wpv_admin_menu_import_export');

        }
        
        add_submenu_page('edit.php?post_type=view', __('Views Subscription','wp-wiews'), __('Views Subscription','wp-wiews'), 'manage_options', WPV_FOLDER . '/menu/main.php', null, WPV_URL . '/res/img/icon16.png');
        
        add_submenu_page('edit.php?post_type=view', __('Help', 'wpv-views'), __('Help', 'wpv-views'), 'manage_options', WPV_FOLDER . '/menu/help.php', null, WPV_URL . '/res/img/icon16.png');
    }

    function settings_box_load(){
        add_meta_box('wpv_settings', '<img src="' . WPV_URL . '/res/img/icon16.png">&nbsp;&nbsp;' . __('View Query - Choose what content to load', 'wpv-views'), array($this, 'settings_box'), 'view', 'normal', 'high');    
        add_meta_box('wpv_layout', '<img src="' . WPV_URL . '/res/img/icon16.png">&nbsp;&nbsp;' . __('View Layout - Edit the layout', 'wpv-views'), 'view_layout_box', 'view', 'normal', 'high');

        add_meta_box('wpv_views_help', '<img src="' . WPV_URL . '/res/img/icon16.png">&nbsp;&nbsp;' . __('Views Help', 'wpv-views'), array($this, 'view_help_box'), 'view', 'side', 'high');

        //add_meta_box('wpv_css', '<img src="' . WPV_URL . '/res/img/icon16.png">&nbsp;&nbsp;' . __('CSS for view', 'wpv-views'), array($this, 'css_box'), 'view', 'normal', 'high');    
        
        global $pagenow;
        if ($pagenow == 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] == 'view') {
            $this->include_admin_css();
        }
        if ($pagenow == 'options-general.php' && isset($_GET['page']) && $_GET['page'] == WPV_FOLDER . '/menu/main.php') {
            $this->include_admin_css();
        }
        if ($pagenow == 'options-general.php' && isset($_GET['page']) && $_GET['page'] == 'wpv-import-theme') {
            $this->include_admin_css();
        }
        

    }
    
    function hide_view_body_controls() {
        global $pagenow, $post;
        if (($pagenow == 'post-new.php' && isset($_GET['post_type']) && $_GET['post_type'] == 'view') ||
                ($pagenow == 'post.php' && isset($_GET['action']) && $_GET['action'] == 'edit')) {

            $post_type = $post->post_type;

            if($pagenow == 'post.php' && $post_type != 'view') {
                return;
            }
            // hide the post body.
            ?>
                <div id="wpv-customize-link" style="display:none;margin-bottom:15px">
                    <a href="#" onclick="wpv_show_post_body()"><?php _e('Fully customize the View HTML output', 'wpv-views'); ?></a>
                </div>
                
                <div id="wpv-learn-about-views-editing" style="display:none;margin-bottom:15px">
                    <?php printf(__('Learn about %sediting Views HTML%s', 'wpv-views'),
                                 '<a href="http://wp-types.com/documentation/user-guides/digging-into-view-outputs/" target="_blank">',
                                 ' &raquo;</a>'); ?>
                    <input class="button-secondary" type="button" value="<?php echo __('Hide this editor', 'wpv-views'); ?>" onclick="wpv_hide_post_body()">
                </div>

                <script type="text/javascript">
                    jQuery('#postdivrich').hide();
                    jQuery('#wpv-learn-about-views-editing').insertAfter('#postdivrich');
                </script>


            <?php
            
            // hide the author as well.
            ?>            
                <script type="text/javascript">
                    jQuery('#authordiv').hide();
                </script>
            <?php

            // add a note about saving changes
            ?>
                <div id="wpv-save-changes" class="wpv_form_notice" style="display:none;margin-top:10px;width:95%">
                    <?php _e('* This View has changed. You need to save for these changes to take effect.', 'wpv-views'); ?>
                </div>
                
            <?php
                
            
        }
        
    }

    /**
     * Output the view query metabox on the view edit page.
     *
     */
    
    function settings_box($post){
        
        global $WPV_view_archive_loop;
        
        ?>
        <div id="wpv_view_query_controls" style="position: relative">
            <span id="wpv_view_query_controls_over" class="wpv_view_overlay" style="display:none">
                <p><strong><?php echo __('The view query settings will be copied from the original', 'wpv-views'); ?></strong></p>
            </span>
            <?php
    
            global $wp_version, $pagenow;        
            if (version_compare($wp_version, '3.2', '<')) {
                echo '<p style="color:red;"><strong>';
                _e('* Requires WordPress 3.2 or greater for best results.', 'wpv-views');
                echo '</strong></p>';
            }
            
            $this->include_admin_css();
            
            wp_nonce_field( 'wpv_get_table_row_ui_nonce', 'wpv_get_table_row_ui_nonce');
    
            ?>        
            <script type="text/javascript">
        
                var wpv_confirm_filter_change = '<?php _e("Are you sure you want to change the filter?\\n\\nIt appears that you made modifications to the filter.", 'wpv-views'); ?>';
                <?php if ($pagenow == 'post-new.php'): ?>
                    jQuery(document).ready(function($){
                       wpv_add_initial_filter_shortcode(); 
                    });
                <?php endif; ?>
                
                var wpv_save_button_text = '<?php _e("Save View", 'wpv-views'); ?>';
            </script>
            
            <?php
            
            global $WP_Views;
            $view_settings = $WP_Views->get_view_settings($post->ID);
            
            // check for creating a new view for an archive loop.
            
            if (isset($_GET['view_archive']) || isset($_GET['view_archive_taxonomy'])) {
                $view_settings['view-query-mode'] = 'archive';
                global $wpv_wp_pointer;
                $wpv_wp_pointer->add_pointer('View Layout',
                                             'This View displays results for an existing WordPress query.</p><p>Now choose the layout style and then add the fields you wish to display.',
                                             'select[name="_wpv_layout_settings[style]"]',
                                             'bottom',
                                             'wpv_layout');

            }
            
            ?>
            
            <p><span style="font-size:1.1em;font-weight:bold;">Does this View query it's own data or replace a standard WordPress archive?</span>&nbsp;&nbsp;&nbsp;<img src="<?php echo WPV_URL_EMBEDDED; ?>/common/res/images/question.png" style="position:relative;top:2px;" />&nbsp;<a href="http://wp-types.com/documentation/user-guides/normal-vs-archive-views/" target="_blank"><?php _e('Learn about Normal and Archive Views &raquo;',
'wpv-views'); ?></a></p>
            <ul style="margin-bottom:10px">
                <?php $checked = $view_settings['view-query-mode'] == 'normal' ? 'checked="checked"' : ''; ?>
                <li><label><input type="radio" name="_wpv_settings[view-query-mode]" value="normal" <?php echo $checked; ?> onclick="jQuery('#wpv-normal-view-mode').show();jQuery('#wpv-archive-view-mode').hide()">&nbsp;<?php _e('<strong>Normal View:</strong> This View queries content from the database (good for inserting Views into content or widgets)', 'wpv-views'); ?></label></li>
                <?php $checked = $view_settings['view-query-mode'] == 'archive' ? 'checked="checked"' : ''; ?>
                <li><label><input type="radio" name="_wpv_settings[view-query-mode]" value="archive" <?php echo $checked; ?> onclick="jQuery('#wpv-normal-view-mode').hide();jQuery('#wpv-archive-view-mode').show()">&nbsp;<?php _e('<strong>Archive View:</strong> This View displays results for an existing WordPress query (good for archive pages, taxonomy listing, search, etc.)', 'wpv-views'); ?></label></li>
            </ul>
            
            <div id="wpv-normal-view-mode"<?php if($view_settings['view-query-mode'] != 'normal') {echo ' style="display:none;"';} ?>>
                <table id="wpv_filter_table" class="widefat fixed">
                    <thead>
                        <tr>
                            <th width="20px"></th>
                            <th width="100%"><?php _e('Filter', 'wpv-views'); ?></th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        <tr id="wpv_filter_type">
                            <?php wpv_filter_types_admin($view_settings); ?>
                        </tr>
                        
                        <?php
                            global $view_settings_table_row;
                            $view_settings_table_row = 0;
                            do_action('wpv_add_filter_table_row', $view_settings);
                        ?>
                        
                    </tbody>
                </table>
        
                <?php
                $view_settings = wpv_types_defaults($view_settings);
                wpv_filter_add_filter_admin($view_settings, null, 'popup_add_filter', '', 'wpv_add_filters', $view_settings['query_type'][0] == 'posts');
                wpv_filter_add_filter_admin($view_settings, null, 'popup_add_filter_taxonomy', '', 'wpv_add_filters_taxonomy', $view_settings['query_type'][0] == 'taxonomy');

				wpv_filter_controls_admin($view_settings);
				
                ?>
                <p>
                    <span style="font-size:1.1em;font-weight:bold;"><?php _e('Pagination settings',
            'wpv-views') ?></span>&nbsp;&nbsp;&nbsp;<img src="<?php echo WPV_URL_EMBEDDED; ?>/common/res/images/question.png" style="position:relative;top:2px;" />&nbsp;<a href="http://wp-types.com/documentation/user-guides/views-pagination/" target="_blank"><?php _e('Everything about Views pagination &raquo;',
            'wpv-views'); ?></a>
                </p>
			    <?php
                wpv_pagination_admin($view_settings);
                
                wpv_filter_meta_html_admin($view_settings);
                
                ?>
            </div>
        
            <?php $WPV_view_archive_loop->view_edit_admin($post->ID, $view_settings); ?>
        
        </div>
        <?php
    }
    
    function view_help_box($post){
        
        global $pagenow;
        ?>
            <div id="wpv-step-help-1" class="wpv-incomplete-step"><?php _e('1. Enter title', 'wpv-views'); ?></div>
            <div id="wpv-step-help-2" class="wpv-incomplete-step"><?php _e('2. Choose what content to load', 'wpv-views'); ?></div>
            <div id="wpv-step-help-3" class="wpv-incomplete-step"><?php _e('3. Edit the layout', 'wpv-views'); ?></div>
            
            <?php if($pagenow == 'post-new.php') :?>
                <div id="wpv-step-help-4" class="wpv-incomplete-step"><?php _e('4. Save this View', 'wpv-views'); ?></div>
            <?php else:?>
                <div id="wpv-step-help-4" class="wpv-complete-step"><?php _e('4. Save this View', 'wpv-views'); ?></div>
            <?php endif;?>
            <br />
            <?php printf(__('Learn how to create a View and how to display it in the complete %sViews Guide%s', 'wpv-views'),
                         '<a target=_"blank" href="http://wp-types.com/documentation/user-guides/views/">',
                         ' &raquo;</a>'); ?>
           
        <?php
        
    }


    /**
     * save the view settings.
     * Called from a post_save action
     *
     */
    
    function save_view_settings($post_id){
        global $wpdb, $sitepress;
        
        list($post_type, $post_status) = $wpdb->get_row("SELECT post_type, post_status FROM {$wpdb->posts} WHERE ID = " . $post_id, ARRAY_N);
        
        if ($post_type == 'view') {
            
            if(isset($_POST['_wpv_settings'])){
                $_POST['_wpv_settings'] = apply_filters('wpv_view_settings_save', $_POST['_wpv_settings']);
                update_post_meta($post_id, '_wpv_settings', $_POST['_wpv_settings']);
            }
            save_view_layout_settings($post_id);
    
            
            if (isset($sitepress)) {
                if (isset($_POST['icl_trid'])) {
                    // save the post from the edit screen.
                    if (isset($_POST['wpv_duplicate_view'])) {
                        update_post_meta($post_id, '_wpv_view_sync', intval($_POST['wpv_duplicate_view']));
                    } else {
                        update_post_meta($post_id, '_wpv_view_sync', "0");
                    }
                    
                    $icl_trid = $_POST['icl_trid'];
                } else {
                    // get trid from database.
                    $icl_trid = $wpdb->get_var("SELECT trid FROM {$wpdb->prefix}icl_translations WHERE element_id={$post_id} AND element_type = 'post_$post_type'");
                }
                
                if (isset($_POST['wpv_duplicate_source_id'])) {
                    $source_id = $_POST['wpv_duplicate_source_id'];
                    $target_id = $post_id;
                } else {
                    // this is the source
                    $source_id = $post_id;
                    $target_id = null;
                }
                
                if ($icl_trid) {
                    $this->duplicate_view($source_id, $target_id, $icl_trid);
                }
            }
        }        
    }
    
    function duplicate_view($source_id, $target_id, $icl_trid) {
        
        global $wpdb;
        
        if ($target_id) {
            // we're saving a translation
            // see if we should copy from the original
            $duplicate = get_post_meta($target_id, '_wpv_view_sync', true);
            if ($duplicate === "") {
                // check the original state
                $duplicate = get_post_meta($source_id, '_wpv_view_sync', true);
            }
            if ($duplicate) {
                $view_settings = get_post_meta($source_id, '_wpv_settings', true);
                update_post_meta($target_id, '_wpv_settings', $view_settings);
                
                $view_layout_settings = get_post_meta($source_id, '_wpv_layout_settings', true);
                update_post_meta($target_id, '_wpv_layout_settings', $view_layout_settings);
            }
        } else {
            // We're saving the original
            // see if we should copy to translations.
            $translations = $wpdb->get_col("SELECT element_id FROM {$wpdb->prefix}icl_translations WHERE trid = {$icl_trid}");
            
            foreach ($translations as $translation_id) {
                if ($translation_id != $source_id) {
                    $this->duplicate_view($source_id, $translation_id, $icl_trid);
                }
            }
        }
        
    }
 
	/**
	 * If the post has a view
	 * add an view edit link to post.
	 */
	
	function edit_post_link($link, $post_id) {

        if ($this->current_view) {		
			remove_filter('edit_post_link', array($this, 'edit_post_link'), 10, 2);
			
			ob_start();
			
			edit_post_link(__('Edit view', 'wpv-views'), '', '', $this->current_view);
			
			$link = $link . ' ' . ob_get_clean();
			
			add_filter('edit_post_link', array($this, 'edit_post_link'), 10, 2);
		}
		
		return $link;
	}

    function admin_add_help() {
        global $pagenow;
        $screen = get_current_screen();
        
        $help = $this->admin_plugin_help('', $screen->id, $screen);
        
        if ($help) {
            $screen->add_help_tab(array(
                                    'id' => 'views-help',
                                    'title' => __('Views', 'wpv-views'),
                                    'content' => $help,
                                    ));
        }
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
        $help = '';
        switch ($screen_id) {
            case 'edit-view-template':
                $help = '<p>'.__("Create <strong>View Templates</strong> and attach them to content types to display content in complex ways. You can read more detail about View Templates on our website:",'wpv-views');
                $help .= '<br /><a href="http://wp-types.com/user-guides/view-templates/" target="_blank">http://wp-types.com/user-guides/view-templates/ &raquo;</a></p>';
                $help .= '<p>'.__("On this page you have the following options:", 'wpv-views').'</p>';
                $help .= '<ul><li>'.__("<strong>Add New</strong> – create a new View Template", 'wpv-views').'</li></ul>';
                $help .= '<p>'.__("Hover over the name of your View Template to get additional options:", 'wpv-views').'</p>';
                $help .= '<ul><li>'.__("<strong>Edit:</strong> Click to Edit the View Template", 'wpv-views').'</li>';
                $help .= '<li>'.__("<strong>Quick Edit:</strong> click to get quick editing options for the View Template, such as title, slug and date", 'wpv-views').'</li>';
                $help .= '<li>'.__("<strong>Trash:</strong> Move the View Template to Trash", 'wpv-views').'</li></ul>';
                $help .= '<p>'.sprintf(__("If you need additional help with View Templates you can visit our <a href='%s' target='_blank'>support forum &raquo;</a>.", 'wpv-views'), WPV_SUPPORT_LINK).'</p>';
                break;
            
            case 'view-template':
                $help = '<p>'.__("Use this page to create and edit <strong>View Templates</strong>. For more information about View Templates visit the user guide on our website:", 'wpv-views');
		$help .= '<br /><a href="http://wp-types.com/user-guides/view-templates/" target="_blank">http://wp-types.com/user-guides/view-templates/ &raquo;</a></p>';
                $help .= '<p>'.__("To Create a View Template", 'wpv-views').'</p>';
                $help .= '<ol><li>'.__("Add a Title", 'wpv-views').'</li>';
                $help .= '<li>'.__("Add shortcodes to the body. You can find these by clicking on the “V” icon", 'wpv-views').'</li>';
                $help .= '<li>'.__("Use HTML mode to style your content (we recommend keeping your styles in style.css or another external stylesheet rather than including them inline)", 'wpv-views').'</li>';
                $help .= '</ol>';
                $help .= '<p>'.sprintf(__("If you need additional help with View Templates you can visit our <a href='%s' target='_blank'>support forum &raquo;</a>.", 'wpv-views'), WPV_SUPPORT_LINK).'</p>';
                break;
            
            case 'edit-view':
                $help = '<p>'.__("Use <strong>Views</strong> to filter and display lists in complex and interesting ways. Read more about Views in our user guide:",'wpv-views');
                $help .= '<br /><a href="http://wp-types.com/user-guides/views/" target="_blank">http://wp-types.com/user-guides/views/ &raquo;</a></p>';
                $help .= '<p>'.__("This page gives you an overview of the Views you have created.", 'wpv-views').'</p>';
                $help .= '<p>'.__("It has the following options:", 'wpv-views').'</p>';
                $help .= '<ul><li>'.__("<strong>Add New</strong>: Add a New View", 'wpv-views').'</li></ul>';
                $help .= '<p>'.__("If you hover over a View's name you also have these options:", 'wpv-views').'</p>';
                $help .= '<ul><li>'.__("<strong>Edit</strong>: Click to edit the View<br />\n", 'wpv-views').'</li>';
                $help .= '<li>'.__("<strong>Quick Edit</strong>: click to get quick editing options for the View, such as title, slug and date", 'wpv-views').'</li>';
                $help .= '<li>'.__("<strong>Trash</strong>: Move the View to Trash", 'wpv-views').'</li></ul>';
                $help .= '<p>'.sprintf(__("If you need additional help with View Templates you can visit our <a href='%s' target='_blank'>support forum &raquo;</a>.", 'wpv-views'), WPV_SUPPORT_LINK).'</p>';
                break;
            
            case 'view':
                $help = '<p>'.__("Use this page to create and edit your <strong>Views</strong>. You can read more about creating Views in our user guide:",'wpv-views');
                $help .= '<br /><a href="http://wp-types.com/user-guides/views/" target="_blank">http://wp-types.com/user-guides/views/ &raquo;</a></p>';
                $help .= '<p>'.__("To Create a View:", 'wpv-views').'</p>';
                $help .= '<ol><li>'.__("Add a Title for your View.", 'wpv-views').'</li>';
                $help .= '<li>'.__("Leave the shortcodes that are in your text area. These are for filtering and displaying your content.", 'wpv-views').'</li>';
                $help .= '<li>'.__("View Query &gt; Filter: Select how you would like your content to be filtered.", 'wpv-views').'</li>';
                $help .= '<li>'.__("View Query &gt; Pagination: Turn pagination on or off.", 'wpv-views').'</li>';
                $help .= '<li>'.__("View Query &gt; View/Edit HTML : fine tune the HTML for your query.", 'wpv-views').'</li>';
                $help .= '<li>'.__("View Layout: Choose your layout.", 'wpv-views').'</li>';
                $help .= '<li>'.__("View Layout &gt; View/Edit HTML: use addition CSS and HTML to control how your View is displayed.", 'wpv-views').'</li></ol>';
                $help .= '<p>'.sprintf(__("If you need additional help with View Templates you can visit our <a href='%s' target='_blank'>support forum &raquo;</a>.", 'wpv-views'), WPV_SUPPORT_LINK).'</p>';
                break;
                
        }
        
        if ($help != '') {
            return $help;
        } else {
            return $contextual_help;
        }
    }
 
	// Add WPML sync options.
	
	function language_options() {
	
		global $sitepress, $post;
		
        if ($post->post_type == 'view') {
            list($translation, $source_id, $translated_id) = $sitepress->icl_get_metabox_states();
            
            echo '<br /><br /><strong>' . __('Views sync', 'wpv-views') . '</strong>';
        
            $checked = '';
            if ($translation) {
                if ($translated_id) {
                    $duplicate = get_post_meta($translated_id, '_wpv_view_sync', true);
                    if ($duplicate === "") {
                        // check the original state
                        $duplicate = get_post_meta($source_id, '_wpv_view_sync', true);
                    }
                } else {
                    // This is a new translation.
                    $duplicate = get_post_meta($source_id, '_wpv_view_sync', true);
                }
                
                if ($duplicate) {
                    $checked = ' checked="checked"';
                }
                echo '<br /><label><input class="wpv_duplicate_from_original" name="wpv_duplicate_view" type="checkbox" value="1" '.$checked . '/>' . __('Duplicate view from original', 'wpml-media') . '</label>';
                echo '<input name="wpv_duplicate_source_id" value="' . $source_id . '" type="hidden" />';
            } else {
    
                $duplicate = get_post_meta($source_id, '_wpv_view_sync', true);
                if ($duplicate) {
                    $checked = ' checked="checked"';
                }
                echo '<br /><label><input name="wpv_duplicate_view" type="checkbox" value="1" '.$checked . '/>' . __('Duplicate view to translations', 'wpv-views') . '</label>'; 
            }
        }
	}
 
    function views_settings_admin() {
        
        global $WPV_templates, $wpdb, $WPV_view_archive_loop;
        
        $options = $this->get_options();
        
        $defaults = array('views_template_loop_blog' => '0');
        $options = wp_parse_args($options, $defaults);
        
        if (isset($_POST['submit']) && $_POST['submit'] == __('Save', 'wpv-views') &&
                            wp_verify_nonce($_POST['wpv_view_templates'], 'wpv_view_templates')) {
            
            $options = $WPV_view_archive_loop->submit($options);
            $options = $WPV_templates->submit($options);
			
			if (isset($_POST['wpv_show_hidden_fields'])) {
				// save as comma separated string.
				$options['wpv_show_hidden_fields'] = implode(',', $_POST['wpv_show_hidden_fields']);
			} else {
				$options['wpv_show_hidden_fields'] = '';
			}
            
            $this->save_options($options);

            ?>
                <div class="updated">
                    <p><?php _e("Settings Saved", 'wpv-views'); ?></p>
                </div>
            <?php
            
        }

        ?>
        
        <div class="wrap">
    
            <div id="icon-views" class="icon32"><br /></div>
            <h2><?php _e('Views Settings', 'wpv-views') ?></h2>
    
            <br />
            
            <form method="post" action="edit.php?post_type=view&page=views-settings">
                
                <?php $WPV_view_archive_loop->admin_settings($options); ?>
                <?php $WPV_templates->admin_settings($options); ?>
                
				<div id="wpv_show_hidden_custom_fields">
					<?php $this->show_hidden_custom_fields($options); ?>
				</div>
				
                <?php wp_nonce_field('wpv_view_templates', 'wpv_view_templates'); ?>
                <p class="submit" style="margin-top:0px;">
                    <input id="submit" class="button-primary" type="submit" value="<?php _e('Save', 'wpv-views'); ?>" name="submit" />
                </p>

				

            </form>
            
            <?php
                // change the preview url when the selector changes.
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function($){
                    jQuery('.views_template_select').change(function() {
                        
                        var taxonomy;
                        var link;
                        var loop = false;
                        if (jQuery(this).attr('name').substring(0, 20) == 'views_template_loop_') {
                            taxonomy = jQuery(this).attr('name').substring(20);
                            link = jQuery('#views_template_loop_preview_' + taxonomy).attr('href');
                            loop = true;
                        } else {
                            taxonomy = jQuery(this).attr('name').substring(19);
                            link = jQuery('#views_template_for_preview_' + taxonomy).attr('href');
                        }
                        
                        var newAdditionalURL = "";
                        var tempArray = link.split("?");
                        var baseURL = tempArray[0];
                        var aditionalURL = '';
                        if (tempArray.length == 2) {
                            aditionalURL = tempArray[1];
                        }
                        var temp = "";
                        if(aditionalURL) {
                            var tempArray = aditionalURL.split("&");
                            for ( var i in tempArray ){
                                if(tempArray[i].indexOf("view-template") == -1){
                                    newAdditionalURL += temp+tempArray[i];
                                    temp = "&";
                                    }
                                }
                        }
                        var rows_txt = temp+"view-template="+jQuery("#" + jQuery(this).attr('id') + ' option:selected').text();
                        var finalURL = baseURL+"?"+newAdditionalURL+rows_txt;
                        if (loop) {
                            jQuery('#views_template_loop_preview_' + taxonomy).attr('href', finalURL);
                        } else {
                            jQuery('#views_template_for_preview_' + taxonomy).attr('href', finalURL);
                            jQuery('#wpv_diff_template_' + taxonomy).hide();
                        }
                    });
                });
            </script>
            
        </div>

        
        <?php
    }

	// called from ajax
	function wpv_get_show_hidden_custom_fields() {
        $options = $this->get_options();
		
		if (isset($_POST['wpv_show_hidden_fields'])) {
			// save as comma separated string.
			$options['wpv_show_hidden_fields'] = implode(',', $_POST['wpv_show_hidden_fields']);
		} else {
			$options['wpv_show_hidden_fields'] = '';
		}
		
		$this->save_options($options);
		
		$this->show_hidden_custom_fields($options);
		
		die();
	}
	
	function show_hidden_custom_fields($options) {

		if (isset($options['wpv_show_hidden_fields']) && $options['wpv_show_hidden_fields'] != '') {
			$defaults = explode(',', $options['wpv_show_hidden_fields']);
		} else {
			$defaults = array();
		}
		
		?>
			<h3 class="title"><?php _e('Show the following hidden custom fields in the Views GUI', 'wpv-views'); ?></h3>

			<div id="wpv_show_hidden_custom_fields_summary" style="margin-left:20px">
				<?php
				
					if (sizeof($defaults) > 0) {
						echo sprintf(__('The following private custom fields are showing in the Views GUI: %s', 'wpv-views'), implode(', ', $defaults));
					} else {
						_e('No private custom fields are showing in the Views GUI.', 'wpv-views');
					}
					
				?>
				<br />
		        <input class="button-secondary" type="button" value="<?php echo __('Edit', 'wpv-views'); ?>" onclick="wpv_show_hidden_custom_fields_edit();"/>
				
			</div>
			<div id="wpv_show_hidden_custom_fields_admin" style="margin-left:20px;display:none">

				<?php
				$meta_keys = $this->get_meta_keys(true);
				
				echo '<table><tr>';

				$count = 0;					
				foreach($meta_keys as $field) {
					
					if (strpos($field, '_') === 0) {
					
						$options[$field]['#default_value'] = in_array($field, $defaults);
						$element = wpv_form_control(array('field' => array(
									'#type' => 'checkbox',
									'#name' => 'wpv_show_hidden_fields[]',
									'#attributes' => array('style' => ''),
									'#inline' => true,
									'#title' => $field,
									'#value' => $field,
									'#before' => '<td>',
									'#after' => '</td>',
									'#default_value' => in_array($field, $defaults)
							 )));
						echo $element;
						
						$count++;
						
						if (!($count % 3)) {
							echo '</tr><tr>';
						}
					}
				}

				// close the table correctly.
				if (!($count % 3)) {
					echo '<td></td><td></td><td></td>';
				}
				while(true) {
					if (!($count % 3)) {
						echo '</tr>';
						break;
					} else {
						echo '<td></td>';
					}
					
					$count++;
					
				}
				
				echo '</table>';

				?>
			
				<input class="button-primary" type="button" value="<?php echo __('Save', 'wpv-views'); ?>" onclick="wpv_show_hidden_custom_fields_save();"/>
				<img id="wpv_show_custom_fields_spinner" src="<?php echo WPV_URL; ?>/res/img/ajax-loader.gif" width="16" height="16" style="display:none" alt="loading" />
		
				<input class="button-secondary" type="button" value="<?php echo __('Cancel', 'wpv-views'); ?>" onclick="wpv_show_hidden_custom_fields_cancel();"/>
				
			
			</div>
		<?php
		
	}
	/**
	 * Get the available View in a select box
	 *
	 */
	
	function get_view_select_box($row, $page_selected, $archives_only = false) {
		global $wpdb, $sitepress;
		
		static $views_available = null;
		
		if (!$views_available) {
			$views_available = $wpdb->get_results("SELECT ID, post_title, post_name FROM {$wpdb->posts} WHERE post_type='view' AND post_status='publish'");
            
            if ($archives_only) {
                foreach ($views_available as $index => $view) {
                    $view_settings = $this->get_view_settings($view->ID);
                    if ($view_settings['view-query-mode'] != 'archive') {
                        unset($views_available[$index]);
                    }
                }
            }

			// Add a "None" type to the list.
			$none = new stdClass();
			$none->ID = '0';
			$none->post_title = __('None', 'wpv-views');
			$none->post_content = '';
			array_unshift($views_available, $none);
		}

        $view_box = '';
		if ($row === '') {
			$view_box .= '<select class="view_select" name="view" id="view">';
		} else {
			$view_box .= '<select class="view_select" name="view_' . $row . '" id="view_' . $row . '">';
		}

        if (function_exists('icl_object_id')) {
            $page_selected = icl_object_id($page_selected, 'view', true);
        }

        foreach($views_available as $view) {
			
			if (isset($sitepress)) {
				// See if we should only display the one for the correct lanuage.
				$lang_details = $sitepress->get_element_language_details($view->ID, 'post_view');
				if ($lang_details) {
					$translations = $sitepress->get_element_translations($lang_details->trid, 'post_view');
					if (count($translations) > 1) {
						$lang = $sitepress->get_current_language();
						if (isset($translations[$lang])) {
							// Only display the one in this language.
							if ($view->ID != $translations[$lang]->element_id) {
								continue;
							}
						}
					}
				}
			}
			
            if ($page_selected == $view->ID)
                $selected = ' selected="selected"';
            else
                $selected = '';
           
			if ($view->post_title) {
				$post_name = $view->post_title;
			} else {
				$post_name = $view->post_name;
			}
			
			$view_box .= '<option value="' . $view->ID . '"' . $selected . '>' . $post_name . '</option>';
			
        }
        $view_box .= '</select>';
        
        return $view_box;
	}
	
	function wpv_ajax_wpv_get_types_field_name() {
		if (wp_verify_nonce($_POST['wpv_nonce'], 'wpv_get_types_field_name_nonce')) {
			if (!defined('WPCF_VERSION')) {
				echo json_encode(array('found' => false,
									   'name' => $_POST['field']));
			} else {
			    if (defined('WPCF_INC_ABSPATH')) {
					require_once WPCF_INC_ABSPATH . '/fields.php';
				}
				
				if (function_exists('wpcf_admin_fields_get_fields')) {
					$fields = wpcf_admin_fields_get_fields();
				} else {
					$fields = array();
				}
				
				$found = false;
				foreach ($fields as $field) {
					if ($_POST['field'] == wpcf_types_get_meta_prefix($field) . $field['slug']) {
						echo json_encode(array('found' => true,
											   'name' => $field['name']));
						$found = true;
						break;
					}
				}
				
				if (!$found) {
					echo json_encode(array('found' => false,
										   'name' => $_POST['field']));
				}

			}
		}
		die();
	}
	
	function wpv_ajax_wpv_get_taxonomy_name() {
		if (wp_verify_nonce($_POST['wpv_nonce'], 'wpv_get_types_field_name_nonce')) {
			
			$taxonomies = get_taxonomies('', 'objects');
			if (isset($taxonomies[$_POST['taxonomy']])) {
				echo json_encode(array('found' => false,
								   'name' => $taxonomies[$_POST['taxonomy']]->labels->name));
			} else {
				echo json_encode(array('found' => false,
								   'name' => $_POST['taxonomy']));
			}
		}
		die();
	}
	
	function add_views_settings_js() {
		if (isset($_GET['page']) && $_GET['page'] == 'views-settings') {
            wp_enqueue_script( 'views-settings-script' , WPV_URL . '/res/js/views_settings.js', array('jquery'), WPV_VERSION);
		}
		
	}
    
}
