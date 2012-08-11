<?php get_header(); ?>

<div id="post-entry">

<?php do_action( 'bp_before_blog_home' ) ?>

<?php if (have_posts()) : ?>

<?php locate_template ( array('lib/templates/wp-template/headline.php'), true ); ?>

<?php while (have_posts()) : the_post(); ?>

<?php do_action( 'bp_before_blog_post' ) ?>

<div <?php if(function_exists("post_class")) : ?><?php post_class(); ?><?php else: ?>class="post"<?php endif; ?> id="post-<?php the_ID(); ?>">
<h1 class="post-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e("Permalink to",TEMPLATE_DOMAIN); ?> <?php the_title(); ?>"><?php the_title(); ?></a></h1>

<div class="post-meta"><?php _e('By',TEMPLATE_DOMAIN); ?>&nbsp;<?php the_author_posts_link(); ?> <?php _e("in",TEMPLATE_DOMAIN); ?> <?php the_time('F jS Y') ?>&nbsp;&nbsp;&nbsp;<?php comments_popup_link(__('No Comment&nbsp;&raquo;',TEMPLATE_DOMAIN), __('1 Comment&nbsp;&raquo;',TEMPLATE_DOMAIN), __('% Comments&nbsp;&raquo;',TEMPLATE_DOMAIN)); ?>
<br />
<?php _e('Filed Under',TEMPLATE_DOMAIN); ?> <?php the_category(', ') ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php the_tags(__('Tags: ',TEMPLATE_DOMAIN), ', ', ''); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php edit_post_link(__('Edit',TEMPLATE_DOMAIN)); ?></div>


<div class="post-content">
<?php do_action( 'bp_before_post_content' ) ?>

<?php if( is_date() || is_search() || is_tag() || is_author() ) { ?>
<?php if(function_exists('the_post_thumbnail')) { ?><?php if(get_the_post_thumbnail() != "") { ?><div class="alignleft">
<?php the_post_thumbnail(); ?></div><?php } } ?>
<?php the_excerpt();?>
<?php if (function_exists('wp_ozh_wsa')) { wp_ozh_wsa("336280nocolor"); } ?>
<?php } else { ?>

<?php the_content( __('<p>Click here to read more</p>',TEMPLATE_DOMAIN) ); ?>
<?php wp_link_pages('before=<p>&after=</p>'); ?>
<?php if (function_exists('wp_ozh_wsa')) { wp_ozh_wsa("336280nocolor"); } ?>
<?php } ?>

<?php do_action( 'bp_after_post_content' ) ?>
</div>

</div>

<?php do_action( 'bp_after_blog_post' ) ?>

<?php endwhile; ?>

<?php locate_template ( array('lib/templates/wp-template/paginate.php'), true ); ?>

<?php else: ?>

<?php locate_template ( array('lib/templates/wp-template/result.php'), true ); ?>

<?php endif; ?>

<?php do_action( 'bp_after_blog_home' ) ?>

</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>