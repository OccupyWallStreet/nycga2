<?php
/**
 * Handles all Admin access functionality.
 */
class Wdqs_AdminPages {

	//var $data;
	var $_link_type = 'status';
	var $_link_title = '';

	function Wdqs_AdminPages () { $this->__construct(); }

	function __construct () {
		$this->data = new Wdqs_Options;
	}

	/**
	 * Main entry point.
	 *
	 * @static
	 */
	function serve () {
		$me = new Wdqs_AdminPages;
		$me->add_hooks();
	}

	/**
	 * Remote page retrieving routine.
	 *
	 * @param string Remote URL
	 * @return mixed Remote page as string, or (bool)false on failure
	 * @access private
	 */
	function get_page_contents ($url) {
		$response = wp_remote_get($url);
		if (is_wp_error($response)) return false;
		return $response['body'];
	}

	function js_load_scripts () {
		if (!current_user_can('publish_posts')) return false;
		if (!$this->data->get('show_on_dashboard')) return false;
		if (defined('WP_NETWORK_ADMIN') && WP_NETWORK_ADMIN) return false;
		wp_enqueue_script('jquery');
		wp_enqueue_script('thickbox');
		wp_enqueue_script('media-upload');
		wp_enqueue_script('wdqs_widget', WDQS_PLUGIN_URL . '/js/widget.js');
		wp_localize_script('wdqs_widget', 'l10nWdqs', array(
			'no_thumbnail' => __('No thumbnail', 'wdqs'),
			'of' => __('of', 'wdqs'),
			'images_found' => __('images found', 'wdqs'),
			'use_default_title' => __('Use default title', 'wdqs'),
			'use_this_title' => __('Use this title', 'wdqs'),
			'post_title' => __('Post title', 'wdqs'),
			'height' => __('Height', 'wdqs'),
			'width' => __('Width', 'wdqs'),
			'leave_empty_for_defaults' => __('Leave these boxes empty for defaults', 'wdqs'),
		));
		echo '<script type="text/javascript">var _wdqs_ajaxurl="' . admin_url('admin-ajax.php') . '";</script>';
	}

	function css_load_styles () {
		if (defined('WP_NETWORK_ADMIN') && WP_NETWORK_ADMIN) return false;
		wp_enqueue_style('wdqs', WDQS_PLUGIN_URL . '/css/wdqs.css');
		wp_enqueue_style('thickbox');
		wp_enqueue_style('wdqs_widget', WDQS_PLUGIN_URL . '/css/widget.css');
	}

	function register_settings () {
		$form = new Wdqs_AdminFormRenderer;

		register_setting('wdqs', 'wdqs');
		add_settings_section('wdqs_settings', __('Status settings', 'wdqs'), create_function('', ''), 'wdqs_options_page');
		add_settings_field('wdqs_public', __('Show on public pages', 'wdqs'), array($form, 'create_show_on_public_pages_box'), 'wdqs_options_page', 'wdqs_settings');
		add_settings_field('wdqs_front', __('Show on front page only', 'wdqs'), array($form, 'create_show_on_front_page_box'), 'wdqs_options_page', 'wdqs_settings');
		add_settings_field('wdqs_back', __('Show on Dashboard', 'wdqs'), array($form, 'create_show_on_dashboard_box'), 'wdqs_options_page', 'wdqs_settings');

		add_settings_section('wdqs_post', __('Status post settings', 'wdqs'), create_function('', ''), 'wdqs_options_page');
		add_settings_field('wdqs_title', __('Default title', 'wdqs'), array($form, 'create_title_box'), 'wdqs_options_page', 'wdqs_post');
		add_settings_field('wdqs_format', __('Post format', 'wdqs'), array($form, 'create_post_format_box'), 'wdqs_options_page', 'wdqs_post');
	}

	function create_admin_menu_entry () {
	if (@$_POST && isset($_POST['option_page'])) {
			$changed = false;
			$update = (defined('WP_NETWORK_ADMIN') && WP_NETWORK_ADMIN) ? 'update_site_option' : 'update_option';
			if('wdqs' == @$_POST['option_page']) {
				$update('wdqs', $_POST['wdqs']);
				$changed = true;
			}

			if ($changed) {
				$goback = add_query_arg('settings-updated', 'true',  wp_get_referer());
				wp_redirect($goback);
				die;
			}
		}
		$perms = (defined('WP_NETWORK_ADMIN') && WP_NETWORK_ADMIN) ? 'manage_network_options' : 'manage_options';
		$page = (defined('WP_NETWORK_ADMIN') && WP_NETWORK_ADMIN) ? 'settings.php' : 'options-general.php';
		add_submenu_page($page, __('Status', 'wdqs'), __('Status', 'wdqs'), $perms, 'wdqs', array($this, 'create_admin_page'));
	}

