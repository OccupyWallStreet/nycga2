<?php

class acf_Select extends acf_Field
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
    	
    	$this->name = 'select';
		$this->title = __("Select",'acf');
		
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
		$field['value'] = isset($field['value']) ? $field['value'] : array();
		$field['multiple'] = isset($field['multiple']) ? $field['multiple'] : false;
		$field['allow_null'] = isset($field['allow_null']) ? $field['allow_null'] : false;
		$field['choices'] = isset($field['choices']) ? $field['choices'] : array();
		$field['optgroup'] = isset($field['optgroup']) ? $field['optgroup'] : false;
		
		
		// no choices
		if(empty($field['choices']))
		{
			echo '<p>' . __("No choices to choose from",'acf') . '</p>';
			return false;
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
		
		// loop through values and add them as options
		foreach($field['choices'] as $key => $value)
		{
			if($field['optgroup'])
			{
				// this select is grouped with optgroup
				if($key != '') echo '<optgroup label="'.$key.'">';
				
				if($value)
				{
					foreach($value as $id => $label)
					{
						$selected = '';
						if(is_array($field['value']) && in_array($id, $field['value']))
						{
							// 2. If the value is an array (multiple select), loop through values and check if it is selected
							$selected = 'selected="selected"';
						}
						else
						{
							// 3. this is not a multiple select, just check normaly
							if($id == $field['value'])
							{
								$selected = 'selected="selected"';
							}
						}	
						echo '<option value="'.$id.'" '.$selected.'>'.$label.'</option>';
					}
				}
				
				if($key != '') echo '</optgroup>';
			}
			else
			{
				$selected = '';
				if(is_array($field['value']) && in_array($key, $field['value']))
				{
					// 2. If the value is an array (multiple select), loop through values and check if it is selected
					$selected = 'selected="selected"';
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


			
		}

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
		$field['multiple'] = isset($field['multiple']) ? $field['multiple'] : '0';
		$field['allow_null'] = isset($field['allow_null']) ? $field['allow_null'] : '0';
		$field['default_value'] = isset($field['default_value']) ? $field['default_value'] : '';
		
		// implode selects so they work in a textarea
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