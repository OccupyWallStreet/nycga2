<?php
/*
Template Name: Wide Thin Thin
*/
?>

<?php get_header(); ?>

	<div id="content">
		<div class="padderHome">

		<?php do_action( 'bp_before_home' ) ?>
		
		<div id="third-section">
		<div class="widget">
			<?php if ( !function_exists('dynamic_sidebar')
			        || !dynamic_sidebar('Column 3') ) : ?>

			<div class="widget-error">
				<?php _e( 'Please log in and add widgets to this section.', 'buddypress' ) ?> <a href="<?php echo get_option('siteurl') ?>/wp-admin/widgets.php?s=&amp;show=&amp;sidebar=first-section"><?php _e( 'Add Widgets', 'buddypress' ) ?></a>
			</div>

			<?php endif; ?>
		</div>
		</div>

		<div id="second-section">
		<div class="widget">
			<?php if ( !function_exists('dynamic_sidebar')
			        || !dynamic_sidebar('Column 1') ) : ?>

			<div class="widget-error">
				<?php _e( 'Please log in and add widgets to this section.', 'buddypress' ) ?> <a href="<?php echo get_option('siteurl') ?>/wp-admin/widgets.php?s=&amp;show=&amp;sidebar=second-section"><?php _e( 'Add Widgets', 'buddypress' ) ?></a>
			</div>

			<?php endif; ?>
		</div>
		</div>
		
		<div id="first-section">
		<div class="widget">
			<?php if ( !function_exists('dynamic_sidebar')
			        || !dynamic_sidebar('Column 2') ) : ?>

			<div class="widget-error">
				<?php _e( 'Please log in and add widgets to this section.', 'buddypress' ) ?> <a href="<?php echo get_option('siteurl') ?>/wp-admin/widgets.php?s=&amp;show=&amp;sidebar=third-section"><?php _e( 'Add Widgets', 'buddypress' ) ?></a>
			</div>

			<?php endif; ?>
		</div>
		</div>

		<?php do_action( 'bp_after_home' ) ?>
		</div>
	</div>

<?php get_footer(); ?>
