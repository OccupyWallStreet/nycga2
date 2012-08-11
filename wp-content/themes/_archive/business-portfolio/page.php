<?php get_header() ?>
	<div id="content">
		<div class="padder">
		<?php if($bp_existed == 'true') : ?>
			<?php do_action( 'bp_before_blog_page' ) ?>
		<?php endif; ?>
		<div class="page" id="blog-page">
					<?php if (have_posts()) :  ?>
								<?php if( $bp_existed == 'true' ) { //check if bp existed ?>		
									<?php bp_wpmu_pageloop(); ?>
								<?php } else { // if not bp detected..let go normal ?>
										<?php wpmu_pageloop(); ?>
								<?php } ?>
					<?php endif; ?>
		</div><!-- .page -->
		<?php if($bp_existed == 'true') : ?>
			<?php do_action( 'bp_after_blog_page' ) ?>
		<?php endif; ?>
		</div><!-- .padder -->
	</div><!-- #content -->
	<?php if( $bp_existed == 'true' ) { //check if bp existed ?>
		<?php get_sidebar('bppage'); ?>		
	<?php } else { // if not bp detected..let go normal ?>
		<?php get_sidebar('page'); ?>
	<?php } ?>
	
<?php get_footer(); ?>
