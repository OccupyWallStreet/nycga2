<?php
/*
Simple:Press
Admin Toolbox Prune Form
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

# function to display topics that meet the pruning filter critera.  Individual topics or all topics can be selected for pruning
function sfa_toolbox_prune_form($topicdata)
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfprunetopics').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfreloaddb').click();
			jQuery('#sfmsgspot').fadeIn();
			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});
});
</script>
<?php

	global $wpdb;

    $ahahURL = SFHOMEURL."index.php?sf_ahah=toolbox-loader&amp;saveform=updatedb";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfprunetopics" name="sfprunetopics">
	<?php echo(sfc_create_nonce('forum-adminform_prunetopics')); ?>
<?php
	sfa_paint_options_init();

	sfa_paint_open_tab(__("Toolbox", "sforum")." - ".__("Prune Database", "sforum"));
		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Prune Database Topics", "sforum"), 'true', 'select-topics-to-prune', false);
				if ($topicdata['message'] != '')
				{
					echo $topicdata['message'];
				} else {
					# grab the topics that meet the filter critera
					$date = $topicdata['date'];
					$forum_id = $topicdata['id'];
					$sql = "SELECT * FROM ".SFTOPICS.
						   " WHERE topic_date <= '".$date."'".$forum_id.
						   " ORDER BY topic_date, forum_id ASC";
					$topics = $wpdb->get_results($sql);

					# display the list of topics if any met the criteria
					if ($topics)
					{
?>
							<h4><?php _e("Select Topics To Prune", "sforum") ?></h4>
							<div class="sfform-panel-spacer"></div>
							<div id="checkboxset">
								<table class="sfsubtable" cellpadding="0" cellspacing="0">
									<tr>
										<th width="5%" align="center"><?php _e("Delete", "sforum") ?></th>
										<th width="5%" align="center"><?php _e("Topic ID", "sforum") ?></th>
										<th width="20%" align="center"><?php _e("Topic Date", "sforum") ?></th>
										<th width="20%" align="center"><?php _e("Forum", "sforum") ?></th>
										<th><?php _e("Topic Title", "sforum") ?></th>
									</tr>
<?php
									$tcount = 0;
									foreach ($topics as $topic)
									{
?>
										<tr>
											<td class="sflabel" align="center" colspan="2">
												<label for="sftopic<?php echo $tcount; ?>"><?php echo $topic->topic_id; ?></label>
												<input type="checkbox" id="sftopic<?php echo $tcount; ?>" name="topic<?php echo $tcount; ?>" value="<?php echo $topic->topic_id; ?>" />
											</td>
											<td align="center"><?php echo sf_date('d', $topic->topic_date); ?></td>
											<td>
												<?php $forum_name = $wpdb->get_var("SELECT forum_name FROM ".SFFORUMS." WHERE forum_id='".$topic->forum_id."'"); ?>
												<?php echo sf_filter_title_display($forum_name); ?>
											</td>
											<td><?php echo sf_filter_title_display($topic->topic_name); ?></td>
										</tr>
<?php
										$tcount++;
									}
?>
								</table>
								<input type="hidden" name="tcount" value="<?php echo($tcount); ?>" />
							</div>

							<table>
								<tr>
									<td><input type="button" class="button button-highlighted" value="<?php esc_attr_e(__('Check All', 'sforum')); ?>" onclick="sfjcheckAll(jQuery('#checkboxset'))" /></td>
									<td></td>
									<td><input type="button" class="button button-highlighted" value="<?php esc_attr_e(__('Uncheck All', 'sforum')); ?>" onclick="sfjuncheckAll(jQuery('#checkboxset'))" /></td>
								</tr>
							</table>
							<div class="clearboth"></div>
							<div class="sfform-panel-spacer"></div>
							<div class="sfform-panel-spacer"></div>
<?php
			sfa_paint_close_fieldset(false);
		sfa_paint_close_panel();
	sfa_paint_close_tab();
?>
	<div class="sfform-submit-bar">
	<input type="submit" class="sfform-panel-button" id="sfprunetopics" name="sfprunetopics" value="<?php esc_attr_e(__('Prune Database', 'sforum')); ?>" />
	</div>
	</form>
<?php
					} else {
			    		echo __("No Topics Found using the Specified Filter Criteria.", "sforum");
					}
				}

	return;
}

?>