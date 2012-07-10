<?php

/**
 * BuddyPress - Single Member Plugins
 *
 * This is a fallback file that external plugins can use if the template they
 * need is not installed in the current theme. Use the actions in this template
 * to output everything your plugin needs.
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

		<?php do_action( 'bp_before_member_plugin_template' ); ?>
	
		<div id="item-header">

			<?php locate_template( array( 'members/single/member-header.php' ), true ); ?>

		</div><!-- #item-header -->

			<div id="item-nav">
				<div class="item-list-tabs bp-tabs no-ajax" id="object-nav" role="navigation">
					<ul>
						<?php bp_get_displayed_user_nav(); ?>
						<?php do_action( 'bp_member_options_nav' ); ?>

					</ul>
				</div>
			</div><!-- #item-nav -->

			<div id="item-body" role="main">

				<?php do_action( 'bp_before_member_body' ); ?>

				<div class="item-list-tabs bp-sub-tabs no-ajax" id="subnav">
					<ul>

						<?php bp_get_options_nav(); ?>

						<?php do_action( 'bp_member_plugin_options_nav' ); ?>

					</ul>
				</div><!-- #subnav -->

				<h2 class="entry-title"><?php do_action( 'bp_template_title' ); ?></h2>

				<div class="entry-content">
					<?php do_action( 'bp_template_content' ); ?>
					<?php do_action( 'bp_after_member_body' ); ?>
				</div><!-- .entry-content -->

			</div><!-- #item-body -->

			<?php do_action( 'bp_after_member_plugin_template' ); ?>
			
		<?php get_sidebar( 'after-content' ); // Loads the sidebar-after-content.php template. ?>

	</div><!--. hfeed -->
	
	<?php do_atomic( 'close_content' ); // fanwood_close_content ?>

</div><!-- #content -->

<?php do_atomic( 'after_content' ); // fanwood_after_content ?>

<?php get_footer(); // Loads the header.php template. ?>
