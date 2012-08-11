<?php

/**
 * Add the "Add filter" button to the views editor
 * Each filter type is added by hooking the 'wpv_add_filters' filter
 *
 */

function wpv_filter_add_filter_admin($view_settings, $filters = null, $id = 'popup_add_filter', $button_text = '', $filter_type = 'wpv_add_filters', $show = true) {
    if ($button_text == '') {
        $button_text = __('Add another filter term', 'wpv-views');
    }
    // Get all the filters    
    if ($filters == null) {
        $filters = array();
        $filters = apply_filters($filter_type, $filters);
    }

    foreach($filters as $type => $filter) {
        // Skip ones that are already there
        if (isset($view_settings[$type])
                || isset($view_settings[$type . '_type']) // custom field
                || isset($view_settings[str_replace('[', '_', trim($type, ']'))]) // tax
                        ) {
            $filters[$type]['hide'] = ' style="display:none"';
        } else {
            $filters[$type]['hide'] = '';
        }
    }
    if (empty($filters)) {
        return '';
    }
    
    ?>
    
        <div id="<?php echo $id; ?>" style="display:none">
            <div id="<?php echo $id; ?>_controls">

                <strong><?php _e('Select what to filter by:', 'wpv-views'); ?></strong>
                
                <select id="<?php echo $id; ?>_select">
                <?php
                    foreach($filters as $type => $filter) {
                        ?>
                        <option value="<?php echo $type; ?>"<?php echo $filter['hide']; ?>><?php echo $filter['name']; ?></option>
                        
                        <?php
                    }
                ?>
                </select>
                <br />
                <strong><?php _e('Condition:', 'wpv-views'); ?></strong>
                <?php
                    foreach($filters as $type => $filter) {
                        ?>
                        <div id="<?php echo $id; ?>_con_<?php echo $type; ?>" class="wpv_add_filter_con_<?php echo $id; ?>" style="display:none;">
                        
                            <?php
                                switch ($filter['type']) {
                                    case 'checkboxes':
                                        echo '<div style="margin-left: 20px;">';
                                        foreach ($filter['value'] as $value) {
                                            echo '<label><input name="' . $type . '[]" type="checkbox" value="' . $value . '"/>' . $value . '</label><br /> ';
                                        }
                                        echo '</div>';
                                        break;
                                    
                                    case 'callback':
                                        if (isset($filter['args'])) {
                                            call_user_func($filter['callback'], $filter['args']);
                                        } else {
                                            call_user_func($filter['callback']);
                                        }
                                        break;
                                }
                            ?>
                        
                        </div>
                        <?php
                    }
                ?>
                
                <br />
                <br />
                <input class="button-secondary" type="button" value="<?php echo __('Add filter', 'wpv-views'); ?>" name="<?php echo __('Add filter', 'wpv-views'); ?>" onclick="wpv_add_filter_submit('<?php echo $id; ?>')" />
                
            </div>  
        </div>

        <input alt="#TB_inline?inlineId=<?php echo $id; ?>" class="thickbox button-secondary <?php echo $filter_type; ?>_button" type="button" value="<?php echo $button_text; ?>" name="<?php echo $button_text; ?>" <?php if($show) {echo '';} else {echo ' style="display:none"';} ?>/>
        <br />
    <?php
}
