<?php
/**
 * This is the template that is loaded when visiting the 'blog tab' within the group.
 * To modify you can either use the provided hooks. You may also copy the whole groupblog over
 * to your active theme and completely re-theme the blog template.
 *
 * Includes: inc/pages.php, inc/posts.php, inc/activity.php
 */
?>
<?php get_header() ?>

	<div id="content">
		<div class="padder">
			<?php if ( bp_has_groups() ) : while ( bp_groups() ) : bp_the_group(); ?>

			<?php do_action( 'bp_before_group_home_content' ) ?>

			<div id="item-header">
				<?php locate_template( array( 'groups/single/group-header.php' ), true ) ?>
			</div>

			<div id="item-nav">
				<div class="item-list-tabs no-ajax" id="object-nav">
					<ul>
						<?php bp_get_options_nav() ?>

						<?php do_action( 'bp_group_options_nav' ) ?>
					</ul>
				</div>
			</div>

			<div id="item-body">

				<?php do_action( 'bp_before_group_body' ) ?>
				
				<?php if ( bp_group_is_visible() && bp_groupblog_is_blog_enabled ( bp_get_group_id() ) ) : ?>			  
			
					<?php switch_to_blog( get_groupblog_blog_id() ); ?>
					
					<?php /********************* Start your custom content *********************/ ?>
				
					<?php /* Uncomment to disable */ include( 'inc/pages.php' ); ?>
			    <?php /* Uncomment to disable */ include( 'inc/posts.php' ); ?>		
			
					<?php /********************* End your custom content *********************/ ?>
					
					<?php	restore_current_blog(); ?>   
			    	
			    <?php /* Uncomment to disable */ include( 'inc/activity.php' ); ?>

				<?php elseif ( !bp_group_is_visible() ) : ?>
					<?php /* The group is not visible, show the status message */ ?>

					<?php do_action( 'bp_before_group_status_message' ) ?>

					<div id="message" class="info">
						<p><?php bp_group_status_message() ?></p>
					</div>

					<?php do_action( 'bp_after_group_status_message' ) ?>

				<?php else : ?>
					<?php
						/* If nothing sticks, just load a group front template if one exists. */
						locate_template( array( 'groups/single/front.php' ), true );
					?>
				<?php endif; ?>

				<?php do_action( 'bp_after_group_body' ) ?>
			</div>

			<?php do_action( 'bp_after_group_home_content' ) ?>

			<?php endwhile; endif; ?>
		</div><!-- .padder -->
	</div><!-- #content -->

	<?php switch_to_blog( get_groupblog_blog_id() ); ?>

		<?php locate_template( array( 'sidebar.php' ), true ) ?>

	<?php	restore_current_blog(); ?>

<?php get_footer() ?>