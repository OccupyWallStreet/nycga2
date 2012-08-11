<?php get_header(); ?>

<div id="post-entry">

<?php if (have_posts()) : ?>

<?php locate_template( array( 'lib/templates/wp-template/headline.php' ), true); ?>

<?php while (have_posts()) : the_post(); ?>

<div <?php if(function_exists("post_class")) : ?><?php post_class(); ?><?php else: ?>class="post"<?php endif; ?> id="post-<?php the_ID(); ?>">

<h1 class="post-title"><?php the_title(); ?></h1>

<div class="post-content">
<?php the_content(); ?>
<?php edit_post_link(__('edit', TEMPLATE_DOMAIN), '<p>', '</p>'); ?>
<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
</div>


</div>


<?php endwhile; ?>

<?php else: ?>

<?php locate_template( array( 'lib/templates/wp-template/result.php'), true ); ?>

<?php endif; ?>

</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>