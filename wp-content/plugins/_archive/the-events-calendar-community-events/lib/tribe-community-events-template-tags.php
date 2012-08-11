<?php
/**
 * Tests if the current page is the My Events page
 *
 * @since 1.0.1
 * @author PaulHughes01
 * @return bool whether it is the My Events page.
 */
function tribe_is_community_my_events_page() {
	$tce = TribeCommunityEvents::instance();
	
	return $tce->isMyEvents;
}

/**
 * Tests if the current page is the Edit Event page
 *
 * @since 1.0.1
 * @author PaulHughes01
 * @return bool whether it is the Edit Event page.
 */
function tribe_is_community_edit_event_page() {
	$tce = TribeCommunityEvents::instance();
	
	return $tce->isEditPage;
}