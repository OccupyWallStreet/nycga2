<?php
/**
 * Handles all Admin access functionality.
 */
class Wdpv_AdminPages {
	var $model;
	var $data;

	function __construct () {
		$this->model = new Wdpv_Model;
		$this->data = new Wdpv_Options;
	}
	function Wdpv_AdminPages () { $this->__construct(); }

	/**
	 * Main entry point.
	 *
	 * @static
	 */
	function serve () {
		$me = new Wdpv_AdminPages;
		$me->add_hooks();
	}

	function create_site_admin_menu_entry () {
		if (@$_POST && isset($_POST['option_page']) && 'wdpv' == @$_POST['option_page']) {
			if (isset($_POST['wdpv'])) {
				$this->data->set_options($_POST['wdpv']);
			}
			if (WP_NETWORK_ADMIN && $this->data->get_option('disable_siteadmin_changes')) {
				// Flush per-blog settings
				$blogs = $this->model->get_blog_ids();
				foreach ($blogs as $blog) delete_blog_option($blog['blog_id'], "wdpv");
			}
			$goback = add_query_arg('settings-updated', 'true',  wp_get_referer());
			wp_redirect($goback);
			die;
		}
		add_submenu_page('settings.php', 'Post Voting', 'Post Voting', 'manage_network_options', 'wdpv', array($this, 'create_admin_page'));
		add_dashboard_page('Voting Stats', 'Voting Stats', 'manage_network_options', 'wdpv_stats', array($this, 'create_stats_page'));
	}

	function register_settings () {
		$form = new Wdpv_AdminFormRenderer;

		register_setting('wdpv', 'wdpv');
		add_settings_section('wdpv_voting', __('Voting settings', 'wdpv'), create_function('', ''), 'wdpv_options_page');
		add_settings_field('wdpv_allow_voting', __('Allow post voting', 'wdpv'), array($form, 'create_allow_voting_box'), 'wdpv_options_page', 'wdpv_voting');
		add_settings_field('wdpv_allow_visitor_voting', __('Allow voting for unregistered users', 'wdpv'), array($form, 'create_allow_visitor_voting_box'), 'wdpv_options_page', 'wdpv_voting');
		add_settings_field('wdpv_use_ip_check_link', __('Use IP check', 'wdpv'), array($form, 'create_use_ip_check_box'), 'wdpv_options_page', 'wdpv_voting');
		add_settings_field('wdpv_show_login_link', __('Show login link for visitors', 'wdpv'), array($form, 'create_show_login_link_box'), 'wdpv_options_page', 'wdpv_voting');
		add_settings_field('wdpv_skip_post_types', __('Do <strong>NOT</strong> show voting for these types', 'wdpv'), array($form, 'create_skip_post_types_box'), 'wdpv_options_page', 'wdpv_voting');
		add_settings_field('wdpv_voting_position', __('Voting box position', 'wdpv'), array($form, 'create_voting_position_box'), 'wdpv_options_page', 'wdpv_voting');
		add_settings_field('wdpv_voting_appearance', __('Appearance', 'wdpv'), array($form, 'create_voting_appearance_box'), 'wdpv_options_page', 'wdpv_voting');
		add_settings_field('wdpv_voting_positive', __('Prevent negative voting', 'wdpv'), array($form, 'create_voting_positive_box'), 'wdpv_options_page', 'wdpv_voting');
		add_settings_field('wdpv_front_page_voting', __('Voting on Front Page', 'wdpv'), array($form, 'create_front_page_voting_box'), 'wdpv_options_page', 'wdpv_voting');
		if (WP_NETWORK_ADMIN) {
			add_settings_field('wdpv_disable_siteadmin_changes', __('Prevent Site Admins from making changes?', 'wdpv'), array($form, 'create_disable_siteadmin_changes_box'), 'wdpv_options_page', 'wdpv_voting');
		}

		// BuddyPress integration
		if (defined('BP_VERSION')) {
			add_settings_section('wdpv_bp', __('BuddyPress integration', 'wdpv'), create_function('', ''), 'wdpv_options_page');
			add_settings_field('wdpv_bp_publish_activity', __('Publish votes to activity stream', 'wdpv'), array($form, 'create_bp_publish_activity_box'), 'wdpv_options_page', 'wdpv_bp');
			add_settings_field('wdpv_bp_profile_votes', __('Show recent votes on user profile page', 'wdpv'), array($form, 'create_bp_profile_votes_box'), 'wdpv_options_page', 'wdpv_bp');
		}

		if (!is_multisite() || (is_multisite() && WP_NETWORK_ADMIN)) { // On multisite, plugins are available only on network admin pages
			add_settings_section('wdpv_plugins', __('Post Voting add-ons', 'wdpv'), create_function('', ''), 'wdpv_options_page');
			add_settings_field('wdpv_plugins_all_plugins', __('All add-ons', 'wdpv'), array($form, 'create_plugins_box'), 'wdpv_options_page', 'wdpv_plugins');
		}

		do_action('wdpv-options-plugins_options', $form);
	}

