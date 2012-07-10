<?php
/**
 * After Header Sidebar Template
 *
 * Displays widgets for the After Header dynamic sidebar if any have been added to the sidebar through the 
 * widgets screen in the admin by the user.  Otherwise, nothing is displayed.
 *
 * @package Fanwood
 * @subpackage Template
 */

if ( is_active_sidebar( 'after-header' ) ) : ?>

	<?php do_atomic( 'before_sidebar_after_header' ); // fanwood_before_sidebar_after_header ?>

	<div id="sidebar-after-header" class="sidebar sidebar-1c sidebar-after-header">
	
		<div class="sidebar-wrap">

			<?php do_atomic( 'open_sidebar_after_header' ); // fanwood_open_sidebar_after_header ?>

			<?php dynamic_sidebar( 'after-header' ); ?>

			<?php do_atomic( 'close_sidebar_after_header' ); // fanwood_close_sidebar_after_header ?>
		
		</div><!-- .sidebar-wrap -->

	</div><!-- #sidebar-after-header -->

	<?php do_atomic( 'after_sidebar_after_header' ); // fanwood_after_sidebar_after_header ?>

<?php endif; ?>