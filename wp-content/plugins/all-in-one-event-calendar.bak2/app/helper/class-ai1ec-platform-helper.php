<?php
//
// class-ai1ec-platform-helper.php
// all-in-one-event-calendar
//
// Created by The Seed Studio on 2012-04-10.
//

/**
 * Ai1ec_Platform_Helper class
 *
 * @package Helpers
 * @author time.ly
 **/
class Ai1ec_Platform_Helper {
	/**
	 * Class instance.
	 *
	 * @var null | object
	 */
	private static $_instance = NULL;

	/**
	 * Constructor
	 *
	 * Default constructor.
	 */
	private function __construct() { }

	/**
	 * Return singleton instance.
	 *
	 * @return object
	 */
	static function get_instance() {
		if( self::$_instance === NULL ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Modifies default role permissions.
	 *
	 * @return void
	 */
	function modify_roles() {
		global $ai1ec_settings;

		// Modify capabilities of most roles; remove roles if in platform mode, or
		// add them back if not.
		$action = $ai1ec_settings->event_platform_active ? 'remove_cap' : 'add_cap';
		foreach( array( 'administrator', 'editor', 'author', 'contributor' ) as $role_name ) {
			$role = get_role( $role_name );

			switch( $role_name ) {
				case 'administrator':
					$role->$action( 'activate_plugins' );
					$role->$action( 'delete_plugins' );
					$role->$action( 'delete_themes' );
					$role->$action( 'edit_dashboard' );
					$role->$action( 'edit_plugins' );
					$role->$action( 'edit_theme_options' );
					$role->$action( 'edit_themes' );
					$role->$action( 'export' );
					$role->$action( 'import' );
					$role->$action( 'install_plugins' );
					$role->$action( 'install_themes' );
					$role->$action( 'manage_options' );
					$role->$action( 'switch_themes' );
				case 'editor':
					$role->$action( 'delete_others_pages' );
					$role->$action( 'delete_others_posts' );
					$role->$action( 'delete_pages' );
					$role->$action( 'delete_private_pages' );
					$role->$action( 'delete_private_posts' );
					$role->$action( 'delete_published_pages' );
					$role->$action( 'edit_others_posts' );
					$role->$action( 'edit_pages' );
					$role->$action( 'edit_others_pages' );
					$role->$action( 'edit_private_pages' );
					$role->$action( 'edit_private_posts' );
					$role->$action( 'edit_published_pages' );
					$role->$action( 'manage_categories' );
					$role->$action( 'manage_links' );
					$role->$action( 'publish_pages' );
					$role->$action( 'read_private_pages' );
					$role->$action( 'read_private_posts' );
				case 'author':
					$role->$action( 'delete_published_posts' );
					$role->$action( 'edit_published_posts' );
					$role->$action( 'publish_posts' );
				case 'contributor':
					$role->$action( 'edit_posts' );
					$role->$action( 'delete_posts' );
			}
		}
	}

	/**
	 * Makes sure several calendar settings are set properly for Event Platform
	 * mode.
	 */
	function check_settings() {
		global $ai1ec_settings;

		// Make sure a calendar page has been defined.
		if( ! $ai1ec_settings->calendar_page_id ) {
			// Auto-create the page.
			$ai1ec_settings->calendar_page_id = $ai1ec_settings->auto_add_page( __( 'Calendar', AI1EC_PLUGIN_NAME ) );
			$ai1ec_settings->save();
		}

		// Make sure the defined calendar page is the default WordPress front page.
		update_option( 'page_on_front', $ai1ec_settings->calendar_page_id );
	}

	/**
	 * Change meta boxes dashboard screen for Event Platform mode.
	 *
	 * @return  void
	 */
	function modify_dashboard() {
		global $ai1ec_settings_helper;

		// Do not modify dashboard for super admins.
		if( current_user_can( 'super_admin' ) ) {
			return;
		}

		// Replace "Right Now" widget with our own.
		remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
		add_meta_box(
			'dashboard_right_now',
			__( 'Right Now' ),
			array( &$this, 'dashboard_right_now' ),
			'dashboard',
			'side',
			'high'
		);

		// Remove other widgets.
		remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
		remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );

		// Add "Calendar Tasks" widget.
		add_meta_box(
			'ai1ec-calendar-tasks',
			_x( 'Calendar Tasks', 'meta box', AI1EC_PLUGIN_NAME ),
			array( &$this, 'calendar_tasks_meta_box' ),
			'dashboard',
			'normal',
			'high'
		);
	}

	/**
	 * Custom "Right Now" dashboard widget for Calendar Administrators.
	 *
	 * @return  void
	 */
	function dashboard_right_now() {
		global $wp_registered_sidebars;

		$num_comm = wp_count_comments();

		echo "\n\t".'<div class="table table_content">';
		echo "\n\t".'<p class="sub">' . __('Content') . '</p>'."\n\t".'<table>';
		echo "\n\t".'<tr class="first">';

		do_action('right_now_content_table_end');
		echo "\n\t</table>\n\t</div>";

		echo "\n\t".'<div class="table table_discussion">';
		echo "\n\t".'<p class="sub">' . __('Discussion') . '</p>'."\n\t".'<table>';
		echo "\n\t".'<tr class="first">';

		// Total Comments
		$num = '<span class="total-count">' . number_format_i18n($num_comm->total_comments) . '</span>';
		$text = _n( 'Comment', 'Comments', $num_comm->total_comments );
		if ( current_user_can( 'moderate_comments' ) ) {
			$num = '<a href="edit-comments.php">' . $num . '</a>';
			$text = '<a href="edit-comments.php">' . $text . '</a>';
		}
		echo '<td class="b b-comments">' . $num . '</td>';
		echo '<td class="last t comments">' . $text . '</td>';

		echo '</tr><tr>';

		// Approved Comments
		$num = '<span class="approved-count">' . number_format_i18n($num_comm->approved) . '</span>';
		$text = _nx( 'Approved', 'Approved', $num_comm->approved, 'Right Now' );
		if ( current_user_can( 'moderate_comments' ) ) {
			$num = "<a href='edit-comments.php?comment_status=approved'>$num</a>";
			$text = "<a class='approved' href='edit-comments.php?comment_status=approved'>$text</a>";
		}
		echo '<td class="b b_approved">' . $num . '</td>';
		echo '<td class="last t">' . $text . '</td>';

		echo "</tr>\n\t<tr>";

		// Pending Comments
		$num = '<span class="pending-count">' . number_format_i18n($num_comm->moderated) . '</span>';
		$text = _n( 'Pending', 'Pending', $num_comm->moderated );
		if ( current_user_can( 'moderate_comments' ) ) {
			$num = "<a href='edit-comments.php?comment_status=moderated'>$num</a>";
			$text = "<a class='waiting' href='edit-comments.php?comment_status=moderated'>$text</a>";
		}
		echo '<td class="b b-waiting">' . $num . '</td>';
		echo '<td class="last t">' . $text . '</td>';

		echo "</tr>\n\t<tr>";

		// Spam Comments
		$num = number_format_i18n($num_comm->spam);
		$text = _nx( 'Spam', 'Spam', $num_comm->spam, 'comment' );
		if ( current_user_can( 'moderate_comments' ) ) {
			$num = "<a href='edit-comments.php?comment_status=spam'><span class='spam-count'>$num</span></a>";
			$text = "<a class='spam' href='edit-comments.php?comment_status=spam'>$text</a>";
		}
		echo '<td class="b b-spam">' . $num . '</td>';
		echo '<td class="last t">' . $text . '</td>';

		echo "</tr>";
		do_action('right_now_table_end');
		do_action('right_now_discussion_table_end');
		echo "\n\t</table>\n\t</div>";

		echo "\n\t".'<div class="versions">';

		// Check if search engines are blocked.
		if ( !is_network_admin() && !is_user_admin() && current_user_can('manage_options') && '1' != get_option('blog_public') ) {
			$title = apply_filters('privacy_on_link_title', __('Your site is asking search engines not to index its content') );
			$content = apply_filters('privacy_on_link_text', __('Search Engines Blocked') );

			echo "<p><a href='options-privacy.php' title='$title'>$content</a></p>";
		}

		$msg = sprintf( __('You are using <span class="b">All-in-One Event Calendar %s</span>.'), AI1EC_VERSION );
		echo "<span id='wp-version-message'>$msg</span>";


		echo "\n\t".'<br class="clear" /></div>';
		do_action( 'ai1ec_rightnow_end' );
		do_action( 'activity_box_end' );
	}

	/**
	 * Enqueue any scripts and styles in the admin side, depending on context.
	 *
	 * @return void
	 */
	function admin_enqueue_scripts( $hook_suffix ) {
		global $ai1ec_view_helper,
		       $ai1ec_settings_helper,
		       $ai1ec_settings;
		// Styles.
		$ai1ec_view_helper->admin_enqueue_style( 'ai1ec-platform', 'platform.css' );
		// ==================
		// = Dashboard only =
		// ==================
		if( $hook_suffix == 'index.php' ) {
			// Styles.
			$ai1ec_view_helper->admin_enqueue_style( 'ai1ec-settings', 'settings.css' );
			$ai1ec_view_helper->admin_enqueue_style( 'ai1ec-dashboard', 'dashboard.css' );
			$ai1ec_view_helper->admin_enqueue_style( 'timely-bootstrap', 'bootstrap.min.css' );
		}
	}

	/**
	 * Renders the contents of the Calendar Tasks meta box.
	 *
	 * @return void
	 */
	function calendar_tasks_meta_box( $object, $box ) {
		global $ai1ec_view_helper,
		       $ai1ec_settings;

		$args = array(
			'add_allowed' => current_user_can( 'edit_ai1ec_events' ),
			'edit_allowed' => current_user_can( 'edit_ai1ec_events' ),
			'categories_allowed' => current_user_can( 'manage_events_categories' ),
			'themes_allowed' => current_user_can( 'manage_ai1ec_options' ),
			'feeds_allowed' => current_user_can( 'manage_ai1ec_options' ),
			'settings_allowed' => current_user_can( 'manage_ai1ec_options' ),
			'add_url' => admin_url( 'post-new.php?post_type=' . AI1EC_POST_TYPE ),
			'edit_url' => admin_url( 'edit.php?post_type=' . AI1EC_POST_TYPE ),
			'categories_url' => admin_url( 'edit-tags.php?taxonomy=events_categories&post_type=' . AI1EC_POST_TYPE ),
			'themes_url' => admin_url( AI1EC_THEME_SELECTION_BASE_URL ),
			'feeds_url' => admin_url( AI1EC_FEED_SETTINGS_BASE_URL ),
			'settings_url' => admin_url( AI1EC_SETTINGS_BASE_URL ),
		);
		$ai1ec_view_helper->display_admin( 'calendar_tasks.php', $args );
	}

	/**
	 * Adds "Site Title" option to Ai1ec general settings.
	 */
	function ai1ec_general_settings_before() {
		?>
		<div class="blogname-container">
		  <label class="textinput" for="blogname"><?php _e( 'Site Title' ); ?>:</label>
		  <div class="alignleft"><input name="blogname" type="text" id="blogname" value="<?php form_option('blogname'); ?>" class="regular-text" /></div>
		  <br class="clear" />
		</div>
		<?php
	}

	/**
	 * Saves the "Site Title" option.
	 *
	 * @param string $settings_page 'settings' or 'feeds'; refers to corresponding saved page.
	 * @param array $params Settings that were saved in key => value structure
	 */
	function ai1ec_save_settings( $settings_page, $params ) {
		if( $settings_page == 'settings' ) {
			// Do essentially the same thing WP does when it saves the blog name.
			$value = trim($params['blogname']);
			$value = stripslashes_deep($value);
			update_option( 'blogname', $value );
		}
	}
}
