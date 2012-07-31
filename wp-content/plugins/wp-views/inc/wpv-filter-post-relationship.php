<?php

if(is_admin()){
	add_action('init', 'wpv_filter_post_relationship_init');
	
	function wpv_filter_post_relationship_init() {

		// only add if Types supports this.
	    if (function_exists('wpcf_pr_get_belongs')) {
			global $pagenow;
			
			if($pagenow == 'post.php' || $pagenow == 'post-new.php'){
				add_action('wpv_add_filter_table_row', 'wpv_add_filter_post_relationship_table_row', 1, 1);
				add_filter('wpv_add_filters', 'wpv_add_filter_post_relationship', 1, 1);
			}
		}
    }
    
    /**
     * Add a post_relationship by post_relationship filter
     * This gets added to the popup that shows the available filters.
     *
     */
    
    function wpv_add_filter_post_relationship($filters) {
        $filters['post_post_relationship'] = array('name' => 'Post Relationship - Post is a child of',
										'type' => 'callback',
										'callback' => 'wpv_add_post_relationship',
										'args' => array());
        return $filters;
    }

    /**
     * get the table row to add the the available filters
     *
     */
    
    function wpv_add_filter_post_relationship_table_row($view_settings) {
        if (isset($view_settings['post_relationship_mode'][0])) {
            global $view_settings_table_row;
            $td = wpv_get_table_row_ui_post_post_relationship($view_settings_table_row, null, $view_settings);
        
            echo '<tr class="wpv_filter_row wpv_post_type_filter_row" id="wpv_filter_row_' . $view_settings_table_row . '">' . $td . '</tr>';
            
            $view_settings_table_row++;
        }
    }
    
    /**
     * get the table info for the post_relationship.
     * This is called (via ajax) when we add a post post_relationship filter
     * It's also called to display the existing post post_relationship filter.
     *
     */
    
    function wpv_get_table_row_ui_post_post_relationship($row, $selected, $view_settings = null) {
        
        if (isset($view_settings['post_relationship_mode']) && is_array($view_settings['post_relationship_mode'])) {
            $view_settings['post_relationship_mode'] = $view_settings['post_relationship_mode'][0];
        }
        if (isset($_POST['post_relationship_mode'])) {
            // coming from the add filter button
            $defaults = array('post_relationship_mode' => $_POST['post_relationship_mode']);
            if (isset($_POST['post_relationship_id'])) {
                $defaults['post_relationship_id'] = $_POST['post_relationship_id'];
            }
            
            $view_settings = wp_parse_args($view_settings, $defaults);
        }

        ob_start();
        wpv_add_post_relationship(array('mode' => 'edit',
                             'view_settings' => $view_settings));
        $data = ob_get_clean();
        
        $td = '<td><img src="' . WPV_URL . '/res/img/delete.png" onclick="on_delete_wpv_filter(\'' . $row . '\')" style="cursor: pointer">';
        $td .= '<td class="wpv_td_filter">';
        $td .= "<div id=\"wpv-filter-post_relationship-show\">\n";
        $td .= wpv_get_filter_post_relationship_summary($view_settings);
        $td .= "</div>\n";
        $td .= "<div id=\"wpv-filter-post_relationship-edit\" style='background:" . WPV_EDIT_BACKGROUND . ";display:none'>\n";

        $td .= '<fieldset>';
        $td .= '<legend><strong>' . __('Post Relationship - Post is a child of', 'wpv-views') . ':</strong></legend>';
        $td .= '<div>' . $data . '</div>';
		$td .= '<div id="wpv-post-relationship-info" style="margin-left: 20px;"></div>';
        $td .= '</fieldset>';
        ob_start();
        ?>
            <input class="button-primary" type="button" value="<?php echo __('OK', 'wpv-views'); ?>" name="<?php echo __('OK', 'wpv-views'); ?>" onclick="wpv_show_filter_post_relationship_edit_ok('wpv-filter-post_relationship', 'post_relationship_mode', 'post_relationship_id', 'post_post_relationship')"/>
            <input class="button-secondary" type="button" value="<?php echo __('Cancel', 'wpv-views'); ?>" name="<?php echo __('Cancel', 'wpv-views'); ?>" onclick="wpv_show_filter_post_relationship_edit_cancel()"/>
        <?php
        $td .= ob_get_clean();
        $td .= '</div></td>';
        
        return $td;
    }
    
    function wpv_get_filter_post_relationship_summary_text($view_settings, $short=false) {
        global $wpdb;
        
        ob_start();
        
        if ($view_settings['post_relationship_mode'] == 'current_page') {
            _e('Select related posts that are a <strong>child</strong> of the <strong>post where this View is inserted</strong>.', 'wpv-views');
		} else if ($view_settings['post_relationship_mode'] == 'parent_view') {
            _e('Select related posts that are a <strong>child</strong> to the <strong>Post set by parent View</strong>.', 'wpv-views');
        } else {
            if (isset($view_settings['post_relationship_id']) && $view_settings['post_relationship_id'] > 0) {
                $selected_title = $wpdb->get_var($wpdb->prepare("
                    SELECT post_title FROM {$wpdb->prefix}posts WHERE ID=%d", $view_settings['post_relationship_id']));
            } else {
                $selected_title = 'None';
            }
            echo sprintf(__('Select related posts that are a child of <strong>%s</strong>.', 'wpv-views'), $selected_title);
        }
        
        $data = ob_get_clean();
		
		if ($short) {
			if (substr($data, -1) == '.') {
				$data = substr($data, 0, -1);
			}
		}
        
        return $data;
        
    }
    function wpv_get_filter_post_relationship_summary($view_settings) {
        global $wpdb;
        
        ob_start();
		
		echo wpv_get_filter_post_relationship_summary_text($view_settings);
        
        ?>
        <br />
        <input class="button-secondary" type="button" value="<?php echo __('Edit', 'wpv-views'); ?>" name="<?php echo __('Edit', 'wpv-views'); ?>" onclick="wpv_show_filter_post_relationship_edit('wpv-filter-post_relationship', 'post_relationship_mode', 'post_relationship_id')"/>
        <?php
        
        $data = ob_get_clean();
        
        return $data;
        
    }
}


/**
 * Add the post_relationship filter to the filter popup.
 *
 */

function wpv_add_post_relationship($args) {
	
    global $wpdb;
    
    $edit = isset($args['mode']) && $args['mode'] == 'edit';
    
    $view_settings = isset($args['view_settings']) ? $args['view_settings'] : array();
    
    $defaults = array('post_relationship_mode' => 'current_page',
                      'post_relationship_id' => 0);
    $view_settings = wp_parse_args($view_settings, $defaults);

    wp_nonce_field('wpv_get_posts_select_nonce', 'wpv_get_posts_select_nonce');

	?>

	<div class="post_relationship-div" style="margin-left: 20px;">

        <ul>
            <?php $radio_name = $edit ? '_wpv_settings[post_relationship_mode][]' : 'post_relationship_mode[]' ?>
            <li>
                <?php $checked = $view_settings['post_relationship_mode'] == 'current_page' ? 'checked="checked"' : ''; ?>
                <label><input type="radio" name="<?php echo $radio_name; ?>" value="current_page" <?php echo $checked; ?>>&nbsp;<?php _e('Post where this View is inserted', 'wpv-views'); ?></label>
            </li>
            
            <li>
                <?php $checked = $view_settings['post_relationship_mode'] == 'parent_view' ? 'checked="checked"' : ''; ?>
                <label><input type="radio" name="<?php echo $radio_name; ?>" value="parent_view" <?php echo $checked; ?>>&nbsp;<?php _e('Post set by parent View', 'wpv-views'); ?></label>
            </li>
            
            <li>
                <?php $checked = $view_settings['post_relationship_mode'] == 'this_page' ? 'checked="checked"' : ''; ?>
                <label><input type="radio" name="<?php echo $radio_name; ?>" value="this_page" <?php echo $checked; ?>>&nbsp;<?php _e('Specific:', 'wpv-views'); ?></label>
                
                <?php $select_id = $edit ? 'wpv_post_relationship_post_type' : 'wpv_post_relationship_post_type_add' ?>
                <select id="<?php echo $select_id; ?>">
                <?php
                    $post_types = get_post_types( array('public' => true), 'objects');
                    if ($view_settings['post_relationship_id'] == 0) {
						if ($edit && isset($_POST['post_relationship_type'])) {
							$selected_type = $_POST['post_relationship_type'];
						} else {
							$selected_type = 'page';
						}
                    } else {
                        $selected_type = $wpdb->get_var($wpdb->prepare("
                                SELECT post_type FROM {$wpdb->prefix}posts WHERE ID=%d", $view_settings['post_relationship_id']));
                        if (!$selected_type) {
                            $selected_type = 'page';
                        }
                    }
                    foreach ($post_types as $post_type) {
                        $selected = $selected_type == $post_type->name ? ' selected="selected"' : '';
                        echo '<option value="' . $post_type->name . '"' . $selected . '>' . $post_type->labels->singular_name . '</option>';
                    }
                    
                    
                    
                ?>
                </select>
                
                <?php $post_relationship_select_name = $edit ? '_wpv_settings[post_relationship_id]' : 'wpv_post_relationship_id_add' ?>
                <?php wpv_show_posts_dropdown($selected_type, $post_relationship_select_name, $view_settings['post_relationship_id']); ?>

                <img id="wpv_update_post_relationship" src="<?php echo WPV_URL; ?>/res/img/ajax-loader.gif" width="16" height="16" style="display:none" alt="loading" />
                
            </li>
        </ul>
        
	</div>
    
	<?php
    
    
}


function wpv_ajax_wpv_get_post_relationship_info() {
    if (wp_verify_nonce($_POST['wpv_nonce'], 'wpv_get_posts_select_nonce')) {
	    if (function_exists('wpcf_pr_get_belongs') && isset($_POST['post_types'])) {
			$post_types = get_post_types('', 'objects');
			
			$output_done = false;
			foreach ($_POST['post_types'] as $post_type) {
        
				$related = wpcf_pr_get_belongs($post_type);
				if ($related === false) {
					echo sprintf(__('Post type <strong>%s</strong> doesn\'t belong to any other post type', 'wpv-views'), $post_types[$post_type]->labels->singular_name, $related);
					echo '<br />';
					$output_done = true;
				}
				if (is_array($related) && count($related)) {
					$keys = array_keys($related);
					$related = array();
					
					foreach($keys as$key) {
						$related[] = $post_types[$key]->labels->singular_name;
					}
					
				}
				if (is_array($related) && count($related) == 1) {
					$related = implode(', ', $related);
					echo sprintf(__('Post type <strong>%s</strong> is a child of <strong>%s</strong> post type', 'wpv-views'), $post_types[$post_type]->labels->singular_name, $related);
					echo '<br />';
					$output_done = true;
				}
				if (is_array($related) && count($related) > 1) {
					$last = array_pop($related);
					$related = implode(', ', $related);
					$related .= __(' and ') . $last;
					echo sprintf(__('Post type <strong>%s</strong> is a child of <strong>%s</strong> post types', 'wpv-views'), $post_types[$post_type]->labels->singular_name, $related);
					echo '<br />';
					$output_done = true;
				}
			}
			if ($output_done) {
				echo '<br />';
			}
		}
	
	}	
	die();
}

add_filter('wpv-view-get-summary', 'wpv_post_relationship_summary_filter', 5, 3);

function wpv_post_relationship_summary_filter($summary, $post_id, $view_settings) {
	if(isset($view_settings['query_type']) && $view_settings['query_type'][0] == 'posts' && isset($view_settings['post_relationship_mode'])) {

        $view_settings['post_relationship_mode'] = $view_settings['post_relationship_mode'][0];

		$result = wpv_get_filter_post_relationship_summary_text($view_settings, true);
		if ($result != '' && $summary != '') {
			$summary .= '<br />';
		}
		$summary .= $result;
	}
	
	return $summary;
}

