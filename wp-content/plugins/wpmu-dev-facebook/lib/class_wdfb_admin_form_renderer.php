<?php
/**
 * Handles rendering of form elements for plugin Options page.
 */
class Wdfb_AdminFormRenderer {

	function _get_option ($name) {
		return WP_NETWORK_ADMIN ? get_site_option($name) : get_option($name);
	}

	function _create_text_box ($pfx, $name, $value) {
		return "<input type='text' class='widefat' name='wdfb_{$pfx}[{$name}]' id='{$name}' value='{$value}' />";
	}
	function _create_small_text_box ($pfx, $name, $value) {
		return "<input type='text' name='wdfb_{$pfx}[{$name}]' id='{$name}' size='3' value='{$value}' />";
	}
	function _create_checkbox ($pfx, $name, $value) {
		return "<input type='hidden' name='wdfb_{$pfx}[{$name}]' value='0' />" . 
			"<input type='checkbox' name='wdfb_{$pfx}[{$name}]' id='{$name}' value='1' " . ($value ? 'checked="checked" ' : '') . " /> "
		;
	}
	function _create_subcheckbox ($pfx, $name, $value, $checked) {
		return "<input type='hidden' name='wdfb_{$pfx}[{$name}][{$value}]' value='0' />" . 
			"<input type='checkbox' name='wdfb_{$pfx}[{$name}][{$value}]' id='{$name}-{$value}' value='{$value}' " . ($checked ? 'checked="checked" ' : '') . " /> ";
	}

	function next_step () {
		//echo "<input type='button' class='wdfb_next_step' value='" . __('Next step', 'wdfb') . "' />";
	}

	function _create_widget_box ($widget, $description) {
		$opt = $this->_get_option('wdfb_widget_pack');
		echo $this->_create_checkbox('widget_pack', "{$widget}_allowed", @$opt["{$widget}_allowed"]);
		echo "<div class='wdfb_widget_description'>{$description}</div>";
	}

	function api_info () {
		printf(__(
			'<p><b>This step must be completed before using the plugin. You must make a Facebook Application to continue.</b></p>' .
			'<p>Before we begin, you need to <a target="_blank" href="http://www.facebook.com/developers/createapp.php">create a Facebook Application</a>.</p>' .
			'<p>To do so, follow these steps:</p>' .
			'<ol>' .
				'<li><a target="_blank" href="http://www.facebook.com/developers/createapp.php">Create your application</a></li>' .
				'<li>Look for <strong>Site URL</strong> field in the <em>Web Site</em> tab and enter your site URL in this field: <code>%s</code></li>' .
				'<li>After this, go to the <a target="_blank" href="http://www.facebook.com/developers/apps.php">Facebook Application List page</a> and select your newly created application</li>' .
				'<li>Copy the values from these fields: <strong>App ID</strong>/<strong>API key</strong>, and <strong>Application Secret</strong>, and enter them here:</li>' .
			'</ol>' .
			'<p>Once you\'re done with that, please click the "Save changes" button below before proceeding onto other steps.<p>',
		'wdfb'),
			get_bloginfo('url')
		);
		echo '<div class="wdfb-api_connect-result">' . 
			__('Checking API settings...', 'wdfb') .
			'&nbsp;' .
			'<img src="' . WDFB_PLUGIN_URL . '/img/waiting.gif" />' . 
		'</div>';
	}

	function api_permissions () {
		printf(__(
			'<p>Some parts of the plugin will require you to grant them extended permissions on Facebook. If you haven\'t done so already, it is highly recommended you do so now:</p>',
		'wdfb'));
		echo '<div class="wdfb_perms_root" style="display:none">' .
			'<p class="wdfb_perms_granted">' .
				'<span class="wdfb_message">' . __('You already granted extended permissions', 'wdfb') . '</span> ' .
			'</p>' .
			'<p class="wdfb_perms_not_granted">' .
				'<a href="#" class="wdfb_grant_perms" wdfb:locale="' . wdfb_get_locale() . '" wdfb:perms="' . Wdfb_Permissions::get_permissions() . '">' . __('Grant extended permissions', 'wdfb') . '</a>' .
			'</p>' .
		'</div>';
		echo '<script type="text/javascript" src="' . WDFB_PLUGIN_URL . '/js/check_permissions.js"></script>';
	}

