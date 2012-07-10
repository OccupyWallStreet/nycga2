<?php
/**
 * After Header 5 Columns Sidebar Template
 *
 * Displays widgets for the After Header 5 Columns dynamic sidebar if any have been added to the sidebar through the 
 * widgets screen in the admin by the user.  Otherwise, nothing is displayed.
 *
 * @package Fanwood
 * @subpackage Template
 */

if ( is_active_sidebar( 'after-header-5c' ) ) : ?>

	<?php do_atomic( 'before_sidebar_after_header_5c' ); // fanwood_before_sidebar_after_header_5c ?>

	<div id="sidebar-after-header-5c" class="sidebar sidebar-5c sidebar-after-header">
	
		<div class="sidebar-wrap">

			<?php do_atomic( 'open_sidebar_after_header_5c' ); // fanwood_open_sidebar_after_header_5c ?>

			<?php dynamic_sidebar( 'after-header-5c' ); ?>

			<?php do_atomic( 'close_sidebar_after_header_5c' ); // fanwood_close_sidebar_after_header_5c ?>
		
		</div><!-- .sidebar-wrap -->

	</div><!-- #sidebar-after-header-5c -->

	<?php do_atomic( 'after_sidebar_after_header_5c' ); // fanwood_after_sidebar_after_header_5c ?>

<?php endif; ?>