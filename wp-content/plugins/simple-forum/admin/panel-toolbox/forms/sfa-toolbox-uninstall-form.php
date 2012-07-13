<?php
/*
Simple:Press
Admin Toolbox Uninstall Form
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_toolbox_uninstall_form()
{
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#sfuninstallform').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfmsgspot').fadeIn();
			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});
});
</script>
<?php

	$sfoptions = sfa_get_uninstall_data();

    $ahahURL = SFHOMEURL."index.php?sf_ahah=toolbox-loader&amp;saveform=uninstall";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfuninstallform" name="sfuninstall">
	<?php echo(sfc_create_nonce('forum-adminform_uninstall')); ?>
<?php

	sfa_paint_options_init();

#== UNINSTALL Tab ==========================================================

	sfa_paint_open_tab(__("Toolbox", "sforum")." - ".__("Uninstall", "sforum"));

		sfa_paint_open_panel();

			echo '<br /><div class="sfoptionerror">';
			echo __("Should you, at any time, decide to remove Simple:Press, check the option below and then deactivate the plugin in the normal way", "sforum");
            echo '.<br />';
            echo __("THIS WILL REMOVE ALL DATA FROM YOUR DATABASE AND CAN NOT BE REVERSED", "sforum");
            echo '!<br />';
            echo __("Please note that you will still manually need to remove the plugin files and other forum data on you server such as avatars, smileys, uploads, etc", "sforum");
            echo '.<br />';
			echo '</div>';

			sfa_paint_open_fieldset(__("Removing Simple:Press", "sforum"), true, 'uninstall', true);
				sfa_paint_checkbox(__("Completely Remove Simple:Press Database Entries", "sforum"), "sfuninstall", $sfoptions['sfuninstall']);
			sfa_paint_close_fieldset();

		sfa_paint_close_panel();

	sfa_paint_close_tab();

?>
	<div class="sfform-submit-bar">
	<input type="submit" class="sfform-panel-button" id="saveit" name="saveit" value="<?php esc_attr_e(__('Uninstall', 'sforum')); ?>" />
	</div>
	</form>
<?php
	return;
}

?>