<?php
/*
Simple:Press
Admin Components Special Ranks Form
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_special_rankings_form($rankings)
{
	global $tab, $SFPATHS;

	echo '<tr>'."\n";
	echo '<th width="65%">'."\n";
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfaddspecialrank').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfreloadfr').click();
			jQuery('#sfmsgspot').fadeIn();
			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});
});
</script>
<?php
	echo '<table width="100%" cellspacing="0">';
	echo '<tr>';
	echo '<th style="padding:1px; text-align:center;width:50%;">'.__("Special Rank Name", "sforum").'</th>'."\n";
	echo '<th style="padding:1px; text-align:center;width:30%;">'.__("Special Rank Badge", "sforum").'</th>'."\n";
	echo '<th style="padding:1px; text-align:center;width:20%;">&nbsp;</th>'."\n";
	echo '</tr>';
	echo '</table>';
	echo '</td>';
	echo '<th style="text-align:center;width:30%;">'.__("Special Rank Members", "sforum").'</th>'."\n";
	echo '<th style="text-align:center;width:5%;">'.__("Remove", "sforum").'</th>'."\n";
	echo '</tr>'."\n";

	# display rankings info
	if ($rankings)
	{
		foreach ($rankings as $rank)
		{
			$data = unserialize($rank['meta_value']);

			echo '<tr>'."\n";
			echo '<td width="100%" colspan="4">'."\n";
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfspecialrankupdate<?php echo $rank['meta_id']; ?>').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfmsgspot').fadeIn();
			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});
});
</script>
<?php
			echo '<div id="srank'.$rank['meta_id'].'">'."\n";

			echo '<table width="100%" cellspacing="0">'."\n";
			echo '<tr>'."\n";
			echo '<td width="65%">'."\n";
            $ahahURL = SFHOMEURL."index.php?sf_ahah=components-loader&amp;saveform=specialranks&amp;action=updaterank&amp;id=".$rank['meta_id'];
?>
			<form action="<?php echo($ahahURL); ?>" method="post" id="sfspecialrankupdate<?php echo $rank['meta_id']; ?>" name="sfspecialrankupdate<?php echo $rank['meta_id']; ?>">
<?php
			echo (sfc_create_nonce('special-rank-update'));
			echo '<table width="100%">';
			echo '<tr>';
			echo '<td width="50%">';
			echo '<input type="text" class="sfpostcontrol" size="20" tabindex="'.$tab.'" name="specialrankdesc['.$rank['meta_id'].']" value="'.$rank['meta_key'].'" />'."\n";
			echo '</td>';
			echo '<td width="30%" align="center">';
			sfa_select_icon_dropdown('specialrankbadge['.$rank['meta_id'].']', __("Select Badge", "sforum"), SF_STORE_DIR.'/'.$SFPATHS['ranks'].'/', $data['badge']);
			echo '</td>';
			echo '<td width="20%" align="center">';
			echo '<input type="submit" class="sfform-submit-button" id="updatespecialrank'.$rank["meta_id"].'" name="updatespecialrank'.$rank["meta_id"].'" value="'.esc_attr(__('Update Rank', 'sforum')).'" />';
			echo '</td>';
			echo '</tr>';
			echo '</table>';
			echo '</form>';
			echo '</td>'."\n";
			echo '<td width="30%" align="center" style="vertical-align:middle">'."\n";
            $loc = '#sfrankshow-'.$rank['meta_id'];
            $loc2 = 'sfrankshow-'.$rank['meta_id'];
            $site = SFHOMEURL."index.php?sf_ahah=components&amp;action=show&amp;key=".$rank['meta_id'];
			$gif= SFADMINIMAGES."working.gif";
			$text = esc_js(__("Show/Hide", "sforum"));
?>
			<input type="button" id="show<?php echo($rank['meta_id']); ?>" class="button button-highlighted" value="<?php echo($text); ?>" onclick="sfjtoggleLayer('<?php echo $loc2; ?>');sfjshowMemberList('<?php echo($site); ?>', '<?php echo($gif); ?>', '<?php echo($rank['meta_id']); ?>');" />
<?php
            $base = SFHOMEURL."index.php?sf_ahah=components-loader";
			$target = "members-".$rank['meta_id'];
			$image = SFADMINIMAGES;
?>
			<input type="button" id="add<?php echo ($rank['meta_id']); ?>" class="button button-highlighted" value="<?php esc_attr_e(__('Add', 'sforum')); ?>" onclick="jQuery('<?php echo $loc; ?>').show();sfjLoadForm('addmembers', '<?php echo($base); ?>', '<?php echo($target); ?>', '<?php echo($image); ?>', '<?php echo ($rank['meta_id']); ?>', 'open'); " />
			<input type="button" id="remove<?php echo ($rank['meta_id']); ?>" class="button button-highlighted" value="<?php esc_attr_e(__('Remove', 'sforum')); ?>" onclick="jQuery('<?php echo $loc; ?>').show();sfjLoadForm('delmembers', '<?php echo($base); ?>', '<?php echo($target); ?>', '<?php echo($image); ?>', '<?php echo ($rank['meta_id']); ?>', 'open'); " />
<?php
			$tab++;
			echo '</td>'."\n";
			echo '<td class="sflabel" align="center" width="5%">'."\n";
			$gif = SFADMINIMAGES."working.gif";
            $site = SFHOMEURL."index.php?sf_ahah=components&amp;action=del_specialrank&amp;key=".$rank['meta_id'];
?>
			<img onclick="sfjDelRank('<?php echo $site; ?>', '<?php echo $gif; ?>', '1', 'srank<?php echo $rank['meta_id']; ?>');" src="<?php echo SFADMINIMAGES; ?>del_cfield.png" title="<?php esc_attr_e(__("Delete Special Rank", "sforum")); ?>" alt="" />
<?php
			echo '</td>'."\n";

			echo '</tr>'."\n";
			echo '<tr class="inline_edit" id="sfrankshow-'.$rank["meta_id"].'">';
			echo '<td colspan="4">';
            echo '<div id="members-'.$rank["meta_id"].'"></div>';
		    echo '</td>';
			echo '</tr>';
			echo '</table>'."\n";
			echo '</div>'."\n";
			echo '</td>'."\n";
			echo '</tr>'."\n";
		}
	}

	# always have one empty slot available for new rank
	echo '<tr>'."\n";
	echo '<td align="right" width="100%" colspan="3">'."\n";
    $ahahURL = SFHOMEURL."index.php?sf_ahah=components-loader&amp;saveform=specialranks&amp;action=newrank";
?>
	<form action="<?php echo ($ahahURL); ?>" method="post" name="sfaddspecialrank" id="sfaddspecialrank">
<?php
	echo(sfc_create_nonce('special-rank-new'));
	$tab++;
	echo '<table>';
	echo '<tr>';
	echo '<td>';
	echo '<input type="text" class="sfpostcontrol" size="25" tabindex="'.$tab.'" name="specialrank" value="" />'."\n";
	echo '</td>';
	echo '<td>';
	echo '<input type="submit" class="sfform-submit-button" id="addspecialrank" name="addspecialrank" value="'.esc_attr(__('Add Special Rank', 'sforum')).'" />';
	echo '</td>';
	echo '</tr>';
	echo '</table>';
	echo '</form>';
	echo '</td>'."\n";
	echo '</tr>'."\n";

	return;
}

?>