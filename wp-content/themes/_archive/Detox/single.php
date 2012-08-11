<?php get_header(); ?>

<div id="content">
<div id="contentmiddle3">

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<div class="sl"></div>

<div class="content-title">
            <?php the_category(' <span>/</span> '); ?>
            <a href="http://facebook.com/share.php?u=<?php the_permalink() ?>&amp;t=<?php echo urlencode(the_title('','', false)) ?>" target="_blank" class="f" title="Share on Facebook"></a>
            <a href="http://twitter.com/home?status=<?php the_title(); ?> <?php echo getTinyUrl(get_permalink($post->ID)); ?>" target="_blank" class="t" title="Spread the word on Twitter"></a>
            <a href="http://digg.com/submit?phase=2&amp;url=<?php the_permalink() ?>&amp;title=<?php the_title(); ?>" target="_blank" class="di" title="Bookmark on Del.icio.us"></a>
            <a href="http://stumbleupon.com/submit?url=<?php the_permalink() ?>&amp;title=<?php echo urlencode(the_title('','', false)) ?>" target="_blank" class="su" title="Share on StumbleUpon"></a>
</div>
        
<h1><?php the_title(); ?></h1>

<span class="date"><?php _e( 'on', 'Detox') ?> <?php the_time('M'); ?><span class="bigdate"><?php the_time('j'); ?></span></span>
<div class="meta">
<?php _e( 'by', 'Detox') ?> <?php the_author_posts_link(); ?> | <?php comments_popup_link('Leave a Comment', '1 Comment', '% Comments'); ?> | <a href="javascript:window.print();"><?php _e( 'Print the article', 'Detox') ?></a> | 
<?php edit_post_link('Edit','',' | '); ?>
</div>
	
<div class="entry">
<?php the_content(__('Read more'));?>
<div class="clearfix"></div><hr class="clear" />
<?php wp_link_pages('before=<div class="navigation">&after=</div>'); ?>
</div>

<div class="meta">

<div class="postauthor">
<?php if ( get_the_author_meta( 'description' ) ) :  ?>

<div id="author-description">
<h5><?php printf( esc_attr__( 'About %s' ), get_the_author() ); ?> :</h5>
<div class="author-avatar">
<?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'author_bio_avatar_size', 60 ) ); ?>
</div>

<?php the_author_meta( 'description' ); ?> | <a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"><?php printf( __( 'View all posts by %s <span class="meta-nav">&rarr;</span>', 'Tracks' ), get_the_author() ); ?></a>
</div>
<?php endif; ?>
</div>

<div class="rel">
<h3><?php _e( 'Related', 'Detox') ?></h3>
<?php 
$max_articles = 4; // How many articles to display 
echo '<ul>'; 
$cnt = 0; $article_tags = get_the_tags(); 
$tags_string = ''; 
if ($article_tags) { 
foreach ($article_tags as $article_tag) { 
$tags_string .= $article_tag->slug . ','; 
} 
} 
$tag_related_posts = get_posts('exclude=' . $post->ID . '&numberposts=' . $max_articles . '&tag=' . $tags_string); 
if ($tag_related_posts) { 
foreach ($tag_related_posts as $related_post) { 
$cnt++; 
echo '<li class="child-' . $cnt . '">'; 
echo '<a href="' . get_permalink($related_post->ID) . '">'; 
echo $related_post->post_title . '</a></li>'; 
} 
} 
if ($cnt < $max_articles) { 
$article_categories = get_the_category($post->ID); 
$category_string = ''; 
foreach($article_categories as $category) { 
$category_string .= $category->cat_ID . ','; 
} 
$cat_related_posts = get_posts('exclude=' . $post->ID . '&numberposts=' . $max_articles . '&category=' . $category_string); 
if ($cat_related_posts) { 
foreach ($cat_related_posts as $related_post) { 
$cnt++; 
if ($cnt > $max_articles) break; 
echo '<li class="child-' . $cnt . '">'; 
echo '<a href="' . get_permalink($related_post->ID) . '">'; 
echo $related_post->post_title . '</a></li>'; 
} 
} 
} 
echo '</ul>'; 
?>
</div>

</div>

<div class="clearfix"></div><hr class="clear" />

<?php get_template_part('ad'); ?>
<div class="postspace"></div>
<div class="sl"></div>

<div class="postspace"></div>
<?php comments_template(); // Get wp-comments.php template ?>

<?php endwhile; else: ?>
<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
<?php endif; ?>

<div class="sl"></div>
<div class="navleft"><?php previous_post('&laquo; %','','yes') ?></div>
<div class="navright"><?php next_post(' % &raquo;','','yes') ?></div>

</div>
</div>

<?php get_template_part('bar'); ?>
</div>
<?php get_footer(); ?>