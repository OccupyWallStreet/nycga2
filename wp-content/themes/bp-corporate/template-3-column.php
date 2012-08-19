<?php
/*
Template Name: 3 Column Widgets
*/
?>


<?php get_header(); ?>
<div id="post-entry" class="home-column">

<?php if (have_posts()) : ?><?php while (have_posts()) : the_post(); ?>

<div <?php if(function_exists("post_class")) : ?><?php post_class(); ?><?php else: ?>class="post"<?php endif; ?> id="post-<?php the_ID(); ?>">

<h1 class="post-title"><?php the_title(); ?></h1>

<div class="post-author"><?php /*the_time('jS F Y') ?> <?php _e( 'by', TEMPLATE_DOMAIN ) ?> <?php if( $bp_existed == 'true' ) { ?><?php printf( __( '%s',TEMPLATE_DOMAIN), bp_core_get_userlink( $post->post_author ) ) ?><?php } else { ?> <?php the_author_posts_link(); ?><?php } ?> <?php _e( 'under', TEMPLATE_DOMAIN ) ?> <?php the_category(', ') ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php edit_post_link(__('edit', TEMPLATE_DOMAIN)); */?></div>

<?php //include( TEMPLATEPATH . '/lib/templates/wp-template/social.php'); ?>

<div class="post-content">
<?php the_content(); ?>

<?php /*
$facebook_like_status = get_option('tn_buddycorp_facebook_like_status'); if ($facebook_like_status == 'enable') { ?>
<iframe src="http://www.facebook.com/plugins/like.php?href=<?php echo urlencode(get_permalink($post->ID)); ?>&amp;layout=standard&amp;show_faces=false&amp;width=450&amp;action=like&amp;colorscheme=light" scrolling="no" frameborder="0" allowTransparency="true" style="border:none; overflow:hidden; width:450px; height:30px"></iframe>
<?php }
*/ ?>

</div>


<div class="post-tagged">
<?php if(has_tag()) { ?>
<p class="tags">
<?php if(function_exists("the_tags")) : ?>
<?php the_tags(__('tagged in&nbsp;', TEMPLATE_DOMAIN), ', ', ''); ?>
<?php endif; ?>
</p>
<?php } ?>
</div>

</div>

<?php endwhile; ?>
<?php endif; ?>

<?php if ( is_active_sidebar( __('left-column', TEMPLATE_DOMAIN ) ) ) : ?>
<?php dynamic_sidebar( __('left-column', TEMPLATE_DOMAIN ) ); ?>
<?php endif; ?>

</div>

<?php locate_template( array( 'home-sidebar.php'), true ); ?>

<?php get_footer(); ?>