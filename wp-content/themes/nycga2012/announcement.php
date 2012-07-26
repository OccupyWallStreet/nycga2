<?php do_action( 'bp_before_sidebar' ) ?>

<div id="announcement" role="complementary" class="announcement container_24">
	<!-- <div class="padder"> -->

	<?php include (ABSPATH . '/wp-content/plugins/announcement-slider/content-slider.php');?>
	<?php //do_shortcode('[spd_slider max_slides="4" post_type="announcements" slider_fx="zoom" ]'); ?> 
	<?php dynamic_sidebar( 'announcement' ) ?>

	<!-- </div> --><!-- .padder -->
</div><!-- #sidebar -->

<?php do_action( 'bp_after_sidebar' ) ?>