	function create_admin_page () {
		include(WDQS_PLUGIN_BASE_DIR . '/lib/forms/plugin_settings.php');
	}

	function status_dashboard_widget () {
		include(WDQS_PLUGIN_BASE_DIR . '/lib/forms/dashboard_widget.php');
	}

	function add_status_dashboard_widget () {
		// We can alternatively use "edit_posts"
		if (!current_user_can('publish_posts')) return false;
		if ($this->data->get('show_on_dashboard')) {
			wp_add_dashboard_widget('wdqs_dashboard_status_widget', __("Status", 'wdqs'), array($this, 'status_dashboard_widget'));
		}
	}

	/**
	 * This is where the extracted link is processed, and
	 * reponse/storage HTML is generated.
	 */
	function generate_link_preview ($link, $data=false, $is_post=false) {
		$preview = false;

		if (is_array($data)) {
			extract($data);
		} else {
			$image = $no_image = $height = $width = false;
		}

		// Is it a video/oEmbed?
		if (!class_exists('WP_oEmbed')) require_once(ABSPATH . '/wp-includes/class-oembed.php');
		$wp_oembed = new WP_oEmbed();
		foreach(array_keys($wp_oembed->providers) as $rx) {
			if (!@preg_match($rx, $link)) continue;
			$args = array();
			if ($height) $args['height'] = $height;
			if ($width) $args['width'] = $width;
			$preview = wp_oembed_get($link, $args);
		}
		if ($preview) {
			$this->_link_type = 'video';
			return "<div class='wdqs wdqs_embed'>{$preview}</div>";
		}

		// Is it an image?
		if (
			// a) Direct link
			preg_match('/\.(png|gif|jpg|jpeg)$/i', $link)
			||
			// b) Dynamic image
			@getimagesize($link)
		) {
			$this->_link_type = 'image';
			$height = $height ? "height='{$height}'" : '';
			$width = $width ? "width='{$width}'" : '';
			return "<div class='wdqs wdqs_image'><img src='{$link}' {$height} {$width} /></div>";
		}

		// We're still here, and it's
		// most likely we're dealing with a link to a page.
		// Parse it, then.
		$page = $this->get_page_contents($link);
		require_once(WDQS_PLUGIN_BASE_DIR . '/lib/external/simple_html_dom.php');
		$html = str_get_html($page);
		$str = $html->find('text');

		if (!$str) return $link;

		$this->_link_type = 'link';

		if (!$image) {
			$images = array();
			$image_els = $html->find('img');
			foreach ($image_els as $el) {
				if ($el->width > 100 && $el->height > 1) // Disregard spacers
					$images[] = $el->src;
			}
			$og_image = $html->find('meta[property=og:image]', 0);
			if ($og_image) array_unshift($images, $og_image->content); //$images[] = $og_image->content;

			if ($is_post) {
				$image = current($images);
				$images = false;
			}
		}
		if ($no_image) {
			$images = $image = false;
		}

		if (!$link_title) {
			$og_title = $html->find('meta[property=og:title]', 0);
			$title = $html->find('title', 0);
			$title = $og_title ? $og_title->content : $title->plaintext;
		} else $title = $link_title;
		$this->_link_title = $title ? $title : $this->_get_default_title();

		if (!$link_text) {
			$p = $html->find('p', 0);
			$og_desc = $html->find('meta[property=og:description]', 0);
			$meta_desc = $html->find('meta[name=description]', 0);
			$meta = $og_desc ? $og_desc : $meta_desc;
			$text = $meta ? $meta->content : $p->plaintext;
		} else $text = $link_text;

		ob_start();
		include(WDQS_PLUGIN_BASE_DIR . '/lib/forms/link_preview.php');
		$preview = ob_get_contents();
		ob_end_clean();
		return $preview;
	}