	function create_api_key_box () {
		$opt = $this->_get_option('wdfb_api');
		echo $this->_create_text_box('api', 'api_key', @$opt['api_key']);
	}
	function create_app_key_box () {
		$opt = $this->_get_option('wdfb_api');
		echo $this->_create_text_box('api', 'app_key',  @$opt['app_key']);
	}
	function create_secret_key_box () {
		$opt = $this->_get_option('wdfb_api');
		echo $this->_create_text_box('api', 'secret_key',  @$opt['secret_key']);
	}
	function create_locale_box () {
		$fb_locales = array(
			__("Automatic detection", 'wdfb') => '',
			"Afrikaans" => "af_ZA",
			"Arabic" => "ar_AR",
			"Azeri" => "az_AZ",
			"Belarusian" => "be_BY",
			"Bulgarian" => "bg_BG",
			"Bengali" => "bn_IN",
			"Bosnian" => "bs_BA",
			"Catalan" => "ca_ES",
			"Czech" => "cs_CZ",
			"Welsh" => "cy_GB",
			"Danish" => "da_DK",
			"German" => "de_DE",
			"Greek" => "el_GR",
			"English (UK)" => "en_GB",
			"English (Pirate)" => "en_PI",
			"English (Upside Down)" => "en_UD",
			"English (US)" => "en_US",
			"Esperanto" => "eo_EO",
			"Spanish (Spain)" => "es_ES",
			"Spanish" => "es_LA",
			"Estonian" => "et_EE",
			"Basque" => "eu_ES",
			"Persian" => "fa_IR",
			"Leet Speak" => "fb_LT",
			"Finnish" => "fi_FI",
			"Faroese" => "fo_FO",
			"French (Canada)" => "fr_CA",
			"French (France)" => "fr_FR",
			"Frisian" => "fy_NL",
			"Irish" => "ga_IE",
			"Galician" => "gl_ES",
			"Hebrew" => "he_IL",
			"Hindi" => "hi_IN",
			"Croatian" => "hr_HR",
			"Hungarian" => "hu_HU",
			"Armenian" => "hy_AM",
			"Indonesian" => "id_ID",
			"Icelandic" => "is_IS",
			"Italian" => "it_IT",
			"Japanese" => "ja_JP",
			"Georgian" => "ka_GE",
			"Korean" => "ko_KR",
			"Kurdish" => "ku_TR",
			"Latin" => "la_VA",
			"Lithuanian" => "lt_LT",
			"Latvian" => "lv_LV",
			"Macedonian" => "mk_MK",
			"Malayalam" => "ml_IN",
			"Malay" => "ms_MY",
			"Norwegian (bokmal)" => "nb_NO",
			"Nepali" => "ne_NP",
			"Dutch" => "nl_NL",
			"Norwegian (nynorsk)" => "nn_NO",
			"Punjabi" => "pa_IN",
			"Polish" => "pl_PL",
			"Pashto" => "ps_AF",
			"Portuguese (Brazil)" => "pt_BR",
			"Portuguese (Portugal)" => "pt_PT",
			"Romanian" => "ro_RO",
			"Russian" => "ru_RU",
			"Slovak" => "sk_SK",
			"Slovenian" => "sl_SI",
			"Albanian" => "sq_AL",
			"Serbian" => "sr_RS",
			"Swedish" => "sv_SE",
			"Swahili" => "sw_KE",
			"Tamil" => "ta_IN",
			"Telugu" => "te_IN",
			"Thai" => "th_TH",
			"Filipino" => "tl_PH",
			"Turkish" => "tr_TR",
			"Ukrainian" => "uk_UA",
			"Vietnamese" => "vi_VN",
			"Simplified Chinese (China)" => "zh_CN",
			"Traditional Chinese (Hong Kong)" => "zh_HK",
			"Traditional Chinese (Taiwan)" => "zh_TW",
		);
		$api = $this->_get_option('wdfb_api');
		echo "<select id='wdfb-api_locale' name='wdfb_api[locale]'>";
		foreach ($fb_locales as $label => $locale) {
			$checked = ($locale == @$api['locale']) ? 'selected="selected"' : '';
			echo "<option value='{$locale}' {$checked}>{$label}</option>";
		}
		echo "</select>";
		echo '<div><small>' . __('By default, the plugin will try to auto-detect your locale settings and load the appropriate API scripts. However, not all locales are supported by Facebook, so your milleage may vary. With this option, you can explicitly tell which language you want to use for communicating with Facebook.', 'wdfb') . '</small></div>';
	}
	function create_prevent_access_box () {
		$opt = $this->_get_option('wdfb_api');
		echo $this->_create_checkbox('api', 'prevent_linked_accounts_access',  @$opt['prevent_linked_accounts_access']);
		echo '<div><small>' . __('If this option is set, your linked accounts (e.g. pages) will <b>NOT</b> be accessible by this plugin', 'wdfb') . '</small></div>';
	}
	function create_allow_propagation_box () {
		$opt = $this->_get_option('wdfb_api');
		echo $this->_create_checkbox('api', 'allow_propagation',  @$opt['allow_propagation']);
	}

