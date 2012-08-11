<?php get_header(); ?>

<div id="post-entry">

<h2><?php _e("Sorry the post you looking for have been removed, feel free to browse our other post",TEMPLATE_DOMAIN); ?></h2>

<div class="post-content">
<ul>
<?php $archive_query = new WP_Query('showposts=100');
while ($archive_query->have_posts()) : $archive_query->the_post(); ?>
<li><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a> (<?php comments_number('0', '1', '%'); ?>)</li>
<?php endwhile; ?>
</ul>
</div>

</div>



<?php get_sidebar(); ?>

<?php get_footer(); ?>