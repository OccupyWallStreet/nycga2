<?php
/*
Simple:Press
Template Tag(s) - Tags
$LastChangedDate: 2010-05-15 14:31:34 -0700 (Sat, 15 May 2010) $
$Rev: 4025 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

/* 	=====================================================================================

	sf_most_used_tags($limit)

	displays the most used tags

	parameters:

		$limit			How many tags to show in the list		number			10 (default)
		echo			write display (returns it if false)		true/false		true

 	===================================================================================*/

function sf_most_used_tags($limit=10, $echo=true)
{
	global $wpdb, $sfvars;

	sf_initialise_globals($sfvars['forumid']);

	$out = '';

	$sftags = $wpdb->get_results(
			"SELECT *
			FROM ".SFTAGS."
			ORDER BY tag_count DESC
			LIMIT ".$limit);

	if ($sftags)
	{
		foreach ($sftags as $sftag)
		{
			$out.= "<li class='sftaglist'>\n";
			$out.= '<a href="'.esc_url(SFURL.'?forum=all&amp;value='.$sftag->tag_slug.'&amp;type=1&amp;include=4&amp;search=1').'">'.$sftag->tag_name.'</a> ('.$sftag->tag_count.')'."\n";
    		$out.= "</li>\n";
		}
	} else {
		$out.= "<li class='sftaglist'>\n";
		$out.= __("No Tags to Display", "sforum")."\n";
		$out.= "</li>\n";
	}

	if ($echo)
	{
		echo $out;
		return;
	} else {
		return $out;
	}
}

/* 	=====================================================================================

	sf_tag_cloud($limit=25, $sort='random', $size=true, $smallest=8, $largest=22, $unit='pt', $color=true, $mincolor='#000000', $maxcolor='#cccccc')

	displays tag cloud

	parameters:

		$limit			How many tags to show in the list			number			25 (default)
		$sort			How to sort the tags in the cloud			text			desc, asc or random (default)
		$size			change size of tags based on count			boolean			true (default) or false
		$smallest		size of least used tag ($size must be 1)	number			8 (default)
		$largest		size of most used tag ($size must be 1)		number			22 (default)
		$unit			Units for the smallest/larget sizes			text			pt (default) or px
		$color			change color of tags based on count			boolean			true (default) or false
		$mincolor		color of least used tag ($color must be 1)	text			#000000 (default)
		$maxcolor		color of least used tag ($color must be 1)	text			#cccccc (default)
		echo			write display (returns it if false)		    true/false		true
		sep			    separator between tags in the cloud		    text       		space

 	===================================================================================*/

function sf_tag_cloud($limit=25, $sort='random', $size=true, $smallest=8, $largest=22, $unit='pt', $color=true, $mincolor='#000000', $maxcolor='#cccccc', $echo=true, $sep=' ')
{
	global $wpdb, $sfvars;

	sf_initialise_globals($sfvars['forumid']);

	$out = '';

	$format = '<a href="%tag_link%" id="sftaglink-%tag_id%" class="sftagcloud" title="%tag_count% '.__("topics", "sforum").'" style="%tag_size% %tag_color%">%tag_name%</a>';

	# remove size and color markers if not used
	if (!$size) $format = str_replace('%tag_size%', '', $format);
	if (!$color) $format = str_replace('%tag_color%', '', $format);

	if ($sort == 'random') {
		$sortby = 'RAND()';
	} else {
		$sortby = 'tag_count '.$sort;
	}
	$sftags = $wpdb->get_results(
			"SELECT *
			FROM ".SFTAGS."
			ORDER BY ".$sortby." LIMIT ".$limit);

	# find min and max counts
	$minval = 999999;
	$maxval = -999999;
	foreach ($sftags as $sftag)
	{
		if ($sftag->tag_count < $minval) $minval = $sftag->tag_count;
		if ($sftag->tag_count > $maxval) $maxval = $sftag->tag_count;
	}

	# make sure smallest is not greater than largest
	if ($smallest > $largest) $smallest = $largest;

	# scaling
	$scale_min = 1;
	$scale_max = 10;
	$minout = max($scale_min, 0);
	$maxout = max($scale_max, $minout);
	$scale = ($maxval > $minval) ? (($maxout - $minout) / ($maxval - $minval)) : 0;

	if ($sftags)
	{
		foreach ($sftags as $sftag)
		{
			$tag_scale = (int) (($sftag->tag_count - $minval) * $scale + $minout);
			$tagout = $format;
			$tagout = str_replace('%tag_name%', esc_html($sftag->tag_name), $tagout);
			$tagout = str_replace('%tag_link%', esc_url(SFURL.'?forum=all&amp;value='.urlencode($sftag->tag_name).'&amp;type=1&amp;include=4&amp;search=1'), $tagout);
			$tagout = str_replace('%tag_id%', $sftag->tag_id, $tagout);
			$tagout = str_replace('%tag_count%', (int) $sftag->tag_count, $tagout);
			$tagout = str_replace('%tag_size%', 'font-size:'.round(($tag_scale - $scale_min)*($largest-$smallest)/($scale_max - $scale_min) + $smallest, 2).$unit.';', $tagout);
			$tagout = str_replace('%tag_color%', 'color:'.sf_get_color_scaled(round(($tag_scale - $scale_min)*(100)/($scale_max - $scale_min), 2),$mincolor,$maxcolor).';', $tagout);
			$out.= $tagout.$sep;
		}
	} else {
		$out.= __("No Tags to Display", "sforum")."\n";
	}

	if ($echo)
	{
		echo $out;
		return;
	} else {
		return $out;
	}
}

/* 	=====================================================================================

	sf_related_topics($limit=10, $topic_id, $listtags=true, $forum=true, $echo=false)

	displays related topics

	parameters:

		$limit			How many tags to show in the list				number			10 (default)
		$topic_id		the topic id for which to find related topics	number			topic id
		$listtags		Wrap in <li> tags (li only)						true/false		true
		$forum			display forum name of related topics			true/false		true
		echo			write display (returns it if false)				true/false		true

 	===================================================================================*/

function sf_related_topics($limit=10, $topic_id, $listtags=true, $forum=true, $echo=true)
{
	global $wpdb, $sfvars;

    $topic_id = sf_esc_int($topic_id);
    if (empty($topic_id)) return;

	sf_initialise_globals($sfvars['forumid']);

	$out = '';

	$tags = $wpdb->get_results("SELECT tag_slug
								FROM ".SFTAGS."
							 	JOIN ".SFTAGMETA." ON ".SFTAGMETA.".tag_id = ".SFTAGS.".tag_id
								WHERE topic_id=".$topic_id);
	if ($tags)
	{
		# build list of tags for the topic id
		$taglist = '';
		foreach ($tags as $tag)
		{
			if ($taglist == '')
			{
				$taglist = "('".$tag->tag_slug."'";
			} else {
				$taglist.= ",'".$tag->tag_slug."'";
			}
		}
		$taglist.= ")";

		# now grab the results
		$LIMIT = ' LIMIT '.$limit;
		$ORDER = ' ORDER BY topic_id DESC';
		$WHERE = SFTOPICS.".topic_id IN (SELECT topic_id FROM ".SFTAGMETA." JOIN ".SFTAGS." ON ".SFTAGMETA.".tag_id = ".SFTAGS.".tag_id
			WHERE tag_slug IN ".$taglist.")";
		$topics = $wpdb->get_results(
				"SELECT SQL_CALC_FOUND_ROWS DISTINCT
				 ".SFTOPICS.".topic_id, topic_name, topic_slug, ".SFTOPICS.".forum_id, forum_name, forum_slug
				 FROM ".SFTOPICS."
				 JOIN ".SFFORUMS." ON ".SFTOPICS.".forum_id = ".SFFORUMS.".forum_id
				 JOIN ".SFPOSTS." ON ".SFTOPICS.".topic_id = ".SFPOSTS.".topic_id
				 WHERE ".$WHERE.$ORDER.$LIMIT.";", ARRAY_A);

		# now output the related topics
		if ($topics)
		{
			foreach ($topics as $topic)
			{
				if (sf_can_view_forum($topic['forum_id']))
				{
					$p = false;

					# Start contruction
					if ($listtags) $out.= "<li class='sftagli'>\n";
					$out.= sf_get_topic_url($topic['forum_slug'], $topic['topic_slug'], $topic['topic_name']);

					if ($forum)
					{
						$out.= "<p class='sftagp'>".__("posted in forum", "sforum").' '.sf_filter_title_display($topic['forum_name'])."&nbsp;"."\n";
						$p = true;
					}

					if ($p) $out.= "</p>\n";
					if ($listtags) $out.= "</li>\n";
				}
			}
		} else {
			$out.= "<li class='sftagli'>\n";
			$out.= __("No Related Topics", "sforum")."\n";
			$out.= "</li>\n";
		}
	} else {
		$out.= "<li class='sftagli'>\n";
		$out.= __("No Related Topics", "sforum")."\n";
		$out.= "</li>\n";
	}

	if ($echo)
	{
		echo $out;
		return;
	} else {
		return $out;
	}
}

?>