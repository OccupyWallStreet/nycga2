<?php
/*
 * Recent images box
 *
 * This code generates a SmoothGallery for the pictures attached to the most
 * recent posts. You can use the function directly somewhere in your theme or
 * use the shortcode inside a post or page. No matter how you decide, don't
 * forget to implement the "insertSmoothGallery" function OR, if you're using
 * the shortcode, add a custom field named "smoothgallery" to the post that
 * contains the configuration for the gallery.
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


/**
 * Looks for pictures attached to the most recent posts and generates markup
 * for them.
 *
 * @param $nr_of_recent_posts we're looking at this many recent posts
 * @returns string the markup or an HTML comment saying that nothing was found
 */
function insert_recent_images_box($nr_of_recent_posts = 3) {
	global $wpdb;
	$sql = 'SELECT post_content AS description, post_title AS title, post_excerpt AS caption,
	               '.$wpdb->posts.'.guid AS url, parent.guid AS link
	        FROM '.$wpdb->posts.',
	             (SELECT id, guid
	              FROM '.$wpdb->posts.'
	              WHERE post_type = "post"
	                    AND post_status = "publish"
	              ORDER BY post_date DESC
	              LIMIT '.$nr_of_recent_posts.') AS parent
	        WHERE parent.id = post_parent
	              AND post_type = "attachment"
	              AND post_mime_type like "%image%"
	        ORDER BY menu_order';
	$images = $wpdb->get_results($sql);
	if (empty($images)) return '<!-- '.__('No images found.', 'smoothgallery').' -->';

	require_once(dirname(__FILE__).'/../utils.php');
	return generate_markup($images);
}


/**
 * Although the user still needs to implement the "insertSmoothGallery"
 * function we'll make this available as a simple tag in the content. This way
 * it should be pretty easy to put a generated gallery in a post.
 */
function insert_recent_images_box_shortcode($atts) {
	return insert_recent_images_box();
}
if (function_exists('add_shortcode') and ENABLE_RECENT_IMAGES_BOX)
	add_shortcode('recent-images-box', 'insert_recent_images_box_shortcode');


/**
 * Registers a recent-images-box widget.
 */
function recent_images_box_widget_register() {
	function recent_images_box_widget($args) {
		extract($args);

		$options = get_option('recent_images_box_widget');
		$title = attribute_escape($options['title']);
		$posts = attribute_escape($options['posts']);

		# Output
		echo $before_widget;
		if (strlen(trim($title)) != 0) {
			echo $before_title;
			echo $title;
			echo $after_title;
		}
		echo insert_recent_images_box($posts);
		echo $after_widget;
	}

	if (function_exists('register_sidebar_widget'))
		register_sidebar_widget('SmoothGallery - recent images box', 'recent_images_box_widget');
	if (function_exists('register_widget_control'))
		register_widget_control('SmoothGallery - recent images box', 'recent_images_box_widget_control');
}
if (function_exists('add_action')) add_action('widgets_init', 'recent_images_box_widget_register');


/**
 * Manage recent-images-box options.
 */
function recent_images_box_widget_control() {
	$options = $newoptions = get_option('recent_images_box_widget');

	if (isset($_POST['recent-images-box-submit'])) {
	    $newoptions['title'] = strip_tags(stripslashes($_POST['recent-images-box-title']));
	    $newoptions['posts'] = strip_tags(stripslashes($_POST['recent-images-box-posts']));
	}

	if ($options != $newoptions) {
	    $options = $newoptions;
	    update_option('recent_images_box_widget', $options);
	}

	$title = attribute_escape($options['title']);
	$posts = attribute_escape($options['posts']);
?>
	<p>
	<label for="recent-images-box-title">
	<?php _e('Title', 'smoothgallery'); ?>: <input type="text" class="widefat" id="recent-images-box-title" name="recent-images-box-title" value="<?php echo $title; ?>" />
	</label>
	<br/>
	<label for="recent-images-box-posts">
	<?php _e('Number of recent posts', 'smoothgallery'); ?>: <input type="text" class="widefat" id="recent-images-box-posts" name="recent-images-box-posts" value="<?php echo $posts; ?>" />
	</label>
	</p>
	<input type="hidden" name="recent-images-box-submit" id="recent-images-box-submit" value="1" />
<?php
}

?>
