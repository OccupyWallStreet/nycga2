<?php

if(is_admin()){
	
	add_action('init', 'wpv_filter_category_init');
	
	function wpv_filter_category_init() {
		global $pagenow;
		
		if($pagenow == 'post.php' || $pagenow == 'post-new.php'){
			add_action('wpv_add_filter_table_row', 'wpv_add_filter_category_table_row', 2, 1);
			add_filter('wpv_add_filters', 'wpv_add_filter_category', 2, 1);
		}
	}
	
	/**
	 * Add a filter for each taxonomy type
	 *
	 */
	
    function wpv_add_filter_category($filters) {
	    $taxonomies = get_taxonomies('', 'objects');
		foreach ($taxonomies as $category_slug => $category) {
			if ($category_slug == 'nav_menu' || $category_slug == 'link_category'
					|| $category_slug == 'post_format') {
				continue;
			}
			
			$taxonomy = $category->name;
			$name = ( $taxonomy == 'category' ) ? 'post_category' : 'tax_input[' . $taxonomy . ']';
			
			$filters[$name] = array('name' => $category->label,
										'type' => 'callback',
										'callback' => 'wpv_add_category_checkboxes',
										'args' => array('name' => $name, 'taxonomy' => $taxonomy));
		}

		// add a nonce field here.
		wp_nonce_field('wpv_add_taxonomy_nonce', 'wpv_add_taxonomy_nonce');
		
        return $filters;
    }

	/**
	 * Return a table row for a category filter
	 *
	 */
	
    function wpv_add_filter_category_table_row($view_settings) {
		global $view_settings_table_row;

		if (!isset($view_settings['taxonomy_relationship'])) {
			$view_settings['taxonomy_relationship'] = 'OR';
		}
		
		// Find any taxonomy
		
		$summary = '';
		$count = 0;
		
		$taxonomies = get_taxonomies('', 'objects');
		foreach ($taxonomies as $category_slug => $category) {
			$save_name = ( $category->name == 'category' ) ? 'post_category' : 'tax_input_' . $category->name;
			$relationship_name = ( $category->name == 'category' ) ? 'tax_category_relationship' : 'tax_' . $category->name . '_relationship';
			
			if (isset($view_settings[$relationship_name])) {
				
				if (!isset($view_settings[$save_name])) {
					$view_settings[$save_name] = array();
				}
		
				$name = ( $category->name == 'category' ) ? 'post_category' : 'tax_input[' . $category->name . ']';
				$td = wpv_get_table_row_ui_post_category($view_settings_table_row, $name, $view_settings[$save_name], null, $view_settings);
				echo '<tr class="wpv_taxonomy_edit_row wpv_filter_row wpv_post_type_filter_row wpv_edit_row" id="wpv_filter_row_' . $view_settings_table_row . '" style="background:' . WPV_EDIT_BACKGROUND . '; display:none;">' . $td . '</tr>';
            
				$view_settings_table_row++;
				$count++;
				
				if ($summary != '') {
					if ($view_settings['taxonomy_relationship'] == 'OR') {
						$summary .= __(' OR ', 'wpv-views');
					} else {
						$summary .= __(' AND ', 'wpv-views');
					}
				}
				
				$summary .= wpv_get_taxonomy_summary($name, $view_settings, $view_settings[$save_name]);
					
			}
		}
		
		if ($summary != '') {
			if ($count > 1) {
				echo '<tr class="wpv_taxonomy_edit_row wpv_filter_row wpv_post_type_filter_row wpv_edit_row" id="wpv_filter_row_' . $view_settings_table_row . '" style="background:' . WPV_EDIT_BACKGROUND . '; display:none;">';
				wpv_filter_taxonomy_relationship_admin($view_settings);			
				echo '</tr>';
			
				$view_settings_table_row++;
			}
			echo '<tr class="wpv_taxonomy_edit_row wpv_filter_row wpv_post_type_filter_row wpv_edit_row" id="wpv_filter_row_' . $view_settings_table_row . '" style="background:' . WPV_EDIT_BACKGROUND . '; display:none;"><td></td><td>';
			?>
				<?php
					$filters = wpv_add_filter_category(array());
					wpv_filter_add_filter_admin($view_settings, $filters, 'popup_add_category_field', 'Add another category');
				?>
				<hr />
				<div class="wpv_taxonomy_param_missing_ok"><?php echo __('A taxonomy parameter is missing or incorrect.', 'wpv-views'); ?></div>
				<input class="button-primary" type="button" value="<?php echo __('OK', 'wpv-views'); ?>" name="<?php echo __('OK', 'wpv-views'); ?>" onclick="wpv_show_filter_taxonomy_edit_ok()"/>
				<input class="button-secondary" type="button" value="<?php echo __('Cancel', 'wpv-views'); ?>" name="<?php echo __('Cancel', 'wpv-views'); ?>" onclick="wpv_show_filter_taxonomy_edit_cancel()"/>
				<span class="wpv-taxonomy-help"><i>
					<?php echo sprintf(__('%sLearn about filtering by taxonomy%s', 'wpv-views'),
								   '<a href="' . WPV_FILTER_BY_TAXONOMY_LINK . '" target="_blank">',
								   ' &raquo;</a>'
								   ); ?>
				</i></span>
				
			<?php
			
			echo '</td></tr>';
		
			$view_settings_table_row++;

			echo '<tr class="wpv_taxonomy_show_row wpv_filter_row wpv_post_type_filter_row" id="wpv_filter_row_' . $view_settings_table_row . '"><td><img src="' . WPV_URL . '/res/img/delete-disabled.png" title="' . __('Edit this filters group to delete items', 'wpv-views') . '"></td><td>';
			_e('Select posts with taxonomy: ', 'wpv-views');
			echo $summary;
			
			?>
			<br />
			<input class="button-secondary" type="button" value="<?php echo __('Edit', 'wpv-views'); ?>" name="<?php echo __('Edit', 'wpv-views'); ?>" onclick="wpv_show_filter_taxonomy_edit()"/>
			<?php
			
			echo '</td></tr>';

			$view_settings_table_row++;
		}

    }

	/**
	 * Add the taxonomy relationship controls to the view query metabox
	 *
	 */
	
	function wpv_filter_taxonomy_relationship_admin($view_settings) {
		if (!isset($view_settings['taxonomy_relationship'])) {
			$view_settings['taxonomy_relationship'] = '';
		}
		?>
		
		<td></td>
		<td>
			<fieldset>
				<legend><strong><?php _e('Taxonomy relationship:', 'wpv-views') ?></strong></legend>            
				<?php _e('Relationship to use when querying with multiple taxonomies:', 'wpv-views'); ?>
				<select name="_wpv_settings[taxonomy_relationship]">            
					<option value="OR"><?php _e('OR', 'wpv-views'); ?>&nbsp;</option>
					<?php $selected = $view_settings['taxonomy_relationship']=='AND' ? ' selected="selected"' : ''; ?>
					<option value="AND" <?php echo $selected ?>><?php _e('AND', 'wpv-views'); ?>&nbsp;</option>
				</select>
				
			</fieldset>
		</td>

		<?php
	}
    
	function wpv_ajax_add_taxonomy() {
		if (wp_verify_nonce($_POST['wpv_nonce'], 'wpv_add_taxonomy_nonce')) {
			global $view_settings_table_row;
			
			if (isset($_POST['taxonomy_name'])) {
				$taxonomy = array();
				for($i = 0; $i < sizeof($_POST['taxonomy_name']); $i++) {
					$name = $_POST['taxonomy_name'][$i];
					if ($name == 'category') {
						$view_name = 'post_category';
					} else {
						$view_name = 'tax_input_' . $name;
					}
					if ($_POST['taxonomy_value'][$i] == '') {
						$taxonomy[$view_name] = array();
					} else {
						$taxonomy[$view_name] = explode(',', $_POST['taxonomy_value'][$i]);
					}
					$taxonomy['tax_' . $name . '_relationship'] = $_POST['taxonomy_relationship'][$i];
					$taxonomy['taxonomy-' . $name . '-attribute-url'] = $_POST['taxonomy_attribute_url'][$i];
					if (isset($_POST['taxonomy_attribute_url_format'][$i])) {
						$taxonomy['taxonomy-' . $name . '-attribute-url-format'] = $_POST['taxonomy_attribute_url_format'][$i];
					} else {
						$taxonomy['taxonomy-' . $name . '-attribute-url-format'] = 'name';
					}
				}
				
				$taxonomy['taxonomy_relationship'] = $_POST['taxonomys_relationship'];
				$view_settings_table_row = $_POST['row'];
				
				wpv_add_filter_category_table_row($taxonomy);
			}
		}
		die();
	}
}

