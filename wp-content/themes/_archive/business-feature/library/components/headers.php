<?php if (is_category()) { ?>
<h3><?php printf( __( 'You are browsing the archive for %1$s.', 'business-feature' ), wp_title( false, false ) ); ?></h3>
<?php } else if (is_tag()) { ?>
<h3><?php printf( __( 'You are browsing the archive for %1$s.', 'business-feature' ), wp_title( false, false ) ); ?></h3>
<?php } else if (is_archive()) { ?>
<h3><?php printf( __( 'You are browsing the archive for %1$s.', 'business-feature' ), wp_title( false, false ) ); ?></h3>
<?php } else if (is_single()) { ?>
<?php } else if (is_search()) { ?>
<h3><?php _e( 'Search Results', 'business-feature' ); ?></h3>
<?php } else {?>
<h3 class="pagetitle"><?php _e( 'Blog', 'business-feature' ); ?></h3>
<?php } ?>