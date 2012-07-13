<?php
/*
Simple:Press
Admin Config Config Form
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}


function sfa_config_config_form()
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfconfigform').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfreloadsl').click();
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
	$ttpath = SFHELP.'admin/tooltips/admin-config-config-tips-'.$lang.'.php';
	if (file_exists($ttpath) == false) $ttpath = SFHELP.'admin/tooltips/admin-config-config-tips-en.php';
	if(file_exists($ttpath))
	{
		include_once($ttpath);
	}

	$sfoptions = sfa_get_config_data();

    $ahahURL = SFHOMEURL."index.php?sf_ahah=config-loader&amp;saveform=config";

?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfconfigform" name="sfconfig">
	<?php echo(sfc_create_nonce('forum-adminform_config')); ?>
<?php

	sfa_paint_options_init();

	sfa_paint_open_tab(__("Configuration", "sforum")." - ".__("Storage Locations", "sforum"));

		sfa_paint_open_panel();

			sfa_paint_open_fieldset(__("Set Storage Locations", "sforum"), true, 'storage-locations', true);

			echo '<tr><td colspan="3"><br /><div class="sfoptionerror">';
			echo __("BEWARE: Please read the help before making any changes to these locations. Incorrect changes may cause Simple:Press to stop functioning.", "sforum");
			echo '</div><br />';

			echo '&nbsp;<img src="'.SFADMINIMAGES.'good.gif" title="'.__("Location found", "sforum").'" alt="" style="vertical-align: middle;" />&nbsp;&nbsp;'.__("Location found", "sforum").'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			echo '<img src="'.SFADMINIMAGES.'bad.gif" title="'.__("Location not found", "sforum").'" alt="" style="vertical-align: middle;" />&nbsp;&nbsp;'.__("Location not found", "sforum").'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			echo '<img src="'.SFADMINIMAGES.'write_on.gif" title="'.__("Write - OK", "sforum").'" alt="" style="vertical-align: middle;" />&nbsp;&nbsp;'.__("Write - OK", "sforum").'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			echo '<img src="'.SFADMINIMAGES.'write_off.gif" title="'.__("Write - denied", "sforum").'" alt="" style="vertical-align: middle;" />&nbsp;&nbsp;'.__("Write - denied", "sforum");

			echo '<p><strong>'.__("Set the new location of your", "sforum").':</strong></p></td></tr>';

			$path = WP_CONTENT_DIR.'/'.$sfoptions['styles'];
			sfa_paint_config_input(__("Styles Folder", "sforum"), "styles", $sfoptions['styles'], $path);

			$path = WP_CONTENT_DIR.'/'.$sfoptions['avatars'];
			sfa_paint_config_input(__("Avatars Folder", "sforum"), "avatars", $sfoptions['avatars'], $path);

			$path = WP_CONTENT_DIR.'/'.$sfoptions['avatar-pool'];
			sfa_paint_config_input(__("Avatar Pool Folder", "sforum"), "avatar-pool", $sfoptions['avatar-pool'], $path);

			$path = WP_CONTENT_DIR.'/'.$sfoptions['smileys'];
			sfa_paint_config_input(__("Smileys Folder", "sforum"), "smileys", $sfoptions['smileys'], $path);

			$path = WP_CONTENT_DIR.'/'.$sfoptions['ranks'];
			sfa_paint_config_input(__("Forum Badges Folder", "sforum"), "ranks", $sfoptions['ranks'], $path);

			$path = WP_CONTENT_DIR.'/'.$sfoptions['image-uploads'];
			sfa_paint_config_input(__("Image Uploads Folder", "sforum"), "image-uploads", $sfoptions['image-uploads'], $path);

			$path = WP_CONTENT_DIR.'/'.$sfoptions['media-uploads'];
			sfa_paint_config_input(__("Media Uploads Folder", "sforum"), "media-uploads", $sfoptions['media-uploads'], $path);

			$path = WP_CONTENT_DIR.'/'.$sfoptions['file-uploads'];
			sfa_paint_config_input(__("File Uploads Folder", "sforum"), "file-uploads", $sfoptions['file-uploads'], $path);

			$path = WP_CONTENT_DIR.'/'.$sfoptions['custom-icons'];
			sfa_paint_config_input(__("Custom Icons Folder", "sforum"), "custom-icons", $sfoptions['custom-icons'], $path);

			$path = WP_CONTENT_DIR.'/'.$sfoptions['policies'];
			sfa_paint_config_input(__("Forum Policy Documents Folder", "sforum"), "policies", $sfoptions['policies'], $path, false, true);

			$path = WP_CONTENT_DIR.'/'.$sfoptions['hooks'].'/sf-hook-template.';
			sfa_paint_config_input(__("Program Hooks file", "sforum"), "hooks", $sfoptions['hooks'], $path, true, true);

			$path = WP_CONTENT_DIR.'/'.$sfoptions['pluggable'].'/sf-pluggable.';
			sfa_paint_config_input(__("Pluggable Functions File", "sforum"), "pluggable", $sfoptions['pluggable'], $path, true, true);

			$path = WP_CONTENT_DIR.'/'.$sfoptions['filters'].'/sf-custom-filters.';
			sfa_paint_config_input(__("Custom Filters File", "sforum"), "filters", $sfoptions['filters'], $path, true, true);

			$path = WP_CONTENT_DIR.'/'.$sfoptions['help'];
			sfa_paint_config_input(__("Admin Help Folder", "sforum"), "help", $sfoptions['help'], $path, false, true);

			sfa_paint_close_fieldset();

		sfa_paint_close_panel();

	sfa_paint_close_tab();

?>
	<div class="sfform-submit-bar">
	<input type="submit" class="sfform-panel-button" id="saveit" name="saveit" value="<?php esc_attr_e(__('Update Storage Locations', 'sforum')); ?>" />
	</div>
	</form>
<?php
	return;
}

function sfa_paint_config_input($label, $name, $value, $path, $dual=false, $na=false)
{
	global $tab, $tooltips;

	$found=false;

	if($dual)
	{
		if(file_exists($path.'txt') || file_exists($path.'php')) $found=true;
	} else {
		if(file_exists($path)) $found=true;
	}
	if($found)
	{
		$icon1 = '<img src="'.SFADMINIMAGES.'good.gif" title="'.__("Location found", "sforum").'" alt="" style="vertical-align: middle;" />&nbsp;&nbsp;';
	} else {
		$icon1 = '<img src="'.SFADMINIMAGES.'bad.gif" title="'.__("Location not found", "sforum").'" alt="" style="vertical-align: middle;" />&nbsp;&nbsp;';
		$icon2 = '<img src="'.SFADMINIMAGES.'write_off.gif" title="'.__("Write - denied", "sforum").'" alt="" style="vertical-align: middle;" />&nbsp;&nbsp;';
	}

	if($found)
	{
		if(is_writable($path))
		{
			$icon2 = '<img src="'.SFADMINIMAGES.'write_on.gif" title="'.__("Write - OK", "sforum").'" alt="" style="vertical-align: middle;" />&nbsp;&nbsp;';
		} else {
			$icon2 = '<img src="'.SFADMINIMAGES.'write_off.gif" title="'.__("Write - denied", "sforum").'" alt="" style="vertical-align: middle;" />&nbsp;&nbsp;';
		}
	}

	if($na) $icon2 = '<img src="'.SFADMINIMAGES.'na.gif" title="" alt="" style="vertical-align: middle;" />&nbsp;&nbsp;';

	echo "<tr valign='top'>\n";
	if($found)
	{
		echo "<td class='sflabel' width='40%'>\n";
	} else {
		echo "<td class='sflabel highlight' width='40%'>\n";
	}

	echo "<span class='sfalignleft'>".$icon1.$icon2.$label.":</span>";
	echo "<span class='sfalignright'>";
	echo '<img src="'.SFADMINIMAGES.'information.png" alt="" class="vtip" title="' . $tooltips[sf_create_slug($name, false)] . '" />&nbsp;&nbsp;';
	echo SF_STORE_RELATIVE_BASE."</span>";

	echo "</td>\n";
	if($found)
	{
		$opentd = "<td>";
	} else {
		$opentd = "<td class='highlight'>\n";
	}
	echo $opentd;
	echo '<input type="text" class="sfpostcontrol" tabindex="'.$tab.'" name="'.$name.'" value="'.esc_attr($value).'" ';
	echo "/></td>\n";

	echo "</tr>\n";
	$tab++;
	return;
}

?>