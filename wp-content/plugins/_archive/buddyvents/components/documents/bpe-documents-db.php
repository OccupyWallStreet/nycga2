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
 * Add a document
 *
 * @package	 Documents
 * @since 	 1.7
 */
function bpe_add_document( $id = null, $event_id, $name, $description, $url, $type )
{
	$document = new Buddyvents_Documents( $id );
	
	$document->event_id 	= $event_id;
	$document->name 		= $name;
	$document->description 	= $description;
	$document->url 			= $url;
	$document->type 		= $type;

	if( $new_id = $document->save() )
		return $new_id;
		
	return false;
}

/**
 * Loop function: get all documents
 *
 * @package	 Documents
 * @since 	 1.7
 */
function bpe_get_documents( $args = '' )
{
	global $bp;
	
	$defaults = array(
		'event_id' 		=> false,
		'name' 			=> false,
		'type' 			=> false,
		'per_page' 		=> 20,
		'page' 			=> 1,
		'search_terms' 	=> false
	);	

	$params = wp_parse_args( $args, $defaults );
	extract( $params, EXTR_SKIP );

	$documents = Buddyvents_Documents::get( (int)$event_id, $name, $type, (int)$page, (int)$per_page, $search_terms );

	return apply_filters( 'bpe_get_documents', $documents, &$params );
}
/**
 * Check if an event has a document
 *
 * @package	 Documents
 * @since 	 1.7
 */
function bpe_has_event_document( $id )
{
	return Buddyvents_Documents::has_event_document( $id );
}

/**
 * Check if an event has a document
 *
 * @package	 Documents
 * @since 	 1.7
 */
function bpe_delete_documents_for_event( $id )
{
	return Buddyvents_Documents::delete_documents_for_event( $id );
}

/**
 * Check all for an event
 *
 * @package	 Documents
 * @since 	 1.7
 */
function bpe_get_docs_for_event( $event_id )
{
	return Buddyvents_Documents::get_docs_for_event( $event_id );
}

/**
 * Check if an event has a document
 *
 * @package	 Documents
 * @since 	 1.7
 */
function bpe_delete_documents_by_ids( $ids )
{
	return Buddyvents_Documents::delete_documents_by_ids( $ids );
}

/**
 * Get file links by ids
 *
 * @package	 Documents
 * @since 	 1.7
 */
function bpe_get_file_links( $ids )
{
	return Buddyvents_Documents::get_file_links( $ids );
}
?>