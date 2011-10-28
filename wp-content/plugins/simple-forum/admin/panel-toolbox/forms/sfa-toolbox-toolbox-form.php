<?php
/*
Simple:Press
Admin Toolbox Toolbox Form
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_toolbox_toolbox_form()
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sftoolboxform').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfmsgspot').fadeIn();
			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});
});
</script>
<?php

	$sfoptions = sfa_get_toolbox_data();

    $ahahURL = SFHOMEURL."index.php?sf_ahah=toolbox-loader&amp;saveform=toolbox";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sftoolboxform" name="sftoolbox">
	<?php echo(sfc_create_nonce('forum-adminform_toolbox')); ?>
<?php

	sfa_paint_options_init();

#== TOOLBOX Tab ============================================================

	sfa_paint_open_tab(__("Toolbox", "sforum")." - ".__("Toolbox", "sforum"));

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Check for Updates", "sforum"), true, 'check-for-updates');
				sfa_paint_update_check();
				sfa_paint_checkbox(__("Auto Check for Updates", "sforum"), "sfcheck", $sfoptions['sfcheck']);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

	sfa_paint_tab_right_cell();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Exclude Check Boxes/Radio Buttons", "sforum"), true, 'exclude-check-box');
				sfa_paint_input(__("Exclude ID List", "sforum"), "sfcbexclusions", $sfoptions['sfcbexclusions']);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Modify Build Number", "sforum"), true, 'modify-build-number');
				echo('<tr><td colspan="2"><div class="sfoptionerror">'.__('WARNING: This value should not be changed unless requested by the Simple:Press team in the support forum as it may cause the install/upgrade script to be re-run.', 'sforum').'</div></td></tr>');
				sfa_paint_input(__("Build Number", "sforum"), "sfbuild", sf_get_option('sfbuild'), false, false);
				sfa_paint_checkbox(__("Force Upgrade to Build Number", "sforum"), "sfforceupgrade", $sfoptions['sfforceupgrade']);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

	sfa_paint_close_tab();

?>
	<div class="sfform-submit-bar">
	<input type="submit" class="sfform-panel-button" id="saveit" name="saveit" value="<?php esc_attr_e(__('Update Toolbox', 'sforum')); ?>" />
	</div>
	</form>
<?php
	return;
}

function sfa_paint_update_check()
{
	$buttontext = __("Check for Updates", "sforum");
    $site = SFHOMEURL."index.php?sf_ahah=toolbox&item=upcheck";
	$target = 'adminucresult';
	$gif = SFADMINIMAGES."working.gif";

	echo "<tr valign='top'>\n";
	echo "<td class='sflabel'>\n";

	echo '<input type="button" class="button button-highlighted sfalignright" value="'.splice(esc_attr(__("Check For Updates", "sforum")),2,0).'" onclick="sfjadminTool(\''.$site.'\', \''.$target.'\', \''.$gif.'\');" />';

	$version = __("Version:", "sforum").'&nbsp;<strong>'.sf_get_option('sfversion').'</strong>';
	$build = __("Build:  ", "sforum").'&nbsp;<strong>'.sf_get_option('sfbuild').'</strong>';
	echo $version.'&nbsp;&nbsp;&nbsp;&nbsp;'.$build;
	echo "</td>\n";
	echo "<td align='center' valign='middle'>\n";
	echo "<div class='subhead' id='adminucresult'>\n";
	echo "</div>\n";
	echo "</td>\n";
	echo "</tr>\n";

	return;
}

?>