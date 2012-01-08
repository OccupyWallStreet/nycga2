<?php

/**
 * bp_group_documents_record_add()
 *
 * records the creation of a new document: [user] uploaded the file [name] to [group]
 */
function bp_group_documents_record_add( $document ) {
	global $bp;

	$params = array('action'=>sprintf( __ ('%s uploaded the file: %s to %s','bp-group-documents'),bp_core_get_userlink($bp->loggedin_user->id),'<a href="' . $document->get_url() . '">' . esc_attr( $document->name ) . '</a>','<a href="' . bp_get_group_permalink( $bp->groups->current_group ) . '">' . esc_attr( $bp->groups->current_group->name ) . '</a>'),
					'content'=>$document->description,
					'component_action'=>'added_group_document',
					'secondary_item_id'=>$document->id);

	bp_group_documents_record_activity($params);

	do_action('bp_group_documents_record_add',$document);
}
add_action('bp_group_documents_add_success','bp_group_documents_record_add',15,1);


/**
 * bp_group_documents_record_edit()
 * 
 * records the modification of a document: "[user] edited the file [name] in [group]"
 */
function bp_group_documents_record_edit( $document ) {
	global $bp;

	$params = array('action'=>sprintf( __ ('%s edited the file: %s in %s','bp-group-documents'),bp_core_get_userlink($bp->loggedin_user->id),'<a href="' . $document->get_url() . '">' . esc_attr( $document->name ) . '</a>','<a href="' . bp_get_group_permalink( $bp->groups->current_group ) . '">' . esc_attr( $bp->groups->current_group->name ) . '</a>'),
					'component_action'=>'edited_group_document',
					'secondary_item_id'=>$document->id);
	bp_group_documents_record_activity($params);
	do_action('bp_group_documents_record_edit',$document);
}
add_action('bp_group_documents_edit_success','bp_group_documents_record_edit',15,1);


/*
 * bp_group_documents_record_delete()
 *
 * records the deletion of a document: "[user] deleted the file [name] from [group]"
 */
function bp_group_documents_record_delete( $document ) {
	global $bp;

	$params = array('action'=>sprintf( __ ('%s deleted the file: %s from %s','bp-group-documents'),bp_core_get_userlink($bp->loggedin_user->id),$document->name,'<a href="' . bp_get_group_permalink( $bp->groups->current_group ) . '">' . esc_attr( $bp->groups->current_group->name ) . '</a>'),
					'component_action'=>'deleted_group_document',
					'secondary_item_id'=>$document->id);
	bp_group_documents_record_activity($params);
	do_action('bp_group_documents_record_delete',$document);
}
add_action('bp_group_documents_delete_success','bp_group_documents_record_delete',15,1);


/**
 * bp_group_documents_record_activity()
 *
 * If the activity stream component is installed, this function will record upload
 * and edit activity items.
 */
function bp_group_documents_record_activity( $args = '' ) {
	global $bp;
	
	if ( !function_exists( 'bp_activity_add' ) )
		return false;

	
	$defaults = array(
		'primary_link' => bp_get_group_permalink( $bp->groups->current_group ),
		'component_name' => 'groups',
		'component_action' => false,
		
		'hide_sitewide' => false, // Optional
		'user_id' => $bp->loggedin_user->id, // Optional		
		'item_id' => $bp->groups->current_group->id, // Optional
		'secondary_item_id' => false, // Optional
        'content' => ''
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );	

	// If the group is not public, don't broadcast updates.
	if ( 'public' != $bp->groups->current_group->status )	
		$hide_sitewide = 1;
	
	return bp_activity_add( array( 'content' => $content, 'primary_link' => $primary_link, 'component_name' => $component_name, 'component_action' => $component_action, 'user_id' => $user_id, 'item_id' => $item_id, 'secondary_item_id' => $secondary_item_id, 'hide_sitewide' => $hide_sitewide, 'action'=>$action ) );
}

/**
 * bp_group_documents_delete_activity_by_document()
 *
 * Deletes all previous activity for the document passed
 */
function bp_group_documents_delete_activity_by_document( $document ) {

	$params = array( 'item_id' => $document->group_id,
					'secondary_item_id' => $document->id );

	bp_group_documents_delete_activity( $params );
	do_action('bp_group_documents_delete_activity_by_document',$document);
}
add_action('bp_group_documents_delete_success','bp_group_documents_delete_activity_by_document',14,1);
add_action('bp_group_documents_delete_with_group','bp_group_documents_delete_activity_by_document');


/**
 * bp_group_documents_delete_activity()
 *
 * Deletes a previously recorded activity - useful for making sure there are no broken links
 * if soemthing is deleted.
 */
function bp_group_documents_delete_activity( $args = true ) {
	global $bp;
	
	if ( function_exists('bp_activity_delete_by_item_id') ) {
		$defaults = array(
			'item_id' => false,
			'component_name' => 'groups',
			'component_action' => false,
			'user_id' => false,
			'secondary_item_id' => false
		);

		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_SKIP );			

		bp_activity_delete_by_item_id( array( 
			'item_id' => $item_id, 
			'component_name' => $component_name,
			
			'component_action' => $component_action, // optional
			'user_id' => $user_id, // optional
			'secondary_item_id' => $secondary_item_id // optional
		) );
	}
}

