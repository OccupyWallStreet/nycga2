<?php
/*
Template Name: Blog
*/
?>

<?php get_header() ?>
			
	<div id="content">
		<div class="padder">

			<?php if ( bp_groupblog_blog_exists( bp_get_groupblog_id() ) ) : ?>
				<?php if ( bp_has_groups( 'type=single-group&slug=' . bp_get_groupblog_slug() ) ) : while ( bp_groups() ) : bp_the_group(); ?>
					
					<?php locate_template( array( 'groupblog/group-header.php' ), true ) ?>
			
					<?php do_action( 'bp_before_blog_page' ) ?>
											
					<div class="page" id="item-body">

					<?php if ( p2_user_can_post() && !is_archive() ) : ?>
						<?php locate_template( array( 'post-form.php' ), true ); ?>
					<?php elseif ( is_home_redirect() && is_user_logged_in() ) : ?>
						<?php locate_template( array( 'groupblog/post-form.php' ), true ); ?>
					<?php endif; ?>
	
					<?php if ( bp_group_is_visible() ) : ?>
					<?php groupblog_locate_layout() ?>
										
					<?php elseif ( !bp_group_is_visible() ) : ?>
						<?php /* The group is not visible, show the status message */ ?>
		
						<?php do_action( 'bp_before_group_status_message' ) ?>
		
						<div id="message" class="info">
							<p><?php bp_group_status_message() ?></p>
						</div>
		
						<?php do_action( 'bp_after_group_status_message' ) ?>
					<?php endif; ?>
	
					</div>
		
					<?php do_action( 'bp_after_blog_page' ) ?>

				<?php endwhile; endif; ?>
			<?php endif; ?>
					
		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer(); ?>
