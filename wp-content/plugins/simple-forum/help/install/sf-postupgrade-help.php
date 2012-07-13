<?php
/*
Simple:Press
Post Install Notes
$LastChangedDate: 2010-03-26 16:38:27 -0700 (Fri, 26 Mar 2010) $
$Rev: 3818 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

	# Check avatars, smileys and upload config
	$av = sf_get_option("sfinstallav");
	$sm = sf_get_option("sfinstallsm");
	if($av || $sm || $up ? $prob=true : $prob=false);

	sfa_paint_options_init();

	sfa_paint_open_tab(__("SPF Upgrader", "sforum")." - ".__("SPF Upgrade Complete", "sforum"));
		sfa_paint_open_panel();
			sfa_paint_open_fieldset(__("SPF Upgrade Complete", "sforum"), false);
?>
        		<h2>Simple:Press has been upgraded to version <?php echo(SFVERSION); ?></h2>
            	<div class="clearboth"></div>

            	<div class="sfhelptext">
        			<h2>SOME IMPORTANT NOTES - PLEASE READ FIRST</h2><br />

<?php
					if($prob)
					{
						echo '<br /><div class="sfoptionerror">';
						echo "Due to your permission setitngs, the Installer was not able to complete all of the required tasks. The information below descibes how to
						      manually complete the install";
						echo '</div><br />';
					}

					# Avatar problem
					if($av)
					{ ?>
						<table class="form-table"><tr>
						<td width="60"><img src="<?php echo SFADMINIMAGES; ?>avatar-problem.png" width="50" height="50" style="padding: 5px;" alt="" title="" /></td>
						<td style="padding: 0 5px;">
						<h2>Installing Avatars</h2>
						<p>Simple:Press was unable to complete the installation of avatars. Please follow these steps to manually complete avatar installation:</p>
						<ol>
						<li>Check if a folder was created under 'wp-content' named <b>'forum-avatars'</b>. If not then create it now.</li>
						<li>Ensure the folder has suitable permissions that will enable users to upload their avatars if you wish them to do this.</li>
						<li>Navigate to the folder: <b>simple-forum/styles/avatars</b> where there will be 3 default avatars.</li>
						<li>Move these 3 default avatars to the new folder <b>wp-content/forum-avatars</b>.</li>
						</ol>
						</td></tr></table>
					<?php }

					# Smiley problem
					if($sm)
					{ ?>
						<table class="form-table"><tr>
						<td width="60"><img src="<?php echo SFADMINIMAGES; ?>smiley-problem.png" width="50" height="50" style="padding: 5px;" alt="" title="" /></td>
						<td style="padding: 0 5px;">
						<h2>Installing Smileys</h2>
						<p>Simple:Press was unable to complete the installation of smileys. Please follow these steps to manually complete smiley installation:</p>
						<ol>
						<li>Check if a folder was created under 'wp-content' named <b>'forum-smileys'</b>. If not then create it now.</li>
						<li>Ensure the folder has suitable permissions that will enable you to upload smileys if you wish to do this.</li>
						<li>Navigate to the folder: <b>simple-forum/styles/smileys</b> where there will be 11 default smileys.</li>
						<li>Move these 11 default smileys to the new folder <b>wp-content/forum-smileys</b>.</li>
						</ol>
						</td></tr></table>
					<?php }

					# Check Storage Locations
					echo '<br /><div class="sfoptionerror">';
					echo "Check the Storage Locations: Forum Admin > Configuration > Storage Locations";
					echo '</div><br />';
?>
        			<table class="form-table"><tr>
        			<td style="padding: 0 5px;">
        			<p><b>These settings determine the location of various folders and files that you can move outside of the
        			standard WordPress plugin folder. Please take a few minutes to review.</b><br /></p><br />
        			<p>If any of the locations are highlighted in red this means that Simple:Press was unable to create the necessary folder.
        			This will most usually be caused by your permissions setting on the WordPress 'wp-content' folder. You will need to create
        			these locations manually. For an explanation of the <b>'Locations Panel'</b> - please click on the help button in the top right.</p><br />
        			<div class="clearboth"></div>
        			</td></tr></table>

                    <br /><hr /><br />

            		<?php echo '<img src="'.SFADMINIMAGES.'SPF-badge-125.png" style="float: right; padding: 15px;" alt="" title="" />'; ?>
            		<p>If you find Simple:Press useful as an addition to your site - please consider a donation to help it continue to grow into the future.
            		All donations are used for hosting, bandwidth and development costs.</p>
            		<p>If however, you wish to remove Simple:Press at any time, please use the proper <b>Uninstall</b> option (see the On-Line Help)
            		which will successfully remove all traces of the forum plugin and all forum data from your database.</p>
        	   </div>
<?php
			sfa_paint_close_fieldset();
		sfa_paint_close_panel();
	sfa_paint_close_tab();

	sf_delete_option('sfinstallav');
	sf_delete_option('sfinstallsm');
	delete_option('sfInstallID'); # use wp option table
?>