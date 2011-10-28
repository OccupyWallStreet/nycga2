<?php
/*
Simple:Press
Public - global code
$LastChangedDate: 2010-09-17 10:04:47 -0700 (Fri, 17 Sep 2010) $
$Rev: 4642 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

# ------------------------------------------------------------------
# sf_load_front_blog_js()
#
# Loads any JS needed on blog when not a forum view
# ------------------------------------------------------------------
function sf_load_front_blog_js()
{
	# syntax Highlighting
	$sfsyntax = sf_get_option('sfsyntax');
	if($sfsyntax['sfsyntaxblog'] == true)
	{
		wp_enqueue_script('jquery');
		wp_enqueue_script('sfsyntax', SFJSCRIPT.'syntax/jquery.syntax.js', array('jquery'));
		wp_enqueue_script('sfsyntaxcache', SFJSCRIPT.'syntax/jquery.syntax.cache.js', array('jquery'));

		# inline js to showsynyax hghlighting on blog post
		add_action( 'wp_footer', 'sfjs_show_syntax' );
	}

	$sfsupport = sf_get_option('sfsupport');
	if ($sfsupport['sfusingliststags'])
	{
		wp_enqueue_script('spf', SFJSCRIPT.'forum/sf-forum.js', array('jquery'));
	}
}

# inline syntax hghlighting n blog post
function sfjs_show_syntax() {
?>
<script type="text/javascript">
jQuery(document).ready(function($) {
	Syntax.root = "<?php echo SFJSCRIPT.'syntax/'; ?>";
	jQuery.syntax({layout: 'table', replace: true})
});
</script>
<?php
}

# ------------------------------------------------------------------
# sf_blog_links_control()
#
# MOVED HERE to make it always available as can be used for
# canonical urls.
#
# General postmeta handling for blog linking
#	$action		save, update, read or delete
#	$postid		WP Post id of the link
#	$forumid	ID of target forum
#	$topicid	ID of target topic
#	$syncedit	Optional - sync edit flag
# ------------------------------------------------------------------
function sf_blog_links_control($action, $postid, $forumid=0, $topicid=0, $syncedit=0)
{
	global $wpdb;

	# seems to sometimes get triggered by other plugins although it suggests a core WP bug
	if(!isset($postid)) return;

	if($action == 'save')
	{
		# check if there already...
		$result = $wpdb->get_results("SELECT * FROM ".SFLINKS." WHERE post_id=".$postid);
		if($result)
		{
			$action = 'update';
		} else {
			$sql="INSERT INTO ".SFLINKS." (post_id, forum_id, topic_id, syncedit) VALUES (".$postid.", ".$forumid.", ".$topicid.", ".$syncedit.");";
			$wpdb->query($sql);

			return;
		}
	}

	if($action == 'update')
	{
		$sql="UPDATE ".SFLINKS." SET forum_id=".$forumid.", topic_id=".$topicid.", syncedit=".$syncedit." WHERE post_id=".$postid;
		$wpdb->query($sql);

		return;
	}
	if($action == 'read')
	{
		$sql = "SELECT * FROM ".SFLINKS." WHERE post_id=".$postid;
		return($wpdb->get_row($sql));
	}
	if($action == 'delete')
	{
		$sql = "DELETE FROM ".SFLINKS." WHERE post_id=".$postid;
		return($wpdb->get_row($sql));
	}
}

# ------------------------------------------------------------------
# sf_mail_filter_from()
#
# Filter Call
# Sets up the 'from' email options
#	$from:		Passed in to filter
# ------------------------------------------------------------------
function sf_mail_filter_from($from)
{
	$sfmail = sf_get_option('sfmail');
	$mailfrom = $sfmail['sfmailfrom'];
	$maildomain = $sfmail['sfmaildomain'];
	if((!empty($mailfrom)) && (!empty($maildomain)))
	{
		$from = $mailfrom.'@'.$maildomain;
	}
	return $from;
}

# ------------------------------------------------------------------
# sf_mail_filter_name()
#
# Filter Call
# Sets up the 'from' email options
#	$from:		Passed in to filter
# ------------------------------------------------------------------
function sf_mail_filter_name($from)
{
	$sfmail = sf_get_option('sfmail');
	$mailsender = $sfmail['sfmailsender'];
	if(!empty($mailsender))
	{
		$from = $mailsender;
	}
	return $from;
}

# ------------------------------------------------------------------
# sf_block_admin()
#
# Blocks normal users from accessing WP admin area
# ------------------------------------------------------------------
function sf_block_admin()
{
	global $current_user, $wp_roles;

	# Is this the admin interface?
	if (strstr(strtolower($_SERVER['REQUEST_URI']),'/wp-admin/') && !strstr(strtolower($_SERVER['REQUEST_URI']),'async-upload.php') && !strstr(strtolower($_SERVER['REQUEST_URI']),'admin-ajax.php'))
	{
		# get the user level and required level to access admin pages
		$sfblock = sf_get_option('sfblockadmin');
		if ($sfblock['blockadmin'] && !empty($sfblock['blockroles']))
		{
            $role_matches = array_intersect_key($sfblock['blockroles'], array_flip($current_user->roles));
            $access = in_array(1, $role_matches);
			# block admin if required
			$is_moderator = sf_get_member_item($current_user->ID, 'moderator');
			if (!sfc_current_user_can('SPF Manage Options') &&
			    !sfc_current_user_can('SPF Manage Forums') &&
			    !sfc_current_user_can('SPF Manage Components') &&
			    !sfc_current_user_can('SPF Manage User Groups') &&
			    !sfc_current_user_can('SPF Manage Permissions') &&
			    !sfc_current_user_can('SPF Manage Tags') &&
			    !sfc_current_user_can('SPF Manage Users') &&
			    !sfc_current_user_can('SPF Manage Profiles') &&
			    !sfc_current_user_can('SPF Manage Admins') &&
			    !sfc_current_user_can('SPF Manage Toolbox') &&
			    !sfc_current_user_can('SPF Manage Configuration') &&
				!$is_moderator &&
				!$access
			    )
			{
				if ($sfblock['blockprofile'])
				{
					$redirect = SFURL.'profile/';
				} else {
					$redirect = $sfblock['blockredirect'];
				}
				wp_redirect($redirect, 302);
			}
		}
	}
}

# compatability function for php 4 and array_intersect_key
if (!function_exists('array_intersect_key'))
{
    function array_intersect_key ($isec, $arr2)
    {
        $argc = func_num_args();
        for ($i = 1; !empty($isec) && $i < $argc; $i++)
        {
             $arr = func_get_arg($i);
             foreach ($isec as $k => $v)
                 if (!isset($arr[$k])) unset($isec[$k]);
        }

        return $isec;
    }
}
#
# Create canonical URL for AIOSEO
# ------------------------------------------------------------------
function sf_aioseo_canonical_url($url)
{
	global $sfvars, $sfglobals, $ISFORUM, $wp_query;

	if ($ISFORUM)
	{
		sf_populate_query_vars();
        $url = sf_canonical_url();
	} else {
        $post = $wp_query->get_queried_object();
        if (!empty($post->ID) && $sfglobals['canonicalurl'] == false)
        {
      		$sfpostlinking = sf_get_option('sfpostlinking');
            $topic = sf_blog_links_control('read', $post->ID);
            if (!empty($topic) && $sfpostlinking['sflinkurls'] == 2) # point blog post to linked topic?
            {
                $forum_slug = sf_get_forum_slug($topic->forum_id);
                $topic_slug = sf_get_topic_slug($topic->topic_id);
                $url = sf_build_url($forum_slug, $topic_slug, 0, 0);
            }
        }
	}

    $sfglobals['canonicalurl'] = true;

	return $url;
}

#
# Create meta description for AIOSEO
# ------------------------------------------------------------------
function sf_aioseo_description($aioseo_descr)
{
	global $sfglobals, $ISFORUM;

	if ($ISFORUM)
	{
		sf_populate_query_vars();
		sf_setup_page_type();
		$sfglobals['metadescription'] = true;

		$description = sfc_get_metadescription();
		if ($description != '')
		{
			$aioseo_descr = $description;
		}
	}

	return $aioseo_descr;
}

#
# Create meta keywords for AIOSEO
# ------------------------------------------------------------------
function sf_aioseo_keywords($aioseo_keywords)
{
	global $sfglobals, $ISFORUM;

	if ($ISFORUM)
	{
		sf_populate_query_vars();
		sf_setup_page_type();
		$sfglobals['metakeywords'] = true;
		$keywords = sfc_get_metakeywords();
		if ($keywords != '')
		{
			$aioseo_keywords = $keywords;
		}
	}

	return $aioseo_keywords;
}

# ------------------------------------------------------------------
function sf_aioseo_homepage($title)
{
	global $ISFORUM;

	if ($ISFORUM)
	{
		$sfseo = array();
		$sfseo = sf_get_option('sfseo');
		$title = sf_setup_title($title, ' '.$sfseo['sfseo_sep'].' ');
	}
	return $title;
}

function sf_build_sitemap()
{
	global $wpdb;

	$generatorObject = &GoogleSitemapGenerator::GetInstance();
	if ($generatorObject != null)
	{
		$topics = $wpdb->get_results("SELECT topic_id, topic_slug, forum_slug FROM ".SFTOPICS." JOIN ".SFFORUMS." ON ".SFTOPICS.".forum_id = ".SFFORUMS.".forum_id WHERE in_sitemap = 1 ORDER BY ".SFTOPICS.".post_ID DESC");
		if ($topics)
		{
			foreach ($topics as $topic)
			{
				$url = sf_build_url($topic->forum_slug, $topic->topic_slug, 0, 0);
				$time = $wpdb->get_var("SELECT UNIX_TIMESTAMP(post_date) as udate FROM ".SFPOSTS." WHERE topic_id = ".$topic->topic_id." ORDER BY post_id DESC LIMIT 1");
				$generatorObject->AddUrl($url, $time, "daily", 0.5);
			}
		}
	}
}

function sf_mobile_check()
{
    global $SFMOBILE;

    $sfmobile = sf_get_option('sfmobile');
    $browsers = explode(",", trim($sfmobile['browsers']));
    $touch = explode(",", trim($sfmobile['touch']));
	$mobiles = array_merge($browsers, $touch);

	$ismobile = null;
	if (!isset($_SERVER["HTTP_USER_AGENT"]) || (isset($_COOKIE['sf_mobile']) && $_COOKIE['sf_mobile'] == 'false'))
    {
		$ismobile = false;
	}
	else if (isset($_COOKIE['sf_mobile']) && $_COOKIE['sf_mobile'] == 'true') {
		$ismobile = true;
	}

	if (is_null($ismobile) && count($mobiles))
    {
		foreach ($mobiles as $mobile)
        {
			if (!empty($mobile) && strpos($_SERVER["HTTP_USER_AGENT"], trim($mobile)) !== false)
            {
				$ismobile = true;
            	$cookiepath = '/';
				setcookie('sf_mobile', 'true', time() + 300000, $cookiepath, false);
			}
		}
	}

	if (is_null($ismobile)) {
		$ismobile = false;
	}

    $SFMOBILE = $ismobile;
    return;
}

?>