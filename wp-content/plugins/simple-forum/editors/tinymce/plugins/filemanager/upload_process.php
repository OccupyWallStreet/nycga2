<?php

require_once(dirname(__FILE__).'/fm-config.php');  # cant us SF constant until after config file read
require_once(SFFMDIR.'fm-support.php');

# delay script if set
if($filemanager['delayprocess']>0) sleep($filemanager['delayprocess']);

# Initialise files array and error vars
$files = array();
$good = 0;
$bad = 0;
$dup = 0;
$total = (isset($_GET['filetotal']) ? $_GET['filetotal'] : 0);

# Assign get variables
$folder = $filemanager['docroot'].urldecode($_GET['folder']);
$foldernow = urlencode(str_replace($filemanager['path'][sffm_esc_str($_GET['type'])],'',urldecode($_GET['folder'])));

if ($handle = opendir($folder))
{
	while (false !== ($file = readdir($handle)))
	{
		if ($file != "." && $file != ".." && substr($file,-1)=='_')
		{
			#-- File Naming
			$tmp_filename = $folder.$file;
			$dest_filename	 = $folder.rtrim($file,'_');

			#-- Duplicate Files
			if(file_exists($dest_filename)) { unlink($tmp_filename); $dup++; continue; }

			#-- Bad extensions
			$nameparts = explode('.',$dest_filename);
			$ext = end($nameparts);

			if(!sffm_validateExtension($ext, $filemanager['prohibited'])) { unlink($tmp_filename); continue; }

			#-- Rename temp file to dest file
			rename($tmp_filename, $dest_filename);
			$good++;

			#-- if image, perform additional processing
			if(sffm_esc_str($_GET['type'])=='image')
			{
				#-- Good mime-types
				$imginfo = getimagesize($dest_filename);
	   			if($imginfo === false) { unlink($dest_filename); continue; }
				$mime = $imginfo['mime'];

				# resize image to maximum height and width, if set
				if($filemanager['imageresize']['width'] > 0 || $filemanager['imageresize']['height'] > 0)
				{
					# assign new width and height values, only if they are less than existing image size
					$widthnew  = ($filemanager['imageresize']['width'] > 0 && $filemanager['imageresize']['width'] < $imginfo[0] ? $filemanager['imageresize']['width'] : $imginfo[0]);
					$heightnew = ($filemanager['imageresize']['height'] > 0 && $filemanager['imageresize']['height'] < $imginfo[1] ? $filemanager['imageresize']['height'] :  $imginfo[1]);

					# only resize if width or height values are different
					if($widthnew != $imginfo[0] || $heightnew != $imginfo[1])
					{
						$im = sffm_convert_image($dest_filename,$mime);
						sffm_resizeimage($im,$widthnew,$heightnew,$dest_filename,$filemanager['imagequality'],$mime);
						imagedestroy($im);
					}
				}

				# generate thumbnail
				$thumbimg = $folder.'_thumbs/_'.rtrim($file,'_');
				if (!file_exists($thumbimg))
				{
					$im = sffm_convert_image($dest_filename,$mime);
					sffm_resizeimage	($im,$filemanager['thumbsize'],$filemanager['thumbsize'],$thumbimg,$filemanager['thumbquality'],$mime);
					imagedestroy ($im);
				}
			}
		}
	}
	closedir($handle);
}

$bad = $total-($good+$dup);

# Check for problem during upload
$fmUploadTab = 'fm-upload-tab.php';

if($total>0 && $bad==$total) header('Location: '.$fmUploadTab.'?type='.sffm_esc_str($_GET['type']).'&permerror=1&total='.$total);
else header('Location: '.$fmUploadTab.'?type='.sffm_esc_str($_GET['type']).'&folder='.$foldernow.'&badfiles='.$bad.'&goodfiles='.$good.'&dupfiles='.$dup);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta http-equiv="Pragma" content="no-cache" />
		<title>Simple:Press File Manager : Process Upload</title>
	</head>
	<body>
		<p>Sorry, there was an error processing file uploads.</p>
	</body>
</html>

<?php die(); ?>