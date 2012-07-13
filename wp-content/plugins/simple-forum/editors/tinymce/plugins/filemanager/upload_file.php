<?php

require_once(dirname(__FILE__).'/fm-config.php');  # cant us SF constant until after config file read
require_once(SFFMDIR.'fm-support.php');

# Check hash is correct (workaround for Flash session bug, to stop external form posting)
if($_GET['obfuscate'] != md5($_SERVER['DOCUMENT_ROOT'].$filemanager['obfuscate']))
{
	die('Access Denied');
}

# Check  and assign get variables
if(isset($_GET['type']))
{
	$typenow = sffm_esc_str($_GET['type']);
} else {
	die('Access Denied');
}

if(isset($_GET['folder']))
{
	$dest_folder = urldecode($_GET['folder']);
} else {
	die('Access Denied');
}

# Check file extension isn't prohibited
$nameparts = explode('.',$_FILES['Filedata']['name']);
$ext = end($nameparts);

if(!sffm_validateExtension($ext, $filemanager['prohibited']))
{
	die('Access Denied');
}

$fmUploadTab = 'fm-upload-tab.php';

# Check file data
if ($_FILES['Filedata']['tmp_name'] && $_FILES['Filedata']['name'])
{
	$source_file = $_FILES['Filedata']['tmp_name'];
	$file_name = stripslashes($_FILES['Filedata']['name']);
	if($filemanager['cleanfilename']) $file_name = sffm_clean_filename($file_name);
	if(is_dir($filemanager['docroot'].$dest_folder))
	{
		$success = copy($source_file,$filemanager['docroot'].$dest_folder.'/'.$file_name.'_');
	}
	if($success)
	{
		header('HTTP/1.1 200 OK'); # if this doesn't work for you, try header('HTTP/1.1 201 Created');
		?><html><head><title>File Upload Success</title></head><body>File Upload Success</body></html><?php
	}
}

die();

?>