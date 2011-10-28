<?php
/*
Simple:Press
Admin Components Tags Form
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_components_tags_form()
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sftagsform').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfmsgspot').fadeIn();
			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});
});
</script>
<?php

	$sfoptions = sfa_get_tags_data();

    $ahahURL = SFHOMEURL."index.php?sf_ahah=components-loader&amp;saveform=tags";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sftagsform" name="sftags">
	<?php echo(sfc_create_nonce('forum-adminform_tags')); ?>
<?php

	sfa_paint_options_init();

#== TAGS Tab ============================================================

	sfa_paint_open_tab(__("Components", "sforum")." - ".__("Announce Tag", "sforum"));

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Announce Template Tag", "sforum"), true, 'announce-template-tag');
				sfa_paint_checkbox(__("Enable Announce Tag", "sforum"), "sfuseannounce", $sfoptions['sfuseannounce']);
				sfa_paint_checkbox(__("Display as Unordered List (default=Table)", "sforum"), "sfannouncelist", $sfoptions['sfannouncelist']);
				sfa_paint_input(__("How many most recent posts to display", "sforum"), "sfannouncecount", $sfoptions['sfannouncecount']);
				sfa_paint_input(__("Tag display Heading", "sforum"), "sfannouncehead", $sfoptions['sfannouncehead']);
				$submessage=__("Text can include the following placeholders: %FORUMNAME%, %TOPICNAME%, %POSTER% and %DATETIME%", "sforum");
				sfa_paint_wide_textarea(__("Text format of tag post link", "sforum"), "sfannouncetext", $sfoptions['sfannouncetext'], $submessage);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

	sfa_paint_tab_right_cell();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Announce Auto Refresh", "sforum"), true, 'announce-auto-refresh');
				sfa_paint_checkbox(__("Enable Auto-Refresh", "sforum"), "sfannounceauto", $sfoptions['sfannounceauto']);
				sfa_paint_input(__("How many seconds before refresh", "sforum"), "sfannouncetime", $sfoptions['sfannouncetime']);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

	sfa_paint_close_tab();

?>
	<div class="sfform-submit-bar">
	<input type="submit" class="sfform-panel-button" id="saveit" name="saveit" value="<?php esc_attr_e(__('Update Announce Tag Component', 'sforum')); ?>" />
	</div>
	</form>
<?php
	return;
}

function sfa_create_forum_select($forumid = 0)
{
	$forums = sf_get_forums_all();
	$out='';
	$default='';
	foreach($forums as $forum)
	{
		if($forum->forum_id == $forumid)
		{
			$default = 'selected="selected" ';
		} else {
			$default - null;
		}
		$out.='<option '.$default.'value="'.$forum->forum_id.'">'.sf_filter_title_display($forum->forum_name).'</option>'."\n";
		$default='';
	}
	return $out;
}

?>