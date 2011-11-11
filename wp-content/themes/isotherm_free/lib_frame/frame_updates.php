<?php

function bizzthemes_updates() {
?>

<div class="clear"><!----></div>
<div id="bizz_options" class="wrap<?php if (get_bloginfo('text_direction') == 'rtl') { echo ' rtl'; } ?>">

<div class="one_col">

	<h3><?php printf(__('BizzThemes Updates Control', 'bizzthemes')); ?></h3>
	<p>
<?php
	echo "\t\t\t<div class=\"updated bizzalert\"><p>" . __('<strong>Important:</strong> before updating, please backup your database and all theme files.', 'bizzthemes') . "</p></div>\n";
?>
	</p>
	<h5 class="updates_h5"><?php printf(__('Theme Update Control', 'bizzthemes')); ?></h5>
<?php
    global $themeid, $themecode;
    $this_theme_data = get_theme_data(TEMPLATEPATH . '/style.css');
    $this_theme_version = $this_theme_data['Version'];
	
	if (base64_decode($themecode) == 'pack_1')
	    $theme_download_url = 'http://bizzthemes.com/amember/member.php';
		// $theme_download_url = 'http://bizzthemes.com/amember/my-downloads/'.strtolower($themeid).'/agency/'.strtolower($themeid).'.zip';
	elseif (base64_decode($themecode) == 'pack_2')
	    $theme_download_url = 'http://bizzthemes.com/amember/member.php';
	    // $theme_download_url = 'http://bizzthemes.com/amember/my-downloads/'.strtolower($themeid).'/standard/'.strtolower($themeid).'_standard.zip';
	elseif (base64_decode($themecode) == 'pack_3')
	    $theme_download_url = 'http://bizzthemes.com/files/'.strtolower($themeid).'_free.zip';
		
	// See if you can connet to remote theme version file
	$url = 'http://bizzthemes.com/demo/'.strtolower($themeid).'/wp-content/themes/'.strtolower($themeid).'/lib_theme/'.$this_theme_version.'.txt';
	$ch = @curl_init($url);
	@curl_setopt($ch, CURLOPT_HEADER, TRUE);
	@curl_setopt($ch, CURLOPT_NOBODY, TRUE);
	@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
	@curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	$status = array();
	preg_match('/HTTP\/.* ([0-9]+) .*/', @curl_exec($ch) , $status);
	if ($status[1] == '200') {
	    echo "\t\t\t<div class=\"updated bizzalert_false\"><p>" . __('You are using the latest theme version.', 'bizzthemes') . "</p></div>\n";
	} else {
        echo "\t\t\t<div class=\"updated bizzalert_true\"><p>" . __('Theme update is available.', 'bizzthemes') . "</p></div>\n";
		echo "\t\t\t<ul class=\"file-download\">\n";
		echo "\t\t\t<li><a href=\"$theme_download_url\" title=\"Download\">". __('Download latest theme version here', 'bizzthemes') . "</a></li>\n";
		echo "\t\t\t</ul>\n";
		echo "\t\t\t<p>". __('To update your theme go through following steps:', 'bizzthemes') . "</p>\n";
		echo "\t\t\t<ol>\n";
		echo "\t\t\t<li>". __('Backup your old theme files and database.', 'bizzthemes') . "</li>\n";
		echo "\t\t\t<li>". __('Replace all files, leave Custom folder in root theme directory intact.', 'bizzthemes') . "</li>\n";
		echo "\t\t\t<li>". __('That&#8217;s it!', 'bizzthemes') . "</li>\n";
		echo "\t\t\t</ol>\n";
	}
	// echo ($status[1]);
	// echo ($url);
	
	if ($status[1] == '200') { // display only if theme runs the latest version
?>
    <h5 class="updates_h5"><?php printf(__('Framework Library Update Control', 'bizzthemes')); ?></h5>
<?php
    global $frameversion, $frameurl;
    
	$this_frame_version = $frameversion;
	$frame_download_url = $frameurl;
		
	// See if you can connect to remote theme version file
	$url2 = 'http://bizzthemes.com/amember/my-downloads/framework/'.$this_frame_version.'.txt';
	$ch2 = @curl_init($url2);
	@curl_setopt($ch2, CURLOPT_HEADER, TRUE);
	@curl_setopt($ch2, CURLOPT_NOBODY, TRUE);
	@curl_setopt($ch2, CURLOPT_FOLLOWLOCATION, FALSE);
	@curl_setopt($ch2, CURLOPT_RETURNTRANSFER, TRUE);
	$status2 = array();
	preg_match('/HTTP\/.* ([0-9]+) .*/', @curl_exec($ch2) , $status2);
	if ($status2[1] == '200') {
	    echo "\t\t\t<div class=\"updated bizzalert_false\"><p>" . __('You are using the latest BizzThemes framework.', 'bizzthemes') . "</p></div>\n";
	} else {
        echo "\t\t\t<div class=\"updated bizzalert_true\"><p>" . __('Framework update is available.', 'bizzthemes') . "</p></div>\n";
		echo "\t\t\t<ul class='file-download'>\n";
		echo "\t\t\t<li><a href='$frame_download_url' title='Download'>" . __('Download latest framework version here', 'bizzthemes') . "</a></li>\n";
		echo "\t\t\t</ul'>\n";
		echo "\t\t\t<p>" . __('To update your theme framework go through following steps:', 'bizzthemes') . "</p>\n";
		echo "\t\t\t<ol>\n";
		echo "\t\t\t<li>" . __('Backup your old theme files and database.', 'bizzthemes') . "</li>\n";
		echo "\t\t\t<li>" . __('Replace all files in lib_frame folder with new ones.', 'bizzthemes') . "</li>\n";
		echo "\t\t\t<li>" . __('That&#8217;s it!', 'bizzthemes') . "</li>\n";
		echo "\t\t\t</ol>\n";
	}
	// echo ($status2[1]);
	// echo ($url2);
	
	}
?>
		
</div>
</div>
<?php
	}

?>