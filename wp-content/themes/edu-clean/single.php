<?php get_header(); ?>

<div id="post-entry">

<?php if (have_posts()) : ?>

<?php locate_template (array('lib/templates/wp-template/headline.php'), true); ?>

<?php while (have_posts()) : the_post(); ?>

<div <?php if(function_exists("post_class")) : ?><?php post_class(); ?><?php else: ?>class="post"<?php endif; ?> id="post-<?php the_ID(); ?>">

<h1 class="post-title"><?php the_title(); ?></h1>

<div class="post-author">

<div class="alignleft">

<?php
$if_author_on = get_option('tn_edus_author_avatar'); ?>
<?php if($if_author_on == 'yes') { ?>
<?php if(function_exists("bp_post_author_avatar")) : ?>
<?php bp_post_author_avatar(); ?>
<?php else: ?>
<?php $author_email = get_the_author_meta('email'); echo get_avatar($author_email,'20'); ?>
<?php endif; ?>&nbsp;
<?php } ?>

<?php _e('by', TEMPLATE_DOMAIN); ?> <?php if( $bp_existed == 'true' ) { ?> <?php printf( __( '%s',TEMPLATE_DOMAIN), bp_core_get_userlink( $post->post_author ) ) ?><?php } else { ?> <?php the_author_posts_link(); ?><?php } ?>&nbsp;<?php _e('in', TEMPLATE_DOMAIN); ?>&nbsp;<?php the_time('F jS Y') ?></div>

<div class="alignright">
<span class="coms-post"><?php comments_popup_link(__('Comments (0)',TEMPLATE_DOMAIN), __('Comments (1)',TEMPLATE_DOMAIN), __('Comments (%)',TEMPLATE_DOMAIN)); ?></span><?php edit_post_link(__('edit',TEMPLATE_DOMAIN)); ?>
</div>
</div>


<div class="post-blog-content">
<?php $facebook_like_status = get_option('tn_edus_facebook_like_status'); if ($facebook_like_status == 'enable') { ?>
<iframe src="http://www.facebook.com/plugins/like.php?href=<?php echo urlencode(get_permalink($post->ID)); ?>&amp;layout=standard&amp;show_faces=false&amp;width=450&amp;action=like&amp;colorscheme=light" scrolling="no" frameborder="0" allowTransparency="true" style="border:none; overflow:hidden; width:450px; height:30px"></iframe>
<?php } ?>
<?php the_content(__('...click here to read more',TEMPLATE_DOMAIN)); ?>
</div>



<?php
$if_cat_on = get_option('tn_edus_post_cat'); ?>
<?php if($if_cat_on == 'yes') { ?>
<div class="post-under">
<?php _e('Category:', TEMPLATE_DOMAIN); ?> <?php the_category(', ') ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php if(function_exists("the_tags")) : ?><?php the_tags() ?><?php endif; ?></div>
<?php } ?>


<?php
$if_social_on = get_option('tn_edus_social'); ?>
<?php if($if_social_on == 'yes') { ?>
<?php locate_template (array('lib/templates/wp-template/social.php'), true); ?>
<?php } ?>
</div>
<?php endwhile; ?>


<?php if ( comments_open() ) { ?> <?php comments_template('',true); ?><?php } ?>


<?php locate_template (array('lib/templates/wp-template/paginate.php'), true); ?>

<?php else: ?>

<?php locate_template (array('lib/templates/wp-template/result.php'), true); ?>

<?php endif; ?>

</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>