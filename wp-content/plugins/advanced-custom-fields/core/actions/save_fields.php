<?php

// strip slashes
$_POST = array_map('stripslashes_deep', $_POST);

// save fields
$fields = $_POST['fields'];

// get all keys to find fields
$dont_delete = array();

if($fields)
{
	$i = -1;
	
	// remove dummy field
	unset($fields['999']);
	
	// loop through and save fields
	foreach($fields as $field)
	{
		$i++;
		
		// each field has a unique id!
		if(!isset($field['key'])) $field['key'] = 'field_' . uniqid();
		
		// add to dont delete array
		$dont_delete[] = $field['key'];
		
		// order
		$field['order_no'] = $i;
		
		// update field
		$this->update_field($post_id, $field);
	}
}

// delete all other field
foreach(get_post_custom_keys($post_id) as $key)
{
	if(strpos($key, 'field_') !== false && !in_array($key, $dont_delete))
	{
		// this is a field, and it wasn't found in the dont_delete array
		delete_post_meta($post_id, $key);
	}
}

// save location
$location = $_POST['location'];

if(!isset($location['allorany'])) { $location['allorany'] = 'all'; }
update_post_meta($post_id, 'allorany', $location['allorany']);

delete_post_meta($post_id, 'rule');
if($location['rules'])
{
	foreach($location['rules'] as $k => $rule)
	{
		
		$rule['order_no'] = $k;
		add_post_meta($post_id, 'rule', $rule);
	}
}

// save options
$options = $_POST['options'];

if(!isset($options['position'])) { $options['position'] = 'normal'; }
if(!isset($options['layout'])) { $options['layout'] = 'default'; }
if(!isset($options['show_on_page'])) { $options['show_on_page'] = array(); }

update_post_meta($post_id, 'position', $options['position']);
update_post_meta($post_id, 'layout', $options['layout']);
update_post_meta($post_id, 'show_on_page', $options['show_on_page']);

?>