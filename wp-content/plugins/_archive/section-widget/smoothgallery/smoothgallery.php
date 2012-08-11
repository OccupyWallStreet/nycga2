<?php
/*
Plugin Name: SmoothGallery
Plugin URI: http://www.christianschenk.org/projects/wordpress-smoothgallery-plugin/
Description: Embeds JonDesign's SmoothGallery.
Version: 1.15.1
Author: Christian Schenk
Author URI: http://www.christianschenk.org/
*/

#
# WordPress SmoothGallery plugin
# Copyright (C) 2008-2009 Christian Schenk
# 
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
# 
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA
#

# Identifier for various actions of this script (e.g. CSS)
define('SMOOTHGALLERY_ACTION', 'smoothgallery_action');
define('SMOOTHGALLERY_ACTION_CSS', 'css');
define('SMOOTHGALLERY_ACTION_CSS_TYPE', 'ct');
define('SMOOTHGALLERY_ACTION_IFRAME', 'iframe');
# Path to this plugin
define('SMOOTHGALLERY_URL', '/wp-content/plugins/smoothgallery');
# Include the custom configuration
require_once('config.php');


/**
 * Parses the actions
 */
if (!empty($_REQUEST[SMOOTHGALLERY_ACTION])) {
	switch ($_REQUEST[SMOOTHGALLERY_ACTION]) {
		case SMOOTHGALLERY_ACTION_CSS:
			header('Content-type: text/css');
			if ($_REQUEST[SMOOTHGALLERY_ACTION_CSS_TYPE] == '1') {
				$css_file = dirname(__FILE__).'/css/jd.gallery.css';
				if (SMOOTHGALLERY_VERSION != '')
					$css_file = str_replace('.css', '.'.SMOOTHGALLERY_VERSION.'.css', $css_file);
			} else if ($_REQUEST[SMOOTHGALLERY_ACTION_CSS_TYPE] == '2') {
				$css_file = dirname(__FILE__).'/css/ReMooz.css';
			} else die();
			$css = file_get_contents($css_file);
			$css = str_replace('<HEIGHT>', $_REQUEST['height'], $css);
			$css = str_replace('<WIDTH>', $_REQUEST['width'], $css);
			$css = str_replace('<BORDERCOLOR>', $_REQUEST['bordercolor'], $css);
			$css = str_replace('<URL>', $_REQUEST['prefix'], $css);
			echo $css;
			die();
			break;

		case SMOOTHGALLERY_ACTION_IFRAME:
			# Hack: $wpdb "outside" WordPress
			require_once(dirname(__FILE__).'/../../../wp-config.php');
			require_once('utils.php');
			# extract interesting parameters from the request
			$atts = get_smoothgallery_iframe_atts();
			foreach ($atts as $key => $value) {
				if (!isset($_REQUEST[$key]) or strlen(trim($_REQUEST[$key])) == 0) continue;
				$atts[$key] = $_REQUEST[$key];
			}
			echo generate_iframe($atts, false);
			die();
			break;

		default:
			die();
			break;
	}
}


/**
 * SmoothGallery init.
 */
function smoothgallery_init() {
	if (function_exists('load_plugin_textdomain')) {
		load_plugin_textdomain('smoothgallery', 'wp-content/plugins/smoothgallery/messages');
	}
}
if (function_exists('add_action')) add_action('init', 'smoothgallery_init');


/**
 * Returns the parameters for the gallery if they're attached to a post or a
 * page, otherwise it will return default values.
 */
function get_smoothgallery_parameters() {
	# get default values
	require_once('utils.php');
	$defaults = get_default_smoothgallery_parameters();

	# overwrite default values with user-supplied data
	$globalRet = get_smoothgallery_global_parameters($defaults);
	$metaRet = get_smoothgallery_metadata($defaults);

	if ($globalRet === false and $metaRet === false) return null;

	return $defaults;
}


/**
 * If there're global parameters for the current page we'll use them.
 * If there're also parameters in a custom field they'll overwrite these defaults.
 *
 * @param array $defaults the defaults (passed by reference)
 * @return bool true if there're global parameters, otherwise false
 */
function get_smoothgallery_global_parameters(&$defaults) {
	$parameters = insertSmoothGallery();
	if ($parameters === false) return false;

	foreach ($parameters as $key => $value) {
		$defaults[$key] = $value;
	}

	return true;
}


