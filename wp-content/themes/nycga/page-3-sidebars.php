<?php
/*
Template Name: Page - 3 Sidebars
*/
?>

<?php get_header() ?>

	<div id="content">
		<div class="padder">

			<?php locate_template( array( 'sidebar-2.php' ), true ) ?>
			<?php locate_template( array( 'sidebar-3.php' ), true ) ?>
			<?php locate_template( array( 'sidebar-4.php' ), true ) ?>

		</div><!-- .padder -->
	</div><!-- #content -->
	
	
	

<?php get_footer(); ?>
