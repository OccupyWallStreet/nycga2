<?php
/**
 * Header Secondary Menu Template
 *
 * Displays the Header Secondary Menu if it has active menu items.
 *
 * @package Fanwood
 * @subpackage Template
 */

if ( has_nav_menu( 'header-secondary' ) ) : ?>

	<?php do_atomic( 'before_menu_header_secondary' ); // fanwood_before_menu_header_secondary ?>

	<div id="menu-header-secondary" class="menu-container">

		<div class="wrap">

			<?php do_atomic( 'open_menu_header_secondary' ); // fanwood_open_menu_header_secondary ?>
			
			<div id="menu-header-secondary-title">
				<?php _e( 'Menu', 'fanwood' ); ?>
			</div><!-- #menu-header-secondary-primary -->

			<?php wp_nav_menu( array( 'theme_location' => 'header-secondary', 'container_class' => 'menu', 'menu_class' => '', 'menu_id' => 'menu-header-secondary-items', 'fallback_cb' => '' ) ); ?>
			
			<?php

				if( hybrid_get_setting( 'fanwood_header_secondary_search' ) ) {
					get_search_form(); // Loads the search-form.php template.
				}
					
			?>	

			<?php do_atomic( 'close_menu_header_secondary' ); // fanwood_close_menu_header_secondary ?>

		</div>

	</div><!-- #menu-header-secondary .menu-container -->

	<?php do_atomic( 'after_menu_header_secondary' ); // fanwood_after_menu_header_secondary ?>

<?php endif; ?>