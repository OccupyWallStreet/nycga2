<?php
/*
Simple:Press
New PM Form Rendering
$LastChangedDate: 2010-10-29 13:47:13 -0700 (Fri, 29 Oct 2010) $
$Rev: 4838 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

function sf_render_add_pm_form()
{
	global $current_user, $sfglobals;

	# inline js - build autocomplete
	add_action( 'wp_footer', 'sfjs_build_PM_users' );

	$out = '';

	$editor="TM";
	if($sfglobals['editor']['sfeditor'] != RICHTEXT) $editor="QT";

	$valmsg0 = esc_js(__("Incomplete Entry! Please correct and re-save", "sforum"));
	$valmsg1 = esc_js(__("No Recipients Selected", "sforum"));
	$valmsg2 = esc_js(__("No Message Title Entered", "sforum"));
	$valmsg3 = esc_js(__("No Message Text Entered", "sforum"));
	$valmsg4 = esc_js(__("Saving PM", "sforum"));
	$valmsg5 = esc_js(__("Please Wait", "sforum"));

	$out.='<br />'."\n";
	$out.='<div id="sfpostform">'."\n";
	$out.='<fieldset>'."\n";
	$out.='<legend><b>'.__("Compose Private Message", "sforum").'</b></legend>'."\n";

	$out.= '<form action="'.SFHOMEURL.'index.php?sf_ahah=pm-post" method="post" name="addpm" onsubmit="return sfjvalidatePMForm(this, \''.$editor.'\', \''.$valmsg0.'\', \''.$valmsg1.'\', \''.$valmsg2.'\', \''.$valmsg3.'\', \''.$valmsg4.'\', \''.$valmsg5.'\')">'."\n";

	$out.= sfc_create_nonce('forum-userform_addpm');

	# use inputs to pass data to js
	$sfpm = array();
	$sfpm = sf_get_option('sfpm');

    # dont limit # of recipients for admins
	if ($current_user->forumadmin) $sfpm['sfpmmaxrecipients'] = 0;

	$cc = $bcc = 0;
	if ($sfpm['sfpmcc'] && !$sfpm['sfpmlimitedsend']) $cc = 1;
	if ($sfpm['sfpmbcc'] && !$sfpm['sfpmlimitedsend']) $bcc = 1;
	$out.= '<input type="hidden" tabindex="0" name="pmcc" id="pmcc" value="'.$cc.'" />'."\n";
	$out.= '<input type="hidden" tabindex="0" name="pmbcc" id="pmbcc" value="'.$bcc.'" />'."\n";
	$out.= '<input type="hidden" tabindex="0" name="pmcount" id="pmcount" value="0" />'."\n";
	$out.= '<script type="text/javascript">count = document.getElementById("pmcount");if (count) {document.getElementById("pmcount").value = 0;}</script>';
	$out.= '<input type="hidden" tabindex="0" name="pmmax" id="pmmax" value="'.$sfpm['sfpmmaxrecipients'].'" />'."\n";
	$out.= '<input type="hidden" id="uid" value="1" />';
	$out.= '<input type="hidden" name="pmimage" id="pmdelimage" value="'.SFADMINIMAGES.'bad.gif'.'" />'."\n";
	$msg = __("Remove Recipient", "sforum");
	$out.= '<input type="hidden" name="pmdelmsg" id="pmdelmsg" value="'.esc_attr($msg).'" />'."\n";
	$out.= '<input type="hidden" name="pmimage" id="pmaddimage" value="'.SFADMINIMAGES.'usergroups.gif'.'" />'."\n";
	$msg = __("Add Recipient to Buddy List", "sforum");
	$out.= '<input type="hidden" name="pmaddmsg" id="pmaddmsg" value="'.esc_attr($msg).'" />'."\n";
	$msg = __("Maximum Number of PM Recipients", "sforum").' ('.$sfpm['sfpmmaxrecipients'].') '.__("Exceeded", "sforum").'!';
	$out.= '<input type="hidden" name="pmmaxmsg" id="pmmaxmsg" value="'.esc_attr($msg).'" />'."\n";
	$out.= '<input type="hidden" tabindex="0" name="pmaction" id="pmaction" value="savepm" />'."\n";
	$out.= '<input type="hidden" tabindex="0" name="pmuser" id="pmuser" value="'.$current_user->ID.'" />'."\n";
	$out.= '<input type="hidden" tabindex="0" name="pmslug" id="pmslug" value="" />'."\n";
	$out.= '<input type="hidden" tabindex="0" name="pmlimited" id="pmlimited" value="'.$sfpm['sfpmlimitedsend'].'" />'."\n";
	$out.= '<input type="hidden" tabindex="0" name="pmall" id="pmall" value="0" />'."\n";

	# Recipient Selection
	$msg = __("Select Message Recipients", "sforum");
	if ($sfpm['sfpmlimitedsend'] && !$current_user->forumadmin) $msg = __("Message Recipient", "sforum");
	$out.= '<fieldset><legend>'.$msg.'</legend>';
	$out.= '<table class="sfpmrecipient">';
	$out.= '<tr valign="top">';
	$out.= '<td width="35%">';
	if (!$sfpm['sfpmlimitedsend'] || $current_user->forumadmin)
	{
		$out.= '<p><label><b>'.__("Select From Members", "sforum").':</b></label><br /><input type="text" id="pmusers" class="sfcontrol" name="pmusers" style="width:87%" />';
		$out.= '<br /><small>'.__("Start Typing a Member's Name in the input field above and it will auto-complete", "sforum").'</small></p>';
        # admins can email all users
        if ($current_user->forumadmin)
        {
    		$out.='<br /><input type="button" class="sfcontrol" id="sfpmall" onclick="sfjpmallusers()" value="'.esc_attr(__("PM All Users", "sforum")).'" />';
    		$out.= '<br /><small>'.__("Not Recommended for Large # of Users", "sforum").'</small><br />';
        }
		$out.= '<br /><p><label><b>'.__("Select From Buddy List", "sforum").':</b></label><br />';
		$out.= '<span id="pmbuddies"><select class="sflistcontrol" tabindex="101" name="pmbudlist" id="pmbudlist" size="6" onchange="sfjpmaddbuddy(\'pmbudlist\');">'."\n";
		$out.= sf_create_pmuser_select();
		$out.= '</select></span></p>'."\n";
		$out.= '</td>';
		$out.= '<td width="5%" ></td>';
		$out.= '<td width="60%">';
		$out.= '<p><label><b>'.__("Message Recipients", "sforum").':</b></label></p>';
	}
	$style = '';
	if ($sfpm['sfpmlimitedsend'] && !$current_user->forumadmin) $style = ' style="min-height:20px;border:none !important;width:250px"';
	$out.= '<div id="pmtonamelist"'.$style.'></div>';
	if (!$sfpm['sfpmlimitedsend'] || $current_user->forumadmin)
	{
		if ($sfpm['sfpmmaxrecipients'] > 0) $out.= '<p style="text-align:center">'.__("Maximum of ", "sforum").$sfpm['sfpmmaxrecipients'].__(" Recipients Allowed", "sforum").'</p>';
        $site = SFHOMEURL."index.php?sf_ahah=pm-manage";
		$out.= '<input type="hidden" name="pmsite" id="pmsite" value="'.$site.'" />'."\n";
		$out.= '<p style="text-align:center"><input type="button" class="sfcontrol" name="addbuddy" id="addbuddy" value="'.esc_attr(__("Add ALL Recipients To Buddy List", "sforum")).'" onclick="sfjpmaddbuddies();" /></p>';
		$out.='<div class="highslide-html-content" id="pm-tolist" style="width: 300px">'."\n";
		$out.='<div class="inline-edit" id="sfpmexceed"></div>'."\n";
		$out.='<input type="button" class="sfcontrol" id="sfpmclose" onclick="return hs.close(this)" value="'.esc_attr(__("Close", "sforum")).'" />'."\n";
		$out.='</div>'."\n";
	}
	$out.= '</td>';
	$out.= '</tr></table>'."\n";
	$out.= '</fieldset>';

	$out.= '<br />'."\n";
	$dummy = "";
	$out.= '<input type="hidden" tabindex="0" size="45" name="pmtoidlist" id="pmtoidlist" value="'.$dummy.'" />'."\n";
	$out.= '<input type="hidden" tabindex="0" name="pmreply" id="pmreply" value="" />'."\n";

	$out.='<table class="sfpostsavetable">';
	$out.='<tr><td width="100%"><p><b>'.__("Title", "sforum").':</b>&nbsp;&nbsp;';
	$out.='<input type="hidden" name="pmoldtitle" id="pmoldtitle" value="" />';
	$out.='<input type="text" tabindex="107" class="sfcontrol sfpostcontrol" size="55" maxlength="180" name="pmtitle" id="pmtitle" value="" /></p></td></tr>'."\n";
	$out.='</table>';

	$out.='<div class="sfformcontainer">'."\n";

	$out.= sf_setup_editor(5);

	$out.='</div>'."\n";

	$out.='<br />'."\n";

	# Send/Smileys/
	$out.= '<table class="sfpostsavetable">'."\n";
	$out.= '<tr>'."\n";

	# Do we show the Smileys cell
	if($sfglobals['smileyoptions']['sfsmallow'] && $sfglobals['smileyoptions']['sfsmtype']==1)
	{
		# Yes we do
		$out.= '<td class="sfpostheading">'.__("Smileys", "sforum").'</td>';
	}

	$out.= '<td class="sfpostheading">'.__("Send Message", "sforum").'</td>';

	$out.= '</tr><tr>';

	# Smileys
	if($sfglobals['smileyoptions']['sfsmallow'] && $sfglobals['smileyoptions']['sfsmtype']==1)
	{
		$out.= '<td valign="top">'."\n";
		$out.= sf_render_smileys();
		$out.= '</td>'."\n";
	}

	# Send/Cancel
	$out.= '<td valign="top">'."\n";

	$out.='<input type="submit" tabindex="108" class="sfcontrol" name="newpm" id="sfsave" value="'.__("Send Message", "sforum").'" />'."\n";
	$out.='<input type="button" tabindex="109" class="sfcontrol" name="cancel" id="sfcancel" value="'.__("Cancel", "sforum").'" onclick="sfjtoggleLayer(\'sfpostform\');" />'."\n";

	$out.='<div class="highslide-html-content" id="my-content" style="width: 200px">'."\n";
	$out.='<div class="inline-edit" id="sfvalid"></div>'."\n";
	$out.='<input type="button" class="sfcontrol" id="sfclosevalid" onclick="return hs.close(this)" value="'.__("Close", "sforum").'" />'."\n";
	$out.='</div>'."\n";

	$out.= '</td>'."\n";

	$out.= '</tr>'."\n";
	$out.= '</table>'."\n";

	$out.='</form>'."\n";
	$out.='</fieldset>'."\n";
	$out.='</div>'."\n";

	return $out;
}

# inline js to create autocomplete lists
function sfjs_build_PM_users() {
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery("#pmusers").autocomplete("<?php echo SFAUTOCOMP; ?>", { onItemSelect:sfjpmadduser, selectOnly:1, delay:10, maxItemsToShow:25, matchContains:1, resultsClass:"sfac_results", extraParams:{ search:1 } });
});
count = document.getElementById('pmcount')
if (count) {document.getElementById('pmcount').value = 0;}
</script>
<?php
}

?>