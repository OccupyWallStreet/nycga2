<?php
/**
 * Handles shortcodes.
 */
class Wdfb_MarkerReplacer {

	var $data;
	var $model;
	var $buttons = array (
		'like_button' => 'wdfb_like_button',
		'events' => 'wdfb_events',
		'album' => 'wdfb_album',
		'connect' => 'wdfb_connect'
	);

	function __construct () {
		$this->model = new Wdfb_Model;
		$this->data =& Wdfb_OptionsRegistry::get_instance();
	}

	function Wdfb_MarkerReplacer () {
		$this->__construct();
	}

	function get_button_tag ($b) {
		if (!isset($this->buttons[$b])) return '';
		return '[' . $this->buttons[$b] . ']';
	}

	function process_connect_code ($atts, $content='') {
		if (!$this->data->get_option('wdfb_connect', 'allow_facebook_registration')) return $content;
		$atts = shortcode_atts(array(
			'avatar_size' => 32,
		), $atts);
		$content = $content ? $content : __('Log in with Facebook', 'wdfb');
		if (!class_exists('Wdfb_WidgetConnect')) {
			echo '<script type="text/javascript" src="' . WDFB_PLUGIN_URL . '/js/wdfb_facebook_login.js"></script>';
		}
		$user = wp_get_current_user();
		$html = '';
		if (!$user->ID) {
			$html = '<p class="wdfb_login_button"><fb:login-button scope="' . Wdfb_Permissions::get_permissions() . '" redirect-url="' . wdfb_get_login_redirect() . '"  onlogin="_wdfb_notifyAndRedirect();">' . $content . '</fb:login-button></p>';
		} else {
			$logout = site_url('wp-login.php?action=logout&redirect_to=' . rawurlencode(home_url()));
			$html .= get_avatar($user->ID, $atts['avatar_size']);
			$html .= "<br /><a href='{$logout}'>" . __('Log out', 'wdfb') . "</a>";
		}
		return $html;
	}

	function process_like_button_code ($atts, $content='') {
		global $wp_current_filter;

		// Check allowed
		$allow = $this->data->get_option('wdfb_button', 'allow_facebook_button');
		if (!apply_filters('wdfb-show_facebook_button', $allow)) return '';

		// Check nesting (i.e. posts within post, reformatted with apply_filters)
		$filters = array_count_values($wp_current_filter);
		if ($filters['the_content'] > 1) return '';

		$atts = shortcode_atts(array(
			'forced' => false,
		), $atts);
		$forced = ($atts['forced'] && 'no' != $atts['forced']) ? true : false;

		$in_types = $this->data->get_option('wdfb_button', 'not_in_post_types');
		if (@in_array(get_post_type(), $in_types) && !$forced) return '';

		$send = $this->data->get_option('wdfb_button', 'show_send_button');
		$layout = $this->data->get_option('wdfb_button', 'button_appearance');
		$url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

		$width = ("standard" == $layout) ? 300 : (
			("button_count" == $layout) ? 150 : 60
		); 
		$width = apply_filters('wdfb-like_button-width', $width); 
		
		if (is_home() && $this->data->get_option('wdfb_button', 'show_on_front_page')) {
			$tmp_url = get_permalink();
			$url = $tmp_url ? $tmp_url : $url;
			$url = rawurlencode($url);
			
			$height = ("box_count" == $layout) ? 60 : 25;
			$height = apply_filters('wdfb-like_button-height', $height); 
			
			return "<div class='wdfb_like_button'><iframe src='http://www.facebook.com/plugins/like.php?&amp;href={$url}&amp;send=false&amp;layout={$layout}&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font&amp;height={$height}&amp;width={$width}' scrolling='no' frameborder='0' style='border:none; overflow:hidden; height:{$height}px; width:{$width}px;' allowTransparency='true'></iframe></div>";
		}

		return '<div class="wdfb_like_button"><fb:like href="' . WDFB_PROTOCOL  . $url . '" send="' . ($send ? 'true' : 'false') . '" layout="' . $layout . '" width="' . $width . '" show_faces="true" font=""></fb:like></div>';
	}

