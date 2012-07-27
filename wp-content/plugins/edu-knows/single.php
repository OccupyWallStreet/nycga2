<?php get_header(); ?>
<div class="entry-box">
<?php if (have_posts()) : ?>
<?php locate_template ( array('includes/headline.php'), true ); ?>
<?php while (have_posts()) : the_post(); $author_email = get_the_author_meta('email'); ?>
<div <?php if(function_exists("post_class")) : ?><?php post_class('post-meta'); ?><?php else: ?>class="post"<?php endif; ?> id="post-<?php the_ID(); ?>">

<div class="post-meta-info">
<?php $if_author_on = get_option('tn_edufaq_post_gravatar'); ?>
<?php if($if_author_on != 'no') { ?><?php echo get_avatar($author_email, '50'); ?><?php } ?>
<h1><?php the_title(); ?></h1>
<div class="post-stats">
<?php _e('By', TEMPLATE_DOMAIN); ?> <?php the_author_posts_link(); ?> <?php _e('on', TEMPLATE_DOMAIN); ?> <?php the_time('F jS Y') ?>  <?php if ( comments_open() ) { ?> - <?php comments_popup_link(__('No Comment', TEMPLATE_DOMAIN), __('1 Comment', TEMPLATE_DOMAIN), __('% Comments', TEMPLATE_DOMAIN)); ?><?php } ?>&nbsp;&nbsp;&nbsp;<?php edit_post_link(__('edit', TEMPLATE_DOMAIN)); ?>
</div>
</div>


<div class="post-content">
<?php the_content(); ?>
</div>

<div class="post-stats">
<?php _e('Filed Under:', TEMPLATE_DOMAIN); ?>&nbsp;<?php the_category(', ') ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php if(function_exists("the_tags")) : ?><?php the_tags() ?><?php endif; ?>
</div>

<?php include (TEMPLATEPATH . '/includes/social.php'); ?>

</div>

<?php endwhile; ?>

<?php if ( comments_open() ) { ?> <?php comments_template('',true); ?><?php } ?>

<?php locate_template ( array('includes/paginate.php'), true ); ?>

<?php else: ?>

<?php locate_template ( array('includes/result.php'), true ); ?>

<?php endif; ?>

</div>

<?php get_footer(); ?>