<?php

require_once(dirname(__FILE__).'/fm-config.php');  # cant us SF constant until after config file read
require_once(SFFMDIR.'fm-support.php');

# Get the upload type
$validtypes = array('image','media','file');
$typenow = ((isset($_GET['type']) && in_array(sf_esc_str($_GET['type']),$validtypes)) ? sf_esc_str($_GET['type']) : 'image');

# Double check credentials
if(sffm_validateuser($current_user, $typenow) == false)
{
	die('Access Denied');
}

# Initalise alert array
$notify = array(
	'type' => array(),
	'message' => array()
);

# Assign file operation variables
$foldernow = str_replace(array('../','..\\','./','.\\'),'',($filemanager['allowfolders'] && isset($_REQUEST['folder']) ? urldecode($_REQUEST['folder']) : ''));
$foldernow = str_replace($filemanager['path'][$typenow], '', $foldernow);
$rowhlightinit =  '';

# Assign browsing options
$sortbynow = (isset($_REQUEST['sortby']) ? sf_esc_str($_REQUEST['sortby']) : $filemanager['order']['by']);
$sorttypenow = (isset($_REQUEST['sorttype']) ? sf_esc_str($_REQUEST['sorttype']) : $filemanager['order']['type']);
$sorttypeflip = ($sorttypenow == 'asc' ? 'desc' : 'asc');
$viewtypenow = (isset($_REQUEST['viewtype']) ? sf_esc_str($_REQUEST['viewtype']) : $filemanager['view']['image']);
$findnow = (isset($_POST['find']) && !empty($_POST['find']) ? sf_esc_str($_POST['find']) : false);
$showpagenow = (isset($_REQUEST['showpage']) ? sf_esc_int($_REQUEST['showpage']) : 0);

# Assign url pass variables
$passfolder = '&folder='.urlencode($foldernow);
$passviewtype = '&viewtype='.$viewtypenow;
$passsortby = '&sortby='.$sortbynow.'&sorttype='.$sorttypenow;

# Assign view, thumbnail and link paths
$browsepath = $filemanager['path'][$typenow].$foldernow;
$linkpath = $filemanager['link'][$typenow].$foldernow;
$thumbpath = $filemanager[$filemanager['thumbsrc']][$typenow].$foldernow;

# Assign sort parameters for column header links
$sortbyget = array();
$sortbyget['name'] = '&viewtype='.$viewtypenow.'&sortby=name';
$sortbyget['size'] = '&viewtype='.$viewtypenow.'&sortby=size';
$sortbyget['type'] = '&viewtype='.$viewtypenow.'&sortby=type';
$sortbyget['modified'] = '&viewtype='.$viewtypenow.'&sortby=modified';
$sortbyget['dimensions'] = '&viewtype='.$viewtypenow.'&sortby=dimensions';
$sortbyget[$sortbynow] .= '&sorttype='.$sorttypeflip;

# Assign css style for current sort type column
$thclass = array();
$thclass['name'] = '';
$thclass['size'] = '';
$thclass['type'] = '';
$thclass['modified'] = '';
$thclass['dimensions'] = '';
$thclass[$sortbynow] = ' class="'.$sorttypenow.'"';

$newthumbqty = 0;

