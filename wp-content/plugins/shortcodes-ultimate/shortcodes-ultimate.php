<?php
	/*
	  Plugin Name: Shortcodes Ultimate
	  Plugin URI: http://shortcodes-ultimate.com/
	  Version: 3.9.5
	  Author: Vladimir Anokhin
	  Author URI: http://gndev.info/
	  Description: Provides support for many easy to use shortcodes
	  Text Domain: shortcodes-ultimate
	  Domain Path: /languages
	  License: GPL2
	 */

	// Load libs
	require_once 'lib/available.php';
	require_once 'lib/admin.php';
	require_once 'lib/color.php';
	require_once 'lib/csv.php';
	require_once 'lib/media.php';
	require_once 'lib/twitter.php';
	require_once 'lib/images.php';
	require_once 'lib/shortcodes.php';
	require_once 'lib/widget.php';

	/**
	 * Plugin initialization
	 */
	function su_plugin_init() {

		// Make plugin available for translation
		load_plugin_textdomain( 'shortcodes-ultimate', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		// Enable shortcodes in text widgets
		add_filter( 'widget_text', 'do_shortcode' );

		// Enable auto-formatting
		if ( get_option( 'su_disable_custom_formatting' ) != 'on' ) {

			// Disable WordPress native formatters
			remove_filter( 'the_content', 'wpautop' );
			remove_filter( 'the_content', 'wptexturize' );

			// Apply custom formatter function
			add_filter( 'the_content', 'su_custom_formatter', 99 );
			add_filter( 'widget_text', 'su_custom_formatter', 99 );
		}

		// Fix for large posts, http://core.trac.wordpress.org/ticket/8553
		@ini_set( 'pcre.backtrack_limit', 500000 );

		// Register styles
		wp_register_style( 'shortcodes-ultimate', su_plugin_url() . '/css/style.css', false, su_get_version(), 'all' );
		wp_register_style( 'shortcodes-ultimate-admin', su_plugin_url() . '/css/admin.css', false, su_get_version(), 'all' );
		wp_register_style( 'shortcodes-ultimate-generator', su_plugin_url() . '/css/generator.css', false, su_get_version(), 'all' );
		wp_register_style( 'nivo-slider', su_plugin_url() . '/css/nivoslider.css', false, su_get_version(), 'all' );
		wp_register_style( 'jcarousel', su_plugin_url() . '/css/jcarousel.css', false, su_get_version(), 'all' );
		wp_register_style( 'codemirror', su_plugin_url() . '/css/codemirror.css', false, su_get_version(), 'all' );
		wp_register_style( 'codemirror-css', su_plugin_url() . '/css/codemirror-css.css', false, su_get_version(), 'all' );
		wp_register_style( 'chosen', su_plugin_url() . '/css/chosen.css', false, su_get_version(), 'all' );

		// Register scripts
		wp_register_script( 'shortcodes-ultimate', su_plugin_url() . '/js/init.js', array( 'jquery' ), su_get_version(), false );
		wp_register_script( 'shortcodes-ultimate-admin', su_plugin_url() . '/js/admin.js', array( 'jquery' ), su_get_version(), false );
		wp_register_script( 'shortcodes-ultimate-generator', su_plugin_url() . '/js/generator.js', array( 'jquery' ), su_get_version(), false );
		wp_register_script( 'nivo-slider', su_plugin_url() . '/js/nivoslider.js', false, su_get_version(), false );
		wp_register_script( 'jcarousel', su_plugin_url() . '/js/jcarousel.js', false, su_get_version(), false );
		wp_register_script( 'codemirror', su_plugin_url() . '/js/codemirror.js', false, su_get_version(), false );
		wp_register_script( 'codemirror-css', su_plugin_url() . '/js/codemirror-css.js', false, su_get_version(), false );
		wp_register_script( 'chosen', su_plugin_url() . '/js/chosen.js', false, su_get_version(), false );
		wp_register_script( 'ajax-form', su_plugin_url() . '/js/jquery.form.js', false, su_get_version(), false );
		wp_register_script( 'jwplayer', su_plugin_url() . '/js/jwplayer.js', false, su_get_version(), false );

		// Front-end scripts and styles
		if ( !is_admin() ) {

			$disabled_scripts = get_option( 'su_disabled_scripts' );
			$disabled_styles = get_option( 'su_disabled_styles' );

			// Enqueue styles
			if ( !isset( $disabled_styles['nivo-slider'] ) ) {
				wp_enqueue_style( 'nivo-slider' );
			}
			if ( !isset( $disabled_styles['jcarousel'] ) ) {
				wp_enqueue_style( 'jcarousel' );
			}
			if ( !isset( $disabled_styles['style'] ) ) {
				wp_enqueue_style( 'shortcodes-ultimate' );
			}

			// Enqueue scripts
			if ( !isset( $disabled_scripts['jquery'] ) ) {
				wp_enqueue_script( 'jquery' );
			}
			if ( !isset( $disabled_scripts['jwplayer'] ) ) {
				wp_enqueue_script( 'jwplayer' );
			}
			if ( !isset( $disabled_scripts['nivo-slider'] ) ) {
				wp_enqueue_script( 'nivo-slider' );
			}
			if ( !isset( $disabled_scripts['jcarousel'] ) ) {
				wp_enqueue_script( 'jcarousel' );
			}
			if ( !isset( $disabled_scripts['init'] ) ) {
				wp_enqueue_script( 'shortcodes-ultimate' );
			}
		}

		// Back-end scripts and styles
		elseif ( isset( $_GET['page'] ) && $_GET['page'] == 'shortcodes-ultimate' ) {

			// Enqueue styles
			wp_enqueue_style( 'codemirror' );
			wp_enqueue_style( 'codemirror-css' );
			wp_enqueue_style( 'shortcodes-ultimate-admin' );

			// Enqueue scripts
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'codemirror' );
			wp_enqueue_script( 'codemirror-css' );
			wp_enqueue_script( 'ajax-form' );
			wp_enqueue_script( 'shortcodes-ultimate-admin' );
		}

		// Scipts and stylesheets for editing pages (shortcode generator popup)
		elseif ( is_admin() ) {

			// Get current page type
			global $pagenow;

			// Pages for including
			$su_generator_includes_pages = array( 'post.php', 'edit.php', 'post-new.php', 'index.php', 'edit-tags.php', 'widgets.php' );

			if ( in_array( $pagenow, $su_generator_includes_pages ) ) {
				// Enqueue styles
				wp_enqueue_style( 'thickbox' );
				wp_enqueue_style( 'farbtastic' );
				wp_enqueue_style( 'chosen' );
				wp_enqueue_style( 'shortcodes-ultimate-generator' );

				// Enqueue scripts
				wp_enqueue_script( 'jquery' );
				wp_enqueue_script( 'thickbox' );
				wp_enqueue_script( 'farbtastic' );
				wp_enqueue_script( 'chosen' );
				wp_enqueue_script( 'shortcodes-ultimate-generator' );
			}
		}

		// Register shortcodes
		foreach ( su_shortcodes() as $shortcode => $params ) {
			if ( $params['type'] != 'opengroup' && $params['type'] != 'closegroup' )
				add_shortcode( su_compatibility_mode_prefix() . $shortcode, 'su_' . $shortcode . '_shortcode' );
		}
	}

	add_action( 'init', 'su_plugin_init' );

	/**
	 * Returns current plugin version.
	 *
	 * @return string Plugin version
	 */
	function su_get_version() {
		if ( !function_exists( 'get_plugins' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		$plugin_folder = get_plugins( '/' . plugin_basename( dirname( __FILE__ ) ) );
		$plugin_file = basename( ( __FILE__ ) );
		return $plugin_folder[$plugin_file]['Version'];
	}

	/**
	 * Returns current plugin url
	 *
	 * @return string Plugin url
	 */
	function su_plugin_url() {
		return plugins_url( basename( __FILE__, '.php' ), dirname( __FILE__ ) );
	}

	/**
	 * Shortcode names prefix in compatibility mode
	 *
	 * @return string Special prefix
	 */
	function su_compatibility_mode_prefix() {
		$prefix = ( get_option( 'su_compatibility_mode' ) == 'on' ) ? 'gn_' : '';
		return $prefix;
	}

	/**
	 * Hook to translate plugin information
	 */
	function su_add_locale_strings() {
		$strings = __( 'Shortcodes Ultimate', 'shortcodes-ultimate' ) . __( 'Vladimir Anokhin', 'shortcodes-ultimate' ) . __( 'Provides support for many easy to use shortcodes', 'shortcodes-ultimate' );
	}

	/*
	 * Custom shortcode function for nested shortcodes support
	 */

	function su_do_shortcode( $content, $modifier ) {
		if ( strpos( $content, '[_' ) !== false ) {
			$content = preg_replace( '@(\[_*)_(' . $modifier . '|/)@', "$1$2", $content );
		}
		return do_shortcode( $content );
	}

	/**
	 * Disable auto-formatting for shortcodes
	 *
	 * @param string $content
	 * @return string Formatted content with clean shortcodes content
	 */
	function su_custom_formatter( $content ) {
		$new_content = '';

		// Matches the contents and the open and closing tags
		$pattern_full = '{(\[raw\].*?\[/raw\])}is';

		// Matches just the contents
		$pattern_contents = '{\[raw\](.*?)\[/raw\]}is';

		// Divide content into pieces
		$pieces = preg_split( $pattern_full, $content, -1, PREG_SPLIT_DELIM_CAPTURE );

		// Loop over pieces
		foreach ( $pieces as $piece ) {

			// Look for presence of the shortcode
			if ( preg_match( $pattern_contents, $piece, $matches ) ) {

				// Append to content (no formatting)
				$new_content .= $matches[1];
			} else {

				// Format and append to content
				$new_content .= wptexturize( wpautop( $piece ) );
			}
		}

		return $new_content;
	}

	/**
	 * Print custom CSS styles in wp_head
	 *
	 * @return string Custom CSS
	 */
	function su_print_custom_css() {
		if ( get_option( 'su_custom_css' ) ) {
			echo "\n<!-- Shortcodes Ultimate custom CSS - begin -->\n<style type='text/css'>\n" . str_replace( '%theme%', get_template_directory_uri(), get_option( 'su_custom_css' ) ) . "\n</style>\n<!-- Shortcodes Ultimate custom CSS - end -->\n\n";
		}
	}

	add_action( 'wp_head', 'su_print_custom_css' );

	/**
	 * Manage settings
	 */
	function su_manage_settings() {

		// Insert default CSS
		if ( !get_option( 'su_custom_css' ) ) {
			$default_css = '';
			update_option( 'su_custom_css', $default_css );
		}

		// Save main settings
		if ( isset( $_POST['save'] ) && $_GET['page'] == 'shortcodes-ultimate' ) {
			update_option( 'su_disable_custom_formatting', $_POST['su_disable_custom_formatting'] );
			update_option( 'su_compatibility_mode', $_POST['su_compatibility_mode'] );
			update_option( 'su_disabled_scripts', $_POST['su_disabled_scripts'] );
			update_option( 'su_disabled_styles', $_POST['su_disabled_styles'] );
		}

		// Save custom css
		if ( isset( $_POST['save-css'] ) && $_GET['page'] == 'shortcodes-ultimate' ) {
			update_option( 'su_custom_css', $_POST['su_custom_css'] );
		}
	}

	add_action( 'admin_init', 'su_manage_settings' );

	/**
	 * Add settings link to plugins dashboard
	 *
	 * @param array $links Links
	 * @return array Links
	 */
	function su_add_settings_link( $links ) {
		$links[] = '<a href="' . admin_url( 'options-general.php?page=shortcodes-ultimate' ) . '">' . __( 'Settings', 'shortcodes-ultimate' ) . '</a>';
		$links[] = '<a href="' . admin_url( 'options-general.php?page=shortcodes-ultimate#tab-3' ) . '">' . __( 'Docs', 'shortcodes-ultimate' ) . '</a>';
		return $links;
	}

	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'su_add_settings_link', -10 );

	/**
	 * Print notification if options saved
	 */
	function su_save_notification() {

		// Save main settings
		if ( isset( $_POST['save'] ) && $_GET['page'] == 'shortcodes-ultimate' ) {
			echo '<div class="updated"><p><strong>' . __( 'Settings saved', 'shortcodes-ultimate' ) . '</strong></p></div>';
		}

		// Save custom css
		if ( isset( $_POST['save-css'] ) && $_GET['page'] == 'shortcodes-ultimate' ) {
			echo '<div class="updated"><p><strong>' . __( 'Custom CSS saved', 'shortcodes-ultimate' ) . '</strong></p></div>';
		}
	}

	/**
	 * Add generator button to Upload/Insert buttons
	 */
	function su_add_generator_button( $page = null, $target = null ) {
		echo '<a href="#TB_inline?width=640&height=600&inlineId=su-generator-wrap" class="thickbox" title="' . __( 'Insert shortcode', 'shortcodes-ultimate' ) . '" data-page="' . $page . '" data-target="' . $target . '"><img src="' . su_plugin_url() . '/images/admin/media-icon.png" alt="" /></a>';
	}

	add_action( 'media_buttons', 'su_add_generator_button', 100 );

	/**
	 * Generator popup box
	 */
	function su_generator_popup() {
		?>
		<div id="su-generator-wrap" style="display:none">
			<div id="su-generator">
				<div id="su-generator-shell">
					<div id="su-generator-header">
						<select id="su-generator-select" data-placeholder="<?php _e( 'Select shortcode', 'shortcodes-ultimate' ); ?>" data-no-results-text="<?php _e( 'Shortcode not found', 'shortcodes-ultimate' ); ?>">
							<option value="raw"></option>
							<?php
							foreach ( su_shortcodes() as $name => $shortcode ) {

								// Open optgroup
								if ( $shortcode['type'] == 'opengroup' )
									echo '<optgroup label="' . $shortcode['name'] . '">';

								// Close optgroup
								elseif ( $shortcode['type'] == 'closegroup' )
									echo '</optgroup>';

								// Option
								else
									echo '<option value="' . $name . '">' . strtoupper( $name ) . ':&nbsp;&nbsp;' . $shortcode['desc'] . '</option>';
							}
							?>
						</select>
						<div id="su-generator-tools">
							<a href="<?php echo admin_url( 'options-general.php?page=shortcodes-ultimate' ); ?>" target="_blank" title="<?php _e( 'Settings', 'shortcodes-ultimate' ); ?>"><?php _e( 'Settings', 'shortcodes-ultimate' ); ?></a> /
							<a href="http://wordpress.org/support/plugin/shortcodes-ultimate" target="_blank" title="<?php _e( 'Support forum', 'shortcodes-ultimate' ); ?>"><?php _e( 'Support forum', 'shortcodes-ultimate' ); ?></a>
						</div>
					</div>
					<div id="su-generator-settings"></div>
					<input type="hidden" name="su-generator-url" id="su-generator-url" value="<?php echo su_plugin_url(); ?>" />
					<input type="hidden" name="su-compatibility-mode-prefix" id="su-compatibility-mode-prefix" value="<?php echo su_compatibility_mode_prefix(); ?>" />
				</div>
			</div>
		</div>
		<?php
	}

	add_action( 'admin_footer', 'su_generator_popup' );
?>