<?php
/*
Template Name: Search Page Template
*/
?>

<?php get_header() ?>

		<div id="content" class="grid_19">

			<?php do_action( 'bp_before_blog_single_post' ) ?>

			<div class="page" id="search-single" role="main">
			
			<?php do_action("advance-search");?>
			
			
			
			
			</div>

		<?php do_action( 'bp_after_blog_single_post' ) ?>

		<!-- .padder -->
	</div><!-- #content -->

	<?php get_sidebar() ?>

<?php get_footer() ?>