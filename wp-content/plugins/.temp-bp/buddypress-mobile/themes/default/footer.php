 <div id="mobileNav">
        <?php if ( is_user_logged_in() ) : ?>

        <ul>
            <li><a href="<?php echo bp_loggedin_user_domain() ?>" title="<?php _e( 'Profile', 'buddypress' ) ?>"><?php _e( 'Profile', 'buddypress' ) ?></a><span class="spinner"></span></li>
        </ul>
        <?php endif; ?>
        
        <?php if ( has_nav_menu( 'mobile-menu' ) ) {
        wp_nav_menu( array( 'container' => false, 'menu_id' => 'mobile-nav', 'theme_location' => 'mobile-menu' ) ); 
        }else{
        wp_nav_menu( array( 'container' => false, 'menu_id' => 'mobile-nav', 'theme_location' => 'primary', 'fallback_cb' => 'bp_dtheme_main_nav' ) ); 
        } ?>
  
    </div><?php if ( $notifications = bp_core_get_notifications_for_user( bp_loggedin_user_id() ) ) {

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
        <p><a href="" id="theme-switch">View non-mobile site</a></p>
    
    
    <?php do_action( 'bp_after_footer' ) ?>
    
    </div><!-- #footer -->