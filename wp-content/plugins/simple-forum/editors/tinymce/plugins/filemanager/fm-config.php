<?php

# Simple:Press - File Manager - Static Configuration

# Based upon TinyBrowser 1.4.1 - A TinyMCE file browser (C) 2009  Bryn Jones
# (author website - http://www.lunarvis.com)

# (Flash upload contains a modified version of FlexUpload by Joseph Montanez
# http://www.gorilla3d.com/v4/index.php/blog/entry/33)

# Published under the terms of the GNU General Public License


if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

require_once (dirname(__FILE__).'/../../../../sf-config.php');
require_once(SF_BASEPATH.'/wp-load.php');

global $sfglobals, $sfvars, $current_user;

# grab sfvars from transient storage for this user
$key = $current_user->ID.'keys';
$sfvars = unserialize(get_sfsetting($key));

sf_setup_sitewide_constants();
sf_setup_forum_constants();
sf_initialise_globals($sfvars['forumid']);

$sfconfig = sf_get_option('sfconfig');
$sfuploads = sf_get_option('sfuploads');
$user_slug = sf_create_slug($current_user->user_login, '');

$image_uploads_link = $sfconfig['image-uploads'].'/';
if($sfuploads['privatefolder']) {
	$image_uploads_link.= $user_slug.'/';
	if($sfvars['profile'] == 'edit') {
		$image_uploads_link.= 'signature/';
	}
}
$image_uploads_path = str_replace('\\', '/', SF_STORE_DIR.'/'.$image_uploads_link);

$media_uploads_link = $sfconfig['media-uploads'].'/';
if($sfuploads['privatefolder']) {
	$media_uploads_link.= $user_slug.'/';
}
$media_uploads_path = str_replace('\\', '/', SF_STORE_DIR.'/'.$media_uploads_link);

$file_uploads_link = $sfconfig['file-uploads'].'/';
if($sfuploads['privatefolder']) {
    $file_uploads_link.= $user_slug.'/';
}
$file_uploads_path = str_replace('\\', '/', SF_STORE_DIR.'/'.$file_uploads_link);

if($sfuploads['showmode'] ? $showmode="detail" : $showmode="thumb");

# set script time out higher, to help with thumbnail generation
@set_time_limit(240);

$filemanager = array();

$filemanager['siteurl'] = SFHOMEURL;
$filemanager['integration'] = 'tinymce';
$filemanager['docroot'] = '';
$filemanager['unixpermissions'] = 0777;
$filemanager['imageresize']['width']  = 0;
$filemanager['imageresize']['height'] = 0;
$filemanager['thumbsrc'] = 'link';
$filemanager['thumbsize'] = $sfuploads['thumbsize'];
$filemanager['imagequality'] = 99;
$filemanager['thumbquality'] = 80;
$filemanager['order']['by']   = 'name';
$filemanager['order']['type'] = 'asc';
$filemanager['window']['width']  = 790;
$filemanager['window']['height'] = 590;
$filemanager['cleanfilename'] = true;
$filemanager['delayprocess'] = 0;
$filemanager['prohibited'] =  explode(',', str_replace(' ', '', $sfuploads['prohibited']));

switch ($sfuploads['deftab'])
{
    case 1:
        $filemanager['deftab'] = 'fm-browse-tab.php';
        break;
    case 2:
        $filemanager['deftab'] = 'fm-upload-tab.php';
        break;
    case 3:
        $filemanager['deftab'] = 'fm-edit-tab.php';
    default:
        break;
    case 4:
        $filemanager['deftab'] = 'fm-folder-tab.php';
        break;
}

# Set default language (ISO 639-1 code)
$filemanager['language'] = $sfglobals['editor']['sflang'];

# File upload paths (set to absolute by default)
$filemanager['path']['image'] = $image_uploads_path;
$filemanager['path']['media'] = $media_uploads_path;
$filemanager['path']['file']  = $file_uploads_path;

# File link paths - these are the paths that get passed back to TinyMCE or your application (set to equal the upload path by default)
$filemanager['link']['image'] = untrailingslashit(SF_STORE_URL).'/'.$image_uploads_link;
$filemanager['link']['media'] = untrailingslashit(SF_STORE_URL).'/'.$media_uploads_link;
$filemanager['link']['file']  = untrailingslashit(SF_STORE_URL).'/'.$file_uploads_link;

# File upload size limit (0 is unlimited)
if($current_user->forumadmin ? $max=0 : $max=$sfuploads['imagemaxsize']);
$filemanager['maxsize']['image'] = $max;
if($current_user->forumadmin ? $max=0 : $max=$sfuploads['mediamaxsize']);
$filemanager['maxsize']['media'] = $max;
if($current_user->forumadmin ? $max=0 : $max=$sfuploads['filemaxsize']);
$filemanager['maxsize']['file']  = $max;

# Date format, as per php date function
$filemanager['dateformat'] = get_option('date_format').' '.get_option('time_format');

# Permitted file extensions
$filemanager['filetype']['image'] = sffm_format_filelist($sfuploads['imagetypes']);
$filemanager['filetype']['media'] = sffm_format_filelist($sfuploads['mediatypes']);
$filemanager['filetype']['file']  = sffm_format_filelist($sfuploads['filetypes']);

# Default image view method
$filemanager['view']['image'] = $showmode;

# File Pagination - split results into pages (0 is none)
$filemanager['pagination'] = $sfuploads['pagecount'];

# TinyMCE dialog.css file location, relative to fm-browse-tab.php (can be set to absolute link)
$filemanager['tinymcecss'] = SFEDSTYLE . 'tinymce/'.$sfglobals["editor"]["sftmdialogCSS"];

# Assign Permissions for Upload etc. Edit and Delete (only applicable to admin so true)
$filemanager['allowupload'] = true;
$filemanager['allowedit']   = true;
$filemanager['allowdelete'] = true;
$filemanager['allowfolders'] = true;

# Set default action for edit page - Possible values: delete, rename
$filemanager['defaultaction'] = 'delete';

# SPF Specific extras
# Paths for the js and css files
$filemanager['csspath'] = SF_PLUGIN_URL .'/editors/tinymce/plugins/filemanager/css/';
$filemanager['jspath']  = SF_PLUGIN_URL .'/editors/tinymce/plugins/filemanager/js/';
$filemanager['tmpath']  = SF_PLUGIN_URL .'/editors/tinymce/';
$filemanager['homepath']= SF_PLUGIN_URL .'/editors/tinymce/plugins/filemanager/';

$filemanager['isadmin']=$current_user->forumadmin;

# Set language
if(isset($filemanager['language']) && file_exists('langs/'.$filemanager['language'].'.php'))
{
	require_once('langs/'.$filemanager['language'].'.php');
} else {
	# Falls back to default English
	require_once('langs/en.php');
}

function sffm_format_filelist($fileitems)
{
	$fileitems = str_replace (' ', '', $fileitems);
	$files=explode(',', $fileitems);
	$filelist='';
	foreach($files as $file)
	{
		$filelist.= '*.'.$file.',';
	}
	$filelist=trim($filelist, ',');
	return $filelist;
}

?>