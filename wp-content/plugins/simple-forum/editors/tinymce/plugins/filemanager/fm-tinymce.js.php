<?php
/*
Simple:Press
File Manager - Startup FM Window
$LastChangedDate: 2009-11-24 19:34:55 +0000 (Tue, 24 Nov 2009) $
$Rev: 3020 $
*/

require_once(dirname(__FILE__).'/fm-config.php');  # cant us SF constant until after config file read

$tbpath = pathinfo($_SERVER['SCRIPT_NAME']);
$fmTab = $tbpath['dirname']."/".$filemanager['deftab'];
$filemanager['obfuscate'] = wp_generate_password(20);

?>
function sf_filemanager(field_name, url, type, win)
{
	var fmURL = "<?php echo $fmTab; ?>" + "?type=" + type;

	tinyMCE.activeEditor.windowManager.open({
		file : fmURL,
		title : 'Simple:Press File Manager',
        width : 790,
        height : 550,
		resizable : "yes",
		scrollbars : "yes",
		inline : "yes",
		close_previous : "no"
	}, {
		window : win,
		input : field_name
	});
	return false;
}