<?php get_header(); ?>
<div id="mid" class="fix">
<div id="single" class="fix"><a name="main"></a>

<h1><?php the_title() ?></h1>

<div id="content">
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<div class="post" id="post-<?php the_ID(); ?>">
<div class="entry">
<?php get_template_part('content'); ?>
</div>
</div>
<?php endwhile; ?>
<?php get_template_part('paginate'); ?>
<?php else: ?>
<?php get_template_part('result'); ?>
<?php endif; ?>

</div>
</div>
</div>
<?php get_footer(); ?>