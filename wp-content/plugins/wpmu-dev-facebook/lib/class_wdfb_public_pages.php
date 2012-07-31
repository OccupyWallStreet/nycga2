<?php
class Wdfb_PublicPages {

	var $data;
	var $replacer;
	var $fb;

	function __construct () {
		$this->data =& Wdfb_OptionsRegistry::get_instance();
		$this->model = new Wdfb_Model;
		$this->replacer = new Wdfb_MarkerReplacer;
	}

	function Wdfb_PublicPages () {
		$this->__construct();
	}

	/**
	 * Main entry point.
	 *
	 * @static
	 */
	function serve () {
		$me = new Wdfb_PublicPages;
		$me->add_hooks();
	}

	function js_load_scripts () {
		wp_enqueue_script('jquery');
		$locale = wdfb_get_locale();
		wp_enqueue_script('facebook-all',  WDFB_PROTOCOL  . 'connect.facebook.net/' . $locale . '/all.js');
	}

	function js_inject_fb_login_script () {
		echo '<script type="text/javascript" src="' . WDFB_PLUGIN_URL . '/js/wdfb_facebook_login.js"></script>';
	}
	function js_setup_ajaxurl () {
		printf(
			'<script type="text/javascript">var _wdfb_ajaxurl="%s";var _wdfb_root_url="%s";</script>',
			admin_url('admin-ajax.php'),
			WDFB_PLUGIN_URL
		);
	}
	function css_load_styles () {
		wp_enqueue_style('wdfb_style', WDFB_PLUGIN_URL . '/css/wdfb.css');
	}

	/**
	 * Inject Facebook button into post content.
	 * This is triggered only for automatic injection.
	 * Adds shortcode in proper place, and lets replacer do its job later on.
	 */
	function inject_facebook_button ($body) {
		if (
			(is_home() && !$this->data->get_option('wdfb_button', 'show_on_front_page'))
			||
			(!is_home() && !is_singular())
		) return $body;

		$position = $this->data->get_option('wdfb_button', 'button_position');
		if ('top' == $position || 'both' == $position) {
			$body = $this->replacer->get_button_tag('like_button') . " " . $body;
		}
		if ('bottom' == $position || 'both' == $position) {
			$body .= " " . $this->replacer->get_button_tag('like_button');
		}
		return $body;
	}

	/**
	 * Inject OpenGraph info in the HEAD
	 */
	function inject_opengraph_info () {
		$title = $url = $site_name = $description = $id = $image = false;
		if (is_singular()) {
			global $post;
			$id = $post->ID;
			$title = $post->post_title;
			$url = get_permalink($id);
			$site_name = get_option('blogname');
			$content = $post->post_excerpt ? $post->post_excerpt : strip_shortcodes($post->post_content);
			$text = htmlspecialchars(wp_strip_all_tags($content), ENT_QUOTES);
			if (strlen($text) > 250) $description = preg_replace('/(.{0,247}).*/um', '$1', preg_replace('/\r|\n/', ' ', $text)) . '...'; //substr($text, 0, 250) . "...";
			else $description = $text;
		} else {
			$title = get_option('blogname');
			$url = home_url('/');
			$site_name = get_option('blogname');
			$description = get_option('blogdescription');
		}
		$image = wdfb_get_og_image($id);

		// App ID
		if (!defined('WDFB_APP_ID_OG_SET')) {
			$app_id = trim($this->data->get_option('wdfb_api', 'app_key'));
			if ($app_id) {
				echo "<meta property='fb:app_id' content='{$app_id}' />\n";
				define('WDFB_APP_ID_OG_SET', true);
			}
		}

		// Type
		$type = false;
		if ($this->data->get_option('wdfb_opengraph', 'og_custom_type')) {
			if (!is_singular()) {
				$type = $this->data->get_option('wdfb_opengraph', 'og_custom_type_not_singular');
				$type = $type ? $type : 'website';
			} else {
				$type = $this->data->get_option('wdfb_opengraph', 'og_custom_type_singular');
				$type = $type ? $type : 'article';
			}
			if (is_home() || is_front_page()) {
				$type = $this->data->get_option('wdfb_opengraph', 'og_custom_type_front_page');
				$type = $type ? $type : 'website';
			}
		}
		$type = $type ? $type : (is_singular() ? 'article' : 'website');
		$type = apply_filters('wdfb-opengraph-type', $type);
		echo "<meta property='og:type' content='{$type}' />\n";

		// Defaults
		$title = apply_filters('wdfb-opengraph-title', $title);
		$url = apply_filters('wdfb-opengraph-url', $url);
		$site_name = apply_filters('wdfb-opengraph-site_name', $site_name);
		$description = apply_filters('wdfb-opengraph-description', $description);

		if ($title) echo "<meta property='og:title' content='{$title}' />\n";
		if ($url) echo "<meta property='og:url' content='{$url}' />\n";
		if ($site_name) echo "<meta property='og:site_name' content='{$site_name}' />\n";
		if ($description) echo "<meta property='og:description' content='{$description}' />\n";
		if ($image) echo "<meta property='og:image' content='{$image}' />\n";

		$extras = $this->data->get_option('wdfb_opengraph', 'og_extra_headers');
		$extras = $extras ? $extras : array();
		foreach ($extras as $extra) {
			$name = apply_filters('wdfb-opengraph-extra_headers-name', @$extra['name']);
			$value = apply_filters('wdfb-opengraph-extra_headers-value', @$extra['value'], @$extra['name']);
			if (!$name || !$value) continue;
			echo "<meta property='{$name}' content='{$value}' />\n";
		}
	}

