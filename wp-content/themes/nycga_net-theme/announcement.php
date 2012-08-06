<?php do_action( 'bp_before_sidebar' ) ?>

<?php if (have_posts()) : ?>

	<script>
	jQuery('div#featured_slider ul').cycle({
		fx: 'fade',
		prev: '.feat_prev',
		next: '.feat_next',
		speed: 800,
		timeout: 5000,
		pager: null
		}); 
</script>
<div id="announcement" role="complementary" class="announcement container_24">
	<!-- <div class="padder"> -->

	<div id="featured_slider">
	
		<ul id="announcement_slider">
	
			<?php
			
			$sort = get_option('sort'); if(empty($sort)){$sort = "post_date";}
			$order = get_option('order'); if(empty($order)){$order = "DESC";}
			
			global $wpdb;
			global $post;
			
			$args = array( 'suppress_filters' => 0, 'post_type' => array('announcement'), 'orderby' => $sort, 'order' => $order);
			
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
	<?php //include (ABSPATH . '/wp-content/plugins/announcement-slider/content-slider.php');?>
	<?php //do_shortcode('[spd_slider max_slides="4" post_type="announcements" slider_fx="zoom" ]'); ?> 
	<?php //dynamic_sidebar( 'announcement' ) ?>

	<!-- </div> --><!-- .padder -->
</div><!-- #sidebar -->
<?php endif; ?>
<?php do_action( 'bp_after_sidebar' ) ?>

