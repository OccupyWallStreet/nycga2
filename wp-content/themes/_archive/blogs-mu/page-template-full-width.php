<?php
/*
Template Name: Full Width
*/
?>

<?php get_header(); ?>

<div class="full-width" id="post-entry">

<?php if (have_posts()) : ?>

<?php while (have_posts()) : the_post(); ?>

<div <?php if(function_exists("post_class")) : ?><?php post_class(); ?><?php else: ?>class="page"<?php endif; ?> id="post-<?php the_ID(); ?>">
<h1 class="post-title"><?php the_title(); ?></h1>

<?php if ( comments_open() ) { ?>
<div class="post-meta"><?php _e('By',TEMPLATE_DOMAIN); ?>&nbsp;<?php the_author_posts_link(); ?> <?php _e("in",TEMPLATE_DOMAIN); ?> <?php the_time('F jS Y') ?>&nbsp;&nbsp;&nbsp;<?php comments_popup_link(__('No Comment&nbsp;&raquo;',TEMPLATE_DOMAIN), __('1 Comment&nbsp;&raquo;',TEMPLATE_DOMAIN), __('% Comments&nbsp;&raquo;',TEMPLATE_DOMAIN)); ?>
</div>
<?php } ?>


<div class="post-content">
<?php the_content(); ?>
<?php wp_link_pages('before=<p>&after=</p>'); ?>
<?php edit_post_link(__('Edit This',TEMPLATE_DOMAIN), '<p>', '</p>'); ?>
</div>


</div>

<?php endwhile; ?>

<?php if ( comments_open() ) { ?> <?php comments_template('',true); ?><?php } ?>

<?php else: ?>

<?php locate_template ( array('lib/templates/wp-template/result.php'), true ); ?>

<?php endif; ?>


</div>

<?php get_footer(); ?>