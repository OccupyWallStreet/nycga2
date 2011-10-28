<?php
/*
Simple:Press
Admin Profiles Custom Profile Fields Form
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_profiles_fields_form()
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sffieldsform').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfreloadcf').click();
			jQuery('#sfmsgspot').fadeIn();
			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});
});
</script>
<?php

	$cfields = sfa_get_fields_data();

    $ahahURL = SFHOMEURL."index.php?sf_ahah=profiles-loader&amp;saveform=fields";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sffieldsform" name="sffields">
	<?php echo(sfc_create_nonce('forum-adminform_fields')); ?>
<?php

	sfa_paint_options_init();

#== CUSTOM FIELDS Tab ============================================================

	sfa_paint_open_tab(__("Profiles", "sforum")." - ".__("Custom Profile Fields", "sforum"));
		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Custom Profile Fields", "sforum"), true, 'custom-fields');
			echo "<tr>";
			echo "<th width='30%' style='text-align:center'>".__("Custom Field Slug (not displayed)", "sforum")."</th>";
			echo "<th width='40%' style='text-align:center'>".__("Values (Select and Radio Only)", "sforum")."</th>";
			echo "<th width='20%' style='text-align:center'>".__("Custom Field Type", "sforum")."</th>";
			echo "<th width='10%' style='text-align:center'>".__("Delete Custom Field", "sforum")."</th>";
			echo "</tr>";

			# organize the custom fields
			if (isset($cfields))
			{
				foreach ($cfields as $x => $info)
				{
					$fields['id'][$x] = $info['meta_id'];
					$fields['name'][$x] = $info['meta_key'];
					$data = unserialize($info['meta_value']);
					$fields['type'][$x] = $data['type'];
					if ($data['type'] == 'radio' || $data['type'] == 'select')
					{
						$fields['selectvalues'][$x] = $data['selectvalues'];
					}
				}
			}

			# display custom field info
			for ($x=0; $x<count($cfields); $x++)
			{
				echo "<tr>";
				echo "<td width='100%' colspan='4' style='border-bottom:0px;padding:0px;'>";
				echo "<div id='cfield".$x."'>";
				echo '<table width="100%" cellspacing="0">';
				echo '<tr>';
				echo "<td class='sflabel' width='30%' align='left'>";
				echo "<input type='text' class='sfpostcontrol' size='40' name='cfieldname[]' value='".esc_attr($fields['name'][$x])."' />";
				echo "</td>";
				echo "<td width='40%' class='sflabel' align='center'>";
				$select = '';
				if (isset($fields['selectvalues'][$x])) $select = $fields['selectvalues'][$x];
				echo "<input type='text' class='sfpostcontrol' size='40' name='cfieldvalues[]' value='".esc_attr($select)."' />";
				echo "</td>";
				echo "<td width='20%' class='sflabel' align='center'>";
				echo "<select class='sfacontrol' name='cfieldtype[]'>";
				$cselected = $iselected = $tselected = $sselected = $rselected ='';
				if ($fields['type'][$x] == 'checkbox') $cselected = ' selected';
				if ($fields['type'][$x] == 'input') $iselected = ' selected';
				if ($fields['type'][$x] == 'textarea') $tselected = ' selected';
				if ($fields['type'][$x] == 'select') $sselected = ' selected';
				if ($fields['type'][$x] == 'radio') $rselected = ' selected';
				echo '<option value="checkbox"'.$cselected.'>'.__("Checkbox", "sforum").'</option>';
				echo '<option value="input"'.$iselected.'>'.__("Input", "sforum").'</option>';
				echo '<option value="textarea"'.$tselected.'>'.__("Textarea", "sforum").'</option>';
				echo '<option value="select"'.$sselected.'>'.__("Select", "sforum").'</option>';
				echo '<option value="radio"'.$rselected.'>'.__("Radio", "sforum").'</option>';
				if ($cselected == '' && $iselected == '' && $tselected == '' && $sselected == '' && $rselected == '') echo '<option value="error" selected>'.__("Error!", "sforum").'</option>';
				echo "</td>";
				echo "<td class='sflabel' width='10%' align='center'>";
                $site = esc_url(SFHOMEURL."index.php?sf_ahah=profiles&action=delete-cfield&id=".$fields['id'][$x]."&cfield=".$x."&fname=".$fields['name'][$x]);
				$gif = SFADMINIMAGES."working.gif";
				?>
				<img onclick="sfjDelCfield('<?php echo $site; ?>', '<?php echo $gif; ?>', 'cfield<?php echo $x; ?>');" src="<?php echo SFADMINIMAGES; ?>del_cfield.png" title="<?php esc_attr_e(__('Delete Custom Field', 'sforum')); ?>" alt="" />&nbsp;
				<?php
				echo '</td>';
				echo '</tr>';
				echo "</table>";
				echo "</div";
				echo "</td>";
				echo "</tr>";
			}

			# always have one empty slot available for new custom field
			echo "<tr>\n";
			echo "<td class='sflabel' width='30%'>\n";
			echo "<input type='text' class=' sfpostcontrol' size='40' name=\"cfieldname[]\" value='' />";
			echo "</td>\n";
			echo "<td class='sflabel' width='40%'>";
			echo "<input type='text' class=' sfpostcontrol' size='40' name=\"cfieldvalues[]\" value='' />";
			echo "</td>";
			echo "<td class='sflabel' width='20%' align='center'>\n";
			echo "<select class='sfcontrol' name='cfieldtype[]'>";
			echo '<option value="none">'.__("Custom Field Type", "sforum").'</option>';
			echo '<option value="checkbox">'.__("Checkbox", "sforum").'</option>';
			echo '<option value="input">'.__("Input", "sforum").'</option>';
			echo '<option value="textarea">'.__("Textarea", "sforum").'</option>';
			echo '<option value="select">'.__("Select", "sforum").'</option>';
			echo '<option value="radio">'.__("Radio", "sforum").'</option>';
			echo "</select>";
			echo "</td>";
			echo "<td class='sflabel' width='10%'></td>";
			echo "</tr>";
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();
	sfa_paint_close_tab();

?>
	<div class="sfform-submit-bar">
	<input type="submit" class="sfform-panel-button" id="saveit" name="saveit" value="<?php esc_attr_e(__('Update Custom Profile Fields', 'sforum')); ?>" />
	</div>
	</form>
<?php
	return;
}

?>