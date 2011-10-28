<?php
/*
Simple:Press
Admin Forums Global RSS Set Form
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

# function to display the add global permission set form. It is hidden until user clicks the add global permission set link
function sfa_forums_global_rssset_form($id)
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfglobalrssset').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfreloadfd').click();
			jQuery('#sfmsgspot').fadeIn();
			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});
});
</script>
<?php

	sfa_paint_options_init();

    $ahahURL = SFHOMEURL."index.php?sf_ahah=forums-loader&amp;saveform=globalrssset";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfglobalrssset" name="sfglobalrssset">
<?php
		echo(sfc_create_nonce('forum-adminform_globalrssset'));
		sfa_paint_open_tab(__("Forums", "sforum")." - ".__("Global RSS Settings", "sforum"));
			sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Globally Enable/Disable RSS Feeds", "sforum"), false);
				echo '<tr><td colspan="2"><br />';
				echo '<div class="sfoptionerror">';
				echo __("Warning: Enabling or Disabling RSS Feeds from this form will apply that setting to ALL forums and overwrite any existing RSS Feed settings. If you wish to individually enable/disable RSS Feeds for a single Group or Forum, please visit the Manage Forums admin panel and edit the group or forum and set the RSS Feed status there.", "sforum");
				echo '<br /><br />';
				if ($id == 1) echo __('Please press the confirm button below to DISABLE RSS Feeds for all Forums.', 'sforum');
				if ($id == 0) echo __('Please press the confirm button below to ENABLE RSS Feds for all Forums.', 'sforum');
				echo '</div><br />';
				echo '</td></tr>';
				echo '<input type="hidden" name="sfglobalrssset" value="'.$id.'" />';
			sfa_paint_close_fieldset();
			sfa_paint_close_panel();
		sfa_paint_close_tab();
?>
		<div class="sfform-submit-bar">
			<input type="submit" class="sfform-panel-button" id="saveit" name="saveit" value="<?php esc_attr_e(__('Confirm RSS Feed Status', 'sforum')); ?>" />
			<input type="button" class="sfform-panel-button" onclick="javascript:jQuery('#sfallrss').html('');" id="sfallrsscancel" name="sfallrsscancel" value="<?php esc_attr_e(__('Cancel', 'sforum')); ?>" />
		</div>
	</form>

	<div class="sfform-panel-spacer"></div>
<?php
	return;
}

?>