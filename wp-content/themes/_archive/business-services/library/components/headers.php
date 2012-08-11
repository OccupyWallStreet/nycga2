<?php if (is_category()) { ?>
<h3><?php printf( __( 'You are browsing the archive for %1$s.', 'business-services' ), wp_title( false, false ) ); ?></h3>
<?php } else if (is_tag()) { ?>
<h3><?php printf( __( 'You are browsing the archive for %1$s.', 'business-services' ), wp_title( false, false ) ); ?></h3>
<?php } else if (is_archive()) { ?>
<h3><?php printf( __( 'You are browsing the archive for %1$s.', 'business-services' ), wp_title( false, false ) ); ?></h3>
<?php } else if (is_single()) { ?>
<?php } else if (is_search()) { ?>
<h3><?php _e( 'Search Results', 'business-services' ); ?></h3>
<?php } else {?>
<h3 class="pagetitle"><?php _e( 'Blog', 'business-services' ); ?></h3>
<?php } ?>