<?php get_header(); ?>

	<div class="box">
      <div class="post">
      <h2>Search Results</h2><div class="hrlineB"></div>
        <div class="entry bags">
		<?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
				<div class="archthumb">
    				<a href="<?php the_permalink(); ?>"><?php grid_attachment_image($post->ID, 'thumbnail', 'alt="' . $post->post_title . '"'); ?></a>
				</div>
				
				
				<h3><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
				<small>By <?php the_author() ?> | <?php the_category(', ') ?></small><br/>
				<?php the_excerpt(); ?>

				
		<div class="hrlineC" style="width:594px;"></div>
		

		<?php endwhile; ?>
		</div>
        <div class="cell last bags">	
        <?php get_sidebar(); ?>
        </div>


		<?php else : ?>

                <div class="post">
                <h2>Not Found!</h2>
                    <p class="center">Sorry, but you are looking for something that isn't here.</p>
                    <?php get_search_form(); ?>
                    <?php wp_tag_cloud('smallest=8&largest=36&'); ?>
                    </div>

		<?php endif; ?>
                <div class="navigation ger">
              	<div class="clear"></div>
                <div class="nav-previous fl"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts' ) ); ?></div>
                <div class="nav-next fr"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>' ) ); ?></div>
                </div>
	</div>


<?php get_footer(); ?>