function wpv_get_taxonomy_summary($type, $view_settings, $category_selected) {
	// find the matching category/taxonomy
	$taxonomy = 'category';
	$taxonomy_name = __('Categories', 'wpv-views');
	$taxonomies = get_taxonomies('', 'objects');
	foreach ($taxonomies as $category_slug => $category) {
		$name = ( $category->name == 'category' ) ? 'post_category' : 'tax_input[' . $category->name . ']';
		
		if ($name == $type) {
			// it's a category type.
			$taxonomy = $category->name;
			$taxonomy_name = $category->label;
			break;
		}
	}
	
	if (!isset($view_settings['tax_' . $taxonomy . '_relationship'])) {
		$view_settings['tax_' . $taxonomy . '_relationship'] = 'IN';
	}
	if (!isset($view_settings['taxonomy-' . $taxonomy . '-attribute-url'])) {
		$view_settings['taxonomy-' . $taxonomy . '-attribute-url'] = '';
	}
	
	$relationship = __('is <strong>One</strong> of these', 'wpv-views');
	switch($view_settings['tax_' . $taxonomy . '_relationship']) {
		case "AND":
			$relationship = __('is <strong>All</strong> of these', 'wpv-views');
			break;
		
		case "NOT IN":
			$relationship = __('is <strong>Not one</strong> of these', 'wpv-views');
			break;

		case "FROM PAGE":
			$relationship = __('the same as the <strong>current page</strong>', 'wpv-views');
			break;

		case "FROM ATTRIBUTE":
			$relationship = __('the same as set by the View shortcode attribute ', 'wpv-views');
			break;

		case "FROM URL":
			$relationship = __('the same as set by the URL parameter ', 'wpv-views');
			break;

		case "FROM PARENT VIEW":
			$relationship = ', ' . __('selected by the parent view.', 'wpv-views');
			break;
	}
	
	ob_start();
	
	if ($view_settings['tax_' . $taxonomy . '_relationship'] == "FROM PAGE") {
		echo '<strong>' . $taxonomy_name . ' </strong>' . $relationship;
	} else if ($view_settings['tax_' . $taxonomy . '_relationship'] == "FROM ATTRIBUTE" || $view_settings['tax_' . $taxonomy . '_relationship'] == "FROM URL") {
		echo '<strong>' . $taxonomy_name . ' </strong>' . $relationship;
		echo '<strong>"' . $view_settings['taxonomy-' . $taxonomy . '-attribute-url'] . '"</strong> ';
		if ($view_settings['tax_' . $taxonomy . '_relationship'] == "FROM ATTRIBUTE") {
			echo sprintf(__('eg. [wpv-views name="view-name" <strong>%s="xxxx"</strong>]', 'wpv-views'), $view_settings['taxonomy-' . $taxonomy . '-attribute-url']);
		} else {
			echo sprintf(__('eg. http://www.example.com/page/?<strong>%s="xxxx"</strong>', 'wpv-views'), $view_settings['taxonomy-' . $taxonomy . '-attribute-url']);
		}
	} else if ($view_settings['tax_' . $taxonomy . '_relationship'] == "FROM PARENT VIEW") {
		echo '<strong>' . $taxonomy_name . ' </strong>' . $relationship;
	} else {
		?>
		<strong><?php echo $taxonomy_name . ' </strong>' . $relationship . ' <strong>(';
		$cat_text = '';
		foreach($category_selected as $cat) {
			$term = get_term($cat, $taxonomy);
			if ($cat_text != '') {
				$cat_text .= ', ';
			}
			$cat_text .= $term->name;
		}
		echo $cat_text;
		?>)</strong>
		
		<?php
	}
	
	$buffer = ob_get_clean();
	
	return $buffer;
}

