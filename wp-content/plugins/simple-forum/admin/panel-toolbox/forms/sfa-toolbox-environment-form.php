<?php
/*
Simple:Press
Admin Toolbox Environmental Info Form
$LastChangedDate: 2011-03-05 07:17:23 -0700 (Sat, 05 Mar 2011) $
$Rev: 5630 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_toolbox_environment_form()
{

#== ENVIRONMENT INFO Tab ==========================================================

	global $wp_version, $wpdb;

	sfa_paint_open_tab(__("Toolbox", "sforum")." - ".__("Environment", "sforum"));

		sfa_paint_open_fieldset(__("Environment", "sforum"), false, '', true);

			sf_render_info_label('Simple:Press Version');
			sf_render_info_data(SFVERSION);

			sf_render_info_label('Simple:Press Build');
			sf_render_info_data(SFBUILD);

			sf_render_info_label('Simple:Press Release Type');
			sf_render_info_data(SFRELEASE);

			sf_render_info_label('WordPress Version');
			sf_render_info_data($wp_version);

			sf_render_info_label('MySQL Version');
			sf_render_info_data($wpdb->db_version());

			sf_render_info_label('PHP Version');
			sf_render_info_data(phpversion());

			$ar = split("[/ ]",$_SERVER['SERVER_SOFTWARE']);
			for ($i=0;$i<(count($ar));$i++)
			{
				switch(strtoupper($ar[$i]))
				{
					case 'APACHE':
					$i++;
					sf_render_info_label('Apache Version');
					sf_render_info_data($ar[$i]);
					break;
				}
			}

			$plugins = get_option('active_plugins');

			sf_render_info_label('Active Plugins');
			echo '<td><table width="100%">';
			foreach($plugins as $p)
			{
				sf_render_plugin_name($p);
			}
			echo '</table>';

		sfa_paint_close_fieldset();
	sfa_paint_close_tab();

	return;
}

function sf_render_info_label($text)
{
	echo '<tr><td><p>'.__($text, "sforum").':</p></td>';
	return;
}

function sf_render_info_data($text)
{
	echo '<td><p><b>'.__($text, "sforum").'</b></p></td></tr>';
	return;
}

function sf_render_plugin_name($text)
{
	$pos = strpos($text, '/');

	if($pos)
	{
		$p=substr($text, 0, $pos);
	} else {
		$p=substr($text, 0, strpos($text, '.php'));
	}

	echo '<tr><td><p><b>'.$p.'</b></p></td></tr>';
	return;
}


?>