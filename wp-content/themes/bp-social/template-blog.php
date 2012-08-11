<?php
/*
Template Name: Blog and News
*/
?>


<?php get_header(); ?>

<?php get_sidebar('left'); //init left sidebar ?>

<div id="post-entry">

<?php
global $more; $more = 0;
$max_num_post = get_option('posts_per_page');
$page = (get_query_var('paged')) ? get_query_var('paged') : 1; query_posts("cat=&showposts=$max_num_post&paged=$page"); while ( have_posts() ) : the_post();
$author_email = get_the_author_meta('email'); $the_post_ids = get_the_ID(); $the_post_title = get_the_title();
?>


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
<h1 class="post-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('permalink to', TEMPLATE_DOMAIN); ?> <?php the_title(); ?>"><?php the_title(); ?></a></h1>
<?php $post_meta_status = get_option('tn_buddysocial_post_meta_status'); if($post_meta_status != 'disable') { ?>
<p><?php the_time( 'j F, Y', TEMPLATE_DOMAIN ); ?>&nbsp;&nbsp;&nbsp;<?php edit_post_link(__('edit', TEMPLATE_DOMAIN), '', ''); ?><br /><?php _e('Published by', TEMPLATE_DOMAIN); ?> <?php if( $bp_existed == 'true' ) { ?><?php printf( __( '%s',TEMPLATE_DOMAIN), bp_core_get_userlink( $post->post_author ) ) ?><?php } else { ?>
<?php _e('by',TEMPLATE_DOMAIN); ?> <?php the_author_posts_link(); ?><?php } ?> <?php _e('in', TEMPLATE_DOMAIN); ?> <?php the_category(', ') ?></p>
<?php } ?>
</div>

</div>

<div class="post-content">
<?php
$post_style = get_option('tn_buddysocial_blog_post_style');
$post_meta_status = get_option('tn_buddysocial_post_meta_status');
if($post_style == '' || $post_style == 'full post') { ?>
<?php the_content(__('...Read more &raquo;', TEMPLATE_DOMAIN) ); ?>
<?php } elseif($post_style == 'excerpt post') { ?>
<?php echo custom_the_content(70); ?>
<?php } elseif($post_style == 'featured thumbnail with excerpt post') { ?>
<?php wp_custom_post_thumbnail($the_post_id=get_the_ID(), $with_wrap='', $wrap_w='', $wrap_h='', $title=get_the_title(), $fetch_size='medium', $fetch_w='200', $fetch_h='200', $alt_class='alignleft feat-thumb'); ?>
<?php echo the_excerpt(); ?>
<?php } ?>
<?php $facebook_like_status = get_option('tn_buddysocial_facebook_like_status'); if ($facebook_like_status == 'enable') { ?>
<div style="margin-top: 10px;">
<div class="fb-like" data-href="<?php echo urlencode(get_permalink($post->ID)); ?>" data-send="false" data-layout="standard" data-width="450" data-show-faces="false" data-font="arial" style="margin-bottom: 6px;"></div>
</div>
<?php } ?>
</div>

<div class="post-tag">
<?php if(has_tag()) { ?><span class="tags"><?php the_tags(__('tags:&nbsp;', TEMPLATE_DOMAIN), ', ', ''); ?></span><?php } ?>
<span class="comments"><?php comments_popup_link( __( 'No Comments &#187;', TEMPLATE_DOMAIN ), __( '1 Comment &#187;', TEMPLATE_DOMAIN ), __( '% Comments &#187;', TEMPLATE_DOMAIN ) ); ?></span></div>
</div>

<?php endwhile; ?>

<div id="post-navigator">
<div class="alignright"><?php next_post_link( __( '&laquo; Previous Entries', TEMPLATE_DOMAIN ) ) ?></div>
<div class="alignleft"><?php previous_post_link( __( 'Next Entries &raquo;', TEMPLATE_DOMAIN ) ) ?></div>
</div>


</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>