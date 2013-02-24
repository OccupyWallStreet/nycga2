<?php if ( $notifications = bp_core_get_notifications_for_user( bp_loggedin_user_id() ) ) {

        echo '<div id="notifications-header"><ul>';

        if ( $notifications ) {
            $counter = 0;
            for ( $i = 0; $i < count($notifications); $i++ ) {
                $badge = count($notifications);
                echo '<li>'.$notifications[$i].'</li>';
            }
            echo '</ul></div>';
            echo '<span id="notifications-badge">'.$badge.'</span>';
        }


    } ?>
    
    <?php do_action( 'bp_after_container' ) ?>
    
    <?php do_action( 'bp_before_footer' ) ?>

    <div id="footer">
   
        <p><a href="" id="theme-switch">view non-mobile site</a></p>
    
    
    <?php do_action( 'bp_after_footer' ) ?>
    
    <?php locate_template( array( 'left-sidebar.php' ), true ) ?>
    
    </div><!-- #footer -->
    
	</body>

</html>