/**
 * get the table info for the category.
 * This is called (via ajax) when we add a category filter
 * It's also called to display the existing category filter.
 *
 */

function wpv_get_table_row_ui_post_category($row, $type, $cats_selected, $not_used, $view_settings = array()) {

	// find the matching category/taxonomy
	$taxonomy = 'category';
	$taxonomy_name = __('Categories', 'wpv-views');
	$taxonomies = get_taxonomies('', 'objects');
	foreach ($taxonomies as $category_slug => $category) {
		$name = ( $category->name == 'category' ) ? 'post_category' : 'tax_input[' . $category->name . ']';
		
		if ($name == $type) {
			// it's a category type.
			$taxonomy = $category->name;
			$taxonomy_name = $category->label;
			break;
		}
	}
	
	if (!isset($view_settings['tax_' . $taxonomy . '_relationship'])) {
		$view_settings['tax_' . $taxonomy . '_relationship'] = 'IN';
	}
	
	if (isset($view_settings['taxonomy-' . $taxonomy . '-attribute-url-format']) && is_array($view_settings['taxonomy-' . $taxonomy . '-attribute-url-format'])) {
		$view_settings['taxonomy-' . $taxonomy . '-attribute-url-format'] = $view_settings['taxonomy-' . $taxonomy . '-attribute-url-format'][0];
	}
	
	ob_start();
	?>
	<td>
		<img src="<?php echo WPV_URL; ?>/res/img/delete.png" onclick="on_delete_wpv_filter('<?php echo $row; ?>')" style="cursor: pointer">
	</td>
	<td class="wpv_td_filter">
		<fieldset>
			<legend><strong><?php echo __('Taxonomy', 'wpv-views') . ' - ' . $taxonomy_name; ?>:</strong></legend>

			<div style="margin-left: 20px;">

				<?php _e('Taxonomy is:', 'wpv-views'); ?>
				<select class="wpv_taxonomy_relationship" name="_wpv_settings[tax_<?php echo $taxonomy; ?>_relationship]">            
					<option value="IN"><?php _e('Any of the following', 'wpv-views'); ?>&nbsp;</option>
					<?php $selected = $view_settings['tax_' . $taxonomy . '_relationship']=='NOT IN' ? ' selected="selected"' : ''; ?>
					<option value="NOT IN" <?php echo $selected ?>><?php _e('NOT one of the following', 'wpv-views'); ?>&nbsp;</option>
					<?php $selected = $view_settings['tax_' . $taxonomy . '_relationship']=='AND' ? ' selected="selected"' : ''; ?>
					<option value="AND" <?php echo $selected ?>><?php _e('All of the following', 'wpv-views'); ?>&nbsp;</option>
					<?php $selected = $view_settings['tax_' . $taxonomy . '_relationship']=='FROM PAGE' ? ' selected="selected"' : ''; ?>
					<option value="FROM PAGE" <?php echo $selected ?>><?php _e('Value set by the current page', 'wpv-views'); ?>&nbsp;</option>
					<?php $selected = $view_settings['tax_' . $taxonomy . '_relationship']=='FROM ATTRIBUTE' ? ' selected="selected"' : ''; ?>
					<option value="FROM ATTRIBUTE" <?php echo $selected ?>><?php _e('Value set by View shortcode attribute', 'wpv-views'); ?>&nbsp;</option>
					<?php $selected = $view_settings['tax_' . $taxonomy . '_relationship']=='FROM URL' ? ' selected="selected"' : ''; ?>
					<option value="FROM URL" <?php echo $selected ?>><?php _e('Value set by URL paremeter', 'wpv-views'); ?>&nbsp;</option>
					<?php $selected = $view_settings['tax_' . $taxonomy . '_relationship']=='FROM PARENT VIEW' ? ' selected="selected"' : ''; ?>
					<option value="FROM PARENT VIEW" <?php echo $selected ?>><?php _e('Value set by parent view', 'wpv-views'); ?>&nbsp;</option>
				</select>
	
				<?php
					// list the categories as checkboxes
				?>
					
				<?php
					$show = '';
					switch ($view_settings['tax_' . $taxonomy . '_relationship']) {
						case 'FROM PAGE':
						case 'FROM ATTRIBUTE':
						case 'FROM URL':
						case 'FROM PARENT VIEW':
							$show = ' style="display:none"';
							break;
					}
					?>	
				<div  id="taxonomy-<?php echo $taxonomy_name; ?>" class="categorydiv"<?php echo $show; ?>>
					<ul class="categorychecklist form-no-clear">
			
					<?php wp_terms_checklist(0, array('taxonomy' => $taxonomy, 'selected_cats' => $cats_selected)) ?>
					
					</ul>
				</div>
	
				<?php
					$show = ' style="display:none"';
					switch ($view_settings['tax_' . $taxonomy . '_relationship']) {
						case 'FROM ATTRIBUTE':
						case 'FROM URL':
							$show = '';
							break;
					}
					
					if (!isset($view_settings['taxonomy-' . $taxonomy . '-attribute-url'])) {
						$view_settings['taxonomy-' . $taxonomy . '-attribute-url'] = '';
					}
					?>	
				<div  id="taxonomy-<?php echo $taxonomy_name; ?>-attribute-url" <?php echo $show; ?>>
					<span class="attribute"<?php if ($view_settings['tax_' . $taxonomy . '_relationship'] != 'FROM ATTRIBUTE') {echo ' style="display:none;"';}?>><?php echo __('Shortcode attribute', 'wpv-views');?></span>
					<span class="url"<?php if ($view_settings['tax_' . $taxonomy . '_relationship'] != 'FROM URL') {echo ' style="display:none;"';}?>><?php echo __('URL parameter', 'wpv-views');?></span>
					: <input type="text" class="wpv_taxonomy_param" name="_wpv_settings[taxonomy-<?php echo $taxonomy; ?>-attribute-url]" value="<?php echo $view_settings['taxonomy-' . $taxonomy . '-attribute-url']; ?>">
					<span class="wpv_taxonomy_param_missing"><?php echo __('<- Please enter a value here', 'wpv-views'); ?></span>
					<?php echo __('Using : ');?>
					<?php
						if (!isset($view_settings['taxonomy-' . $taxonomy . '-attribute-url-format'])) {
							$view_settings['taxonomy-' . $taxonomy . '-attribute-url-format'] = 'name';
						}
					?>
					<?php $checked = $view_settings['taxonomy-' . $taxonomy . '-attribute-url-format'] == 'name' ? 'checked="checked"' : ''; ?>
					<label><input type="radio" name="_wpv_settings[taxonomy-<?php echo $taxonomy; ?>-attribute-url-format][]" value="name" <?php echo $checked;?>><?php echo __('Taxonomy name', 'wpv-views');?></label>
					<?php $checked = $view_settings['taxonomy-' . $taxonomy . '-attribute-url-format'] == 'slug' ? 'checked="checked"' : ''; ?>
					<label><input type="radio" name="_wpv_settings[taxonomy-<?php echo $taxonomy; ?>-attribute-url-format][]" value="slug" <?php echo $checked;?>><?php echo __('Taxonomy slug', 'wpv-views');?></label>
					
				</div>

			</div>
			
		</fieldset>
	</td>
	
	<?php
	
	$buffer = ob_get_clean();
	
	$buffer = str_replace($type . '[]', '_wpv_settings[' . str_replace(array('[', ']'), array('_', ''), $type) . '][]', $buffer);

	return $buffer;
}

