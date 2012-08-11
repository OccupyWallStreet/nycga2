<?php if($bp_existed == 'true') : ?>
<?php do_action( 'bp_before_sidebar' ) ?>
<?php endif; ?>
<!-- start archive sidebar -->
<div id="sidebar"><!-- start #sidebar -->
	<div class="padder">
			<?php if ( is_active_sidebar( 'sidebar-archive' ) ) : ?>
					<?php dynamic_sidebar( 'sidebar-archive' ); ?>
				<?php else : ?>
						<div class="widget-error">
							<?php _e( 'Please log in and add widgets to this column.', 'studio' ) ?> <a href="<?php echo get_option('siteurl') ?>/wp-admin/widgets.php?s=&amp;show=&amp;sidebar=sidebar-archive"><?php _e( 'Add Widgets', 'studio' ) ?></a>
						</div>
			<?php endif; ?>
				<?php if($bp_existed == 'true') : ?>
				<?php do_action( 'bp_inside_after_sidebar' ) ?>
				<?php endif; ?>
	</div>
</div><!-- end #sidebar -->
<!-- end blog sidebar -->
<?php if($bp_existed == 'true') : ?>
<?php do_action( 'bp_after_sidebar' ) ?>
<?php endif; ?>