/**
 * Examines the metadata for the current post or page and changes the default
 * values accordingly.
 *
 * @param array $defaults the defaults (passed by reference)
 * @return bool true if we changed the default values from $defaults, otherwise false
 */
function get_smoothgallery_metadata(&$defaults) {
	global $post;
	if (isset($post) == false) return false;

	# get the post's metadata and change the default values accordingly
	$meta = get_post_meta($post->ID, 'smoothgallery', true);
	# XXX: hack-start
	# -> starting with WP 2.7 get_post_meta doesn't return a result the second
	#    time it's called
	# -> that's weird
	if (!isset($GLOBALS['smoothgallery_meta'])) $GLOBALS['smoothgallery_meta'] = $meta;
	else if (empty($meta)) $meta = $GLOBALS['smoothgallery_meta'];
	# XXX: hack-end
	if (empty($meta)) return false;

	$meta = strtolower($meta);
	if ($meta != '1' and $meta != 'on') {
		# the user may use these keys
		require_once('utils.php');
		$metaKeyMap = get_default_smoothgallery_parameters(false);
		foreach ($metaKeyMap as $key => $value) {
			$param = get_smoothgallery_parameter($meta, $value);
			if ($param !== false) $defaults[$key] = $param;
		}
	}

	return true;
}


/**
 * Adds a link to the CSS stylesheet in the header.
 */
function add_smoothgallery_css() {
	$parameters = get_smoothgallery_parameters();
	if (empty($parameters)) return;

	require_once('utils.php');
	echo get_smoothgallery_css($parameters);
}
if (function_exists('add_action')) add_action('wp_head', 'add_smoothgallery_css');


/**
 * This will add the JavaScript to the footer.
 */
function add_smoothgallery_js() {
	$parameters = get_smoothgallery_parameters();
	if (empty($parameters)) return;

	require_once('utils.php');
	echo get_smoothgallery_js($parameters);
}
if (function_exists('add_action')) add_action('wp_footer', 'add_smoothgallery_js');


/**
 * Adds a custom section to the "advanced" post/page edit screens that
 * contains the generated markup for the gallery.
 */
function smoothgallery_add_custom_box() {
	if(function_exists( 'add_meta_box' )) {
		add_meta_box( 'myplugin_sectionid', 'SmoothGallery', 'smoothgallery_inner_custom_box', 'post', 'advanced' );
		add_meta_box( 'myplugin_sectionid', 'SmoothGallery', 'smoothgallery_inner_custom_box', 'page', 'advanced' );
	} else {
		add_action('dbx_post_advanced', 'smoothgallery_old_custom_box' );
		add_action('dbx_page_advanced', 'smoothgallery_old_custom_box' );
	}
}
if (function_exists('add_action')) add_action('admin_menu', 'smoothgallery_add_custom_box');

/**
 * Prints the inner fields for the custom post/page section
 */
function smoothgallery_inner_custom_box() {
	require_once('utils.php');
	$images = get_images_for_post();

	# check whether there're images available
	$markup = $images; $rows = 1;
	if (!empty($images) and is_array($images)) {
		$markup = generate_markup($images);
		$rows = 8;
	} else {
		$markup = __("There aren't any images attached here.", 'smoothgallery');
	}
?>
	<label for="smoothgallery_code"><?php _e("If you want more control over the exact markup for the gallery, copy the following code into your content.", 'smoothgallery'); ?><br/>
	<?php _e('Otherwise you may want to use the <a href="http://www.christianschenk.org/projects/wordpress-smoothgallery-plugin/#shortcode">shortcode</a> <tt>[smoothgallery]</tt> instead.', 'smoothgallery'); ?><br/>
	<?php _e("If you aren't using the <a href=\"http://www.christianschenk.org/projects/wordpress-smoothgallery-plugin/#iframe\">iFrame</a> feature make sure to add a custom field with some <a href=\"http://www.christianschenk.org/projects/wordpress-smoothgallery-plugin/#option\">options</a> for the gallery.", 'smoothgallery'); ?>
	</label><br/><br/>
	<textarea name="smoothgallery_code" readonly="readonly" cols="64" rows="<?php echo $rows; ?>"><?php echo $markup; ?></textarea>
<?php
}

