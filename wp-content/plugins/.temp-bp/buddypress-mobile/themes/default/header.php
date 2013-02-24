<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

	<head profile="http://gmpg.org/xfn/11">

		<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
		<meta content="minimum-scale=1.0, width=device-width, maximum-scale=0.6667, user-scalable=no" name="viewport" />
		<meta name="apple-mobile-web-app-capable" content="no">

		<title><?php bp_page_title() ?></title>


		<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" /> <!-- leave this for stats -->

		<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
		
		<?php do_action( 'bp_head' ) ?>

		
		<?php wp_head(); ?>
		
		<script>
		addEventListener("load", function(){
        		setTimeout(updateLayout, 0);
    		}, false);

    		var currentWidth = 0;
    
    	function updateLayout(){
        	if (window.innerWidth != currentWidth)
        {
            currentWidth = window.innerWidth;

            var orient = currentWidth == 320 ? "profile" : "landscape";
            document.body.setAttribute("orient", orient);
            setTimeout(function(){
                window.scrollTo(0, 1);
            }, 100);            
        }
    }

    setInterval(updateLayout, 400);
		</script>

	</head>

	<body <?php body_class() ?>>
		
		<div id="topbar">
			<?php if (bp_is_front_page()) { ?>
				
				<?php if ( is_user_logged_in() ) { ?>
   				<div id="leftnav-login"><a href="#login"><?php _e( 'Logout', 'buddypress' ) ?></a> </div>
   				<?php } else { ?>
   				<div id="leftnav-login"><a href="#login"><?php _e( 'Login', 'buddypress' ) ?></a> </div>
   				<?php } ?>
   			
   			<?php } else { ?>
  			 	<div id="leftnav"><a href="<?php echo site_url() ?>"><?php _e( 'Home', 'buddypress' ) ?></a> </div>
  			 <?php } ?>
			
		 		<div id="rightnav"><a href="#menu"><?php _e( 'Menu', 'buddypress' ) ?></a> </div>	
				<div id="title"><?php bp_site_name() ?></div>
				
		</div>
		
		<div id="loginNav">
			<div id="userInfo">
			<?php if ( is_user_logged_in() ) : ?>

		<?php do_action( 'bp_before_sidebar_me' ) ?>

		
			<a href="<?php echo bp_loggedin_user_domain() ?>">
				<?php bp_loggedin_user_avatar( 'type=thumb&width=40&height=40' ) ?>
			</a>

			<h4><?php echo bp_core_get_userlink( bp_loggedin_user_id() ); ?></h4>
			<a class="button logout" href="<?php echo wp_logout_url( bp_get_root_domain() ) ?>"><?php _e( 'Log Out', 'buddypress' ) ?></a>

			<?php do_action( 'bp_sidebar_me' ) ?>
	

		<?php do_action( 'bp_after_sidebar_me' ) ?>

		<?php if ( function_exists( 'bp_message_get_notices' ) ) : ?>
			<?php bp_message_get_notices(); /* Site wide notices to all users */ ?>
		<?php endif; ?>

	<?php else : ?>

		<?php do_action( 'bp_before_sidebar_login_form' ) ?>

		<p id="login-text">
			<?php _e( 'To start connecting please log in first.', 'buddypress' ) ?>
			<?php if ( bp_get_signup_allowed() ) : ?>
				<?php printf( __( ' You can also <a href="%s" title="Create an account">create an account</a>.', 'buddypress' ), site_url( BP_REGISTER_SLUG . '/' ) ) ?>
			<?php endif; ?>
		</p>

		<form name="login-form" id="sidebar-login-form" class="standard-form" action="<?php echo site_url( 'wp-login.php', 'login_post' ) ?>" method="post">
			<label><?php _e( 'Username', 'buddypress' ) ?><br />
			<input type="text" name="log" id="sidebar-user-login" class="input" value="<?php echo esc_attr(stripslashes($user_login)); ?>" tabindex="97" /></label>

			<label><?php _e( 'Password', 'buddypress' ) ?><br />
			<input type="password" name="pwd" id="sidebar-user-pass" class="input" value="" tabindex="98" /></label>

			<p class="forgetmenot"><label><input name="rememberme" type="checkbox" id="sidebar-rememberme" value="forever" tabindex="99" /> <?php _e( 'Remember Me', 'buddypress' ) ?></label></p>

			<?php do_action( 'bp_sidebar_login_form' ) ?>
			<input type="submit" name="wp-submit" id="sidebar-wp-submit" value="<?php _e('Log In'); ?>" tabindex="100" />
			<input type="hidden" name="testcookie" value="1" />
		</form>

		<?php do_action( 'bp_after_sidebar_login_form' ) ?>

	<?php endif; ?>

			</div>
		</div>
