<?php

class acf_Post_object extends acf_Field
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
    	
    	$this->name = 'post_object';
		$this->title = __("Post Object",'acf');
		
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
		$field['multiple'] = isset($field['multiple']) ? $field['multiple'] : false;
		$field['post_type'] = isset($field['post_type']) ? $field['post_type'] : false;
		//$field['meta_key'] = isset($field['meta_key']) ? $field['meta_key'] : false;
		//$field['meta_value'] = isset($field['meta_value']) ? $field['meta_value'] : false;
		
		
		if(!$field['post_type'] || !is_array($field['post_type']) || $field['post_type'][0] == "")
		{
			$field['post_type'] = get_post_types(array('public' => true));
		}
		
		// multiple select
		$multiple = '';
		if($field['multiple'] == '1')
		{
			$multiple = ' multiple="multiple" size="5" ';
			$field['name'] .= '[]';
		} 
		
		// html
		echo '<select id="' . $field['name'] . '" class="' . $field['class'] . '" name="' . $field['name'] . '" ' . $multiple . ' >';
		
		// null
		if($field['allow_null'] == '1')
		{
			echo '<option value="null"> - Select - </option>';
		}
		
		
		foreach($field['post_type'] as $post_type)
		{
			// get posts
			$posts = false;
			
			if(is_post_type_hierarchical($post_type))
			{				
				// get pages
				$posts = get_pages(array(
					'numberposts' => -1,
					'post_type' => $post_type,
					'sort_column' => 'menu_order',
					'order' => 'ASC',
					//'meta_key' => $field['meta_key'],
					//'meta_value' => $field['meta_value'],
				));
			}
			else
			{
				// get posts
				$posts = get_posts(array(
					'numberposts' => -1,
					'post_type' => $post_type,
					'orderby' => 'title',
					'order' => 'ASC',
					//'meta_key' => $field['meta_key'],
					//'meta_value' => $field['meta_value'],
				));
			}
		
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


			// if posts, make a group for them
			if($posts)
			{
				echo '<optgroup label="'.$post_type.'">';
				
				foreach($posts as $post)
				{
					$key = $post->ID;
					
					$value = '';
					$ancestors = get_ancestors($post->ID, $post_type);
					if($ancestors)
					{
						foreach($ancestors as $a)
						{
							$value .= '– ';
						}
					}
					$value .= get_the_title($post->ID);
					$selected = '';
					
					
					if(is_array($field['value']))
					{
						// 2. If the value is an array (multiple select), loop through values and check if it is selected
						if(in_array($key, $field['value']))
						{
							$selected = 'selected="selected"';
						}
					}
					else
					{
						// 3. this is not a multiple select, just check normaly
						if($key == $field['value'])
						{
							$selected = 'selected="selected"';
						}
					}	
					
					
					echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
					
					
				}	
				
				echo '</optgroup>';
				
			}// endif
			
		}// endforeach
		

		echo '</select>';
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
		$field['multiple'] = isset($field['multiple']) ? $field['multiple'] : '0';
		$field['allow_null'] = isset($field['allow_null']) ? $field['allow_null'] : '0';
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
				$post_types = array('' => '-All-');
				
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
				<label><?php _e("Allow Null?",'acf'); ?></label>
			</td>
			<td>
				<?php 
				$this->parent->create_field(array(
					'type'	=>	'radio',
					'name'	=>	'fields['.$key.'][allow_null]',
					'value'	=>	$field['allow_null'],
					'choices'	=>	array(
						'1'	=>	'Yes',
						'0'	=>	'No',
					),
					'layout'	=>	'horizontal',
				));
				?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Select multiple values?",'acf'); ?></label>
			</td>
			<td>
				<?php 
				$this->parent->create_field(array(
					'type'	=>	'radio',
					'name'	=>	'fields['.$key.'][multiple]',
					'value'	=>	$field['multiple'],
					'choices'	=>	array(
						'1'	=>	'Yes',
						'0'	=>	'No',
					),
					'layout'	=>	'horizontal',
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
		// get value
		$value = parent::get_value($post_id, $field);
		
		if(!$value)
		{
			return false;
		}
		
		if($value == 'null')
		{
			return false;
		}
		
		if(is_array($value))
		{
			foreach($value as $k => $v)
			{
				$value[$k] = get_post($v);
			}
		}
		else
		{
			$value = get_post($value);
		}
		
		return $value;
	}
		
}

?>