	function inject_fb_init_js () {
		echo "<script type='text/javascript'>
         FB.init({
            appId: '" . trim($this->data->get_option('wdfb_api', 'app_key')) . "',
            status: true,
            cookie: true,
            xfbml: true,
            oauth: true
         });
      </script>";
	}

	/**
	 * Injects Facebook root div needed for XFBML near page footer.
	 */
	function inject_fb_root_div () {
		echo "<div id='fb-root'></div>";
	}

	function inject_fb_login () {
		echo '<p class="wdfb_login_button"><fb:login-button scope="' . Wdfb_Permissions::get_permissions() . '" redirect-url="' . wdfb_get_login_redirect(true) . '"  onlogin="_wdfb_notifyAndRedirect();">' . __("Login with Facebook", 'wdfb') . '</fb:login-button></p>';
	}

	function inject_fb_login_for_bp () {
		echo '<p class="wdfb_login_button"><fb:login-button scope="' . Wdfb_Permissions::get_permissions() . '" redirect-url="' . wdfb_get_login_redirect() . '"  onlogin="_wdfb_notifyAndRedirect();">' . __("Login with Facebook", 'wdfb') . '</fb:login-button></p>';
	}

	function inject_fb_comments_admin_og () {
		if (defined('WDFB_APP_ID_OG_SET')) return false;
		$app_id = trim($this->data->get_option('wdfb_api', 'app_key'));
		if (!$app_id) return false;
		echo "<meta property='fb:app_id' content='{$app_id}' />\n";
		define('WDFB_APP_ID_OG_SET', true);
	}

	function inject_fb_comments ($defaults) {
		if (!comments_open() && !$this->data->get_option('wdfb_comments', 'override_wp_comments_settings')) return $defaults;

		$link = get_permalink();
		$xid = rawurlencode($link);

		$width = (int)$this->data->get_option('wdfb_comments', 'fb_comments_width');
		$width = $width ? $width : '550';

		$num_posts = (int)$this->data->get_option('wdfb_comments', 'fb_comments_number');

		$reverse = $this->data->get_option('wdfb_comments', 'fb_comments_reverse') ? 'true' : 'false';

		echo "<fb:comments href='{$link}' " .
			"xid='{$xid}' " .
			"num_posts='{$num_posts}' " .
			"width='{$width}px' " .
			"reverse='{$reverse}' " .
			"publish_feed='true'></fb:comments>";
		return $defaults;
	}

	function get_commenter_avatar ($old, $comment, $size) {
		if (!is_object($comment)) return $old;
		$meta = get_comment_meta($comment->comment_ID, 'wdfb_comment', true);
		if (!$meta) return $old;

		$fb_size_map = false;
		if ($size <= 50) $fb_size_map = '?type=small';
		if ($size > 50 && $size <= 100) $fb_size_map = '?type=normal';
		if ($size > 100) $fb_size_map = '?type=large';

		return '<img src="' . WDFB_PROTOCOL  . 'graph.facebook.com/' . $meta['fb_author_id'] . '/picture' . $fb_size_map . '" class="avatar avatar-' . $size . ' photo" height="' . $size . '" width="' . $size . '" />';
	}

