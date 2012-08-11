<?php get_header(); ?>

<?php get_sidebar('left'); //init left sidebar ?>

<div id="post-entry">

<?php if (have_posts()) : ?>

<?php while (have_posts()) : the_post(); $author_email = get_the_author_meta('email'); $the_post_ids = get_the_ID(); $the_post_title = get_the_title(); ?>

<div <?php if(function_exists("post_class")) : ?><?php post_class(); ?><?php else: ?>class="post"<?php endif; ?> id="post-<?php the_ID(); ?>">

<div class="post-meta">
<div style="width: 100%;" class="post-info">
<h1 class="post-title"><?php the_title(); ?></h1>
</div>
</div>

<div class="post-content">
<?php $facebook_like_status = get_option('tn_buddysocial_facebook_like_status'); if ($facebook_like_status == 'enable') { ?>
<div class="fb-like" data-href="<?php echo urlencode(get_permalink($post->ID)); ?>" data-send="false" data-layout="standard" data-width="450" data-show-faces="false" data-font="arial" style="margin-bottom: 6px;"></div>
<?php } ?>
<?php the_content(); ?>
<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
</div>
</div>


<?php endwhile; ?>

<?php if ( comments_open() ) { ?><?php comments_template('', true); ?><?php } ?>

<?php else: ?>

<?php locate_template ( array('lib/templates/wp-template/result.php'), true ); ?>

<?php endif; ?>

</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>