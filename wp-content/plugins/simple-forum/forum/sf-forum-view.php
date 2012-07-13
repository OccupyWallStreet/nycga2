<?php
/*
Simple:Press
Forum View Display
$LastChangedDate: 2010-11-18 07:46:36 -0700 (Thu, 18 Nov 2010) $
$Rev: 4960 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

function sf_render_forum()
{
	global $sfvars, $current_user, $sfglobals;

	$forumid = $sfvars['forumid'];

	if (!sf_forum_exists($forumid))
	{
        status_header(404);
		$sfvars['error'] = true;
		update_sfnotice('sfmessage', '1@'.sprintf(__('Forum %s Not Found', 'sforum'), $sfvars['forumslug']));
		$out = sf_render_queued_message();
		$out.= '<div class="sfmessagestrip">'."\n";
		$out.= sprintf(__("There is no such forum named %s", "sforum"), $sfvars['forumslug'])."\n";
		$out.= '</div>'."\n";
        return $out;
	}

	# Setup stuff we will need for the query
	if (empty($sfvars['page'])) ($sfvars['page'] = 1);
	if (empty($sfvars['searchvalue']) ? $search = false : $search = true);
	$showadd = false;

	# grab the records for this page
	$forums = sf_get_combined_forums_and_topics($sfvars['forumid'], $sfvars['page']);

	# If No Access to anything then return access denied flag
	if ($forums[0]['forum_id'] == "Access Denied") return 'Access Denied';

	if ($forums)
	{
		# grab the complete first/last post stats
		$posts = array();
		foreach ($forums as $forum)
		{
			if (isset($forum['topics']))
			{
				foreach ($forum['topics'] as $topic)
				{
					$posts[$topic['topic_id']] = $topic['post_count'];
				}
			}
		}
		$stats = sf_get_combined_topic_stats($posts, $current_user->ID);
		foreach ($forums as $forum)
		{
			$out = '';
			if (($forum['children']) != '' && $search == false)
			{
				$out.= sf_render_subforums(sf_filter_title_display($forum['forum_name']), $forum['children'], $forum['group_id']);
			}

			# setup more vars settings later
			if ($forum['forum_status'] ?  $forumlock=true : $forumlock=false);
			if (($sfglobals['admin']['sftools'] && $current_user->sfforumicons) ? $admintools=true : $admintools=false);

			# == IS FORUM LOCKED OR CAN WE ADD
			if ($current_user->sfaddnew) $showadd = true;
			if ($forumlock) $showadd = false;
			if ($current_user->forumadmin) $showadd = true;
			if ($sfglobals['lockdown']) $showadd = false;

			# Setup more stuff we will need
			$coldisplaytext = sf_render_search_heading($search);


			# make sure we have some page links (top/bottom/both)
			$pageoverride = false;
			if($search == true && $sfglobals['display']['pagelinks']['ptop'] == false && $sfglobals['display']['pagelinks']['pbottom'] == false)
			{
				$pageoverride = true;
			}

			# Setup page links for this topic
			$thispagelinks = sf_compile_paged_topics($sfvars['forumslug'], $sfvars['forumid'], $sfvars['page'], $search, $sfvars['searchpage'], $forum['topic_count']);

			# Setup forum icon
			if (!empty($forum['forum_icon']))
			{
				$icon = SFCUSTOMURL.$forum['forum_icon'];
			} else {
				$icon = SFRESOURCES.'forum.png';
			}

			# Start display
			# Display forum header
			$out.= '<div id="forum-'.$forum['forum_id'].'" class="sfblock">'."\n";
			$out.= sf_render_main_header_table('forum', '', sf_filter_title_display($forum['forum_name']), sf_filter_text_display($forum['forum_desc']), $icon, $forumlock, $showadd);

			# Display top page link navigation
			$out.= sf_render_topic_pagelinks($thispagelinks, false, $showadd, $forumlock, true, $pageoverride);

			if (isset($forum['topics']))
			{
				# Special Message
				if (!empty($forum['forum_message']))
				{
					$out.= sf_render_special_forum_message(sf_filter_text_display($forum['forum_message']));
				}

				$out.= '<table class="sfforumtable">'."\n";

				# Display topic column headers
				$out.= sf_render_forum_column_header_row($coldisplaytext);

				$alt = '';
				$value['forumlock']=$forumlock;
				$value['admintools']=$admintools;

				foreach ($forum['topics'] as $topic)
				{
					# Display current topic row
					$out.= sf_render_topic_entry_row($forum, $topic, $stats[$topic['topic_id']], $value, $alt);

					if ($alt == '') $alt = 'sfalt'; else $alt = '';
				}
				$out.= '</table>'."\n";

				# Display bottom page link navigation
				$out.= sf_render_topic_pagelinks($thispagelinks, true, $showadd, $forumlock, true, $pageoverride);

				# Store the topic page so that we can get back to it later
				sf_push_topic_page($sfvars['forumid'], $sfvars['page']);

			} else {
				if ($search)
				{
					$out.= '<div class="sfmessagestrip">'.__("The Search found No Results", "sforum").'</div>'."\n";
				} else {
    				if (!empty($forum['forum_message']))
    				{
    					$out.= sf_render_special_forum_message(sf_filter_text_display($forum['forum_message']));
    				}

					$out.= '<div class="sfmessagestrip">'.__("There are No Topics defined in this Forum", "sforum").'</div>'."\n";

					# Display bottom page link navigation
					$out.= sf_render_topic_pagelinks($thispagelinks, true, $showadd, $forumlock, true, $pageoverride);
				}
			}
		}
	}
	$out.= '</div>'."\n";

	# Display new (hidden) topic form
	if ($showadd)
	{
		$out.= '<a id="dataform"></a>'."\n";
		$out.= sf_add_topic($sfvars['forumid'], $forum['forum_name'], $forum['topic_status_set'], $forum['use_tags'], $current_user->ID);

		if (isset($_GET['new']) && $_GET['new'] == 'topic')
		{
			# inline js to open topic form
			add_action( 'wp_footer', 'sfjs_open_topic_form' );
		}
	}
	return $out;
}

# inline opens add topic wondow of called frm post view
function sfjs_open_topic_form() {
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	sfjtoggleLayer("sfpostform");
});
</script>
<?php
}

?>