	function create_allow_facebook_registration_box () {
		$opt = $this->_get_option('wdfb_connect');
		echo $this->_create_checkbox('connect', 'allow_facebook_registration',  @$opt['allow_facebook_registration']);
	}
	function create_force_facebook_registration_box () {
		$opt = $this->_get_option('wdfb_connect');
		echo $this->_create_checkbox('connect', 'force_facebook_registration',  @$opt['force_facebook_registration']);
		echo '<span id="wdfb-force_facebook_registration-help"></span>';
		echo '<br />';
		echo $this->_create_checkbox('connect', 'require_facebook_account', @$opt['require_facebook_account']);
		echo ' <label for="require_facebook_account">' . __('Require Facebook account', 'wdfb') . '</label>';
		echo '<div><small>' . __('By default, Facebook registration form will allow your users to register with their Facebook account, or with their chosen usernames and emails.', 'wdfb') . '</small></div>';
		echo '<div><small>' . __('Check this if users will need to have a Facebook account already.', 'wdfb') . '</small></div>';
	}
	function create_no_main_site_registration_box () {
		$opt = $this->_get_option('wdfb_connect');
		echo $this->_create_checkbox('connect', 'no_main_site_registration',  @$opt['no_main_site_registration']);
		echo '<div><small>' . __('This option does not apply to single-click registration.', 'wdfb') . '</small></div>';
	}
	function create_easy_facebook_registration_box () {
		$opt = $this->_get_option('wdfb_connect');
		echo $this->_create_checkbox('connect', 'easy_facebook_registration',  @$opt['easy_facebook_registration']);
		echo '<div><small>' . __('If enabled, the "Login with Facebook" button will work as a single-click register button for your new users', 'wdfb') . '</small></div>';
	}
	function create_login_redirect_box () {
		$opt = $this->_get_option('wdfb_connect');
		$base = @$opt['login_redirect_base'] ? @$opt['login_redirect_base'] : 'site_url';
		$base = ($base == 'site_url') ? 'site_url' : 'admin_url';
		$allowed = array(
			__('Site URL', 'wdfb') => 'site_url',
			__('Admin URL', 'wdfb') => 'admin_url',
		);
		$url = @$opt['login_redirect_url'] ? '<code>' . apply_filters('wdfb-login-redirect_url', $base(@$opt['login_redirect_url'])) . '</code>' : __('current page, or plugin default', 'wdfb');

		echo "<select name='wdfb_connect[login_redirect_base]'>";
		foreach ($allowed as $label => $item) {
			$checked = ($base == $item) ? 'selected="selected"' : '';
			echo "<option value='{$item}' {$checked}>{$label}</option>";
		}
		echo "</select><span id='wdfb-login_redirect_base-help'></span>";
		echo '<div><small id="wdfb-login_redirect_base-url_fragment">' . sprintf(__('Select your site area (above), then fill in the relative URL fragment:', 'wdfb'), $url) . '</small></div>';
		
		echo $this->_create_text_box('connect', 'login_redirect_url', @$opt['login_redirect_url']);
		echo '<div><small>' . sprintf(__('This is what will happen upon login: my users will be redirected to %s.', 'wdfb'), $url) . '</small></div>';
	}
	function create_captcha_box () {
		$opt = $this->_get_option('wdfb_connect');
		echo $this->_create_checkbox('connect', 'no_captcha',  @$opt['no_captcha']);
	}
	function create_buddypress_registration_fields_box () {
		$opt = $this->_get_option('wdfb_connect');
		$fb_fields = array (
			'_nothing' => __('Nothing', 'wdfb'),
			'name' => __('Name', 'wdfb'),
			'first_name' => __('First name', 'wdfb'),
			'middle_name' => __('Middle name', 'wdfb'),
			'last_name' => __('Last name', 'wdfb'),
			'gender' => __('Gender', 'wdfb'),
			'bio' => __('Bio', 'wdfb'),
			'birthday' => __('Birthday', 'wdfb'),
			'about' => __('About', 'wdfb'),
			'hometown' => __('Hometown', 'wdfb'),
			'location' => __('Location', 'wdfb'),
			'link' => __('Facebook profile', 'wdfb'),
			'locale' => __('Locale', 'wdfb'),
			'languages' => __('Languages', 'wdfb'),
			'username' => __('Facebook username', 'wdfb'),
			'email' => __('Email', 'wdfb'),
			'relationship_status' => __('Relationship status', 'wdfb'),
			'significant_other' => __('Significant other', 'wdfb'),
			'political' => __('Political view', 'wdfb'),
			'religion' => __('Religion', 'wdfb'),
			'favorite_teams' => __('Favorite teams', 'wdfb'),
			'quotes' => __('Favorite quotes', 'wdfb'),
		);
		$model = new Wdfb_Model;
		$bp_fields = $model->get_bp_xprofile_fields();
		if (!is_array($bp_fields)) return '';

		foreach ($bp_fields as $bpf) {
			_e(sprintf('Map %s to', $bpf['name']), 'wdfb');
			echo ' <select name="wdfb_connect[buddypress_registration_fields_' . $bpf['id'] . ']">';
			foreach ($fb_fields as $fbf_key=>$fbf_label) {
				echo '<option value="' . $fbf_key . '" ' . ((@$opt['buddypress_registration_fields_' . $bpf['id']] == $fbf_key) ? 'selected="selected"' : '') . ' >' . $fbf_label . '</option>';
			}
			echo '</select><br />';
		}
	}
	function create_wordpress_registration_fields_box () {
		$opt = $this->_get_option('wdfb_connect');
		$fb_fields = array (
			'_nothing' => __('Nothing', 'wdfb'),
			'name' => __('Name', 'wdfb'),
			'first_name' => __('First name', 'wdfb'),
			'middle_name' => __('Middle name', 'wdfb'),
			'last_name' => __('Last name', 'wdfb'),
			'gender' => __('Gender', 'wdfb'),
			'bio' => __('Bio', 'wdfb'),
			'birthday' => __('Birthday', 'wdfb'),
			'about' => __('About', 'wdfb'),
			'hometown' => __('Hometown', 'wdfb'),
			'location' => __('Location', 'wdfb'),
			'link' => __('Facebook profile', 'wdfb'),
			'locale' => __('Locale', 'wdfb'),
			'languages' => __('Languages', 'wdfb'),
			'username' => __('Facebook username', 'wdfb'),
			'email' => __('Email', 'wdfb'),
			'relationship_status' => __('Relationship status', 'wdfb'),
			'significant_other' => __('Significant other', 'wdfb'),
			'political' => __('Political view', 'wdfb'),
			'religion' => __('Religion', 'wdfb'),
			'favorite_teams' => __('Favorite teams', 'wdfb'),
			'quotes' => __('Favorite quotes', 'wdfb'),
		);

		// Set up default mapping
		$wp_defaults = array (
			array('wp' => 'first_name', 'fb' => 'first_name'),
			array('wp' => 'last_name', 'fb' => 'last_name'),
			array('wp' => 'description', 'fb' => 'bio'),
		);
		$wp_reg = @$opt['wordpress_registration_fields'];
		$wp_reg = is_array($wp_reg) ? $wp_reg : $wp_defaults;

		echo '<div id="wdfb_connect_wp_registration_container">';
		foreach ($wp_reg as $idx => $reg) {
			$wp_box = $fb_box = '';
			$wp_box = '<input type="text" size="12" maxsize="32" name="wdfb_connect[wordpress_registration_fields][' . $idx . '][wp]" value="' . $reg['wp'] . '" />';
			$fb_box = '<select name="wdfb_connect[wordpress_registration_fields][' . $idx . '][fb]">';
			foreach ($fb_fields as $fbf_key=>$fbf_label) {
				$fb_box .= '<option value="' . $fbf_key . '" ' . ((@$reg['fb'] == $fbf_key) ? 'selected="selected"' : '') . '>' . $fbf_label . '</option>';
			}
			$fb_box .= '</select>';
			printf(
				'<div class="wdfb_connect_wp_registration">' . __('Map %s to %s', 'wdfb') . '</div>',
				$wp_box, $fb_box
			);
		}
		echo '</div>';
		echo '<p><input type="button" id="wdfb_connect_add_mapping" value="' . __('Add another mapping', 'wdfb') . '" /></p>';

		/*
		global $wdfb_wp_profile_fields;

		foreach ($wdfb_wp_profile_fields as $wpf_id => $wpf_label) {
			_e(sprintf('Map %s to', $wpf_label), 'wdfb');
			echo ' <select name="wdfb_connect[wordpress_registration_fields_' . $wpf_id . ']">';
			foreach ($fb_fields as $fbf_key=>$fbf_label) {
				echo '<option value="' . $fbf_key . '" ' . ((@$opt['wordpress_registration_fields_' . $wpf_id] == $fbf_key) ? 'selected="selected"' : '') . '">' . $fbf_label . '</option>';
			}
			echo '</select><br />';
		}
		*/
	}

