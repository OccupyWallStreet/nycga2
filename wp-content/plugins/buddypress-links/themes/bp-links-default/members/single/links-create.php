<?php
	do_action( 'bp_before_member_body' );
	do_action( 'bp_before_profile_link_creation_content' );
	bp_links_locate_template( array( 'single/forms/details.php' ), true );
	do_action( 'bp_after_profile_link_creation_content' );
	do_action( 'bp_after_member_body' );
?>
