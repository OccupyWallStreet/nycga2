<?php
/*
Simple:Press
Forum Search url creation
$LastChangedDate: 2010-07-06 09:39:48 -0700 (Tue, 06 Jul 2010) $
$Rev: 4236 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

$url = $_SERVER['HTTP_REFERER'];

# Type =
#	1 - Standard - match any word
#	2 - Standard - match all words
#	3 - Standard - match phrase
#	6 - Topic Status
#	8 - Member 'Posted In'
#	9 - Member 'Started'
#
#	NOTE type 6 or above reserved
#	for integer search values only
#
# Include =
#	1 - Posts and Topic Titles
#	2 - Posts Only
#	3 - Topic Titles only
#	4 - Tags Only

$param=array();

if (isset($_POST['statussearch']))
{
	# topic status search
	$param['forum'] = sf_esc_str($_POST['forumslug']);
	$param['value'] = sf_esc_int($_POST['statvalue']);
	$param['type'] = 6;
	$param['search'] = 1;
	$url = add_query_arg($param, SFURL);

} else if (isset($_POST['membersearch'])) {
	# member 'posted in' search
	if ($_POST['searchoption'] == 'All Forums')
	{
		$param['forum'] = 'all';
	} else {
		$param['forum'] = sf_esc_str($_POST['forumslug']);
	}
	$id = sf_esc_int($_POST['userid']);
	$param['value'] = $id;
	$param['type'] = 8;
	$param['search'] = 1;
	$url = add_query_arg($param, SFURL);

} else if (isset($_POST['memberstarted'])) {
	# member 'started' search
	if ($_POST['searchoption'] == 'All Forums')
	{
		$param['forum'] = 'all';
	} else {
		$param['forum'] = sf_esc_str($_POST['forumslug']);
	}
	$id = sf_esc_int($_POST['userid']);
	$param['value'] = $id;
	$param['type'] = 9;
	$param['search'] = 1;
	$url = add_query_arg($param, SFURL);

} else if (isset($_POST['searchvalue'])) {
	# standard search
	$searchvalue = trim(stripslashes($_POST['searchvalue']));
	$searchvalue = trim($searchvalue, '"');
	$searchvalue = trim($searchvalue, "'");
	$param = array();
	if ($_POST['searchoption'] == 'All Forums')
	{
		$param['forum'] = 'all';
	} else {
		$param['forum'] = sf_esc_str($_POST['forumslug']);
	}
	$param['value'] = urlencode($searchvalue);
	$param['type'] = sf_esc_int($_POST['searchtype']);
	$param['include'] = sf_esc_int($_POST['encompass']);
	$param['search'] = 1;
	$url = add_query_arg($param, SFURL);
}

wp_redirect($url);
die();

?>