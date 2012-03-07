<?php get_header() ?>

	<div id="content">
		<div class="padder" id="jes-padder">
<?php $jes_adata = get_option( 'jes_events' ); ?>
	<?php
			$jes_adata = get_option( 'jes_events' );
			$createa = $jes_adata[ 'jes_events_createnonadmin_disable' ];
			$showcreate = 0;
			if ( is_user_logged_in() )
					{
						if (!$createa )
							{
								$showcreate = 1;
							}
						if (current_user_can('manage_options'))
							{
								$showcreate = 1;
							}
					}
	?>

		<form action="<?php bp_event_creation_form_action() ?>" method="post" id="create-event-form" class="standard-form" enctype="multipart/form-data">
			<h3><?php _e( 'Create an Event', 'jet-event-system' ) ?> &nbsp;<a class="button" href="<?php echo bp_get_root_domain() . '/' . JES_SLUG . '/' ?>"><?php _e( 'Events Directory', 'jet-event-system' ) ?></a></h3>

			<?php do_action( 'bp_before_create_event' ) ?>

			<div class="item-list-tabs no-ajax" id="event-create-tabs">
				<ul>
					<?php bp_event_creation_tabs(); ?>
				</ul>
			</div>

			<?php do_action( 'template_notices' ) ?>

		
			
			<div class="item-body" id="event-create-body">

				<?php /* Event creation step 1: Basic event details */ ?>
				<?php if ( jes_is_event_creation_step( 'event-details' ) ) : ?>

					<?php do_action( 'bp_before_event_details_creation_step' ); ?>
	<div style="clear:left;"></div>
	<table valign="top">
		<tr>
			<td width="50%" style="vertical-align:top;">
				<h4><?php _e('Base event details','jet-event-system') ?></h4>
					<label for="event-name"><?php _e('* Event Name', 'jet-event-system') ?> <?php _e( '(required)', 'jet-event-system' )?></label>
					<input type="text" name="event-name" id="event-name" value="<?php bp_new_event_name() ?>" />

	<?php 
		$shifta = $jes_adata[ 'jes_events_adminapprove_enable' ];
		if ($shifta)
			{
				if ( current_user_can('manage_options') )
					{ ?>
						<input type="hidden" name="event-eventapproved" value="1">
					<?php }
						else
					{ ?>
						<input type="hidden" name="event-eventapproved" value="0">
					<?php }
					
			} 
				else
			{ ?>
				<input type="hidden" name="event-eventapproved" value="1">
			<?php } ?>
			
				<label for="event-etype"><?php _e('* Event classification', 'jet-event-system') ?> <?php _e( '(required)', 'jet-event-system' )?></label>
					
	<?php if (!$jes_adata[ 'jes_events_class_enable' ])  { ?>					
			<input type="text" name="event-etype" id="event-etype" value="<?php bp_new_event_etype() ?>" />	
	<?php } else { ?>
			<select name="event-etype" id="event-etype" size = "1">
				<option value="<?php echo $jes_adata['jes_events_text_one' ] ?>"><?php echo $jes_adata['jes_events_text_one' ] ?></option> 
			<?php if ($jes_adata['jes_events_text_two' ] != null) { ?>
				<option value="<?php echo $jes_adata['jes_events_text_two' ] ?>"><?php echo $jes_adata['jes_events_text_two' ] ?></option>
			<?php } ?>
			<?php if ($jes_adata['jes_events_text_three' ] != null) { ?>
				<option value="<?php echo $jes_adata['jes_events_text_three' ] ?>"><?php echo $jes_adata['jes_events_text_three' ] ?></option>
			<?php } ?>
			<?php if ($jes_adata['jes_events_text_four' ] != null) { ?>
				<option value="<?php echo $jes_adata['jes_events_text_four' ] ?>"><?php echo $jes_adata['jes_events_text_four' ] ?></option>
			<?php } ?>
			<?php if ($jes_adata['jes_events_text_five' ] != null) { ?>
				<option value="<?php echo $jes_adata['jes_events_text_five' ] ?>"><?php echo $jes_adata['jes_events_text_five' ] ?></option>
			</select>
		<?php } ?>
	<?php } ?>					
					
		<label for="event-desc"><?php _e('* Event Description', 'jet-event-system') ?> <?php _e( '(required)', 'jet-event-system' )?></label>
			<textarea name="event-desc" id="event-desc"><?php bp_new_event_description() ?></textarea>

	<?php if ($jes_adata[ 'jes_events_countryopt_enable' ]) { ?>
		<label for="event-placedcountry"><?php _e('* Event Country', 'jet-event-system') ?> <?php _e( '(required)', 'jet-event-system' )?>
		<input type="text" name="event-placedcountry" id="event-placedcountry" size="15" maxlength="25" value="<?php bp_new_event_placedcountry() ?>" /></label>
	<?php } else { ?>
		<input type="hidden" name="event-placedcountry" id="event-placedcountry" value="<?php $jes_adata[ 'jes_events_countryopt_def' ] ?>" />		
	<?php } ?>

	<?php if ($jes_adata[ 'jes_events_stateopt_enable' ]) { ?>
		<label for="event-placedstate"><?php _e('* Event State', 'jet-event-system') ?> <?php _e( '(required)', 'jet-event-system' )?>
		<input type="text" name="event-placedstate" id="event-placedstate" size="15" maxlength="25" value="<?php bp_new_event_placedstate() ?>" /></label>
	<?php } else { ?>
		<input type="hidden" name="event-placedstate" id="event-placedstate" value="<?php $jes_adata[ 'jes_events_stateopt_def' ] ?>" />		
	<?php } ?>		
	<?php if ($jes_adata[ 'jes_events_cityopt_enable' ]) { ?>					
		<label for="event-placedcity"><?php _e('* Event City', 'jet-event-system') ?> <?php _e( '(required)', 'jet-event-system' )?>
		<input type="text" name="event-placedcity" id="event-placedcity" size="15" maxlength="25" value="<?php bp_new_event_placedcity() ?>" /></label>
	<?php } else { ?>
		<input type="hidden" name="event-placedcity" id="event-placedcity" value="<?php $jes_adata[ 'jes_events_cityopt_def' ] ?>" />		
	<?php } ?>	
		<label for="event-placedaddress"><?php _e('Event address', 'jet-event-system') ?></label>
		<input type="text" name="event-placedaddress" id="event-placedaddress" size="40" maxlength="70" value="<?php bp_new_event_placedaddress() ?>" />

	<?php if ($jes_adata[ 'jes_events_noteopt_enable' ]) { ?>
		<label for="event-placednote"><?php _e('Event note', 'jet-event-system') ?></label>
		<input type="text" name="event-placednote" id="event-placednote" size="40" maxlength="70" value="<?php bp_new_event_placednote() ?>" />
	<?php } else { ?>
		<input type="hidden" name="event-placednote" id="event-placednote" size="25" maxlength="40" value="<?php bp_new_event_placednote() ?>" />	
	<?php } ?>
	</td>
			<td width="50%" style="vertical-align:top;">	
		<?php if ($jes_adata['jes_events_publicnews_enable'] || $jes_adata['jes_publicnews_enable']) { ?>	
			<h4><?php _e('News for event','jet-event-system') ?></h4>					
		<?php } ?>
		<?php if ($jes_adata['jes_events_publicnews_enable']) { ?>
			<label for="event-newspublic"><?php _e('Event Public news', 'jet-event-system') ?></label>
			<textarea name="event-newspublic" id="event-newspublic"><?php bp_new_event_newspublic() ?></textarea>
		<?php } ?>
		<?php if ($jes_adata['jes_events_privatenews_enable']) { ?>
			<label for="event-newsprivate"><?php _e('Private Event News', 'jet-event-system') ?></label>
			<textarea name="event-newsprivate" id="event-newsprivate"><?php bp_new_event_newsprivate() ?></textarea>
		<?php } ?>	
		<?php if ($jes_adata['jes_events_specialconditions_enable']) { ?> 				
				<label for="event-eventterms"><h4><?php _e('Special Conditions', 'jet-event-system') ?></h4></label>
				<textarea name="event-eventterms" id="event-eventterms"><?php bp_new_event_eventterms() ?></textarea>
		<?php } ?>

