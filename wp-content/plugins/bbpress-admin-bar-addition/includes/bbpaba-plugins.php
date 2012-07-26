<?php
/**
 * Display links to active bbPress 2.x plugins/extensions settings' pages
 *
 * @package    bbPress Admin Bar Addition
 * @subpackage Plugin/Extension Support
 * @author     David Decker - DECKERWEB
 * @copyright  Copyright 2011-2012, David Decker - DECKERWEB
 * @license    http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link       http://genesisthemes.de/en/wp-plugins/bbpress-admin-bar-addition/
 * @link       http://twitter.com/#!/deckerweb
 *
 * @since 1.0
 * @version 1.2
 */

/**
 * GD bbPress Attachments (free, by Dev4Press)
 *
 * @since 1.0
 */
if ( ( ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'gd-bbpress-attachments/gd-bbpress-attachments.php' ) ) || class_exists( 'gdbbPressAttachments' ) ) && current_user_can( GDBBPRESSATTACHMENTS_CAP ) ) {

	/** Entry at "Extensions" level submenu */
	$menu_items['ext-gdattachments'] = array(
		'parent' => $extensions,
		'title'  => __( 'GD bbPress Attachments', 'bbpaba' ),
		'href'   => admin_url( 'edit.php?post_type=forum&page=gdbbpress_attachments' ),
		'meta'   => array( 'target' => '', 'title' => __( 'GD bbPress Attachments', 'bbpaba' ) )
	);

	/** Entry at "Forums" level submenu */
	$menu_items['f_gdattachments'] = array(
		'parent' => $forums,
		'title'  => __( 'Attachments', 'bbpaba' ),
		'href'   => admin_url( 'edit.php?post_type=forum&page=gdbbpress_attachments' ),
		'meta'   => array( 'target' => '', 'title' => __( 'Attachments', 'bbpaba' ) )
	);

}  // end-if bbP Attachments


/**
 * GD bbPress Tools (free, by Dev4Press)
 *
 * @since 1.5
 */
if ( ( ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'gd-bbpress-tools/gd-bbpress-tools.php' ) ) || class_exists( 'gdbbPressTools' ) ) && current_user_can( GDBBPRESSTOOLS_CAP ) ) {

	/** Entry at "Extensions" level submenu */
	$menu_items['extgdtools'] = array(
		'parent' => $extensions,
		'title'  => __( 'GD bbPress Tools', 'bbpaba' ),
		'href'   => admin_url( 'edit.php?post_type=forum&page=gdbbpress_tools' ),
		'meta'   => array( 'target' => '', 'title' => __( 'GD bbPress Tools', 'bbpaba' ) )
	);
	$menu_items['extgdtools-bbcode'] = array(
		'parent' => $extgdtools,
		'title'  => __( 'BBCodes Settings', 'bbpaba' ),
		'href'   => admin_url( 'edit.php?post_type=forum&page=gdbbpress_tools&tab=bbcode' ),
		'meta'   => array( 'target' => '', 'title' => __( 'BBCodes Settings', 'bbpaba' ) )
	);
	$menu_items['extgdtools-views'] = array(
		'parent' => $extgdtools,
		'title'  => __( 'Views Settings', 'bbpaba' ),
		'href'   => admin_url( 'edit.php?post_type=forum&page=gdbbpress_tools&tab=views' ),
		'meta'   => array( 'target' => '', 'title' => __( 'Views Settings', 'bbpaba' ) )
	);

	/** Entry at "Forums" level submenu */
	$menu_items['f_gdtools'] = array(
		'parent' => $forums,
		'title'  => __( 'Tools', 'bbpaba' ),
		'href'   => admin_url( 'edit.php?post_type=forum&page=gdbbpress_tools' ),
		'meta'   => array( 'target' => '', 'title' => __( 'Tools', 'bbpaba' ) )
	);
	$menu_items['f_gdtools-bbcode'] = array(
		'parent' => $f_gdtools,
		'title'  => __( 'BBCodes Settings', 'bbpaba' ),
		'href'   => admin_url( 'edit.php?post_type=forum&page=gdbbpress_tools&tab=bbcode' ),
		'meta'   => array( 'target' => '', 'title' => __( 'BBCodes Settings', 'bbpaba' ) )
	);
	$menu_items['f_gdtools-views'] = array(
		'parent' => $f_gdtools,
		'title'  => __( 'Views Settings', 'bbpaba' ),
		'href'   => admin_url( 'edit.php?post_type=forum&page=gdbbpress_tools&tab=views' ),
		'meta'   => array( 'target' => '', 'title' => __( 'Views Settings', 'bbpaba' ) )
	);

}  // end-if bbP Tools


