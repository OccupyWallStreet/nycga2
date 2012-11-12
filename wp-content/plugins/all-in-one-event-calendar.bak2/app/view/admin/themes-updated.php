<div class="wrap">

	<?php screen_icon(); ?>

	<h2><?php _e( 'Update Calendar Themes', AI1EC_PLUGIN_NAME ); ?></h2>

	<?php echo $msg; ?>

	<?php if ( $errors ): ?>
		<?php foreach ( $errors as $error ): ?>
			<?php echo $error; ?>
		<?php endforeach; ?>
	<?php endif; ?>

	<div class="updated"><p>
		<?php _e( 'Whenever core files are updated, please be sure to reload your web browser to make sure the most recent versions of the files are used.', AI1EC_PLUGIN_NAME ); ?>
	</p></div>

	<p><a class="button" href="<?php echo AI1EC_SETTINGS_BASE_URL; ?>"><?php _e( 'All-in-One Event Calendar Settings »', AI1EC_PLUGIN_NAME ); ?></a></p>
</div>
