<?php
/*
Simple:Press
Admin Toolbox Uninstall Form
$LastChangedDate: 2010-04-18 09:22:20 -0700 (Sun, 18 Apr 2010) $
$Rev: 3920 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_toolbox_log_form()
{
	$sflog = sfa_get_log_data();

#== log Tab ==========================================================

	sfa_paint_open_tab(__("Toolbox", "sforum")." - ".__("Install Log", "sforum"));

			if(!$sflog)
			{
				_e("There are no Install Log Entries", "sforum");
				return;
			}

			sfa_paint_open_fieldset(__("Install Log", "sforum"), false, '', true);
				echo "<tr>";
				echo "<th>".__("Version","sforum")."</th>";
				echo "<th>".__("Build","sforum")."</th>";
				echo "<th>".__("Release","sforum")."</th>";
				echo "<th>".__("Installed","sforum")."</th>";
				echo "<th>".__("By","sforum")."</th>";
				echo "</tr>";

				foreach ($sflog as $log)
				{
					echo "<tr>";
					echo "<td class='sflabel'>".$log['version']."</td>";
					echo "<td class='sflabel'>".$log['build']."</td>";
					echo "<td class='sflabel'>".$log['release_type']."</td>";
					echo "<td class='sflabel'>".sf_date('d', $log['install_date'])."</td>";
					echo "<td class='sflabel'>".sf_filter_name_display($log['display_name'])."</td>";
					echo "</tr>";
				}
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();
	sfa_paint_close_tab();

	return;
}

?>