<?php get_header(); ?>

<div id="post-entry">

<?php if (have_posts()) : ?>

<?php while (have_posts()) : the_post(); ?>

<div <?php if(function_exists("post_class")) : ?><?php post_class(); ?><?php else: ?>class="post"<?php endif; ?> id="post-<?php the_ID(); ?>">

<h1 class="post-title"><?php the_title(); ?></h1>
 
<div class="post-blog-content">
<?php the_content(); ?>
<p><?php edit_post_link(__('Edit Page', TEMPLATE_DOMAIN)); ?></p>
</div>

</div>

<?php endwhile; ?>

<?php if ( comments_open() ) { ?> <?php comments_template('',true); ?><?php } ?>

<?php else: ?>

<?php locate_template (array('lib/templates/wp-template/result.php'), true); ?>

<?php endif; ?>

</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>