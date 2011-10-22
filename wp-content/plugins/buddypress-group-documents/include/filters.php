<?php

add_filter('bp_group_documents_name_out','htmlspecialchars');
add_filter('bp_group_documents_description_out','htmlspecialchars');

add_filter('bp_group_documents_filename_in','bp_group_documents_prepare_filename');
add_filter('bp_group_documents_featured_in','bp_group_documents_prepare_checkbox');
add_filter('bp_group_documents_category_ids_in','bp_group_documents_cast_array');

function bp_group_documents_prepare_filename($file) {
	
	$file = time() . '-' . $file;
	$file = preg_replace('/[^0-9a-zA-Z-_.]+/','',$file);
	return $file;
}

//html checkboxes don't send anything if they are not checked
//turn the absence of an explicit "true" into a false
function bp_group_documents_prepare_checkbox( $value ) {
	if( !(isset( $value ) && $value ) )
		$value = false;

	return $value;
}

//when passing category ids to taxonomy functions, they
//cannot be strings.
function bp_group_documents_cast_array( $array ) {
	if( is_array( $array) and count( $array ) ){
		foreach( $array as &$value ) {
			$value = (int)$value;
		}
	}
	return $array;
}
