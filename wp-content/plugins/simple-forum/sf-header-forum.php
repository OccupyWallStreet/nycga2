<?php
/*
Simple:Press
Start Up Functions to support the forum
$LastChangedDate: 2011-03-05 07:42:11 -0700 (Sat, 05 Mar 2011) $
$Rev: 5631 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

# ------------------------------------------------------------------
# sf_load_front_js()
#
# Enqueue's necessary javascript and inline header script
# ------------------------------------------------------------------
function sf_load_front_js()
{
	global $sfvars;

	# set up the sfvars array
	sf_populate_query_vars();

	# determine page view type
	sf_setup_page_type();

	if(sf_get_option('sfscriptfoot') ? $footer = true : $footer = false);

	# Load up the javascript files
	wp_register_script('jquery', SFWPJSCRIPT.'jquery.js', false, false, $footer);
	wp_enqueue_script('jquery');

	if(SF_USE_PRETTY_CBOX)
	{
		wp_enqueue_script('jquery.checkboxes', SFJSCRIPT.'checkboxes/prettyCheckboxes.js', array('jquery'), false, $footer);
	}

	wp_enqueue_script('highslide', SFJSCRIPT.'highslide/highslide.js', false, false, $footer);

	wp_enqueue_script('sftags', SFJSCRIPT.'tags/sf-tags.js', array('jquery'), false, $footer);
	wp_localize_script('sftags', 'sfSettings', array('url' => SFHOMEURL));

	wp_enqueue_script('sfprint', SFJSCRIPT.'print/jquery.jqprint.js', array('jquery'), false, $footer);

	if($sfvars['pageview'] == 'topic')
	{
		wp_enqueue_script('sfcookie', SFJSCRIPT.'jquery/jcookie.js', array('jquery'), false, $footer);
	}

    # hack for pm detection since sfvars not set up yet
	$pmstuff = explode('/', $_SERVER['QUERY_STRING']);
    $pmpage = get_query_var('sf_pm');
    if (isset($_GET['pmaction'])) $pmoldpage = $_GET['pmaction'];
 	if (!empty($pmpage) || !empty($pmoldpage) || (isset($pmstuff[1]) && $pmstuff[1] == 'private-messaging'))
	{
		wp_enqueue_script('sfautoc', SFJSCRIPT.'autocomplete/jquery.autocomplete.js', array('jquery'), false, $footer);
		wp_enqueue_script('sfsplitter', SFJSCRIPT.'jquery/splitter.js', array('jquery'), false, $footer);
		wp_enqueue_script('sfcookie', SFJSCRIPT.'jquery/jcookie.js', array('jquery'), false, $footer);
		wp_enqueue_script('sfpmjs', SFJSCRIPT.'messaging/sf-messaging.js', array('jquery'), false, $footer);
	}

	# syntax Highlighting
	$sfsyntax = sf_get_option('sfsyntax');
	if($sfsyntax['sfsyntaxforum'] == true)
	{
		wp_enqueue_script('sfsyntax', SFJSCRIPT.'syntax/jquery.syntax.js', array('jquery'), false, $footer);
		wp_enqueue_script('sfsyntaxcache', SFJSCRIPT.'syntax/jquery.syntax.cache.js', array('jquery'), false, $footer);
	}

	# Special case: if profile edit load uploader support
	if($sfvars['pageview'] == 'profileedit')
	{
		wp_enqueue_script('sfupload', SFJSCRIPT.'ajaxupload/ajaxupload.js', array('jquery'), false, $footer);
		wp_enqueue_script('sfform', SFWPJSCRIPT.'jquery.form.js', array('jquery'), false, $footer);
	}

	wp_enqueue_script('spf', SFJSCRIPT.'forum/sf-forum.js', array('jquery'), false, $footer);

	if(SF_USE_PRETTY_CBOX)
	{
		?>
		<script type='text/javascript'>
		var pcbExclusions = new Array(
		<?php
			$exc = sf_get_option('sfcbexclusions');
			if($exc)
			{
				$exc = rtrim($exc, ",");
				$exclist = explode(",", $exc);
				foreach($exclist as $item)
				{
					echo '"'.trim($item).'",';
				}
			}
			echo '"sfcbdummy"'."\n";
		?>
		);
		</script>
		<?php
	}
	return;
}

# ------------------------------------------------------------------
# sf_setup_header()
#
# Constructs the header for the forum - Javascript and CSS
# ------------------------------------------------------------------
function sf_setup_header()
{
	global $wp_query, $current_user, $wp_rewrite, $sfglobals, $sfvars, $SFSTATUS, $SFMOBILE;

	# The CSS is being set early in case we have to bow out quickly due to
	# the forum needing to be ugraded. This is to ensure that
	# this is the very FIRST thing to happen in the header
	echo '<link rel="stylesheet" type="text/css" href="' . SFSKINCSS .'" />' . "\n";
	if(get_bloginfo('text_direction') == 'rtl')
	{
		echo '<link rel="stylesheet" type="text/css" href="' . SFCSSRTL . '" />' . "\n";
	}

	# So - check if it needs to be upgraded...
	if($SFSTATUS != 'ok')
	{
		return sf_forum_unavailable();
	}

	# If page is password protected, ensure it matches before starting
	if (!empty($wp_query->post->post_password))
	{
		if ($_COOKIE['wp-postpass_'.COOKIEHASH] != $wp_query->post->post_password)
		{
			return;
		}
	}

	while ($x = has_filter('the_content', 'wpautop'))
	{
		remove_filter('the_content', 'wpautop', $x);
	}
	remove_filter('the_content', 'convert_smilies');

	$value = SFSIZE;
	if(!empty($value))
	{
		$value=trim($value, '%');
		if(intval($value) != 0)
		{
			echo '<style type="text/css">';
			echo '#sforum {font-size: '.$value.'%; }';
			echo '</style>'."\n";
		}
	}

	# --------------------------------------------------------
	# now to start populatng all of the variables we need

	# set up the sfvars array - should be already done but no ham checking
	sf_populate_query_vars();

	# filemanager - hive off sfvars for fm to use
	if(($sfvars['pageview']=='profileedit' && $current_user->sfuploadsig) ||
		(($sfvars['pageview']=='forum' || $sfvars['pageview']=='topic') &&
		($current_user->sfuploadimg || $current_user->sfuploadmedia || $current_user->sfuploadfile)));
	{
		$key = $current_user->ID.'keys';
		update_sfsetting($key, serialize($sfvars));
	}

	# determine page view type -  - should be already done but no ham checking
	sf_setup_page_type();

	# do meta stuff
	sf_setup_meta_tags();

    # load page specific css
	if ($sfvars['pageview'] == 'topic')
	{
		echo '<link rel="stylesheet" type="text/css" href="'.SFPOSTCSS.'" />' . "\n";

		# if setting for post content width apply word-wrap
		if($sfvars['postwidth'] > 0)
		{
			?>
			<style type="text/css">
			.sfpostcontent {
				max-width: <?php echo($sfvars['postwidth']); ?>px !important; }
			.sfpostcontent p, .sfpostcontent pre, .sfpostcontent blockquote, .sfpostcontent table {
				max-width: <?php echo($sfvars['postwidth']); ?>px !important;
				text-wrap: normal;
				word-wrap: break-word; }
			.sfpostcontent .sfcode table.syntax {
				max-width: <?php echo($sfvars['postwidth']-5); ?>px !important;
				text-wrap: normal;
				word-wrap: break-word; }
			.sfpostcontent .sfcode table.syntax tr.line {
				max-width: <?php echo($sfvars['postwidth']-2); ?>px !important;
				text-wrap: normal;
				word-wrap: break-word; }
			.sfpostcontent .sfcode table.syntax td.number {
				max-width: 15px;
				min-width: 15px; }
			.sfpostcontent .sfcode table.syntax td.source span {
				max-width: <?php echo($sfvars['postwidth']-18); ?>px !important;
				text-wrap: normal;
				word-wrap: break-word; }
			</style>
			<?php
		}
	}

	if ($sfvars['pageview'] == 'pm')
	   echo '<link rel="stylesheet" type="text/css" href="'.SFPMCSS.'" />' . "\n";

	if ($sfvars['pageview'] == 'profileedit' || $sfvars['pageview'] == 'profileshow')
	   echo '<link rel="stylesheet" type="text/css" href="'.SFPROFILECSS.'" />' . "\n";

	# --------------------------------------------------------
	# Set up the rest of the header info
	?>
	<link rel="stylesheet" type="text/css" href="<?php echo(SFADMINCSS); ?>highslide/highslide.css" />
	<?php

	# load HTML or bbCode editor if required. TinyMCE can be loaded in footer
	if(strpos("forum topic pm profileedit", $sfvars['pageview']) !== false)
	{
		if ($SFMOBILE)
		{
			$sfglobals['editor']['sfeditor'] == PLAIN;
		} else {
			switch($sfglobals['editor']['sfeditor'])
			{
				case HTML:
					include_once(SFEDITORDIR."html/sf-htmlinit.php");
					break;
				case BBCODE:
					include_once(SFEDITORDIR."bbcode/sf-bbcodeinit.php");
					break;
			}
		}
	}
}

# ------------------------------------------------------------------
# sf_setup_footer()
#
# Constructs the footer for the forum - Javascript
# ------------------------------------------------------------------

function sf_setup_footer()
{
	global $sfvars, $sfglobals, $current_user, $SFMOBILE;

	# wait for page load and run JS inits
	?>
	<script type="text/javascript">
		var jspf = jQuery.noConflict();
		jspf(document).ready(function() {

			<?php
			# Checkboxes/radio buttons
			if(SF_USE_PRETTY_CBOX) { ?>
				jspf("input[type=checkbox],input[type=radio]").prettyCheckboxes();
			<?php } ?>

			<?php
			# syntax Highlighting - optional maybe and may need loading outside of SP
			$sfsyntax = sf_get_option('sfsyntax');
			if($sfsyntax['sfsyntaxforum'] == true) { ?>
				Syntax.root = "<?php echo SFJSCRIPT.'syntax/'; ?>";
				jspf.syntax({layout: 'table', replace: true});
			<?php } ?>

			<?php
			# Sets cookies with content and paragraph widths
			$docookie = true;
			$sfpostwrap = array();
			$sfpostwrap = sf_get_option('sfpostwrap');
			if($sfpostwrap['postwrap'] == false) $docookie = false;
			if($sfvars['postwidth'] > 0) $docookie = false;
			if($sfvars['displaymode'] == 'edit') $docookie = false;
			if($sfvars['pageview'] != 'topic') $docookie = false;
			if($current_user->forumadmin == false) $docookie = false;

			if($docookie) { ?>
				var c = jspf(".sfpostcontent").width();
				var p = jspf(".sfpostcontent p").width();
				if(c && p) {
					jspf.cookie('c_width', c, { path: '/' });
					jspf.cookie('p_width', p, { path: '/' });
				}
			<?php } ?>

			<?php
			# set up Highslide...
			?>
				hs.graphicsDir = "<?php echo(SFJSCRIPT); ?>highslide/support/";
				hs.outlineType = "rounded-white";
				hs.outlineWhileAnimating = true;
				hs.cacheAjax = false;
				hs.showCredits = false;
				hs.lang = {
					cssDirection : '<?php bloginfo('text_direction'); ?>',
					closeText : '',
					closeTitle : '<?php echo esc_js(__("Close", "sforum")); ?>',
					moveText  : '',
					moveTitle : '<?php echo esc_js(__("Move", "sforum")); ?>',
					loadingText  : '<?php echo esc_js(__("Loading", "sforum")); ?>'
				};

				/* pre-load wait image */
				waitImage = new Image(27,27);
				waitImage.src = '<?php echo(SFRESOURCES."wait.png"); ?>';

			<?php
			# turn on auto update of required
			$sfauto=array();
			$sfauto=sf_get_option('sfauto');
			if($sfauto['sfautoupdate']) {
				$timer = ($sfauto['sfautotime'] * 1000);
			    $url = SFHOMEURL."index.php?sf_ahah=autoupdate";
				?>
				sfjAutoUpdate("<?php echo($url); ?>", "<?php echo($timer); ?>");
			<?php } ?>

		});
	</script>
	<?php

	# load editor if required
	if(strpos("forum topic pm profileedit", $sfvars['pageview']) !== false)
	{
		if ($SFMOBILE)
		{
			$sfglobals['editor']['sfeditor'] == PLAIN;
		} else {
			switch($sfglobals['editor']['sfeditor'])
			{
				case RICHTEXT:
					include_once(SFEDITORDIR."tinymce/sf-tinyinit.php");
					break;
			}
		}
	}
}

