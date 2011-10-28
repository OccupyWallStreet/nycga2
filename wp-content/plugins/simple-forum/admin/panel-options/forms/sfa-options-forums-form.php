<?php
/*
Simple:Press
Admin Options Forums Form
$LastChangedDate: 2010-08-26 14:35:07 -0700 (Thu, 26 Aug 2010) $
$Rev: 4527 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_options_forums_form()
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfforumsform').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfmsgspot').fadeIn();
			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});
});
</script>
<?php

	$sfoptions = sfa_get_forums_data();

    $ahahURL = SFHOMEURL."index.php?sf_ahah=options-loader&amp;saveform=forums";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfforumsform" name="sfforums">
	<?php echo(sfc_create_nonce('forum-adminform_forums')); ?>
<?php

	sfa_paint_options_init();

#== FORUMS Tab ============================================================

	sfa_paint_open_tab(__("Options", "sforum")." - ".__("Forum Settings", "sforum"));

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Group Display Options", "sforum"), true, 'group-display-options');
				sfa_paint_checkbox(__("Display Group Description", "sforum"), "grpdescription", $sfoptions['grpdescription']);
				sfa_paint_checkbox(__("Display Sub Forums below Parent Forum", "sforum"), "showsubforums", $sfoptions['showsubforums']);
				sfa_paint_checkbox(__("If Shown - Display All Nested Sub Forums", "sforum"), "showallsubs", $sfoptions['showallsubs']);
				sfa_paint_checkbox(__("Combine Counts and Last Post into Parent Forum", "sforum"), "combinesubcount", $sfoptions['combinesubcount']);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Forum Display Options", "sforum"), true, 'forum-display-options');
				sfa_paint_checkbox(__("Display Forum Description", "sforum"), "frmdescription", $sfoptions['frmdescription']);
				sfa_paint_checkbox(__("Display New Post Icon", "sforum"), "newposticon", $sfoptions['newposticon']);
				sfa_paint_checkbox(__("Display Forum Page Links", "sforum"), "pagelinks", $sfoptions['pagelinks']);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Single Forum Sites", "sforum"), true, 'single-forum-sites');
				sfa_paint_checkbox(__("Skip 'Group' View on Single Forum Sites", "sforum"), "sfsingleforum", $sfoptions['sfsingleforum']);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

	sfa_paint_tab_right_cell();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Forum View Formatting", "sforum"), true, 'forum-view-formatting');
				sfa_paint_checkbox(__("Display Recent Posts on Front Page", "sforum"), "sfshownewuser", $sfoptions['sfshownewuser']);
				sfa_paint_input(__("Number of Recent Posts to Display", "sforum"), "sfshownewcount", $sfoptions['sfshownewcount'], false, false);
				sfa_paint_checkbox(__("Display Recent Posts Above Groups", "sforum"), "sfshownewabove", $sfoptions['sfshownewabove']);
				sfa_paint_checkbox(__("Sort New Posts Within Forums", "sforum"), "sfsortinforum", $sfoptions['sfsortinforum']);
				sfa_paint_checkbox(__("Display 'Pinned: ' prior to pinned forums and topics", "sforum"), "sfpinned", $sfoptions['sfpinned'], false, false);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Forum View Columns", "sforum"), true, 'forum-view-columns');
				sfa_paint_checkbox(__("Show the Last Post Column", "sforum"), "fc_last", $sfoptions['fc_last']);
				sfa_paint_checkbox(__("Show Topic Title as Link", "sforum"), "showtitle", $sfoptions['showtitle']);
				sfa_paint_checkbox(__("Show Topic Title above Topic Info (if showing)", "sforum"), "showtitletop", $sfoptions['showtitletop']);
				sfa_paint_checkbox(__("Show the Topic Count Column", "sforum"), "fc_topics", $sfoptions['fc_topics']);
				sfa_paint_checkbox(__("Show the Post Count Column", "sforum"), "fc_posts", $sfoptions['fc_posts']);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

	sfa_paint_close_tab();

?>
	<div class="sfform-submit-bar">
	<input type="submit" class="sfform-panel-button" id="saveit" name="saveit" value="<?php esc_attr_e(__('Update Forum Options', 'sforum')); ?>" />
	</div>
	</form>
<?php
	return;
}

?>