<?php
/*
 * This template mimmicks the Group Home page. Usefull to figure out how to replace the group home page exactly.
 * activity.php, activity-post-form.php, activity-loop.php
 */
?>

<?php get_header() ?>

	<div id="content">
		<div class="padder">		
			<?php if ( bp_has_groups( 'type=single-group&slug=' . bp_get_groupblog_slug() ) ) : while ( bp_groups() ) : bp_the_group(); ?>

			<?php do_action( 'bp_before_group_home_content' ) ?>

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

			<div id="item-body">
				<?php do_action( 'bp_before_group_body' ) ?>


				<?php if ( bp_group_is_visible() && bp_is_active( 'activity' ) ) : ?>
					<?php locate_template( array( 'activity.php' ), true ) ?>

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

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer() ?>