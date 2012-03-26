<?php

$path = dirname(__FILE__).'/widgets';

if ($handle = opendir($path)) {
    while (false !== ($file = readdir($handle))) {
		if ($file != "." && $file != "..") {
        	$file_info = pathinfo($path.'/'.$file);
        	if($file_info['extension']=='php')
        		require_once($path.'/'.$file);
        }
    }
	closedir($handle);
}

?>