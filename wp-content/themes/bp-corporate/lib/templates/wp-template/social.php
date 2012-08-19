<?php
include( TEMPLATEPATH . '/options-var.php' );
$the_title = get_the_title();
$the_trim_title = str_replace(" ", "+", $the_title);
$the_title_permalink = get_permalink();
?>

<div class="post-social">
<?php if($tn_buddycorp_delicious_status == 'enable') { ?>
 <p class="delicious"><a href="http://del.icio.us/post?url=<?php the_permalink() ?>&amp;title=<?php echo $the_trim_title; ?>"><?php _e("Bookmark",TEMPLATE_DOMAIN); ?></a></p>
<?php } ?>

<?php if($tn_buddycorp_tweet_this_status == 'enable') { ?>
 <p class="twitter">
 <a href="http://twitter.com/home?status=<?php echo $the_trim_title; ?>: <?php echo wp_get_shortlink(); ?> " rel="nofollow"><?php _e("Tweet This",TEMPLATE_DOMAIN); ?></a></p>
<?php } ?>

<?php if($tn_buddycorp_add_to_facebook_status == 'enable') { ?>
 <p class="facebook"><a href="http://www.facebook.com/share.php?title=<?php echo $the_trim_title; ?>&amp;u=<?php the_permalink() ?>"><?php _e("Add to Facebook",TEMPLATE_DOMAIN); ?></a></p>
<?php } ?>

<?php if($tn_buddycorp_stumble_upon_status == 'enable') { ?>
 <p class="stumble"><a href="http://www.stumbleupon.com/submit?title=<?php echo $the_trim_title; ?>&amp;url=<?php the_permalink() ?>"><?php _e("Share",TEMPLATE_DOMAIN); ?></a></p>
<?php } ?>

 </div>

