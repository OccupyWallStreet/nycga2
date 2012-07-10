<?php
/**
 * After Singular Sidebar Template
 *
 * Displays widgets for the After Content dynamic sidebar if any have been added to the sidebar through the 
 * widgets screen in the admin by the user.  Otherwise, nothing is displayed.
 *
 * @package Fanwood
 * @subpackage Template
 */

if ( is_active_sidebar( 'after-singular' ) ) : ?>

	<?php do_atomic( 'after_sidebar_after_singular' ); // fanwood_after_sidebar_after_singular ?>

	<div id="sidebar-after-singular" class="sidebar">

		<?php do_atomic( 'open_sidebar_after_singular' ); // fanwood_open_sidebar_after_singular ?>

		<?php dynamic_sidebar( 'after-singular' ); ?>

		<?php do_atomic( 'close_sidebar_after_singular' ); // fanwood_close_sidebar_after_singular ?>

	</div><!-- #sidebar-after-singular -->

	<?php do_atomic( 'after_sidebar_after_singular' ); // fanwood_after_sidebar_after_singular ?>

<?php endif; ?>