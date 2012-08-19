<?php
$data = $this->get_updates();

if (!empty($_POST) && $this->allowed_user()) {
	if ( $data['membership'] == 'full' ) { //free member
		update_site_option('wdp_un_hide_upgrades', $_POST['hide_upgrades']);
		update_site_option('wdp_un_hide_notices', $_POST['hide_notices']);
		update_site_option('wdp_un_hide_releases', $_POST['hide_releases']);
	} else if ( is_numeric( $data['membership'] ) ) { //single
		update_site_option('wdp_un_hide_upgrades', $_POST['hide_upgrades']);
	}
	
	//limit to whoever saves the settings
	if ($this->get_apikey())
		update_site_option('wdp_un_limit_to_user', $current_user->ID);
		
	?><div class="updated fade"><p><?php _e('Settings Saved!', 'wpmudev'); ?></p></div><?php
}


$profile = $this->get_profile();

if ( $this->get_apikey() && ($data['membership'] == 'full' || is_numeric($data['membership'])) && isset($data['downloads']) && $data['downloads'] != 'enabled' ) {
	?><div class="error fade"><p><?php _e('You have reached your maximum enabled sites for automatic updates, one-click installations, and direct support through the WPMU DEV Dashboard plugin. You may <a href="http://premium.wpmudev.org/wp-admin/profile.php?page=wdpun">change which sites are enabled or upgrade to a higher membership level here &raquo;</a>', 'wpmudev'); ?></p></div><?php
}
?>
<hr class="section-head-divider" />
<div class="wrap grid_container">
<h1 class="section-header"><i class="icon-cogs"></i><?php _e('Manage', 'wpmudev') ?></h1>

<?php if ( $users = $this->get_allowed_users() ) { ?>
<div id="allowed-users"><?php echo $users; ?></div>
<span class="tooltip" id="allowed-users-tooltip"><i class="icon-info-sign icon-large"></i>
	<section>
		<?php _e('To change which users can have access to the WPMU DEV Dashboard, place this line in your wp-config.php file with the desired userids:', 'wpmudev'); ?>
		<br /><code>define('WPMUDEV_LIMIT_TO_USER', '<?php echo $current_user->ID; ?>');</code> <?php _e('or', 'wpmudev') ?>
		<br /><code>define('WPMUDEV_LIMIT_TO_USER', '<?php echo $current_user->ID; ?>, 12');</code>
	</section>
</span>
<?php } ?>

