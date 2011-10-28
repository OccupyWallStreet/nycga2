<?php
/*
Simple:Press
Admin Components Forum Ranks Form
$LastChangedDate: 2011-04-16 10:18:49 -0700 (Sat, 16 Apr 2011) $
$Rev: 5903 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

include_once (SF_PLUGIN_DIR.'/admin/panel-components/forms/sfa-components-special-ranks-form.php');

function sfa_components_forumranks_form()
{
	global $SFPATHS;
?>
<script type= "text/javascript">/*<![CDATA[*/
jQuery(document).ready(function(){

	jQuery('#sfforumranksform').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfreloadfr').click();
			jQuery('#sfmsgspot').fadeIn();
			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});

	var button = jQuery('#sf-upload-button'), interval;
	new AjaxUpload(button,{
		action: '<?php echo SFUPLOADER; ?>',
		name: 'uploadfile',
	    data: {
		    saveloc : '<?php echo addslashes(SF_STORE_DIR."/".$SFPATHS['ranks']."/"); ?>'
	    },
		onSubmit : function(file, ext){
			/* check for valid extension */
			if (! (ext && /^(jpg|png|jpeg|gif|JPG|PNG|JPEG|GIF)$/.test(ext))){
				jQuery('#sf-upload-status').html('<p class="sf-upload-status-fail"><?php echo esc_js(__("Only JPG, PNG or GIF files are allowed!", "sforum")); ?></p>');
				return false;
			}
			/* change button text, when user selects file */
			utext = '<?php echo esc_js(__("Uploading", "sforum")); ?>';
			button.text(utext);
			/* If you want to allow uploading only 1 file at time, you can disable upload button */
			this.disable();
			/* Uploding -> Uploading. -> Uploading... */
			interval = window.setInterval(function(){
				var text = button.text();
				if (text.length < 13){
					button.text(text + '.');
				} else {
					button.text(utext);
				}
			}, 200);
		},
		onComplete: function(file, response){
			jQuery('#sf-upload-status').html('');
			button.text('<?php echo esc_js(__("Browse", "sforum")); ?>');
			window.clearInterval(interval);
			/* re-enable upload button */
			this.enable();
			/* add file to the list */
			if (response==="success"){
                site = "<?php echo SFHOMEURL; ?>index.php?sf_ahah=components&amp;action=delbadge&amp;file=" + file;
				jQuery('<table width="100%"></table>').appendTo('#sf-rank-badges').html('<tr><td width="60%" align="center"><img class="sfrankbadge" src="<?php echo SFRANKS; ?>/' + file + '" alt="" /></td><td class="sflabel" align="center" width="30%">' + file + '</td><td class="sflabel" align="center" width="9%"><img src="<?php echo SFADMINIMAGES; ?>' + 'del_cfield.png' + '" title="<?php echo esc_js(__("Delete Rank Badge", "sforum")); ?>" alt="" onclick="sfjDelBadge(\'' + site + '\');" /></td></tr>');
				jQuery('#sf-upload-status').html('<p class="sf-upload-status-success"><?php echo esc_js(__("Forum Badge Uploaded!", "sforum")); ?></p>');
			} else if (response==="exists"){
				jQuery('#sf-upload-status').html('<p class="sf-upload-status-fail"><?php echo esc_js(__("Sorry, the file already exists!", "sforum")); ?></p>');
			} else if (response==="invalid"){
				jQuery('#sf-upload-status').html('<p class="sf-upload-status-fail"><?php echo esc_js(__("Sorry, the file has an invalid format!", "sforum")); ?></p>');
			} else {
				jQuery('#sf-upload-status').html('<p class="sf-upload-status-fail"><?php echo esc_js(__("Error uploading file!!", "sforum")); ?></p>');
			}
		}
	});
});/*]]>*/</script>

<?php

	$rankings = sfa_get_forumranks_data();

    $ahahURL = SFHOMEURL."index.php?sf_ahah=components-loader&amp;saveform=forumranks";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfforumranksform" name="sfforumranks">
	<?php echo(sfc_create_nonce('forum-adminform_forumranks')); ?>
