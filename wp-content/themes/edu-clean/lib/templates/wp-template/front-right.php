<?php include (TEMPLATEPATH . '/options.php'); ?>

<div id="front-right">
<div id="top-right-front">

<?php if($bp_existed == 'true') { ?>
<?php locate_template( array( 'lib/templates/bp-template/bp-searchform.php'), true ); ?>
<?php } ?>


<div id="latest-news">

<?php if($bp_existed == 'true') { ?>
<?php locate_template( array( 'lib/templates/bp-template/bp-profile.php'), true ); ?>
<?php } else { ?>
<?php locate_template( array( 'lib/templates/wp-template/profile.php'), true ); ?>
<?php } // end profile checked ?>


<h3><?php _e('Latest News &raquo;', TEMPLATE_DOMAIN); ?></h3>
<?php
$max_num_post = get_option('posts_per_page');
$my_query = new WP_Query('category_name=&showposts='.$max_num_post);
while ($my_query->have_posts()) : $my_query->the_post();
$do_not_duplicate = $post->ID; ?>

<div class="news">
<h1><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h1>
<p class="date-in"><?php the_time('F jS') ?></p>
<p><?php the_excerpt_feature($excerpt_length=25); ?>...(<?php comments_popup_link(__('No Comment', TEMPLATE_DOMAIN), __('1 Comment', TEMPLATE_DOMAIN), __('% Comments', TEMPLATE_DOMAIN) ); ?>)</p>
</div>
<?php endwhile; ?>
</div>

</div>


<?php if ( is_active_sidebar( __('home-side-right', TEMPLATE_DOMAIN ) ) ) : ?>
<div id="bottom-right-front">
<ul class="sidebar_list">
<?php dynamic_sidebar( __('home-side-right', TEMPLATE_DOMAIN ) ); ?>
</ul>
</div>
<?php endif; ?>

</div>