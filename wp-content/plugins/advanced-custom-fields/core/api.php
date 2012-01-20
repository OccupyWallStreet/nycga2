<?php

// set some globals
reset_the_repeater_field();


/*--------------------------------------------------------------------------------------
*
*	get_fields
*
*	@author Elliot Condon
*	@since 1.0.3
* 
*-------------------------------------------------------------------------------------*/

function get_fields($post_id = false)
{
	// vars
	global $post;
	
	if(!$post_id)
	{
		$post_id = $post->ID;
	}
	elseif($post_id == "options")
	{
		$post_id = 999999999;
	}
	
	// default
	$value = array();
	
	$keys = get_post_custom_keys($post_id);
		
	if($keys)
	{
		foreach($keys as $key)
		{
			if(substr($key, 0, 1) != "_")
			{
				$value[$key] = get_field($key, $post_id);
			}
		}
 	}
 	
	// no value
	if(empty($value))
	{
		return false;
	}
	
	return $value;
	
}



/*--------------------------------------------------------------------------------------
*
*	get_field
*
*	@author Elliot Condon
*	@since 1.0.3
* 
*-------------------------------------------------------------------------------------*/

function get_field($field_name, $post_id = false)
{
	global $post, $acf;
	
	if(!$post_id)
	{
		$post_id = $post->ID;
	}
	elseif($post_id == "options")
	{
		$post_id = 999999999;
	}
	
	// default
	$value = "";
	
	// get value
	$field_key = get_post_meta($post_id, '_' . $field_name, true);
	
	if($field_key != "")
	{
		// we can load the field properly!
		$field = $acf->get_acf_field($field_key);
		$value = $acf->get_value_for_api($post_id, $field);
	}
	else
	{
		// just load the text version
		$value = get_post_meta($post_id, $field_name, true);
	}
	
	// no value?
	if($value == "") $value = false;
		
	return $value;
	
}


/*--------------------------------------------------------------------------------------
*
*	the_field
*
*	@author Elliot Condon
*	@since 1.0.3
* 
*-------------------------------------------------------------------------------------*/

function the_field($field_name, $post_id = false)
{
	$value = get_field($field_name, $post_id);
	
	if(is_array($value))
	{
		$value = @implode(', ',$value);
	}
	
	echo $value;
}


/*--------------------------------------------------------------------------------------
*
*	the_repeater_field
*
*	@author Elliot Condon
*	@since 1.0.3
* 
*-------------------------------------------------------------------------------------*/

function the_repeater_field($field_name, $post_id = false)
{
	
	// if no field, create field + reset count
	if(!$GLOBALS['acf_field'])
	{
		reset_the_repeater_field();
		$GLOBALS['acf_field'] = get_field($field_name, $post_id);
	}
	
	// increase order_no
	$GLOBALS['acf_count']++;
	
	// vars
	$field = $GLOBALS['acf_field'];
	$i = $GLOBALS['acf_count'];
	
	if(isset($field[$i]))
	{
		return true;
	}
	
	// no row, reset the global values
	reset_the_repeater_field();
	return false;
	
}

function the_flexible_field($field_name, $post_id = false)
{
	return the_repeater_field($field_name, $post_id);
}


/*--------------------------------------------------------------------------------------
*
*	reset_the_repeater_field
*
*	@author Elliot Condon
*	@since 1.0.3
* 
*-------------------------------------------------------------------------------------*/

function reset_the_repeater_field()
{
	$GLOBALS['acf_field'] = false;
	$GLOBALS['acf_count'] = -1;
}


/*--------------------------------------------------------------------------------------
*
*	get_sub_field
*
*	@author Elliot Condon
*	@since 1.0.3
* 
*-------------------------------------------------------------------------------------*/

function get_sub_field($field_name)
{

	// vars
	$field = $GLOBALS['acf_field'];
	$i = $GLOBALS['acf_count'];
	
	// no value
	if(!$field) return false;

	if(!isset($field[$i][$field_name])) return false;
	
	return $field[$i][$field_name];
}


/*--------------------------------------------------------------------------------------
*
*	the_sub_field
*
*	@author Elliot Condon
*	@since 1.0.3
* 
*-------------------------------------------------------------------------------------*/

function the_sub_field($field_name, $field = false)
{
	$value = get_sub_field($field_name, $field);
	
	if(is_array($value))
	{
		$value = implode(', ',$value);
	}
	
	echo $value;
}


/*--------------------------------------------------------------------------------------
*
*	register_field
*
*	@author Elliot Condon
*	@since 3.0.0
* 
*-------------------------------------------------------------------------------------*/

$GLOBALS['acf_register_field'] = array();

function register_field($class = "", $url = "")
{
	$GLOBALS['acf_register_field'][] =  array(
		'url'	=> $url,
		'class'	=>	$class,
	);
}

function acf_register_field($array)
{
	$array = array_merge($array, $GLOBALS['acf_register_field']);
	
	return $array;
}
add_filter('acf_register_field', 'acf_register_field');



/*--------------------------------------------------------------------------------------
*
*	register_options_page
*
*	@author Elliot Condon
*	@since 3.0.0
* 
*-------------------------------------------------------------------------------------*/

$GLOBALS['acf_register_options_page'] = array();

function register_options_page($title = "")
{
	$GLOBALS['acf_register_options_page'][] =  array(
		'title'	=> $title,
		'slug' => 'options-' . sanitize_title_with_dashes( $title ),
	);
}

function acf_register_options_page($array)
{
	$array = array_merge($array, $GLOBALS['acf_register_options_page']);
	
	return $array;
}
add_filter('acf_register_options_page', 'acf_register_options_page');



/*--------------------------------------------------------------------------------------
*
*	get_sub_field
*
*	@author Elliot Condon
*	@since 1.0.3
* 
*-------------------------------------------------------------------------------------*/

function get_row_layout()
{
	
	// vars
	$field = $GLOBALS['acf_field'];
	$i = $GLOBALS['acf_count'];
	
	// no value
	if(!$field) return false;

	if(!isset($field[$i]['acf_fc_layout'])) return false;
	
	return $field[$i]['acf_fc_layout'];
}


?>