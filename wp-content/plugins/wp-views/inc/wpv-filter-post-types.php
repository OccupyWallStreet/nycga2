<?php

function wpv_post_default_settings($view_settings) {

    if (!isset($view_settings['post_type'])) {
        $view_settings['post_type'] = array();
    }
    if (!isset($view_settings['post_type_dont_include_current_page'])) {
        $view_settings['post_type_dont_include_current_page'] = true;
    }

    return $view_settings;
}

add_filter('wpv_view_settings_save', 'wpv_post_types_defaults_save', 10, 1);
function wpv_post_types_defaults_save($view_settings) {

    // we need to set 0 for the checkboxes that aren't checked and are missing for the $_POST.
    
    $defaults = array('post_type_dont_include_current_page' => 0);
    $view_settings = wpv_parse_args_recursive($view_settings, $defaults);

    return $view_settings;
}

function wpv_get_post_filter_summary($view_settings) {
    
    $view_settings = wpv_post_default_settings($view_settings);
    $selected = $view_settings['post_type'];
    
    $post_types = get_post_types(array('public'=>true), 'objects');
    $post_type_names = get_post_types(array('public'=>true));
    
	$selected_post_types = sizeof($selected);
	switch ($selected_post_types) {
		case 0:
			_e('<strong>ALL</strong> post types', 'wpv-views');
			break;
		
		case 1:
            if (isset($post_types[$selected[0]])) {
                $name = $post_types[$selected[0]]->labels->name;
            } else {
                $name = $selected[0];
            }
            echo sprintf(__('<strong>%s</strong>', 'wpv-views'), $name);
			break;
		
		default:
			for($i = 0; $i < $selected_post_types - 1; $i++) {
                if (isset($post_types[$selected[$i]])) {
                    $name = $post_types[$selected[$i]]->labels->name;
                } else {
                    $name = $selected[$i];
                }
                if ($i > 0) {
                    echo ', ';
                }
                    
                echo '<strong>' . $name . '</strong>';
                    
			}
            
            if (isset($post_types[$selected[$i]])) {
                $name = $post_types[$selected[$i]]->labels->name;
            } else {
                $name = $selected[$i];
            }
            
			echo ', <strong>' . $name . '</strong>';
			break;
		
	}
            
}

function wpv_post_types_checkboxes($view_settings) {
    $post_types = get_post_types(array('public'=>true), 'objects');
    
    // remove any post types that don't exist any more.
    foreach($view_settings['post_type'] as $type) {
        if (!isset($post_types[$type])) {
            unset($view_settings['post_type'][$type]);
        }
    }

    ?>
        <ul style="padding-left:30px;">
            <?php foreach($post_types as $p):?>
                <?php 
                    $checked = @in_array($p->name, $view_settings['post_type']) ? ' checked="checked"' : '';
                ?>
                <li><label><input type="checkbox" name="_wpv_settings[post_type][]" value="<?php echo $p->name ?>" <?php echo $checked ?> onclick="wpv_filter_vmenu_items();" />&nbsp;<?php echo $p->labels->name ?></label></li>
            <?php endforeach; ?>
        </ul>
    <?php
}

function wpv_post_types_settings($view_settings) {
    
    ?>
    <strong><?php echo __('Settings:', 'wpv-views'); ?></strong>
    <ul style="padding-left:30px;">
        <?php $checked = $view_settings['post_type_dont_include_current_page']  ? ' checked="checked"' : '';?>
        <li><label><input type="checkbox" name="_wpv_settings[post_type_dont_include_current_page]" value="1" <?php echo $checked ?> />&nbsp;<?php echo __('Don\'t include current page in query result', 'wpv-views'); ?></label></li>
    </ul>
    <?php
}

add_filter('wpv-view-get-content-summary', 'wpv_post_types_summary_filter', 5, 3);

function wpv_post_types_summary_filter($summary, $post_id, $view_settings) {
	if(!isset($view_settings['query_type']) || (isset($view_settings['query_type']) && $view_settings['query_type'][0] == 'posts')) {
		ob_start();
		wpv_get_post_filter_summary($view_settings);
		$summary .= ob_get_contents();
		ob_end_clean();
	}
	
	return $summary;
}
