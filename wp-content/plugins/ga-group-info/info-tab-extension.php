<?php
class gait_info_tab extends BP_Group_Extension {

	var $visibility = 'public';
	
	function my_group_extension() {
		$this->name = 'GA Group Info Tab Extension';
		$this->slug = 'ga-info';
		$this->greate_step_position = 21;
		$this->nav_item_position = 31;
	}
	
	function create_screen(){
		if ( !bp_is_group_creation_step( $this->slug ) )
			return false;
		?>
		HTML GOES HERE
		
		<?php
		wp_nonce_field( 'groups_create_save_' . $this->slug );
	}
	
	function create_screen_save(){
		global $bp;
		
		check_admin_referer( 'groups_create_save_' . $this->slug );
		
		/* Save details here - update this later */
		groups_updated_groupmeta( $bp->groups->new_group_id, 'my_meta_name', 'value' );
	}
	
	function edit_screen(){
		if ( !bp_is_group_admin_screen( $this->slug ) )
			return false; ?>
		<h2><?php echo esc_attr( $this->name ); ?></h2>
		
		<p>Edit steps here</p>
		<input type="submit" name="save" value="save" />
		
		<?php
		wp_nonce_field( 'groups_edit_save_' . $this->slug );
	}
	
	function edit_screen_save(){
		global $bp;
		
		if ( !isset( $_POST['save'] ) )
			return false;
		
		check_admin_referer( 'groups_edit_save' . $this->slug );
		
		/* Put Edit Screen "Save" code here */
		
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
