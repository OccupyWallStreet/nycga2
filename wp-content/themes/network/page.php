<?php get_header() ?>
<div id="container-background">
	<div id="content"><!-- start #content -->
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
		</div>
	</div><!-- end #content -->	
	<div class="clear">
	</div>
<?php get_footer(); ?>
