<?php
$direct_path =  get_bloginfo('wpurl')."/wp-content/plugins/featured-posts-slideshow";
?>
<script type="text/javascript" src="<?php echo $direct_path;?>/scripts/jquery-1.1.3.1.min.js"></script> 
<script type="text/javascript" src="<?php echo $direct_path;?>/scripts/jquery.cycle.js"></script>
<script type="text/javascript" src="<?php echo $direct_path;?>/scripts/jquery.scrollable.js"></script>
<style>

#feature_wrap {
margin: 10px 0px 0px 0px;
width: <?php echo get_option('div-width'); ?>px;
height: 150px;
overflow:hidden;
background-color:#<?php echo get_option('div-color'); ?>;
float: left;
}

#scrollable {
background-image: url(<?php echo $direct_path;?>/images/slider-bg.png);
background-repeat: no-repeat;
background-position: center;
padding:5px 0px 10px 0px;
width:685px;
min-height:106px;
margin: 10px auto;
}
div.items {
height:60px;	
margin-left:8px;	
margin-right:4px;
margin-top: 25px;
padding-bottom:8px;
float:left;
width:604px !important;
background-color:#FFFFFF;
}
div.items a:link , div.items a:visited{
display:block;
float:left;
margin-right:8px;
margin-bottom:8px;
width:60px;
height:60px;
background-color: #<?php echo get_option('image-bg-color'); ?>;
color:#ccc;
cursor:pointer;
border: 3px solid #<?php echo get_option('image-border-color'); ?>;
}

.featit {
font-size: 6px;
float: left;
}

.featit a {
font-size: 6px;
text-decoration: none;
color: #3b3b3b;
}

div.items a:hover {
color:#999;	
border: 3px solid #<?php echo get_option('image-border-hover-color'); ?>;
}
a.prev, a.next {
display:block;
width:28px;
height:28px;
float:left;
background-repeat:no-repeat;	
margin:40px 0 0 4px;
}
a.prev {
background:url(<?php echo $direct_path;?>/images/next-arrow-left.png);		
}
a.next {
background:url(<?php echo $direct_path;?>/images/next-arrow-right.png);		
}


</style>



<div id="feature_wrap">
<script type="text/javascript" >
	$(function() {
		$("#scrollable").scrollable({horizontal:true,size: 8});
	});
</script>
<div id="scrollable">
<a class="prev"></a>     
<div class="items"> 
<?php $category = get_option('category-id'); ?>
<?php $numberposts = get_option('number-posts'); ?>
<?php query_posts('orderby=rand&cat=' . get_option('category-id') . '&showposts=' . get_option('numberposts')); ?>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<?php 
		// check for thumbnail
$thumb = get_post_meta($post->ID, 'thumbnail', $single = true);
?>
<a href="<?php the_permalink() ?>" title="<?php the_title(); ?>"><img src="<?php echo $thumb; ?>" style="border: none;" alt="<?php the_title(); ?>" /></a>
<?php endwhile; else: ?>
<?php endif; ?>  
</div> 
<a class="next"></a> 
</div>
<div class="featit"></div>
<div style="clear: both;"></div>
</div>