<?php
/*
Simple:Press
Template Tag(s) - Links
$LastChangedDate: 2010-11-25 12:56:15 -0700 (Thu, 25 Nov 2010) $
$Rev: 5028 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

/* 	=====================================================================================

	sf_forum_homelink()

	displays a standard link to your forums home page. Will use your theme styling.

	parameters:

			None

 	===================================================================================*/

function sf_forum_homelink()
{
	global $wpdb;

	$pid=sf_get_option('sfpage');
	$ftitle=$wpdb->get_var("SELECT post_title FROM $wpdb->posts WHERE ID=".$pid);

	echo '<a href="'.get_permalink($pid).'"/>'.$ftitle.'</a>';
	return;
}

/* 	=====================================================================================

	sf_group_link($groupid, $linktext, $listtags)

	displays a link to a specific forum group if current user has access privilege

	parameters:

		$groupid		ID of the group to display				Required
		$linktext		Text for link - leave as empty string to use group name
		$listtags		Wrap in <li> tags (li only)				true/false		true

 	===================================================================================*/

function sf_group_link($groupid, $linktext='', $listtags=true)
{
	global $wpdb, $current_user, $sfvars;

    $groupid = sf_esc_int($groupid);
	if (empty($groupid)) return '';

	sf_initialise_globals($sfvars['forumid']);
	$out = '';

	$forums = $wpdb->get_results("SELECT forum_id FROM ".SFFORUMS." WHERE group_id='".$groupid."'");
	if ($forums)
	{
		foreach ($forums as $forum)
		{
			if (sf_can_view_forum($forum->forum_id)) {
				$grouprec = sf_get_group_record($groupid);
				if (empty($linktext)) $linktext=sf_filter_title_display($grouprec->group_name);
				if ($listtags) $out.="<li>\n";
				$out.= '<a href="'.sf_build_qurl('group='.$groupid).'">'.$linktext.'</a>'."\n";
				if ($listtags) $out.="</li>\n";
				break;
			}
		}
	} else {
		$out = sprintf(__('Group %s Not Found', 'sforum'), $groupid)."\n";
	}
	echo $out;
	return;
}

/* 	=====================================================================================

	sf_forum_link($forumid, $linktext, $listtags)

	displays a link to a specific forum topic listing if current user has access privilege

	parameters:

		$forumid		ID of the forum to display				Required
		$linktext		Text for link - leave as empty string to use forum name
		$listtags		Wrap in <li> tags (li only)				true/false		true
		$display		echo or return content					true/false		true (echo)
 	===================================================================================*/

function sf_forum_link($forumid, $linktext='', $listtags=true, $display=true)
{
	global $current_user;

    $forumid = sf_esc_int($forumid);
	if (empty($forumid)) return '';

	sf_initialise_globals($forumid);
	$out='';

	if(sf_can_view_forum($forumid))
	{
		$forumrec=sf_get_forum_record($forumid);

		$forumslug = $forumrec->forum_slug;
		if(empty($linktext)) $linktext=sf_filter_title_display($forumrec->forum_name);
		if($listtags) $out.="<li>\n";
		$out.= '<a href="'.sf_build_url($forumslug, '', 0, 0).'">'.$linktext.'</a>'."\n";
		if($listtags) $out.="</li>\n";
	} else {
		$out = sprintf(__('Forum %s Not Found', 'sforum'), $forumid)."\n";
	}

	if ($display)
	{
		echo $out;
		return;
	} else {
		return $out;
	}
}

/* 	=====================================================================================

	sf_topic_link($forumid, $topicid, $linktext, $listtags)

	displays a link to a specific topic post listing if current user has access privilege

	parameters:

		$forumid		ID of the forum topic belongs to		Required
		$topicid		ID of the topic to display posts of		Required
		$linktext		Text for link - leave as empty string to use topic name
		$listtags		Wrap in <li> tags (li only)				true/false		true

 	===================================================================================*/

