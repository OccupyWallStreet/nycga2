<?php
// Set headers
header("Status: 200");
header("HTTP/1.1 200 OK");
header('Content-Type: text/html');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', FALSE);
header('Pragma: no-cache');
header("Vary: Accept-Encoding");
 
function get_content($url) {
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');

	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}

$url = 'http://mvied.com/wphttps-updates.html';

$content = file_get_contents($url);

if (!$content) {
	 $content = get_content($url);
}

if ($content) {
	echo $content;
} else {
	echo "<p class=\"error\">Unable to retrieve updates.</p>";
}
?>