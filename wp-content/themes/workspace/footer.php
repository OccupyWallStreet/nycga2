			</div><!-- #container -->
			<div id="footer-callout">
				<div id="callout-left">
					<?php echo get_option('workspace_callout_left'); ?>
				</div><!-- #callout-left -->
				<div id="callout-right">
					<?php echo get_option('workspace_callout_right'); ?>
				</div><!-- #callout-right -->
			</div><!-- #footer-callout -->
			
			<div class="clear"></div>
			
			<?php if ( is_active_sidebar( 'footer-widget-area-1' ) || is_active_sidebar( 'footer-widget-area-2' ) || is_active_sidebar( 'footer-widget-area-3' ) || is_active_sidebar( 'footer-widget-area-4' )) { ?>
				<div id="footer">
					<div class="footer-wrap">
					<div id="footer-widget-1">
						<?php if ( is_active_sidebar( 'footer-widget-area-1' ) ) :  dynamic_sidebar( 'footer-widget-area-1'); endif; ?>
					</div><!-- #footer-widget-1 -->
					<div id="footer-widget-2">
						<?php if ( is_active_sidebar( 'footer-widget-area-2' ) ) :  dynamic_sidebar( 'footer-widget-area-2'); endif; ?>
					</div><!-- #footer-widget-2 -->
					<div id="footer-widget-3">
						<?php if ( is_active_sidebar( 'footer-widget-area-3' ) ) :  dynamic_sidebar( 'footer-widget-area-3'); endif; ?>
					</div><!-- #footer-widget-3 -->
					<div id="footer-widget-4">
                        <?php if ( is_active_sidebar( 'footer-widget-area-4' ) ) :  dynamic_sidebar( 'footer-widget-area-4'); endif; ?>
					</div><!-- #footer-widget-4 -->
					<div class="clear"></div>
					</div><!-- .footer-wrap -->
				</div><!-- #footer -->
			<?php } ?>
			<div class="clear"></div>
			<div class="copyright">	
				<div class="left">
					&copy; <?php echo date('Y'); ?> <a href="<?php echo home_url(); ?>" title="<?php bloginfo( 'description' ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>. <?php _e('All rights reserved','themejunkie'); ?>.
				</div><!-- .left -->
				<div class="right">
					<?php echo get_option('workspace_footer_credit'); ?>
				</div><!-- .right -->
				<div class="clear"></div>
			</div><!-- .copyright -->
		</div><!-- .inner-wrap -->
	</div> <!-- #wrapper -->
	<?php wp_footer(); ?>
</body>
</html>