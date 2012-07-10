<?php
/**
 * Subsidiary 3 Columns Sidebar Template
 *
 * Displays widgets for the Subsidiary 3 Columns dynamic sidebar if any have been added to the sidebar through the 
 * widgets screen in the admin by the user.  Otherwise, nothing is displayed.
 *
 * @package Fanwood
 * @subpackage Template
 */

if ( is_active_sidebar( 'subsidiary-3c' ) ) : ?>

	<?php do_atomic( 'before_sidebar_subsidiary_3c' ); // fanwood_before_sidebar_subsidiary_3c ?>

	<div id="sidebar-subsidiary-3c" class="sidebar sidebar-3c sidebar-subsidiary">
		
		<div class="sidebar-wrap">

			<?php do_atomic( 'open_sidebar_subsidiary_3c' ); // fanwood_open_sidebar_subsidiary_3c ?>

			<?php dynamic_sidebar( 'subsidiary-3c' ); ?>

			<?php do_atomic( 'close_sidebar_subsidiary_3c' ); // fanwood_close_sidebar_subsidiary_3c ?>
		
		</div><!-- .sidebar-wrap -->

	</div><!-- #sidebar-subsidiary-3c -->

	<?php do_atomic( 'after_sidebar_subsidiary_3c' ); // fanwood_after_sidebar_subsidiary_3c ?>

<?php endif; ?>