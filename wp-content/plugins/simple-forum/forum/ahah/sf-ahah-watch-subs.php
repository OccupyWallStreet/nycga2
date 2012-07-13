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

include_once (SF_PLUGIN_DIR.'/forum/sf-forum-components.php');

global $current_user, $sfglobals, $sfvars;

sf_initialise_globals();

$action = $_GET['action'];
if (empty($action)) die();

# --------------------------------------------

if($action == 'watch' || $action == 'subs')
{
    $out = '';
    $out.= '<div id="sforum">'."\n";
    $out.= '<div class="sfblock">'."\n";

    if ($action == 'watch')
    {
    	$datarows = sf_get_watched_topics();
        if ($datarows) $type = 'watches'; else $type = 'forum';
    	$out.= sf_render_main_header_table($type, 0, sf_filter_title_display(__('Watched Topics', 'sforum')), sf_filter_text_display(__('List of topics that you are currently watching.', 'sforum')), SFRESOURCES.'watchicon.png');
    } else {
    	$datarows = sf_get_subscribed_topics();
        if ($datarows) $type = 'subs'; else $type = 'forum';
    	$out.= sf_render_main_header_table($type, 0, sf_filter_title_display(__('Subscribed Topics', 'sforum')), sf_filter_text_display(__('List of topics to which you have subscribed.', 'sforum')), SFRESOURCES.'usersubscribed.png');
    }

    if ($datarows)
    {
    	$topics = $datarows['records'];
    	$count = $datarows['count'];
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
    		$out.= '<tr>';
            $out.= '<th colspan="2"></th>'."\n";
    		$out.= '<th width="14%">'.__("Started", "sforum").'</th>'."\n";
    		$out.= '<th width="14%">'.__("Last Post", "sforum").'</th>'."\n";

    		if($action == 'watch')
    		{
    			$out.= '<th width="100">'.__("End Watch", "sforum").'</th>'."\n";
    		} else {
    			$out.= '<th width="100">'.__("Unsubscribe", "sforum").'</th>'."\n";
    		}
    		$out.= '</tr>';

    		$alt = '';

    		foreach ($topics as $topic)
    		{
    			$forum = array();
    			$forum['group_id'] = $topic['group_id'];
    			$forum['forum_id'] = $topic['forum_id'];
    			$forum['forum_slug'] = $topic['forum_slug'];
    			$forum['forum_name'] = $topic['forum_name'];

    			$value['forumlock'] = '';
    			$value['watches'] = true;

    			# Display current topic row
    			$out.= '<tr id="topic'.$topic['topic_id'].'">'."\n";
    			$out.= '<td width="50" class="'.$alt.'">';
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
    			$out.= sf_render_topic_title($forum['forum_slug'], $topic['topic_slug'], sf_filter_title_display($topic['topic_name']), $topic['post_tip'])."\n";
    			$out.= '</p>';

    			if($sfglobals['display']['topics']['pagelinks'])
    			{
    				$out.= sf_render_inline_pagelinks($forum['forum_slug'], $topic['topic_slug'], $topic['post_count'])."\n";
    			}
    			$out.= '</td>';

    			# first/last post
    			if(isset($statkeys[0]))
    			{
    				$out.= sf_render_first_last_post_cell($forum['forum_slug'], $topic['topic_slug'], $stats[$topic['topic_id']][$statkeys[0]], $alt);
    				$out.= sf_render_first_last_post_cell($forum['forum_slug'], $topic['topic_slug'], $stats[$topic['topic_id']][$statkeys[1]], $alt);
    			}

    			if (!$sfglobals['lockdown'])
    			{
    				if($action == 'watch')
    				{
            			$text = __("End Topic Watch", "sforum");
                        $site = SFHOMEURL."index.php?sf_ahah=watch-subs&action=remove-watch&topic=".$topic['topic_id']."&user=".$current_user->ID;
                        $out.= '<td class="'.$alt.'" width="100" align="center">';
            			$out.= '<img onclick="sfjremoveItem(\''.$site.'\', \'topic'.$topic['topic_id'].'\');" src="'.SFRESOURCES.'watchoff.png" alt="" title="'.esc_attr($text).'"/>';
                        $out.= '</td>';
    				} else {
            			$text = __("End Subscription", "sforum");
                        $site = SFHOMEURL."index.php?sf_ahah=watch-subs&action=remove-sub&topic=".$topic['topic_id']."&user=".$current_user->ID;
                        $out.= '<td class="'.$alt.'" width="100" align="center">';
            			$out.= '<img onclick="sfjremoveItem(\''.$site.'\', \'topic'.$topic['topic_id'].'\');" src="'.SFRESOURCES.'unsubscribe.png" alt="" title="'.esc_attr($text).'"/>';
                        $out.= '</td>';
    				}
    			} else {
    				$out.= '<td></td>';
    			}

    			$out.= '</tr>'."\n";
				if ($alt == '') $alt = 'sfalt'; else $alt = '';
    		}
    		$out.= '</table>'."\n";
    		$out.= '</div><br />'."\n";
    	} else {
    		if($action == 'watch')
    		{
    			$out.='<br /><div class="sfmessagestrip">'.__("You are not currently watching any topics!", "sforum").'</div>'."\n";
    		} else {
    			$out.='<br /><div class="sfmessagestrip">'.__("You are not currently subscribed to any topics!", "sforum").'</div>'."\n";
    		}
    	}
    } else {
    	if($action == 'watch')
    	{
    		$out.='<br /><div class="sfmessagestrip">'.__("You are not currently watching any topics!", "sforum").'</div>'."\n";
    	} else {
    		$out.='<br /><div class="sfmessagestrip">'.__("You are not currently subscribed to any topics!", "sforum").'</div>'."\n";
    	}
    }

    $out.='</div>'."\n";
    $out.= '</div>'."\n";

    echo $out;
}

if ($action == 'remove-watch')
{
    $topic = sf_esc_int($_GET['topic']);
    $user = sf_esc_int($_GET['user']);

    if ($user == $current_user->ID)
    {
        sf_remove_watch($topic, $user);
    }
}

if ($action == 'remove-sub')
{
    $topic = sf_esc_int($_GET['topic']);
    $user = sf_esc_int($_GET['user']);

    if ($user == $current_user->ID)
    {
        sf_remove_subscription($topic, $user);
    }
}

die();

?>