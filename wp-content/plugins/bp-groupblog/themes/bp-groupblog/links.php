<?php
/*
Template Name: Links
*/
?>

<?php get_header() ?>

	<div id="content">
		<div class="padder">
			<?php if ( bp_has_groups( 'type=single-group&slug=' . bp_get_groupblog_slug() ) ) : while ( bp_groups() ) : bp_the_group(); ?>

			<?php do_action( 'bp_before_blog_links' ) ?>

			<div id="item-header">
				<?php locate_template( array( 'groups/single/group-header.php' ), true ) ?>
			</div>

			<div id="item-nav">
				<div class="item-list-tabs no-ajax" id="object-nav">
					<ul>
						<?php bp_groupblog_options_nav() ?>
						
						<?php do_action( 'bp_group_options_nav' ) ?>
					</ul>
				</div>
			</div>
			
			<div class="page" id="blog-latest">
	
				<h2 class="pagetitle"><?php _e( 'Links', 'buddypress' ) ?></h2>
	
				<ul id="links-list">
					<?php get_links_list(); ?>
				</ul>
	
			</div>
	
			<?php do_action( 'bp_after_blog_links' ) ?>

			<?php endwhile; endif; ?>
		</div>
	</div>

<?php get_footer(); ?>
