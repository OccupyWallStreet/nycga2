<?php
/**
 * Footer Menu Template
 *
 * Displays the In Footer Menu if it has active menu items.
 *
 * @package Fanwood
 * @subpackage Template
 */

if ( has_nav_menu( 'footer' ) ) : ?>

	<?php do_atomic( 'before_menu_footer' ); // fanwood_before_menu_footer ?>

	<div id="menu-footer" class="menu-container">

		<div class="wrap">

			<?php do_atomic( 'open_menu_footer' ); // fanwood_open_menu_footer ?>

			<?php wp_nav_menu( array( 'theme_location' => 'footer', 'container_class' => 'menu', 'menu_class' => '', 'menu_id' => 'menu-footer-items', 'fallback_cb' => '' ) ); ?>

			<?php do_atomic( 'close_menu_footer' ); // fanwood_close_menu_footer ?>

		</div>

	</div><!-- #menu-footer .menu-container -->

	<?php do_atomic( 'after_menu_footer' ); // fanwood_after_menu_footer ?>

<?php endif; ?>