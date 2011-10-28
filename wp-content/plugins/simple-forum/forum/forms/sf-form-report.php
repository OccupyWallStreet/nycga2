<?php
/*
Simple:Press
New report Post Form Rendering
$LastChangedDate: 2010-08-14 04:52:34 -0700 (Sat, 14 Aug 2010) $
$Rev: 4424 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sf_render_report_post_form()
{
	global $wpdb, $current_user, $wp_rewrite;

	$out = '';

	# Check and validate user
	if($current_user->ID != $_POST['rpuser'])
	{
		update_sfnotice('sfmessage', '1@'.__('Access Denied', "sforum"));
		$out = sf_render_queued_message();
		$out.= '<a href="'.$returnurl.'" />'.__("Return", "sforum").'</a>';
		return $out;
	}
	if(isset($_POST['rpurl']))
	{
		$returnurl = sf_permalink_from_postid(sf_esc_int($_POST["rppost"]));
		update_sfsetting($current_user->ID.'@rpurl', $returnurl);
	} else {
		$returnurl = get_sfsetting($current_user->ID.'@rpurl');
	}

	$postcontent = $wpdb->get_var("SELECT post_content FROM ".SFPOSTS." WHERE post_id=".sf_esc_int($_POST['rppost']));
	$postcontent = sf_filter_content_display($postcontent);

	$out.= '<div id="sforum">'."\n";
	$out.='<br /><br />'."\n";
	$out.='<div id="sfstandardform">'."\n";
	$out.='<fieldset>'."\n";
	$out.='<legend>'.__("Report Post", "sforum").'</legend>'."\n";

	$out.='<br /><br /><strong>'.__("Report Questionable Post", "sforum").':</strong><br />';
	$out.='<div class="sfpostreport"><br />';
	$out.= stripslashes($_POST['rpposter']).':<br /><br />';
	$out.= $postcontent;
	$out.='<br /><br /></div>';

	$out.= '<form method="post" action="'.$returnurl.'">';
	$out.= '<input type="hidden" tabindex="0" name="posturl" id="posturl" value="'.esc_attr($returnurl).'" />'."\n";
	$out.= '<input type="hidden" tabindex="0" name="postauthor" id="postauthor" value="'.$_POST['rpposter'].'" />'."\n";
	$out.= '<input type="hidden" tabindex="0" name="postcontent" id="postcontent" value="'.esc_attr($postcontent).'" />'."\n";

	$out.= '<div class="sfformcontainer">'."\n";
	$out.= '<b>'.__("Your Comments On this Post:", "sforum").'</b> ('.__("Required", "sforum").')';
	$out.= '<textarea class="sftextarea" name="postreport" rows="10" cols="60"></textarea>';

	$out.='</div>'."\n";
	$out.='<br />'."\n";

	$out.='<input type="submit" tabindex="100" class="sfcontrol" name="sendrp" value="'.__("Send Post Report", "sforum").'" />'."\n";
	$out.='&nbsp;<input type="button" class="sfcontrol" name="button5" value="'.__("Return to Forum", "sforum").'" onclick="sfjreDirect(\''.$returnurl.'\');" />'."\n";

	$out.='</form>'."\n";
	$out.='</fieldset>'."\n";
	$out.='</div></div>'."\n";

	return $out;
}

?>