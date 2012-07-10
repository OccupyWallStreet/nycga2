<?php
/**
 * BP Template Pack Admin
 *
 * Adds admin page to copy over BP templates and deactivation hooks.
 *
 * @package BP_TPack
 * @subpackage Admin
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * When BPT is deactivated, remove a few options from the DB
 */
function bp_tpack_deactivate() {
	/* Cleanup */
	delete_option( 'bp_tpack_disable_js' );
	delete_option( 'bp_tpack_disable_css' );
	delete_option( 'bp_tpack_configured' );
}
register_deactivation_hook( __FILE__, 'bp_tpack_deactivate' );

/**
 * Adds the BPT admin page under the "Themes" menu.
 */
function bp_tpack_add_theme_menu() {
	add_theme_page( __( 'BP Compatibility', 'bp-tpack' ), __( 'BP Compatibility', 'bp-tpack' ), 'switch_themes', 'bp-tpack-options', 'bp_tpack_theme_menu' );
}
add_action( 'admin_menu', 'bp_tpack_add_theme_menu' );

/**
 * Loads custom language file.
 */
function bp_tpack_load_language() {
	load_plugin_textdomain( 'bp-tpack', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
}
add_action( 'plugins_loaded', 'bp_tpack_load_language', 9 );

/**
 * Adds an admin notice if BPT hasn't been setup yet.
 */
function bp_tpack_admin_notices() {
	global $wp_version;

	// if WP version is less than 3.2, show notice when on TPack options page
	if ( isset( $_GET['page'] ) && 'bp-tpack-options' == $_GET['page'] ) {
		if ( version_compare( $wp_version, '3.2', '<' ) ) {
		?>
			<div class="error">
				<p><?php _e( "Hey you! You're using an older version of WordPress.  Please upgrade to <strong>WordPress 3.2</strong>, otherwise the javascript bundled with BuddyPress will cease to work with your WordPress theme.", 'bp-tpack' ); ?></p>
			</div>
		<?php
		}

		return;
	}

	if ( !(int)get_option( 'bp_tpack_configured' ) ) {
		?>

		<div id="message" class="updated fade">
			<p><?php printf( __( 'You have activated the BuddyPress Template Pack, but you haven\'t completed the setup process. Visit the <a href="%s">BP Compatibility</a> page to wrap up.', 'bp-tpack' ), add_query_arg( 'page', 'bp-tpack-options', admin_url( 'themes.php' ) ) ) ?></p>
		</div>

		<?php
	}
}
add_action( 'admin_notices', 'bp_tpack_admin_notices' );

/**
 * Output the BPT admin page
 */
function bp_tpack_theme_menu() {
	if ( !empty( $_GET['finish'] ) )
		update_option( 'bp_tpack_configured', 1 );

	if ( !empty( $_GET['reset'] ) )
		delete_option( 'bp_tpack_configured' );

	if (   !file_exists( get_query_template('bp-tpack', array('activity', 'blogs', 'forums', 'groups', 'members', 'registration') ) ) ) {
		$step = 1;

		if ( !empty( $_GET['move'] ) ) {
			$step = 2;
			$error = false;

			/* Attempt to move the directories */
			if ( !bp_tpack_move_templates() )
				$error = true;
		}

		/* Make sure we reset if template files have been deleted. */
		delete_option( 'bp_tpack_configured' );
	} else
		$step = 3;

	if ( !empty( $_POST['bp_tpack_save'] ) ) {
		/* Save options */
		if ( !empty( $_POST['bp_tpack_disable_css'] ) )
			update_option( 'bp_tpack_disable_css', 1 );
		else
			delete_option( 'bp_tpack_disable_css' );

		if ( !empty( $_POST['bp_tpack_disable_js'] ) )
			update_option( 'bp_tpack_disable_js', 1 );
		else
			delete_option( 'bp_tpack_disable_js' );
	}

	if ( !(int)get_option( 'bp_tpack_configured' ) ) {
?>
	<div class="wrap">
		<h2><?php _e( 'Making Your Theme BuddyPress Compatible', 'bp-tpack' ); ?></h2>

		<p><?php _e( 'Adding support for BuddyPress to your existing WordPress theme is a straightforward process. Follow the setup instructions on this page.', 'bp-tpack' ); ?></p>

		<?php switch( $step ) {
			case 1: ?>

				<h2><?php _e( 'Step One: Moving template files automatically', 'bp-tpack' ); ?></h2>

				<p><?php _e( 'BuddyPress needs some extra template files in order to display its pages correctly. This plugin will attempt to automatically move the necessary files into your current theme.', 'bp-tpack' ); ?></p>

				<p><?php _e( 'Click the button below to start the process.', 'bp-tpack' ); ?></p>

				<p><a class="button" href="?page=bp-tpack-options&move=1"><?php _e( 'Move Template Files', 'bp-tpack' ); ?></a></p>

			<?php break; ?>

		<?php case 2: ?>

				<?php if ( $error ) : ?>

					<h2><?php _e( 'Step Two: Moving templates manually', 'bp-tpack' ); ?></h2>

					<p><?php _e( "<strong>Moving templates failed.</strong> There was an error when trying to move the templates automatically. This probably means that we don't have the correct permissions. That's all right - it just means you'll have to move the template files manually.", 'bp-tpack' ); ?></p>

					<p><?php _e( 'You will need to connect to your WordPress files using FTP. When you are connected browse to the following directory:', 'bp-tpack' ); ?><p>

					<p><code><?php echo BP_PLUGIN_DIR . '/bp-themes/bp-default/' ?></code></p>

					<p><?php _e( 'In this directory you will find six folders (/activity/, /blogs/, /forums/, /groups/, /members/, /registration/). If you want to use all of the features of BuddyPress then you must move these six directories to the following folder:', 'bp-tpack' ); ?></p>

					<p><code><?php echo get_stylesheet_directory() . '/' ?></code></p>

					<p><?php _e( "If you decide that you don't want to use a feature of BuddyPress, then you can actually ignore the template folders for these features. For example, if you don't want to use the groups and forums features, you can simply avoid copying the /groups/ and /forums/ template folders to your active theme. (If you're not sure what to do, just copy all six folders over to your theme directory.)", 'bp-tpack' ); ?></p>

					<p><?php _e( 'Once you have correctly copied the folders into your active theme, please use the button below to move onto step three.', 'bp-tpack' ); ?></p>

					<p><a href="?page=bp-tpack-options" class="button"><?php _e( "I've finished moving template folders", 'bp-tpack' ); ?></a></p>

				<?php else : ?>

					<h2><?php _e( 'Templates moved successfully!', 'bp-tpack' ); ?></h2>

					<p><?php _e( 'Great news! BuddyPress templates are now in the correct position in your theme, which means that we can skip Step Two: Moving Templates Manually, and move directly to Step Three. Cool!', 'bp-tpack' ); ?></p>

					<p><a class="button" href="?page=bp-tpack-options"><?php _e( 'Continue to Step Three', 'bp-tpack' ); ?></a></p>

				<?php endif; ?>

		<?php break; ?>
		<?php case 3: ?>
			<h2><?php _e( 'Step Three: Tweaking your layout', 'bp-tpack' ); ?></h2>

			<p><?php printf( __( 'Now that the template files are in the correct location, <a href="%s" target="_blank">check out your site</a>. (You can come back to the current page at any time, by visiting Dashboard > Appearance > BP Compatibility.) You should see a BuddyPress admin bar at the top of the page. Try visiting some of the links in the "My Account" menu. If everything has gone right up to this point, you should be able to see your BuddyPress content.', 'bp-tpack' ), get_bloginfo( 'url' ) ) ?></p>

			<p><?php _e( 'If you find that the pages are not quite aligned correctly, or the content is overlapping the sidebar, you may need to tweak the template HTML. Please follow the "fixing alignment" instructions below. If the content in your pages is aligned to your satisfaction, then you can skip to the "Finishing Up" section at the bottom of this page.', 'bp-tpack' ); ?></p>

			<h3><?php _e( 'Fixing Alignment', 'bp-tpack' ); ?></h3>

			<p><?php _e( 'By default BuddyPress templates use this HTML structure:', 'bp-tpack' ); ?></p>

<p><pre><code style="display: block; width: 40%; padding-left: 15px;">
[HEADER]

&lt;div id="container"&gt;
	&lt;div id="content"&gt;
		[PAGE CONTENT]
	&lt;/div&gt;

	&lt;div id="sidebar"&gt;
		[SIDEBAR CONTENT]
	&lt;/div&gt;
&lt;/div&gt;

[FOOTER]

</code></pre></p>

			<p><?php _e( "If BuddyPress pages are not aligned correctly, then you may need to modify some of the templates to match your theme's HTML structure. The best way to do this is to access your theme's files, via FTP, at:", 'bp-tpack' ); ?></p>

			<p><code><?php echo get_stylesheet_directory() . '/' ?></code></p>

			<p><?php _e( 'Open up the <code>page.php</code> file (if this does not exist, use <code>index.php</code>). Make note of the HTML template structure of the file, specifically the <code>&lt;div&gt;</code> tags that surround the content and sidebar.', 'bp-tpack' ); ?></p>

			<p><?php _e( 'You will need to change the HTML structure in the BuddyPress templates that you copied into your theme to match the structure in your <code>page.php</code> or <code>index.php</code> file.', 'bp-tpack' ); ?></p>

			<?php if ( version_compare( BP_VERSION, '1.3' ) > 0 ) : ?>
				<p><?php _e( "There are two methods for making the necessary template changes.", 'bp-tpack' ) ?></p>

				<ol>
					<li>
						<?php _e( "The first method is to locate the following templates (leave out any folders that you didn't copy over in Step Two):", 'bp-tpack' ) ?>

			<?php else : ?>
				<p><?php _e( 'The files that you need to edit are as follows (leave out any folders you have not copied over in step two):', 'bp-tpack' ); ?></p>
			<?php endif; ?>

			<ul style="list-style: disc; margin-left: 40px;">
				<li><code><?php echo '/activity/index.php' ?></code></li>
				<li><code><?php echo '/blogs/index.php' ?></code></li>
				<li><code><?php echo '/forums/index.php' ?></code></li>
				<li><code><?php echo '/groups/index.php' ?></code></li>
				<li><code><?php echo '/groups/create.php' ?></code></li>
				<li><code><?php echo '/groups/single/home.php' ?></code></li>
				<li><code><?php echo '/groups/single/plugins.php' ?></code></li>
				<li><code><?php echo '/members/index.php' ?></code></li>
				<li><code><?php echo '/members/single/home.php' ?></code></li>
				<li><code><?php echo '/members/single/plugins.php' ?></code></li>
				<li><code><?php echo '/members/single/settings/delete-account.php' ?></code></li>
				<li><code><?php echo '/members/single/settings/notifications.php' ?></code></li>
				<li><code><?php echo '/members/single/settings/general.php' ?></code></li>
				<li><code><?php echo '/registration/register.php' ?></code></li>
				<li><code><?php echo '/registration/activate.php' ?></code></li>

				<?php if ( is_multisite() ) : ?>
					<li><code><?php echo '/blogs/create.php' ?></code></li>
				<?php endif; ?>
			</ul>

			<?php if ( version_compare( BP_VERSION, '1.3' ) > 0 ) : ?>
					</li>

					<li>
						<p><?php _e( "Alternatively, you may find it easier to make copies of your theme's <code>header.php</code>, <code>sidebar.php</code> and <code>footer.php</code> and rename them to <code>header-buddypress.php</code>, <code>sidebar-buddypress.php</code>, and <code>footer-buddypress.php</code>.", 'bp-tpack' ); ?></p>

						<p><?php _e( "Then you can alter the structure of these new template files (<code>header-buddypress.php</code>, <code>sidebar-buddypress.php</code>, and <code>footer-buddypress.php</code>) to resemble your theme's <code>page.php</code> (or <code>index.php</code>).", 'bp-tpack' ); ?></p>
					</li>
				</ol>
			<?php endif; ?>

			<p><?php _e( 'Once you are done matching up the HTML structure of your theme in these template files, please take another look through your site. You should find that BuddyPress pages now fit inside the content structure of your theme.', 'bp-tpack' ); ?></p>

			<h3><?php _e( 'Finishing Up', 'bp-tpack' ); ?></h3>

			<p><?php _e( "You're now all done with the conversion process. Your WordPress theme will now happily provide BuddyPress compatibility support. Once you hit the finish button you will be presented with a new permanent theme options page, which will allow you to tweak some settings.", 'bp-tpack' ); ?></p>

			<p><a href="?page=bp-tpack-options&finish=1" class="button-primary"><?php _e( 'Finish', 'bp-tpack' ); ?></a></p>
			<p>&nbsp;</p>

		<?php break;?>

		<?php } ?>
	</div>

<?php } else { // The theme steps have been completed, just show the permanent page ?>

	<div class="wrap">

		<h2><?php _e( 'BuddyPress Theme Compatibility', 'bp-tpack' ); ?></h2>

		<?php if ( !empty( $_GET['finish'] ) ) : ?>
			<div id="message">
				<p><strong><?php _e( 'Congratulations, you have completed the BuddyPress theme compatibility setup procedure!', 'bp-tpack' ); ?></strong></p>
			</div>
		<?php endif; ?>

		<form action="" name="bp-tpack-settings" method="post" style="width: 60%; float: left; margin-right: 3%;">

			<p><strong><input type="checkbox" name="bp_tpack_disable_css" value="1"<?php if ( (int)get_option( 'bp_tpack_disable_css' ) ) : ?> checked="checked"<?php endif; ?> /> <?php _e( 'Disable BP Template Pack CSS', 'bp-tpack' ); ?></strong></p>
			<p>
				<small style="display: block; margin-left:18px; font-size: 11px"><?php _e( "The BuddyPress template pack comes with basic wireframe CSS styles that will format the layout of BuddyPress pages. You can extend upon these styles in your theme's CSS file, or simply turn them off and build your own styles.", 'bp-tpack' ); ?></small>
			</p>

			<p style="margin-top: 20px;"><strong><input type="checkbox" name="bp_tpack_disable_js" value="1"<?php if ( (int)get_option( 'bp_tpack_disable_js' ) ) : ?> checked="checked"<?php endif; ?> /> <?php _e( 'Disable BP Template Pack JS / AJAX', 'bp-tpack' ); ?></strong></p>
				<small style="display: block; margin-left:18px; font-size: 11px"><?php _e( 'The BuddyPress template pack will automatically integrate the BuddyPress default theme javascript and AJAX functionality into your theme. You can switch this off, however the experience will be somewhat degraded.', 'bp-tpack' ); ?></small>

			<p class="submit">
				<input type="submit" name="bp_tpack_save" value="<?php _e( 'Save Settings', 'bp-tpack' ); ?>" class="button" />
			</p>
		</form>

		<div style="float: left; width: 37%;">

			<?php /* In BP 1.5+, we remove the "BuddyPress is ready" message dynamically */ ?>
			<?php if ( version_compare( BP_VERSION, '1.3' ) <= 0 ) : ?>
				<p style="line-height: 180%; border: 1px solid #eee; background: #fff; padding: 5px 10px;"><?php _e( '<strong>NOTE:</strong> To remove the "BuddyPress is ready" message you will need to add a "buddypress" tag to your theme. You can do this by editing the <code>style.css</code> file of your active theme and adding the tag to the "Tags:" line in the comment header.', 'bp-tpack' ); ?></p>
			<?php endif ?>

			<h4><?php _e( 'Navigation Links', 'bp-tpack' ); ?></h4>

			<p><?php _e( 'You may want to add new navigation tabs or links to your theme to link to BuddyPress directory pages. The default set of links are:', 'bp-tpack' ); ?></p>
				<ul>
					<?php if ( bp_is_active( 'activity' ) ) : ?>
						<li><?php _e( 'Activity', 'bp-tpack' ); ?>: <a href="<?php echo get_option('home') . '/' . bp_get_root_slug( BP_ACTIVITY_SLUG ) . '/'; ?>"><?php echo get_option('home') . '/' . BP_ACTIVITY_SLUG . '/'; ?></a></li>
					<?php endif ?>

					<li><?php _e( 'Members', 'bp-tpack' ); ?>: <a href="<?php echo get_option('home') . '/' . bp_get_root_slug( BP_MEMBERS_SLUG ) . '/'; ?>"><?php echo get_option('home') . '/' . BP_MEMBERS_SLUG . '/'; ?></a></li>

					<?php if ( bp_is_active( 'groups' ) ) : ?>
						<li><?php _e( 'Groups', 'bp-tpack' ); ?>: <a href="<?php echo get_option('home') . '/' . bp_get_root_slug( BP_GROUPS_SLUG ) . '/'; ?>"><?php echo get_option('home') . '/' . BP_GROUPS_SLUG . '/'; ?></a></li>
					<?php endif ?>

					<?php if ( bp_is_active( 'forums' ) ) : ?>
						<li><?php _e( 'Forums', 'bp-tpack' ); ?>: <a href="<?php echo get_option('home') . '/' . bp_get_root_slug( BP_FORUMS_SLUG ) . '/'; ?>"><?php echo get_option('home') . '/' . BP_FORUMS_SLUG . '/'; ?></a></li>
					<?php endif ?>

					<li><?php _e( 'Register', 'bp-tpack' ); ?>: <a href="<?php echo get_option('home') . '/' . bp_get_root_slug( BP_REGISTER_SLUG ) . '/'; ?>"><?php echo get_option('home') . '/' . BP_REGISTER_SLUG . '/'; ?></a> <?php _e( '(registration must be enabled)', 'bp-tpack' ); ?></li>

					<?php if ( is_multisite() && bp_is_active( 'blogs' ) ) : ?>
						<li><?php _e( 'Blogs', 'bp-tpack' ); ?>: <a href="<?php echo get_option('home') . '/' . bp_get_root_slug( BP_BLOGS_SLUG ) . '/'; ?>"><?php echo get_option('home') . '/' . BP_BLOGS_SLUG . '/'; ?></a></li>
					<?php endif; ?>
				</ul>

			<h4><?php _e( 'Reset Setup', 'bp-tpack' ); ?></h4>
			<p><?php _e( "If you would like to run through the setup process again please use the reset button (you will start at step three if you haven't removed the template files):", 'bp-tpack' ); ?></p>
			<p><a class="button" href="?page=bp-tpack-options&reset=1"><?php _e( 'Reset', 'bp-tpack' ); ?></a></p>
		</div>

<?php
	}
}

/**
 * Function to copy over bp-default's main templates to the current WP theme
 *
 * @uses bp_tpack_recurse_copy()
 */
function bp_tpack_move_templates() {
	$destination_dir = get_stylesheet_directory() . '/';
	$source_dir = BP_PLUGIN_DIR . '/bp-themes/bp-default/';

	$dirs = array( 'activity', 'blogs', 'forums', 'groups', 'members', 'registration' );

	foreach ( (array)$dirs as $dir ) {
		if ( !bp_tpack_recurse_copy( $source_dir . $dir, $destination_dir . $dir ) )
			return false;
	}

	return true;
}

/**
 * Removes the "you'll need to activate a BuddyPress-compatible theme" message from the admin when
 * the plugin is up and running successfully
 *
 * @since 1.3
 */
function bp_tpack_remove_compatibility_message() {
	global $bp;

	// Only works with BP 1.5 or greater
	if ( !empty( $bp->admin->notices ) ) {
		// Check to see whether we've completed the setup
		if ( get_option( 'bp_tpack_configured' ) ) {
			// Remove the message. They're not semantically keyed, so this is a hack
			// Search for the themes.php link, which will work under translations
			foreach( $bp->admin->notices as $key => $notice ) {
				if ( false !== strpos( $notice, 'themes.php' ) ) {
					unset( $bp->admin->notices[$key] );
				}
			}

			// Reset the indexes
			$bp->admin->notices = array_values( $bp->admin->notices );
		}
	}
}
add_action( 'admin_notices', 'bp_tpack_remove_compatibility_message', 2 );

/**
 * Helper function to copy files from one folder over to another
 *
 * @param string $src Location of source directory to copy
 * @param string $dst Location of destination directory where the copied files should reside
 * @see bp_tpack_move_templates()
 */
function bp_tpack_recurse_copy( $src, $dst ) {
	$dir = @opendir( $src );

	if ( !@mkdir( $dst ) )
		return false;

	while ( false !== ( $file = readdir( $dir ) ) ) {
		if ( ( $file != '.' ) && ( $file != '..' ) ) {
			if ( is_dir( $src . '/' . $file ) )
				bp_tpack_recurse_copy( $src . '/' . $file, $dst . '/' . $file );
			else {
				if ( !@copy( $src . '/' . $file, $dst . '/' . $file ) )
					return false;
			}
		}
	}

	@closedir( $dir );

	return true;
}

if ( !function_exists( 'bp_get_root_slug' ) ) :
/**
 * BP 1.2-compatible version of bp_get_root_slug()
 */
function bp_get_root_slug( $slug ) {
	if ( empty ( $slug ) )
		return false;

	return $slug;
}
endif;

?>