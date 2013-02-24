<?php
/*
Template Name: Links
*/
?>

<?php get_header() ?>

    <div id="content" class="span8">
		<div class="padder">

		<?php do_action( 'bp_before_blog_links' ) ?>

		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

			<h2 class="pagetitle"><?php _e( 'Links', 'cc' ) ?></h2>

			<ul id="links-list">
				<?php wp_list_bookmarks(); ?>
			</ul>

		</div>

		<?php do_action( 'bp_after_blog_links' ) ?>

		</div>
	</div>

<?php get_footer(); ?>