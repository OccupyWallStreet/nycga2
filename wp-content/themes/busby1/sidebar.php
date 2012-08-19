<div id="right">
			<?php if ( ! dynamic_sidebar( 'sidebar-1' ) ) : ?>
					<h3 class="widgettitle"><?php _e( 'Archives', 'busby' ); ?></h3>  
                    <div class="widget">
					<ul>
						<?php wp_get_archives( array( 'type' => 'monthly' ) ); ?>
					</ul>
				</div>
					<h3 class="widgettitle"><?php _e( 'Meta', 'busby' ); ?></h2>
					  <div class="widget">
                    <ul>
						<?php wp_register(); ?>
						<aside><?php wp_loginout(); ?></aside>
						<?php wp_meta(); ?>
					</ul>
</div>

			<?php endif; // end sidebar widget area ?>
		</div>

		<?php if ( is_active_sidebar( 'sidebar-2' ) ) : ?>
		<div id="tertiary" class="widget-area" role="complementary">
			<?php dynamic_sidebar( 'sidebar-2' ); ?>
		</div>
		<?php endif; ?>

</div>