	function get_fb_avatar ($avatar, $id_or_email, $size=false) {
		$fb_uid = false;
		$wp_uid = false;
		if (is_object($id_or_email)) {
			if (isset($id_or_email->comment_author_email)) $id_or_email = $id_or_email->comment_author_email;
			else return $avatar;
		}

		if (is_numeric($id_or_email)) {
			$wp_uid = (int)$id_or_email;
		} else if (is_email($id_or_email)) {
			$user = get_user_by('email', $id_or_email);
			if ($user) $wp_uid = $user->ID;
		} else return $avatar;
		if (!$wp_uid) return $avatar;

		$fb_uid = $this->model->get_fb_user_from_wp($wp_uid);
		if (!$fb_uid) return $avatar;

		$img_size = $size ? "width='{$size}px'" : '';
		$fb_size_map = false;
		if ($size <= 50) $fb_size_map = '?type=small';
		if ($size > 50 && $size <= 100) $fb_size_map = '?type=normal';
		if ($size > 100) $fb_size_map = '?type=large';

		return "<img class='avatar' src='" . WDFB_PROTOCOL  . "graph.facebook.com/{$fb_uid}/picture{$fb_size_map}' {$img_size} />";
	}

	function inject_optional_facebook_registration_button () {
		$url = add_query_arg('fb_registration_page', 1);
		echo '<p><a class="wdfb_register_button" href="' . $url . '"><span>' . __('Register with Facebook', 'wdfb') . '</span></a></p>';
	}

	function process_facebook_registration () {
		// Should we even be here?
		if ($this->data->get_option('wdfb_connect', 'force_facebook_registration')) {
			global $pagenow;
			if ('wp-signup.php' == $pagenow) $_GET['fb_registration_page'] = 1;
			if ('wp-login.php' == $pagenow && isset($_GET['action']) && 'register' == $_GET['action']) $_GET['fb_registration_page'] = 1;

			if (defined('BP_VERSION')) { // BuddyPress :/
				global $bp;
				if ('register' == $bp->current_component) $_GET['fb_registration_page'] = 1;
			}
		}
		if (!isset($_GET['fb_registration_page']) && !isset($_GET['fb_register'])) return false;

		// Are registrations allowed?
		$wp_grant_blog = false;
		if (is_multisite()) {
			$reg = get_site_option('registration');
			if ('all' == $reg) $wp_grant_blog = true;
			else if ('user' != $reg) return false;
		} else {
			if (!(int)get_option('users_can_register')) return false;
		}

		// We're here, so registration is allowed
		$registration_success = false;
		$errors = array();
		// Process registration data
		if (isset($_GET['fb_register'])) {
			list($encoded_sig, $payload) = explode('.', $_REQUEST['signed_request'], 2);
			$data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);

			// We're good here
			if ($data['registration']) {
				$user_id = $this->model->register_fb_user();
				if ($user_id && $wp_grant_blog) {
					$new_blog_title = '';
					$new_blog_url = '';
					remove_filter('wpmu_validate_blog_signup', 'signup_nonce_check');

					// Set up proper blog name
					$blog_domain = preg_replace('/[^a-z0-9]/', '', strtolower($data['registration']['blog_domain']));
					// All numbers? Fix that
					if (preg_match('/^[0-9]$/', $blog_domain)) {
						$letters = shuffle(range('a', 'z'));
						$blog_domain .= $letters[0];
					}
					// Set up proper title
					$blog_title = $data['registration']['blog_title'];
					$blog_title = $blog_title ? $blog_title : __("My new blog", 'wdfb');

					$result = wpmu_validate_blog_signup($blog_domain, $blog_title);
					$iteration = 0;
					// Blog domain failed, try making it unique
					while ($result['errors']->get_error_code()) {
						if ($iteration > 10) break; // We should really gtfo
						$blog_domain .= rand();
						$result = wpmu_validate_blog_signup($blog_domain, $blog_title);
						$iteration++;
					}

					if (!$result['errors']->get_error_code()) {
						global $current_site;
						$blog_meta = array('public' => 1);
						$blog_id = wpmu_create_blog($result['domain'], $result['path'], $result['blog_title'], $user_id, $blog_meta, $current_site->id);
						$new_blog_title = $result['blog_title'];
						$new_blog_url = get_blog_option($blog_id, 'siteurl');
						$registration_success = true;
					} else {
						// Remove user
						$this->model->delete_wp_user($user_id);
						$errors = array_merge($errors, array_values($result['errors']->errors));
					}
				} else if ($user_id) {
					$registration_success = true;
				} else {
					$msg = Wdfb_ErrorRegistry::get_last_error_message();
					if ($msg) $errors[] = $msg;
					$errors[] = __('Could not register such user', 'wdfb');
				}
			}
		}

