<?php get_header();?>
<div id="main">
	<div id="content">
      <?php if(have_posts()) : ?>
        <?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
        <?php /* If this is a category archive */ if (is_category()) { ?>
        <h2 class="post-title">
          <?php  printf(__("Archive for the '%s' category", "voidy" ), single_cat_title('', False)) ; ?>
        </h2>

        <?php /* If this is a daily archive */ } elseif (is_day()) { ?>
        <h2 class="post-title">
          <?php _e("Archive for: ", "voidy"); the_time('F jS, Y'); ?>
        </h2>

        <?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
        <h2 class="post-title">
          <?php _e("Archive for: ", "voidy" ); the_time('F, Y'); ?>
        </h2>

        <?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
        <h2 class="post-title">
          <?php _e("Archive for: ", "voidy" ); the_time('Y'); ?>
        </h2>

        <?php /* If this is a search */ } elseif (is_search()) { ?>
        <h2 class="post-title"><?php _e("Search Results", "voidy" ); ?></h2>

        <?php /* If this is an author archive */ } elseif (is_author()) { ?>
        <h2 class="post-title"><?php _e("Author Archive", "voidy" ); ?></h2>

        <?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
        <h2 class="post-title"><?php _e("Blog Archives", "voidy" ); ?></h2>

        <?php } ?>
      <?php endif; ?>
	    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	        <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <h2 class="title"><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h2>
            <div class="meta">
				<p>
					<?php the_time('M d Y'); ?>
					<?php _e("Published by", "voidy" ); ?> <?php the_author_posts_link() ?> <?php _e("under", "voidy" ); ?> <?php the_category(', ') ?> <?php edit_post_link(); ?>
				</p>
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