/**
 * Prints the edit form for pre-WordPress 2.5 post/page
 */
function smoothgallery_old_custom_box() {
	echo '<div class="dbx-b-ox-wrapper">' . "\n";
	echo '<fieldset id="smoothgallery_fieldsetid" class="dbx-box">' . "\n";
	echo '<div class="dbx-h-andle-wrapper"><h3 class="dbx-handle">SmoothGallery</h3></div>';
	echo '<div class="dbx-c-ontent-wrapper"><div class="dbx-content">';
	// output editing form
	smoothgallery_inner_custom_box();
	echo "</div></div></fieldset></div>\n";
}


/**
 * Supplies the user with a shortcode that generates a gallery with the images
 * attached to the current post/page. If the parameter 'id' is given we'll
 * fetch the images from that post/page instead of the current one.
 */
function smoothgallery_shortcode($atts) {
	require_once('utils.php');
	$basicAtts = array('id' => NULL, 'imgsize' => NULL, 'iframe' => NULL, 'iframebgcolor' => NULL, 'dir' => NULL, 'filter' => NULL, 'randomize' => NULL);
	$flickrAtts = array('flickrusername' => NULL, 'flickrphotoset' => NULL);
	$picasaAtts = array('picasaurl' => NULL);
	$allAtts = array_merge($basicAtts, $flickrAtts, $picasaAtts);
	foreach (get_extra_smoothgallery_parameters() as $key => $value) $allAtts[$value] = NULL;
	extract(shortcode_atts($allAtts, $atts));

	# sanity checks
	#if (!is_numeric($id)) $id = NULL; FIXME
	if ($iframe !== NULL) $iframe = true;
	if ($randomize !== NULL) $randomize = true;

	if ($iframe) {
		global $post;
		$atts['id'] = ($id == NULL) ? $post->ID : $id;
		$atts['iframebgcolor'] = $iframebgcolor;
		$atts['dir'] = $dir;
		$atts['filter'] = $filter;
		$atts['randomize'] = $randomize;
		$atts['flickrusername'] = $flickrusername;
		$atts['flickrphotoset'] = $flickrphotoset;
		$atts['picasaurl'] = $picasaurl;
		# remove 'iframe' key because we don't need it after this point
		removeElementFromArray('iframe', $atts);

		return generate_iframe($atts);
	}

	return generate_markup(get_smoothgallery_images($id, $dir, $filter, $randomize, $flickrusername, $flickrphotoset, $picasaurl), $imgsize);
}
if (function_exists('add_shortcode')) add_shortcode('smoothgallery', 'smoothgallery_shortcode');


/**
 * Adds some extra fields to media edit screen.
 */
function smoothgallery_image_attachment_fields_to_edit($form_fields, $post) {
    if (substr($post->post_mime_type, 0, 5) == 'image') {
		# Link
		$link = wp_get_attachment_metadata($post->ID);
		$form_fields['smoothgallery_link'] = array('label' => __('SmoothGallery link', 'smoothgallery'),
		                                           'value' => $link['image_meta']['smoothgallery_link'],
		                                           'helps' => array(__('Link location instead of large image', 'smoothgallery')));
	}

	return $form_fields;
}
if (function_exists('add_filter')) add_filter('attachment_fields_to_edit', 'smoothgallery_image_attachment_fields_to_edit', 99, 2);

/**
 * Saves the fields from the media edit screen.
 */
function smoothgallery_image_attachment_fields_to_save($post, $attachment) {
	if (substr($post['post_mime_type'], 0, 5) == 'image') {
		$post_id = $post['ID'];
		$meta = wp_get_attachment_metadata($post_id);
		$meta['image_meta']['smoothgallery_link'] = $attachment['smoothgallery_link'];
		wp_update_attachment_metadata($post_id,  $meta);
	}
	return $post;
}
if (function_exists('add_filter')) add_filter('attachment_fields_to_save', 'smoothgallery_image_attachment_fields_to_save', 11, 2);


/**
 * Renders the widget. There can be multiple instances so the user can place as
 * many galleries in the sidebar as he wants to.
 *
 * $widget_args: number
 *    number: which of the several widgets of this type do we mean
 */
