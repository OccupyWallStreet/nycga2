<?php 
	$signupfeat_on = get_option('dev_buddydaily_signupfeat_on');
	$signupfeat_text = get_option('dev_buddydaily_signupfeat_text');
?>
<?php

if ($signupfeat_on != "no"){

?>
<div id="signup-bar">
	<div class="dark-container">
		<p class="signup">
			<?php echo stripslashes($signupfeat_text); ?>
		</p>
	<?php signup_button(); ?>
	</div>
</div>
<?php

}

?>
<?php 
	$spotlight_cat = get_option('dev_buddydaily_spotlight_category');
	$spotlight_num = get_option('dev_buddydaily_spotlight_number');	
	$spotlight_image_display = get_option('dev_buddydaily_spotlight_image_size');
?>
<?php if ($spotlight_cat == ""){?>
	<div class="dark-container">
		<p>
			<?php _e( 'Please set your spotlight category up under theme options', 'bp-daily' ) ?>
		</p>
	</div>
	<?php } else {?>
				<?php query_posts('category_name='. $spotlight_cat . '&posts_per_page='. $spotlight_num .''); ?>
								  <?php while (have_posts()) : the_post(); ?>
										<div class="spotlight-post">
					<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><span class="attach-post-image alignleft" style="height:100px;width:100px;display:block;background:url('<?php the_post_image_url($spotlight_image_display); ?>') center center no-repeat">&nbsp;</span></a>
							<h4><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h4>	
							<p>
						<span class="byline"><?php the_time('M j Y') ?> <em><?php _e( 'by ', 'bp-daily' ) ?><?php the_author_link();  ?></em></span></p>
						
					<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>" class="button">More</a>
					<div class="clear"></div>
						</div>
				<?php endwhile; } ?>