/**
 * GD bbPress Widgets (free, by Dev4Press)
 *
 * @since 1.5
 */
if ( ( ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'gd-bbpress-widgets/gd-bbpress-widgets.php' ) ) || class_exists( 'gdbbPressWidgets' ) ) && current_user_can( GDBBPRESSWIDGETS_CAP ) ) {

	/** Entry at "Extensions" level submenu */
	$menu_items['ext-gdwidgets'] = array(
		'parent' => $extensions,
		'title'  => __( 'GD bbPress Widgets', 'bbpaba' ),
		'href'   => admin_url( 'edit.php?post_type=forum&page=gdbbpress_widgets' ),
		'meta'   => array( 'target' => '', 'title' => __( 'GD bbPress Widgets', 'bbpaba' ) )
	);

	/** Entry at "Forums" level submenu */
	$menu_items['f_gdwidgets'] = array(
		'parent' => $forums,
		'title'  => __( 'Widgets Settings', 'bbpaba' ),
		'href'   => admin_url( 'edit.php?post_type=forum&page=gdbbpress_widgets' ),
		'meta'   => array( 'target' => '', 'title' => _x( 'Additional Widgets Settings', 'Translators: For the tooltip', 'bbpaba' ) )
	);

}  // end-if bbP Widgets


/**
 * GD bbPress Toolbox Pro (premium, by Dev4Press)
 *
 * @since 1.6
 */
if ( ( ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'gd-bbpress-toolbox/gd-bbpress-toolbox.php' ) ) || class_exists( 'gdbbPressToolbox' ) ) && current_user_can( 'edit_dashboard' ) ) {

	/** Entry at "Extensions" level submenu */
	$menu_items['extgdtoolbox'] = array(
		'parent' => $extensions,
		'title'  => __( 'GD bbPress Toolbox', 'bbpaba' ),
		'href'   => admin_url( 'admin.php?page=gd-bbpress-toolbox-front' ),
		'meta'   => array( 'target' => '', 'title' => __( 'GD bbPress Toolbox', 'bbpaba' ) )
	);
	$menu_items['extgdtoolbox-attachments'] = array(
		'parent' => $extgdtoolbox,
		'title'  => __( 'Attachments', 'bbpaba' ),
		'href'   => admin_url( 'admin.php?page=gd-bbpress-toolbox-attachments' ),
		'meta'   => array( 'target' => '', 'title' => __( 'Attachments', 'bbpaba' ) )
	);
	$menu_items['extgdtoolbox-settings'] = array(
		'parent' => $extgdtoolbox,
		'title'  => __( 'Settings', 'bbpaba' ),
		'href'   => admin_url( 'admin.php?page=gd-bbpress-toolbox-settings' ),
		'meta'   => array( 'target' => '', 'title' => __( 'Settings', 'bbpaba' ) )
	);
	$menu_items['extgdtoolbox-errorlog'] = array(
		'parent' => $extgdtoolbox,
		'title'  => __( 'Error Log', 'bbpaba' ),
		'href'   => admin_url( 'admin.php?page=gd-bbpress-toolbox-errors' ),
		'meta'   => array( 'target' => '', 'title' => __( 'Error Log', 'bbpaba' ) )
	);
	$menu_items['extgdtoolbox-overview'] = array(
		'parent' => $extgdtoolbox,
		'title'  => __( 'Overview', 'bbpaba' ),
		'href'   => admin_url( 'admin.php?page=gd-bbpress-toolbox-front' ),
		'meta'   => array( 'target' => '', 'title' => __( 'Overview', 'bbpaba' ) )
	);
	$menu_items['extgdtoolbox-about'] = array(
		'parent' => $extgdtoolbox,
		'title'  => __( 'About', 'bbpaba' ),
		'href'   => admin_url( 'admin.php?page=gd-bbpress-toolbox-about' ),
		'meta'   => array( 'target' => '', 'title' => __( 'About', 'bbpaba' ) )
	);

	/** Entry at "Forums" level submenu */
	$menu_items['f_gdtoolbox'] = array(
		'parent' => $forums,
		'title'  => __( 'Toolbox', 'bbpaba' ),
		'href'   => admin_url( 'admin.php?page=gd-bbpress-toolbox-front' ),
		'meta'   => array( 'target' => '', 'title' => __( 'Toolbox', 'bbpaba' ) )
	);
	$menu_items['f_gdtools-attachments'] = array(
		'parent' => $f_gdtoolbox,
		'title'  => __( 'Attachments', 'bbpaba' ),
		'href'   => admin_url( 'admin.php?page=gd-bbpress-toolbox-attachments' ),
		'meta'   => array( 'target' => '', 'title' => __( 'Attachments', 'bbpaba' ) )
	);
	$menu_items['f_gdtools-settings'] = array(
		'parent' => $f_gdtoolbox,
		'title'  => __( 'Settings', 'bbpaba' ),
		'href'   => admin_url( 'admin.php?page=gd-bbpress-toolbox-settings' ),
		'meta'   => array( 'target' => '', 'title' => __( 'Settings', 'bbpaba' ) )
	);

}  // end-if bbP Toolbox Pro


