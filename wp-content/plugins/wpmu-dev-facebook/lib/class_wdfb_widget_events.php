<?php
/**
 * Shows Facebook Events box.
 */
class Wdfb_WidgetEvents extends WP_Widget {
	var $model;

	function Wdfb_WidgetEvents () {
		$this->model = new Wdfb_Model();
		$widget_ops = array('classname' => __CLASS__, 'description' => __('Shows Facebook Events', 'wdfb'));

		add_action('wp_print_styles', array($this, 'css_load_styles'));
		//add_action('wp_print_scripts', array($this, 'js_load_scripts'));
		add_action('admin_print_scripts-widgets.php', array($this, 'js_load_scripts'));
		add_action('admin_print_styles-widgets.php', array($this, 'css_load_admin_styles'));

		parent::WP_Widget(__CLASS__, 'Facebook Events', $widget_ops);
	}

	function css_load_styles () {
		wp_enqueue_style('wdfb_widget_events', WDFB_PLUGIN_URL . '/css/wdfb_widget_events.css');
	}
	function css_load_admin_styles () {
		wp_enqueue_style('wdfb_jquery_ui_style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/ui-lightness/jquery-ui.css');
	}
	function js_load_scripts () {
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_script('wdfb_widget_events', WDFB_PLUGIN_URL . '/js/wdfb_widget_events.js', array('jquery', 'jquery-ui-datepicker'));
		wp_localize_script('wdfb_widget_events', 'l10nWdfbEventsEditor', array(
			'insuficient_perms' => __("Your app doesn't have enough permissions to access your events", 'wdfb'),
			'grant_perms' => __("Grant needed permissions now", 'wdfb'),
		));
	}

	function form($instance) {
		$title = esc_attr($instance['title']);
		$for = esc_attr($instance['for']);
		$show_image = esc_attr($instance['show_image']);
		$show_location = esc_attr($instance['show_location']);
		$show_start_date = esc_attr($instance['show_start_date']);
		$show_end_date = esc_attr($instance['show_end_date']);
		$date_threshold = esc_attr($instance['date_threshold']);
		$only_future = esc_attr($instance['only_future']);

		// Set defaults
		// ...
		$only_future = (isset($instance['only_future'])) ? $instance['only_future'] : true;

		$html = '';

		$fb_user = $this->model->fb->getUser();
		if (!$fb_user) {
			$html .= '<div class="wdfb_admin_message message">';
			$html .= sprintf(__('You should be logged into your Facebook account when adding this widget. <a href="%s">Click here to do so now</a>, then refresh this page.'), $this->model->fb->getLoginUrl());
			$html .= '</div>';
		} else {
			$html .= '<div class="wdfb_admin_message message">Facebook user ID: ' . $fb_user . '</div>';
		}

		$html .= '<div class="wdfb_widget_events_home">';

		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id('title') . '">' . __('Title:', 'wdfb') . '</label>';
		$html .= '<input type="text" name="' . $this->get_field_name('title') . '" id="' . $this->get_field_id('title') . '" class="widefat" value="' . $title . '"/>';
		$html .= '</p>';

		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id('for') . '">' . __('Show events for:', 'wdfb') . '</label>';
		$html .= '<input type="text" name="' . $this->get_field_name('for') . '" id="' . $this->get_field_id('for') . '" value="' . $for . '"/>';
		$html .= '<div>Leave this box empty to display your own events.</div>';
		$html .= '</p>';

		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id('show_image') . '">' . __('Show image:', 'wdfb') . '</label>';
		$html .= ' <input type="checkbox" name="' . $this->get_field_name('show_image') . '" id="' . $this->get_field_id('show_image') . '" value="1" ' . ($show_image ? 'checked="checked"' : '') . '/>';
		$html .= '</p>';

		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id('show_location') . '">' . __('Show location:', 'wdfb') . '</label>';
		$html .= ' <input type="checkbox" name="' . $this->get_field_name('show_location') . '" id="' . $this->get_field_id('show_location') . '" value="1" ' . ($show_location ? 'checked="checked"' : '') . '/>';
		$html .= '</p>';

		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id('show_start_date') . '">' . __('Show event start:', 'wdfb') . '</label>';
		$html .= ' <input type="checkbox" name="' . $this->get_field_name('show_start_date') . '" id="' . $this->get_field_id('show_start_date') . '" value="1" ' . ($show_start_date ? 'checked="checked"' : '') . '/>';
		$html .= '</p>';

		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id('show_end_date') . '">' . __('Show event end:', 'wdfb') . '</label>';
		$html .= ' <input type="checkbox" name="' . $this->get_field_name('show_end_date') . '" id="' . $this->get_field_id('show_end_date') . '" value="1" ' . ($show_end_date ? 'checked="checked"' : '') . '/>';
		$html .= '</p>';

		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id('only_future') . '">' . __('Show only future events:', 'wdfb') . '</label>';
		$html .= ' <input type="checkbox" name="' . $this->get_field_name('only_future') . '" id="' . $this->get_field_id('only_future') . '" value="1" ' . ($only_future ? 'checked="checked"' : '') . '/>';
		$html .= '</p>';

		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id('date_threshold') . '">' . __('Show events starting from this date:', 'wdfb') . '</label>';
		$html .= ' <input type="text" class="widefat wdfb_date_threshold" name="' . $this->get_field_name('date_threshold') . '" id="' . $this->get_field_id('date_threshold') . '" value="' . $date_threshold . '"/>';
		$html .= '<br /><small>(YYYY-mm-dd, e.g. 2011-06-09)</small>';
		$html .= '</p>';

		$html .= '</div>';

		echo $html;
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$for = strip_tags($new_instance['for']);
		$instance['for'] = $for ? $for : $this->model->fb->getUser();
		$instance['show_image'] = strip_tags($new_instance['show_image']);
		$instance['show_location'] = strip_tags($new_instance['show_location']);
		$instance['show_start_date'] = strip_tags($new_instance['show_start_date']);
		$instance['show_end_date'] = strip_tags($new_instance['show_end_date']);
		$instance['date_threshold'] = strip_tags($new_instance['date_threshold']);
		$instance['only_future'] = strip_tags($new_instance['only_future']);

		//$instance['events'] = empty($instance['events']) ? $this->model->get_events_for($instance['for']) : $instance['events'];

		return $instance;
	}

