		<?php include (get_template_directory() . '/library/options/options.php'); ?>
<?php if($bp_existed == 'true') : ?>
<?php do_action( 'bp_before_sidebar' ) ?>
<?php endif; ?>
<div id="sidebar">
	<div class="padder">
				<?php if ( is_active_sidebar( 'default-sidebar' ) ) : ?>
						<?php dynamic_sidebar( 'default-sidebar' ); ?>
				<?php endif; ?>
					<?php if($bp_existed == 'true') : ?>
					<?php do_action( 'bp_inside_after_sidebar' ) ?>
					<?php endif; ?>
		<?php locate_template( array( 'library/components/advert-sidebar.php' ), true ); ?>
	</div><!-- .padder -->
</div><!-- #sidebar -->
<?php if($bp_existed == 'true') : ?>
<?php do_action( 'bp_after_sidebar' ) ?>
<?php endif; ?>