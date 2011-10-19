<?php

function add_group_meta_box() { 
	
	global $bp;
	
	?>
	<fieldset><legend class="screen-reader-text"><span><?php _e('Core Group Options', 'bp-group-organizer') ?></span></legend>
	<p><label for="group_name">
	<?php _e('Group Name', 'bp-group-organizer'); ?>
	<input id="group_name" type="text" name="group_name" value="" />
	</label></p>
	<p><label for="group_slug">
	<?php _e('Group Slug', 'bp-group-organizer'); ?>
	<input id="group_slug" type="text" name="group_slug" value="" />
	</label></p>
	<p><label for="group_desc">
	<?php _e('Group Description', 'bp-group-organizer'); ?>
	<input id="group_desc" type="text" name="group_desc" value="" />
	</label></p>
	</fieldset>
	<fieldset><legend class="screen-reader-text"><span><?php _e('Advanced Group Options', 'bp-group-organizer') ?></span></legend>
	<p><label for="group_status">
	<?php _e( 'Privacy Options', 'buddypress' ); ?>
	<select id="group_status" name="group_status">
		<?php foreach($bp->groups->valid_status as $status) : ?>
		<option value="<?php echo $status ?>"><?php echo ucfirst($status) ?></option>
		<?php endforeach; ?>
	</select>
	</label></p>
	<?php if ( bp_is_active( 'forums' ) && ( function_exists( 'bp_forums_is_installed_correctly' ) && bp_forums_is_installed_correctly() ) ) : ?>
	<p><label for="group_forum">
	<?php _e('Enable discussion forum', 'buddypress'); ?>
	<input id="group_forum" type="checkbox" name="group_forum" checked />
	</label></p>
	<?php endif; ?>
	<p><label for="group_creator">
	<?php _e('Group Creator', 'bp-group-organizer'); ?>
	<input id="group_creator" type="text" name="group_creator" value="" />
	</label></p>
	</fieldset>
	<?php if(defined('BP_GROUP_HIERARCHY_IS_INSTALLED') ) : ?>
	<fieldset><legend class="screen-reader-text"><span><?php _e('Group Hierarchy Options') ?></span></legend>
		<?php if(method_exists('BP_Groups_Hierarchy','get_tree')) : ?>
		<p><label for="group_parent">
		<?php _e( 'Parent Group', 'bp-group-hierarchy' ); ?>
		<?php $all_groups = BP_Groups_Hierarchy::get_tree(); ?>
		<select name="group_parent" id="group_parent">
			<option value="0"><?php _e('Site Root','bp-group-hierarchy') ?></option>
			<?php foreach($all_groups as $group) : ?>
			<option value="<?php echo $group->id ?>"><?php echo $group->name ?> (<?php echo $group->slug ?>)</option>
			<?php endforeach; ?>
		</select>
		</label></p>
		<?php else: ?>
		<p><?php _e('Your version of BuddyPress Group Hierarchy is too old to use with the organizer. Please upgrade to version <code>1.2.1</code> or higher.', 'bp-group-organizer'); ?></p>
		<?php endif; ?>
	</fieldset>
	<?php endif; ?>
	<?php do_action( 'bp_group_organizer_display_new_group_options' ); ?>
	<p><?php _e('Create a group to add to your site.', 'bp-group-organizer'); ?></p>
	<p class="button-controls">
		<img class="waiting" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" alt="" />
		<?php submit_button( __( 'Add' ), 'primary', 'group-organizer-create', false ); ?>
	</p>
	<?php
}


add_meta_box('add_group_meta_box', __('Create a Group', 'bp-group-organizer'), 'add_group_meta_box', 'group-organizer', 'side');

?>