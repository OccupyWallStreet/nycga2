<?php
/*
Template Name: blog news
*/
?>

<?php get_header() ?>
<div id="container-background">
	<div id="content"><!-- start #content -->
		<div class="padder">
		<?php if($bp_existed == 'true') : ?>
			<?php do_action( 'bp_before_blog_home' ) ?>
		<?php endif; ?>
		<div class="page wideColumn" id="blog-latest"><!-- start #blog-latest -->
					<?php if( $bp_existed == 'true' ) { ?>
						<?php do_action( 'bp_before_blog_post' ) ?>			
							<?php bp_wpmu_blogpageloop(); ?>
								<?php do_action( 'bp_after_blog_post' ) ?>
					<?php } else { ?>
							<?php wpmu_blogpageloop(); ?>
					<?php } ?>
					<?php locate_template( array( '/library/components/pagination.php' ), true ); ?>
		</div><!-- end #blog-latest -->
		<?php if($bp_existed == 'true') : ?>
			<?php do_action( 'bp_after_blog_home' ) ?>
		<?php endif; ?>
		</div>
	</div><!-- end #content -->
	<?php get_sidebar('blog'); ?>
<div class="clear">
</div>
<?php get_footer() ?>