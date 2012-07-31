<?php get_header(); ?>
<div id="mid" class="fix">
<div id="single" class="fix"><a name="main"></a>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<h1><?php the_title() ?></h1>

<div id="dias">
<div class="post" id="post-<?php the_ID(); ?>">
<div class="entry">
<?php get_template_part('content'); ?>
</div>
</div>

<?php comments_template(); ?>

<?php endwhile; ?>
<?php else: ?>
<?php get_template_part('result'); ?>
<?php endif; ?>

</div>

<div id="dia">
<?php get_template_part('dia'); ?>
</div>

</div>
</div>
<?php get_footer(); ?>