<?php get_header() ?>
	<div id="content">
		<div class="padder">
			<?php 
				locate_template( array( '/library/components/feature-content.php' ), true ); ?>
		</div><!-- .padder -->
	</div><!-- #content -->
		
					<?php if( $bp_existed == 'true' ) { //check if bp existed ?>
						<?php get_sidebar('bphome'); ?>
					<?php } else { // if not bp detected..let go normal ?>
					<?php get_sidebar('home'); ?>
					<?php } ?>
		
<?php get_footer() ?>

