<?php get_header(); ?>

<?php get_sidebar('left'); //init left sidebar ?>

<div id="post-entry">

<?php if (have_posts()) : ?>

<?php locate_template ( array('lib/templates/wp-template/headline.php'), true ); ?>

<?php while (have_posts()) : the_post(); $author_email = get_the_author_meta('email'); $the_post_ids = get_the_ID();
$the_post_title = get_the_title(); ?>

<div <?php if(function_exists("post_class")) : ?><?php post_class(); ?><?php else: ?>class="post"<?php endif; ?> id="post-<?php the_ID(); ?>">


<div class="post-meta">
<div class="post-avatar">
<?php if(function_exists("bp_post_author_avatar")) : ?>
<?php bp_post_author_avatar(); ?>
<?php else: ?>
<?php echo get_avatar($author_email,'32'); ?>
<?php endif; ?>
</div>

<div class="post-info">
<h1 class="post-title"><?php the_title(); ?></h1>
<p><?php the_time( 'j F, Y', TEMPLATE_DOMAIN ); ?>&nbsp;&nbsp;&nbsp;<?php edit_post_link(__('edit', TEMPLATE_DOMAIN), '', ''); ?><br /><?php _e('Published by', TEMPLATE_DOMAIN); ?> <?php if( $bp_existed == 'true' ) { ?><?php printf( __( '%s',TEMPLATE_DOMAIN), bp_core_get_userlink( $post->post_author ) ) ?><?php } else { ?> <?php the_author_posts_link(); ?><?php } ?> <?php _e('in', TEMPLATE_DOMAIN); ?> <?php the_category(', ') ?></p>
</div>
</div>

<div class="post-content">
<?php $facebook_like_status = get_option('tn_buddysocial_facebook_like_status'); if ($facebook_like_status == 'enable') { ?>
<div class="fb-like" data-href="<?php echo urlencode(get_permalink($post->ID)); ?>" data-send="false" data-layout="standard" data-width="450" data-show-faces="false" data-font="arial" style="margin-bottom: 6px;"></div>
<?php } ?>
<?php the_content(); ?>

</div>

<div class="post-tag">
<?php if(has_tag()) { ?><span class="tags"><?php the_tags(__('tags:&nbsp;', TEMPLATE_DOMAIN), ', ', ''); ?></span><?php } ?>
</div>
</div>

<?php endwhile; ?>

<?php if ( comments_open() ) { ?><?php comments_template('', true); ?><?php } ?>

<?php locate_template ( array('lib/templates/wp-template/paginate.php'), true ); ?>

<?php else: ?>

<?php locate_template ( array('lib/templates/wp-template/result.php'), true ); ?>

<?php endif; ?>


</div>


<?php get_sidebar(); ?>

<?php get_footer(); ?>