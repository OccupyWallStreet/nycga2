	<?php include (get_template_directory() . '/library/options/options.php'); ?>

<!-- start footer-page sidebar -->
<div id="footerWidgets" class="generic-box"><!-- start #sidebar -->
	<div class="padder">
		<?php if ( is_active_sidebar( 'footer-page' ) ) : ?>
				<?php dynamic_sidebar( 'footer-page' ); ?>
			<?php else : ?>
		<?php endif; ?>
			<?php if($bp_existed == 'true') : ?>
			<?php do_action( 'bp_inside_after_sidebar' ) ?>
			<?php endif; ?>
	</div>
	<div class="clear"></div>
</div><!-- end #sidebar -->
<!-- end footer-page sidebar -->