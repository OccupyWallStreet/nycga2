<?php
/**
 * Shows "Vote" box with number of votes.
 */
class Wdpv_WidgetVoting extends WP_Widget {

	function Wdpv_WidgetVoting () {
		$widget_ops = array('classname' => __CLASS__, 'description' => __('Shows "Vote" box for current post/page with number of votes.', 'wdpv'));
		parent::WP_Widget(__CLASS__, 'Voting Widget', $widget_ops);
	}

	function form($instance) {
		$title = esc_attr($instance['title']);
		$show_vote_up = esc_attr($instance['show_vote_up']);
		$show_vote_down = esc_attr($instance['show_vote_down']);
		$show_vote_result = esc_attr($instance['show_vote_result']);

		// Set defaults
		// ...

		$html = '<p>';
		$html .= '<label for="' . $this->get_field_id('title') . '">' . __('Title:', 'wdpv') . '</label>';
		$html .= '<input type="text" name="' . $this->get_field_name('title') . '" id="' . $this->get_field_id('title') . '" class="widefat" value="' . $title . '"/>';
		$html .= '</p>';

		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id('show_vote_up') . '">' . __('Show "Vote up" button:', 'wdpv') . '</label>';
		$html .= '<input type="checkbox" name="' . $this->get_field_name('show_vote_up') . '" id="' . $this->get_field_id('show_vote_up') . '" value="1" ' . ($show_vote_up ? 'checked="checked"' : '') . ' />';
		$html .= '</p>';

		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id('show_vote_down') . '">' . __('Show "Vote down" button:', 'wdpv') . '</label>';
		$html .= '<input type="checkbox" name="' . $this->get_field_name('show_vote_down') . '" id="' . $this->get_field_id('show_vote_down') . '" value="1" ' . ($show_vote_down ? 'checked="checked"' : '') . ' />';
		$html .= '</p>';

		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id('show_vote_result') . '">' . __('Show voting results:', 'wdpv') . '</label>';
		$html .= '<input type="checkbox" name="' . $this->get_field_name('show_vote_result') . '" id="' . $this->get_field_id('show_vote_result') . '" value="1" ' . ($show_vote_result ? 'checked="checked"' : '') . ' />';
		$html .= '</p>';

		echo $html;
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['show_vote_up'] = strip_tags($new_instance['show_vote_up']);
		$instance['show_vote_down'] = strip_tags($new_instance['show_vote_down']);
		$instance['show_vote_result'] = strip_tags($new_instance['show_vote_result']);

		return $instance;
	}

	function widget($args, $instance) {
		extract($args);
		$title = apply_filters('widget_title', $instance['title']);
		$show_vote_up = (int)@$instance['show_vote_up'];
		$show_vote_up = $show_vote_up ? true : false;

		$show_vote_down = (int)@$instance['show_vote_down'];
		$show_vote_down = $show_vote_down ? true : false;

		$show_vote_down = (int)@$instance['show_vote_down'];
		$show_vote_down = $show_vote_down ? true : false;

		$show_vote_result = (int)@$instance['show_vote_result'];
		$show_vote_result = $show_vote_result ? true : false;

		$show_entire_widget = ($show_vote_up && $show_vote_down && $show_vote_result);

		if (is_singular()) { // Show widget only on votable pages
			$codec = new Wdpv_Codec;
			echo $before_widget;
			if ($title) echo $before_title . $title . $after_title;

			if ($show_entire_widget) {
				echo $codec->process_vote_widget_code(array());
			} else {
				if ($show_vote_up) echo $codec->process_vote_up_code(array('standalone'=>'no'));
				if ($show_vote_result) echo $codec->process_vote_result_code(array('standalone'=>'no'));
				if ($show_vote_down) echo $codec->process_vote_down_code(array('standalone'=>'no'));
				echo "<div class='wdpv_clear'></div>";
			}

			echo $after_widget;
		}
	}
}