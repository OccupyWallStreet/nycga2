<?php
/*
Plugin Name: Front-page editing
Description: Allows you to embed front-page editing for events into your site public pages, using a shortcode.
Plugin URI: http://premium.wpmudev.org/project/events-and-booking
Version: 1.0
Author: Ve Bailovity (Incsub)
*/

/*
Detail: By default, Front-page editor will work with preconfigured stub URL. However, you can create your own page, add the Front-page editing shortcode (<code>[eab_event_editor]</code>) to the content and configure your Add/Edit links in plugin settings to use this page instead.
*/

class Eab_Events_FrontPageEditing {
	
	const SLUG = 'edit-event';
	private $_data;
	private $_options = array();
	
	private function __construct () {
		$this->_data = Eab_Options::get_instance();
		$this->_options = wp_parse_args($this->_data->get_option('eab-events-fpe'), array(
			'id' => false,
			'integrate_with_my_events' => false,
		));
	}
	
	public static function serve () {
		$me = new Eab_Events_FrontPageEditing;
		$me->_add_hooks();
	}
	
	private function _add_hooks () {
		/*
		if (!$this->_options['id']) {
			add_action('wp', array($this, 'check_page_location'));
		}
		*/
		add_action('wp', array($this, 'check_page_location'));
		
		add_action('eab-settings-after_plugin_settings', array($this, 'show_settings'));
		add_filter('eab-settings-before_save', array($this, 'save_settings'));
		
		// Add/Edit links
		add_filter('eab-events-after_event_details', array($this, 'add_edit_link'), 10, 2);
		add_filter('eab-buddypress-group_events-after_head', array($this, 'add_new_link'));
		if (!is_admin()) add_action('admin_bar_menu', array($this, 'admin_bar_add_menu_links'), 99);
		if ($this->_options['integrate_with_my_events']) {
			add_action('eab-events-my_events-set_up_navigation', array($this, 'my_events_add_event'));
		}
		
		add_shortcode('eab_event_editor', array($this, 'handle_editor_shortcode'));
		
		add_action('wp_ajax_eab_events_fpe-save_event', array($this, 'json_save_event'));
	}
	
/* ----- Settings ----- */

	function show_settings () {
		$pages = get_pages();
		$integrate_with_my_events = $this->_options['integrate_with_my_events'] ? 'checked="checked"' : '';
		$tips = new WpmuDev_HelpTooltips();
		$tips->set_icon_url(plugins_url('events-and-bookings/img/information.png'));
?>
<div id="eab-settings-fpe" class="eab-metabox postbox">
	<h3 class="eab-hndle"><?php _e('Front-page editing :', Eab_EventsHub::TEXT_DOMAIN); ?></h3>
	<div class="eab-inside">
		<div class="eab-settings-settings_item">
			<label for="eab-events-fpe-use_slug">
				<?php _e('I want to use this page as my Front Editor page', Eab_EventsHub::TEXT_DOMAIN); ?>:
			</label>
			<select id="eab-events-fpe-use_slug" name="eab-events-fpe[id]">
				<option value=""><?php _e('Use default value', Eab_EventsHub::TEXT_DOMAIN);?>&nbsp;</option>
			<?php 
			foreach ($pages as $page) { 
				$selected = ($this->_options['id'] == $page->ID) ? 'selected="selected"' : '';
				echo "<option value='{$page->ID}' {$selected}>{$page->post_title}</option>";
			} 
			?>
			</select>
			<?php echo $tips->add_tip(__("Don't forget to add this shortcode to your selected page: <code>[eab_event_editor]</code>")); ?>
			<div><?php _e('By default, Front-page editor will work with preconfigured stub URL. However, you can create your own page, add the Front-page editing shortcode (<code>[eab_event_editor]</code>) to the content and configure your Add/Edit links here to use this page instead.', Eab_EventsHub::TEXT_DOMAIN);?></div>
		</div>
<?php if (Eab_AddonHandler::is_plugin_active('eab-buddypres-my_events')) { ?>
		<div class="eab-settings-settings_item">
			<label for="eab-events-fpe-integrate_with_my_events">
				<input type="hidden" name="eab-events-fpe[integrate_with_my_events]" value="" />
				<input type="checkbox" id="eab-events-fpe-integrate_with_my_events" name="eab-events-fpe[integrate_with_my_events]" value="1" <?php echo $integrate_with_my_events; ?> />
				<?php _e('Integrate with <em>My Events</em> add-on', Eab_EventsHub::TEXT_DOMAIN); ?>
			</label>
			<?php echo $tips->add_tip(__("Enabling this option will add a new &quot;Add Event&quot; tab to &quot;My Events&quot;")); ?>
		</div>
<?php } ?>
	</div>
</div>
<?php		
	}
	
