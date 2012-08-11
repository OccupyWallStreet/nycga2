	<div id="widgets"><!-- start #widget one -->
		<div class="widget-section">
			<?php if ( is_active_sidebar( 'sidebar-one' ) ) : ?>
					<?php dynamic_sidebar( 'sidebar-one' ); ?>
			<?php endif; ?>
		</div>
			<div class="widget-section">
				<?php if ( is_active_sidebar( 'sidebar-two' ) ) : ?>
						<?php dynamic_sidebar( 'sidebar-two' ); ?>
				<?php endif; ?>
			</div>
				<div class="widget-section">
					<?php if ( is_active_sidebar( 'sidebar-three' ) ) : ?>
							<?php dynamic_sidebar( 'sidebar-three' ); ?>
					<?php endif; ?>
				</div>
					<div class="widget-section-end">
						<?php if ( is_active_sidebar( 'sidebar-four' ) ) : ?>
								<?php dynamic_sidebar( 'sidebar-four' ); ?>
						<?php endif; ?>
					</div>
							<div class="clear"></div>
						</div>