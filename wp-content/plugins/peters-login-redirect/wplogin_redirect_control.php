<?php
    // This assumes that this files sits in "wp-content/plugins/peters-login-redirect/wplogin_redirect_control.php" and that you haven't moved your wp-content folder
    if( file_exists( '../../../wp-load.php' ) )
    {
        include '../../../wp-load.php';
    }
    else
    {
        print 'Plugin paths not configured correctly.';
    }

    $current_user = wp_get_current_user();
    $redirect_to = admin_url();
    $redirect_url = redirect_wrapper( $redirect_to, '', $current_user );
    wp_redirect( $redirect_url );
    die();
?>
