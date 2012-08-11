<?php

function wpv_taxonomy_order_by_default_settings($view_settings) {

    if (!isset($view_settings['taxonomy_orderby'])) {
        $view_settings['taxonomy_orderby'] = 'name';
    }
    if (!isset($view_settings['taxonomy_order'])) {
        $view_settings['taxonomy_order'] = 'DESC';
    }
    
    return $view_settings;
}

$taxonomy_order_by = array(
    'id' => 'ID',
    'count' => 'Count',
    'name' => 'Name',
    'slug' => 'Slug',
    'term_group' => 'Term_group',
    'none' => 'None'
);

function wpv_filter_taxonomy_order_by_admin_summary($view_settings) {
    $view_settings = wpv_taxonomy_order_by_default_settings($view_settings);
    
    global $taxonomy_order_by;
    $order_by = $taxonomy_order_by[$view_settings['taxonomy_orderby']];
    
    $order = __('descending', 'wpv-views');
    if ($view_settings['taxonomy_order'] == 'ASC') {
        $order = __('ascending', 'wpv-views');
    }
    echo sprintf(__(', ordered by <strong>%s</strong>, <strong>%s</strong>', 'wpv-views'), $order_by, $order);
    
}

function wpv_filter_taxonomy_order_by_admin($view_settings) {
    $view_settings = wpv_taxonomy_order_by_default_settings($view_settings);
    
    global $WP_Views, $taxonomy_order_by;
    
    ?>
    <fieldset>
        <legend><strong><?php _e('Order by:', 'wpv-views') ?></strong></legend>            
        <ul style="padding-left:30px;">
            <li>
                <select name="_wpv_settings[taxonomy_orderby]">
                    <?php
                        foreach($taxonomy_order_by as $id => $text) {
                            $selected = $view_settings['taxonomy_orderby']==$id ? ' selected="selected"' : ''
                            ?>
                                <option value="<?php echo $id; ?>" <?php echo $selected ?>><?php echo $text; ?></option>
                            <?php
                            
                        }
                    ?>
                    
                </select>
            </li>
            <li>
                <select name="_wpv_settings[taxonomy_order]">            
                    <option value="DESC"><?php _e('Descending', 'wpv-views'); ?>&nbsp;</option>
                    <?php $selected = $view_settings['taxonomy_order']=='ASC' ? ' selected="selected"' : ''; ?>
                    <option value="ASC" <?php echo $selected ?>><?php _e('Ascending', 'wpv-views'); ?>&nbsp;</option>
                </select>
            </li>
        </ul>
        
    </fieldset>

    <?php
}
add_filter('wpv-view-get-content-summary', 'wpv_taxonomy_order_summary_filter', 5, 3);

function wpv_taxonomy_order_summary_filter($summary, $post_id, $view_settings) {
	if(isset($view_settings['query_type']) && $view_settings['query_type'][0] == 'taxonomy') {
		ob_start();
		wpv_filter_taxonomy_order_by_admin_summary($view_settings);
		$summary .= ob_get_contents();
		ob_end_clean();
	}
	
	return $summary;
}
