<?php

if(is_admin()){
	add_action('init', 'wpv_filter_status_init');
	
	function wpv_filter_status_init() {
        global $pagenow;
        
        if($pagenow == 'post.php' || $pagenow == 'post-new.php'){
            add_action('wpv_add_filter_table_row', 'wpv_add_filter_status_table_row', 1, 1);
            add_filter('wpv_add_filters', 'wpv_add_filter_status', 1, 1);
        }
    }
    
    /**
     * Add a search by status filter
     * This gets added to the popup that shows the available filters.
     *
     */
    
    function wpv_add_filter_status($filters) {
        $filters['post_status'] = array('name' => 'Post status',
                                    'type' => 'checkboxes',
                                    'value' => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash', 'any'));
        return $filters;
    }

    /**
     * get the table row to add the the available filters
     *
     */
    
    function wpv_add_filter_status_table_row($view_settings) {
        if (isset($view_settings['post_status'])) {
            global $view_settings_table_row;
            $td = wpv_get_table_row_ui_post_status($view_settings_table_row, $view_settings['post_status']);
        
            echo '<tr class="wpv_filter_row wpv_post_type_filter_row" id="wpv_filter_row_' . $view_settings_table_row . '"' . wpv_filter_type_hide_element($view_settings, 'posts') . '>' . $td . '</tr>';
            
            $view_settings_table_row++;
        }
    }
    
    /**
     * get the table info for the status.
     * This is called (via ajax) when we add a post status filter
     * It's also called to display the existing post status filter.
     *
     */
    
    function wpv_get_table_row_ui_post_status($row, $selected, $view_settings = null) {
        
        if (isset($_POST['checkboxes'])) {
            // From ajax.
            $selected = $_POST['checkboxes'];
        } elseif (!is_array($selected)) {
			$selected = array();
		}
		
		
        $checkboxes = wpv_render_checkboxes(array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash', 'any'),
											$selected,
											'post_status');
        
		$td = wpv_render_filter_td($row, 'status', __('Post status', 'wpv-views'), 'wpv_get_filter_status_summary', $selected, $checkboxes);
        
        return $td;
    }

    function wpv_get_filter_status_summary_text($selected, $short=false) {
        ob_start();
        
        if (sizeof($selected)) {
			if ($short) {
				_e('status of ', 'wpv-views');
			} else {
				_e('Select posts with status of ', 'wpv-views');
			}
            $first = true;
            foreach($selected as $value) {
                if ($first) {
                    echo '<strong>' . $value . '</strong>';
                    $first = false;
                } else {
                    _e(' or ', 'wpv-views');
                    echo '<strong>' . $value . '</strong>';
                }
            }
        } else {
			if ($short) {
				_e('any status.', 'wpv-views');
			} else {
				_e('Select posts with any status.', 'wpv-views');
			}
        }
        $data = ob_get_clean();
        
        return $data;
        
    }
    
    function wpv_get_filter_status_summary($selected) {
        ob_start();

		echo wpv_get_filter_status_summary_text($selected);        
        ?>
        <br />
        <input class="button-secondary" type="button" value="<?php echo __('Edit', 'wpv-views'); ?>" name="<?php echo __('Edit', 'wpv-views'); ?>" onclick="wpv_show_filter_status_edit()"/>
        <?php
        
        $data = ob_get_clean();
        
        return $data;
        
    }
    
    add_filter('wpv-view-get-summary', 'wpv_status_summary_filter', 5, 3);

	function wpv_status_summary_filter($summary, $post_id, $view_settings) {
		if(isset($view_settings['query_type']) && $view_settings['query_type'][0] == 'posts' && isset($view_settings['post_status'])) {
			$selected = $view_settings['post_status'];
			
			$result = wpv_get_filter_status_summary_text($selected, true);
			if ($result != '' && $summary != '') {
				$summary .= '<br />';
			}
			$summary .= $result;
		}
		
		return $summary;
	}
    
}