	function create_allow_facebook_button_box () {
		$opt = $this->_get_option('wdfb_button');
		echo $this->_create_checkbox('button', 'allow_facebook_button',  @$opt['allow_facebook_button']);
	}
	function create_show_send_button_box () {
		$opt = $this->_get_option('wdfb_button');
		echo $this->_create_checkbox('button', 'show_send_button',  @$opt['show_send_button']);
	}
	function create_show_on_front_page_box () {
		$opt = $this->_get_option('wdfb_button');
		echo $this->_create_checkbox('button', 'show_on_front_page',  @$opt['show_on_front_page']);
	}
	function create_do_not_show_button_box () {
		$opt = $this->_get_option('wdfb_button'); // 'not_in_post_types'
		$types = get_post_types(array('public'=>true), 'objects');
		foreach ($types as $type) {
			echo $this->_create_subcheckbox('button', 'not_in_post_types',  $type->name, @in_array($type->name, $opt['not_in_post_types']));
			echo '<label>' . ucfirst($type->labels->name) . '</label><br />';
		}
	}
	function create_button_position_box () {
		$opt = $this->_get_option('wdfb_button');
		$positions = array('top' => __('Before', 'wdfb'), 'bottom' => __('After', 'wdfb'), 'both' => __('Before and after', 'wdfb'), 'manual' => __('Manual, use shortcodes in ', 'wdfb'));

		foreach ($positions as $pos => $label) {
			echo '<input type="radio" name="wdfb_button[button_position]" value="' . $pos . '" ' . (($opt['button_position'] == $pos) ? 'checked="checked"' : '') . ' /> ';
			printf(__("%s the contents of your post <br />", 'wdfb'), $label);
		}
	}
	function create_button_appearance_box () {
		$opt = $this->_get_option('wdfb_button');
		$blog_uri = get_option('siteurl');
		$send_value = @$opt['show_send_button'] ? 'true' : 'false';

		echo "<table border='0'>";

		echo '<tr>';
		echo '<td valign="top"><input type="radio" name="wdfb_button[button_appearance]" value="standard" ' . (($opt['button_appearance'] == "standard") ? 'checked="checked"' : '') . ' /></td>';
		echo '<td valign="top"><iframe src="http://www.facebook.com/plugins/like.php?href=' . $blog_uri . '&amp;send=false&amp;layout=standard&amp;width=450&amp;show_faces=true&amp;action=like&amp;colorscheme=light&amp;font&amp;height=80" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:450px; height:25px;" allowTransparency="true"></iframe></td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td valign="top"><input type="radio" name="wdfb_button[button_appearance]" value="button_count" ' . (($opt['button_appearance'] == "button_count") ? 'checked="checked"' : '') . ' /></td>';
		echo '<td valign="top"><iframe src="http://www.facebook.com/plugins/like.php?href=' . $blog_uri . '&amp;send=false&amp;layout=button_count&amp;width=450&amp;show_faces=true&amp;action=like&amp;colorscheme=light&amp;font&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:450px; height:21px;" allowTransparency="true"></iframe></td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td valign="top"><input type="radio" name="wdfb_button[button_appearance]" value="box_count" ' . (($opt['button_appearance'] == "box_count") ? 'checked="checked"' : '') . ' /></td>';
		echo '<td valign="top"><iframe src="http://www.facebook.com/plugins/like.php?href=' . $blog_uri . '&amp;send=false&amp;layout=box_count&amp;width=450&amp;show_faces=true&amp;action=like&amp;colorscheme=light&amp;font&amp;height=65" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:50px; height:65px;" allowTransparency="true"></iframe></td>';
		echo '</tr>';

		echo "</table>";
	}

