<?php
/**
 * After Content Sidebar Template
 *
 * Displays widgets for the After Content dynamic sidebar if any have been added to the sidebar through the 
 * widgets screen in the admin by the user.  Otherwise, nothing is displayed.
 *
 * @package Fanwood
 * @subpackage Template
 */

if ( is_active_sidebar( 'after-content' ) ) : ?>

	<?php do_atomic( 'after_sidebar_after_content' ); // fanwood_after_sidebar_after_content ?>

	<div id="sidebar-after-content" class="sidebar">

		<?php do_atomic( 'open_sidebar_after_content' ); // fanwood_open_sidebar_after_content ?>

		<?php dynamic_sidebar( 'after-content' ); ?>

		<?php do_atomic( 'close_sidebar_after_content' ); // fanwood_close_sidebar_after_content ?>

	</div><!-- #sidebar-after-content -->

	<?php do_atomic( 'after_sidebar_after_content' ); // fanwood_after_sidebar_after_content ?>

<?php endif; ?>