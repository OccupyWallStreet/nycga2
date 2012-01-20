<?php

class acf_Repeater extends acf_Field
{
	
	/*--------------------------------------------------------------------------------------
	*
	*	Constructor
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	*	@updated 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function __construct($parent)
	{
    	parent::__construct($parent);
    	
    	$this->name = 'repeater';
		$this->title = __("Repeater",'acf');
		
   	}
   	
   	
   	/*--------------------------------------------------------------------------------------
	*
	*	admin_print_scripts / admin_print_styles
	*
	*	@author Elliot Condon
	*	@since 3.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function admin_print_scripts()
	{
		wp_enqueue_script(array(

			'jquery-ui-sortable',
			
		));
	}
	
	function admin_print_styles()
	{
  
	}
	
	
   	/*--------------------------------------------------------------------------------------
	*
	*	admin_head
	*
	*	@author Elliot Condon
	*	@since 2.0.6
	* 
	*-------------------------------------------------------------------------------------*/
	
	function admin_head()
	{
		?>
		<script type="text/javascript">
		(function($){
			
			
			function uniqid()
	        {
	        	var newDate = new Date;
	        	return newDate.getTime();
	        }
	        
			/*----------------------------------------------------------------------
			*
			*	Update Order Numbers
			*
			*---------------------------------------------------------------------*/
		
			function update_order_numbers(div)
			{
				div.children('table').children('tbody').children('tr.row').each(function(i){
					$(this).children('td.order').html(i+1);
				});
			
			};
			
			
			/*----------------------------------------------------------------------
			*
			*	Make Sortable
			*
			*---------------------------------------------------------------------*/
			function make_sortable(div){
				
				var fixHelper = function(e, ui) {
					ui.children().each(function() {
						$(this).width($(this).width());
					});
					return ui;
				};
				
				div.children('table').children('tbody').unbind('sortable').sortable({
					update: function(event, ui){
						update_order_numbers(div);
					},
					handle: 'td.order',
					helper: fixHelper,
				});
			};
			
			
			$(document).ready(function(){
				
				$('#poststuff .repeater').each(function(){
				
					var div = $(this);
					var row_limit = parseInt(div.attr('data-row_limit'));
					var row_count = div.children('table').children('tbody').children('tr.row').length;
					
					// has limit been reached?
					if(row_count >= row_limit) div.find('#add_field').attr('disabled','true');
					
					// sortable
					if(row_limit > 1){
						make_sortable(div)
					}
					
				});
					
			});
			
			// add field
			$('#poststuff .repeater #add_field').live('click', function(){
				
				var div = $(this).closest('.repeater');
				var row_limit = parseInt(div.attr('data-row_limit'));			
				var row_count = div.children('table').children('tbody').children('tr.row').length;
				
				// row limit
				if(row_count >= row_limit)
				{
					// reached row limit!
					div.find('#add_field').attr('disabled','true');
					return false;
				}
				
				// deactivate any wysiwygs
				div.children('table').children('tbody').children('tr.row_clone').acf_deactivate_wysiwyg();
			
				// create and add the new field
				var new_field = div.children('table').children('tbody').children('tr.row_clone').clone(false);
				new_field.attr('class', 'row');
				
				// update names
				var new_id = uniqid();
				new_field.find('[name]').each(function(){
				
					var name = $(this).attr('name').replace('[999]','[' + new_id + ']');
					$(this).attr('name', name);
					$(this).attr('id', name);
					
				});
				
				// add row
				div.children('table').children('tbody').append(new_field); 
				
				// activate wysiwyg
				new_field.acf_activate_wysiwyg();
			
				update_order_numbers(div);
				
				// there is now 1 more row
				row_count ++;
				
				// disable the add field button if row limit is reached
				if((row_count+1) >= row_limit)
				{
					div.find('#add_field').attr('disabled','true');
				}
				
				return false;
				
			});
			
			
			// remove field
			$('#poststuff .repeater a.remove_field').live('click', function(){
				
				var div = $(this).closest('.repeater');
				var tr = $(this).closest('tr');
				
				tr.animate({'left' : '50px', 'opacity' : 0}, 250,function(){
					tr.remove();
					update_order_numbers(div);
				});
				
				div.find('#add_field').removeAttr('disabled');
				
				return false;
				
			});
			
		})(jQuery);
		</script>
		<?php
	}
	

