<?php
/**
 * Widgets Template Sidebar Template
 *
 * Displays widgets for the Widgets Template dynamic sidebar if any have been added to the sidebar through the 
 * widgets screen in the admin by the user.  Otherwise, nothing is displayed.
 *
 * @package Fanwood
 * @subpackage Template
 */

if ( is_active_sidebar( 'widgets-template' ) ) : ?>

	<?php do_atomic( 'before_sidebar_widgets_template' ); // fanwood_before_sidebar_widgets_template ?>

	<div id="sidebar-widgets-template" class="sidebar">

		<?php do_atomic( 'open_sidebar_widgets_template' ); // fanwood_open_sidebar_widgets_template ?>

		<?php dynamic_sidebar( 'widgets-template' ); ?>

		<?php do_atomic( 'close_sidebar_widgets_template' ); // fanwood_close_sidebar_widgets_template ?>

	</div><!-- #sidebar-widgets-template -->

	<?php do_atomic( 'after_sidebar_widgets_template' ); // fanwood_after_sidebar_widgets_template ?>

<?php endif; ?>