/**
 * bbPress Moderation (free, by Ian Haycox)
 *
 * @since 1.3
 */
if ( ( ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'bbpressmoderation/bbpressmoderation.php' ) ) || class_exists( 'bbPressModeration' ) ) && current_user_can( 'manage_options' ) ) {
	$menu_items['ext-bbpmoderation'] = array(
		'parent' => $extensions,
		'title'  => __( 'bbPress Moderation Settings', 'bbpaba' ),
		'href'   => admin_url( 'options-general.php?page=bbpressmoderation' ),
		'meta'   => array( 'target' => '', 'title' => __( 'bbPress Moderation Settings', 'bbpaba' ) )
	);
	$menu_items['s-bbpmoderation'] = array(
		'parent' => $bbpsettings,
		'title'  => __( 'Moderation Settings (Plugin)', 'bbpaba' ),
		'href'   => admin_url( 'options-general.php?page=bbpressmoderation' ),
		'meta'   => array( 'title' => __( 'Moderation Settings (Plugin)', 'bbpaba' ) )
	);
}  // end-if bbP Moderation


/**
 * WangGuard (free, by WangGuard Team)
 *
 * @since 1.4
 */
if ( ( ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'wangguard/wangguard-admin.php' ) ) || function_exists( 'wangguard_init' ) ) && current_user_can( 'manage_options' ) ) {

	/** Entries at "Users" level submenu */
	$menu_items['userswangguard'] = array(
		'parent' => $users,
		'title'  => __( 'WangGuard Moderation Queue', 'bbpaba' ),
		'href'   => network_admin_url( 'admin.php?page=wangguard_queue' ),
		'meta'   => array( 'target' => '', 'title' => __( 'WangGuard Moderation Queue', 'bbpaba' ) )
	);
	$menu_items['userswangguard-users'] = array(
		'parent' => $users,
		'title'  => __( 'WangGuard Users', 'bbpaba' ),
		'href'   => network_admin_url( 'admin.php?page=wangguard_users' ),
		'meta'   => array( 'target' => '', 'title' => __( 'WangGuard Users', 'bbpaba' ) )
	);
	$menu_items['userswangguard-wizard'] = array(
		'parent' => $users,
		'title'  => __( 'WangGuard Wizard', 'bbpaba' ),
		'href'   => network_admin_url( 'admin.php?page=wangguard_wizard' ),
		'meta'   => array( 'title' => __( 'WangGuard Wizard', 'bbpaba' ) )
	);
	$menu_items['userswangguard-stats'] = array(
		'parent' => $users,
		'title'  => __( 'WangGuard Statistics', 'bbpaba' ),
		'href'   => network_admin_url( 'admin.php?page=wangguard_stats' ),
		'meta'   => array( 'title' => __( 'WangGuard Statistics', 'bbpaba' ) )
	);

	/** Entries at "Extensions" level submenu */
	$menu_items['extwangguard'] = array(
		'parent' => $extensions,
		'title'  => __( 'WangGuard Moderation Queue', 'bbpaba' ),
		'href'   => network_admin_url( 'admin.php?page=wangguard_queue' ),
		'meta'   => array( 'target' => '', 'title' => __( 'WangGuard Moderation Queue', 'bbpaba' ) )
	);
	$menu_items['extwangguard-users'] = array(
		'parent' => $extwangguard,
		'title'  => __( 'WangGuard Users', 'bbpaba' ),
		'href'   => network_admin_url( 'admin.php?page=wangguard_users' ),
		'meta'   => array( 'target' => '', 'title' => __( 'WangGuard Users', 'bbpaba' ) )
	);
	$menu_items['extwangguard-wizard'] = array(
		'parent' => $extwangguard,
		'title'  => __( 'Wizard', 'bbpaba' ),
		'href'   => network_admin_url( 'admin.php?page=wangguard_wizard' ),
		'meta'   => array( 'title' => __( 'Wizard', 'bbpaba' ) )
	);
	$menu_items['extwangguard-stats'] = array(
		'parent' => $extwangguard,
		'title'  => __( 'Statistics', 'bbpaba' ),
		'href'   => network_admin_url( 'admin.php?page=wangguard_stats' ),
		'meta'   => array( 'title' => __( 'Statistics', 'bbpaba' ) )
	);
	$menu_items['extwangguard-config'] = array(
		'parent' => $extwangguard,
		'title'  => __( 'Configuration', 'bbpaba' ),
		'href'   => network_admin_url( 'admin.php?page=wangguard_conf' ),
		'meta'   => array( 'title' => __( 'Configuration', 'bbpaba' ) )
	);
}  // end-if WangGuard