function sf_topic_link($forumid, $topicid, $linktext='', $listtags=true)
{
	global $current_user;

    $forumid = sf_esc_int($forumid);
    $topicid = sf_esc_int($topicid);
	if (empty($forumid) || empty($topicid)) return '';

	sf_initialise_globals($forumid);
	$out = '';

	if(sf_topic_exists($topicid))
	{
		if(sf_can_view_forum($forumid))
		{
			$forumslug = sf_get_forum_slug($forumid);
			$topicrec = sf_get_topic_record($topicid);
			$topicslug = $topicrec->topic_slug;

			if(empty($linktext)) $linktext=sf_filter_title_display($topicrec->topic_name);
			if($listtags) $out.="<li>\n";
			$out.= '<a href="'.sf_build_url($forumslug, $topicslug, 1, 0).'">'.$linktext.'</a>'."\n";
			if($listtags) $out.="</li>\n";
		}
	} else {
		$out = sprintf(__('Topic %s Not Found', 'sforum'), $topicid)."\n";
	}
	echo $out;
	return;
}

/* 	=====================================================================================

	sf_forum_dropdown($forumids)

	displays a dropdown of links to forums

	parameters:

		$forumid		ID's of forums (comma delimited in quotes)		Required

 	===================================================================================*/

function sf_forum_dropdown($forumid = 0)
{
	global $current_user, $wpdb, $sfvars;

    $forumid = sf_esc_str($forumid);

	sf_initialise_globals($sfvars['forumid']);

	$out = '';

	if ($forumid == 0)
    {
        $forums = $wpdb->get_col("SELECT forum_id FROM ".SFFORUMS);
    } else {
        $forums = explode(',', $forumid);
    }

	$out.= '<select name="forumselect" class="sfcontrol" onChange="javascript:sfjchangeForumURL(this)">'."\n";
	$out.= '<option>'.__("Select Forum", "sforum").'</option>'."\n";
	foreach ($forums as $forum)
	{
		if (sf_can_view_forum($forum))
		{
			$forumrec = sf_get_forum_record($forum);
			$forumslug = $forumrec->forum_slug;
			$out.='<option value="'.sf_build_url($forumslug, '', 0, 0).'">--'.sf_filter_title_display($forumrec->forum_name).'</option>'."\n";
		}
	}
	$out.= '</select>'."\n";

	$out.= '<script type="text/javascript">';
	$out.= 'function sfjchangeForumURL(menuObj) {';
	$out.= 'var i = menuObj.selectedIndex;';
	$out.= 'if(i > 0) {';
	$out.= 'if(menuObj.options[i].value != "#") {';
	$out.= 'window.location = menuObj.options[i].value;';
	$out.= '}}}';
	$out.= '</script>';

	echo $out;
	return;
}

/* 	=====================================================================================

	sf_add_new_topic_tag($linktext, $beforelink, $afterlink, $beforetext, $aftertext)

	Creates a link for a user to go directly to a designated forum
	and to an open Add Topic form.

	parameters:
		$forumid		ID of the Forum
		$linktext		textual content of link					text
			defaults to "Add new topic in the %FORUMNAME% forum"
			where placeholder %FORUMNAME% is eplaced by designated forum name

		$beforelink		before link text/HTML					''
		$afterlink		after link text/html					''
		$beforetext		before text text/HTML					''
		$aftertext		after text text/html					''

 	===================================================================================*/

function sf_add_new_topic_tag($forumid, $linktext="Add new topic in the %FORUMNAME% forum", $beforelink='', $afterlink='', $beforetext='', $aftertext='')
{
	global $current_user;

    $forumid = sf_esc_int($forumid);
	if($forumid == 0) return;

	sf_initialise_globals($forumid);

	if(sf_can_view_forum($forumid))
	{
		$forum=sf_get_forum_record($forumid);
		$linktext = str_replace("%FORUMNAME%", sf_filter_title_display($forum->forum_name), $linktext);
		$url = trailingslashit(sf_build_url($forum->forum_slug, '', 0, 0));
		$url = sf_get_sfqurl($url).'new=topic';
		$out = '<p>'.$beforelink.'<a href="'.$url.'">'.$beforetext.$linktext.$aftertext.'</a>'.$afterlink.'</p>';
		echo $out;
	}
	return;
}

/* 	=====================================================================================

	sf_profile_link()

	displays a standard link to the current users profile.  If its not a user (ie a guest),
    nothing will be displayed

	parameters:

		$linktext		Text for link - leave as empty string to use default

 	===================================================================================*/

function sf_profile_link($linktext = '')
{
	global $current_user;

    $out = '';
	if($current_user->ID != 0 && $current_user->ID != '')
    {
        if (empty($linktext)) $linktext = __("User Profile", "sforum");
    	$out = '<a href="'.SFURL.'profile/">'.$linktext.'</a>';
    }

    echo $out;

	return;
}

?>