<?php include (get_template_directory() . '/options.php'); ?>
	<div class="footer-block">
			<?php if ( is_active_sidebar( 'footer2-sidebar' ) ) : ?>
					<?php dynamic_sidebar( 'footer2-sidebar' ); ?>
			<?php endif; ?>
	</div>