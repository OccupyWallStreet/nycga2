<?php
/*
Template Name: full width
*/
?>
<div id="container-background">
<?php get_header() ?>
	<div id="content-fullwidth">
		<div class="padder">
		<?php if($bp_existed == 'true') : ?>
			<?php do_action( 'bp_before_blog_home' ) ?>
		<?php endif; ?>
		<div class="page" id="blog-latest">
					<?php if( $bp_existed == 'true' ) { //check if bp existed ?>
						<?php do_action( 'bp_before_blog_post' ) ?>			
							<?php bp_wpmu_blogpageloop(); ?>
								<?php do_action( 'bp_after_blog_post' ) ?>
					<?php } else { // if not bp detected..let go normal ?>
							<?php wpmu_blogpageloop(); ?>
					<?php } ?>
					<?php locate_template( array( '/library/components/pagination.php' ), true ); ?>
		</div>
		<?php if($bp_existed == 'true') : ?>
			<?php do_action( 'bp_after_blog_home' ) ?>
		<?php endif; ?>
		</div><!-- .padder -->
	</div><!-- #content-fullwidth -->
	<div class="clear">
	</div>
<?php get_footer() ?>