	function save_settings ($options) {
		$options['eab-events-fpe'] = @$_POST['eab-events-fpe'];
		return $options;
	}
	
/* ----- Add/Edit Links ----- */

	/**
	 * BuddyPress:My Events integration
	 */
	function my_events_add_event () {
		global $bp;
		bp_core_new_subnav_item(array(
			'name' => __('Add Event', Eab_EventsHub::TEXT_DOMAIN),
			'slug' => 'edit-event',
			'parent_url' => trailingslashit(trailingslashit($bp->displayed_user->domain) . 'my-events'),
			'parent_slug' => 'my-events',
			'screen_function' => array($this, 'bind_bp_add_event_page'),
		));
	}
	
	/**
	 * Edit link for singular events.
	 */
	function add_edit_link ($content, $event) {
		if (!$this->_check_perms($event->get_id())) return false;
		
		// Do not edit recurring events
		if ($event->is_recurring()) return $content;
		
		// Do not edit multiple dates events
		$start_dates = $event->get_start_dates();
		if (count($start_dates) > 1) return $content;
		
		return 
			$content .
			'<p>' .
				'<a href="' . $this->_get_front_editor_link($event->get_id()) . '" class="button">' . 
					__('Edit event', Eab_EventsHub::TEXT_DOMAIN) . 
				'</a>' .
			'</p>' .
		'';
	}

	/**
	 * Add new link on top of group events.
	 */
	function add_new_link () {
		if (!$this->_check_perms(false)) return false;
		
		echo '' . 
			'<p>' .
				'<a href="' . $this->_get_front_editor_link() . '">' . 
					__('Add event', Eab_EventsHub::TEXT_DOMAIN) . 
				'</a>' .
			'</p>' .
		'';
	}
	
	/**
	 * Admin toolbar integration.
	 */
	function admin_bar_add_menu_links () {
		global $wp_admin_bar, $post;

		$wp_admin_bar->add_menu(array(
			'id' => 'eab-events-fpe-admin_bar',
			'title' => __('Events', Eab_EventsHub::TEXT_DOMAIN),
			'href' => $this->_get_front_editor_link(),
		));
		$wp_admin_bar->add_menu(array(
			'parent' => 'eab-events-fpe-admin_bar',
			'id' => 'eab-events-fpe-admin_bar-add_event',
			'title' => __('Add Event', Eab_EventsHub::TEXT_DOMAIN),
			'href' => $this->_get_front_editor_link(),
		));
		if (is_singular() && $post && isset($post->post_type) && $post->post_type == Eab_EventModel::POST_TYPE) {
			$wp_admin_bar->remove_node('edit');
			$wp_admin_bar->add_menu(array(
				'parent' => 'eab-events-fpe-admin_bar',
				'id' => 'eab-events-fpe-admin_bar-edit_event',
				'title' => __('Edit this Event', Eab_EventsHub::TEXT_DOMAIN),
				'href' => $this->_get_front_editor_link($post->ID),
			));
		}
	}
	
/* ----- Internals ----- */

	function _get_front_editor_link ($event_id=false) {
		$url = $this->_options['id']
			? get_permalink($this->_options['id'])
			: site_url(self::SLUG)
		;
		$event_id = (int)$event_id ? "?event_id={$event_id}" : '';
		return "{$url}{$event_id}"; 
	}
	
	function check_page_location () {
		global $wp_query;
		$qobj = get_queried_object();
		//if (self::SLUG != $wp_query->query_vars['pagename']) return false;
		if (
			($this->_options['id'] && is_object($qobj) && isset($qobj->ID) && $this->_options['id'] != $qobj->ID)
			||
			(!$this->_options['id'] && self::SLUG != $wp_query->query_vars['name'])
		) return false;
		
		add_filter('the_content', array($this, 'the_editor_content'), 99);
		status_header( 200 );
		$wp_query->is_page = 1;
		$wp_query->is_singular = 1;
		$wp_query->post_count = 1;
		$wp_query->is_404 = null;
	}
	
