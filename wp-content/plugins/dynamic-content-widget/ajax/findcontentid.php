<?php

define('DOING_AJAX', true);
define('WP_ADMIN', true);

require_once(dirname(__FILE__) . '/../../../../wp-load.php');
require_once(ABSPATH . '/wp-admin/includes/admin.php');
require_once('../dcw-common.php');

@header('Content-Type: text/json; charset=' . get_option('blog_charset'));
send_nosniff_header();

$q = strtolower($_REQUEST["q"]);
if (!$q) return;

$q = urldecode($q);

function write_result($rows) {
	$status = 'OK';
	if (sizeof($rows) > 1) {
		$status = 'TOO_MANY_FOUND';
	}
	echo '{"status" : "' . $status . '", "id" : "' . $rows[0]->ID . '", "title" : "'. $rows[0]->post_title .'"}';
}

$results = dcw_find_content_id($q);
if (sizeof($results) > 0) {
	write_result($results);
} else {
	echo '{"status" : "NOT_FOUND"}';
}
?>