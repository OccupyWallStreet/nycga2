<?php if (is_category()) { ?>

<h2 id="post-header"><?php _e('Archives for', TEMPLATE_DOMAIN); ?> <?php single_cat_title(); ?></h2>

<?php } else if (is_tag()) { ?>

<h2 id="post-header"><?php _e('Tag archives for', TEMPLATE_DOMAIN); ?> <?php single_cat_title(); ?></h2>

<?php } else if (is_archive()) { ?>

<h2 id="post-header">
<?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
<?php /* If this is a category archive */ if (is_day()) { ?>
<?php _e('Archives for', TEMPLATE_DOMAIN); ?> <?php the_time('F jS, Y'); ?>
<?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
<?php _e('Archives for', TEMPLATE_DOMAIN); ?> <?php the_time('F, Y'); ?>
<?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
<?php _e('Archives for', TEMPLATE_DOMAIN); ?> <?php the_time('Y'); ?>
<?php } ?>
</h2>

<?php } else if (is_single()) { ?>

<?php } else if (is_search()) { ?>

<h2 id="post-header"><?php _e('Search result for', TEMPLATE_DOMAIN); ?> &quot; <?php the_search_query(); ?> &quot;</h2>

<?php } ?>