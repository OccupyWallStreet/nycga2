<?php

class ExtensionIntegrations extends PageLinesExtensions {
	
	/**
	 * Integrations tab.
	 * 
	 */
	function extension_integrations( $tab = '' ) {
		
		$type = 'integration';
	
		$integrations = $this->get_latest_cached( 'integrations' );

		if ( !is_object($integrations ) ) 
			return $integrations;

		$integrations = json_decode( json_encode( $integrations ), true ); // convert objects to arrays	

		
		$list = $this->get_master_list( $integrations, $type, $tab );
		
		$args = array( 
			'list' 	=> $list, 
			'tab' 	=> $tab, 
			'type' 	=> 'integrations', 
			'mode' 	=> 'download' 
		);
		
		return $this->ui->extension_list( $args );
	}
	
}


/**
 *
 * @TODO document
 *
 */
function is_integration_active( $key ){
	
	$ints = get_option( PAGELINES_INTEGRATIONS );
	
	if( is_array($ints) 
		&& isset( $ints[$key] )
		&& isset( $ints[$key]['activated'] )
	){
		return $ints[$key]['activated'];
	} else 
		return false;
		
}


/**
 *
 * @TODO document
 *
 */
function integration_activate( $type, $slug, $name, $uploader, $checked ) {
	
	toggle_integration($slug, $name, true);
	
	echo __( 'Options Activated', 'pagelines' );
	
 	integration_reload( PL_ADMIN_STORE_SLUG );
	
}


/**
 *
 * @TODO document
 *
 */
function integration_deactivate( $type, $slug, $name, $uploader, $checked ) {

	
	toggle_integration($slug, $name, false);
	
	echo __( 'Options Deactivated', 'pagelines' );
	
	integration_reload( PL_ADMIN_STORE_SLUG );
	
}


/**
 *
 * @TODO document
 *
 */
function integration_reload( $location ){
	
	$r = rand( 1,100 );
	
	$admin = admin_url( sprintf( 'admin.php?r=%1$s&page=%2$s', $r, $location ) );

	printf( 
		'<script type="text/javascript">setTimeout(function(){ window.location.href = \'%s\';}, %s);</script>', 
		$admin, 
		700
	);
	
	
}


/**
 *
 * @TODO document
 *
 */
function toggle_integration( $slug, $name, $activated = false){
	
	
	$current_integrations = get_option( PAGELINES_INTEGRATIONS );

	$new = array(
		$slug => array(
				'name'		=> $name,
				'slug'		=> $slug,
				'activated'	=> $activated
			)
	);
	
	$new_integrations = wp_parse_args($new, $current_integrations);
	
	update_option( PAGELINES_INTEGRATIONS, $new_integrations );
	
}

// Returns the name

/**
 *
 * @TODO document
 *
 */
function get_integration_path($ext){	
	
	$name = (isset($ext['name'])) ? $ext['name'] : 'No Name';

	$path = $ext['name'];

	return $path;
	
}


/**
 *
 * @TODO document
 *
 */
function handle_integrations_meta(){
	
	global $metapanel_options;
	
	$current_integrations = get_option( PAGELINES_INTEGRATIONS );
	
	if ( ! $current_integrations )
		return array();

	$ints = array();
	foreach($current_integrations as $slug => $info){
		
		if(isset($info['activated']) && $info['activated']){
			
			$key = str_replace('pagelines-integration-', '', strtolower($slug));
		
			$ints[$key] = array(
				'icon'		=> sprintf('%s/%s.png', PL_ADMIN_ICONS, $key),
				'metapanel' => $metapanel_options->posts_metapanel( $key, 'integration' ),	
			);
		
		}
		
	}
	
	return $ints;	
}
