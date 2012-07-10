<?php
$direct_path =  get_bloginfo('wpurl')."/wp-content/plugins/wp-featured-content-slider";
?>

<script type="text/javascript">
	jQuery('#featured_slider ul').cycle({ 
		fx: '<?php $effect = get_option('effect'); if(!empty($effect)) {echo $effect;} else {echo "scrollLeft";}?>',
		prev: '.feat_prev',
		next: '.feat_next',
		speed:  800, 
		timeout: <?php $timeout = get_option('timeout'); if(!empty($timeout)) {echo $timeout;} else {echo 4000;}?>, 
		pager:  null
	});
</script>

<style>

#featured_slider {
float: left;
margin: 10px 0px;
position: relative;
background-color: #<?php $bg = get_option('feat_bg'); if(!empty($bg)) {echo $bg;} else {echo "FFF";}?>;
border: 1px solid #<?php $border = get_option('feat_border'); if(!empty($border)) {echo $border;} else {echo "CCC";}?>;
width: <?php $width = get_option('feat_width'); if(!empty($width)) {echo $width;} else {echo "860";}?>px;
}

#featured_slider ul, #featured_slider ul li {
list-style: none !important;
border: none !important;
float: left;
margin: 10px;
width: <?php $width = get_option('feat_width'); if(!empty($width)) {echo $width;} else {echo "860";}?>px;
height: <?php $height = get_option('feat_height'); if(!empty($height)) {echo $height;} else {echo "210";}?>px;
}

#featured_slider .img_right {
float: left;
width: <?php $img_width = get_option('img_width'); if(!empty($img_width)) {echo $img_width;} else {echo "320";}?>px;
height: <?php $img_height = get_option('img_height'); if(!empty($img_height)) {echo $img_height;} else {echo "200";}?>px;
margin-left: 20px;
}

#featured_slider .img_right img {
width: <?php $img_width = get_option('img_width'); if(!empty($img_width)) {echo $img_width;} else {echo "320";}?>px;
height: <?php $img_height = get_option('img_height'); if(!empty($img_height)) {echo $img_height;} else {echo "200";}?>px;
}

#featured_slider .content_left {
float: left;
color: #<?php $text_color = get_option('text_color'); if(!empty($text_color)) {echo $text_color;} else {echo "333";}?>;
width: <?php $text_width = get_option('text_width'); if(!empty($text_width)) {echo $text_width;} else {echo "450";}?>px;
}

#featured_slider .content_left p {
line-height: 22px !important;
color: #<?php $text_color = get_option('text_color'); if(!empty($text_color)) {echo $text_color;} else {echo "333";}?>;
}

#featured_slider .content_left h2 {
font-size: 20px !important;
margin-bottom: 20px;
}

#featured_slider .feat_prev {
background: transparent url(<?php echo $direct_path;?>/images/sprite.png) no-repeat;
background-position: 0px 0px;
width: 17px;
z-index: 10;
height: 16px;
position: absolute;
left: 20px;
cursor: pointer;
bottom: 30px;
float: left;
}

#featured_slider .feat_prev:hover {
background-position: 0px -16px;
}

#featured_slider .feat_next {
background: transparent url(<?php echo $direct_path;?>/images/sprite.png) no-repeat;
background-position: -17px 0px;
width: 17px;
z-index: 10;
height: 16px;
position: absolute;
left: 40px;
bottom: 30px;
cursor: pointer;
}

#featured_slider .feat_next:hover {
background-position: -18px -16px;
}

</style>

<div id="featured_slider">
	

	<ul id="slider">

		<?php
		
		$sort = get_option('sort'); if(empty($sort)){$sort = "post_date";}
		$order = get_option('order'); if(empty($order)){$order = "DESC";}
		
		global $wpdb;
	
		global $post;
		
		$args = array( 'meta_key' => 'feat_slider', 'meta_value'=> '1', 'suppress_filters' => 0, 'post_type' => array('announcements', 'post', 'page'), 'orderby' => $sort, 'order' => $order);
		
		$myposts = get_posts( $args );
		
		foreach( $myposts as $post ) :	setup_postdata($post);
			
			$custom = get_post_custom($post->ID);
			
			$thumb = get_wp_generated_thumb("feat_slider");
			
		?>
		
		<li><div class="content_left"><h2><a href="<?php the_permalink();?>"><?php the_title();?></a></h2><?php the_excerpt();?></div><div class="img_right"><a href="<?php the_permalink();?>"><img src="<?php echo $thumb;?>" /></a></div></li>
		
		<?php endforeach; ?>
	
	</ul>
	
	<div class="feat_next"></div>
	<div class="feat_prev"></div>
	
	
</div>