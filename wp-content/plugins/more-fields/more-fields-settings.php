<?php

global $more_fields_settings, $more_fields, $more_types, $more_types_settings;

//  __d($_POST);

$required = '<em class="required">' . __('Required', 'more-plugins') . '</em>';

	$fields = $more_fields_settings->data;

if (!$more_fields_settings->navigation || $more_fields_settings->navigation == 'boxes') {

	echo '<p>';
	_e('Listed below are the boxes that were created in More Fields. These boxes appear on the Write/Edit page. If a box cannot be edited, it was created programmatically. Click on the name of a box to edit and add fields', 'more-plugins');
	echo '</p>';

	$titles = array(
				__('Box', 'more-plugins'), 
				__('Nbr of fields', 'more-plugins'),
				__('Actions', 'more-plugins')
			);

	$fields = $more_fields_settings->data;

	$ancestor_keys = array();
	$more_fields_settings->table_header($titles);
	$nbr = 0;
	foreach ((array) $fields['_plugin'] as $key => $item) {
		if ($a = $item['ancestor_key']) $ancestor_keys[] = $a;
		$title = __('More Fields boxes', 'more-plugins');
		$keys = '_plugin,' . $key;
		$label = stripslashes($item['label']);
		$caption = __('Boxes created with More Fields or overridden from elsewhere.', 'more-plugins');
		echo '<caption><h3>' . $title . '</h3><p>' . $caption . '</p></caption>';
		$edit_link = array('navigation' => 'box', 'action' => 'edit', 'keys' => $keys);
		$delete_link = array('action' => 'delete', 'action_keys' => $keys, 'class' => 'more-common-delete');
		$export_link = array('navigation' => 'export', 'keys' => $keys);
		$warning = '';
		$data = array(	
				$more_fields_settings->settings_link($label, $edit_link) . $warning,
				count($item['fields']),
				$more_fields_settings->settings_link(__('Edit', 'more-plugins'), $edit_link) . ' | ' .
				$more_fields_settings->settings_link(__('Delete', 'more-plugins'), $delete_link) . ' | ' .
				$more_fields_settings->settings_link(__('Export', 'more-plugins'), $export_link) . 
				$more_fields_settings->updown_link($nbr, count($fields['_plugin']))
			);
		$more_fields_settings->table_row($data, $nbr++);
	}
	if (empty($fields['_plugin'])) {
		$data = array(__('There are no boxes', 'more-plugins'), '', '');
		$more_fields_settings->table_row($data, $nbr++);
	}
	$more_fields_settings->table_footer($titles);
	
	$new_key = '_plugin,'. $more_fields_settings->add_key;
	$options = array('action' => 'add', 'navigation' => 'box', 'keys' => $new_key, 'class' => 'button-primary');
	echo '<p>' . $more_fields_settings->settings_link('Add new input box', $options) . '</p>';
	
	if (!empty($fields['_plugin_saved'])) {
		$more_fields_settings->table_header($titles);
		$title = __('Saved boxes', 'more-plugins');
		$caption = __('Boxes from files created with More Types.', 'more-plugins');
		echo '<caption><h3>' . $title . '</h3><p>' . $caption . '</p></caption>';

		foreach ($fields['_plugin_saved'] as $key => $item) {
			$label = $item['label'];

			// Is this overwritten?
			$class = (in_array($key, $ancestor_keys)) ? 'disabled' : false;
			if (!$class) $class = (array_key_exists($key, $fields['_plugin'])) ? 'disabled' : false ;

			$edit_link = array('navigation' => 'box', 'action' => 'edit', 'keys' => '_plugin_saved,'.$key);
			//$delete_link = array('action' => 'delete', 'action_keys' => $key, 'class' => 'more-common-delete');
			$export_link = array('navigation' => 'export', 'keys' => '_plugin_saved,'.$key);
			$data = array(	
					$label,
					count($item['fields']),
					$more_fields_settings->settings_link(__('Override', 'more-plugins'), $edit_link) . ' | ' .
					// $more_fields_settings->settings_link(__('Delete'), $delete_link) . ' | ' .
					$more_fields_settings->settings_link(__('Export', 'more-plugins'), $export_link) 
					//$more_fields_settings->updown_link($nbr, count($data['_plugin']))
				);
			if ($class) $data = array($label, count($item['fields']),  __('Overridden above', 'more-plugins'));
			$more_fields_settings->table_row($data, $nbr++, $class);
		}
		$more_fields_settings->table_footer($titles);	
	}
	
	if (!empty($fields['_other'])) {
		$titles = array(
						__('Box', 'more-plugins'),
						__('Actions', 'more-plugins')
					);

		$more_fields_settings->table_header($titles);
		$title = __('Boxes created elsewhere', 'more-plugins');
		$caption = __('Boxes created in functions.php or by other plugins.', 'more-plugins');
		echo '<caption><h3>' . $title . '</h3><p>' . $caption . '</p></caption>';

		foreach ($fields['_other'] as $key => $item) {
			$label = $item['label'];
			$keys = '_other,'. $key;
			$class = (in_array($key, $ancestor_keys)) ? 'disabled' : '' ;
			$edit_link = array('navigation' => 'box', 'action' => 'edit', 'keys' => $keys);
			// $delete_link = array('action' => 'delete', 'action_keys' => $key, 'class' => 'more-common-delete');
			$export_link = array('navigation' => 'export', 'keys' => $keys);
			$data = array(	
					$label,
					$more_fields_settings->settings_link(__('Override', 'more-plugins'), $edit_link) . ' | ' .
					$more_fields_settings->settings_link(__('Export', 'more-plugins'), $export_link) 
				);
			if ($class) $data = array($label, __('Overridden above', 'more-plugins'));
			$more_fields_settings->table_row($data, $nbr++, $class);
		}
		$more_fields_settings->table_footer($titles);	
	}
	



} else if ($more_fields_settings->navigation == 'box') {

	$data = $more_fields_settings->data;
	// Set up the navigation
	$navtext = $more_fields_settings->get_val('label');
	if ($more_fields_settings->action == 'add') $navtext = __('Add new', 'more-plugins');
	$more_fields_settings->navigation_bar(array($navtext));

//	echo '';

// __d($data);

	echo '<p>';
	_e('You can control for what post type this box shows up by using the More Types plugin.', 'more-plugins');
	echo '</p>';

	$more_fields_settings->settings_form_header(array('navigation' => 'boxes', 'action' => 'save'));
	
	?>
	<table class="form-table">
	<?php
		$box = esc_attr($_GET['keys']);
	
		$comment = __('This is the title that will appear at the head of the box.', 'more-plugins');
		$comment = '<em>' . $comment . '</em>';
		$row = array(__('Title of the box', 'more-plugins') . $required, $more_fields_settings->settings_input('label') . $comment);
		$more_fields_settings->setting_row($row);

		// Available to type
		//if (!is_plugin_active('more-types/more-types.php')) {
			$types = $more_fields_settings->get_post_types();
			
			// Remove these for now
			$remove = array('attachment', 'revision', 'nav_menu_item');
			foreach ($remove as $r) if ($types[$r]) unset($types[$r]);
			
			$options = array();
			if (is_object($more_types) && !empty($more_types)) {
				$box_key = $this->keys[1];
				$link = (method_exists($more_types, 'options_url')) ? $more_types->options_url : '';
				$mfts = $more_types->get_objects(array('_plugin_saved', '_other'));
				$mftss = $more_types->get_objects(array('_plugin_saved', '_other', '_plugin'));
				foreach ($mftss as $key => $mft) {
					$options[$key] = array();
	
					// Override value?
					$mftb = (array_key_exists('boxes', $mft)) ? $mft['boxes'] : array();
					if (in_array($box_key, (array) $mftb)) 
						$options[$key] = array_merge($options[$key], array('value' => 'on'));

					// If this is created by file or elsewhere there's nothing we can do. 
					if (array_key_exists($key, (array) $mfts)) 
						$options[$key] = array_merge($options[$key], array('disabled' => true, 'text' => __("If you want to use your box for this post type you need to import it using <a href='$link'>More Types</a>.", 'more-types')));
				}			
			}

			$row = array(__('Used with post types', 'more-plugins'), $more_fields_settings->checkbox_list('post_types', $types, $options));
			$more_fields_settings->setting_row($row);
		/*} else {
			$comment = sprintf(__('To attach boxes to post types use %s!', 'more-plugins'), '<a href="options-general.php?page=more-types">More Types</a>');
			$comment = $more_fields_settings->format_comment($comment);
			$row = array(__('Available to', 'more-plugins'), $comment);
			$more_fields_settings->setting_row($row);
		}*/

		echo '</table>';
if ($more_fields_settings->action != 'add') {
		echo '<p>';
		_e('There are the fields of this box', 'more-plugins');
		echo '</p>';

		$titles = array(
					__('Field', 'more-plugins'), 
					__('Custom field key', 'more-plugins'), 
					__('Type', 'more-plugins'), 
					__('Actions', 'more-plugins')
					);
		$more_fields_settings->table_header($titles);
		$nbr = 0;
		
		$fields = $more_fields_settings->get_val('fields');
	//	$fields = $more_fields_settings->extract_array($fields);

		if (empty($fields)) {
			$data = array(__('No input fields exists', 'more-plugins'), '', '', '');
			$more_fields_settings->table_row($data, $nbr);
		}
		
		$total = count($fields);

		foreach((array) $fields as $key => $item) {
			if (!$key) continue;
			$label = stripslashes($item['label']);
			$keys = $more_fields_settings->keys;
			$akeys = $keys[0] . ',' . $keys[1] . ',fields,' . $key;
			$edit_link = array('navigation' => 'field', 'action' => 'edit', 'keys' => $akeys);
			$delete_link = array('navigation' => 'box', 'action' => 'delete','keys' => $keys, 'class' => 'more-common-delete', 'action_keys' => $akeys);
			$warning = '';
			$row = array(	
					$more_fields_settings->settings_link($label, $edit_link) . $warning,
					$item['key'],
					ucfirst($item['field_type']),
					$more_fields_settings->settings_link('Edit', $edit_link) . ' | ' .
					$more_fields_settings->settings_link('Delete', $delete_link) . 
					$more_fields_settings->updown_link($nbr, $total, array('keys' => $box, 'action_keys' => array($box, 'fields')))
			);
	
			$more_fields_settings->table_row($row, $nbr);
			$nbr++;				
		}
		
		?>
			</tbody>
				<tfoot>
					<tr>
					<th colspan="4">
					<?php
						$new_key = array_merge($more_fields_settings->keys , array('fields', $more_fields_settings->add_key));
						$options = array('action' => 'add', 'navigation' => 'field', 'keys' => $new_key, 'class' => 'button-primary');
						echo '<p>' . $more_fields_settings->settings_link('Add new Field', $options) . '</p>';

					
//						$keys = implode(',', array($more_fields_settings->keys[0], $more_fields_settings->keys[1], 'fields'));
//						echo '<p>' . $more_fields_settings->settings_link(__('Add a field', 'more-plugins'), array('navigation' => 'field', 'keys' => $keys, 'action' => 'add', 'class' => 'button-primary')) . '</p>';
					?>
					</th>
					</tr>
				</tfoot>
			</table>
		<?php
//		$more_fields_settings->table_footer($titles);

//		$options = array('title' => 'Add a field', 'action' => 'add', 'navigation' => 'field');
//		$more_fields_settings->add_button($options);

//		$keys = array($more_fields_settings->keys[0], 'fields'); // implode(',', $more_fields_settings->keys);
//		echo '<p>' . $more_fields_settings->settings_link(__('Add a field', 'more-plugins'), array('navigation' => 'field', 'keys' => $keys, 'action' => 'add', 'class' => 'button-primary')) . '</p>';
} // end is edit
else {

	echo '<p>';
	_e('Once you have saved your new box, you can add fields to it.', 'more-plugins');
	echo '</p>';


}

		?>
		<div class="more-plugins-advanced-settings">
			<h3 class="more-advanced-settings-toggle"><a href="#">Advanced settings <span>show/hide</span></a></h3>
			<div class="more-advanced-settings">
				<table class="form-table">
			<?php
			
				$comment = __('The default column the box will appear in. Note that a user can move boxes on the Write/Edit page as they see fit.', 'more-plugins');
				$comment = '<em>' . $comment . '</em>';
				$select = array('right' => __('Right', 'more-plugins'), 'left' => __('Left', 'more-plugins'));
				$row = array(__('Default position', 'more-plugins'), $more_fields_settings->settings_radiobuttons('position', $select) . $comment);
				$more_fields_settings->setting_row($row);

				$comment = __('The roles for which this field will be visible.', 'more-plugins');
				$comment = $more_fields_settings->format_comment($comment);
				$roles = $more_fields_settings->get_roles();
				$row = array(__('Roles to access', 'more-plugins') . $comment, $more_fields_settings->checkbox_list('more_access_cap', $roles));
				$more_fields_settings->setting_row($row);

				// __d($fields);
				echo $more_fields_settings->settings_hidden('fields');

			?>
				</table>
			</div>
			</div>
		<?php
		$more_fields_settings->settings_save_button()



	?>
	<?php


} else if ($more_fields_settings->navigation == 'field') {
//	$data = $more_fields_settings->read_data();
//	$data = $more_fields_settings->get_data();
	$more_fields_settings->default = array('position' => 'right', 'field_type' => 'text');

	// Set up the navigation	
///	$keys = $_GET['keys'];

	$keys = $more_fields_settings->keys;

	$box = $more_fields_settings->get_val('label', array($keys[0], $keys[1]));
	$navtext = $more_fields_settings->get_val('label');

	if (!$box) $box = $more_fields_settings->get_val('label', array($keys[1]));
	if (!$navtext) $navtext = __('Add new', 'more-plugins');
		
	$navurl = $more_fields_settings->settings_link($box, array('keys' => $keys[0].','.$keys[1], 'navigation' => 'box'));
	$more_fields_settings->navigation_bar(array($navurl, $navtext));

	$form_link = array('navigation' => 'box', 'keys' => $keys[0] . ',' . $keys[1], 'action_keys' => $keys, 'action' => 'save');
	$more_fields_settings->settings_form_header($form_link); 
	
	?>
	<table class="form-table">

	<?php

	$comment = __('This is the name of the field as it will appear in the box.', 'more-plugins');
	$comment = $more_fields_settings->format_comment($comment);
	$row = array(__('Field title', 'more-plugins') . $required, $more_fields_settings->settings_input('label') . $comment);
	$more_fields_settings->setting_row($row);

	$comment = __('This is the key to be used to access this data in the templates.', 'more-plugins');
	if ($val = $more_fields_settings->get_val('key')) {
		$comment .= ' ' . __('E.g. you can use <code>&lt;?php meta(\'' . $val . '\'); ?&gt;</code> to print the value of this field in a template.', 'more-plugins'); 
	}
	$comment = $more_fields_settings->format_comment($comment);
	$row = array(__('Custom field key', 'more-plugins') . $required, $more_fields_settings->settings_input('key') . $comment);
	$more_fields_settings->setting_row($row);

	$comment = __('If you enter instructions for the user on what the field is for, it will appear under the field in the box. Html code is  allowed here.', 'more-plugins');
	$comment = $more_fields_settings->format_comment($comment);
	$row = array(__('Caption', 'more-plugins'), $more_fields_settings->settings_textarea('caption') . $comment);
	$more_fields_settings->setting_row($row);


	$field_types = $more_fields_settings->get_field_types_select();
	$comments = $more_fields_settings->get_field_types_comments();
	$row = array(__('Field type', 'more-plugins'), $more_fields_settings->settings_radiobuttons('field_type', $field_types, $comments));
	$more_fields_settings->setting_row($row);
	
	$comment = __('If the field type permitts a list of pre-defined values, enter them here, comma separated. E.g. values could be <code>Drums, Bells, *Whistles</code>, where <code>Whistles</code> becomes the default value.', 'more-plugins');
	$comment = $more_fields_settings->format_comment($comment);
	$row = array(__('Values (if applicable)', 'more-plugins'), $more_fields_settings->settings_input('values') . $comment);
	$more_fields_settings->setting_row($row);


?>
	</table>
		<div class="more-plugins-advanced-settings">
			<h3 class="more-advanced-settings-toggle"><a href="#">Advanced settings <span>show/hide</span></a></h3>
			<div class="more-advanced-settings">
				<table class="form-table">
				<?php

					$comment = __('If permalinks are enabled, then this is the slug under which you will find an archive page for this custom field.', 'more-plugins');
					if ($val = $more_fields_settings->get_val('key')) {
						$url = get_option('siteurl') . '/' . $val . '/some_value/';
						$comment .= ' ' . __('For this field: <code>' . $url . '</code>', 'more-plugins'); 
					}
					$comment = $more_fields_settings->format_comment($comment);
					$pl = $more_fields_settings->permalink_warning();
					$row = array(__('Slug', 'more-plugins'), $more_fields_settings->settings_input('slug') . $comment . $pl);
					$more_fields_settings->setting_row($row);

/*


Skulle man inte kunna göra så här: Permalinks are currently not enabled! To use this feature, enable permalinks in the Permalinks settings. If permalinks were enabled, then this is the slug under which you will find an archive page for this custom field. For this field: http://labs.dagensskiva.com/trunk/everytext/some_value/

*/


					$roles = $more_fields_settings->get_roles();
					$comment = __('The roles for which this box will be visible.', 'more-plugins');
					$comment = $more_fields_settings->format_comment($comment);
					$row = array(__('Access', 'more-plugins') . $comment, $more_fields_settings->checkbox_list('more_access_cap', $roles));
					$more_fields_settings->setting_row($row);
				?>
				</table>
			</div>
			</div>

	<?php $more_fields_settings->settings_save_button(); ?>

	</form>

	<?php
}
//	echo '<pre>';
//	print_r($more_fields_settings->data);
//	echo '</pre>';



?>