<?php
	sfa_paint_options_init();

#== FORUM RANKS Tab ============================================================

	sfa_paint_open_tab(__("Components", "sforum")." - ".__("Forum Ranks", "sforum"));
		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Forum Ranks", "sforum"), true, 'forum-ranks');
				sfa_paint_rankings_table($rankings);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();
	sfa_paint_close_tab();
?>
	<div class="sfform-submit-bar">
	<input type="submit" class="sfform-panel-button" id="saveit" name="saveit" value="<?php esc_attr_e(__('Update Forum Ranks Components', 'sforum')); ?>" />
	</div>
	</form>

	<div class="sfform-panel-spacer"></div>
<?php

	$special_rankings = sfa_get_specialranks_data();

	sfa_paint_open_tab(__("Components", "sforum")." - ".__("Forum Ranks", "sforum"));
		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Special Forum Ranks", "sforum"), true, 'special-ranks', false);
        		echo "<table class='sfsubtable' width='100%'>\n";
				sfa_special_rankings_form($special_rankings);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();
	sfa_paint_close_tab();

	echo '<div class="sfform-panel-spacer"></div>';

	sfa_paint_open_tab(__("Components", "sforum")." - ".__("Forum Ranks", "sforum"));
		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Custom Rank Badge Upload", "sforum"), true, 'badges-upload');
				$loc = SF_STORE_DIR."/".$SFPATHS['ranks']."/";
				sfa_paint_file(__("Select Rank Badge(s) to Upload", "sforum"), 'newrankfile', false, true, $loc);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Custom Rank Badges", "sforum"), true, 'rank-badges');
			sfa_paint_rank_images();
			sfa_paint_close_fieldset(true);
		sfa_paint_close_panel();

	sfa_paint_close_tab();

	return;
}