	/*--------------------------------------------------------------------------------------
	*
	*	create_field
	*
	*	@author Elliot Condon
	*	@since 2.0.5
	*	@updated 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function create_field($field)
	{
		// vars
		$row_limit = ( isset($field['row_limit']) && is_numeric($field['row_limit']) ) ? $field['row_limit'] : 999;
		$layout = isset($field['layout']) ? $field['layout'] : 'table';
		$sub_fields = isset($field['sub_fields']) ? $field['sub_fields'] : array();
		
		// add clone field
		if($row_limit == 1 && count($field['value']) == 0)
		{
			$field['value'][] = array();
		}
		
		// setup values for row clone
		$field['value'][999] = array();
		foreach($sub_fields as $sub_field)
		{
			$sub_value = isset($sub_field['default_value']) ? $sub_field['default_value'] : '';
			$field['value'][999][$sub_field['name']] = $sub_value;
		}

		?>
		<div class="repeater" data-row_limit="<?php echo $row_limit; ?>">
			<table class="widefat <?php if($layout == 'row'): ?>row_layout<?php endif; ?>">
			<?php if($layout == 'table'): ?>
			<thead>
				<tr>
					<?php if($row_limit > 1): ?>
					<th class="order"><!-- order --></th>
					<?php endif; ?>
					
					<?php foreach($sub_fields as $sub_field_i => $sub_field):?>
					<th class="<?php echo $sub_field['name']; ?>" <?php if($sub_field_i != 0): ?>style="width:<?php echo 95/count($sub_fields); ?>%;"<?php endif; ?>><span><?php echo $sub_field['label']; ?></span></th>
					<?php endforeach; ?>
					
					<?php if($row_limit > 1): ?>
					<th class="remove"></th>
					<?php endif; ?>
				</tr>
			</thead>
			<?php endif; ?>
			<tbody>
				<?php foreach($field['value'] as $i => $value):?>
				<?php //if(($i+1) > $row_limit){continue;} ?>
				<tr class="<?php echo ($i == 999) ? "row_clone" : "row"; ?>">
					
					<?php if($row_limit > 1): ?>
						<td class="order">
						<?php echo $i+1; ?>
						</td>
					<?php endif; ?>
					
					<?php if($layout == 'row'): ?><td><?php endif; ?>
					
					
					<?php foreach($sub_fields as $j => $sub_field):?>
					
					<?php if($layout == 'table'): ?>
					<td>
					<?php else: ?>
					<label class="field_label"><?php echo $sub_field['label']; ?></label>
					<?php endif; ?>	
						
						<?php 
						// add value
						$sub_field['value'] = isset($value[$sub_field['name']]) ? $value[$sub_field['name']] : '';
						
						// add name
						$sub_field['name'] = $field['name'] . '[' . $i . '][' . $sub_field['key'] . ']';
						
						// create field
						$this->parent->create_field($sub_field);
						?>
						
					<?php if($layout == 'table'): ?>
					</td>
					<?php else: ?>

					<?php endif; ?>	
					
					<?php endforeach; ?>
					
					<?php if($layout == 'row'): ?></td><?php endif; ?>
					
					<?php if($row_limit > 1): ?>
						<td class="remove"><a class="remove_field" href="javascript:;"></a></td>
					<?php endif; ?>
				</tr>
				<?php endforeach; ?>
			</tbody>
			</table>
			<?php if($row_limit > 1): ?>
			<div class="table_footer">
				<div class="order_message"></div>
				<a href="javascript:;" id="add_field" class="button-primary"><?php _e("+ Add Row",'acf'); ?></a>
			</div>	
			<?php endif; ?>	
		</div>
		<?php
	}
	
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	create_options
	*
	*	@author Elliot Condon
	*	@since 2.0.6
	*	@updated 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function create_options($key, $field)
	{
		// vars
		$fields_names = array();
		$field['row_limit'] = isset($field['row_limit']) ? $field['row_limit'] : '';
		$field['layout'] = isset($field['layout']) ? $field['layout'] : 'table';
		$field['sub_fields'] = isset($field['sub_fields']) ? $field['sub_fields'] : array();
		
		// add clone field
		$field['sub_fields'][999] = array(
				'label'		=>	__("New Field",'acf'),
				'name'		=>	'new_field',
				'type'		=>	'text',
				'order_no'	=>	'1',
				'instructions'	=>	'',
		);
		
		// get name of all fields for use in field type
		foreach($this->parent->fields as $f)
		{
			$fields_names[$f->name] = $f->title;
		}
		unset($fields_names['repeater']);
		
		?>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e("Repeater Fields",'acf'); ?></label>
	</td>
	<td>
	<div class="repeater">
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

			<div class="no_fields_message" <?php if(count($field['sub_fields']) > 1){ echo 'style="display:none;"'; } ?>>
				<?php _e("No fields. Click the \"+ Add Field button\" to create your first field.",'acf'); ?>
			</div>
	
			<?php foreach($field['sub_fields'] as $key2 => $sub_field): ?>
				<div class="<?php if($key2 == 999){echo "field_clone";}else{echo "field";} ?> sub_field">
					
					<?php if(isset($sub_field['key'])): ?>
						<input type="hidden" name="fields[<?php echo $key; ?>][sub_fields][<?php echo $key2; ?>][key]" value="<?php echo $sub_field['key']; ?>" />
					<?php endif; ?>
					<div class="field_meta">
					<table class="acf widefat">
						<tr>
							<td class="field_order"><span class="circle"><?php echo ($key2+1); ?></span></td>
							<td class="field_label">
								<strong>
									<a class="acf_edit_field" title="Edit this Field" href="javascript:;"><?php echo $sub_field['label']; ?></a>
								</strong>
								<div class="row_options">
									<span><a class="acf_edit_field" title="Edit this Field" href="javascript:;">Edit</a> | </span>
									<span><a class="acf_delete_field" title="Delete this Field" href="javascript:;">Delete</a>
								</div>
							</td>
							<td class="field_name"><?php echo $sub_field['name']; ?></td>
							<td class="field_type"><?php echo $sub_field['type']; ?></td>
						</tr>
					</table>
					</div>
					
					<div class="field_form_mask">
					<div class="field_form">
						
						<table class="acf_input widefat">
							<tbody>
								<tr class="field_label">
									<td class="label">
										<label><span class="required">*</span><?php _e("Field Label",'acf'); ?></label>
										<p class="description"><?php _e("This is the name which will appear on the EDIT page",'acf'); ?></p>
									</td>
									<td>
										<?php 
										$this->parent->create_field(array(
											'type'	=>	'text',
											'name'	=>	'fields['.$key.'][sub_fields]['.$key2.'][label]',
											'value'	=>	$sub_field['label'],
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
										$this->parent->create_field(array(
											'type'	=>	'text',
											'name'	=>	'fields['.$key.'][sub_fields]['.$key2.'][name]',
											'value'	=>	$sub_field['name'],
											'class'	=>	'name',
										));
										?>
									</td>
								</tr>
								<tr class="field_type">
									<td class="label"><label><span class="required">*</span><?php _e("Field Type",'acf'); ?></label></td>
									<td>
										<?php 
										$this->parent->create_field(array(
											'type'	=>	'select',
											'name'	=>	'fields['.$key.'][sub_fields]['.$key2.'][type]',
											'value'	=>	$sub_field['type'],
											'class'	=>	'type',
											'choices'	=>	$fields_names
										));
										?>
									</td>
								</tr>
								<?php 
								foreach($fields_names as $field_name => $field_title){
									$this->parent->fields[$field_name]->create_options($key.'][sub_fields]['.$key2, $sub_field);
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
				
					</div><!-- End Form -->
					</div><!-- End Form Mask -->
				
				</div>
			<?php endforeach; ?>
		</div>
		<div class="table_footer">
			<div class="order_message"></div>
			<a href="javascript:;" id="add_field" class="button-primary"><?php _e('+ Add Field','acf'); ?></a>
		</div>
	</div>
	</td>
</tr>
	
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e("Row Limit",'acf'); ?></label>
	</td>
	<td>
		<?php 
		$this->parent->create_field(array(
			'type'	=>	'text',
			'name'	=>	'fields['.$key.'][row_limit]',
			'value'	=>	$field['row_limit'],
		));
		?>
	</td>
</tr>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e("Layout",'acf'); ?></label>
	</td>
	<td>
		<?php 
		$this->parent->create_field(array(
			'type'	=>	'radio',
			'name'	=>	'fields['.$key.'][layout]',
			'value'	=>	$field['layout'],
			'layout'	=>	'horizontal',
			'choices'	=>	array(
				'table'	=>	'Table (default)',
				'row'	=>	'Row'
			)
		));
		?>
	</td>
</tr>
		<?php
	}
	

	/*--------------------------------------------------------------------------------------
	*
	*	pre_save_field
	*	- called just before saving the field to the database.
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function pre_save_field($field)
	{
		// format sub_fields
		if($field['sub_fields'])
		{
			// remove dummy field
			unset($field['sub_fields'][999]);
			
			// loop through and save fields
			$i = -1;
			
			$sub_fields = array();
			
			foreach($field['sub_fields'] as $f)
			{
				$i++;
				
				// each field has a unique id!
				if(!isset($f['key'])) $f['key'] = 'field_' . uniqid();

				// order
				$f['order_no'] = $i;
				
				// format
				$f = $this->parent->pre_save_field($f);
				
				$sub_fields[] = $f;
			}
			
			$field['sub_fields'] = $sub_fields;
		}
		
		// return updated repeater field
		return $field;

	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	update_value
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function update_value($post_id, $field, $value)
	{
		$total = 0;
		
		if($value)
		{
			// remove dummy field
			unset($value[999]);
			
			$i = -1;
			
			// loop through rows
			foreach($value as $row)
			{	
				$i++;
				
				// increase total
				$total++;
					
				// loop through sub fields
				foreach($field['sub_fields'] as $sub_field)
				{
					// get sub field data
					$v = isset($row[$sub_field['key']]) ? $row[$sub_field['key']] : '';
					
					// add to parent value
					//$parent_value[$i][$sub_field['name']] = $v;
					
					// update full name
					$sub_field['name'] = $field['name'] . '_' . $i . '_' . $sub_field['name'];
					
					// save sub field value
					$this->parent->update_value($post_id, $sub_field, $v);
				}
			}
		}
		
		parent::update_value($post_id, $field, $total);
		
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	get_value
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function get_value($post_id, $field)
	{
		// vars
		$values = array();
		$total = (int) get_post_meta($post_id, $field['name'], true);
		
		if($total > 0)
		{
			// loop through rows
			for($i = 0; $i < $total; $i++)
			{
				// loop through sub fields
				foreach($field['sub_fields'] as $sub_field)
				{
					// store name
					$field_name = $sub_field['name'];
					
					// update full name
					$sub_field['name'] = $field['name'] . '_' . $i . '_' . $field_name;
					
					$values[$i][$field_name] = $this->parent->get_value($post_id, $sub_field);
				}
			}
			
			return $values;
		}
		
		return array();
	}
	
	/*--------------------------------------------------------------------------------------
	*
	*	get_value_for_api
	*
	*	@author Elliot Condon
	*	@since 3.0.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function get_value_for_api($post_id, $field)
	{
		// vars
		$values = array();
		$total = (int) get_post_meta($post_id, $field['name'], true);
		
		if($total > 0)
		{
			// loop through rows
			for($i = 0; $i < $total; $i++)
			{
				// loop through sub fields
				foreach($field['sub_fields'] as $sub_field)
				{
					// store name
					$field_name = $sub_field['name'];
					
					// update full name
					$sub_field['name'] = $field['name'] . '_' . $i . '_' . $field_name;
					
					$values[$i][$field_name] = $this->parent->get_value_for_api($post_id, $sub_field);
				}
			}
			
			return $values;
		}
		
		return array();
	}
	
}

?>