/**
 * Used to display the categories in the add filter popup.
 *
 */

function wpv_add_category_checkboxes($args) {
	
	?>

	<div id="taxonomy-<?php echo $args['taxonomy']; ?>" class="categorydiv" style="margin-left: 20px;">

		<?php _e('Taxonomy is:', 'wpv-views'); ?>
		<select class="wpv_taxonomy_relationship" name="tax_<?php echo $args['taxonomy']; ?>_relationship">            
			<option value="IN"><?php _e('Any of the following', 'wpv-views'); ?>&nbsp;</option>
			<option value="NOT IN"><?php _e('NOT one of the following', 'wpv-views'); ?>&nbsp;</option>
			<option value="AND"><?php _e('All of the following', 'wpv-views'); ?>&nbsp;</option>
			<option value="FROM PAGE"><?php _e('Value set by the current page', 'wpv-views'); ?>&nbsp;</option>
			<option value="FROM ATTRIBUTE"><?php _e('Value set by View shortcode attribute', 'wpv-views'); ?>&nbsp;</option>
			<option value="FROM URL"><?php _e('Value set by URL parameter', 'wpv-views'); ?>&nbsp;</option>
			<option value="FROM PARENT VIEW"><?php _e('Value set by parent view', 'wpv-views'); ?>&nbsp;</option>
		</select>

		<div>
			<ul class="categorychecklist form-no-clear">
	
			<?php wp_terms_checklist(0, array('taxonomy' => $args['taxonomy'])) ?>
			
			</ul>
		</div>
		
		<input type="text" class="wpv_taxonomy_param" name="tax_<?php echo $args['taxonomy']; ?>_attribute_url" style="display:none;">
		<span class="wpv_taxonomy_param_missing"><?php echo __('<- Please enter a value here', 'wpv-views'); ?></span>

		<br />
		<span class="wpv-taxonomy-help"><i>
			<?php echo sprintf(__('%sLearn about filtering by taxonomy%s', 'wpv-views'),
						   '<a href="' . WPV_FILTER_BY_TAXONOMY_LINK . '" target="_blank">',
						   ' &raquo;</a>'
						   ); ?>
		</i></span>

		
	</div>

	<?php
}


