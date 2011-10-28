<?php
/*
Simple:Press
Related Tags
$LastChangedDate: 2009-04-22 02:36:11 -0700 (Wed, 22 Apr 2009) $
$Rev: 1757 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

sf_forum_ahah_support();

# --------------------------------------------

if (isset($_GET['topicid'])) $topic_id = sf_esc_int($_GET['topicid']);
if (empty($topic_id)) die();

include_once (SF_PLUGIN_DIR.'/forum/sf-forum-components.php');

global $current_user, $sfglobals, $sfvars, $wpdb;

sf_initialise_globals();

$out = '';

$tags = $wpdb->get_results("SELECT tag_slug
							FROM ".SFTAGS."
						 	JOIN ".SFTAGMETA." ON ".SFTAGMETA.".tag_id = ".SFTAGS.".tag_id
							WHERE topic_id=".$topic_id);
if ($tags)
{
	$out.= '<div id="sforum">'."\n";

	# Display Header
	$out.= '<div class="sfblock">'."\n";
	$out.= sf_render_main_header_table('forum', 0, sf_filter_title_display(__('Related Topics (Top Ten)', 'sforum')), sf_filter_text_display(__('List of related topics based on tags.', 'sforum')), SFRESOURCES.'small-tag.png');

	$topics = sf_get_related_topics($tags);
	if ($topics)
	{
		# grab the complete first/last post stats
		$posts = array();
		foreach($topics as $topic)
		{
			$posts[$topic['topic_id']]=$topic['post_count'];
		}
		$stats = sf_get_combined_topic_stats($posts, $current_user->ID);

		# Start table display
		$out.= '<table class="sfforumtable">'."\n";

		# Column headers
		$out.= '<tr><th colspan="2"></th>'."\n";
		$out.= '<th>'.__("Started", "sforum").'</th>'."\n";
		$out.= '<th>'.__("Last Post", "sforum").'</th>'."\n";
		$out.= '</tr>';

		$alt = '';
		foreach($topics as $topic)
		{
			$forum = array();
			$forum['group_id'] = $topic['group_id'];
			$forum['forum_id'] = $topic['forum_id'];
			$forum['forum_slug'] = $topic['forum_slug'];
			$forum['forum_name'] = $topic['forum_name'];

			$value['forumlock'] = '';

			# Display current topic row

			$out.= '<tr id="topic-'.$topic['topic_id'].'">'."\n";
			$out.= '<td class="'.$alt.'">';
			$out.= '<table><tr><td class="sficoncell '.$alt.'">';
			$statkeys = sf_create_stats_keys($stats[$topic['topic_id']]);

			$out.= sf_render_topic_icon($topic['topic_id'], $stats[$topic['topic_id']][$statkeys[1]]['udate'], $stats[$topic['topic_id']]['thisuser'], $alt);
			$out.= '</td></tr>';
			$out.= '</table>';
			$out.= '</td>';


			$out.= '<td class="'.$alt.'">';
			$out.= '<p>';
			$forumname=sf_filter_title_display($forum['forum_name']);
			$out.=$forumname.'<br />';
			$out.= sf_render_topic_title($forum['forum_slug'], $topic['topic_slug'], sf_filter_title_display($topic['topic_name']), '')."\n";
			$out.= '</p>';

			if($sfglobals['display']['topics']['pagelinks'])
			{
				$out.= sf_render_inline_pagelinks($forum['forum_slug'], $topic['topic_slug'], $topic['post_count'])."\n";
			}
			# first/last post
			if(isset($statkeys[0]))
			{
				$out.= sf_render_first_last_post_cell($forum['forum_slug'], $topic['topic_slug'], $stats[$topic['topic_id']][$statkeys[0]], $alt);
				$out.= sf_render_first_last_post_cell($forum['forum_slug'], $topic['topic_slug'], $stats[$topic['topic_id']][$statkeys[1]], $alt);
			}

			$out.= '</tr>'."\n";
		}
		$out.= '</table>'."\n";
	} else {
		$out.='<br /><div class="sfmessagestrip">'.__("Sorry, couldn't find any related topics!", "sforum").'</div>'."\n";
	}

	$out.= '</div>'."\n";
	$out.= '</div>'."\n";
} else {
	$out.='<br /><div class="sfmessagestrip">'.__("Sorry, couldn't find any related topics!", "sforum").'</div>'."\n";
}

echo $out;

die();

?>