/**
 * Members (free, by Justin Tadlock)
 *
 * @since 1.4
 */
if ( ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'members/members.php' ) ) && current_user_can( 'edit_roles' ) ) {

	/** Entries at "Users" level submenu */
	/** Edit Roles */
	if ( current_user_can( 'edit_roles' ) ) {
		$menu_items['u-members'] = array(
			'parent' => $users,
			'title'  => __( 'Members: Adjust Roles &amp; Capabilities', 'bbpaba' ),
			'href'   => admin_url( 'users.php?page=roles' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Members: Adjust Roles &amp; Capabilities', 'bbpaba' ) )
		);
	}  // end-if cap check

	/** Add new Role */
	if ( current_user_can( 'create_roles' ) ) {
		$menu_items['u-members-add'] = array(
			'parent' => $users,
			'title'  => __( 'Members: Add new Role', 'bbpaba' ),
			'href'   => admin_url( 'users.php?page=role-new' ),
			'meta'   => array( 'title' => __( 'Members: Add new Role', 'bbpaba' ) )
		);
	}  // end-if cap check

}  // end-if Members


/**
 * bbPress Post Toolbar (free, by master5o1)
 *
 * @since 1.0
 */
if ( ( ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'bbpress-post-toolbar/bbpress-post-toolbar.php' ) ) || class_exists( 'class bbp_5o1_toolbar' ) ) && current_user_can( 'manage_options' ) ) {
	$menu_items['ext-posttoolbar'] = array(
		'parent' => $extensions,
		'title'  => __( 'bbPress Post Toolbar', 'bbpaba' ),
		'href'   => admin_url( 'plugins.php?page=bbpress-post-toolbar' ),
		'meta'   => array( 'target' => '', 'title' => __( 'bbPress Post Toolbar', 'bbpaba' ) )
	);
}  // end-if bbP Post Toolbar


/**
 * bbPress Antispam (free, by Daniel Huesken)
 *
 * @since 1.3
 */
if ( ( ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'bbpress-antispam/bbpress-antispam.php' ) ) || class_exists( 'bbPress_Antispam' ) ) && current_user_can( 'manage_options' ) ) {
	$menu_items['ext-antispam'] = array(
		'parent' => $extensions,
		'title'  => __( 'bbPress Antispam Settings', 'bbpaba' ),
		'href'   => admin_url( 'options-general.php?page=bbpress#bbpress-antispam' ),
		'meta'   => array( 'target' => '', 'title' => __( 'bbPress Antispam Settings', 'bbpaba' ) )
	);
}  // end-if bbP Antispam


