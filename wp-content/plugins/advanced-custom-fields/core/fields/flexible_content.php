<?php

class acf_Flexible_content extends acf_Field
{

	/*--------------------------------------------------------------------------------------
	*
	*	Constructor
	*	- $parent is passed buy reference so you can play with the acf functions
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function __construct($parent)
	{
    	parent::__construct($parent);
    	
    	$this->name = 'flexible_content';
		$this->title = __("Flexible Content",'acf');
		
		add_action('admin_head', array($this,'admin_head_field'));
   	}


	/*--------------------------------------------------------------------------------------
	*
	*	create_field
	*	- called in lots of places to create the html version of the field
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function create_field($field)
	{

		$layouts = array();
		foreach($field['layouts'] as $l)
		{
			$layouts[$l['name']] = $l;
		}
		
		?>
		<div class="acf_flexible_content">
			
			<div class="no_value_message" <?php if($field['value']){echo 'style="display:none;"';} ?>>
				<?php _e("Click the \"add row\" button below to start creating your layout",'acf'); ?>
			</div>
			
			<div class="clones">
			<?php $i = -1; ?>
			<?php foreach($layouts as $layout): $i++; ?>
			<table class="widefat" data-layout="<?php echo $layout['name'] ?>">
			<?php if($layout['display'] == 'table'): ?>
			<thead>
				<tr>
					<th class="order"><!-- order --></th>
					<?php foreach($layout['sub_fields'] as $sub_field_i => $sub_field):?>
					<th class="<?php echo $sub_field['name']; ?>" <?php if($sub_field_i != 0): ?>style="width:<?php echo 95/count($layout['sub_fields']); ?>%;"<?php endif; ?>><span><?php echo $sub_field['label']; ?></span></th>
					<?php endforeach; ?>
					<th class="remove"><!-- remove --></th>
				</tr>
			</thead>
			<?php endif; ?>
			<tbody>
				<tr>
					<td class="order"><?php echo $i+1; ?></td>
					<?php if($layout['display'] == 'row'): ?><td><?php endif; ?>
					<?php foreach($layout['sub_fields'] as $sub_field):?>
						<?php if($layout['display'] == 'table'): ?><td><?php else: ?><label class="field_label"><?php echo $sub_field['label']; ?></label><?php endif; ?>	
						<input type="hidden" name="<?php echo $field['name'] ?>[999][acf_fc_layout]" value="<?php echo $layout['name']; ?>" />
						<?php 
						// add value
						$sub_field['value'] = isset($sub_field['default_value']) ? $sub_field['default_value'] : '';
						
						// add name
						$sub_field['name'] = $field['name'] . '[999][' . $sub_field['key'] . ']';
						
						// create field
						$this->parent->create_field($sub_field);
						?>
						<?php if($layout['display'] == 'table'): ?></td><?php endif; ?>	
					<?php endforeach; ?>
					<?php if($layout['display'] == 'row'): ?></td><?php endif; ?>
					<td class="remove"><a class="remove_field" href="javascript:;"></a></td>
				</tr>
			</tbody>
			</table>
			<?php endforeach; ?>
			</div>
			<div class="values">
				<?php if($field['value']): ?>
					<?php foreach($field['value'] as $i => $value):?>
						
						
						<?php if(!isset($layouts[$value['acf_fc_layout']])) continue; ?>
						<?php $layout = $layouts[$value['acf_fc_layout']]; ?>

						
						<table class="widefat" data-layout="<?php echo $layout['name'] ?>">
						<?php if($layout['display'] == 'table'): ?>
						<thead>
							<tr>
								<th class="order"><!-- order --></th>
								<?php $l = 0; foreach($layout['sub_fields'] as $sub_field): $l++; ?>
								<th class="<?php echo $sub_field['name']; ?>" <?php if($l != count($layout['sub_fields'])): ?>style="width:<?php echo 100/count($layout['sub_fields']) - 5; ?>%;"<?php endif; ?>><span><?php echo $sub_field['label']; ?></span></th>
								<?php endforeach; ?>
								<th class="remove"><!-- remove --></th>
							</tr>
						</thead>
						<?php endif; ?>
						<tbody>
							<tr>
								<td class="order"><?php echo $i+1; ?></td>
								<?php if($layout['display'] == 'row'): ?><td><?php endif; ?>
								<?php foreach($layout['sub_fields'] as $sub_field):?>
									<?php if($layout['display'] == 'table'): ?><td><?php else: ?><label class="field_label"><?php echo $sub_field['label']; ?></label><?php endif; ?>	
									<input type="hidden" name="<?php echo $field['name'] ?>[<?php echo $i ?>][acf_fc_layout]" value="<?php echo $layout['name']; ?>" />
									<?php 
									// add value
									$sub_field['value'] = isset($value[$sub_field['name']]) ? $value[$sub_field['name']] : '';
									
									// add name
									$sub_field['name'] = $field['name'] . '[' . $i . '][' . $sub_field['key'] . ']';
									
									// create field
									$this->parent->create_field($sub_field);
									?>
									<?php if($layout['display'] == 'table'): ?></td><?php endif; ?>
								<?php endforeach; ?>
								<?php if($layout['display'] == 'row'): ?></td><?php endif; ?>
								<td class="remove"><a class="remove_field" href="javascript:;"></a></td>
							</tr>
						</tbody>
						</table>
						

					<?php endforeach; ?>
				<?php endif; ?>
				<?php // values here ?>
			</div>
			<div class="table_footer">
				<div class="order_message"></div>
				<div class="acf_popup">
					<ul>
						<?php foreach($field['layouts'] as $layout): $i++; ?>
						<li><a href="javascript:;" data-layout="<?php echo $layout['name']; ?>"><?php echo $layout['label']; ?></a></li>
						<?php endforeach; ?>
					</ul>
					<div class="bit"></div>
				</div>
				<a href="javascript:;" id="add_field" class="button-primary"><?php _e("+ Add Row",'acf'); ?></a>
				<div class="clear"></div>
			</div>	

		</div>
		<?php
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	create_options
	*	- called from core/field_meta_box.php to create special options
	*
	*	@params : 	$key (int) - neccessary to group field data together for saving
	*				$field (array) - the field data from the database
	*	@author Elliot Condon
	*	@since 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function create_options($key, $field)
	{
		// vars
		$fields_names = array();
		$field['layouts'] = isset($field['layouts']) ? $field['layouts'] : array();
		
		// load default layout
		if(empty($field['layouts']))
		{
			$field['layouts'][] = array(
				'name' => '',
				'label' => '',
				'display' => 'table',
				'sub_fields' => array(),
			);
		}
		
		// get name of all fields for use in field type
		foreach($this->parent->fields as $f)
		{
			$fields_names[$f->name] = $f->title;
		}
		unset($fields_names['repeater']);
		unset($fields_names['flexible_content']);
		
		// loop through layouts and create the options for them
		if($field['layouts']):
		foreach($field['layouts'] as $layout_key => $layout):
		
			// add clone field
			$layout['sub_fields'][999] = array(
					'label'		=>	__("New Field",'acf'),
					'name'		=>	'new_field',
					'type'		=>	'text',
					'order_no'	=>	'1',
					'instructions'	=>	'',
			);
		?>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e("Layout",'acf'); ?></label>
		<p class="desription">
			<span><a class="acf_fc_reorder" title="Edit this Field" href="javascript:;"><?php _e("Reorder",'acf'); ?></a> | </span>
			<span><a class="acf_fc_add" title="Edit this Field" href="javascript:;"><?php _e("Add New",'acf'); ?></a> | </span>
			<span><a class="acf_fc_delete" title="Delete this Field" href="javascript:;"><?php _e("Delete",'acf'); ?></a>
		</p>
	</td>
	<td>
	<div class="repeater">
		
		<table class="acf_cf_meta">
			<body>
				<tr>
					<td class="acf_fc_label" style="padding-left:0;">
						<label><?php _e('Label','acf'); ?></label>
						<?php 
						$this->parent->create_field(array(
							'type'	=>	'text',
							'name'	=>	'fields['.$key.'][layouts][' . $layout_key . '][label]',
							'value'	=>	$layout['label'],
						));
						?>
					</td>
					<td class="acf_fc_name">
						<label><?php _e('Name','acf'); ?></label>
						<?php 
						$this->parent->create_field(array(
							'type'	=>	'text',
							'name'	=>	'fields['.$key.'][layouts][' . $layout_key . '][name]',
							'value'	=>	$layout['name'],
						));
						?>
					</td>
					<td style="padding-right:0;">
						<label><?php _e('Display','acf'); ?></label>
						<?php 
						$this->parent->create_field(array(
							'type'	=>	'select',
							'name'	=>	'fields['.$key.'][layouts][' . $layout_key . '][display]',
							'value'	=>	$layout['display'],
							'choices'	=>	array('table' => 'Table', 'row' => 'Row')
						));
						?>
					</td>
				</tr>
			</body>
		</table>
					
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

			<div class="no_fields_message" <?php if(count($layout['sub_fields']) > 1){ echo 'style="display:none;"'; } ?>>
				<?php _e("No fields. Click the \"+ Add Field button\" to create your first field.",'acf'); ?>
			</div>
	
			<?php foreach($layout['sub_fields'] as $key2 => $sub_field): ?>
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
											'name'	=>	'fields['.$key.'][layouts][' . $layout_key . '][sub_fields]['.$key2.'][label]',
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
											'name'	=>	'fields['.$key.'][layouts][' . $layout_key . '][sub_fields]['.$key2.'][name]',
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
											'name'	=>	'fields['.$key.'][layouts][' . $layout_key . '][sub_fields]['.$key2.'][type]',
											'value'	=>	$sub_field['type'],
											'class'	=>	'type',
											'choices'	=>	$fields_names
										));
										?>
									</td>
								</tr>
								<?php 
								foreach($fields_names as $field_name => $field_title){
									$this->parent->fields[$field_name]->create_options($key.'][layouts][' . $layout_key . '][sub_fields]['.$key2, $sub_field);
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
			
			<?php
			endforeach;
			endif;
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	admin_head
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function admin_head()
	{
		?>
		<script type="text/javascript">
		(function($){
			
			function uniqid()
	        {
	        	var newDate = new Date();
	        	return newDate.getTime();
	        }
	        
			/*----------------------------------------------------------------------
			*
			*	Update Order Numbers
			*
			*---------------------------------------------------------------------*/
		
