<?php get_header(); ?>

	<?php if(get_option('workspace_slider_enable') == 'on') { ?>
		<?php get_template_part('includes/home-featured-slider'); ?>
	<?php } ?>

	<?php if(get_option('workspace_slogan_enable') == 'on') { ?>
		<div id="slogan">
	        <div class="slogan-content">
	            <?php echo get_option('workspace_slogan');?>
	        </div><!-- .slogan-content -->
			<a class="button large white slogan-button" href="<?php echo get_option('workspace_slogan_button_link');?>"><?php echo get_option('workspace_slogan_button_text');?></a>
	    </div><!-- #slogan -->
    <?php } ?>
    
	<?php if(get_option('workspace_home_portfolio_enable') == 'on') { ?>	
		<div id="home-recent-works" class="clear">
			<div class="side-content">
				<h3><?php echo get_option('workspace_home_portfolio_title'); ?></h3>
				<p><?php echo get_option('workspace_home_portfolio_desc');?></p>
			</div><!-- .side-content -->
			<ul class="grid clear">
	            <?php get_template_part('includes/home-recent-works')?>
			</ul><!-- .grid -->
		</div><!-- #home-recent-works -->
	<?php } ?>
	
	<?php if(get_option('workspace_home_blog_enable') == 'on') { ?>	
		<div id="home-recent-posts" class="clear">
			<div class="side-content">
				<h3><?php echo get_option('workspace_home_blog_title'); ?></h3>
				<p><?php echo get_option('workspace_home_blog_desc');?></p>
			</div><!-- end .side-content -->
			<ul class="grid clear">
	            <?php get_template_part('includes/home-recent-posts')?>
			</ul><!-- .grid -->
		</div><!-- #home-recent-posts-->
	<?php } ?>
    
<?php get_footer(); ?>