		// Allow registration page templating
		// By KFUK-KFUM
		// Thank you so much!
		$page = (isset($_GET['fb_register']) && $registration_success)
			//? WDFB_PLUGIN_BASE_DIR . '/lib/forms/registration_page_success.php'
			? $this->get_template_page('registration_page_success.php')
			//: WDFB_PLUGIN_BASE_DIR . '/lib/forms/registration_page.php'
			: $this->get_template_page('registration_page.php')
		;
		require_once $page;
		exit();
	}

	/**
	 * Allows registration page templating.
	 * Method by KFUK-KFUM
	 * Thank you so much!
	 */
	function get_template_page ($template) {
		$theme_file = locate_template(array($template));
		if ($theme_file) {
			// Look for the template file in the theme directory
			// Anyone who wants to theme the registration page can copy
			// the template file to their theme directory while keeping
			// the file name intact
			$file = $theme_file;
		} else {
			// If none was found in the current theme, use the default plugin template
			$file = WDFB_PLUGIN_BASE_DIR . '/lib/forms/' . $template;
		}
		return $file;
	}

	function publish_post_on_facebook ($id) {
		if (!$id) return false;

		$post_id = $id;
		if ($rev = wp_is_post_revision($post_id)) $post_id = $rev;

		// Should we even try?
		if (!$this->data->get_option('wdfb_autopost', 'allow_autopost')) return false;
		if (!$this->data->get_option('wdfb_autopost', 'allow_frontend_autopost')) return false;

		$post = get_post($post_id);
		if ('publish' != $post->post_status) return false; // Draft, auto-save or something else we don't want

		$is_published = get_post_meta($post_id, 'wdfb_published_on_fb', true);
		if ($is_published) return true; // Already posted and no manual override, nothing to do

		$post_type = $post->post_type;
		$post_title = $post->post_title;
		$post_content = strip_shortcodes($post->post_content);

		$post_as = $this->data->get_option('wdfb_autopost', "type_{$post_type}_fb_type");
		$post_to = $this->data->get_option('wdfb_autopost', "type_{$post_type}_fb_user");
		if (!$post_to) return false; // Don't know where to post, bail

		$as_page = false;
		if ($post_to != $this->model->get_current_user_fb_id()) {
			$as_page = isset($_POST['wdfb_post_as_page']) ? $_POST['wdfb_post_as_page'] : $this->data->get_option('wdfb_autopost', 'post_as_page');
		}

		if (!$post_as) return true; // Skip this type

		switch ($post_as) {
			case "notes":
				$send = array (
					'subject' => $post_title,
					'message' => $post_content,
				);
				break;
			case "events":
				$time = time();
				$start_time = apply_filters('wdfb-autopost-events-start_time', $time, $post);
				$end_time = apply_filters('wdfb-autopost-events-end_time', $time+86400, $post);
				$location = apply_filters('wdfb-autopost-events-location', false, $post);
				$send = array(
					'name' => $post_title,
					'description' => $post_content,
					'start_time' => $start_time,
					'end_time' => $end_time,
				);
				if ($location) {
					$send['location'] = $location;
				}
				break;
			case "feed":
			default:
				$use_shortlink = $this->data->get_option('wdfb_autopost', "type_{$post_type}_use_shortlink");
				$permalink = $use_shortlink ? wp_get_shortlink($post_id) : get_permalink($post_id);
				$permalink = $permalink ? $permalink : get_permalink($post_id);
				$picture = wdfb_get_og_image($post_id);
				$send = array(
					'caption' => substr($post_content, 0, 999),
					'message' => $post_title,
					'link' => $permalink,
					'name' => $post->post_title,
					'description' => get_option('blogdescription'),
				);
				if ($picture) $send['picture'] = $picture;
				break;
		}
		$res = $this->model->post_on_facebook($post_as, $post_to, $send, $as_page);
		if ($res) update_post_meta($post_id, 'wdfb_published_on_fb', 1);
	}

	/**
	 * Hooks to appropriate places and adds stuff as needed.
	 *
	 * @access private
	 */
	function add_hooks () {
		// Step1a: Add script and style dependencies
		add_action('wp_print_scripts', array($this, 'js_load_scripts'));
		add_action('wp_print_styles', array($this, 'css_load_styles'));
		add_action('wp_head', array($this, 'js_setup_ajaxurl'));

		add_action('get_footer', array($this, 'inject_fb_root_div'));
		add_action('get_footer', array($this, 'inject_fb_init_js'));

		// Automatic Facebook button
		if ('manual' != $this->data->get_option('wdfb_button', 'button_position')) {
			add_filter('the_content', array($this, 'inject_facebook_button'), 10);
		}

		// OpenGraph
		if ($this->data->get_option('wdfb_opengraph', 'use_opengraph')) {
			add_action('wp_head', array($this, 'inject_opengraph_info'));
		}

		// Connect
		if ($this->data->get_option('wdfb_connect', 'allow_facebook_registration')) {
			add_filter('get_avatar', array($this, 'get_fb_avatar'), 10, 3);

			add_action('login_head', array($this, 'js_inject_fb_login_script'));
			add_action('login_head', array($this, 'js_setup_ajaxurl'));
			add_action('login_form', array($this, 'inject_fb_login'));
			add_action('login_footer', array($this, 'inject_fb_root_div'));
			add_action('login_footer', array($this, 'inject_fb_init_js'));

			// BuddyPress
			if (defined('BP_VERSION')) {
				add_action('bp_before_profile_edit_content', 'wdfb_dashboard_profile_widget');
				add_action('bp_before_sidebar_login_form', array($this, 'inject_fb_login_for_bp'));
				add_action('wp_head', array($this, 'js_inject_fb_login_script'));

				// Have to kill BuddyPress redirection, or our registration doesn't work
				remove_action('wp', 'bp_core_wpsignup_redirect');
				remove_action('init', 'bp_core_wpsignup_redirect');
				add_action('bp_include', create_function('', "remove_action('bp_init', 'bp_core_wpsignup_redirect');"), 99); // Untangle for BP 1.5
			}

			// New login/register
			// First, do optionals
			if (is_multisite()) add_action('before_signup_form', array($this, 'inject_optional_facebook_registration_button'));
			else if (isset($_GET['action']) && 'register' == $_GET['action']) {
				add_action('login_head', create_function('', 'echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"' . WDFB_PLUGIN_URL . '/css/wdfb.css\" />";'));
				// Better registration button placement for single site
				// Fix by riyaku
				// Thank you so much!
				//add_action('login_message', array($this, 'inject_optional_facebook_registration_button'));
				add_action('register_form', array($this, 'inject_optional_facebook_registration_button'));
			}

			// BuddyPress
			add_filter('bp_before_register_page', array($this, 'inject_optional_facebook_registration_button')); // BuddyPress

			// Jack the signup
			add_action('init', array($this, 'process_facebook_registration'), 20);
		}

		// Comments
		if ($this->data->get_option('wdfb_comments', 'use_fb_comments')) {
			$hook = $this->data->get_option('wdfb_comments', 'fb_comments_custom_hook');
			add_action('wp_head', array($this, 'inject_fb_comments_admin_og'));
			if (!$hook) {
				add_filter('comment_form_defaults', array($this, 'inject_fb_comments'));
				add_filter('bp_before_blog_comment_list', array($this, 'inject_fb_comments')); // BuddyPress :/
			} else {
				add_action($hook, array($this, 'inject_fb_comments'));
			}
		}
		add_filter('get_avatar', array($this, 'get_commenter_avatar'), 10, 3);

		// Autopost for front pages
		if ($this->data->get_option('wdfb_autopost', 'allow_autopost') && $this->data->get_option('wdfb_autopost', 'allow_frontend_autopost')) {
			add_action('save_post', array($this, 'publish_post_on_facebook'));
		}

		$rpl = $this->replacer->register();
	}
}