# ------------------------------------------------------------------
# sf_populate_query_vars()
#
# Populate the forum  query variables from the URL
# ------------------------------------------------------------------
function sf_populate_query_vars()
{
	global $sfvars, $sfglobals, $wp_rewrite;

	if ($sfglobals['queryvarsdone'] == true) return;

	# load query vars

	$sfglobals['queryvarsdone'] = true;

	$sfvars['error'] = false;
	$sfvars['forumid'] = 0;
	$sfvars['topicid'] = 0;

	# Special var for post content width if being used
	$sfvars['postwidth'] = 0;
	$sfpostwrap = array();
	$sfpostwrap = sf_get_option('sfpostwrap');
	if($sfpostwrap['postwrap'])
	{
		$sfvars['postwidth'] = $sfpostwrap['postwidth'];

		# if not set then try and get from cookie
		if($sfvars['postwidth'] == false)
		{
			if(isset($_COOKIE["p_width"]) && isset($_COOKIE["c_width"]))
			{
				$c_width = $_COOKIE["c_width"];
				$p_width = $_COOKIE["p_width"];
				if($p_width > $c_width ? $w = ($c_width-80) : $w = $p_width);
				$sfpostwrap['postwidth'] = $w;
				sf_update_option('sfpostwrap', $sfpostwrap);
				$sfvars['postwidth'] = $w;
			}
		}
	}

	# We can check to see if the url is a pre V3 url
	# this checks for numeric value. Fine as long as
	# someone doesn't name their forum with simply a number!
	if((isset($_GET['forum'])) && (is_numeric($_GET['forum'])))
	{
		# suggests an old url
		$sfvars['forumslug'] = sf_get_forum_slug(sf_esc_int($_GET['forum']));
		if(isset($_GET['topic'])) $sfvars['topicslug'] = sf_get_topic_slug(sf_esc_int($_GET['topic']));
		sf_populate_support_vars();
		return;
	}

	# if V3 link and user has permalinks
	if($wp_rewrite->using_permalinks())
	{
		# post V3 permalinks
		# using permalinks so get the values from the query vars

		$sfvars['forumslug'] = sf_esc_str(get_query_var('sf_forum'));
		if(empty($sfvars['forumslug']) && isset($_GET['search']))
		{
			$sfvars['forumslug']=sf_esc_str($_GET['forum']);
		}
		$sfvars['topicslug'] = sf_esc_str(get_query_var('sf_topic'));
		$sfvars['profile'] = sf_esc_str(get_query_var('sf_profile'));
		$sfvars['member'] = sf_esc_str(get_query_var('sf_member'));
		$sfvars['list'] = sf_esc_str(get_query_var('sf_list'));
		$sfvars['policy'] = sf_esc_str(get_query_var('sf_policy'));
		$sfvars['newposts'] = sf_esc_str(get_query_var('sf_newposts'));
		$sfvars['pm'] = sf_esc_str(get_query_var('sf_pm'));
		$sfvars['box'] = sf_esc_str(get_query_var('sf_box'));
		if (get_query_var('sf_page') != '')
		{
			$sfvars['page'] = sf_esc_int(get_query_var('sf_page'));
		}

		sf_populate_support_vars();
		return;
	} else {
		# post V3 but using default
		# Not using permalinks so we need to parse the query string from the url and do it ourselves

		$stuff=array();
		$stuff=explode('/', $_SERVER['QUERY_STRING']);

		# deal with non-standard cases first
		if (isset($_GET['search']))
		{
			sf_build_search_vars($stuff);
		} else {
			sf_build_standard_vars($stuff);
		}

		sf_populate_support_vars();

		if(empty($sfvars['forumid'])) $sfvars['forumid']=0;
		if(empty($sfvars['topicid'])) $sfvars['topicid']=0;
		if(empty($sfvars['profile'])) $sfvars['profile']=0;
		if(empty($sfvars['member'])) $sfvars['member']=0;
		if(empty($sfvars['list'])) $sfvars['list']=0;
		if(empty($sfvars['policy'])) $sfvars['policy']=0;
		if(empty($sfvars['newposts'])) $sfvars['newposts']=0;
		if(empty($sfvars['pm'])) $sfvars['pm']=0;
		if(empty($sfvars['box'])) $sfvars['box']=0;
		return;
	}
}

