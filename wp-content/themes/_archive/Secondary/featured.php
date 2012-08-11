<div id="featured">
<h3 class="featured"><?php _e( 'Featured <span>Stories</span>' ) ?></h3>

<div id="myGallery">    

<?php 
	$my_query = new WP_Query('showposts=8&offset=0');
while ($my_query->have_posts()) : $my_query->the_post();$do_not_duplicate = $post->ID;
?>

<div class="imageElement">   
<h3><a href="<?php the_permalink() ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h3>        
<p><?php the_content_rss('', FALSE, ' ', 26); ?></p>
<div class="read">
<a title="<?php _e( 'Read' ) ?> <?php the_title(); ?>" href="<?php the_permalink() ?>"><?php _e( 'Read More' ) ?></a>
</div>
        
<a class="open" href="<?php the_permalink() ?>"></a>         
    
<img src="<?php $image_id = get_post_thumbnail_id();
$image_url = wp_get_attachment_image_src($image_id,’large’, true);
echo $image_url[0];  ?>" class="full" alt="<?php the_title(); ?>" />
<img src="<?php bloginfo('template_directory'); ?>/images/test.jpg" class="thumbnail" alt="<?php the_title(); ?>" />
</div>    

<?php endwhile; ?> 

</div>
</div>

<?php
if ( function_exists( 'bp_is_active' ) ){
	get_template_part('bp');
} else {
	get_template_part('pb');
}
?>