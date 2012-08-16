<?php
// 4/26/2011 4:05:56 PM
// Check for and update the following postmeta value names:
// cs-expire-date > _cs-expire-date
// cs-enable-schedule > _cs-enable-schedule
// Can we just do a MySQL search and replace?
/*
update TABLE_NAME set FIELD_NAME = replace(FIELD_NAME, ‘find this string’, ‘replace found string with this string’);
update client_table set company_name = replace(company_name, ‘Old Company’, ‘New Company’)
*/
global $wpdb;

$wpdb->update( 
			$wpdb->postmeta, 
			array( 'meta_key' => '_cs-expire-date' ),
			array( 'meta_key' => 'cs-expire-date' ),
			'%s', 
			'%s'
			);
$wpdb->update( 
			$wpdb->postmeta, 
			array( 'meta_key' => '_cs-enable-schedule' ),
			array( 'meta_key' => 'cs-enable-schedule' ),
			'%s', 
			'%s'
			);
?>
