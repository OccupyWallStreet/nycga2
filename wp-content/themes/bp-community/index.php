<?php
/*
customization on index.php so static frontpage and post page can be use properly
*/
?>

<?php if ( '' == get_option( 'page_on_front' )  || '0' == get_option( 'page_on_front' ) ) : $post_set_page = get_option( 'page_for_posts' ); ?>

<?php if( is_home() && !is_page( $post_set_page )  ) {  ?>

<?php locate_template( array('index-home.php'), true ); ?>

<?php } else { ?>

<?php locate_template( array('index-post.php'), true ); ?>

<?php } ?>

<?php else: //if static frontpage were set ?>

<?php locate_template( array('index-post.php'), true ); ?>

<?php endif; ?>