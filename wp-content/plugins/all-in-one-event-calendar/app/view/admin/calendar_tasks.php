<div class="timely">
	<div class="hero-unit">
		<h1><?php _e( 'Welcome', AI1EC_PLUGIN_NAME ); ?></h1>
		<p><?php _e( 'to the All-in-One Event Calendar by <a href="http://time.ly/" target="_blank">Timely</a>', AI1EC_PLUGIN_NAME ); ?></p>
	</div>

	<div class="clearfix">
		<div class="row-fluid">
			<?php if( $add_allowed ): ?>
				<div class="span6">
					<p>
						<a class="btn btn-primary btn-large" href="<?php echo esc_attr( $add_url ); ?>">
							<i class="icon-plus"></i>&nbsp;&nbsp;<?php _e( 'Post Your Event', AI1EC_PLUGIN_NAME ); ?>
						</a>
					</p>
					<strong><?php _e( 'Add a new event to the calendar.', AI1EC_PLUGIN_NAME ); ?></strong>
				</div>
			<?php endif; ?>

			<?php if( $edit_allowed ): ?>
				<div class="span6">
					<p>
						<a class="btn btn-primary btn-large" href="<?php echo esc_attr( $edit_url ); ?>">
							<i class="icon-pencil"></i>&nbsp;&nbsp;<?php _e( 'Manage Events', AI1EC_PLUGIN_NAME ); ?>
						</a>
					</p>
					<strong><?php _e( 'View and edit all your events.', AI1EC_PLUGIN_NAME ); ?></strong>
				</div>
			<?php endif; ?>
		</div>

		<hr />

		<div class="row-fluid">
			<?php if( $categories_allowed ): ?>
				<div class="span6">
					<p>
						<a class="btn" href="<?php echo esc_attr( $categories_url ); ?>">
							<i class="icon-tags icon-large"></i> <?php _e( 'Manage Event Categories', AI1EC_PLUGIN_NAME ); ?>
						</a>
					</p>
					<p><strong><?php _e( 'Organize and color-code your events.', AI1EC_PLUGIN_NAME ); ?></strong></p>
				</div>
			<?php endif; ?>

			<?php if( $themes_allowed ): ?>
				<div class="span6">
					<p>
						<a class="btn" href="<?php echo esc_attr( $themes_url ); ?>">
							<i class="icon-leaf icon-large"></i> <?php _e( 'Choose Your Theme', AI1EC_PLUGIN_NAME ); ?>
						</a>
					</p>
					<p><strong><?php _e( 'Change the look and feel.', AI1EC_PLUGIN_NAME ); ?></strong></p>
				</div>
			<?php endif; ?>
		</div>

		<div class="row-fluid">
			<?php if( $feeds_allowed ): ?>
				<div class="span6">
					<p>
						<a class="btn" href="<?php echo esc_attr( $feeds_url ); ?>">
							<i class="icon-refresh icon-large"></i> <?php _e( 'Manage Calendar Feeds', AI1EC_PLUGIN_NAME ); ?>
						</a>
					</p>
					<p><strong><?php _e( 'Subscribe to other calendars.', AI1EC_PLUGIN_NAME ); ?></strong></p>
				</div>
			<?php endif; ?>

			<?php if( $settings_allowed ): ?>
				<div class="span6">
					<p>
						<a class="btn" href="<?php echo esc_attr( $settings_url ); ?>">
							<i class="icon-cog icon-large"></i> <?php _e( 'Edit Calendar Settings', AI1EC_PLUGIN_NAME ); ?>
						</a>
					</p>
					<p><strong><?php _e( 'Make this calendar your own.', AI1EC_PLUGIN_NAME ); ?></strong></p>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