			function update_order_numbers(div)
			{
				div.children('.values').children('table').each(function(i){
					$(this).children('tbody').children('tr').children('td.order').html(i+1);
				});
			
			}
			
			
			/*----------------------------------------------------------------------
			*
			*	Make Sortable
			*
			*---------------------------------------------------------------------*/
			function make_sortable(div){
				
				div.children('.values').unbind('sortable').sortable({
					update: function(event, ui){
						update_order_numbers(div);
					},
					handle: 'td.order'
				});
			}
			
			
			/*----------------------------------------------------------------------
			*
			*	Add Row
			*
			*---------------------------------------------------------------------*/
			
			$('#poststuff .acf_flexible_content #add_field').live('click', function(){
				
				if($(this).hasClass('active'))
				{
					$(this).removeClass('active');
					$(this).siblings('.acf_popup').animate({ opacity : 0, bottom : '35px' }, 250);
				}
				else
				{
					$(this).addClass('active');
					$(this).siblings('.acf_popup').css({display : 'block', opacity : 0, bottom : '15px'}).animate({ opacity : 1, bottom : '25px' }, 250);
				}
			});
			
			
			/*----------------------------------------------------------------------
			*
			*	Delete Row
			*
			*---------------------------------------------------------------------*/
			
			$('#poststuff .acf_flexible_content a.remove_field').live('click', function(){
				
				var div = $(this).closest('.acf_flexible_content');
				var table = $(this).closest('table');
				var temp = $('<div style="height:' + table.height() + 'px"></div>');
				
				table.animate({'left' : '50px', 'opacity' : 0}, 250, function(){
					table.before(temp).remove();
					
					temp.animate({'height' : 0 }, 250, function(){
						temp.remove();
					});
					
					update_order_numbers(div);
				
					if(!div.children('.values').children('table').exists())
					{
						div.children('.no_value_message').show();
					}
					
				});
				
				return false;
				
			});
			
			
			/*----------------------------------------------------------------------
			*
			*	Add Layout
			*
			*---------------------------------------------------------------------*/
			
