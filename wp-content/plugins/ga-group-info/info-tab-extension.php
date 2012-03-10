<?php
class gait_info_tab extends BP_Group_Extension {

	var $visibility = 'public';
	var $allowed_html = array( // any html not in this array will get filtered out of all fields
			'a' => array(
				'href' => array(),
				'title' => array()
			),
			'br' => array(),
			'em' => array(),
			'strong' => array(),
			'img' => array(
				'src' => array(),
				'title' => array(),
				'alt' => array(),
				'class' => array(),
				'id' => array()
			)
		);
	
	function gait_info_tab() {
		$this->name = 'Info';
		$this->slug = 'info';
		$this->create_step_position = 2;
		$this->nav_item_position = 14;
		$this->enable_nav_item = $this->enable_nav_item();
		$this->enable_edit_item = current_user_can('manage_options');
	}
	
	function create_screen(){ 
		if ( !bp_is_group_creation_step( $this->slug ) )
			return false;
		
		require( dirname( __FILE__) . '/info-tab-create.php' );
	}
	
	function create_screen_save(){
		global $bp;
		
		check_admin_referer( 'groups_create_save_' . $this->slug );
		
		// build default field structure
		require( dirname( __FILE__ ) . '/info-tab-default-data.php');
		$data = gait_default_fields();
		
		// update structure with inputted data
		foreach ($data as $slug => $metadata){
			$data[$slug]['value'] = wp_kses( $_POST['gait-'.$slug], $this->allowed_html );
		}
		
		// put it into the db
		$success = groups_update_groupmeta( $bp->groups->current_group->id, $this->slug, $data );
		
	}
	
	function edit_screen(){
		if ( !bp_is_group_admin_screen( $this->slug ) )
			return false;
		
		global $bp;
		require_once( dirname( __FILE__ ) . '/info-tab-admin.php' );
		
	}
	
	function edit_screen_save(){
		global $bp;
		
		if ( !isset( $_POST['save'] ) )
			return false;
		
		check_admin_referer( 'gait_edit_save_' . $this->slug );
		
		/* Put Edit Screen "Save" code here */
		$finaldata = groups_get_groupmeta( $bp->groups->current_group->id, $this->slug ); // grab current data to be updated

		foreach ($finaldata as $slug => $metadata){
			$finaldata[$slug]['value'] = wp_kses( $_POST['gait-'.$slug], $this->allowed_html );
		}

		$success = groups_update_groupmeta( $bp->groups->current_group->id, $this->slug, $finaldata );
		
		/* error & success messages */
		if ( !$success )
			bp_core_add_message( __( 'There was an error saving, please try again', 'buddypress' ), 'error' );
		else
			bp_core_add_message( __( 'Successfully saved', 'buddypress' ) );
		
		//bp_core_redirect( bp_get_group_permalink( $bp->groups->current_group ) . '/admin/' . $this->slug );
	}
	
	function display(){
		require( dirname( __FILE__ ) . '/info-tab-content.php' );
	}
	
	function enable_nav_item() {
		global $bp;
		
		$data = groups_get_groupmeta( $bp->groups->current_group->id, $this->slug );
		if (is_array($data)){
			return true;
		} else {
			return false;
		}
	}

}
