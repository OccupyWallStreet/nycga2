<?php get_header();?>
<div id="main">
	<div id="content">
	    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	        <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <h1 class="title"><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h1>
			 <div class="meta"><p><?php edit_post_link(); ?></p></div>
 
			      <div class="entry">
              <?php the_content(__('Continue Reading &#187;', "voidy" )); ?>
              <?php wp_link_pages(); ?>
              <?php $sub_pages = wp_list_pages( 'sort_column=menu_order&depth=1&title_li=&echo=0&child_of=' . $id );?>
              <?php if ($sub_pages <> "" ){?>
              <p class="meta"><?php _e("This page has the following sub pages.", "voidy" ); ?></p>
              <ul>
                <?php echo $sub_pages; ?>
              </ul>
              <?php }?>
            </div>
            <p class="comments">
              <?php comments_popup_link(__('No responses yet', "voidy" ), __('One response so far', "voidy" ), __('% responses so far', "voidy" ),'comments-link', ''); ?>
            </p>
	          <?php comments_template(); // Get wp-comments.php template ?>
	        </div>
      <?php endwhile; else: ?>
          <p><?php _e('Sorry, no posts matched your criteria.', "voidy" ); ?></p>
      <?php endif; ?>
      <p class="newer-older"><?php posts_nav_link(' ',__('&#171; Newer posts', "voidy" ),__('Older posts &#187;', "voidy" )) ?></p>
	</div>
  <?php get_sidebar();?>
  <?php get_footer();?>