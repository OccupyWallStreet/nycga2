<?php
/*
Simple:Press
Admin Components General Support Functions
$LastChangedDate: 2010-11-11 17:51:01 -0700 (Thu, 11 Nov 2010) $
$Rev: 4917 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_get_editor_data()
{
	$sfcomps = array();
	$sfeditor = array();

	$sfeditor = sf_get_option('sfeditor');
	$sfcomps['sfeditor'] = $sfeditor['sfeditor'];
	$sfcomps['sfusereditor'] = $sfeditor['sfusereditor'];
	$sfcomps['sfrejectformat'] = $sfeditor['sfrejectformat'];
	$sfcomps['sftmcontentCSS'] = $sfeditor['sftmcontentCSS'];
	$sfcomps['sftmuiCSS'] = $sfeditor['sftmuiCSS'];
	$sfcomps['sftmdialogCSS'] = $sfeditor['sftmdialogCSS'];
	$sfcomps['SFhtmlCSS'] = $sfeditor['SFhtmlCSS'];
	$sfcomps['SFbbCSS'] = $sfeditor['SFbbCSS'];
	$sfcomps['sflang'] = $sfeditor['sflang'];
	$sfcomps['sfrtl'] =  $sfeditor['sfrtl'];
	$sfcomps['sfrelative'] = $sfeditor['sfrelative'];

	return $sfcomps;
}

function sfa_get_toolbar_extras()
{
	$extras = sf_get_option('sftbextras');
	return $extras;
}

function sfa_get_smileys_data()
{
	$sfcomps = array();

	$sfsmileys = array();
	$sfsmileys = sf_get_option('sfsmileys');
	$sfcomps['sfsmallow'] = $sfsmileys['sfsmallow'];
	$sfcomps['sfsmtype'] = $sfsmileys['sfsmtype'];

	return $sfcomps;
}

function sfa_get_login_data()
{
	$sfcomps = array();

	$sflogin = array();
	$sflogin = sf_get_option('sflogin');
	$sfcomps['sfshowlogin'] = $sflogin['sfshowlogin'];
	$sfcomps['sfshowreg'] = $sflogin['sfshowreg'];
	$sfcomps['sfshowavatar'] = $sflogin['sfshowavatar'];
	$sfcomps['sfregmath'] = $sflogin['sfregmath'];
	$sfcomps['sfinlogin'] = $sflogin['sfinlogin'];
	$sfcomps['sfloginskin'] = $sflogin['sfloginskin'];
	$sfcomps['sfloginurl'] = sf_filter_url_display($sflogin['sfloginurl']);
	$sfcomps['sfloginemailurl'] = sf_filter_url_display($sflogin['sfloginemailurl']);
	$sfcomps['sflogouturl'] = sf_filter_url_display($sflogin['sflogouturl']);
	$sfcomps['sfregisterurl'] = sf_filter_url_display($sflogin['sfregisterurl']);
	$sfcomps['sflostpassurl'] = sf_filter_url_display($sflogin['sflostpassurl']);

	$sfrpx = array();
	$sfrpx = sf_get_option('sfrpx');
	$sfcomps['sfrpxenable'] = $sfrpx['sfrpxenable'];
	$sfcomps['sfrpxkey'] = $sfrpx['sfrpxkey'];
	$sfcomps['sfrpxredirect'] = sf_filter_url_display($sfrpx['sfrpxredirect']);

	$sneakpeek = sf_get_sfmeta('sneakpeek', 'message');
	$adminview = sf_get_sfmeta('adminview', 'message');

	$sfcomps['sfsneakpeek'] = '';
	$sfcomps['sfadminview'] = '';
	if (!empty($sneakpeek[0])) $sfcomps['sfsneakpeek'] = sf_filter_text_edit($sneakpeek[0]['meta_value']);
	if (!empty($adminview[0])) $sfcomps['sfadminview'] = sf_filter_text_edit($adminview[0]['meta_value']);

	return $sfcomps;
}

function sfa_get_seo_data()
{
	$sfcomps = array();

	# browser title
	$sfseo = array();
	$sfseo = sf_get_option('sfseo');
	$sfcomps['sfseo_topic'] = $sfseo['sfseo_topic'];
	$sfcomps['sfseo_forum'] = $sfseo['sfseo_forum'];
	$sfcomps['sfseo_page'] = $sfseo['sfseo_page'];
	$sfcomps['sfseo_sep'] = $sfseo['sfseo_sep'];

	# meta tags
	$sfmetatags= array();
	$sfmetatags = sf_get_option('sfmetatags');
	$sfcomps['sfdescr'] = sf_filter_title_display($sfmetatags['sfdescr']);
	$sfcomps['sfdescruse'] = $sfmetatags['sfdescruse'];
	$sfcomps['sfusekeywords'] = sf_filter_title_display($sfmetatags['sfusekeywords']);
	$sfcomps['sfkeywords'] = $sfmetatags['sfkeywords'];
	$sfcomps['sftagwords'] = $sfmetatags['sftagwords'];

	$sfcomps['sfbuildsitemap'] = sf_get_option('sfbuildsitemap');

	# cron scheduled?
	$sfcomps['sched'] = wp_get_schedule('spf_cron_sitemap');

	return $sfcomps;
}

function sfa_get_pm_data()
{
	$sfcomps = array();

	# Load Private Message options
	$sfcomps['sfprivatemessaging'] = sf_get_option('sfprivatemessaging');
	$sfpm = array();
	$sfpm = sf_get_option('sfpm');
	$sfcomps['sfpmemail'] = $sfpm['sfpmemail'];
	$sfcomps['sfpmmax'] = $sfpm['sfpmmax'];
	$sfcomps['sfpmcc'] = $sfpm['sfpmcc'];
	$sfcomps['sfpmbcc'] = $sfpm['sfpmbcc'];
	$sfcomps['sfpmmaxrecipients'] = $sfpm['sfpmmaxrecipients'];
	$sfcomps['sfpmlimitedsend'] = $sfpm['sfpmlimitedsend'];
	$sfcomps['sfpmremove'] = $sfpm['sfpmremove'];
	$sfcomps['sfpmkeep'] = $sfpm['sfpmkeep'];

	# cron scheduled?
	$sfcomps['sched'] = wp_get_schedule('spf_cron_pm');

	return $sfcomps;
}

function sfa_get_links_data()
{
	$sfoptions = array();
	$sfoptions = sf_get_option('sfpostlinking');

	$sflinkposttype = array();
	$sflinkposttype = sf_get_option('sflinkposttype');

	$post_types=get_post_types();

	$list = array();
	foreach($post_types as $key=>$value)
	{
		if($key != 'attachment' && $key != 'revision' && $key != 'nav_menu_item')
		{
			if(in_array($key, $sflinkposttype))
			{
				$list[$key] = $sflinkposttype[$key];
			} else {
				$list[$key] = false;
			}
		}
	}

	$sfoptions['posttypes'] = $list;

	return $sfoptions;
}

function sfa_get_uploads_data()
{
	$sfcomps = array();
	$sfcomps = sf_get_option('sfuploads');

	return $sfcomps;
}

function sfa_get_topicstatus_data()
{
	$sfcomps = array();

	$tsets = sf_get_sfmeta('topic-status', false);
	if ($tsets) { $sfcomps['topic-status'] = $tsets; } else { $sfcomps['topic-status']=0; }

	return $sfcomps;
}

function sfa_get_forumranks_data()
{
	$rankings = sf_get_sfmeta('forum_rank');

	return $rankings;
}

function sfa_get_specialranks_data()
{
	$special_rankings = sf_get_sfmeta('special_rank');

	return $special_rankings;
}

function sfa_get_messages_data()
{
	$sfcomps = array();

	# custom message for posts
	$sfpostmsg = array();
	$sfpostmsg = sf_get_option('sfpostmsg');
	$sfcomps['sfpostmsgtext'] = sf_filter_text_edit($sfpostmsg['sfpostmsgtext']);
	$sfcomps['sfpostmsgtopic'] = $sfpostmsg['sfpostmsgtopic'];
	$sfcomps['sfpostmsgpost'] = $sfpostmsg['sfpostmsgpost'];

	# custom editor message
	$sfcomps['sfeditormsg'] = sf_filter_text_edit(sf_get_option('sfeditormsg'));

	return $sfcomps;
}

function sfa_get_icons_data()
{
	$sfcomps = array();

	$sfcustom = array();
	$sfcustom = sf_get_option('sfcustom');

	$sfcomps['custext0'] = sf_filter_title_display($sfcustom[0]['custext']);
	$sfcomps['cuslink0'] = sf_filter_url_display($sfcustom[0]['cuslink']);
	$sfcomps['cusicon0'] = sf_filter_title_display($sfcustom[0]['cusicon']);
	$sfcomps['custext1'] = sf_filter_title_display($sfcustom[1]['custext']);
	$sfcomps['cuslink1'] = sf_filter_url_display($sfcustom[1]['cuslink']);
	$sfcomps['cusicon1'] = sf_filter_title_display($sfcustom[1]['cusicon']);
	$sfcomps['custext2'] = sf_filter_title_display($sfcustom[2]['custext']);
	$sfcomps['cuslink2'] = sf_filter_url_display($sfcustom[2]['cuslink']);
	$sfcomps['cusicon2'] = sf_filter_title_display($sfcustom[2]['cusicon']);

	return $sfcomps;
}

function sfa_get_tags_data()
{
	$sfoptions = array();

	$sfoptions['sfuseannounce'] = sf_get_option('sfuseannounce');
	$sfoptions['sfannouncelist'] = sf_get_option('sfannouncelist');
	$sfoptions['sfannouncecount'] = sf_get_option('sfannouncecount');
	$sfoptions['sfannouncehead'] = sf_filter_text_edit(sf_get_option('sfannouncehead'));
	$sfoptions['sfannouncetext'] = sf_filter_text_edit(sf_get_option('sfannouncetext'));

	$sfoptions['sfannounceauto'] = sf_get_option('sfannounceauto');
	$sfoptions['sfannouncetime'] = sf_get_option('sfannouncetime');

	return $sfoptions;
}

function sfa_get_policies_data()
{
	$sfcomps = sf_get_option('sfpolicy');

	$policy = sf_get_sfmeta('registration', 'policy');
	$sfcomps['sfregpolicy'] = '';
	if (!empty($policy[0])) $sfcomps['sfregpolicy'] = sf_filter_text_edit($policy[0]['meta_value']);

	$policy = sf_get_sfmeta('privacy', 'policy');
	$sfcomps['sfprivpolicy'] = '';
	if (!empty($policy[0])) $sfcomps['sfprivpolicy'] = sf_filter_text_edit($policy[0]['meta_value']);

	return $sfcomps;
}

# Special toolbar routines
function sfa_render_remove_toolbar()
{
	$ipath = SFADMINIMAGES.'toolbar/';
	$delprompt = esc_js(__("Remove Selected Toolbar Button?", "sforum"));
	echo '<label for="sftbarall" class="sublabel">'.__("Click on the buttons you wish to remove. When finished, click on the Update Toolbar button to save", "sforum").'</label><br /><br />'."\n";
	?>
	<ul id="sftbarall">
	<?php
	$tbmeta = sf_get_sfmeta('tinymce_toolbar', 'user');
	$tbdata = unserialize($tbmeta[0]['meta_value']);
	$toolbar = array_merge($tbdata['tbar_buttons'], $tbdata['tbar_buttons_add']);
	$thisb = 0;
	foreach($toolbar as $button)
	{
		if($button == "|")
		{
			$img = "separator.gif";
			$bname = 'separator';
		} else {
			$img = $button.'.gif';
			$bname = $button;
		}

		if($thisb >= count($tbdata['tbar_buttons']))
		{
			$buttonid = "plugin_" . ($thisb - count($tbdata['tbar_buttons']));
		} else {
			$buttonid = "button_" . $thisb;
		}
		?>
		<li id="del_btn_<?php echo($thisb); ?>">
		<a id="<?php echo($buttonid); ?>" href="javascript:void(0);" onclick="sfjDelTbButton(this, '<?php echo($delprompt); ?>');"><img src="<?php echo($ipath.$img); ?>" alt="" title="<?php esc_attr_e($bname); ?>" /></a>
		</li>
		<?php
		$thisb++;
	}
	return;
}

function sfa_get_mobile_data()
{
	$sfcomps = array();

	$sfmobile = sf_get_option('sfmobile');
	$sfcomps['browsers'] = $sfmobile['browsers'];
	$sfcomps['touch'] = $sfmobile['touch'];

	return $sfcomps;
}

function sfa_paint_custom_smileys()
{
	global $SFPATHS, $tab;

	$out='';

	# load smiles from sfmeta
	$smileys = array();
	$meta = sf_get_sfmeta('smileys', 'smileys');
	$smeta = $meta[0]['meta_value'];
	$smileys = unserialize($smeta);

	# Open forum-smileys folder and get cntents for matching
	$path = SF_STORE_DIR.'/'.$SFPATHS['smileys'].'/';
	$dlist = @opendir($path);
	if(!$dlist)
	{
		echo '<strong>'.__("The 'forum-smileys' folder does not exist", "sforum").'</strong>';
		return;
	}

	# start the table display
	$out.= '<tr>';
	$out.= '<th style="width:5%;text-align:center"></th>';
	$out.= '<th style="width:30%;text-align:center">'.__("File", "sforum").'</th>';
	$out.= '<th style="width:30%;text-align:center">'.__("Name", "sforum").'</th>';
	$out.= '<th style="width:30%;text-align:center">'.__("Code", "sforum").'</th>';
	$out.= '<th style="width:5%;text-align:center">'.__("Remove", "sforum").'</th>';
	$out.= '</tr>';

    $out.= '<tr><td colspan="5">';
    $out.= '<div id="sf-smiley-imgs">';
	while (false !== ($file = readdir($dlist)))
	{
		if ($file != "." && $file != "..")
		{
			$found = false;
		    $out.= '<table width="100%">';
			$out.= '<tr>';
			$out.= '<td width="5%" class="sflabel" align="center"><img class="sfsmiley" src="'.SFSMILEYS.$file.'" alt="" /></td>';
			if($smileys)
			{
				foreach ($smileys as $name => $info)
				{
					if($info[0] == $file)
					{
						$found = true;
						break;
					}
				}
			}
			if(!$found)
			{
				$name = '';
				$code = '';
			} else {
				$code = sf_filter_name_display($info[1]);
			}

			$out.= '<td width="30%" class="sflabel" class="sflabel" align="center">';
			$out.= '<input type="hidden" name="smfile[]" value="'.$file.'" />';
			$out.= $file;
			$out.= '</td>';
			$out.= '<td width="30%" class="sflabel" align="center">';
			$out.= '<input type="text" class="sfpostcontrol" size="20" tabindex="'.$tab.'" name="smname[]" value="'.sf_filter_title_display($name).'" />';
			$out.= '</td>';
			$out.= '<td width="30%" class="sflabel" align="center">';
			$out.= '<input type="text" class="sfpostcontrol" size="20" tabindex="'.$tab.'" name="smcode[]" value="'.$code.'" />';
			$out.= '</td>';
			$out.= '<td width="5%" class="sflabel" align="center">';
			$site = esc_url(SFHOMEURL."index.php?sf_ahah=components&action=delsmiley&amp;file=".$file);
			$out.= '<img src="'.SFADMINIMAGES.'del_cfield.png" title="'.esc_attr(__("Delete Smiley", "sforum")).'" alt="" onclick="sfjDelSmiley(\''.$site.'\');" />';
			$out.= '</td>';
			$out.= '</tr>';
			$out.= '</table>';
		}
	}
	$out.= '</div>';
	$out.= '</td></tr>';
	closedir($dlist);

	echo $out;
	return;
}

function sfa_paint_rank_images()
{
	global $tab, $SFPATHS;

	$out = '';

	# Open badges folder and get cntents for matching
	$path = SF_STORE_DIR.'/'.$SFPATHS['ranks'].'/';
	$dlist = @opendir($path);
	if (!$dlist)
	{
		echo '<strong>'.__("The rank badges folder does not exist", "sforum").'</strong>';
		return;
	}

	# start the table display
	$out.= '<tr>';
	$out.= '<th style="width:60%;text-align:center">'.__("Badge", "sforum").'</th>';
	$out.= '<th style="width:30%;text-align:center">'.__("Filename", "sforum").'</th>';
	$out.= '<th style="width:9%;text-align:center">'.__("Remove", "sforum").'</th>';
	$out.= '</tr>';

    $out.= '<tr><td colspan="3">';
    $out.= '<div id="sf-rank-badges">';
	while (false !== ($file = readdir($dlist)))
	{
		if ($file != "." && $file != "..")
		{
			$found = false;
		    $out.= '<table width="100%">';
			$out.= '<tr>';
			$out.= '<td align="center" width="60%" ><img class="sfrankbadge" src="'.esc_url(SFRANKS.'/'.$file).'" alt="" /></td>';
			$out.= '<td align="center" width="30%" class="sflabel">';
			$out.= $file;
			$out.= '</td>';
			$out.= '<td align="center" width="9%" class="sflabel">';
			$site = esc_url(SFHOMEURL."index.php?sf_ahah=components&action=delbadge&amp;file=".$file);
			$out.= '<img src="'.SFADMINIMAGES.'del_cfield.png" title="'.esc_attr(__("Delete Rank Badge", "sforum")).'" alt="" onclick="sfjDelBadge(\''.$site.'\');" />';
			$out.= '</td>';
			$out.= '</tr>';
			$out.= '</table>';
		}
	}
	$out.= '</div>';
	$out.= '</td></tr>';
	closedir($dlist);

	echo $out;
	return;
}

function sfa_paint_custom_icons()
{
	global $tab, $SFPATHS;

	$out='';

	# Open custom icons folder and get cntents for matching
	$path = SF_STORE_DIR.'/'.$SFPATHS['custom-icons'].'/';
	$dlist = @opendir($path);
	if (!$dlist)
	{
		echo '<strong>'.__("The custom icons folder does not exist", "sforum").'</strong>';
		return;
	}

	# start the table display
	$out.= '<tr>';
	$out.= '<th style="width:60%;text-align:center">'.__("Icon", "sforum").'</th>';
	$out.= '<th style="width:30%;text-align:center">'.__("Filename", "sforum").'</th>';
	$out.= '<th style="width:9%;text-align:center">'.__("Remove", "sforum").'</th>';
	$out.= '</tr>';

    $out.= '<tr><td colspan="3">';
    $out.= '<div id="sf-custom-icons">';
	while (false !== ($file = readdir($dlist)))
	{
		if ($file != "." && $file != "..")
		{
			$found = false;
		    $out.= '<table width="100%">';
			$out.= '<tr>';
			$out.= '<td align="center" width="60%" ><img class="sfcustomicon" src="'.esc_url(SFCUSTOMURL.'/'.$file).'" alt="" /></td>';
			$out.= '<td align="center" width="30%" class="sflabel">';
			$out.= $file;
			$out.= '</td>';
			$out.= '<td align="center" width="9%" class="sflabel">';
			$site = esc_url(SFHOMEURL."index.php?sf_ahah=components&action=delicon&amp;file=".$file);
			$out.= '<img src="'.SFADMINIMAGES.'del_cfield.png" title="'.__("Delete Custom Icon", "sforum").'" alt="" onclick="sfjDelIcon(\''.$site.'\');" />';
			$out.= '</td>';
			$out.= '</tr>';
			$out.= '</table>';
		}
	}
	$out.= '</div>';
	$out.= '</td></tr>';
	closedir($dlist);

	echo $out;
	return;
}

?>