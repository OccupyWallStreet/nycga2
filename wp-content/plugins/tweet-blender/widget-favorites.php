<?php

// version 3.3.15

class TweetBlenderFavorites extends WP_Widget {
	
	// constructor	 
	function TweetBlenderFavorites() {
		parent::WP_Widget('tweetblenderfavorites', __('Tweet Blender Favorites', 'tweetblender'), array('description' => __('Shows favorite tweets for one or multiple @users', 'tweetblender')));	
	}
 
	// display widget	 
	function widget($args, $instance) {

		global $post;
		if (sizeof($args) > 0) {
			extract($args, EXTR_SKIP);			
		}
		$tb_o = get_option('tweet-blender');
		
		// find out id/url of the archive page
		$archive_post_id = tb_get_archive_post_id();
		$archive_page_url = $instance['widget_view_more_url'];
		if (!$archive_page_url && $archive_post_id > 0) {
			$archive_page_url = get_permalink($archive_post_id);
		}
		
		// don't show widget on the archive page
		if ($post == null || ($post->ID != $archive_post_id && $archive_page_url != "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'])) {

			echo $before_widget;
			$instance['title'] = trim($instance['title']);
			$title = empty($instance['title']) ? '' : apply_filters('widget_title', $instance['title']);
			if ( !empty( $title ) ) { echo $before_title . $title . $after_title; };
			
			$sources = preg_split('/[\s+\n\r]/m',trim($instance['widget_sources']));
			$private_sources = array();
			if ($instance['widget_private_sources'] != '') {
				$private_sources = split(',',$instance['widget_private_sources']);
			}
			// remove private from general sources to avoid duplicates
			$sources = array_diff($sources,$private_sources);
			// mark private sources as private by prepending ! sign
			array_walk($private_sources,create_function('&$v,$k','if($v != "") { $v = "!".$v; }'));

			// set "view more" text
			if(isset($instance['widget_view_more_text']) && $instance['widget_view_more_text'] != '') {
				$view_more_text = $instance['widget_view_more_text'];
			} 
			else {
				$view_more_text = __("view more", 'tweetblender');
			}
			
			// add configuraiton options
			echo '<form id="' . $this->id . '-f" class="tb-widget-configuration" action="#"><div>';
			echo '<input type="hidden" name="sources" value="' . join(',',array_merge($sources,$private_sources)) . '" />';
			echo '<input type="hidden" name="refreshRate" value="' . $instance['widget_refresh_rate'] . '" />';
			echo '<input type="hidden" name="tweetsNum" value="' . $instance['widget_tweets_num'] . '" />';
			echo '<input type="hidden" name="viewMoreText" value="' . esc_attr($view_more_text) . '" />';
			echo '<input type="hidden" name="viewMoreUrl" value="' . $archive_page_url . '" />';
			echo '<input type="hidden" name="favoritesOnly" value="true" />';
			echo '</div></form>';
						
			// print out header and list of tweets
			echo '<div id="'. $this->id . '-mc">';
			echo tb_create_markup($mode = 'widget',$instance,$this->id,$tb_o);

			echo '<div class="tb_footer">';
			if(!$tb_o['archive_is_disabled'] || $tb_o['archive_is_disabled'] == false) {
				
				// indicate that using default url
				$default = '';
				if (!$instance['widget_view_more_url']) {
					$default = ' defaultUrl';
				}
				if ($archive_page_url != '') {
					echo '<a class="tb_archivelink' . $default . '" href="' . $archive_page_url . '">' . $view_more_text . ' &raquo;</a>';
				}
				elseif ($archive_post_id > 0) {
					echo '<a class="tb_archivelink' . $default . '" href="' . get_permalink($archive_post_id) . '">' . $view_more_text . ' &raquo;</a>';
				}
			}
			echo '</div>';

			echo '</div>';
			echo $after_widget;
		}
	}

	// update/save function
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$tb_o = get_option('tweet-blender');
		
		// process sources
		$errors = array();
		if (isset($old_instance['widget_sources'])) {
			$old_sources = preg_split('/[\n\r]/m', $old_instance['widget_sources']);
		}
		else {
			$old_sources = array();
		}
		$new_sources = preg_split('/[\n\r]/m', $new_instance['widget_sources']);
		$oAuth = null;
		$have_bad_sources = false; $need_oauth_tokens = false; $status_msg = array(); $log_msg = ''; $private_sources = array();

		if (isset($tb_o['widget_check_sources']) && $tb_o['widget_check_sources']) {
			foreach($new_sources as $src) {
				$src = trim($src);
				// if there is an alias
				if(strpos($src,':') > 0 ) {
					$sourceToCheck = substr($src, 0, strpos($src,':'));
				}
				else {
					$sourceToCheck = $src;
				}
				if ($src != '') {
					list($is_ok,$is_private,$need_oauth,$msg,$log) = $this->check_source($sourceToCheck,$tb_o);
					
					if (!$is_ok) {
						$have_bad_sources = true;
					}
					if ($need_oauth) {
						$need_oauth_tokens = true;
					}				
					if ($is_private) {
						$private_sources[] = $src;
					}
					$status_msg[] = $msg;
					$log_msg .= $log;
				}
			}
		
			if ($need_oauth_tokens) {				
				
				if (class_exists('TwitterOAuth')) {
					// Create TwitterOAuth object and get request token
					$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
					 
					// Get request token
					$request_token = $connection->getRequestToken(get_bloginfo('url') . '/' . PLUGINDIR . "/tweet-blender/lib/twitteroauth/callback.php");
					 
					if ($connection->http_code == 200) {
						// Save request token to session
						$tb_o['oauth_token'] = $token = $request_token['oauth_token'];
						$tb_o['oauth_token_secret'] = $request_token['oauth_token_secret'];
						update_option('tweet-blender',$tb_o);
						
						$errors[] = __("Sources have protected screen names.", 'tweetblender') . "<a href='javascript:tAuth(\"" . $connection->getAuthorizeURL($token) . "\")' title= __('Authorize Twitter Access', 'tweetblender')> __('Use your Twitter account to access them', 'tweetblender')</a>.";
		
					}
					else {
						$errors[] = __("Sources have protected screen names but Twitter oAuth is not possible at this time. Please remove them from the list.", 'tweetblender') .  "<!--" . $connection->last_api_call . "-->";
					}
				}
				else {
					$errors[] = __("Sources have protected screen names but Twitter oAuth class is not available. Please remove them from the list.", 'tweetblender');
				}
			}
		}

		if (sizeof($errors) == 0 && !$have_bad_sources) {
			$this->message = __('Settings saved', 'tweetblender') . '<br/><br/>' . join(', ',$status_msg);
			$instance['title'] = trim(strip_tags($new_instance['title']));
			$instance['widget_refresh_rate'] = $new_instance['widget_refresh_rate'];
			$instance['widget_tweets_num'] = $new_instance['widget_tweets_num'];
			$instance['widget_sources'] = $new_instance['widget_sources'];
			$instance['widget_private_sources'] = join(',',$private_sources);
			$instance['widget_view_more_url'] = $new_instance['widget_view_more_url'];
			$instance['widget_view_more_text'] = trim($new_instance['widget_view_more_text']);
			return $instance;
		}
		else {
			$this->error = join(', ',$status_msg) . " $log_msg";
			if (sizeof($errors) > 0) {
				$this->error .= '<br/><br/>' . join(', ', $errors);
			}
			$this->bad_input = $new_instance;
			return false;
		}
	}
 
