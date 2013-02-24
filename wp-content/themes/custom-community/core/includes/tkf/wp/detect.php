<?php
class TK_WP_Detect {
	/**
	 * PHP 4 constructor
	 *
	 * @package Custom Community
	 * @since 1.8.3
	 */
	function tk_detect_page_type() {
		$this->__construct();
	}

	/**
	 * PHP 5 constructor
	 *
	 * @package Custom Community
	 * @since 1.8.3
	 */	
	function __construct() {
		if( $this->tk_is_buddypress() ){
			add_filter( 'tk_get_page_type', array( $this, 'tk_get_bp_page_type' ) );
		}
	}
	
	/**
	* tk_get_wp_type
	* 
	* @return string 'wp' for a wordpress blog 'wpmu' for a wordpress network blog.
	*/ 
	function tk_get_wp_type(){
		global $blog_id;
		
		if( defined( 'SITE_ID_CURRENT_SITE' ) ){
			if ( $blog_id != SITE_ID_CURRENT_SITE ){
				$wp_type = 'mu';		
			}else{ 
				$wp_type = 'wp';			
			}
		}else{
			$wp_type = 'wp';
		}
		return apply_filters( 'tk_wp_type', $wp_type );	
	}
	
	/**
	* tk_is_buddypress
	* 
	* @return boolean true if buddypress is installed, false if not.
	*/ 
	function tk_is_buddypress(){
			if ( defined( 'BP_VERSION' ) ){ return true; }else{ return false; }
	}
	
	/**
	* tk_get_page_type
	* 
	* @return string: the page type of the current shown page
	*/ 
	function tk_get_page_type(){
		global $post;
        // if is wordpress and no buddypress
		if( $this->tk_get_wp_type() == "wp" ) {
			if( is_admin() ) $page_type = 'wp-admin';
			if( ( is_home() || is_front_page()) && !$this->tk_is_signup() ) $page_type = 'wp-home';
			if( is_single() ) $page_type = 'wp-post';	
			if( is_page() && !is_front_page() ){ $page_type = 'wp-page'; }			
			if( !empty($post) && is_sticky() && !is_home()) $page_type = 'wp-sticky';	 				
			if( is_category() ) $page_type = 'wp-category';	 			
			if( is_tag() ) $page_type = 'wp-tag';
			if( is_tax() ) $page_type = 'wp-tax'; 
			if( is_author() ) $page_type = 'wp-author';
			if( is_archive() ) $page_type = 'wp-archive';
			if( is_search() ) $page_type = 'wp-search';
			if( $this->tk_is_signup() ) $page_type = 'wp-signup';
			if( is_404() ) $page_type = 'wp-404';
		}
		
		// if is wordpress mu
		if( $this->tk_get_wp_type() == "mu" ) {
			if( is_admin() ) $page_type = 'mu-admin'; // Whats happening here on mu blogs?
			if( ( is_home() || is_front_page()) && !$this->tk_is_signup() ) $page_type = 'mu-home';
			if( is_single() ) $page_type = 'mu-post';	
			if( is_page() ) $page_type = 'mu-page';	 	
			if( !empty($post) && is_sticky() ) $page_type = 'mu-sticky';	 				
			if( is_category() ) $page_type = 'mu-category';	 			
			if( is_tag() ) $page_type = 'mu-tag';
			if( is_tax() ) $page_type = 'mu-tax'; 
			if( is_author() ) $page_type = 'mu-author';
			if( is_archive() ) $page_type = 'mu-archive';
			if( is_search() ) $page_type = 'mu-search';
			if( is_404() ) $page_type = 'mu-404';
		}

		return apply_filters( 'tk_get_page_type', $page_type );
	}

	function tk_is_signup(){
		if(empty($_REQUEST['action']))
			return false;
	
		if( $_REQUEST['action'] == 'register' ){
			return true;
		}else{
			return false;
		}
	}
	
	function tk_get_bp_page_type( $page_type ){
		global $bp;
		
		if( is_page() && $this->tk_is_buddypress() && $bp->current_component != '' ){

			$slug = $bp->current_component;
			$action = $bp->current_action;
	
			if(isset($bp) && property_exists($bp, 'displayed_user') && property_exists($bp->displayed_user, 'id') && $bp->displayed_user->id != 0 && $slug == 'activity' && $action == 'just-me'){
				$slug = 'profile';	
			}
		
			$component = $this->tk_get_bp_component_by_slug( $slug );
			
			
			if( $component != '' ){
				if( $action != '' ){
					if( bp_is_group_forum_topic() ){
						$page_type = 'bp-component-' . $component . '-' . $action . '-topic';							
					
					}elseif ( !bp_is_component_front_page( 'activity' ) &&  bp_is_activity_component() && $action != 'just-me' ){
						$page_type = 'bp-component-activity-activity';
					}else{
						$page_type = 'bp-component-' . $component . '-' . $action;
					}
				}else{
					$page_type = 'bp-component-' . $component;
				}
			}
		}
		return apply_filters( 'tk_get_bp_page_type', $page_type );
	}

	function tk_get_bp_component_by_slug( $slug ){
		
		$component_slugs = array();
		
		if ( defined( 'BP_ACTIVITY_SLUG' ) ) $component_slugs[ BP_ACTIVITY_SLUG ] = "activity";
		if ( defined( 'BP_BLOGS_SLUG' ) ) $component_slugs[ BP_BLOGS_SLUG ] = "blogs";
		if ( defined( 'BP_MEMBERS_SLUG' ) ) $component_slugs[ BP_MEMBERS_SLUG ] = "members";
		if ( defined( 'BP_FRIENDS_SLUG' ) ) $component_slugs[ BP_FRIENDS_SLUG ] = "friends";
		if ( defined( 'BP_GROUPS_SLUG' ) ) $component_slugs[ BP_GROUPS_SLUG ] = "groups";
		if ( defined( 'BP_FORUMS_SLUG' ) ) $component_slugs[ BP_FORUMS_SLUG ] = "forums";
		if ( defined( 'BP_MESSAGES_SLUG' ) ) $component_slugs[ BP_MESSAGES_SLUG ] = "messages";
		if ( defined( 'BP_WIRE_SLUG' ) ) $component_slugs[ BP_WIRE_SLUG ] = "wire";
		if ( defined( 'BP_XPROFILE_SLUG' ) ) $component_slugs[ BP_XPROFILE_SLUG ] = "profile";
		
		if ( defined( 'BP_REGISTER_SLUG' ) ) $component_slugs[ BP_REGISTER_SLUG ] = "register";
		if ( defined( 'BP_ACTIVATION_SLUG' ) ) $component_slugs[ BP_ACTIVATION_SLUG ] = "activate";
		if ( defined( 'BP_SEARCH_SLUG' ) ) $component_slugs[ BP_SEARCH_SLUG ] = "search";
	
		if( !empty($component_slugs[ $slug ]) ){
			$component = $component_slugs[ $slug ];
		}else{
			$component = $slug;
		}
	return $component;	
	}

	function tk_bp_is_active_component( $slug ){
		global $bp;
		
		$component_name = tk_get_bp_component_by_slug( $slug );
		
		$components = array_keys( $bp->active_components );
		
		foreach( $components AS $key => $component ){
			$components_arr[ $key ] = tk_get_bp_component_by_slug( $component );
		}
		
		if( is_array( $components ) ){
			if( in_array( $component_name, $components_arr ) ){
				return true;		
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
}