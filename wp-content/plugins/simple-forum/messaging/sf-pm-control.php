<?php
/*
Simple:Press
Private Messaging control
$LastChangedDate: 2011-01-07 03:19:34 -0700 (Fri, 07 Jan 2011) $
$Rev: 5274 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

function sf_message_control($view)
{
	global $current_user, $sfglobals, $sfvars, $wpdb, $targetscript;

	# inline js to create spiltter bar
    add_action( 'wp_footer', 'sfjs_add_PM_splitter' );

	$targetuser = false;
	$alt = '';
	$out = '';

	# Check and validate user and ensure PMs are allowed
	if ($current_user->ID && !$current_user->sfusepm)
    {
		# we have a user but not one who can use PMs
		$out.= '<div id="sforum">'."\n";
		$out.= '<div class="sfblock">'."\n";
		$out.= '<div class="sfmessagestrip">';
		$out.= __('Access Denied! You do not have permission to use Private Messaging', 'sforum');
		$out.= '</div>'."\n";
		$out.= '<br /><img class="sfalignright" src="'. SFRESOURCES .'spacer.png" alt="" /><a class="sficon sfalignleft" href="'.SFURL.'"><img src="'.SFRESOURCES.'goforum.png" alt="" title="'.esc_attr(__("Return to forum", "sforum")).'" />'.sf_render_icons("Return to forum").'</a>'."\n";
		$out.= '<br /><br /></div>';
		$out.= '</div>';
		return $out;
	} else if ($current_user->ID == 0) {
        # not a registered user
		$returnurl = $_SERVER['REQUEST_URI'];
		$out.= '<div id="sforum">'."\n";
		$out.= '<div class="sfblock">'."\n";
		$out.= sf_render_login_strip('pm', 'pm', '');
		$out.= sf_render_login_form();
		$out.= '<br /><img class="sfalignright" src="'. SFRESOURCES .'spacer.png" alt="" /><a class="sficon sfalignleft" href="'.SFURL.'"><img src="'.SFRESOURCES.'goforum.png" alt="" title="'.esc_attr(__("Return to forum", "sforum")).'" />'.sf_render_icons("Return to forum").'</a>'."\n";
		$out.= '<br /><br /></div>';
		$out.= '</div>';
		return $out;
	}

	# Did we enter this with a user id to message to? (From the forum)
	if (!empty($sfvars['member']))
	{
		$targetuser = sf_get_user_id_from_user_login(urldecode($sfvars['member']));
		$targetname = $wpdb->get_var("SELECT display_name FROM ".SFMEMBERS." WHERE user_id=".$targetuser);
        if (empty($targetuser))
        {
      		update_sfnotice('sfmessage', '1@'.__('The specified user does not exist!', "sforum"));
        }
	}

	# Setup some stuff we need
	$lastvisit = sf_track_online();

	# Load up the data we need
	if ($view == 'inbox')
	{
		# ensure admins have pm rights - crude fix for occasional admin pm flag getting cleared somewhere
		$wpdb->query("UPDATE ".SFMEMBERS." SET pm=1 WHERE admin=1");

		$messagebox = sf_get_pm_inbox($current_user->ID);
	} else {
		$messagebox = sf_get_pm_sentbox($current_user->ID);
	}

	# Grab message count
	$messagecount = $wpdb->get_var("SELECT FOUND_ROWS()");

	# Load the max box size if set
	$sfpm = sf_get_option('sfpm');
	$maxsize = $sfpm['sfpmmax'];
	$cansendpm = true;
	$boxmsg = '';

	# Prepare the messages if box size exceeded...
 	if ($current_user->forumadmin == 0 && $maxsize > 0)
	{
		$boxsize = sf_get_pm_boxcount($current_user->ID);
		if($boxsize > $maxsize)
		{
			$boxmsg = __("Your Inbox/Sentbox", "sforum").'('.$boxsize.__('messages', 'sforum').') '.__("has exceeded the Maximum Allowed", "sforum").' ('.$maxsize.') - '.__("Please delete some messages", "sforum");
			$cansendpm = false;
		} elseif($boxsize == $maxsize)
		{
			$boxmsg = __("Your Inbox/Sentbox", "sforum").'('.$boxsize.__('messages', 'sforum').') '.__("has reached the Maximum Allowed", "sforum").' ('.$maxsize.') - '.__("Please delete some messages", "sforum");
			$cansendpm = false;
		} elseif($boxsize > ($maxsize-5))
		{
			$boxmsg = __("Your Inbox/Sentbox", "sforum").'('.$boxsize.__('messages', 'sforum').') '.__("is approaching the Maximum Allowed", "sforum").' ('.$maxsize.') - '.__("Please delete some messages", "sforum");
		}
	}

	# dont show compose if forum locked down
	if ($sfglobals['lockdown']) $cansendpm = false;

	# Top of the pm (same as forum) - Display starts here
	$out = sf_render_queued_message();

	# Start Display
	$out.= '<div class="inline_edit" id="sfdummy"></div>';
	$out.= '<div class="inline_edit" id="pmview">'.$view.'</div>';
	$out.= '<div id="sforum">'."\n";
	$out.= '<a id="forumtop">&nbsp;</a>'."\n";
	$out.= '<div id="sflogininfo"></div>';

	if ($sfglobals['member']['admin_options']['sfadminbar'])
	{
		# Check if admin and if any new posts waiting
		$newposts='';
		if($current_user->forumadmin || $current_user->moderator)
		{
			$newposts = sf_get_admins_queued_posts();
		}
		$out.= sf_render_admin_strip('inbox', 'inbox', $newposts);
	}

	if ($sfglobals['lockdown']) $out.= sf_render_lockdown();
	$out.= sf_render_login_strip('pm', 'pm', '');

	$out.= '<div class="sfmessagestrip">';
	$out.= '<table><tr>'."\n";
	$out.= '<td class="sficoncell"><img class="" src="'.esc_url(SFRESOURCES."inbox.png").'" alt="" /></td>'."\n";
	if ($view == 'inbox')
	{
		$heading = __("Inbox", "sforum");
	} else {
		$heading = __("Sentbox", "sforum");
	}
	$out.= '<td><p>'.$heading.'<br /><small>'.sf_filter_name_display($current_user->display_name).'</small></p></td>'."\n";
	$out.= '<td class="sflogincell">';
	$sfpm = array();
	$sfpm = sf_get_option('sfpm');
	if ($cansendpm && (!$sfpm['sfpmlimitedsend'] || $current_user->forumadmin))
	{
		$out.= '<a class="sficon sfalignright" onclick="sfjtoggleLayer(\'sfpostform\');"><img src="'.SFRESOURCES.'compose.png" alt="" title="'.esc_attr(__("Compose PM", "sforum")).'" />'.sf_render_icons("Compose PM").'</a>';
	}

	if ($view == 'sentbox')
	{
		$url = SFURL."private-messaging/inbox/";
		$out.= '<a class="sficon sfalignright" href="'.$url.'"><img src="'.SFRESOURCES.'goinbox.png" alt="" title="'.esc_attr(__("Go To Inbox", "sforum")).'" />&nbsp;'.sf_render_icons("Go To Inbox").'</a>'."\n";
	} else {
		$url = SFURL."private-messaging/sentbox/";
		$out.= '<a class="sficon sfalignright" href="'.$url.'"><img src="'.SFRESOURCES.'gosentbox.png" alt="" title="'.esc_attr(__("Go To Sentbox", "sforum")).'" />&nbsp;'.sf_render_icons("Go To Sentbox").'</a>'."\n";
	}
	$out.= '<img class="sfalignright" src="'. SFRESOURCES .'spacer.png" alt="" /><a class="sficon sfalignright" href="'.SFURL.'"><img src="'.SFRESOURCES.'goforum.png" alt="" title="'.esc_attr(__("Return to forum", "sforum")).'" />'.sf_render_icons("Return to forum").'</a>'."\n";
	$out.= '</td>'."\n";
	$out.= '</tr></table></div>'."\n";

	# notice about auto delete pms
	$sfpm = sf_get_option('sfpm');
	if ($sfpm['sfpmremove'])
	{
		$msg = __("Please note, PMs are automatically removed after", "sforum").' '.$sfpm['sfpmkeep'].' '.__("days", "sforum").'.';
		$out.= sf_render_pm_inbox_warning($msg);
	}

	# inbox/outbox size warnings
	if ($boxmsg != '')
	{
		$out.= sf_render_pm_inbox_warning($boxmsg);
	}

	if (!$messagebox)
	{
		$out.= '<div class="sfmessagestrip">'.sprintf(__("Your %s is empty", "sforum"), sf_localise_boxname($view)).'</div>'."\n";
	} else {
    	$out.= '<table class="sfforumtable" id="sfmainpmheader">'."\n";
    	$out.= sf_render_pm_outer_header_row($view, $messagecount);
        $out.= '</table>';

    	# Paint the table
        $out.= '<div id="pmsplitter">';
    	$out.= '<div id="pmtoppane">';

    	# Begin main outer table of threads
    	$out.= '<table class="sfforumtable" id="sfmainpmtable">'."\n";
    	$out.= sf_render_pm_table($view, $messagebox, $messagecount, $cansendpm);
        $out.= '</table>';

        # close toppane
    	$out.= '</div>';

        # message preview block
    	$out.= '<div id="pmbottompane">';
    	$out.= '<div id="sfpmpreview">';
    	$out.= sf_render_pm_preview($view, $messagebox, $messagecount, $cansendpm);
    	$out.= '</div>';

        # close bottompane
    	$out.= '</div>';
        # close splitter
    	$out.= '</div>';
    }

	$out.= sf_render_bottom_iconstrip($view, $current_user->ID, $cansendpm);

	if ($cansendpm)
	{
		$out.= '<a id="dataform"></a>'."\n";
		$out.= sf_add_pm();

		# Did we enter this from the forum with a message to write?
		if($targetuser)
		{
			$title = '';
			$reply = 0;
			$editor = $sfglobals['editor']['sfeditor'];
			$targetscript = '<script type="text/javascript">'."\n";
			$targetscript.= 'jQuery(document).ready(function() {'."\n";
			$targetscript.= 'sfjsendPMTo(\''.$targetuser.'\', \''.$targetname.'\', \''.$title.'\', \''.$reply.'\', \'\', \''.$editor.'\');';
			$targetscript.= '});'."\n";
			$targetscript.= '</script>'."\n"."\n";

			# inline js to send Pm to target user (from post pm button) - $targetscript global
		    add_action( 'wp_footer', 'sfjs_send_pm_target' );
		}
	} else if (!$sfglobals['lockdown']) {
		$out.= sf_render_pm_inbox_warning(esc_attr(__("You will be unable to send any further messages until your Inbox/Sentbox size is reduced", "sforum")));
	}

	$out.= sf_render_stats();
	$out.= sf_process_hook('sf_hook_footer_inside', '');

	$out.= sf_render_version_strip();
	$out.= '<a id="forumbottom">&nbsp;</a>'."\n";
	$out.= '</div>'."\n";

	$out.= sf_process_hook('sf_hook_footer_outside', '');

	return $out;
}


# inline js to send Pm to target user (from post pm button)
function sfjs_send_pm_target()
{
	global $targetscript;
	echo $targetscript;
}

# inline js to create spiltter bar
function sfjs_add_PM_splitter() {
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery("#pmsplitter").splitter({
		type: "h",
		cookie: "sfsplitter",
		sizeTop: true	/* use height set in stylesheet */
	});
});
</script>
<?php
}

?>