<?php

function bp_get_options_class() {
	global $bp;

	if ( ( !bp_is_home() && $bp->current_component == $bp->profile->slug ) || ( !bp_is_home() && $bp->current_component == $bp->friends->slug ) || ( !bp_is_home() && $bp->current_component == $bp->blogs->slug ) ) {
		echo ' class="arrow"';
	}
	
	if ( ( $bp->current_component == $bp->groups->slug && $bp->is_single_item ) || ( $bp->current_component == $bp->groups->slug && !bp_is_home() ) )
		echo ' class="arrow"';	
}

function bp_has_icons() {
	global $bp;

	if ( ( !bp_is_home() ) )
		echo ' class="icons"';
}

/* Login Form To Match New Template Layout */
function bp_login_box() {
	global $bp;
	
	if ( !is_user_logged_in() ) : ?>

		<form name="login-form" id="login-form-wrap" action="<?php echo $bp->root_domain . '/wp-login.php' ?>" method="post">
			Username<input type="text" name="log" value="" />
			Password<input type="password" name="pwd" class="input" value="" />
			<div id="login-rem-button">
				<input type="checkbox" name="rememberme" value="forever" title="<?php _e( 'Remember Me', 'buddypress' ) ?>" />
			</div>
			<div id="login-submit">
				<input type="submit" name="wp-submit" value="<?php _e( 'Log In', 'buddypress' ) ?>"/>				
			</div>

				<input type="hidden" name="redirect_to" value="http://<?php echo $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] ?>" />
				<input type="hidden" name="testcookie" value="1" />
						
			<?php do_action( 'bp_login_bar_logged_out' ) ?>

		</form>
		
		<div id="no-account-register">
			<a href="<?php echo bp_signup_page() ?>">Register</a>
		</div>
		
		<div id="close-popup-login">
			<a href="javascript:login('hide');">- close -</a> <!-- Closes the box-->
		</div>
	<?php else : ?>
		
		<div id="logout-link">
			<?php bp_loggedinuser_avatar_thumbnail( 20, 20 ) ?> &nbsp;
			<?php bp_loggedinuser_link() ?>
			<?php 
				if ( function_exists('wp_logout_url') ) {
					$logout_link = '/ <a href="' . wp_logout_url( $bp->root_domain ) . '">' . __( 'Log Out', 'buddypress' ) . '</a>';
				} else {
					$logout_link = '/ <a href="' . $bp->root_domain . '/wp-login.php?action=logout&amp;redirect_to=' . $bp->root_domain . '">' . __( 'Log Out', 'buddypress' ) . '</a>';					
				}			
				
				echo apply_filters( 'bp_logout_link', $logout_link );
			?>
			
			<?php do_action( 'bp_login_bar_logged_in' ) ?>
		</div>
		
	<?php endif;
}


// Remove Original Login and Register Link from Adminbar
remove_action( 'bp_adminbar_menus', 'bp_adminbar_login_menu', 2 );
add_action( 'bp_adminbar_menus', 'my_bp_adminbar_login_menu', 2 );


function my_bp_adminbar_login_menu() {
	global $bp;

	if ( !is_user_logged_in() ) {	
		?><div id="popupbox"> 
<?php bp_login_box() ?>
</div> 
<li class="bp-signup no-arrow"><a href="javascript:login('show');">Login</a></li> <!--Opens the box --><?php
		
		// Show "Sign Up" link if registrations are allowed
		if ( get_site_option( 'registration' ) != 'none' ) {
					?><li class="bp-signup no-arrow"><a href="<?php echo bp_signup_page() ?>">Sign Up</a></li><?php
		}
	}
}

?>