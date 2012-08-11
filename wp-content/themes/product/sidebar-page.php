
<div class="shadow-spacer"></div>
<div id="sidebar">
	<div class="padder">
		<div class="sidebar-box">
						<?php if ( is_active_sidebar( 'pageleft-sidebar' ) ) : ?>
								<?php dynamic_sidebar( 'pageleft-sidebar' ); ?>
									<?php else : ?>
									<div class="widget-error">
									<?php _e( 'Please log in and add widgets to this column.', 'product' ) ?> <a href="<?php echo get_option('siteurl') ?>/wp-admin/widgets.php?s=&amp;show=&amp;sidebar=pageleft-sidebar"><?php _e( 'Add Widgets', 'product' ) ?></a>
									</div>
								<?php endif; ?>
		</div>

			<div class="sidebar-box">
							<?php if ( is_active_sidebar( 'pagemiddle-sidebar' ) ) : ?>
									<?php dynamic_sidebar( 'pagemiddle-sidebar' ); ?>
										<?php else : ?>
										<div class="widget-error">
										<?php _e( 'Please log in and add widgets to this column.', 'product' ) ?> <a href="<?php echo get_option('siteurl') ?>/wp-admin/widgets.php?s=&amp;show=&amp;sidebar=pagemiddle-sidebar"><?php _e( 'Add Widgets', 'product' ) ?></a>
										</div>
									<?php endif; ?>		</div>
					<div id="login-box">
						<?php if ( !function_exists('dynamic_sidebar')
						|| !dynamic_sidebar('pageright-sidebar') ) : ?>
									<div class="widget-error">
										<?php _e( 'Please log in and add widgets to this column.', 'product' ) ?> <a href="<?php echo get_option('siteurl') ?>/wp-admin/widgets.php?s=&amp;show=&amp;sidebar=pageright-sidebar"><?php _e( 'Add Widgets', 'product' ) ?></a>
									</div>
						<?php endif; ?>			</div>
						<div class="clear"></div>
	</div><!-- .padder -->
</div><!-- #sidebar -->
<div class="shadow-spacer"></div>