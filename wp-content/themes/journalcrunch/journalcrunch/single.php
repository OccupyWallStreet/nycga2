<?php get_header(); ?>

<!-- Begin #colleft -->
			<div id="colLeft">
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>	
				<div id="singlePost">
					<h1><?php the_title(); ?></h1>
					<div class="meta">
					 <?php the_time('M j, Y') ?> by <?php the_author_posts_link()?>&nbsp;&nbsp;&nbsp;<img src="<?php bloginfo('template_directory'); ?>/images/ico_post_comments.png" alt="" /> <?php comments_popup_link('No Comments', '1 Comment ', '% Comments'); ?>&nbsp;&nbsp;&nbsp;<img src="<?php bloginfo('template_directory'); ?>/images/ico_post_date.png" alt="" /> Posted under:  <?php the_category(', ') ?> 
					</div>
					<?php the_content(); ?>
					 <div class="postTags"><?php the_tags(); ?></div>
				</div>
				<?php comments_template(); ?>
		<?php endwhile; else: ?>

		<p>Sorry, but you are looking for something that isn't here.</p>

	<?php endif; ?>
			
			</div>
			<!-- End #colLeft -->
			

<?php get_sidebar(); ?>	
<?php get_footer(); ?>