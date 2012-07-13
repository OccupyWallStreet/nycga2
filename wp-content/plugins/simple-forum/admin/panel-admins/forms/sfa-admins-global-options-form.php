<?php
/*
Simple:Press
Admin Admins New Admins Form
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_admins_global_options_form()
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfadminoptionsform').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfmsgspot').fadeIn();
			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});
});
</script>
<?php

	$sfoptions = sfa_get_admins_global_options_data();

    $ahahURL = SFHOMEURL."index.php?sf_ahah=admins-loader&amp;saveform=globaladmin";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfadminoptionsform" name="sfadminoptions">
	<?php echo(sfc_create_nonce('global-admin_options')); ?>
<?php

	sfa_paint_options_init();

	sfa_paint_open_tab(__("Admins", "sforum")." - ".__("Global Admin Options", "sforum"));

	sfa_paint_open_panel();
		sfa_paint_open_fieldset(__("Global Admin Options", "sforum"), 'true', 'admin-options');

			sfa_paint_checkbox(__("Use the Admins New Post Queue", "sforum"), "sfqueue", $sfoptions['sfqueue']);
			sfa_paint_checkbox(__("Force Queue Removal From Admins Bar Only", "sforum"), "sfbaronly", $sfoptions['sfbaronly']);
			sfa_paint_checkbox(__("Allow Moderators to Remove New Posts from Admins Unread queue", "sforum"), "sfmodasadmin", $sfoptions['sfmodasadmin']);
			sfa_paint_checkbox(__("Include Posts by Moderators in list", "sforum"), "sfshowmodposts", $sfoptions['sfshowmodposts']);
			sfa_paint_checkbox(__("Display Edit Tool Icons", "sforum"), "sftools", $sfoptions['sftools']);

		sfa_paint_close_fieldset();

	sfa_paint_close_panel();
	sfa_paint_tab_right_cell();
	sfa_paint_open_panel();
		sfa_paint_open_fieldset(__("Dashboard Options", "sforum"), 'true', 'dashboard-options');

			sfa_paint_checkbox(__("Display New Forum Posts in the Dashboard", "sforum"), "sfdashboardposts", $sfoptions['sfdashboardposts']);
			sfa_paint_checkbox(__("Display Forum Statistics in the Dashboard", "sforum"), "sfdashboardstats", $sfoptions['sfdashboardstats']);

		sfa_paint_close_fieldset();
	sfa_paint_close_panel();

	sfa_paint_close_tab();
?>
	<div class="sfform-submit-bar">
	<input type="submit" class="sfform-panel-button" id="saveit" name="saveit" value="<?php esc_attr_e(__('Update Global Admin Options', 'sforum')); ?>" />
	</div>
	</form>
<?php
	return;
}

?>