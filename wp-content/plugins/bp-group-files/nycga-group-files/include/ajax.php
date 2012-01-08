<?php

/*
 * nycga_group_files_increment_download_count()
 *
 * instanciates a document object based on the POST id, 
 * then increments the download_count field in the database by 1.
 *
 * This fires in the background when a user clicks on a document link
 */
function nycga_group_files_increment_download_count(){
	$document_id = (string)$_POST['document_id'];
	if( isset( $document_id ) && ctype_digit( $document_id ) ){
		$document = new NYCGA_Group_Files( $document_id );
		$document->increment_download_count();
	}
}
add_action('wp_ajax_nycga_group_files_increment_downloads','nycga_group_files_increment_download_count');



/*
 * nycga_group_files_ajax_move()
 * the function this calls checks the POST array for any filenames, assigns
 * the meta information and moves them to the selected group.
 *
 * this fires when a user selects "move file" from the bulk uploads section
 * of the site admin screen
 */
function nycga_group_files_ajax_move() {
	echo nycga_group_files_check_uploads_submit();
}
add_action('wp_ajax_nycga_group_files_admin_upload_submit','nycga_group_files_ajax_move');

/**
 * Adds a category that is specifed in the POST array
 * returns the HTML for a new list item to insert at the end
 * of the current category list
 */
function nycga_group_files_add_category() {
	global $bp;

	$category_name = $_POST['category'];

	$parent_id = NYCGA_Group_Files_Template::get_parent_category_id();

	if( !term_exists( $category_name, 'group-files-category',$parent_id ) )
		$new_term = wp_insert_term( $category_name,'group-files-category',array('parent'=>$parent_id));

	$output = "<li id='category-{$new_term['term_id']}'><strong>$category_name</strong>";

	$term_id = $new_term['term_id'];
	$edit_link = wp_nonce_url('?edit=' . $term_id, 'group-files-category-edit');
	$delete_link = wp_nonce_url('?delete=' . $term_id,'group-files-category-delete');

	$output .= '&nbsp; <a class="group-files-category-edit" href="' . $edit_link . '">Edit</a>';
	$output .= ' | <a class="group-files-category-delete" href="' . $delete_link . '">Delete</a>';

	$output .= '</li>';

	die($output);
}
add_action('wp_ajax_nycga_group_files_add_category','nycga_group_files_add_category');

/**
 * Deletes a category via ajax from the group administrator screen
 */
function nycga_group_files_delete_category() {
	$id = $_POST['category_id'];

	if( ctype_digit( $id ) && term_exists( (int)$id,'group-files-category' ) ) {

		if( wp_delete_term( (int)$id, 'group-files-category') ) {
			die('1');
		} else {
			die('0');
		}
	}
}
add_action('wp_ajax_nycga_group_files_delete_category','nycga_group_files_delete_category');