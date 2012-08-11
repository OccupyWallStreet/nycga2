<?php
/**
 * Controller for the location views in BP (using mvc terms here)
 */
function bp_em_my_locations() {
	global $bp, $EM_Location;
	if( !is_object($EM_Location) && !empty($_REQUEST['location_id']) ){
		$EM_Location = new EM_Location($_REQUEST['location_id']);
	}
	
	do_action( 'bp_em_my_locations' );
	
	//plug into EM admin code (at least for now)
	include_once(EM_DIR.'/admin/em-admin.php');
	em_admin_load_scripts();
	add_action('wp_head','em_admin_general_script');
	
	$template_title = 'bp_em_my_locations_title';
	$template_content = 'bp_em_my_locations_content';

	if( count($bp->action_variables) > 0 ){
		if( !empty($bp->action_variables[0]) ){
			switch($bp->action_variables[0]){
				case 'edit':
					$template_title = 'bp_em_my_locations_editor_title';
					$template_content = 'bp_em_my_locations_editor_content';
					break;
				default :
					$template_title = 'bp_em_my_locations_title';
					$template_content = 'bp_em_my_locations_content';
					break;
			}
		}else{
			$template_title = 'bp_em_my_locations_title';
			$template_content = 'bp_em_my_locations_content';
		}
	}

	add_action( 'bp_template_title', $template_title );
	add_action( 'bp_template_content', $template_content );
	
	/* Finally load the plugin template file. */
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function bp_em_my_locations_title() {
	_e( 'My Locations', 'dbem' );
}

/**
 * Determines whether to show location page or locations page, and saves any updates to the location or locations
 * @return null
 */
function bp_em_my_locations_content() {
	em_locate_template('buddypress/my-locations.php', true);
}

function bp_em_my_locations_editor_title() {
	global $EM_Location;
	if( is_object($EM_Location) ){
		_e( 'Edit Location', 'dbem' );	
	}else{
		_e( 'Add Location', 'dbem' );
	}
}

function bp_em_my_locations_editor_content(){
	em_locate_template('forms/location-editor.php', true);
}

?>