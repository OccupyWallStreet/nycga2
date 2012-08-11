	<?php include (get_template_directory() . '/library/options/options.php'); ?>
		
	<?php 
	$contentshow = get_option('dev_product_feature_show');{
		if ($contentshow == "yes"){
				?>
				<div class="shadow-spacer"></div>
				<?php				
		}
		else if ($contentshow == "no"){
		}
		else{
			?>
			<div class="shadow-spacer"></div>
			<?php			
		}
	}		
?>
<div id="sidebar">
	<div class="padder">
			<div class="sidebar-box">
								<?php if ( is_active_sidebar( 'homeleft-sidebar' ) ) : ?>
										<?php dynamic_sidebar( 'homeleft-sidebar' ); ?>
											<?php else : ?>
											<div class="widget-error">
											<?php _e( 'Please log in and add widgets to this column.', 'product' ) ?> <a href="<?php echo get_option('siteurl') ?>/wp-admin/widgets.php?s=&amp;show=&amp;sidebar=homeleft-sidebar"><?php _e( 'Add Widgets', 'product' ) ?></a>
											</div>
										<?php endif; ?>
			</div>

				<div class="sidebar-box">
								<?php if ( is_active_sidebar( 'homemiddle-sidebar' ) ) : ?>
										<?php dynamic_sidebar( 'homemiddle-sidebar' ); ?>
									<?php else : ?>
											<div class="widget-error">
												<?php _e( 'Please log in and add widgets to this column.', 'product' ) ?> <a href="<?php echo get_option('siteurl') ?>/wp-admin/widgets.php?s=&amp;show=&amp;sidebar=homemiddle-sidebar"><?php _e( 'Add Widgets', 'product' ) ?></a>
											</div>
								<?php endif; ?>	
						</div>
						<div id="login-box">
							<?php if ( !function_exists('dynamic_sidebar')
							|| !dynamic_sidebar('homeright-sidebar') ) : ?>
										<div class="widget-error">
											<?php _e( 'Please log in and add widgets to this column.', 'product' ) ?> <a href="<?php echo get_option('siteurl') ?>/wp-admin/widgets.php?s=&amp;show=&amp;sidebar=homeright-sidebar"><?php _e( 'Add Widgets', 'product' ) ?></a>
										</div>
							<?php endif; ?>			</div>
							<div class="clear"></div>
	</div><!-- .padder -->
</div><!-- #sidebar -->	
	<div class="shadow-spacer"></div>