function sfa_paint_rankings_table($rankings)
{
	global $tab, $SFPATHS;

	echo "<tr>\n";
	echo "<th width='20%' style='text-align:center'>".__("Forum Rank Name", "sforum")."</th>\n";
	echo "<th width='20%' style='text-align:center'>".__("# Posts For Rank", "sforum")."</th>\n";
	echo "<th width='20%' style='text-align:center'>".__("User Group Membership Attained", "sforum")."</th>\n";
	echo "<th width='20%' style='text-align:center'>".__("Badge", "sforum")."</th>\n";
	echo "<th width='9%' style='text-align:center'>".__("Remove", "sforum")."</th>\n";
	echo "</tr>\n";
	$usergroups = sfa_get_usergroups_all();

	# sort rankings from lowest to highest
	if ($rankings)
	{
		foreach ($rankings as $x => $info)
		{
			$ranks['id'][$x] = $info['meta_id'];
			$ranks['title'][$x] = $info['meta_key'];
			$data = unserialize($info['meta_value']);
			$ranks['posts'][$x] = $data['posts'];
			$ranks['usergroup'][$x] = $data['usergroup'];
			$ranks['badge'][$x] = $data['badge'];
		}
		array_multisort($ranks['posts'], SORT_ASC, $ranks['title'], $ranks['usergroup'], $ranks['badge'], $ranks['id']);
	}

	# display rankings info
	for ($x=0; $x<count($rankings); $x++)
	{
		echo '<tr>'."\n";
		echo '<td width="100%" colspan="5" style="border-bottom:0px;padding:0px;">'."\n";
		echo '<div id="rank'.$x.'">'."\n";
		echo '<table width="100%" cellspacing="0">'."\n";
		echo '<tr>'."\n";
		echo "<td width='20%' class='sflabel'>\n";
		echo "<input type='text' class='sfpostcontrol' size='20' tabindex='$tab' name='rankdesc[]' value='".esc_attr($ranks['title'][$x])."' />\n";
		echo "<input type='hidden' class='sfpostcontrol' size='0' tabindex='$tab' name='rankid[]' value='".esc_attr($ranks['id'][$x])."' />\n";
		$tab++;
		echo "</td>\n";
		echo "<td width='20%' class='sflabel' align='center'>\n";
		echo __("Up to", "sforum")." &#8594;\n";
		echo "<input type='text' class='sfpostcontrol' size='7' tabindex='$tab' name='rankpost[]' value='".$ranks['posts'][$x]."' />\n";
		$tab++;
		echo " ".__("Posts", "sforum")." \n";
		echo "</td>\n";
		echo "<td width='20%' class='sflabel' align='center'>\n";
		echo "<select class='sfacontrol' name='rankug[]'>\n";
		if ($data['usergroup'] == 'none')
		{
			$out = '<option value="none" selected="selected">'.__("No User Group Membership", "sforum").'</option>'."\n";
		} else {
			$out = '<option value="none">'.__("No User Group Membership", "sforum").'</option>'."\n";
		}
		foreach ($usergroups as $usergroup)
		{
			if ($ranks['usergroup'][$x] == $usergroup->usergroup_id)
			{
				$selected = ' SELECTED';
			} else {
				$selected = '';
			}
			$out.='<option value="'.$usergroup->usergroup_id.'"'.$selected.'>'.sf_filter_title_display($usergroup->usergroup_name).'</option>'."\n";
		}
		echo $out;
		echo "</select>\n";
		$tab++;
		echo "</td>\n";
		echo "<td width='20%' class='sflabel' align='center'>"."\n";
		sfa_select_icon_dropdown('rankbadge[]', __("Select Badge", "sforum"), SF_STORE_DIR.'/'.$SFPATHS['ranks'].'/', $ranks['badge'][$x]);
		$tab++;
		echo "</td>\n";
		echo "<td class='sflabel' align='center' width='9%'>"."\n";
		$gif = SFADMINIMAGES."working.gif";
        $site = SFHOMEURL."index.php?sf_ahah=components&amp;action=del_rank&amp;key=".$ranks['id'][$x];
		?>
		<img onclick="sfjDelRank('<?php echo $site; ?>', '<?php echo $gif; ?>', '1', 'rank<?php echo $x; ?>');" src="<?php echo SFADMINIMAGES; ?>del_cfield.png" title="<?php echo __("Delete Rank", "sforum"); ?>" alt="" />
		<?php
		echo "</td>"."\n";
		echo "</tr>"."\n";
		echo "</table>"."\n";
		echo "</div>"."\n";
		echo "</td>"."\n";
		echo "</tr>"."\n";
	}

	# always have one empty slot available for new rank
	echo "<tr>\n";
	echo "<td class='sflabel' width='20%'>\n";
	echo "<input type='text' class='sfpostcontrol' size = '20' tabindex='$tab' name='rankdesc[]' value='' />\n";
	echo "<input type='hidden' class='sfpostcontrol' size = '0' tabindex='$tab' name='rankid[]' value='-1' />\n";
	$tab++;
	echo "</td>\n";
	echo "<td width='20%' class='sflabel' align='center'>\n";
	echo __("Up to", "sforum")." &#8594;\n";
	echo "<input type='text' class=' sfpostcontrol' size ='7' tabindex='$tab' name='rankpost[]' value='' />\n";
	$tab++;
	echo " ".__("Posts", "sforum")." \n";
	echo "</td>\n";
	echo "<td width='20%' class='sflabel' align='center'>\n";
	echo "<select class='sfacontrol' name='rankug[]'>";
	$out = '<option value="none">'.__("No User Group Membership", "sforum").'</option>'."\n";
	foreach ($usergroups as $usergroup)
	{
		$out.='<option value="'.$usergroup->usergroup_id.'">'.sf_filter_title_display($usergroup->usergroup_name).'</option>'."\n";
	}
	echo $out;
	echo "</select>\n";
	echo "</td>\n";
	echo "<td width='20%' class='sflabel' align='center'>"."\n";
	sfa_select_icon_dropdown('rankbadge[]', __("Select Badge", "sforum"), SF_STORE_DIR.'/'.$SFPATHS['ranks'].'/', '');
	echo "</td>\n";
	echo "<td width='9%'></td>\n";
	echo "</tr>\n";

	return;
}

?>