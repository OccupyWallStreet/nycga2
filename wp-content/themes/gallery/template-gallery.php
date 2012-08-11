<?php
/*
Template Name: gallery
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
	<div id="content-fullwidth">
		<div class="padder">
		<?php if($bp_existed == 'true') : ?>
			<?php do_action( 'bp_before_blog_home' ) ?>
		<?php endif; ?>
		<div class="page" id="blog-latest">
			<?php wpmu_galleryloop(); ?>
		</div>
		<?php if($bp_existed == 'true') : ?>
			<?php do_action( 'bp_after_blog_home' ) ?>
		<?php endif; ?>
		</div><!-- .padder -->
	</div><!-- #content-fullwidth -->
<?php get_footer() ?>
