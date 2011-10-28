<?php
/*
Simple:Press
Admin Options Topics Form
$LastChangedDate: 2011-01-06 08:38:37 -0700 (Thu, 06 Jan 2011) $
$Rev: 5270 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_options_topics_form()
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sftopicsform').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfmsgspot').fadeIn();
			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});
});
</script>
<?php

	$sfoptions = sfa_get_topics_data();

    $ahahURL = SFHOMEURL."index.php?sf_ahah=options-loader&amp;saveform=topics";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sftopicsform" name="sftopics">
	<?php echo(sfc_create_nonce('forum-adminform_topics')); ?>
<?php

	sfa_paint_options_init();

#== TOPIC Tab ============================================================

	sfa_paint_open_tab(__("Options", "sforum")." - ".__("Topic Settings", "sforum"));

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Topic View Formatting", "sforum"), true, 'topic-view-formatting');
				sfa_paint_input(__("Topics to Display Per Page", "sforum"), "sfpagedtopics", $sfoptions['sfpagedtopics']);
				sfa_paint_input(__("Number of Topic Paging Links to show", "sforum"), "sfpaging", $sfoptions['sfpaging'], false, false);
				sfa_paint_checkbox(__("Sort Topics by Most recent Postings (newest first)", "sforum"), "sftopicsort", $sfoptions['sftopicsort']);
				sfa_paint_checkbox(__("Display Sub Forums below Parent Forum", "sforum"), "showsubforums", $sfoptions['showsubforums']);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Topic Row Display Options", "sforum"), true, 'topic-row-display');
				sfa_paint_checkbox(__("Show Topic Information Icons", "sforum"), "statusicons", $sfoptions['statusicons']);
				sfa_paint_checkbox(__("Show Post Rating Aggregation", "sforum"), "postrating", $sfoptions['postrating']);
				sfa_paint_checkbox(__("Show Topic Page Links", "sforum"), "pagelinks", $sfoptions['pagelinks']);
				sfa_paint_checkbox(__("Show Current Topic Status", "sforum"), "topicstatus", $sfoptions['topicstatus']);
				sfa_paint_checkbox(__("Show Topic Tags", "sforum"), "topictags", $sfoptions['topictags']);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

	sfa_paint_tab_right_cell();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Topic Tags Entry (if enabled on current forum)", "sforum"), true, 'topic-tag-formatting');
				sfa_paint_input(__("Max Number of Tags Per Topic (0 = unlimited)", "sforum"), "sfmaxtags", $sfoptions['sfmaxtags'], false, false);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Topic View Columns", "sforum"), true, 'topic-view-columns');
				sfa_paint_checkbox(__("Show the Topic Started Column", "sforum"), "tc_first", $sfoptions['tc_first']);
				sfa_paint_checkbox(__("Show the Last Post Column", "sforum"), "tc_last", $sfoptions['tc_last']);
				sfa_paint_checkbox(__("Show the Post Count Column", "sforum"), "tc_posts", $sfoptions['tc_posts']);
				sfa_paint_checkbox(__("Show the Views Count Column", "sforum"), "tc_views", $sfoptions['tc_views']);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("The Topic", "sforum"), true, 'the-topic');
				sfa_paint_checkbox(__("Show First Post Extract as Tooltip", "sforum"), "posttip", $sfoptions['posttip']);
				sfa_paint_checkbox(__("Display the Print Topic Button", "sforum"), "print", $sfoptions['print']);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

	sfa_paint_close_tab();

?>
	<div class="sfform-submit-bar">
	<input type="submit" class="sfform-panel-button" id="saveit" name="saveit" value="<?php esc_attr_e(__('Update Topic Options', 'sforum')); ?>" />
	</div>
	</form>
<?php
	return;
}

?>