<?php
/**
 * @package		WordPress
 * @subpackage	BuddyPress
 * @author		Boris Glumpler
 * @copyright	2011, ShabuShabu Webdesign
 * @link		http://shabushabu.eu
 * @license		http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */

// No direct access is allowed
if( ! defined( 'ABSPATH' ) ) exit;

/**
 * Process document edit
 * 
 * @package	Documents
 * @since 	1.7
 */
function bpe_process_document( $input, $files, $displayed_event, $api = false, $edit = false )
{
	$documents = bpe_upload_documents( $files, $input );
	$docs = $documents['docs'];
	
	if( ! empty( $documents['errors'] ) )
	{
		if( ! $api )
		{
			bpe_add_message( implode( '<br />', (array)$documents['errors'] ), 'error' );
			bp_core_redirect( bp_get_root_domain() . $input['_wp_http_referer'] );
		}
		else
			return bpe_api_message( implode( ' ', (array)$documents['errors'] ), 'failed' );
	}

	// process any uploaded documents
	if( is_array( $docs ) )
	{
		foreach( $docs as $key => $document )
		{
			$name 		 = ( isset( $document['name'] ) ) ? $document['name'] : '';
			$description = ( isset( $document['desc'] ) ) ? $document['desc'] : '';
			$link 		 = ( isset( $document['link'] ) ) ? $document['link'] : '';
			
			$file_type = bpe_file_extension( $link );
			
			bpe_add_document( null, bpe_get_event_id( $displayed_event ), $name, $description, $link, $file_type );
		}
	}
	
	$doc_ids = array();
	$documents = ( isset( $input['document'] ) ) ? (array)$input['document'] : array();
	// update any existing documents now
	foreach( $documents as $doc )
	{
		$id   = ( isset( $doc['id']   ) ) ? $doc['id']   : '';
		$name = ( isset( $doc['name'] ) ) ? $doc['name'] : '';
		$desc = ( isset( $doc['desc'] ) ) ? $doc['desc'] : '';
		$link = ( isset( $doc['link'] ) ) ? $doc['link'] : '';
		$type = ( isset( $doc['type'] ) ) ? $doc['type'] : '';

		bpe_add_document( $id, bpe_get_event_id( $displayed_event ), $name, $desc, $link, $type );
		$doc_ids[] = $id;
	}

	// delete any documents now
	$existing_docs = explode( ',', $input['document_ids'] );
	
	$delete_documents = array_diff( (array)$existing_docs, $doc_ids );
	
	if( count( $delete_documents ) > 0 )
	{
		bpe_delete_files_by_ids( $delete_documents );
		bpe_delete_documents_by_ids( $delete_documents );
	}
	
	if( $edit )
		do_action( 'bpe_updated_event_documents', $displayed_event );

	if( ! $api && $edit )
		bpe_add_message( __( 'Documents have been updated.', 'events' ) );
	else
		return bpe_api_message( 'Documents have been updated', 'success' );
}

/**
 * Display documents for editing
 *
 * @package Documents
 * @since 	1.7
 */
function bpe_edit_documents( $eid = false )
{
	if( ! $eid )
		$eid = bpe_get_displayed_event( 'id' );
		
	$document_ids = array();
	
	if( bpe_has_documents( array( 'event_id' => $eid ) ) ) :
	
		while ( bpe_documents() ) : bpe_the_document();
		
		$document_ids[] = bpe_get_document_id();
		
		?>
		<div class="MultiFile-label">
			<a class="button del-document" href="#">X</a>
            
            <input type="hidden" name="document[<?php bpe_document_id() ?>][id]" value="<?php bpe_document_id() ?>" />
            <input type="hidden" name="document[<?php bpe_document_id() ?>][link]" value="<?php bpe_document_url_raw() ?>" />
            <input type="hidden" name="document[<?php bpe_document_id() ?>][type]" value="<?php bpe_document_type() ?>" />
            			
			<label for="name-<?php bpe_document_id() ?>"><?php _e( '* File Name', 'events' ) ?></label>
			<input type="text" id="name-<?php bpe_document_id() ?>" name="document[<?php bpe_document_id() ?>][name]" value="<?php bpe_document_name_raw() ?>" />
			
			<label for="desc-<?php bpe_document_id() ?>"><?php _e( 'File Description', 'events' ) ?></label>
			<textarea id="desc-<?php bpe_document_id() ?>" name="document[<?php bpe_document_id() ?>][desc]"><?php bpe_document_description_raw() ?></textarea>                            
		</div>
		<?php
		
		endwhile;
	endif;
	
	echo '<input type="hidden" name="document_ids" value="'. implode( ',', $document_ids ) .'" />';
}

/**
 * Delete old files
 *
 * @package Documents
 * @since 	1.7
 */
function bpe_delete_files_for_event( $event_id )
{
	$docs = bpe_get_docs_for_event( $event_id );
	
	foreach( (array)$docs as $doc )
		@unlink( ABSPATH . $doc->url );
}

/**
 * Delete files by ids
 *
 * @package Documents
 * @since 	1.7
 */
