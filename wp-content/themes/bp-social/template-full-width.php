<?php
/*
Template Name: Full width
*/
?>

<?php get_header(); ?>

<?php print "<style type=\"text/css\" media=\"all\"> "; ?>
#blog-entry, #post-entry, #post-entry .post-info {
width: 100% !important;
padding: 0px;
border: 0px none !important;
}
<?php print "</style>"; ?>

<div id="blog-entry">
<div id="post-entry">

<?php if (have_posts()) : ?>

<?php while (have_posts()) : the_post(); $the_post_ids = get_the_ID(); ?>

<div <?php if(function_exists("post_class")) : ?><?php post_class(); ?><?php else: ?>class="post"<?php endif; ?> id="post-<?php the_ID(); ?>">


<div class="post-meta">
<div class="post-info">
<h1 class="post-title"><?php the_title(); ?></h1>
</div>
</div>

<div class="post-content">
<?php the_content(); ?>
<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>

<?php edit_post_link(__('edit', TEMPLATE_DOMAIN), '<p>', '</p>'); ?>
</div>
</div>


<?php endwhile; ?>

<?php if ( comments_open() ) { ?><?php comments_template('', true); ?><?php } ?>

<?php endif; ?>


</div>

</div>


<?php get_footer(); ?>