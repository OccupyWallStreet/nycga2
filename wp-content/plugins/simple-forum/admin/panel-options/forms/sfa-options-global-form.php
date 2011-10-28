<?php
/*
Simple:Press
Admin Options Global Form
$LastChangedDate: 2010-06-04 16:05:34 -0700 (Fri, 04 Jun 2010) $
$Rev: 4109 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_options_global_form()
{
?>
<script type="text/javascript">
jQuery(document).ready(function()
	{
	jQuery('#sfglobalform').ajaxForm({
		target: '#sfmsgspot',
		success: function() {
			jQuery('#sfreloadog').click();
			jQuery('#sfmsgspot').fadeIn();
			jQuery('#sfmsgspot').fadeOut(6000);
		}
	});
});
</script>
<?php

	global $wpdb, $wp_roles;

	$sfoptions = sfa_get_global_data();

    $ahahURL = SFHOMEURL."index.php?sf_ahah=options-loader&amp;saveform=global";
?>
	<form action="<?php echo($ahahURL); ?>" method="post" id="sfglobalform" name="sfglobal">
	<?php echo(sfc_create_nonce('forum-adminform_global')); ?>
<?php

	sfa_paint_options_init();

#== GLOBAL Tab ============================================================

	sfa_paint_open_tab(__("Options", "sforum")." - ".__("Global Settings", "sforum"));

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Lock Down Forum", "sforum"), true, 'lock-down-forum');
				sfa_paint_checkbox(__("Lock the entire forum (read only)", "sforum"), "sflockdown", $sfoptions['sflockdown']);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("WP Admin Pages Access", "sforum"), true, 'block-admin');
				sfa_paint_checkbox(__("Block User Access to WP Admin Pages", "sforum"), "blockadmin", $sfoptions['blockadmin']);
                if ($sfoptions['blockadmin'])
                {
    				$roles = array_keys($wp_roles->role_names);
    				if ($roles)
    				{
    				    echo '<tr><td colspan="2"><p class="subhead">'.__("Allow these WP Roles Access to the WP Admin", "sforum").':</p>';
         			    echo '<p><strong><small>('.__("Administrators will always have access", "sforum").')</small></strong></p></td></tr>';
    					foreach ($roles as $index => $role)
    					{
                            if ($role != 'administrator')
                            {
        				        sfa_paint_checkbox($role, "role-".$index, $sfoptions['blockroles'][$role]);
                            }
                        }
    				}
    				sfa_paint_input(__("URL to Redirect to if Blocking Admin Access", "sforum"), "blockredirect", $sfoptions['blockredirect'], false, true);
                    sfa_paint_checkbox(__("Redirect to User's Profile Page (overrides URL above)", "sforum"), "blockprofile", $sfoptions['blockprofile']);
                }
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

	sfa_paint_tab_right_cell();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("RSS Feeds", "sforum"), true, 'rss-feeds');
				sfa_paint_input(__("Number of Recent Posts to feed", "sforum"), "sfrsscount", $sfoptions['sfrsscount']);
				sfa_paint_input(__("Limit to Number of Words (0=all)", "sforum"), "sfrsswords", $sfoptions['sfrsswords']);
				sfa_paint_checkbox(__("Enable Feedkeys for Private RSS Feeds", "sforum"), "sfrssfeedkey", $sfoptions['sfrssfeedkey']);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Auto Update", "sforum"), true, 'auto-update');
				sfa_paint_checkbox(__("Use Auto Update", "sforum"), "sfautoupdate", $sfoptions['sfautoupdate']);
				sfa_paint_input(__("How many seconds before refresh", "sforum"), "sfautotime", $sfoptions['sfautotime']);
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();

	sfa_paint_close_tab();

?>
	<div class="sfform-submit-bar">
	<input type="submit" class="sfform-panel-button" id="saveit" name="saveit" value="<?php esc_attr_e(__('Update Global Options', 'sforum')); ?>" />
	</div>
	</form>
<?php
	return;
}

?>