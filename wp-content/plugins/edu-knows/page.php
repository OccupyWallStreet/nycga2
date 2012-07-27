<?php get_header(); ?>

<div class="entry-box">

<?php if (have_posts()) : ?>

<?php locate_template ( array('headline.php'), true ); ?>

<?php while (have_posts()) : the_post(); ?>

<div class="post-meta" id="post-<?php the_ID(); ?>">

<h1><?php the_title(); ?></h1>


<div class="post-content">
<?php the_content(); ?>
<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
</div>


</div>

<?php endwhile; ?>

<?php if ( comments_open() ) { ?> <?php comments_template('',true); ?><?php } ?>

<?php locate_template ( array('paginate.php'), true ); ?>

<?php else: ?>

<?php locate_template ( array('result.php'), true ); ?>

<?php endif; ?>

</div>

<?php get_footer(); ?>