<?php
/*
Simple:Press
Admin Options style Form
$LastChangedDate: 2011-06-05 09:16:54 -0700 (Sun, 05 Jun 2011) $
$Rev: 6253 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_options_style_form()
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfstyleform').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfreloadst').click();
			jQuery('#sfmsgspot').fadeIn();
//			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});
});
</script>
<?php

	$sfoptions = sfa_get_style_data();

    $ahahURL = SFHOMEURL."index.php?sf_ahah=options-loader&amp;saveform=style";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfstyleform" name="sfstyle">
	<?php echo(sfc_create_nonce('forum-adminform_style')); ?>
<?php

	sfa_paint_options_init();

#== STYLE Tab ============================================================

	sfa_paint_open_tab(__("Options", "sforum")." - ".__("Style Settings", "sforum"));

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Forum Skin", "sforum"), true, 'forum-skin');
				sfa_paint_select_start(__("Select Skin", "sforum"), "sfskin", "sfskin");
				echo(sfa_create_skin_select($sfoptions['sfskin']));
				sfa_paint_select_end();
				sfa_paint_checkbox(__("Use CSS Source files instead of minimized CSS", "sforum"), "sfcsssrc", $sfoptions['sfcsssrc']);
				sfa_paint_checkbox(__("Use Text Wrap Width for Posts", "sforum"), "postwrap", $sfoptions['postwrap']);
				sfa_paint_input(__("Entire Post Content", "sforum").' - '.__("Maximum width displayed", "sforum") , "postwidth", $sfoptions['postwidth']);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Adjust Font Size", "sforum"), true, 'adjust-font-size');
				sfa_paint_input(__("Enter a Base Font Size (or Leave Empty)", "sforum"), "sfsize", $sfoptions['sfsize']);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

	sfa_paint_tab_right_cell();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Forum Icons", "sforum"), true, 'forum-icons');
				echo(sfa_create_icon_select(__("Select Icon Set", "sforum"), "sficon", "sficon", $sfoptions['sficon']));
				sfa_paint_select_end();
				sfa_paint_checkbox(__("Update Icons to Default Settings for this Skin if Template Exists?", "sforum"), "sftemplate", $sfoptions['sftemplate']);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Forum Float Clear", "sforum"), true, 'forum-clear-floats');
				sfa_paint_checkbox(__("Clear Float Styles Before Outputting Forum Display Code", "sforum"), "sffloatclear", $sfoptions['sffloatclear']);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Filter WP List Pages", "sforum"), true, 'filter-wp-list-pages');
				sfa_paint_checkbox(__("Filter WP List Pages", "sforum"), "sfwplistpages", $sfoptions['sfwplistpages']);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Display Icon Text", "sforum"), true, 'display-icon-text', false);

				echo '<div id="checkboxset">';
				$i = count($sfoptions['icon-list']);
//				$x = 0;

				$rows  = ($i/4);
				if (!is_int($rows))
				{
					$rows = (intval($rows)+1);
				}
				$thisrow = 0;
				$closed = false;
				echo '<table class="outsershell" width="100%" border="0"><tr valign="top">';

				foreach ($sfoptions['icon-list'] as $key=>$value)
				{

					if ($thisrow == 0)
					{
						echo '<td width="25%" valign="top">';
						echo '<table class="form-table">';
						$closed = false;
					}
					$iName = str_replace(' ', '_', $key);
					sfa_paint_checkbox(__($key, "sforum"), $iName, $value, false, false, false);
//					$x++;

					$thisrow++;
					if ($thisrow == $rows)
					{
						echo '</table>';
						echo '</td>';
						$thisrow = 0;
						$closed = true;
					}
				}

				if (!$closed)
				{
					echo '</table></td>';
				}
				echo '</tr></table>';
				echo '</div>';

				echo '<br /><div class="clearboth"></div>';
?>
				<table>
				<tr>
				<td>

				<input type="button" class="button button-highlighted" value="<?php esc_attr_e(__('Check All', 'sforum')); ?>" onclick="sfjcheckAll(jQuery('#checkboxset'))" />

				</td>
				<td />
				<td>

				<input type="button" class="button button-highlighted" value="<?php esc_attr_e(__('Uncheck All', 'sforum')); ?>" onclick="sfjuncheckAll(jQuery('#checkboxset'))" />

				</td>
				</tr>
				</table>
<?php

	sfa_paint_close_tab();

?>
	<div class="sfform-submit-bar">
	<input type="submit" class="sfform-panel-button" id="saveit" name="saveit" value="<?php esc_attr_e(__('Update Style Options', 'sforum')); ?>" />
	</div>
	</form>
<?php
	return;
}

function sfa_create_skin_select($skin)
{
	global $SFPATHS;

	$path = SF_STORE_DIR . '/'.$SFPATHS['styles'].'/skins/';
	$out='';
	$default='';
	$dlist = opendir($path);

	while (false !== ($file = readdir($dlist)))
	{
		if (($file != "." && $file != "..") && (is_dir($path.'/'.$file)))
		{
			if($file == $skin)
			{
				$default = 'selected="selected" ';
			} else {
				$default - null;
			}
			$out.='<option '.$default.'value="'.esc_attr($file).'">'.$file.'</option>'."\n";
			$default='';
		}
	}
	closedir($dlist);
	return $out;
}

function sfa_create_icon_select($label, $name, $helpname, $icon)
{
	global $tab, $SFPATHS;

	$out = "<tr valign='top'>\n";
	$out.= "<td class='sflabel' width='60%'>\n$label";
	$out.= "\n</td>\n";
	$out.= "<td>\n";
	$out.= "<select style='width:130px' class=' sfacontrol' tabindex='$tab' name='$name' onchange='jQuery(\"#sfupdateicons\").show();'>\n";
	$tab++;

	$path = SF_STORE_DIR . '/'.$SFPATHS['styles'].'/icons/';

	$default='';
	$dlist = opendir($path);

	while (false !== ($file = readdir($dlist)))
	{
		if (($file != "." && $file != "..") && (is_dir($path.'/'.$file)))
		{
			if($file == $icon)
			{
				$default = 'selected="selected" ';
			} else {
				$default - null;
			}
			$out.='<option '.$default.'value="'.esc_attr($file).'">'.$file.'</option>'."\n";
			$default='';
		}
	}
	closedir($dlist);

	$out.= "</select>\n";
	$out.= "</td>\n";
	$out.= "</tr>\n";

	echo($out);
	return;
}

?>