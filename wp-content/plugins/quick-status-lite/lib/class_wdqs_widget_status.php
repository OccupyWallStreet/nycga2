<?php
/**
 * Shows one or more last Status posts
 */
class Wdqs_WidgetStatus extends WP_Widget {

	function Wdqs_WidgetStatus () {
		$widget_ops = array('classname' => __CLASS__, 'description' => __('Shows one or more last Status posts', 'wdqs'));
		parent::WP_Widget(__CLASS__, 'Status', $widget_ops);
	}

	function form($instance) {
		$title = esc_attr($instance['title']);
		$count = esc_attr($instance['count']);
		$autorefresh = esc_attr($instance['autorefresh']);

		// Set defaults
		// ...
		$html = '<div class="error below-h2"><p><a title="Upgrade Now" href="http://premium.wpmudev.org/project/quick-status">Upgrade to Pro version to enable additional features</a></p></div>';

		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id('title') . '">' . __('Title:', 'wdqs') . '</label>';
		$html .= '<input type="text" name="' . $this->get_field_name('title') . '" id="' . $this->get_field_id('title') . '" class="widefat" value="' . $title . '"/>';
		$html .= '</p>';

		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id('count') . '">' . __('Show this many posts:', 'wdqs') . '</label>';
		$html .= '<select disabled="disabled" id="' . $this->get_field_id('count') . '">';
		for ($i=1; $i<11; $i++) {
			$html .= '<option value="' . $i . '" ' . (($i == $count) ? 'selected="selected"' : '') . '>' . $i . '</option>';
		}
		$html .= '</select>';
		$html .= '</p>';

		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id('autorefresh') . '">' . __('Auto-update rate <small>(seconds)</small>:', 'wdqs') . '</label>';
		$html .= '<select disabled="disabled" id="' . $this->get_field_id('autorefresh') . '">';
		$html .= '<option value="">' . __('Never auto-update', 'wdqs') . '</option>';
		for ($i=5; $i<=120; $i+=5) {
			$html .= '<option value="' . $i . '" ' . (($i == $autorefresh) ? 'selected="selected"' : '') . '>' . $i . '</option>';
		}
		$html .= '</select> ';
		$html .= '</p>';

		echo $html;
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);

		return $instance;
	}

	function widget($args, $instance) {
		extract($args);
		$title = apply_filters('widget_title', $instance['title']);
		$count = 1;

		echo $before_widget;
		if ($title) echo $before_title . $title . $after_title;

		echo '<div class="wdqs_widget_status_root">';
		echo '<ul class="wdqs_widget_status">';
		echo $this->_prepare_post_list_items($count);
		echo '</ul>';
		if (!defined('WDQS_WIDGET_STATUS_JAVASCRIPT_INCLUDED')) {
			$this->inject_script_dependencies();
			define('WDQS_WIDGET_STATUS_JAVASCRIPT_INCLUDED', true);
		}
		echo '</div>';

		echo $after_widget;
	}

	function inject_script_dependencies () {
		echo '<link type="text/css" rel="stylesheet" href="' .WDQS_PLUGIN_URL  . '/css/status_widget.css" />';
	}

	private function _prepare_post_list_items ($count=1, $type=false) {
		$posts = $this->_get_status_posts($count, $type);
		$out = '';
		foreach ($posts as $post) {
			$out .= '<li>';
			$out .= '<div class="wdqs_widget_status_title">' . $post->post_title . '</div>';
			$out .= '<div class="wdqs_widget_status_body">' . $post->post_content . '</div>';
			$out .= '</li>';
		}
		return $out;
	}

	private function _get_status_posts ($count=1, $type=false) {
		$args = array(
			'posts_per_page' => $count,
			'meta_key' => 'wdqs_type',
			'post_status' => 'publish',
			'orderby' => 'date',
		);
		if ($type) {
			$args['meta_value'] = $type;
		}
		$q = new Wp_Query($args);
		return $q->posts;
	}
}