<?php	/* Google Map */ ?>
	<?php if ($jes_adata[ 'jes_events_googlemapopt_enable' ]) {
			if ($jes_adata[ 'jes_events_googlemapopt_type' ] == 'image') { ?>
		<label for="event-placedgooglemap"><?php _e('Link to Google Maps or any other image', 'jet-event-system') ?></label> */ ?>
		<input type="text" name="event-placedgooglemap" id="event-placedgooglemap" size="50" maxlength="250" value="<?php bp_new_event_placedgooglemap() ?>" />
	<?php } else { ?>
		<input type="hidden" name="event-placedgooglemap" id="event-placedgooglemap" size="50" maxlength="250" value="<?php bp_new_event_placedgooglemap() ?>" />
	<?php }	?>		
	<?php } else { ?>
		<input type="hidden" name="event-placedgooglemap" id="event-placedgooglemap" size="50" maxlength="250" value="<?php bp_new_event_placedgooglemap() ?>" />	
	<?php } ?>

	<?php if ($jes_adata[ 'jes_events_flyeropt_enable' ]) { ?>
		<label for="event-flyer"><?php _e('Link to image flyer', 'jet-event-system') ?></label>
		<input type="text" name="event-flyer" id="event-flyer" size="50" maxlength="250" value="<?php bp_new_event_flyer() ?>" />
	<?php } else { ?>
		<input type="hidden" name="event-flyer" id="event-flyer" size="50" maxlength="250" value="<?php bp_new_event_flyer() ?>" />	
	<?php } ?>
			</td>
		</tr>
