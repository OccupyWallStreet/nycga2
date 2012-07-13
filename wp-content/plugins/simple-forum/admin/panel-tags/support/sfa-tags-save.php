<?php
/*
Simple:Press
Admin Tags Support Functions
$LastChangedDate: 2010-03-26 16:38:27 -0700 (Fri, 26 Mar 2010) $
$Rev: 3818 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_save_tags_edit_tags()
{
	global $wpdb, $sfglobals;

    check_admin_referer('forum-adminform_sfedittags', 'forum-adminform_sfedittags');

    $topic_id_list = $_POST['topic_id'];
    $tag_id_list = $_POST['tag_id'];
    $tag_list = $_POST['tags'];

	# take the easy way out and remove all tags and then add back in the new list
	for ($x=0; $x<count($topic_id_list); $x++)
	{
		if (!empty($tag_id_list[$x]))  # if no tags originally, dont delete anything
		{
			# grab all the tag rows and decrement the tag count
			$sql = "SELECT tag_id, tag_count FROM ".SFTAGS." WHERE tag_id IN (".sf_esc_str($tag_id_list[$x]).")";
			$tags = $wpdb->get_results($sql);
			foreach ($tags as $tag)
			{
				# decrement tag count and delete if it gets to zero or update the new count
				$tag->tag_count--;
				if ($tag->tag_count == 0)
				{

					$wpdb->query("DELETE FROM ".SFTAGS." WHERE tag_id=".$tag->tag_id); # count is zero so delete
				} else {
					$wpdb->query("UPDATE ".SFTAGS." SET tag_count=".$tag->tag_count." WHERE tag_id=".$tag->tag_id); # update count
				}
			}

			# remove all the tag meta entries for the topic
			$wpdb->query("DELETE FROM ".SFTAGMETA." WHERE topic_id=".sf_esc_int($topic_id_list[$x]));
		}

		# now add the current tags back in for the topic
		if (!empty($tag_list[$x]))
		{
		    $tags = trim(sf_esc_str($tag_list[$x]));
		    $tags = trim($tags, ',');  # no extra commas allowed
			$tags = explode(',', $tags);
			$tags = array_unique($tags);  # remove any duplicates
			$tags = array_values($tags);  # put back in order
			if ($sfglobals['display']['topics']['maxtags'] > 0 && count($tags) > $sfglobals['display']['topics']['maxtags'])
			{
				$tags = array_slice($tags, 0, $sfglobals['display']['topics']['maxtags']);  # limit to maxt tags opton
			}
			$mess = sfc_add_tags($topic_id_list[$x], $tags);
			if ($mess != '')
			{
				return $mess;
			}
		}
	}

	$mess = __("Tags Updated", "sforum");

	return $mess;
}

function sfa_save_tags_rename_tags()
{
	global $wpdb;

    check_admin_referer('forum-adminform_sfrenametags', 'forum-adminform_sfrenametags');

    $oldtags = $_POST['renametag_old'];
    $newtags = $_POST['renametag_new'];

    if (empty($oldtags) || empty($newtags))
    {
    	$mess = __("Renaming Tags requires old Tag(s) and new Tags(s) entries - No Tags renamed!", "sforum");
		return $mess;
    }

    # prep the new tags
    $tags = trim(sf_esc_str($newtags));
    $tags = trim($tags, ',');  # no extra commas allowed
	$tags = explode(',', $tags);
	$tags = array_unique($tags);  # remove any duplicates
	$tags = array_values($tags);  # put back in order

    # prep the old tags
    $otags = trim(sf_esc_str($oldtags));
    $otags = trim($otags, ',');  # no extra commas allowed
	$otags = explode(',', $otags);
	$otags = array_unique($otags);  # remove any duplicates
	$otags = array_values($otags);  # put back in order
    foreach ($otags as $tag)
    {
		$tagslug = sf_create_slug($tag, 'tag', false);
		$tagid = $wpdb->get_var("SELECT tag_id FROM ".SFTAGS." WHERE tag_slug='".$tagslug."'");
		if ($tagid)
		{
			# delete tag itself
			$wpdb->query("DELETE FROM ".SFTAGS." WHERE tag_id=".$tagid);

			# find the topics that use this tag
			$topics = $wpdb->get_results("SELECT topic_id FROM ".SFTAGMETA." WHERE tag_id=".$tagid);
			if ($topics)
			{
				foreach ($topics as $topic)
				{
					# delete tag metas for this topic
					$wpdb->query("DELETE FROM ".SFTAGMETA." WHERE topic_id=".$topic->topic_id." AND tag_id=".$tagid);

					# add in the new tags
					sfc_add_tags($topic->topic_id, $tags);
				}
			}
		}
    }

	$mess = __("Tags Renamed or Merged", "sforum");
	return $mess;
}

function sfa_save_tags_delete_tags()
{
	global $wpdb;

    check_admin_referer('forum-adminform_sfdeletetags', 'forum-adminform_sfdeletetags');

    $deltags = $_POST['deletetag_name'];

    if (empty($deltags))
    {
    	$mess = __("Deleting Tags requires Tags(s) entry - No Tags deleted!", "sforum");
		return $mess;
    }

	$deleted = 0; # indicate nothing deleted
    # loop through tags and delete the tag and the tag metas
    $tags = trim(sf_esc_str($deltags));
    $tags = trim($tags, ',');  # no extra commas allowed
	$tags = explode(',', $tags);
	$tags = array_unique($tags);  # remove any duplicates
	$tags = array_values($tags);  # put back in order
    foreach ($tags as $tag)
    {
		$tagslug = sf_create_slug($tag, 'tag');
		$tagid = $wpdb->get_var("SELECT tag_id FROM ".SFTAGS." WHERE tag_slug='".$tagslug."'");
		if ($tagid)
		{
			# delete tag metas with this tag id
			$wpdb->query("DELETE FROM ".SFTAGMETA." WHERE tag_id=".$tagid);

			# delete tag itself
			$wpdb->query("DELETE FROM ".SFTAGS." WHERE tag_id=".$tagid);

			# indicate at least some tags deleted
			if ($deleted == 0) $deleted = 1;
		} else {
			if ($deleted == 1) $deleted = 2; # indicate some deleted but some not found
		}
    }

    # output deletion results message
    switch ($deleted)
    {
    	case 0:
			$mess = __("No Tags Matched For Deletion!", "sforum");
			break;
    	case 1:
			$mess = __("Tags Successfully Deleted!", "sforum");
			break;
    	case 2:
			$mess = __("Some Tags Deleted, but Others Not Found!", "sforum");
			break;
    }

	return $mess;
}

function sfa_save_tags_add_tags()
{
	global $wpdb, $sfglobals;

    check_admin_referer('forum-adminform_sfaddtags', 'forum-adminform_sfaddtags');

    $matchtags = $_POST['addtag_match'];
    $addtags = $_POST['addtag_new'];

    if (empty($addtags))
    {
    	$mess = __("Adding Tags requires new Tags(s) entry - No Tags added!", "sforum");
		return $mess;
    }

	# prep the new tags
    $tags = trim(sf_esc_str($addtags));
    $tags = trim($tags, ',');  # no extra commas allowed
	$tags = explode(',', $tags);
	$tags = array_unique($tags);  # remove any duplicates
	$tags = array_values($tags);  # put back in order
	if ($sfglobals['display']['topics']['maxtags'] > 0 && count($tags) > $sfglobals['display']['topics']['maxtags'])
	{
		$tags = array_slice($tags, 0, $sfglobals['display']['topics']['maxtags']);  # limit to max tags opton
	}

    # if not match tags, add the new tags to all topics
    if (empty($matchtags))
    {
    	# get topics
		$topics = $wpdb->get_results("SELECT topic_id FROM ".SFTOPICS);
		if ($topics)
		{
			foreach ($topics as $topic)
			{
				# now add the tags
				sfc_add_tags($topic->topic_id, $tags);
			}

			$mess = __("Tags Added to All Topics!", "sforum");
			return $mess;
		} else {
	    	$mess = __("No Topics to Add Tags to - No Tags added!", "sforum");
			return $mess;
		}
    }

	# alrighty, so need to match tags before we add the new ones
	# prep the match tags
    $mtags = trim(sf_esc_str($matchtags));
    $mtags = trim($mtags, ',');  # no extra commas allowed
	$mtags = explode(',', $mtags);
	$mtags = array_unique($mtags);  # remove any duplicates
	$mtags = array_values($mtags);  # put back in order
	if ($mtags)
	{
		$mtag_list = '(';
		$first = true;
		# Now put the tags back together in list
		foreach ($mtags as $mtag)
		{
			# convert to a tag slug and build slug list
			$mtagslug = sf_create_slug($mtag, 'tag');
			if ($first)
			{
				$mtag_list.= "'".$mtagslug."'";
				$first = false;
			} else {
				$mtag_list.= ",'".$mtagslug."'";
			}
		}
		$mtag_list.= ")";

		# grab any topics that have a matching slug
		$tagids = $wpdb->get_results("SELECT tag_id FROM ".SFTAGS." WHERE tag_slug IN".$mtag_list);
		if ($tagids)
		{
			# now find the topics with these matched tags and add the new tags
			foreach ($tagids as $tagid)
			{
				$topics = $wpdb->get_results("SELECT topic_id FROM ".SFTAGMETA." WHERE tag_id = ".$tagid->tag_id);
				if ($topics)
				{
					foreach ($topics as $topic)
					{
						# now add the tags
						sfc_add_tags($topic->topic_id, $tags);
					}
				}
			}

			$mess = __("Tags Added to Topics with Matched Tags!", "sforum");
			return $mess;
		} else {
			$mess = __("No Tags Matched!", "sforum");
			return $mess;
		}
	} else {
		$mess = __("Invalid Matching Tags Entry - No Tags Added!", "sforum");
		return $mess;
	}

	$mess = __("Oh Oh - This Shouldn't Happen!", "sforum");
	return;
}

function sfa_save_tags_cleanup_tags()
{
	global $wpdb;

    check_admin_referer('forum-adminform_sfcleanup', 'forum-adminform_sfcleanup');

	# remove orphaned tags
	$tagids = $wpdb->get_results("SELECT tag_id FROM ".SFTAGS);
	if ($tagids)
	{
		foreach ($tagids as $tagid)
		{
			$meta = $wpdb->get_results("SELECT meta_id FROM ".SFTAGMETA." WHERE tag_id = ".$tagid->tag_id);
			if (!$meta)
			{
				# no metas so its orphaned and can be deleted
				$wpdb->query("DELETE FROM ".SFTAGS." WHERE tag_id=".$tagid->tag_id);

			}
		}
	}

	# remove orphaned tag meta
	$tagids = $wpdb->get_results("SELECT tag_id FROM ".SFTAGMETA);
	if ($tagids)
	{
		foreach ($tagids as $tagid)
		{
			$tags = $wpdb->get_results("SELECT tag_id FROM ".SFTAGS." WHERE tag_id = ".$tagid->tag_id);
			if (!$tags)
			{
				# no tags so its orphaned and can be deleted
				$wpdb->query("DELETE FROM ".SFTAGMETA." WHERE tag_id=".$tagid->tag_id);

			}
		}
	}

	# clean up the tag counts
	$tagids = $wpdb->get_results("SELECT tag_id FROM ".SFTAGS);
	if ($tagids)
	{
		foreach ($tagids as $tagid)
		{
			# get the number of topics using this tag
			$count = $wpdb->get_var("SELECT COUNT(tag_id) FROM ".SFTAGMETA." WHERE tag_id = ".$tagid->tag_id);

			# set the count to number of topics using
			$wpdb->query("UPDATE ".SFTAGS." SET tag_count=".$count." WHERE tag_id = ".$tagid->tag_id);
		}
	}

	$mess = __("Tags Database Cleaned Up!", "sforum");
	return $mess;
}

?>