<?php
/**
 * Any functions related to upgrading from previous version go in this file
 */

/**
 * Perform upgrades if necessary
 *
 * @param integer $db_version
 * @return boolean
 */
function bp_links_upgrade( $db_version ) {
	if ( is_numeric( $db_version ) )
		return bp_links_upgrade_04( $db_version );
	else
		return false;
}

/**
 * Perform upgrades for version 0.4
 *
 * @param integer $db_version
 * @return boolean
 */
function bp_links_upgrade_04( $db_version ) {
	global $bp, $wpdb;

	// if DB version is 7 or higher, skip this upgrade
	if ( $db_version >= 7 )
		return true;

	// populate the new cloud_id column in the links table
	// we are trying to produce a PERMANENT unique hash, it doesn't need to be reproducable
	$sql_cloud = $wpdb->prepare( "UPDATE {$bp->links->table_name} SET cloud_id = MD5(CONCAT(%s,id,url,name))", $bp->root_domain );
	if ( false === $wpdb->query($sql_cloud) )
		return false;

	// update the activity table item_id column replacing the link_id with the cloud_id
	$sql_activity = $wpdb->prepare( "UPDATE {$bp->links->table_name} AS l, {$bp->activity->table_name} AS a SET a.item_id = l.cloud_id WHERE l.id = a.item_id AND a.component = %s", $bp->links->id );
	if ( false === $wpdb->query($sql_activity) )
		return false;

	// success!
	return true;
}

?>
