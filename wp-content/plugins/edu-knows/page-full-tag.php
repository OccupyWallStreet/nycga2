<?php
/*
Template Name: Page Full Tag
*/
?>

<?php get_header(); ?>

<div class="entry-box">

<?php if (have_posts()) : ?>

<?php while (have_posts()) : the_post(); ?>

<div <?php if(function_exists("post_class")) : ?><?php post_class('post-meta'); ?><?php else: ?>class="post"<?php endif; ?> id="post-<?php the_ID(); ?>">

<h1><?php the_title(); ?></h1>

<div class="post-content">

<ul class="nolist">
<li>
<?php if(function_exists("wp_tag_cloud")) { ?>
<?php wp_tag_cloud('smallest=12&largest=24'); ?>
<?php } ?>
</li>
</ul>

</div>

</div>

<?php endwhile; ?>

<?php else: ?>

<?php locate_template ( array('includes/result.php'), true ); ?>  

<?php endif; ?>

</div>

<?php get_footer(); ?>