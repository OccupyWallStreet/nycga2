<?php get_header(); ?>

<div id="content">
<div id="middle">

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<div class="sl"></div>
<div id="sfeatured">
<?php
if (function_exists('vp_get_thumb_url')) {
        $thumb=vp_get_thumb_url($post->post_content, 'bigg');
}
?>
<img src="<?php if ($thumb!='') echo $thumb; ?>" alt="<?php the_title(); ?>" />
<h1><?php the_title(); ?></h1>
</div>

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
 
<div class="content-title">
            <?php the_category(' <span>/</span> '); ?>
            <a href="http://facebook.com/share.php?u=<?php the_permalink() ?>&amp;t=<?php echo urlencode(the_title('','', false)) ?>" target="_blank" class="f" title="<?php _e('Share on Facebook', 'Detox') ?>"></a>
            <a href="http://twitter.com/home?status=<?php the_title(); ?> <?php the_permalink() ?>" target="_blank" class="t" title="<?php _e('Spread the word on Twitter', 'Detox') ?>"></a>
            <a href="http://digg.com/submit?phase=2&amp;url=<?php the_permalink() ?>&amp;title=<?php the_title(); ?>" target="_blank" class="di" title="<?php _e('Bookmark on Del.icio.us', 'Detox') ?>"></a>
            <a href="http://stumbleupon.com/submit?url=<?php the_permalink() ?>&amp;title=<?php echo urlencode(the_title('','', false)) ?>" target="_blank" class="su" title="<?php _e('Share on StumbleUpon', 'Detox') ?>"></a>
</div>
        
<span class="date"><?php _e( 'on', 'Detox') ?> <?php the_time('M'); ?><span class="bigdate"><?php the_time('j'); ?></span></span>
<div class="meta">
<?php _e( 'by', 'Detox') ?> <?php the_author_posts_link(); ?> | <?php comments_popup_link('Leave a Comment', '1 Comment', '% Comments'); ?> | 
<?php edit_post_link('Edit','',' | '); ?>
</div>
	
<div class="entry">
<div class="sentry"><?php the_content(__('Read more', 'Detox'));?></div>
<div class="social">
<h3><?php _e( 'Related', 'Detox') ?></h3>

<?php
$tags = wp_get_post_tags($post->ID);
if ($tags) {
  echo '';
  $first_tag = $tags[0]->term_id;
  $args=array(
    'tag__in' => array($first_tag),
    'post__not_in' => array($post->ID),
    'showposts'=>4,
    'caller_get_posts'=>1
   );
  $my_query = new WP_Query($args);
  if( $my_query->have_posts() ) {
    while ($my_query->have_posts()) : $my_query->the_post(); ?>
    
    <div class="f72">
    
    <div class="limg">
    <div class="hg">
<a href="<?php the_permalink() ?>">
<?php
if (function_exists('vp_get_thumb_url')) {
        $thumb=vp_get_thumb_url($post->post_content, 'sth');
}
?>
<img src="<?php if ($thumb!='') echo $thumb; ?>" alt="<?php the_title(); ?>" />
</a>
</div>
    <div class="lound"><a href="<?php the_permalink() ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></div>
    </div>
  
    </div>
      <?php
    endwhile;
  }
}
?>

</div>

</div>
</div>

<div class="clearfix"></div><hr class="clear" />
<?php wp_link_pages('before=<div class="navigation">&after=</div>'); ?>

<div class="clearfix"></div><hr class="clear" />

 <div class="post-navigation clear">
                <?php
                    $prev_post = get_adjacent_post(false, '', true);
                    $next_post = get_adjacent_post(false, '', false); ?>
                    <?php if ($prev_post) : $prev_post_url = get_permalink($prev_post->ID); $prev_post_title = $prev_post->post_title; ?>
                        <a class="post-prev" href="<?php echo $prev_post_url; ?>"><em><?php _e('Previous post', 'Detox') ?></em><span><?php echo $prev_post_title; ?></span></a>
                    <?php endif; ?>
                    <?php if ($next_post) : $next_post_url = get_permalink($next_post->ID); $next_post_title = $next_post->post_title; ?>
                        <a class="post-next" href="<?php echo $next_post_url; ?>"><em><?php _e('Next post', 'Detox') ?></em><span><?php echo $next_post_title; ?></span></a>
                    <?php endif; ?>
                <div class="line"></div>
            </div>
            
<div class="sl"></div>

<div class="postspace"></div>
<?php comments_template(); ?> 

<?php endwhile; else: ?>
<p><?php _e('Sorry, no posts matched your criteria.', 'Detox') ?></p>
<?php endif; ?>

</div>

<?php get_template_part('bar'); ?>

</div>
<?php get_footer(); ?>