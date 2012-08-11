<?php
/*
Template Name: full width
*/
?>

<?php get_header() ?>
	<div id="content-fullwidth">
		<div class="padder">
		<?php if($bp_existed == 'true') : ?>
			<?php do_action( 'bp_before_blog_home' ) ?>
				<?php endif; ?>
		<div class="page" id="blog-latest">
				<?php if (have_posts()) :  ?>
							<?php if( $bp_existed == 'true' ) { //check if bp existed ?>		
								<?php bp_wpmu_pageloop(); ?>
							<?php } else { // if not bp detected..let go normal ?>
									<?php wpmu_pageloop(); ?>
							<?php } ?>
				<?php endif; ?>
		</div>
		<?php if($bp_existed == 'true') : ?>
			<?php do_action( 'bp_after_blog_home' ) ?>
		<?php endif; ?>
		</div><!-- .padder -->
	</div><!-- #content -->
<?php get_footer() ?>
