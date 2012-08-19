<div class="wrap">
	<h2>Google Maps Plugin Options</h2>
<div class="error below-h2"><p><a title="Upgrade Now" href="http://premium.wpmudev.org/project/wordpress-google-maps-plugin">Upgrade to Google Maps Pro to enable additional features</a></p></div>

<p>The Google Map plugin adds a “Add Map” icon to your visual editor. &nbsp;Once you’ve created your new map it is inserted into write Post / Page area as shortcode which looks like this: [map id="1"].</p>
<p>It also adds a widget so you can add maps to your sidebar (see Appearance &gt; Widgets).</p>
<?php if (!is_multisite()) { ?>
	<p>For more detailed instructions on how to use refer to <a target="_blank" href="http://premium.wpmudev.org/project/wordpress-google-maps-plugin/installation/">Google Maps Installation and Use instructions</a>.</p>
<?php } ?>

	<form action="options.php" method="post">
	<?php settings_fields('agm_google_maps'); ?>
	<?php do_settings_sections('agm_google_maps_options_page'); ?>
	<div class="error below-h2"><p><a title="Upgrade Now" href="http://premium.wpmudev.org/project/wordpress-google-maps-plugin">Upgrade to Google Maps Pro to enable editing these fields</a></p></div>
	<p class="submit">
		<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
	</p>
	</form>
</div>