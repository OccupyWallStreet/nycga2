<?php
/*
Simple:Press
Ahah - Post Rating
$LastChangedDate: 2010-05-13 19:49:45 -0700 (Thu, 13 May 2010) $
$Rev: 4017 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

global $current_user, $sfglobals;

$fid = sf_esc_int($_GET['fid']);
$pid = sf_esc_int($_GET['pid']);
if (empty($fid) || empty($pid)) die();

sf_initialise_globals($fid);

if (!$current_user->sfrateposts)
{
	echo (__('Access Denied', "sforum"));
	die();
}

$postratings = sf_get_option('sfpostratings');

$rating_data = sf_get_postratings($pid);
if ($rating_data == '')
{
	$rating_sum = 0;
	$votes = 0;
	$ips = array();
	$members = array();
} else {
	$rating_sum = $rating_data->ratings_sum;
	$ips = unserialize($rating_data->ips);
	$members = unserialize($rating_data->members);
	$votes = $rating_data->vote_count;
}

if ($postratings['sfratingsstyle'] == 1) # thumb up/down
{
	$rate = $_GET['rate'];
	if ($rate == 'up') $rating_sum++; else $rating_sum--;
    if ($rating_sum >0) $rating_sum = '+'.$rating_sum;
	$votes++;

	$out.= '<div class="sfpostratingscontainer sfthumbs">';
	$out.= '<div class="sfposticon sfpostrating">'.$rating_sum.'</div>';
	$text = __("Post Rating: ", "sforum").$rating_sum;
	$out.= '<div class="sfposticon sfpostratedown"><img src="'.SFRESOURCES.'ratings/ratedowngrey.png" alt="" title="'.esc_attr($text).'" /></div>';
	$out.= '<div class="sfposticon sfpostrateup"><img src="'.SFRESOURCES.'ratings/rateupgrey.png" alt="" title="'.esc_attr($text).'" /></div>';
	$out.= '</div>';
} else {
	$star_rating = sf_esc_int($_GET['rate']);
	$rating_sum = $rating_sum + $star_rating;
	$votes++;
	$newrating = round($rating_sum / $votes, 1);
	$intrating = floor($newrating);
	$out.= '<div class="sfpostratingscontainer sfstars">';
	$out.= '<div class="sfposticon sfpostrating">'.$newrating.'</div>';
	$out.= '<div class="sfposticon sfpoststars">';
	$text = __("Post Rating: ", "sforum").$newrating;
    for ($x = 0; $x < $intrating; $x++)
	{
		$out.= '<img src="'.SFRESOURCES.'ratings/ratestaron.png'.'" alt="" title="'.esc_attr($text).'" />';
	}
    for ($x = 0; $x < (5 - $intrating); $x++)
	{
		$out.= '<img src="'.SFRESOURCES.'ratings/ratestaroff.png'.'" alt="" title="'.esc_attr($text).'" />';
	}
	$out.= '</div>';
	$out.= '</div>';
}

if ($current_user->member)
{
	$members[] = $current_user->ID;
} else {
	$ips[] = getenv("REMOTE_ADDR");
}

if ($members) $members = serialize($members); else $members = null;
if ($ips) $ips = serialize($ips); else $ips = null;

if ($votes == 1)
{
	sf_add_postratings($pid, $votes, $rating_sum, $ips, $members);
} else {
	sf_update_postratings($pid, $votes, $rating_sum, $ips, $members);
}

#record the vote in users members profile
sf_add_postrating_vote($pid);

echo $out;

die();

?>