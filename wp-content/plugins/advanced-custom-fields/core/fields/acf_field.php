<?php

/*
 *	This is the base acf field frow which
 *	all other fields extend. Here you can 
 *	find every function for your field
 *
 */
 
class acf_Field
{
	var $name;
	var $title;
	var $parent;
	
	
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
		$this->parent = $parent;
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
		update_post_meta($post_id, $field['name'], $value);
		update_post_meta($post_id, '_' . $field['name'], $field['key']);
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
		return $field;
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
		// If this is a new acf, there will be no custom keys!
	 	if(!get_post_custom_keys($post_id) && isset($field['default_value']))
	 	{
	 		return $field['default_value'];
	 	}
	 	
		return get_post_meta($post_id, $field['name'], true);
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
		return $this->get_value($post_id, $field);
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	format_value_for_input
	*	- 
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	* 
	*-------------------------------------------------------------------------------------
	
	function format_value_for_input($value, $field)
	{
		return $value;
	}
	*/
	
	/*--------------------------------------------------------------------------------------
	*
	*	format_value_for_api
	*	- 
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	* 
	*-------------------------------------------------------------------------------------
	
	function format_value_for_api($value, $field)
	{
		return $value;
	}
	*/
}

?>