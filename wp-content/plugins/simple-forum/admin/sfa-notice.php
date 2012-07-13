<?php
/*
Simple:Press
Special Admin Notice
$LastChangedDate: 2010-08-14 04:52:34 -0700 (Sat, 14 Aug 2010) $
$Rev: 4424 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

# Check Whether User is WP Admin
if (!current_user_can('administrator'))
{
	echo (__('Access Denied', "sforum"));
	die();
}

global $SFSTATUS;

include_once (SF_PLUGIN_DIR.'/admin/library/sfa-tab-support.php');
include_once (SF_PLUGIN_DIR.'/admin/panel-admins/support/sfa-admins-prepare.php');

if ($SFSTATUS != 'ok')
{
	include_once (SFINSTALL);
	die();
}

sfa_header();
	sfa_paint_options_init();
	sfa_paint_open_tab(__("Special WP Admin Notice", "sforum")." - ".__("Special WP Admin Notice", "sforum"));
		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("Special WP Admin Notice", "sforum"));
    			echo '<tr><td colspan="3"><br /><p>';
    			echo __("Please note that while you are a WP Admin, you are not currently an SPF Admin. By default, WP Admins are not SPF Admins.", "sforum");
                echo '<br />';
    			echo __("Contact one of the SPF Admins listed below to see if they want to grant you SPF Admin access on the SPF Manage Admins panel:", "sforum");
    			echo '</p>';

                # list all current SPF Admins
            	$adminrecords = sfa_get_admins_caps_data();
               	if ($adminrecords)
                {
                    echo '<p>';
                    echo '<ul>';
    				foreach ($adminrecords as $admin)
    				{
    				    echo '<li>'.sf_filter_name_display($admin['display_name']).'</li>';
                    }
                    echo '</ul>';
        			echo '</p><br />';
                }
       			echo '</td></tr>';
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();
	sfa_paint_close_tab();
sfa_footer();

?>