<!-- Data -->		
		<tr>
			<td width="50%" style="vertical-align:top;">
				<h4><?php _e('Event Date','jet-event-system') ?></h4>
					<table>
						<tr>
							<td width="49%">
								<label for="event-edtsd"><?php _e('* Event Start date', 'jet-event-system') ?><br /> <?php _e( '(required)', 'jet-event-system' )?></label>
							</td>
							<td>
								<input type="text" readonly name="event-edtsd" id="event-edtsd" size="12" maxlength="20" value="<?php bp_new_event_edtsd() ?>" /><br />
								<select name="event-edtsth" id="event-edtsth" size=1>
								<?php
									for ($i=0; $i<24; $i++) { ?>
									<?php if ($i<10)
										{ $codei = '0'.$i; }
											else
										{ $codei = $i; } ?>
										<option value="<?php echo $codei;?>"><?php echo $codei; ?></option>
								<?php } ?>
								</select><?php _e(' hours','jet-event-system'); ?>
								<select name="event-edtstm" id="event-edtstm" size=1>
								<?php for ($i=0;$i<60;$i=$i+5)
									{ ?>
									<?php if ($i<10)
										{ $codei = '0'.$i; }
											else
										{ $codei = $i; } ?>
								<option value="<?php echo $codei; ?>"><?php echo $codei; ?></option>
								<?php } ?>
								</select><?php _e(' minutes','jet-event-system'); ?>
							</td>
						</tr>
					</table>
			</td>
			<td width="50%" style="vertical-align:bottom;">
					<table>
						<tr>
							<td width="49%">
								<label for="event-edted"><?php _e('* Event End date', 'jet-event-system') ?><br /> <?php _e( '(required)', 'jet-event-system' )?></label>
							</td>
							<td>
								<input type="text" readonly name="event-edted" id="event-edted" size="12" maxlength="20" value="<?php bp_new_event_edted() ?>" /><br />
								<select name="event-edteth" id="event-edteth" size=1>
								<?php
									for ($i=0; $i<24; $i++) { ?>
									<?php if ($i<10)
										{ $codei = '0'.$i; }
											else
										{ $codei = $i; } ?>
										<option value="<?php echo $codei;?>"><?php echo $codei; ?></option>
								<?php } ?>
								</select><?php _e(' hours','jet-event-system'); ?>
								<select name="event-edtetm" id="event-edtetm" size=1>
								<?php for ($i=0;$i<60;$i=$i+5)
									{ ?>
									<?php if ($i<10)
										{ $codei = '0'.$i; }
											else
										{ $codei = $i; } ?>
								<option value="<?php echo $codei; ?>"><?php echo $codei; ?></option>
								<?php } ?>
								</select><?php _e(' minutes','jet-event-system'); ?>
							</td>
						</tr>
					</table>
			</td>
		</tr>
