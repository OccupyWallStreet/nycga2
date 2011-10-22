<?php 
/*
 Plugin Name: Site UnderConstruction
 Description: Any non-logged in users shown black screen with "site under construction" 
 Version: 9.0
 Author: Eric Lewis

 */



function under_construction() {
    if( ! is_user_logged_in() ) {
        if(strpos( $_SERVER['REQUEST_URI'], 'wp-login' ) || strpos( $_SERVER['REQUEST_URI'], 'load' ) )
            return;
        ?>
        <html>
        <head>
        <style>
        body {
            background-color: black;
            color:white;
            font-family: Helvetica;
            font-weight:100;
        }
        </style>
        </head>
        <body>
        <div style="text-align:center;margin-top:300px">
            site under construction
            <BR><BR><BR><BR>
            <a style="color:white" href="<?php echo site_url("/wp-login.php") ?>">admin login</a>
        </div>
        </body>
        </html>
        <?php
        die;
    }
}

add_action('init','under_construction');

?>