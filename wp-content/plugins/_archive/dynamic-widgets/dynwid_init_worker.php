<?php
/**
 * dynwid_init_worker.php
 *
 * @version $Id: dynwid_init_worker.php 488903 2012-01-12 18:17:27Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	$DW->message('Dynamic Widgets INIT');
	echo "\n" . '<!-- Dynamic Widgets v' . DW_VERSION . ' by QURL - http://www.qurl.nl //-->' . "\n";

	// Register the overrule maintypes
	$DW->registerOverrulers();

	// UserAgent detection
	$DW->message('UserAgent: ' . $DW->useragent);

	$DW->message('User has role(s): ' . implode(', ', $DW->userrole));

	$DW->whereami = $DW->detectPage();
	$DW->message('Page is ' . $DW->whereami);

	if ( $DW->whereami == 'single' ) {
		$post = $GLOBALS['post'];
		$DW->message('post_id = ' . $post->ID);

		$post_type = get_post_type($post);
		$DW->message('Post Type = ' . $post_type);
		if ( $post_type != 'post' ) {
			$DW->custom_post_type = TRUE;
			$DW->whereami = $post_type;
			$DW->message('Custom Post Type detected, page changed to ' . $DW->whereami);
		}
	}

	if ( $DW->whereami == 'page' ) {
		// WPSC/WPEC Plugin Support
		include_once(DW_MODULES . 'wpec_module.php');
		include_once(DW_MODULES . 'bp_module.php');
		if ( DW_WPSC::detect(FALSE) ) {
			DW_WPSC::detectCategory();
		} else if ( DW_BP::detect(FALSE) ) {	// BuddyPress Plugin Support -- else if needed, otherwise WPEC pages are detected as BP
			DW_BP::detectComponent();
		}
	}

	if ( $DW->whereami == 'tax_archive' ) {
		$wp_query =  $GLOBALS['wp_query'];
		$taxonomy = $wp_query->get('taxonomy');

		$DW->custom_taxonomy = TRUE;
		$DW->whereami = 'tax_' . $taxonomy;
		$DW->message('Page changed to tax_'. $taxonomy. ' (term: ' . $wp_query->get_queried_object_id() . ')');
	}

	$DW->dwList($DW->whereami);
?>