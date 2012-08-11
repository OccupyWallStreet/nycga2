<div id="bar">	

<div class="sl"></div>
<div id="slider1" class="sliderwrapper">

<?php 
	$slidecat = get_option('Detox_slicer_category'); 
	$my_query = new WP_Query('category_name= '. $slidecat .'&showposts=8&offset=0');
while ($my_query->have_posts()) : $my_query->the_post();$do_not_duplicate = $post->ID;
?>
                
<div class="contentdiv"> 

<div class="cis">
<h3><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h3> 
<div class="aligncenter">
<a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>">
<?php $image = get_the_post_thumbnail($post->ID, 'browse'); ?>
<?php echo $image; ?>
</a>
</div>
<div class="read"><a title="<?php _e( 'Read more here', 'Detox') ?>" href="<?php the_permalink() ?>"><?php _e( 'Read on', 'Detox') ?> </a></div>
</div>
</div> 
               
<?php endwhile; ?>                           
                
<div id="paginate-slider1" class="pagination"></div>
<script type="text/javascript">
featuredcontentslider.init({
id: "slider1", 
contentsource: ["inline", ""], 
toc: "#increment", 
nextprev: ["", ""], 
revealtype: "mouseover", 
enablefade: [true, 0.2], 
autorotate: [true, 3000], 
onChange: function(previndex, curindex){ 
}
})
</script>
</div>

<div class="sl"></div>
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

<div class="clearfix"></div>
<div class="sl"></div>
<?php if ( !function_exists('dynamic_sidebar')
	        || !dynamic_sidebar('sidebar') ) : ?>
	        
<h3><?php _e( 'Topics', 'Detox') ?></h3>
<div class="cats">
<ul>
<?php wp_list_cats('sort_column=name&hide_empty=0'); ?>
</ul>
</div>

<div class="clearfix"></div>
<div class="sl"></div>
	       
<?php endif; ?>

<?php get_template_part('sbar'); ?>

</div>