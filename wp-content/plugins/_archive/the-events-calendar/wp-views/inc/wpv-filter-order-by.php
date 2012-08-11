<?php

function wpv_filter_order_by_admin_summary($view_settings) {
    $view_settings = wpv_order_by_default_settings($view_settings);
    switch($view_settings['orderby']) {
        case 'post_date':
            $order_by = __('post date', 'wpv-views');
            break;
        
        case 'post_title':
            $order_by = __('post title', 'wpv-views');
            break;
        
        case 'ID':
            $order_by = __('post ID', 'wpv-views');
            break;
        
        case 'menu_order':
            $order_by = __('menu order', 'wpv-views');
            break;
        
        case 'rand':
            $order_by = __('random order', 'wpv-views');
            break;
            
        default:
            $order_by = str_replace('field-', '', $view_settings['orderby']);
            $order_by = sprintf(__('Field - %s', 'wpv-views'), $order_by);
            break;
        
    }
    $order = __('descending', 'wpv-views');
    if ($view_settings['order'] == 'ASC') {
        $order = __('ascending', 'wpv-views');
    }
    echo sprintf(__(', ordered by <strong>%s</strong>, <strong>%s</strong>', 'wpv-views'), $order_by, $order);
    
}

function wpv_filter_order_by_admin($view_settings) {
    
    global $WP_Views;
    
    ?>
    <fieldset>
        <legend><strong><?php _e('Order by:', 'wpv-views') ?></strong></legend>            
        <ul style="padding-left:30px;">
            <li>
                <select name="_wpv_settings[orderby]">
                    <option value="post_date"><?php _e('post date', 'wpv-views'); ?></option>
                    <?php $selected = $view_settings['orderby']=='post_title' ? ' selected="selected"' : ''; ?>
                    <option value="post_title" <?php echo $selected ?>><?php _e('post title', 'wpv-views'); ?></option>
                    <?php $selected = $view_settings['orderby']=='ID' ? ' selected="selected"' : ''; ?>
                    <option value="ID" <?php echo $selected ?>><?php _e('post id', 'wpv-views'); ?></option>
                    <?php $selected = $view_settings['orderby']=='menu_order' ? ' selected="selected"' : ''; ?>
                    <option value="menu_order" <?php echo $selected ?>><?php _e('menu order', 'wpv-views'); ?></option>
                    <?php $selected = $view_settings['orderby']=='rand' ? ' selected="selected"' : ''; ?>
                    <option value="rand" <?php echo $selected ?>><?php _e('random order', 'wpv-views'); ?></option>
                    
                    <?php
                        $cf_keys = $WP_Views->get_meta_keys();
                        foreach ($cf_keys as $key) {
                            $selected = $view_settings['orderby'] == "field-" . $key ? ' selected="selected"' : '';
                            $option = '<option value="field-' . $key . '"' . $selected . '>';
                            $option .= sprintf(__('Field - %s', 'wpv-views'), $key);
                            $option .= '</option>';
                            echo $option;
                        }
                    ?>
                </select>
            </li>
            <li>
                <select name="_wpv_settings[order]">            
                    <option value="DESC"><?php _e('Descending', 'wpv-views'); ?>&nbsp;</option>
                    <?php $selected = $view_settings['order']=='ASC' ? ' selected="selected"' : ''; ?>
                    <option value="ASC" <?php echo $selected ?>><?php _e('Ascending', 'wpv-views'); ?>&nbsp;</option>
                </select>
            </li>
        </ul>
        
    </fieldset>

    <?php
}

add_filter('wpv-view-get-content-summary', 'wpv_order_summary_filter', 5, 3);

function wpv_order_summary_filter($summary, $post_id, $view_settings) {
	if(!isset($view_settings['query_type']) || (isset($view_settings['query_type']) && $view_settings['query_type'][0] == 'posts')) {
		ob_start();
		wpv_filter_order_by_admin_summary($view_settings);
		$summary .= ob_get_contents();
		ob_end_clean();
	}
	
	return $summary;
}


