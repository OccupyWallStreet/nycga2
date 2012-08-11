<div id="heads">

<div class="hads">
<?php if ( !function_exists('dynamic_sidebar')
	        || !dynamic_sidebar('twitter') ) : ?>
<?php twitter_messages('milo317', 1, false, true, '&#187;', true, false, true); ?>
<?php endif; ?>
</div>

<div class="twt">
<?php $urltweet = get_option('Detox_urltweet'); ?>
<a href="<?php echo ($urltweet); ?>" rel="bookmark" title="<?php _e( 'Follow us on Twitter', 'Detox') ?>"><?php _e( 'Follow us on Twitter', 'Detox') ?></a>
</div>

<?php get_template_part('searchform'); ?>

<div id="feed">
<?php 
$url25 = get_option('Detox_url25'); 
?>
<a href="<?php echo ($url25); ?>" class="rss">RSS</a>
</div>

</div>