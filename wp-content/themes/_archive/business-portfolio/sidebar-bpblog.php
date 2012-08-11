<?php include (get_template_directory() . '/library/options/options.php'); ?>
<?php if($bp_existed == 'true') : ?>
<?php do_action( 'bp_before_sidebar' ) ?>
<?php endif; ?>
<div id="sidebar">
	<div class="padder">
<ul class="sidebar_list">
				<?php if ( is_active_sidebar( 'blog-sidebar' ) ) : ?>
						<?php dynamic_sidebar( 'blog-sidebar' ); ?>
				<?php endif; ?>
					<?php if($bp_existed == 'true') : ?>
					<?php do_action( 'bp_inside_after_sidebar' ) ?>
					<?php endif; ?>
	</ul>
	</div><!-- .padder -->
</div><!-- #sidebar -->
<?php if($bp_existed == 'true') : ?>
<?php do_action( 'bp_after_sidebar' ) ?>
<?php endif; ?>