<form action="" method="post">
<div class="section-contents" id="settings">
	
	<?php if (isset($profile['profile']) && $this->get_apikey() && !defined( 'WPMUDEV_APIKEY') && $this->allowed_user()) { ?>
	<h2><?php _e('Your Membership Details', 'wpmudev') ?>
		<span class="tooltip"><i class="icon-question-sign icon-large"></i>
			<section>
				<?php _e('If you need to hide this information, place this line in your wp-config.php file:', 'wpmudev'); ?><br /><code>define('WPMUDEV_APIKEY', '<?php echo $this->get_apikey(); ?>');</code>
			</section>
		</span>
	</h2>
	<div id="membership-details">
		<h3><?php _e('Your WPMU DEV API Key', 'wpmudev') ?></h3>
		<span class="description">
			<?php echo $this->get_apikey(); ?>
			<small>
				&nbsp;&nbsp;<a href="<?php echo $this->dashboard_url; ?>&clear_key=1" title="<?php _e('Change your API key', 'wpmudev') ?>"><?php _e('EDIT', 'wpmudev') ?> <i class="icon-pencil icon-large"></i></a>
			</small>
		</span>
	</div>
	<div id="subscription-info"><h3><?php echo $profile['profile']['subscription']; ?></h3></div>
		<br /><a class="button" href="https://premium.wpmudev.org/membership/"><i class="icon-edit icon-large"></i><?php _e('MODIFY MEMBERSHIP', 'wpmudev') ?></a>
	</div>
	<div class="clear"></div>
	<?php } ?>
	
	<?php if ( !$this->get_apikey() ) { ?>
	<h3><?php _e('Please enter your API Key to enable settings:', 'wpmudev') ?>
		<small>
			&nbsp;&nbsp;<a href="<?php echo $this->dashboard_url; ?>" title="<?php _e('Add your API key', 'wpmudev') ?>"><?php _e('ADD', 'wpmudev') ?> <i class="icon-pencil icon-large"></i></a>
		</small>
	</h3>
	<?php } ?>
	
	<h2><?php _e('Admin Notices', 'wpmudev') ?></h2>
	<div class="inside">
		<span class="description"><?php _e('Notices are only displayed to site Administrators (Super-Admins in Multisite installs). Full & current WPMU DEV members can permanently disable all admin notices, though individual notices can always be dismissed by any admin.', 'wpmudev') ?></span>
		<table class="form-table">
			<tbody>
				<tr>
					<td width="13.31%" class="option-label"><?php _e('Upgrade<br />notice', 'wpmudev') ?></td>
					<td width="4%">&nbsp;</td>
					<td width="30.6%">
						<?php
							$disable = '';
							if ( ($data['membership'] != 'full' && !is_numeric($data['membership'])) || !$this->allowed_user())
								$disable = ' disabled="disabled"';
							$checked = (get_site_option('wdp_un_hide_upgrades')) ? 1 : 0;
						?>
						<label><input value="0"<?php echo $disable; ?> name="hide_upgrades" type="radio" <?php checked($checked, 0) ?> /> <?php _e('Show', 'wpmudev') ?></label>
						<label><input value="1"<?php echo $disable; ?> name="hide_upgrades" type="radio" <?php checked($checked, 1) ?> /> <?php _e('Hide', 'wpmudev') ?></label>
						<?php if ($disable) { ?><span class="description"><i><?php _e('Only current WPMU DEV members <br />can hide the upgrade notice', 'wpmudev') ?></i></span><?php } ?>
					</td>
					<td width="17.3%" class="option-label"><?php _e('New release<br />announcements', 'wpmudev') ?></td>
					<td width="4%">&nbsp;</td>
					<td width="30.6%" valign="top">
						<?php
							$disable = '';
							if ( $data['membership'] != 'full' || !$this->allowed_user() )
								$disable = ' disabled="disabled"';
							$checked = (get_site_option('wdp_un_hide_releases')) ? 1 : 0;
						?>
						<label><input value="0"<?php echo $disable; ?> name="hide_releases" type="radio" <?php checked($checked, 0) ?> /> <?php _e('Show', 'wpmudev') ?></label>
						<label><input value="1"<?php echo $disable; ?> name="hide_releases" type="radio" <?php checked($checked, 1) ?> /> <?php _e('Hide', 'wpmudev') ?></label>
						<?php if ($disable) { ?><span class="description"><i><?php _e('Only full WPMU DEV members can hide new release announcements', 'wpmudev') ?></i></span><?php } ?>
					</td>
				</tr>
				<tr><td colspan="6" height="20">&nbsp;</td></tr>
				<tr>
					<td class="option-label"><?php _e('Special<br />offers', 'wpmudev') ?></td>
					<td>&nbsp;</td>
					<td>
						<?php
							$disable = '';
							if ( $data['membership'] != 'full' || !$this->allowed_user() )
								$disable = ' disabled="disabled"';
							$checked = (get_site_option('wdp_un_hide_notices')) ? 1 : 0;
						?>
						<label><input value="0"<?php echo $disable; ?> name="hide_notices" type="radio" <?php checked($checked, 0) ?> /> <?php _e('Show', 'wpmudev') ?></label>
						<label><input value="1"<?php echo $disable; ?> name="hide_notices" type="radio" <?php checked($checked, 1) ?> /> <?php _e('Hide', 'wpmudev') ?></label>
						<?php if ($disable) { ?><span class="description"><i><?php _e('Only full WPMU DEV members can hide <br />special offers', 'wpmudev') ?></i></span><?php } ?>
					</td>
					<td class="option-label"><?php _e('Dashboard<br />widgets', 'wpmudev') ?></td>
					<td>&nbsp;</td>
					<td><span class="description"><i><?php _e('You may hide the WPMU DEV dashboard widgets via the "Screen Options" dropdown on the dashboard.', 'wpmudev') ?></i></span></td>
				</tr>
			</tbody>
		</table>
		<p class="submit">
			<input type="submit" class="button-primary" name="Submit" value="<?php _e('SAVE CHANGES', 'wpmudev') ?>" />
		</p>
	</div>

</div>
		
</div>
</form>
</div>