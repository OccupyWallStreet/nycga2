<?PHP

class SocializeAdmin {

    function SocializeAdmin() {
        if (is_admin()) {
            add_action('admin_menu', array(&$this, 'settings_subpanel'));
            add_action('admin_menu', array(&$this, 'socialize_add_meta_box'));
            add_action('admin_print_scripts', array(&$this, 'add_socialize_admin_scripts'));
            add_action('admin_print_styles', array(&$this, 'add_socialize_admin_styles'));
            add_action('save_post', array(&$this, 'socialize_admin_process'));
            add_filter('plugin_action_links_' . SOCIALIZE_BASENAME, array(&$this, 'plugin_settings_link'));
        }
    }

    function plugin_settings_link($links) {
        $url = admin_url('options-general.php?page=socialize');
        $settings_link = '<a href="'.$url.'">' . __('Settings') . '</a>';
        array_unshift( $links, $settings_link );
        return $links;
    }

    function settings_subpanel() {
        if (function_exists('add_options_page')) {
            add_options_page('Socialize', 'Socialize', 'manage_options', 'socialize', array(&$this, 'socialize_admin'));
        }
    }

    function socialize_admin() {
        $tabs = self::admin_tabs();
        if (isset($_GET['tab'])) {
            $tabs[$_GET['tab']]['function'];
            call_user_func($tabs[$_GET['tab']]['function']);
        } else {
            //print_r($tabs['general']['function']);
            call_user_func($tabs['general']['function']);
        }
    }

    function admin_tabs() {
        $tabs = array(
            'general' => array(
                'title' => __('General', 'socialize'),
                'function' => array(&$this, 'socialize_settings_admin')
            ),
            'display' => array(
                'title' => __('Display', 'socialize'),
                'function' => array(&$this, 'socialize_display_admin')
            ),
            'buttons' => array(
                'title' => __('Buttons', 'socialize'),
                'function' => array(&$this, 'socialize_services_admin')
            )
        );

        $tabs = apply_filters('socialize_settings_tabs_array', $tabs);
        return $tabs;
    }

    //=============================================
    // Load admin styles
    //=============================================
    function add_socialize_admin_styles() {
        global $pagenow;
        if ($pagenow == 'options-general.php' && isset($_GET['page']) && strstr($_GET['page'], "socialize")) {
            wp_enqueue_style('dashboard');
            wp_enqueue_style('global');
            wp_enqueue_style('wp-admin');
            wp_enqueue_style('farbtastic');
        }
        wp_enqueue_style('socialize-admin', SOCIALIZE_URL . 'admin/css/socialize-admin.css');
    }

    //=============================================
    // Load admin scripts
    //=============================================
    function add_socialize_admin_scripts() {
        global $pagenow;
        if ($pagenow == 'options-general.php' && isset($_GET['page']) && strstr($_GET['page'], "socialize")) {
            wp_enqueue_script('postbox');
            wp_enqueue_script('dashboard');
            //wp_enqueue_script('custom-background');
        }
        if (isset($_GET['tab']) && $_GET['tab'] == 'display') {
            wp_enqueue_script('farbtastic');
            wp_enqueue_script('socialize-admin-color', SOCIALIZE_URL . 'admin/js/socialize-admin-color-picker.js');
            wp_enqueue_script('socialize-admin-form', SOCIALIZE_URL . 'admin/js/socialize-admin-form.js');
        } else if (isset($_GET['tab']) && $_GET['tab'] == 'buttons') {
            wp_enqueue_script('socialize-admin-form', SOCIALIZE_URL . 'admin/js/socialize-admin-form.js');
        }
        wp_enqueue_script('socialize-admin-sortable', SOCIALIZE_URL . 'admin/js/socialize-admin-sortable.js');

        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-widget');
        wp_enqueue_script('jquery-ui-mouse');
        wp_enqueue_script('jquery-ui-sortable');
    }