	function process_events_code ($atts, $content='') {
		$post_id = get_the_ID();
		if (!$post_id) return '';

		$atts = shortcode_atts(array(
			'for' => false,
			'starting_from' => false,
			'only_future' => false,
			'show_image' => "true",
			'show_location' => "true",
			'show_start_date' => "true",
			'show_end_date' => "true",
			'order' => false,
		), $atts);

		if (!$atts['for']) return ''; // We don't know whose events to show

		$api = new Wdfb_EventsBuffer;
		$events = $api->get_for($atts['for']);
		if (!is_array($events)) return $content;

		if ($atts['order']) {
			$events = $this->_sort_by_time($events, $atts['order']);
		}

		$show_image = ("true" == $atts['show_image']) ? true : false;
		$show_location = ("true" == $atts['show_location']) ? true : false;
		$show_start_date = ("true" == $atts['show_start_date']) ? true : false;
		$show_end_date = ("true" == $atts['show_end_date']) ? true : false;
		$timestamp_format = get_option('date_format') . ' ' . get_option('time_format');

		$date_threshold = $atts['starting_from'] ? strtotime($atts['starting_from']) : false;
		if ($atts['only_future'] && 'false' != $atts['only_future']) {
			$now = time();
			$date_threshold = ($date_threshold && $date_threshold > $now) ? $date_threshold : $now;
		}

		ob_start();
		foreach ($events as $event) {
			if ($date_threshold > strtotime($event['start_time'])) continue;
			include (WDFB_PLUGIN_BASE_DIR . '/lib/forms/event_item.php');
		}
		$ret = ob_get_contents();
		ob_end_clean();

		return "<div><ul>{$ret}</ul></div>";
	}
	
	function process_album_code ($atts, $content='') {
		$post_id = get_the_ID();
		if (!$post_id) return '';

		$atts = shortcode_atts(array(
			'id' => false,
			'limit' => false,
			'photo_class' => 'thickbox',
			'album_class' => false,
			'photo_width' => 75,
			'photo_height' => false,
			'crop' => false,
			'link_to' => 'source',
			'columns' => false,
		), $atts);

		if (!$atts['id']) return ''; // We don't know what album to show
		$img_w = (int)$atts['photo_width'];
		$img_h = (int)$atts['photo_height'];

		$fb_open = ('source' != $atts['link_to']);
		
		$api = new Wdfb_AlbumPhotosBuffer;
		$photos = $api->get_for($atts['id']);
		if (!is_array($photos)) return $content;
		
		$ret = false;
		$i = 0;
		
		$display_idx = ($img_w >= 130)
			? (($img_w >= 180) ? 0 : 1)
			: (($img_w >= 75) ? 2 : 3)
		;
		
		$columns = (int)$atts['columns'];
		$current = 1;
		foreach ($photos as $photo) {
			$photo_idx = isset($photo['images'][$display_idx]) ? $display_idx : count($photo['images'])-1;
			$style = $atts['crop'] ? "style='display:block;float:left;height:{$img_h}px;overflow:hidden'" : '';
			$url = $fb_open
				? 'http://www.facebook.com/photo.php?fbid=' . $photo['id']
				: $photo['images'][0]['source']
			;
			$ret .= '<a href="' . $url . 
				'" class="' . $atts['photo_class'] . '" rel="' . $atts['id'] . '-photo" ' . $style . ' >' .
					'<img src="' . $photo['images'][$photo_idx]['source'] . '" ' .
						($img_w ? "width='{$img_w}'" : '') .
						($img_h && !$atts['crop'] ? "height='{$img_h}'" : '') .
					' />' .
			'</a>';
			if ($columns && (++$i % $columns) == 0) $ret .= '<br ' . ($style ? 'style="clear:left"' : '') . '/>';
			if ((int)$atts['limit'] && $current >= (int)$atts['limit']) break;
			$current++;
		}
		return "<div class='{$atts['album_class']}'>{$ret}</div>";
	}

	/**
	 * Helper for sorting events by their start_time.
	 */
	function _sort_by_time ($events, $direction="ASC") {
		usort($events, create_function(
			'$a,$b',
			'if (strtotime($a["start_time"]) == strtotime($b["start_time"])) return 0;' .
			'return (strtotime($a["start_time"]) > strtotime($b["start_time"])) ? 1 : -1;'
		));
		return ("DESC" == $direction) ? array_reverse($events) : $events;
	}

	/**
	 * Registers shortcode handlers.
	 */
	function register () {
		foreach ($this->buttons as $key=>$shortcode) {
			//var_export("process_{$key}_code");
			add_shortcode($shortcode, array($this, "process_{$key}_code"));
		}
	}
}