/**
 * bbPress reCaptcha (free, by Pippin Williamson)
 *
 * @since 1.0
 */
if ( ( ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'bbpress-recaptcha/bbpress-recaptcha.php' ) ) ) && current_user_can( 'manage_options' ) ) {
	$menu_items['ext-recaptcha'] = array(
		'parent' => $extensions,
		'title'  => __( 'bbPress reCaptcha', 'bbpaba' ),
		'href'   => admin_url( 'options-general.php?page=bbpress-recaptcha-settings' ),
		'meta'   => array( 'target' => '', 'title' => __( 'bbPress reCaptcha', 'bbpaba' ) )
	);
}  // end-if bbP reCaptcha


/**
 * bbPress WP Tweaks (free, by veppa)
 *
 * @since 1.4
 */
if ( ( ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'bbpress-wp-tweaks/bbpress-wp-tweaks.php' ) ) || class_exists( 'BbpressWpTweaks' ) ) && current_user_can( 'manage_options' ) ) {
	$menu_items['ext-wptweaks'] = array(
		'parent' => $extensions,
		'title'  => __( 'bbPress WP Tweaks', 'bbpaba' ),
		'href'   => admin_url( 'options-general.php?page=bbpress-wp-tweaks' ),
		'meta'   => array( 'target' => '', 'title' => __( 'bbPress WP Tweaks', 'bbpaba' ) )
	);
}  // end-if bbP WP Tweaks


/**
 * bbPress2 BBCode (free, by antonchanning + bOingball + Viper007Bond)
 *
 * @since 1.0
 */
if ( ( ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'bbpress-bbcode/bbpress2-bbcode.php' ) ) || class_exists( 'BBCode' ) ) && current_user_can( 'manage_options' ) ) {
	$menu_items['ext-bbcode'] = array(
		'parent' => $extensions,
		'title'  => __( 'bbPress2 BBCode', 'bbpaba' ),
		'href'   => admin_url( 'options-general.php?page=bbPress2-bbcode' ),
		'meta'   => array( 'target' => '', 'title' => __( 'bbPress2 BBCode', 'bbpaba' ) )
	);
}  // end-if bbP BBCode


/**
 * bbPress2 Shortcode Whitelist (free, by antonchanning)
 *
 * @since 1.0
 */
if ( ( ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'bbpress2-shortcode-whitelist/bbpress2-shortcode-whitelist.php' ) ) || class_exists( 'bbPressShortcodeWhitelist' ) ) && current_user_can( 'manage_options' ) ) {
	$menu_items['ext-shortcode-whitelist'] = array(
		'parent' => $extensions,
		'title'  => __( 'bbPress2 Shortcode Whitelist', 'bbpaba' ),
		'href'   => admin_url( 'options-general.php?page=bbPress2-shortcode-whitelist' ),
		'meta'   => array( 'target' => '', 'title' => __( 'bbPress2 Shortcode Whitelist', 'bbpaba' ) )
	);
}  // end-if bbP Shortcode Whitelist


/**
 * Custom Post Type Privacy (free, by Kev Price)
 *
 * @since 1.4
 */
if ( ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'custom-post-type-privacy/custom-post-type-privacy.php' ) ) || class_exists( 'wp_cpt_sentry' ) ) {

	/** Entry at "Extensions" level submenu */
	if ( current_user_can( 'edit_plugins' ) || current_user_can( 'edit_users' ) ) {
		$menu_items['extcptprivacy'] = array(
			'parent' => $extensions,
			'title'  => __( 'Custom Post Type Privacy / User Groups', 'bbpaba' ),
			'href'   => admin_url( 'admin.php?page=custom-post-type-privacy/custom-post-type-privacy.php' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Custom Post Type Privacy / User Groups', 'bbpaba' ) )
		);
	}  // end-if cap check

	/** Adjust "Forums" & "Topics" privacy */
	if ( current_user_can( 'edit_others_posts' ) ) {

		/** Entries at "Extensions" level submenu */
		$menu_items['extcptprivacy-forums'] = array(
			'parent' => $extcptprivacy,
			'title'  => __( 'Adjust Forum Privacy', 'bbpaba' ),
			'href'   => admin_url( 'admin.php?page=cpt-sentry-forum' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Adjust Forum Privacy', 'bbpaba' ) )
		);
		$menu_items['extcptprivacy-topics'] = array(
			'parent' => $extcptprivacy,
			'title'  => __( 'Adjust Topic Privacy', 'bbpaba' ),
			'href'   => admin_url( 'admin.php?page=cpt-sentry-topic' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Adjust Topic Privacy', 'bbpaba' ) )
		);

		/** Entry at "Forums" level submenu */
		$menu_items['f_cptprivacy-forums'] = array(
			'parent' => $forums,
			'title'  => __( 'Adjust Forum Privacy', 'bbpaba' ),
			'href'   => admin_url( 'admin.php?page=cpt-sentry-forum' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Adjust Forum Privacy', 'bbpaba' ) )
		);

		/** Entry at "Topics" level submenu */
		$menu_items['t_cptprivacy-topics'] = array(
			'parent' => $topics,
			'title'  => __( 'Adjust Topic Privacy', 'bbpaba' ),
			'href'   => admin_url( 'admin.php?page=cpt-sentry-topic' ),
			'meta'   => array( 'target' => '', 'title' => __( 'Adjust Topic Privacy', 'bbpaba' ) )
		);

	}  // end-if cap check

}  // end-if bbP CPT Privacy


/**
 * bbConverter (free, by anointed + AWJunkies)
 *
 * @since 1.2
 */
if ( ( ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'bbconverter/bbconverter.php' ) ) || class_exists( 'bbConverter' ) ) && current_user_can( 'manage_options' ) ) {
	$menu_items['extbbpconvert'] = array(
		'parent' => $extensions,
		'title'  => __( 'bbConverter', 'bbpaba' ),
		'href'   => admin_url( 'options-general.php?page=bbconverter' ),
		'meta'   => array( 'target' => '', 'title' => __( 'bbConverter', 'bbpaba' ) )
	);
	$menu_items['ext-bbconverter-site'] = array(
		'parent' => $extbbpconvert,
		'title'  => __( 'bbConverter Support Site', 'bbpaba' ),
		'href'   => 'http://www.bbconverter.com/',
		'meta'   => array( 'title' => __( 'bbConverter Support Site', 'bbpaba' ) )
	);
}  // end-if bbConverter


/**
 * WP SyntaxHighlighter (free, by redcocker)
 *
 * @since 1.3
 */
if ( ( ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'wp-syntaxhighlighter/wp-syntaxhighlighter.php' ) ) || function_exists( 'wp_sh_language_array' ) ) && current_user_can( 'manage_options' ) ) {
	$menu_items['ext-wpsyntaxhighlighter'] = array(
		'parent' => $extensions,
		'title'  => __( 'WP SyntaxHighlighter', 'bbpaba' ),
		'href'   => admin_url( 'options-general.php?page=wp-syntaxhighlighter-options' ),
		'meta'   => array( 'target' => '', 'title' => __( 'WP SyntaxHighlighter', 'bbpaba' ) )
	);
}  // end-if WP Syntaxhighlighter


/**
 * s2Member (free version, by WebSharks, Inc.)
 *
 * @since 1.5
 */
if ( defined( 'WS_PLUGIN__S2MEMBER_VERSION' ) && current_user_can( 'create_users' ) ) {
	$menu_items['exts2member'] = array(
		'parent' => $extensions,
		'title'  => __( 's2Member Integration', 'bbpaba' ),
		'href'   => network_admin_url( 'admin.php?page=ws-plugin--s2member-integrations' ),
		'meta'   => array( 'target' => '', 'title' => __( 's2Member Integration', 'bbpaba' ) )
	);
	$menu_items['exts2member-settings'] = array(
		'parent' => $exts2member,
		'title'  => __( 's2Member Settings', 'bbpaba' ),
		'href'   => network_admin_url( 'admin.php?page=ws-plugin--s2member-gen-ops' ),
		'meta'   => array( 'target' => '', 'title' => __( 's2Member Settings', 'bbpaba' ) )
	);
}  // end-if s2Member