    //=============================================
    // On save post, update post meta
    //=============================================
    function socialize_admin_process($post_ID) {
        if (!isset($_POST['socialize_settings_noncename']) || !wp_verify_nonce($_POST['socialize_settings_noncename'], plugin_basename(__FILE__))) {
            return $post_ID;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return $post_ID;

        if ('page' == $_POST['post_type']) {
            if (!current_user_can('edit_page', $post_ID))
                return $post_ID;
        } else {
            if (!current_user_can('edit_post', $post_ID))
                return $post_ID;
        }

        $socializemetaarray = array();
        $socializemetaarray_text = "";

        if (isset($_POST['hide_alert']) && ($_POST['hide_alert'] > 0)) {
            array_push($socializemetaarray, $_POST['hide_alert']);
        }
        if (isset($_POST['socialize_text']) && ($_POST['socialize_text'] != "")) {
            $socializemetaarray_text = $_POST['socialize_text'];
        }
        if (isset($_POST['socialize_buttons'])) {
            foreach ($_POST['socialize_buttons'] as $button) {
                if (($button > 0)) {
                    array_push($socializemetaarray, $button);
                }
                $formid++;
            }
        }
        $socializemeta = implode(',', $socializemetaarray);

        if (!wp_is_post_revision($post_ID) && !wp_is_post_autosave($post_ID)) {
            update_post_meta($post_ID, 'socialize_text', $socializemetaarray_text);
            update_post_meta($post_ID, 'socialize', $socializemeta);
        }
    }

    // On post edit, load metabox
    function socialize_metabox_admin() {
        if (get_post_custom_keys($_GET['post']) && in_array('socialize', get_post_custom_keys($_GET['post']))) {
            $socializemeta = explode(',', get_post_meta(intval($_GET['post']), 'socialize', true));
        } else {
            $socialize_settings = socializeWP::get_options();
            $socializemeta = explode(',', $socialize_settings['sharemeta']);
        }

        $default_content = "";
        $socialize_buttons = self::sort_buttons_array($socializemeta);

        $default_content .= '<input type="hidden" name="socialize_settings_noncename" id="socialize_settings_noncename" value="' . wp_create_nonce(plugin_basename(__FILE__)) . '" />';
        $default_content .= '<div id="socialize-div1"><strong>InLine Buttons</strong><br /><ul id="inline-sortable">';
        foreach ($socialize_buttons[0] as $socialize_button) {
            $default_content .= '<li class="ui-state-default"><label class="selectit"><input value="' . $socialize_button . '" type="checkbox" name="socialize_buttons[]" id="post-share-' . $socialize_button . '"' . checked(in_array($socialize_button, $socializemeta), true, false) . '/> <span>' . __($socialize_buttons[2][$socialize_button]) . '</span></label></li>';
        }
        $default_content .= '</ul></div><div id="socialize-div2"><strong>Alert Box Buttons</strong><br /><ul id="alert-sortable">';
        foreach ($socialize_buttons[1] as $socialize_button) {
            $default_content .= '<li class="ui-state-default"><label class="selectit"><input value="' . $socialize_button . '" type="checkbox" name="socialize_buttons[]" id="post-share-' . $socialize_button . '"' . checked(in_array($socialize_button, $socializemeta), true, false) . '/> <span>' . __($socialize_buttons[2][$socialize_button]) . '</span></label></li>';
        }
        $default_content .= '</ul></div><div class="clear"></div><strong>* You can rearrange the buttons by <em>clicking</em> and <em>dragging</em></strong>';
        echo $default_content;
    }

    // On post edit, load action +metabox
    function socialize_metabox_action_admin() {
        $socialize_settings = socializeWP::get_options();
        $socializemeta_text = $socialize_settings['socialize_text'];
        $socializemeta = explode(',', $socialize_settings['sharemeta']);

        if (get_post_custom_keys($_GET['post'])) {
            if (in_array('socialize', get_post_custom_keys($_GET['post']))) {

                $socializemeta = explode(',', get_post_meta(intval($_GET['post']), 'socialize', true));
            }
            if (in_array('socialize_text', get_post_custom_keys($_GET['post']))) {
                $socializemeta_text = get_post_meta(intval($_GET['post']), 'socialize_text', true);
            }
        }
        echo '<input type="hidden" name="socialize_settings_noncename" id="socialize_settings_noncename" value="' . wp_create_nonce(plugin_basename(__FILE__)) . '" />';

        echo '<p><textarea name="socialize_text" rows="4" style="width:100%;">' . $socializemeta_text . '</textarea></p>';

        echo '<div class="socialize-div3" style="width:100%;">';
        echo '	<strong>Hide Alert Box</strong><br />';
        echo '	<label class="selectit"><input value="21" type="checkbox" name="hide_alert" id="post-share-alert"' . checked(in_array(21, $socializemeta), true) . '/> ' . __('Hide alert box below this post') . '</label>	';
        echo '</div>';
        echo '<div class="clear"></div>';
    }

    // Creates meta box
    function socialize_add_meta_box() {
        if (function_exists('get_post_types')) {
            $post_types = get_post_types(array(), 'objects');
            foreach ($post_types as $post_type) {
                if ($post_type->show_ui) {
                    add_meta_box('socialize-buttons-meta', __('Socialize: Buttons', 'socialize'), array(&$this, 'socialize_metabox_admin'), $post_type->name, 'side');
                    add_meta_box('socialize-action-meta', __('Socialize: Call To Action Text', 'socialize'), array(&$this, 'socialize_metabox_action_admin'), $post_type->name, 'normal');
                }
            }
        } else {
            add_meta_box('socialize-buttons-meta', __('Socialize: Buttons', 'socialize'), array(&$this, 'socialize_metabox_admin'), 'post', 'side');
            add_meta_box('socialize-action-meta', __('Socialize: Call To Action Text', 'socialize'), array(&$this, 'socialize_metabox_action_admin'), 'post', 'normal');

            add_meta_box('socialize-buttons-meta', __('Socialize Settings', 'socialize'), array(&$this, 'socialize_metabox_admin'), 'page', 'side');
            add_meta_box('socialize-action-meta', __('Socialize: Call To Action Text', 'socialize'), array(&$this, 'socialize_metabox_action_admin'), 'page', 'normal');
        }
    }

    //=============================================
    // Display support info
    //=============================================
    function socialize_show_plugin_support() {
        $content = '<p>Leave a comment on the <a target="_blank" href="http://www.jonbishop.com/downloads/wordpress-plugins/socialize/#comments">Socialize Plugin Page</a></p>
		<p style="text-align:center;">- or -</p>
		<p>Create a new topic on the <a target="_blank" href="http://wordpress.org/tags/socialize">WordPress Support Forum</a></p>';
        return self::socialize_postbox('socialize-support', 'Support', $content);
    }

    //=============================================
    // Display support info
    //=============================================
    function socialize_show_donate() {
        $content = '<p><strong>Looking for a karmic boost?</strong><br />
		If you like this plugin please consider donating a few bucks to support its development. If you can\'t spare any change you can also help by giving me a good rating on WordPress.org and tweeting this plugin to your followers.
		<ul>
			<li><a target="_blank" href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=jonbish%40gmail%2ecom&lc=US&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted">Donate With PayPal</a></li>
			<li><a target="_blank" href="http://wordpress.org/extend/plugins/socialize/">Give Me A Good Rating</a></li>
			<li><a target="_blank" href="http://twitter.com/?status=WordPress Plugin: Selectively Add Social Bookmarks to Your Posts http://bit.ly/IlCdN (via @jondbishop)">Share On Twitter</a></li>
		</ul></p>';
        return self::socialize_postbox('socialize-donate', 'Donate & Share', $content);
    }

    //=============================================
    // Display feed
    //=============================================
    function socialize_show_blogfeed() {

        include_once(ABSPATH . WPINC . '/feed.php');
        $content = "";
        $rss = fetch_feed("http://feeds.feedburner.com/JonBishop");
        if (!is_wp_error($rss)) {
            $maxitems = $rss->get_item_quantity(5);
            $rss_items = $rss->get_items(0, $maxitems);
        }

        if ($maxitems == 0) {
            $content .= "<p>No Posts</p>";
        } else {
            $content .= "<ul>";
            foreach ($rss_items as $item) {
                $content .= "<li><a href='" . $item->get_permalink() . "' title='Posted " . $item->get_date('j F Y | g:i a') . "'>" . $item->get_title() . "</a></li>";
            }
            $content .= "</ul>";
            $content .= "<p><a href='" . $rss->get_permalink() . "'>More Posts &raquo;</a></p>";
        }
        return self::socialize_postbox('socialize-blog-rss', 'Tips and Tricks', $content);
    }

    //=============================================
    // Contact page options
    //=============================================
    function socialize_display_admin() {
        $socialize_settings = self::process_socialize_display_admin();

        $wrapped_content = "";
        $default_content = "";
        $general_content = "";
        $display_content = "";
        $template_content = "";
        $alert_content = "";

        if (function_exists('wp_nonce_field')) {
            $general_content .= wp_nonce_field('socialize-update-display_options', '_wpnonce', true, false);
        }
        
        $general_content .= '<p><strong>' . __("Floating Share Bar") . '</strong><br />
					<label>Off<input type="radio" value="in" name="socialize_button_display" ' . checked($socialize_settings['socialize_button_display'], 'in', false) . '/></label>
					<label>On<input type="radio" value="out" name="socialize_button_display" ' . checked($socialize_settings['socialize_button_display'], 'out', false) . '/></label>
					<small>Turn this on to display your buttons floating next to your content. The floating share bar will only be active on single <strong>pages</strong> and <strong>post types</strong> (New feature in active development).</small></p>';
        
        $general_content .= '<div id="socialize-display-out" class="socialize-display-select"><p><strong>' . __("Margin") . '</strong><br />
					<input type="text" name="socialize_out_margin" value="' . $socialize_settings['socialize_out_margin'] . '" /> <small>Floating share bar margin in relation to the posts content.</small></p></div>';
        
        $general_content .= '<div id="socialize-display-in" class="socialize-display-select"><p><strong>' . __("Inline Button Alignment") . '</strong><br />
					<label>Left<input type="radio" value="left" name="socialize_float" ' . checked($socialize_settings['socialize_float'], 'left', false) . '/></label>
					<label>Right<input type="radio" value="right" name="socialize_float" ' . checked($socialize_settings['socialize_float'], 'right', false) . '/></label>
					<small>Choose whether to display the buttons in the content on the right or left.</small></p>';
        
        $general_content .= '<p><strong>' . __("Inline Button Position") . '</strong><br />
					<label>Vertical<input type="radio" value="vertical" name="socialize_position" ' . checked($socialize_settings['socialize_position'], 'vertical', false) . '/></label>
					<label>Horizontal<input type="radio" value="horizontal" name="socialize_position" ' . checked($socialize_settings['socialize_position'], 'horizontal', false) . '/></label>
					<small>Choose whether to display the buttons in a line vertically or horizontally.</small></p></div>';

        $general_content .= '<p><strong>' . __("Show/Hide Buttons") . '</strong><br />
            <small>This will show or hide both inline buttons and the call to action box on selected post types.</small></p>';
        $general_content .= '<p><input type="checkbox" name="socialize_display_front" ' . checked($socialize_settings['socialize_display_front'], 'on', false) . ' />
					Front Page</p>';
        $general_content .= '<p><input type="checkbox" name="socialize_display_archives" ' . checked($socialize_settings['socialize_display_archives'], 'on', false) . ' />
					Archive pages</p>';
        $general_content .= '<p><input type="checkbox" name="socialize_display_search" ' . checked($socialize_settings['socialize_display_search'], 'on', false) . ' />
					Search page</p>';
        $general_content .= '<p><input type="checkbox" name="socialize_display_posts" ' . checked($socialize_settings['socialize_display_posts'], 'on', false) . ' />
					Posts</p>';
        $general_content .= '<p><input type="checkbox" name="socialize_display_pages" ' . checked($socialize_settings['socialize_display_pages'], 'on', false) . ' />
					Pages</p>';
        foreach (get_post_types(array('public' => true, '_builtin' => false), 'objects') as $custom_post) {
            $general_content .= '<p><input type="checkbox" name="socialize_display_custom_' . $custom_post->name . '" ' . checked(in_array($custom_post->name, $socialize_settings['socialize_display_custom']), true, false) . ' />
					' . $custom_post->label . '</p>';
        }

        $general_content .= '<p><input type="checkbox" name="socialize_display_feed" ' . checked($socialize_settings['socialize_display_feed'], 'on', false) . ' />
					Feed Entries</p>';
        $wrapped_content .= self::socialize_postbox('socialize-settings-general', 'Button Display Settings', $general_content);

        $alert_content .= '<p><strong>' . __("'Call To Action' Box Background Color") . '</strong><br />
					<input type="text" name="socialize_alert_bg" id="background-color" value="' . $socialize_settings['socialize_alert_bg'] . '" />
					<a class="hide-if-no-js" href="#" id="pickcolor">' . __('Select a Color') . '</a>
					<div id="colorPickerDiv" style="z-index: 100; background:#eee; border:1px solid #ccc; position:absolute; display:none;"></div>
					<small>By default, the background color of the \'Call To Action\' box is a yellowish tone.</small></p>';
        $alert_content .= '<p><strong>' . __("'Call To Action' Box Border") . '</strong></p>';
        $alert_content .= '<p>' . __("Border Color") . '<br />
					<input type="text" name="socialize_alert_border_color" id="border-color" value="' . $socialize_settings['socialize_alert_border_color'] . '" />
					<a class="hide-if-no-js" href="#" id="pickcolor_border">' . __('Select a Color') . '</a>
					<div id="colorPickerDiv_border" style="z-index: 100; background:#eee; border:1px solid #ccc; position:absolute; display:none;"></div></p>';
        $alert_content .= '<p>' . __("Border Style") . '<br />
					<select name="socialize_alert_border_style">';
        foreach (array('solid', 'dotted', 'dashed', 'double') as $socialize_alert_border_style) {
            $alert_content .= '<option value="' . $socialize_alert_border_style . '" ' . selected($socialize_settings['socialize_alert_border_style'], $socialize_alert_border_style, false) . '>' . $socialize_alert_border_style . '</option>';
        }
        $alert_content .= '</select></p>';
        $alert_content .= '<p>' . __("Border Size") . '<br />
					<select name="socialize_alert_border_size">';
        foreach (array('0px', '1px', '2px', '3px', '4px', '5px', '6px') as $socialize_alert_border_size) {
            $alert_content .= '<option value="' . $socialize_alert_border_size . '" ' . selected($socialize_settings['socialize_alert_border_size'], $socialize_alert_border_size, false) . '>' . $socialize_alert_border_size . '</option>';
        }
        $alert_content .= '</select></p>';
        $alert_content .= '<p><strong>' . __("Show/Hide 'Call to Action' Box") . '</strong></p>';
        $alert_content .= '<p><input type="checkbox" name="socialize_alert_box" ' . checked($socialize_settings['socialize_alert_box'], 'on', false) . ' />
					Single Posts</p>';
        $alert_content .= '<p><input type="checkbox" name="socialize_alert_box_pages" ' . checked($socialize_settings['socialize_alert_box_pages'], 'on', false) . ' />
					Single Pages</p>';
        $wrapped_content .= self::socialize_postbox('socialize-settings-alert', '\'Call To Action\' Box Settings', $alert_content);

        $template_content .= '<p><strong>' . __("Call to Action Box Template") . '</strong><br />
					<textarea name="socialize_action_template" rows="6" style="width:100%;">' . $socialize_settings['socialize_action_template'] . '</textarea><br />
                                            <small>This is the HTML used within the Call to Action box. You can use some of the tags below if you want to be creative. This is experimental at the moment so please use discretion.<br /><br />
                                            <strong>Note:</strong> If this box is empty, nothing will display in the Call to Action box. To fix this, deactivate and reactivate the plugin to reset your settings. Setting swill only reset if this box is empty.</small></p>
                                            <table class="socialize-table" cellspacing=0 callpadding=0>
                                            <th colspan="2">Customize the Call to Action template using the provided template tags</th>
                                            <tr>
                                                <td>%%buttons%%</td>
                                                <td>Display social media buttons</td>
                                            </tr>
                                            <tr>
                                                <td>%%content%%</td>
                                                <td>Display your call to action text</td>
                                            </tr>
                                            <tr>
                                                <td>%%facebook_like_standard%%<br />
                                                    %%facebook_compact%%</td>
                                                <td>Display the standard facebook button or a compant version</td>
                                            </tr>
                                            <tr>
                                                <td>%%tweetmeme_compact%%<br />
                                                    %%twitter_compact%%</td>
                                                <td>Display the compact Twitter or Tweetmeme buttons</td>
                                            </tr>
                                        </table>';
        $template_content .= '<p><strong>' . __("Disable Socialize Stylesheet") . '</strong><br />
					<input type="checkbox" name="socialize_css" ' . checked($socialize_settings['socialize_css'], 'on', false) . ' />
					<small>Check this if you want to disable the stylesheet included with this plugin so you can use custom css in your own stylesheet.</small></p>';

        $wrapped_content .= self::socialize_postbox('socialize-settings-alert', 'Advanced: Edit Template and CSS', $template_content);

        self::socialize_admin_wrap('Socialize: Display Settings', $wrapped_content);
    }

    //=============================================
    // Process contact page form data
    //=============================================
    function process_socialize_display_admin() {
        
        if (!empty($_POST['socialize_option_submitted'])) {
            if (strstr($_GET['page'], "socialize") && check_admin_referer('socialize-update-display_options')) {
                $socialize_settings = socializeWP::get_options();
                
                if (isset($_POST['socialize_text'])) {
                    $socialize_settings['socialize_text'] = stripslashes($_POST['socialize_text']);
                }
                $color = preg_replace('/[^0-9a-fA-F]/', '', $_POST['socialize_alert_bg']);
                if ((strlen($color) == 6 || strlen($color) == 3) && isset($_POST['socialize_alert_bg'])) {
                    $socialize_settings['socialize_alert_bg'] = $_POST['socialize_alert_bg'];
                }
                $border_color = preg_replace('/[^0-9a-fA-F]/', '', $_POST['socialize_alert_border_color']);
                if ((strlen($border_color) == 6 || strlen($border_color) == 3) && isset($_POST['socialize_alert_border_color'])) {
                    $socialize_settings['socialize_alert_border_color'] = $_POST['socialize_alert_border_color'];
                }
                if (isset($_POST['socialize_alert_border_style'])) {
                    $socialize_settings['socialize_alert_border_style'] = $_POST['socialize_alert_border_style'];
                }
                if (isset($_POST['socialize_alert_border_size'])) {
                    $socialize_settings['socialize_alert_border_size'] = $_POST['socialize_alert_border_size'];
                }
                if (isset($_POST['socialize_display_front'])) {
                    $socialize_settings['socialize_display_front'] = $_POST['socialize_display_front'];
                } else {
                    $socialize_settings['socialize_display_front'] = '';
                }
                if (isset($_POST['socialize_display_archives'])) {
                    $socialize_settings['socialize_display_archives'] = $_POST['socialize_display_archives'];
                } else {
                    $socialize_settings['socialize_display_archives'] = '';
                }
                if (isset($_POST['socialize_display_search'])) {
                    $socialize_settings['socialize_display_search'] = $_POST['socialize_display_search'];
                } else {
                    $socialize_settings['socialize_display_search'] = '';
                }
                if (isset($_POST['socialize_display_posts'])) {
                    $socialize_settings['socialize_display_posts'] = $_POST['socialize_display_posts'];
                } else {
                    $socialize_settings['socialize_display_posts'] = '';
                }
                $socialize_settings['socialize_display_custom'] = array();
                foreach (get_post_types(array('public' => true, '_builtin' => false), 'names') as $custom_post) {
                    if (isset($_POST['socialize_display_custom_' . $custom_post])) {
                        array_push($socialize_settings['socialize_display_custom'], $custom_post);
                    }
                }
                if (isset($_POST['socialize_display_pages'])) {
                    $socialize_settings['socialize_display_pages'] = $_POST['socialize_display_pages'];
                } else {
                    $socialize_settings['socialize_display_pages'] = '';
                }
                if (isset($_POST['socialize_display_feed'])) {
                    $socialize_settings['socialize_display_feed'] = $_POST['socialize_display_feed'];
                } else {
                    $socialize_settings['socialize_display_feed'] = '';
                }
                if (isset($_POST['socialize_alert_box'])) {
                    $socialize_settings['socialize_alert_box'] = $_POST['socialize_alert_box'];
                } else {
                    $socialize_settings['socialize_alert_box'] = '';
                }
                if (isset($_POST['socialize_alert_box_pages'])) {
                    $socialize_settings['socialize_alert_box_pages'] = $_POST['socialize_alert_box_pages'];
                } else {
                    $socialize_settings['socialize_alert_box_pages'] = '';
                }
                if (isset($_POST['socialize_float'])) {
                    $socialize_settings['socialize_float'] = $_POST['socialize_float'];
                }
                if (isset($_POST['socialize_position'])) {
                    $socialize_settings['socialize_position'] = $_POST['socialize_position'];
                }
                if (isset($_POST['socialize_action_template'])) {
                    $socialize_settings['socialize_action_template'] = stripslashes($_POST['socialize_action_template']);
                }
                if (isset($_POST['socialize_css'])) {
                    $socialize_settings['socialize_css'] = $_POST['socialize_css'];
                } else {
                    $socialize_settings['socialize_css'] = '';
                }
                if (isset($_POST['socialize_out_margin'])) {
                    $socialize_settings['socialize_out_margin'] = $_POST['socialize_out_margin'];
                } else {
                    $socialize_settings['socialize_out_margin'] = '';
                }
                if (isset($_POST['socialize_button_display'])) {
                    $socialize_settings['socialize_button_display'] = $_POST['socialize_button_display'];
                }
                echo "<div id=\"updatemessage\" class=\"updated fade\"><p>Socialize settings updated.</p></div>\n";
                echo "<script type=\"text/javascript\">setTimeout(function(){jQuery('#updatemessage').hide('slow');}, 3000);</script>";

                socializeWP::update_options($socialize_settings);
            }
        }//updated
        $socialize_settings = socializeWP::get_options();
        return $socialize_settings;
    }

    //=============================================
    // Contact page options
    //=============================================
    function socialize_services_admin() {
        $socialize_settings = self::process_socialize_services_admin();


        $wrapped_content = "";
        $digg_buttons_content = "";
        $twiter_buttons_content = "";
        $facebook_buttons_content = "";
        $default_content = "";
        $reddit_buttons_content = "";
        $stumbleupon_buttons_content = "";
        $pinterest_buttons_content = "";
        $buffer_buttons_content  = "";
        $google_plusone_buttons_content = "";
        $yahoo_buttons_content = "";
        $linkedin_buttons_content = "";

        if (function_exists('wp_nonce_field')) {
            $default_content .= wp_nonce_field('socialize-update-services_options', '_wpnonce', true, false);
        }
        
        $default_content .= "This is where you will be able to add custom buttons to Socialize. This is still in development but you can currently <a href='http://www.jonbishop.com/downloads/wordpress-plugins/socialize/socialize-api/' target='_blank'>add new buttons using the API.</a>";
        
        $wrapped_content .= self::socialize_postbox('socialize-settings-buttons-custom', 'Custom Buttons', $default_content);
        
        // Facebook
        $facebook_buttons_content .= '<p>' . __("Choose which Facebook share button to display") . ':<br />
					<label><input type="radio" value="official-like" name="socialize_fbWidget" ' . checked($socialize_settings['socialize_fbWidget'], 'official-like', false) . '/> <a href="http://developers.facebook.com/docs/reference/plugins/like" target="_blank">Official Like Button</a></label><br />
					<label><input type="radio" value="fbshareme" name="socialize_fbWidget" ' . checked($socialize_settings['socialize_fbWidget'], 'fbshareme', false) . '/> <a href="http://www.fbshare.me/" target="_blank">fbShare.me</a></label><br /></p>';
        $facebook_buttons_content .= '<div id="socialize-facebook-official-like" class="socialize-facebook-select">';
        $facebook_buttons_content .= '<p><strong>' . __("Facebook Button Settings") . '</strong></p>';
        $facebook_buttons_content .= '<p>' . __("Layout Style") . '<br />
					<select name="fb_layout">';
        foreach (array('standard', 'button_count', 'box_count') as $fb_layout) {
            $facebook_buttons_content .= '<option value="' . $fb_layout . '" ' . selected($socialize_settings['fb_layout'], $fb_layout, false) . '>' . $fb_layout . '</option>';
        }
        $facebook_buttons_content .= '</select></p>';
        $facebook_buttons_content .= '<p>' . __("Show Faces?") . '<br />
					<select name="fb_showfaces">';
        foreach (array('true', 'false') as $fb_showfaces) {
            $facebook_buttons_content .= '<option value="' . $fb_showfaces . '" ' . selected($socialize_settings['fb_showfaces'], $fb_showfaces, false) . '>' . $fb_showfaces . '</option>';
        }
        $facebook_buttons_content .= '</select></p>';
        /* Does not work in iframe
        $facebook_buttons_content .= '<p>' . __("Send Button") . '<br />
					<select name="fb_sendbutton">';
        foreach (array('true', 'false') as $fb_sendbutton) {
            $facebook_buttons_content .= '<option value="' . $fb_sendbutton . '" ' . selected($socialize_settings['fb_sendbutton'], $fb_sendbutton, false) . '>' . $fb_sendbutton . '</option>';
        }
        $facebook_buttons_content .= '</select></p>';
         */
        $facebook_buttons_content .= '<p>' . __("Width") . '<br />
					<input type="text" name="fb_width" value="' . $socialize_settings['fb_width'] . '" /></p>';
        $facebook_buttons_content .= '<p>' . __("Verb to Display") . '<br />
					<select name="fb_verb">';
        foreach (array('like', 'recommend') as $fb_verb) {
            $facebook_buttons_content .= '<option value="' . $fb_verb . '" ' . selected($socialize_settings['fb_verb'], $fb_verb, false) . '>' . $fb_verb . '</option>';
        }
        $facebook_buttons_content .= '</select></p>';
        $facebook_buttons_content .= '<p>' . __("Font") . '<br />
					<select name="fb_font">';
        foreach (array('arial', 'lucida grande', 'segoe ui', 'tahoma', 'trebuchet ms', 'verdana') as $fb_font) {
            $facebook_buttons_content .= '<option value="' . $fb_font . '" ' . selected($socialize_settings['fb_font'], $fb_font, false) . '>' . $fb_font . '</option>';
        }
        $facebook_buttons_content .= '</select></p>';
        $facebook_buttons_content .= '<p>' . __("Color") . '<br />
					<select name="fb_color">';
        foreach (array('light', 'dark') as $fb_color) {
            $facebook_buttons_content .= '<option value="' . $fb_color . '" ' . selected($socialize_settings['fb_color'], $fb_color, false) . '>' . $fb_color . '</option>';
        }
        $facebook_buttons_content .= '</select></p>';
        $facebook_buttons_content .= '</div>';
        $wrapped_content .= self::socialize_postbox('socialize-settings-buttons-facebook', 'Facebook Button Settings', $facebook_buttons_content);
        
        // Twitter
        $twiter_buttons_content .= '<p>' . __("Choose which Twitter retweet button to display") . ':<br />
					<label><input type="radio" value="official" name="socialize_twitterWidget" ' . checked($socialize_settings['socialize_twitterWidget'], 'official', false) . '/> <a href="http://twitter.com/goodies/tweetbutton" target="_blank">Official Tweet Button</a></label><br />
					<label><input type="radio" value="tweetmeme" name="socialize_twitterWidget" ' . checked($socialize_settings['socialize_twitterWidget'], 'tweetmeme', false) . '/> <a href="http://tweetmeme.com/" target="_blank">TweetMeme</a></label><br />
					<label><input type="radio" value="topsy" name="socialize_twitterWidget" ' . checked($socialize_settings['socialize_twitterWidget'], 'topsy', false) . '/> <a href="http://topsy.com/" target="_blank">Topsy</a></label><br /></p>';
        $twiter_buttons_content .= '<p>' . __("Twitter Source") . '<br />
					<input type="text" name="socialize_twitter_source" value="' . $socialize_settings['socialize_twitter_source'] . '" />
					<small>This is your Twitter name. By default, the source is @socializeWP.</small></p>';
        $twiter_buttons_content .= '<div id="socialize-twitter-official" class="socialize-twitter-select">';
        $twiter_buttons_content .= '<p><strong>' . __("Official Twitter Button Settings") . '</strong></p>';
        $twiter_buttons_content .= '<p>' . __("Button Count") . '<br />
					<select name="socialize_twitter_count">';
        foreach (array('horizontal', 'vertical', 'none') as $twittercount) {
            $twiter_buttons_content .= '<option value="' . $twittercount . '" ' . selected($socialize_settings['socialize_twitter_count'], $twittercount, false) . '>' . $twittercount . '</option>';
        }
        $twiter_buttons_content .= '</select></p>';
        $twiter_buttons_content .= '<p>' . __("Twitter Refer") . '<br />
					<input type="text" name="socialize_twitter_related" value="' . $socialize_settings['socialize_twitter_related'] . '" />
					<small>Recommend a Twitter account for users to follow after they share content from your website.</small></p>';
        $twiter_buttons_content .= '</div>';
        $twiter_buttons_content .= '<div id="socialize-twitter-topsy" class="socialize-twitter-select">';
        $twiter_buttons_content .= '<p><strong>' . __("Topsy Button Settings") . '</strong></p>';
        $twiter_buttons_content .= '<p>' . __("Topsy Theme") . '<br />
					<select name="socialize_topsy_theme">';
        foreach (array('wisteria', 'brown', 'monochrome', 'jade', 'brick-red', 'sea-foam', 'mustard', 'light-blue', 'hot-pink', 'silver', 'sand', 'red', 'blue') as $topsytheme) {
            $twiter_buttons_content .= '<option value="' . $topsytheme . '" ' . selected($socialize_settings['socialize_topsy_theme'], $topsytheme, false) . '>' . $topsytheme . '</option>';
        }
        $twiter_buttons_content .= '</select></p>';
        $twiter_buttons_content .= '<p>' . __("Topsy Size") . '<br />
					<select name="socialize_topsy_size">';
        foreach (array('big', 'small') as $topsysize) {
            $twiter_buttons_content .= '<option value="' . $topsysize . '" ' . selected($socialize_settings['socialize_topsy_size'], $topsysize, false) . '>' . $topsysize . '</option>';
        }
        $twiter_buttons_content .= '</select></p>';
        $twiter_buttons_content .= '</div>';
        $twiter_buttons_content .= '<div id="socialize-twitter-tweetmeme" class="socialize-twitter-select">';
        $twiter_buttons_content .= '<p><strong>' . __("Tweetmeme Button Settings") . '</strong></p>';

        $twiter_buttons_content .= '<p>' . __("Tweetmeme Style") . '<br />
					<select name="socialize_tweetmeme_style">';
        foreach (array('normal', 'compact') as $tweetmemestyle) {
            $twiter_buttons_content .= '<option value="' . $tweetmemestyle . '" ' . selected($socialize_settings['socialize_tweetmeme_style'], $tweetmemestyle, false) . '>' . $tweetmemestyle . '</option>';
        }
        $twiter_buttons_content .= '</select></p>';

        $twiter_buttons_content .= '</div>';

        $wrapped_content .= self::socialize_postbox('socialize-settings-buttons-twitter', 'Twitter Button Settings', $twiter_buttons_content);
        
        // Reddit
        $reddit_buttons_content .= '<p>' . __("Choose which Reddit share button to display") . ':<br />
					<select name="reddit_type">';
        foreach (array('compact' => '1', 'normal' => '2', 'big' => '3') as $reddit_type => $reddit_type_value) {
            $reddit_buttons_content .= '<option value="' . $reddit_type_value . '" ' . selected($socialize_settings['reddit_type'], $reddit_type_value, false) . '>' . $reddit_type . '</option>';
        }
        $reddit_buttons_content .= '</select></p>';
        $reddit_buttons_content .= '<p>' . __("Background Color") . '<br />
					<input type="text" name="reddit_bgcolor" value="' . $socialize_settings['reddit_bgcolor'] . '" />
					<small>Background color of Reddit Button</small></p>';
        $reddit_buttons_content .= '<p>' . __("Background Border Color") . '<br />
					<input type="text" name="reddit_bordercolor" value="' . $socialize_settings['reddit_bordercolor'] . '" />
					<small>Background border color of Reddit Button</small></p>';

        $wrapped_content .= self::socialize_postbox('socialize-settings-buttons-reddit', 'Reddit Button Settings', $reddit_buttons_content);
        
        // Stumbleupon
        $stumbleupon_buttons_content .= '<p>' . __("Choose which StumbleUpon button to display") . ':<br />
					<select name="su_type">';
        foreach (array('horizontal square' => '1', 'horizontal rounded' => '2', 'horizontal simple' => '3', 'vertical' => '5', 'round large' => '6', 'round small' => '4') as $su_type => $su_type_value) {
            $stumbleupon_buttons_content .= '<option value="' . $su_type_value . '" ' . selected($socialize_settings['su_type'], $su_type_value, false) . '>' . $su_type . '</option>';
        }
        $stumbleupon_buttons_content .= '</select></p>';
        $wrapped_content .= self::socialize_postbox('socialize-settings-buttons-stumbleupon', 'Stumbleupon Button Settings', $stumbleupon_buttons_content);

        // Pinterest
        $pinterest_buttons_content .= '<p>' . __("Choose which Pinterest button to display") . ':<br />
					<select name="pinterest_counter">';
        foreach (array('vertical', 'horizontal', 'none') as $pinterest_counter) {
            $pinterest_buttons_content .= '<option value="' . $pinterest_counter . '" ' . selected($socialize_settings['pinterest_counter'], $pinterest_counter, false) . '>' . $pinterest_counter . '</option>';
        }
        $pinterest_buttons_content .= '</select></p>';
        $wrapped_content .= self::socialize_postbox('socialize-settings-buttons-pinterest', 'Pinterest Button Settings', $pinterest_buttons_content);
        
        // Buffer
        $buffer_buttons_content .= '<p>' . __("Choose which Buffer button to display") . ':<br />
					<select name="buffer_counter">';
        foreach (array('vertical', 'horizontal', 'none') as $buffer_counter) {
            $buffer_buttons_content .= '<option value="' . $buffer_counter . '" ' . selected($socialize_settings['buffer_counter'], $buffer_counter, false) . '>' . $buffer_counter . '</option>';
        }
        $buffer_buttons_content .= '</select></p>';
        $wrapped_content .= self::socialize_postbox('socialize-settings-buttons-buffer', 'Buffer Button Settings', $buffer_buttons_content);
        
        // Google Plus
        $google_plusone_buttons_content .= '<p>' . __("Choose which Google +1 button to display") . ':<br />
					<select name="plusone_style">';
        foreach (array('small', 'medium', 'standard', 'tall') as $plusone_style) {
            $google_plusone_buttons_content .= '<option value="' . $plusone_style . '" ' . selected($socialize_settings['plusone_style'], $plusone_style, false) . '>' . $plusone_style . '</option>';
        }
        $google_plusone_buttons_content .= '</select></p>';
        $wrapped_content .= self::socialize_postbox('socialize-settings-buttons-google-plusone', 'Google +1 Button Settings', $google_plusone_buttons_content);

        // LinkedIn
        $linkedin_buttons_content .= '<p>' . __("Choose which LinkedIn button to display") . ':<br />
					<select name="linkedin_counter">';
        foreach (array('top', 'right', 'none') as $linkedin_counter) {
            $linkedin_buttons_content .= '<option value="' . $linkedin_counter . '" ' . selected($socialize_settings['linkedin_counter'], $linkedin_counter, false) . '>' . $linkedin_counter . '</option>';
        }
        $linkedin_buttons_content .= '</select></p>';
        $wrapped_content .= self::socialize_postbox('socialize-settings-buttons-linkedin', 'LinkedIn Button Settings', $linkedin_buttons_content);
        
        // Digg
        $digg_buttons_content .= '<p>' . __("Choose which Digg button to display") . ':<br />
					<select name="digg_size">';
        foreach (array('Wide' => 'DiggWide', 'Medium' => 'DiggMedium', 'Compact' => 'DiggCompact', 'Icon' => 'DiggIcon') as $digg_size => $digg_size_value) {
            $digg_buttons_content .= '<option value="' . $digg_size_value . '" ' . selected($socialize_settings['digg_size'], $digg_size_value, false) . '>' . $digg_size . '</option>';
        }
        $digg_buttons_content .= '</select></p>';
        $wrapped_content .= self::socialize_postbox('socialize-settings-buttons-digg', 'Digg Button Settings', $digg_buttons_content);

        self::socialize_admin_wrap('Socialize: Button Settings', $wrapped_content);
    }

    //=============================================
    // Process contact page form data
    //=============================================
    function process_socialize_services_admin() {
        if (!empty($_POST['socialize_option_submitted'])) {
            if (strstr($_GET['page'], "socialize") && check_admin_referer('socialize-update-services_options')) {
                $socialize_settings = socializeWP::get_options();

                if (isset($_POST['socialize_text'])) {
                    $socialize_settings['socialize_text'] = stripslashes($_POST['socialize_text']);
                }
                if (isset($_POST['socialize_twitterWidget'])) {
                    $socialize_settings['socialize_twitterWidget'] = $_POST['socialize_twitterWidget'];
                }
                if (isset($_POST['socialize_fbWidget'])) {
                    $socialize_settings['socialize_fbWidget'] = $_POST['socialize_fbWidget'];
                }
                if (isset($_POST['fb_layout'])) {
                    $socialize_settings['fb_layout'] = $_POST['fb_layout'];
                }
                if (isset($_POST['fb_showfaces'])) {
                    $socialize_settings['fb_showfaces'] = $_POST['fb_showfaces'];
                }
                if (isset($_POST['fb_verb'])) {
                    $socialize_settings['fb_verb'] = $_POST['fb_verb'];
                }
                if (isset($_POST['fb_font'])) {
                    $socialize_settings['fb_font'] = $_POST['fb_font'];
                }
                if (isset($_POST['fb_color'])) {
                    $socialize_settings['fb_color'] = $_POST['fb_color'];
                }
                if (isset($_POST['fb_width'])) {
                    $socialize_settings['fb_width'] = $_POST['fb_width'];
                }
                if (isset($_POST['fb_sendbutton'])) {
                    $socialize_settings['fb_sendbutton'] = $_POST['fb_sendbutton'];
                }
                if (isset($_POST['socialize_twitter_source'])) {
                    $socialize_settings['socialize_twitter_source'] = $_POST['socialize_twitter_source'];
                }
                if (isset($_POST['socialize_topsy_theme'])) {
                    $socialize_settings['socialize_topsy_theme'] = $_POST['socialize_topsy_theme'];
                }
                if (isset($_POST['socialize_topsy_size'])) {
                    $socialize_settings['socialize_topsy_size'] = $_POST['socialize_topsy_size'];
                }
                if (isset($_POST['socialize_twitter_related'])) {
                    $socialize_settings['socialize_twitter_related'] = $_POST['socialize_twitter_related'];
                }
                if (isset($_POST['socialize_twitter_count'])) {
                    $socialize_settings['socialize_twitter_count'] = $_POST['socialize_twitter_count'];
                }
                if (isset($_POST['socialize_tweetmeme_style'])) {
                    $socialize_settings['socialize_tweetmeme_style'] = $_POST['socialize_tweetmeme_style'];
                }
                if (isset($_POST['socialize_tweetcount_via'])) {
                    $socialize_settings['socialize_tweetcount_via'] = $_POST['socialize_tweetcount_via'];
                }
                if (isset($_POST['socialize_tweetcount_links'])) {
                    $socialize_settings['socialize_tweetcount_links'] = $_POST['socialize_tweetcount_links'];
                }
                if (isset($_POST['socialize_tweetcount_size'])) {
                    $socialize_settings['socialize_tweetcount_size'] = $_POST['socialize_tweetcount_size'];
                }
                if (isset($_POST['socialize_tweetcount_background'])) {
                    $socialize_settings['socialize_tweetcount_background'] = $_POST['socialize_tweetcount_background'];
                }
                if (isset($_POST['socialize_tweetcount_border'])) {
                    $socialize_settings['socialize_tweetcount_border'] = $_POST['socialize_tweetcount_border'];
                }
                if (isset($_POST['reddit_type'])) {
                    $socialize_settings['reddit_type'] = $_POST['reddit_type'];
                }
                if (isset($_POST['reddit_bgcolor'])) {
                    $socialize_settings['reddit_bgcolor'] = $_POST['reddit_bgcolor'];
                }
                if (isset($_POST['reddit_bordercolor'])) {
                    $socialize_settings['reddit_bordercolor'] = $_POST['reddit_bordercolor'];
                }
                if (isset($_POST['su_type'])) {
                    $socialize_settings['su_type'] = $_POST['su_type'];
                }
                if (isset($_POST['buzz_style'])) {
                    $socialize_settings['buzz_style'] = $_POST['buzz_style'];
                }
                if (isset($_POST['plusone_style'])) {
                    $socialize_settings['plusone_style'] = $_POST['plusone_style'];
                }
                if (isset($_POST['digg_size'])) {
                    $socialize_settings['digg_size'] = $_POST['digg_size'];
                }
                if (isset($_POST['yahoo_badgetype'])) {
                    $socialize_settings['yahoo_badgetype'] = $_POST['yahoo_badgetype'];
                }
                if (isset($_POST['linkedin_counter'])) {
                    $socialize_settings['linkedin_counter'] = $_POST['linkedin_counter'];
                }
                if (isset($_POST['pinterest_counter'])) {
                    $socialize_settings['pinterest_counter'] = $_POST['pinterest_counter'];
                }
                if (isset($_POST['buffer_counter'])) {
                    $socialize_settings['buffer_counter'] = $_POST['buffer_counter'];
                }

                echo "<div id=\"updatemessage\" class=\"updated fade\"><p>Socialize settings updated.</p></div>\n";
                echo "<script type=\"text/javascript\">setTimeout(function(){jQuery('#updatemessage').hide('slow');}, 3000);</script>";

                socializeWP::update_options($socialize_settings);
            }
        }//updated
        $socialize_settings = socializeWP::get_options();
        return $socialize_settings;
    }

    //=============================================
    // Contact page options
    //=============================================
    function socialize_settings_admin() {
        $socialize_settings = self::process_socialize_settings_admin();
        $socializemeta = explode(',', $socialize_settings['sharemeta']);
        $socialize_buttons = self::sort_buttons_array($socializemeta);

        $wrapped_content = "";
        $bitly_content = "";
        $facebook_content = "";
        $general_content = "";
        $default_content = "";
        $og_content = "";

        if (function_exists('wp_nonce_field')) {
            $default_content .= wp_nonce_field('socialize-update-settings_options', '_wpnonce', true, false);
        }

        $default_content .= '<p>Rearrange the buttons by <em>clicking</em> and <em>dragging</em></p>';
        $default_content .= '<div id="socialize-div1"><strong>InLine Social Buttons</strong><ul id="inline-sortable">';
        foreach ($socialize_buttons[0] as $socialize_button) {
            $checkbox_class = str_replace(" ", "-", strtolower($socialize_buttons[2][$socialize_button]));
            $checkbox_class = str_replace("+", "plus", $checkbox_class);
            $default_content .= '<li class="ui-state-default"><label class="selectit"><div class="socialize-sm-icon-list socialize-settings-buttons-' . $checkbox_class . '-icon"></div><input value="' . $socialize_button . '" type="checkbox" name="socialize_buttons[]" id="post-share-' . $socialize_button . '"' . checked(in_array($socialize_button, $socializemeta), true, false) . '/> <span>' . __($socialize_buttons[2][$socialize_button]) . '</span></label></li>';
        }
        $default_content .= '</ul></div><div id="socialize-div2"><strong>Alert Box Social Buttons</strong><br /><ul id="alert-sortable">';
        foreach ($socialize_buttons[1] as $socialize_button) {
            $checkbox_class = str_replace(" ", "-", strtolower($socialize_buttons[2][$socialize_button]));
            $checkbox_class = str_replace("+", "plus", $checkbox_class);
            $default_content .= '<li class="ui-state-default"><label class="selectit"><div class="socialize-sm-icon-list socialize-settings-buttons-' . $checkbox_class . '-icon"></div><input value="' . $socialize_button . '" type="checkbox" name="socialize_buttons[]" id="post-share-' . $socialize_button . '"' . checked(in_array($socialize_button, $socializemeta), true, false) . '/> <span>' . __($socialize_buttons[2][$socialize_button]) . '</span></label></li>';
        }
        $default_content .= '</ul></div><div class="clear"></div>';

        $default_content .= '<p><strong>' . __("'Call To Action' Box Text") . '</strong><br />
                                <textarea name="socialize_text" rows="4" style="width:100%;">' . $socialize_settings['socialize_text'] . '</textarea><br />
                                <small>Here you can change your \'Call To Action\' box text. (If you are using a 3rd party site to handle your RSS, like FeedBurner, please make sure any links to your RSS are updated.)<br />
                                There is also an option below that will save your settings and overwrite all individual post and page button settings.</small></p>';
        
        $default_content .= '<p>
            <input type="submit" name="socialize_option_submitted" class="button-primary" value="Save Changes" />
            <span class="socialize-warning">
                <select name="socialize_default_type">';
        foreach (array('Buttons and Call to Action' => 'buttons/cta', 'Buttons' => 'buttons', 'Call to Action' => 'cta') as $socialize_default_name => $socialize_default_type) {
            $default_content .= '<option value="' . $socialize_default_type . '">' . $socialize_default_name . '</option>';
        }
        $default_content .= '</select> ';
        $default_content .= '<input type="submit" name="socialize_default_reset" class="button-primary" value="Overwrite All Post/Page Settings" /></span></p>';
        //$default_content .= '<p>The button below will save your settings and overwrite all individual post and page button settings.</p>';
        $wrapped_content .= self::socialize_postbox('socialize-settings-default', 'Default Setup', $default_content);

        $bitly_content .= '<p>' . __("Bitly Username") . '<br />
					<input type="text" name="socialize_bitly_name" value="' . $socialize_settings['socialize_bitly_name'] . '" /></p>';
        $bitly_content .= '<p>' . __("Bitly API Key") . '<br />
					<input type="text" name="socialize_bitly_key" value="' . $socialize_settings['socialize_bitly_key'] . '" />
					<small>If you have a Bitly account, you can find your API key <a href="http://bit.ly/a/your_api_key/" target="_blank">here</a>.</small></p>';
        $wrapped_content .= self::socialize_postbox('socialize-settings-bitly', 'Bitly Settings', $bitly_content);

        $og_content .= '<p>' . __("Enable Open Graph") . '<br />
					<input type="checkbox" name="socialize_og" ' . checked($socialize_settings['socialize_og'], 'on', false) . ' />
					<small>Uncheck this if you do not want to insert <a href="http://developers.facebook.com/docs/opengraph/" target="_blank">open graph</a> meta data into your HTML head.</small></p>';
        $og_content .= '<p>' . __("Facebook App ID") . '<br />
				      <input type="text" name="socialize_fb_appid" value="' . $socialize_settings['socialize_fb_appid'] . '" />
                                      <small>You can set up and get your Facebook App ID <a href="http://www.facebook.com/developers/apps.php" target="_blank">here</a>.</small></p>';
        $og_content .= '<p>' . __("Facebook Admin IDs") . '<br />
				      <input type="text" name="socialize_fb_adminid" value="' . $socialize_settings['socialize_fb_adminid'] . '" />
                                      <small>A comma-separated list of Facebook user IDs. Find it <a href="http://apps.facebook.com/whatismyid" targe="_blank">here</a>.</small></p>';



        $og_content .= '<p>' . __("Facebook Page ID") . '<br />
				      <input type="text" name="socialize_fb_pageid" value="' . $socialize_settings['socialize_fb_pageid'] . '" />
                                      <small>A Facebook Page ID.</small></p>';




        $wrapped_content .= self::socialize_postbox('socialize-settings-facebook', 'Open Graph Settings', $og_content);


        self::socialize_admin_wrap('Socialize: General Settings', $wrapped_content);
    }

    //=============================================
    // Process contact page form data
    //=============================================
    function process_socialize_settings_admin() {
        if (!empty($_POST['socialize_default_reset'])) {
            if (strstr($_GET['page'], "socialize") && check_admin_referer('socialize-update-settings_options')) {
                $socialize_settings = socializeWP::get_options();
                // If Buttons or Both
                if($_POST['socialize_default_type'] == 'buttons/cta' || $_POST['socialize_default_type'] == 'buttons'){
                    $socializemetaarray = array();
                    if (isset($_POST['socialize_buttons'])) {
                        foreach ($_POST['socialize_buttons'] as $button) {
                            if (($button > 0)) {
                                array_push($socializemetaarray, $button);
                            }
                        }
                    }
                    $socializemeta = implode(',', $socializemetaarray);
                    $socialize_settings['sharemeta'] = $socializemeta;
                }
                // If CTA or Both
                if($_POST['socialize_default_type'] == 'buttons/cta' || $_POST['socialize_default_type'] == 'cta'){
                    $socialize_text = $_POST['socialize_text'];
                    $socialize_settings['socialize_text'] = stripslashes($_POST['socialize_text']);
                }
                // Loop through all posts with socialize custom meta and update with new settings
                $mod_posts = new WP_Query(
                        array(
                            'meta_key' => 'socialize',
                            'post_status' => array('publish', 'pending', 'draft', 'future', 'private'),
                            'post_type' => 'any',
                            'posts_per_page' => -1
                        )
                    );
                while ( $mod_posts->have_posts() ) : $mod_posts->the_post();
                    if($_POST['socialize_default_type'] == 'buttons/cta' || $_POST['socialize_default_type'] == 'buttons')
                        update_post_meta(get_the_ID(), 'socialize', $socializemeta);
                    if($_POST['socialize_default_type'] == 'buttons/cta' || $_POST['socialize_default_type'] == 'cta')
                        update_post_meta(get_the_ID(), 'socialize_text', $socialize_text);
                endwhile;
                wp_reset_postdata();

                // Update settings
                socializeWP::update_options($socialize_settings);

                // Update user
                echo "<div id=\"updatemessage\" class=\"updated fade\"><p>Default Socialize settings updated.</p></div>\n";
                echo "<script type=\"text/javascript\">setTimeout(function(){jQuery('#updatemessage').hide('slow');}, 3000);</script>";
            }
        }

        if (!empty($_POST['socialize_option_submitted'])) {
            if (strstr($_GET['page'], "socialize") && check_admin_referer('socialize-update-settings_options')) {
                $socialize_settings = socializeWP::get_options();
                $socializemetaarray = array();
                if (isset($_POST['socialize_buttons'])) {
                    foreach ($_POST['socialize_buttons'] as $button) {
                        if (($button > 0)) {
                            array_push($socializemetaarray, $button);
                        }
                    }
                }
                $socializemeta = implode(',', $socializemetaarray);
                $socialize_settings['sharemeta'] = $socializemeta;
                if (isset($_POST['socialize_bitly_name'])) {
                    $socialize_settings['socialize_bitly_name'] = $_POST['socialize_bitly_name'];
                }
                if (isset($_POST['socialize_bitly_key'])) {
                    $socialize_settings['socialize_bitly_key'] = $_POST['socialize_bitly_key'];
                }
                if (isset($_POST['socialize_fb_appid'])) {
                    $socialize_settings['socialize_fb_appid'] = $_POST['socialize_fb_appid'];
                }
                if (isset($_POST['socialize_fb_adminid'])) {
                    $socialize_settings['socialize_fb_adminid'] = $_POST['socialize_fb_adminid'];
                }
                if (isset($_POST['socialize_og'])) {
                    $socialize_settings['socialize_og'] = $_POST['socialize_og'];
                } else {
                    $socialize_settings['socialize_og'] = '';
                }
                if (isset($_POST['socialize_fb_pageid'])) {
                    $socialize_settings['socialize_fb_pageid'] = $_POST['socialize_fb_pageid'];
                } else {
                    $socialize_settings['socialize_fb_pageid'] = '';
                }

                echo "<div id=\"updatemessage\" class=\"updated fade\"><p>Socialize settings updated.</p></div>\n";
                echo "<script type=\"text/javascript\">setTimeout(function(){jQuery('#updatemessage').hide('slow');}, 3000);</script>";

                socializeWP::update_options($socialize_settings);
            }
        }//updated
        $socialize_settings = socializeWP::get_options();
        return $socialize_settings;
    }

    //=============================================
    // Create postbox for admin
    //=============================================
    function socialize_postbox($id, $title, $content) {
        $postbox_wrap = "";
        $postbox_wrap .= '<div id="' . $id . '" class="postbox socialize-admin">';
        $postbox_wrap .= '<div class="handlediv" title="Click to toggle"><br /></div>';
        $postbox_wrap .= '<h3 class="hndle"><div class="socialize-sm-icon"></div><span>' . $title . '</span></h3>';
        $postbox_wrap .= '<div class="inside">' . $content . '</div>';
        $postbox_wrap .= '</div>';
        return $postbox_wrap;
    }

    //=============================================
    // Admin page wrap
    //=============================================
    function socialize_admin_wrap($title, $content) {
        ?>
        <div class="wrap">
            <div class="dashboard-widgets-wrap">
                <div class="socialize-icon icon32"></div>
                <h2 class="nav-tab-wrapper socialize-tab-wrapper">
        <?php
        $tabs = self::admin_tabs();

        if (isset($_GET['tab'])) {
            $current_tab = $_GET['tab'];
        } else {
            $current_tab = 'general';
        }

        foreach ($tabs as $name => $tab_data) {
            echo '<a href="' . admin_url('options-general.php?page=socialize&tab=' . $name) . '" class="nav-tab ';
            if ($current_tab == $name)
                echo 'nav-tab-active';
            echo '">' . $tab_data['title'] . '</a>';
        }

        do_action('socialize_settings_tabs');
        ?>
                </h2>
                <form method="post" action="">
                    <div id="dashboard-widgets" class="metabox-holder">
                        <div class="postbox-container" style="width:60%;">
                            <div class="meta-box-sortables ui-sortable">
        <?php
        echo $content;
        ?>
                                <p class="submit">
                                    <input type="submit" name="socialize_option_submitted" class="button-primary" value="Save Changes" />
                                </p>
                            </div>
                        </div>
                        <div class="postbox-container" style="width:40%;">
                            <div class="meta-box-sortables ui-sortable">
        <?php
        echo self::socialize_show_donate();
        echo self::socialize_show_plugin_support();
        echo self::socialize_show_blogfeed();
        ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php
    }

    function sort_buttons_array($socializemeta) {
        $inline_buttons_array = SocializeServices::get_button_array('inline');
        $alert_buttons_array = SocializeServices::get_button_array('action');
        $r_socializemeta = array_reverse($socializemeta);

        $socialize_buttons = array();
        $socialize_buttons[0] = $inline_buttons_array;
        $socialize_buttons[1] = $alert_buttons_array;
        
        $service_names_array = array();
        foreach (socializeWP::$socialize_services as $service_name=>$service_data){
            if(isset($service_data['inline']))
                $service_names_array[$service_data['inline']] = $service_name;
            if(isset($service_data['action']))
                $service_names_array[$service_data['action']] = $service_name;
        }

        $service_names_array = apply_filters('socialize-sort_buttons_array', $service_names_array);

        $socialize_buttons[2] = $service_names_array;

        foreach ($r_socializemeta as $socialize_button) {
            if (in_array($socialize_button, $inline_buttons_array)) {
                array_splice($inline_buttons_array, array_search($socialize_button, $inline_buttons_array), 1);
                array_unshift($inline_buttons_array, $socialize_button);
                $socialize_buttons[0] = $inline_buttons_array;
            } else if (in_array($socialize_button, $alert_buttons_array)) {
                array_splice($alert_buttons_array, array_search($socialize_button, $alert_buttons_array), 1);
                array_unshift($alert_buttons_array, $socialize_button);
                $socialize_buttons[1] = $alert_buttons_array;
            }
        }
        return $socialize_buttons;
    }

}
?>