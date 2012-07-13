<?php
/**
 * Custom Group extension
 */
class BP_Links_Group_Extension extends BP_Group_Extension {

	function  __construct() {
		// required
		$this->name = sprintf( __( 'Links (%d)', 'buddypress-links' ), bp_links_total_links_for_group() );
		$this->slug = BP_LINKS_SLUG;
		// optional
		$this->visibility  = 'private';
		$this->enable_create_step  = false;
		$this->enable_edit_item  = false;
	}

	function display() {
		global $bp;

		do_action( 'bp_links_screen_group_links' );

		if ( 'create' == $bp->action_variables[0] ) {
			// load create group link template
			bp_links_locate_template( array( 'groups/single/links-create.php' ), true );
		} else {
			// load group links list template
			bp_links_locate_template( array( 'groups/single/links-list.php' ), true );
		}
	}

	function widget_display() {
		return;
	}
}
bp_register_group_extension( 'BP_Links_Group_Extension' );

?>