# ------------------------------------------------------------------
# sf_populate_support_vars()
#
# Query Variables support routine
# ------------------------------------------------------------------
function sf_populate_support_vars()
{
	global $sfvars;

	# Populate the rest of sfvars

	if(empty($sfvars['page']))
	{
		$sfvars['page'] = 1;
	}
	if(!empty($sfvars['forumslug']) && $sfvars['forumslug'] != 'all')
	{
		$record = sf_get_forum_record_from_slug($sfvars['forumslug']);
		$sfvars['forumid'] = $record->forum_id;
		if(empty($sfvars['forumid'])) $sfvars['forumid']=0;
		$sfvars['forumname'] = $record->forum_name;

		# Is it a subforum?
		$forumparent = $record->parent;

		while($forumparent > 0)
		{
			$parent = sf_get_forum_record($forumparent);
			$sfvars['parentforumid'][] = $forumparent;
			$sfvars['parentforumslug'][] = $parent->forum_slug;
			$sfvars['parentforumname'][] = $parent->forum_name;
			$forumparent = $parent->parent;
		}
	}
	if(!empty($sfvars['topicslug']))
	{
		$record = sf_get_topic_record_from_slug($sfvars['topicslug']);
		$sfvars['topicid'] = $record->topic_id;
		if(empty($sfvars['topicid'])) $sfvars['topicid']=0;
		$sfvars['topicname'] = $record->topic_name;
	}

	# Add Search Vars
	if(isset($_GET['search']))
	{
		if($_GET['search'] != '') $sfvars['searchpage'] = intval($_GET['search']);
		$sfvars['searchpage'] = sf_esc_int($sfvars['searchpage']);

		if(isset($_GET['type']) ? $sfvars['searchtype'] = intval($_GET['type']) : $sfvars['searchtype'] = 1);
		$sfvars['searchtype'] = sf_esc_int($sfvars['searchtype']);
		if($sfvars['searchtype'] == 0 || empty($sfvars['searchtype'])) $sfvars['searchtype'] =1;

		if(isset($_GET['include']) ? $sfvars['searchinclude'] = intval($_GET['include']) : $sfvars['searchinclude'] = 1);
		$sfvars['searchinclude'] = sf_esc_int($sfvars['searchinclude']);
		if($sfvars['searchinclude'] == 0 || empty($sfvars['searchinclude'])) $sfvars['searchinclude'] =1;

		if(isset($_GET['value']) ? $sfvars['searchvalue'] = sf_filter_save_nohtml(urldecode($_GET['value'])) : $sfvars['searchvalue'] = '');

		# if 'type' 6,8,9 then value must be integer
		if($sfvars['searchtype'] > 5 && isset($_GET['value']))
		{
			$sfvars['searchvalue'] = sf_esc_int(intval($sfvars['searchvalue']));
		} else {
			$sfvars['searchvalue'] = stripslashes($sfvars['searchvalue']);
		}

		$sfvars['searchvalue'] = sf_filter_table_prefix($sfvars['searchvalue']);

		if(empty($sfvars['searchvalue']))
		{
			$sfvars['searchpage'] = 0;
			$sfvars['searchtype'] = 0;
			$sfvars['searchinclude'] = 0;
			update_sfnotice('sfmessage', '1@'.__("Invalid Search Query", "sforum"));
		}
	} else {
		$sfvars['searchpage'] = 0;
	}
	$sfvars['searchresults']=0;

	return;
}