<!-- Data -->
<tr>
	<td>
<?php
/* Notify */

$notaccessrem = 0;

if ($jes_adata[ 'jes_events_notifymembers_enable' ] == 'none' )
	{
		$notaccessrem = 1;
	} else
	{
		if ($jes_adata[ 'jes_events_notifymembers_enable' ] == 'admin' )
			{
				if ( current_user_can('manage_options') )
					{
						$notaccessrem = 1;
					}
			} else
			{
				if ($jes_adata[ 'jes_events_notifymembers_enable' ] == 'user' )
					{
						$notaccessrem = 1;
					} else
					{
						$notaccessrem = 0;
					}
			}
	}

	if ($notaccessrem)
		{ ?>
<h4><?php _e( 'Reminder', 'jet-event-system' ); ?></h4>
		<label for="notifytimedenable"><?php echo sprintf ( __('Remind participants about the early events in %s hours?','jet-event-system'),$jes_adata[ 'jes_events_notify_timed' ] ); ?></label>
				<select name="notifytimedenable" id="notifytimedenable">
					<option value="1"><?php _e('Yes','jet-event-system'); ?></option>
					<option selected value="0"><?php _e('No','jet-event-system'); ?></option>
				</select>
<?php 	} 
			else
		{ ?>
			<input id="notifytimedenable" name="notifytimedenable" type="hidden">
<?php	}
	?>
	</td>
