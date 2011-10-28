<?php
/*
Simple:Press
Admin Profiles Manage Data Form
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_profiles_data_form()
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfdataform').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfmsgspot').fadeIn();
			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});
});
</script>
<?php

	$pdata = sfa_get_data_data();

    $ahahURL = SFHOMEURL."index.php?sf_ahah=profiles-loader&amp;saveform=data";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfdataform" name="sfdata">
	<?php echo(sfc_create_nonce('forum-adminform_data')); ?>
<?php

	sfa_paint_options_init();

#== DATA Tab ============================================================

	sfa_paint_open_tab(__("Profiles", "sforum")." - ".__("Profile Data", "sforum"));
		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Manage Profile Data", "sforum"), true, 'profile-data');

			echo "<tr>";
			echo "<th width='25%' style='text-align:center'>".__("Data Item", "sforum")."</th>";
			echo "<th width='45%' style='text-align:center'>".__("Label on Profile Display", "sforum")."</th>";
			echo "<th width='10%' style='text-align:center'>".__("Required on Profile Form", "sforum")."</th>";
			echo "<th width='10%' style='text-align:center'>".__("Include on Profile Form", "sforum")."</th>";
			echo "<th width='10%' style='text-align:center'>".__("Include on Profile Display", "sforum")."</th>";
			echo "</tr>";

			# organise the custom fields
			if (isset($pdata))
			{
				# standard and custom fields
				foreach($pdata['require'] as $field=>$value)
				{
					echo "<tr>";
					echo "<td><p><b>".$field.'</b></p></td>';
					sfa_paint_pdata_input('l-'.$field, $pdata['label'][$field]);
					sfa_paint_pdata_checkbox(__("Required", "sforum"), 'r-'.$field, $value);
					sfa_paint_pdata_checkbox(__("Include", "sforum"), 'i-'.$field, $pdata['include'][$field]);
					sfa_paint_pdata_checkbox(__("Display", "sforum"), 'd-'.$field, $pdata['display'][$field]);
					echo "</tr>";
				}

				# system data - display only
				if (isset($pdata['system']))
				{
					foreach($pdata['system'] as $field=>$value)
					{
						echo "<tr>";
						echo "<td><p><b>".$field."</b></p></td>";
						sfa_paint_pdata_input('l-'.$field, $pdata['label'][$field]);
						echo "<td></td>";
						echo "<td></td>";
						echo sfa_paint_pdata_checkbox(__("Display", "sforum"), $field, $value);
						echo "</tr>";
					}
				}
			}

			sfa_paint_close_fieldset();
		sfa_paint_close_panel();
	sfa_paint_close_tab();

?>
	<div class="sfform-submit-bar">
	<input type="submit" class="sfform-panel-button" id="saveit" name="saveit" value="<?php esc_attr_e(__('Update Profile Data', 'sforum')); ?>" />
	</div>
	</form>
<?php
	return;
}



function sfa_paint_pdata_checkbox($label, $name, $value)
{
	global $tab;

	echo "<td class='sflabel'>\n";
	echo "<label for='sf-".$name."'>$label:</label>\n";
	echo "<input type='checkbox' tabindex='$tab' name='$name' id='sf-$name' ";
	if($value == true)
	{
		echo "checked='checked' ";
	}
	echo "/>\n";
	echo "</td>\n";
	$tab++;
	return;
}



function sfa_paint_pdata_input($name, $value)
{
	global $tab;

	echo "<td>\n";
	echo '<input type="text" class="sfpostcontrol" tabindex="'.$tab.'" name="'.$name.'" value="'.esc_attr($value).'" />';
	echo "</td>\n";

	$tab++;
	return;
}

?>