<?php

$title = 'Error';
$message = 'This URL is not inteded to be accessed directly.';

// include WP functions
require_once("../../../../../wp-blog-header.php");

// if on PHP5, include oAuth library and config
if(!version_compare(PHP_VERSION, '5.0.0', '<'))
{
    class_exists('TwitterOAuth') || include_once dirname(__FILE__).'/twitteroauth.php';
	include_once dirname(__FILE__).'/config.php';
}
else {
	$title = 'Error';
	$message = 'Twitter oAuth is not supported since PHP5 is not available';
}

// get configuration options
$tb_o = get_option('tweet-blender');

// If the oauth_token is not correct show an error message.
if (isset($_REQUEST['oauth_token']) && $tb_o['oauth_token'] !== $_REQUEST['oauth_token']) {
	$message = 'Auth token is not correct';
}
elseif ($_REQUEST['oauth_verifier']) {
	// Create TwitteroAuth object with app key/secret and token key/secret from default phase
	$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $tb_o['oauth_token'], $tb_o['oauth_token_secret']);
	
	// Request access tokens from Twitter
	$access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);
	
	// If HTTP response is 200 continue
	if ($connection->http_code == 200) {
		// Save the access tokens. Remove no longer needed request tokens
		$tb_o['oauth_access_token'] = $access_token;
		unset($tb_o['oauth_token']);
		unset($tb_o['oauth_token_secret']);
		update_option('tweet-blender',$tb_o);
	
		// Report success
		$title = 'OK';
		$message = 'Authorization was successful. You may <a href="javascript:window.close()">close</a> this window.';
	}
	// otherwise send to connect page to retry
	else {
		$title = 'Connection Error';
		$message = 'Error while connecting to Twitter. Please retry.';
		$redirect_url = $connection->getAuthorizeURL($tb_o['oauth_token']);
	}
}
?>
<html>
<head>
	<title><?php echo $title; ?></title>
	<?php if ($redirect_url) { ?>
		<META http-equiv="refresh" content="5;URL=<?php echo $redirect_url; ?>"> 
	<?php } ?>
</head>
<body>
	<h2><?php echo $title; ?></h2>
	<?php echo $message; ?>
</body>
</html>