/**
 * Add a filter so that we can determine if the "type" is a taxonomy type
 *
 */

add_filter('wpv_get_table_row_ui_type', 'wpv_get_table_row_ui_type_cat');
function wpv_get_table_row_ui_type_cat($type) {
	// see if this is a category type.

	$taxonomies = get_taxonomies('', 'objects');
	foreach ($taxonomies as $category_slug => $category) {
		$taxonomy = $category->name;
		$name = ( $taxonomy == 'category' ) ? 'post_category' : 'tax_input[' . $taxonomy . ']';
		
		if ($name == $type) {
			// it's a category type.
			return 'post_category';
		}
	}
	
	return $type;
}

add_filter('wpv-view-get-summary', 'wpv_category_summary_filter', 6, 3);

function wpv_category_summary_filter($summary, $post_id, $view_settings) {
	$result = '';
	$taxonomies = get_taxonomies('', 'objects');
	foreach ($taxonomies as $category_slug => $category) {
		$save_name = ( $category->name == 'category' ) ? 'post_category' : 'tax_input_' . $category->name;
		$relationship_name = ( $category->name == 'category' ) ? 'tax_category_relationship' : 'tax_' . $category->name . '_relationship';
		
		if (isset($view_settings[$relationship_name])) {
			
			if (!isset($view_settings[$save_name])) {
				$view_settings[$save_name] = array();
			}
	
			$name = ( $category->name == 'category' ) ? 'post_category' : 'tax_input[' . $category->name . ']';
			if ($result != '') {
				if ($view_settings['taxonomy_relationship'] == 'OR') {
					$result .= __(' OR ', 'wpv-views');
				} else {
					$result .= __(' AND ', 'wpv-views');
				}
			}
			
			$result .= wpv_get_taxonomy_summary($name, $view_settings, $view_settings[$save_name]);
				
		}
	}

	if ($result != '' && $summary != '') {
		$summary .= '<br />';
	}
	$summary .= $result;
	
	return $summary;
}


function wpv_taxonomy_get_url_params($view_settings) {
	$results = array();
	
	$taxonomies = get_taxonomies('', 'objects');
	foreach ($taxonomies as $category_slug => $category) {
		$save_name = ( $category->name == 'category' ) ? 'post_category' : 'tax_input_' . $category->name;
		$relationship_name = ( $category->name == 'category' ) ? 'tax_category_relationship' : 'tax_' . $category->name . '_relationship';
		
		if (isset($view_settings[$relationship_name]) && $view_settings[$relationship_name] == 'FROM URL') {

			$url_parameter = $view_settings['taxonomy-' . $category->name . '-attribute-url'];
			
			$results[] = array('name' => $category->name, 'param' => $url_parameter, 'mode' => 'tax', 'cat' => $category);

		}
	}
	
	return $results;
}
