<?php
/*
Template Name: full width
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
					<?php if( $bp_existed == 'true' ) { //check if bp existed ?>
						<?php do_action( 'bp_before_blog_post' ) ?>			
							<?php bp_wpmu_pageloop(); ?>
								<?php do_action( 'bp_after_blog_post' ) ?>
					<?php } else { // if not bp detected..let go normal ?>
							<?php wpmu_pageloop(); ?>
					<?php } ?>
					<?php locate_template( array( '/library/components/pagination.php' ), true ); ?>
		</div>
		<?php if($bp_existed == 'true') : ?>
			<?php do_action( 'bp_after_blog_home' ) ?>
		<?php endif; ?>
		</div><!-- .padder -->
	</div><!-- #content-fullwidth -->
<?php get_footer() ?>
