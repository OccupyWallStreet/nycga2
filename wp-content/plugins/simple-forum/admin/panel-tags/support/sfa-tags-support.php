<?php
/*
Simple:Press
Admin Database Tags Support
$LastChangedDate: 2010-03-26 16:38:27 -0700 (Fri, 26 Mar 2010) $
$Rev: 3818 $
*/

define('SFMANAGETAGSNUM', 35);

function sfa_database_get_tags($order, $search, $page)
{
	global $wpdb;

	# get ordering
	if ($order == 'natural')
	{
		$orderby = " ORDER BY tag_name ASC";
	} else if ($order == 'asc') {
		$orderby = " ORDER BY tag_count ASC";
	} else {
		$orderby = " ORDER BY tag_count DESC";
	}

	# search term requested?
	$like = '';
	if (!empty($search))
	{
		$like = " WHERE tag_name LIKE '%".esc_sql(like_escape($search))."%'";
	}

	# paging
	$limit = " LIMIT ".($page * SFMANAGETAGSNUM).", ".SFMANAGETAGSNUM;

	$sql = "SELECT tag_name, tag_count FROM ".SFTAGS.$like.$orderby.$limit;
	$tags['tags'] = $wpdb->get_results($sql);
	$tags['count'] = $wpdb->get_var("SELECT count(*) FROM ".SFTAGS);

	return $tags;
}

function sfa_database_get_topics($currentpage, $tpaged, $date, $forum, $search)
{
	global $wpdb;

	if(!$tpaged) $tpaged=20;

	# how many topics per page?
	$startlimit = 0;
	if ($currentpage != 1)
	{
		$startlimit = (($currentpage-1) * $tpaged);
	}
	$limit = " LIMIT ".$startlimit.', '.$tpaged;

	# build the where clause for specific forum
	$where = '';
	if (!empty($forum) && $forum != 0)
	{
		$where.= " WHERE ".SFTOPICS.".forum_id=".$forum;
	}

	# build the where clause for specific date
	if (!empty($date) && $date != 0)
	{
		$year = substr($date, 0, 4);
		$month = substr($date, 4, 2);
		if (empty($where))
		{
			$where.= ' WHERE';
		} else {
			$where.= ' AND';
		}
		$where.= ' MONTH(topic_date)='.$month.' AND YEAR(topic_date)='.$year;
	}

	# build the where clause for topic title search term
	if (!empty($search))
	{
		if (empty($where))
		{
			$where.= ' WHERE';
		} else {
			$where.= ' AND';
		}
		$where.= " topic_name LIKE '%".esc_sql(like_escape($search))."%'";
	}

	# retrieve topic records
	$sql = "SELECT topic_id, topic_name, topic_slug, forum_slug, forum_name
			FROM ".SFTOPICS."
		 	JOIN ".SFFORUMS." ON ".SFFORUMS.".forum_id = ".SFTOPICS.".forum_id".
			$where."
			ORDER BY topic_id DESC".
			$limit;
	$records = $wpdb->get_results($sql, ARRAY_A);

	$topics = '';
	if ($records)
	{
		$topics['count'] = $wpdb->get_var("SELECT count(topic_id) FROM ".SFTOPICS.$where);
		foreach ($records as $index => $topic)
		{
			$topics['topic'][$index]['topic_id'] = $topic['topic_id'];
			$topics['topic'][$index]['topic_name'] = $topic['topic_name'];
			$topics['topic'][$index]['topic_slug'] = $topic['topic_slug'];
			$topics['topic'][$index]['forum_slug'] = $topic['forum_slug'];
			$topics['topic'][$index]['forum_name'] = $topic['forum_name'];

			# get tags for topic
			$tags = $wpdb->get_results("SELECT tag_name, ".SFTAGS.".tag_id
										FROM ".SFTAGS."
									 	JOIN ".SFTAGMETA." ON ".SFTAGMETA.".tag_id = ".SFTAGS.".tag_id
										WHERE topic_id=".$topic['topic_id'], ARRAY_A);
			$topictags = '';
			$topicids = '';
			if ($tags)
			{
				foreach ($tags as $tag)
				{
					$topictags[] = $tag['tag_name'];
					$topicids[] = $tag['tag_id'];
				}
				$topics['topic'][$index]['tags']['list'] = implode(', ', $topictags);
				$topics['topic'][$index]['tags']['ids'] = implode(',', $topicids);
			}
		}
	}

	return $topics;
}

?>