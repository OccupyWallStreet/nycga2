<?php class CI_Tweets extends WP_Widget {

 function CI_Tweets(){
  $widget_ops = array('description' => 'Display your latest tweets');
  $control_ops = array('width' => 200, 'height' => 400);
  parent::WP_Widget('ci_twitter_widget', $name='-= CI Tweets =-', $widget_ops, $control_ops);
 }

 // display in frontend
 function widget($args, $instance) {

 		extract($args);
   $ci_title = $instance['ci_title'];
   $ci_username = $instance['ci_username'];
   $ci_number   = $instance['ci_number'];
 		echo $before_widget;
 		if ($ci_title) echo $before_title . $ci_title . $after_title;
 		echo '<div id="twitter_update_list"></div>';
 		?>
   <script type="text/javascript" src="http://twitter.com/statuses/user_timeline/<?php echo $ci_username; ?>.json?callback=twitterCallback2&amp;count=<?php echo $ci_number; ?>"></script>
 		<?php 
 		echo $after_widget;
 }

 // update widget
 function update($new_instance, $old_instance){
 		$instance = $old_instance;
 		$instance['ci_title'] = stripslashes($new_instance['ci_title']);
 		$instance['ci_username'] = stripslashes($new_instance['ci_username']);
 		$instance['ci_number'] = stripslashes($new_instance['ci_number']);
 		return $instance;
 	}
 
 // widget form
 function form($instance){
  	$instance = wp_parse_args( (array) $instance, array('ci_title' => '', 'ci_username'=>'', 'ci_number'=>'') );
 		$ci_title = htmlspecialchars($instance['ci_title']);
 		$ci_username = htmlspecialchars($instance['ci_username']);
 		$ci_number = htmlspecialchars($instance['ci_number']);
 		echo '<p><label>' . 'Title:' . '</label><input id="' . $this->get_field_id('ci_title') . '" name="' . $this->get_field_name('ci_title') . '" type="text" value="' . $ci_title . '" class="widefat" /></p>';
 		echo '<p><label>' . 'Username:' . '</label><input id="' . $this->get_field_id('ci_username') . '" name="' . $this->get_field_name('ci_username') . '" type="text" value="' . $ci_username . '" class="widefat" /></p>';
 		echo '<p><label>' . 'Number of tweets:' . '</label><input id="' . $this->get_field_id('ci_number') . '" name="' . $this->get_field_name('ci_number') . '" type="text" value="' . $ci_number . '" class="widefat" /></p>';

 } // form

} // class


function CI_DisplayTweets() {  
	register_widget('CI_Tweets'); 
}
add_action('widgets_init', 'CI_DisplayTweets');


if (is_active_widget(false, false, 'ci_twitter_widget'))
{
	wp_enqueue_script('wp_twitter', get_bloginfo('template_url').'/js/twitter_script.js');
}


?>