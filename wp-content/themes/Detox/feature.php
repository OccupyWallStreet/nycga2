<div id="featured">
  		
<div id="glidercontent" class="glidecontentwrapper">
 
<?php 
	$slidecat = get_option('Detox_slide_category'); 
	$my_query = new WP_Query('category_name= '. $slidecat .'&showposts=4&offset=0');
while ($my_query->have_posts()) : $my_query->the_post();$do_not_duplicate = $post->ID;
?>

<div class="glidecontent">

<div class="hentry">
<div class="lead">
<?php $image = get_the_post_thumbnail($post->ID, 'slider'); ?>
<?php echo $image; ?>
</div>

<div class="txt">
<h2><a href="<?php the_permalink() ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
<?php the_excerpt(__(''));?>
<div class="read"><a title="<?php _e( 'Read more here', 'Detox') ?>" href="<?php the_permalink() ?>"><?php _e( 'Read on', 'Detox') ?> </a></div>
</div>

</div>

</div>
<?php endwhile; ?>

</div>

<div id="togglebox" class="glidecontenttoggler">
<a href="#" class="prev"></a> 
<?php 
	$slidecat = get_option('Detox_slide_category');  
	$my_query = new WP_Query('category_name= '. $slidecat .'&showposts=4&offset=0');
while ($my_query->have_posts()) : $my_query->the_post();$do_not_duplicate = $post->ID;
?>

<a href="#" class="toc"> 
<?php $image = get_the_post_thumbnail($post->ID, 'teaser'); ?>
<?php echo $image; ?>
<small><?php
$category = get_the_category();
echo $category[0]->cat_name;
?></small>
<span class="fet"><?php the_title(); ?></span>
</a>
<?php endwhile; ?> 

<a href="#" class="next"></a>
</div>

<div id="cright">

<?php if ( !function_exists('dynamic_sidebar')
	        || !dynamic_sidebar('feature_bar') ) : ?>
	        
<?php
if ( function_exists( 'bp_is_active' ) ){
get_template_part('bpbar');
} else {
get_template_part('login');
}
?>

<?php endif; ?>

</div>
</div>

<?php
if ( function_exists( 'bp_is_active' ) ){
	get_template_part('style');
} else {
}
?>