function smoothgallery_widget($args, $widget_args = 1) {
	extract($args, EXTR_SKIP);
	if (is_numeric($widget_args))
		$widget_args = array('number' => $widget_args);
	$widget_args = wp_parse_args($widget_args, array('number' => -1));
	extract($widget_args, EXTR_SKIP);

	# Data should be stored as array:  array( number => data for that instance of the widget, ... )
	$options = get_option('smoothgallery_widget');
	if (!isset($options[$number])) return;

	# remove 'widget_'-prefix from array keys
	$atts = array();
	foreach ($options[$number] as $key => $value) $atts[substr($key, strlen('widget_'), strlen($key))] = $value;
	$atts['iframe'] = true;

	# Output
	echo $before_widget;
	if (strlen(trim($atts['title'])) != 0) {
		echo $before_title;
		echo $atts['title'];
		echo $after_title;
	}
	echo smoothgallery_shortcode($atts);
	echo $after_widget;
}


/**
 * Contains the options available in the widget.
 */
function smoothgallery_widget_options() {
	require_once('utils.php');
	$options = array_merge(array('title' => NULL, 'id' => NULL, 'dir' => NULL, 'imgsize' => '150x150', 'filter' => NULL),
	                       array_slice(get_default_smoothgallery_parameters(), count(get_default_smoothgallery_parameters()) - 3, 3),
	                       array_slice(get_default_smoothgallery_parameters(), 0, 6));
	# adapt defaults
	$options['width'] = 150;
	$options['height'] = 150;

	return $options;
}


/**
 * Displays form for a particular instance of the widget.  Also updates the data after a POST submit
 *
 * $widget_args: number
 *  number: which of the several widgets of this type do we mean
 */
function smoothgallery_widget_control($widget_args = 1) {
	require_once('utils.php');
	global $wp_registered_widgets;
	static $updated = false; # Whether or not we have already updated the data after a POST submit

	if ( is_numeric($widget_args) )
		$widget_args = array( 'number' => $widget_args );
	$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
	extract( $widget_args, EXTR_SKIP );

	# Data should be stored as array:  array( number => data for that instance of the widget, ... )
	$options = get_option('smoothgallery_widget');
	if ( !is_array($options) )
		$options = smoothgallery_widget_options();

	# We need to update the data
	if ( !$updated && !empty($_POST['sidebar']) ) {
		# Tells us what sidebar to put the data in
		$sidebar = (string) $_POST['sidebar'];

		$sidebars_widgets = wp_get_sidebars_widgets();
		if ( isset($sidebars_widgets[$sidebar]) )
			$this_sidebar =& $sidebars_widgets[$sidebar];
		else
			$this_sidebar = array();

		foreach ( $this_sidebar as $_widget_id ) {
			/*
			 * Remove all widgets of this type from the sidebar.  We'll add the
 			 * new data in a second.  This makes sure we don't get any duplicate data since
			 * widget ids aren't necessarily persistent across multiple updates
			 */
			if ( 'smoothgallery_widget' == $wp_registered_widgets[$_widget_id]['callback'] && isset($wp_registered_widgets[$_widget_id]['params'][0]['number']) ) {
				$widget_number = $wp_registered_widgets[$_widget_id]['params'][0]['number'];
				# the widget has been removed. "smoothgallery-$widget_number" is "{id_base}-{widget_number}
				if ( !in_array( "smoothgallery-$widget_number", $_POST['widget-id'] ) )
					unset($options[$widget_number]);
			}
		}

		# compile data from $smoothgallery_widget_instance
		foreach ( (array) $_POST['smoothgallery-widget'] as $widget_number => $smoothgallery_widget_instance ) {
			# user clicked cancel
			if ( !isset($smoothgallery_widget_instance['widget_id']) && isset($options[$widget_number]) ) continue;

			foreach (smoothgallery_widget_options() as $key => $value) {
				$options[$widget_number]['widget_'.$key] = wp_specialchars($smoothgallery_widget_instance['widget_'.$key]);
			}
		}

		update_option('smoothgallery_widget', $options);

		$updated = true; # So that we don't go through this more than once
	}

	# set variables
	if ( -1 == $number ) {
		$number = '%i%';
	}

	# The form has inputs with names like
	# smoothgallery-widget[$number][widget_id] so that all data for that
	# instance of the widget are stored in one $_POST variable:
	# $_POST['smoothgallery-widget'][$number]
?>
	<p>
	<?php
		foreach (smoothgallery_widget_options() as $key => $value) { 
			$realValue = attribute_escape($options[$number]['widget_'.$key]);
			if (empty($options[$number])) $realValue = attribute_escape($options[$key]);
			if (!isset($realValue) and $value != $realValue) $realValue = $value;
	?>
			<label for="smoothgallery-widget-<?php echo $key.'-'.$number; ?>"><br/><?php echo $key; ?>:<br/>
				<input class="widefat" id="smoothgallery-widget-<?php echo $key.'-'.$number; ?>" name="smoothgallery-widget[<?php echo $number; ?>][widget_<?php echo $key; ?>]" type="text" value="<?php echo $realValue; ?>" />
			</label>
	<?php } ?>
		<input type="hidden" id="smoothgallery-widget-submit-<?php echo $number; ?>" name="smoothgallery-widget[<?php echo $number; ?>][submit]" value="1" />
	</p>
<?php
}