	function json_save_event () {
		global $current_user;
		header('Content-type: application/json');
		if (!isset($_POST['data'])) die(json_encode(array(
			'status' => 0,
			'message' => __('No data received', Eab_EventsHub::TEXT_DOMAIN),
		)));
		
		$data = $_POST['data'];
		if (!$this->_check_perms((int)$data['id'])) die(json_encode(array(
			'status' => 0,
			'message' => __('Insufficient privileges', Eab_EventsHub::TEXT_DOMAIN),
		)));
		$post = array();
		
		$start = date('Y-m-d H:i', strtotime($data['start']));
		$end = date('Y-m-d H:i', strtotime($data['end']));
		
		$post_type = get_post_type_object(Eab_EventModel::POST_TYPE);
		$post['post_title'] = strip_tags($data['title']);
		$post['post_content'] = current_user_can('unfiltered_html') ? $data['content'] : wp_filter_post_kses($data['content']);
		$post['post_status'] = current_user_can($post_type->cap->publish_posts) ? 'publish' : 'pending';
		$post['post_type'] = Eab_EventModel::POST_TYPE;
		$post['post_author'] = $current_user->id;
		
		if ((int)$data['id']) {
			$post['ID'] = $post_id = $data['id'];
			wp_update_post($post);
		} else {
			$post_id = wp_insert_post($post);
		}
		if (!$post_id) die(json_encode(array(
			'status' => 0,
			'message' => __('There has been an error saving this Event', Eab_EventsHub::TEXT_DOMAIN),
		)));
		
		update_post_meta($post_id, 'incsub_event_start', $start);
		update_post_meta($post_id, 'incsub_event_end', $end);
		update_post_meta($post_id, 'incsub_event_status', strip_tags($data['status']));
		
		$venue_map = get_post_meta($post_id, 'agm_map_created', true);
		if (!$venue_map && $data['venue'] && class_exists('AgmMapModel')) {
			$model = new AgmMapModel;
			$model->autocreate_map($post_id, false, false, $data['venue']);
		} 
		update_post_meta($post_id, 'incsub_event_venue', strip_tags($data['venue']));
		
		
		$is_paid = (int)$data['is_premium'];
		$fee = $is_paid ? strip_tags($data['fee']) : '';
		update_post_meta($post_id, 'incsub_event_paid', ($is_paid ? '1' : ''));
		update_post_meta($post_id, 'incsub_event_fee', $fee);
		do_action('eab-events-fpe-save_meta', $post_id, $data);

		wp_set_post_terms($post_id, array((int)$data['category']), 'eab_events_category', false);
		
		$message = current_user_can($post_type->cap->publish_posts)
			? __('Event saved and published', Eab_EventsHub::TEXT_DOMAIN)
			: __('Event saved and waiting for approval', Eab_EventsHub::TEXT_DOMAIN)
		;
		die(json_encode(array(
			'status' => 1,
			'post_id' => $post_id,
			'permalink' => get_permalink($post_id),
			'message' => $message,
		)));
	}

	private function _check_perms ($event_id) {
		$post_type = get_post_type_object(Eab_EventModel::POST_TYPE);
		if ($event_id) {
			return current_user_can($post_type->cap->edit_post, $event_id);
		} else {
			return current_user_can($post_type->cap->edit_posts);
		}
		return false;
	}
	
/* ----- Output ----- */

	function bind_bp_add_event_page () {
		add_action('bp_template_content', array($this, 'output_bp_event_editor'));
		bp_core_load_template(apply_filters('bp_core_template_plugin', 'members/single/plugins'));
	}

	function output_bp_event_editor () {
		echo do_shortcode('[eab_event_editor]');
	}

	function handle_editor_shortcode ($args=array(), $content='') {
		global $post, $wp_current_filter;
		
		$event_id = (int)@$_GET['event_id'];
		if (!$this->_check_perms($event_id)) return false;
		if (defined('EAB_EVENTS_FPE_ALREADY_HERE')) return $content;
		
		define('EAB_EVENTS_FPE_ALREADY_HERE', true, true);
		return $this->_edit_event_form($event_id); // ... and YAY! for not being able to return wp_editor >.<
	}
	