	// admin control form
	function form($instance) {
		global $tb_refresh_periods;

		$default = 	array( 
			'title' => __('Favorite Tweets', 'tweetblender'),
			'widget_refresh_rate' => 0,
			'widget_tweets_num' => 4,
			'widget_sources' => ''
		);
		$instance = wp_parse_args( (array) $instance, $default );
 
 		// report errors if any
 		if (isset($this->error)) {
 			echo tb_wrap_javascript("function tAuth(url) {var tWin = window.open(url,'tWin','width=800,height=410,toolbar=0,location=1,status=0,menubar=0,resizable=1');}");
 			echo '<div class="error">' . $this->error . '</div>';
			$instance = $this->bad_input;
 		}
		// report messages if an
 		if (isset($this->message)) {
 			echo '<div class="updated">' . $this->message . '</div>';
 		}
		
 		// title		
		$field_id = $this->get_field_id('title');
		$field_name = $this->get_field_name('title');
		echo "\r\n".'<p><label for="'.$field_id.'">'.__('Title', 'tweetblender').': <input type="text" class="widefat" id="'.$field_id.'" name="'.$field_name.'" value="'.esc_attr( $instance['title'] ).'" /></label></p>';

		// sources
		$field_id = $this->get_field_id('widget_sources');
		$field_name = $this->get_field_name('widget_sources');
		echo "\r\n".'<p><label for="'.$field_id.'">'.__('Sources (one per line)', 'tweetblender').': <textarea class="widefat" id="'.$field_id.'" name="'.$field_name.'" rows=4 cols=20 wrap="hard">' . esc_attr( $instance['widget_sources'] ) . '</textarea></label></p>';

		// private sources
		$field_id = $this->get_field_id('widget_private_sources');
		$field_name = $this->get_field_name('widget_private_sources');
		if (!isset($instance['widget_private_sources'])) {
			$instance['widget_private_sources'] = '';
		} 
		echo "\r\n".'<input type="hidden" id="'.$field_id.'" name="'.$field_name.'" value="' . esc_attr( $instance['widget_private_sources'] ) . '" />';
		 		
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
		echo "\r\n".'<br/><label for="'.$field_id.'">'.__('Show', 'tweetblender').' <select id="'.$field_id.'" name="'.$field_name.'">';
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
		echo "\r\n".'</select>' .__('tweets', 'tweetblender') . '</label><br>';

		// specify text for "view more" link
		$field_id = $this->get_field_id('widget_view_more_text');
		$field_name = $this->get_field_name('widget_view_more_text');
		if (!isset($instance['widget_view_more_text'])) {
			$instance['widget_view_more_text'] = '';
		} 
		echo "\r\n".'<br/><label for="'.$field_id.'">'. sprintf(__('Text for %s link', 'tweetblender'),'&quot;' . __('view more','tweetblender') . '&quot') . ':</label>';
		echo "\r\n".'<input class="widefat" type="text" id="'.$field_id.'" name="'.$field_name.'" value="' . esc_attr( $instance['widget_view_more_text'] ) . '">';
				
		// specify URL for "view more" link
		$field_id = $this->get_field_id('widget_view_more_url');
		$field_name = $this->get_field_name('widget_view_more_url');
		if (!isset($instance['widget_view_more_url'])) {
			$instance['widget_view_more_url'] = '';
		} 
		echo "\r\n".'<br/><label for="'.$field_id.'">' . sprintf(__('URL for %s link', 'tweetblender'),'&quot;' . __('view more','tweetblender') . '&quot') . ':</label>';
		echo "\r\n".'<input class="widefat" type="text" id="'.$field_id.'" name="'.$field_name.'" value="' . esc_attr( $instance['widget_view_more_url'] ) . '"><br/>';
		if ($archive_post = tb_get_archive_post_id()) {
			echo '<span style="color:#777;font-style:italic;">' . __('Leave blank to use ', 'tweetblender') . '<a href="page.php?action=edit&post=' . $archive_post . '" target="_blank">' . __('existing page', 'tweetblender') . '</a></span>';
		}
	}
	