/**
 * Registers a SmoothGallery widget.
 */
function smoothgallery_widget_register() {
	if (!$options = get_option('smoothgallery_widget')) $options = array();

	$widget_ops = array('classname' => 'smoothgallery_widget', 'description' => __('A SmoothGallery with some images', 'smoothgallery'));
	$control_ops = array('width' => 400, 'height' => 350, 'id_base' => 'smoothgallery');

	$registered = false;
	foreach (array_keys($options) as $o) {
		# Old widgets can have null values for some reason
		if (!isset($options[$o]['widget_id'])) continue;

		# $id should look like {$id_base}-{$o}
		$id = "smoothgallery-$o";
		$registered = true;
		wp_register_sidebar_widget($id, 'SmoothGallery', 'smoothgallery_widget', $widget_ops, array('number' => $o));
		wp_register_widget_control($id, 'SmoothGallery', 'smoothgallery_widget_control', $control_ops, array('number' => $o));
	}

	# If there are none, we register the widget's existance with a generic template
	if (!$registered) {
		wp_register_sidebar_widget('smoothgallery-1', 'SmoothGallery', 'smoothgallery_widget', $widget_ops, array('number' => -1));
		wp_register_widget_control('smoothgallery-1', 'SmoothGallery', 'smoothgallery_widget_control', $control_ops, array('number' => -1));
	}
}
if (isset($wp_version) and version_compare($wp_version, '2.8', 'lt'))
	if (function_exists('add_action')) add_action('widgets_init', 'smoothgallery_widget_register');

if (isset($wp_version) and version_compare($wp_version, '2.8', 'ge')) {
	class SmoothGallery_Widget extends WP_Widget {
		function SmoothGallery_Widget() {
			$widget_ops = array('classname' => 'widget_smoothgallery', 'description' => __('A SmoothGallery with some images', 'smoothgallery'));
			$this->WP_Widget('smoothgallery', 'SmoothGallery', $widget_ops);
		}

		function widget($args, $instance) {
			extract($args, EXTR_SKIP);

			$instance['iframe'] = true;

			# Output
			echo $before_widget;
			if (strlen(trim($instance['title'])) != 0) {
				echo $before_title;
				echo $instance['title'];
				echo $after_title;
			}
			echo smoothgallery_shortcode($instance);
			echo $after_widget;
		}

		function update($new_instance, $old_instance) {
			$instance = $old_instance;
			foreach (smoothgallery_widget_options() as $key => $value) {
				$instance[$key] = strip_tags($new_instance[$key]);
			}
			return $instance;
		}

		function form($instance) {
			$instance = wp_parse_args( (array) $instance, smoothgallery_widget_options());

			foreach (smoothgallery_widget_options() as $key => $value) { 
				$realValue = attribute_escape(strip_tags($instance[$key]));
				if (!isset($realValue) and $value != $realValue) $realValue = $value;
			?>
			<p><label for="<?php echo $this->get_field_id($key); ?>"><br/><?php echo $key; ?>:<br/>
				<input class="widefat" id="<?php echo $this->get_field_id($key); ?>" name="<?php echo $this->get_field_name($key); ?>" type="text" value="<?php echo $realValue; ?>" />
			</label></p>
			<?php }
		}
	}

	if (function_exists('add_action'))
		add_action('widgets_init', create_function('', 'return register_widget("SmoothGallery_Widget");'));
}

?>
