<?php


add_filter('wpv_view_settings_save', 'wpv_taxonomy_defaults_save', 10, 1);
function wpv_taxonomy_defaults_save($view_settings) {
    global $taxonomy_checkboxes_defaults;

    // we need to set 0 for the checkboxes that aren't checked and are missing for the $_POST.
    
    $defaults = array();
    foreach($taxonomy_checkboxes_defaults as $key => $value) {
        $defaults[$key] = 0;
    }
    $view_settings = wpv_parse_args_recursive($view_settings, $defaults);

    return $view_settings;
}

function wpv_get_taxonomy_filter_summary($view_settings) {
    
    $view_settings = wpv_taxonomy_default_settings($view_settings);
    $selected = $view_settings['taxonomy_type'];
    
	$taxonomies = get_taxonomies('', 'objects');
	
	if (isset($taxonomies[$selected[0]])) {
		$name = $taxonomies[$selected[0]]->labels->name;
	} else {
		$name = $selected[0];
	}
    echo sprintf(__('This View selects <strong>Taxonomy</strong> of type <strong>%s</strong>', 'wpv-views'), $name);
            
}


function wpv_taxonomy_radios($view_settings) {
	$taxonomies = get_taxonomies('', 'objects');
    
    // remove any  that don't exist any more.
    foreach($view_settings['taxonomy_type'] as $type) {
        if (!isset($taxonomies[$type])) {
            unset($view_settings['taxonomy_type'][$type]);
        }
    }
    
    ?>
        <ul style="padding-left:30px;">
            <?php foreach($taxonomies as $tax):?>
                <?php 
                    if (sizeof($view_settings['taxonomy_type']) == 0) {
                        $view_settings['taxonomy_type'][] = $tax->name;
                    }
                    $checked = @in_array($tax->name, $view_settings['taxonomy_type']) ? ' checked="checked"' : '';
                ?>
                <li><label><input type="radio" name="_wpv_settings[taxonomy_type][]" value="<?php echo $tax->name ?>" <?php echo $checked ?> />&nbsp;<?php echo $tax->labels->name ?></label></li>
            <?php endforeach; ?>
        </ul>
    <?php
}

function wpv_taxonomy_settings($view_settings) {
    
    ?>
    <strong><?php echo __('Settings:', 'wpv-views'); ?></strong>
    <ul style="padding-left:30px;">
        <?php $checked = $view_settings['taxonomy_hide_empty']  ? ' checked="checked"' : '';?>
        <li><label><input type="checkbox" name="_wpv_settings[taxonomy_hide_empty]" value="1" <?php echo $checked ?> />&nbsp;<?php echo __('Don\'t show empty terms', 'wpv-views'); ?></label></li>
        <?php $checked = $view_settings['taxonomy_include_non_empty_decendants']  ? ' checked="checked"' : '';?>
        <li><label><input type="checkbox" name="_wpv_settings[taxonomy_include_non_empty_decendants]" value="1" <?php echo $checked ?> />&nbsp;<?php echo __('Include terms that have non-empty descendants', 'wpv-views'); ?></label></li>
        <?php $checked = $view_settings['taxonomy_pad_counts']  ? ' checked="checked"' : '';?>
        <li><label><input type="checkbox" name="_wpv_settings[taxonomy_pad_counts]" value="1" <?php echo $checked ?> />&nbsp;<?php echo __('Include children in the post count', 'wpv-views'); ?></label></li>
    </ul>
    <?php
}

add_filter('wpv-view-get-content-summary', 'wpv_taxonomy_summary_filter', 5, 3);

function wpv_taxonomy_summary_filter($summary, $post_id, $view_settings) {
	if(isset($view_settings['query_type']) && $view_settings['query_type'][0] == 'taxonomy') {
		ob_start();
		wpv_get_taxonomy_filter_summary($view_settings);
		$summary .= ob_get_contents();
		ob_end_clean();
	}
	return $summary;
}