# ------------------------------------------------------------------
# sf_build_search_vars()
#
# Query Variables support routine
# ------------------------------------------------------------------
function sf_build_search_vars($stuff)
{
	global $sfvars;

	if(isset($_GET['forum']))
	{
		# means searching all
		$sfvars['forumslug'] = sf_esc_str($_GET['forum']);
	} else {
		# searching single forum
		if(!empty($stuff[1]))
		{
			$sfvars['forumslug'] = $stuff[1];
		}

		# (2) topic
		if(!empty($stuff[2]))
		{
			$parts = explode("&", $stuff[2]);
			$sfvars['topicslug'] = $parts[0];
		}
	}
	return;
}

# ------------------------------------------------------------------
# sf_build_standard_vars()
#
# Query Variables support routine
# ------------------------------------------------------------------
function sf_build_standard_vars($stuff)
{
	global $sfvars, $current_user;

	# (1) forum first
	if(!empty($stuff[1]))
	{
        if ($stuff[1] == 'profile')
        {
            if ($stuff[2] == 'permissions' || $stuff[2] == 'buddies') {
                $sfvars['profile'] = sf_esc_str($stuff[2]);
            } else {
                if (empty($stuff[2]))
                {
                    $sfvars['member'] = urlencode($current_user->login_name);
                    $sfvars['profile'] = 'edit';
                } else if (empty ($stuff[3])) {
                    $sfvars['member'] = sf_esc_str($stuff[2]);
                    $sfvars['profile'] = 'show';
                } else {
                    $sfvars['member'] = sf_esc_str($stuff[2]);
                    $sfvars['profile'] = 'edit';
                }
            }
        } else if ($stuff[1] == 'members') {
            $sfvars['list'] = 'members';
    		if (preg_match("/page-(\d+)/", $stuff[2], $matches))
    		{
    			$sfvars['page'] = intval($matches[1]);
    		}
        } else if ($stuff[1] == 'newposts') {
            $sfvars['newposts'] = 'all';
        } else if ($stuff[1] == 'policy') {
            $sfvars['policy'] = 'show';
        } else if ($stuff[1] == 'private-messaging') {
            if ($stuff[2] == 'send')
            {
                $sfvars['pm'] = $stuff[2];
                $sfvars['box'] = 'inbox';
                $sfvars['member'] = $stuff[3];
            } else {
                $sfvars['box'] = $stuff[2];
                $sfvars['pm'] = $stuff[2];
            }
        } else {
    		$substuff = explode('&', $stuff[1]);
            $sfvars['forumslug'] = $substuff[0];

        	# (2) topic or page?
        	if(!empty($stuff[2]))
        	{
        		$matches = array();
        		if(preg_match("/page-(\d+)/", $stuff[2], $matches))
        		{
        			$sfvars['page'] = intval($matches[1]);
        		} else {
        			$substuff = explode('&', $stuff[2]);
        			$sfvars['topicslug'] = $substuff[0];
        		}
        	}

        	# (3) if here must be page
        	if(!empty($stuff[3]))
        	{
        		if(preg_match("/page-(\d+)/", $stuff[3], $matches))
        		{
        			$sfvars['page'] = intval($matches[1]);
        		}
        	}
        }
	}

	return;
}

