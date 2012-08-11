<?php
/*
Template Name: Full Width Page
*/
?>

<?php get_header(); ?>

<!-- full width page template -->

<div id="post-entry full">

<?php if (have_posts()) : ?>

<?php locate_template (array('lib/templates/wp-template/headline.php'), true); ?>

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

<?php locate_template (array('lib/templates/wp-template/paginate.php'), true); ?>

<?php else: ?>

<?php locate_template (array('lib/templates/wp-template/result.php'), true); ?>

<?php endif; ?>

</div>


<?php get_footer(); ?>