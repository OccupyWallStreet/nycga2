<?php get_header() ?>
	<div id="content"><!-- start #content -->
		<div class="padder">
			<?php if($bp_existed == 'true') : ?>
				<?php do_action( 'bp_before_blog_single_post' ) ?>
			<?php endif; ?>
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
		<?php if($bp_existed == 'true') : ?>
			<?php do_action( 'bp_after_blog_single_post' ) ?>
		<?php endif; ?>
		</div>
	</div><!-- end #content -->
	<?php get_sidebar('single'); ?>
<?php get_footer() ?>