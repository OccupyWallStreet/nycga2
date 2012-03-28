<?php get_header(); ?>

	<div class="box">
	<div class="post">
		<?php if (have_posts()) : ?>

 	  <?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
 	  <?php /* If this is a category archive */ if (is_category()) { ?>
		<h2 >Archive for the &#8216;<?php single_cat_title(); ?>&#8217; Category</h2>
 	  <?php /* If this is a tag archive */ } elseif( is_tag() ) { ?>
		<h2 >Posts Tagged &#8216;<?php single_tag_title(); ?>&#8217;</h2>
 	  <?php /* If this is a daily archive */ } elseif (is_day()) { ?>
		<h2 >Archive for <?php the_time('F jS, Y'); ?></h2>
 	  <?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
		<h2 >Archive for <?php the_time('F, Y'); ?></h2>
 	  <?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
		<h2 >Archive for <?php the_time('Y'); ?></h2>
	  <?php /* If this is an author archive */ } elseif (is_author()) { ?>
		<h2 >Author Archive</h2>
 	  <?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
		<h2 >Blog Archives</h2>
 	  <?php } ?>
		<div class="hrlineB"></div>

		
        <div class="entry bags">
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
