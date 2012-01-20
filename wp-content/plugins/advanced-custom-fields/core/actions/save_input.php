<?php

// strip slashes
$_POST = array_map('stripslashes_deep', $_POST);

// save fields
$fields = $_POST['fields'];

if($fields)
{
	foreach($fields as $key => $value)
	{
		// get field
		$field = $this->get_acf_field($key);
		
		$this->update_value($post_id, $field, $value);
	}
}

?>