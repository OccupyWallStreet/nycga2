<?php get_header() ?>
<div id="container-background">
	<div id="content">
		<div class="padder">
		<?php if($bp_existed == 'true') : ?>
			<?php do_action( 'bp_before_blog_home' ) ?>
		<?php endif; ?>
		<div class="page" id="blog-latest">
			<?php if ( have_posts() ) : ?>
					<?php if( $bp_existed == 'true' ) { //check if bp existed ?>
						<?php do_action( 'bp_before_blog_post' ) ?>			
						<?php bp_wpmu_excerptloop(); ?>
								<?php do_action( 'bp_after_blog_post' ) ?>
					<?php } else { // if not bp detected..let go normal ?>
							<?php wpmu_excerptloop(); ?>
					<?php } ?>
					<?php locate_template( array( '/libary/components/pagination.php' ), true ); ?>
			<?php else : ?>
					<?php locate_template( array( '/libary/components/messages.php' ), true ); ?>
			<?php endif; ?>
		</div>
		<?php if($bp_existed == 'true') : ?>
			<?php do_action( 'bp_after_blog_home' ) ?>
		<?php endif; ?>
		</div><!-- .padder -->
	</div><!-- #content -->
	<?php get_sidebar('blog'); ?>
	<div class="clear">
	</div>
<?php get_footer() ?>
