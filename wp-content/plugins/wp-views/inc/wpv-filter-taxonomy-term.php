<?php

if(is_admin()){
	add_action('init', 'wpv_filter_taxonomy_term_init');
	
	function wpv_filter_taxonomy_term_init() {
        global $pagenow;
        
        if($pagenow == 'post.php' || $pagenow == 'post-new.php'){
            add_action('wpv_add_filter_table_row', 'wpv_add_filter_taxonomy_term_table_row', 1, 1);
            add_filter('wpv_add_filters_taxonomy', 'wpv_add_filter_term_taxonomy', 1, 1);
        }
    }
    
    /**
     * Add a taxonomy term filter
     * This gets added to the popup that shows the available filters.
     *
     */
    
    function wpv_add_filter_term_taxonomy($filters) {
        $filters['taxonomy_term'] = array('name' => 'Taxonomy term',
										'type' => 'callback',
										'callback' => 'wpv_add_term_taxonomy',
										'args' => array());
        return $filters;
    }

    /**
     * get the table row to add the the available filters
     *
     */
    
    function wpv_add_filter_taxonomy_term_table_row($view_settings) {
        if (isset($view_settings['taxonomy_terms']) && sizeof($view_settings['taxonomy_terms']) > 0) {
            global $view_settings_table_row;
            $td = wpv_get_table_row_ui_taxonomy_term($view_settings_table_row, null, $view_settings);
        
            echo '<tr class="wpv_filter_row wpv_taxonomy_filter_row" id="wpv_filter_row_' . $view_settings_table_row . '">' . $td . '</tr>';
            
            $view_settings_table_row++;
        }
    }
    
    /**
     * get the table info for the taxonomy term.
     * This is called (via ajax) when we add a taxonomy term filter
     * It's also called to display the existing taxonomy term filter.
     *
     */
    
    function wpv_get_table_row_ui_taxonomy_term($row, $selected, $view_settings = null) {
        
        if (isset($view_settings['taxonomy_type']) && is_array($view_settings['taxonomy_type'])) {
            $view_settings['taxonomy_type'] = $view_settings['taxonomy_type'][0];
        }
        if ($view_settings == null) {
            // coming from the add filter button
			$defaults['taxonomy_type'] = $_POST['taxonomy'];
            
            if (isset($_POST['taxonomy_term_checks'])) {
                $defaults['taxonomy_terms'] = $_POST['taxonomy_term_checks'];
            } else {
                $defaults['taxonomy_terms'] = array();
            }
            
            $view_settings = wp_parse_args($view_settings, $defaults);
        }

        ob_start();
        wpv_add_term_taxonomy(array('mode' => 'edit',
                             'view_settings' => $view_settings));
        $data = ob_get_clean();
        
        $td = '<td><img src="' . WPV_URL . '/res/img/delete.png" onclick="on_delete_wpv_filter(\'' . $row . '\')" style="cursor: pointer">';
        $td .= '<td class="wpv_td_filter">';
        $td .= "<div id=\"wpv-filter-taxonomy-term-show\">\n";
        $td .= wpv_get_filter_taxonomy_term_summary($view_settings);
        $td .= "</div>\n";
        $td .= "<div id=\"wpv-filter-taxonomy-term-edit\" style='background:" . WPV_EDIT_BACKGROUND . ";display:none'>\n";

        $td .= '<fieldset>';
        $td .= '<legend><strong>' . __('Taxonomy Term', 'wpv-views') . ':</strong></legend>';
        $td .= '<div>' . $data . '</div>';
        $td .= '</fieldset>';
        ob_start();
        ?>
            <input class="button-primary" type="button" value="<?php echo __('OK', 'wpv-views'); ?>" name="<?php echo __('OK', 'wpv-views'); ?>" onclick="wpv_show_taxonomy_term_edit_ok()"/>
            <input class="button-secondary" type="button" value="<?php echo __('Cancel', 'wpv-views'); ?>" name="<?php echo __('Cancel', 'wpv-views'); ?>" onclick="wpv_show_taxonomy_term_edit_cancel()"/>
        <?php
        $td .= ob_get_clean();
        $td .= '</div></td>';
        
        return $td;
    }
    
    function wpv_get_filter_taxonomy_term_summary($view_settings) {
        global $wpdb;
        
        ob_start();
        
    	echo __('Taxonomy is <strong>One</strong> of these', 'wpv-views');
        echo '<strong> (';
		$cat_text = '';
        $category_selected = $view_settings['taxonomy_terms'];
        $taxonomy = $view_settings['taxonomy_type'];
        
		foreach($category_selected as $cat) {
			$term = get_term($cat, $taxonomy);
			if ($cat_text != '') {
				$cat_text .= ', ';
			}
			$cat_text .= $term->name;
		}
		echo $cat_text;
		?>)</strong>

        <br />
        <input class="button-secondary" type="button" value="<?php echo __('Edit', 'wpv-views'); ?>" name="<?php echo __('Edit', 'wpv-views'); ?>" onclick="wpv_show_taxonomy_term_edit()"/>
        <?php
        
        $data = ob_get_clean();
        
        return $data;
        
    }
    
    
}


