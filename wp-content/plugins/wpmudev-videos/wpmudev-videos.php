<?php
/*
Plugin Name: WPMU DEV Videos
Plugin URI: http://premium.wpmudev.org/project/unbranded-video-tutorials
Description: A simple way to integrate WPMU DEV's over 40 unbranded support videos into your websites. Simply activate this plugin, then configure where and how you want to display the video tutorials.
Author: Aaron Edwards (Incsub)
Version: 1.0.6
Author URI: http://premium.wpmudev.org/
Network: true
WDP ID: 248
*/

/*
Copyright 2007-2011 Incsub (http://incsub.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License (Version 2 - GPLv2) as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

class WPMUDEV_Videos {

	//------------------------------------------------------------------------//
	//---Config---------------------------------------------------------------//
	//------------------------------------------------------------------------//

	var $version = '1.0.6';
	var $api_url = 'http://premium.wpmudev.org/video-api-register.php';
	var $video_list;
	var $video_cats;
	var $page_url;

	function WPMUDEV_Videos() {
		
		add_action( 'admin_menu', array( &$this, 'plug_pages' ) );
		add_action( 'network_admin_menu', array( &$this, 'plug_pages' ) ); //for 3.1

		//localize the plugin
		load_plugin_textdomain( 'wpmudev_vids', false, dirname( plugin_basename( __FILE__ ) ) . '/includes/languages/' );
		
		//attach videos to contextual help dropdowns if set
		if (!$this->is_mapped() && $this->get_setting('contextual_help', 1))
			include_once( dirname( __FILE__ ) . '/includes/contextual-help.php' );
			
		add_shortcode( 'wpmudev-video', array( &$this, 'handle_shortcode' ) );
		
		//default settings
		$this->video_list = array(
			'add-heading' => __('Add Heading', 'wpmudev_vids'),
			'add-image-from-media-library' => __('Adding an Image From Media Library', 'wpmudev_vids'),
			'add-image-from-pc' => __('Adding an Image From Your Computer', 'wpmudev_vids'),
			'add-image-from-url' => __('Adding an Image From a URL', 'wpmudev_vids'),
			'add-links' => __('Adding Links', 'wpmudev_vids'),
			'add-media' => __('Adding Media', 'wpmudev_vids'),
			'add-new-page' => __('Adding New Pages', 'wpmudev_vids'),
			'add-new-post' => __('Adding New Posts', 'wpmudev_vids'),
			'add-paragraph' => __('Adding Paragraphs', 'wpmudev_vids'),
			'admin-bar' => __('Admin Bar', 'wpmudev_vids'),
			'categories' => __('Categories', 'wpmudev_vids'),
			'change-password' => __('Changing Your Password', 'wpmudev_vids'),
			'comments' => __('Comments', 'wpmudev_vids'),
			'dashboard' => __('Dashboard', 'wpmudev_vids'),
			'delete-image' => __('Deleting Images', 'wpmudev_vids'),
			'edit-image' => __('Editing Images', 'wpmudev_vids'),
			'edit-text' => __('Editing Text', 'wpmudev_vids'),
			'excerpt' => __('Excerpts', 'wpmudev_vids'),
			'featured-image' => __('Featured Images', 'wpmudev_vids'),
			'hyperlinks' => __('Hyperlinks', 'wpmudev_vids'),
			'image-editor-crop-and-scale' => __('Image Editor - Cropping and Scaling', 'wpmudev_vids'),
			'image-editor-rotate-flip-undo-redo' => __('Image Editor - Flip, Undo, Redo', 'wpmudev_vids'),
			'image-editor' => __('Image Editor', 'wpmudev_vids'),
			'link-categories' => __('Link Categories', 'wpmudev_vids'),
			'lists' => __('Lists', 'wpmudev_vids'),
			'media-library' => __('Media Library', 'wpmudev_vids'),
			'oEmbed' => __('oEmbed', 'wpmudev_vids'),
			'paste-from-word' => __('Pasting From Word', 'wpmudev_vids'),
			'quickpress' => __('QuickPress', 'wpmudev_vids'),
			'replace-image' => __('Replacing an Image', 'wpmudev_vids'),
			'restore-page' => __('Restoring a Page', 'wpmudev_vids'),
			'restore-post' => __('Restoring a Post', 'wpmudev_vids'),
			'tags' => __('Tags', 'wpmudev_vids'),
			'the-toolbar' => __('The Toolbar', 'wpmudev_vids'),
			'trash-page' => __('Trashing a Page', 'wpmudev_vids'),
			'trash-post' => __('Trashing a Post', 'wpmudev_vids'),
			'widgets' => __('Managing Widgets', 'wpmudev_vids'),
			'menus' => __('Creating and Managing Custom Navigation Menus', 'wpmudev_vids'),
			'change-theme' => __('Switching Themes', 'wpmudev_vids')
		);
		
		//videos by category
		$this->video_cats = array(
			'dashboard' => array('name' => __('The Dashboard', 'wpmudev_vids'),
													 'list' => array('dashboard', 'admin-bar', 'quickpress', 'change-password')),
			'posts' => array('name' => __('Posts', 'wpmudev_vids'),
											 'list' => array('add-new-post', 'trash-post', 'restore-post')),
			'pages' => array('name' => __('Pages', 'wpmudev_vids'),
											 'list' => array('add-new-page', 'trash-page', 'restore-page')),
			'editor' => array('name' => __('The Visual Editor', 'wpmudev_vids'),
												'list' => array('the-toolbar', 'edit-text', 'add-paragraph', 'add-heading', 'hyperlinks', 'lists', 'paste-from-word', 'oEmbed', 'excerpt')),
			'images' => array('name' => __('Working With Images', 'wpmudev_vids'),
												'list' => array('add-image-from-pc', 'add-image-from-media-library', 'add-image-from-url', 'edit-image', 'replace-image', 'delete-image', 'featured-image')),
			'media' => array('name' => __('Media Library', 'wpmudev_vids'),
												'list' => array('media-library', 'add-media', 'image-editor', 'image-editor-crop-and-scale', 'image-editor-rotate-flip-undo-redo')),
			'appearance' => array('name' => __('Appearance', 'wpmudev_vids'),
														'list' => array('change-theme', 'widgets', 'menus')),
			'organizing' => array('name' => __('Organizing Content', 'wpmudev_vids'),
														'list' => array('categories', 'tags')),
			'links' => array('name' => __('Managing Links', 'wpmudev_vids'),
														'list' => array('add-links', 'link-categories')),
			'Comments' => array('name' => __('Managing Comments', 'wpmudev_vids'),
														'list' => array('comments'))
		);
		
	}

	//------------------------------------------------------------------------//
	//---Functions------------------------------------------------------------//
	//------------------------------------------------------------------------//
	
	//an easy way to get to our settings array without undefined indexes
	function get_setting($key, $default = null) {
    $settings = get_site_option( 'wpmudev_vids_settings' );
    $setting = isset($settings[$key]) ? $settings[$key] : $default;
		return apply_filters( "wpmudev_vids_setting_$key", $setting, $default );
	}

	function update_setting($key, $value) {
    $settings = get_site_option( 'wpmudev_vids_settings' );
    $settings[$key] = $value;
		return update_site_option('wpmudev_vids_settings', $settings);
	}
	
	function plug_pages() {
		global $wpdb;
		
		//define this in wp-config to hide the setting menu
		if ( !defined('WPMUDEV_VIDS_HIDE_SETTINGS') )
			define('WPMUDEV_VIDS_HIDE_SETTINGS', false);
		
		if ( !WPMUDEV_VIDS_HIDE_SETTINGS ) {	
			if ( is_multisite() && is_network_admin() ) {
				$page = add_submenu_page( 'settings.php', __('WPMU DEV Videos', 'wpmudev_vids'), __('WPMU DEV Videos', 'wpmudev_vids'), 'manage_network_options', 'wpmudev-videos', array( &$this, 'settings_output') );
			} else if (!is_multisite()) {
				$page = add_submenu_page( 'options-general.php', __('WPMU DEV Videos', 'wpmudev_vids'), __('WPMU DEV Videos', 'wpmudev_vids'), 'manage_options', 'wpmudev-videos', array( &$this, 'settings_output') );
			}
		}
		
		if (!is_network_admin() && !$this->is_mapped()) {
			if ($this->get_setting('menu_location') == 'dashboard') {
				add_submenu_page( 'index.php', $this->get_setting('menu_title'), $this->get_setting('menu_title'), 'read', 'videos', array( &$this, 'page_output') );
				$this->page_url = admin_url("index.php?page=videos");
			} else if ($this->get_setting('menu_location') == 'support_system') {
				add_submenu_page( 'incsub_support', $this->get_setting('menu_title'), $this->get_setting('menu_title'), 'read', 'videos', array( &$this, 'page_output') );
				$this->page_url = admin_url("admin.php?page=videos");
			} else if ($this->get_setting('menu_location') == 'top') {
				add_menu_page( $this->get_setting('menu_title'), $this->get_setting('menu_title'), 'read', 'videos', array( &$this, 'page_output'), plugins_url( 'includes/icon.png' , __FILE__ ), 50 );
				$this->page_url = admin_url("admin.php?page=videos");
			}
		}
	}

	function handle_shortcode($atts) {
		extract( shortcode_atts( array( 'video' => false, 'group' => false, 'show_title' => true  ), $atts ) );
		
		if ($group && isset($this->video_cats[$group])) {
			$output = $show_title ? '<h3 class="wpmudev_video_group_title">'.$this->video_cats[$group]['name'].'</h3>' : '';
			foreach ($this->video_cats[$group]['list'] as $video) {
				$output .= '<p class="wpmudev_video">' . (is_ssl() ? '<iframe src="https://premium.wpmudev.org/video/' . urlencode($video) . '" frameborder="0" height="325" width="480"></iframe>' : '<iframe src="http://premium.wpmudev.org/video/' . urlencode($video) . '" frameborder="0" height="325" width="480"></iframe>') . '</p>';
			}
			return '<div class="wpmudev_video_group">'.$output.'</div>';
		}
		
		if ($video && isset($this->video_list[$video]))
			return '<p class="wpmudev_video">' . (is_ssl() ? '<iframe src="https://premium.wpmudev.org/video/' . urlencode($video) . '" frameborder="0" height="325" width="480"></iframe>' : '<iframe src="http://premium.wpmudev.org/video/' . urlencode($video) . '" frameborder="0" height="325" width="480"></iframe>') . '</p>';
		else
			return '';
	}
	
	function is_mapped() {
		if (is_multisite() && class_exists('domain_map')) {
			if (get_site_option('map_admindomain') != 'original')
				return true;
		}
		return false;
	}
	
	//------------------------------------------------------------------------//
	//---Page Output Functions------------------------------------------------//
	//------------------------------------------------------------------------//

	function settings_output() {
		global $wpdb, $current_site;
		
		//save settings
		if (isset($_POST['save_settings'])) {
			//strip slashes
			$_POST['wpmudev_vids']['menu_title'] = stripslashes($_POST['wpmudev_vids']['menu_title']);
			update_site_option('wpmudev_vids_settings', $_POST['wpmudev_vids']);
			
			echo '<div class="updated fade"><p>'.__('Settings saved.', 'wpmudev_vids').'</p></div>';
		}
		?>
		<div class="wrap">
		<?php if ($this->is_mapped()) { ?>
		<div class="error"><p><?php _e('Displaying support videos in the admin area of sites is incompatible with your <a href="settings.php?page=domainmapping_options">domain mapping settings</a>. Administration mapping must be set to "original domain" to display videos in the admin area.', 'wpmudev_vids') ?></p></div>
		<?php } ?>
		<div class="icon32"><img src="<?php echo plugins_url( 'includes/video.png' , __FILE__ ); ?>" /><br /></div>
		<h2><?php _e('WPMU DEV Video Settings', 'wpmudev_vids') ?></h2>
		
		<form action="" method="post">
		<div id="poststuff" class="metabox-holder">
			
		<div class="postbox">
			<h3 class='hndle'><span><?php _e('Display Settings', 'wpmudev_vids') ?></span></h3>
			<div class="inside">
				<table class="form-table">
					<tr>
						<th scope="row"><?php _e('Register This Domain', 'wpmudev_vids') ?></th>
						<td>
							<?php
							$result = wp_remote_get($this->api_url . '?domain=' . network_site_url() );
							if ($result['body'] != 'true') { ?>
							<span class="description"><?php printf(__('Please register this domain "%s" below: ', 'wpmudev_vids'), network_site_url()); ?></span>
							<iframe src="<?php echo $this->api_url . '?new_domain=' . network_site_url(); ?>" width="100%" height="150"></iframe>
							<?php } else { ?>
								<strong><?php _e('Already Registered!', 'wpmudev_vids') ?></strong>
							<?php } ?>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e('Add Videos to Contextual Help', 'wpmudev_vids') ?></th>
						<td>
						<label><input<?php echo $this->is_mapped() ? ' disabled="disabled"' : ''; ?> value="1" name="wpmudev_vids[contextual_help]" type="radio"<?php checked($this->get_setting('contextual_help', 1), 1) ?> /> <?php _e('Yes', 'wpmudev_vids') ?></label>
						<label><input value="0" name="wpmudev_vids[contextual_help]" type="radio"<?php checked($this->get_setting('contextual_help', 1), 0) ?> /> <?php _e('No', 'wpmudev_vids') ?></label>
						<br /><span class="description"><?php _e('This will add the appropriate video tutorials to the help dropdowns on WordPress admin screens.', 'wpmudev_vids') ?></span>	
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e('Menu Location', 'wpmudev_vids') ?></th>
						<td>
						<select name="wpmudev_vids[menu_location]">
							<option<?php echo $this->is_mapped() ? ' disabled="disabled"' : ''; ?> value="dashboard"<?php selected($this->get_setting('menu_location'), 'dashboard') ?>><?php _e('Dashboard', 'wpmudev_vids') ?></option>
							<option<?php echo $this->is_mapped() ? ' disabled="disabled"' : ''; ?> value="top"<?php selected($this->get_setting('menu_location'), 'top') ?>><?php _e('Top Level', 'wpmudev_vids') ?></option>
							<?php if (function_exists('incsub_support')) { ?>
							<option<?php echo $this->is_mapped() ? ' disabled="disabled"' : ''; ?> value="support_system"<?php selected($this->get_setting('menu_location'), 'support_system') ?>><?php _e('Support System Plugin', 'wpmudev_vids') ?></option>
							<?php } ?>
							<option value="none"<?php selected($this->get_setting('menu_location'), 'none') ?>><?php _e('No Menu', 'wpmudev_vids') ?></option>
						</select>
						<br /><span class="description"><?php _e('What part of the admin menu should the video tutorial page be added?', 'wpmudev_vids') ?></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e('Menu Title', 'wpmudev_vids') ?></th>
						<td>
						<label><input size="25" name="wpmudev_vids[menu_title]" value="<?php esc_attr_e($this->get_setting('menu_title', __('Video Tutorials', 'wpmudev_vids'))); ?>" type="text" /></label>
						<br /><span class="description"><?php _e('Sets the menu title for the video tutorial page.', 'wpmudev_vids') ?></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e('Hide Videos', 'wpmudev_vids') ?></th>
						<td>
							<span class="description"><?php _e('Check any videos here that you want to hide from users:', 'wpmudev_vids') ?></span><br />
						<?php
						$hidden = $this->get_setting('hide');
						foreach ($this->video_list as $key => $label) {
							$checked = isset($hidden[$key]) ? ' checked="checked"' : '';
							?>
						<label><input name="wpmudev_vids[hide][<?php echo $key; ?>]" value="1" type="checkbox"<?php echo $checked; ?> /> <?php esc_attr_e($label); ?></label> 
						<?php } ?>
						</td>
					</tr>
				</table>
				<p class="submit">
				<input type="submit" name="save_settings" value="<?php _e('Save Changes', 'wpmudev_vids') ?>" />
				</p>
			</div>
		</div>
		
		<div class="postbox">
			<h3 class='hndle'><span><?php _e('Shortcodes', 'wpmudev_vids') ?></span></h3>
			<div class="inside">
				<p><?php _e('These shortcodes allow you to easiliy embed video tutorials in posts and pages on your sites. Simply type or paste them into your post or page content where you would like them to appear. Also properly handles SSL protected pages.', 'wpmudev_vids') ?></p>
				<table class="form-table">
					<?php foreach ($this->video_list as $url => $label) { ?>
					<tr>
					<th scope="row"><?php esc_attr_e($label); ?></th>
					<td>
						<strong>[wpmudev-video video="<?php echo $url; ?>"]</strong>
					</td>
					</tr>
					<?php } ?>
				</table>
				<h2><?php _e('Group Shortcodes', 'wpmudev_vids') ?> - <span class="description"><?php _e('These shortcodes allow you to embed a whole group of videos at a time.', 'wpmudev_vids') ?></span></h2>
				<table class="form-table">
					<?php foreach ($this->video_cats as $url => $label) { ?>
					<tr>
					<th scope="row"><?php echo esc_attr($label['name']); ?></th>
					<td>
						<strong>[wpmudev-video group="<?php echo $url; ?>" show_title="1"]</strong> or <strong>[wpmudev-video group="<?php echo $url; ?>" show_title="0"]</strong>
					</td>
					</tr>
					<?php } ?>
				</table>
			</div>
		</div>
		
		</div>
		</form>
		</div>
		<?php
	}
	
	function page_output() {
		global $wpdb, $current_site;
		
		//remove any hidden videos from the list
		$hidden = $this->get_setting('hide');
		if (is_array($hidden) && count($hidden)) {
			foreach ($this->video_cats as $cat_key => $cat) {
				foreach ($cat['list'] as $key => $video) {
					if (isset($hidden[$video])) {
						unset($this->video_cats[$cat_key]['list'][$key]);
					}
				}
			}
		}
		?>
		<div class="wrap">
		<div class="icon32"><img src="<?php echo plugins_url( 'includes/video.png' , __FILE__ ); ?>" /><br /></div>
		<h2><?php echo $this->get_setting('menu_title'); ?></h2>
		
		<div id="poststuff" class="metabox-holder">
			
			<?php if (isset($_GET['vid']) && isset($this->video_list[$_GET['vid']])) { ?>			
			<div class="postbox" style="width: 500px;">
				<h3 class='hndle'><span><?php esc_attr_e($this->video_list[$_GET['vid']]); ?></span></h3>
				<div class="inside">
					<iframe src="<?php echo is_ssl() ? 'https' : 'http'; ?>://premium.wpmudev.org/video/<?php echo urlencode($_GET['vid']); ?>?autoplay=1" frameborder="0" height="325" width="480"></iframe>
				</div>
			</div>
			<?php } ?>
			
			<div class="postbox">
				<h3 class='hndle'><span><?php _e('Select a Video Tutorial', 'wpmudev_vids') ?></span></h3>
				<div class="inside" style="padding-left:1%;padding-right:0;">
				<?php foreach ($this->video_cats as $cat) {
					//skip if no vids in category
					if (count($cat['list']) == 0)
						continue;
					?>
					<table class='widefat' style="width: 19%; float: left; margin-right: 1%;margin-bottom: 10px;clear: none;">
					<thead><tr>
					<th scope='col'><?php echo $cat['name']; ?></th>
					</tr></thead>
					<tbody id='the-list'>
					<?php
						$class = '';
						foreach ($cat['list'] as $video) {
							if (!isset($this->video_list[$video]))
								continue;
							//=========================================================//
							$highlight = (isset($_GET['vid']) && $_GET['vid'] == $video) ? ' style="color:#D54E21;font-weight:bold;"' : '';
							echo "<tr class='$class'>";
							echo "<td valign='top'><a href='" . $this->page_url . "&vid=$video" . "'$highlight>" . esc_attr($this->video_list[$video]) . "</a></td>";
							echo "</tr>";
							$class = ('alternate' == $class) ? '' : 'alternate';
							//=========================================================//
						}
					?>
					</tbody>
					</table>
				<?php } ?>
					<div class="clear"></div>
				</div>
			</div>
			
		</div>
				
	</div>
	<?php
	}

}

global $wpmudev_vids;
$wpmudev_vids = new WPMUDEV_Videos();

///////////////////////////////////////////////////////////////////////////
/* -------------------- Update Notifications Notice -------------------- */
if ( !function_exists( 'wdp_un_check' ) ) {
  add_action( 'admin_notices', 'wdp_un_check', 5 );
  add_action( 'network_admin_notices', 'wdp_un_check', 5 );
  function wdp_un_check() {
    if ( !class_exists( 'WPMUDEV_Update_Notifications' ) && current_user_can( 'update_plugins' ) )
      echo '<div class="error fade"><p>' . __('Please install the latest version of <a href="http://premium.wpmudev.org/project/update-notifications/" title="Download Now &raquo;">our free Update Notifications plugin</a> which helps you stay up-to-date with the most stable, secure versions of WPMU DEV themes and plugins. <a href="http://premium.wpmudev.org/wpmu-dev/update-notifications-plugin-information/">More information &raquo;</a>', 'wpmudev') . '</a></p></div>';
  }
}
/* --------------------------------------------------------------------- */
?>