	function widget($args, $instance) {
		extract($args);
		$title = apply_filters('widget_title', $instance['title']);
		$for = $instance['for'];
		$show_image = (int)$instance['show_image'];
		$show_location = (int)$instance['show_location'];
		$show_start_date = (int)$instance['show_start_date'];
		$show_end_date = (int)$instance['show_end_date'];
		$date_threshold = $instance['date_threshold'];
		$only_future = $instance['only_future'];

		$date_threshold = $date_threshold ? strtotime($date_threshold) : false;
		$now = time();
		if ($only_future) {
			$date_threshold = ($date_threshold && $date_threshold > $now) ? $date_threshold : $now;
		}
/*
		if (
			!@$instance['_last_checked_on'] 
			|| 
			(WDFB_TRANSIENT_TIMEOUT + (int)@$instance['_last_checked_on']) < $now
		) {
			$events = $this->model->get_events_for($for);
			if (!empty($events['data'])) {
				// We have a valid FB connection.
				// Use that to refresh data:
				// Update the instance with fresh events
				$all_instances = $this->get_settings();
				$all_instances[$this->number]['events'] = $events;
				$all_instances[$this->number]['_last_checked_on'] = $now;
				$this->save_settings($all_instances);
			} else {
				$events = $instance['events'];
			}
		} else {
			$events = $instance['events'];
		}
		$events = $events['data'];
*/
		$api = new Wdfb_EventsBuffer;
		$events = $api->get_for($for);

		$timestamp_format = get_option('date_format') . ' ' . get_option('time_format');

		echo $before_widget;
		if ($title) echo $before_title . $title . $after_title;

		if (is_array($events)) {
			echo '<ul class="wdfb_widget_events">';
			foreach ($events as $event) {
				if ($date_threshold > strtotime($event['start_time'])) continue;
				include (WDFB_PLUGIN_BASE_DIR . '/lib/forms/event_item.php');
			}
			echo '</ul>';
		}

		echo $after_widget;
	}
}