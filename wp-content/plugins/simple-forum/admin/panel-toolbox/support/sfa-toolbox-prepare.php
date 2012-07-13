<?php
/*
Simple:Press
Admin Toolbox Support Functions
$LastChangedDate: 2010-03-26 16:38:27 -0700 (Fri, 26 Mar 2010) $
$Rev: 3818 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_get_toolbox_data()
{
	$sfoptions = array();

	$sfoptions['sfpermalink']=sf_get_option('sfpermalink');
	$sfoptions['sfcheck']=sf_get_option('sfcheck');
	$sfoptions['sfcbexclusions']=sf_get_option('sfcbexclusions');
	$sfoptions['sfforceupgrade']=sf_get_option('sfforceupgrade');

	if(sf_get_option('sfbuild') == SFBUILD) $sfoptions['sfforceupgrade']=0;

	return $sfoptions;
}

function sfa_get_log_data()
{
	global $wpdb;

	$sflog = array();

	$sql = "
		SELECT install_date, release_type, version, build, display_name
		FROM ".SFLOG."
		JOIN ".SFMEMBERS." ON ".SFLOG.".user_id=".SFMEMBERS.".user_id
		ORDER BY id ASC;";

	$sflog=$wpdb->get_results($sql, ARRAY_A);

	return $sflog;
}

function sfa_get_uninstall_data()
{
	$sfoptions = array();

	$sfoptions['sfuninstall'] = sf_get_option('sfuninstall');

	return $sfoptions;
}

# function to create an sql query for a list of topics based on the filter criteria
# these topics then get displayed in another form for the admin to mark the topics for pruning
function sfa_prepare_filter_topics()  {

    check_admin_referer('forum-adminform_filtertopics', 'forum-adminform_filtertopics');

    $topicdata = array();

	$gcount = $_POST['gcount'];
	$fcount = $_POST['fcount'];

	$first = true;
	for ($x=0; $x<$gcount; $x++)
	{
		for( $y=0; $y<$fcount[$x]; $y++)
		{
			if (isset($_POST['group'.$x.'forum'.$y]))
			{
				if ($first)
				{
					$forum_ids = ' AND (forum_id='.sf_esc_int($_POST['group'.$x.'forum'.$y]);
					$first = false;
				} else {
					$forum_ids .= ' OR forum_id='.sf_esc_int($_POST['group'.$x.'forum'.$y]);
				}
			}
		}
	}

	$topicdata['message'] = '';
	if ($first)
	{
        $topicdata['message'] = __("Error - No Forum(s) Specified for Filtering!", "sforum");
	} else {
		$forum_ids .= ')';
	}
	$topicdata['id'] = $forum_ids;

	$xdate = getdate(strtotime(sf_esc_str($_POST['date'])));
	$filterdate = $xdate['year'].'-'.$xdate['mon'].'-'.$xdate['mday'].' 23:59:59';
	$topicdata['date'] = $filterdate;

	return $topicdata;
}

?>