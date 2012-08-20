<?php do_action( 'bp_before_sidebar' ) ?>

<?php

global $post;

$args = array(
    'orderby'         => 'post_date',
    'order'           => 'DESC',
    'post_type'       => 'announcement',
    'post_status'     => 'publish',
    'suppress_filters' => true );

$myposts = get_posts( $args );

$myquery = new WP_Query('post_type=announcement&post_status=publish'); 

?>

<?php if ($myquery->have_posts()) : ?> 

	<script>
	//<![CDATA[
	jQuery('div#featured_slider ul').cycle({
		fx: 'fade',
		prev: '.feat_prev',
		next: '.feat_next',
		speed: 800,
		timeout: 5000,
		pager: null
		}); 
	//]]>	
</script>

<div id="announcement" role="complementary" class="announcement container_24">

	<div id="featured_slider">
	
		<ul id="announcement_slider">
				
			<?php foreach( $myposts as $post ) :	setup_postdata($post); ?>
			
			<li><h2><?php the_title();?></h2> <?php the_content();?></li>
			
			<?php endforeach; ?>
		
		</ul>
		
		<div class="feat_next"></div>
		<div class="feat_prev"></div>	
		
	</div>
	
</div>

<?php else : ?>

<!-- No announcements -->

<?php endif; ?>

<?php do_action( 'bp_after_sidebar' ) ?>

