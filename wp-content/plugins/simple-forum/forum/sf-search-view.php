<?php
/*
Simple:Press
Topic View Display
$LastChangedDate: 2010-11-18 07:46:36 -0700 (Thu, 18 Nov 2010) $
$Rev: 4960 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

function sf_render_search_all()
{
	global $sfvars, $current_user, $sfglobals;

	$out = '';

	# Get topic list
	$topics = sf_get_combined_full_topic_search();

	# Display Header
	$out.= '<div id="search-results" class="sfblock">'."\n";
	$out.= sf_render_main_header_table('searchall', 0, '', '', SFRESOURCES.'searchicon.png', false);

	if($topics)
	{
		# grab the complete first/last post stats
		$posts = array();
		foreach($topics as $topic)
		{
			$posts[$topic['topic_id']]=$topic['post_count'];
		}
		$stats = sf_get_combined_topic_stats($posts);

		# make sure we have some page links (top/bottom/both)
		$pageoverride = false;
		if($sfglobals['display']['pagelinks']['ptop'] == false && $sfglobals['display']['pagelinks']['pbottom'] == false)
		{
			$pageoverride = true;
		}

		# Set some stuff we need
		$thispagelinks = sf_compile_paged_topics('all', 0, $sfvars['page'], true, $sfvars['searchpage'], 0);

		# Display page links
		$out.= sf_render_topic_pagelinks($thispagelinks, false, false, false, true, $pageoverride);

		# Start table display
		$out.= '<table class="sfforumtable">'."\n";
		$out.= sf_render_searchall_column_header_row();
		$alt = '';

		foreach($topics as $topic)
		{
			# Display result row
			$out.= sf_render_searchall_entry_row($topic, $stats[$topic['topic_id']], $alt);
			if ($alt == '') $alt = 'sfalt'; else $alt = '';
		}
		$out.= '</table>'."\n";

		# Display page links
		$out.= sf_render_topic_pagelinks($thispagelinks, true, false, false, true, $pageoverride);
		$out.= '</div><br />'."\n";

	} else {

		$out.='<br /><div class="sfmessagestrip">'.__("No Matches Found", "sforum").'</div>'."\n";
		$out.='</div>'."\n";
	}
	$out.= '<div class="sfloginstrip">'."\n";
	$out.= '<table align="center" cellpadding="0" cellspacing="0"><tr><td width="45%"></td>'."\n";
	$out.= '<td><a href="#forumtop"><img class="sfalignright" src="'.SFRESOURCES.'top.png" alt="" title="'.esc_attr(__("go to top", "sforum")).'" /></a></td>'."\n";
	$out.= '<td width="45%"></td>'."\n";
	$out.= '</tr></table></div>';

	return $out;
}

?>