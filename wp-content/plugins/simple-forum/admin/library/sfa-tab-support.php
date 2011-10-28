<?php
/*
Simple:Press
Admin Panels - Options/Components Tab Rendering Support
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

# == PAINT ROUTINES

# ------------------------------------------------------------------
# sfa_paint_options_init()
# Initialises the tab index sequence starting with 100
# ------------------------------------------------------------------
function sfa_paint_options_init()
{
	global $tab;

	$tab=100;
	return;
}

# ------------------------------------------------------------------
# sfa_paint_open_tab()
# Creates the containing block around a form or main section
# ------------------------------------------------------------------
function sfa_paint_open_tab($tabname, $full=false)
{
	echo "<div class='sfform-panel'>";
	echo "<div class='sfform-panel-head'><span class='sftitlebar'>".$tabname."</span></div>\n";

	echo "<table width='100%' cellpadding='0' cellspacing='0'>\n";
	echo "<tr valign='top'>\n";
    if ($full)
    	echo "<td colspan='2' width='50%'>\n";
    else
    	echo "<td width='50%'>\n";
	return;
}

# ------------------------------------------------------------------
# sfa_paint_options_init()
# Initialises the tab index sequence starting with 100
# ------------------------------------------------------------------
function sfa_paint_close_tab()
{
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</div>\n";
	return;
}

function sfa_paint_tab_right_cell()
{
	echo "</td>\n";
	echo "<td width='50%'>\n";
	return;
}

function sfa_paint_open_panel()
{
	echo "<table width='100%'>\n";
	return;
}

function sfa_paint_close_panel()
{
	echo "</table>\n";
	return;
}

function sfa_paint_open_fieldset($legend, $displayhelp=false, $helpname='', $opentable=true)
{
	global $adminhelpfile;

	echo "<tr>\n";
	echo "<td>\n";
	echo "<fieldset class='sffieldset'>\n";
	echo "<legend><strong>$legend</strong></legend>\n";
	if($displayhelp) echo sfa_paint_help($helpname, $adminhelpfile);
	if($opentable)
	{
		echo "<table class='form-table' width='100%'>\n";
	}
	return;
}

function sfa_paint_close_fieldset($closetable=true)
{
	if($closetable)
	{
		echo "</table>\n";
	}
	echo "</fieldset>\n";
	echo "</td>\n";
	echo "</tr>\n";
	return;
}

function sfa_paint_input($label, $name, $value, $disabled=false, $large=false)
{
	global $tab;

	echo "<tr valign='top'>\n";
	if($large)
	{
		echo "<td class='sflabel' width='40%'>\n";
	} else {
		echo "<td class='sflabel' width='60%'>\n";
	}
	echo "<span class='sfalignleft'>".$label.":</span>";

	echo "</td>\n";
	echo "<td>\n";
	echo '<input type="text" class="sfpostcontrol" tabindex="'.$tab.'" name="'.$name.'" value="'.esc_attr($value).'" ';
	if($disabled == true)
	{
		echo "disabled='disabled' ";
	}
	echo "/></td>\n";

	echo "</tr>\n";
	$tab++;
	return;
}

function sfa_paint_file($label, $name, $disabled, $large, $path)
{
	global $tab;

	echo "<tr valign='top'>\n";
	if($large)
	{
		echo "<td class='sflabel' width='30%' valign='top' >\n";
	} else {
		echo "<td class='sflabel' width='50%'>\n";
	}
	echo $label.":</td>\n";
	echo "<td>\n";
	if (is_writable($path))
	{
		echo '<div id="sf-upload-button" class="sfform-upload-button">'.__('Browse', 'sforum').'</div>';
		echo '<div id="sf-upload-status"></div>';
	} else {
		echo '<div id="sf-upload-status">';
		echo '<p class="sf-upload-status-fail">'.__("Sorry, uploads disabled! Storage location does not exist or is not writable. Please see forum - configuration - storage locations to correct.", "sforum").'</p>';
		echo '</div>';
	}
	echo "</td>\n";

	echo "</tr>\n";
	$tab++;
	return;
}

function sfa_paint_hidden_input($name, $value)
{
	echo "<tr style='display:none'><td>\n";
	echo "<input type='hidden' name='$name' value='".esc_attr($value)."' />";
	echo "</td></tr>\n";
	return;
}

function sfa_paint_textarea($label, $name, $value, $submessage='', $rows=6)
{
	global $tab;

	echo "<tr valign='top'>\n";
	echo "<td class='sflabel' width='60%'>\n$label";
	if(!empty($submessage))
	{
		echo "<br /><small><strong>".esc_html($submessage)."</strong></small>\n";
	}
	echo "</td>\n";
	echo "<td>\n";
	echo "<textarea rows='".$rows."' cols='80' class='sftextarea' tabindex='$tab' name='$name'>".esc_html($value)."</textarea>\n";
	echo "</td>\n";
	echo "</tr>\n";
	$tab++;
	return;
}

function sfa_paint_wide_textarea($label, $name, $value, $submessage='', $xrows=3)
{
	global $tab;

	echo "<tr valign='top'>\n";
	echo "<td class='sflabel' width='100%' colspan='2'>\n$label:";
	if(!empty($submessage))
	{
		echo "<small><br /><strong>$submessage</strong><br /><br /></small>\n";
	}
	echo "</td></tr><tr><td colspan='2'>";
	echo "<textarea rows='".$xrows."' cols='80' class='sftextarea' tabindex='$tab' name='$name'>".esc_attr($value)."</textarea>\n";
	echo "</td>\n";
	echo "</tr>\n";
	$tab++;
	return;
}

function sfa_paint_checkbox($label, $name, $value, $disabled=false, $large=false, $displayhelp=true)
{
	global $tab;

	echo "<tr valign='top'>\n";

	echo "<td class='sflabel' width='100%' colspan='2'>\n";
	echo '<table class="form-table table-cbox"><tr><td class="td-cbox">';
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

function sfa_paint_radiogroup($label, $name, $values, $current, $large=false, $displayhelp=true)
{
	global $tab;

	$pos = 1;

	echo "<tr valign='top'>\n";

	echo "<td class='sflabel' width='100%' colspan='2'>\n";
	echo '<table class="form-table table-cbox"><tr><td class="td-cbox">';

	echo $label;
	echo ":\n</td>\n";
	echo "<td width='70%' class='td-cbox'>\n";
	foreach($values as $value)
	{
		$check = '';
		if($current == $pos) $check = ' checked="checked" ';
		echo '<label for="sfradio-'.$tab.'" class="sflabel radio">'.esc_html(__($value, "sforum")).'</label>'."\n";
		echo '<input type="radio" name="'.$name.'" id="sfradio-'.$tab.'"  tabindex="'.$tab.'" value="'.$pos.'" '.$check.' />'."\n";
		$pos++;
		$tab++;
	}
	echo "</td></tr></table>";
	echo "</td>\n";
	echo "</tr>\n";
	$tab++;
	return;
}

function sfa_paint_radiogroup_confirm($label, $name, $values, $current, $msg, $large=false, $displayhelp=true)
{
	global $tab;

	$pos = 1;

	echo "<tr valign='top'>\n";

	echo "<td class='sflabel' width='100%' colspan='2'>\n";
	echo '<table class="form-table table-cbox"><tr><td class="td-cbox">';
	echo $label;
	echo ":\n</td>\n";
	echo "<td width='70%' class='td-cbox'>\n";
	foreach($values as $value)
	{
		$check = '';
		$select = '';
		if ($current == $pos)
		{
			$check = " checked = 'checked' ";
		} else {
			$select = " onclick ='sfjtoggleLayer(\"confirm-".$name."\")'";
		}
		echo "<input type='radio' id='sfradio".$pos."' name='".$name."' tabindex='".$tab."' value='".$pos."'".$check.$select." />";
		echo "<label class='sflabel radio' for='sfradio".$pos."'>".esc_html(__($value, 'sforum'))."</label><br />";
		$pos++;
		$tab++;
	}
	echo "</td></tr></table>";
	echo "</td>\n";
	echo "</tr>\n";


	echo "<tr valign='top' id='confirm-".$name."' style='display:none'>";

		echo "<td colspan='2'>";

			echo '<table class="form-table table-cbox">';

				echo '<tr>';

					echo "<td class='longmessage'>".$msg."</td>";

				echo "</tr><tr>";

					echo "<td class='sflabel' width='100%'>";

						echo '<table class="form-table table-cbox"><tr><td class="td-cbox">';
						echo "<label for='sfconfirm-box-".$name."'>".esc_html(__('Confirm', 'sforum'))."</label>\n";
						echo "<input type='checkbox' name='confirm-box-".$name."' id='sfconfirm-box-".$name."' tabindex='$tab' />\n";
						echo "</td></tr></table>";

					echo "</td>";

				echo "</tr>";

			echo "</table>";

		echo '</td>';

	echo "</tr>";


	$tab++;
	return;
}

function sfa_paint_select_start($label, $name, $helpname)
{
	global $tab;

	echo "<tr valign='top'>\n";
	echo "<td class='sflabel' width='60%'>\n$label:";
	echo "\n</td>\n";
	echo "<td>\n";
	echo "<select style='width:130px' class=' sfacontrol' tabindex='$tab' name='$name'>\n";
	$tab++;
	return;
}

function sfa_paint_select_end()
{
	echo "</select>\n";
	echo "</td>\n";
	echo "</tr>\n";
	return;
}

function sfa_paint_link($link, $label)
{
	echo "<tr>\n";
	echo "<td class='sflabel' colspan='2'>\n";
	echo "<a href='".esc_url($link)."'>$label</a>\n";
	echo "</td>\n";
	echo "</tr>\n";
	return;
}

function sfa_paint_icon($icon)
{
	if(empty($icon)) return;

	$path = SFCUSTOM.$icon;
	if(!file_exists($path))
	{
		echo "<p class='sfoptionerror'>".sprintf(__("Custom Icon '%s' does not exist", "sforum"), $icon)."</p>\n";
	} else {
		echo "<p><img src='".esc_url(SFCUSTOMURL.$icon)."' alt='' /></p>\n";
	}
	return;
}

function sfa_paint_spacer()
{
	echo "<br /><div class='clearboth'></div>";
	return;
}

function sfa_paint_help($name, $helpfile, $show=true)
{
    $site = SFHOMEURL."index.php?sf_ahah=help&amp;file=".$helpfile."&amp;item=".$name;
	$out = '';

	$out.= '<div class="sfhelplink">';
	if($show)
	{
		$out.= '<a class="sfalignright sfxcontrol" href="'.$site.'" onclick="return hs.htmlExpand(this, { objectType: \'ajax\', preserveContent: true, width: 550} )">';
		$out.= __("Help", "sforum").'</a>'."\n";
	}
	$out.= '</div>';

	return $out;
}

?>