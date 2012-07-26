<?php
/**
 * Display links to active extensions specific settings' pages: CollabPress.
 *
 * @package    BuddyPress Toolbar
 * @subpackage Plugin/Extension Support
 * @author     David Decker - DECKERWEB
 * @copyright  Copyright 2012, David Decker - DECKERWEB
 * @license    http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link       http://genesisthemes.de/en/wp-plugins/buddypress-toolbar/
 * @link       http://twitter.com/#!/deckerweb
 *
 * @since 1.2
 */

/**
 * Support for: CollabPress (free, by WebDevStudios)
 *
 * @since 1.2
 */
	/** Get CollabPress options setting */
	$cp_options = get_option( 'cp_options' );

	/** Entries at "Extensions" level submenu */
	/** CollabPress Dashboard */
	$menu_items['extcollabpress'] = array(
		'parent' => $extensions,
		'title'  => __( 'CollabPress Dashboard', 'buddypress-toolbar' ),
		'href'   => admin_url( 'admin.php?page=collabpress-dashboard' ),
		'meta'   => array( 'target' => '', 'title' => __( 'CollabPress Dashboard', 'buddypress-toolbar' ) )
	);

	/** CollabPress Post Types */
	if ( $cp_options['debug_mode'] == 'enabled' ) {
		/** Projects */
		$menu_items['extcollabpress-projects'] = array(
			'parent' => $extcollabpress,
			'title'  => __( 'Projects', 'buddypress-toolbar' ),
			'href'   => admin_url( 'edit.php?post_type=cp-projects' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Projects', 'buddypress-toolbar' ) )
		);
		$menu_items['extcollabpress-projects-add'] = array(
			'parent' => $extcollabpress,
			'title'  => __( 'Add new Project', 'buddypress-toolbar' ),
			'href'   => admin_url( 'post-new.php?post_type=cp-projects' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Add new Project', 'buddypress-toolbar' ) )
		);
		$menu_items['extcollabpress-projects-bpgroups'] = array(
			'parent' => $extcollabpress,
			'title'  => __( 'Projects: BP Groups', 'buddypress-toolbar' ),
			'href'   => admin_url( 'edit-tags.php?taxonomy=cp-bp-group&post_type=cp-projects' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Projects: BP Groups', 'buddypress-toolbar' ) )
		);
		/** Task Lists */
		$menu_items['extcollabpress-tasklists'] = array(
			'parent' => $extcollabpress,
			'title'  => __( 'Task Lists', 'buddypress-toolbar' ),
			'href'   => admin_url( 'edit.php?post_type=cp-task-lists' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Task Lists', 'buddypress-toolbar' ) )
		);
		$menu_items['extcollabpress-tasklists-add'] = array(
			'parent' => $extcollabpress,
			'title'  => __( 'Add new Task List', 'buddypress-toolbar' ),
			'href'   => admin_url( 'post-new.php?post_type=cp-task-lists' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Add new Task List', 'buddypress-toolbar' ) )
		);
		/** Tasks */
		$menu_items['extcollabpress-tasks'] = array(
			'parent' => $extcollabpress,
			'title'  => __( 'Tasks', 'buddypress-toolbar' ),
			'href'   => admin_url( 'edit.php?post_type=cp-tasks' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Tasks', 'buddypress-toolbar' ) )
		);
		$menu_items['extcollabpress-tasks-add'] = array(
			'parent' => $extcollabpress,
			'title'  => __( 'Add new Task', 'buddypress-toolbar' ),
			'href'   => admin_url( 'post-new.php?post_type=cp-tasks' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Add new Task', 'buddypress-toolbar' ) )
		);
		/** Meta Data */
		$menu_items['extcollabpress-metadata'] = array(
			'parent' => $extcollabpress,
			'title'  => __( 'Meta Data', 'buddypress-toolbar' ),
			'href'   => admin_url( 'edit.php?post_type=cp-meta-data' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Meta Data', 'buddypress-toolbar' ) )
		);
		$menu_items['extcollabpress-metadata-add'] = array(
			'parent' => $extcollabpress,
			'title'  => __( 'Add new Meta Data', 'buddypress-toolbar' ),
			'href'   => admin_url( 'post-new.php?post_type=cp-meta-data' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Add new Meta Data', 'buddypress-toolbar' ) )
		);
	}  // end-if cap check

	/** CollabPress Settings */
	if ( current_user_can( $cp_settings_user_role ) ) {
		/** Settings */
		$menu_items['extcollabpress-settings'] = array(
			'parent' => $extcollabpress,
			'title'  => __( 'Settings', 'buddypress-toolbar' ),
			'href'   => admin_url( 'admin.php?page=collabpress-settings' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Settings', 'buddypress-toolbar' ) )
		);
		/** Help */
		$menu_items['extcollabpress-help'] = array(
			'parent' => $extcollabpress,
			'title'  => __( 'Help', 'buddypress-toolbar' ),
			'href'   => admin_url( 'admin.php?page=collabpress-help' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Help', 'buddypress-toolbar' ) )
		);
	}  // end-if cap check

	/** CollabPress Debug Mode */
	if ( $cp_options['debug_mode'] == 'enabled' ) {
		$menu_items['extcollabpress-debug'] = array(
			'parent' => $extcollabpress,
			'title'  => __( 'Debug', 'buddypress-toolbar' ),
			'href'   => admin_url( 'admin.php?page=collabpress-debug' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Debug', 'buddypress-toolbar' ) )
		);
	}  // end-if cap check

	/** Entries at "Manage Groups" level submenu */

	/** Activate display */
	$bptb_is_bpmg = 'bpmg_yes';

	/** CollabPress Dashboard */
	$menu_items['bpmgcollabpress'] = array(
		'parent' => $managegroups,
		'title'  => __( 'CollabPress Dashboard', 'buddypress-toolbar' ),
		'href'   => admin_url( 'admin.php?page=collabpress-dashboard' ),
		'meta'   => array( 'target' => '', 'title' => __( 'CollabPress Dashboard', 'buddypress-toolbar' ) )
	);

	/** CollabPress Post Types */
	if ( $cp_options['debug_mode'] == 'enabled' ) {
		/** Projects */
		$menu_items['bpmgcollabpress-projects'] = array(
			'parent' => $bpmgcollabpress,
			'title'  => __( 'Projects', 'buddypress-toolbar' ),
			'href'   => admin_url( 'edit.php?post_type=cp-projects' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Projects', 'buddypress-toolbar' ) )
		);
		$menu_items['bpmgcollabpress-projects-add'] = array(
			'parent' => $bpmgcollabpress,
			'title'  => __( 'Add new Project', 'buddypress-toolbar' ),
			'href'   => admin_url( 'post-new.php?post_type=cp-projects' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Add new Project', 'buddypress-toolbar' ) )
		);
		$menu_items['bpmgcollabpress-projects-bpgroups'] = array(
			'parent' => $bpmgcollabpress,
			'title'  => __( 'Projects: BP Groups', 'buddypress-toolbar' ),
			'href'   => admin_url( 'edit-tags.php?taxonomy=cp-bp-group&post_type=cp-projects' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Projects: BP Groups', 'buddypress-toolbar' ) )
		);
		/** Task Lists */
		$menu_items['bpmgcollabpress-tasklists'] = array(
			'parent' => $bpmgcollabpress,
			'title'  => __( 'Task Lists', 'buddypress-toolbar' ),
			'href'   => admin_url( 'edit.php?post_type=cp-task-lists' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Task Lists', 'buddypress-toolbar' ) )
		);
		$menu_items['bpmgcollabpress-tasklists-add'] = array(
			'parent' => $bpmgcollabpress,
			'title'  => __( 'Add new Task List', 'buddypress-toolbar' ),
			'href'   => admin_url( 'post-new.php?post_type=cp-task-lists' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Add new Task List', 'buddypress-toolbar' ) )
		);
		/** Tasks */
		$menu_items['bpmgcollabpress-tasks'] = array(
			'parent' => $bpmgcollabpress,
			'title'  => __( 'Tasks', 'buddypress-toolbar' ),
			'href'   => admin_url( 'edit.php?post_type=cp-tasks' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Tasks', 'buddypress-toolbar' ) )
		);
		$menu_items['bpmgcollabpress-tasks-add'] = array(
			'parent' => $bpmgcollabpress,
			'title'  => __( 'Add new Task', 'buddypress-toolbar' ),
			'href'   => admin_url( 'post-new.php?post_type=cp-tasks' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Add new Task', 'buddypress-toolbar' ) )
		);
		/** Meta Data */
		$menu_items['bpmgcollabpress-metadata'] = array(
			'parent' => $bpmgcollabpress,
			'title'  => __( 'Meta Data', 'buddypress-toolbar' ),
			'href'   => admin_url( 'edit.php?post_type=cp-meta-data' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Meta Data', 'buddypress-toolbar' ) )
		);
		$menu_items['bpmgcollabpress-metadata-add'] = array(
			'parent' => $bpmgcollabpress,
			'title'  => __( 'Add new Meta Data', 'buddypress-toolbar' ),
			'href'   => admin_url( 'post-new.php?post_type=cp-meta-data' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Add new Meta Data', 'buddypress-toolbar' ) )
		);
	}  // end-if cap check

	/** CollabPress Settings */
	if ( current_user_can( $cp_settings_user_role ) ) {
		/** Settings */
		$menu_items['bpmgcollabpress-settings'] = array(
			'parent' => $bpmgcollabpress,
			'title'  => __( 'Settings', 'buddypress-toolbar' ),
			'href'   => admin_url( 'admin.php?page=collabpress-settings' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Settings', 'buddypress-toolbar' ) )
		);
		/** Help */
		$menu_items['bpmgcollabpress-help'] = array(
			'parent' => $bpmgcollabpress,
			'title'  => __( 'Help', 'buddypress-toolbar' ),
			'href'   => admin_url( 'admin.php?page=collabpress-help' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Help', 'buddypress-toolbar' ) )
		);
	}  // end-if cap check

	/** CollabPress Debug Mode */
	if ( $cp_options['debug_mode'] == 'enabled' ) {
		$menu_items['bpmgcollabpress-debug'] = array(
			'parent' => $bpmgcollabpress,
			'title'  => __( 'Debug', 'buddypress-toolbar' ),
			'href'   => admin_url( 'admin.php?page=collabpress-debug' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Debug', 'buddypress-toolbar' ) )
		);
	}  // end-if cap check
