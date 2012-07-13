<?php
/*
Simple:Press
Admin Options Posts Form
$LastChangedDate: 2010-08-10 23:05:26 -0700 (Tue, 10 Aug 2010) $
$Rev: 4376 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_options_posts_form()
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfpostsform').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfmsgspot').fadeIn();
			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});
});
</script>
<?php

	$sfoptions = sfa_get_posts_data();

    $ahahURL = SFHOMEURL."index.php?sf_ahah=options-loader&amp;saveform=posts";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfpostsform" name="sfposts">
	<?php echo(sfc_create_nonce('forum-adminform_posts')); ?>
<?php

	sfa_paint_options_init();

#== POSTS Tab ============================================================

	sfa_paint_open_tab(__("Options", "sforum")." - ".__("Post Settings", "sforum"));

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Post View Formatting", "sforum"), true, 'post-view-formatting');
				sfa_paint_input(__("Posts to Display Per Page", "sforum"), "sfpagedposts", $sfoptions['sfpagedposts']);
				sfa_paint_input(__("Number of Post Paging Links to show", "sforum"), "sfpostpaging", $sfoptions['sfpostpaging'], false, false);
				sfa_paint_checkbox(__("Display User Info Above Post", "sforum"), "sfuserabove", $sfoptions['sfuserabove']);
				sfa_paint_checkbox(__("Sort Posts Newest to Oldest", "sforum"), "sfsortdesc", $sfoptions['sfsortdesc']);
				sfa_paint_checkbox(__("Display Subsequent Post-Edit Details", "sforum"), 'sfshoweditdata', $sfoptions['sfshoweditdata']);
				sfa_paint_checkbox(__("If Showing Edits - Show Last Edit Only", "sforum"), 'sfshoweditlast', $sfoptions['sfshoweditlast']);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Topic Tags", "sforum"), true, 'topic-tags');
				sfa_paint_checkbox(__("Display Topic Tags Above Posts", "sforum"), "sftagsabove", $sfoptions['sftagsabove']);
				sfa_paint_checkbox(__("Display Topic Tags Below Posts", "sforum"), "sftagsbelow", $sfoptions['sftagsbelow']);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Topic Status", "sforum"), true, 'topic-status');
				sfa_paint_checkbox(__("Display Topic Status in Heading", "sforum"), "topicstatushead", $sfoptions['topicstatushead']);
				sfa_paint_checkbox(__("Display Topic Status Change Control", "sforum"), "topicstatuschanger", $sfoptions['topicstatuschanger']);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

	sfa_paint_tab_right_cell();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("The Post", "sforum"), true, 'the-post');
				sfa_paint_checkbox(__("Display User's Online Status", "sforum"), "online", $sfoptions['online']);
				sfa_paint_checkbox(__("Display the Time of Post", "sforum"), "time", $sfoptions['time']);
				sfa_paint_checkbox(__("Display the Date of Post", "sforum"), "date", $sfoptions['date']);
				sfa_paint_checkbox(__("Display the Type/Rank of User", "sforum"), "usertype", $sfoptions['usertype']);
				sfa_paint_checkbox(__("Display Both the Type/Rank and Badge of User (if displaying type/rank and using badges)", "sforum"), "rankdisplay", $sfoptions['rankdisplay']);
				sfa_paint_checkbox(__("Display the User's Location", "sforum"), "location", $sfoptions['location']);
				sfa_paint_checkbox(__("Display the User's Post Count", "sforum"), "postcount", $sfoptions['postcount']);
				sfa_paint_checkbox(__("Display the Post Permalink Button", "sforum"), "permalink", $sfoptions['permalink']);
				sfa_paint_checkbox(__("Display the Print Post Button", "sforum"), "print", $sfoptions['print']);
				sfa_paint_checkbox(__("Show Twitter Follow Me Button if User has Twitter Account in Profile", "sforum"), "sftwitterfollow", $sfoptions['sftwitterfollow']);
				sfa_paint_checkbox(__("Show Facebook Connect Button if User has Facebook Account in Profile", "sforum"), "sffbconnect", $sfoptions['sffbconnect']);
				sfa_paint_checkbox(__("Show LinkedIn  Button if User has LinkedIn Account in Profile", "sforum"), "sflinkedin", $sfoptions['sflinkedin']);
				sfa_paint_checkbox(__("Show MySpace Button if User has MySpace Account in Profile", "sforum"), "sfmyspace", $sfoptions['sfmyspace']);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

	sfa_paint_close_tab();

?>
	<div class="sfform-submit-bar">
	<input type="submit" class="sfform-panel-button" id="saveit" name="saveit" value="<?php esc_attr_e(__('Update Post Options', 'sforum')); ?>" />
	</div>
	</form>
<?php
	return;
}

?>