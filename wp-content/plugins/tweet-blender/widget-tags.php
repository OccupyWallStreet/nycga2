<?php

// v3.3.4

class TweetBlenderForTags extends WP_Widget {
	
	// constructor	 
	function TweetBlenderForTags() {
		parent::WP_Widget('tweetblenderfortags', __('Tweet Blender For Tags', 'tweetblender'), array('description' => __('Shows related tweets by searching Twitter using tags of your post as keywords.', 'tweetblender')));
	}
 
	// display widget	 
	function widget($args, $instance) {

		global $post;
				
		// don't show widget if we are not on a post page
		if ($post == null || $post->post_type != 'post') {
			echo '<!-- ' . __('Tweet Blender: Not shown as this is not a post', 'tweetblender') . ' -->';
			return;
		}

		// check custom tb_tags field
		$sources = array();
		$custom_fields = get_post_custom($post->ID);
 		$post_tags = get_the_tags($post->ID);
		if (isset($custom_fields['tb_tags'])) {
			foreach($custom_fields['tb_tags'] as $key => $tags) {
				$sources = array_merge($sources,explode(',',$tags));
			}			
		}
		// check general post tags
		elseif (isset($post_tags) && is_array($post_tags) && sizeof($post_tags) > 0) {
			foreach($post_tags as $tag) {
				$sources[] = trim($tag->name);			
			}
		}
		// don't show widget if there are no tags
		else {
			echo '<!-- ' . __('Tweet Blender: Not shown as there are no tags for this post', 'tweetblender') . ' -->';
			return;
		}

		if (sizeof($args) > 0) {
			extract($args, EXTR_SKIP);			
		}
		$tb_o = get_option('tweet-blender');
		
		echo $before_widget;
		$title = empty($instance['title']) ? '&nbsp;' : apply_filters('widget_title', $instance['title']);
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; };

		$instance['widget_sources'] = join('\n\r',$sources);
			
		// add configuraiton options
		echo '<form id="' . $this->id . '-f" class="tb-widget-configuration">';
		echo '<input type="hidden" name="sources" value="' . addslashes(join(',',$sources)) . '">';
		echo '<input type="hidden" name="refreshRate" value="' . $instance['widget_refresh_rate'] . '">';
		echo '<input type="hidden" name="tweetsNum" value="' . $instance['widget_tweets_num'] . '">';
		echo '</form>';
			
		// print out header and list of tweets
		echo '<div id="'. $this->id . '-mc">';
		echo tb_create_markup($mode = 'widget',$instance,$this->id,$tb_o);

		// print out footer
		echo '<div class="tb_footer"></div>';

		echo '</div>';
		echo $after_widget;
	}

	// update/save function
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = trim(strip_tags($new_instance['title']));
		$instance['widget_refresh_rate'] = $new_instance['widget_refresh_rate'];
		$instance['widget_tweets_num'] = $new_instance['widget_tweets_num'];

		$this->message = __('Settings saved', 'tweetblender');
		return $instance;
	}
 
	// admin control form
	function form($instance) {
		global $tb_refresh_periods;

		$default = 	array( 
			'title' => __('Tweet Blender', 'tweetblender'),
			'widget_refresh_rate' => 0,
			'widget_tweets_num' => 4
		);
		$instance = wp_parse_args( (array) $instance, $default );
 
		// report messages if an
 		if (isset($this->message)) {
 			echo '<div class="updated">' . $this->message . '</div>';
 		}
		
 		// title		
		$field_id = $this->get_field_id('title');
		$field_name = $this->get_field_name('title');
		echo "\r\n".'<p><label for="'.$field_id.'">'.__('Title', 'tweetblender').': <input type="text" class="widefat" id="'.$field_id.'" name="'.$field_name.'" value="'.esc_attr( $instance['title'] ).'" /></label></p>';

		// specify refresh
		$field_id = $this->get_field_id('widget_refresh_rate');
		$field_name = $this->get_field_name('widget_refresh_rate');
		echo "\r\n".'<label for="'.$field_id.'">'.__('Refresh', 'tweetblender').'</label>';
		echo "\r\n".'<select id="'.$field_id.'" name="'.$field_name.'">';
			
		foreach ($tb_refresh_periods as $name => $sec) {
			echo "\r\n".'<option value="' . $sec . '"';
			if ($sec == $instance['widget_refresh_rate']) {
				echo ' selected';
			}
			echo '>' . $name . '</option>';
		}
		echo "\r\n".'</select><br>';

		// specify number of tweets
		$field_id = $this->get_field_id('widget_tweets_num');
		$field_name = $this->get_field_name('widget_tweets_num');
		echo "\r\n".'<br/><label for="'.$field_id.'">' . __('Show', 'tweetblender') . ' <select id="'.$field_id.'" name="'.$field_name.'">';
		for ($i = 1; $i <= 15; $i++) {
			echo "\r\n".'<option value="' . $i . '"';
			if ($i == $instance['widget_tweets_num']) {
				echo ' selected';
			}
			echo '>' . $i . '</option>';
		}
		for ($i = 20; $i <= 100; $i+=10) {
			echo "\r\n".'<option value="' . $i . '"';
			if ($i == $instance['widget_tweets_num']) {
				echo ' selected';
			}
			echo '>' . $i . '</option>';
		}
		echo "\r\n".'</select>' . __('tweets', 'tweetblender') . '</label><br>';
	}
}

add_action( 'widgets_init', create_function('', 'return register_widget("TweetBlenderForTags");') );

?>