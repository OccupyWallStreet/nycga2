<?php
$direct_path =  get_bloginfo('wpurl')."/wp-content/plugins/announcement-slider";
?>

<script>
	jQuery('#featured_slider ul').cycle({
		fx: 'fade',
		prev: '.feat_prev',
		next: '.feat_next',
		speed: 800,
		timeout: 5000,
		pager: null
		}); 
</script>

<div id="featured_slider">

	<ul id="announcement_slider">

		<?php
		
		$sort = get_option('sort'); if(empty($sort)){$sort = "post_date";}
		$order = get_option('order'); if(empty($order)){$order = "DESC";}
		
		global $wpdb;
	
		global $post;
		
		$args = array( 'meta_key' => 'feat_slider', 'meta_value'=> '1', 'suppress_filters' => 0, 'post_type' => array('announcement', 'post', 'page'), 'orderby' => $sort, 'order' => $order);
		
		$myposts = get_posts( $args );
		
		foreach( $myposts as $post ) :	setup_postdata($post);
			
			$custom = get_post_custom($post->ID);
			
			
		?>
		
		<li><h2><?php the_title();?></h2> <?php the_content();?></li>
		
		<?php endforeach; ?>
	
	</ul>
	
	<div class="feat_next"></div>
	<div class="feat_prev"></div>	
	
</div>