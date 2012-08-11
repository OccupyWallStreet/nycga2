<?php
/* WARNING! This file may change in the near future as we intend to add features to BuddyPress - 2012-02-14 */
	global $wpdb, $current_user, $EM_Notices;
	include_once(EM_DIR.'/admin/em-bookings.php');
	include_once(EM_DIR.'/admin/em-admin.php');
	include_once(EM_DIR.'/admin/bookings/em-cancelled.php');
	include_once(EM_DIR.'/admin/bookings/em-confirmed.php');
	include_once(EM_DIR.'/admin/bookings/em-events.php');
	include_once(EM_DIR.'/admin/bookings/em-pending.php');
	include_once(EM_DIR.'/admin/bookings/em-person.php');
	include_once(EM_DIR.'/admin/bookings/em-rejected.php');
	em_bookings_page();
?>