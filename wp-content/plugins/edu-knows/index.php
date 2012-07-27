<?php get_header(); ?>

<div class="entry-box">

<?php if (have_posts()) : ?>

<?php locate_template ( array('includes/headline.php'), true ); ?>

<?php while (have_posts()) : the_post(); $author_email = get_the_author_meta('email'); ?>

<div <?php if(function_exists("post_class")) : ?><?php post_class('post-meta'); ?><?php else: ?>class="post"<?php endif; ?> id="post-<?php the_ID(); ?>">

<div class="post-meta-info">
<?php $if_author_on = get_option('tn_edufaq_post_gravatar'); ?>
<?php if($if_author_on != 'no') { ?><?php echo get_avatar($author_email, '50'); ?><?php } ?>
<h1><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h1>
<div class="post-stats">
<?php _e('By', TEMPLATE_DOMAIN); ?> <?php the_author_posts_link(); ?> <?php _e('on', TEMPLATE_DOMAIN); ?> <?php the_time('F jS Y') ?>  <?php if ( comments_open() ) { ?> - <?php comments_popup_link(__('No Comment', TEMPLATE_DOMAIN), __('1 Comment', TEMPLATE_DOMAIN), __('% Comments', TEMPLATE_DOMAIN)); ?><?php } ?>&nbsp;&nbsp;&nbsp;<?php edit_post_link(__('edit', TEMPLATE_DOMAIN)); ?>
</div>
</div>


<div class="post-content">

<?php if( is_date() || is_search() || is_tag() || is_author() ) { ?>

<?php wp_custom_post_thumbnail($the_post_id=get_the_ID(), $with_wrap='', $wrap_w='', $wrap_h='', $title=get_the_title(), $fetch_size='medium',$fetch_w='200', $fetch_h='auto',$alt_class='alignleft feat-thumb'); ?>

<?php the_excerpt(); ?>

<?php } else { ?>

<?php the_content(__('...click here to read more', TEMPLATE_DOMAIN)); ?>
<?php wp_link_pages('before=<p>&after=</p>'); ?>
<?php } ?>

</div>

<div class="post-stats">
<?php _e('Filed Under:', TEMPLATE_DOMAIN); ?>&nbsp;<?php the_category(', ') ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php if(function_exists("the_tags")) : ?><?php the_tags() ?><?php endif; ?>
</div>

<?php include (TEMPLATEPATH . '/includes/social.php'); ?>

</div>

<?php endwhile; ?>

<?php locate_template ( array('includes/paginate.php'), true ); ?>

<?php else: ?>

<?php locate_template ( array('include/result.php'), true ); ?>

<?php endif; ?>

</div>

<?php get_footer(); ?>