<?php
/*
Simple:Press
Admin Users Panel Rendering
$LastChangedDate: 2010-04-22 15:20:30 -0700 (Thu, 22 Apr 2010) $
$Rev: 3939 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_render_users_panel($formid)
{
?>
	<div class="clearboth"></div>

	<div class="wrap sfatag">
		<?php
			sfa_render_sidemenu();
		?>
		<div id='sfmsgspot'></div>
		<div id="sfmaincontainer">
			<?php sfa_render_users_container($formid); ?>
		</div>
			<div class="clearboth"></div>
	</div>
<?php
	return;
}

function sfa_render_users_container($formid)
{
	switch ($formid)
	{
		case 'members':
			require_once (ABSPATH.'wp-admin/includes/admin.php');
			include_once (SF_PLUGIN_DIR.'/admin/panel-users/forms/sfa-users-members-form.php');
			sfa_users_members_form();
			break;
		case 'memberprofile':
			include_once (SF_PLUGIN_DIR.'/admin/panel-users/forms/sfa-users-profile-form.php');
			sfa_users_profile_form(sf_esc_int($_GET['id']));
			break;
		case 'subwatches':
			include_once (SF_PLUGIN_DIR.'/admin/panel-users/forms/sfa-users-subs-watches-form.php');
			sfa_users_subs_watches_form();
			break;
		case 'pmstats':
			require_once (ABSPATH.'wp-admin/includes/admin.php');
			include_once (SF_PLUGIN_DIR.'/admin/panel-users/forms/sfa-users-pm-stats-form.php');
			sfa_users_pm_stats_form();
			break;
		case 'spamreg':
			include_once (SF_PLUGIN_DIR.'/admin/panel-users/forms/sfa-users-spam-reg-form.php');
			sfa_users_spam_registrations_form();
			break;
		case 'showspamreg':
			include_once (SF_PLUGIN_DIR.'/admin/panel-users/forms/sfa-users-show-spam-reg-form.php');
			sfa_users_show_spam_registrations_form();
			break;
	}
	return;
}

?>