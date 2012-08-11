<?php get_header();?> 
<div id="floatswrap" class="smallftfl clearfix">
	<div class="container clearfix">
	
		<div id="featuredArea" class="clearfix">
			<?php include (TEMPLATEPATH . '/featuredArea/featuredStickyPosts.php'); ?>
		</div>
				
		<?php if ( is_sidebar_active('frontpage_seperator_widget_area') ) : dynamic_sidebar('frontpage_seperator_widget_area'); endif; ?>
	
		
			<div id="main_col">
			
				<?php include (TEMPLATEPATH . '/posts/indexPostColumns.php'); ?> 
			
			</div><!-- main_col -->
					
		<?php get_sidebar(); ?>
		
	</div><!-- container -->
</div><!-- floatswrap-->
<?php get_footer(); ?>