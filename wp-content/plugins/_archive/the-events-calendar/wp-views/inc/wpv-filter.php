<?php

require WPV_PATH . '/inc/wpv-filter-add-filter.php';
require WPV_PATH . '/inc/wpv-filter-types.php';
require WPV_PATH_EMBEDDED . '/inc/wpv-filter-types-embedded.php';
require WPV_PATH_EMBEDDED . '/inc/wpv-filter-post-types-embedded.php';
require WPV_PATH . '/inc/wpv-filter-post-types.php';
require WPV_PATH_EMBEDDED . '/inc/wpv-filter-taxonomy-embedded.php';
require WPV_PATH . '/inc/wpv-filter-taxonomy.php';
require WPV_PATH_EMBEDDED . '/inc/wpv-filter-order-by-embedded.php';
require WPV_PATH . '/inc/wpv-filter-order-by.php';
require WPV_PATH . '/inc/wpv-filter-taxonomy-order-by.php';
require WPV_PATH . '/inc/wpv-pagination.php';
require WPV_PATH . '/inc/wpv-filter-meta-html.php';
require WPV_PATH . '/inc/wpv-filter-limit.php';
require WPV_PATH_EMBEDDED . '/inc/wpv-filter-limit-embedded.php';


function wpv_filter_interface_select($view_settings, $key, $output_text, $short_code, $allow_multiple = false) {

    if (!isset($view_settings[$key])) {
        $view_settings[$key] = '';
    }
    ?>
    
    <select class="wpv_interface_select" name="_wpv_settings[<?php echo $key; ?>]" output_text="<?php echo $output_text; ?>" short_code="<?php echo $short_code; ?>">
        <option value="none"><?php _e('None', 'wpv-views'); ?></option>
        <?php if($allow_multiple): ?>
            <?php $selected = $view_settings[$key]=='checkboxes' ? ' selected="selected"' : ''; ?>
            <option value="checkboxes" <?php echo $selected ?>><?php _e('Checkboxes', 'wpv-views'); ?></option>
        <?php endif; ?>
        <?php $selected = $view_settings[$key]=='radios' ? ' selected="selected"' : ''; ?>
        <option value="radios" <?php echo $selected ?>><?php _e('Radios', 'wpv-views'); ?></option>
        <?php $selected = $view_settings[$key]=='drop_down' ? ' selected="selected"' : ''; ?>
        <option value="drop_down" <?php echo $selected ?>><?php _e('Drop down list', 'wpv-views'); ?></option>
    </select>
    
    <?php
}


function wpv_filter_add_js() {    
    wp_enqueue_script( 'views-filter-script' , WPV_URL . '/res/js/views_filter.js', array(), WPV_VERSION);
    wp_enqueue_script( 'views-filter-search-script' , WPV_URL . '/res/js/views_filter_search.js', array(), WPV_VERSION);
    wp_enqueue_script( 'views-filter-parent-script' , WPV_URL . '/res/js/views_filter_parent.js', array(), WPV_VERSION);
    wp_enqueue_script( 'views-filter-custom-fields-script' , WPV_URL . '/res/js/views_filter_custom_fields.js', array(), WPV_VERSION);
    wp_enqueue_script( 'views-filter-custom-taxonomy-script' , WPV_URL . '/res/js/views_filter_taxonomy.js', array(), WPV_VERSION);
    wp_enqueue_script( 'views-add-filter-script' , WPV_URL . '/res/js/views_add_filter.js', array(), WPV_VERSION);
    wp_enqueue_script( 'views-pagination-script' , WPV_URL . '/res/js/views_pagination.js', array(), WPV_VERSION);
    wp_enqueue_script( 'views-filter-meta-html-script' , WPV_URL . '/res/js/views_filter_meta_html.js', array(), WPV_VERSION);
    wp_enqueue_script( 'views-filter-post-relationship-script' , WPV_URL . '/res/js/views_filter_post_relationship.js', array(), WPV_VERSION);
    wp_enqueue_script( 'views-insert-controls-script' , WPV_URL . '/res/js/views_insert_controls.js', array(), WPV_VERSION);
    wp_enqueue_script( 'views-filter-controls-script' , WPV_URL . '/res/js/views_filter_controls.js', array(), WPV_VERSION);
}

