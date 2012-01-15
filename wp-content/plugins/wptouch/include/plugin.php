<?php
	function bnc_get_wp_version() {
		$version = (float)str_replace('.','',get_bloginfo('version'));
		if ($version < 100) { $version = $version * 10; }
		$version = $version / 100;
		return $version; 
		}