/**
 * Add the taxonomy term filter to the filter popup.
 *
 */

function wpv_add_term_taxonomy($args) {
	
    global $wpdb;
    
    $edit = isset($args['mode']) && $args['mode'] == 'edit';
    
    $view_settings = isset($args['view_settings']) ? $args['view_settings'] : array();
    
    $defaults = array('taxonomy_terms' => array());
    $view_settings = wp_parse_args($view_settings, $defaults);

    wp_nonce_field('wpv_get_taxonomy_term_check_nonce', 'wpv_get_taxonomy_term_check_nonce');

	?>

	<div id="wpv-taxonomy-term-tax" class="taxonomy-term-div categorydiv" style="margin-left: 20px;">

        <?php
            if (isset($view_settings['taxonomy_type']) && $view_settings['taxonomy_type'] != '') {
                $taxonomy = $view_settings['taxonomy_type'];
            } else {
                $taxonomy = 'category';
            }
            
        ?>
        <input type="hidden" id="wpv-current-taxonomy-term" value="<?php echo $taxonomy; ?>">
        
		<ul class="categorychecklist form-no-clear">
        
        <?php
            ob_start();
            wp_terms_checklist(0, array('taxonomy' => $taxonomy, 'selected_cats' => $view_settings['taxonomy_terms']));
            
            $checklist = ob_get_clean();

            if ($edit) {
                if ($taxonomy == 'category') {
                    $checklist = str_replace('post_category[]', '_wpv_settings[taxonomy_terms][]', $checklist);
                } else {
                    $checklist = str_replace('tax_input[' . $taxonomy . '][]', '_wpv_settings[taxonomy_terms][]', $checklist);
                }
            }
            echo $checklist;
        ?>
        
        </ul>

        <img id="wpv_update_taxonomy_term" src="<?php echo WPV_URL; ?>/res/img/ajax-loader.gif" width="16" height="16" style="display:none" alt="loading" />
        
        
	</div>
    
	<?php
    
    
}

function wpv_get_taxonomy_term_check() {
    if (wp_verify_nonce($_POST['wpv_nonce'], 'wpv_get_taxonomy_term_check_nonce')) {
			
	$taxonomy = $_POST['taxonomy'];
	
        ?>
	<input type="hidden" id="wpv-current-taxonomy-term" value="<?php echo $taxonomy; ?>">
	<ul class="categorychecklist form-no-clear">
	<?php
        echo wp_terms_checklist(0, array('taxonomy' => $taxonomy));
    }
    echo '</ul>';
    die();
	
}

function wpv_ajax_get_taxonomy_term_summary() {
    if (wp_verify_nonce($_POST['wpv_get_taxonomy_term_check_nonce'], 'wpv_get_taxonomy_term_check_nonce')) {
        $_POST['_wpv_settings']['taxonomy_type'] = $_POST['taxonomy_type'];
        if (!isset($_POST['_wpv_settings']['taxonomy_terms'])) {
            $_POST['_wpv_settings']['taxonomy_terms'] = array();
        }
        echo wpv_get_filter_taxonomy_term_summary($_POST['_wpv_settings']);
    }    
    die;
}

add_filter('wpv-view-get-summary', 'wpv_taxonomy_term_summary_filter', 5, 3);

function wpv_taxonomy_term_summary_filter($summary, $post_id, $view_settings) {
	if(isset($view_settings['query_type']) && $view_settings['query_type'][0] == 'taxomomy') {
		ob_start();
		wpv_get_filter_taxonomy_term_summary($view_settings);
		$summary .= ob_get_contents();
		ob_end_clean();
	}
	return $summary;
}

