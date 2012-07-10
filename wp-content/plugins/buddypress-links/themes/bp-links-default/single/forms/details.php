<?php do_action( 'bp_before_link_details_form' ); ?>

<form action="<?php bp_link_details_form_action() ?>" method="post" id="link-details-form" class="standard-form">

	<!-- Link Details Section 1: Base (Required Fields) -->
	<?php do_action( 'bp_before_link_details_form_base' ); ?>

	<fieldset>
		<legend>
			<?php _e( 'Details', 'buddypress-links' ) ?>
		</legend>

		<label for="link-category">
			<?php _e('Category', 'buddypress-links') ?>
		</label>
		<?php if ( BP_LINKS_CREATE_CATEGORY_SELECT ): ?>
			<select name="link-category">
				<?php bp_links_category_select_options( bp_get_link_details_form_category_id() ); ?>
			</select>
		<?php else: ?>
			<?php bp_links_category_radio_options( bp_get_link_details_form_category_id(), 'link-category', '' ) ?>
		<?php endif; ?>

		<label for="link-url">
			<?php _e('URL', 'buddypress-links') ?>&nbsp;
			<span class="ajax-loader"></span>
		</label>
		<input type="text" name="link-url" id="link-url" value="<?php bp_link_details_form_url() ?>"<?php if ( bp_get_link_details_form_link_url_readonly() ): ?> readonly="readonly"<?php endif; ?>/>

		<?php bp_links_auto_embed_panel_from_data( bp_get_link_details_form_url_embed_data() ) ?>
		<?php wp_nonce_field( 'bp_links_save_link-auto-embed', '_wpnonce-link-auto-embed' ) ?>

		<div id="link-name-desc-fields"<?php if ( !bp_get_link_details_form_name_desc_fields_display() ): ?> style="display: none;"<?php endif; ?>>
			<label for="link-name">
				<?php _e('Name', 'buddypress-links') ?>
			</label>
			<input type="text" name="link-name" id="link-name" value="<?php bp_link_details_form_name() ?>" />
			<label for="link-desc">
				<?php _e('Description', 'buddypress-links') ?>
			</label>
			<textarea name="link-desc" id="link-desc" cols="60" rows="4"><?php bp_link_details_form_description() ?></textarea>
		</div>
	</fieldset>

	<?php do_action( 'bp_after_link_details_form_base' ); ?>

	<!-- Link Details Section 2: Avatar Options -->
	<?php do_action( 'bp_before_link_details_form_avatar' ); ?>

	<fieldset>
		<legend>
			<a href="#edit-avatar" id="link-avatar-fields-toggle"><?php _e( 'Edit Avatar Options', 'buddypress-links' ) ?></a>
		</legend>
		<div id="link-avatar-fields"<?php if ( !bp_get_link_details_form_avatar_fields_display() ): ?> style="display: none;"<?php endif; ?>>
			<div class="link-avatar-option">
				<label>
					<input type="radio" name="link-avatar-option" id="link-avatar-option-current" value="0"<?php if ( bp_get_link_details_form_avatar_option() === 0 ): ?> checked="checked"<?php endif; ?>>
					<strong><?php _e('Current', 'buddypress-links') ?></strong>
				</label>
				<p>
					<?php bp_link_details_form_avatar_thumb_default( 'avatar-default' ) ?>
					<?php bp_link_details_form_avatar_thumb() ?>
				</p>
			</div>
			<div class="link-avatar-option">
				<label>
					<input type="radio" name="link-avatar-option" id="link-avatar-option-custom" value="1"<?php if ( bp_get_link_details_form_avatar_option() === 1 ): ?> checked="checked"<?php endif; ?>>
					<strong><?php _e('Customize', 'buddypress-links') ?></strong>
				</label>
				<p><?php _e('After saving this link you will be directed to a page where you can customize the avatar.', 'buddypress-links') ?></p>
			</div>
		</div>
	</fieldset>

	<?php do_action( 'bp_after_link_details_form_avatar' ); ?>

	<!-- Link Details Section 3: Advanced Settings -->
	<?php do_action( 'bp_before_link_details_form_advanced' ); ?>

	<fieldset>
		<legend><a href="#edit-settings" id="link-settings-fields-toggle"><?php _e( 'Edit Advanced Settings', 'buddypress-links' ) ?></a></legend>

		<div id="link-settings-fields"<?php if ( !bp_get_link_details_form_settings_fields_display() ): ?> style="display: none;"<?php endif; ?>>

			<label for="link-status"><?php _e( 'Privacy Options', 'buddypress-links' ); ?></label>

			<label>
				<input type="radio" name="link-status" value="<?php echo BP_Links_Link::STATUS_PUBLIC ?>"<?php if ( BP_Links_Link::STATUS_PUBLIC == bp_get_link_details_form_status() || !bp_get_link_details_form_status() ) { ?> checked="checked"<?php } ?> />
				<?php _e( 'This is a public link', 'buddypress-links' ) ?>
			</label>
				<ul>
					<li><?php _e( 'Any site member can see this link, and comment on it.', 'buddypress-links' ) ?></li>
					<li><?php _e( 'This link will be listed in the links directory and in search results.', 'buddypress-links' ) ?></li>
					<li><?php _e( 'Link content and activity will be visible to any site member.', 'buddypress-links' ) ?></li>
				</ul>

			<label>
				<input type="radio" name="link-status" value="<?php echo BP_Links_Link::STATUS_FRIENDS ?>"<?php if ( BP_Links_Link::STATUS_FRIENDS == bp_get_link_details_form_status() ) { ?> checked="checked"<?php } ?> />
				<?php _e( 'This is a friends-only link', 'buddypress-links' ) ?>
			</label>
				<ul>
					<li><?php _e( 'Only users who are in your friends list can see the link and comment on it.', 'buddypress-links' ) ?></li>
					<li><?php _e( 'This link will NOT be listed in the links directory or in search results.', 'buddypress-links' ) ?></li>
					<li><?php _e( 'Link content and activity will only be visible to your friends.', 'buddypress-links' ) ?></li>
				</ul>

			<label>
				<input type="radio" name="link-status" value="<?php echo BP_Links_Link::STATUS_HIDDEN ?>"<?php if ( BP_Links_Link::STATUS_HIDDEN == bp_get_link_details_form_status() ) { ?> checked="checked"<?php } ?> />
				<?php _e('This is a hidden link', 'buddypress-links') ?>
			</label>
				<ul>
					<li><?php _e( 'Only you can see the link.', 'buddypress-links' ) ?></li>
					<li><?php _e( 'This link will NOT be listed in the links directory or in search results.', 'buddypress-links' ) ?></li>
					<li><?php _e( 'Link content and activity will only be visible to you.', 'buddypress-links' ) ?></li>
				</ul>

		</div>

	</fieldset>

	<?php do_action( 'bp_after_link_details_form_advanced' ); ?>

	<?php do_action( 'bp_before_link_details_form_buttons' ); ?>

	<input type="submit" value="<?php _e('Save Link', 'buddypress-links') ?> &rarr;" name="save" />
	
	<?php do_action( 'bp_after_link_details_form_buttons' ); ?>

	<!-- Don't leave out these hidden fields -->
	<input type="hidden" name="link-group-id" id="link-group-id" value="<?php bp_link_details_form_link_group_id() ?>" />
	<input type="hidden" name="link-url-readonly" id="link-url-readonly" value="<?php bp_link_details_form_link_url_readonly() ?>" />
	<input type="hidden" name="link-avatar-fields-display" id="link-avatar-fields-display" value="<?php bp_link_details_form_avatar_fields_display() ?>" />
	<input type="hidden" name="link-settings-fields-display" id="link-settings-fields-display" value="<?php bp_link_details_form_settings_fields_display() ?>" />
	<?php wp_nonce_field( 'bp_link_details_form_save' ) ?>
</form>

<?php do_action( 'bp_after_link_details_form' ) ?>