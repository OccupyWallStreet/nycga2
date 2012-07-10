<?php do_action( 'bp_before_sidebar' ) ?>

<div id="announcement" role="complementary" class="announcement">
	<!-- <div class="padder"> -->

	<?php include (ABSPATH . '/wp-content/plugins/announcement-slider/content-slider.php');?>
	<?php dynamic_sidebar( 'announcement' ) ?>

	<!-- </div> --><!-- .padder -->
</div><!-- #sidebar -->

<?php do_action( 'bp_after_sidebar' ) ?>

