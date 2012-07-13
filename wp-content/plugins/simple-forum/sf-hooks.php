<?php
/*
Simple:Press
WP Hooks - Actions and Filters
$LastChangedDate: 2011-06-24 01:23:05 -0700 (Fri, 24 Jun 2011) $
$Rev: 6374 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

# ----------------------------------------------------------
# SITEWIDE GLOBAL WP Hooks - all page loads
# ----------------------------------------------------------
function sf_setup_sitewide_hooks()
{
	global $SFSTATUS;

	# ------------------------------------------------------------------
	# localisation
	# ------------------------------------------------------------------
	add_action('init', 'sfg_localisation');
	# ------------------------------------------------------------------

	# ------------------------------------------------------------------
	# mobile check
	# ------------------------------------------------------------------
	add_action('init', 'sf_mobile_check');
	# ------------------------------------------------------------------

	# ------------------------------------------------------------------
	# wp admin access
	# ------------------------------------------------------------------
	if($SFSTATUS == 'ok' && sf_get_option('sfblockadmin')) {
		add_action('init', 'sf_block_admin');
	}
	# ------------------------------------------------------------------

	# ------------------------------------------------------------------
	# Rewrite Rules
	# ------------------------------------------------------------------
	add_filter('page_rewrite_rules', 'sfg_set_rewrite_rules');
	add_filter('query_vars', 'sfg_set_query_vars');
	add_action('permalink_structure_changed', 'sfg_permalink_changed', 10, 2);
	# redirect for forum on front page
	add_filter('redirect_canonical', 'sfg_front_page_redirect');
	# ------------------------------------------------------------------

	# ------------------------------------------------------------------
	# Main front end page header
	# ------------------------------------------------------------------
    remove_action( 'wp_head', 'rel_canonical' );
	add_filter('wp_head', 'sf_check_header');
	# ------------------------------------------------------------------

	# ------------------------------------------------------------------
	# all in one seo pack plugin api
	# ------------------------------------------------------------------
	add_filter('aioseop_canonical_url', 'sf_aioseo_canonical_url');
	add_filter('aioseop_description', 'sf_aioseo_description');
	add_filter('aioseop_keywords', 'sf_aioseo_keywords');
	add_filter('aioseop_home_page_title','sf_aioseo_homepage');
	# ------------------------------------------------------------------

	# ------------------------------------------------------------------
	# Boot up forum or forum admin
	# These hooks are the earliest we can determine if the the request
	# is for either back/front end SPF and fron here we can load the
	# header code and start to include necessary files etc.
	# ------------------------------------------------------------------
	add_action('wp_print_scripts', 'sf_boot_forum');
	add_action('admin_menu', 'sf_boot_forum_admin');
	# ------------------------------------------------------------------

	# ------------------------------------------------------------------
	# RSS feeds
	# ------------------------------------------------------------------
	add_action('template_redirect', 'sfg_feed');
	# ------------------------------------------------------------------

	# ------------------------------------------------------------------
	# 404
	# ------------------------------------------------------------------
	add_action('template_redirect', 'sfg_404');
	# ------------------------------------------------------------------

	# ------------------------------------------------------------------
	# browser title
	# ------------------------------------------------------------------
	add_filter('wp_title', 'sfg_setup_browser_title');
	# ------------------------------------------------------------------

	# ------------------------------------------------------------------
	# Blog/Topic Linking
	# ------------------------------------------------------------------
	$sfsupport=sf_get_option('sfsupport');
	$sfpostlinking = sf_get_option('sfpostlinking');

	if($sfsupport['sfusinglinking']) {
		if($sfpostlinking['sfuseautolabel']) {
			add_filter('the_content', 'sf_show_blog_link');
			add_filter('the excerpt', 'sf_show_blog_link');
		}
		add_action('save_post', 'sf_save_blog_link');

		add_action('draft_to_publish', 'sf_publish_blog_link', 10, 1);
		add_action('future_to_publish', 'sf_publish_blog_link', 10, 1);
		add_action('pending_to_publish', 'sf_publish_blog_link', 10, 1);
		add_action('new_to_publish', 'sf_publish_blog_link', 10, 1);
		add_action('edit_post', 'sf_update_blog_link');
		add_action('delete_post', 'sf_delete_blog_link');

		if($sfpostlinking['sflinkcomments'] == 2 || $sfpostlinking['sflinkcomments'] == 3) {
			add_filter('comments_array', 'sf_topic_as_comments');
			add_filter('get_avatar_comment_types', 'sf_add_comment_type');
			add_filter('edit_comment_link', 'sf_remove_edit_comment_link', 1, 2);
		}

		if(isset($sfpostlinking['sfpostcomment']) && $sfpostlinking['sfpostcomment']==true) {
			add_action('wp_set_comment_status', 'sf_process_new_comment', 10, 2);
			add_action('comment_post', 'sf_process_new_comment', 10, 2);
			add_action('edit_comment', 'sf_update_comment_post');
		}

		if($SFSTATUS == 'ok') {
			add_action('admin_init', 'sf_blog_link_form');
			add_filter('manage_posts_columns', 'sf_add_admin_link_column');
			add_filter('manage_pages_columns', 'sf_add_admin_link_column');
			add_action('manage_posts_custom_column', 'sf_show_admin_link_column', 10, 2);
			add_action('manage_pages_custom_column', 'sf_show_admin_link_column', 10, 2);
		}
	}
	# ------------------------------------------------------------------

	# ------------------------------------------------------------------
	# Credential Actions/Filters
	# ------------------------------------------------------------------
	if($SFSTATUS == 'ok') {
		add_action('login_head', 'sf_login_header');
		add_filter('login_headerurl', 'sf_login_url');
		add_filter('login_headertitle', 'sf_login_title');
		add_action('wp_login', 'sf_post_login_check');
		add_filter('site_url', 'sf_login_site_url', 10, 3);
	}
	# ------------------------------------------------------------------

	# ------------------------------------------------------------------
	# user creation and deletion
	# ------------------------------------------------------------------
    # multisite user hooks
    add_action('wpmu_new_user', 'sf_create_member_data', 99);
    add_action('wpmu_activate_user', 'sf_create_member_data', 99);
    add_action('added_existing_user', 'sf_create_member_data', 99);
    add_action('wpmu_delete_user', 'sf_delete_member_data');
    add_action('remove_user_from_blog', 'sf_delete_member_data');

    # standard wp user hooks
    add_action('user_register', 'sf_create_member_data', 99);
    add_action('delete_user', 'sf_delete_member_data');

	# ------------------------------------------------------------------
	# user registrations and logout
	# ------------------------------------------------------------------
	add_action('wp_logout', 'sf_track_logout');
	add_action('register_form', 'sf_register_math', 1);
	add_filter('registration_errors', 'sf_register_error');

	# ------------------------------------------------------------------
	# RPX Support
	# ------------------------------------------------------------------
    $sfrpx = sf_get_option('sfrpx');
    if ($sfrpx['sfrpxenable'])
    {
        add_action('parse_request', 'spf_rpx_process_token');
        add_action('login_head', 'spf_rpx_login_head');
        add_action('show_user_profile', 'spf_rpx_edit_user_page');
    }

	# ------------------------------------------------------------------
	# user updates
	# ------------------------------------------------------------------
	add_action('profile_update', 'sf_update_member_data');
	add_action('set_user_role', 'sf_map_role_to_ug');
	# ------------------------------------------------------------------

	# ------------------------------------------------------------------
	# Deactivating and Removal
	# ------------------------------------------------------------------
	add_action('deactivate_simple-forum/sf-control.php', 'sfg_remove_data');
	# ------------------------------------------------------------------

	# ------------------------------------------------------------------
	# cron hooks
	# ------------------------------------------------------------------
	add_action('spf_cron_pm', 'spf_remove_pms');  # auto removal of pms
	add_action('spf_cron_user', 'spf_remove_users');  # auto removal of spam users
	add_action('spf_cron_sitemap', 'spf_generate_sitemap');  # daily sitemap building
	# ------------------------------------------------------------------

	# ------------------------------------------------------------------
	# google sitemap generator
	# ------------------------------------------------------------------
	add_action('sm_buildmap','sf_build_sitemap');
	# ------------------------------------------------------------------

	# ------------------------------------------------------------------
	# ahah request handler
	# ------------------------------------------------------------------
	add_action('parse_request','sf_ahah_handler');
	# ------------------------------------------------------------------

	# ------------------------------------------------------------------
	# WP Avatar replacement
	# ------------------------------------------------------------------
	$sfavatars = array();
	$sfavatars = sf_get_option('sfavatars');
    if (!empty($sfavatars['sfavatarreplace'])) {
		add_filter('get_avatar', 'sf_avatar', 900, 3); # low priority - let everyone else settle out
	}
	# ------------------------------------------------------------------
}

# ----------------------------------------------------------
# SITEWIDE GLOBAL WP Hooks - all page loads
# NOTE: Called AFTER determination of whether forum view
# ----------------------------------------------------------
function sf_setup_sitewide_late_hooks()
{
	global $ISFORUM;

	$sfwplistpages = sf_get_option('sfwplistpages');
	if ($ISFORUM && $sfwplistpages) {
		add_filter('wp_list_pages', 'sf_wp_list_pages');
		add_filter('wp_nav_menu', 'sf_wp_list_pages');
	}

	# ------------------------------------------------------------------
	# Syntax Highlighting
	# ------------------------------------------------------------------
	$sfsyntax = sf_get_option('sfsyntax');
	if($sfsyntax['sfsyntaxforum'] == true && $ISFORUM == true) {
		add_filter('the_content', 'sf_filter_syntax_display', 0);
	}
	if($sfsyntax['sfsyntaxblog'] == true && $ISFORUM == false) {
		add_filter('the_content', 'sf_filter_syntax_display', 0);
		add_filter('the_excerpt', 'sf_filter_syntax_display', 0);
		add_filter('comment_text', 'sf_filter_syntax_display', 0);
	}
	# ------------------------------------------------------------------

	# ------------------------------------------------------------------
	# get_permalink() filter for forum pages
	# ------------------------------------------------------------------
	if ($ISFORUM)
	{
		add_filter('page_link', 'sf_get_permalink', 10, 3);
	}
	# ------------------------------------------------------------------
}

# ----------------------------------------------------------
# ADMIN WP Hooks - all page loads
# ----------------------------------------------------------
function sf_setup_admin_hooks()
{
	# ------------------------------------------------------------------
	# Build admin header and footer
	# ------------------------------------------------------------------
	add_action('admin_head', 'sfa_admin_header', 1);
	add_action('in_admin_footer', 'sfa_admin_footer');
	# ------------------------------------------------------------------

	# ------------------------------------------------------------------
	# Plugin page updating and links
	# ------------------------------------------------------------------
	add_action('after_plugin_row', 						'sfa_check_plugin_version' );
	add_filter('network_admin_plugin_action_links', 	'sf_add_plugin_action', 10, 2);
	add_filter('plugin_action_links', 					'sf_add_plugin_action', 10, 2);
	add_action('admin_head', 							'sf_check_removal');
	# ------------------------------------------------------------------

	# ------------------------------------------------------------------
	# Drop down help panel
	# ------------------------------------------------------------------
	add_filter('contextual_help', 'sf_add_slider_help');
	# ----------------------------------------------------------

	# ------------------------------------------------------------------
	# Dashboard notifications
	# ------------------------------------------------------------------
	add_action('wp_dashboard_setup', 'sf_dashboard_setup', 1 );
	# ------------------------------------------------------------------
}

# ----------------------------------------------------------
# FORUM WP Hooks
# ----------------------------------------------------------
function sf_setup_forum_hooks()
{
	global $ISFORUM;

	$sffilters = array();
	$sffilters = sf_get_option('sffilters');
	$sfimage = array();
	$sfimage = sf_get_option('sfimage');

	# ------------------------------------------------------------------
	# Main front end footer
	# ------------------------------------------------------------------
	add_action('wp_footer', 'sf_setup_footer');
	# ------------------------------------------------------------------

	# ------------------------------------------------------------------
	# Main front end page content
	# ------------------------------------------------------------------
	add_filter('the_content', 'sf_setup_forum', 1);
	# ------------------------------------------------------------------

	# ------------------------------------------------------------------
	# Page Content Level Display Filters
	# ------------------------------------------------------------------
	remove_filter('the_content', 'wptexturize');
	add_filter('the_content', 'sf_wptexturize');
	# ------------------------------------------------------------------

	# ------------------------------------------------------------------
	# Shortcodes
	# ------------------------------------------------------------------
	add_shortcode('spoiler', 'sf_filter_display_spoiler');
	# ------------------------------------------------------------------

	# ------------------------------------------------------------------
	# Remove WP canonical url for forum pages since it would always
    # point to the single wp page
	# ------------------------------------------------------------------
    remove_action('wp_head', 'rel_canonical');

	# ------------------------------------------------------------------
	# WP Page Title
	# ------------------------------------------------------------------
	if ($ISFORUM) {
		add_action('loop_start', 'sf_title_hook');
		add_filter('the_title', 'sf_setup_page_title');

		# keep wp capital P stuff from making menus show full page title
		remove_filter('the_content', 'capital_P_dangit', 11);
		remove_filter('the_title', 'capital_P_dangit', 11);
		remove_filter('comment_text', 'capital_P_dangit', 31);
	}
	# ------------------------------------------------------------------
}

?>