<?php

class acf_Color_picker extends acf_Field
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
    	
    	$this->name = 'color_picker';
		$this->title = __("Color Picker",'acf');
		
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
		wp_enqueue_script( 'farbtastic' );
	}
	
	function admin_print_styles()
	{
		wp_enqueue_style( 'farbtastic' );
  
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
		?>
		<script type="text/javascript">
		(function($){
			
			var farbtastic;
			
			$(document).ready(function(){
			
				$('body').append('<div id="acf_color_picker" />');
				farbtastic = $.farbtastic('#acf_color_picker');
				
			});
						
			$('#poststuff input.acf_color_picker').live('focus', function(){
				
				var input = $(this);
				
				$('#acf_color_picker').css({
					left: input.offset().left,
					top: input.offset().top - $('#acf_color_picker').height(),
					display: 'block'
				});
				
				farbtastic.linkTo(this);
				
			}).live('blur', function(){

				$('#acf_color_picker').css({
					display: 'none'
				});
								
			});
			
		})(jQuery);
		</script>
		<style type="text/css">
		#acf_color_picker {
			position: absolute;
			top: 0;
			left: 0;
			display: none;
			background: #fff;
			border: #AAAAAA solid 1px;
			border-radius: 4px;
		}
		</style>
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
		// defaults
		if($field['value'] == "") $field['value'] = '#ffffff';
		
		// html
		echo '<input type="text" value="' . $field['value'] . '" class="acf_color_picker" name="' . $field['name'] . '" id="' . $field['name'] . '" />';

	}
	
	
}

?>