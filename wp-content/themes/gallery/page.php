<?php get_header() ?>
<?php if($bp_existed == 'true') : ?>
	<?php do_action( 'bp_before_blog_page' ) ?>
<?php endif ?>
<div id="site-container">
<div id="content"><!-- start #content -->
	<div class="padder">
		<div class="page" id="blog-page"><!-- start #blog-page -->
						<?php if (have_posts()) :  ?>
							<script type="text/javascript">
							  jQuery.noConflict();
							 jQuery(document).ready(function() {
							       		jQuery("a[rel=exhibition_gallery]").fancybox({
											'overlayShow'	: true,
											'overlayOpacity' : 0.9,
											'overlayColor' : '#111111',
											'transitionIn'	: 'elastic',
											'transitionOut'	: 'elastic'
										});
							   });
							   </script>
									<?php if( $bp_existed == 'true' ) { ?>		
										<?php bp_wpmu_pageloop(); ?>
									<?php } else { ?>
											<?php wpmu_pageloop(); ?>
									<?php } ?>
						<?php endif; ?>
		</div><!-- end #blog-page -->
	</div>
</div><!-- end #content -->
	<?php get_sidebar('page'); ?>
<div class="clear"></div>
</div>
<?php if($bp_existed == 'true') : ?>
	<?php do_action( 'bp_after_blog_page' ) ?>
<?php endif ?>
<?php get_footer() ?>
