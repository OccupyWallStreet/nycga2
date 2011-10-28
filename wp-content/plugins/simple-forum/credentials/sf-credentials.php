<?php
/*
Simple:Press
Login (etc) Form Actions and Filters
$LastChangedDate: 2010-08-17 13:57:41 -0700 (Tue, 17 Aug 2010) $
$Rev: 4466 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

function sf_login_header()
{
	if(is_admin()) return;

	$sflogin=array();
	$sflogin=sf_get_option('sflogin');
	if(!$sflogin['sfloginskin']) return;

	if(isset($_REQUEST['view']))
	{
		$sfstyle=array();
		$sfstyle=sf_get_option('sfstyle');

		echo '<link rel="stylesheet" type="text/css" href="'.SF_PLUGIN_URL.'/styles/skins/'.$sfstyle['sfskin'].'/sf-credentials.css" />'."\n";
		echo '<script type="text/javascript" src="'.SFJSCRIPT.'admin/sf-credentials.js"></script>'."\n";

		?>
		<script type="text/javascript">
			window.onload=function(){
			sfjsetCredentials("<?php echo(SFSITEURL); ?>", "<?php echo(SFURL); ?>");
			}
		</script>
		<?php
	}
}

function sf_login_url()
{
	if(is_admin()) return;

	$sflogin=array();
	$sflogin=sf_get_option('sflogin');
	if(!$sflogin['sfloginskin']) return;

	if(isset($_REQUEST['view']))
	{
		echo SFURL;
	}
}

function sf_login_title()
{
	if(is_admin()) return;

	$sflogin=array();
	$sflogin=sf_get_option('sflogin');
	if(!$sflogin['sfloginskin']) return;

	if(isset($_REQUEST['view']))
	{
		echo get_option('blogname');
	}
}

function sf_login_form_action()
{
	if(is_admin()) return;

	$sflogin=array();
	$sflogin=sf_get_option('sflogin');
	if(!$sflogin['sfloginskin']) return;

	if(isset($_REQUEST['view']))
	{
	?>
		<p class="submit"><input type="button" name="button1" value="<?php esc_attr_e('Go to Forum', "sforum"); ?>" onclick="sfjreDirect('<?php echo(esc_js(SFURL)); ?>');" /></p>
	<?php
	}
}

function sf_post_login_check($login_name)
{
	include_once (SF_PLUGIN_DIR.'/library/sf-filters.php');
	$dname = sf_filter_name_display(sf_get_login_display_name($login_name));

	$cookiepath = preg_replace('|https?://[^/]+|i', '', SFSITEURL );
	setcookie('sforum_' . COOKIEHASH, $dname, time() + 30000000, $cookiepath, false);
}

function sf_get_login_display_name($login_name)
{
	global $wpdb;

	return $wpdb->get_var(
			"SELECT ".SFMEMBERS.".display_name
			 FROM ".SFMEMBERS."
			 JOIN ".SFUSERS." ON ".SFUSERS.".ID = ".SFMEMBERS.".user_id
			 WHERE user_login = '".$login_name."';");
}

function sf_login_site_url($url, $path, $scheme)
{
    # only care about login site urls with spf skinned login
    if ($scheme != 'login' || !isset($_REQUEST['view'])) return $url;

    if (strpos($url, 'action='))
    {
        $url.= '&view=forum';
    } else {
        $url.= '?view=forum';
    }

    return $url;
}

?>