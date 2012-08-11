<?php
/*
Template Name: Blog and News
*/
?>


<?php get_header(); ?>

<div id="post-entry">

<?php
global $more; $more = 0;
$max_num_post = get_option('posts_per_page');
$page = (get_query_var('paged')) ? get_query_var('paged') : 1; query_posts("cat=&showposts=$max_num_post&paged=$page"); while ( have_posts() ) : the_post();
?>

<div <?php if(function_exists("post_class")) : ?><?php post_class(); ?><?php else: ?>class="post"<?php endif; ?> id="post-<?php the_ID(); ?>">

<h1 class="post-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h1>


<div class="post-author">
<div class="alignleft">

<?php
$if_author_on = get_option('tn_edus_author_avatar'); ?>
<?php if($if_author_on == 'yes') { ?>
<?php if(function_exists("bp_post_author_avatar")) : ?>
<?php bp_post_author_avatar(); ?>
<?php else: ?>
<?php echo get_avatar($author_email,'20'); ?>
<?php endif; ?>&nbsp;
<?php } ?>

<?php _e('by', TEMPLATE_DOMAIN); ?> <?php if( $bp_existed == 'true' ) { ?> <?php printf( __( '%s',TEMPLATE_DOMAIN), bp_core_get_userlink( $post->post_author ) ) ?><?php } else { ?> <?php the_author_posts_link(); ?><?php } ?>&nbsp;<?php _e('in', TEMPLATE_DOMAIN); ?>&nbsp;<?php the_time('F jS Y') ?></div>

<div class="alignright">
<span class="coms-post"><?php comments_popup_link(__('Comments (0)', TEMPLATE_DOMAIN), __('Comments (1)', TEMPLATE_DOMAIN), __('Comments (%)', TEMPLATE_DOMAIN)); ?></span><?php edit_post_link(__('edit', TEMPLATE_DOMAIN)); ?>
</div>
</div>


<div class="post-blog-content">
<?php the_content( __('...Continue Reading', TEMPLATE_DOMAIN) ); ?>
</div>

</div>

<?php endwhile; ?>

<div id="post-navigator">
<div class="alignright"><?php next_posts_link( __( '&laquo; Previous Entries', TEMPLATE_DOMAIN) ) ?></div>
<div class="alignleft"><?php previous_posts_link( __( 'Next Entries &raquo;', TEMPLATE_DOMAIN ) ) ?></div>
</div>


</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>