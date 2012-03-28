<?php class CI_SearchTweets extends WP_Widget {

	function CI_SearchTweets(){
		$widget_ops = array('description' => __('Displays recent Tweets matching a search term', CI_DOMAIN));
		$control_ops = array('width' => 300, 'height' => 400);
		parent::WP_Widget('ci-search-tweets-widget', $name='-= CI Search Tweets =-', $widget_ops, $control_ops);
	}


	function widget($args, $instance) {
 		extract($args);

		$search = $instance['ci_search'];
		$interval = $instance['ci_interval'];;
		$title = $instance['ci_title'];
		$subject = $instance['ci_subject'];
		
		if ($instance['ci_width_auto']=='auto')
			$width="'auto'";
		else
			$width = intval($instance['ci_width']);
		
		$height= intval($instance['ci_height']);
		
		$s = intval($instance['ci_style']);
		
		//Default blue color scheme
		$styles[0]['shell_bg'] = '#8ec1da';
		$styles[0]['shell_col'] = '#ffffff';
		$styles[0]['tweet_bg'] = '#ffffff';
		$styles[0]['tweet_col'] = '#444444';
		$styles[0]['tweet_link'] = '#1985b5';

		//Black color scheme
		$styles[1]['shell_bg'] = '#000000';
		$styles[1]['shell_col'] = '#ffffff';
		$styles[1]['tweet_bg'] = '#ffffff';
		$styles[1]['tweet_col'] = '#000000';
		$styles[1]['tweet_link'] = '#1985b5';
		
		//White color scheme
		$styles[2]['shell_bg'] = '#ffffff';
		$styles[2]['shell_col'] = '#000000';
		$styles[2]['tweet_bg'] = '#ffffff';
		$styles[2]['tweet_col'] = '#000000';
		$styles[2]['tweet_link'] = '#1985b5';
		
		//Yellow color scheme
		$styles[3]['shell_bg'] = '#fffde6';
		$styles[3]['shell_col'] = '#000000';
		$styles[3]['tweet_bg'] = '#ffffff';
		$styles[3]['tweet_col'] = '#000000';
		$styles[3]['tweet_link'] = '#1985b5';
		
 		echo $before_widget;
 		
		echo '<div class="side-category">';

 		?>

		<script src="http://widgets.twimg.com/j/2/widget.js"></script>
		<script>
		new TWTR.Widget({
		  version: 2,
		  type: 'search',
		  search: '<?php echo $search; ?>',
		  interval: <?php echo ($interval * 1000); ?>,
		  title: '<?php echo $title; ?>',
		  subject: '<?php echo $subject; ?>',
		  width: <?php echo $width; ?>,
		  height: <?php echo $height; ?>,
		  theme: {
		    shell: {
		      background: '<?php echo $styles[$s]['shell_bg']; ?>',
		      color: '<?php echo $styles[$s]['shell_col']; ?>'
		    },
		    tweets: {
		      background: '<?php echo $styles[$s]['tweet_bg']; ?>',
		      color: '<?php echo $styles[$s]['tweet_col']; ?>',
		      links: '<?php echo $styles[$s]['tweet_link']; ?>'
		    }
		  },
		  features: {
		    scrollbar: false,
		    loop: true,
		    live: true,
		    hashtags: true,
		    timestamp: true,
		    avatars: true,
		    toptweets: true,
		    behavior: 'default'
		  }
		}).render().start();
		</script>
		<?

		echo '</div>';

 		echo $after_widget;

	} // widget


 	function update($new_instance, $old_instance){
		$instance = $old_instance;
		$instance['ci_search'] = stripslashes($new_instance['ci_search']);
		$instance['ci_interval'] = intval($new_instance['ci_interval']);
		$instance['ci_title'] = stripslashes($new_instance['ci_title']);
		$instance['ci_subject'] = stripslashes($new_instance['ci_subject']);
		$instance['ci_width_auto'] = stripslashes($new_instance['ci_width_auto']);
		$instance['ci_width'] = intval($new_instance['ci_width']);
		$instance['ci_height'] = intval($new_instance['ci_height']);
		$instance['ci_search'] = stripslashes($new_instance['ci_search']);
		$instance['ci_style'] = intval($new_instance['ci_style']);
				
		return $instance;
 	} // save
 
	function form($instance){
	
		$instance = wp_parse_args( (array) $instance, array(
			'ci_search'=>'CSSIgniter', 
			'ci_interval'=>6, 
			'ci_title'=>'What our customers say about us...', 
			'ci_subject'=>'Is CSSIgniter cool or what?', 
			'ci_width_auto'=>'auto', 
			'ci_width'=>250, 
			'ci_height'=>300, 
			'ci_style'=>0
		));
 		
		$search = $instance['ci_search'];
		$interval = $instance['ci_interval'];;
		$title = $instance['ci_title'];
		$subject = $instance['ci_subject'];
		$width_auto = $instance['ci_width_auto'];
		$width = intval($instance['ci_width']);
		$height= intval($instance['ci_height']);
		$style = intval($instance['ci_style']);
 		
 		
 		echo '<p><label>' . __('Search tweets for', CI_DOMAIN) . '</label><input id="' . $this->get_field_id('ci_search') . '" name="' . $this->get_field_name('ci_search') . '" type="text" value="' . $search . '" class="widefat" /></p>';
 		echo '<p><label>' . __('Search interval (in seconds)', CI_DOMAIN) . '</label><input id="' . $this->get_field_id('ci_interval') . '" name="' . $this->get_field_name('ci_interval') . '" type="text" value="' . $interval . '" class="widefat" /></p>';
 		echo '<p><label>' . __('Title', CI_DOMAIN) . '</label><input id="' . $this->get_field_id('ci_title') . '" name="' . $this->get_field_name('ci_title') . '" type="text" value="' . $title . '" class="widefat" /></p>';
 		echo '<p><label>' . __('Subject', CI_DOMAIN) . '</label><input id="' . $this->get_field_id('ci_subject') . '" name="' . $this->get_field_name('ci_subject') . '" type="text" value="' . $subject . '" class="widefat" /></p>';

		echo '<p><input id="'.$this->get_field_id('ci_width_auto').'" name="' . $this->get_field_name('ci_width_auto') . '" type="checkbox" class="checkbox" value="auto" '.checked($width_auto, 'auto', false).' /> ';
		echo '<label for="'.$this->get_field_id('ci_width_auto').'">'.__('Set width automatically?', CI_DOMAIN).'</label></p>';

 		echo '<p><label>' . __('Width (in pixels)', CI_DOMAIN) . '</label><input id="' . $this->get_field_id('ci_width') . '" name="' . $this->get_field_name('ci_width') . '" type="text" value="' . $width . '" class="widefat" /></p>';
 		echo '<p><label>' . __('Height (in pixels)', CI_DOMAIN) . '</label><input id="' . $this->get_field_id('ci_height') . '" name="' . $this->get_field_name('ci_height') . '" type="text" value="' . $height . '" class="widefat" /></p>';
 		
		echo '<label>' . __('Style', CI_DOMAIN) . '</label>';
		echo '<select id="' . $this->get_field_id('ci_style') . '" name="' . $this->get_field_name('ci_style') . '" >';
		echo '<option value="0" '.selected($style, 0, false).' >Default (blue)</option>';
		echo '<option value="1" '.selected($style, 1, false).' >Black</option>';
		echo '<option value="2" '.selected($style, 2, false).' >White</option>';
		echo '<option value="3" '.selected($style, 3, false).' >Yellow</option>';
		echo '</select>';

	} // form

} // class



function CI_SearchTweetsWidget() {
	register_widget('CI_SearchTweets');
}
add_action('widgets_init', 'CI_SearchTweetsWidget');
	
	
?>