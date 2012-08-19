<?php
/**
 * @file
 *
 * Attaches theme-specific JS.
 */

/**
 * Load theme-specific JS.
 */
function ai1ec_vortex_scripts() {
  $vortex_url = apply_filters(
    'ai1ec_template_root_url',
    apply_filters( 'ai1ec_template', 'vortex' )
  );

  wp_enqueue_script( 'ai1ec-vortex-general', "$vortex_url/js/general.min.js" );
}
add_action( 'wp_print_scripts', 'ai1ec_vortex_scripts' );