	function create_use_opengraph_box () {
		$opt = $this->_get_option('wdfb_opengraph');
		echo $this->_create_checkbox('opengraph', 'use_opengraph',  @$opt['use_opengraph']);
		echo '<div><small>' . __("OpenGraph allows you better control over what is shared on Facebook when someone clicks \"Like/Send\" button.", 'wdfb') . '</small></div>';
	}
	function create_always_use_image_box () {
		$opt = $this->_get_option('wdfb_opengraph');
		echo $this->_create_text_box('opengraph', 'always_use_image',  @$opt['always_use_image']);
		if (@$opt['always_use_image']) {
			printf(__('<p>Preview:<br /><img src="%s" /></p>', 'wdfb'), @$opt['always_use_image']);
		}
		echo '<div><small>' . __("Please, use full URL to image (e.g. <code>http://example.com/images/example.jpg</code>). If set, this image will <b>always</b> be used on Facebook for content sharing", 'wdfb') . '</small></div>';
	}
	function create_fallback_image_box () {
		$opt = $this->_get_option('wdfb_opengraph');
		echo $this->_create_text_box('opengraph', 'fallback_image',  @$opt['fallback_image']);
		if (@$opt['fallback_image']) {
			printf(__('<p>Preview:<br /><img src="%s" /></p>', 'wdfb'), @$opt['fallback_image']);
		}
		echo '<div><small>' . __("By default, we will attempt to use featured image or first image from the post for sharing on Facebook.", 'wdfb') . '</small></div>';
		echo '<div><small>' . __("If we fail, this image will be used instead. Please, use full URL to image (e.g. <code>http://example.com/images/example.jpg</code>).", 'wdfb') . '</small></div>';
	}
	function create_og_type_box () {
		$opt = $this->_get_option('wdfb_opengraph');

		echo $this->_create_checkbox('opengraph', 'og_custom_type', @$opt['og_custom_type']);
		echo '<label for="og_custom_type">' . __('Do not autodetect my OpenGraph type, I will set it manually', 'wdfb') . '</label> ';
		echo '<div><small>' . __("If you check this option the plugin will not autodetect your OpenGraph type and you'll be able to set it manually.", 'wdfb') . '</small></div>';

		echo '<div id="og_custom_mapping">';
		echo '<label for="og_custom_type_front_page">' . __('OpenGraph type on my front page', 'wdfb') . '</label> ';
		echo $this->_create_text_box('opengraph', 'og_custom_type_front_page', @$opt['og_custom_type_front_page']);

		echo '<label for="og_custom_type_not_singular">' . __('OpenGraph type on my taxonomies pages', 'wdfb') . '</label> ';
		echo $this->_create_text_box('opengraph', 'og_custom_type_not_singular', @$opt['og_custom_type_not_singular']);
		echo '<div><small>' . __("This type will be used on your pages that show multiple content (e.g. archives, tags, categories).", 'wdfb') . '</small></div>';

		echo '<label for="og_custom_type_singular">' . __('OpenGraph type on my content pages', 'wdfb') . '</label> ';
		echo $this->_create_text_box('opengraph', 'og_custom_type_singular', @$opt['og_custom_type_singular']);
		echo '<div><small>' . __("This type will be used on your pages that show individual content pieces (e.g. posts and pages).", 'wdfb') . '</small></div>';

		echo '</div>';
	}
	function create_og_extras_box () {
		$opt = $this->_get_option('wdfb_opengraph');
		$extras = @$opt['og_extra_headers'] ? $opt['og_extra_headers'] : array();

		foreach ($extras as $idx=>$extra) {
			$name = esc_attr(@$extra['name']);
			if (!$name) continue;
			$value = esc_attr(@$extra['value']);
			echo '<div class="wdfb_og_extra_mapping">';
			echo "<label for='og_extra_name-{$idx}'>" . __('OpenGraph name:', 'wdfb') . '</label> ' .
				"<input id='og_extra_name-{$idx}' size='24' name='wdfb_opengraph[og_extra_headers][{$idx}][name]' value='{$name}' />" .
			"&nbsp;&nbsp;&nbsp;";
			echo "<label for='og_extra_value-{$idx}'>" . __('OpenGraph value:', 'wdfb') . '</label> ' .
				"<input id='og_extra_value-{$idx}' size='24' name='wdfb_opengraph[og_extra_headers][{$idx}][value]' value='{$value}' />" .
			"&nbsp;&nbsp;&nbsp;";
			echo "<a href='' class='wdfb_og_remove_extra'>" . __('Remove', 'wdfb') . '</a>';
			echo "</div>";
		}
		$last = count($extras);
		echo '<div class="wdfb_og_extra_mapping">';
			echo "<label for='og_extra_name'>" . __('OpenGraph name:', 'wdfb') . '</label> ' .
				"<input id='og_extra_name' size='24' name='wdfb_opengraph[og_extra_headers][{$last}][name]' value='' />" .
			"&nbsp;&nbsp;&nbsp;";
			echo "<label for='og_extra_value-'>" . __('OpenGraph value:', 'wdfb') . '</label> ' .
				"<input id='og_extra_value' size='24' name='wdfb_opengraph[og_extra_headers][{$last}][value]' value='' />" .
			"";
			echo "</div>";
		echo '<input type="button" class="wdfb-save_settings" data-wdfb_section_id="wdfb_opengraph" value="' . esc_attr(__('Add header', 'wdfb')) . '" />';
	}

	function create_import_fb_comments_box () {
		$opt = $this->_get_option('wdfb_comments');
		echo $this->_create_checkbox('comments', 'import_fb_comments',  @$opt['import_fb_comments']);
		echo '<div><small>' . __('If checked, comments on your posts on Facebook will be periodically imported and merged with your regular comments.', 'wdfb') . '</small></div>';
	}
	function create_import_fb_comments_skip_box () {
		$opt = $this->_get_option('wdfb_comments');
		$skips = @$opt['skip_import'];
		$skips = is_array($skips) ? $skips : array();
		$reverse = @$opt['reverse_skip_logic'];
		$data = Wdfb_OptionsRegistry::get_instance();
		$accounts = $data->get_option('wdfb_api', 'auth_tokens');
		if (is_array($accounts)) {
			echo '<div id="wdfb-comments-skip_accounts">'; 
			foreach ($accounts as $fb_uid => $token) {
				if (!$fb_uid) continue;
				$checked = in_array($fb_uid, $skips) ? true : false;
				echo $this->_create_subcheckbox('comments', 'skip_import',  $fb_uid, $checked) .
					" <label for='skip_import-{$fb_uid}'><span class='wdfb_uid_to_name_mapper'>{$fb_uid}</span></label><br />";
			}
			echo '<div><small>' . 
				(
					$reverse
					? __('Comments on your posts on Facebook will <b>ONLY</b> be imported for the checked IDs.', 'wdfb')
					: __('Comments on your posts on Facebook will <b>NOT</b> be imported for any of the checked IDs.', 'wdfb')
				) . 
			'</small></div>';
			echo '<br />' . 
				'<label for="reverse_skip_logic">' . __('Reverse checking logic:', 'wdfb') . '</label>' .
				'&nbsp;' .
				$this->_create_checkbox('comments', 'reverse_skip_logic', $reverse) .
			'';
			echo '<div><small>' . __('Togglig this option will reverse import skipping options.', 'wdfb') . '</small></div>';
			echo '</div>';
		}
	}
	function create_fb_comments_limit_box () {
		$opt = $this->_get_option('wdfb_comments');
		echo "<select name='wdfb_comments[comment_limit]>";
		for ($i=0; $i<110; $i+=10) {
			echo "<option value='{$i}' " . (($i == @$opt['comment_limit']) ? 'selected="selected"' : '') . ">{$i}</option>";
		}
		echo "</select>";
		echo '<div><small>' . __('This value is applied to number of Facebook posts inspected for comments. Set the limit to lower value if you experience performance issues.', 'wdfb') . '</small></div>';
	}
	function create_notify_authors_box () {
		$opt = $this->_get_option('wdfb_comments');
		echo $this->_create_checkbox('comments', 'notify_authors',  @$opt['notify_authors']);
		echo '<div><small>' . __('If checked, post authors will be notified when new comments from Facebook are added to their posts.', 'wdfb') . '</small></div>';
	}
	function create_import_now_box () {
		echo '<a href="#" class="wdfb_import_comments_now">' . __("Import comments now (this can take a while)", 'wdfb') . '<a>';
	}

