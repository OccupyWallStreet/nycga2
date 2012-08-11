<?php

function wpv_ajax_get_type_filter_summary() {
    if (wp_verify_nonce($_POST['wpv_type_filter_nonce'], 'wpv_type_filter_nonce')) {
        wpv_get_type_filter_summary($_POST['_wpv_settings']);
    }    
    die;
}

function wpv_filter_types_admin($view_settings) {

    $view_settings = wpv_types_defaults($view_settings);
    $view_settings = wpv_post_default_settings($view_settings);
    $view_settings = wpv_taxonomy_default_settings($view_settings);
    $view_settings = wpv_order_by_default_settings($view_settings);

    ?>
    <td></td>
    <td>
        <div id="wpv-filter-type-show">
            <?php
            
                wpv_get_type_filter_summary($view_settings);
                
            ?>
        </div>
        <div id="wpv-filter-type-edit" style="background:<?php echo WPV_EDIT_BACKGROUND;?>;display:none">
            <?php wp_nonce_field('wpv_type_filter_nonce', 'wpv_type_filter_nonce'); ?>
            <fieldset>
                <legend style="margin-bottom:5px"><strong><?php _e('Select what content type to load:', 'wpv-views') ?></strong></legend>
                <ul style="padding-left:20px;">
                    <?php $checked = $view_settings['query_type'][0] == 'posts' ? ' checked="checked"' : ''; ?>
                    <li><label><input type="radio" name="_wpv_settings[query_type][]" value="posts" <?php echo $checked ?> onclick="wpv_select_post_type_filter()"/>&nbsp;<?php echo __("Posts (This View returns posts)", 'wpv-views'); ?></label></li>
                    <?php $checked = $view_settings['query_type'][0] == 'taxonomy' ? ' checked="checked"' : ''; ?>
                    <li><label><input type="radio" name="_wpv_settings[query_type][]" value="taxonomy" <?php echo $checked ?> onclick="wpv_select_taxonomy_type_filter()"/>&nbsp;<?php echo __("Taxonomy (This View returns taxonomies)", 'wpv-views'); ?></label></li>
                </ul>
                
                <div id="wpv-post-type-checkboxes"<?php if($view_settings['query_type'][0] != 'posts') { echo ' style="display:none"'; }?>>
                    <?php wpv_post_types_checkboxes($view_settings); ?>
                </div>

                <div id="wpv-taxonomy-radios"<?php if($view_settings['query_type'][0] != 'taxonomy') { echo ' style="display:none"'; }?>>
                    <?php wpv_taxonomy_radios($view_settings); ?>
                </div>
            </fieldset>
            
            <div id="wpv-post-types-settings"<?php if($view_settings['query_type'][0] != 'posts') { echo ' style="display:none"'; }?>>
                <?php wpv_post_types_settings($view_settings); ?>
            </div>
            <div id="wpv-post-order-by"<?php if($view_settings['query_type'][0] != 'posts') { echo ' style="display:none"'; }?>>
                <?php wpv_filter_order_by_admin($view_settings); ?>
            </div>
            <div id="wpv-post-limit"<?php if($view_settings['query_type'][0] != 'posts') { echo ' style="display:none"'; }?>>
                <?php wpv_filter_limit_admin($view_settings); ?>
            </div>

            <div id="wpv-taxonomy-settings"<?php if($view_settings['query_type'][0] != 'taxonomy') { echo ' style="display:none"'; }?>>
                <?php wpv_taxonomy_settings($view_settings); ?>
            </div>
            <div id="wpv-taxonomy-order-by"<?php if($view_settings['query_type'][0] != 'taxonomy') { echo ' style="display:none"'; }?>>
                <?php wpv_filter_taxonomy_order_by_admin($view_settings); ?>
            </div>
            <div id="wpv-taxonomy-limit"<?php if($view_settings['query_type'][0] != 'taxonomy') { echo ' style="display:none"'; }?>>
                <?php wpv_filter_limit_admin($view_settings, 'taxonomy'); ?>
            </div>

            
            <input class="button-primary" type="button" value="<?php echo __('OK', 'wpv-views'); ?>" name="<?php echo __('OK', 'wpv-views'); ?>" onclick="wpv_show_type_edit_ok()"/>
            <input class="button-secondary" type="button" value="<?php echo __('Cancel', 'wpv-views'); ?>" name="<?php echo __('Cancel', 'wpv-views'); ?>" onclick="wpv_show_type_edit_cancel()"/>
        </div>
    </td>
    
    <?php    
}

function wpv_get_type_filter_summary($view_settings) {
    switch($view_settings['query_type'][0]) {
        case 'posts':
            wpv_get_post_filter_summary($view_settings);
            wpv_filter_order_by_admin_summary($view_settings);
            wpv_filter_limit_admin_summary($view_settings);
            break;
        
        case 'taxonomy':
            wpv_get_taxonomy_filter_summary($view_settings);
            wpv_filter_taxonomy_order_by_admin_summary($view_settings);
            wpv_filter_limit_admin_summary($view_settings, 'taxonomy');
            break;
        
    }
    
    ?>
    <br />
    <input class="button-secondary" type="button" value="<?php echo __('Edit', 'wpv-views'); ?>" name="<?php echo __('Edit', 'wpv-views'); ?>" onclick="wpv_show_type_edit()"/>
    <?php    

}

function wpv_filter_type_hide_element($view_settings, $type) {
    if ($view_settings['query_type'][0] != $type) {
        // hide the element.
        return ' style="display:none"';
    } else {
        return '';
    }
}
