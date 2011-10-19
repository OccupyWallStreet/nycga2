<?php
/**
 * Group Organizer Template class - based on Navigation Menu template functions
 */

class Walker_Group extends Walker {

	var $db_fields = array( 'parent' => 'parent_id', 'id' => 'id' );

}

function walk_group_tree( $items, $depth, $r ) {
	$walker = ( empty($r->walker) ) ? new Walker_Group : $r->walker;
	$args = array( $items, $depth, $r );

	return call_user_func_array( array(&$walker, 'walk'), $args );
}