	function create_use_fb_comments_box () {
		$opt = $this->_get_option('wdfb_comments');
		echo '<hr />';
		echo $this->_create_checkbox('comments', 'use_fb_comments',  @$opt['use_fb_comments']);
		echo '<div><small>' . __('If checked, <a href="http://developers.facebook.com/blog/post/472">Facebook comments</a> will be used instead of, or in addition to, regular WordPress comments.', 'wdfb') . '</small></div>';
	}
	function create_override_wp_comments_settings_box () {
		$opt = $this->_get_option('wdfb_comments');
		echo $this->_create_checkbox('comments', 'override_wp_comments_settings',  @$opt['override_wp_comments_settings']);
		echo '<div><small>' . __(
			'<p>By default, Facebook comments will appear <em>in addition</em> to WordPress comments.</p>' .
			'<p>Check this box if you wish to disable WordPress comments, but still allow Facebook Comments.</p>' .
			'<p><strong>Note:</strong> if you enable this option, Facebook Comments will always appear, disregarding any WordPress settings.</p>'
			,
		'wdfb') . '</small></div>';
	}
	function create_fb_comments_width_box () {
		$opt = $this->_get_option('wdfb_comments');

		// Box width
		$width = @$opt['fb_comments_width'];
		$width = $width ? $width : '550';
		echo $this->_create_small_text_box('comments', 'fb_comments_width',  $width) . 'px';
	}
	function create_fb_comments_reverse_box () {
		$opt = $this->_get_option('wdfb_comments');
		// Reverse?
		echo $this->_create_checkbox('comments', 'fb_comments_reverse',  @$opt['fb_comments_reverse']);
	}
	function create_fb_comments_number_box () {
		$opt = $this->_get_option('wdfb_comments');
		// Comments number
		$num = isset($opt['fb_comments_number']) ? $opt['fb_comments_number'] : 10;
		echo '<select name="wdfb_comments[fb_comments_number]">';
		for ($i=0; $i<100; $i+=5) {
			$selected = ($i == $num) ? 'selected="selected"' : '';
			echo "<option value='{$i}' {$selected}>{$i}</option>";
		}
		echo '</select>';
	}
	function create_fb_comments_custom_hook_box () {
		$opt = $this->_get_option('wdfb_comments');
		echo $this->_create_text_box('comments', 'fb_comments_custom_hook',  @$opt['fb_comments_custom_hook']);
		echo '<div><small>' . __('<p><b>Warning:</b> You should leave this box empty unless you know what you&#039;re doing.', 'wdfb') . '</small></div>';
		echo '<div><small>' . __('<p>Themes can use different approaches to showing comments, which can cause Facebook Comments not to appear in some cases.', 'wdfb') . '</small></div>';
		echo '<div><small>' . __('<p>If you experience such a conflict between Facebook Comments and your theme, you can enter a custom hook for Facebook Comments here.', 'wdfb') . '</small></div>';
		echo '<div><small>' . __('<p>If everything else fails, you can enter this code in your theme where you want your comments to appear: <code>&lt;?php do_action("my_action_name");?&gt;</code>, then enter "my_action_name" (without quotes) in this box.', 'wdfb') . '</small></div>';
	}

