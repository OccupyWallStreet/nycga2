<?php
/*
Simple:Press
Admin Components Special Rank Add Member Form
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_components_sr_add_members_form($rank_id)
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfmembernew<?php echo $rank_id; ?>').ajaxForm({
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
	sfa_paint_options_init();

    $ahahURL = SFHOMEURL."index.php?sf_ahah=components-loader&amp;saveform=specialranks&amp;action=addmember&amp;id=".$rank_id;
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfmembernew<?php echo $rank_id; ?>" name="sfmembernew<?php echo $rank_id ?>">
<?php
		echo(sfc_create_nonce('special-rank-add'));
		sfa_paint_open_tab(__("Components", "sforum")." - ".__("Forum Ranks", "sforum"));
			sfa_paint_open_panel();
				sfa_paint_open_fieldset(__("Add Members", "sforum"), false, '', false);
?>
					<p align="center"><?php _e("Select Members To Add (use CONTROL for multiple members)", "sforum") ?></p>
<?php
                	$from = esc_js(__("Eligible Members", "sforum"));
                	$to = esc_js(__("Selected Members", "sforum"));
                    $action = 'addru';
                	include_once (SF_PLUGIN_DIR.'/library/ahah/sfc-ahah.php');
?>
					<div class="clearboth"></div>
<?php
				sfa_paint_close_fieldset(false);
			sfa_paint_close_panel();
		sfa_paint_close_tab();
        $loc = 'sfrankshow-'.$rank_id;
?>
		<div class="sfform-submit-bar">
		<input type="submit" class="sfform-panel-button" id="sfmembernew<?php echo $rank_id; ?>" name="sfmembernew<?php echo $rank_id; ?>" onclick="javascript:jQuery('#amember_id<?php echo $rank_id; ?> option').each(function(i) {jQuery(this).attr('selected', 'selected');});" value="<?php esc_attr_e(__('Add Members', 'sforum')); ?>" />
		<input type="button" class="sfform-panel-button" onclick="sfjtoggleLayer('<?php echo $loc; ?>');javascript:jQuery('#members-<?php echo $rank_id; ?>').html('');" id="addmemberscancel<?php echo $rank_id; ?>" name="addmemberscancel<?php echo $rank_id; ?>" value="<?php esc_attr_e(__('Cancel', 'sforum')); ?>" />
		</div>
	</form>

	<div class="sfform-panel-spacer"></div>
<?php
	return;
}

?>