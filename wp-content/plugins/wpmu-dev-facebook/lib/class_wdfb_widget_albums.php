<?php
/**
 * Shows Facebook Albums box.
 */
class Wdfb_WidgetAlbums extends WP_Widget {
	var $model;

	function Wdfb_WidgetAlbums () {
		$this->model = new Wdfb_Model();
		$widget_ops = array('classname' => __CLASS__, 'description' => __('Shows photos from a Facebook Album', 'wdfb'));

		add_action('admin_print_styles-widgets.php', array($this, 'css_load_styles'));
		add_action('admin_print_scripts-widgets.php', array($this, 'js_load_editor'));
		add_action('init', create_function('', 'add_thickbox();'));

		parent::WP_Widget(__CLASS__, 'Facebook Albums', $widget_ops);
	}

	function css_load_styles () {
		wp_enqueue_style('wdfb_album_editor', WDFB_PLUGIN_URL . '/css/wdfb_album_editor.css');
	}
	function js_load_editor () {
		wp_enqueue_script('wdfb_widget_editor_album', WDFB_PLUGIN_URL . '/js/widget_editor_album.js');
		wp_localize_script('wdfb_widget_editor_album', 'l10nWdfbEditor', array(
			'add_fb_photo' => __('Add FB Photo', 'wdfb'),
			'insert_album' => __('Insert album', 'wdfb'),
			'insert_album_photos' => __('Insert album photos', 'wdfb'),
			'insert' => __('Insert', 'wdfb'),
			'go_back' => __('Go back', 'wdfb'),
			'use_this_image' => __('Use this image', 'wdfb'),
			'please_wait' => __('Please, wait...', 'wdfb'),
			'insuficient_perms' => __("Your app doesn't have enough permissions to access your albums", 'wdfb'),
			'grant_perms' => __("Grant needed permissions now", 'wdfb'),
		));
	}

