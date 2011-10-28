<?php
/*
Simple:Press
Edit Tools - Admin Links - 'Manage' and 'Edit Tools'
$LastChangedDate: 2009-04-26 18:53:38 +0100 (Sun, 26 Apr 2009) $
$Rev: 1802 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

sf_forum_ahah_support();
sf_setup_admin_constants();

$fid = '';
if (isset($_GET['forum'])) $fid = sf_esc_int($_GET['forum']);

sf_initialise_globals($fid);

global $wpdb;
# ----------------------------------

# get out of here if no action specified
if (empty($_GET['action'])) die();
$action = $_GET['action'];

if ($action == 'manage')
{
	# admin links to forum admin backend
	echo sf_create_admin_links();
	die();
}

if ($action == 'topictools')
{
	# admins topic tool set
	$tid = sf_esc_int($_GET['topic']);
	$page = sf_esc_int($_GET['page']);
    if (empty($fid) || empty($tid)) die();

	$forum = $wpdb->get_row("SELECT * FROM ".SFFORUMS." WHERE forum_id=".$fid,  ARRAY_A);
	$topic = $wpdb->get_row("SELECT * FROM ".SFTOPICS." WHERE topic_id=".$tid,  ARRAY_A);
	echo sf_render_topic_tools($topic, $forum, $page);
	die();
}

if ($action == 'posttools')
{
	# admins post tool set
	$postid = sf_esc_int($_GET['post']);
	$page = sf_esc_int($_GET['page']);
	$postnum = sf_esc_int($_GET['postnum']);
	$displayname = esc_html(urldecode($_GET['name']));

    if (empty($postid)) die();
	$post = $wpdb->get_row("SELECT * FROM ".SFPOSTS." WHERE post_id=".$postid,  ARRAY_A);
	$forum = $wpdb->get_row("SELECT * FROM ".SFFORUMS." WHERE forum_id=".$post['forum_id'],  ARRAY_A);
	$topic = $wpdb->get_row("SELECT * FROM ".SFTOPICS." WHERE topic_id=".$post['topic_id'],  ARRAY_A);

	# establish email
	if($post['user_id']==NULL || $post['user_id']==0)
	{
		$useremail = '';
		$guestemail = sf_filter_email_display($post['guest_email']);
	} else {
		$useremail = sf_filter_email_display($wpdb->get_var("SELECT user_email FROM ".SFUSERS." WHERE ID=".$post['user_id']));
		$guestemail = '';
	}
	echo sf_render_post_tools($post, $forum, $topic, $page, $postnum, $useremail, $guestemail, $displayname);
	die();
}

die();

# Admins Post Tools
function sf_render_post_tools($post, $forum, $topic, $page, $postnum, $useremail, $guestemail, $displayname)
{
	global $current_user;

    $out = '';

	if($post['post_pinned'])
	{
		$pintext = __("Unpin this Post", "sforum");
	} else {
		$pintext = __("Pin this Post", "sforum");
	}

	$out.='<table class="sfpopuptable">';

	$out.='<tr><td colspan="2" class="sfdata"><b>'.$displayname.'/#'.$postnum.'</b></td></tr>';

	if(($post['post_status'] == 1) && ($current_user->sfapprove))
	{
		$out.='<tr><td>';
		$out.= '<img src="'.SFRESOURCES.'approve.png" alt="" title="'.esc_attr(__("approve this post", "sforum")).'" />';
		$out.='</td><td class="sfdata">';
		$out.= '<form action="'.sf_build_url($forum['forum_slug'], $topic['topic_slug'], $page, $post['post_id'], $post['post_index']).'" method="post" name="postapprove'.$post['post_id'].'">'."\n";
		$out.= '<input type="hidden" name="approvepost" value="'.$post['post_id'].'" />'."\n";
		$out.= '<a href="javascript:document.postapprove'.$post['post_id'].'.submit();">'.__("Approve This Post", "sforum").'</a>';
		$out.= '</form>'."\n";
		$out.='</td></tr>';
	}

	if($current_user->sfemail)
	{
		$content = '';
		if($post['user_id']) {
			$content.= esc_attr(__('User ID', 'sforum')).': '.$post['user_id'].' - '.$displayname.'<br /><br />';
		}
		$email=$useremail;
		if(empty($email)) $email=$guestemail;
		$content.= $email.'<br />'.$post['poster_ip'].'<br /><br />';
		$out.='<tr><td>';
		$out.= '<img src="'.SFRESOURCES.'email.png" alt="" title="'.esc_attr(__("Users Email and IP", "sforum")).'" />';
		$out.='</td><td class="sfdata">';
		$out.= '<form action="">'."\n";
		$msg = esc_js(__('Users Email and IP', 'sforum').'<br />');
		$out.= '<a href="" id="sfshowmail'.$post['post_id'].'" onclick="sfjshowUserMail(\''.$msg.'\',\''.$content.'\',\''.$post['post_id'].'\');return false;">'.__("Users Email and IP", "sforum").'</a>'."\n";
		$out.='<div class="highslide-html-content" id="mail-content'.$post['post_id'].'" style="width: 300px">';
		$out.='<div class="inline-edit" id="sfmail'.$post['post_id'].'"></div>';
		$out.='<input type="button" class="sfcontrol" id="sfclosevalid'.$post['post_id'].'" onclick="return hs.close(this)" value="'.esc_attr(__("Close", "sforum")).'" />';
		$out.='</div>';
		$out.= '</form>'."\n";
		$out.='</td></tr>';
	}

	if($current_user->sfpinposts)
	{
		$out.='<tr><td>';
		$out.= '<img src="'.SFRESOURCES.'pin.png" alt="" title="'.esc_attr($pintext).'" />';
		$out.='</td><td class="sfdata">';
		$out.= '<form action="'.sf_build_url($forum['forum_slug'], $topic['topic_slug'], $page, $post['post_id'], $post['post_index']).'" method="post" name="postpin'.$post['post_id'].'">'."\n";
		$out.= '<input type="hidden" name="pinpost" value="'.$post['post_id'].'" />'."\n";
		$out.= '<input type="hidden" name="pinpostaction" value="'.esc_attr($pintext).'" />'."\n";
		$out.= '<a href="javascript:document.postpin'.$post['post_id'].'.submit();">'.$pintext.'</a>'."\n";
		$out.= '</form>'."\n";
		$out.='</td></tr>';
	}

	if($current_user->sfedit)
	{
		$out.='<tr><td>';
		$out.= '<img src="'.SFRESOURCES.'edit.png" alt="" title="'.esc_attr(__("edit this post", "sforum")).'" />';
		$out.='</td><td class="sfdata">';
		$out.= '<form action="'.sf_build_url($forum['forum_slug'], $topic['topic_slug'], $page, $post['post_id'], $post['post_index']).'" method="post" name="admineditpost'.$post['post_id'].'">'."\n";
		$out.= '<input type="hidden" name="adminedit" value="'.$post['post_id'].'" />'."\n";
		$out.= '<a href="javascript:document.admineditpost'.$post['post_id'].'.submit();">'.__("Edit This Post", "sforum").'</a>'."\n";
		$out.= '</form>'."\n";
		$out.='</td></tr>';
	}

	if($current_user->sfdelete || $current_user->sfdeleteown && $current_user->ID == $post['user_id'])
	{
		$out.='<tr><td>';
		$out.= '<img src="'.SFRESOURCES.'delete.png" alt="" title="'.esc_attr(__("delete this post", "sforum")).'" />';
		$out.='</td><td class="sfdata">';
		$out.= '<form action="'.sf_build_url($forum['forum_slug'], $topic['topic_slug'], $page, 0).'" method="post" name="postkill'.$post['post_id'].'">'."\n";
		$out.= '<input type="hidden" name="killpost" value="'.$post['post_id'].'" />'."\n";
		$out.= '<input type="hidden" name="killposttopic" value="'.$post['topic_id'].'" />'."\n";
		$out.= '<input type="hidden" name="killpostforum" value="'.$post['forum_id'].'" />'."\n";
		$out.= '<input type="hidden" name="killpostposter" value="'.$post['user_id'].'" />'."\n";
		$msg = esc_js(__('Are you sure you want to delete this Post?','sforum'));
		$out.= '<a href="javascript: if(confirm(\''.$msg.'\')) {document.postkill'.$post['post_id'].'.submit();}">'.__("Delete This Post", "sforum").'</a>'."\n";
		$out.= '</form>'."\n";
		$out.='</td></tr>';
	}

	if($current_user->sfmoveposts)
	{
		$out.='<tr><td>';
		$out.= '<img src="'.SFRESOURCES.'move.png" alt="" title="'.esc_attr(__("move this post", "sforum")).'" />';
		$out.='</td><td class="sfdata">';
		$out.= '<form action="">'."\n";
        $site = SFHOMEURL."index.php?sf_ahah=admintools&action=mp&amp;id=".$post['topic_id']."&amp;pid=".$post['post_id']."&amp;pix=".$post['post_index'];
		$out.= '<a rel="nofollow" href="'.$site.'" onclick="return hs.htmlExpand(this, { objectType: \'ajax\', preserveContent: true} )">'.__("Move This Post", "sforum").'</a>';
		$out.= '</form>';
		$out.='</td></tr>';
	}

	if($current_user->sfreassign)
	{
		$out.='<tr><td>';
		$out.= '<img src="'.SFRESOURCES.'reassign.png" alt="" title="'.esc_attr(__("reassign this post", "sforum")).'" />';
		$out.='</td><td class="sfdata">';
		$out.= '<form action="">'."\n";
        $site = SFHOMEURL."index.php?sf_ahah=admintools&action=rp&amp;id=".$post['topic_id']."&amp;pid=".$post['post_id']."&amp;uid=".$post['user_id'];
		$out.= '<a rel="nofollow" href="'.$site.'" onclick="return hs.htmlExpand(this, { objectType: \'ajax\', preserveContent: true} )">'.__("Reassign This Post", "sforum").'</a>';
		$out.= '</form>';
		$out.='</td></tr>';
	}

	if($current_user->forumadmin)
	{
		$out.='<tr><td>';
		$out.= '<img src="'.SFRESOURCES.'properties.png" alt="" title="'.esc_attr(__("post properties", "sforum")).'" />';
		$out.='</td><td class="sfdata">';
		$out.= '<form action="">'."\n";
        $site = SFHOMEURL."index.php?sf_ahah=admintools&action=props&amp;forum=".$post['forum_id']."&amp;topic=".$post['topic_id']."&amp;post=".$post['post_id'];
		$out.= '<a rel="nofollow" href="'.$site.'" onclick="return hs.htmlExpand(this, { objectType: \'ajax\', preserveContent: true} )">'.__("View Properties", "sforum").'</a>';
		$out.= '</form>';
		$out.='</td></tr>';
	}

	$out.= '</table>';
	$out.= '</div>'."\n";

	return $out;
}

# Admins Topic Tools
function sf_render_topic_tools($topic, $forum, $page)
{
	global $current_user, $sfglobals;

    $out = '';

	$locktext=__("Lock this Topic", "sforum");
	if($topic['topic_status']) $locktext=__("Unlock this Topic", "sforum");
	$pintext=__("Pin this Topic", "sforum");
	if($topic['topic_pinned']) $pintext=__("Unpin this Topic", "sforum");

	$order="ASC"; # default
	if($sfglobals['display']['posts']['sortdesc']) $order="DESC"; # global override

	if($order == "ASC")
	{
		$sorttext=__("Sort Most Recent Posts to Top", "sforum");
	} else {
		$sorttext=__("Sort Most Recent Posts to Bottom", "sforum");
	}

	$out.='<table class="sfpopuptable">';

	$out.='<tr><td colspan="2" class="sfdata"><b>'.sf_filter_title_display($topic['topic_name']).'</b></td></tr>';

	if($current_user->sflock)
	{
		$out.='<tr><td>';
		$out.= '<img src="'.SFRESOURCES.'locked.png" alt="" title="'.esc_attr($locktext).'" />';
		$out.='</td><td class="sfdata">';
		$out.= '<form action="'.sf_build_url($forum['forum_slug'], '', $page, 0).'" method="post" name="topiclock'.$topic['topic_id'].'">'."\n";
		$out.= '<input type="hidden" name="locktopic" value="'.$topic['topic_id'].'" />'."\n";
		$out.= '<input type="hidden" name="locktopicaction" value="'.esc_attr($locktext).'" />'."\n";
		$out.= '<a href="javascript:document.topiclock'.$topic['topic_id'].'.submit();">'.$locktext.'</a>'."\n";
		$out.= '</form>';
		$out.='</td></tr>';
	}

	if($current_user->sfpintopics)
	{
		$out.='<tr><td>';
		$out.='<img src="'.SFRESOURCES.'pin.png" alt="" title="'.esc_attr($pintext).'" />';
		$out.='</td><td class="sfdata">';
		$out.= '<form action="'.sf_build_url($forum['forum_slug'], '', $page, 0).'" method="post" name="topicpin'.$topic['topic_id'].'">'."\n";
		$out.= '<input type="hidden" name="pintopic" value="'.$topic['topic_id'].'" />'."\n";
		$out.= '<input type="hidden" name="pintopicaction" value="'.esc_attr($pintext).'" />'."\n";
		$out.= '<a href="javascript:document.topicpin'.$topic['topic_id'].'.submit();">'.$pintext.'</a>'."\n";
		$out.= '</form>';
		$out.='</td></tr>';
	}

	if(($current_user->sfeditowntitle && $topic['user_id'] == $current_user->ID) || $current_user->sfeditalltitle)
	{
		$out.='<tr><td>';
		$out.='<img src="'.SFRESOURCES.'edit.png" alt="" title="'.esc_attr(__("Edit This Topic Title", "sforum")).'" />';
		$out.='</td><td class="sfdata">';
		$out.= '<form action="'.sf_build_url($forum['forum_slug'], '', $page, 0).'#topicedit" method="post" name="edittopic'.$topic['topic_id'].'">'."\n";
		$out.= '<input type="hidden" name="topicedit" value="'.$topic['topic_id'].'" />'."\n";
		$out.= '<a href="javascript:document.edittopic'.$topic['topic_id'].'.submit();">'.__("Edit This Topic Title", "sforum").'</a>'."\n";
		$out.= '</form>';
		$out.='</td></tr>';
	}

	if(($current_user->sftopicstatus) && ($forum['topic_status_set'] != 0))
	{
		$out.='<tr><td>';
		$out.='<img src="'.SFRESOURCES.'topicstatus.png" alt="" title="'.esc_attr(__("Change Topic Status", "sforum")).'" />';
		$out.='</td><td class="sfdata">';
		$out.= '<form action="">'."\n";
        $site = SFHOMEURL."index.php?sf_ahah=admintools&action=ct&amp;id=".$topic['topic_id']."&amp;flag=".$topic['topic_status_flag']."&amp;set=".$forum['topic_status_set']."&amp;returnpage=".$page;
		$out.= '<a rel="nofollow" href="'.$site.'" onclick="return hs.htmlExpand(this, { objectType: \'ajax\', preserveContent: true} )">'.__("Change Topic Status", "sforum").'</a>';
		$out.= '</form>';
		$out.='</td></tr>';
	}

	if($current_user->sfdelete)
	{
		$out.='<tr><td>';
		$out.='<img src="'.SFRESOURCES.'delete.png" alt="" title="'.esc_attr(__("Delete This Topic", "sforum")).'" />';
		$out.='</td><td class="sfdata">';
		$out.= '<form action="'.sf_build_url($forum['forum_slug'], '', $page, 0).'" method="post" name="topickill'.$topic['topic_id'].'">'."\n";
		$out.= '<input type="hidden" name="killtopic" value="'.$topic['topic_id'].'" />'."\n";
		$msg = esc_js(__("Are you sure you want to delete this Topic?", "sforum"));
		$out.= '<a rel="nofollow" href="javascript: if(confirm(\''.$msg.'\')) {document.topickill'.$topic['topic_id'].'.submit();}">'.__("Delete This Topic", "sforum").'</a>'."\n";
		$out.= '</form>';
		$out.='</td></tr>';
	}

	if($current_user->sfmovetopics)
	{
		$out.='<tr><td>';
		$out.='<img src="'.SFRESOURCES.'move.png" alt="" title="'.esc_attr(__("Move This Topic", "sforum")).'" />';
		$out.='</td><td class="sfdata">';
		$out .= '<form action="">'."\n";
        $site = SFHOMEURL."index.php?sf_ahah=admintools&action=mt&amp;topicid=".$topic['topic_id']."&amp;forumid=".$forum['forum_id'];
		$out.= '<a rel="nofollow" href="'.$site.'" onclick="return hs.htmlExpand(this, { objectType: \'ajax\', preserveContent: true} )">'.__("Move This Topic", "sforum").'</a>';
		$out.= '</form>';
		$out.='</td></tr>';
	}

	if(($topic['blog_post_id'] != 0) && ($current_user->sfbreaklink))
	{
		$out.='<tr><td>';
		$out.='<img src="'.SFRESOURCES.'breaklink.png" alt="" title="'.esc_attr(__("Break Topic Link To Blog Post", "sforum")).'" />';
		$out.='</td><td class="sfdata">';
		$out.= '<form action="'.sf_build_url($forum['forum_slug'], '', $page, 0).'" method="post" name="breaklink'.$topic['topic_id'].'">'."\n";
		$out.= '<input type="hidden" name="linkbreak" value="'.$topic['topic_id'].'" />'."\n";
		$out.= '<input type="hidden" name="blogpost" value="'.$topic['blog_post_id'].'" />'."\n";
		$out.= '<a rel="nofollow" href="javascript:document.breaklink'.$topic['topic_id'].'.submit();">'.__("Break Blog Link", "sforum").'</a><br />'."\n";
		$out.= '</form>';
		$out.='</td></tr>';
	}

	if($current_user->forumadmin || $current_user->moderator)
	{
		$out.='<tr><td>';
		$out.='<img src="'.SFRESOURCES.'tag_edit.png" alt="" title="'.esc_attr(__("Edit Topic Tags", "sforum")).'" />';
		$out.='</td><td class="sfdata">';
		$out.= '<form action="">'."\n";
        $site = SFHOMEURL."index.php?sf_ahah=admintools&action=edit-tags&amp;topicid=".$topic['topic_id'];
		$out.= '<a rel="nofollow" href="'.$site.'" onclick="return hs.htmlExpand(this, { objectType: \'ajax\', preserveContent: true} )">'.__("Edit Topic Tags", "sforum").'</a>';
		$out.= '</form>';
		$out.='</td></tr>';
	}

	if($current_user->forumadmin || $current_user->moderator)
	{
		$out.='<tr><td>';
		$out.='<img src="'.SFRESOURCES.'properties.png" alt="" title="'.esc_attr(__("View Properties", "sforum")).'" />';
		$out.='</td><td class="sfdata">';
		$out.= '<form action="">'."\n";
        $site = SFHOMEURL."index.php?sf_ahah=admintools&action=props&amp;group=".$forum['group_id']."&amp;forum=".$forum['forum_id']."&amp;topic=".$topic['topic_id'];
		$out.= '<a rel="nofollow" href="'.$site.'" onclick="return hs.htmlExpand(this, { objectType: \'ajax\', preserveContent: true} )">'.__("View Properties", "sforum").'</a>';
		$out.= '</form>';
		$out.='</td></tr>';
	}

	$out.= '</table>';

	return $out;
}

# Admins 'Manage' Links
function sf_create_admin_links()
{
	global $current_user;

	$out = '';
	$class = "sfalignleft";

	$out.='<table class="sfpopuptable">';

	if ($current_user->allcaps['SPF Manage Forums'])
	{
		$out.='<tr><td>';
		$out.='<img src="'.SFRESOURCES.'sf-adminforums.png" alt="" title="" />'."\n";
		$out.='</td><td class="sfdata">';
		$out.='<a class="'.$class.'" href="'.SFADMINFORUM.'">';
		$out.=__("Forums", "sforum").'</a>';
		$out.='</td></tr>';
	}

	if ($current_user->allcaps['SPF Manage Options'])
	{
		$out.='<tr><td>';
		$out.='<img src="'.SFRESOURCES.'sf-adminoptions.png" alt="" title="" />'."\n";
		$out.='</td><td class="sfdata">';
		$out.='<a class="'.$class.'" href="'.SFADMINOPTION.'">';
		$out.=__("Options", "sforum").'</a>';
		$out.='</td></tr>';
	}

	if ($current_user->allcaps['SPF Manage Components'])
	{
		$out.='<tr><td>';
		$out.='<img src="'.SFRESOURCES.'sf-admincomponents.png" alt="" title="" />'."\n";
		$out.='</td><td class="sfdata">';
		$out.='<a class="'.$class.'" href="'.SFADMINCOMPONENT.'">';
		$out.=__("Components", "sforum").'</a>';
		$out.='</td></tr>';
	}

	if ($current_user->allcaps['SPF Manage User Groups'])
	{
		$out.='<tr><td>';
		$out.='<img src="'.SFRESOURCES.'sf-adminusergroups.png" alt="" title="" />'."\n";
		$out.='</td><td class="sfdata">';
		$out.='<a class="'.$class.'" href="'.SFADMINUSERGROUP.'">';
		$out.=__("User Groups", "sforum").'</a>';
		$out.='</td></tr>';
	}

	if ($current_user->allcaps['SPF Manage Permissions'])
	{
		$out.='<tr><td>';
		$out.='<img src="'.SFRESOURCES.'sf-adminpermissions.png" alt="" title="" />'."\n";
		$out.='</td><td class="sfdata">';
		$out.='<a class="'.$class.'" href="'.SFADMINPERMISSION.'">';
		$out.=__("Permissions", "sforum").'</a>';
		$out.='</td></tr>';
	}

	if ($current_user->allcaps['SPF Manage Users'])
	{
		$out.='<tr><td>';
		$out.='<img src="'.SFRESOURCES.'sf-adminusers.png" alt="" title="" />'."\n";
		$out.='</td><td class="sfdata">';
		$out.='<a class="'.$class.'" href="'.SFADMINUSER.'">';
		$out.=__("Users", "sforum").'</a>';
		$out.='</td></tr>';
	}

	if ($current_user->allcaps['SPF Manage Profiles'])
	{
		$out.='<tr><td>';
		$out.='<img src="'.SFRESOURCES.'sf-adminprofiles.png" alt="" title="" />'."\n";
		$out.='</td><td class="sfdata">';
		$out.='<a class="'.$class.'" href="'.SFADMINPROFILE.'">';
		$out.=__("Profiles", "sforum").'</a>';
		$out.='</td></tr>';
	}

	if ($current_user->allcaps['SPF Manage Admins'])
	{
		$out.='<tr><td>';
		$out.='<img src="'.SFRESOURCES.'sf-adminadmins.png" alt="" title="" />'."\n";
		$out.='</td><td class="sfdata">';
		$out.='<a class="'.$class.'" href="'.SFADMINADMIN.'">';
		$out.=__("Admins", "sforum").'</a>';
		$out.='</td></tr>';
	}

	if ($current_user->allcaps['SPF Manage Tags'])
	{
		$out.='<tr><td>';
		$out.='<img src="'.SFRESOURCES.'sf-admintags.png" alt="" title="" />'."\n";
		$out.='</td><td class="sfdata">';
		$out.='<a class="'.$class.'" href="'.SFADMINTAGS.'">';
		$out.=__("Tags", "sforum").'</a>';
		$out.='</td></tr>';
	}

	if ($current_user->allcaps['SPF Manage Toolbox'])
	{
		$out.='<tr><td>';
		$out.='<img src="'.SFRESOURCES.'sf-admintoolbox.png" alt="" title="" />'."\n";
		$out.='</td><td class="sfdata">';
		$out.='<a class="'.$class.'" href="'.SFADMINTOOLBOX.'">';
		$out.=__("Toolbox", "sforum").'</a>';
		$out.='</td></tr>';
	}

	if ($current_user->allcaps['SPF Manage Configuration'])
	{
		$out.='<tr><td>';
		$out.='<img src="'.SFRESOURCES.'sf-adminconfig.png" alt="" title="" />'."\n";
		$out.='</td><td class="sfdata">';
		$out.='<a class="'.$class.'" href="'.SFADMINCONFIG.'">';
		$out.=__("Configuration", "sforum").'</a>';
		$out.='</td></tr>';
	}

	$out.='</table>';

	return $out;
}

die();

?>