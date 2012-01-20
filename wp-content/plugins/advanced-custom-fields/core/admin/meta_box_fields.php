<?php
/**
 * Meta Box: Fields
 *
 * This file creates the HTML for a list of fields within a Field Group
 */
 
// vars
global $post;
$fields_names = array();

// get fields
$fields = $this->get_acf_fields($post->ID);

// add clone
$fields[999] = array(
	'label'		=>	__("New Field",'acf'),
	'name'		=>	'new_field',
	'type'		=>	'text',
	'order_no'	=>	'1',
	'instructions'	=>	'',
);

// get name of all fields for use in field type drop down
foreach($this->fields as $field)
{
	$fields_names[$field->name] = $field->title;
}

// hidden field for saving
?>
<input type="hidden" name="save_fields" value="true" />
<div class="fields_header">
	<table class="acf widefat">
		<thead>
			<tr>
				<th class="field_order"><?php _e('Field Order','acf'); ?></th>
				<th class="field_label"><?php _e('Field Label','acf'); ?></th>
				<th class="field_name"><?php _e('Field Name','acf'); ?></th>
				<th class="field_type"><?php _e('Field Type','acf'); ?></th>
			</tr>
		</thead>
	</table>
</div>
<div class="fields">
	<div class="no_fields_message" <?php if(sizeof($fields) > 1){ echo 'style="display:none;"'; } ?>>
		<?php _e("No fields. Click the <strong>+ Add Field</strong> button to create your first field.",'acf'); ?>
	</div>
	<?php foreach($fields as $key => $field): ?>
	<div class="<?php echo ($key == 999) ? "field_clone" : "field"; ?>">
		<?php if(isset($field['key'])): ?><input type="hidden" name="fields[<?php echo $key; ?>][key]" value="<?php echo $field['key']; ?>" /><?php endif; ?>
		<div class="field_meta">
			<table class="acf widefat">
				<tr>
					<td class="field_order"><span class="circle"><?php echo (int)$field['order_no'] + 1; ?></span></td>
					<td class="field_label">
						<strong>
							<a class="acf_edit_field row-title" title="Edit this Field" href="javascript:;"><?php echo $field['label']; ?></a>
						</strong>
						<div class="row_options">
							<span><a class="acf_edit_field" title="Edit this Field" href="javascript:;"><?php _e("Edit",'acf'); ?></a> | </span>
							<span><a title="Read documentation for this field" href="http://plugins.elliotcondon.com/advanced-custom-fields/documentation/" target="_blank"><?php _e("Docs",'acf'); ?></a> | </span>
							<span><a class="acf_delete_field" title="Delete this Field" href="javascript:;"><?php _e("Delete",'acf'); ?></a>
						</div>
					</td>
					<td class="field_name"><?php echo $field['name']; ?></td>
					<td class="field_type"><?php echo $fields_names[$field['type']]; ?></td>
				</tr>
			</table>
		</div>
		<div class="field_form_mask">
			<div class="field_form">
				
				<table class="acf_input widefat acf_field_form_table">
					<tbody>
						<tr class="field_label">
							<td class="label">
								<label><span class="required">*</span><?php _e("Field Label",'acf'); ?></label>
								<p class="description"><?php _e("This is the name which will appear on the EDIT page",'acf'); ?></p>
							</td>
							<td>
								<?php 
								$this->create_field(array(
									'type'	=>	'text',
									'name'	=>	'fields['.$key.'][label]',
									'value'	=>	$field['label'],
									'class'	=>	'label',
								));
								?>
							</td>
						</tr>
						<tr class="field_name">
							<td class="label">
								<label><span class="required">*</span><?php _e("Field Name",'acf'); ?></label>
								<p class="description"><?php _e("Single word, no spaces. Underscores and dashes allowed",'acf'); ?></p>
							</td>
							<td>
								<?php 
								$this->create_field(array(
									'type'	=>	'text',
									'name'	=>	'fields['.$key.'][name]',
									'value'	=>	$field['name'],
									'class'	=>	'name',
								));
								?>
							</td>
						</tr>
						<tr class="field_type">
							<td class="label"><label><span class="required">*</span><?php _e("Field Type",'acf'); ?></label></td>
							<td>
								<?php 
								$this->create_field(array(
									'type'		=>	'select',
									'name'		=>	'fields['.$key.'][type]',
									'value'		=>	$field['type'],
									'choices' 	=>	$fields_names,
								));
								?>
							</td>
						</tr>
						<tr class="field_instructions">
							<td class="label"><label><?php _e("Field Instructions",'acf'); ?></label>
							<p class="description"><?php _e("Instructions for authors. Shown when submitting data",'acf'); ?></p></td>
							<td>
								<?php 
								$this->create_field(array(
									'type'	=>	'textarea',
									'name'	=>	'fields['.$key.'][instructions]',
									'value'	=>	$field['instructions'],
								));
								?>
							</td>
						</tr>
						<?php 
						foreach($fields_names as $field_name => $field_title){
							$this->fields[$field_name]->create_options($key, $field);
						} 
						?>
						<tr class="field_save">
							<td class="label">
								<label><?php _e("Save Field",'acf'); ?></label>
							</td>
							<td><input type="submit" value="Save Field" class="button-primary" name="save" />
								<?php _e("or",'acf'); ?> <a class="acf_edit_field" title="<?php _e("Hide this edit screen",'acf'); ?>" href="javascript:;"><?php _e("continue editing ACF",'acf'); ?></a>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>	
	</div>
	<?php endforeach; ?>
</div>
<div class="table_footer">
	<div class="order_message"></div>
	<a href="javascript:;" id="add_field" class="button-primary"><?php _e('+ Add Field','acf'); ?></a>
</div>