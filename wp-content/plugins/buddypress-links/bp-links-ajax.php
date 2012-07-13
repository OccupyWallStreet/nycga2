<?php
/***
 * Global AJAX Helper Functions and Actions
 */

/**
 * Helper function for echoing AJAX responses
 */
function bp_links_ajax_response_string() {
	$args = func_get_args();
	echo join( '[[split]]', $args );
	die();
}

/**
 * Handle voting on a link
 */
function bp_links_ajax_link_vote() {
	global $bp;

	if ( ( $bp->loggedin_user->id ) && ( check_ajax_referer( 'link_vote', false, false ) ) ) {

		$link = bp_links_cast_vote( $_REQUEST['link_id'], substr( $_REQUEST['up_or_down'], 0, 4 ) );

		if ( !empty( $link ) ) {
			if ( $link instanceof BP_Links_Link ) {
				bp_links_ajax_response_string( 1, __( 'Vote recorded.', 'buddypress-links' ), sprintf( '%1$+d', $link->vote_total), $link->vote_count );
			} else {
				bp_links_ajax_response_string( 0, __( 'You have already voted.', 'buddypress-links' ) );
			}
		} else {
			bp_links_ajax_response_string( -1, __( 'There was a problem recording your vote. Please try again.', 'buddypress-links' ) );
		}

	} else {
		// sorry, not logged in
		bp_links_ajax_response_string( -1, __( 'You must be logged in to vote!', 'buddypress-links' ) );
	}
}
add_action( 'wp_ajax_link_vote', 'bp_links_ajax_link_vote' );

/**
 * Display auto-embed panel on the create/admin form
 */
function bp_links_ajax_link_auto_embed_url() {

	check_ajax_referer( 'bp_links_save_link-auto-embed' );

	try {
		// try to load a service
		$embed_service = BP_Links_Embed::FromUrl( $_POST['url'] );

		// did we get a rich media service?
		if ( $embed_service instanceof BP_Links_Embed_From_Url ) {
			// output response
			bp_links_ajax_response_string(
				1, // 0
				$embed_service->title(), // 1
				$embed_service->description(), // 2
				bp_get_links_auto_embed_panel_content( $embed_service ) // 3
			);
		}

		// NOT rich media, fall back to page parser
		$page_parser = BP_Links_Embed_Page_Parser::GetInstance();

		if ( $page_parser->from_url( $_POST['url'] ) ) {

			$page_title = $page_parser->title();
			$page_desc = $page_parser->description();

			if ( !empty( $page_title ) || !empty( $page_desc ) ) {
				// output response
				bp_links_ajax_response_string( 2, $page_title, $page_desc );
			}
		}

	} catch ( BP_Links_Embed_User_Exception $e ) {
		bp_links_ajax_response_string( -1, esc_html( $e->getMessage() ) );
	} catch ( Exception $e ) {
		// fall through to generic error for all other exceptions
		// TODO comment out this debug line before tagging a version
//		bp_links_ajax_response_string( -1, esc_html( $e->getMessage() ) );
	}

	// if all else fails, just spit out generic warning message
	bp_links_ajax_response_string( -2, __( 'Auto-fill not available for this URL.', 'buddypress-links' ) );
}
add_action( 'wp_ajax_link_auto_embed_url', 'bp_links_ajax_link_auto_embed_url' );

/**
 * Return lightbox content for a link
 */
function bp_links_ajax_link_lightbox() {
	global $bp;

	if ( !empty( $_POST['link_id'] ) && is_numeric( $_POST['link_id'] ) ) {

		$link = new BP_Links_Link( (int) $_POST['link_id'] );

		if ( $link instanceof BP_Links_Link && $link->embed() instanceof BP_Links_Embed_Has_Html ) {
			bp_links_ajax_response_string( 1, $link->embed()->html() );
		}
	}

	bp_links_ajax_response_string( -1, __( 'Invalid request', 'buddypress-links' ) );
}
add_filter( 'wp_ajax_link_lightbox', 'bp_links_ajax_link_lightbox' );

?>
