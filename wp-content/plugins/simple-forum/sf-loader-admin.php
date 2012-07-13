<?php
/*
Simple:Press
Admin Header Routines
$LastChangedDate: 2011-06-18 14:03:05 -0700 (Sat, 18 Jun 2011) $
$Rev: 6348 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

global $ISFORUMADMIN, $apage;

# set to current admin page being served
$apage = sf_extract_admin_page();
# = SETUP ADMIN MENU ==========================
function sfa_admin_menu()
{
	global $current_user, $sfglobals, $SFSTATUS;

	if($SFSTATUS == 'ok')
	{
		sf_build_memberdata_cache();

		if (sfc_current_user_can('SPF Manage Options') ||
		    sfc_current_user_can('SPF Manage Forums') ||
		    sfc_current_user_can('SPF Manage Components') ||
		    sfc_current_user_can('SPF Manage User Groups') ||
		    sfc_current_user_can('SPF Manage Permissions') ||
		    sfc_current_user_can('SPF Manage Tags') ||
		    sfc_current_user_can('SPF Manage Users') ||
		    sfc_current_user_can('SPF Manage Profiles') ||
		    sfc_current_user_can('SPF Manage Admins') ||
		    sfc_current_user_can('SPF Manage Toolbox') ||
		    sfc_current_user_can('SPF Manage Configuration') ||
			$sfglobals['member']['moderator'])
		{
            # figure out the parent page for menu
            if (sfc_current_user_can('SPF Manage Forums')) $parent = 'simple-forum/admin/panel-forums/sfa-forums.php';
            else if (sfc_current_user_can('SPF Manage Options')) $parent = 'simple-forum/admin/panel-options/sfa-options.php';
            else if (sfc_current_user_can('SPF Manage Components')) $parent = 'simple-forum/admin/panel-components/sfa-components.php';
            else if (sfc_current_user_can('SPF Manage User Groups')) $parent = 'simple-forum/admin/panel-usergroups/sfa-usergroups.php';
            else if (sfc_current_user_can('SPF Manage Permissions')) $parent = 'simple-forum/admin/panel-permissions/sfa-permissions.php';
            else if (sfc_current_user_can('SPF Manage Users')) $parent = 'simple-forum/admin/panel-users/sfa-users.php';
            else if (sfc_current_user_can('SPF Manage Profiles')) $parent = 'simple-forum/admin/panel-profiles/sfa-profiles.php';
            else if (sfc_current_user_can('SPF Manage Admins')) $parent = 'simple-forum/admin/panel-admins/sfa-admins.php';
            else if (sfc_current_user_can('SPF Manage Tags')) $parent = 'simple-forum/admin/panel-tags/sfa-tags.php';
            else if (sfc_current_user_can('SPF Manage Toolbox')) $parent = 'simple-forum/admin/panel-toolbox/sfa-toolbox.php';
            else if (sfc_current_user_can('SPF Manage Configuration')) $parent = 'simple-forum/admin/panel-config/sfa-config.php';
            else if ($sfglobals['member']['moderator']) $parent = 'simple-forum/admin/panel-admins/sfa-admins.php';

			add_object_page('Simple:Press', esc_attr(__('Forum', 'sforum')), 'read', $parent, '', 'div');

			if (sfc_current_user_can('SPF Manage Forums'))
			{
				add_submenu_page($parent, esc_attr(__('Forums', 'sforum')), esc_attr(__('Forums', 'sforum')), 'read', 'simple-forum/admin/panel-forums/sfa-forums.php');
			}
			if (sfc_current_user_can('SPF Manage Options'))
			{
				add_submenu_page($parent, esc_attr(__('Options', 'sforum')), esc_attr(__('Options', 'sforum')), 'read', 'simple-forum/admin/panel-options/sfa-options.php');
			}
			if (sfc_current_user_can('SPF Manage Components'))
			{
				add_submenu_page($parent, esc_attr(__('Components', 'sforum')), esc_attr(__('Components', 'sforum')), 'read', 'simple-forum/admin/panel-components/sfa-components.php');
			}
			if (sfc_current_user_can('SPF Manage User Groups'))
			{
				add_submenu_page($parent, esc_attr(__('Usergroups', 'sforum')), esc_attr(__('User Groups', 'sforum')), 'read', 'simple-forum/admin/panel-usergroups/sfa-usergroups.php');
			}
			if (sfc_current_user_can('SPF Manage Permissions'))
			{
				add_submenu_page($parent, esc_attr(__('Permissions', 'sforum')), esc_attr(__('Permission Sets', 'sforum')), 'read', 'simple-forum/admin/panel-permissions/sfa-permissions.php');
			}
			if (sfc_current_user_can('SPF Manage Users'))
			{
				add_submenu_page($parent, esc_attr(__('Users', 'sforum')), esc_attr(__('Users', 'sforum')), 'read', 'simple-forum/admin/panel-users/sfa-users.php');
			}
			if (sfc_current_user_can('SPF Manage Profiles'))
			{
				add_submenu_page($parent, esc_attr(__('Profiles', 'sforum')), esc_attr(__('Profiles', 'sforum')), 'read', 'simple-forum/admin/panel-profiles/sfa-profiles.php');
			}
			if (sfc_current_user_can('SPF Manage Admins') || $sfglobals['member']['admin'] || $sfglobals['member']['moderator'])
			{
				add_submenu_page($parent, esc_attr(__('Admins', 'sforum')), esc_attr(__('Admins', 'sforum')), 'read', 'simple-forum/admin/panel-admins/sfa-admins.php');
			}
			if (sfc_current_user_can('SPF Manage Tags'))
			{
				add_submenu_page($parent, esc_attr(__('Tags', 'sforum')), esc_attr(__('Tags', 'sforum')), 'read', 'simple-forum/admin/panel-tags/sfa-tags.php');
			}
			if (sfc_current_user_can('SPF Manage Toolbox'))
			{
				add_submenu_page($parent, esc_attr(__('Toolbox', 'sforum')), esc_attr(__('Toolbox', 'sforum')), 'read', 'simple-forum/admin/panel-toolbox/sfa-toolbox.php');
			}
			if (sfc_current_user_can('SPF Manage Configuration'))
			{
				add_submenu_page($parent, esc_attr(__('Configuration', 'sforum')), esc_attr(__('Configuration', 'sforum')), 'read', 'simple-forum/admin/panel-config/sfa-config.php');
			}
			if (sfc_current_user_can('SPF Manage Options'))
			{
				add_submenu_page($parent, esc_attr(__('Integration', 'sforum')), esc_attr(__('WP Integration', 'sforum')), 'read', 'simple-forum/admin/panel-integration/sfa-integration.php');
			}
		} else if (current_user_can('administrator')) {
    		$parent = 'simple-forum/admin/sfa-notice.php';
			add_object_page('Simple:Press', esc_attr(__('Forum', 'sforum')), 'manage_options', $parent, '', 'div');
			add_submenu_page($parent, __('WP Admin Notice', 'sforum'), __('WP Admin Notice', 'sforum'), 'read', 'simple-forum/admin/sfa-notice.php');
        }
	} else {
		$parent = 'simple-forum/sf-loader-install.php';
		add_object_page('Simple:Press',esc_attr( __("Forum", "sforum")), 'activate_plugins', $parent, '', 'div');

		global $submenu;
		if($SFSTATUS == 'Install')
		{
			add_submenu_page($parent, esc_attr(__("Install Simple:Press", "sforum")), esc_attr(__("Install Simple:Press", "sforum")), 'activate_plugins', $parent);
			$submenu[$parent][1] = array(esc_attr(__('Install Simple:Press', 'spa')), 'xxxxxxx', $parent); # manual dummy entry so next shows
		} else {
			add_submenu_page($parent, esc_attr(__("Upgrade Simple:Press", "sforum")), esc_attr(__("Upgrade Simple:Press", "sforum")), 'activate_plugins', $parent);
			$submenu[$parent][1] = array(esc_attr(__('Upgrade Simple:Press', 'spa')), 'xxxxxxx', $parent); # manual dummy entry so next shows
		}

		# need to register the called page if coming to spf admin page, but then change to the installer so install can be done
		global $plugin_page;
		if (strpos($plugin_page, 'simple-forum') !== false)
		{
			add_submenu_page($parent, '', '', 'read', $plugin_page);
			$plugin_page = $parent;
		}
	}
}

# = SETUP ADMIN JS =========
function sfa_admin_load_js()
{
	global $apage, $SFSTATUS;

	if($SFSTATUS == 'ok')
	{
		if ($apage != 'notice')
		{
			# Do our own thing. Loads the WP jQuery for all versions
			wp_enqueue_script('jquery');
			wp_enqueue_script('jquery.ui', SFWPJSCRIPT.'ui.core.js', array('jquery'));
			wp_enqueue_script('jquery.ui.widget', SFWPJSCRIPT.'ui.widget.js', array('jquery'));
			wp_enqueue_script('jquery.ui.mouse', SFWPJSCRIPT.'ui.mouse.js', array('jquery'));
			wp_enqueue_script('jquey.ui.accordion', SFJSCRIPT.'admin/ui.accordion.js', array('jquery'));
			wp_enqueue_script('jquey.ui.sortable', SFWPJSCRIPT.'ui.sortable.js', array('jquery'));
			wp_enqueue_script('jquery.form', SFWPJSCRIPT.'jquery.form.js', array('jquery'));
			wp_enqueue_script('sfjs.calendar', SFJSCRIPT.'admin/sfa-calendar.js');
			wp_enqueue_script('sfatags', SFJSCRIPT.'admin/sfa-tags.js', array('jquery'));
			wp_enqueue_script('sfajaxupload', SFJSCRIPT.'ajaxupload/ajaxupload.js', array('jquery'));

			# Load checkbox/radio button code if turned on
			if (SF_USE_PRETTY_CBOX)
			{
				wp_enqueue_script('jquery.checkboxes', SFJSCRIPT.'checkboxes/prettyCheckboxes.js', array('jquery'));
			}
		}
		# Load Highslide and SPF Admin js needed on all admin views
		wp_enqueue_script('highslide', SFJSCRIPT.'highslide/highslide.js', '');
		wp_enqueue_script('sfjs', SFJSCRIPT.'admin/sfa-admin.js', '');
		?>
		<script type='text/javascript'>
		var pcbExclusions = new Array(
			"sfcbdummy"
		);
		</script>
		<?php
	} else {
		# Install and Upgrade - use our own jQuery and ui.core
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery.ui', SFWPJSCRIPT.'ui.core.js', array('jquery'));
		wp_enqueue_script('jquery.ui.widget', SFWPJSCRIPT.'ui.widget.js', array('jquery'));
		wp_enqueue_script('jquery.ui.progress', SFJSCRIPT.'install/ui.progressbar.js', array('jquery'));
		wp_enqueue_script('sfjs', SFJSCRIPT.'install/sfa-install.js', '');
	}
	return;
}

# = SETUP ADMIN HEADER =====
function sfa_admin_header()
{
	global $ISFORUMADMIN, $SFSTATUS, $apage, $ACTIVEPANEL, $current_user;

	 ?>
		<link rel="stylesheet" type="text/css" href="<?php echo(SFADMINCSS);?>menu/sf-menu.css" />
	<?php

	if($ISFORUMADMIN == false) return;

	# Compile admin colour array
	$sfadminsettings = sf_get_member_list($current_user->ID, 'admin_options');
	$sfacolours = $sfadminsettings['admin_options']['colors'];
	if (!isset($sfacolours)) $sfacolours = sf_get_option('sfacolours');
	?>
	<link rel="stylesheet" type="text/css" href="<?php echo(SFADMINCSS);?>admin/sf-admin.css" />
	<?php
	if(get_bloginfo('text_direction') == 'rtl')
	{ ?>
	<link rel="stylesheet" type="text/css" href="<?php echo(SFADMINCSS);?>admin/sf-admin-rtl.css" />
	<?php } ?>
	<style type="text/css">
	.sfform-upload-button, .sfform-submit-button, .sfform-panel-button { background-color: <?php echo('#'.$sfacolours['submitbg']); ?> !important; color: <?php echo('#'.$sfacolours['submitbgt']); ?> !important; }
	.sfsidebutton, .sfsidebutton a { background-color: <?php echo('#'.$sfacolours['bbarbg']); ?>; color: <?php echo('#'.$sfacolours['bbarbgt']); ?>; }
	.sfform-panel { background: <?php echo('#'.$sfacolours['formbg']); ?>; color: <?php echo('#'.$sfacolours['formbgt']); ?>; }
	.form-table td.longmessage { color: <?php echo('#'.$sfacolours['formbgt']); ?>; }
	.sffieldset legend { background-color: <?php echo('#'.$sfacolours['panelbg']); ?>; color: <?php echo('#'.$sfacolours['formbgt']); ?>; }
	.sfsubfieldset legend { background-color: <?php echo('#'.$sfacolours['panelsubbg']); ?>; color: <?php echo('#'.$sfacolours['formbgt']); ?>; }
	.subhead { color: <?php echo('#'.$sfacolours['formbgt']); ?> !important; }
	.form-table td { color: <?php echo('#'.$sfacolours['panelbgt']); ?>; }
	.sffieldset { color: <?php echo('#'.$sfacolours['panelbgt']); ?>; background-color: <?php echo('#'.$sfacolours['panelbg']); ?>; }
	.sublabel { color: <?php echo('#'.$sfacolours['panelbgt']); ?> !important; }
	.sfsubfieldset { color: <?php echo('#'.$sfacolours['panelsubbgt']); ?>; background-color: <?php echo('#'.$sfacolours['panelsubbg']); ?>; }
	#sfavataroptions span { background-color: <?php echo('#'.$sfacolours['panelsubbg']); ?>; color: <?php echo('#'.$sfacolours['panelsubbgt']); ?>; }
	.form-table th { background-color: <?php echo('#'.$sfacolours['formtabhead']); ?>; color: <?php echo('#'.$sfacolours['formtabheadt']); ?>; }
	.sfmaintable th, .sfsubtable th, .sfsubsubtable th { background: <?php echo('#'.$sfacolours['tabhead']); ?>; color: <?php echo('#'.$sfacolours['tabheadt']); ?>; }
	.sfmaintable { background-color: <?php echo('#'.$sfacolours['tabrowmain']); ?>; }
	.sfmaintable td { color: <?php echo('#'.$sfacolours['tabrowmaint']); ?>; }
	.sfsubtable, .sfsubsubtable { background-color: <?php echo('#'.$sfacolours['tabrowsub']); ?>; }
	.sfsubtable td, .sfsubsubtable td { color: <?php echo('#'.$sfacolours['tabrowsubt']); ?>; }
	</style>

	<?php
	if($SFSTATUS == 'ok')
	{
	?>
	<link rel="stylesheet" type="text/css" href="<?php echo(SFADMINCSS);?>highslide/highslide.css" />

	<script type="text/javascript">
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

	<?php
		if ($apage != 'notice')
		{ ?>
    		var jspf = jQuery.noConflict();
    		jspf(document).ready(function()
    		{
    			jspf("#sfadminmenu").accordion({
    				autoHeight: false,
    				collapsible: true,
    				active: <?php echo($ACTIVEPANEL[$apage]); ?>
    			});

				jspf(function(jspf){vtip("<?php echo(SFADMINIMAGES.'vtip_arrow.png'); ?>");})

    			<?php if(SF_USE_PRETTY_CBOX) { ?>
    				jspf('input[type=checkbox],input[type=radio]').prettyCheckboxes();
    			<?php } ?>
    		});
		<?php } ?>
	</script>

	<?php
	}
	return;
}

# = ADD FOOTER CREDIT ================
function sfa_admin_footer()
{
	global $ISFORUMADMIN;
	if ($ISFORUMADMIN) echo SFPLUGHOME.' | '.__('Version', 'sforum').' '.SFVERSION.'<br />';
	return;
}

function sf_extract_admin_page()
{
	$apage = strrchr($_SERVER['QUERY_STRING'] , '/');
	if(strpos($apage, '/sfa-', 0) === false) return 'none';
	$apage = substr($apage, 5, 26);
	$apage = reset(explode(".php", $apage));
	return $apage;
}

function sf_add_slider_help($help)
{
	global $ISFORUMADMIN;

	if(!$ISFORUMADMIN) return $help;

	$out.= '<h5>'.__("Simple:Press - Help Options", "sforum").'</h5>';
	$out.= '<div class="metabox-prefs">';
	$out.= '<p>'.sprintf(__("For contextual help with Simple:Press, click on the %s links", "sforum"), __("Help", "sforum")).'<br />';
	$out.= __("For troubleshooting, how-tos, template tags, program hooks and more, use the", "sforum").' <a target="_blank" href="http://wiki.simple-press.com">'.__("Simple:Press Wiki", "sforum").'</a><br />';
	$out.= __("If you cannot find your answer and need extra help, please visit our", "sforum").' <a href="'.SFHOMESITE.'/support-forum">'.__("Support Forums", "sforum").'</a></p>';
	$out.= '</div>';

	return $out;
}

# ------------------------------------------------------------------
# sf_dashboard_setup()
#
# Filter Call
# Sets up the forum advisory in the dashboard
# ------------------------------------------------------------------
function sf_dashboard_setup()
{
	global $sfglobals;

	if($sfglobals['admin']['sfdashboardposts'] || $sfglobals['admin']['sfdashboardstats']);
	{
	    wp_add_dashboard_widget('sf_announce', esc_attr(__('Forums','sforum' )), 'sf_announce');
	}
}

# ------------------------------------------------------------------
# sf_announce()
#
# Filter Call
# Sets up the forum advisory in the dashboard
# ------------------------------------------------------------------
function sf_announce()
{
	global $sfglobals, $current_user, $SFSTATUS;

	sf_initialise_globals();

	if(sf_is_forum_admin($current_user->ID))
	{
		$current_user->forumadmin = true;
	}

	$out='';

	# check we have an installed version
	if($SFSTATUS != 'ok')
	{
		$out.= '<div style="border: 1px solid #eeeeee; padding: 10px; font-weight: bold;">'."\n";
		$out.= '<img class="sfalignleft" src="'.SFRESOURCES.'information.png" alt="" />'."\n";
		$out.= '<p>&nbsp;&nbsp;'.__("The forum is temporarily unavailable while being upgraded to a new version", "sforum").'</p>';

		if ($current_user->forumadmin)
		{
			$out.= '&nbsp;&nbsp;<a style="text-decoration: underline;" href="'.SFADMINFORUM.'">'.__("Perform Upgrade", "sforum").'</a>';
		}
		$out.= '</div>';
		echo $out;
		return;
	}

	$out.= '<div id="sf-dashboard">';

	# New/Unread Admin Post List
	if($sfglobals['admin']['sfdashboardposts'])
	{
        # if admin, get unread admin queue otherwise return unread user list
        if ($current_user->forumadmin)
        {
            $unreads = sf_get_unread_forums();
			$out.= '<p>'.__("New Forum Posts", "sforum").'</p>';
        } else {
            $unreads = sf_get_users_new_post_list($sfglobals['display']['forums']['newcount']);
			$out.= '<p>'.__("New/Recently Updated Topics", "sforum").'</p>';
        }
		if($unreads)
		{
			$out.='<table class="sfdashtable">';
			foreach($unreads as $unread)
			{
				$out.='<tr>';
                if ($current_user->forumadmin)
                {
    				if($unread->post_count == 1)
    				{
    					$mess = sprintf(__("There is %s new post", "sforum"), $unread->post_count);
    				} else {
    					$mess = sprintf(__("There are %s new posts", "sforum"), $unread->post_count);
    				}
				    $out.= '<td>'.$mess." ".__("in the forum topic", "sforum").'</td><td>'.sf_get_topic_url_dashboard(sf_get_forum_slug($unread->forum_id), sf_get_topic_slug($unread->topic_id), $unread->post_id)."</td>";
                } else {
 				    $out.= '<td>'.sf_get_topic_url_dashboard(sf_get_forum_slug($unread->forum_id), sf_get_topic_slug($unread->topic_id), $unread->post_id)."</td>";
                }
				$out.='</tr>';
			}
			$out.='</table>';
		} else {
			$out.='<p>'. __("There are no new forum posts", "sforum")."</p>";
		}

        if ($current_user->forumadmin)
        {
    		$waiting = sf_get_awaiting_approval();
    		if($waiting == 1)
    		{
    			$out.= '<table  class="sfdashtable><tr><td>'. __("There is 1 post awaiting approval", "sforum")."</td></tr></table>";
    		}
    		if($waiting > 1)
    		{
    			$out.= '<table class="sfdashtable><tr><td>'.__("There are", "sforum").' '.$waiting.' '.__("posts awaiting approval", "sforum").".</td></tr></table>";
    		}
        }
	}

	if($sfglobals['admin']['sfdashboardstats'])
	{
		include_once (SF_PLUGIN_DIR.'/forum/sf-page-components.php');
		$out.= '<br /><table class="sfdashtable">'."\n";
		$out.= '<tr>'."\n";
		$out.= sf_render_online_stats(true);
		$out.= sf_render_forum_stats(true);
		$stats = sf_get_post_stats();
		$out.= sf_render_member_stats($stats, true);
		$out.= sf_render_newusers();
		$out.= sf_render_ownership($stats, true);
		$out.= '</table><br />'."\n";
	}
	$out.='<p><a href="'.esc_url(sf_get_option('sfpermalink')).'">'.esc_attr(__("Go To Forum", "sforum")).'</a></p>';
	$out.= '</div>';
	echo($out);

	return;
}

# ------------------------------------------------------------------
# sf_get_topic_url_dashboard()
#
# Builds a new post url for the dashboard notification section
#	$forumslug:		forum slug for url
#	$topicslug:		topic slug for url
# ------------------------------------------------------------------
function sf_get_topic_url_dashboard($forumslug, $topicslug, $postid)
{
	$out = '<a href="'.sf_build_url($forumslug, $topicslug, 0, $postid).'"><img src="'. SFRESOURCES .'announcenew.png" alt="" />&nbsp;&nbsp;'.sf_get_topic_name($topicslug).'</a>'."\n";

	return $out;
}

# = PLUGIN VERSION CHECK ON PLUGINS PAGE ======
function sfa_check_plugin_version($plugin)
{
	global $SFSTATUS;

 	if($plugin == 'simple-forum/sf-control.php')
 	{
 		$msg='';

		# get wp admin screen type
		$screen = get_current_screen();

		if(sf_get_option('sfuninstall'))
		{
			if (!$screen->is_network) $msg = '<p style="color:red;">'.__("Simple:Press is READY TO BE REMOVED. When you deactivate it ALL DATA WILL BE DELETED", "sforum").'</p>';

		} elseif ($SFSTATUS == 'Upgrade') {
 			if (!$screen->is_network) $msg = __("Select the Forum Menu to complete the upgrade of your Simple:Press", "sforum");
 		} else {
			$checkfile = SFVERCHECK;

			$vcheck = wp_remote_fopen($checkfile);
			if($vcheck)
			{
				$installed_version = sf_get_option('sfversion');
				$installed_build = sf_get_option('sfbuild');

				if(empty($installed_version)) return;

				$status = explode('@', $vcheck);
				$home_version = $status[1];
				$home_build   = $status[3];
				$home_message = $status[5];

				if((version_compare($home_version, $installed_version, '>') == 1) || (version_compare($home_build, $installed_build, '>') == 1))
				{
					$msg = __("Latest version available:", "sforum").' <strong>'.$home_version.'</strong> - '.__("Build:", "sforum").' <strong>'.$home_build.'</strong> - '.$home_message;
					$msg.= __("For details and to download please visit", "sforum").': '.SFPLUGHOME;
					$msg.= ' ('.__("Please Note: Automatic Upgrade is not available", "sforum").')';
				}
			}
		}
		if ($msg) echo '<td colspan="3" class="plugin-update colspanchange"><div class="update-message">'.$msg.'</div></td>';
	}
	return;
}

# = PLUGIN LNK STRIP ON PLUGINS PAGE ======
function sf_add_plugin_action($links, $plugin)
{
	global $SFSTATUS;

	if($plugin == 'simple-forum/sf-control.php')
	{
		if($SFSTATUS != 'ok')
		{
			# Install or Upgrade
			$actionlink = '<a href="'.admin_url('admin.php?page=simple-forum/sf-loader-install.php').'">'.__($SFSTATUS, 'sforum').'</a>';
			array_unshift( $links, $actionlink );
		} else {
			# Uninstall
			if(sf_get_option('sfuninstall') == false)
			{
				$param['spf']='uninstall';
				$passURL = add_query_arg($param, $_SERVER['REQUEST_URI']);
				$msg=sprintf('Are You Sure? %sThis option will REMOVE ALL FORUM DATA %safter deactivating Simple:Press %sPress OK to prepare for data removal', '\n\n', '\n', '\n\n');
				$actionlink = '<a href="javascript: if(confirm(\''.$msg.'\')) {window.location=\''.$passURL.'\';}">'.__('Uninstall', 'sforum').'</a>';
				array_unshift( $links, $actionlink );
			}
		}
	}

	return $links;
}

# Set the uninstall flag if required
function sf_check_removal()
{
	if(isset($_GET['spf']) && $_GET['spf'] == 'uninstall')
	{
		sf_update_option('sfuninstall', true);
	}
}

?>