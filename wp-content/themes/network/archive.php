<?php get_header(); ?>
<div id="container-background">
	<div id="content"><!-- start #content -->
		<div class="padder">
		<?php if($bp_existed == 'true') : ?>
			<?php do_action( 'bp_before_archive' ) ?>
		<?php endif; ?>
		<div class="page wideColumn" id="blog-archives"><!-- start #blog-archives -->
				<?php locate_template( array( '/library/components/headers.php' ), true ); ?>
			<?php if ( have_posts() ) : ?>
					<?php if( $bp_existed == 'true' ) { ?>
						<?php do_action( 'bp_before_blog_post' ) ?>			
						<?php bp_wpmu_excerptloop(); ?>
								<?php do_action( 'bp_after_blog_post' ) ?>
					<?php } else { ?>
							<?php wpmu_excerptloop(); ?>
					<?php } ?>
				<?php locate_template( array( '/library/components/pagination.php' ), true ); ?>
			<?php else: ?>
					<?php locate_template( array( '/library/components/messages.php' ), true ); ?>
			<?php endif; ?>
		</div><!-- end #blog-archives -->
		<?php if($bp_existed == 'true') : ?>
			<?php do_action( 'bp_after_archive' ) ?>
		<?php endif; ?>
		</div>
	</div><!-- end #content -->
	<?php get_sidebar('blog'); ?>
	<div class="clear">
	</div>
<?php get_footer(); ?>
