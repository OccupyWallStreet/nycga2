<?php get_header() ?>
<div id="container-background">
	<div id="content"><!-- start #content -->
		<div class="padder">
	<div class="page wideColumn" id="blog-latest"><!-- start #blog-latest -->
		<?php if($bp_existed == 'true') : ?>
			<?php do_action( 'bp_before_blog_single_post' ) ?>
		<?php endif; ?>
		<?php if (have_posts()) :  ?>
		
		<?php if( $bp_existed == 'true' ) { ?>
			<?php bp_wpmu_singleloop(); ?>
		<?php } else { ?>
			<?php wpmu_singleloop(); ?>
		<?php } ?>
		
		
		<?php if($bp_existed == 'true') : ?>
			<?php do_action( 'bp_after_blog_single_post' ) ?>
		<?php endif; ?>
		<?php endif; ?>
	</div> <!-- wideColumn -->
	</div>
</div><!-- end #content -->
	<?php get_sidebar('blog'); ?>
	<div class="clear">
	</div>
<?php get_footer() ?>