<?php

// vars
$fields = isset($args['args']['fields']) ? $args['args']['fields'] : false ;	
$options = isset($args['args']['options']) ? $args['args']['options'] : false;
$show = isset($args['args']['show']) ? $args['args']['show'] : "false";

// defaults
if(!$options)
{
	$options = array(
		'layout'	=>	'default'
	);
}
$post_id = $post ? $post->ID : 999999999;
	
if($fields)
{
	echo '<input type="hidden" name="save_input" value="true" />';
	echo '<div class="options" data-layout="' . $options['layout'] . '" data-show="' . $show . '" style="display:none"></div>';
	foreach($fields as $field)
	{
		// if they didn't select a type, skip this field
		if($field['type'] == 'null') continue;
		
		// set value
		$field['value'] = $this->get_value($post_id, $field);
		
		echo '<div class="field">';
						
			echo '<label class="field_label" for="fields[' . $field['key'] . '][value]">' . $field['label'] . '</label>';
			if($field['instructions']) echo '<p class="instructions">' . $field['instructions'] . '</p>';
			
			$field['name'] = 'fields[' . $field['key'] . ']';
			$this->create_field($field);
		
		echo '</div>';
		
	}
}
	
?>