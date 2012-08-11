jQuery( function( $ ) {
  // If in strict mode, hide all administrative functions other than event, user
  // and media management.
  if ( ai1ec_platform.strict_mode ) {
    $( '#dashboard-widgets .postbox' )
      .not( '#ai1ec-calendar-tasks, #dashboard_right_now' )
      .remove();
    $( '#adminmenu > li' )
      .not( '.wp-menu-separator, #menu-dashboard, #menu-posts-ai1ec_event, #menu-media, #menu-appearance, #menu-users, #menu-settings' )
      .remove();
    $( '#menu-appearance > .wp-submenu li, #menu-settings > .wp-submenu li' )
      .not( ':has(a[href*="all-in-one-event-calendar"])' )
      .remove();
  }
});
