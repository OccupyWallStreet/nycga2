<?php

/**
 * bbPress Updater
 *
 * @package bbPress
 * @subpackage Updater
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * If there is no raw DB version, this is the first installation
 *
 * @since bbPress (r3764)
 *
 * @uses get_option()
 * @uses bbp_get_db_version() To get bbPress's database version
 * @return bool True if update, False if not
 */
function bbp_is_install() {
	return ! bbp_get_db_version_raw();
}

/**
 * Compare the bbPress version to the DB version to determine if updating
 *
 * @since bbPress (r3421)
 *
 * @uses get_option()
 * @uses bbp_get_db_version() To get bbPress's database version
 * @return bool True if update, False if not
 */
function bbp_is_update() {
	return (bool) ( (int) bbp_get_db_version_raw() < (int) bbp_get_db_version() );
}

/**
 * Determine if bbPress is being activated
 *
 * Note that this function currently is not used in bbPress core and is here
 * for third party plugins to use to check for bbPress activation.
 *
 * @since bbPress (r3421)
 *
 * @return bool True if activating bbPress, false if not
 */
function bbp_is_activation( $basename = '' ) {
	$bbp = bbpress();

	$action = false;
	if ( ! empty( $_REQUEST['action'] ) && ( '-1' != $_REQUEST['action'] ) )
		$action = $_REQUEST['action'];
	elseif ( ! empty( $_REQUEST['action2'] ) && ( '-1' != $_REQUEST['action2'] ) )
		$action = $_REQUEST['action2'];

	// Bail if not activating
	if ( empty( $action ) || !in_array( $action, array( 'activate', 'activate-selected' ) ) )
		return false;

	// The plugin(s) being activated
	if ( $action == 'activate' )
		$plugins = isset( $_GET['plugin'] ) ? array( $_GET['plugin'] ) : array();
	else
		$plugins = isset( $_POST['checked'] ) ? (array) $_POST['checked'] : array();

	// Set basename if empty
	if ( empty( $basename ) && !empty( $bbp->basename ) )
		$basename = $bbp->basename;

	// Bail if no basename
	if ( empty( $basename ) )
		return false;

	// Is bbPress being deactivated?
	return in_array( $basename, $plugins );
}

/**
 * Determine if bbPress is being deactivated
 *
 * @since bbPress (r3421)
 * @return bool True if deactivating bbPress, false if not
 */
function bbp_is_deactivation( $basename = '' ) {
	$bbp = bbpress();

	$action = false;
	if ( ! empty( $_REQUEST['action'] ) && ( '-1' != $_REQUEST['action'] ) )
		$action = $_REQUEST['action'];
	elseif ( ! empty( $_REQUEST['action2'] ) && ( '-1' != $_REQUEST['action2'] ) )
		$action = $_REQUEST['action2'];

	// Bail if not deactivating
	if ( empty( $action ) || !in_array( $action, array( 'deactivate', 'deactivate-selected' ) ) )
		return false;

	// The plugin(s) being deactivated
	if ( $action == 'deactivate' )
		$plugins = isset( $_GET['plugin'] ) ? array( $_GET['plugin'] ) : array();
	else
		$plugins = isset( $_POST['checked'] ) ? (array) $_POST['checked'] : array();

	// Set basename if empty
	if ( empty( $basename ) && !empty( $bbp->basename ) )
		$basename = $bbp->basename;

	// Bail if no basename
	if ( empty( $basename ) )
		return false;

	// Is bbPress being deactivated?
	return in_array( $basename, $plugins );
}

/**
 * Update the DB to the latest version
 *
 * @since bbPress (r3421)
 * @uses update_option()
 * @uses bbp_get_db_version() To get bbPress's database version
 */
function bbp_version_bump() {
	$db_version = bbp_get_db_version();
	update_option( '_bbp_db_version', $db_version );
}

/**
 * Setup the bbPress updater
 *
 * @since bbPress (r3419)
 *
 * @uses bbp_version_bump()
 * @uses bbp_deactivation()
 * @uses bbp_activation()
 */
function bbp_setup_updater() {

	// Are we running an outdated version of bbPress?
	if ( bbp_is_update() ) {

		// Call the automated updater
		bbp_version_updater();

		// Bump the version
		bbp_version_bump();

		// Run the deactivation function to wipe roles, caps, and rewrite rules
		bbp_deactivation();

		// Run the activation function to reset roles, caps, and rewrite rules
		bbp_activation();
	}
}

/**
 * Create a default forum, topic, and reply
 *
 * @since bbPress (r3767)
 * @param array $args Array of arguments to override default values
 */
function bbp_create_initial_content( $args = array() ) {

	$defaults = array(
		'forum_parent'  => 0,
		'forum_status'  => 'publish',
		'forum_title'   => __( 'General',                                  'bbpress' ),
		'forum_content' => __( 'General chit-chat',                        'bbpress' ),
		'topic_title'   => __( 'Hello World!',                             'bbpress' ),
		'topic_content' => __( 'I am the first topic in your new forums.', 'bbpress' ),
		'reply_title'   => __( 'Re: Hello World!',                         'bbpress' ),
		'reply_content' => __( 'Oh, and this is what a reply looks like.', 'bbpress' ),
	);
	$r = bbp_parse_args( $args, $defaults, 'create_initial_content' );
	extract( $r );

	// Create the initial forum
	$forum_id = bbp_insert_forum( array(
		'post_parent'  => $forum_parent,
		'post_status'  => $forum_status,
		'post_title'   => $forum_title,
		'post_content' => $forum_content
	) );

	// Create the initial topic
	$topic_id = bbp_insert_topic(
		array(
			'post_parent'  => $forum_id,
			'post_title'   => $topic_title,
			'post_content' => $topic_content
		),
		array( 'forum_id'  => $forum_id )
	);

	// Create the initial reply
	$reply_id = bbp_insert_reply(
		array(
			'post_parent'  => $topic_id,
			'post_title'   => $reply_title,
			'post_content' => $reply_content
		),
		array(
			'forum_id'     => $forum_id,
			'topic_id'     => $topic_id
		)
	);

	return array(
		'forum_id' => $forum_id,
		'topic_id' => $topic_id,
		'reply_id' => $reply_id
	);
}

/**
 * bbPress's version updater looks at what the current database version is, and
 * runs whatever other code is needed.
 *
 * This is most-often used when the data schema changes, but should also be used
 * to correct issues with bbPress meta-data silently on software update.
 *
 * @since bbPress (r4104)
 */
function bbp_version_updater() {

	// Get the raw database version
	$raw_db_version = (int) bbp_get_db_version_raw();

	// Bail if no database version exists
	if ( empty( $raw_db_version ) )
		return;

	/** 2.0 Branch ************************************************************/

	// 2.0, 2.0.1, 2.0.2, 2.0.3
	if ( $raw_db_version < 200 ) {
		// Do nothing
	}

	/** 2.1 Branch ************************************************************/

	// 2.1, 2.1.1
	if ( $raw_db_version < 211 ) {

		/**
		 * Repair private and hidden forum data
		 *
		 * @link http://bbpress.trac.wordpress.org/ticket/1891
		 */
		bbp_admin_repair_forum_visibility();
	}

	/** 2.2 Branch ************************************************************/

	// 2.2
	if ( $raw_db_version < 220 ) {
		// No changes yet
	}
}
