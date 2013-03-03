<?php
/*
Plugin Name: Anti-Splog (Feature: Spammed Notice and Splog Review Form)
Plugin URI: http://premium.wpmudev.org/project/anti-splog
Description: The ultimate plugin and service to stop and kill splogs in WordPress Multisite and BuddyPress
Author: Aaron Edwards (Incsub)
Author URI: http://premium.wpmudev.org
Version: 1.1
*/

//return header to remove from search engines
header('HTTP/1.1 410 Gone');

//don't display spam form if archived
if ($current_blog->archived == '1')
  graceful_fail(__('This blog has been archived.'));

require_once( ABSPATH . WPINC . '/pluggable.php' );

//setup proper urls
if ( version_compare($wp_version, '3.0.9', '>') ) {
  $ust_admin_url = network_admin_url('settings.php?page=ust');
} else {
  $ust_admin_url = network_admin_url('ms-admin.php?page=ust');
}

//process form
if (isset($_POST['spam-submit']) && !get_option('ust_email_sent')) {
  $reason = wp_filter_nohtml_kses(stripslashes(trim($_POST['reason'])));  
  
  if (strlen($reason) < 20)
    $error1 = '<p class="error">'.__("Please enter a valid reason.", 'ust').'</p>';
  
  //check reCAPTCHA
  $recaptcha = get_site_option('ust_recaptcha');
  if ($recaptcha['privkey']) {
   	require_once(WP_PLUGIN_DIR . '/anti-splog/includes/recaptchalib.php');
  	$resp = rp_recaptcha_check_answer($recaptcha['privkey'], $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);
  	
  	if (!$resp->is_valid) {
  	  $error2 = '<p class="error">'.__("The reCAPTCHA wasn't entered correctly. Please try again.", 'ust').'</p>';
  	}
  }
  
  if (!$error1 && !$error2) {
    
    $admin_email = get_site_option( "admin_email" );
    $user_email = get_option('admin_email');
    $review_url = $ust_admin_url . "&tab=splogs&bid=$blog_id";
    $unspam_url = network_admin_url("edit.php?action=confirm&action2=unspamblog&id=$blog_id&ref=" . urlencode($ust_admin_url) . "&msg=" . urlencode( sprintf( __( "You are about to unspam the blog %s" ), get_bloginfo('name') ) ) );
    $message_headers = "MIME-Version: 1.0\n" . "From: $user_email\n" . "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"\n";
    $subject = sprintf(__('Splog Review Request: %s', 'ust'), get_bloginfo('url'));
    $message = sprintf(__("Someone is disputing the spam status for the blog %s (%s).\nHere is their reason:\n_______________________\n\n%s\n\n_______________________\n", 'ust'), get_bloginfo('name'), get_bloginfo('url'), $reason);
    $message .= sprintf(__("Review: %s\n", 'ust'), $review_url);
    $message .= sprintf(__("Unspam: %s\n", 'ust'), $unspam_url);
    wp_mail($admin_email, $subject, $message, $message_headers);
    
    //save that the email was sent
    update_option('ust_email_sent', '1');
    $email_sent = true;
  }
}

$auto_spammed = get_option('ust_auto_spammed');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head>
	<title><?php echo $current_site->site_name; ?> &rsaquo; <?php _e('Blog Spammed') ?></title>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
	<meta name="robots" content="noindex, nofollow" />
	<?php
	wp_admin_css( 'login', true );
	wp_admin_css( 'colors-fresh', true );

	if ( $is_iphone ) {
	?>
	<meta name="viewport" content="width=320; initial-scale=0.9; maximum-scale=1.0; user-scalable=0;" /> 
	<style type="text/css" media="screen"> 
	form { margin-left: 0px; }
	#login { margin-top: 20px; }
	</style>
	<?php
	}
  //display modified logo if premium Login Image plugin is installed
  if (function_exists('login_image_stylesheet')) {
    login_image_stylesheet();
  }
 ?>
  <style type="text/css" media="screen"> 
    #login { width: 340px; }
    #login form p { margin-bottom: 5px; }
    #login h1 a { width: 340px; }
    #reCAPTCHA { margin-left: -10px; }
    p.error { border: 1px solid red; padding: 5px; }
  </style>
</head>
<body class="login">

<div id="login"><h1><a href="<?php echo 'http://' . $current_site->domain . $current_site->path; ?>" title="<?php echo $current_site->site_name; ?>"></a></h1>

<form name="contactform" id="loginform" action="<?php echo trailingslashit( get_bloginfo('url') ); ?>" method="post">
  
<?php if ($email_sent) { ?>

  <p><?php _e('Your message has been sent. We will review it shortly.', 'ust'); ?></p>
  
<?php } else { ?>
  <?php if ($auto_spammed) { ?>
    <p><?php _e('Our automated filters have determined that this blog signup looks like it could be by a spammer. Because of this, to complete you registration please describe in one or two sentences what you intend to use this blog for in the form below and we will review your request. Thank you for your cooperation!', 'ust'); ?></p>
  <?php } else { ?>
    <p><?php _e('Sorry, but this blog has been marked as spam as defined in our Terms of Service.', 'ust'); ?></p>
  <?php } ?>
  
<?php if (!get_option('ust_email_sent')) { ?>
  <?php if (!$auto_spammed) { ?>
  <p><?php _e('If you believe this decision was made in error you may contact us with your <strong>detailed</strong> reasons using the form below:', 'ust'); ?></p>
  <?php }
  echo $error1; ?>
  <p>
		<label><?php _e('Reason:', 'ust') ?><br />
		<textarea name="reason" style="width: 100%" rows="5" tabindex="20"><?php echo htmlentities($reason); ?></textarea></label>
	</p>
	<?php
    $recaptcha = get_site_option('ust_recaptcha');
    
    if ($recaptcha['privkey']) {
      require_once(WP_PLUGIN_DIR . '/anti-splog/includes/recaptchalib.php');
      
      echo "<script type='text/javascript'>var RecaptchaOptions = { theme : 'white', lang : '{$recaptcha['lang']}' , tabindex : 30 };</script>";
      echo '<p><label>'.__('Human Verification:', 'ust').'</label></p>';
    	echo $error2;
      echo '<div id="reCAPTCHA">';
      echo rp_recaptcha_get_html($recaptcha['pubkey']);
      echo '</div>&nbsp;<br />';
    }
  ?>
	<br class="clear" />
	<p class="submit">
		<input type="submit" name="spam-submit" id="wp-submit" value="<?php _e('Submit', 'ust'); ?>" tabindex="100" />
	</p>
<?php } else { ?>
  <p><?php _e('The admin has already been contacted to review.', 'ust'); ?></p>
<?php  
  }
} ?>
	
</form>

</div>

<p id="backtoblog"><a href="<?php echo 'http://' . $current_site->domain . $current_site->path; ?>" title="<?php _e('Are you lost?') ?>"><?php echo sprintf(__('&larr; Back to %s', 'ust'), $current_site->site_name); ?></a></p>

</body>
</html>