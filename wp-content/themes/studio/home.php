<?php get_header() ?>
<div id="homepage"><!-- start #homepage-->
	<?php 
	$slideshow = get_option('dev_studio_slideshow');
	if ($slideshow == "yes"){
		locate_template( array( '/library/components/slideshow.php' ), true );					
	} else {
	?>
		<div class="padder">
		<?php if($bp_existed == 'true') : ?>
			<?php do_action( 'bp_before_blog_home' ) ?>
		<?php endif; ?>
		<div class="page" id="blog-latest"><!-- start #blog-latest -->
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
	<?php
	}
	?>
	<?php
	$featurecontent_on = get_option('dev_studio_feature_show');
	if ($featurecontent_on == "yes"){
		locate_template( array( '/library/components/feature-content.php' ), true );
	}
	?>			
</div><!-- end #homepage -->
<?php get_footer() ?>