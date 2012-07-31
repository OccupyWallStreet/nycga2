<?php
/**
 * Shows Facebook Recommendations box.
 * See http://developers.facebook.com/docs/reference/plugins/recommendations/
 */
class Wdfb_WidgetRecommendations extends WP_Widget {

	function Wdfb_WidgetRecommendations () {
		$widget_ops = array('classname' => __CLASS__, 'description' => __('Shows Facebook Recommendations box.', 'wdfb'));
		parent::WP_Widget(__CLASS__, 'Facebook Recommendations', $widget_ops);
	}

	function form($instance) {
		$title = esc_attr($instance['title']);
		$width = esc_attr($instance['width']);
		$height = esc_attr($instance['height']);
		$show_header = esc_attr($instance['show_header']);
		$color_scheme = esc_attr($instance['color_scheme']);

		// Set defaults
		// ...

		$html = '<p>';
		$html .= '<label for="' . $this->get_field_id('title') . '">' . __('Title:', 'wdfb') . '</label>';
		$html .= '<input type="text" name="' . $this->get_field_name('title') . '" id="' . $this->get_field_id('title') . '" class="widefat" value="' . $title . '"/>';
		$html .= '</p>';

		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id('width') . '">' . __('Width:', 'wdfb') . '</label>';
		$html .= '<input type="text" name="' . $this->get_field_name('width') . '" id="' . $this->get_field_id('width') . '" size="3" value="' . $width . '"/>';
		$html .= '</p>';

		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id('height') . '">' . __('Height:', 'wdfb') . '</label>';
		$html .= '<input type="text" name="' . $this->get_field_name('height') . '" id="' . $this->get_field_id('height') . '" size="3" value="' . $height . '"/>';
		$html .= '</p>';

		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id('show_header') . '">' . __('Show header:', 'wdfb') . '</label>';
		$html .= '<input type="checkbox" name="' . $this->get_field_name('show_header') . '" id="' . $this->get_field_id('show_header') . '" value="1" ' . ($show_header ? 'checked="checked"' : '') . ' />';
		$html .= '</p>';

		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id('color_scheme') . '">' . __('Color scheme:', 'wdfb') . '</label>';
		$html .= '<select name="' . $this->get_field_name('color_scheme') . '" id="' . $this->get_field_id('color_scheme') . '">';
		$html .= '<option value="light" ' . (('light' == $color_scheme) ? 'selected="selected"' : '') . '>Light</option>';
		$html .= '<option value="dark" ' . (('dark' == $color_scheme) ? 'selected="selected"' : '') . '>Dark</option>';
		$html .= '</select>';
		$html .= '</p>';

		echo $html;
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['width'] = strip_tags($new_instance['width']);
		$instance['height'] = strip_tags($new_instance['height']);
		$instance['show_header'] = strip_tags($new_instance['show_header']);
		$instance['color_scheme'] = strip_tags($new_instance['color_scheme']);

		return $instance;
	}

	function widget($args, $instance) {
		extract($args);
		$title = apply_filters('widget_title', $instance['title']);
		$width = $instance['width'];
		$width = $width ? $width : 250;
		$height = $instance['height'];
		$height = $height ? $height : 250;
		$show_header = (int)@$instance['show_header'];
		$show_header = $show_header ? 'true' : 'false';
		$color_scheme = $instance['color_scheme'];
		$color_scheme = $color_scheme ? $color_scheme : 'light';

		$url = get_option('siteurl');
		$locale = wdfb_get_locale();

		echo $before_widget;
		if ($title) echo $before_title . $title . $after_title;

		echo '<iframe src="http://www.facebook.com/plugins/recommendations.php?site=' . 
			$url . '&amp;width=' . $width . '&amp;height=' . $height . 
			'&amp;locale=' . $locale . '&amp;header=' . $show_header . '&amp;colorscheme=' . 
			$color_scheme . '&amp;font&amp;border_color" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:' . 
			$width . 'px; height:' . $height . 'px;" allowTransparency="true"></iframe>'
		;

		echo $after_widget;
	}
}