	/**
	 * Here we extract the link and pass it on for further processing,
	 * then replace it with the results.
	 */
	function generate_preview_html ($txt, $data=false, $is_post=false) {
		$link = false;
		if (preg_match('/\s+/', $txt)) {
			$parts = preg_split('/\s+/', trim($txt));
			foreach ($parts as $part) {
				if (preg_match('/^https?:/', $part)) {
					$link = $part; break;
				}
			}
		} else {
			$link = preg_match('/^https?:/', trim($txt)) ? trim($txt) : false;
		}

		//if (!$link && !$is_post) return $is_post ? $txt : false;
		if (!$link && !$is_post) {
			$this->_link_type = 'generic';
			return $txt;
		}
		if (!preg_match('!^https?:!', $link)) return $is_post ? $txt : false;

		$txt = preg_replace('!' . preg_quote($link) . '!', '###WDQS_LINK###', $txt);
		if (!current_user_can('unfiltered_html')) {
			$txt = wp_filter_post_kses($txt);
		}
		$txt = nl2br($txt);
		$preview = preg_replace('!###WDQS_LINK###!', $this->generate_link_preview($link, $data, $is_post), $txt);

		return $preview;
	}

	private function _get_default_title () {
		$title = $this->data->get('default_title');
		$title = $title ? $title : __('My quick %s post', 'wdqs');
		return sprintf($title, $this->_link_type);
	}

	function create_post ($data) {
		if (!current_user_can('publish_posts')) return false;
		global $user_ID;
		$send = array(
			'image' => $data['thumbnail'],
			'no_image' => (int)$data['no_thumbnail'],
			'height' => (int)$data['height'],
			'width' => (int)$data['width'],
			'link_title' => @$data['link_title'],
			'link_text' => @$data['link_text'],
		);
		$text = $this->generate_preview_html($data['data'], $send, true);
		$title = @$data['title'] ? $data['title'] : $this->_get_default_title();
		$post = array (
			'post_title' => $title,
			'post_content' => $text,
			'post_date' => current_time('mysql'),
			'post_status' => @$_POST['is_draft'] ? 'draft' : 'publish',
			'post_author' => $user_ID,
		);

		$post_id = wp_insert_post($post);

		if ($post_id) {
			update_post_meta($post_id, 'wdqs_type', $this->_link_type);
			update_post_meta($post_id, 'wdqs_posted', time());
			$post = get_post($post_id);

			$post = get_post($post_id);

 			// Prepare old post for UFb triggering
			// Walkaround for 1.3 UFb fix
 			$old_post = clone $post;
 			$old_post->post_status = 'draft';
			// Make sure we trigger this hook, as that's what UFb uses
			do_action('post_updated', $post_id, $post, $old_post);
		}

		return $post_id;
	}

	function json_generate_preview () {
		$data = array(
			'height' => @$_POST['height'],
			'width' => @$_POST['width'],
		);
		$preview = $this->generate_preview_html(@$_POST['text'], $data);
		$title = (isset($_POST['title']) && $_POST['title']) ? $_POST['title'] : $this->_link_title;
		$title = $title ? $title : $this->_get_default_title();

		$status = strlen($preview);
		header('Content-type: application/json');
		echo json_encode(array(
			'status' => $status,
			'preview' => array(
				'markup' => $preview,
				'type' => $this->_link_type,
				'title' => $title,
				'height' => @$_POST['height'],
				'width' => @$_POST['width'],
			),
		));
		exit();
	}

	function json_post () {
		$status = $this->create_post($_POST);
		ob_start();
		include(WDQS_PLUGIN_BASE_DIR . '/lib/forms/dashboard_widget.php');
		$form = ob_get_contents();
		ob_end_clean();
		header('Content-type: application/json');
		echo json_encode(array(
			'status' => $status,
			'form' => $form,
		));
		exit();
	}

	function add_hooks () {
		// Step0: Register options and menu
		add_action('admin_init', array($this, 'register_settings'));
		add_action('admin_menu', array($this, 'create_admin_menu_entry'));
		add_action('network_admin_menu', array($this, 'create_admin_menu_entry'));

		add_action('admin_print_scripts-index.php', array($this, 'js_load_scripts'));
		add_action('admin_print_styles-index.php', array($this, 'css_load_styles'));

		add_action('wp_dashboard_setup', array($this, 'add_status_dashboard_widget'));

		add_action('wp_ajax_wdqs_generate_preview', array($this, 'json_generate_preview'));
		add_action('wp_ajax_wdqs_post', array($this, 'json_post'));

	}
}