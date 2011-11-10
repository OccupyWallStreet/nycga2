<?php get_header();?> 

<div id="floatswrap" class="smallftfl clearfix">
	<div class="container clearfix">
	
		<div id="main_col">
			<h2><span><?php _e('Whoops! - Not Found','smashingMultiMedia');?></span></h2>		
				
			<p>
				<?php _e('So sorry, but we could not find what you were looking for.','smashingMultiMedia');?>
				<br/> 
				<?php _e('Perhaps searching will help.','smashingMultiMedia');?>
			</p>
				
			<div class="main_col_searchform">
				<?php include (TEMPLATEPATH . '/searchform.php'); ?>
			</div><!-- main_col_searchform -->
				
		</div><!-- main_col -->
			
		<?php get_sidebar(); ?>
	</div><!-- container -->
</div><!-- floatswrap-->
<?php get_footer(); ?>