	<?php include (get_template_directory() . '/library/options/options.php'); ?>
<?php if($bp_existed == 'true') : ?>
<?php do_action( 'bp_before_sidebar' ) ?>
<?php endif; ?>
<!-- start blog sidebar -->
<div id="sidebar"><!-- start #sidebar -->
	<div class="padder">
			<?php if ( is_active_sidebar( 'sidebar-blog' ) ) : ?>
					<?php dynamic_sidebar( 'sidebar-blog' ); ?>
				<?php else : ?>
				
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