	function the_editor_content ($content) {
		global $post, $wp_current_filter;
		if ($post) return $content; // If not fictional, we're not interested
		
		$event_id = (int)@$_GET['event_id'];
		if (!$this->_check_perms($event_id)) return false;
		if (defined('EAB_EVENTS_FPE_ALREADY_HERE')) return $content;
		
		$is_excerpt = array_reduce($wp_current_filter, create_function('$ret,$val', 'return $ret ? true : preg_match("/excerpt/", $val);'), false);
		$is_head = array_reduce($wp_current_filter, create_function('$ret,$val', 'return $ret ? true : preg_match("/head/", $val);'), false);
		$is_title = array_reduce($wp_current_filter, create_function('$ret,$val', 'return $ret ? true : preg_match("/title/", $val);'), false);
		if ($is_excerpt || $is_head || $is_title) return $content;
		
		define('EAB_EVENTS_FPE_ALREADY_HERE', true, true);
		return $this->_edit_event_form($event_id); // ... and YAY! for not being able to return wp_editor >.<
	}
	
	private function _edit_event_form ($event_id) {
		add_action('get_footer', array($this, 'enqueue_dependency_data'));
		$post = $event_id ? get_post($event_id) : false;
		$event = new Eab_EventModel($post);

		$this->_enqueue_dependencies();
		
		$style = $event->get_id() ? '' : 'style="display:none"';
		$ret .= '<div id="eab-events-fpe">';
		$ret .= '<a id="eab-events-fpe-back_to_event" href="' . get_permalink($event->get_id()) . '" ' . $style . '>' . __('Back to Event', Eab_EventsHub::TEXT_DOMAIN) . '</a>';
		$ret .= '<input type="hidden" id="eab-events-fpe-event_id" value="' . (int)$event->get_id() . '" />';
		$ret .= '<div>';
		$ret .= '<label>' . __('Title', Eab_EventsHub::TEXT_DOMAIN) . '</label>';
		$ret .= '<br /><input type="text" name="" id="eab-events-fpe-event_title" value="' . esc_attr($event->get_title()) . '" />';
		$ret .= '</div>';
		
		$ret .= '<div id="fpe-editor"></div>';
		
		$ret .= $this->_get_event_meta_boxes($event);
		$ret .= '</div>';
		
		return $ret;
	}
	
