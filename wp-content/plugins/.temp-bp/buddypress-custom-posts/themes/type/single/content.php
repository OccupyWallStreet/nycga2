<?php
	global $bp;
	$type = $bp->active_components[ $bp->current_component ];
	do_action( 'bpcp_single_before_home', $type );
	do_action( 'bpcp_' . $type . '_single_before_home' );

	the_content();

	do_action( 'bpcp_single_after_home', $type );
	do_action( 'bpcp_' . $type . '_single_after_home' );
