<?php

require_once(dirname(__FILE__).'/fm-config.php');  # cant us SF constant until after config file read
require_once(SFFMDIR.'fm-support.php');

if(!$filemanager['allowupload'])
{
	echo FM_UPDENIED;
	exit;
}

# Initalise alert array
$notify = array(
	'type' => array(),
	'message' => array()
);

# Get the upload type
$validtypes = array('image','media','file');
$typenow = ((isset($_GET['type']) && in_array(sf_esc_str($_GET['type']),$validtypes)) ? sf_esc_str($_GET['type']) : 'image');

# Double check credentials
if(sffm_validateuser($current_user, $typenow) == false)
{
	die('Access Denied');
}

# Assign get variables
$foldernow = str_replace(array('../','..\\','./','.\\'),'',($filemanager['allowfolders'] && isset($_REQUEST['folder']) ? urldecode($_REQUEST['folder']) : ''));
$passfolder = '&folder='.urlencode($foldernow);
$foldernow = str_replace($filemanager['path'][$typenow], '', $foldernow);

# do we need to create folder...
$browsepath = $filemanager['path'][$typenow].$foldernow;
if(!file_exists($filemanager['docroot'].$browsepath))
{
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

# Assign upload path
$uploadpath = urlencode($filemanager['path'][$typenow].$foldernow);

# Assign directory structure to array
$uploaddirs=array();
sffm_dirtree($uploaddirs,$filemanager['filetype'][$typenow],$filemanager['docroot'],$filemanager['path'][$typenow]);

# determine file dialog file types
switch ($typenow)
{
	case 'image':
		$filestr = FM_TYPEIMG;
		break;
	case 'media':
		$filestr = FM_TYPEMEDIA;
		break;
	case 'file':
		$filestr = FM_TYPEFILE;
		break;
}
$fileexts = str_replace(",",";",$filemanager['filetype'][$typenow]);
$filelist = $filestr.' ('.$filemanager['filetype'][$typenow].')';

$goodqty = (isset($_GET['goodfiles']) ? sf_esc_int($_GET['goodfiles']) : 0);
$badqty = (isset($_GET['badfiles']) ? sf_esc_int($_GET['badfiles']) : 0);
$dupqty = (isset($_GET['dupfiles']) ? sf_esc_int($_GET['dupfiles']) : 0);

if($goodqty>0)
{
	$notify['type'][]='success';
	$notify['message'][]=sprintf(FM_MSGUPGOOD, $goodqty);
}
if($badqty>0)
{
	$notify['type'][]='failure';
	$notify['message'][]=sprintf(FM_MSGUPBAD, $badqty);
}
if($dupqty>0)
{
	$notify['type'][]='failure';
	$notify['message'][]=sprintf(FM_MSGUPDUP, $dupqty);
}
if(isset($_GET['permerror']))
{
	$notify['type'][]='failure';
	$notify['message'][]=sprintf(FM_MSGUPFAIL, $filemanager['docroot'].$filemanager['path'][$typenow]);
}

# urls for the forms and tabs
$fmThisForm =  'fm-upload-tab.php';
$fmBrowseTab = 'fm-browse-tab.php?type='.$typenow.$passfolder;
$fmUploadTab = 'fm-upload-tab.php?type='.$typenow.$passfolder;
$fmEditTab =   'fm-edit-tab.php?type='.$typenow.$passfolder;
$fmFolderTab = 'fm-folder-tab.php?type='.$typenow.$passfolder;

$folderpath = $filemanager['path'][$typenow];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Simple:Press File Manager : <?php echo FM_UPLOAD; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo(get_option('blog_charset')); ?>" />
<meta http-equiv="Pragma" content="no-cache" />
<link rel="stylesheet" type="text/css" media="all" href="<?php echo $filemanager['tinymcecss']; ?>" />
<link rel="stylesheet" type="text/css" media="all" href="<?php echo($filemanager['csspath'].'filemanager-tm.css.php'); ?>" />
<script type="text/javascript" src="<?php echo($filemanager['jspath'].'swfobject.js'); ?>"></script>

<script type="text/javascript">
function uploadComplete(url) {
	document.location = url;
}
</script>

</head>
<body onload='
      var so = new SWFObject("<?php echo($filemanager['homepath'].'flexupload.swf'); ?>", "mymovie", "100%", "340", "9", "#ffffff", "high");
      so.addVariable("folder", "<?php echo $uploadpath; ?>");
      so.addVariable("uptype", "<?php echo $typenow; ?>");
      so.addVariable("maxsize", "<?php echo $filemanager['maxsize'][$typenow]; ?>");
      so.addVariable("obfus", "<?php echo md5($_SERVER['DOCUMENT_ROOT'].$filemanager['obfuscate']); ?>");
      so.addVariable("filenames", "<?php echo $filelist; ?>");
      so.addVariable("extensions", "<?php echo $fileexts; ?>");
      so.addVariable("filenamelbl", "<?php echo FM_FILENAME; ?>");
      so.addVariable("sizelbl", "<?php echo FM_SIZE; ?>");
      so.addVariable("typelbl", "<?php echo FM_TYPE; ?>");
      so.addVariable("progresslbl", "<?php echo FM_PROGRESS; ?>");
      so.addVariable("browselbl", "<?php echo FM_BROWSE; ?>");
      so.addVariable("removelbl", "<?php echo FM_REMOVE; ?>");
      so.addVariable("uploadlbl", "<?php echo FM_UPLOAD; ?>");
      so.addVariable("uplimitmsg", "<?php echo FM_MSGMAXSIZE; ?>");
      so.addVariable("uplimitlbl", "<?php echo FM_TTLMAXSIZE; ?>");
      so.addVariable("uplimitbyte", "<?php echo FM_BYTES; ?>");
      so.addParam("allowScriptAccess", "always");
      so.addParam("type", "application/x-shockwave-flash");
      so.write("flashcontent");'>
<?php
if(count($notify['type'])>0) sffm_alert($notify);
sffm_form_open('foldertab',false,$fmThisForm,'?type='.$typenow);
?>
<div class="tabs">
<ul>
<li id="browse_tab"><span><a href="<?php echo $fmBrowseTab ?>"><?php echo FM_BROWSE; ?></a></span></li>
<li id="upload_tab" class="current"><span><a href="<?php echo $fmUploadTab ?>"><?php echo FM_UPLOAD; ?></a></span></li>
<?php
if($filemanager['isadmin'] == '1')
{
	?><li id="edit_tab"><span><a href="<?php echo $fmEditTab ?>"><?php echo FM_EDIT; ?></a></span></li><?php
}
if($filemanager['allowfolders'])
{
	?><li id="folders_tab"><span><a href="<?php echo $fmFolderTab ?>"><?php echo FM_FOLDERS; ?></a></span></li><?php
}
# Display folder select, if multiple exist
if(count($uploaddirs)>1)
{
	?><li id="folder_tab" class="right"><span><?php
	sffm_form_select($uploaddirs,'folder',FM_FOLDERCURR,urlencode($foldernow),true);
	?></span></li><?php
}
?>
</ul>
</div>
</form>
<div class="panel_wrapper">
<div id="general_panel" class="panel currentmod">
<fieldset>
<legend><?php echo FM_UPLOADFILES; ?></legend>
<div id="flashcontent"></div>
</fieldset></div></div>
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
</script>

<?php die(); ?>