function sf_setup_page_type()
{
	global $sfvars, $sfglobals, $current_user, $CACHE;

	if (isset($sfglobals['pagetypedone']) && $sfglobals['pagetypedone'] == true) return;
	$sfglobals['pagetypedone'] = true;

	sf_initialise_globals($sfvars['forumid']);

	# Maybe a profile edit or first time logged in?
	# If user has made no posts yet optionaly load the profile form
    $pageview = '';
	$sfvars['newuser'] = false;
	if ($current_user->member)
	{
		if ($sfglobals['member']['posts'] == -1 || empty($current_user->user_email))
        {
        	# Add to new user list
    		sf_push_newuser($current_user->ID, $current_user->display_name);
    		sf_update_member_item($current_user->ID, 'posts', 0);

        	$sfprofile = sf_get_option('sfprofile');
            if ($sfprofile['firstvisit'] || empty($current_user->user_email))
            {
    			$sfprofile = sf_get_option('sfprofile');
    			$sfvars['newuser'] = $sfprofile['firstvisit'];
                $sfvars['member'] = urlencode($current_user->login_name);
                $pageview = 'profileedit';
                $sfvars['forumslug'] = '';
                $sfvars['topicslug'] = '';
            }
		}
    }

    if ($pageview == '')
    {
        if (!empty($sfvars['forumslug'])) {
    		$pageview = 'forum';
    	} else if (!empty($sfvars['profile'])) {
    		if ($sfvars['profile'] == 'edit') $pageview = 'profileedit';
    		if ($sfvars['profile'] == 'show') $pageview = 'profileshow';
    		if ($sfvars['profile'] == 'permissions') $pageview = 'permissions';
    		if ($sfvars['profile'] == 'buddies') $pageview = 'buddies';
    	} else if (!empty($sfvars['newposts'])) {
    		$pageview = 'newposts';
    	} else if (!empty($sfvars['list'])) {
    		$pageview = 'list';
    	} else if (!empty($sfvars['policy'])) {
    		$pageview = 'policy';
    	} else if (!empty($sfvars['pm'])) {
    		$pageview = 'pm';
    	} else {
    		$pageview = 'group';
    		# Check if single forum only is on
    		if (isset($sfglobals['display']['forums']['singleforum']) && $sfglobals['display']['forums']['singleforum'])
    		{
    			$fid = sf_single_forum_user();
    			if ($fid)
    			{
    				$cforum = sf_get_forum_record($fid);
    				$sfvars['forumid'] = $fid;
    				$sfvars['forumslug'] = $cforum->forum_slug;
    				$sfvars['forumname'] = $cforum->forum_name;
    				$CACHE = '';
    				sf_initialise_globals($sfvars['forumid']);
    				$pageview = 'forum';
    			}
    		}
    	}

    	if (!empty($sfvars['topicslug'])) $pageview = 'topic';
    }
	$sfvars['pageview'] = $pageview;
}

function sf_setup_meta_tags()
{
	global $sfglobals;

	if (empty($sfglobals['metadescription']))
	{
		$description = sfc_get_metadescription();
		if ($description != '')
		{
			$description = str_replace('"', '', $description);
			echo '<meta name="description" content="'.$description.'" />'."\n";
		}
	}

	if (empty($sfglobals['metakeywords']))
	{
		$keywords = sfc_get_metakeywords();
		if ($keywords != '')
		{
			$keywords = str_replace('"', '', $keywords);
			echo '<meta name="keywords" content="'.$keywords.'" />'."\n";
		}
	}

	if (empty($sfglobals['canonicalurl']))
	{
		# output the canonical url
		$url = sf_canonical_url();
		echo '<link rel="canonical" href="'.$url.'" />'."\n";
	}
}

?>