<?php
/*
Simple:Press
Registration Policy Form
$LastChangedDate: 2010-08-17 13:57:41 -0700 (Tue, 17 Aug 2010) $
$Rev: 4466 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sf_render_policy_form()
{
	$sflogin = sf_get_option('sflogin');
	$sfpolicy = sf_get_option('sfpolicy');

	$out='';

	$out .= '<div id="sforum">'."\n";
	$out .= '<div id="sfstandardform">'."\n";

	$out .= '<fieldset><legend><b>'.__("Registration Policy", "sforum").'</b></legend>'."\n";
	$out .= '<div class="sfregdocument">';

	$out.= sf_retrieve_policy_document('registration');

	$out.= '</div>';
	$out.= '</fieldset>'."\n";
	$out .= '<br />'."\n";

	if($sfpolicy['sfregcheck'])
	{
		$out .= '<p>'.__("Accept Policy to Register", "sforum")."\n";
		$out .= '<input type="checkbox" name="accept" tabindex="1" value="" onchange="sfjtoggleRegister(this);" /></p>'."\n";
		$enabled = ' disabled="disabled" ';
	} else {
		$enabled=" ";
	}
	$out .= '<br />'."\n";
	$out .= '<input type="button" class="sfcontrol"'.$enabled.' tabindex="2" id="regbutton" name="regbutton" value="'.esc_attr(__('Register', "sforum")).'" onclick="sfjreDirect( \''.$sflogin['sfregisterurl'].'&amp;accept=true\');" />'."\n";
	$out .= '<input type="button" class="sfcontrol" tabindex="3" id="retbutton" name="retbutton" value="'.esc_attr(__('Return to Forum', "sforum")).'" onclick="sfjreDirect(\''.SFURL.'\');" />'."\n";
	$out .= '</div></div>'."\n";

	return $out;
}

?>