<?php
/*
Simple:Press
tinymce init
$LastChangedDate: 2011-06-18 12:57:35 -0700 (Sat, 18 Jun 2011) $
$Rev: 6343 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}
	if($sfglobals['editor']['sfrtl'] ? $SFDIR='rtl' : $SFDIR='ltr');

	# prepare uploading settings...
	$sfconfig = sf_get_option('sfconfig');
	$canimage = '0';
	$canmedia = '0';
	$canfile  = '0';
	$cansignature = '0';

	if($sfvars['profile'] == 'edit' ? $issignature = '1' : $issignature = '0');

	$up_path = SF_STORE_DIR.'/'.$sfconfig['image-uploads'];
	if(file_exists($up_path) && is_writable($up_path) && $current_user->sfuploadimg == true) {
		$canimage = '1';
	}

	$up_path = SF_STORE_DIR.'/'.$sfconfig['media-uploads'];
	if(file_exists($up_path) && is_writable($up_path) && $current_user->sfuploadmedia == true) {
		$canmedia = '1';
	}

	$up_path = SF_STORE_DIR.'/'.$sfconfig['file-uploads'];
	if(file_exists($up_path) && is_writable($up_path) && $current_user->sfuploadfile == true) {
		$canfile = '1';
	}

	$up_path = SF_STORE_DIR.'/'.$sfconfig['image-uploads'];
	if(file_exists($up_path) && is_writable($up_path) && $current_user->sfuploadsig == true) {
		$cansignature = '1';
	}

	echo '<script type="text/javascript" src="'.SFEDITORURL.'tinymce/tiny_mce.js"></script>'. "\n";

	if($canimage || $canmedia || $canfile || ($cansignature && $issignature))
	{
		echo '<script type="text/javascript" src="'.SFEDITORURL.'tinymce/plugins/filemanager/fm-tinymce.js.php"></script>'. "\n";
	}
?>
	<script type="text/javascript">
	tinyMCE.init({
		canimage: <?php echo($canimage); ?>,
		canmedia: <?php echo($canmedia); ?>,
		canfile: <?php echo($canfile); ?>,
		cansignature: <?php echo($cansignature); ?>,
		issignature: <?php echo($issignature); ?>,
		mode : "exact",
		elements : "postitem, signature",
		theme : "advanced",
		theme_advanced_layout_manager : "SimpleLayout",
		skin : "o2k7",
		content_css : "<?php echo(SFEDSTYLE . 'tinymce/'.$sfglobals["editor"]["sftmcontentCSS"]); ?>",
		popup_css : "<?php echo(SFEDSTYLE . 'tinymce/'.$sfglobals["editor"]["sftmdialogCSS"]); ?>",
		editor_css : "<?php echo(SFEDSTYLE . 'tinymce/'.$sfglobals["editor"]["sftmuiCSS"]); ?>",
		language : "<?php echo($sfglobals['editor']['sflang']); ?>",
		directionality : "<?php echo($SFDIR); ?>",
		auto_reset_designmode : true,
		width : "100%",
		height: <?php if($sfvars['pageview']=='profileedit' ? $h="200" : $h="360"); echo '"'.$h.'"'; ?>,
		<?php if($canimage || $canmedia || $canfile || ($cansignature && $issignature))
		{ ?>
		file_browser_callback : "sf_filemanager",
		<?php } ?>
		relative_urls : false,
		<?php if(!$sfglobals['editor']['sfrelative'])
		{ ?>
		convert_urls : false,
		<?php } ?>
		extended_valid_elements: "code",
		apply_source_formatting : true,
		paste_text_use_dialog : true,
		paste_convert_middot_lists : true,
		paste_remove_spans : true,
		paste_remove_styles : true,
		paste_convert_headers_to_strong : true,
		paste_strip_class_attributes : "mso",
		remove_redundant_brs : true,
		force_p_newlines : true,
		force_br_newlines : false,
		remove_linebreaks: false,
		convert_newlines_to_brs : false,
		remove_redundant_brs : true,
		entities:"38,amp,60,lt,62,gt",
		plugins : "<?php echo(sf_build_tb_plugins()); ?>",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location: "bottom",
		theme_advanced_resizing : true,
		theme_advanced_resizing_use_cookie : false,
		theme_advanced_buttons1 : "<?php echo(sf_build_tb_buttons()); ?>",
		theme_advanced_buttons2 : "<?php echo(sf_get_option('sftbextras')); ?>",
		theme_advanced_buttons3 : "",
		theme_advanced_buttons1_add : "<?php echo(sf_build_tb_buttons_add()); ?>",
		plugin_preview_width : "750",
		plugin_preview_height : "400",
		gecko_spellcheck: true,
		brushes: "<?php echo(sf_build_tb_code_brushes()); ?>"
	});
	</script>
<?php


# Toolbar Support Routines

function sf_build_tb_plugins()
{
	global $sfglobals;

	$tb = implode(",", $sfglobals['toolbar']['tbar_plugins']);
	if($sfglobals['smileyoptions']['sfsmallow'] && $sfglobals['smileyoptions']['sfsmtype']!=2)
	{
		$tb = str_replace('emotions,', '', $tb);
		$tb = str_replace('emotions', '', $tb);
	}
	return $tb;
}

function sf_build_tb_buttons()
{
	global $sfglobals;

	return implode(",", $sfglobals['toolbar']['tbar_buttons']);
}

function sf_build_tb_buttons_add()
{
	global $sfglobals, $current_user;

	$tb = implode(",", $sfglobals['toolbar']['tbar_buttons_add']);

    # using smileys?
	if ($sfglobals['smileyoptions']['sfsmallow'] && $sfglobals['smileyoptions']['sfsmtype']!=2)
	{
		$tb = str_replace('emotions,', '', $tb);
		$tb = str_replace('emotions', '', $tb);
	}

    # spoilers allowed?
	if (!$current_user->sfspoilers)
	{
		$tb = str_replace('spoiler,', '', $tb);
		$tb = str_replace('spoiler', '', $tb);
	}

    # syntax highlighting?
    $sfsyntax = sf_get_option('sfsyntax');
    if($sfsyntax['sfsyntaxforum'] == false)
	{
		$tb = str_replace('ddcode,', '', $tb);
		$tb = str_replace('ddcode', '', $tb);
	}

	return $tb;
}

function sf_build_tb_code_brushes()
{
	$brushes = '';
    $sfsyntax = sf_get_option('sfsyntax');
    if($sfsyntax['sfsyntaxforum'] == true)
	{
		$brushes = $sfsyntax['sfbrushes'];
	}

	return $brushes;
}

?>