function bpe_delete_files_by_ids( $ids )
{
	$files = bpe_get_file_links( $ids );
	
	foreach( $files as $file )
		@unlink( ABSPATH . $file );
}

/**
 * Add all documents from the parent event
 * 
 * @package Documents
 * @since 	2.0
 */
function bpe_add_recurrent_documents( $old_event_id, $new_event_id )
{
	if( bpe_get_option( 'enable_documents' ) == 4 || empty( $old_event_id ) || empty( $new_event_id ) )
		return false;

	$old_documents = bpe_get_documents( array( 'event_id' => $old_event_id ) );

	foreach( $old_documents as $document )
	{
		$info = pathinfo( $document->url );
		$url = $info['dirname'] .'/'. time() .'-'. sanitize_file_name( str_replace( '.'. $document->type, '', $document->name ) ) .'.'. $info['extension'];

		copy( ABSPATH .'/'. $document->url, ABSPATH .'/'. $url );
		bpe_add_document( null, $new_event_id, $document->name, $document->description, $url, $document->type );
	}
}
add_action( 'bpe_add_to_recurrent_via_component', 'bpe_add_recurrent_documents', 10, 2 );

/**
 * Upload documents
 * 
 * @package	Documents
 * @since 	1.7
 */
function bpe_upload_documents( $files = false, $input = false, $field = 'docs', $folder = 'docs' )
{
	global $bp;
	
	$docs = array();
	$errors = array();
	
	if( ! $files )
		$files = $_FILES;
		
	if( ! $input )
		$input = $_POST;

	if( $files[$field]['error'][0] == 0 )
	{
		$files = $files[$field];
	
		if( is_array( $files ) )
		{
			$upload1 = bpe_upload_path();
			$upload2 = bp_loggedin_user_id() . '/'. $folder .'/';
			
			if( ! is_dir( $upload1 . $upload2 ) )
			{
				if( ! wp_mkdir_p( $upload1 . $upload2 ) )
					$errors[] = __( 'The directory to store your documents could not be created.', 'events' );
			}
					
			foreach( $files['name'] as $k => $v )
			{
				if( $files['error'][$k] == 0 )
				{
					$temp = $files['tmp_name'][$k];
					$old_name = $files['name'][$k];
					$name = sanitize_file_name( $files['name'][$k] );
					
					$parts = pathinfo( $name );
						
					$ext = array( 'pdf', 'doc', 'txt', 'docx', 'xls', 'pps', 'ppt', 'zip' );
					$ext = apply_filters( 'bpe_allowed_doc_extensions', $ext );
					
					if( ! in_array( $parts['extension'], $ext ) )
						$errors[] = sprintf( __( 'The file %s has a forbidden extension.', 'events' ), $name );
					
					if( filesize( $temp ) > 3145728 )
						$errors[] = sprintf( __( 'The file %s is larger than 3 MB.', 'events' ), $name );
						
					$new_name = time() .'-'. $name;
	
					if( ! @move_uploaded_file( $temp, $upload1 . $upload2 . $new_name ) )
						$errors[] = sprintf( __( 'The file %s could not be uploaded.', 'events' ), $name );
					else
					{
						$url = $upload1 . $upload2 . $new_name;
						
						$new_doc = str_replace( ABSPATH, '/', $url );
						$new_doc = str_replace( $_SERVER['DOCUMENT_ROOT'] .'/', '', $new_doc );
						
						$new = $input['doc'][$old_name];
	
						$docs[$new_name]['link'] = $new_doc;
						$docs[$new_name]['name'] = ( empty( $new['name'] ) ) ? $name : $new['name'];
						$docs[$new_name]['desc'] = $new['desc'];
					}
				}
			}
		}
	}
	
	$documents = array( 'docs' => $docs, 'errors' => $errors );
	
	return apply_filters( 'bpe_upload_documents', $documents, $errors );
}

/**
 * Modify the page title
 * 
 * @package Documents
 * @since 	2.1.1
 */
function bpe_documents_adjust_page_title( $title, $sep )
{
	if( ! bpe_is_event_documents() )
		return $title;
	
	$title = stripslashes( bpe_get_displayed_event( 'name' ) ) .' '. $sep .' '. __( 'Documents', 'events' );
	
	return apply_filters( 'bpe_documents_adjust_page_title', $title, $sep );
}
add_filter( 'bpe_adjust_page_title', 'bpe_documents_adjust_page_title', 10, 2 );

/**
 * Get the file extension
 * 
 * @package Documents
 * @since 	1.7
 */
function bpe_file_extension( $link )
{
	$parts = pathinfo( $link );
	return $parts['extension'];
}

/**
 * Delete all data associated with an event
 *
 * @package	 Documents
 * @since 	 2.1.1
 */
function bpe_delete_documents_data( $event )
{
	// delete files
	bpe_delete_files_for_event( bpe_get_event_id( $event ) );

	// delete documents
	bpe_delete_documents_for_event( bpe_get_event_id( $event ) );
}
add_action( 'bpe_delete_event_action', 'bpe_delete_documents_data' );
?>