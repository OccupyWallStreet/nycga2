<?php include (get_template_directory() . '/library/options/options.php'); ?>
<?php if($bp_existed == 'true') : ?>
<?php do_action( 'bp_before_sidebar' ) ?>
<?php endif; ?>
<div id="sidebar">
	<div class="padder">
		
				<?php if ( !is_user_logged_in() ) : ?>
				<?php

					locate_template( array( '/library/components/signup-box.php' ), true );

				?>
				
			<?php endif; ?>
						<?php if ( is_active_sidebar( 'blog-sidebar' ) ) : ?>
								<?php dynamic_sidebar( 'blog-sidebar' ); ?>
						<?php endif; ?>
							<?php if($bp_existed == 'true') : ?>
							<?php do_action( 'bp_inside_after_sidebar' ) ?>
							<?php endif; ?>
	</div><!-- .padder -->
</div><!-- #sidebar -->
<?php if($bp_existed == 'true') : ?>
<?php do_action( 'bp_after_sidebar' ) ?>
<?php endif; ?>