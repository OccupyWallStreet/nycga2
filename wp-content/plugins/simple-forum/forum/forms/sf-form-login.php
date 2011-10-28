<?php
/*
Simple:Press
In Line Login
$LastChangedDate: 2011-05-07 09:51:29 -0700 (Sat, 07 May 2011) $
$Rev: 6054 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sf_render_inline_login_form()
{
	global $sfglobals;

	$user_login = '';
	$user_pass = '';
	$using_cookie = false;

	$sflogin = sf_get_option('sflogin');

	$redirect_to = $_SERVER['REQUEST_URI'];

   	$out = '<div id="sfloginform">'."\n";

	do_action('login_head', 'sploginform');

	$message = '';
	$message = apply_filters('sf_filter_login_message', $message);
	if (!empty($message)) $out.= $message."\n";

   	$out.= '<fieldset style="vertical-align: top; height: 200px;">'."\n";
    $out.= '<form name="loginform" id="loginform" action="'.$sflogin['sfloginurl'].'" method="post">'."\n";

    $sfrpx = sf_get_option('sfrpx');
    if ($sfrpx['sfrpxenable'])
    {
        $out.= spf_rpx_loginform('sfloginform', '100%', true);
    }

	$out.= '<p><label for="log">'.__("Username:", "sforum").'&nbsp;<input type="text" class="sfcontrol" tabindex="84" name="log" id="log" value="'.esc_attr($user_login).'" size="11" /></label></p>'."\n";
	$out.= '<p><label for="login_password">'.__("Password:", "sforum").'&nbsp;<input type="password" class="sfcontrol" tabindex="85" name="pwd" id="login_password" value="" size="11" /></label></p>'."\n";
	$out.= '<p><input type="checkbox" tabindex="86" id="rememberme" name="rememberme" value="forever" /><label for="rememberme">&nbsp;'.__("Remember me", "sforum").'</label></p>';
	$out.= '<div class="sfclear"></div>';

	$out.= do_action('login_form');
	$out.= '<div class="sfclear"></div>';

	$out.= '<p><input type="submit" class="sfcontrol" name="submit" id="submit" value="'.__("Login", "sforum").'" tabindex="87" /></p>'."\n";
	$out.= '<input type="hidden" name="redirect_to" value="'.esc_attr($redirect_to).'" />'."\n";
	$out.= '</form>'."\n";
	$out.= '<br /><p>';

	if (TRUE == get_option('users_can_register') && !$sfglobals['lockdown'] && $sflogin['sfshowreg'])
	{
	    $out.= '<a href="'.$sflogin['sfregisterurl'].'">'.esc_attr(__('Register', "sforum")).'?</a>'."\n";
		$out.= ' | ';
	}

    $out.= '<a href="'.$sflogin['sflostpassurl'].'">'.esc_attr(__('Lost Your Password', "sforum")).'?</a>'."\n";
    $out.= '</p>'."\n";

   	$out.= '</fieldset></div>'."\n";
	$out.= '<div class="sfclear"></div>';
	return $out;
}

?>