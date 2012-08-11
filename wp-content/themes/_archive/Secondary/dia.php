<div class="content-title">
            <?php the_category(' <span>/</span> '); ?>
            <a href="http://facebook.com/share.php?u=<?php the_permalink() ?>&amp;t=<?php echo urlencode(the_title('','', false)) ?>" target="_blank" class="f" title="Share on Facebook"></a>
            <a href="http://twitter.com/home?status=<?php the_title(); ?> <?php echo getTinyUrl(get_permalink($post->ID)); ?>" target="_blank" class="t" title="Spread the word on Twitter"></a>
            <a href="http://digg.com/submit?phase=2&amp;url=<?php the_permalink() ?>&amp;title=<?php the_title(); ?>" target="_blank" class="di" title="Bookmark on Del.icio.us"></a>
            <a href="http://stumbleupon.com/submit?url=<?php the_permalink() ?>&amp;title=<?php echo urlencode(the_title('','', false)) ?>" target="_blank" class="su" title="Share on StumbleUpon"></a>
</div>

<div class="meta">
<?php _e('by'); ?> <?php the_author_posts_link(); ?> | 
<?php comments_popup_link('Leave a Comment', '1 Comment', '% Comments'); ?>
<div class="date"><?php the_time('M'); ?> <span class="bigdate"><?php the_time('j'); ?></span></div>
</div>

<div class="meta">
<div class="rel">
<?php if ( get_the_author_meta( 'description' ) ) :  ?>
<div class="author-avatar alignright">
<a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"><?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'author_bio_avatar_size', 50 ) ); ?></a>
</div>

<div id="author-description">
<h3><?php printf( esc_attr__( 'About %s', 'Bruce' ), get_the_author() ); ?> :</h3>
<p><?php the_author_meta( 'description' ); ?> | <a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"><?php printf( __( 'View all posts by %s <span class="meta-nav">&rarr;</span>', 'Bruce' ), get_the_author() ); ?></a><?php edit_post_link(' | Edit','',''); ?></p>
</div>
<?php endif; ?>
</div>

<div class="rell">
<h3>Related Posts</h3>
<?php 
$max_articles = 4; // How many articles to display 
echo '<ul class="list columns">'; 
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

<div class="post-navigation clear">
                <?php
                    $prev_post = get_adjacent_post(false, '', true);
                    $next_post = get_adjacent_post(false, '', false); ?>
                    <?php if ($prev_post) : $prev_post_url = get_permalink($prev_post->ID); $prev_post_title = $prev_post->post_title; ?>
                        <a class="post-prev" href="<?php echo $prev_post_url; ?>"><em>Previous post</em><span><?php echo $prev_post_title; ?></span></a>
                    <?php endif; ?>
                    <?php if ($next_post) : $next_post_url = get_permalink($next_post->ID); $next_post_title = $next_post->post_title; ?>
                        <a class="post-next" href="<?php echo $next_post_url; ?>"><em>Next post</em><span><?php echo $next_post_title; ?></span></a>
                    <?php endif; ?>
                
</div>

<?php if ( !function_exists('dynamic_sidebar')
		        || !dynamic_sidebar('sbar') ) : ?>
	        
<?php endif; ?>		        