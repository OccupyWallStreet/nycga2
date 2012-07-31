<div id="l_sidebar">
<?php if ( !function_exists('dynamic_sidebar')
		        || !dynamic_sidebar('leftbar') ) : ?>

<h3><?php _e( 'Archi<span>ve</span>' ) ?></h3>
<ul><?php wp_get_archives('type=monthly&show_post_count=0'); ?></ul>

<h3><?php _e( 'User <span>Photos</span>' ) ?></h3>
<?php get_flickrRSS(); ?>

<?php endif; ?>
</div>