<?php
/*
Template Name: blog content
*/
?>

<?php get_header() ?>
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
<div id="site-container">
<div id="content"><!-- start #content -->
	<div class="padder">
		<div class="page" id="blog-latest"><!-- start #blog-latest -->		
		<?php if( $bp_existed == 'true' ) { ?>
					<?php do_action( 'bp_before_blog_post' ) ?>			
						<?php bp_wpmu_blogpageloopcontent(); ?>
							<?php do_action( 'bp_after_blog_post' ) ?>
				<?php } else { ?>
						<?php wpmu_blogpageloopcontent(); ?>
				<?php } ?>
						<?php locate_template( array( '/library/components/pagination.php' ), true ); ?>
		</div><!-- end #blog-latest -->
	</div>
</div><!-- end #content -->
	<?php get_sidebar('blog'); ?>
<div class="clear"></div>
</div>
<?php get_footer() ?>