			$('#poststuff .acf_flexible_content .table_footer .acf_popup ul li a').live('click', function(){
				
				// vars
				var layout = $(this).attr('data-layout');
				var div = $(this).closest('.acf_flexible_content');
				
				// deactivate any wysiwygs
				div.children('.clones').acf_deactivate_wysiwyg();
				
				// create new field
				var new_field = div.children('.clones').children('table[data-layout="' + layout + '"]').clone(false);
				
				// update names
				var new_id = uniqid();
				new_field.find('[name]').each(function(){
				
					var name = $(this).attr('name').replace('[999]','[' + new_id + ']');
					$(this).attr('name', name);
					$(this).attr('id', name);
					
				});

				// hide no values message
				div.children('.no_value_message').hide();
				
				// add row
				div.children('.values').append(new_field); 
				
				// activate wysiwyg
				new_field.acf_activate_wysiwyg();
				
				update_order_numbers(div);
				
				// hide acf popup
				$(this).closest('.table_footer').find('#add_field').removeClass('active');
				$(this).closest('.acf_popup').hide();
					
				return false;
				
			});
			
			
			
			$(document).ready(function(){
				
				$('#poststuff .acf_flexible_content').each(function(){

					// sortable
					make_sortable($(this));
				});
				
			});
			
		})(jQuery);
		</script>
		<?php
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	admin_head_field
	*	- add extra script / style to field edit page
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function admin_head_field()
	{
		// only add to edit pages
		if(in_array($GLOBALS['pagenow'], array('post.php', 'post-new.php')))
		{
			
			if($GLOBALS['post_type'] == 'acf')
			{
			?>
			<style type="text/css">
			table.acf_input table.acf_cf_meta {
				width: 100%;
				border: 0 none;
			}
			
			table.acf_input table.acf_cf_meta td {
				border: 0 none;
				width: 33%;
			}
			
			table.acf_input table.acf_cf_meta label {
				width: 20%;
				color: #333333;
				font-weight: bold;
			}
			
			table.acf_input table.acf_cf_meta input {
				width: 80%;
			}
			
			table.acf_input table.acf_cf_meta select {
				width: 75%;
			}
			
			.acf_fc_reorder {
				cursor: move;
			}
			
			.ui-state-highlight {
				height: 40px;
			}
			
			.field_form table.acf_input .field_option_flexible_content > td {
				background: #f9f9f9;
			}
			</style>
			<script type="text/javascript">
				(function($){
				
				/*----------------------------------------------------------------------
				*
				*	uniqid
				*
				*---------------------------------------------------------------------*/
				
				function uniqid()
		        {
		        	var newDate = new Date;
		        	return newDate.getTime();
		        }
		        
	
				/*----------------------------------------------------------------------
				*
				*	Add Layout Option
				*
				*---------------------------------------------------------------------*/
				
				$('#acf_fields .acf_fc_add').live('click', function(){
					
					// vars
					var tr = $(this).closest('tr.field_option_flexible_content');
					var new_tr = $(this).closest('.field_form').find('tr.field_option_flexible_content:first').clone(false);
					
					// remove sub fields
					new_tr.find('.sub_field.field').remove();
					
					// show add new message
					new_tr.find('.no_fields_message').show();
					
					// reset layout meta values
					new_tr.find('.acf_cf_meta input[type="text"]').val('');
					new_tr.find('.acf_cf_meta select').val('table');
					
					// update id / names
					var new_id = uniqid();
					
					new_tr.find('[name]').each(function(){
					
						var name = $(this).attr('name').replace('[layouts][0]','[layouts]['+new_id+']');
						$(this).attr('name', name);
						$(this).attr('id', name);
						
					});
					
					// add new tr
					tr.after(new_tr);
					
					// add drag / drop
					new_tr.find('.fields').sortable({
						handle: 'td.field_order'
					});
					
					return false;
				});
				
				
				/*----------------------------------------------------------------------
				*
				*	Delete Layout Option
				*
				*---------------------------------------------------------------------*/
				
				$('#acf_fields .acf_fc_delete').live('click', function(){
		
					var tr = $(this).closest('tr.field_option_flexible_content');
					var tr_count = tr.siblings('tr.field_option.field_option_flexible_content').length;
	
					if(tr_count == 0)
					{
						alert('Flexible Content requires at least 1 layout');
						return false;
					}
					
					tr.animate({'left' : '50px', 'opacity' : 0}, 250, function(){
						tr.remove();
					});
					
				});
				
				
				/*----------------------------------------------------------------------
				*
				*	Sortable Layout Option
				*
				*---------------------------------------------------------------------*/
				
				$('#acf_fields .acf_fc_reorder').live('mouseover', function(){
					
					var table = $(this).closest('table.acf_field_form_table');
					
					if(table.hasClass('sortable')) return false;
					
					var fixHelper = function(e, ui) {
						ui.children().each(function() {
							$(this).width($(this).width());
						});
						return ui;
					};
					
					table.addClass('sortable').children('tbody').sortable({
						items: ".field_option_flexible_content",
						handle: 'a.acf_fc_reorder',
						helper: fixHelper,
						placeholder: "ui-state-highlight"
					});
					
				});
				
				
				/*----------------------------------------------------------------------
				*
				*	Label update name
				*
				*---------------------------------------------------------------------*/
				
				$('#acf_fields .acf_fc_label input[type="text"]').live('blur', function(){
					var input = $(this).parents('td').siblings('td.acf_fc_name').find('input[type="text"]');
					if(input.val() == ''){
						input.val($(this).val());
					};
				});
				
				})(jQuery);
			</script>
			<?php
			}
		}
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
	*	update_value
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function update_value($post_id, $field, $value)
	{
		$sub_fields = array();
		
		foreach($field['layouts'] as $layout)
		{
			foreach($layout['sub_fields'] as $sub_field)
			{
				$sub_fields[$sub_field['key']] = $sub_field;
			}
		}

		$total = array();
		
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
				$total[] = $row['acf_fc_layout'];
				unset($row['acf_fc_layout']);
					
				// loop through sub fields
				foreach($row as $field_key => $value)
				{
					$sub_field = $sub_fields[$field_key];

					// update full name
					$sub_field['name'] = $field['name'] . '_' . $i . '_' . $sub_field['name'];
					
					// save sub field value
					$this->parent->update_value($post_id, $sub_field, $value);
				}
			}
		}
		
		parent::update_value($post_id, $field, $total);
		
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
		if($field['layouts'])
		{

			$layouts = array();
			
			// loop through and save fields
			foreach($field['layouts'] as $l)
			{				
				// remove dummy field
				unset($l['sub_fields'][999]);
				
				// loop through and save fields
				$i = -1;
				
				$sub_fields = array();
				
				if($l['sub_fields'])
				{
				foreach($l['sub_fields'] as $f)
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
				}
				$l['sub_fields'] = $sub_fields;
				
				$layouts[] = $l;
				
			}
			
			$field['layouts'] = $layouts;
			
		}
		
		// return updated repeater field
		return $field;

	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	get_value
	*	- called from the input edit page to get the value.
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function get_value($post_id, $field)
	{
		$layouts = array();
		foreach($field['layouts'] as $l)
		{
			$layouts[$l['name']] = $l;
		}

		// vars
		$values = array();
		$layout_order = get_post_meta($post_id, $field['name'], true);
		

		if($layout_order)
		{
			$i = -1;
			// loop through rows
			foreach($layout_order as $layout)
			{
				$i++;
				$values[$i]['acf_fc_layout'] = $layout;
				
				// check if layout still exists
				if(isset($layouts[$layout]))
				{
					// loop through sub fields
					foreach($layouts[$layout]['sub_fields'] as $sub_field)
					{
						// store name
						$field_name = $sub_field['name'];
						
						// update full name
						$sub_field['name'] = $field['name'] . '_' . $i . '_' . $field_name;
						
						$values[$i][$field_name] = $this->parent->get_value($post_id, $sub_field);
					}
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
		$layouts = array();
		foreach($field['layouts'] as $l)
		{
			$layouts[$l['name']] = $l;
		}

		// vars
		$values = array();
		$layout_order = get_post_meta($post_id, $field['name'], true);
		

		if($layout_order)
		{
			$i = -1;
			// loop through rows
			foreach($layout_order as $layout)
			{
				$i++;
				$values[$i]['acf_fc_layout'] = $layout;
				
				// loop through sub fields
				foreach($layouts[$layout]['sub_fields'] as $sub_field)
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