<?php

# Include SimplePie if it doesn't exist
if ( !class_exists('SimplePie') ) {
	require_once (ABSPATH . WPINC . '/class-feed.php');
}

/**
 * Handles interactions with Google Analytics' Stat API
 *
 * @author Spiral Web Consulting
 **/
class GoogleAnalyticsStats
{
	
	# Class variables
	var $baseFeed = 'https://www.google.com/analytics/feeds';
	var $accountId;
	var $token = false;
	var $responseHash = array();
	var $responseCode = '';
	
	/**
	 * Constructor
	 *
	 * @param token - a one-time use token to be exchanged for a real token
	 **/
	function GoogleAnalyticsStats($token = false)
	{		
		# If we need to request a permanent token
		if ( $token ) {
			
			# Check if we're deauthorizing an account
			if ( $token == 'deauth' ) {
				
				# Get the current token
				$this->token = get_option('ga_google_token');
				
				# Revoke the current token
				$response = $this->http('https://www.google.com/accounts/AuthSubRevokeToken');
				
				# Remove the current token
				update_option('ga_google_token', '');
				
				return '';
				
			}
			
			$this->token = $token;
			
			# Request authentication with Google
			$response = $this->http('https://www.google.com/accounts/AuthSubSessionToken');
		
			# Get the authentication token
			$this->token = substr(strstr($response, "Token="), 6);
			
			# Save the token for future use
			update_option('ga_google_token', $this->token);
			
			# Remove the old username and password fields if they still exists
			delete_option('google_stats_user');
			delete_option('google_stats_password');
			
		} else {
			$this->token = get_option('ga_google_token');
		}
	}
	
	/**
	 * Connects using the WordPress HTTP API to get data
	 *
	 * @param url - url to request
	 * @param post - post data to pass through WordPress
	 * @return the raw http response
	 **/
	function http($url, $post = false)
	{
		# Return false if the token has not been set
		if ( trim($this->token) == '' )
			return '';
		
		# Set the arguments to pass to WordPress
		$args = array(
			'sslverify' => false
		);
		
		# Add the optional post values
		if ( $post ) {
			$post .= '&service=analytics&source=google-analyticator-' . GOOGLE_ANALYTICATOR_VERSION;
			$args['body'] = $post;
		}
		
		# Set the content to form data
		$args['headers'] = array('Content-Type' => 'application/x-www-form-urlencoded');
		
		# Add the token information
		if ( $this->token ) {
			$args['headers']['Authorization'] = 'AuthSub token="' . $this->token . '"';
		} 
		
		# Disable the fopen transport since it doesn't work with the Google API
		add_filter('use_fopen_transport', create_function('$a', 'return false;'));
		
		# Check compatibility mode settings
		if ( get_option('ga_compatibility') == 'level1' ) {
			add_filter('use_curl_transport', create_function('$a', 'return false;'));
		} elseif ( get_option('ga_compatibility') == 'level2' ) {
			add_filter('use_curl_transport', create_function('$a', 'return false;'));
			add_filter('use_streams_transport', create_function('$a', 'return false;'));
		}
		
		# Make the connection
		if ( $post )
			$response = wp_remote_post($url, $args);
		else
			$response = wp_remote_get($url, $args);
		
		# Check for WordPress error
		if ( is_wp_error($response) ) {
			$this->responseHash['error'] = __('WordPress HTTP error.', 'google-analyticator');
			return '';
		}
		
		# Set the message response
		$this->responseCode = $response['response']['code'];
		
		# Build an array of messages
		foreach( explode("\n", $response['body']) as $line ) {
			if ( trim($line) != '' ) {
				$pos = strpos($line, '=');
				if ( $pos !== false ) {
					$this->responseHash[strtolower(substr($line, 0, $pos))] = substr($line, $pos+1);
				}
			}
		}
		
		# Return the body of the response
		return $response['body'];
	}
	
	/**
	 * Checks if the username and password worked by looking at the token
	 *
	 * @return Boolean if the login details worked
	 **/
	function checkLogin()
	{
		if ( $this->token != false )
			return true;
		else
			return false;
	}
	
	/**
	 * Sets the account id to use for queries
	 *
	 * @param id - the account id
	 **/
	function setAccount($id)
	{
		$this->accountId = $id;
	}
	
