<?php

/**
 * BuddyPress - Single Group Plugins
 *
 * @package BuddyPress
 * @subpackage Theme
 */


get_header(); // Loads the header.php template. ?>

<?php do_atomic( 'before_content' ); // fanwood_before_content ?>

<div id="content">

	<?php do_atomic( 'open_content' ); // fanwood_open_content ?>

	<div class="hfeed">
		
		<?php get_sidebar( 'before-content' ); // Loads the sidebar-before-content.php template. ?>
		
		<?php if ( bp_has_groups() ) : while ( bp_groups() ) : bp_the_group(); ?>

		<?php do_action( 'bp_before_group_plugin_template' ) ?>

		<div id="item-header">
			<?php locate_template( array( 'groups/single/group-header.php' ), true ) ?>
		</div><!-- #item-header -->

		<div id="item-nav">
			<div class="item-list-tabs bp-tabs no-ajax" id="object-nav" role="navigation">
				<ul>
					<?php bp_get_options_nav() ?>

					<?php do_action( 'bp_group_plugin_options_nav' ) ?>
				</ul>
			</div>
		</div><!-- #item-nav -->

		<div id="item-body">
		
			<div class="entry-content">

				<?php do_action( 'bp_before_group_body' ) ?>
				<?php do_action( 'bp_template_content' ) ?>
				<?php do_action( 'bp_after_group_body' ) ?>
				
			</div><!-- .entry-content -->

		</div><!-- #item-body -->

		<?php do_action( 'bp_after_group_plugin_template' ) ?>

		<?php endwhile; endif; ?>

		<?php get_sidebar( 'after-content' ); // Loads the sidebar-after-content.php template. ?>

	</div><!--. hfeed -->
	
	<?php do_atomic( 'close_content' ); // fanwood_close_content ?>

</div><!-- #content -->

<?php do_atomic( 'after_content' ); // fanwood_after_content ?>

<?php get_footer(); // Loads the header.php template. ?>
