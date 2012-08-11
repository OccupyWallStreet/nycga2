<?php
/**
 * @file
 *
 * Theme-specific hook implementations.
 */

/**
 * Load theme-specific CSS.
 */
function ai1ec_plana_styles() {
  $plana_url = apply_filters(
    'ai1ec_template_root_url',
    apply_filters( 'ai1ec_template', 'plana' )
  );

  wp_enqueue_style( 'ai1ec_plana-style', "$plana_url/style.css" );
}
add_action( 'wp_print_styles', 'ai1ec_plana_styles' );
