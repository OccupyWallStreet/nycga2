<?php
/*
Simple:Press
Admin config Optimise Form
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

function sfa_config_optimise_form($sfsupport)
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfsupport').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfmsgspot').fadeIn();
			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});
	jQuery(function(jQuery){vtip("<?php echo(SFADMINIMAGES.'vtip_arrow.png'); ?>");})
});
</script>
<?php
	# Get correct tooltips file
	$lang = WPLANG;
	if (empty($lang)) $lang = 'en';
	$ttpath = SFHELP.'admin/tooltips/admin-config-optimizations-tips-'.$lang.'.php';
	if (file_exists($ttpath) == false) $ttpath = SFHELP.'admin/tooltips/admin-config-optimizations-tips-en.php';
	if(file_exists($ttpath))
	{
		include_once($ttpath);
	}

    $ahahURL = SFHOMEURL."index.php?sf_ahah=config-loader&amp;saveform=saveoptions";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfsupport" name="sfsupport">
<?php
	echo sfc_create_nonce('forum-adminform_sfsupport');

	sfa_paint_options_init();
	sfa_paint_open_tab(__("Configuration", "sforum")." - ".__("Code And Query Optimizations", "sforum"));
		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Code And Query Optimizations", "sforum"), true, 'code-and-query-optimisations', true);

			echo '<br /><div class="sfoptionerror">';
			echo __("Simple:Press needs to load extra code and execute some database queries on non-forum pages. You can minimise these if you are not using any of the following features.  See the popup help for listing of which tags are in which files:", "sforum");
			echo '</div><br />';

			sfa_paint_config_checkbox(__("Check if you ARE using Blog/Topic Linking", "sforum"), "sfusinglinking", $sfsupport['sfusinglinking']);
			sfa_paint_config_checkbox(__("Check if you ARE using Linked Topic Posts as Blog Comments", "sforum"), "sfusinglinkcomments", $sfsupport['sfusinglinkcomments']);
			sfa_paint_config_checkbox(__("Check if you ARE using the Forum Widget", "sforum"), "sfusingwidgets", $sfsupport['sfusingwidgets']);
			sfa_paint_config_checkbox(__("Check if you ARE using any General Template Tags", "sforum"), "sfusinggeneraltags", $sfsupport['sfusinggeneraltags']);
			sfa_paint_config_checkbox(__("Check if you ARE using any Avatar Template Tags", "sforum"), "sfusingavatartags", $sfsupport['sfusingavatartags']);
			sfa_paint_config_checkbox(__("Check if you ARE using any Listing Template Tags", "sforum"), "sfusingliststags", $sfsupport['sfusingliststags']);
			sfa_paint_config_checkbox(__("Check if you ARE using any Tags Template Tags", "sforum"), "sfusingtagstags", $sfsupport['sfusingtagstags']);
			sfa_paint_config_checkbox(__("Check if you ARE using any Pages Template Tags", "sforum"), "sfusingpagestags", $sfsupport['sfusingpagestags']);
			sfa_paint_config_checkbox(__("Check if you ARE using any PM Template Tags", "sforum"), "sfusingpmtags", $sfsupport['sfusingpmtags']);
			sfa_paint_config_checkbox(__("Check if you ARE using any Stats Template Tags", "sforum"), "sfusingstatstags", $sfsupport['sfusingstatstags']);
			sfa_paint_config_checkbox(__("Check if you ARE using any Links Template Tags", "sforum"), "sfusinglinkstags", $sfsupport['sfusinglinkstags']);

			sfa_paint_close_fieldset();
		sfa_paint_close_panel();
	sfa_paint_close_tab();

?>
	<div class="sfform-submit-bar">
	<input type="submit" class="sfform-panel-button" id="sfsupport" name="sfsupport" value="<?php esc_attr_e(__('Update Code and Query Optimizations', 'sforum')); ?>" />
	</div>
	</form>

<?php
	return;
}

function sfa_paint_config_checkbox($label, $name, $value, $disabled=false, $large=false, $displayhelp=true)
{
	global $tab, $tooltips;

	echo "<tr valign='top'>\n";

	echo "<td class='sflabel' width='100%' colspan='2'>\n";
	echo '<table class="form-table table-cbox"><tr>';
    echo '<td width="30" class="td-cbox">';
	echo '<img src="'.SFADMINIMAGES.'information.png" alt="" class="vtip" title="' . $tooltips[sf_create_slug($name, false)] . '" />&nbsp;&nbsp;';
    echo '</td><td class="td-cbox">';
	echo "<label for='sf-".$name."'>$label:</label>\n";
	echo "<input type='checkbox' tabindex='$tab' name='$name' id='sf-$name' ";
	if($value == true)
	{
		echo "checked='checked' ";
	}
	if($disabled == true)
	{
		echo "disabled='disabled' ";
	}

	echo "/>\n";
	echo "</td></tr></table>";
	echo "</td>\n";
	echo "</tr>\n";
	$tab++;
	return;
}

?>