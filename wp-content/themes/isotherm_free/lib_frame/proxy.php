<?php
	// PHP Proxy
	// Loads a XML from any location. Used with Flash/Flex apps to bypass security restrictions
	// Author: Paulo Fierro
	// January 29, 2006
	// usage: proxy.php?url=http://mysite.com/myxml.xml
 
	$session = curl_init($_GET['url']);
	curl_setopt($session, CURLOPT_HEADER, false);
	curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
	$xml = curl_exec($session);
	header("Content-Type: text/xml");
	echo $xml;
	curl_close($session);
?>