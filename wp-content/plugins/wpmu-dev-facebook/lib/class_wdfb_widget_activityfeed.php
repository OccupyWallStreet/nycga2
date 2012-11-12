<?php
/**
 * Shows Facebook Recommendations box.
 * See http://developers.facebook.com/docs/reference/plugins/recommendations/
 */
class Wdfb_WidgetActivityFeed extends WP_Widget {

	function Wdfb_WidgetActivityFeed () {
		$widget_ops = array('classname' => __CLASS__, 'description' => __('Shows Facebook Activity Feed.', 'wdfb'));
		parent::WP_Widget(__CLASS__, 'Facebook Activity Feed', $widget_ops);
	}

	function form($instance) {
		$title = esc_attr($instance['title']);
		$url = esc_attr($instance['url']);
		$width = esc_attr($instance['width']);
		$height = esc_attr($instance['height']);
		$show_header = esc_attr($instance['show_header']);
		$recommendations = esc_attr($instance['recommendations']);
		$filter = esc_attr($instance['filter']);
		$color_scheme = esc_attr($instance['color_scheme']);
		$links = esc_attr($instance['links']);
		$iframe = esc_attr($instance['iframe']);

		// Set defaults
		// ...

		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id('title') . '">' . __('Title:', 'wdfb') . '</label>';
		$html .= '<input type="text" name="' . $this->get_field_name('title') . '" id="' . $this->get_field_id('title') . '" class="widefat" value="' . $title . '"/>';
		$html .= '</p>';

		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id('url') . '">' . __('Site:', 'wdfb') . '</label> ';
		$html .= '<input type="text" name="' . $this->get_field_name('url') . '" id="' . $this->get_field_id('url') . '" class="widefat" value="' . $url . '"/>';
		$html .= '<div><small>' . __('Enter a comma separated list of domains to show activity for, or leave blank to use current domain.', 'wdfb') . '</small></div>';
		$html .= '</p>';

		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id('filter') . '">' . __('Filter:', 'wdfb') . '</label> ';
		$html .= '<input type="text" name="' . $this->get_field_name('filter') . '" id="' . $this->get_field_id('filter') . '" class="widefat" value="' . $filter . '"/>';
		$html .= '<div><small>' . __('Only URLs which contain the filter string in the first two path parameters of the URL will be shown. Leave this field empty to prevent filtering.', 'wdfb') . '</small></div>';
		$html .= '</p>';

		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id('width') . '">' . __('Width:', 'wdfb') . '</label> ';
		$html .= '<input type="text" name="' . $this->get_field_name('width') . '" id="' . $this->get_field_id('width') . '" size="3" value="' . $width . '"/>px';
		$html .= '</p>';

		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id('height') . '">' . __('Height:', 'wdfb') . '</label> ';
		$html .= '<input type="text" name="' . $this->get_field_name('height') . '" id="' . $this->get_field_id('height') . '" size="3" value="' . $height . '"/>px';
		$html .= '</p>';

		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id('show_header') . '">' . __('Show header:', 'wdfb') . '</label> ';
		$html .= '<input type="checkbox" name="' . $this->get_field_name('show_header') . '" id="' . $this->get_field_id('show_header') . '" value="1" ' . ($show_header ? 'checked="checked"' : '') . ' />';
		$html .= '</p>';

		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id('recommendations') . '">' . __('Show recommendations:', 'wdfb') . '</label> ';
		$html .= '<input type="checkbox" name="' . $this->get_field_name('recommendations') . '" id="' . $this->get_field_id('recommendations') . '" value="1" ' . ($recommendations ? 'checked="checked"' : '') . ' />';
		$html .= '</p>';

		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id('color_scheme') . '">' . __('Color scheme:', 'wdfb') . '</label> ';
		$html .= '<select name="' . $this->get_field_name('color_scheme') . '" id="' . $this->get_field_id('color_scheme') . '">';
		$html .= '<option value="light" ' . (('light' == $color_scheme) ? 'selected="selected"' : '') . '>Light</option>';
		$html .= '<option value="dark" ' . (('dark' == $color_scheme) ? 'selected="selected"' : '') . '>Dark</option>';
		$html .= '</select>';
		$html .= '</p>';

		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id('links') . '">' . __('Link target:', 'wdfb') . '</label> ';
		$html .= '<select name="' . $this->get_field_name('links') . '" id="' . $this->get_field_id('links') . '">';
		$html .= '<option value="_top" ' . (('_top' == $links) ? 'selected="selected"' : '') . '>_top</option>';
		$html .= '<option value="_parent" ' . (('_parent' == $links) ? 'selected="selected"' : '') . '>_parent</option>';
		$html .= '<option value="_blank" ' . (('_blank' == $links) ? 'selected="selected"' : '') . '>_blank</option>';
		$html .= '</select>';
		$html .= '</p>';
		
		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id('iframe') . '">' . __('Do not use xfbml tag:', 'wdfb') . '</label> ';
		$html .= '<input type="checkbox" name="' . $this->get_field_name('iframe') . '" id="' . $this->get_field_id('iframe') . '" value="1" ' . ($iframe ? 'checked="checked"' : '') . ' />';
		$html .= '<div><small>' . __("If you're experiencing issues with your Activity Feeds, try checking this option", 'wdfb') . '</small></div>';
		$html .= '</p>';

		echo $html;
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['width'] = strip_tags($new_instance['width']);
		$instance['height'] = strip_tags($new_instance['height']);
		$instance['url'] = strip_tags($new_instance['url']);
		$instance['show_header'] = strip_tags($new_instance['show_header']);
		$instance['recommendations'] = strip_tags($new_instance['recommendations']);
		$instance['filter'] = strip_tags($new_instance['filter']);
		$instance['color_scheme'] = strip_tags($new_instance['color_scheme']);
		$instance['links'] = strip_tags($new_instance['links']);
		$instance['iframe'] = strip_tags($new_instance['iframe']);

		return $instance;
	}

