<?php
/**
 * Shows list of posts with highest number of votes on current blog.
 */
class Wdpv_WidgetPopular extends WP_Widget {

	function Wdpv_WidgetPopular () {
		$widget_ops = array('classname' => __CLASS__, 'description' => __('Shows list of posts with highest number of votes on current blog.', 'wdpv'));
		parent::WP_Widget(__CLASS__, 'Top voted Posts', $widget_ops);
	}

	function form($instance) {
		$title = esc_attr($instance['title']);
		$limit = esc_attr($instance['limit']);
		$posted_timeframe = esc_attr($instance['posted_timeframe']);
		$voted_timeframe = esc_attr($instance['voted_timeframe']);

		// Set defaults
		$limit = $limit ? $limit : 5;

		$html = '<p>';
		$html .= '<label for="' . $this->get_field_id('title') . '">' . __('Title:', 'wdpv') . '</label>';
		$html .= '<input type="text" name="' . $this->get_field_name('title') . '" id="' . $this->get_field_id('title') . '" class="widefat" value="' . $title . '"/>';
		$html .= '</p>';

		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id('limit') . '">' . __('Show this many posts:', 'wdpv') . '</label>';
		$html .= '<select name="' . $this->get_field_name('limit') . '" id="' . $this->get_field_id('limit') . '">';
		for ($i=1; $i<20; $i++) {
			$html .= "<option value='{$i}' " . (($i == $limit) ? 'selected="selected"' : '') . ">{$i}</option>";
		}
		$html .= '</select>';
		$html .= '</p>';

		$data = new Wdpv_Options;
		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id('posted_timeframe') . '">' . __('Posted within:', 'wdpv') . '</label>';
		$html .= '<select name="' . $this->get_field_name('posted_timeframe') . '" id="' . $this->get_field_id('posted_timeframe') . '">';
		$html .= '<option value="">' . __('All time', 'wdpv') . '</option>';
		foreach ($data->timeframes as $time => $label) {
			$html .= "<option value='{$time}' " . (($time == $posted_timeframe) ? 'selected="selected"' : '') . ">{$label}</option>";
		}
		$html .= '</select>';
		$html .= '</p>';

		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id('voted_timeframe') . '">' . __('Voted within:', 'wdpv') . '</label>';
		$html .= '<select name="' . $this->get_field_name('voted_timeframe') . '" id="' . $this->get_field_id('voted_timeframe') . '">';
		$html .= '<option value="">' . __('All time', 'wdpv') . '</option>';
		foreach ($data->timeframes as $time => $label) {
			$html .= "<option value='{$time}' " . (($time == $voted_timeframe) ? 'selected="selected"' : '') . ">{$label}</option>";
		}
		$html .= '</select>';
		$html .= '</p>';

		echo $html;
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['limit'] = strip_tags($new_instance['limit']);
		$instance['posted_timeframe'] = strip_tags($new_instance['posted_timeframe']);
		$instance['voted_timeframe'] = strip_tags($new_instance['voted_timeframe']);

		return $instance;
	}

	function widget($args, $instance) {
		extract($args);
		$title = apply_filters('widget_title', $instance['title']);

		$limit = (int)@$instance['limit'];
		$limit = $limit ? $limit : 5;

		$data = new Wdpv_Options;
		$posted_timeframe = @$instance['posted_timeframe'];
		$voted_timeframe = @$instance['voted_timeframe'];
		if (!in_array($posted_timeframe, array_keys($data->timeframes))) $posted_timeframe = false;
		if (!in_array($voted_timeframe, array_keys($data->timeframes))) $voted_timeframe = false;

		$model = new Wdpv_Model;
		$posts = $model->get_popular_on_current_site($limit, $posted_timeframe, $voted_timeframe);

		echo $before_widget;
		if ($title) echo $before_title . $title . $after_title;

		if (is_array($posts)) {
			echo "<ul class='wdpv_popular_posts'>";
			foreach ($posts as $post) {
				echo "<li>";
				echo '<a href="' . get_permalink($post['ID']) . '">' . $post['post_title'] . '</a> ';
				printf(__('<span class="wdpv_vote_count">(%s votes)</span>', 'wdpv'), $post['total']);
				echo "</li>";
			}
			echo "</ul>";
		}

		echo $after_widget;
	}
}