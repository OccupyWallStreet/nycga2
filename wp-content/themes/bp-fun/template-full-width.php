<?php
/*
Template Name: Full Width
*/
?>

<?php get_header(); ?>

<div class="full-width" id="post-entry">

<?php if (have_posts()) : ?>

<?php locate_template ( array('lib/templates/wp-template/headline.php'), true ); ?>

<?php while (have_posts()) : the_post(); ?>

<div <?php if(function_exists("post_class")) : ?><?php post_class(); ?><?php else: ?>class="post"<?php endif; ?> id="post-<?php the_ID(); ?>">

<h1 class="post-title"><?php the_title(); ?></h1>

<div class="post-content">
<?php the_content(); ?>
<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
<p><?php edit_post_link(__('edit', TEMPLATE_DOMAIN)); ?></p>
</div>

<?php if ( comments_open() ) { ?>
<div class="post-tagged">
<p class="com">
<?php comments_popup_link(__('Leave Comments &rarr;',TEMPLATE_DOMAIN), __('One Comment &rarr;',TEMPLATE_DOMAIN), __('% Comments &rarr;',TEMPLATE_DOMAIN)); ?>
</p>
</div>
<?php } ?>


</div>


<?php endwhile; ?>

<?php if ( comments_open() ) { ?> <?php comments_template('', true); ?><?php } ?>

<?php else: ?>

<?php locate_template ( array('lib/templates/wp-template/result.php'), true ); ?>

<?php endif; ?>

</div>

<?php get_footer(); ?>