	function create_blog_admin_menu_entry () {
		$settings_perms = $this->data->get_option('disable_siteadmin_changes') ? 'manage_network_options' : 'manage_options';
		add_options_page('Post Voting', 'Post Voting', $settings_perms, 'wdpv', array($this, 'create_admin_page'));
		add_dashboard_page('Voting Stats', 'Voting Stats', 'manage_options', 'wdpv_stats', array($this, 'create_stats_page'));
	}

	/**
	 * Creates Admin menu page.
	 *
	 * @access private
	 */
	function create_admin_page () {
		include(WDPV_PLUGIN_BASE_DIR . '/lib/forms/plugin_settings.php');
	}

	/**
	 * Creates Admin Stats page.
	 *
	 * @access private
	 */
	function create_stats_page () {
		$limit = 2000;
		$overall = WP_NETWORK_ADMIN ? $this->model->get_popular_on_network($limit) : $this->model->get_popular_on_current_site($limit);
		include(WDPV_PLUGIN_BASE_DIR . '/lib/forms/plugin_stats.php');
	}

	function clear_orphaned_data ($post_id) {
		$this->model->remove_votes_for_post($post_id);
	}

	function json_record_vote () {
		$status = false;
		if (isset($_POST['wdpv_vote']) && isset($_POST['post_id'])) {
			$vote = (int)$_POST['wdpv_vote'];
			$post_id = (int)$_POST['post_id'];
			$blog_id = (int)@$_POST['blog_id'];
			$status = $this->model->update_post_votes($blog_id, $post_id, $vote);
		}
		header('Content-type: application/json');
		echo json_encode(array(
			'status' => (int)$status,
		));
		exit();
	}

	function json_vote_results () {
		$data = false;
		if (isset($_POST['post_id'])) {
			$data = $this->model->get_votes_total((int)$_POST['post_id'], false, (int)@$_POST['blog_id']);
		}
		header('Content-type: application/json');
		echo json_encode(array(
			'status' => ($data ? 1 : 0),
			'data' => (int)$data,
		));
		exit();
	}

	function bp_record_vote_activity ($site_id, $blog_id, $post_id, $vote) {
		if (!bp_loggedin_user_id()) return false;

		$username = bp_get_loggedin_user_fullname();
		$username = $username ? $username : bp_get_loggedin_user_username();
		if (!$username) return false;

		$user_link = bp_get_loggedin_user_link();
		$link = get_blog_permalink($blog_id, $post_id);

		$post = get_blog_post($blog_id, $post_id);
		$title = $post->post_title;

		$args = array (
			'action' => sprintf(
				__('<a href="%s">%s</a> voted on <a href="%s">%s</a>', 'wdpv'),
				$user_link, $username, $link, $title
			),
			'component' => 'wdpv_post_vote',
			'type' => 'wdpv_post_vote',
			'item_id' => $blog_id,
			'secondary_item_id' => $post_id,
			'hide_sitewide' => $this->data->get_option('bp_publish_activity_local'),
		);
		$res = bp_activity_add($args);
		return $res;
	}

	function json_activate_plugin () {
		$status = Wdpv_PluginsHandler::activate_plugin($_POST['plugin']);
		echo json_encode(array(
			'status' => $status ? 1 : 0,
		));
		exit();
	}

	function json_deactivate_plugin () {
		$status = Wdpv_PluginsHandler::deactivate_plugin($_POST['plugin']);
		echo json_encode(array(
			'status' => $status ? 1 : 0,
		));
		exit();
	}

	function add_hooks () {
		// Step0: Register options and menu
		add_action('admin_init', array($this, 'register_settings'));
		if (WP_NETWORK_ADMIN) {
			add_action('network_admin_menu', array($this, 'create_site_admin_menu_entry'));
		} else {
			add_action('admin_menu', array($this, 'create_blog_admin_menu_entry'));
		}

		// Step1: add AJAX hooks
		if ($this->data->get_option('allow_voting')) {
			// Step1a: add AJAX hooks for visitors
			if ($this->data->get_option('allow_visitor_voting')) {
				add_action('wp_ajax_nopriv_wdpv_record_vote', array($this, 'json_record_vote'));
				add_action('wp_ajax_nopriv_wdpv_vote_results', array($this, 'json_vote_results'));
			}
			// Step1b: add AJAX hooks for registered users
			add_action('wp_ajax_wdpv_record_vote', array($this, 'json_record_vote'));
			add_action('wp_ajax_wdpv_vote_results', array($this, 'json_vote_results'));
		}

		// Cleanup
		add_action('deleted_post', array($this, 'clear_orphaned_data'));

		// Optional hooks for BuddyPress
		if (defined('BP_VERSION')) {
			if ($this->data->get_option('bp_profile_votes')) {
				//add_action('bp_before_profile_content', array($this, 'bp_show_recent_votes'));
				add_action('wdpv_voted', array($this, 'bp_record_vote_activity'), 10, 4);
			}
		}

		// AJAX plugin handlers
		add_action('wp_ajax_wdpv_activate_plugin', array($this, 'json_activate_plugin'));
		add_action('wp_ajax_wdpv_deactivate_plugin', array($this, 'json_deactivate_plugin'));
	}
}