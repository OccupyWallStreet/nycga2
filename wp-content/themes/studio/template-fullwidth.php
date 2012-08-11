<?php
/*
Template Name: full width
*/
?>

<?php get_header() ?>
	<div id="content-fullwidth">
		<div class="padder">
				<?php if($bp_existed == 'true') : ?>
					<?php do_action( 'bp_before_blog_page' ) ?>
				<?php endif; ?>
				<div class="page" id="blog-page"><!-- start #blog-page -->
							<?php if (have_posts()) :  ?>
										<?php if( $bp_existed == 'true' ) { ?>		
											<?php bp_wpmu_pageloop(); ?>
										<?php } else { ?>
												<?php wpmu_pageloop(); ?>
										<?php } ?>
							<?php endif; ?>
				</div><!-- end #blog-page -->
				<?php if($bp_existed == 'true') : ?>
					<?php do_action( 'bp_after_blog_page' ) ?>
				<?php endif; ?>
		</div><!-- .padder -->
	</div><!-- #content-fullwidth -->
<?php get_footer() ?>
