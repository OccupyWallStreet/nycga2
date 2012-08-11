<?php get_header() ?>
	<div id="content">
		<div class="padder">
				<?php if ( !is_user_logged_in() ) : ?>
				<?php

					locate_template( array( '/library/components/signup-box.php' ), true );

				?>				
							<?php endif; ?>
			<div id="searchform">
			<?php
				locate_template( array( '/library/components/searchform.php' ), true );	
			?>	
			</div>
					<?php
						locate_template( array( '/library/components/content-rows.php' ), true );	
					?>
		</div><!-- .padder -->
	</div><!-- #content -->
		<?php if( $bp_existed == 'true' ) { //check if bp existed ?>
			<?php get_sidebar('bphome'); ?>	
		<?php } else { // if not bp detected..let go normal ?>
			<?php get_sidebar('home'); ?>
		<?php } ?>
<?php get_footer() ?>
