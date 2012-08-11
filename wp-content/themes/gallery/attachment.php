<?php get_header() ?>
	<div id="content-fullwidth">
		<div class="padder">
		<?php if($bp_existed == 'true') : ?>
	<?php do_action( 'bp_before_attachment' ) ?>
		<?php endif; ?>
		<div class="page" id="attachments-page">
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
					<?php wpmu_attachmentloop(); ?>
				<?php else: ?>
						<p><?php _e( 'Sorry, no attachments matched your criteria.', 'gallery') ?></p>
				<?php endif; ?>
				<?php if($bp_existed == 'true') : ?>
							<?php do_action( 'bp_after_blog_post' ) ?>
				<?php endif; ?>
		</div>
		<?php if($bp_existed == 'true') : ?>
		<?php do_action( 'bp_after_attachment' ) ?>
		<?php endif; ?>
		</div><!-- .padder -->
	</div><!-- #content-fullwidth -->
<?php get_footer() ?>
