<?php
/*
Simple:Press
Topic View Display
$LastChangedDate: 2011-06-16 22:56:41 -0700 (Thu, 16 Jun 2011) $
$Rev: 6329 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

function sf_render_topic()
{
	global $sfvars, $current_user, $sfglobals, $sfavatarcache, $sfsigcache, $sfrankcache, $sfidentitycache;

	if(!sf_topic_exists($sfvars['topicid']))
	{
        status_header(404);
		$sfvars['error'] = true;
		update_sfnotice('sfmessage', '1@'.sprintf(__('Topic %s Not Found', 'sforum'), $sfvars['topicslug']));
		$out = sf_render_queued_message()."\n";
		$out.= '<div class="sfmessagestrip">'."\n";
		$out.= sprintf(__("There is no such topic named %s", "sforum"), $sfvars['topicslug'])."\n";
		$out.= '</div>'."\n";
		return $out;
	}

	if($current_user->sfaccess)
	{
		# Get Topic Record
		$topic=sf_get_combined_topics_and_posts($sfvars['topicid']);
		if($topic)
		{
			$sfavatarcache = array();
			$sfsigcache = array();
			$sfrankcache = array();
			$sfidentitycache = array();

			# Setup stuff we will need
			$editstrip = false;
			$admintools = false;
			$userposts = 0;

			if(isset($_POST['useredit']) || isset($_POST['adminedit']))
			{
				$sfvars['displaymode'] = 'edit';
			} else {
				$sfvars['displaymode'] = 'posts';
			}

			# == IS FORUM LOCKED OR CAN WE ADD
			if($current_user->sfreply) $showadd = true;
			if($current_user->forumadmin) $showadd = true;
			if ($sfglobals['lockdown']) $showadd = false;

			if(($sfglobals['admin']['sftools']) && ($current_user->sftopicicons)) $admintools=true;

			$lastpost = false;

			# Setup more stuff we will need
			$topiclock = false;
			if($topic['topic_status'] || $topic['forum_status']) $topiclock = true;

			# Does this have a link to blog post
			$bloglink = $topic['blog_post_id'];

			# Setup page links for this topic
			$thispagelinks = sf_compile_paged_posts($sfvars['forumslug'], $sfvars['topicslug'], $sfvars['topicid'], $sfvars['page'], $topic['post_count']);

			# Start display
			$out = '';
			$out.= '<div id="topic-'.$sfvars['topicid'].'" class="sfblock">'."\n";
			$out.= sf_render_main_header_table('topic', 0, sf_filter_title_display($topic['topic_name']), '', SFRESOURCES.'topic.png', false, $showadd, $topiclock, $topic['blog_post_id'], '', false, 0, $topic['topic_status_set'], $topic['topic_status_flag']);

			# Display top page link navigation
			$out.= sf_render_post_pagelinks($thispagelinks, false, $topiclock, $topic['topic_subs'], $topic['topic_page'], $topic['topic_total_pages']);

			# Display any tags for this topic if enabled
			if ($topic['use_tags'] && $sfglobals['display']['posts']['tagstop'])
			{
				$out.= '<table class="sffooter"><tr><td>'."\n";
				$out.= '<div class="sfstatustags">';
				$out.= sf_render_topic_tags($topic['use_tags'], $topic, true, 'top');
				$out.= '</div>';
				$out.= '</td></tr></table>';

				$out.= sf_process_hook('sf_hook_post_topic_top_tags', array($sfvars['forumid'], $sfvars['topicid']));
			}

			if($topic['posts'])
			{
				$numposts=count($topic['posts']);
				$thispost=1;
				$alt = '';

				# Start the Outer Table
				$out.= '<table class="sfposttable">'."\n";

				# Display post column headers
				$out.= sf_render_topic_column_header_row();

				foreach($topic['posts'] as $post)
				{
					# Setup even more stuff we will need
					$currentguest = false;
					$currentmember = false;
					$posterstatus = 'user';
					$postcount = '';
					$sig = false;
					$displaypost = true;
					$approve_text = '';
					$editmode = false;
					$postboxclass = '';

					# is this the last post (which can have the edit button)
					if(($sfglobals['display']['posts']['sortdesc']==true && $thispost==1) ||
					   ($sfglobals['display']['posts']['sortdesc']==false && ($thispost == $numposts)))
					{
						$lastpost = true;
					}

					# Status of the poster of this post (first check Amdin)
					if ($post['admin']) $posterstatus = 'admin';

					# Or was it posted by the user currently loading the page?
					if(($current_user->member) && ($current_user->ID == $post['user_id'])) $currentmember=true;

					# Prepare Posters name and URL if exists
					$poster = '';
                    $cacheid = $post['guest_email'];
					if($post['user_id'])
					{
						$poster=sf_build_name_display($post['user_id'], sf_filter_name_display($post['display_name']));
                        $cacheid = $post['user_id'];
					}
					$username = sf_filter_name_display($post['display_name']);

					if(empty($poster))
					{
						# Must be a guest
						$poster = sf_filter_name_display($post['guest_name']);
						$posterstatus = 'guest';

						# Was it the guest currently loading the page?
						if(($current_user->guestname == $poster) && ($current_user->guestemail == sf_filter_email_display($post['guest_email']))) $currentguest = true;
						$username = $poster;
					}

					# Are we in 'edit' mode with this post?
					if (((isset($_POST['useredit']) && $_POST['useredit'] == $post['post_id']) ||
                         (isset($_POST['adminedit'])) && ($_POST['adminedit'] == $post['post_id'])) &&
			            (($current_user->sfstopedit && $lastpost) || $current_user->sfeditall))
                    {
                    	# in edit mode. But is current user original poster?
						if (($currentmember || $currentguest || $current_user->sfeditall) && !$sfglobals['lockdown'])
						{
							$editmode=true;
							$sfvars['displaymode'] = 'edit';
							$showadd = false;
                    	}
					}

					if(!$editmode) $postboxclass= 'class="sfpostcontent '.$alt.'"';

					# Get post count if poster was a member
					if(!empty($post['user_id']))
					{
						$userposts = $post['posts'];
						$postcount = __("posts ", "sforum").$userposts;
					}

					# Setup Signature line if exists
					if($posterstatus != 'guest')
					{
						$sig = sf_filter_text_display($post['signature']);
					}

					# Determine approval status of post - is it still awaiting aproval?
					if($post['post_status'] == 1)
					{
						$approve_text.= '<p><em>'.__("Post Awaiting Approval by Forum Administrator", "sforum").'</em></p>';
						$postboxclass= 'class="sfpostcontent sfmoderate"';
						if($current_user->moderator == false || $current_user->sfapprove == false)
						{
							$displaypost = false;
						}
					}

					# Outer table - display post row (in sections)
                	if (!array_key_exists($cacheid, $sfrankcache))
                	{
                		$sfrankcache[$cacheid] = sf_render_usertype($posterstatus, $post['user_id'], $userposts);
                	}
              		$rank = $sfrankcache[$cacheid];
					$rank_class = apply_filters('sanitize_title', $rank);
					$out.= '<tr id="post-'.$post['post_id'].'" class="rank-'.$rank_class.' sftype-'.$posterstatus.'" valign="top">'."\n";

					# Poster Details Cell
					if($sfglobals['display']['posts']['userabove'] || $editmode == true)
					{
						# Open outer cell (above)
						$out.= '<td colspan="2" class="sfuserinfoabove '.$alt.'">'."\n";
						$out.= '<a id="p'.$post['post_id'].'"></a>'."\n";
						$out.= sf_render_poster_details_above($post, $posterstatus, $poster, $rank, $postcount, $alt, $cacheid);
					} else {
						# Open outer cell (side)
						$out.= '<td class="sfuserinfoside '.$alt.'">'."\n";
						$out.= '<a id="p'.$post['post_id'].'"></a>'."\n";
						$out.= sf_render_poster_details_side($post, $posterstatus, $poster, $rank, $postcount, $alt, $cacheid);
					}

					# Close outer cell
					$out.= '</td>'."\n";

					# Close poster detail row and prepare next (post content) if single column mode
					if($sfglobals['display']['posts']['userabove'] || $editmode == true) $out.= '</tr><tr valign="top">'."\n";

					# Open outer cell
					if($editmode == true)
					{
						$out.= '<td colspan="2" class="'.$alt.'">'."\n";
					} else {
						$out.= '<td class="'.$alt.'">'."\n";
					}
					# Start Inner post cell table
					$out.= '<table class="sfinnerposttable">'."\n";

					# we only want the bloglink on the first post otherwise send a zero
					if($post['post_index'] == 1 ? $passbloglink = $bloglink : $passbloglink = 0);

					# Display post icon strip if not in edit mode
					if(!$editmode)
					{
						$out.='<tr>'."\n";
						$out.= sf_render_post_icon_strip($post, $posterstatus, $current_user->ID, $username, $currentguest, $currentmember, $displaypost, $topiclock, $lastpost, $alt, $admintools, $topic['post_ratings']);
						# Close the inner iconstrip row
						$out.= '</tr>'."\n";
						$editstrip=true;
					}

					# open postcontent row, inner table cell
					$out.= '<tr>'."\n";

					# Display Post Content (Edit/Normal/Moderation modes)
					$out.= '<td '.$postboxclass.'>'."\n";

					# Pre Post-content hook
					$out.= sf_process_hook('sf_hook_pre_post', array($sfvars['topicid'], $post['post_id']));

					$out.= sf_render_post_content($post, $editmode, $displaypost, $approve_text, $currentguest, $currentmember, $passbloglink);
					$out.= '</td>'."\n";

					# Close the inner post content row
					$out.= '</tr>'."\n";

					# Display Signature of set
					if ($sig && !$editmode)
					{
						$out.= '<tr><td class="sfsignature '.$alt.'">'."\n";
						if (!array_key_exists($cacheid, $sfsigcache))
						{
							$sfsigcache[$cacheid] = sf_render_signature_strip($sig);
						}
						$out.= $sfsigcache[$cacheid];
						$out.= '</td>'."\n";
						$out.= '</tr>'."\n";
					}

					# Feedburner 'Flare' Hook/first and last post hooks
					$permalink = sf_build_url($sfvars['forumslug'], $sfvars['topicslug'], $sfvars['page'], $post['post_id'], $post['post_index']);

					$hook = '';
					$hook.= sf_process_hook('sf_hook_post_feedflare', array($permalink));

					if($post['post_index'] == 1)
					{
						$hook.= sf_process_hook('sf_hook_first_post', array($sfvars['forumid'], $sfvars['topicid']));
					}

					if($lastpost == true)
					{
						$hook.= sf_process_hook('sf_hook_last_post', array($sfvars['forumid'], $sfvars['topicid']));
					}

					if($post['post_index'] != 1 && $lastpost == false)
					{
						$hook.= sf_process_hook('sf_hook_other_posts', array($sfvars['forumid'], $sfvars['topicid']));
					}


					if($hook)
					{
						$out.= '<tr><td class="'.$alt.'">'.$hook.'</td></tr>'."\n";
						$hook='';
					}

					# Post post-content hook
					$hook = '';
					$hook.= sf_process_hook('sf_hook_post_post', array($sfvars['topicid'], $post['post_id']));
					$hook.= sf_process_hook('sf_hook_post_post_by_position', array($sfvars['topicid'], $post['post_id'], $thispost));
					if($hook)
					{
						$out.= '<tr><td class="'.$alt.'">'.$hook.'</td></tr>'."\n";
						$hook='';
					}

					# End Inner post cell table
					$out.= '</table>'."\n";
					# End Outer post cell middle
					$out.= '</td>'."\n";

					# Close outer right cell
					$out.= '</tr>'."\n";

					$thispost++;
					if ($alt == '') $alt = 'sfalt'; else $alt = '';
				}
				# Close outer table
				$out.= '</table>'."\n";
				$out.= '<div class="sfdivider"></div>';

				# topic status updater
				if($sfglobals['display']['posts']['topicstatuschanger'])
				{
					if(!empty($topic['topic_status_set']))
					{
						$out.= sf_render_topic_status_updater($topic['topic_status_set'], $topic['topic_status_flag'], $topic['user_id']);
					}
				}

				# Display any tags for this topic if enabled
				if ($topic['use_tags'] && $sfglobals['display']['posts']['tagsbottom'])
				{
					$out.= '<table class="sfclear sffooter"><tr><td>'."\n";
					$out.= '<div class="sfstatustags">';
					$out.= sf_render_topic_tags($topic['use_tags'], $topic, true, 'bottom');
					$out.= '</div>';
					$out.= '</td></tr></table>';

					$out.= sf_process_hook('sf_hook_post_topic_bottom_tags', array($sfvars['forumid'], $sfvars['topicid']));
				}

				# Topic footer hook
				$out.= sf_process_hook('sf_hook_topic_footer', array($sfvars['forumid'], $sfvars['topicid']));

				# Display bottom page link navigation
				$out.= sf_render_post_pagelinks($thispagelinks, true, $topiclock, '', $topic['topic_page'], $topic['topic_total_pages']);

			} else {
				$out.= '<div class="sfmessagestrip">'.__("There are No Posts for this Topic", "sforum").'</div>'."\n";
			}
			$out.= '</div>'."\n";
		}
		# Display Add Post form (hidden)
		if((!$topiclock) || ($current_user->forumadmin))
		{
			if($current_user->sfreply && $sfvars['displaymode'] == 'posts')
			{
				$out.= '<a id="dataform"></a>'."\n";
				$out.= sf_add_post($sfvars['forumid'], $sfvars['topicid'], $topic['topic_name'], $topic['topic_status_set'], $topic['topic_status_flag'], $topic['user_id'], $topic['topic_subs'], $topic['topic_watches']);
			}
		}
	} else {
		$out = 'Access Denied';
	}

	return $out;
}

?>