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
		<?php if($bp_existed == 'true') : ?>
			<?php do_action( 'bp_before_blog_single_post' ) ?>
		<?php endif; ?>
		<div id="site-container">
		<div id="content"><!-- start #content -->
			<div class="padder">
				<div class="page" id="blog-single"><!-- start #blog-single -->
							<?php if (have_posts()) :  ?>
								<?php if( $bp_existed == 'true' ) { ?>
									<?php bp_wpmu_singleloop(); ?>
								<?php } else { ?>
									<?php wpmu_singleloop(); ?>
								<?php } ?>
						<?php locate_template( array( '/library/components/pagination.php' ), true ); ?>
					<?php else: ?>
							<?php locate_template( array( '/library/components/messages.php' ), true ); ?>
					<?php endif; ?>
				</div><!-- end #blog-single -->
			</div>
		</div><!-- end #content -->
		<?php locate_template( array( 'sidebar.php' ), true ) ?>
		<div class="clear"></div>
		</div>
	<?php if($bp_existed == 'true') : ?>
		<?php do_action( 'bp_after_blog_single_post' ) ?>
	<?php endif; ?>
<?php get_footer() ?>