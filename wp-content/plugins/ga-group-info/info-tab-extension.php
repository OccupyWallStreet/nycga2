<?php
class gait_info_tab extends BP_Group_Extension {

	var $visibility = 'public';
	var $enable_nav_item = true;
	
	function gait_info_tab() {
		$this->name = 'Info';
		$this->slug = 'info';
		//$this->create_step_position = 6;
		$this->nav_item_position = 14;
	}
	
	function create_screen(){ 
		if ( !bp_is_group_creation_step( $this->slug ) )
			return false;
		?>
		Curent Function: gait_info_tab::create_screen() (line 18)
		
		<?php
		wp_nonce_field( 'groups_create_save_' . $this->slug );
	}
	
	function create_screen_save(){
		global $bp;
		
		check_admin_referer( 'groups_create_save_' . $this->slug );
		
		// Save details here - update this later 
		groups_updated_groupmeta( $bp->groups->new_group_id, 'my_meta_name', 'value' );
	}
	
	function edit_screen(){
		if ( !bp_is_group_admin_screen( $this->slug ) )
			return false;
		
		require_once( dirname( __FILE__ ) . '/info-tab-admin.php' );
		
	}
	
	function edit_screen_save(){
		global $bp;
		
		if ( !isset( $_POST['save'] ) )
			return false;
		
		check_admin_referer( 'gait_edit_save_' . $this->slug );
		
		/* Put Edit Screen "Save" code here */
		$finaldata = groups_get_groupmeta( $bp->groups->current_group->id, $this->slug ); // grab current data to be updated
		$data = $_POST['gait'];
		$allowed_html = array( // any html not in this array will get filtered out.
			'a' => array(
				'href' => array(),
				'title' => array()
			),
			'br' => array(),
			'em' => array(),
			'strong' => array()
		);
		foreach ($finaldata['name'] as $slug => $name){
			$finaldata['data'][$slug] = wp_kses( $_POST['gait-'.$slug], $allowed_html );
		}
		
		groups_update_groupmeta( $bp->groups->current_group->id, $this->slug, $finaldata );
		
		/* error & success messages */
		if ( !$success )
			bp_core_add_message( __( 'There was an error saving, please try again', 'buddypress' ), 'error' );
		else
			bp_core_add_message( __( 'Successfully saved', 'buddypress' ) );
		
		bp_core_redirect( bp_get_group_permalink( $bp->groups->current_group ) . '/admin/' . $this->slug );
	}
	
	function display(){
		require( dirname( __FILE__ ) . '/info-tab-content.php' );
	}

}
