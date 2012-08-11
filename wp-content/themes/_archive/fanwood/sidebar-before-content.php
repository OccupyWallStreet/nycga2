<?php
/**
 * Before Content Sidebar Template
 *
 * Displays widgets for the Before Content dynamic sidebar if any have been added to the sidebar through the 
 * widgets screen in the admin by the user.  Otherwise, nothing is displayed.
 *
 * @package Fanwood
 * @subpackage Template
 */

if ( is_active_sidebar( 'before-content' ) ) : ?>

	<?php do_atomic( 'before_sidebar_before_content' ); // fanwood_before_sidebar_before_content ?>

	<div id="sidebar-before-content" class="sidebar">

		<?php do_atomic( 'open_sidebar_before_content' ); // fanwood_open_sidebar_before_content ?>

		<?php dynamic_sidebar( 'before-content' ); ?>

		<?php do_atomic( 'close_sidebar_before_content' ); // fanwood_close_sidebar_before_content ?>

	</div><!-- #sidebar-before-content -->

	<?php do_atomic( 'after_sidebar_before_content' ); // fanwood_after_sidebar_before_content ?>

<?php endif; ?>