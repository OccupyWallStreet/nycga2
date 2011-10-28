<?php
/*
Simple:Press
Admin Components Topic Status Form
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_components_topicstatus_form()
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sftopicstatusform').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfreloadts').click();
			jQuery('#sfmsgspot').fadeIn();
			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});
});
</script>
<?php

	$sfcomps = sfa_get_topicstatus_data();

    $ahahURL = SFHOMEURL."index.php?sf_ahah=components-loader&amp;saveform=topicstatus";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sftopicstatusform" name="sftopicstatus">
	<?php echo(sfc_create_nonce('forum-adminform_topicstatus')); ?>
<?php

	sfa_paint_options_init();

#== TOPIC STATUS Tab ============================================================

	sfa_paint_open_tab(__("Components", "sforum")." - ".__("Topic Status", "sforum"));

	sfa_paint_open_panel();

	for($x=0; $x<count($sfcomps['topic-status'])+1; $x++)
	{
		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Topic Status Set", "sforum"), !$x, 'topic-status-sets');

				if(isset($sfcomps['topic-status'][$x]))
				{
					sfa_paint_input(__("Topic Status Set Name", "sforum"), "sftopstatname[$x]", sf_filter_title_display($sfcomps['topic-status'][$x]['meta_key']), false, false);
					sfa_paint_input(__("Enter status phrases - separate with a comma. Enter them in the order they are to appear in the selection list", "sforum"), "sftopstatwords[$x]", sf_filter_title_display($sfcomps['topic-status'][$x]['meta_value']), false, true);
					sfa_paint_hidden_input("sftopstatid[$x]", $sfcomps['topic-status'][$x]['meta_id']);
				} else {
					sfa_paint_input(__("Topic Status Set Name", "sforum"), "sftopstatname[$x]", '', false, false);
					sfa_paint_input(__("Enter status phrases - separate with a comma. Enter them in the order they are to appear in the selection list", "sforum"), "sftopstatwords[$x]", '', false, true);
					sfa_paint_hidden_input("sftopstatid[$x]", '');
				}

				if(isset($sfcomps['topic-status'][$x]['meta_id']))
				{
					echo "<tr valign='top'>\n";
					echo "<td class='sflabel' width='100%' colspan='2'>\n";
					echo "<label for='sftopstatdel-".$x."'>".__("Delete this Topic Status Set", "sforum")."</label>\n";
					echo "<input type='checkbox' tabindex='51' name='sftopstatdel[".$x."]' id='sftopstatdel-".$x."' />\n";
					echo "</td>\n";
					echo "</tr>\n";
				}
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();
	}

	sfa_paint_close_panel();

	sfa_paint_close_tab();

?>
	<div class="sfform-submit-bar">
	<input type="submit" class="sfform-panel-button" id="saveit" name="saveit" value="<?php esc_attr_e(__('Update Topic Status Component', 'sforum')); ?>" />
	</div>
	</form>
<?php
	return;
}

?>