	function check_source($src,$tb_o) {

		global $wp_json;
		$need_oauth = false;
		$is_private = false;
		$source_check_result = '';
		$log_msg = '';
		$is_ok = false;

	    // if we don't have json class, get the library
		if ( !is_a($wp_json, 'Services_JSON') ) {
			if (file_exists(ABSPATH . WPINC . '/class-json.php')) {
				require_once( ABSPATH . WPINC . '/class-json.php' );
			}
			else {
				require(dirname(__FILE__).'/lib/JSON.php');
			}
			$wp_json = new Services_JSON();
		}
				
		// remove private account markup
		if (stripos($src,'!') === 0) {
			$src = substr($src,1);
		}
		
		// remove modifiers
		if (stripos($src,'|') > 1) {
			$source_check_result = ' ' . $src . ' - <span class="fail">' . __('FAIL', 'tweetblender') . '</span>';
			$log_msg = "($src)" . __('only screen names work with favorites', 'tweetblender') ."\n";
			return array($is_ok,$is_private,$need_oauth,$source_check_result,$log_msg);
		}
		
		$source_is_screen_name = false;
		// if it's a list, report it as bad source
		if (stripos($src,'@') === 0 && stripos($src,'/') > 1) {
			$source_check_result = ' ' . $src . ' - <span class="fail">' . __('FAIL', 'tweetblender') . '</span>';
			$log_msg = "($src)" . __('only screen names work with favorites', 'tweetblender') ."\n";
			return array($is_ok,$is_private,$need_oauth,$source_check_result,$log_msg);
		}
		// if it's a screen name, use timeline API (search would not give us private/public check)
		elseif (stripos($src,'@') === 0) {
			$source_is_screen_name = true;
			$apiUrl = 'http://api.twitter.com/1/favorites/' . substr($src,1) . '.json';
		}
		// else assume it's a hashtag or keyword, report as bad source
		else {
			$source_check_result = ' ' . $src . ' - <span class="fail">' . __('FAIL', 'tweetblender') . '</span>';
			$log_msg = "($src)" . __('only screen names work with favorites',  'tweetblender') ."\n";
			return array($is_ok,$is_private,$need_oauth,$source_check_result,$log_msg);
		}

		if (!class_exists('WP_Http')) {
			 include_once( ABSPATH . WPINC. '/class-http.php' ); 
		}		
		$http = new WP_Http;
		$result = $http->request($apiUrl);

		// try to get data from Twitter
		if (!is_wp_error($result)) {
			$jsonData = $wp_json->decode($result['body']);
			// if Twitter reported error
			if (!isset($jsonData)) {
				$source_check_result = ' ' . $src . ' - <span class="fail">' . __('FAIL', 'tweetblender') . '</span>';
				$log_msg = "($src)" . __('json error','tweetblender') . ': ' . __('could not decode body',  'tweetblender') ."\n";
			}
			elseif(isset($jsonData->{'error'})) {
			
				// if it's a private user
				if (strpos($jsonData->{'error'},"Not authorized") !== false){
					$is_private = true;
					// if we don't have access tokens - error
					if (!array_key_exists('oauth_access_token',$tb_o)) {
						$source_check_result = $src . ' - <span class="fail">' . __('PRIVATE', 'tweetblender') . '</span>';
						$log_msg = "($src)" . __('Private: needs oAuth',  'tweetblender') ."\n";
						$need_oauth = true;
					}
					// if we do have tokens - OK
					else {
						$is_ok = true;
						$source_check_result = ' ' . $src . ' - <span class="pass">' . __('PRIVATE', 'tweetblender') . '</span>';
						$log_msg = "($src)" . __('Private: we have oAuth', 'tweetblender') ."\n";
					}
				}
				// if it's just limit error we are OK
				elseif (strpos($jsonData->{error},"Rate limit exceeded") === 0) {
					$is_ok = true;
					$source_check_result = ' ' . $src . ' - <span class="pass">' . __('OK', 'tweetblender') . '</span>';
					$log_msg = "($src)" . __('Error','tweetblender') . ': ' . __('limit error', 'tweetblender') ."\n";
				}
				// any other error is an error
				else {
					$source_check_result = ' ' . $src . ' - <span class="fail">' . __('FAIL', 'tweetblender') . '</span>';
					$log_msg = "($src)" . __('json error','tweetblender') . ': ' . $jsonData->{error} . "\n";
				}
			}
			// else we assume OK
			else {
				$is_ok = true;
				$source_check_result = ' ' . $src . ' - <span class="pass">' . __('OK', 'tweetblender') . '</span>';
				$log_msg = "($src)" . __('Got json with no errors',  'tweetblender') . "\n";
			}
		}
		// if HTTP request failed
		else {

			// if it's a protected source
			if ($source_is_screen_name && $result['response']['code'] == 401) {
				$is_private = true;
				// if have tokens - try to get it
				if($tb_o['oauth_user_access_key'] && $tb_o['oauth_user_access_secret']) {
					if (!$oAuth) {
						$oAuth = new TwitterOAuth(
							$tb_o['oauth_consumer_key'],
							$tb_o['oauth_consumer_secret'],
							$tb_o['oauth_user_access_key'],
							$tb_o['oauth_user_access_secret']
						);
					}
					$content = $oAuth->OAuthRequest('https://api.twitter.com/1/favorites/' . substr($src,1) . 'search.json', array(), 'GET');
				}
				// else make user authorize
				else {
					$need_oauth = true;
					$source_check_result = ' ' . $src . ' - <span class="fail">' . __('PRIVATE', 'tweetblender') . '</span>';
					$log_msg = "($src)" . __('Private: needs oAuth',  'tweetblender') . "\n";
				}
			}
			// else it's a bad source
			else {
				$source_check_result = ' ' . $src . ':<span class="fail">' . __('FAIL', 'tweetblender') . '</span>';
				$log_msg = "($src)" . __('HTTP error:  ',  'tweetblender') . $result->get_error_message() . "\n";
			}
		}
		
		return array($is_ok,$is_private,$need_oauth,$source_check_result,$log_msg);

	}
}

add_action( 'widgets_init', create_function('', 'return register_widget("TweetBlenderFavorites");') );

?>