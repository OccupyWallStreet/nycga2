<?php

global $wpdb;

// tables
$acf_fields = $wpdb->prefix.'acf_fields';
$acf_values = $wpdb->prefix.'acf_values';
$acf_rules = $wpdb->prefix.'acf_rules';
$wp_postmeta = $wpdb->prefix.'postmeta';

// vars
$return = array(
	'status'	=>	false,
	'message'	=>	"",
	'next'		=>	false,
);

// versions
switch($_POST['version'])
{	

	/*---------------------
	*
	*	3.0.0
	* 
	*--------------------*/
	
	case '3.0.0':
	
		// upgrade options first as "field_group_layout" will cause get_fields to fail!
		
		// get acf's
		$acfs = get_pages(array(
			'numberposts' 	=> 	-1,
			'post_type'		=>	'acf',
			'sort_column' => 'menu_order',
			'order' => 'ASC',
		));
		
		if($acfs)
		{
			foreach($acfs as $acf)
			{
				// position
				update_post_meta($acf->ID, 'position', 'normal');
				
				//layout
				$layout = get_post_meta($acf->ID, 'field_group_layout', true) ? get_post_meta($acf->ID, 'field_group_layout', true) : 'in_box';
				if($layout == 'in_box')
				{
					$layout = 'default';
				}
				else
				{
					$layout = 'no_box';
				}
				update_post_meta($acf->ID, 'layout', $layout);
				delete_post_meta($acf->ID, 'field_group_layout');
				
				// show_on_page
				$show_on_page = get_post_meta($acf->ID, 'show_on_page', true) ? get_post_meta($acf->ID, 'show_on_page', true) : array();
				if($show_on_page)
		 		{
		 			$show_on_page = unserialize($show_on_page);
		 		}
		 		update_post_meta($acf->ID, 'show_on_page', $show_on_page);
		 		
			}
		}
		
	    $return = array(
	    	'status'	=>	true,
			'message'	=>	"Migrating Options...",
			'next'		=>	'3.0.0 (step 2)',
	    );
	    
	break;
	
	/*---------------------
	*
	*	3.0.0
	* 
	*--------------------*/
	
	case '3.0.0 (step 2)':
		
		// get acf's
		$acfs = get_pages(array(
			'numberposts' 	=> 	-1,
			'post_type'		=>	'acf',
			'sort_column' => 'menu_order',
			'order' => 'ASC',
		));
		
		if($acfs)
		{
			foreach($acfs as $acf)
			{
				// allorany doesn't need to change!
				
	 			$rules = $wpdb->get_results("SELECT * FROM $acf_rules WHERE acf_id = '$acf->ID' ORDER BY order_no ASC", ARRAY_A);

				if($rules)
				{
					foreach($rules as $rule)
					{
						// options rule has changed
						if($rule['param'] == 'options_page')
						{
							$rule['value'] = 'Options';
						}
						
						add_post_meta($acf->ID, 'rule', $rule);
					}
				}

			}	
		}
		
	    $return = array(
	    	'status'	=>	true,
			'message'	=>	"Migrating Location Rules...",
			'next'		=>	'3.0.0 (step 3)',
	    );
	    
	break;
	
	/*---------------------
	*
	*	3.0.0
	* 
	*--------------------*/

	case '3.0.0 (step 3)':
		
		$message = "Migrating Fields…";
	    
	    $parent_id = 0;
	    $fields = $wpdb->get_results("SELECT * FROM $acf_fields WHERE parent_id = $parent_id ORDER BY order_no, name", ARRAY_A);
	 	
	 	if($fields)
	 	{
			// loop through fields
		 	foreach($fields as $field)
		 	{
		 		
				// unserialize options
				if(@unserialize($field['options']))
				{
					$field['options'] = unserialize($field['options']);
				}
				else
				{
					$field['options'] = array();
				}
	
		 		
		 		// sub fields
		 		if($field['type'] == 'repeater')
		 		{
		 			$field['options']['sub_fields'] = array();
		 			
		 			$parent_id = $field['id'];
		 			$sub_fields = $wpdb->get_results("SELECT * FROM $acf_fields WHERE parent_id = $parent_id ORDER BY order_no, name", ARRAY_A);
			
		 			
		 			// if fields are empty, this must be a new or broken acf. 
				 	if(empty($sub_fields))
				 	{
				 		$field['options']['sub_fields'] = array();
				 	}
				 	else
				 	{
				 		// loop through fields
					 	foreach($sub_fields as $sub_field)
					 	{
					 		// unserialize options
					 		if(@unserialize($sub_field['options']))
							{
								$sub_field['options'] = @unserialize($sub_field['options']);
							}
							else
							{
								$sub_field['options'] = array();
							}
							
							// merge options with field
					 		$sub_field = array_merge($sub_field, $sub_field['options']);
					 		
					 		unset($sub_field['options']);
							
							// each field has a unique id!
							if(!isset($sub_field['key'])) $sub_field['key'] = 'field_' . $sub_field['id'];
				
							$field['options']['sub_fields'][] = $sub_field;
						}
				 	}
				 			 	
		 		}
		 		// end if sub field
		 		
		 		
		 		// merge options with field
		 		$field = array_merge($field, $field['options']);
		 		
		 		unset($field['options']);
		 		
		 		// each field has a unique id!
				if(!isset($field['key'])) $field['key'] = 'field_' . $field['id'];
				
				// update field
				update_post_meta($field['post_id'], $field['key'], $field);
				//$this->update_field($field['post_id'], $field);
				
				
		 		// create field name (field_rand)
		 		//$message .= print_r($field, true) . '<br /><br />';
		 	}
		 	// end foreach $fields
	 	}
	 	
		
		$return = array(
	    	'status'	=>	true,
			'message'	=>	$message,
			'next'		=>	'3.0.0 (step 4)',
	    );
	    
	break;
	
	/*---------------------
	*
	*	3.0.0
	* 
	*--------------------*/
	
	case '3.0.0 (step 4)':
		
		$message = "Migrating Values...";
		
		// update normal values
		$values = $wpdb->get_results("SELECT v.field_id, m.post_id, m.meta_key, m.meta_value FROM $acf_values v LEFT JOIN $wp_postmeta m ON v.value = m.meta_id WHERE v.sub_field_id = 0", ARRAY_A);
		if($values)
		{
			foreach($values as $value)
			{
				// options page
				if($value['post_id'] == 0) $value['post_id'] = 999999999;
				
				// unserialize value (relationship, multi select, etc)
				if(@unserialize($value['meta_value']))
				{
					$value['meta_value'] = unserialize($value['meta_value']);
				}		
				
				update_post_meta($value['post_id'], $value['meta_key'], $value['meta_value']);
				update_post_meta($value['post_id'], '_' . $value['meta_key'], 'field_' . $value['field_id']);
			}
		}
		
		// update repeater values
		$values = $wpdb->get_results("SELECT v.field_id, v.sub_field_id, v.order_no, m.post_id, m.meta_key, m.meta_value FROM $acf_values v LEFT JOIN $wp_postmeta m ON v.value = m.meta_id WHERE v.sub_field_id != 0", ARRAY_A);
		if($values)
		{
			$rows = array();

			foreach($values as $value)
			{
				// update row count
				$row = (int) $value['order_no'] + 1;
				
				// options page
				if($value['post_id'] == 0) $value['post_id'] = 999999999;
				
				// unserialize value (relationship, multi select, etc)
				if(@unserialize($value['meta_value']))
				{
					$value['meta_value'] = unserialize($value['meta_value']);
				}
				
				// current row
				$current_row = isset($rows[$value['post_id']][$value['field_id']]) ? $rows[$value['post_id']][$value['field_id']] : 0;
				if($row > $current_row) $rows[$value['post_id']][$value['field_id']] = (int) $row;
				
				// get field name
				$field_name = $wpdb->get_var($wpdb->prepare("SELECT name FROM $acf_fields WHERE id = %d", $value['field_id']));
				
				// get sub field name
				$sub_field_name = $wpdb->get_var($wpdb->prepare("SELECT name FROM $acf_fields WHERE id = %d", $value['sub_field_id']));
				
				// save new value
				$new_meta_key = $field_name . '_' . $value['order_no'] . '_' . $sub_field_name;
				update_post_meta($value['post_id'], $new_meta_key , $value['meta_value']);
				
				// save value hidden field id
				update_post_meta($value['post_id'], '_' . $new_meta_key, 'field_' . $value['sub_field_id']);
			}
			
			foreach($rows as $post_id => $field_ids)
			{
				foreach($field_ids as $field_id => $row_count)
				{
					// get sub field name
					$field_name = $wpdb->get_var($wpdb->prepare("SELECT name FROM $acf_fields WHERE id = %d", $field_id));
				
					delete_post_meta($post_id, $field_name);
					update_post_meta($post_id, $field_name, $row_count);
					update_post_meta($post_id, '_' . $field_name, 'field_' . $field_id);
			
				}
			}
			
		}
		
		// update version (only upgrade 1 time)
		update_option('acf_version','3.0.0');
		 	
	    $return = array(
	    	'status'	=>	true,
			'message'	=>	$message,
			'next'		=>	false,
	    );
	    
	break;
	
	/*---------------------
	*
	*	3.0.0 rc1
	* 
	*--------------------
	
	case '3.0.0 beta rc1':
		
		$message = "Renaming ACF data...";
		
		$acfs = get_pages(array(
			'numberposts' 	=> 	-1,
			'post_type'		=>	'acf',
			'sort_column' => 'menu_order',
			'order' => 'ASC',
		));
		
		if($acfs)
		{
			foreach($acfs as $acf)
			{
				
				
				
				$keys = get_post_custom_keys($post_id);
		
				if($keys)
				{
					foreach($keys as $key)
					{
						if(strpos($key, 'field_') !== false)
						{
							$sub_field_name = $wpdb->get_var($wpdb->prepare("UPDATE meta_key %s FROM $wp_postmeta WHERE meta_key = %s", $value['sub_field_id']));
							
							$field = $this->get_acf_field($key, $post_id);
					
					
					
					
				
				$fields = $this->get_acf_fields($acf->ID);
				
				if($fields)
				{
					foreach($acfs as $acf)
					{
						update_post_meta($post_id, $field['key'], $field);
					}
				}
				
			}
		}
		
	break;
	*/
}

// return json
echo json_encode($return);
die;

?>