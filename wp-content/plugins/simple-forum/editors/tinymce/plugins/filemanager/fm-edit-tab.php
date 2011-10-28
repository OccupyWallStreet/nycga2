<?php

require_once(dirname(__FILE__).'/fm-config.php');  # cant us SF constant until after config file read
require_once(SFFMDIR.'fm-support.php');

if(($filemanager['isadmin']==false) || (!$filemanager['allowedit'] && !$filemanager['allowdelete']))
{
	echo FM_EDDENIED;
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

# Assign file operation variables
$foldernow = str_replace(array('../','..\\','./','.\\'),'',($filemanager['allowfolders'] && isset($_REQUEST['folder']) ? urldecode($_REQUEST['folder']) : ''));
$destfolder = (isset($_POST['destination']) ? $filemanager['path'][$typenow].urldecode($_POST['destination']) : '');
$destfoldernow = (isset($_POST['destination']) ? urldecode($_POST['destination']) : $foldernow);

# Assign edit and thumbnail path
$editpath = $filemanager['path'][$typenow].$foldernow;
$thumbpath = $filemanager[$filemanager['thumbsrc']][$typenow].$foldernow;

# Assign browsing options
$sortbynow = (isset($_REQUEST['sortby']) ? sf_esc_str($_REQUEST['sortby']) : $filemanager['order']['by']);
$sorttypenow = (isset($_REQUEST['sorttype']) ? sf_esc_str($_REQUEST['sorttype']) : $filemanager['order']['type']);
$sorttypeflip = ($sorttypenow == 'asc' ? 'desc' : 'asc');
$viewtypenow = 'detail';
$findnow = (isset($_REQUEST['find']) && !empty($_REQUEST['find']) ? sf_esc_str($_REQUEST['find']) : false);
$actionnow = (isset($_REQUEST['action']) ? sf_esc_str($_REQUEST['action']) : $filemanager['defaultaction'] );
$showpagenow = (isset($_REQUEST['showpage']) ? sf_esc_int($_REQUEST['showpage']) : 0);

# Assign url pass variables
$passfolder = '&folder='.urlencode($foldernow);
$passaction = '&action='.$actionnow;
$passsortby = '&sortby='.$sortbynow.'&sorttype='.$sorttypenow;

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

# Assign sort parameters for column header links
$sortbyget = array();
$sortbyget['name'] = '&sortby=name';
$sortbyget['size'] = '&sortby=size';
$sortbyget['type'] = '&sortby=type';
$sortbyget['modified'] = '&sortby=modified';
$sortbyget['dimensions'] = '&sortby=dimensions';
$sortbyget[$sortbynow] .= '&sorttype='.$sorttypeflip;

# Assign css style for current sort type column
$thclass = array();
$thclass['name'] = '';
$thclass['size'] = '';
$thclass['type'] = '';
$thclass['modified'] = '';
$thclass['dimensions'] = '';
$thclass[$sortbynow] = ' class="'.$sorttypenow.'"';

$deleteqty = 0;
$renameqty = 0;
$resizeqty = 0;
$rotateqty = 0;
$moveqty = 0;
$errorqty = 0;

# Set when rotating images to force thumbnail refresh
$imagerefresh ='';

# Delete any checked files
if(isset($_POST['deletefile']))
{
	foreach($_POST['deletefile'] as $delthis => $val)
	{
		$delthisfile = $filemanager['docroot'].$editpath.$_POST['actionfile'][$delthis];
		if (file_exists($delthisfile) && unlink($delthisfile)) $deleteqty++; else $errorqty++;
		if($typenow=='image')
		{
			$delthisthumb = $filemanager['docroot'].$editpath.'_thumbs/_'.$_POST['actionfile'][$delthis];
			if (file_exists($delthisthumb)) unlink($delthisthumb);
		}
	}
}

# Rename any files with changed name
if(isset($_POST['renamefile']))
{
	foreach($_POST['renamefile'] as $namethis => $newname)
	{
		if($_POST['actionfile'][$namethis] != $newname.$_POST['renameext'][$namethis])
		{
			$namethisfilefrom = $filemanager['docroot'].$editpath.$_POST['actionfile'][$namethis];
			$namethisfileto = $filemanager['docroot'].$editpath.sffm_clean_filename($newname.$_POST['renameext'][$namethis]);
			if (file_exists($namethisfilefrom) && rename($namethisfilefrom,$namethisfileto)) $renameqty++; else $errorqty++;
			if($typenow=='image')
			{
				$namethisthumbfrom = $filemanager['docroot'].$editpath.'_thumbs/_'.$_POST['actionfile'][$namethis];
				$namethisthumbto = $filemanager['docroot'].$editpath.'_thumbs/_'.sffm_clean_filename($newname.$_POST['renameext'][$namethis]);
				if (file_exists($namethisthumbfrom)) rename($namethisthumbfrom,$namethisthumbto);
			}
		}
	}
}

# Move any checked files
if(isset($_POST['movefile']))
{
	foreach($_POST['movefile'] as $movethis => $val)
	{
		$movethisfile = $filemanager['docroot'].$editpath.$_POST['actionfile'][$movethis];
		$movefiledest = $filemanager['docroot'].$destfolder.$_POST['actionfile'][$movethis];
		if (!file_exists($movefiledest) && file_exists($movethisfile) && copy($movethisfile,$movefiledest))
        {
        	$moveqty++;
			unlink($movethisfile);
			if($typenow=='image')
			{
				$movethisthumb = $filemanager['docroot'].$editpath.'_thumbs/_'.$_POST['actionfile'][$movethis];
				$movethumbdest = $filemanager['docroot'].$destfolder.'_thumbs/_'.$_POST['actionfile'][$movethis];
				if (file_exists($movethisthumb) && copy($movethisthumb,$movethumbdest)) unlink($movethisthumb);
			}
		} else $errorqty++;
	}
}

# Resize any files with new size
if(isset($_POST['resizefile']))
{
	foreach($_POST['resizefile'] as $sizethis => $newsize)
	{
		$newsize = intval($newsize);
		if($newsize)
		{
			# detect silly sizes
			if($newsize > $filemanager['thumbsize'])
			{
				# do image resize
				$targetimg = $filemanager['docroot'].$editpath.$_POST['actionfile'][$sizethis];
				if (file_exists($targetimg))
				{
					$mime = getimagesize($targetimg);
					if($_POST['resizetype'][$sizethis]=='width')
					{
						$rw = $newsize;
						$rh = $mime[1];
					} else {
						$rw = $mime[0];
						$rh = $newsize;
					}
					$im = sffm_convert_image($targetimg,$mime['mime']);
					sffm_resizeimage($im,$rw,$rh,$targetimg,$filemanager['imagequality'],$mime['mime']);
					imagedestroy($im);
					$resizeqty++;
				} else $errorqty++;
			} else $errorqty++;
		}
	}
}

# Rotate any selected files
if(isset($_POST['rotatefile']))
{
	$imagerefresh = '?refresh='.uniqid('');
	foreach($_POST['rotatefile'] as $rotatethis => $direction)
	{
		if($direction != 'none')
		{
			$targetimg = $filemanager['docroot'].$editpath.$_POST['actionfile'][$rotatethis];
			if (file_exists($targetimg))
			{
				# rotate image
				if($direction == 'clock') $degree=270; else $degree=90;
				$mime = getimagesize($targetimg);
				$im = sffm_convert_image($targetimg,$mime['mime']);

				# additional processing for png / gif transparencies (credit to Dirk Bohl)
				if($mime['mime'] == 'image/x-png' || $mime['mime'] == 'image/png')
				{
					imagealphablending($newim, false);
					imagesavealpha($newim, true);
				} elseif($mime['mime'] == 'image/gif') {
					$originaltransparentcolor = imagecolortransparent( $im );
					if($originaltransparentcolor >= 0 && $originaltransparentcolor < imagecolorstotal( $im ))
					{
						$transparentcolor = imagecolorsforindex( $im, $originaltransparentcolor );
						$newtransparentcolor = imagecolorallocate($newim,$transparentcolor['red'],$transparentcolor['green'],$transparentcolor['blue']);
						imagefill( $newim, 0, 0, $newtransparentcolor );
						imagecolortransparent( $newim, $newtransparentcolor );
					}
				}
				$newim = imagerotate($im, $degree, 0);
				imagedestroy($im);

            	if($mime['mime'] == 'image/pjpeg' || $mime['mime'] == 'image/jpeg')
					imagejpeg ($newim,$targetimg,$filemanager['imagequality']);
            	elseif($mime['mime'] == 'image/x-png' || $mime['mime'] == 'image/png')
               		imagepng ($newim,$targetimg,substr($filemanager['imagequality'],0,1));
            	elseif($mime['mime'] == 'image/gif')
               		imagegif ($newim,$targetimg);
				imagedestroy($newim);
				$rotateqty++;

				# delete and recreate thumbnail image
				$targetthumb = $filemanager['docroot'].$editpath.'_thumbs/_'.$_POST['actionfile'][$rotatethis];
				if (file_exists($targetthumb)) unlink($targetthumb);
				$mime = getimagesize($targetimg);
				$im = sffm_convert_image($targetimg,$mime['mime']);
				sffm_resizeimage($im,$filemanager['thumbsize'],$filemanager['thumbsize'],$targetthumb,$filemanager['thumbquality'],$mime['mime']);
				imagedestroy($im);
			} else $errorqty++;
		}
	}
}

# Read directory contents and populate $file array
$dh = opendir($filemanager['docroot'].$editpath);
$file = array();
while (($filename = readdir($dh)) !== false)
{
	# get file extension
	$nameparts = explode('.',$filename);
	$ext = end($nameparts);

	# filter directories and prohibited file types
	if($filename != '.' && $filename != '..' && !is_dir($filemanager['docroot'].$editpath.$filename) && !in_array($ext, $filemanager['prohibited']) && ($typenow == 'file' || strpos(strtolower($filemanager['filetype'][$typenow]),strtolower($ext))))
	{
		# search file name if search term entered
		if($findnow) $exists = strpos(strtolower($filename),strtolower($findnow));

		# assign file details to array, for all files or those that match search
		if(!$findnow || ($findnow && $exists !== false))
		{
			$file['name'][] = $filename;
			$file['sortname'][] = strtolower($filename);
			$file['modified'][] = filemtime($filemanager['docroot'].$editpath.$filename);
			$file['size'][] = filesize($filemanager['docroot'].$editpath.$filename);

			# image specific info or general
			if($typenow=='image' && $imginfo = getimagesize($filemanager['docroot'].$editpath.$filename))
			{
				$file['width'][] = $imginfo[0];
				$file['height'][] = $imginfo[1];
				$file['dimensions'][] = $imginfo[0] + $imginfo[1];
				$file['type'][] = $imginfo['mime'];
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

# Assign directory structure to array
$editdirs=array();
sffm_dirtree($editdirs,$filemanager['filetype'][$typenow],$filemanager['docroot'],$filemanager['path'][$typenow]);

# generate alert if files deleted
if($deleteqty>0)
{
	$notify['type'][]='success';
	$notify['message'][]=sprintf(FM_MSGDELETE, $deleteqty);
} elseif($renameqty>0) {
	# generate alert if files renamed
	$notify['type'][]='success';
	$notify['message'][]=sprintf(FM_MSGRENAME, $renameqty);
} elseif($moveqty>0) {
	# generate alert if files renamed
	$notify['type'][]='success';
	$notify['message'][]=sprintf(FM_MSGMOVE, $moveqty);
} elseif($resizeqty>0) {
	# generate alert if images resized
	$notify['type'][]='success';
	$notify['message'][]=sprintf(FM_MSGRESIZE, $resizeqty);
} elseif($rotateqty>0) {
	# generate alert if images rotated
	$notify['type'][]='success';
	$notify['message'][]=sprintf(FM_MSGROTATE, $rotateqty);
}

# generate alert if file errors encountered
if($errorqty>0)
{
	$notify['type'][]='failure';
	$notify['message'][]=sprintf(FM_MSGEDITERR, $errorqty);
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
	$showpagestart = ($showpagenow ? (sf_esc_int($_REQUEST['showpage'])*$filemanager['pagination'])-$filemanager['pagination'] : 0);
	$showpageend = $showpagestart+$filemanager['pagination'];
	if($showpageend>$num_of_files) $showpageend = $num_of_files;
} else {
	$showpagestart = 0;
	$showpageend = $num_of_files;
}

# urls for the forms and tabs
$fmThisForm =  'fm-edit-tab.php';
$fmBrowseTab = 'fm-browse-tab.php?type='.$typenow.$passfolder;
$fmUploadTab = 'fm-upload-tab.php?type='.$typenow.$passfolder;
$fmEditTab =   'fm-edit-tab.php?type='.$typenow.$passfolder;
$fmFolderTab = 'fm-folder-tab.php?type='.$typenow.$passfolder;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Simple:Press File Manager : <?php echo FM_EDIT; ?></title>
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
<li id="browse_tab"><span><a href="<?php echo($fmBrowseTab); ?>"><?php echo FM_BROWSE; ?></a></span></li><?php
if($filemanager['allowupload'])
{
	?><li id="upload_tab"><span><a href="<?php echo($fmUploadTab); ?>"><?php echo FM_UPLOAD; ?></a></span></li><?php
}
?><li id="edit_tab" class="current"><span><a href="<?php echo($fmEditTab); ?>"><?php echo FM_EDIT; ?></a></span></li><?php
if($filemanager['allowfolders'])
{
	?><li id="folders_tab"><span><a href="<?php echo($fmFolderTab); ?>"><?php echo FM_FOLDERS; ?></a></span></li><?php
}
# Display folder select, if multiple exist
if(count($editdirs)>1)
{
	?><li id="folder_tab" class="right"><span><?php
	sffm_form_select($editdirs,'folder',FM_FOLDERCURR,urlencode($foldernow),true);
	sffm_form_hidden_input('sortby',$sortbynow);
	sffm_form_hidden_input('sorttype',$sorttypenow);
	sffm_form_hidden_input('showpage',$showpagenow);
	sffm_form_hidden_input('action',$actionnow);
	?></span></li><?php
}
?>
</ul>
</div>
</form>
<div class="panel_wrapper">
<div id="general_panel" class="panel currentmod">
<fieldset>
<legend><?php echo FM_EDITFILES; ?></legend>
<?php
sffm_form_open('edit','custom',$fmThisForm,'?type='.$typenow.$passfolder);
?>
<div class="pushleft">
<?php

# Assign edit actions based on file type and permissions
$select = array();
if($filemanager['allowdelete']) $select[] = array('delete',FM_DELETE);
if($filemanager['allowedit']) $select[] = array('rename',FM_RENAME);
if($filemanager['allowfolders']) $select[] = array('move',FM_MOVE);
if($typenow=='image' && $filemanager['allowedit'])
{
	$select[] = array('resize',FM_RESIZE);
	$select[] = array('rotate',FM_ROTATE);
}
sffm_form_select($select,'action',FM_ACTION,$actionnow,true);

# Show page select if pagination is set
if($filemanager['pagination']>0)
{
	$pagelimit = ceil($num_of_files/$filemanager['pagination'])+1;
	$page = array();
	for($i=1;$i<$pagelimit;$i++)
	{
		$page[] = array($i,FM_PAGE.' '.$i);
	}
	if($i>2) sffm_form_select($page,'showpage',SHOW,$showpagenow,true);
}
?></div><div class="pushright"><?php

sffm_form_hidden_input('sortby',$sortbynow);
sffm_form_hidden_input('sorttype',$sorttypenow);
sffm_form_text_input('find',false,$findnow,25,50);
sffm_form_submit_button('search',FM_SEARCH,'');
?></div><?php

sffm_form_open('actionform','custom',$fmThisForm,'?type='.$typenow.$passfolder);

if($actionnow=='move')
{ ?><div class="pushleft"><?php
	sffm_form_select($editdirs,'destination',FM_FOLDERDEST,urlencode($destfoldernow),false);
	?></div><?php
}

if($typenow=='image')
{
	$selectresize = array(
		array('width',FM_WIDTH),
		array('height',FM_HEIGHT)
	);
}

switch($actionnow)
{
	case 'delete':
		$actionhead = FM_DELETE;
		break;
	case 'rename':
		$actionhead = FM_RENAME;
		break;
	case 'resize':
		$actionhead = FM_RESIZE;
		break;
	case 'rotate':
		$actionhead = FM_ROTATE;
		break;
 	case 'move':
		$actionhead = FM_MOVE;
		break;
	default:
		# do nothing
}
?><div class="tabularwrapper"><table class="browse"><tr>
<th><a href="?type=<?php echo $typenow.$passaction.$passfolder.$sortbyget['name']; ?>"<?php echo $thclass['name']; ?>><?php echo FM_FILENAME; ?></a></th>
<th><a href="?type=<?php echo $typenow.$passaction.$passfolder.$sortbyget['size']; ?>"<?php echo $thclass['size']; ?>><?php echo FM_SIZE; ?></a></th>
<th><a href="?type=<?php echo $typenow.$passaction.$passfolder.$sortbyget['type']; ?>"<?php echo $thclass['type']; ?>><?php echo FM_TYPE; ?></th>
<th class="nohvr"><?php echo $actionhead; ?></th></tr>
<?php

for($i=$showpagestart;$i<$showpageend;$i++)
{
	$alt = (sffm_IsOdd($i) ? 'r1' : 'r0');
	echo '<tr class="'.$alt.'">';
	if($typenow=='image') echo '<td><a class="imghover" href="#" onclick="return false;" title="'.$file['name'][$i].'&#13;&#10;'.FM_DIMENSIONS.': '.$file['width'][$i].' x '.$file['height'][$i].'&#13;&#10;'.FM_DATE.': '.date($filemanager['dateformat'],$file['modified'][$i]).'"><img src="'.$thumbpath.'_thumbs/_'.$file['name'][$i].$imagerefresh.'" alt="" />' .sffm_truncate_text($file['name'][$i],30).'</a></td>';
	else echo '<td title="'.$file['name'][$i].'&#13;&#10;'.FM_DATE.': '.date($filemanager['dateformat'],$file['modified'][$i]).'">'.sffm_truncate_text($file['name'][$i],30).'</td>';
	echo '<td>'.sffm_bytestostring($file['size'][$i],1).'</td><td>'.$file['type'][$i].'</td>'
	.'<td>';
	sffm_form_hidden_input('actionfile['.$i.']',$file['name'][$i]);
	switch($actionnow)
	{
		case 'delete':
			echo '<input class="del" type="checkbox" name="deletefile['.$i.']" value="1" />';
			break;
		case 'rename':
			# get file extension
			$nameparts = explode('.',$file['name'][$i]);
			$ext = end($nameparts);
			sffm_form_hidden_input('renameext['.$i.']',$ext);
			sffm_form_text_input('renamefile['.$i.']',false,basename($file['name'][$i],$ext),30,120); echo $ext;
			break;
		case 'resize':
			sffm_form_text_input('resizefile['.$i.']',false,'',4,4); sffm_form_select($selectresize,'resizetype['.$i.']',false,'',false);
			break;
		case 'rotate':
			echo '<img src="img/rotate_c.gif" alt="'.FM_ROTATECW.'" /><input class="rad" type="radio" name="rotatefile['.$i.']" value="clock"><img src="img/rotate_ac.gif" alt="'.FM_ROTATECCW.'" /><input class="rad" type="radio" name="rotatefile['.$i.']" value="anticlock">'.FM_NONE.'<input class="rad" type="radio" name="rotatefile['.$i.']" value="none" checked>';
			break;
		case 'move':
			echo '<input class="del" type="checkbox" name="movefile['.$i.']" value="1" />';
			break;
		default:
			# do nothing
	}
	echo "</td></tr>\n";
}

echo "</table></div>\n".'<div class="pushright">';
if($filemanager['allowdelete'] || $filemanager['allowedit'])
{
	sffm_form_hidden_input('sortby',$sortbynow);
	sffm_form_hidden_input('sorttype',$sorttypenow);
	sffm_form_hidden_input('find',$findnow);
	sffm_form_hidden_input('showpage',$showpagenow);
	sffm_form_hidden_input('action',$actionnow);
	sffm_form_submit_button('commit',$actionhead.' '.FM_FILES,'edit');
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