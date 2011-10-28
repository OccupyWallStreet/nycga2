<?php
/*
Simple:Press
User Group Specials
$LastChangedDate: 2011-03-04 10:37:35 -0700 (Fri, 04 Mar 2011) $
$Rev: 5607 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

sfa_admin_ahah_support();

global $wpdb;

# Check Whether User Can Manage User Groups
if (!sfc_current_user_can('SPF Manage User Groups'))
{
	echo (__('Access Denied', "sforum"));
	die();
}

if (isset($_GET['ug']))
{
	$usergroup_id = sf_esc_int($_GET['ug']);
	$sql = "SELECT ".SFMEMBERSHIPS.".user_id, display_name
			FROM ".SFMEMBERSHIPS."
			JOIN ".SFMEMBERS." ON ".SFMEMBERS.".user_id = ".SFMEMBERSHIPS.".user_id
			WHERE ".SFMEMBERSHIPS.".usergroup_id=".$usergroup_id."
			ORDER BY display_name";
	$members = $wpdb->get_results($sql);
	echo sfa_display_member_roll($members);
	die();
}

function sfa_display_member_roll($members)
{
	$out = '';
	$cap = 'A';
	$first = true;
	$out.= '<fieldset class="sfsubfieldset">';
    $out.= '<legend>'.__("User Group Members", "sforum").'</legend>';
	if ($members)
	{
		for ($x=0; $x<count($members); $x++)
		{
			if(strncasecmp($members[$x]->display_name, $cap, 1) != 0)
			{
				if($first == false)
				{
					$out.= '</ul>';
				}

				$cap = substr($members[$x]->display_name, 0, 2);
				if(function_exists('mb_strwidth'))
				{
					if(mb_strwidth($cap) == 2) $cap = substr($cap, 0, 1);
				} else {
					$cap = substr($cap, 0, 1);
				}

				$out.= '<p style="clear:both;"><hr /><h4>'.strtoupper($cap).'</h4></p>';
				$out.= '<ul class="memberlist">';
				$first = false;
			}
			$out.= '<li>'.sf_filter_name_display($members[$x]->display_name).'</li>';
		}
		$out.= '</ul>';
	} else {
		$out.= __("No Members in this User Group.", "sforum");
	}
    $out.= '</fieldset>';

	return $out;
}

?>