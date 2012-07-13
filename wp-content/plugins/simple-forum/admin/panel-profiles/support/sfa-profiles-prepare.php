<?php
/*
Simple:Press
Admin Config Support Functions
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_get_options_data()
{
	$sfprofile=sf_get_option('sfprofile');

	$sfsigimagesize = sf_get_option('sfsigimagesize');
	$sfprofile['sfsigwidth'] = $sfsigimagesize['sfsigwidth'];
	$sfprofile['sfsigheight'] = $sfsigimagesize['sfsigheight'];

	return $sfprofile;
}

function sfa_get_data_data()
{
	$sfprofile=sf_get_option('sfprofile');

	return $sfprofile;
}

function sfa_get_fields_data()
{
	$custom_fields = sf_get_sfmeta('custom_field');

	return $custom_fields;
}

function sfa_get_avatars_data()
{
	$sfavatars = sf_get_option('sfavatars');

	return $sfavatars;
}

function sfa_paint_avatar_pool()
{
	global $tab, $SFPATHS;

	$out='';

	# Open avatar pool folder and get cntents for matching
	$path = SF_STORE_DIR.'/'.$SFPATHS['avatar-pool'].'/';
	$dlist = @opendir($path);
	if (!$dlist)
	{
		echo '<strong>'.__("The avatar pool folder does not exist", "sforum").'</strong>';
		return;
	}

	# start the table display
	$out.= '<tr>';
	$out.= '<th style="width:60%;text-align:center">'.__("Avatar", "sforum").'</th>';
	$out.= '<th style="width:30%;text-align:center">'.__("Filename", "sforum").'</th>';
	$out.= '<th style="width:9%;text-align:center">'.__("Remove", "sforum").'</th>';
	$out.= '</tr>';

    $out.= '<tr><td colspan="3">';
    $out.= '<div id="sf-avatar-pool">';
	while (false !== ($file = readdir($dlist)))
	{
		if ($file != "." && $file != "..")
		{
			$found = false;
		    $out.= '<table width="100%">';
			$out.= '<tr>';
			$out.= '<td align="center" width="60%" ><img class="sfavatarpool" src="'.esc_url(SFAVATARPOOLURL.'/'.$file).'" alt="" /></td>';
			$out.= '<td align="center" width="30%" class="sflabel">';
			$out.= $file;
			$out.= '</td>';
			$out.= '<td align="center" width="9%" class="sflabel">';
            $site = esc_url(SFHOMEURL."index.php?sf_ahah=profiles&action=delavatar&amp;file=".$file);
			$out.= '<img src="'.SFADMINIMAGES.'del_cfield.png" title="'.esc_attr(__("Delete Avatar", "sforum")).'" alt="" onclick="sfjDelAvatar(\''.$site.'\');" />';
			$out.= '</td>';
			$out.= '</tr>';
			$out.= '</table>';
		}
	}
	$out.= '</div>';
	$out.= '</td></tr>';
	closedir($dlist);

	echo $out;
	return;
}

?>