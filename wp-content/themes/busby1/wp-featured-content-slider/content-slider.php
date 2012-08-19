<?php
$direct_path =  get_bloginfo('template_directory')."/wp-featured-content-slider";

global $up_options;	
	
?>
<script type="text/javascript" src="<?php echo $direct_path.'/scripts/jquery.cycle.all.2.72.js';?>"></script>
<script type="text/javascript">
	jQuery('#featured_slider ul').cycle({ 
		fx: '<?php $effect = $up_options->slider_effect; if(!empty($effect)) {echo $effect;} else {echo "fade";}?>',
		prev: '.feat_prev',
		next: '.feat_next',
		speed:  3000, 
		timeout: <?php $timeout = $up_options->slider_timeout; if(!empty($timeout)) {echo $timeout;} else {echo 8000;}?>, 
		pager:  null
	});
</script>

<style>

#featured_slider {
	float: left;
	margin: 0px 0px;
	position: relative;
	border: 0px solid;
	width: 575px;
	height:266px;
	overflow:hidden;
}

#featured_slider ul, #featured_slider ul li {
	list-style: none !important;
	border: none !important;
	float: left;
	margin: 0px;
	width: 575px;
	height: 266px;
}



#featured_slider h2{
	position:absolute; bottom:0px; left:0px;
	width:575px;
	background-color:#000000;
	color:white;
	height:50px;
	line-height:47px;
	padding-left:10px;
}

#featured_slider a{
	color:#FFFFFF;
	font-weight:normal;
	font-family: Georgia, "Times New Roman", Times, serif;
	font-size: 24px;
	font-weight: normal;
	letter-spacing: -0.5px;
	width:575px;
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
	top: 30px;
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
	top: 30px;
	cursor: pointer;
}

#featured_slider .feat_next:hover {
	background-position: -18px -16px;
}

.feat_link {
	float: right;
	position: relative;
	top: -5px;
}

.feat_link a {
	float: left;
	font-size: 20px;
	color: #CCC;
}

</style>

<div id="featured_slider">
	<ul id="slider">

		<?php
		global $wpdb;
		
		$querystr = "
			SELECT wposts.* 
			FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta
			WHERE wposts.ID = wpostmeta.post_id 
			AND wpostmeta.meta_key = 'feat_slider' 
			AND wpostmeta.meta_value = '1' 
			AND wposts.post_status = 'publish' 
			AND (wposts.post_type = 'post' OR wposts.post_type = 'page')";
		
		$pageposts = $wpdb->get_results($querystr, OBJECT); ?>
		
		<?php if ($pageposts): ?>
			<?php global $post; ?>
			<?php foreach ($pageposts as $post): ?>
			<?php setup_postdata($post);
			$custom = get_post_custom($post->ID);
			$thumb = get_wp_generated_thumb("feat_slider");
		?>
		
		<li><h2><a href="<?php the_permalink();?>"><?php the_title();?></a></h2><a href="<?php the_permalink();?>"><img src="<?php echo $thumb;?>" /></a></li>
		<?php endforeach; ?>
		<?php endif; ?>
	</ul>
	<div class="feat_next"></div>
	<div class="feat_prev"></div>
</div>