<?php
/**
 * Shows Facebook Facepile box.
 * See http://developers.facebook.com/docs/reference/plugins/facepile/
 */
class Wdfb_WidgetFacepile extends WP_Widget {

	function Wdfb_WidgetFacepile () {
		$widget_ops = array('classname' => __CLASS__, 'description' => __('Shows Facebook Facepile box', 'wdfb'));
		parent::WP_Widget(__CLASS__, 'Facebook Facepile', $widget_ops);
	}

	function form($instance) {
		$title = esc_attr($instance['title']);
		$url = esc_attr($instance['url']);
		$width = esc_attr($instance['width']);
		$rows = esc_attr($instance['rows']);

		// Set defaults
		// ...

		$html = '<p>';
		$html .= '<label for="' . $this->get_field_id('title') . '">' . __('Title:', 'wdfb') . '</label>';
		$html .= '<input type="text" name="' . $this->get_field_name('title') . '" id="' . $this->get_field_id('title') . '" class="widefat" value="' . $title . '"/>';
		$html .= '</p>';

		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id('url') . '">' . __('URL <small>(optional)</small>:', 'wdfb') . '</label>';
		$html .= '<input type="text" name="' . $this->get_field_name('url') . '" id="' . $this->get_field_id('url') . '" class="widefat" value="' . $url . '"/>';
		$html .= '<div>' . __('By default, the widget will show the Facepile box for your page. You can override that by providing a custom URL here.', 'wdfb') . '</div>';
		$html .= '</p>';

		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id('width') . '">' . __('Width:', 'wdfb') . '</label>';
		$html .= '<input type="text" name="' . $this->get_field_name('width') . '" id="' . $this->get_field_id('width') . '" size="3" value="' . $width . '"/>';
		$html .= '</p>';

		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id('rows') . '">' . __('Rows:', 'wdfb') . '</label>';
		$html .= '<select name="' . $this->get_field_name('rows') . '" id="' . $this->get_field_id('rows') . '">';
		for ($i=1; $i<11; $i++) {
			$html .= '<option value="' . $i . '" ' . (($i == $rows) ? 'selected="selected"' : '') . '>' . $i . '</option>';
		}
		$html .= '</select>';
		$html .= '</p>';

		echo $html;
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['url'] = strip_tags($new_instance['url']);
		$instance['width'] = strip_tags($new_instance['width']);
		$instance['rows'] = strip_tags($new_instance['rows']);

		return $instance;
	}

	function widget($args, $instance) {
		extract($args);
		$title = apply_filters('widget_title', $instance['title']);
		$url = $instance['url'];
		$width = $instance['width'];
		$width = $width ? $width : 250;
		$rows = $instance['rows'];
		$rows = $rows ? $rows : 4;

		$url = ($url) ? $url : rawurlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		
		$locale = wdfb_get_locale();

		echo $before_widget;
		if ($title) echo $before_title . $title . $after_title;

		echo '<iframe src="http://www.facebook.com/plugins/facepile.php?href=' . 
			$url . '&amp;width=' . $width . '&amp;locale=' . $locale . '&amp;max_rows=' . 
			$rows . '" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:' . 
			$width . 'px;" allowTransparency="true"></iframe>'
		;

		echo $after_widget;
	}
}