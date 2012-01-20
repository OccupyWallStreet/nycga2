<?php

class acf_Date_picker extends acf_Field
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
    	
    	$this->name = 'date_picker';
		$this->title = __("Date Picker",'acf');
		
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
		// add datepicker
		echo '<link rel="stylesheet" type="text/css" href="'.$this->parent->dir.'/core/fields/date_picker/style.date_picker.css" />';
		echo '<script type="text/javascript" src="'.$this->parent->dir.'/core/fields/date_picker/jquery.ui.datepicker.js" ></script>';
		?>
		<script type="text/javascript">
		(function($){

				
			$('#poststuff input.acf_datepicker').live('focus', function(){

				var input = $(this);
				
				if(!input.hasClass('active'))
				{
					
					// vars
					var format = input.attr('data-date_format') ? input.attr('data-date_format') : 'dd/mm/yy';
					
					// add date picker and refocus
					input.addClass('active').datepicker({ 
						dateFormat: format 
					})
					
					// set a timeout to re focus the input (after it has the datepicker!)
					setTimeout(function(){
						input.trigger('blur').trigger('focus');
					}, 1);
					
					// wrap the datepicker (only if it hasn't already been wrapped)
					if($('body > #ui-datepicker-div').length > 0)
					{
						$('#ui-datepicker-div').wrap('<div class="ui-acf" />');
					}
					
				}
				
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
		$field['date_format'] = isset($field['date_format']) ? $field['date_format'] : 'dd/mm/yy';
		
		// html
		echo '<input type="text" value="' . $field['value'] . '" class="acf_datepicker" name="' . $field['name'] . '" data-date_format="' . $field['date_format'] . '" />';

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
		$field['date_format'] = isset($field['date_format']) ? $field['date_format'] : '';
		
		?>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Date format",'acf'); ?></label>
				<p class="description"><?php _e("eg. dd/mm/yy. read more about",'acf'); ?> <a href="http://docs.jquery.com/UI/Datepicker/formatDate">formatDate</a></p>
			</td>
			<td>
				<input type="text" name="fields[<?php echo $key; ?>][date_format]" value="<?php echo $field['date_format']; ?>" />
			</td>
		</tr>

		<?php
	}
		
	
	
}

?>