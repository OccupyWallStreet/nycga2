<?php
get_header();
?>

<!-- Begin #colLeft -->
		<div id="colLeft">
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div class="postItem">
				<h1><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h1> 
				<div class="meta">
							<?php the_time('M j, Y') ?> &nbsp;&nbsp;//&nbsp;&nbsp; by <span class="author"><?php the_author_link(); ?></span> &nbsp;&nbsp;//&nbsp;&nbsp;  <?php the_category(', ') ?>  &nbsp;//&nbsp;  <?php comments_popup_link('No Comments', '1 Comment ', '% Comments'); ?> 
						</div>
				<?php the_content(__('read more')); ?> 
				
                    <div class="postTags"><?php the_tags(); ?></div>
							
							<div id="shareLinks">
								<a href="#" class="share">[+] Share &amp; Bookmark</a>
								<span id="icons">
									<a href="http://twitter.com/home/?status=<?php the_title(); ?> : <?php the_permalink(); ?>" title="Tweet this!">
									<!--<img src="<?php bloginfo('template_directory'); ?>/images/twitter.png" alt="Tweet this!" />-->&#8226; Twitter</a>				
									<a href="http://www.stumbleupon.com/submit?url=<?php the_permalink(); ?>&amp;amp;title=<?php the_title(); ?>" title="StumbleUpon.">
									<!--<img src="<?php bloginfo('template_directory'); ?>/images/stumbleupon.png" alt="StumbleUpon" />-->&#8226; StumbleUpon</a>
									<a href="http://digg.com/submit?phase=2&amp;amp;url=<?php the_permalink(); ?>&amp;amp;title=<?php the_title(); ?>" title="Digg this!">
									<!--<img src="<?php bloginfo('template_directory'); ?>/images/digg.png" alt="Digg This!" />-->&#8226; Digg</a>				
									<a href="http://del.icio.us/post?url=<?php the_permalink(); ?>&amp;amp;title=<?php the_title(); ?>" title="Bookmark on Delicious.">
									<!--<img src="<?php bloginfo('template_directory'); ?>/images/delicious.png" alt="Bookmark on Delicious" />-->&#8226; Delicious</a>
									<a href="http://www.facebook.com/sharer.php?u=<?php the_permalink();?>&amp;amp;t=<?php the_title(); ?>" title="Share on Facebook.">
									<!--<img src="<?php bloginfo('template_directory'); ?>/images/facebook.png" alt="Share on Facebook" id="sharethis-last" />-->&#8226; Facebook</a>
								</span>
							</div>
		
		
        <?php comments_template(); ?>
			</div>
		<?php endwhile; else: ?>

		<p>Sorry, but you are looking for something that isn't here.</p>

	<?php endif; ?>
		
			</div>
		<!-- End #colLeft -->

<?php get_sidebar(); ?>	

<?php get_footer(); ?>
