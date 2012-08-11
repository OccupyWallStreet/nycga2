<?php
	$featured_category = get_option('dev_businessportfolio_feature_cat');
	$featured_number = get_option('dev_businessportfolio_feature_number');
	$side_title = get_option('dev_businessportfolio_side_title');
	$side_description = get_option('dev_businessportfolio_side_description');
	$news_category = get_option('dev_businessportfolio_news_cat');
	$news_number = get_option('dev_businessportfolio_news_number');
	$news_strapline = get_option('dev_businessportfolio_news_strapline');
	$news_title = get_option('dev_businessportfolio_news_title');
?>
<?php get_header() ?>
<div id="content-left">
<?php
if ($featured_category != ""){
?>
<div id="skills">
	<?php query_posts('category_name='. $featured_category . '&showposts='. $featured_number . ''); ?>
					  <?php while (have_posts()) : the_post(); ?>
					<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2><br />
						<?php if(function_exists('the_post_thumbnail')) { ?><?php if(get_the_post_thumbnail() != "") { ?><div class="alignleft">
						<?php the_post_thumbnail(); ?></div><?php } } ?>
						<?php the_excerpt(); ?>
							<div class="clear"></div>
							<?php endwhile; 
							?>		
</div>
<?php
}
else{
	?>
	<div id="skills">
						<h2><?php _e("Configure this under theme options.", 'business-portfolio'); ?></h2>
	</div>
	<?php
}
?>

</div>
<div id="content-right">
<div id="about-businessportfolio">
		<?php
			if ($side_title == ""){
				$side_title = "Side title goes here";
			}
		?>
<h3><?php echo stripslashes($side_title); ?></h3>
<p>
		<?php
			if ($side_description == ""){
				$side_description = "Side description goes here";
			}
		?>
<?php echo stripslashes($side_description); ?>
</p>
</div>
	<?php if ( !is_user_logged_in() ) : ?>
	<?php
		locate_template( array( '/library/components/signup-box.php' ), true );
	?>				
				<?php endif; ?>
<div class="clear"></div>
	<?php if($bp_existed == 'true') : ?>
			<?php locate_template( array( '/library/components/buddypress/buddypress-panel.php' ), true ); ?>
	<?php endif ?>
<div id="fromtheblog">
		<?php
			if ($news_title == ""){
				$news_title = "News title goes here";
			}
		?>
<h3><?php echo stripslashes($news_title); ?><br />
<span><?php echo stripslashes($news_strapline); ?></span></h3>
<?php
if ($news_category != ""){
?>
<ul>
		<?php query_posts('category_name='. $news_category . '&showposts='. $news_number . ''); ?>
				  <?php while (have_posts()) : the_post(); ?>
	<li><div class="calendar"><?php the_time('j'); ?><br />
<strong><?php the_time('M'); ?></strong></div>
<h1><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h1>
<div class="more-inside"><a title="full article on <?php the_title(); ?>" href="<?php the_permalink() ?>"><?php _e('View article in full &raquo;', 'business-portfolio'); ?></a></div>
</li>
			<?php endwhile; 
			?>
</ul>
<?php
}
else{
	?>
	<ul>
		<li>
	<h1><?php _e("Configure this under theme options.", 'business-portfolio'); ?></h1>
	</li>
	</ul>
	<?php
}
?>
</div>
</div>
<?php get_footer() ?>