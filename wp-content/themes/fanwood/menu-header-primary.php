<?php
/**
 * Header Primary Menu Template
 *
 * Displays the Header Primary Menu if it has active menu items.
 *
 * @package Fanwood
 * @subpackage Template
 */

if ( has_nav_menu( 'header-primary' ) ) : ?>

	<?php do_atomic( 'before_menu_header_primary' ); // fanwood_before_menu_header_primary ?>

	<div id="menu-header-primary" class="menu-container">

		<div class="wrap">

			<?php do_atomic( 'open_menu_header_primary' ); // fanwood_open_menu_header_primary ?>
			
			<div id="menu-header-primary-title">
				<?php _e( 'Menu', 'fanwood' ); ?>
			</div><!-- #menu-header-primary-title -->

			<?php wp_nav_menu( array( 'theme_location' => 'header-primary', 'container_class' => 'menu', 'menu_class' => '', 'menu_id' => 'menu-header-primary-items', 'fallback_cb' => '' ) ); ?>
			
			<?php

				if( hybrid_get_setting( 'fanwood_header_primary_search' ) ) {
					get_search_form(); // Loads the search-form.php template.
				}
					
			?>		

			<?php do_atomic( 'close_menu_header_primary' ); // fanwood_close_menu_header_primary ?>

		</div>

	</div><!-- #menu-header-primary .menu-container -->

	<?php do_atomic( 'after_menu_header_primary' ); // fanwood_after_menu_header_primary ?>

<?php endif; ?>