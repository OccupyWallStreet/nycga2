<?php

class acf_Radio extends acf_Field
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
    	
    	$this->name = 'radio';
		$this->title = __('Radio Button','acf');
		
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
		// defaults
		$field['layout'] = isset($field['layout']) ? $field['layout'] : 'vertical';
		$field['choices'] = isset($field['choices']) ? $field['choices'] : array();
		
		// no choices
		if(empty($field['choices']))
		{
			echo '<p>' . __("No choices to choose from",'acf') . '</p>';
			return false;
		}
				
		echo '<ul class="radio_list ' . $field['class'] . ' ' . $field['layout'] . '">';
			
		foreach($field['choices'] as $key => $value)
		{
			$selected = '';
			
			if($key == $field['value'])
			{
				$selected = 'checked="checked" data-checked="checked"';
			}
			
			echo '<li><label><input type="radio" name="' . $field['name'] . '" value="' . $key . '" ' . $selected . ' />' . $value . '</label></li>';
		}
		
		echo '</ul>';

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
		$field['layout'] = isset($field['layout']) ? $field['layout'] : 'vertical';
		$field['default_value'] = isset($field['default_value']) ? $field['default_value'] : '';

		
		// implode checkboxes so they work in a textarea
		if(isset($field['choices']) && is_array($field['choices']))
		{		
			foreach($field['choices'] as $choice_key => $choice_val)
			{
				$field['choices'][$choice_key] = $choice_key.' : '.$choice_val;
			}
			$field['choices'] = implode("\n", $field['choices']);
		}
		else
		{
			$field['choices'] = "";
		}
		
		?>


		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label for=""><?php _e("Choices",'acf'); ?></label>
				<p class="description"><?php _e("Enter your choices one per line<br />
				<br />
				Red<br />
				Blue<br />
				<br />
				or<br />
				<br />
				red : Red<br />
				blue : Blue",'acf'); ?></p>
			</td>
			<td>
				<textarea rows="5" name="fields[<?php echo $key; ?>][choices]" id=""><?php echo $field['choices']; ?></textarea>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Default Value",'acf'); ?></label>
			</td>
			<td>
				<?php 
				$this->parent->create_field(array(
					'type'	=>	'text',
					'name'	=>	'fields['.$key.'][default_value]',
					'value'	=>	$field['default_value'],
				));
				?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label for=""><?php _e("Layout",'acf'); ?></label>
			</td>
			<td>
				<?php 
				$this->parent->create_field(array(
					'type'	=>	'radio',
					'name'	=>	'fields['.$key.'][layout]',
					'value'	=>	$field['layout'],
					'layout' => 'horizontal', 
					'choices' => array(
						'vertical' => 'Vertical', 
						'horizontal' => 'Horizontal'
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
		// defaults
		$field['choices'] = isset($field['choices']) ? $field['choices'] : '';
		
		// vars
		$new_choices = array();
		
		// explode choices from each line
		if(strpos($field['choices'], "\n") !== false)
		{
			// found multiple lines, explode it
			$field['choices'] = explode("\n", $field['choices']);
		}
		else
		{
			// no multiple lines! 
			$field['choices'] = array($field['choices']);
		}
		
		// key => value
		foreach($field['choices'] as $choice)
		{
			if(strpos($choice, ' : ') !== false)
			{
				$choice = explode(' : ', $choice);
				$new_choices[trim($choice[0])] = trim($choice[1]);
			}
			else
			{
				$new_choices[trim($choice)] = trim($choice);
			}
		}
		
		// update choices
		$field['choices'] = $new_choices;
		
		// return updated field
		return $field;

	}
}

?>