	private function _get_event_meta_boxes ($event) {
		$ret = '<div id="eab-events-fpe-meta_info">';
		$ret .= '<div class="eab-events-fpe-col_wrapper">';
		
		// Date, time
		$ret .= '<div class="eab-events-fpe-meta_box" id="eab-events-fpe-date_time">';
		// Start date/time
		$start = $event->get_start_timestamp();
		$start = $start ? $start : time();
		$ret .= '<div>';
		$ret .= '<label>' . __('Starts on', Eab_EventsHub::TEXT_DOMAIN) . '</label>';
		$ret .= ' <input type="text" name="" id="eab-events-fpe-start_date" value="' . date('Y-m-d', $start) . '" size="10" />';
		$ret .= ' <input type="text" name="" id="eab-events-fpe-start_time" value="' . date('H:i', $start) . '" size="3" />';
		$ret .= '</div>';
		
		// End date/time
		$end = $event->get_end_timestamp();
		$end = $end ? $end : time() + 3600;
		$ret .= '<div>';
		$ret .= '<label>' . __('Ends on', Eab_EventsHub::TEXT_DOMAIN) . '</label>';
		$ret .= ' <input type="text" name="" id="eab-events-fpe-end_date" value="' . date('Y-m-d', $end) . '" size="10" />';
		$ret .= ' <input type="text" name="" id="eab-events-fpe-end_time" value="' . date('H:i', $end) . '" size="3" />';
		$ret .= '</div>';
		
		// End date, time, venue
		$ret .= '</div>';
		
		// Status, type, misc
		$ret .= '<div class="eab-events-fpe-meta_box" id="eab-events-fpe-status_type">';
		
		// Status
		$ret .= '<div>';
		$ret .= '<label>' . __('Event status', Eab_EventsHub::TEXT_DOMAIN) . '</label>';
		$ret .= '<select name="" id="eab-events-fpe-status">';
		$ret .= '	<option value="' . Eab_EventModel::STATUS_OPEN . '" '.(($event->is_open())?'selected="selected"':'').' >'.__('Open', Eab_EventsHub::TEXT_DOMAIN).'</option>';
		$ret .= '	<option value="' . Eab_EventModel::STATUS_CLOSED . '" '.(($event->is_closed())?'selected="selected"':'').' >'.__('Closed', Eab_EventsHub::TEXT_DOMAIN).'</option>';
		$ret .= '	<option value="' . Eab_EventModel::STATUS_EXPIRED . '" '.(($event->is_expired())?'selected="selected"':'').' >'.__('Expired', Eab_EventsHub::TEXT_DOMAIN).'</option>';
		$ret .= '	<option value="' . Eab_EventModel::STATUS_ARCHIVED . '" '.(($event->is_archived())?'selected="selected"':'').' >'.__('Archived', Eab_EventsHub::TEXT_DOMAIN).'</option>';
		$ret .= '</select>';
		$ret .= '</div>';
		
		// Type
		$ret .= '<div>';
		$ret .= '<label>' . __('Is this a paid event?', Eab_EventsHub::TEXT_DOMAIN) . '</label>';
		$ret .= '<select name="" id="eab-events-fpe-is_premium">';
		$ret .= '	<option value="1" ' . ($event->is_premium() ? 'selected="selected"' : '') . '>'.__('Yes', Eab_EventsHub::TEXT_DOMAIN).'</option>';
		$ret .= '	<option value="0" ' . ($event->is_premium() ? '' : 'selected="selected"') . '>'.__('No', Eab_EventsHub::TEXT_DOMAIN).'</option>';
		$ret .= '</select>';
		$ret .= '<div id="eab-events-fpe-event_fee-wrapper">';
		$ret .= '<label for="eab-events-fpe-event_fee">' . __('Fee', Eab_EventsHub::TEXT_DOMAIN) . '</label>';
		$ret .= ' <input type="text" name="" id="eab-events-fpe-event_fee" size="6" value="' . esc_attr($event->get_price()) . '" />';
		$ret .= '</div>'; // eab-events-fpe-event_fee-wrapper
		$ret .= '</div>';

		// End status, type, misc
		$ret .= '</div>';
		
		$ret .= '</div>'; // eab-events-fpe-col_wrapper
		$ret .= '<div class="eab-events-fpe-col_wrapper">';
		
		// Start Venue
		$ret .= '<div class="eab-events-fpe-meta_box" id="eab-events-fpe-meta_box-venue">';
		// Venue
		$ret .= '<div>';
		$ret .= '<label>' . __('Venue', Eab_EventsHub::TEXT_DOMAIN) . '</label>';
		$ret .= '<br /><input type="text" name="" id="eab-events-fpe-venue" value="' . esc_attr($event->get_venue_location()) . '" />';
		$ret .= '</div>';
		// End venue
		$ret .= '</div>';
		
		$ret .= '</div>'; // eab-events-fpe-col_wrapper
		$ret .= '<div class="eab-events-fpe-col_wrapper">';

		// Start Categories
		$event_cat_ids = $event->get_category_ids();
		$event_cat_ids = $event_cat_ids ? $event_cat_ids : array();
		$all_cats = get_terms('eab_events_category');
		$all_cats = $all_cats ? $all_cats : array();
		$ret .= '<div class="eab-events-fpe-meta_box" id="eab-events-fpe-meta_box-categories">';
		// Categories
		$ret .= '<div>';
		$ret .= '<label>' . __('Category', Eab_EventsHub::TEXT_DOMAIN) . '</label>';
		$ret .= '<br /><select id="eab-events-fpe-categories"><option value=""></option>';
		foreach ($all_cats as $cat) {
			$selected = in_array($cat->term_id, $event_cat_ids) ? "selected='selected'" : '';
			$ret .= "<option value='{$cat->term_id}' {$selected}>{$cat->name}</option>";
		}
		$ret .= "</select>";
		$ret .= '</div>';
		// End Categories
		$ret .= '</div>';
		
		$ret .= '</div>'; // eab-events-fpe-col_wrapper
		$ret .= '<div class="eab-events-fpe-col_wrapper">';

		$addons = apply_filters('eab-events-fpe-add_meta', '', $event);
		if ($addons) {		
			$ret .= '<div class="eab-events-fpe-col_wrapper">';
			$ret .= $addons;
			$ret .= '</div>'; // eab-events-fpe-col_wrapper
		}
		
		// OK/Cancel
		$ok_label = $event->get_id() ?  __('Update', Eab_EventsHub::TEXT_DOMAIN) : __('Publish', Eab_EventsHub::TEXT_DOMAIN);
		$ret .= '<div id="eab-events-fpe-ok_cancel">';
		$ret .= '<input type="button" class="button button-primary" id="eab-events-fpe-ok" value="' . esc_attr($ok_label) . '" />';
		$ret .= '<input type="button" class="button" id="eab-events-fpe-cancel" value="' . esc_attr(__('Cancel', Eab_EventsHub::TEXT_DOMAIN)) . '" />';
		$ret .= '</div>';

		$ret .= '</div>'; // eab-events-fpe-col_wrapper
		$ret .= '<div class="eab-events-fpe-col_wrapper">';
		
		// RSVPs
		$ret .= '<div class="eab-events-fpe-meta_box" id="eab-events-fpe-rsvps">';
		
		if ($event->has_bookings()) {
			$ret .= '<a href="#toggle_rsvps" id="eab-events-fpe-toggle_rsvps">' . __('Toggle RSVPs', Eab_EventsHub::TEXT_DOMAIN) . '</a>';
			$ret .= '<div id="eab-events-fpe-rsvps-wrapper" style="display:none">';
			$ret .= '<div>';
			$ret .= Eab_Template::get_admin_bookings(Eab_EventModel::BOOKING_YES, $event);
			$ret .= '</div>';
			
			$ret .= '<div>';
			$ret .= Eab_Template::get_admin_bookings(Eab_EventModel::BOOKING_MAYBE, $event);
			$ret .= '</div>';
			
			$ret .= '<div>';
			$ret .= Eab_Template::get_admin_bookings(Eab_EventModel::BOOKING_NO, $event);
			$ret .= '</div>';
			$ret .= '</div>'; //eab-events-fpe-rsvps-wrapper
		}
		
		// End RSVPs
		$ret .= '</div>';
		
		$ret .= '</div>'; // eab-events-fpe-col_wrapper
		
		$ret .= '</div>';
		return $ret;
	}

