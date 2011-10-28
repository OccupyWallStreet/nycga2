<?php
/*
Simple:Press
Recent Posts Widget
$LastChangedDate: 2011-01-07 03:19:34 -0700 (Fri, 07 Jan 2011) $
$Rev: 5274 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

# ===== RECENT FOUM POST WIDGET WP ======================================================================

class WP_Widget_SPF extends WP_Widget {
	function WP_Widget_SPF()
	{
		$widget_ops = array('classname' => 'widget_spf', 'description' => __('A Widget to display the latest Simple:Press posts', "sforum"));
		$this->WP_Widget('spf', __('Recent Forum Posts', 'sforum'), $widget_ops);
	}

	function widget($args, $instance)
	{
		extract($args);
		$title = empty($instance['title']) ? __("Recent Forum Posts", "sforum") : $instance['title'];
		$limit = empty($instance['limit']) ? 5 : $instance['limit'];
		$forum = empty($instance['forum']) ? 0 : $instance['forum'];
		$user = empty($instance['user']) ? 0 : $instance['user'];
		$postdate = empty($instance['postdate']) ? 0 : $instance['postdate'];
		$posttime = empty($instance['posttime']) ? 0 : $instance['posttime'];
		$idlist = empty($instance['idlist']) ? 0 : $instance['idlist'];
		$avatars = empty($instance['avatars']) ? 0 : $instance['avatars'];
		$size = empty($instance['size']) ? 25 : $instance['size'];

		# generate output
		echo $before_widget . $before_title . $title . $after_title . "<ul class='sftagul'>";
		sf_recent_posts_tag($limit, $forum, $user, $postdate, true, $idlist, $posttime, $avatars, $size);
		echo "</ul>".$after_widget;
	}

	function update($new_instance, $old_instance)
	{
		$instance = $old_instance;
		$instance['title'] = strip_tags(stripslashes($new_instance['title']));
		$instance['limit'] = strip_tags(stripslashes($new_instance['limit']));
		if (isset($new_instance['forum']))
		{
			$instance['forum'] = 1;
		} else {
			$instance['forum'] = 0;
		}
		if (isset($new_instance['user']))
		{
			$instance['user'] = 1;
		} else {
			$instance['user'] = 0;
		}
		if (isset($new_instance['postdate']))
		{
			$instance['postdate'] = 1;
		} else {
			$instance['postdate'] = 0;
		}
		if (isset($new_instance['posttime']))
		{
			$instance['posttime'] = 1;
		} else {
			$instance['posttime'] = 0;
		}
		if (isset($new_instance['avatars']))
		{
			$instance['avatars'] = 1;
		} else {
			$instance['avatars'] = 0;
		}
		$instance['size'] = strip_tags(stripslashes($new_instance['size']));
        $instance['idlist'] = strip_tags(stripslashes($new_instance['idlist']));
		return $instance;
	}

	function form($instance)
	{
		global $wpdb;
		$instance = wp_parse_args((array) $instance, array('title' => __('Recent Forum Posts', 'sforum'), 'limit' => 5, 'forum' => 1, 'user' => 1, 'postdate' => 1, 'idlist' => 0, 'posttime' => 1, 'avatars' => 0, 'size' => 25));
		$title = htmlspecialchars($instance['title'], ENT_QUOTES);
		$limit = htmlspecialchars($instance['limit'], ENT_QUOTES);
		$forum = $instance['forum'];
		$user = $instance['user'];
		$postdate = $instance['postdate'];
		$posttime = $instance['posttime'];
		$idlist = htmlspecialchars($instance['idlist'], ENT_QUOTES);
		$avatars = $instance['avatars'];
		$size = htmlspecialchars($instance['size'], ENT_QUOTES);
?>
		<!--title-->
		<p style="text-align:right;">
		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'sforum')?>
			<input style="width: 200px;" type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($title) ?>"/>
		</label></p>

		<!--how many to show -->
		<p style="text-align:right;">
		<label for="<?php echo $this->get_field_id('limit'); ?>"><?php _e('List how many posts:', 'sforum')?>
			<input style="width: 50px;" type="text" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" value="<?php echo $limit ?>"/>
		</label></p>

		<!--include forum name-->
		<p style="text-align:right;">
		<label for="sfforum-<?php echo $this->get_field_id('forum'); ?>"><?php _e('Show forum name:', 'sforum')?>
			<input type="checkbox" id="sfforum-<?php echo $this->get_field_id('forum'); ?>" name="<?php echo $this->get_field_name('forum'); ?>"
			<?php if($instance['forum'] == TRUE) {?> checked="checked" <?php } ?> />
		</label></p>

		<!--include user name-->
		<p style="text-align:right;">
		<label for="sfforum-<?php echo $this->get_field_id('user'); ?>"><?php _e('Show users name:', 'sforum')?>
			<input type="checkbox" id="sfforum-<?php echo $this->get_field_id('user'); ?>" name="<?php echo $this->get_field_name('user'); ?>"
			<?php if($instance['user'] == TRUE) {?> checked="checked" <?php } ?> />
		</label></p>

		<!--include post date-->
		<p style="text-align:right;">
		<label for="sfforum-<?php echo $this->get_field_id('postdate'); ?>"><?php _e('Show date of post:', 'sforum')?>
			<input type="checkbox" id="sfforum-<?php echo $this->get_field_id('postdate'); ?>" name="<?php echo $this->get_field_name('postdate'); ?>"
			<?php if($instance['postdate'] == TRUE) {?> checked="checked" <?php } ?> />
		</label></p>

		<!--include post time-->
		<p style="text-align:right;">
		<label for="sfforum<?php echo $this->get_field_id('posttime'); ?>"><?php _e('Show time of post (requires post date):', 'sforum')?>
			<input type="checkbox" id="sfforum-<?php echo $this->get_field_id('posttime'); ?>" name="<?php echo $this->get_field_name('posttime'); ?>"
			<?php if($instance['posttime'] == TRUE) {?> checked="checked" <?php } ?> />
		</label></p>

		<!--include avatars-->
		<p style="text-align:right;">
		<label for="sfforum-<?php echo $this->get_field_id('avatars'); ?>"><?php _e('Display Avatars:', 'sforum')?>
			<input type="checkbox" id="sfforum-<?php echo $this->get_field_id('avatars'); ?>" name="<?php echo $this->get_field_name('avatars'); ?>"
			<?php if($instance['avatars'] == TRUE) {?> checked="checked" <?php } ?> />
		</label></p>

		<!--size of avatar -->
		<p style="text-align:right;">
		<label for="<?php echo $this->get_field_id('size'); ?>"><?php _e('Maximum Avatar Display Width (pixels):', 'sforum')?>
			<input style="width: 50px;" type="text" id="<?php echo $this->get_field_id('size'); ?>" name="<?php echo $this->get_field_name('size'); ?>" value="<?php echo $size ?>"/>
		</label></p>

		<!--forum id list (comma separated)-->
		<p style="text-align:right;">
		<label for="<?php echo $this->get_field_id('idlist'); ?>"><?php _e('Forum IDs:', 'sforum')?>
			<input style="width: 100px;" type="text" id="<?php echo $this->get_field_id('idlist'); ?>" name="<?php echo $this->get_field_name('idlist'); ?>" value="<?php echo $idlist ?>"/>
		</label></p>
		<small><?php _e("If specified, Forum ID's must be separated by commas. To use ALL forums, enter a value of zero", 'sforum')?></small>
<?php
	}
}

add_action('widgets_init', 'widget_sf_init', 5);
function widget_sf_init()
{
	new WP_Widget_SPF();
	register_widget('WP_Widget_SPF');
}

?>