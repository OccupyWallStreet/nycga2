<?php

require_once(dirname(__FILE__).'/fm-config.php');  # cant us SF constant until after config file read
require_once(SFFMDIR.'fm-support.php');

if(!$filemanager['allowfolders'])
{
	echo FM_FODENIED;
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

# Assign request / get / post variables
$foldernow = str_replace(array('../','..\\','./','.\\'),'',($filemanager['allowfolders'] && isset($_REQUEST['folder']) ? urldecode($_REQUEST['folder']) : ''));
$dirpath = $filemanager['path'][$typenow];
$foldernow = str_replace($filemanager['path'][$typenow], '', $foldernow);
$passfolder = '&folder='.urlencode($foldernow);

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

# Assign browsing options
$actionnow = (isset($_POST['editaction']) ? sf_esc_str($_POST['editaction']) : 'create' );

$createqty = 0;
$deleteqty = 0;
$renameqty = 0;
$errorqty = 0;

# Create any child folders with entered name
if(isset($_POST['createfolder']))
{
	foreach($_POST['createfolder'] as $parent => $newfolder)
	{
		if($newfolder != '')
		{
			$createthisfolder = $filemanager['docroot'].$dirpath.urldecode($_POST['actionfolder'][$parent]).sffm_clean_filename($newfolder);
			if (!file_exists($createthisfolder) && sffm_createfolder($createthisfolder,$filemanager['unixpermissions'])) $createqty++; else $errorqty++;
			if($typenow=='image')
			{
				sffm_createfolder($createthisfolder.'/_thumbs/',$filemanager['unixpermissions']);
			}
		}
	}
}

# Delete any checked folders
if(isset($_POST['deletefolder']))
{
	foreach($_POST['deletefolder'] as $delthis => $val)
	{
		if($typenow=='image')
		{
			$delthisthumbdir = $filemanager['docroot'].$dirpath.urldecode($_POST['actionfolder'][$delthis]).'_thumbs/';
			if (is_dir($delthisthumbdir)) rmdir($delthisthumbdir);
		}
		$delthisdir = $filemanager['docroot'].$dirpath.urldecode($_POST['actionfolder'][$delthis]);
		if (is_dir($delthisdir) && rmdir($delthisdir)) $deleteqty++; else $errorqty++;
		if($foldernow==urldecode($_POST['actionfolder'][$delthis]))
		{
        	$foldernow = '';
			$passfolder = '';
		}
	}
}

# Rename any folders with changed name
if(isset($_POST['renamefolder']))
{
	foreach($_POST['renamefolder'] as $namethis => $newname)
	{
		$urlparts = explode('/',rtrim(urldecode($_POST['actionfolder'][$namethis]),'/'));
		if(array_pop($urlparts) != $newname)
		{
			$namethisfolderfrom = $filemanager['docroot'].$dirpath.urldecode($_POST['actionfolder'][$namethis]);
			$renameurl = implode('/',$urlparts).'/'.sffm_clean_filename($newname).'/';
			$namethisfolderto = $filemanager['docroot'].$dirpath.$renameurl;
			if (is_dir($namethisfolderfrom) && rename($namethisfolderfrom,$namethisfolderto)) $renameqty++; else $errorqty++;
			if($foldernow==urldecode($_POST['actionfolder'][$namethis]))
            {
            	$foldernow = ltrim($renameurl,'/');
            	$passfolder = '&folder='.urlencode(ltrim($renameurl,'/'));
            }
		}
	}
}

# Assign directory structure to array
$dirs=array();
sffm_dirtree($dirs,$filemanager['filetype'][$typenow],$filemanager['docroot'],$filemanager['path'][$typenow]);

# generate alert if folders deleted
if($createqty>0)
{
	$notify['type'][]='success';
	$notify['message'][]=sprintf(FM_MSGCREATE, $createqty);
} elseif($deleteqty>0) {
	# generate alert if folders deleted
	$notify['type'][]='success';
	$notify['message'][]=sprintf(FM_MSGDELETE, $deleteqty);
} elseif($renameqty>0) {
	# generate alert if folders renamed
	$notify['type'][]='success';
	$notify['message'][]=sprintf(FM_MSGRENAME, $renameqty);
}

# generate alert if file errors encountered
if($errorqty>0)
{
	$notify['type'][]='failure';
	$notify['message'][]=sprintf(FM_MSGEDITERR, $errorqty);
}

# count folders
$num_of_folders = (isset($dirs) ? count($dirs) : 0);

# urls for the forms and tabs
$fmThisForm =  'fm-folder-tab.php';
$fmBrowseTab = 'fm-browse-tab.php?type='.$typenow.$passfolder;
$fmUploadTab = 'fm-upload-tab.php?type='.$typenow.$passfolder;
$fmEditTab =   'fm-edit-tab.php?type='.$typenow.$passfolder;
$fmFolderTab = 'fm-folder-tab.php?type='.$typenow.$passfolder;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Simple:Press File Manager : <?php echo FM_FOLDERS; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo(get_option('blog_charset')); ?>" />
<meta http-equiv="Pragma" content="no-cache" />
<link rel="stylesheet" type="text/css" media="all" href="<?php echo $filemanager['tinymcecss']; ?>" />
<link rel="stylesheet" type="text/css" media="all" href="<?php echo($filemanager['csspath'].'filemanager-tm.css.php'); ?>" />
</head>
<body onload="rowHighlight();">
<?php
if(count($notify['type'])>0) sffm_alert($notify);
sffm_form_open('foldertab',false,$fmThisForm,'?type='.$typenow);
?>
<div class="tabs">
<ul>
<li id="browse_tab"><span><a href="<?php echo $fmBrowseTab ?>"><?php echo FM_BROWSE; ?></a></span></li><?php
if($filemanager['allowupload'])
{
	?><li id="upload_tab"><span><a href="<?php echo $fmUploadTab ?>"><?php echo FM_UPLOAD; ?></a></span></li><?php
}
if($filemanager['isadmin'] == '1')
{
	?><li id="edit_tab"><span><a href="<?php echo $fmEditTab ?>"><?php echo FM_EDIT; ?></a></span></li><?php
}
if($filemanager['allowfolders'])
{
	?><li id="folders_tab" class="current"><span><a href="<?php echo $fmFolderTab ?>"><?php echo FM_FOLDERS; ?></a></span></li><?php
}
?>
</ul>
</div>
</form>
<div class="panel_wrapper">
<div id="general_panel" class="panel currentmod">
<fieldset>
<legend><?php echo FM_FOLDERS; ?></legend>
<?php
sffm_form_open('edit','custom',$fmThisForm,'?type='.$typenow.$passfolder);
?>
<div class="pushleft">
<?php

# Assign edit actions based on file type and permissions
$select = array();
if($filemanager['allowfolders']) $select[] = array('create',FM_CREATE);
if($filemanager['allowdelete']) $select[] = array('delete',FM_DELETE);
if($filemanager['allowedit']) $select[] = array('rename',FM_RENAME);

sffm_form_select($select,'editaction',FM_ACTION,$actionnow,true);
?></form></div><?php

sffm_form_open('actionform','custom',$fmThisForm,'?type='.$typenow.$passfolder);

if($actionnow=='move')
{ ?><div class="pushleft"><?php
	sffm_form_select($editdirs,'destination',FM_FOLDERDEST,urlencode($foldernow),false);
	?></div><?php
}

switch($actionnow)
{
	case 'delete':
		$actionhead = FM_DELETE;
		break;
	case 'rename':
		$actionhead = FM_RENAME;
		break;
	case 'create':
		$actionhead = FM_CREATE;
		break;
	default:
		# do nothing
}
?><div class="tabularwrapper"><table class="browse"><tr>
<th class="nohvr"><?php echo FM_FOLDERNAME; ?></th>
<th class="nohvr"><?php echo FM_FILES; ?></th>
<th class="nohvr"><?php echo FM_DATE; ?></th>
<th class="nohvr"><?php echo $actionhead; ?></th></tr>
<?php

for($i=0;$i<$num_of_folders;$i++)
{
	$disable = ($i == 0 ? true : false);
	$alt = (sffm_IsOdd($i) ? 'r1' : 'r0');
	echo '<tr class="'.$alt.'">';
	echo '<td>'.$dirs[$i][2].'</td>';
	echo '<td>'.$dirs[$i][4].'</td><td>'.date($filemanager['dateformat'],$dirs[$i][5]).'</td>'
	.'<td>';
	sffm_form_hidden_input('actionfolder['.$i.']',$dirs[$i][0]);
	switch($actionnow)
	{
		case 'create':
			echo '&rarr; ';
			sffm_form_text_input('createfolder['.$i.']',false,'',30,120);
			break;
		case 'delete':
			$disabledel = ($dirs[$i][4] > 0 ? ' DISABLED' : '');
			if(!$disable) echo '<input class="del" type="checkbox" name="deletefolder['.$i.']" value="1"'.$disabledel.' />';
			break;
		case 'rename':
			if(!$disable) sffm_form_text_input('renamefolder['.$i.']',false,$dirs[$i][3],30,120);
			break;
		default:
			# do nothing
	}
	echo "</td></tr>\n";
}

echo "</table></div>\n".'<div class="pushright">';
if($filemanager['allowdelete'] && $filemanager['allowedit'])
{
	sffm_form_hidden_input('editaction',$actionnow);
	sffm_form_submit_button('commit',$actionhead.' '.FM_FOLDERS,'edit');
}
?>
</div></fieldset></div></div>
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