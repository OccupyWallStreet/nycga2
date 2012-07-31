<?php
/**
 * Shows recently imported Facebook comments.
 */
class Wdfb_WidgetRecentComments extends WP_Widget {

	function Wdfb_WidgetRecentComments () {
		$widget_ops = array('classname' => __CLASS__, 'description' => __('Shows your recently imported Facebook comments', 'wdfb'));
		parent::WP_Widget(__CLASS__, 'Facebook recent comments', $widget_ops);
	}

	function form($instance) {
		$title = esc_attr(@$instance['title']);
		$limit = esc_attr(@$instance['limit']);
		$avatar_size = esc_attr(@$instance['avatar_size']);
		$hide_text = esc_attr(@$instance['hide_text']);

		// Set defaults
		// ...
		$html = '';
		$limit = $limit ? $limit : 5;
		
		// Sanity check
		$data =& Wdfb_OptionsRegistry::get_instance();
		if (!$data->get_option('wdfb_comments', 'import_fb_comments')) {
			$html .= '<div class="error below-h2"><p>' . 
				__('Your comments are currently not being imported from Facebook. This will make the widget show stale data, if any.', 'wdfb') . 
			'</p></div>';
		}
		

		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id('title') . '">' . __('Title:', 'wdfb') . '</label>';
		$html .= '<input type="text" name="' . $this->get_field_name('title') . '" id="' . $this->get_field_id('title') . '" class="widefat" value="' . $title . '"/>';
		$html .= '</p>';
		
		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id('limit') . '">' . __('Display only this many comments:', 'wdfb') . '</label>';
		$html .= '<select name="' . $this->get_field_name('limit') . '" id="' . $this->get_field_id('limit') . '">';
		for ($i=1; $i<21; $i++) {
			$html .= '<option value="' . $i . '" ' . (($limit == $i) ? 'selected="selected"' : '') . '>' . $i . '</option>';
		}
		$html .= '</select>';
		$html .= '</p>';

		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id('avatar_size') . '">' . __('Avatar size:', 'wdfb') . '</label>';
		$html .= '<select name="' . $this->get_field_name('avatar_size') . '" id="' . $this->get_field_id('avatar_size') . '">';
		for ($i=15; $i<125; $i+=5) {
			$html .= '<option value="' . $i . '" ' . (($avatar_size == $i) ? 'selected="selected"' : '') . '>' . $i . '</option>';
		}
		$html .= '</select>px';
		$html .= '<br />';
		$checked = (!$avatar_size) ? 'checked="checked"' : '';
		$html .= '<input type="checkbox" ' . $checked . ' name="' . $this->get_field_name('avatar_size') . '" id="' .  $this->get_field_id('avatar_show'). '" value="0" /> ';
		$html .= '<label for="' . $this->get_field_id('avatar_show') . '">' . __('Do not show avatars', 'wdfb') . '</label>';
		$html .= '</p>';
		
		$html .= '<p>';
		$checked = $hide_text ? 'checked="checked"' : '';
		$html .= '<input type="checkbox" ' . $checked . ' name="' . $this->get_field_name('hide_text') . '" id="' . $this->get_field_id('hide_text') . '" value="1" />';
		$html .= '&nbsp;<label for="' . $this->get_field_id('hide_text') . '">' . __("Do not show comment text", 'wdfb') . '</label>';
		$html .= '</p>';

		echo $html;
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['limit'] = strip_tags($new_instance['limit']);
		$instance['avatar_size'] = strip_tags($new_instance['avatar_size']);
		$instance['hide_text'] = strip_tags($new_instance['hide_text']);

		return $instance;
	}

	function widget($args, $instance) {
		extract($args);
		$title = apply_filters('widget_title', $instance['title']);
		$limit = (int)@$instance['limit'];
		$size = (int)@$instance['avatar_size'];
		$hide_text = (int)@$instance['hide_text'];
		
		$limit = $limit ? $limit : 5;

		echo $before_widget;
		if ($title) echo $before_title . $title . $after_title;

		$comments = $this->_get_comments($limit);
		echo '<ul class="wdfb-recent_facebook_comments">';
		foreach ($comments as $comment) {
			$meta = unserialize($comment->meta_value);
			echo '<li>';
			
			echo '<div class="wdfb-comment_author vcard">';
			if ($size) {
				echo '<img src="http://graph.facebook.com/' . esc_attr($meta['fb_author_id']) . '/picture" class="avatar avatar-' . $size . ' photo" height="' . $size . '" width="' . $size . '" />';
			}
			echo '<cite class="fn"><a href="http://www.facebook.com/profile.php?id=' . esc_attr($meta['fb_author_id']) . '">' . esc_html($comment->comment_author) . '</a></cite>';
			echo '</div>';
			
			if (!$hide_text) {
				echo '<div class="wdfb-comment_body">';
				echo esc_html($comment->comment_content);
				echo '</div>';
			}
			
			echo '<div class="wdfb-comment_meta">';
			echo mysql2date(get_option('date_format') . ' ' . get_option('time_format'), $comment->comment_date);
			echo __('&nbsp;on&nbsp;', 'wdfb');
			echo '<a href="' . get_permalink($comment->comment_post_ID) . '">' . get_the_title($comment->comment_post_ID) . '</a>';
			echo '</div>';
			
			echo '</li>';
		}
		echo '</ul>';
		
		echo $after_widget;
	}

	function _get_comments ($limit=5) {
		global $wpdb;
		$limit = (int)$limit;
		$limit = $limit ? $limit : 5;
		
		return $wpdb->get_results("SELECT * FROM {$wpdb->comments} AS c, {$wpdb->commentmeta} AS mc WHERE mc.meta_key='wdfb_comment' AND c.comment_ID=mc.comment_id ORDER BY c.comment_date LIMIT {$limit}");
	}
}