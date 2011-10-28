<?php
/*
Simple:Press
New User Email (SPF Option)
$LastChangedDate: 2010-04-18 09:22:20 -0700 (Sun, 18 Apr 2010) $
$Rev: 3920 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

# = NEW USER EMAIL REPLACEMENT ================
if(!function_exists('wp_new_user_notification')):
function wp_new_user_notification($user_id, $user_pass='')
{
	$user = new WP_User($user_id);
	$sflogin = sf_get_option('sflogin');

	$eol = "\r\n";
	$message='';

	$user_login = $user->user_login;
	$user_email = $user->user_email;

	$message .= esc_attr(__('New user registration on your blog', "sforum").' :'.get_option('blogname').$eol.$eol);
	$message .= esc_attr(__('Username', "sforum").': '.$user_login.$eol);
	$message .= esc_attr(__('E-mail', "sforum").': '.$user_email.$eol);
	$message .= esc_attr(__('Registration IP', "sforum").': '.$_SERVER['REMOTE_ADDR'].$eol);

	sf_send_email(get_option('admin_email'), get_option('blogname').' '.esc_attr(__('New User Registration', "sforum")), $message);

	if ( empty($user_pass) )
		return;

	$mailoptions = sf_get_option('sfnewusermail');
	$subject = stripslashes($mailoptions['sfnewusersubject']);
	$body = stripslashes($mailoptions['sfnewusertext']);
	if((empty($subject)) || (empty($body)))
	{
		$subject = esc_attr(get_option('blogname').' '.__('Your username and password', "sforum").$eol.$eol);
		$body = esc_attr(__('Username', "sforum").': '.$user_login.$eol);
		$body.= esc_attr(__('Password', "sforum").': '.$user_pass.$eol.$eol);
		$body.= $sflogin['sfloginemailurl'].$eol;
	} else {
		$blogname = get_bloginfo('name');
		$subject = str_replace('%USERNAME%', $user_login, $subject);
		$subject = str_replace('%PASSWORD%', $user_pass, $subject);
		$subject = str_replace('%BLOGNAME%', $blogname, $subject);
		$subject = str_replace('%SITEURL%', SFURL, $subject);
		$subject = str_replace('%LOGINURL%', $sflogin['sfloginemailurl'], $subject);
		$body = str_replace('%USERNAME%', $user_login, $body);
		$body = str_replace('%PASSWORD%', $user_pass, $body);
		$body = str_replace('%BLOGNAME%', $blogname, $body);
		$body = str_replace('%SITEURL%', SFURL, $body);
		$body = str_replace('%LOGINURL%', $sflogin['sfloginemailurl'], $body);
		$body = str_replace('%NEWLINE%', $eol, $body);
	}
	str_replace('<br />', $eol, $body);

	sf_send_email($user_email, $subject, $body);
	return;
}
endif;

?>