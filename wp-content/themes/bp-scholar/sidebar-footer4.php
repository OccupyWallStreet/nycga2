<?php include (get_template_directory() . '/options.php'); ?>
	<div class="footer-block-end">
			<?php if ( is_active_sidebar( 'footer4-sidebar' ) ) : ?>
					<?php dynamic_sidebar( 'footer4-sidebar' ); ?>
			<?php endif; ?>
	</div>