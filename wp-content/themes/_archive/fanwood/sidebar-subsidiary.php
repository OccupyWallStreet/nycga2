<?php
/**
 * Subsidiary Sidebar Template
 *
 * Displays widgets for the Subsidiary dynamic sidebar if any have been added to the sidebar through the 
 * widgets screen in the admin by the user.  Otherwise, nothing is displayed.
 *
 * @package Fanwood
 * @subpackage Template
 */

if ( is_active_sidebar( 'subsidiary' ) ) : ?>

	<?php do_atomic( 'before_sidebar_subsidiary' ); // fanwood_before_sidebar_subsidiary ?>

	<div id="sidebar-subsidiary" class="sidebar sidebar-1c sidebar-subsidiary">
	
		<div class="sidebar-wrap">

			<?php do_atomic( 'open_sidebar_subsidiary' ); // fanwood_open_sidebar_subsidiary ?>

			<?php dynamic_sidebar( 'subsidiary' ); ?>

			<?php do_atomic( 'close_sidebar_subsidiary' ); // fanwood_close_sidebar_subsidiary ?>
		
		</div><!-- .sidebar-wrap -->

	</div><!-- #sidebar-subsidiary -->

	<?php do_atomic( 'after_sidebar_subsidiary' ); // fanwood_after_sidebar_subsidiary ?>

<?php endif; ?>