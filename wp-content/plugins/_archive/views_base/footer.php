<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package views_base
 */
?>
	</div><!-- #main -->
	<?php do_action( 'views_base_before_close_main_container' );?>
	</div><!-- #main-container -->
	<div id="footer-container" class="clearfix">
	<footer id="colophon" role="contentinfo">
		<div class="site-info">
			<?php do_action( 'views_base_footer' ); ?>
		<nav class="site-navigation footer-navigation" role="navigation">
			<?php wp_nav_menu( array(
	   			'theme_location' => 'footer', 
	   			'menu_class' => 'footermenu',
	   			'container' => '',
				'depth'	=> 1
	   	)); ?>
		</nav>
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
	</div><!-- #footer-container -->
</div><!-- #site-container -->

<?php 
do_action( 'views_base_before_footer' );
wp_footer(); 
do_action( 'views_base_after_footer' );
?>
</body>
</html>