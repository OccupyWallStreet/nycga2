<?php
/*
Simple:Press
Admin Options Content Form
$LastChangedDate: 2011-01-07 03:19:34 -0700 (Fri, 07 Jan 2011) $
$Rev: 5274 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_options_content_form()
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfcontentform').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfmsgspot').fadeIn();
			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});
});
</script>
<?php

	$sfoptions = sfa_get_content_data();

    $ahahURL = SFHOMEURL."index.php?sf_ahah=options-loader&amp;saveform=content";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfcontentform" name="sfcontent">
	<?php echo(sfc_create_nonce('forum-adminform_content')); ?>
<?php

	sfa_paint_options_init();

#== POSTS Tab ============================================================

	sfa_paint_open_tab(__("Options", "sforum")." - ".__("Content Settings", "sforum"));

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Date/Time Formatting", "sforum"), true, 'date-time-formatting');
				sfa_paint_input(__("Date Display Format", "sforum"), "sfdates", $sfoptions['sfdates']);
				sfa_paint_input(__("Time Display Format", "sforum"), "sftimes", $sfoptions['sftimes']);
				sfa_paint_link("http://codex.wordpress.org/Formatting_Date_and_Time", __("Date/Time Help", "sforum"));
				$tz = get_option('timezone_string');
				if(empty($tz)) $tz = __("Unknown", "sforum");
				echo '&nbsp;'.__("Server Timezone set to", "sforum").': '.$tz;
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Image Enlargement", "sforum"), true, 'image-enlarging');
				sfa_paint_checkbox(__("Use Popup Image Enlargement", "sforum"), "sfimgenlarge", $sfoptions['sfimgenlarge']);
				sfa_paint_checkbox(__("Always Use Image Thumbnails", "sforum"), "process", $sfoptions['process']);
				sfa_paint_input(__("Thumbnail width of images in posts<br />(Minimum 100)", "sforum"), "sfthumbsize", $sfoptions['sfthumbsize']);

				sfa_paint_select_start(__("Default Image Style", "sforum"), "style", 'style');
				echo(sfa_create_imagestyle_select($sfoptions['style']));
				sfa_paint_select_end();

			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Post Ratings Support", "sforum"), true, 'post-ratings');
				sfa_paint_checkbox(__("Enable Post Rating", "sforum"), "sfpostratings", $sfoptions['sfpostratings']);
				$values = array(__("Thumbs Up/Down", "sforum"), __("Stars", "sforum"));
				$msg = '<p>'.__("WARNING: Changing the rating styles will reset all of the currently collected ratings data.  Please check the confirm box to indicate that you really want to do this.  The database tables will be reset when the options are saved.", "sforum").'</p>';
				sfa_paint_radiogroup_confirm(__("Select Post Rating Style", "sforum"), 'ratingsstyle', $values, $sfoptions['sfratingsstyle'], $msg, false, true);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Spam Posts", "sforum"), true, 'spam-links');
				sfa_paint_input(__("Maximum Links Allowed in Post (0=Unlimited)", "sforum"), "sfmaxlinks", $sfoptions['sfmaxlinks']);
				sfa_paint_checkbox(__("Refuse Duplicate Post made by Member", "sforum"), "sfdupemember", $sfoptions['sfdupemember']);
				sfa_paint_checkbox(__("Refuse Duplicate Post made by Guest", "sforum"), "sfdupeguest", $sfoptions['sfdupeguest']);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Additional Filtering", "sforum"), true, 'additional-filters');
				sfa_paint_checkbox(__("Filter out HTML &lt;pre&gt; tags", "sforum"), "sffilterpre", $sfoptions['sffilterpre']);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Code Syntax Highlighting", "sforum"), true, 'syntax-highlighting');
				sfa_paint_checkbox(__("Use Syntax Highlighting in Forum Posts", "sforum"), "sfsyntaxforum", $sfoptions['sfsyntaxforum']);
				sfa_paint_checkbox(__("Use Syntax Highlighting in Blog Posts", "sforum"), "sfsyntaxblog", $sfoptions['sfsyntaxblog']);
				sfa_paint_input(__("Languages (comma separated)", "sforum"), "sfbrushes", $sfoptions['sfbrushes'], false, true);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

	sfa_paint_tab_right_cell();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Profanity Filtering", "sforum"), true, 'profanity-filter');
				$submessage=__("Enter profanities one word per line - there must be a corresponding entry in replacement words.", "sforum");
				sfa_paint_textarea(__("Profanity Word List - Words to Filter from Post", "sforum"), "sfbadwords", $sfoptions['sfbadwords'], $submessage);
				$submessage=__("Enter replacement words one word per line - there must be a corresponding entry in profanities.", "sforum");
				sfa_paint_textarea(__("Replacement Word List - Words to Replace in Post", "sforum"), "sfreplacementwords", $sfoptions['sfreplacementwords'], $submessage);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Post Links Filtering", "sforum"), true, 'post-links-filtering');
				sfa_paint_checkbox(__("Add 'nofollow' to links", "sforum"), "sfnofollow", $sfoptions['sfnofollow']);
				sfa_paint_checkbox(__("Open links in new tab/window", "sforum"), "sftarget", $sfoptions['sftarget']);
				sfa_paint_input(__("URL Shortening Limit (0=Not Shortened)", "sforum"), "sfurlchars", $sfoptions['sfurlchars']);
				$submessage=__("If Post Viewer Doesn't have View Links Permission, this Custom Message Will be Displayed Instead.", "sforum");
				sfa_paint_textarea(__("Hidden Links Custom Message", "sforum"), "sfnolinksmsg", $sfoptions['sfnolinksmsg'], $submessage);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Shortcodes Filtering", "sforum"), true, 'shortcode-filters');
				sfa_paint_checkbox(__("Filter WP shortcodes (if disabled ALL WP shortcodes will be passed)", "sforum"), "sffiltershortcodes", $sfoptions['sffiltershortcodes']);
				$submessage=__("Enter allowed WP shortcodes (if filtering enabled above) - one shortcode per line.", "sforum");
				sfa_paint_textarea(__("Allowed WP Shortcodes in Posts", "sforum"), "sfshortcodes", $sfoptions['sfshortcodes'], $submessage);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

	sfa_paint_close_tab();

?>
	<div class="sfform-submit-bar">
	<input type="submit" class="sfform-panel-button" id="saveit" name="saveit" value="<?php esc_attr_e(__('Update Content Options', 'sforum')); ?>" />
	</div>
	</form>
<?php
	return;
}


function sfa_create_imagestyle_select($defstyle)
{
	$styles = array('left', 'right', 'baseline', 'top', 'middle', 'bottom', 'text-top', 'text-bottom');
	$default='';
	foreach ($styles as $style)
	{
		if($style == $defstyle)
		{
			$default = 'selected="selected" ';
		} else {
			$default - null;
		}
		$out.='<option '.$default.'value="'.$style.'">'.$style.'</option>'."\n";
		$default='';
	}
	return $out;
}

?>