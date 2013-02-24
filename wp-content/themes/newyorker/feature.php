<div id="featured">
  		
<div id="slider1" class="sliderwrapper">

<?php 
	$my_query = new WP_Query('showposts=4&offset=0');
while ($my_query->have_posts()) : $my_query->the_post();$do_not_duplicate = $post->ID;
?>
                
<div class="contentdiv"> 

<div class="h-f">
<div class="cis">
<div class="h-t">
<h1><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h1>
<div class="ctitle"><?php the_category(' <span>/</span> '); ?></div>
<?php the_excerpt(); ?>
</div>
<a href="<?php the_permalink() ?>">
<?php
if (function_exists('vp_get_thumb_url')) {
        $thumb=vp_get_thumb_url($post->post_content, 'bigg');
}
?>
<img src="<?php if ($thumb!='') echo $thumb; ?>" alt="<?php the_title(); ?>" />
</a>
</div>
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
enablefade: [true, 0.9], 
autorotate: [true, 9000], 
onChange: function(previndex, curindex){ 
}
})
</script>
</div>

</div>