<?php

class Eab_NetworkUpcoming_Widget extends Eab_Widget {
	
	function __construct () {
		$widget_ops = array('classname' => __CLASS__, 'description' => __('Displays List of Upcoming Events from your entire network', $this->translation_domain));
		parent::WP_Widget(__CLASS__, __('Network Upcoming', $this->translation_domain), $widget_ops);
	}
	
	function form($instance) {
		$title = esc_attr($instance['title']);
		$limit = esc_attr($instance['limit']);
		
		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id('title') . '">' . __('Title:', 'wdfb') . '</label>';
		$html .= '<input type="text" name="' . $this->get_field_name('title') . '" id="' . $this->get_field_id('title') . '" class="widefat" value="' . $title . '"/>';
		$html .= '</p>';

		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id('limit') . '">' . __('Display only this many events:', 'wdfb') . '</label>';
		$html .= '<select name="' . $this->get_field_name('limit') . '" id="' . $this->get_field_id('limit') . '">';
		for ($i=1; $i<11; $i++) {
			$html .= '<option value="' . $i . '" ' . (($limit == $i) ? 'selected="selected"' : '') . '>' . $i . '</option>';
		}
		$html .= '</select>';
		$html .= '</p>';
		
		echo $html;
	}
	
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['limit'] = strip_tags($new_instance['limit']);

		return $instance;
	}
	
	function widget($args, $instance) {
		extract($args);
		$title = apply_filters('widget_title', $instance['title']);
		$limit = (int)$instance['limit'];
		
		$events = Eab_Network::get_upcoming_events($limit);
	
		echo $before_widget;
		if ($title) echo $before_title . $title . $after_title;
		if ($events) {
			echo '<ul>';
			foreach ($events as $event) {
				echo '<li><a href="' . 
					get_blog_permalink($event->blog_id, $event->ID) . '">' . $event->post_title . '</a>' .
				'</li>';
			}
			echo '</ul>';
		} else {
			echo '<p>' . __('No upcoming events on network', $this->translation_domain) . '</p>';
		}
		echo $after_widget;	
	}
}
