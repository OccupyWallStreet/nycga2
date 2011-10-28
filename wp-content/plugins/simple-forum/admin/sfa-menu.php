<?php
/*
Simple:Press
Admin Side Menu
$LastChangedDate: 2011-03-05 07:42:11 -0700 (Sat, 05 Mar 2011) $
$Rev: 5631 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

global $SFSTATUS;
if($SFSTATUS == 'ok')
{
	# Get correct tooltips file
	$lang = WPLANG;
	if (empty($lang)) $lang = 'en';
	$ttpath = SFHELP.'admin/tooltips/admin-menu-tips-'.$lang.'.php';
	if (file_exists($ttpath) == false) $ttpath = SFHELP.'admin/tooltips/admin-menu-tips-en.php';
	if(file_exists($ttpath))
	{
		include_once($ttpath);
	}
}

function sfa_render_sidemenu()
{
	global $apage, $sfglobals, $sfatooltips;

	if(isset($_GET['tab']) ? $formid=$_GET['tab'] : $formid='');

?>

	<div id="sfsidepanel">
		<div id="sfadminmenu">

			<!-- FORUMS -->
			<?php if (sfc_current_user_can('SPF Manage Forums')) { ?>
				<div class="sfsidebutton" id="sfaccforums">
					<img src="<?php echo(SFADMINIMAGES); ?>forums.gif" alt="" class="vtip" title="<?php echo $sfatooltips['forums']; ?>" />
					<a href="#"><?php _e("Forums", "sforum"); ?></a>
				</div>
				<div><?php sfa_render_forums_formlist($formid); ?></div>
			<?php } ?>

			<!-- OPTIONS -->
			<?php if (sfc_current_user_can('SPF Manage Options')) { ?>
				<div class="sfsidebutton" id="sfaccoptions">
					<img src="<?php echo(SFADMINIMAGES); ?>options.gif" alt="" class="vtip" title="<?php echo $sfatooltips['options']; ?>" />
					<a href="#"><?php _e("Options", "sforum"); ?></a>
				</div>
				<div><?php sfa_render_options_formlist($formid); ?></div>
			<?php } ?>

			<!-- COMPONENTS -->
			<?php if (sfc_current_user_can('SPF Manage Components')) { ?>
				<div class="sfsidebutton" id="sfacccomponents">
					<img src="<?php echo(SFADMINIMAGES); ?>components.gif" alt="" class="vtip" title="<?php echo $sfatooltips['components']; ?>" />
					<a href="#"><?php _e("Components", "sforum"); ?></a>
				</div>
				<div><?php sfa_render_components_formlist($formid); ?></div>
			<?php } ?>

			<!-- USER GROUPS -->
			<?php if (sfc_current_user_can('SPF Manage User Groups')) { ?>
				<div class="sfsidebutton" id="sfaccusergroups">
					<img src="<?php echo(SFADMINIMAGES); ?>usergroups.gif" alt="" class="vtip" title="<?php echo $sfatooltips['usergroups']; ?>" />
					<a href="#"><?php _e("User Groups", "sforum"); ?></a>
				</div>
				<div><?php sfa_render_usergroups_formlist($formid); ?></div>
			<?php } ?>

			<!-- PERMISSIONS -->
			<?php if (sfc_current_user_can('SPF Manage Permissions')) { ?>
				<div class="sfsidebutton" id="sfaccpermissions">
					<img src="<?php echo(SFADMINIMAGES); ?>permissions.gif" alt="" class="vtip" title="<?php echo $sfatooltips['permissions']; ?>" />
					<a href="#"><?php _e("Permissions", "sforum"); ?></a>
				</div>
				<div><?php sfa_render_permissions_formlist($formid); ?></div>
			<?php } ?>

			<!-- USERS -->
			<?php if (sfc_current_user_can('SPF Manage Users')) { ?>
				<div class="sfsidebutton" id="sfaccusers">
					<img src="<?php echo(SFADMINIMAGES); ?>users.gif" alt="" class="vtip" title="<?php echo $sfatooltips['users']; ?>" />
					<a href="#"><?php _e("Users", "sforum"); ?></a>
				</div>
				<div><?php sfa_render_users_formlist($formid); ?></div>
			<?php } ?>

			<!-- PROFILES -->
			<?php if (sfc_current_user_can('SPF Manage Profiles')) { ?>
				<div class="sfsidebutton" id="sfaccprofiles">
					<img src="<?php echo(SFADMINIMAGES); ?>profiles.gif" alt="" class="vtip" title="<?php echo $sfatooltips['profiles']; ?>" />
					<a href="#"><?php _e("Profiles", "sforum"); ?></a>
				</div>
				<div><?php sfa_render_profiles_formlist($formid); ?></div>
			<?php } ?>

			<!-- ADMINS -->
			<?php if (sfc_current_user_can('SPF Manage Admins') || $sfglobals['member']['admin'] || $sfglobals['member']['moderator']) { ?>
				<div class="sfsidebutton" id="sfaccadmins">
					<img src="<?php echo(SFADMINIMAGES); ?>admins.gif" alt="" class="vtip" title="<?php echo $sfatooltips['admins']; ?>" />
					<a href="#"><?php _e("Admins", "sforum"); ?></a>
				</div>
				<div><?php sfa_render_admins_formlist($formid); ?></div>
			<?php } ?>

			<!-- TAGS -->
			<?php if (sfc_current_user_can('SPF Manage Tags')) { ?>
				<div class="sfsidebutton"id="sfacctags">
					<img src="<?php echo(SFADMINIMAGES); ?>tags.gif" alt="" class="vtip" title="<?php echo $sfatooltips['tags']; ?>" />
					<a href="#"><?php _e("Tags", "sforum"); ?></a>
				</div>
				<div><?php sfa_render_tags_formlist($formid); ?></div>
			<?php } ?>

			<!-- TOOLBOX -->
			<?php if (sfc_current_user_can('SPF Manage Toolbox')) { ?>
				<div class="sfsidebutton" id="sfacctoolbox">
					<img src="<?php echo(SFADMINIMAGES); ?>toolbox.gif" alt="" class="vtip" title="<?php echo $sfatooltips['toolbox']; ?>" />
					<a href="#"><?php _e("Toolbox", "sforum"); ?></a>
				</div>
				<div><?php sfa_render_toolbox_formlist($formid); ?></div>
			<?php } ?>

			<!-- CONFIGURATION -->
			<?php if (sfc_current_user_can('SPF Manage Configuration')) { ?>
				<div class="sfsidebutton" id="sfaccconfiguration">
					<img src="<?php echo(SFADMINIMAGES); ?>configuration.gif" alt="" class="vtip" title="<?php echo $sfatooltips['configuration']; ?>" />
					<a href="#"><?php _e("Configuration", "sforum"); ?></a>
				</div>
				<div><?php sfa_render_config_formlist($formid); ?></div>
			<?php } ?>

			<!-- WP INTEGRATION -->
			<?php if (sfc_current_user_can('SPF Manage Options')) { ?>
				<div class="sfsidebutton" id="sfaccintegration">
					<img src="<?php echo(SFADMINIMAGES); ?>integration.gif" alt="" class="vtip" title="<?php echo $sfatooltips['integration']; ?>" />
					<a href="#"><?php _e("WP Integration", "sforum"); ?></a>
				</div>
				<div><?php sfa_render_integration_formlist($formid); ?></div>
			<?php } ?>

		</div>
	</div>

<?php
	return;
}

function sfa_render_forums_formlist($current='')
{
    $base = SFHOMEURL."index.php?sf_ahah=forums-loader";

	$forms = array(
		__('Manage Groups And Forums', 'sforum')=>array('forums'=>'sfreloadfb'),
		__('Create New Group', 'sforum')=>array('creategroup'=>''),
		__('Create New Forum', 'sforum')=>array('createforum'=>''),
		__('Add Global Permission Set', 'sforum')=>array('globalperm'=>''),
		__('Delete All Permission Sets', 'sforum')=>array('removeperms'=>''),
		__('Global RSS Settings', 'sforum')=>array('globalrss'=>'sfreloadfd'));

	sfa_menu_link($base, $forms, $current);
}

function sfa_render_options_formlist($current='')
{
    $base = SFHOMEURL."index.php?sf_ahah=options-loader";

	$forms = array(
		__('Global Settings', 'sforum')=>array('global'=>'sfreloadog'),
		__('Global Display Settings', 'sforum')=>array('display'=>''),
		__('Forum Settings', 'sforum')=>array('forums'=>''),
		__('Topic Settings', 'sforum')=>array('topics'=>''),
		__('Post Settings', 'sforum')=>array('posts'=>''),
		__('Content Settings', 'sforum')=>array('content'=>''),
		__('Member Settings', 'sforum')=>array('members'=>'sfreloadms'),
		__('EMail Settings', 'sforum')=>array('email'=>''),
		__('Style Settings', 'sforum')=>array('style'=>'sfreloadst'));

	sfa_menu_link($base, $forms, $current);
}

function sfa_render_components_formlist($current='')
{
    $base = SFHOMEURL."index.php?sf_ahah=components-loader";

	$forms = array(
		__('Editor', 'sforum')=>array('editor'=>''),
		__('Editor Toolbar', 'sforum')=>array('toolbar'=>'sfreloadtb'),
		__('Smileys', 'sforum')=>array('smileys'=>'sfreloadsm'),
		__('Login And Registration', 'sforum')=>array('login'=>''),
		__('SEO', 'sforum')=>array('seo'=>'sfreloadse'),
		__('Private Messaging', 'sforum')=>array('pm'=>'sfreloadpm'),
		__('Blog Linking', 'sforum')=>array('links'=>''),
		__('Uploads', 'sforum')=>array('uploads'=>''),
		__('Topic Status', 'sforum')=>array('topicstatus'=>'sfreloadts'),
		__('Forum Ranks', 'sforum')=>array('forumranks'=>'sfreloadfr'),
		__('Custom Messages', 'sforum')=>array('messages'=>''),
		__('Custom Icons', 'sforum')=>array('icons'=>'sfreloadci'),
		__('Announce Tag', 'sforum')=>array('tags'=>''),
		__('Policy Documents', 'sforum')=>array('policies'=>''),
		__('Mobile Support', 'sforum')=>array('mobile'=>''));

	sfa_menu_link($base, $forms, $current);
}

function sfa_render_usergroups_formlist($current='')
{
    $base = SFHOMEURL."index.php?sf_ahah=usergroups-loader";

	$forms = array(
		__('Manage User Groups', 'sforum')=>array('usergroups'=>'sfreloadub'),
		__('Create New User Group', 'sforum')=>array('createusergroup'=>''));

	sfa_menu_link($base, $forms, $current);
}

function sfa_render_permissions_formlist($current='')
{
    $base = SFHOMEURL."index.php?sf_ahah=permissions-loader";

	$forms = array(
		__('Manage Permissions', 'sforum')=>array('permissions'=>'sfreloadpb'),
		__('Add New Permission', 'sforum')=>array('createperm'=>''));

	sfa_menu_link($base, $forms, $current);
}

function sfa_render_users_formlist($current='')
{
    $base = SFHOMEURL."index.php?sf_ahah=users-loader";

	$forms = array(
		__('Member Information', 'sforum')=>array('members'=>''),
		__('Subscriptions And Watches', 'sforum')=>array('subwatches'=>''),
		__('Member PM Stats', 'sforum')=>array('pmstats'=>''),
		__('Spam Registrations', 'sforum')=>array('spamreg'=>'sfreloadsr'));

	sfa_menu_link($base, $forms, $current);
}

function sfa_render_profiles_formlist($current='')
{
    $base = SFHOMEURL."index.php?sf_ahah=profiles-loader";

	$forms = array(
		__('Profile Options', 'sforum')=>array('options'=>''),
		__('Profile Data', 'sforum')=>array('data'=>''),
		__('Custom Profile Fields', 'sforum')=>array('fields'=>'sfreloadcf'),
		__('Avatars', 'sforum')=>array('avatars'=>'sfreloadav'));

	sfa_menu_link($base, $forms, $current);
}

function sfa_render_admins_formlist($current='')
{
	global $sfglobals;

    $base = SFHOMEURL."index.php?sf_ahah=admins-loader";

	$forms = array(
		__('Your Admin Options', 'sforum')=>array('youradmin'=>'sfreloadao'));

	sfa_menu_link($base, $forms, $current);

	if (sfc_current_user_can('SPF Manage Admins'))
	{
		$forms = array(
			__('Global Admin Options', 'sforum')=>array('globaladmin'=>''),
			__('Manage Admins', 'sforum')=>array('manageadmin'=>'sfreloadma'));

		sfa_menu_link($base, $forms, $current);
	}
}

function sfa_render_tags_formlist($current='')
{
    $base = SFHOMEURL."index.php?sf_ahah=tags-loader";

	$forms = array(
		__('Manage Tags', 'sforum')=>array('managetags'=>'sfreloadmb'),
		__('Mass Edit Tags', 'sforum')=>array('edittags'=>'sfreloadeb'));

	sfa_menu_link($base, $forms, $current);
}

function sfa_render_toolbox_formlist($current='')
{
    $base = SFHOMEURL."index.php?sf_ahah=toolbox-loader";

	$forms = array(
		__('Toolbox', 'sforum')=>array('toolbox'=>''),
		__('Prune Database', 'sforum')=>array('database'=>'sfreloaddb'),
		__('Install Log', 'sforum')=>array('log'=>''),
		__('Environment', 'sforum')=>array('environment'=>''),
		__('Uninstall', 'sforum')=>array('uninstall'=>''));

	sfa_menu_link($base, $forms, $current);
}

function sfa_render_config_formlist($current='')
{
    $base = SFHOMEURL."index.php?sf_ahah=config-loader";

	$forms = array(
		__('Storage Locations', 'sforum')=>array('config'=>'sfreloadsl'),
		__('Code And Query Optimizations', 'sforum')=>array('optimise'=>'sfreloadqb'));

	sfa_menu_link($base, $forms, $current);
}

function sfa_render_integration_formlist($current='')
{
    $base = SFHOMEURL."index.php?sf_ahah=integration-loader";

	$forms = array(
		__('Page and Permalink', 'sforum')=>array('page'=>'sfreloadpp'));

	sfa_menu_link($base, $forms, $current);
}

function sfa_menu_link($base, $forms, $current)
{
	$target = "sfmaincontainer";
	$image = SFADMINIMAGES;
	$upgrade = admin_url("admin.php?page=simple-forum/sf-loader-install.php");

	foreach($forms as $label=>$data)
	{
		foreach($data as $formid=>$reload)
		{
			$id="";

			echo '<div class="sfsideitem">';
			if($reload != '')
			{
				$id=' id="'.$reload.'"';
			} else {
				$id=' id="acc'.$formid.'"';
			}
			?>
			<a<?php echo($id); ?> href="#" onclick="sfjLoadForm('<?php echo($formid); ?>', '<?php echo($base); ?>', '<?php echo($target); ?>', '<?php echo($image); ?>', '', 'sfopen', '<?php echo($upgrade); ?>');"><?php echo($label); ?></a>
			<?php
		}
		echo '</div>';
	}
	return;
}

?>