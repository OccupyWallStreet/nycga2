<h2><?php echo $plugin_name; ?></h2>
<?php if ( isset( $plugin_info ) ) : ?>
	<?php echo $plugin_info; ?>
	<br class="clear" />
<?php endif; ?>
<?php foreach ( $plugin_settings as $setting) : ?>
	<?php
	$description = esc_html( $setting['setting-description'] );
	$value = esc_attr( $setting['setting-value'] );
	$id = esc_attr( $setting['setting-id'] );
	?>
	<label class="textinput" for="<?php echo $id; ?>"><?php echo $description; ?></label>
	<input name="<?php echo $id; ?>" id="<?php echo $id; ?>" type="text"  value="<?php echo $value; ?>" />
	<br class="clear" />
<?php endforeach; ?>