	function create_allow_autopost_box () {
		$opt = $this->_get_option('wdfb_autopost');
		echo $this->_create_checkbox('autopost', 'allow_autopost',  @$opt['allow_autopost']);
		echo '<p class="wdfb_perms_not_granted">' .
			__('If you haven\'t done so already, you will need to <a href="#" class="wdfb_skip_to_step_1">grant <strong>extended permissions</strong> to the plugin</a> for this to work.', 'wdfb') .
		'</p>';
	}
	function create_allow_frontend_autopost_box () {
		$opt = $this->_get_option('wdfb_autopost');
		echo $this->_create_checkbox('autopost', 'allow_frontend_autopost',  @$opt['allow_frontend_autopost']);
		echo '<div><small>' . __('Enable this option to allow auto-publishing on Facebook with frontend posting plugins', 'wdfb') . '</small></div>';
	}
	function create_show_status_column_box () {
		$opt = $this->_get_option('wdfb_autopost');
		echo $this->_create_checkbox('autopost', 'show_status_column',  @$opt['show_status_column']);
		echo '<div><small>' . __('Enabling this will add a new column that shows if the post has already been published on Facebook to your post management pages.', 'wdfb') . '</small></div>';
	}
	function create_autopost_map_box () {
		$post_types = get_post_types(array('public'=>true), 'objects');
		$fb_locations = array (
			'0' => __("Don't post this type to Facebook", 'wdfb'),
			'feed' => __("Facebook wall", 'wdfb'),
			'events' => __("Facebook events", 'wdfb'),
			'notes' => __("Facebook notes", 'wdfb'),
		);
		$opts = $this->_get_option('wdfb_autopost');

		$user = wp_get_current_user();
		$fb_accounts = get_user_meta($user->ID, 'wdfb_api_accounts', true);
		$fb_accounts = isset($fb_accounts['auth_accounts']) ? $fb_accounts['auth_accounts'] : array();

		echo '<div id="wdfb-autopost_map_message"></div>';
		echo "<ul>";
		foreach ($post_types as $pname=>$pval) {
			$pname = $pval->name;
			$pval = $pval->labels->name;
			$fb_action = @$opts["type_{$pname}_fb_type"];
			$box = '<select name="wdfb_autopost[type_' . $pname . '_fb_type]">';
			foreach ($fb_locations as $fbk=>$fbv) {
				$box .= "<option value='{$fbk}' " . (($fbk == $fb_action) ? 'selected="selected"' : '') . ">{$fbv}</option>";
			}
			$box .= '</select>';

			$fb_user_val = @$opts["type_{$pname}_fb_user"];
			$fb_user = '<select name="wdfb_autopost[type_' . $pname . '_fb_user]">';
			foreach ($fb_accounts as $aid=>$aval) {
				$fb_user .= "<option value='{$aid}' " . (($fb_user_val == $aid) ? 'selected="selected"' : '') . ">{$aval}</option>";
			}
			$fb_user .= '</select>';
			
			$shortlink = "" .
				"<input type='hidden' class='wdfb-autopost-shortlink' name='wdfb_autopost[type_{$pname}_use_shortlink]' value='' />" .
				"<input type='checkbox' class='wdfb-autopost-shortlink' name='wdfb_autopost[type_{$pname}_use_shortlink]' " .
					"id='wdfb-{$pname}_use_shortlink' value='1' " . (@$opts["type_{$pname}_use_shortlink"] ? 'checked="checked"' : '') . " /> " .
				'<label for="wdfb-' . $pname . '_use_shortlink">' . __('Use shortlink', 'wdfb') . '</label>' .
			"";

			printf(
				__("<li>Autopost %s to %s of this user: %s %s </li>", 'wdfb'),
				ucfirst($pval), $box, $fb_user, $shortlink
			);
		}
		echo "</ul>";
		echo '<label for="post_as_page">' . __("If posting to a page, post <b>AS</b> page", "wdfb") . '</label> ' .
			$this->_create_checkbox('autopost', 'post_as_page', @$opts['post_as_page'])
		;
		echo '<div><small>' . __("" .
			"Choose the Facebook user ID from the <em>user</em> box, or leave selection to &quot;Me&quot; to use your own profile. " .
			"Remember, the plugin needs to be granted <strong>extended permissions</strong> to access the profile. <br />" .
			"To post to a fan page as page, you need to be administrator of that page.".
		"", 'wdfb') . '</small></div>';
	}
	function create_allow_post_metabox_box () {
		$opt =  $this->_get_option('wdfb_autopost');
		echo $this->_create_checkbox('autopost', 'prevent_post_metabox',  @$opt['prevent_post_metabox']);
		echo '<div><small>' . __('If you check this box, your users will <b>NOT</b> be able to make individual posts to Facebook using the post editor metabox.', 'wdfb') . '</small></div>';
	}

	function create_override_all_box () {
		echo '<input id="_override_all" type="checkbox" name="_override_all" value="1" />';
		echo '<div><small>' . __('If you check this box, all your users individual Facebook settings will be deleted and replaced with what you set here.', 'wdfb') . '</small></div>';
	}
	function create_preserve_api_box () {
		echo '<input type="checkbox" name="_preserve_api" value="1" checked="checked" />';
		echo '<div><small>' . __('If you check this box, your users individual Facebook API settings will be <em>preserved</em> when replacing their options.', 'wdfb') . '</small></div>';
	}

	function create_prevent_blog_settings_box () {
		$opt = get_site_option('wdfb_network', array());
		echo $this->_create_checkbox('network', 'prevent_blog_settings',  @$opt['prevent_blog_settings']);
		echo '<div><small>' . __('If you check this box, your users will <b>NOT</b> be able to make individual changes to plugin settings from now on.', 'wdfb')  . '</small></div>';
		echo '<div><small>' . __('Also, if you want to use this option, you may want to allow your subsites to use your global FB app credentials in <a href="#" class="wdfb_skip_to_step_0">Facebook API settings</a>.', 'wdfb')  . '</small></div>';
	}

