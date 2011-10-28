<?php
/*
Simple:Press
Template Tag(s) - PM
$LastChangedDate: 2010-12-30 17:02:14 -0700 (Thu, 30 Dec 2010) $
$Rev: 5210 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

/* 	=====================================================================================

	sf_pm_tag($display)

	template tag to display number of new PMs in the current user inbox.  This tag includes
	default text that is output with the pm count data and inbox hyperlink.   This text can
	be supressed by setting $display to false. 	If supressed, the new PM count and hyperlink
	are returned to the call in an array.  A -1 count and empty url will be returned for
	guests or user that do not have PM permissions.  Additionally, if the default text is used,
	the no permissions for pm default text can be supressed or those without permissions.

	parameters:

		$display		Determines whether to display pm count plus informational text
		$usersonly		If $display is true, only display pm text for users with pm permissions

 	===================================================================================*/

function sf_pm_tag($display=true, $usersonly=false)
{
	global $wpdb, $current_user, $sfvars;

	sf_initialise_globals($sfvars['forumid']);

	$pm = array();
	if ($current_user->sfusepm)
	{
		$pm['count'] = $wpdb->get_var("SELECT COUNT(message_id) AS cnt FROM ".SFMESSAGES." WHERE (to_id = ".$current_user->ID." AND message_status = 0 AND inbox=1)");
		$pm['url'] = SFURL."private-messaging/inbox/";
	} else {
		$pm['count'] = -1;
		$pm['url'] = '';
	}

	if ($display)
	{
		$out = '';
		if ($current_user->sfusepm)
		{
			$out .= '<p class="sfpmcount">';
			$out .= __("You have ", "sforum").$pm['count'].__(" PM(s) in your ", "sforum").'<a href="'.$pm['url'].'">'.__("inbox", "sforum").'</a>.';
			$out .= '</p>';
		} else if (!$usersonly){
			$out .= '<p class="sfpmcount">';
			$out .= __("You do not have PM permissions.", "sforum");
			$out .= '</p>';
		}
		echo $out;
	}
	return $pm;
}

/* 	=====================================================================================

	sf_sendpm_tag($userid, $text)

	template tag to send a pm to a user.  Default text will be used for the link unless the
	optional $text argument is sent.  If you specify the $text argument, you need to specify
	where in the string you want the link inserted by the sequence %%.  For example:

	$text = '<a href="%%" title="Send PM">Send PM</a>';

	If the person viewing the site is not a registered member or does not have PM permissions,
	then an empty string is returned.

	parameters:

		$userid		user to send a PM to
		$text		optional parameter to specify text, img or html for the link

 	===================================================================================*/

function sf_sendpm_tag($userid, $text='')
{
	global $current_user, $sfvars, $wpdb;

    $userid = sf_esc_int($userid);
	if ($current_user->ID == '' || !$current_user->sfusepm || empty($userid)) return;

	sf_initialise_globals($sfvars['forumid']);

	$out = '';
	if ($userid)
	{
		$user = $wpdb->get_var("SELECT user_login FROM ".SFUSERS." WHERE ID=".$userid);
	    $url = SFURL.'private-messaging/send/'.urlencode($user).'/';
		if ($text == '')
		{
			$out.= '<a class="sfsendpmtag" href="'.$url.'"><img src="'.SFRESOURCES.'sendpm-small.png" title="'.esc_attr(__("Send PM", "sforum")).'" />&nbsp;'.sf_render_icons("Send PM").'</a>';
		} else {
			$out.= str_replace('%%', $url, $text);
		}
	}

	echo $out;

	return;
}

?>