</tr>
<!-- Notify -->		
</table>
					<?php do_action( 'bp_after_event_details_creation_step' ); ?>

					<?php wp_nonce_field( 'events_create_save_event-details' ) ?>

				<?php endif; ?>

				<?php /* Event creation step 2: Event settings */ ?>
				<?php if ( jes_is_event_creation_step( 'event-settings' ) ) : ?>

					<?php do_action( 'bp_before_event_settings_creation_step' ); ?>

					<h4><?php _e( 'Privacy Options', 'jet-event-system' ); ?></h4>

					<div class="radio">
						<label><input type="radio" name="event-status" value="public"<?php if ( 'public' == bp_get_new_event_status() || !bp_get_new_event_status() ) { ?> checked="checked"<?php } ?> />
							<strong><?php _e( 'This is a public event', 'jet-event-system' ) ?></strong>
							<ul>
								<li><?php _e( 'Any site member can join this event.', 'jet-event-system' ) ?></li>
								<li><?php _e( 'This event will be listed in the events directory and in search results.', 'jet-event-system' ) ?></li>
								<li><?php _e( 'Event content and activity will be visible to any site member.', 'jet-event-system' ) ?></li>
							</ul>
						</label>

						<label><input type="radio" name="event-status" value="private"<?php if ( 'private' == bp_get_new_event_status() ) { ?> checked="checked"<?php } ?> />
							<strong><?php _e( 'This is a private event', 'jet-event-system' ) ?></strong>
							<ul>
								<li><?php _e( 'Just send a request to join, users can join the event.', 'jet-event-system' ) ?></li>
								<li><?php _e( 'This event will be listed in the events directory and in search results.', 'jet-event-system' ) ?></li>
								<li><?php _e( 'Event content and activity will only be visible to members of the event.', 'jet-event-system' ) ?></li>
							</ul>
						</label>

						<label><input type="radio" name="event-status" value="hidden"<?php if ( 'hidden' == bp_get_new_event_status() ) { ?> checked="checked"<?php } ?> />
							<strong><?php _e('This is a hidden event', 'jet-event-system') ?></strong>
							<ul>
								<li><?php _e( 'Only users who are invited can join the event.', 'jet-event-system' ) ?></li>
								<li><?php _e( 'This event will not be listed in the events directory or search results.', 'jet-event-system' ) ?></li>
								<li><?php _e( 'Event content and activity will only be visible to members of the event.', 'jet-event-system' ) ?></li>
							</ul>
						</label>
					</div>
	
				<h4><?php _e( 'Group/Forum link options', 'jet-event-system' ); ?></h4>					
					<label><?php _e('Select the group you want to contact the event:','jet-event-system'); ?></label>
					<?php jes_event_groups_dropdown( $_POST['event-grouplink'] ) ?>

	<input type="hidden" name="event-forumlink" id="event-forumlink" value="1" />
										
					<?php do_action( 'bp_after_event_settings_creation_step' ); ?>

					<?php wp_nonce_field( 'events_create_save_event-settings' ) ?>

				<?php endif; ?>

				<?php /* Event creation step 3: Avatar Uploads */ ?>
				<?php if ( jes_is_event_creation_step( 'event-avatar' ) ) : ?>

					<?php do_action( 'bp_before_event_avatar_creation_step' ); ?>

					<?php if ( !bp_get_avatar_admin_step() ) : ?>

						<div class="left-menu">
							<?php bp_new_event_avatar() ?>
						</div><!-- .left-menu -->

						<div class="main-column">
							<p><?php _e("Upload an image to use as an avatar for this event. The image will be shown on the main event page, and in search results.", 'jet-event-system') ?></p>

							<p>
								<input type="file" name="file" id="file" />
								<input type="submit" name="upload" id="upload" value="<?php _e( 'Upload Image', 'jet-event-system' ) ?>" />
								<input type="hidden" name="action" id="action" value="bp_avatar_upload" />
							</p>

							<p><?php _e( 'To skip the avatar upload process, hit the "Next Step" button.', 'jet-event-system' ) ?></p>
						</div><!-- .main-column -->

					<?php endif; ?>

					<?php if ( 'crop-image' == bp_get_avatar_admin_step() ) : ?>

						<h3><?php _e( 'Crop Event Avatar', 'jet-event-system' ) ?></h3>

						<img src="<?php bp_avatar_to_crop() ?>" id="avatar-to-crop" class="avatar" alt="<?php _e( 'Avatar to crop', 'jet-event-system' ) ?>" />

						<div id="avatar-crop-pane">
							<img src="<?php bp_avatar_to_crop() ?>" id="avatar-crop-preview" class="avatar" alt="<?php _e( 'Avatar preview', 'jet-event-system' ) ?>" />
						</div>

						<input type="submit" name="avatar-crop-submit" id="avatar-crop-submit" value="<?php _e( 'Crop Image', 'jet-event-system' ) ?>" />

						<input type="hidden" name="image_src" id="image_src" value="<?php bp_avatar_to_crop_src() ?>" />
						<input type="hidden" name="upload" id="upload" />
						<input type="hidden" id="x" name="x" />
						<input type="hidden" id="y" name="y" />
						<input type="hidden" id="w" name="w" />
						<input type="hidden" id="h" name="h" />

					<?php endif; ?>

					<?php do_action( 'bp_after_event_avatar_creation_step' ); ?>

					<?php wp_nonce_field( 'events_create_save_event-avatar' ) ?>

				<?php endif; ?>

				<?php /* Event creation step 4: Invite friends to event */ ?>
				<?php if ( jes_is_event_creation_step( 'event-invites' ) ) : ?>

					<?php do_action( 'bp_before_jes_event_invites_creation_step' ); ?>

					<?php if ( function_exists( 'bp_get_total_friend_count' ) && bp_get_total_friend_count( bp_loggedin_user_id() ) ) : ?>
						<div class="left-menu">

							<div id="invite-list">
								<ul>
									<?php bp_new_jes_event_invite_friend_list() ?>
								</ul>

								<?php wp_nonce_field( 'events_invite_uninvite_user', '_wpnonce_invite_uninvite_user' ) ?>
							</div>

						</div><!-- .left-menu -->

						<div class="main-column">

							<div id="message" class="info">
								<p><?php _e('Select people to invite from your friends list.', 'jet-event-system'); ?></p>
							</div>

							<?php /* The ID 'friend-list' is important for AJAX support. */ ?>
							<ul id="friend-list" class="item-list">
							<?php if ( bp_event_has_invite_jes() ) : ?>

								<?php while ( bp_jes_event_invite_jes() ) : bp_event_the_invite(); ?>

									<li id="<?php bp_jes_event_invite_item_id() ?>">
										<?php bp_jes_event_invite_user_avatar() ?>

										<h4><?php bp_jes_event_invite_user_link() ?></h4>
										<span class="activity"><?php bp_jes_event_invite_user_last_active() ?></span>

										<div class="action">
											<a class="remove" href="<?php bp_jes_event_invite_user_remove_invite_url() ?>" id="<?php bp_jes_event_invite_item_id() ?>"><?php _e( 'Remove Invite', 'jet-event-system' ) ?></a>
										</div>
									</li>

								<?php endwhile; ?>

								<?php wp_nonce_field( 'events_send_invites', '_wpnonce_send_invites' ) ?>
							<?php endif; ?>
							</ul>

						</div><!-- .main-column -->

					<?php else : ?>

						<div id="message" class="info">
							<p><?php _e( 'Once you have built up friend connections you will be able to invite others to your event. You can send invites any time in the future by selecting the "Send Invites" option when viewing your new event.', 'jet-event-system' ); ?></p>
						</div>

					<?php endif; ?>

					<?php wp_nonce_field( 'events_create_save_event-invites' ) ?>
					<?php do_action( 'bp_after_jes_event_invites_creation_step' ); ?>

				<?php endif; ?>

				<?php do_action( 'events_custom_create_steps' ) // Allow plugins to add custom event creation steps ?>

				<?php do_action( 'bp_before_event_creation_step_buttons' ); ?>

				<?php if ( 'crop-image' != bp_get_avatar_admin_step() ) : ?>
					<div class="submit" id="previous-next">
						<?php /* Previous Button */ ?>
						<?php if ( !bp_is_first_event_creation_step() ) : ?>
							<input type="button" value="&larr; <?php _e('Previous Step', 'jet-event-system') ?>" id="event-creation-previous" name="previous" onclick="location.href='<?php bp_event_creation_previous_link() ?>'" />
						<?php endif; ?>

						<?php /* Next Button */ ?>
						<?php if ( !bp_is_last_event_creation_step() && !bp_is_first_event_creation_step() ) : ?>
							<input type="submit" value="<?php _e('Next Step', 'jet-event-system') ?> &rarr;" id="event-creation-next" name="save" />
						<?php endif;?>

						<?php /* Create Button */ ?>
						<?php if ( bp_is_first_event_creation_step() ) : ?>
							<input type="submit" onClick="CreateGeoCode()" value="<?php _e('Create Event and Continue', 'jet-event-system') ?> &rarr;" id="event-creation-create" name="save" />
						<?php endif; ?>

						<?php /* Finish Button */ ?>
						<?php if ( bp_is_last_event_creation_step() ) : ?>
							<input type="submit" value="<?php _e('Finish', 'jet-event-system') ?> &rarr;" id="event-creation-finish" name="save" />
						<?php endif; ?>
					</div>
				<?php endif;?>

				<?php do_action( 'bp_after_event_creation_step_buttons' ); ?>

				<?php /* Don't leave out this hidden field */ ?>
				<input type="hidden" name="event_id" id="event_id" value="<?php bp_new_event_id() ?>" />

				<?php do_action( 'bp_directory_events_content' ) ?>

			</div><!-- .item-body -->

			<?php do_action( 'bp_after_create_event' ) ?>

		</form>

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer() ?>
