<div id="left-sidebar">

 <div id="left-mobileNav">
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
  
    
 </div>

</div>