	function create_widget_connect_box () {
		$description = sprintf(__('Easily display Facebook Connect options on your front page. <table> <tr><th>Widget settings preview</th><th>Widget preview<th></tr> <tr><td valign="top"><img src="%s/img/connect_allowed.jpg" /></td><td valign="top"><img src="%s/img/connect_allowed_result.jpg" /></td></tr> </table>', 'wdfb'), WDFB_PLUGIN_URL, WDFB_PLUGIN_URL);
		$this->_create_widget_box('connect', $description);
	}
	function create_widget_albums_box () {
		$description = sprintf(__('Easily display Facebook Albums. <table> <tr><th>Widget settings preview</th><th>Widget preview<th></tr> <tr><td valign="top"><img src="%s/img/albums_allowed.jpg" /></td><td valign="top"><img src="%s/img/albums_allowed_result.jpg" /></td></tr> </table>', 'wdfb'), WDFB_PLUGIN_URL, WDFB_PLUGIN_URL);
		$this->_create_widget_box('albums', $description);
	}
	function create_widget_events_box () {
		$description = sprintf(__('Easily display Facebook Events. <table> <tr><th>Widget settings preview</th><th>Widget preview<th></tr> <tr><td valign="top"><img src="%s/img/events_allowed.jpg" /></td><td valign="top"><img src="%s/img/events_allowed_result.jpg" /></td></tr> </table>', 'wdfb'), WDFB_PLUGIN_URL, WDFB_PLUGIN_URL);
		$this->_create_widget_box('events', $description);
	}
	function create_widget_facepile_box () {
		$description = sprintf(__('Easily display Facebook Facepile. <table> <tr><th>Widget settings preview</th><th>Widget preview<th></tr> <tr><td valign="top"><img src="%s/img/facepile_allowed.jpg" /></td><td valign="top"><img src="%s/img/facepile_allowed_result.jpg" /></td></tr> </table>', 'wdfb'), WDFB_PLUGIN_URL, WDFB_PLUGIN_URL);
		$this->_create_widget_box('facepile', $description);
	}
	function create_widget_likebox_box () {
		$description = sprintf(__('Easily display Facebook Like Box. <table> <tr><th>Widget settings preview</th><th>Widget preview<th></tr> <tr><td valign="top"><img src="%s/img/likebox_allowed.jpg" /></td><td valign="top"><img src="%s/img/likebox_allowed_result.jpg" /></td></tr> </table>', 'wdfb'), WDFB_PLUGIN_URL, WDFB_PLUGIN_URL);
		$this->_create_widget_box('likebox', $description);
	}
	function create_widget_recommendations_box () {
		$description = sprintf(__('Easily display Facebook Recommendations. <table> <tr><th>Widget settings preview</th><th>Widget preview<th></tr> <tr><td valign="top"><img src="%s/img/recommendations_allowed.jpg" /></td><td valign="top"><img src="%s/img/recommendations_allowed_result.jpg" /></td></tr> </table>', 'wdfb'), WDFB_PLUGIN_URL, WDFB_PLUGIN_URL);
		$this->_create_widget_box('recommendations', $description);
	}
	function create_widget_activityfeed_box () {
		$description = sprintf(__('Easily display Facebook Activity Feed. <table> <tr><th>Widget settings preview</th><th>Widget preview<th></tr> <tr><td valign="top"><img src="%s/img/activityfeed_allowed.jpg" /></td><td valign="top"><img src="%s/img/activityfeed_allowed_result.jpg" /></td></tr> </table>', 'wdfb'), WDFB_PLUGIN_URL, WDFB_PLUGIN_URL);
		$this->_create_widget_box('activityfeed', $description);
	}
	function create_widget_recent_comments_box () {
		$description = sprintf(__('Easily display your recently imported Facebook comments. <table> <tr><th>Widget settings preview</th><th>Widget preview<th></tr> <tr><td valign="top"><img src="%s/img/recent_comments_allowed.jpg" /></td><td valign="top"><img src="%s/img/recent_comments_allowed_result.jpg" /></td></tr> </table>', 'wdfb'), WDFB_PLUGIN_URL, WDFB_PLUGIN_URL);
		$this->_create_widget_box('recent_comments', $description);
	}
	function create_dashboard_permissions_box () {
		$description = sprintf(__('Display extended permissions granting box for your users in the Dashboard. <table> <th>Widget preview<th></tr> <tr><td valign="top"><img src="%s/img/dashboard_permissions_allowed.jpg" /></td></tr> </table>', 'wdfb'), WDFB_PLUGIN_URL);
		$this->_create_widget_box('dashboard_permissions', $description);
	}

/* ===== Metaboxes ===== */

	function facebook_publishing_metabox () {
		global $post;

		$opt = $this->_get_option('wdfb_autopost');

		$meta = get_post_meta($post->ID, 'wdfb_published_on_fb', true);
		if ($meta) {
			echo '<div style="margin: 5px 0 15px; background-color: #FFFFE0; border-color: #E6DB55; border-radius: 3px 3px 3px 3px; border-style: solid; border-width: 1px; padding: 0 0.6em;">' .
				'<p>' . __("This post has already been published on Facebook", 'wdfb') . '</p>' .
			'</div>';
		}


		echo '<div>';
		echo '<label for="">' . __('Publish on Facebook with different title:', 'wdfb') . '</label>';
		echo '<input type="text" class="widefat" name="wdfb_metabox_publishing_title" id="wdfb_metabox_publishing_title" />';
		echo __('<p><small>Leave this value blank to use the post title.</small></p>', 'wdfb');
		echo '</div>';


		if (!@$opt['allow_autopost']) {
			echo '<div>';
			echo '	<input type="checkbox" name="wdfb_metabox_publishing_publish" id="wdfb_metabox_publishing_publish" value="1" />';
			echo '	<label for="wdfb_metabox_publishing_publish">' . __('I want to publish this post to Facebook', 'wdfb') . '</label>';
			echo __('<p><small>If checked, the post will be unconditionally published on Facebook</small></p>', 'wdfb');
			echo '</div>';

			echo '<div class="wdfb_perms_not_granted" style="display:none">' .
				'<div class="error below-h2">' .
					'<p>' .
						__("Your app doesn't have enough permissions to publish on Facebook", 'wdfb') . '<br />' .
						'<a class="wdfb_grant_perms" href="#" wdfb:perms="' .
						Wdfb_Permissions::get_publisher_permissions() .
						'" wdfb:locale="' . wdfb_get_locale() . '" >' .
							__('Grant needed permissions now', 'wdfb') .
						'</a>' .
					'</p>' .
				'</div>' .
			'</div>';

			$user = wp_get_current_user();
			$fb_accounts = get_user_meta($user->ID, 'wdfb_api_accounts', true);
			$fb_accounts = isset($fb_accounts['auth_accounts']) ? $fb_accounts['auth_accounts'] : array();

			echo '<div>';
			echo '	<label for="wdfb_metabox_publishing_account">' . __('Publish to wall of this Facebook account:', 'wdfb') . '</label>';
			echo '	<select name="wdfb_metabox_publishing_account" id="wdfb_metabox_publishing_account">';
			foreach ($fb_accounts as $aid=>$aval) {
				echo "<option value='{$aid}'>{$aval}</option>";
			}
			echo '	</select>';
			echo '<br />';
			echo '<label for="post_as_page">' . __("If posting to a page, post <b>AS</b> page", "wdfb") . '</label> ' .
				'<input type="checkbox" name="wdfb_post_as_page" id="post_as_page" value="1" />'
			;
			echo '</div>';
			echo '<p class="wdfb_perms_not_granted"><small>' . __('Please make sure that you granted extended permissions to your Facebook App', 'wdfb') . '</small></p>';
		}
		echo '<script type="text/javascript" src="' . WDFB_PLUGIN_URL . '/js/check_permissions.js"></script>';
	}
}