<?php
/*
Simple:Press
Image Uploader Script
$LastChangedDate: 2011-05-03 22:45:25 -0700 (Tue, 03 May 2011) $
$Rev: 6038 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) die('Access Denied');

# workaround function for php installs without exif.  leave original function since this is slower.
if (!function_exists('exif_imagetype'))
{
    function exif_imagetype($filename)
	{
        if ((list($width, $height, $type, $attr) = getimagesize($filename)) !== false) return $type;
    	return false;
    }
}

$uploaddir = sf_esc_str($_POST['saveloc']);
$filename = basename($_FILES['uploadfile']['name']);

# Verify the file extension
$path = pathinfo($filename);
$ext = strtolower($path['extension']);
if ($ext != 'jpg' && $ext != 'jpeg' && $ext != 'gif' && $ext != 'png')
{
	echo 'extension';
	die();
}

# check image file mimetype
$mimetype = 0;
$mimetype = exif_imagetype($_FILES['uploadfile']['tmp_name']);
if (empty($mimetype) || $mimetype == 0 || $mimetype > 3)
{
	echo 'invalid';
	die();
}

# make sure file extension and mime type actually match
if (($mimetype == 1 && $ext != 'gif') ||
	($mimetype == 2 && ($ext != 'jpg' && $ext != 'jpeg')) ||
	($mimetype == 3 && $ext != 'png')) {
	echo 'match';
	die();
}

# Clean up file name just in case
$uploadfile = $uploaddir . sf_filter_filename_save($filename);

# check for existence
if (file_exists($uploadfile))
{
	echo 'exists';
	die();
}

# check file size against limit if provided
if (isset($_POST['size']))
{
	if ($_FILES['uploadfile']['size'] > $_POST['size'])
	{
		echo 'size';
		die();
	}
}

# try uploading the file over
if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $uploadfile)) {
	@chmod("$uploadfile", 0644);
	echo "success";
} else {
	# WARNING! DO NOT USE "FALSE" STRING AS A RESPONSE!
	# Otherwise onSubmit event will not be fired
	echo "error";
}

die();

?>