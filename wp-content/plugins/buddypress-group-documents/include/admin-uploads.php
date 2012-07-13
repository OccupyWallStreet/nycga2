<?php

/* Admin Uploads
 * This allows the site administrator to upload multiple files quickly,
 * or to upload large files via FTP and bypass normal HTTP restrictions
 */


/*
 * bp_group_bulk_uploads()
 * 
 * This checks for any files in the bulk "uploads" plugin folder
 * if there are any files, it displays them below the normal plugin settings area
 * the site admin can then select a group, name, and description, and move the file.
 * the actual file is moved out of the uploads folder, and a database record is created.
 */
function bp_group_documents_bulk_uploads(){

	/* this is normally taken care of with AJAX, but it is here as well in case
	something goes wrong (no javascript) and a normal form submit occurs */
	bp_group_documents_check_uploads_submit();

	//array to hold file names
	$files = array();

	$dh = opendir(BP_GROUP_DOCUMENTS_ADMIN_UPLOAD_PATH);	

	if( $dh ) {
		//read files
		while( false !== ($file = readdir( $dh ))) {
			if( $file != '.' && $file != '..' ) {
				$files[] = $file;
			}
		}

		if( !empty( $files ) ) { ?>
			<hr />
			<div id="bp-group-documents-bulk-message"></div>
			<h2><?php _e('Bulk File Uploads','bp-group-documents'); ?></h2>
			<div id="bp-group-documents-bulk-upload">
			<div class="doc-list">
			<?php foreach( $files as $file ) { ?>
				<div class="doc-single">
					<form method="post" class="bp-group-documents-admin-upload" action="">
					<input type="hidden" name="file" value="<?php echo $file; ?>" />
					<div class="file"><strong><?php echo $file; ?></strong></div>
					<div class="group"><select name="group">
						<option value="0"><?php _e('Select Group...','bp-group-documents'); ?></option>
	<?php 
			$groups_list = BP_Groups_Group::get_alphabetically();
			$groups_list = $groups_list['groups'];
			foreach( $groups_list as $group ) {
				echo "<option value='{$group->id}'>" . stripslashes($group->name) . "</option>\n";
			}
	?>
					</select></div>
					<div class="name"><input type="text" name="name" /></div>
					<div class="description"><textarea name="description"></textarea></div>
					<div class="submit"><input type="submit" value="<?php _e('Move File','bp-group-documents'); ?>" /></div>
					</form>
					<div class="clear"></div>
				</div>
			<?php }
			echo '</div></div>';
		}

		closedir($dh);
	}
}
add_action('bp_group_documents_admin_end','bp_group_documents_bulk_uploads');


/*
 * bp_group_documents_check_uploads_submit()
 *
 * this function does the actual heavy-lifting of the moving the files
 * it first check if any files have been submitted, then validates the data,
 * moves the file, and displays applicable messages
 *
 * be sure all message end with a period.  I'm finding extra junk returned
 * with ajax responses, so the period designates the end of a message.
 */
function bp_group_documents_check_uploads_submit($msg_fmt = true) {

	//if user is submitting form
	if( isset( $_POST['file'] ) && isset( $_POST['group'] ) ) {
		if( '0' == $_POST['group'] ) {
			 _e('You must choose a group for the file.','bp-group-documents');
			return false;
		}
		
		//get rid of extra slashes
		if ( get_magic_quotes_gpc() ) {
			$_POST = array_map( 'stripslashes_deep', $_POST );
		}

		//create and populate a shiney new object
		$document = new BP_Group_Documents();
		$document->user_id = get_current_user_id();
		$document->group_id = $_POST['group'];
		$document->file = apply_filters('bp_group_documents_filename_in',$_POST['file']);
		if( $_POST['name'] ) {
			$document->name = $_POST['name'];
		} else {
			$document->name = $_POST['file'];
		}
		$document->description = apply_filters('bp_group_documents_description_in', $_POST['description']);
		$current_path = WP_PLUGIN_DIR . '/buddypress-group-documents/uploads/' . $_POST['file'];

		if( rename( $current_path, $document->get_path(0,1))) {
			if( $document->save(false)) {//passing false tells it not to look for uplaods
				 _e('Document moved successfully.','bp-group-documents');
				do_action('bp_group_documents_admin_upload_success', $document);
			} else {
				_e('There was a problem saving the file info.','bp-group-documents');
			}
		} else {
			 _e('There was a problem moving the file.','bp-group-documents');
		}
	}
}
