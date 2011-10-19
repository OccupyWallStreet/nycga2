<?php

// version 3.3.15

// include WP functions
require_once("../../../wp-blog-header.php");

// if on PHP5, include oAuth library and config
if(!version_compare(PHP_VERSION, '5.0.0', '<'))
{
    class_exists('TwitterOAuth') || include_once dirname(__FILE__).'/lib/twitteroauth/twitteroauth.php';
	include_once dirname(__FILE__).'/lib/twitteroauth/config.php';
}

// include TweetBlender library
include_once(dirname(__FILE__).'/lib/lib.php');

// fix GoDaddy's 404 status
status_header(200);

// get options from WP
$tb_o = get_option('tweet-blender');

// if we don't have json class, get the library
if (!isset($wp_json) || !is_a($wp_json, 'Services_JSON') ) {
	if (file_exists(ABSPATH . WPINC . '/class-json.php')) {
		require_once( ABSPATH . WPINC . '/class-json.php' );
	}
	else {
		require(dirname(__FILE__).'/lib/JSON.php');
	}
	$wp_json = new Services_JSON();
}


// if request is for favorites, search results, user timeline, or list timeline
if (in_array($_GET['action'],array('search','list_timeline','user_timeline','favorites'))) {

	$params = array();
	parse_str($_SERVER['QUERY_STRING'],$params);
	unset($params['action']);

	if ($_GET['action'] == 'search') {
		// if its for screen names
		if (isset($_GET['from'])) {
			$sources = split(' OR ',$_GET['from']);
			// add the @ sign
			array_walk($sources, create_function('&$src','$src = "@" . $src;'));
		}
		elseif (isset($_GET['ors'])) {
			$sources = split(' ',$_GET['ors']);
		}
		else {
			$sources = split(' OR ',$_GET['q']);
		}
		$url = 'http://search.twitter.com/search.json';
		
	}
	elseif($_GET['action'] == 'list_timeline') {
		$sources = array('@'.$_GET['user'].'/'.$_GET['list']);
		$url = 'https://api.twitter.com/1/' . $_GET['user'] . '/lists/' . $_GET['list'] . '/statuses.json';
		unset($params['user']);
		unset($params['list']);
	}
	elseif($_GET['action'] == 'favorites') {
		$sources = array('@'.$_GET['screen_name']);
		$url = 'https://api.twitter.com/1/favorites/' . $_GET['screen_name'] . '.json';
		unset($params['user']);
	}
	elseif($_GET['action'] == 'user_timeline') {
		$sources = array('@'.$_GET['user']);
		$url = 'https://api.twitter.com/1/statuses/user_timeline/' . $_GET['screen_name'] . '.json';
		unset($params['screen_name']);
	}

	// check if it's a private source or if we are rerouting with oAuth
	if (isset($_GET['is_private']) || ($tb_o['advanced_reroute_on'] && $tb_o['advanced_reroute_type'] == 'oauth')) {
		
		// check to make sure we have the class
		if (!class_exists('TwitterOAuth')) {
			echo $wp_json->encode(array('error' => __('Twitter oAuth is not available', 'tweetb')));
			exit;
		}

		// make sure we have oAuth info
		if (!isset($tb_o['oauth_access_token'])){
			echo $wp_json->encode(array('error' => __("Do not have oAuth login info", 'tweetb')));
			exit;
		}
		else {
			// try to get it directly
			$oAuth = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $tb_o['oauth_access_token']['oauth_token'],$tb_o['oauth_access_token']['oauth_token_secret']);
			$json_data = $oAuth->OAuthRequest($url, 'GET', $params);
			if ($oAuth->http_code == 200) {
				echo $json_data;
				exit;
			}
			// else, try to get it from cache and if that fails report an error
			else {
				if ($json_data = tb_get_cached_tweets_json($sources)) {
					echo $json_data;
				}
				else {
					echo $json->encode(array('error' => __('No cache. Connection status code', 'tweetb') . ' ' . $oAuth->http_code));
				}
				exit;
			}
		}
	}
	// if we are not private/rerouting, use direct access
	else {
		// for WP3 we need to explicitly include the WP HTTP class
		if (!class_exists('WP_Http')) {
			 include_once( ABSPATH . WPINC. '/class-http.php' ); 
		}
		
		$http = new WP_Http;
		$result = $http->request($url . '?' . http_build_query($params));
	
	 	// if we could get it, return data
		if (!is_wp_error($result)) {
			if ($result['response']['code'] == 200) {
				$json_data = $result['body'];
				echo $json_data;		
				exit;
			}
			// else try to get it from cache
			else {
				
				// if found in cache, return it
				if ($json_data = tb_get_cached_tweets_json($sources)) {
					echo $json_data;
				}
				// else, report error
				else {
					echo $json->encode(array('error' => __('No cache. Connection status code', 'tweetb') . ' ' . $result['response']['code'] . " " . $result->response['message']));
				}
				exit;
			}
		}
		// if it was an error
		else {
			echo $wp_json->encode(array('error' => $result->get_error_message()));	
		}
	}
}

// check rate limit
elseif ($_GET['action'] == 'rate_limit_status') {

	if (($json_data = tb_get_server_rate_limit_json($tb_o)) != false) {
		echo $json_data;
		exit;
	}
	else {
		echo $wp_json->encode(array('error' => __("Can't retrieve limit info from Twitter", 'tweetb')));
		exit;
	}
}

// cache data
elseif($_GET['action'] == 'cache_data') {

	// make sure request came from valid source
	if (array_key_exists('HTTP_REFERER', $_SERVER)) {
		$referer = parse_url(esc_attr($_SERVER['HTTP_REFERER']));
		if ($referer['host'] != esc_attr($_SERVER['SERVER_NAME']) && $referer['host'] != 'www.' . esc_attr($_SERVER['SERVER_NAME'])) {
			echo $wp_json->encode(array('error' => __('Request from unauthorized page', 'tweetblender') . ".\n" . esc_attr($_SERVER['SERVER_NAME']) . "\n" . $referer['host']));
			exit;
		}
	}
	
	// TODO: make sure the source we are caching for is in the config of at least one widget
	
	// make sure data is really JSON
	$data = stripslashes($_POST['tweets']);
	if($tweets = $wp_json->decode($data)) {

		if(tb_save_cache($tweets)) {
			// return OK
			echo $wp_json->encode(array('OK' => 1));
		}
		else {
			echo $wp_json->encode(array('error' => __('Cannot store tweets to DB', 'tweetblender')));
		}
		exit;
	}
	else {
		echo $wp_json->encode(array('error' => __('Invalid data format', 'tweetblender')));
		exit;
	}		
}

?>