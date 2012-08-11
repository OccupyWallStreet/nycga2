<?php

if(is_admin()){
	add_action('init', 'wpv_filter_parent_init');
	
	function wpv_filter_parent_init() {
        global $pagenow;
        
        if($pagenow == 'post.php' || $pagenow == 'post-new.php'){
            add_action('wpv_add_filter_table_row', 'wpv_add_filter_parent_table_row', 1, 1);
            add_action('wpv_add_filter_table_row', 'wpv_add_filter_taxonomy_parent_table_row', 1, 1);
            add_filter('wpv_add_filters', 'wpv_add_filter_parent', 1, 1);
            add_filter('wpv_add_filters_taxonomy', 'wpv_add_filter_parent_taxonomy', 1, 1);
        }
    }
    
    /**
     * Add a parent by parent filter
     * This gets added to the popup that shows the available filters.
     *
     */
    
    function wpv_add_filter_parent($filters) {
        $filters['post_parent'] = array('name' => 'Post parent',
										'type' => 'callback',
										'callback' => 'wpv_add_parent',
										'args' => array());
        return $filters;
    }

    /**
     * Add a taxonomy parent filter
     * This gets added to the popup that shows the available filters.
     *
     */
    
    function wpv_add_filter_parent_taxonomy($filters) {
        $filters['taxonomy_parent'] = array('name' => 'Taxonomy parent',
										'type' => 'callback',
										'callback' => 'wpv_add_parent_taxonomy',
										'args' => array());
        return $filters;
    }

    /**
     * get the table row to add the the available filters
     *
     */
    
    function wpv_add_filter_parent_table_row($view_settings) {
        if (isset($view_settings['parent_mode'][0])) {
            global $view_settings_table_row;
            $td = wpv_get_table_row_ui_post_parent($view_settings_table_row, null, $view_settings);
        
            echo '<tr class="wpv_filter_row wpv_post_type_filter_row" id="wpv_filter_row_' . $view_settings_table_row . '">' . $td . '</tr>';
            
            $view_settings_table_row++;
        }
    }
    
    /**
     * get the table info for the parent.
     * This is called (via ajax) when we add a post parent filter
     * It's also called to display the existing post parent filter.
     *
     */
    
    function wpv_get_table_row_ui_post_parent($row, $selected, $view_settings = null) {
        
        if (isset($view_settings['parent_mode']) && is_array($view_settings['parent_mode'])) {
            $view_settings['parent_mode'] = $view_settings['parent_mode'][0];
        }
        if (isset($_POST['parent_mode'])) {
            // coming from the add filter button
            $defaults = array('parent_mode' => $_POST['parent_mode']);
            if (isset($_POST['parent_id'])) {
                $defaults['parent_id'] = $_POST['parent_id'];
            }
            
            $view_settings = wp_parse_args($view_settings, $defaults);
        }

        ob_start();
        wpv_add_parent(array('mode' => 'edit',
                             'view_settings' => $view_settings));
        $data = ob_get_clean();
        
        $td = '<td><img src="' . WPV_URL . '/res/img/delete.png" onclick="on_delete_wpv_filter(\'' . $row . '\')" style="cursor: pointer">';
        $td .= '<td class="wpv_td_filter">';
        $td .= "<div id=\"wpv-filter-parent-show\">\n";
        $td .= wpv_get_filter_parent_summary($view_settings);
        $td .= "</div>\n";
        $td .= "<div id=\"wpv-filter-parent-edit\" style='background:" . WPV_EDIT_BACKGROUND . ";display:none'>\n";

        $td .= '<fieldset>';
        $td .= '<legend><strong>' . __('Post parent', 'wpv-views') . ':</strong></legend>';
        $td .= '<div>' . $data . '</div>';
        $td .= '</fieldset>';
        ob_start();
        ?>
            <input class="button-primary" type="button" value="<?php echo __('OK', 'wpv-views'); ?>" name="<?php echo __('OK', 'wpv-views'); ?>" onclick="wpv_show_filter_parent_edit_ok('wpv-filter-parent', 'parent_mode', 'parent_id', 'post_parent')"/>
            <input class="button-secondary" type="button" value="<?php echo __('Cancel', 'wpv-views'); ?>" name="<?php echo __('Cancel', 'wpv-views'); ?>" onclick="wpv_show_filter_parent_edit_cancel()"/>
        <?php
        $td .= ob_get_clean();
        $td .= '</div></td>';
        
        return $td;
    }
    
    function wpv_get_filter_parent_summary_text($view_settings, $short = false) {
        global $wpdb;
        
        ob_start();
        
        if ($view_settings['parent_mode'] == 'current_page') {
            if ($short) {
				_e('parent is the <strong>current page</strong>', 'wpv-views');
			} else {
				_e('Select posts whose parent is the <strong>current page</strong>.', 'wpv-views');
			}
        } else {
            if (isset($view_settings['parent_id']) && $view_settings['parent_id'] > 0) {
                $selected_title = $wpdb->get_var($wpdb->prepare("
                    SELECT post_title FROM {$wpdb->prefix}posts WHERE ID=%d", $view_settings['parent_id']));
            } else {
                $selected_title = 'None';
            }
            if ($short) {
	            echo sprintf(__('parent is <strong>%s</strong>', 'wpv-views'), $selected_title);
			} else {
	            echo sprintf(__('Select posts whose parent is <strong>%s</strong>.', 'wpv-views'), $selected_title);
			}
        }
        
        $data = ob_get_clean();
        
        return $data;
        
    }

    function wpv_get_filter_parent_summary($view_settings) {
        global $wpdb;
        
        ob_start();

		echo wpv_get_filter_parent_summary_text($view_settings);        
        
        ?>
        <br />
        <input class="button-secondary" type="button" value="<?php echo __('Edit', 'wpv-views'); ?>" name="<?php echo __('Edit', 'wpv-views'); ?>" onclick="wpv_show_filter_parent_edit('wpv-filter-parent', 'parent_mode', 'parent_id')"/>
        <?php
        
        $data = ob_get_clean();
        
        return $data;
        
    }

    /**
     * get the table row to add the the available filters
     *
     */
    
    function wpv_add_filter_taxonomy_parent_table_row($view_settings) {
        if (isset($view_settings['taxonomy_parent_mode'][0]) && $view_settings['taxonomy_parent_mode'][0] != '') {
            global $view_settings_table_row;
            $td = wpv_get_table_row_ui_taxonomy_parent($view_settings_table_row, null, $view_settings);
        
            echo '<tr class="wpv_filter_row wpv_taxonomy_filter_row" id="wpv_filter_row_' . $view_settings_table_row . '">' . $td . '</tr>';
            
            $view_settings_table_row++;
        }
    }
    
    /**
     * get the table info for the parent.
     * This is called (via ajax) when we add a post parent filter
     * It's also called to display the existing post parent filter.
     *
     */
    
    function wpv_get_table_row_ui_taxonomy_parent($row, $selected, $view_settings = null) {
        
        if (isset($view_settings['taxonomy_parent_mode']) && is_array($view_settings['taxonomy_parent_mode'])) {
            $view_settings['taxonomy_parent_mode'] = $view_settings['taxonomy_parent_mode'][0];
        }
        if (isset($view_settings['taxonomy_type']) && is_array($view_settings['taxonomy_type']) && sizeof($view_settings['taxonomy_type']) > 0 ) {
            $view_settings['taxonomy_type'] = $view_settings['taxonomy_type'][0];
        }
        if (isset($_POST['parent_mode'])) {
            // coming from the add filter button
            $defaults = array('taxonomy_parent_mode' => $_POST['parent_mode']);
            if (isset($_POST['parent_id'])) {
                $defaults['taxonomy_parent_id'] = $_POST['parent_id'];
            }
			$defaults['taxonomy_type'] = $_POST['taxonomy'];
            
            $view_settings = wp_parse_args($view_settings, $defaults);
        }

        ob_start();
        wpv_add_parent_taxonomy(array('mode' => 'edit',
                             'view_settings' => $view_settings));
        $data = ob_get_clean();
        
        $td = '<td><img src="' . WPV_URL . '/res/img/delete.png" onclick="on_delete_wpv_filter(\'' . $row . '\')" style="cursor: pointer">';
        $td .= '<td class="wpv_td_filter">';
        $td .= "<div id=\"wpv-filter-taxonomy-parent-show\">\n";
        $td .= wpv_get_filter_taxonomy_parent_summary($view_settings);
        $td .= "</div>\n";
        $td .= "<div id=\"wpv-filter-taxonomy-parent-edit\" style='background:" . WPV_EDIT_BACKGROUND . ";display:none'>\n";

        $td .= '<fieldset>';
        $td .= '<legend><strong>' . __('Taxonomy parent', 'wpv-views') . ':</strong></legend>';
        $td .= '<div>' . $data . '</div>';
        $td .= '</fieldset>';
        ob_start();
        ?>
            <input class="button-primary" type="button" value="<?php echo __('OK', 'wpv-views'); ?>" name="<?php echo __('OK', 'wpv-views'); ?>" onclick="wpv_show_filter_parent_edit_ok('wpv-filter-taxonomy-parent', 'taxonomy_parent_mode', 'taxonomy_parent_id', 'taxonomy_parent')"/>
            <input class="button-secondary" type="button" value="<?php echo __('Cancel', 'wpv-views'); ?>" name="<?php echo __('Cancel', 'wpv-views'); ?>" onclick="wpv_show_filter_taxonomy_parent_edit_cancel()"/>
        <?php
        $td .= ob_get_clean();
        $td .= '</div></td>';
        
        return $td;
    }
    
    function wpv_get_filter_taxonomy_parent_summary($view_settings) {
        global $wpdb;
        
        ob_start();
        
        if ($view_settings['taxonomy_parent_mode'] == 'current_view') {
            _e('Select taxonomy whose parent is the value set by the <strong>parent view</strong>.', 'wpv-views');
        } else {
            if (isset($view_settings['taxonomy_parent_id']) && $view_settings['taxonomy_parent_id'] > 0) {
                $selected_taxonomy = get_term($view_settings['taxonomy_parent_id'], $view_settings['taxonomy_type']);
				$selected_taxonomy = $selected_taxonomy->name;
            } else {
                $selected_taxonomy = 'None';
            }
            echo sprintf(__('Select taxonomy whose parent is <strong>%s</strong>.', 'wpv-views'), $selected_taxonomy);
        }
        
        ?>
        <br />
        <input class="button-secondary" type="button" value="<?php echo __('Edit', 'wpv-views'); ?>" name="<?php echo __('Edit', 'wpv-views'); ?>" onclick="wpv_show_filter_parent_edit('wpv-filter-taxonomy-parent', 'taxonomy_parent_mode', 'taxonomy_parent_id')"/>
        <?php
        
        $data = ob_get_clean();
        
        return $data;
        
    }
    
    
}

/**
 * Add the parent filter to the filter popup.
 *
 */

function wpv_add_parent($args) {
	
    global $wpdb;
    
    $edit = isset($args['mode']) && $args['mode'] == 'edit';
    
    $view_settings = isset($args['view_settings']) ? $args['view_settings'] : array();
    
    $defaults = array('parent_mode' => 'current_page',
                      'parent_id' => 0);
    $view_settings = wp_parse_args($view_settings, $defaults);

    wp_nonce_field('wpv_get_posts_select_nonce', 'wpv_get_posts_select_nonce');

	?>

	<div class="parent-div" style="margin-left: 20px;">

        <ul>
            <?php $radio_name = $edit ? '_wpv_settings[parent_mode][]' : 'parent_mode[]' ?>
            <li>
                <?php $checked = $view_settings['parent_mode'] == 'current_page' ? 'checked="checked"' : ''; ?>
                <label><input type="radio" name="<?php echo $radio_name; ?>" value="current_page" <?php echo $checked; ?>>&nbsp;<?php _e('Parent is the current page', 'wpv-views'); ?></label>
            </li>
            
            <li>
                <?php $checked = $view_settings['parent_mode'] == 'this_page' ? 'checked="checked"' : ''; ?>
                <label><input type="radio" name="<?php echo $radio_name; ?>" value="this_page" <?php echo $checked; ?>>&nbsp;<?php _e('Parent is:', 'wpv-views'); ?></label>
                
                <?php $select_id = $edit ? 'wpv_parent_post_type' : 'wpv_parent_post_type_add' ?>
                <select id="<?php echo $select_id; ?>">
                <?php
                    $hierarchical_post_types = get_post_types( array( 'hierarchical' => true ), 'objects');
                    if ($view_settings['parent_id'] == 0) {
                        $selected_type = 'page';
                    } else {
                        $selected_type = $wpdb->get_var($wpdb->prepare("
                                SELECT post_type FROM {$wpdb->prefix}posts WHERE ID=%d", $view_settings['parent_id']));
                        if (!$selected_type) {
                            $selected_type = 'page';
                        }
                    }
                    foreach ($hierarchical_post_types as $post_type) {
                        $selected = $selected_type == $post_type->name ? ' selected="selected"' : '';
                        echo '<option value="' . $post_type->name . '"' . $selected . '>' . $post_type->labels->singular_name . '</option>';
                    }
                    
                    
                    
                ?>
                </select>
                
                <?php $parent_select_name = $edit ? '_wpv_settings[parent_id]' : 'wpv_parent_id_add' ?>
                <?php wp_dropdown_pages(array('name'=>$parent_select_name, 'selected'=>$view_settings['parent_id'], 'post_type'=> $selected_type, 'show_option_none' => __('None', 'wpv-views'))); ?>

                <img id="wpv_update_parent" src="<?php echo WPV_URL; ?>/res/img/ajax-loader.gif" width="16" height="16" style="display:none" alt="loading" />
                
            </li>
        </ul>
        
	</div>
    
	<?php
    
    
}

/**
 * Add the taxonomy parent filter to the filter popup.
 *
 */

function wpv_add_parent_taxonomy($args) {
	
    global $wpdb;
    
    $edit = isset($args['mode']) && $args['mode'] == 'edit';
    
    $view_settings = isset($args['view_settings']) ? $args['view_settings'] : array();
    
    $defaults = array('taxonomy_parent_mode' => 'current_view',
                      'taxonomy_parent_id' => 0);
    $view_settings = wp_parse_args($view_settings, $defaults);

    wp_nonce_field('wpv_get_taxonomy_select_nonce', 'wpv_get_taxonomy_select_nonce');

	?>

	<div class="taxonomy-parent-div" style="margin-left: 20px;">

        <ul>
            <?php $radio_name = $edit ? '_wpv_settings[taxonomy_parent_mode][]' : 'taxonomy_parent_mode[]' ?>
            <li>
                <?php $checked = $view_settings['taxonomy_parent_mode'] == 'current_view' ? 'checked="checked"' : ''; ?>
                <label><input type="radio" name="<?php echo $radio_name; ?>" value="current_view" <?php echo $checked; ?>>&nbsp;<?php _e('Parent is the taxonomy selected by the <strong>parent view</strong>', 'wpv-views'); ?></label>
            </li>
            
            <li>
                <?php $checked = $view_settings['taxonomy_parent_mode'] == 'this_parent' ? 'checked="checked"' : ''; ?>
                <label><input type="radio" name="<?php echo $radio_name; ?>" value="this_parent" <?php echo $checked; ?>>&nbsp;<?php _e('Parent is:', 'wpv-views'); ?></label>

				<?php
					if (isset($view_settings['taxonomy_type']) && $view_settings['taxonomy_type'] != '') {
						$taxonomy = $view_settings['taxonomy_type'];
					} else {
						$taxonomy = 'category';
					}
					$parent_select_name = $edit ? '_wpv_settings[taxonomy_parent_id]' : 'wpv_taxonomy_parent_id'
					
				?>
				<input type="hidden" id="wpv-current-taxonomy-parent" value="<?php echo $taxonomy; ?>">
				
				<select name="<?php echo $parent_select_name; ?>">
					<option value="0"><?php echo __('None', 'wpv-views'); ?></option>
					<?php $my_walker = new Walker_Category_select($view_settings['taxonomy_parent_id']);
					
	
					echo wp_terms_checklist(0, array('taxonomy' => $taxonomy, 'walker' => $my_walker));
					?>
				</select>

                <img id="wpv_update_taxonomy_parent" src="<?php echo WPV_URL; ?>/res/img/ajax-loader.gif" width="16" height="16" style="display:none" alt="loading" />
                
            </li>
        </ul>
        
	</div>
    
	<?php
    
    
}


function wpv_get_posts_select() {
    if (wp_verify_nonce($_POST['wpv_nonce'], 'wpv_get_posts_select_nonce')) {
		wpv_show_posts_dropdown($_POST['post_type']);
    }
    die();
}

function wpv_show_posts_dropdown($post_type, $name = '_wpv_settings[parent_id]', $selected = 0) {

	$hierarchical_post_types = get_post_types( array( 'hierarchical' => true ) );
	
	$hierarchical = in_array($post_type, $hierarchical_post_types) ? 1 : 0;
	
	$attr = array('name'=> $name,
				  'post_type' => $post_type,
				  'show_option_none' => __('None', 'wpv-views'),
				  'selected' => $selected);
	
	if ($hierarchical) {
		wp_dropdown_pages($attr);
	} else {
		$defaults = array(
			'depth' => 0, 'child_of' => 0,
			'selected' => $selected, 'echo' => 1,
			'name' => 'page_id', 'id' => '',
			'show_option_none' => '', 'show_option_no_change' => '',
			'option_none_value' => ''
		);
		$r = wp_parse_args( $attr, $defaults );
		extract( $r, EXTR_SKIP );
		
		$pages = get_posts(array('numberposts' => -1, 'post_type' => $post_type, 'suppress_filters' => false));
		$output = '';
		// Back-compat with old system where both id and name were based on $name argument
		if ( empty($id) )
			$id = $name;
	
		if ( ! empty($pages) ) {
			$output = "<select name='" . esc_attr( $name ) . "' id='" . esc_attr( $id ) . "'>\n";
			if ( $show_option_no_change )
				$output .= "\t<option value=\"-1\">$show_option_no_change</option>";
			if ( $show_option_none )
				$output .= "\t<option value=\"" . esc_attr($option_none_value) . "\">$show_option_none</option>\n";
			$output .= walk_page_dropdown_tree($pages, $depth, $r);
			$output .= "</select>\n";
		}
	
		echo $output;	
	}
}
function wpv_get_taxonomy_parents_select() {
    if (wp_verify_nonce($_POST['wpv_nonce'], 'wpv_get_taxonomy_select_nonce')) {
		?>
		<select name="wpv_taxonomy_parent_id">
			<option selected="selected" value="0"><?php echo __('None', 'wpv-views'); ?></option>
			<?php $my_walker = new Walker_Category_select(0);
			
			$taxonomy = $_POST['taxonomy'];
	
			echo wp_terms_checklist(0, array('taxonomy' => $taxonomy, 'walker' => $my_walker));
		?>
		</select>
		<?php
    }
    die();
	
}

add_filter('wpv-view-get-summary', 'wpv_parent_summary_filter', 5, 3);

function wpv_parent_summary_filter($summary, $post_id, $view_settings) {
	if(isset($view_settings['query_type']) && $view_settings['query_type'][0] == 'posts' && isset($view_settings['parent_mode'][0])) {
        $view_settings['parent_mode'] = $view_settings['parent_mode'][0];
		$result = wpv_get_filter_parent_summary_text($view_settings, true);
		if ($result != '' && $summary != '') {
			$summary .= '<br />';
		}
		$summary .= $result;
	}
	return $summary;
}


