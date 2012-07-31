<?php
/**
 * Shows Facebook Connect box.
 */
class Wdfb_WidgetConnect extends WP_Widget {

	function Wdfb_WidgetConnect () {
		$widget_ops = array('classname' => __CLASS__, 'description' => __('Shows Facebook Connect box.', 'wdfb'));

		add_action('wp_print_styles', array($this, 'css_load_styles'));
		add_action('wp_print_scripts', array($this, 'js_load_scripts'));

		parent::WP_Widget(__CLASS__, 'Facebook Connect', $widget_ops);
	}

	function css_load_styles () {
		wp_enqueue_style('wdfb_connect_widget_style', WDFB_PLUGIN_URL . '/css/wdfb_connect_widget.css');
	}
	function js_load_scripts () {
		wp_enqueue_script('wdfb_connect_widget', WDFB_PLUGIN_URL . '/js/wdfb_connect_widget.js');
		wp_enqueue_script('wdfb_facebook_login', WDFB_PLUGIN_URL . '/js/wdfb_facebook_login.js');
	}

	function form($instance) {
		$title = esc_attr($instance['title']);
		$width = esc_attr($instance['width']);
		$avatar_size = esc_attr($instance['avatar_size']);
		$register = esc_attr($instance['register']);

		// Set defaults
		// ...

		$html = '<p>';
		$html .= '<label for="' . $this->get_field_id('title') . '">' . __('Title:', 'wdfb') . '</label>';
		$html .= '<input type="text" name="' . $this->get_field_name('title') . '" id="' . $this->get_field_id('title') . '" class="widefat" value="' . $title . '"/>';
		$html .= '</p>';

		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id('width') . '">' . __('Width:', 'wdfb') . '</label>';
		$html .= '<input type="text" name="' . $this->get_field_name('width') . '" id="' . $this->get_field_id('width') . '" size="3" value="' . $width . '"/>px';
		$html .= __('<p><small>For registration, the recommended width is greater then 240px</small></p>', 'wdfb');
		$html .= '</p>';

		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id('avatar_size') . '">' . __('Avatar size:', 'wdfb') . '</label>';
		$html .= '<input type="text" name="' . $this->get_field_name('avatar_size') . '" id="' . $this->get_field_id('avatar_size') . '" size="3" value="' . $avatar_size . '"/>px';
		$html .= '</p>';

		$html .= '<p>';
		$html .= '<label for="' . $this->get_field_id('register') . '">' . __('Show register:', 'wdfb') . '</label>';
		$html .= '<input type="checkbox" name="' . $this->get_field_name('register') . '" id="' . $this->get_field_id('register') . '" value="1" ' . ($register ? 'checked="checked"' : '') . ' />';
		$html .= '</p>';

		echo $html;
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['width'] = strip_tags($new_instance['width']);
		$instance['avatar_size'] = strip_tags($new_instance['avatar_size']);
		$instance['register'] = strip_tags($new_instance['register']);

		return $instance;
	}

	function widget($args, $instance) {
		extract($args);
		$title = apply_filters('widget_title', $instance['title']);
		$register = (int)@$instance['register'];
		$width = $instance['width'];
		$width = $width ? $width : 250;
		$avatar_size = $instance['avatar_size'];
		$avatar_size = $avatar_size ? $avatar_size : 32;

		$opts = Wdfb_OptionsRegistry::get_instance();
		$register = ($register && get_option('users_can_register')) ? $register : false;

		if ($opts->get_option('wdfb_connect', 'allow_facebook_registration')) {
			echo $before_widget;
			if ($title) echo $before_title . $title . $after_title;

			$user = wp_get_current_user();

			if (!$user->ID) {
				if (!$register) { // Do the simple thing first
					echo '<p class="wdfb_login_button"><fb:login-button scope="' . Wdfb_Permissions::get_permissions() . '" redirect-url="' . wdfb_get_login_redirect() . '" onlogin="_wdfb_notifyAndRedirect();">' . __("Login with Facebook", 'wdfb') . '</fb:login-button></p>';
				} else {
					$fields = wdfb_get_registration_fields();
					$force = ($opts->get_option('wdfb_connect', 'force_facebook_registration') && $opts->get_option('wdfb_connect', 'require_facebook_account'))
						? 'fb_only=true&' : ''
					;
					echo '<div class="wdfb_connect_widget_container">';
					echo '	<div class="wdfb_connect_widget_tabs"><ul class="wdfb_connect_widget_action_links">';
					echo '		<li><a href="#wdfb_connect_widget_login"><span>' . __("Login", 'wdfb') . '</span></a></li>';
					echo '		<li><a href="#wdfb_connect_widget_register"><span>' . __("Register", 'wdfb') . '</span></a></li>';
					echo '	</ul></div>';
					echo '	<div style="clear:both"></div>';
					echo '	<div class="wdfb_connect_target" id="wdfb_connect_widget_login">';
					echo '		<p class="wdfb_login_button"><fb:login-button scope="' . Wdfb_Permissions::get_permissions() . '" redirect-url="' . wdfb_get_login_redirect() . '" onlogin="_wdfb_notifyAndRedirect();">' . __("Login with Facebook", 'wdfb') . '</fb:login-button></p>';
					echo '	</div>';
					echo '	<div class="wdfb_connect_target" id="wdfb_connect_widget_register">';
					echo '	<iframe src="http://www.facebook.com/plugins/registration.php?' . $force .
						        'client_id=' . trim($opts->get_option('wdfb_api', 'app_key')) . '&' .
						        'redirect_uri=' . urlencode(site_url('/wp-signup.php?action=register&fb_register=1')) . '&' .
						        'fields=' . $fields . '&width=' . $width . '&locale=' . wdfb_get_locale() . '"
						        scrolling="auto"
						        frameborder="no"
						        style="border:none; overflow:hidden; width:' . $width . 'px;"
						        allowTransparency="true"
						        width="' . $width . '"
						        height="650">
							</iframe>';
					echo '	</div>';
					echo '</div>';
				}
			} else {
				$logout = site_url('wp-login.php?action=logout&redirect_to=' . rawurlencode(home_url()));
				echo get_avatar($user->ID, $avatar_size);
				echo "<p><a href='{$logout}'>" . __('Log out', 'wdfb') . "</a></p>";
			}

			echo $after_widget;
		}
	}
}