	public function enqueue_dependency_data () {
		printf(
			'<script type="text/javascript">var _eab_events_fpe_data={"ajax_url": "%s", "root_url": "%s"};</script>',
			admin_url('admin-ajax.php'), plugins_url('events-and-bookings/img/')
		);
		
		$event_id = (int)@$_GET['event_id'];
		$post = $event_id ? get_post($event_id) : false;
		$event = new Eab_EventModel($post);
		echo '<div id="fpe-editor-root" style="display:none">';
		wp_editor(
			$post->post_content, 
			'eab-events-fpe-content', array(
				'textarea_rows' => 5,
				'media_buttons' => true,
			)
		);
		echo '</div>';
	}
	
	private function _enqueue_dependencies () {
		wp_enqueue_style('eab-events-fpe', plugins_url(basename(EAB_PLUGIN_DIR) . "/css/eab-events-fpe.css"));
		wp_enqueue_style('eab_jquery_ui');
		
		wp_enqueue_script('jquery');
		wp_enqueue_script('eab-events-fpe', plugins_url(basename(EAB_PLUGIN_DIR) . "/js/eab-events-fpe.js"), array('jquery'));
		wp_localize_script('eab-events-fpe', 'l10nFpe', array(
			'mising_time_date' => __('Please set both start and end dates and times', Eab_EventsHub::TEXT_DOMAIN),
			'check_time_date' => __('Please check your time and date settings', Eab_EventsHub::TEXT_DOMAIN),
			'general_error' => __('Error', Eab_EventsHub::TEXT_DOMAIN),
			'missing_id' => __('Save failed', Eab_EventsHub::TEXT_DOMAIN),
			'all_good' => __('All good!', Eab_EventsHub::TEXT_DOMAIN),
		));
		wp_enqueue_script('eab_jquery_ui');
		
		do_action('eab-events-fpe-enqueue_dependencies');
	}
	
}

Eab_Events_FrontPageEditing::serve();
