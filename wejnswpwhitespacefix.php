<?php
/*
Wordpress leading whitespace fix
================================
Ever got the infamous "xml declaration not at start of external
entity" error instead of your RSS feed when using Wordpress?

Well, you're not alone. I've spent couple hours tracking down
which of the active Wordpress plugins/themes broke my RSS feed.

When the same situation repeated again on a different blog,
my patience ran out... and I wrote this script that takes
care of the issue once and for all.

If you suffer with the same problem, download the plaintext
version and follow install instructions to free yourself from
the whitespace tyranny. ;)

Download
--------
Plaintext version: http://wejn.org/stuff/wejnswpwhitespacefix.php
VIM Syntax colored: http://wejn.org/stuff/wejnswpwhitespacefix.php.html

Requirements
------------
Works with PHP5 only, as the headers_list() function is missing
in PHP4 which makes output Content-Type detection impossible.

Installation
------------
Either use this as auto_prepend in your .htaccess:

php_value "auto_prepend_file" /path/to/wejnswpwhitespacefix.php

or include it as first thing in Wordpress' index.php file even
before that "short and sweet" line:

<?php
include("wejnswpwhitespacefix.php");
// Short and sweet
define('WP_USE_THEMES', true);
require('./wp-blog-header.php');
?>

Note: For the .htaccess way your AllowOverride must include
"Options" (or better yet, be set to "All"); otherwise all you'll
be getting is "Internal Server Error".

Author: Wejn {wejn at box dot cz}
License: GPL v2.0, no latter version(s)
Version: 2.0
Changelog:
- Added better mime-type detection
- Now works even when C-T header not set
- Changed intro text to better target keywords
*/

function ___wejns_wp_whitespace_fix($input) {
	/* valid content-type? */
	$allowed = false;

	/* found content-type header? */
	$found = false;

	/* we mangle the output if (and only if) output type is text/* */
	foreach (headers_list() as $header) {
		if (preg_match("/^content-type:\\s+(text\\/|application\\/((xhtml|atom|rss)\\+xml|xml))/i", $header)) {
			$allowed = true;
		}

		if (preg_match("/^content-type:\\s+/i", $header)) {
			$found = true;
		}
	}

	/* do the actual work */
	if ($allowed || !$found) {
		return preg_replace("/\\A\\s*/m", "", $input);
	} else {
		return $input;
	}
}

/* start output buffering using custom callback */
ob_start("___wejns_wp_whitespace_fix");
?>
