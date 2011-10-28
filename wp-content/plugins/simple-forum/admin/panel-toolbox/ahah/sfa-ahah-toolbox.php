<?php
/*
Simple:Press Admin
Ahah call for Amdin tools (from options)
$LastChangedDate: 2010-04-25 01:46:39 -0700 (Sun, 25 Apr 2010) $
$Rev: 3960 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

sfa_admin_ahah_support();
sf_build_memberdata_cache();

global $sfglobals;

# ----------------------------------
# Check Whether User Can Manage Toolbox
if (sfc_current_user_can('SPF Manage Toolbox')==false && sf_get_option('sfcheck')==false)
{
	echo (__('Access Denied', "sforum"));
	die();
}

if (isset($_GET['item']))
{
	$item = $_GET['item'];
	if($item == 'upcheck') sfa_sf_check_for_updates(sf_get_option('sfversion'), sf_get_option('sfbuild'), false);
	if($item == 'inlinecheck') sfa_sf_check_for_updates(sf_get_option('sfversion'), sf_get_option('sfbuild'), true);
}

die();

function sfa_sf_check_for_updates($version, $build, $inline=false)
{
	$checkfile = SFVERCHECK;

	$vcheck = wp_remote_fopen($checkfile);
	if($vcheck)
	{
		$status = explode('@', $vcheck);
		if(isset($status[1]))
		{
			$theVersion = $status[1];
			$theBuild   = $status[3];
			$theMessage = $status[5];

			if((version_compare(floatval($theVersion), floatval($version), '>') == 1) || (version_compare(intval($theBuild), intval($build), '>') == 1))
			{
				if($inline)
				{
					$msg = __("Latest version available:", "sforum").' <strong>'.$theVersion.'</strong> '.__("Build:", "sforum").' <strong>'.$theBuild.'</strong> - '.$theMessage;
				} else {
					$msg = __("Latest version available:", "sforum").' <br /><strong>'.$theVersion.'</strong><br />';
					$msg.= __("Build:", "sforum").' <strong>'.$theBuild.'</strong><br />';
					$msg.= $theMessage;
				}
				if($inline)
				{
					echo '<span class="sfalignleft" style="border:1px solid silver; background: #FFFFCC; padding: 4px 4px 0px 4px;margin: 5px 12px 0px 0px;">'.$msg.'</span>';
					echo '<div class="clearboth"></div>';
				} else {
					echo $msg;
				}
			} else {
				if($inline) return;
				$msg = __("Your system is up to date", "sforum");
				echo $msg;
			}
		}
	} else {
		if(!$inline)
		{
			echo __("Unable to check - your host has disabled reading remote files", "sforum");
		}
	}

	return;
}

die();

?>