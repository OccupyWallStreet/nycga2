<?php

add_filter('nycga_group_files_name_out','htmlspecialchars');
add_filter('nycga_group_files_description_out','htmlspecialchars');

add_filter('nycga_group_files_filename_in','nycga_group_files_prepare_filename');
add_filter('nycga_group_files_featured_in','nycga_group_files_prepare_checkbox');
add_filter('nycga_group_files_category_ids_in','nycga_group_files_cast_array');

function nycga_group_files_prepare_filename($file) {
	
	$file = time() . '-' . $file;
	$file = preg_replace('/[^0-9a-zA-Z-_.]+/','',$file);
	return $file;
}

//html checkboxes don't send anything if they are not checked
//turn the absence of an explicit "true" into a false
function nycga_group_files_prepare_checkbox( $value ) {
	if( !(isset( $value ) && $value ) )
		$value = false;

	return $value;
}

//when passing category ids to taxonomy functions, they
//cannot be strings.
function nycga_group_files_cast_array( $array ) {
	if( is_array( $array) and count( $array ) ){
		foreach( $array as &$value ) {
			$value = (int)$value;
		}
	}
	return $array;
}
