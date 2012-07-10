<?php
/**
 * Secondary Menu Template
 *
 * Displays the Secondary Menu if it has active menu items.
 *
 * @package Fanwood
 * @subpackage Template
 */

if ( has_nav_menu( 'secondary' ) ) : ?>

	<?php do_atomic( 'before_menu_secondary' ); // fanwood_before_menu_secondary ?>

	<div id="menu-secondary" class="menu-container">

		<div class="wrap">

			<div id="menu-secondary-title">
				<?php _e( 'Menu', 'fanwood' ); ?>
			</div><!-- #menu-secondary-title -->

			<?php do_atomic( 'open_menu_secondary' ); // fanwood_open_menu_secondary ?>

			<?php wp_nav_menu( array( 'theme_location' => 'secondary', 'container_class' => 'menu', 'menu_class' => '', 'menu_id' => 'menu-secondary-items', 'fallback_cb' => '' ) ); ?>

			<?php do_atomic( 'close_menu_secondary' ); // fanwood_close_menu_secondary ?>

		</div>

	</div><!-- #menu-secondary .menu-container -->

	<?php do_atomic( 'after_menu_secondary' ); // fanwood_after_menu_secondary ?>

<?php endif; ?>