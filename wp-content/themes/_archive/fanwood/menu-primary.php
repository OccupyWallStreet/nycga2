<?php
/**
 * Primary Menu Template
 *
 * Displays the Primary Menu if it has active menu items.
 *
 * @package Fanwood
 * @subpackage Template
 */
 
if ( has_nav_menu( 'primary' ) ) : ?>

	<?php do_atomic( 'before_menu_primary' ); // fanwood_before_menu_primary ?>

	<div id="menu-primary" class="menu-container">

		<div class="wrap">
		
			<div id="menu-primary-title">
				<?php _e( 'Menu', 'fanwood' ); ?>
			</div><!-- #menu-primary-title -->

			<?php do_atomic( 'open_menu_primary' ); // fanwood_open_menu_primary ?>

			<?php wp_nav_menu( array( 'theme_location' => 'primary', 'container_class' => 'menu', 'menu_class' => '', 'menu_id' => 'menu-primary-items', 'fallback_cb' => '' ) ); ?>

			<?php do_atomic( 'close_menu_primary' ); // fanwood_close_menu_primary ?>

		</div>

	</div><!-- #menu-primary .menu-container -->

	<?php do_atomic( 'after_menu_primary' ); // fanwood_after_menu_primary ?>

<?php endif; ?>