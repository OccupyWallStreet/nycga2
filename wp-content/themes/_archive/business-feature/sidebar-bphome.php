<?php include (get_template_directory() . '/library/options/options.php'); ?>	
<?php if($bp_existed == 'true') : ?>
<?php do_action( 'bp_before_sidebar' ) ?>
<?php endif; ?>
<?php
	$twitter = get_option('dev_businessfeature_twitter');
	$twitter_text = get_option('dev_businessfeature_twitter_text');
	$twitter_link = get_option('dev_businessfeature_twitter_link');
	$twitter_linktitle = get_option('dev_businessfeature_twitter_linktext');
	$sidebar_title = get_option('dev_businessfeature_sidebar_title');
	$sidebar_feed = get_option('dev_businessfeature_sidebar_feed');
	$sidebar_feedtitle = get_option('dev_businessfeature_sidebar_feedtitle');
	$sidebar_category = get_option('dev_businessfeature_sidebar_cat');
	$sidebar_number = get_option('dev_businessfeature_sidebar_number');
?>
<div id="sidebar">
		<div id="sidebar-top">
	<div id="sidebar-content">
	<div class="padder">
		<?php
		
		if ($twitter == "yes"){
			
		?>
			<div id="tweet">
				<div class="tweet-info">
					<p>
						<?php echo stripslashes($twitter_text); ?>
					</p>
					<a href="<?php echo $twitter_link; ?>" rel="bookmark" title="<?php echo $twitter_linktitle; ?>" class="button"><?php echo stripslashes($twitter_linktitle); ?></a></div>
			</div>
		<?php	
		}
		?>
		<?php
		if ($sidebar_category != ""){
			?>
			<ul class="sidebar_list">
			<li>
			<h2>
			<div class="alignleft"><?php echo stripslashes($sidebar_title); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div><div class="alignright"><a href="<?php echo $sidebar_feed; ?>" title="<?php echo $sidebar_feedtitle; ?>"><img width="19" height="19" alt="RSS" src="<?php bloginfo('template_directory');?>/library/styles/colour-images/rss.png"></a> </div>
			</h2>
			<ul id="blog-news">
				<?php query_posts('category_name='. $sidebar_category . '&showposts='. $sidebar_number . ''); ?>
								  <?php while (have_posts()) : the_post(); ?>
			<li><h3><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3><br>
			<small><?php the_date(); ?></small>
			<br>
			<?php the_excerpt(); ?>
			</li>
			
						<?php endwhile; ?>
			</ul>
			</li>
			</ul>
			<?php
		}
		
		?>
							<?php if ( is_active_sidebar( 'home-sidebar' ) ) : ?>
									<?php dynamic_sidebar( 'home-sidebar' ); ?>
							<?php endif; ?>
								<?php if($bp_existed == 'true') : ?>
								<?php do_action( 'bp_inside_after_sidebar' ) ?>
								<?php endif; ?>
	</div><!-- .padder -->
</div><!-- #sidebar -->
</div>
</div>
<?php if($bp_existed == 'true') : ?>
<?php do_action( 'bp_after_sidebar' ) ?>
<?php endif; ?>