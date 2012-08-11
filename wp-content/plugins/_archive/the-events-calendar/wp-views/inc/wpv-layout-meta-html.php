<?php

function wpv_layout_taxonomy_V($menu) {
    
    // remove post items and add taxonomy items.
    
    global $wpv_shortcodes;
    
    $basic = __('Basic', 'wpv-views');
    $menu = array($basic => array());
    
    $taxonomy = array('wpv-taxonomy-title',
                      'wpv-taxonomy-link',
                      'wpv-taxonomy-url',
                      'wpv-taxonomy-description',
                      'wpv-taxonomy-post-count');

    foreach ($taxonomy as $key) {
        $menu[$basic][$wpv_shortcodes[$key][1]] = array($wpv_shortcodes[$key][1],
                                                                        $wpv_shortcodes[$key][0],
                                                                        $basic,
                                                                        '');
    }    
    return $menu;

}

/*
  
    Add controls to the admin page for specifying the layout_meta_html
    
*/

function wpv_layout_meta_html_admin($post, $view_layout_settings) {
    global $WP_Views;
    
    $view_settings = $WP_Views->get_view_settings($post->ID);
    
    
    $defaults = array('layout_meta_html' => '',
                      'generated_layout_meta_html' => '');
    $view_layout_settings = wp_parse_args($view_layout_settings, $defaults);
    
    ?>
        <div id="wpv_layout_meta_html_admin">
            <div id="wpv_layout_meta_html_admin_show">
                <p><i><?php echo __('The layout-style and fields that you selected generate meta HTML. This meta HTML includes shortcodes and HTML, which you can edit, to fully customize the appearance of this View\'s content output section.', 'wpv-views'); ?></i></p>
                <input type="button" class="button-secondary" onclick="wpv_view_layout_meta_html()" value="<?php _e('View/Edit Meta HTML', 'wpv-views'); ?>" />
            </div>
            <div id="wpv_layout_meta_html_admin_edit" style="background:<?php echo WPV_EDIT_BACKGROUND;?>;display:none">
                <div style="margin:10px 10px 10px 10px;">
                    <p><?php _e('<strong>Meta HTML</strong> - This is used to layout the posts found. It gets generated from the View Layout settings and can be modified to suit.', 'wpv-views'); ?></p>
                    <div id="wpv_layout_meta_html_content_error" class="wpv_form_errors" style="display:none;">
                        <p><?php _e("Changes can't be applied. It appears that you made manual modifications to the Meta HTML.", 'wpv-views'); ?></p>
                        <a style="cursor:pointer;margin-bottom:10px;" onclick="wpv_layout_meta_html_generate_new()"><strong><?php echo __('Generate the new layout content', 'wpv-views'); ?></strong></a> <?php _e('(your edits will be displayed and you can apply them again)', 'wpv-views'); ?>
                    </div>
           
                    <?php
                        $show = $view_settings['query_type'][0] == 'posts' ? '' : 'style="display:none"';
                    ?>
                    <div id="wpv-layout-v-icon-posts" <?php echo $show;?>>
                    <?php echo $WP_Views->editor_addon->add_form_button('', '#wpv_layout_meta_html_content'); ?>
                    </div>
                    
                    <?php
                        $show = $view_settings['query_type'][0] == 'taxonomy' ? '' : 'style="display:none"';
                    ?>
                    <div id="wpv-layout-v-icon-taxonomy" <?php echo $show;?>>
                    <?php
                        // add a "V" icon for taxonomy
                        remove_filter('editor_addon_menus_wpv-views', 'wpv_post_taxonomies_editor_addon_menus_wpv_views_filter', 11);
                        add_filter('editor_addon_menus_wpv-views', 'wpv_layout_taxonomy_V');

                        echo $WP_Views->editor_addon->add_form_button('', '#wpv_layout_meta_html_content');
                        
                        remove_filter('editor_addon_menus_wpv-views', 'wpv_layout_taxonomy_V');
                        add_filter('editor_addon_menus_wpv-views', 'wpv_post_taxonomies_editor_addon_menus_wpv_views_filter', 11);
                    ?>
                    </div>
                    
                    <textarea name="_wpv_layout_settings[layout_meta_html]" id="wpv_layout_meta_html_content" cols="40" rows="10" style="width:100%;margin-top:10px"><?php echo $view_layout_settings['layout_meta_html']; ?></textarea>
                    <div id="wpv_layout_meta_html_content_old_div" style="display:none">
                        <div class="wpv_form_notice"><?php _e('<strong>Your edits are shown below:</strong>', 'wpv-views'); ?> <a style="cursor:pointer;margin-bottom:10px;" onclick="wpv_layout_meta_html_old_dismiss()"><strong><?php echo __('dismiss', 'wpv-views'); ?></strong></a></div>
                        <textarea id="wpv_layout_meta_html_content_old" cols="40" rows="10" style="width:100%;margin-top:10px"></textarea>
                    </div>
                    <textarea name="_wpv_layout_settings[generated_layout_meta_html]" id="wpv_generated_layout_meta_html_content" cols="40" rows="10" style="display:none"><?php echo $view_layout_settings['generated_layout_meta_html']; ?></textarea>
                    <div id="wpv_layout_meta_html_notice" class="wpv_form_notice" style="display:none;"><?php _e('* These updates will take effect when you save the view.', 'wpv-views'); ?></div>
                    <p><a style="cursor:pointer;margin-bottom:10px;" onclick="wpv_view_layout_meta_html_close()"><strong><?php _e('Close', 'wpv-views'); ?></strong></a></p>
                </div>
            </div>
        </div>

    <?php
    
}


