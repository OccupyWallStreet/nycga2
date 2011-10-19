<?php
/**
 * Controller for the event views in BP (using mvc terms here)
 */
function bp_em_my_events() {
	global $bp, $EM_Event;
	if( !is_object($EM_Event) && !empty($_REQUEST['event_id']) ){
		$EM_Event = new EM_Event($_REQUEST['event_id']);
	}
	
	do_action( 'bp_em_my_events' );
	
	//plug into EM admin code (at least for now)
	include_once(EM_DIR.'/admin/em-admin.php');
	em_admin_load_scripts();
	add_action('wp_head','em_admin_general_script');
	
	$template_title = 'bp_em_my_events_title';
	$template_content = 'bp_em_my_events_content';

	if( count($bp->action_variables) > 0 ){
		if( !empty($bp->action_variables[0]) ){
			switch($bp->action_variables[0]){
				case 'edit':
					$template_title = 'bp_em_my_events_editor_title';
					$template_content = 'bp_em_my_events_editor';
					break;
			}
		}
	}

	add_action( 'bp_template_title', $template_title );
	add_action( 'bp_template_content', $template_content );
	
	/* Finally load the plugin template file. */
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function bp_em_my_events_title() {
	_e( 'My Events', 'dbem' );
}

/**
 * Determines whether to show event page or events page, and saves any updates to the event or events
 * @return null
 */
function bp_em_my_events_content() {
	em_locate_template('buddypress/my-events.php', true);
}

function bp_em_my_events_editor_title() {
	global $EM_Event;
	if( is_object($EM_Event) ){
		_e( 'Edit Event', 'dbem' );	
	}else{
		_e( 'Add Event', 'dbem' );
	}
}

function bp_em_my_events_editor(){
	em_locate_template('forms/event-editor.php', true);
}

?>