<div class="wrap">
	<h2><?php _e('Ultimate Facebook Widget Pack', 'wdfb');?></h2>

<div class="message important"><?php _e('Turning a widget on here will make it available in your widget settings.');?></div>

<?php if (WP_NETWORK_ADMIN) { ?>
	<form action="settings.php" method="post">
<?php } else { ?>
	<form action="admin.php" method="post">
<?php } ?>

	<?php settings_fields('wdfb_widgets'); ?>
<div id="wdtg_accordion">
	<?php do_settings_sections('wdfb_widget_options_page'); ?>
</div>
	<p class="submit">
		<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
	</p>
	</form>
</div>