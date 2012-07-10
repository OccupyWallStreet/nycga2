<?php
/**
 * 
 *
 *  API for Working with WordPress User Profiles
 *
 *
 *  @package PageLines Framework
 *  @since 2.0.b16
 *
 */
class ProfileEngine {

	public $tabs = array( );
	public $current_tabs = array( );
	public $admin_options = array( );


	/**
	*
	* @TODO document
	*
	*/
	function __construct( array $settings = array() ) { 
		
		// Template Actions
		
			// Load Tabs
			add_action( 'edit_user_profile', array( &$this, 'admin_settings_tab' ) );
			add_action( 'edit_user_profile_update', array( &$this, 'admin_settings_tab' ) );
			
			if ( current_user_can('edit_user') ){
				add_action( 'show_user_profile', array( &$this, 'admin_settings_tab' ) );
				add_action( 'personal_options_update', array( &$this, 'admin_settings_tab' ) );
			}
			
			// Draw Panel
			add_action( 'edit_user_profile', array( &$this, 'do_panel' ) );
			add_action( 'show_user_profile', array( &$this, 'do_panel' ) );		
				
			// Save!
			add_action( 'edit_user_profile_update', array( &$this, 'save_profile_admin' ) );
			add_action( 'personal_options_update', array( &$this, 'save_profile_admin' ) );
		
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function save_profile_admin( $user_ID ){

		if(!isset($this->tabs) || empty($this->tabs))
			return;

		// Loop through tabs
		foreach($this->tabs as $tab => $t){
			
			// Loop through tab options
			foreach($t->options as $oid => $o){
				
				
				// Note: If the value is null, then test to see if the option is already set to something
				// create and overwrite the option to null in that case (i.e. it is being set to empty)
				if(isset($o['selectvalues']) && pagelines_is_multi_option($oid, $o) ){
					
					foreach($o['selectvalues'] as $sid =>$s ){
						$option_value =  isset($_POST[$sid]) ? $_POST[$sid] : null;
						
						if(!empty($option_value) || pl_um($sid, $user_ID))
							update_post_meta($user_ID, $sid, $option_value );
					}
					
				} else {
				
					
					$option_value =  isset($_POST[$oid]) ? $_POST[$oid] : null;

					if(!empty($option_value) || pl_um($oid, $user_ID))
						update_user_meta($user_ID, $oid, $option_value );
					
				}
			
				
			}
		
		}
		
	}
	
	
	

	/**
	*
	* @TODO document
	*
	*/
	function do_panel( $user ){
	
		if( empty($this->tabs) )
			return;

		$set = array(
				'handle'	=> 'profiletabs',
				'title' 	=> 'Profile Options',
				'tag' 		=> false,
				'type'		=> 'profile',
				'stext' 	=> __('Save Profile Options','pagelines'),
				'tabs' 		=> $this->tabs, 
				'user'		=> $user
			);

		$panel = new PLPanel();

		$panel->the_panel( $set );
		
	}

	

	/**
	*
	* @TODO document
	*
	*/
	function admin_settings_tab( $user ){
		
		if( empty($this->admin_options) )
			return;
			
		$set = array(
			'id'		=> 'profile_admin_settings', 
			'opts'		=> $this->admin_options, 
			'icon'		=> PL_ADMIN_ICONS.'/admin.png', 
			'role'		=> 'admin', 
			'name'		=> 'Admin Options'
		);

		$this->register_tab($set, 'top');
		
	}
	
	public function register_admin_opts( $opts ){
		
		$this->admin_options = array_merge( $this->admin_options, $opts );
		
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function user_opts(  ){
		
		$this->current_tabs = array_merge($this->current_tabs, $this->get_tabs('user'));

	}
	

	/**
	*
	* @TODO document
	*
	*/
	function get_tabs($role = 'user'){
	 
		$rtabs = array();
		foreach($this->tabs as $tid => $t){
			if($t->role == $role)
				$rtabs[$tid] = $t; 
		}
		
		return $rtabs;
	}
	
	/**
	 * Register a new tab for the meta panel
	 * This will look at Clone values and draw cloned tabs for cloned sections
	 *
	 * @since 2.0.b4
	 */
	function register_tab( $set, $location = 'bottom') {
		
		$d = array(
				'id' 		=> '',
				'opts'		=> array(),
				'name' 		=> '',
				'icon' 		=> '',
				'role'		=> 'user',
				'active'	=> true
			);

		$s = wp_parse_args($set, $d);
		
		$tab_id = $s['id'];
		
		if($location == 'top'){
			
			$top[ $tab_id ] = new stdClass;
			
			$top[$tab_id]->options = $s['opts'];
			$top[$tab_id]->icon = $s['icon'];
			$top[$tab_id]->active = $s['active'];
			$top[$tab_id]->name = $s['name'];
			$top[$tab_id]->role = $s['role'];
			
			$this->tabs = array_merge($top, $this->tabs);
			
		} else {
			
			$this->tabs[ $tab_id ] = new stdClass;
			
			$this->tabs[ $tab_id ]->options = $s['opts'];
			$this->tabs[ $tab_id ]->icon = $s['icon'];
			$this->tabs[ $tab_id ]->active = $s['active'];
			$this->tabs[ $tab_id ]->name = $s['name'];
			$this->tabs[ $tab_id ]->role = $s['role'];
		}

	}


}

/**
*
* @TODO do
*
*/
function register_profile_tab( $set, $location = 'bottom' ){

	global $profile_panel_options;

	$profile_panel_options->register_tab($set, $location);

}

/**
*
* @TODO do
*
*/
function register_profile_admin_opts( $opts ){

	global $profile_panel_options;

	$profile_panel_options->register_admin_opts( $opts );

}



/**
*
* @TODO do
*
*/
function pl_user_id(){
	
	
	if(isset($_GET['user_id']))
		return (int) $_GET['user_id'];
	else{
		
		global $user_ID;
		return $user_ID;
		
	}
	
}


/**
 * 
 * Not used currently, added here for reference.
 */
function pl_leaderboard(){
	global $wpdb;

	// Returns the SUM of karma within a period, and total
	$rows = $wpdb->get_results( "SELECT k.user_id,
									SUM(IF(k.timestamp > ADDDATE( NOW(), INTERVAL - {$period_units} {$period} ), k.karma, 0)) AS recent_karma, 
									SUM(k.karma) as total_karma
									FROM $wpdb->chess_karma k,  $wpdb->usermeta u
									WHERE u.user_id = k.user_id
										AND u.meta_key = 'pagelines_pro_publish'
										AND u.meta_value = 'true'
									GROUP BY k.user_id 
									ORDER BY recent_karma DESC LIMIT $number" );
	
	return $rows;															
}