	function form($instance) {
		$title = esc_attr($instance['title']);
		$album_id = esc_attr($instance['album_id']);
		$limit = esc_attr($instance['limit']);
		$per_row = esc_attr($instance['per_row']);
		$img_h = esc_attr($instance['img_h']);
		$img_w = esc_attr($instance['img_w']);

		// Set defaults
		// ...
		$img_w = isset($instance['img_h']) ? $img_w : '75';
		$img_h = isset($instance['img_w']) ? $img_h : '75';
		$img_crop = $instance['img_crop'];
		$fb_open = $instance['fb_open'];

		$html = '';

		$fb_user = $this->model->fb->getUser();
		if (!$fb_user) {
			$html .= '<div class="wdfb_admin_message message">';
			$html .= sprintf(__('You should be logged into your Facebook account when adding this widget. <a href="%s">Click here to do so now</a>, then refresh this page.'), $this->model->fb->getLoginUrl());
			$html .= '</div>';
		} else {
			$html .= '<div class="wdfb_admin_message message">Facebook user ID: ' . $fb_user . '</div>';
		}

		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id('title') . '">' . __('Title:', 'wdfb') . '</label>';
		$html .= '<input type="text" name="' . $this->get_field_name('title') . '" id="' . $this->get_field_id('title') . '" class="widefat" value="' . $title . '"/>';
		$html .= '</p>';

		$html .= '<p class="wdfb_album_widget_select_album">';
		$html .= '<label for="' . $this->get_field_id('album_id') . '">' . __('Album ID:', 'wdfb') . '</label>';
		$html .= '<input type="text" name="' . $this->get_field_name('album_id') . '" id="' . $this->get_field_id('album_id') . '" value="' . $album_id . '"/>';
		$html .= '<a href="#" class="wdfb_widget_open_editor">' . __('Select album') . '</a>';
		$html .= '</p>';

		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id('limit') . '">' . __('Display only this many photos:', 'wdfb') . '</label>';
		$html .= '<select name="' . $this->get_field_name('limit') . '" id="' . $this->get_field_id('limit') . '">';
		for ($i=1; $i<51; $i++) {
			$html .= '<option value="' . $i . '" ' . (($limit == $i) ? 'selected="selected"' : '') . '>' . $i . '</option>';
		}
		$html .= '</select>';
		$html .= '</p>';

		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id('per_row') . '">' . __('Photos per row:', 'wdfb') . '</label>';
		$html .= '<select name="' . $this->get_field_name('per_row') . '" id="' . $this->get_field_id('per_row') . '">';
		for ($i=1; $i<10; $i++) {
			$html .= '<option value="' . $i . '" ' . (($per_row == $i) ? 'selected="selected"' : '') . '>' . $i . '</option>';
		}
		$html .= '</select>';
		$html .= '</p>';

		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id('img_w') . '">' . __('Image size:', 'wdfb') . '</label>';
		$html .= '<input type="text" name="' . $this->get_field_name('img_w') . '" size="2" maxsize="3" id="' . $this->get_field_id('img_w') . '" value="' . $img_w . '"/>';
		$html .= 'x';
		$html .= '<input type="text" name="' . $this->get_field_name('img_h') . '" size="2" maxsize="3" id="' . $this->get_field_id('img_h') . '" value="' . $img_h . '"/>';
		$html .= '<br /><small>' . __('Format: width x height. <br />Omitting one of dimensions will show your images in scale.', 'wdfb')  . '</small>';
		$html .= '</p>';

		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id('img_crop') . '">' . __('Crop image height:', 'wdfb') . '</label> ';
		$html .= '<input type="checkbox" name="' . $this->get_field_name('img_crop') . '" id="' . $this->get_field_id('img_crop') . '" value="1" ' . ($img_crop ? 'checked="checked"' : '') . ' />';
		$html .= '<br /><small>' . __('Selecting this option will crop your oversized images vertically.', 'wdfb')  . '</small>';
		$html .= '</p>';

		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id('fb_open') . '">' . __('Link to image on Facebook:', 'wdfb') . '</label> ';
		$html .= '<input type="checkbox" name="' . $this->get_field_name('fb_open') . '" id="' . $this->get_field_id('fb_open') . '" value="1" ' . ($fb_open ? 'checked="checked"' : '') . ' />';
		$html .= '<br /><small>' . __('By default, your images will be linked to their full size sources. Selecting this option will link your images to Facebook ones.', 'wdfb')  . '</small>';
		$html .= '</p>';

		echo $html;
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['album_id'] = strip_tags($new_instance['album_id']);
		$instance['limit'] = strip_tags($new_instance['limit']);
		$instance['per_row'] = strip_tags($new_instance['per_row']);
		$instance['img_w'] = strip_tags($new_instance['img_w']);
		$instance['img_h'] = strip_tags($new_instance['img_h']);
		$instance['img_crop'] = isset($new_instance['img_crop']) ? 1 : 0;
		$instance['fb_open'] = isset($new_instance['fb_open']) ? 1 : 0;

		//$instance['photos'] = empty($instance['photos']) ? $this->model->get_album_photos($instance['album_id'], $limit) : $instance['photos'];

		return $instance;
	}

	function widget($args, $instance) {
		extract($args);
		$title = apply_filters('widget_title', $instance['title']);
		$limit = (int)$instance['limit'];
		$per_row = (int)$instance['per_row'];
		$img_w = (int)$instance['img_w'];
		$img_h = (int)$instance['img_h'];
		$img_crop = (int)$instance['img_crop'];
		$fb_open = (int)$instance['fb_open'];
		$album_id = $instance['album_id'];

		$api = new Wdfb_AlbumPhotosBuffer;
		$photos = $api->get_for($album_id);

		echo $before_widget;
		if ($title) echo $before_title . $title . $after_title;

		if (is_array($photos)) {
			echo '<table cellspacing="0" cellpadding="0" border="0" class="wdfb_album_photos">';
			$count = $overall = 0;
			echo '<tr>';
			foreach ($photos as $photo) {
				if ($overall >= $limit) break;
				$style = $img_crop ? "display:block;float:left;height:{$img_h}px;overflow:hidden" : '';
				$url = $fb_open
					? 'http://www.facebook.com/photo.php?fbid=' . $photo['id']
					: $photo['images'][0]['source']
				;
				echo '<td valign="top">' .
					'<a href="' . $url . '" style="' . $style . '">' .
						'<img src="' . $photo['images'][count($photo['images'])-1]['source'] . '" ' .
						($img_w ? "width='{$img_w}'" : '') .
						($img_h && !$img_crop ? "height='{$img_h}'" : '') .
						' />' .
					'</a>' .
				'</td>';
				++$count; ++$overall;
				if ($count == $per_row) {
					echo '</tr><tr>';
					$count = 0;
				}
			}
			if ($count < $per_row) {
				echo '<td colspan="' . ($per_row-$count) . '"></td>';
			}
			echo '<tr>';
			echo '</table>';
		}

		echo $after_widget;
	}
}