# read folder contents if folder exists
if(file_exists($filemanager['docroot'].$browsepath))
{
	# Read directory contents and populate $file array
	$dh = opendir($filemanager['docroot'].$browsepath);
	$file = array();
	while (($filename = readdir($dh)) !== false)
	{
		# get file extension
		$nameparts = explode('.',$filename);
		$ext = end($nameparts);

		# filter directories and prohibited file types
		if($filename != '.' && $filename != '..' && !is_dir($filemanager['docroot'].$browsepath.$filename) && !in_array($ext, $filemanager['prohibited']) && ($typenow == 'file' || strpos(strtolower($filemanager['filetype'][$typenow]),strtolower($ext))))
		{
			# search file name if search term entered
			if($findnow) $exists = strpos(strtolower($filename),strtolower($findnow));

			# assign file details to array, for all files or those that match search
			if(!$findnow || ($findnow && $exists !== false))
			{
				$file['name'][] = $filename;
				$file['sortname'][] = strtolower($filename);
				$file['modified'][] = filemtime($filemanager['docroot'].$browsepath.$filename);
				$file['size'][] = filesize($filemanager['docroot'].$browsepath.$filename);

				# image specific info or general
				if($typenow=='image' && $imginfo = getimagesize($filemanager['docroot'].$browsepath.$filename))
				{
					$file['width'][] = $imginfo[0];
					$file['height'][] = $imginfo[1];
					$file['dimensions'][] = $imginfo[0] + $imginfo[1];
					$file['type'][] = $imginfo['mime'];

					# Check a thumbnail exists
					if(!file_exists($filemanager['docroot'].$browsepath.'_thumbs/')) sffm_createfolder($filemanager['docroot'].$browsepath.'_thumbs/',$filemanager['unixpermissions']);
			  		$thumbimg = $filemanager['docroot'].$browsepath.'_thumbs/_'.$filename;
					if (!file_exists($thumbimg))
					{
						$nothumbimg = $filemanager['docroot'].$browsepath.$filename;
						$mime = getimagesize($nothumbimg);
						$im = sffm_convert_image($nothumbimg,$mime['mime']);
						sffm_resizeimage($im,$filemanager['thumbsize'],$filemanager['thumbsize'],$thumbimg,$filemanager['thumbquality'],$mime['mime']);
						imagedestroy($im);
						$newthumbqty++;
					}
				} else {
					$file['width'][] = 'N/A';
					$file['height'][] = 'N/A';
					$file['dimensions'][] = 'N/A';
					$file['type'][] = sffm_returnMIMEType($filename);
				}
			}
		}
	}
	closedir($dh);
} else {
	# create file upload folder
	$success = sffm_createfolder($filemanager['docroot'].$browsepath,$filemanager['unixpermissions']);
    $userdir = str_replace('\\', '/', $browsepath);
    $userdir = preg_replace('/\/+$/', '', $userdir);
    $userdir = explode('/', $userdir);
    $l = count($userdir) - 1;
	if($success)
	{
		if($typenow=='image') sffm_createfolder($filemanager['docroot'].$browsepath.'_thumbs/',$filemanager['unixpermissions']);
		$notify['type'][]='success';
		$notify['message'][]=sprintf(FM_MSGMKDIR, $userdir[$l]);
	} else {
		$notify['type'][]='error';
		$notify['message'][]=sprintf(FM_MSGMKDIRFAIL, $userdir[$l]);
	}
}

# Assign directory structure to array
$browsedirs=array();
sffm_dirtree($browsedirs,$filemanager['filetype'][$typenow],$filemanager['docroot'],$filemanager['path'][$typenow]);

# generate alert if new thumbnails created
if($newthumbqty>0)
{
	$notify['type'][]='info';
	$notify['message'][]=sprintf(FM_MSGNEWTHUMBS, $newthumbqty);
}

# determine sort order
$sortorder = ($sorttypenow == 'asc' ? SORT_ASC : SORT_DESC);
$num_of_files = (isset($file['name']) ? count($file['name']) : 0);

if($num_of_files>0)
{
	# sort files by selected order
	sffm_sortfileorder($sortbynow,$sortorder,$file);
}

# determine pagination
if($filemanager['pagination']>0)
{
	$showpage_start = ($showpagenow ? (sf_esc_int($_REQUEST['showpage'])*$filemanager['pagination'])-$filemanager['pagination'] : 0);
	$showpage_end = $showpage_start+$filemanager['pagination'];
	if($showpage_end>$num_of_files) $showpage_end = $num_of_files;
} else {
	$showpage_start = 0;
	$showpage_end = $num_of_files;
}