	/**
	 * Get a list of Analytics accounts
	 *
	 * @return a list of analytics accounts
	 **/
	function getAnalyticsAccounts()
	{		
		# Request the list of accounts
		$response = $this->http($this->baseFeed . '/accounts/default');
		
		# Check if the response received exists, else stop processing now
		if ( $response == '' || $this->responseCode != '200' )
			return false;
		
		# Parse the XML using SimplePie
		$simplePie = new SimplePie();
		$simplePie->set_raw_data($response);
		$simplePie->init();
		$simplePie->handle_content_type();
		$accounts = $simplePie->get_items();
		
		# Make an array of the accounts
		$ids = array();
		foreach ( $accounts AS $account ) {
			$id = array();
			
			# Get the list of properties
			$properties = $account->get_item_tags('http://schemas.google.com/analytics/2009', 'property');
			
			# Loop through the properties
			foreach ( $properties AS $property ) {
				
				# Get the property information
				$name = $property['attribs']['']['name'];
				$value = $property['attribs']['']['value'];
				
				# Add the propery data to the id array
				$id[$name] = $value;
				
			}
			
			# Add the backward compatibility array items
			$id['title'] = $account->get_title();
			$id['id'] = 'ga:' . $id['ga:profileId'];
			
			$ids[] = $id;
		}
		
		return $ids;
	}
	
	/**
	 * Get a specific data metric
	 *
	 * @param metric - the metric to get
	 * @param startDate - the start date to get
	 * @param endDate - the end date to get
	 * @return the specific metric
	 **/
	function getMetric($metric, $startDate, $endDate)
	{
		# Ensure the start date is after Jan 1 2005
		$startDate = $this->verifyStartDate($startDate);
		
		# Request the metric data
		$response = $this->http($this->baseFeed . "/data?ids=$this->accountId&start-date=$startDate&end-date=$endDate&metrics=$metric");
		
		# Check if the response received exists, else stop processing now
		if ( $response == '' || $this->responseCode != '200' )
			return false;
		
		# Parse the XML using SimplePie
		$simplePie = new SimplePie();
		$simplePie->set_raw_data($response);
		$simplePie->init();
		$simplePie->handle_content_type();
		$datas = $simplePie->get_items();
	
		# Read out the data until the metric is found
		foreach ( $datas AS $data ) {
			$data_tag = $data->get_item_tags('http://schemas.google.com/analytics/2009', 'metric');
		 	return $data_tag[0]['attribs']['']['value'];
		}
	}
	
	/**
	 * Get a specific data metrics
	 *
	 * @param metrics - the metrics to get
	 * @param startDate - the start date to get
	 * @param endDate - the end date to get
	 * @param dimensions - the dimensions to grab
	 * @param sort - the properties to sort on
	 * @param filter - the property to filter on
	 * @param limit - the number of items to get
	 * @return the specific metrics in array form
	 **/
	function getMetrics($metric, $startDate, $endDate, $dimensions = false, $sort = false, $filter = false, $limit = false)
	{
		# Ensure the start date is after Jan 1 2005
		$startDate = $this->verifyStartDate($startDate);
		
		# Build the query url
		$url = $this->baseFeed . "/data?ids=$this->accountId&start-date=$startDate&end-date=$endDate&metrics=$metric";
		
		# Add optional dimensions
		if ( $dimensions )
			$url .= "&dimensions=$dimensions";
		
		# Add optional sort
		if ( $sort )
			$url .= "&sort=$sort";
		
		# Add optional filter
		if ( $filter )
			$url .= "&filters=$filter";
		
		# Add optional limit
		if ( $limit )
			$url .= "&max-results=$limit";
		
		# Request the metric data
		$response = $this->http($url);
		
		# Check if the response received exists, else stop processing now
		if ( $response == '' || $this->responseCode != '200' )
			return false;
		
		# Parse the XML using SimplePie
		$simplePie = new SimplePie();
		$simplePie->set_raw_data($response);
		$simplePie->enable_order_by_date(false);
		$simplePie->init();
		$simplePie->handle_content_type();
		$datas = $simplePie->get_items();
		
		$ids = array();
	
		# Read out the data until the metric is found
		foreach ( $datas AS $data ) {
			$metrics = $data->get_item_tags('http://schemas.google.com/analytics/2009', 'metric');
			$dimensions = $data->get_item_tags('http://schemas.google.com/analytics/2009', 'dimension');
			$id = array();
			
			$id['title'] = $data->get_title();
			
			# Loop through the dimensions
			if ( is_array($dimensions) ) {
				foreach ( $dimensions AS $property ) {
				
					# Get the property information
					$name = $property['attribs']['']['name'];
					$value = $property['attribs']['']['value'];
				
					# Add the propery data to the id array
					$id[$name] = $value;
				
				}
			}
		
			# Loop through the metrics
			if ( is_array($metrics) ) {
				foreach ( $metrics AS $property ) {
				
					# Get the property information
					$name = $property['attribs']['']['name'];
					$value = $property['attribs']['']['value'];
				
					# Add the propery data to the id array
					$id[$name] = $value;
				
				}
			}
			
			$ids[] = $id;
		}
		
		return $ids;
	}
	
	/**
	 * Checks the date against Jan. 1 2005 because GA API only works until that date
	 *
	 * @param date - the date to compare
	 * @return the correct date
	 **/
	function verifyStartDate($date)
	{
		if ( strtotime($date) > strtotime('2005-01-01') )
			return $date;
		else
			return '2005-01-01';
	}
	
} // END class

?>