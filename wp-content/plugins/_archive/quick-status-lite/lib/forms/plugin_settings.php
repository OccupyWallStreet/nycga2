<div class="wrap">
	<h2><?php _e('Status settings', 'wdqs');?></h2>
<div class="error below-h2"><p><a title="Upgrade Now" href="http://premium.wpmudev.org/project/quick-status">Upgrade to Pro version to enable additional features</a></p></div>
<?php if (WP_NETWORK_ADMIN) { ?>
	<form action="settings.php" method="post">
<?php } else { ?>
	<form action="options.php" method="post">
<?php } ?>

	<?php settings_fields('wdqs'); ?>
	<?php do_settings_sections('wdqs_options_page'); ?>
	<p class="submit">
		<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
	</p>
	</form>

</div>