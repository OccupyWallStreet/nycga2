<?php
/*
Template Name: Blog and News
*/
?>

<?php get_header(); ?>

<div id="post-entry">

<?php do_action( 'bp_before_blog_home' ) ?>

<?php
global $more; $more = 0;
$max_num_post = get_option('posts_per_page');
$page = (get_query_var('paged')) ? get_query_var('paged') : 1; query_posts("cat=&showposts=$max_num_post&paged=$page"); while ( have_posts() ) : the_post(); ?>

<?php do_action( 'bp_before_blog_post' ) ?>

<div <?php if(function_exists("post_class")) : ?><?php post_class(); ?><?php else: ?>class="post"<?php endif; ?> id="post-<?php the_ID(); ?>">
<h1 class="post-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e("Permalink to",TEMPLATE_DOMAIN); ?> <?php the_title(); ?>"><?php the_title(); ?></a></h1>

<div class="post-meta"><?php _e('By',TEMPLATE_DOMAIN); ?>&nbsp;<?php the_author_posts_link(); ?> <?php _e("in",TEMPLATE_DOMAIN); ?> <?php the_time('F jS Y') ?>&nbsp;&nbsp;&nbsp;<?php comments_popup_link(__('No Comment&nbsp;&raquo;',TEMPLATE_DOMAIN), __('1 Comment&nbsp;&raquo;',TEMPLATE_DOMAIN), __('% Comments&nbsp;&raquo;',TEMPLATE_DOMAIN)); ?>
</div>


<div class="post-content">
<?php do_action( 'bp_before_post_content' ) ?>
<?php the_content( __('...Click here to read more',TEMPLATE_DOMAIN) ); ?>
<?php do_action( 'bp_after_post_content' ) ?>
</div>

<div class="post-meta">
<?php _e('Filed Under',TEMPLATE_DOMAIN); ?> <?php the_category(', ') ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php the_tags(__('Tags: ',TEMPLATE_DOMAIN), ', ', ''); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php edit_post_link(__('Edit',TEMPLATE_DOMAIN)); ?>
</div>

</div>

<?php do_action( 'bp_after_blog_post' ) ?>

<?php endwhile; ?>

<div id="post-navigator">
<div class="alignright"><?php next_posts_link( __( '&laquo; Previous Entries', TEMPLATE_DOMAIN ) ) ?></div>
<div class="alignleft"><?php previous_posts_link( __( 'Next Entries &raquo;', TEMPLATE_DOMAIN ) ) ?></div>
</div>


<?php do_action( 'bp_after_blog_home' ) ?>

</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>