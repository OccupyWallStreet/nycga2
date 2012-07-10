<?php
$direct_path =  get_bloginfo('wpurl')."/wp-content/plugins/announcement-slider";
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
		
<script type="text/javascript">
//  jQuery(document).ready(function(){
//    $('#slider').bxSlider();
//  });
</script>

<script type="text/javascript">
	//jQuery(document).ready(function(){	
	//	jQuery("#slider").easySlider();
	//});
</script>

<div id="featured_slider">

	<ul id="slider">

		<?php
		
		$sort = get_option('sort'); if(empty($sort)){$sort = "post_date";}
		$order = get_option('order'); if(empty($order)){$order = "DESC";}
		
		global $wpdb;
	
		global $post;
		
		$args = array( 
		'meta_key' => 'feat_slider', 
		'meta_value'=> '1',
		'suppress_filters' => 0, 
		'post_type' => array('announcements', 'post', 'page'), 
		'orderby' => $sort, 
		'order' => $order
		);
		
		$myposts = get_posts( $args );
		
		foreach( $myposts as $post ) :	setup_postdata($post);
			
		$custom = get_post_custom($post->ID);
			
		$thumb = get_wp_generated_thumb("feat_slider");
			
		?>
		
		
		<li><h2><a href="<?php the_permalink();?>"><?php the_title();?></a></h2>
		<?php the_excerpt() ?></li>
		
		<?php endforeach; ?>
	
	</ul>
	
	<div class="feat_next"></div>
	<div class="feat_prev"></div>
	
	
</div>

