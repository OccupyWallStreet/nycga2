<?php 

// ALL HOOK DEFINITIONS	
	
	// header.php
	function bizz_head_before() { do_action( 'bizz_head_before' ); }	
	function bizz_head_after() { do_action( 'bizz_head_after' ); }
	function bizz_body_tag() { do_action( 'bizz_body_tag' ); }
	function bizz_body_after() { do_action( 'bizz_body_after' ); }
	
	// footer.php
	function bizz_foot_before() { do_action( 'bizz_foot_before' ); }	
	function bizz_foot_after() { do_action( 'bizz_foot_after' ); }

	
?>