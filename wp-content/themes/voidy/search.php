<?php get_header();?>
<div id="main">
	<div id="content">
      <h2>Search Results for "<?php the_search_query(); ?>"</h2>
	    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	        <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <p>
              <?php the_time('M d Y'); ?>
          </p>
            <h2 class="title"><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h2>
            <div class="meta">
				      <p>Published by <?php the_author_posts_link() ?>  under <?php the_category(',') ?> <?php edit_post_link(); ?></p>
			      </div>
			      <div class="entry">
              <?php the_content(__('Continue Reading &#187;', "voidy" )); ?>
              <?php wp_link_pages(); ?>
      			</div>
            <p class="comments">
				<?php comments_popup_link(__('No responses yet', "voidy" ), __('One response so far', "voidy" ), __('% responses so far', "voidy" ),'comments-link', 'Comments are off for this post'); ?>
            </p>	          
	        </div>
      <?php endwhile; else: ?>
          <p><?php _e('Sorry, no posts matched your criteria.', "voidy" ); ?></p>
      <?php endif; ?>
      <p class="newer-older"><?php posts_nav_link(' ',__('&#171; Newer posts', "voidy" ),__('Older posts &#187;', "voidy" )) ?></p>
	</div>
  <?php get_sidebar();?>
  <?php get_footer();?>