<div id="bar">	

<div class="col4">

<?php if ( !function_exists('dynamic_sidebar')
	        || !dynamic_sidebar('sidebar1') ) : ?>
          
<div id="slider1" class="sliderwrapper">

<?php 
	$my_query = new WP_Query('showposts=8&offset=0');
while ($my_query->have_posts()) : $my_query->the_post();$do_not_duplicate = $post->ID;
?>
                
<div class="contentdiv"> 

<div class="cis">
<h3><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h3> 
<div class="hg">
<a href="<?php the_permalink() ?>">
<?php
if (function_exists('vp_get_thumb_url')) {
        $thumb=vp_get_thumb_url($post->post_content, 'browse');
}
?>
<img src="<?php if ($thumb!='') echo $thumb; ?>" alt="<?php the_title(); ?>" />
</a>
</div>
</div> 
<div class="clearfix"></div><hr class="clear" />
<div class="read"><a title="<?php _e( 'Read more here', 'NewYorker') ?>" href="<?php the_permalink() ?>"><?php _e( 'Read on', 'NewYorker') ?> </a></div>
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

<?php endif; ?>
</div>


<div class="col5">

<?php if ( !function_exists('dynamic_sidebar')
	        || !dynamic_sidebar('sidebar2') ) : ?>
          	        
<h3><?php _e( 'Topics', 'NewYorker') ?></h3>
<div class="cats">
<ul>
<?php wp_list_categories('sort_column=name&hide_empty=0'); ?>
</ul>
</div>

<?php endif; ?>
</div>

<div class="col6">

<?php if ( !function_exists('dynamic_sidebar')
	        || !dynamic_sidebar('sidebar3') ) : ?>
          	        
<h3><?php _e( 'Topics', 'NewYorker') ?></h3>
<div class="cats">
<ul>
<?php wp_list_categories('sort_column=name&hide_empty=0'); ?>
</ul>
</div>

<?php endif; ?>
</div>

<div class="col7">

<?php if ( !function_exists('dynamic_sidebar')
	        || !dynamic_sidebar('sidebar4') ) : ?>
          	        
<h3><?php _e( 'Topics', 'NewYorker') ?></h3>
<div class="cats">
<ul>
<?php wp_list_categories('sort_column=name&hide_empty=0'); ?>
</ul>
</div>

<?php endif; ?>
</div>
	       
</div>