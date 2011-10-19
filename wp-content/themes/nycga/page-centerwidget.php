<?php
/*
Template Name: Center Widget Page
*/
?>


<?php get_header() ?>

	<div id="content">
		<div class="padder">
        
        <?php if( is_front_page() ) {
		dynamic_content_gallery();
		} ?>
        
      <!-- MK - add a widget area to the page-->
        <?php dynamic_sidebar( 'centerwidget-page' ); ?>
        
        

		<?php do_action( 'bp_before_blog_page' ) ?>

		

		<?php do_action( 'bp_after_blog_page' ) ?>
		
		</div><!-- .padder -->
	</div><!-- #content -->


<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer(); ?>
