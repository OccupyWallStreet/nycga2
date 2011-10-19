<?php

/**
 * Group API
 *
 * http://codex.buddypress.org/developer-docs/group-extension-api/
 */
class BP_Groupblog_Extension extends BP_Group_Extension {

	function bp_groupblog_extension() {
		global $bp;

		$this->name = __( 'Group Blog', 'groupblog' );
		$this->slug = 'group-blog';

		$this->enable_create_step   = true;
		$this->create_step_position = 15;

		$this->enable_edit_item = true;

		$this->nav_item_name     = 'Blog';
		$this->nav_item_position = 30;
		//$this->enable_nav_item   = $this->enable_nav_item();
		$this->enable_nav_item	 = false;
		$this->template_file     = 'groupblog/blog';
	}

	function create_screen() {
		global $bp, $groupblog_create_screen;

		if ( !bp_is_group_creation_step( $this->slug ) )
			return false;

		$groupblog_create_screen = true;

		// Attaching the markup via a hook, so that plugins can unhook and replace with
		// their own markup. This is a bit of a hack.
		add_action( 'bp_groupblog_create_screen_markup', 'bp_groupblog_signup_blog' );
		do_action( 'bp_groupblog_create_screen_markup' );

		echo '<input type="hidden" name="groupblog-group-id" value="' . $bp->groups->current_group->id . '" />';
		echo '<input type="hidden" name="groupblog-create-save" value="groupblog-create-save" />';

		wp_nonce_field( 'groups_create_save_' . $this->slug );
	}

	function create_screen_save() {
		if ( isset( $_POST['save'] ) ) {
			groupblog_edit_settings();
		}
	}

	function edit_screen() {
		global $bp;

		if ( !bp_is_group_admin_screen( $this->slug ) )
			return false;

		// Attaching the markup via a hook, so that plugins can unhook and replace with
		// their own markup. This is a bit of a hack.
		add_action( 'bp_groupblog_edit_screen_markup', 'bp_groupblog_signup_blog' );
		do_action( 'bp_groupblog_edit_screen_markup' );

	}

	function edit_screen_save() {
		if ( isset( $_POST['save'] ) ) {
			groupblog_edit_settings();
		}
	}

	function display() {
	}

	function widget_display() {
	}

	function enable_nav_item() {
		return bp_is_group() && groups_get_groupmeta( bp_get_current_group_id(), 'groupblog_enable_blog' );
	}

}
bp_register_group_extension( 'BP_Groupblog_Extension' );

?>