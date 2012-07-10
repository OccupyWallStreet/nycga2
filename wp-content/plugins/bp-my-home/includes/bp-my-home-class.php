<?php

if ( !class_exists( 'BP_Component' ) ){
	require_once( WP_PLUGIN_DIR . '/buddypress/bp-loader.php' );
}

class BP_MYHOME_Component extends BP_Component {
	function __construct() {
		$this->slug = 'my-home';
		parent::start(
			'my-home',
			__( 'My Home', 'bp-my-home' ),
			BP_MYHOME_PLUGIN_DIR
		);
		$this->setup_nav();
	}
	
	
	function setup_nav() {
		global $bp;
		
		$main_nav = array(
			'name'                => __( 'My Home', 'bp-my-home' ),
			'slug'                => $this->slug,
			'position'            => 0,
			'show_for_displayed_user' => false,
			'screen_function'     => 'bp_my_home_home',
			'default_subnav_slug' => 'my-widgets',
			'item_css_id'         => $this->id
		);

		$my_home_link = trailingslashit( $bp->loggedin_user->domain . $this->slug );

		// Add the My Widgets and My Home nav item
		$sub_nav[] = array(
			'name'            => __( 'My Widgets', 'bp-my-home' ),
			'slug'            => 'my-widgets',
			'parent_url'      => $my_home_link,
			'parent_slug'     => $this->slug,
			'screen_function' => 'bp_my_home_home',
			'position'        => 10,
			'item_css_id'     => 'my-home-my-widgets'
		);
		$sub_nav[] = array(
			'name'            => __( 'My Settings', 'bp-my-home' ),
			'slug'            => 'my-settings',
			'parent_url'      => $my_home_link,
			'parent_slug'     => $this->slug,
			'screen_function' => 'bp_my_home_settings',
			'position'        => 20,
			'item_css_id'     => 'my-home-my-settings'
		);
		
		parent::setup_nav( $main_nav, $sub_nav );
	}
}

$bp->myhome = new BP_MYHOME_Component();
?>