	function widget($args, $instance) {
		extract($args);
		$title = apply_filters('widget_title', $instance['title']);
		$width = (int)$instance['width'];
		$width = $width ? $width : 300;
		$height = (int)$instance['height'];
		$height = $height ? $height : 300;
		$url = $instance['url'];
		$url = $url ? $url : parse_url(site_url(), PHP_URL_HOST);
		$show_header = (int)@$instance['show_header'];
		$show_header = $show_header ? 'true' : 'false';
		$recommendations = (int)@$instance['recommendations'];
		$recommendations = $recommendations ? 'true' : 'false';
		$filter = @$instance['filter'];
		$color_scheme = $instance['color_scheme'];
		$color_scheme = $color_scheme ? $color_scheme : 'light';
		$links = $instance['links'];
		$links = $links ? $links : '_blank';
		$iframe = (int)$instance['iframe'];

		echo $before_widget;
		if ($title) echo $before_title . $title . $after_title;

		if (!$iframe) {
			echo "<fb:activity site='{$url}' width='{$width}' height='{$height}' header='{$show_header}' recommendations='{$recommendations}' linktarget='{$links}'></fb:activity>";
		} else {
			$data = Wdfb_OptionsRegistry::get_instance();
			$key = $data->get_option('wdfb_api', 'app_key');
			$locale = wdfb_get_locale();
			echo "<iframe src='//www.facebook.com/plugins/activity.php?api_key={$key}&amp;app_id={$key}&amp;site={$url}&amp;width={$width}&amp;height={$height}&amp;header={$show_header}&amp;colorscheme={$color_scheme}&amp;linktarget={$links}&amp;locale={$locale}&amp;border_color&amp;font&amp;recommendations={$recommendations}&amp;appId={$key}' scrolling='no' frameborder='0' style='border:none; overflow:hidden; width:{$width}px; height:{$height}px;' allowTransparency='true'></iframe>";
		} 

		echo $after_widget;
	}
}