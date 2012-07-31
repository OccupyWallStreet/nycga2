<?php

// If uniinstall not called from wordpress exit
if(!defined('WP_UNINSTALL_PLUGIN'))
{
	exit();
}
// Delete option from Option table
delete_option('WP_PAGENAVI_STYLE_OPTION');
?>