# urls for the forms and tabs
$fmThisForm =  'fm-browse-tab.php';
$fmBrowseTab = 'fm-browse-tab.php?type='.$typenow.$passfolder;
$fmUploadTab = 'fm-upload-tab.php?type='.$typenow.$passfolder;
$fmEditTab =   'fm-edit-tab.php?type='.$typenow.$passfolder;
$fmFolderTab = 'fm-folder-tab.php?type='.$typenow.$passfolder;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Simple:Press File Manager : <?php echo FM_BROWSE; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo(get_option('blog_charset')); ?>" />
<meta http-equiv="Pragma" content="no-cache" />
<script language="javascript" type="text/javascript" src="<?php echo($filemanager['tmpath'].'tiny_mce_popup.js'); ?>"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo $filemanager['tinymcecss']; ?>" />
<link rel="stylesheet" type="text/css" media="all" href="<?php echo($filemanager['csspath'].'filemanager-tm.css.php'); ?>" />
</head>
<body<?php echo $rowhlightinit; ?>>

<?php
if(count($notify['type'])>0) sffm_alert($notify);
sffm_form_open('foldertab',false,$fmThisForm,'?type='.$typenow.$passviewtype.$passsortby);
?>
<div class="tabs">
<ul>
<li id="browse_tab" class="current"><span><a href="<?php echo $fmBrowseTab ?>"><?php echo FM_BROWSE; ?></a></span></li><?php

if($filemanager['allowupload'])
{
	?><li id="upload_tab"><span><a href="<?php echo $fmUploadTab ?>"><?php echo FM_UPLOAD; ?></a></span></li><?php
}
if($filemanager['isadmin'])
{
	?><li id="edit_tab"><span><a href="<?php echo $fmEditTab ?>"><?php echo FM_EDIT; ?></a></span></li><?php
}
if($filemanager['allowfolders'])
{
	?><li id="folders_tab"><span><a href="<?php echo $fmFolderTab ?>"><?php echo FM_FOLDERS; ?></a></span></li><?php
}

# Display folder select, if multiple exist
if(count($browsedirs)>1)
{
	?><li id="folder_tab" class="right"><span><?php
	sffm_form_select($browsedirs,'folder',FM_FOLDERCURR,urlencode($foldernow),true);
	?></span></li><?php
}
?>
</ul>
</div>
</form>

<div class="panel_wrapper">
<div id="general_panel" class="panel currentmod">

<fieldset>
<legend><?php echo FM_BROWSEFILES; ?></legend>
<?php
sffm_form_open('browse','custom',$fmThisForm,'?type='.$typenow.$passfolder);
?>
<div class="pushleft">
<?php
# Offer view type if file type is image
if($typenow=='image')
{
	$select = array(
		array('thumb',FM_THUMBS),
		array('detail',FM_DETAILS)
	);
	sffm_form_select($select,'viewtype',FM_VIEW,$viewtypenow,true);
}

# Show page select if pagination is set
if($filemanager['pagination']>0)
{
	$pagelimit = ceil($num_of_files/$filemanager['pagination'])+1;
	$page = array();
	for($i=1;$i<$pagelimit;$i++)
	{
		$page[] = array($i,FM_PAGE.' '.$i);
	}
	if($i>2) sffm_form_select($page,'showpage',FM_SHOW,$showpagenow,true);
}
?></div><div class="pushright"><?php

sffm_form_hidden_input('sortby',$sortbynow);
sffm_form_hidden_input('sorttype',$sorttypenow);
sffm_form_text_input('find',false,$findnow,25,50);
sffm_form_submit_button('search',FM_SEARCH,'');

?></div>
<?php

# if image show dimensions header
if($typenow=='image')
{
	$imagehead = '<th><a href="?type='.$typenow.$passfolder.$sortbyget['dimensions'].'"'.$thclass['dimensions'].'>'.FM_DIMENSIONS.'</a></th>'."\n";
}
else $imagehead = '';

echo '<div class="tabularwrapper"><table class="browse">'
	.'<tr><th><a href="?type='.$typenow.$passfolder.$sortbyget['name'].'"'.$thclass['name'].'>'.FM_FILENAME.'</a></th>'
	.'<th><a href="?type='.$typenow.$passfolder.$sortbyget['size'].'"'.$thclass['size'].'>'.FM_SIZE.'</a></th>'
	.$imagehead
	.'<th><a href="?type='.$typenow.$passfolder.$sortbyget['type'].'"'.$thclass['type'].'>'.FM_TYPE.'</th>'
	.'<th><a href="?type='.$typenow.$passfolder.$sortbyget['modified'].'"'.$thclass['modified'].'>'.FM_DATE.'</th></tr>';

