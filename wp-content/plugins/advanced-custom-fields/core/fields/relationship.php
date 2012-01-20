<?php

class acf_Relationship extends acf_Field
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
    	
    	$this->name = 'relationship';
		$this->title = __("Relationship",'acf');
		
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
			
			/*----------------------------------------------------------------------
			*
			*	update_input_val
			*
			*---------------------------------------------------------------------*/
			
			$.fn.update_input_val = function()
			{
				// vars
				var div = $(this);
				var right = div.find('.relationship_right .relationship_list');
				var value = new Array();
				
				// add id's to array
				right.find('a:not(.hide)').each(function(){
					value.push($(this).attr('data-post_id'));
				});
				
				// set value
				div.children('input').val(value.join(','));
			};
			
			
			/*----------------------------------------------------------------------
			*
			*	Add
			*
			*---------------------------------------------------------------------*/
			
			$('#poststuff .acf_relationship .relationship_left .relationship_list a').live('click', function(){
				
				// vars
				var id = $(this).attr('data-post_id');
				var div = $(this).closest('.acf_relationship');
				var max = parseInt(div.attr('data-max')); if(max == -1){ max = 9999; }
				var right = div.find('.relationship_right .relationship_list');
				
				// max posts
				if(right.find('a:not(.hide)').length >= max)
				{
					alert('Maximum values reached ( ' + max + ' values )');
					return false;
				}

				// hide / show
				$(this).addClass('hide');
				right.find('a[data-post_id="' + id + '"]').removeClass('hide').appendTo(right);
				
				// update input value
				div.update_input_val();
				
				return false;
				
			});
			
			
			/*----------------------------------------------------------------------
			*
			*	Remove
			*
			*---------------------------------------------------------------------*/
			
			$('#poststuff .acf_relationship .relationship_right .relationship_list a').live('click', function(){
				
				// vars
				var id = $(this).attr('data-post_id');
				var div = $(this).closest('.acf_relationship');
				var left = div.find('.relationship_left .relationship_list');
				
				// hide / show
				$(this).addClass('hide');
				left.find('a[data-post_id="' + id + '"]').removeClass('hide');
				
				// update input value
				div.update_input_val();
				
				return false;
				
			});
			
			
			/*----------------------------------------------------------------------
			*
			*	Search Left List
			*
			*---------------------------------------------------------------------*/
			
			$.expr[':'].Contains = function(a,i,m){
		    	return (a.textContent || a.innerText || "").toUpperCase().indexOf(m[3].toUpperCase())>=0;
			};
			
			$('#poststuff .acf_relationship input.relationship_search').live('change', function()
			{	
				// vars
				var filter = $(this).val();
				var div = $(this).closest('.acf_relationship');
				var left = div.find('.relationship_left .relationship_list');
				
			    if(filter)
			    {
					left.find("a:not(:Contains(" + filter + "))").addClass('filter_hide');
			        left.find("a:Contains(" + filter + "):not(.hide)").removeClass('filter_hide');
			    }
			    else
			    {
			    	left.find("a:not(.hide)").removeClass('filter_hide');
			    }
		
			    return false;
			    
			})
			.live('keyup', function(){
			    $(this).change();
			})
			.live('focus', function(){
				$(this).siblings('label').hide();
			})
			.live('blur', function(){
				if($(this).val() == "")
				{
					$(this).siblings('label').show();
				}
			});
			
			
			/*----------------------------------------------------------------------
			*
			*	setup_acf_relationship
			*
			*---------------------------------------------------------------------*/
			
			$.fn.setup_acf_relationship = function(){
				
				$(this).find('.acf_relationship').each(function(){
				
					// vars
					var div = $(this);
					
					// reset search value
					div.find('input.relationship_search').val('');
					
					// make right sortable
					div.find('.relationship_right .relationship_list').sortable({
						axis: "y", // limit the dragging to up/down only
					    start: function(event, ui)
					    {
							ui.item.addClass('sortable_active');
					    },
					    stop: function(event, ui)
					    {
					    	ui.item.removeClass('sortable_active');
					    	div.update_input_val();
					    }
					});
				
				});
				
			};
			
			$(document).ready(function(){
				
				$('#poststuff').setup_acf_relationship();
				
				// create wysiwyg when you add a repeater row
				$('.repeater #add_field').live('click', function(){

					var repeater = $(this).closest('.repeater');
					
					// run after the repeater has added the row
					setTimeout(function(){
						repeater.children('table').children('tbody').children('tr:last-child').setup_acf_relationship();
					}, 1);
					
				});
				
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

		$field['max'] = isset($field['max']) ? $field['max'] : '-1';
		$field['post_type'] = isset($field['post_type']) ? $field['post_type'] : false;
		$field['taxonomy'] = isset($field['taxonomy']) ? $field['taxonomy'] : array('all');
		//$field['meta_key'] = isset($field['meta_key']) ? $field['meta_key'] : false;
		//$field['meta_value'] = isset($field['meta_value']) ? $field['meta_value'] : false;
	
		if(!$field['post_type'] || !is_array($field['post_type']) || $field['post_type'][0] == "")
		{
			$field['post_type'] = get_post_types(array('public' => true));
		}
		
		// attachment doesn't work if it is the only item in an array???
		if(is_array($field['post_type']) && count($field['post_type']) == 1)
		{
			$field['post_type'] = $field['post_type'][0];
		}

		$posts = get_posts(array(
			'numberposts' 	=> 	-1,
			'post_type'		=>	$field['post_type'],
			'orderby'		=>	'title',
			'order'			=>	'ASC',
			//'meta_key'		=>	$field['meta_key'],
			//'meta_value'	=>	$field['meta_value'],
		));
		
		// filter by taxonomy
		if(in_array('all', $field['taxonomy']))
		{
			// leave all posts
		}
		else
		{
			if($posts)
			{
				foreach($posts as $k => $post)
				{
					if(!$this->parent->in_taxonomy($post, $field['taxonomy']))
					{
						unset($posts[$k]);
					}
				}
			}
		}
		
		$values_array = array();
		if($field['value'] != "")
		{
			$temp_array = explode(',', $field['value']);
			foreach($temp_array as $p)
			{
				// if the post doesn't exist, continue
				if(!get_the_title($p)) continue;
				
				$values_array[] = $p;
			}
		}
		
		
		
		
		
		?>
		<div class="acf_relationship" data-max="<?php echo $field['max']; ?>">
			
			<input type="hidden" name="<?php echo $field['name']; ?>" value="<?php echo implode(',', $values_array); ?>" />
			
			<div class="relationship_left">
				<table class="widefat">
					<thead>
						<tr>
							<th>
								<label class="relationship_label" for="relationship_<?php echo $field['name']; ?>">Search...</label>
								<input class="relationship_search" type="text" id="relationship_<?php echo $field['name']; ?>" />
								<div class="clear_relationship_search"></div>
							</th>
						</tr>
					</thead>
				</table>
				<div class="relationship_list">
				<?php
				if($posts)
				{
					foreach($posts as $post)
					{
						if(!get_the_title($post->ID)) continue;
						
						$class = in_array($post->ID, $values_array) ? 'hide' : '';
						echo '<a href="javascript:;" class="' . $class . '" data-post_id="' . $post->ID . '">' . get_the_title($post->ID) . '<span class="add"></span></a>';
					}
				}
				?>
				</div>
			</div>
			
			<div class="relationship_right">
				<div class="relationship_list">
				<?php
				$temp_posts = array();
				
				if($posts)
				{
					foreach($posts as $post)
					{
						$temp_posts[$post->ID] = $post;
					}
				}
				
				if($temp_posts)
				{
					foreach($values_array as $value)
					{
						echo '<a href="javascript:;" class="" data-post_id="' . $temp_posts[$value]->ID . '">' . get_the_title($temp_posts[$value]->ID) . '<span class="remove"></span></a>';
						unset($temp_posts[$value]);
					}
					
					foreach($temp_posts as $id => $post)
					{
						echo '<a href="javascript:;" class="hide" data-post_id="' . $post->ID . '">' . get_the_title($post->ID) . '<span class="remove"></span></a>';
					}
				}
					
				?>
				</div>
			</div>
			
			
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
		// defaults
		$field['post_type'] = isset($field['post_type']) ? $field['post_type'] : '';
		$field['max'] = isset($field['max']) ? $field['max'] : '-1';
		$field['taxonomy'] = isset($field['taxonomy']) ? $field['taxonomy'] : array('all');
		//$field['meta_key'] = isset($field['meta_key']) ? $field['meta_key'] : '';
		//$field['meta_value'] = isset($field['meta_value']) ? $field['meta_value'] : '';
		?>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label for=""><?php _e("Post Type",'acf'); ?></label>
			</td>
			<td>
				<?php 
				$post_types = array('' => '- All -');
				
				foreach (get_post_types(array('public' => true)) as $post_type ) {
				  $post_types[$post_type] = $post_type;
				}
				
				$this->parent->create_field(array(
					'type'	=>	'select',
					'name'	=>	'fields['.$key.'][post_type]',
					'value'	=>	$field['post_type'],
					'choices'	=>	$post_types,
					'multiple'	=>	'1',
				));
				?>
			</td>
		</tr>
		<?php /*<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Filter Posts",'acf'); ?></label>
				<p class="description"><?php _e("Where meta_key == meta_value",'acf'); ?></p>
			</td>
			<td>
				<div style="width:45%; float:left">
				<?php 
				$this->parent->create_field(array(
					'type'	=>	'text',
					'name'	=>	'fields['.$key.'][meta_key]',
					'value'	=>	$field['meta_key'],
				));
				?>
				</div>
				<div style="width:10%; float:left; text-align:center; padding:5px 0 0;">is equal to</div>
				<div style="width:45%; float:left">
				<?php 
				$this->parent->create_field(array(
					'type'	=>	'text',
					'name'	=>	'fields['.$key.'][meta_value]',
					'value'	=>	$field['meta_value'],
				));
				?>
				</div>
			</td>
		</tr>*/ ?>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Filter from Taxonomy",'acf'); ?></label>
			</td>
			<td>
				<?php 
				$choices = array(
					'' => array(
						'all' => '- All -'
					)
				);
				$choices = array_merge($choices, $this->parent->get_taxonomies_for_select());
				$this->parent->create_field(array(
					'type'	=>	'select',
					'name'	=>	'fields['.$key.'][taxonomy]',
					'value'	=>	$field['taxonomy'],
					'choices' => $choices,
					'optgroup' => true,
					'multiple'	=>	'1',
				));
				?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Maximum posts",'acf'); ?></label>
				<p class="description"><?php _e("Set to -1 for inifinit",'acf'); ?></p>
			</td>
			<td>
				<?php 
				$this->parent->create_field(array(
					'type'	=>	'text',
					'name'	=>	'fields['.$key.'][max]',
					'value'	=>	$field['max'],
				));
				?>
			</td>
		</tr>
		
		

		<?php
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
		$value = parent::get_value($post_id, $field);
		$return = false;
		
		if(!$value || $value == "")
		{
			return $return;
		}
		
		$value = explode(',', $value);
		
		if(is_array($value))
		{
			$return = array();
			foreach($value as $v)
			{
				$return[] = get_post($v);
			}
		}
		else
		{
			$return = array(get_post($value));
		}
		
		return $return;
	}
	

	
}

?>