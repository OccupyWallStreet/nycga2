
<div class="shadow-spacer"></div>
<div id="sidebar">
	<div class="padder">
				<div class="sidebar-box">
												<?php if ( is_active_sidebar( 'blogleft-sidebar' ) ) : ?>
														<?php dynamic_sidebar( 'blogleft-sidebar' ); ?>
													<?php else : ?>
																<div class="widget-error">
																	<?php _e( 'Please log in and add widgets to this column.', 'product' ) ?> <a href="<?php echo get_option('siteurl') ?>/wp-admin/widgets.php?s=&amp;show=&amp;sidebar=blogleft-sidebar"><?php _e( 'Add Widgets', 'product' ) ?></a>
																</div>
												<?php endif; ?>
				</div>

					<div class="sidebar-box">
							<?php if ( is_active_sidebar( 'blogmiddle-sidebar' ) ) : ?>
									<?php dynamic_sidebar( 'blogmiddle-sidebar' ); ?>
								<?php else : ?>
										<div class="widget-error">
											<?php _e( 'Please log in and add widgets to this column.', 'product' ) ?> <a href="<?php echo get_option('siteurl') ?>/wp-admin/widgets.php?s=&amp;show=&amp;sidebar=blogmiddle-sidebar"><?php _e( 'Add Widgets', 'product' ) ?></a>
										</div>
							<?php endif; ?>
						</div>
							<div id="login-box">
								<?php if ( is_active_sidebar( 'blogright-sidebar' ) ) : ?>
										<?php dynamic_sidebar( 'blogright-sidebar' ); ?>
									<?php else : ?>
																		<div class="widget-error">
																			<?php _e( 'Please log in and add widgets to this column.', 'product' ) ?> <a href="<?php echo get_option('siteurl') ?>/wp-admin/widgets.php?s=&amp;show=&amp;sidebar=blogright-sidebar"><?php _e( 'Add Widgets', 'product' ) ?></a>
																		</div>
								<?php endif; ?>		
						    </div>
		<div class="clear"></div>
	</div><!-- .padder -->
</div><!-- #sidebar -->	
	<div class="shadow-spacer"></div>