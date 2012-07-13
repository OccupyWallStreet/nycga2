<?php
/*
Simple:Press
Installer/Upgrader
$LastChangedDate: 2011-03-13 04:55:01 -0700 (Sun, 13 Mar 2011) $
$Rev: 5691 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

define('SF_WP_VER',  '3.1');
define('SF_PHP_VER', '4.3');
define('SF_SQL_VER', '4.1.21');

require_once (SF_PLUGIN_DIR.'/sf-includes.php');
require_once (SF_PLUGIN_DIR.'/library/sf-primitives.php');

?>
<style type="text/css">

.imessage, .zmessage, .showmessage, #debug {
	display: none;
	width: 700px;
	height: auto;
	color: #000000;
	font-weight: bold;
	font-size: 11px;
	font-family: Tahoma, Helvetica, Arial, Verdana;
	margin: 2px 10px;
	padding: 5px;
	border: 2px solid #555555;
    -moz-border-radius: 5px;
    -khtml-border-radius: 5px;
    -webkit-border-radius: 5px;
    border-radius: 5px;
}

.showmessage {
	display: block;
	padding-bottom: 10px;
}

.imessage {
	background-color: #FFF799;
}

.zmessage {
	background-color: #A7C1FF;
}

.pbar {
	margin: 2px 20px;
	width: 685px;
}

#zonecount {
	display:none;
}

.stayleft {
	float: left;
	padding-right: 15px;
}

</style>
<?php

global $SFALLOPTIONS;
$SFALLOPTIONS = sf_load_alloptions();

# get current version  and build from database
$current_version = sf_get_option('sfversion');
$current_build = sf_get_option('sfbuild');

# check if we are coming back in with post values to install
if(isset($_POST['goinstall']))
{
	sf_go_install();
	return;
}

# check if we are coming back in with post values to upgrade
if(isset($_POST['goupgrade']))
{
	sf_go_upgrade($current_version, $current_build);
	return;
}

# check if we are coming back in with post values to upgrade network
if(isset($_POST['gonetworkupgrade']))
{
	sf_go_network_upgrade($current_version, $current_build);
	return;
}

# Has the systen been installed?
if(version_compare($current_version, '1.0', '<'))
{
	sf_install_required();
	return;
}

# Base already installed - check Version and Build Number
if(($current_build < SFBUILD) || ($current_version > SFVERSION))
{
	sf_upgrade_required();
	return;
}

# set up install
function sf_install_required()
{
	?>
	<div class="wrap"><br />
		<?php $bad = sf_version_checks();
		if($bad != '')
		{
			echo($bad.'</div>');
			return;
		}
		?>
		<div class="showmessage">
		<img class="stayleft" src="<?php echo SFADMINIMAGES; ?>SPF-badge-125.png" alt="" title="" />
		<h2><?php _e("Install Simple:Press Version", "sforum"); ?> <?php echo(SFVERSION); ?> - <?php _e("Build", "sforum"); ?> <?php echo(SFBUILD); ?></h2>
		</div><br />
			<form name="sfinstall" method="post" action="<?php echo admin_url('admin.php?page=simple-forum/sf-loader-install.php'); ?>"><br />
				<input type="submit" class="button-secondary" id="sbutton" name="goinstall" value="<?php esc_attr_e('Perform Installation', 'sforum'); ?>" />
			</form>
	</div>
	<?php
}

# set up upgrade
function sf_upgrade_required()
{
	?>
	<div class="wrap"><br />
		<?php $bad = sf_version_checks();
		if($bad != '')
		{
			echo($bad.'</div>');
			return;
		}
		?>
		<div class="showmessage">
		<img class="stayleft" src="<?php echo SFADMINIMAGES; ?>SPF-badge-125.png" alt="" title="" />
		<h2><?php echo sprintf(__("Upgrade Simple:Press From Version %s to %s", "sforum"), sf_get_option('sfversion'), SFVERSION); ?> -
		(<?php _e("Build", "sforum"); ?> <?php echo(sf_get_option('sfbuild')); ?> <?php _e("to", "sforum"); ?> <?php _e("Build", "sforum"); ?> <?php echo(SFBUILD); ?>)</h2>
		</div><hr />
			<form name="sfupgrade" method="post" action="<?php echo admin_url('admin.php?page=simple-forum/sf-loader-install.php'); ?>"><br />
				<input type="submit" class="button-secondary" id="sbutton" name="goupgrade" value="<?php esc_attr_e('Perform Upgrade', 'sforum'); ?>" />
                <?php if (is_multisite() && is_super_admin()) { ?>
    				<input type="submit" class="button-secondary" id="sbutton" name="gonetworkupgrade" value="<?php esc_attr_e('Perform Network Upgrade', 'sforum'); ?>" />
                <?php } ?>
			</form>
	</div>
	<?php
}

# perform install
function sf_go_install()
{
	global $wpdb, $current_user;

	add_option('sfInstallID', $current_user->ID); # use wp option table

    $phpfile = SFHOMEURL."index.php?sf_ahah=install";
	$image = SFADMINIMAGES."working.gif";

	# how many users passes at 250 a pop?
	$users = $wpdb->get_var("SELECT COUNT(ID) FROM ".SFUSERS);
	$subphases = ceil($users / 250);
	$nextsubphase = 1;

	?>
	<div class="wrap"><br />
		<div class="showmessage">
		<img class="stayleft" src="<?php echo SFADMINIMAGES; ?>SPF-badge-125.png" alt="" title="" />
		<h2><?php _e("Simple:Press is being installed", "sforum"); ?></h2></div>
		<div style="clear: both"></div>
		<br />
		<div class="wrap sfatag">
			<div class="imessage" id="imagezone"></div><br />
			<div class="pbar" id="progressbar"></div><br />
		</div>
		<div style="clear: both"></div>
		<table id="sfinstalltable" border="0" cellspacing="6" cellpadding="2">
			<tr><td><div class="zmessage" id="zone0"><?php __("Installing", "sforum"); ?>...</div></td></tr>
			<tr><td><div class="zmessage" id="zone1"></div></td></tr>
			<tr><td><div class="zmessage" id="zone2"></div></td></tr>
			<tr><td><div class="zmessage" id="zone3"></div></td></tr>
			<tr><td><div class="zmessage" id="zone4"></div></td></tr>
			<tr><td><div class="zmessage" id="zone5"></div></td></tr>
			<tr><td><div class="zmessage" id="zone6"></div></td></tr>
			<tr><td><div class="zmessage" id="zone7"></div></td></tr>
			<tr><td><div class="zmessage" id="zone8"></div></td></tr>
			<tr><td><div class="zmessage" id="zone9"></div></td></tr>
			<tr><td><div class="zmessage" id="zone10"></div></td></tr>
		</table>
		<div class="zmessage" id="errorzone"></div>
		<div id="finishzone"></div>

<?php
		$pass = 10;
		$curr = 0;
		$messages = esc_js(__("Go to Forum Admin", "sforum"))."@".esc_js(__("Installation is in progress - please wait", "sforum"))."@".esc_js(__("Installation Completed", "sforum"))."@".esc_js(__("Installation has been Aborted", "sforum"));
		$out = '<script type="text/javascript">'."\n";
		$out.= 'sfjPerformInstall("'.$phpfile.'", "'.$pass.'", "'.$curr.'", "'.$subphases.'", "'.$nextsubphase.'", "'.$image.'", "'.$messages.'");'."\n";
		$out.= '</script>'."\n";
		echo $out;
?>
	</div>
	<?php
	return;
}

# perform upgrade
function sf_go_upgrade($current_version, $current_build)
{
	global $current_user;

	update_option('sfInstallID', $current_user->ID); # use wp option table
	sf_update_option('sfStartUpgrade', $current_build);

    $phpfile = SFHOMEURL."index.php?sf_ahah=upgrade";
	$image = SFADMINIMAGES."working.gif";

	$targetbuild = SFBUILD;
	?>
	<div class="wrap"><br />
		<div class="showmessage">
		<img class="stayleft" src="<?php echo SFADMINIMAGES; ?>SPF-badge-125.png" alt="" title="" />
		<h2><?php _e("Simple:Press is being upgraded", "sforum"); ?></h2>
		</div><br />
		<div class="wrap sfatag">
			<div class="imessage" id="imagezone"></div>
		</div><br />
		<div class="pbar" id="progressbar"></div><br />
		<div class="wrap sfatag">
			<div class="zmessage" id="errorzone"></div>
			<div id="finishzone"></div><br />
		</div><br />
		<div id="debug">
			</p><b>Please copy the details below and include them on any support forum question you may have:</b><br /><br /></p>
		</div>

<?php

		$messages = esc_js(__("Go to Forum Admin", "sforum"))."@".esc_js(__("Upgrade is in progress - please wait", "sforum"))."@".esc_js(__("Upgrade Completed", "sforum")).'@'.esc_js(__("Upgrade Aborted", "sforum"));
		$out = '<script type="text/javascript">'."\n";
		$out.= 'sfjPerformUpgrade("'.$phpfile.'", "'.$current_build.'", "'.$targetbuild.'", "'.$current_build.'", "'.$image.'", "'.$messages.'");'."\n";
		$out.= '</script>'."\n";
		echo $out;
?>
	</div>
	<?php
	return;
}

# perform network upgrade
function sf_go_network_upgrade($current_version, $current_build)
{
	global $current_user, $wpdb;

	?>
	<div class="wrap"><br />
		<div class="showmessage">
		<img class="stayleft" src="<?php echo SFADMINIMAGES; ?>SPF-badge-125.png" alt="" title="" />
		<h2><?php _e("Simple:Press is upgrading the Network.", "sforum"); ?></h2>
		</div><br />
		<div class="wrap sfatag">
			<div class="imessage" id="imagezone"></div>
		</div><br />
		<div class="pbar" id="progressbar"></div><br />
		<div class="wrap sfatag">
			<div class="zmessage" id="errorzone"></div>
			<div id="finishzone"></div><br />
		</div><br />
		<div id="debug">
			</p><b>Please copy the details below and include them on any support forum question you may have:</b><br /><br /></p>
		</div>
	</div>
	<?php

    # get list of network sites
    $sites = get_blog_list(0, 'all');

    # save current site to restore when finished
    $current_site = get_current_site();

    # loop through all blogs and upgrade ones with active simple:press
    foreach ($sites as $site)
    {
        # switch to network site and see if simple:press is active
        switch_to_blog($site['blog_id']);
        $installed = $wpdb->get_results("SELECT option_id FROM ".$wpdb->prefix."sfoptions WHERE option_name='sfversion'");
        if ($installed)
        {
            $phpfile = SFHOMEURL."index.php?sf_ahah=upgrade&sfnetworkid=".$site['blog_id'];
            $image = SFADMINIMAGES."working.gif";
            $targetbuild = SFBUILD;
            update_option('sfInstallID', $current_user->ID); # use wp option table

            # save the build info
            $out = __("Upgrading Network Site Id", "sforum").': '.$site['blog_id'].'<br />';
			sf_update_option('sfStartUpgrade', $current_build);

            # upgrade the network site
    		$messages = esc_js(__("Go to Forum Admin", "sforum"))."@".esc_js(__("Upgrade is in progress - please wait", "sforum"))."@".esc_js(__("Upgrade Completed", "sforum")).'@'.esc_js(__("Upgrade Aborted", "sforum"));
    		$out.= '<script type="text/javascript">'."\n";
    		$out.= 'sfjPerformUpgrade("'.$phpfile.'", "'.$current_build.'", "'.$targetbuild.'", "'.$current_build.'", "'.$image.'", "'.$messages.'");'."\n";
    		$out.= '</script>'."\n";
    		echo $out;
        }
    }

    #restore original network site
    switch_to_blog($current_site);

	return;
}

# Perform version checks prior to install
function sf_version_checks()
{
	global $wp_version, $wpdb;

	$message = '';
	$testtable = true;

	$logo = '<div class="showmessage"><img src="'.SFADMINIMAGES.'SPF4-banner.png" alt="" title="" /><br /><hr />';

	# WordPress version check
	if (sf_version_compare(SF_WP_VER, $wp_version) == false)
	{
		$message.= $logo;
		$message.= "<h2>".sprintf(__("%s Version %s", "sforum"), 'WordPress', $wp_version)."</h2>";
		$message.= "<p>". sprintf(__("Your version of %s is not supported by %s %s", "sforum"), 'WordPress', 'Simple:Press', SFVERSION).'<br />';
		$message.= sprintf(__("%s version %s or above is required", "sforum"), 'WordPress', SF_WP_VER)."</p><br />";
		$logo='<hr />';
	}

	# MySQL Check
	if (sf_version_compare(SF_SQL_VER, $wpdb->db_version()) == false)
	{
		$message.= $logo;
		$message.= "<h2>".sprintf(__("%s Version %s", "sforum"), 'MySQL', $wpdb->db_version())."</h2>";
		$message.= "<p>". sprintf(__("Your version of %s is not supported by %s %s", "sforum"), 'MySQL', 'Simple:Press', SFVERSION).'<br />';
		$message.= sprintf(__("%s version %s or above is required", "sforum"), 'MySQL', SF_SQL_VER)."</p><br />";
		$logo='<hr />';
		$testtable = false;
	}

	# PHP Check
	if (sf_version_compare(SF_PHP_VER, phpversion()) == false)
	{
		$message.= $logo;
		$message.= "<h2>".sprintf(__("%s Version %s", "sforum"), 'PHP', phpversion())."</h2>";
		$message.= "<p>". sprintf(__("Your version of %s is not supported by %s %s", "sforum"), 'PHP', 'Simple:Press', SFVERSION).'<br />';
		$message.= sprintf(__("%s version %s or above is required", "sforum"), 'PHP', SF_PHP_VER)."</p><br />";
		$logo='<hr />';
	}

	# test we can create database tables
	if($testtable)
	{
		if(sf_test_table_create() == false)
		{
			$message.= $logo;
			$message.= "<h2>".__("Database Problem", "sforum")."</h2>";
			$message.= "<p>". sprintf(__("%s can not Create Tables in your database", "sforum"),'Simple:Press').'</p><br />';
		}
	}

	if($message) $message.='</div>';
	return $message;
}

function sf_version_compare($need, $got)
{
	$need= explode(".", $need);
	$got = explode(".", $got);

	if(isset($need[0]) && intval($need[0]) > intval($got[0])) return false;
	if(isset($need[0]) && intval($need[0]) < intval($got[0])) return true;

	if(isset($need[1]) && intval($need[1]) > intval($got[1])) return false;
	if(isset($need[1]) && intval($need[1]) < intval($got[1])) return true;

	if(isset($need[2]) && intval($need[2]) > intval($got[2])) return false;

	return true;
}

function sf_test_table_create()
{
	global $wpdb;

	$sql = "
		CREATE TABLE sfCheckCreate (
			id int(4) NOT NULL,
			item varchar(15) default NULL,
			PRIMARY KEY  (id)
		) ENGINE=MyISAM ".sf_get_charset().";";

	$wpdb->query($sql);

	$success = $wpdb->query("SHOW TABLES LIKE 'sfCheckCreate'");
	if($success == 0)
	{
		return false;
	} else {
		$wpdb->query("DROP TABLE sfCheckCreate");
		return true;
	}
}

function sf_get_charset()
{
	global $wpdb;

	$charset='';

	if ( ! empty($wpdb->charset) )
	{
		$charset = "DEFAULT CHARSET $wpdb->charset";
	} else {
		$charset = "DEFAULT CHARSET utf8";
	}

	return $charset;
}

?>