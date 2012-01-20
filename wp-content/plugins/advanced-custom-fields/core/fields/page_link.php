<?php

class acf_Page_link extends acf_Field
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
    	
    	$this->name = 'page_link';
		$this->title = __('Page Link','acf');
		
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
			foreach($field['post_type'] as $key => $value)
			{
				if($value == 'attachment')
				{
					unset($field['post_type'][$key]);
				}
			}
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
		
		?>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label for=""><?php _e("Post Type",'acf'); ?></label>
				<p class="description"><?php _e("Filter posts by selecting a post type<br />
				Tip: deselect all post types to show all post type's posts",'acf'); ?></p>
			</td>
			<td>
				<?php 
				$post_types = array('' => '-All-');
				
				foreach (get_post_types() as $post_type ) {
				  $post_types[$post_type] = $post_type;
				}
				
				unset($post_types['attachment']);
				unset($post_types['nav_menu_item']);
				unset($post_types['revision']);
				unset($post_types['acf']);
				
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
				$value[$k] = get_permalink($v);
			}
		}
		else
		{
			$value = get_permalink($value);
		}
		
		return $value;
	}
	

	
}

?>