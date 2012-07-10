<?php
/**
 * Header Horizontal Menu Template
 *
 * Displays the Header Horizontal Menu if it has active menu items.
 *
 * @package Fanwood
 * @subpackage Template
 */

if ( has_nav_menu( 'header-horizontal' ) ) : ?>

	<?php do_atomic( 'before_menu_header_horizontal' ); // fanwood_before_menu_header_horizontal ?>

	<div id="menu-header-horizontal" class="menu-container">

		<div class="wrap">

			<?php do_atomic( 'open_menu_header_horizontal' ); // fanwood_open_menu_header_horizontal ?>
			
			<div id="menu-header-horizontal-title">
				<?php _e( 'Menu', 'fanwood' ); ?>
			</div><!-- #menu-header-horizontal-title -->

			<?php wp_nav_menu( array( 'theme_location' => 'header-horizontal', 'container_class' => 'menu', 'menu_class' => '', 'menu_id' => 'menu-header-horizontal-items', 'fallback_cb' => '' ) ); ?>

			<?php do_atomic( 'close_menu_header_horizontal' ); // fanwood_close_menu_header_horizontal ?>

		</div>

	</div><!-- #menu-header-horizontal .menu-container -->

	<?php do_atomic( 'after_menu_header_horizontal' ); // fanwood_after_menu_header_horizontal ?>

<?php endif; ?>