# show image thumbnails, unless detail view is selected
if($typenow=='image' && $viewtypenow != 'detail')
{
	echo '</table></div>'."\n";

	for($i=$showpage_start;$i<$showpage_end;$i++)
	{
		echo '<div class="img-browser"><a href="#" onclick="sffmselectURL(\''.$linkpath.$file['name'][$i].'\');" title="'.FM_FILENAME.': '.$file['name'][$i]
			.'&#13;&#10;'.FM_DIMENSIONS.': '.$file['width'][$i].' x '.$file['height'][$i]
			.'&#13;&#10;'.FM_DATE.': '.date($filemanager['dateformat'],$file['modified'][$i])
			.'&#13;&#10;'.FM_TYPE.': '.$file['type'][$i]
			.'&#13;&#10;'.FM_SIZE.': '.sffm_bytestostring($file['size'][$i],1)
			.'"><img src="'.$thumbpath.'_thumbs/_'.$file['name'][$i]
			.'"  /><div class="filename">'.$file['name'][$i].'</div></a></div>'."\n";
	}
} else {
	for($i=$showpage_start;$i<$showpage_end;$i++)
	{
		$alt = (sffm_IsOdd($i) ? 'r1' : 'r0');
		echo '<tr class="'.$alt.'">';
		if($typenow=='image') echo '<td><a class="imghover" href="#" onclick="sffmselectURL(\''.$linkpath.$file['name'][$i].'\');" title="'.$file['name'][$i].'"><img src="'.$thumbpath.'_thumbs/_'.$file['name'][$i].'" alt="" />'.sffm_truncate_text($file['name'][$i],30).'</a></td>'."\n";
		else echo '<td><a href="#" onclick="sffmselectURL(\''.$linkpath.$file['name'][$i].'\');" title="'.$file['name'][$i].'">'.sffm_truncate_text($file['name'][$i],30).'</a></td>'."\n";
		echo '<td>'.sffm_bytestostring($file['size'][$i],1).'</td>'."\n";
		if($typenow=='image') echo '<td>'.$file['width'][$i].' x '.$file['height'][$i].'</td>'."\n";
		echo '<td>'.$file['type'][$i].'</td>'."\n"
			.'<td>'.date($filemanager['dateformat'],$file['modified'][$i]).'</td></tr>'."\n";
	}
	echo '</table></div>'."\n";
}
?>
</fieldset></div></div>
<form name="passform"><input name = "fileurl" type="hidden" value= "" /></form>
</body>
</html>

<!-- Supporting Javascript -->

<script type="text/javascript">
rowHighlight = function()
{
	var x = document.getElementsByTagName('tr');
	for (var i=0;i<x.length;i++)
	{
		x[i].onmouseover = function () {this.className = "over " + this.className;}
		x[i].onmouseout = function () {this.className = this.className.replace("over", ""); this.className = this.className.replace(" ", "");}
	}
	var y = document.getElementsByTagName('th');
	for (var ii=0;ii<y.length;ii++)
	{
		y[ii].onmouseover = function () {if(this.className != "nohvr") this.className = "over " + this.className;}
		y[ii].onmouseout = function () {this.className = this.className.replace("over", ""); this.className = this.className.replace(" ", "");}
	}
}

function sffmselectURL(url)
{
	document.passform.fileurl.value = url;
	FileBrowserDialogue.mySubmit();
}
var FileBrowserDialogue = {
	init : function () {
		rowHighlight();
	},
		mySubmit : function () {
		var URL = document.passform.fileurl.value;
		var win = tinyMCEPopup.getWindowArg("window");

		win.document.getElementById(tinyMCEPopup.getWindowArg("input")).value = URL;

		if (typeof(win.ImageDialog) != "undefined" && document.URL.indexOf('type=image') != -1)
		{
			if (win.ImageDialog.getImageData) win.ImageDialog.getImageData();
			if (win.ImageDialog.showPreviewImage) win.ImageDialog.showPreviewImage(URL);
		}
		tinyMCEPopup.close();
	}
}
tinyMCEPopup.onInit.add(FileBrowserDialogue.init, FileBrowserDialogue);

</script>

<?php die(); ?>