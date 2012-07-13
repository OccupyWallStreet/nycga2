<?php
/*
Simple:Press
Admin Config Update Support Functions
$LastChangedDate: 2010-03-26 16:38:27 -0700 (Fri, 26 Mar 2010) $
$Rev: 3818 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

function sfa_save_config_data()
{
	check_admin_referer('forum-adminform_config', 'forum-adminform_config');

	$mess = __('Configuration Updated', "sforum").$mess;

	$sfconfig = array();
	$sfconfig = sf_get_option('sfconfig');

	if (!empty($_POST['styles'])) {
		$sfconfig['styles'] = trim(sf_filter_title_save(trim($_POST['styles'])), '/');
	}
	if (!empty($_POST['avatars'])) {
		$sfconfig['avatars'] = trim(sf_filter_title_save(trim($_POST['avatars'])), '/');
	}
	if (!empty($_POST['avatar-pool'])) {
		$sfconfig['avatar-pool'] = trim(sf_filter_title_save(trim($_POST['avatar-pool'])), '/');
	}
	if (!empty($_POST['smileys'])) {
		$sfconfig['smileys'] = trim(sf_filter_title_save(trim($_POST['smileys'])), '/');
	}
	if (!empty($_POST['ranks'])) {
		$sfconfig['ranks'] = trim(sf_filter_title_save(trim($_POST['ranks'])), '/');
	}
	if (!empty($_POST['image-uploads'])) {
		$sfconfig['image-uploads'] = trim(sf_filter_title_save(trim($_POST['image-uploads'])), '/');
	}
	if (!empty($_POST['media-uploads'])) {
		$sfconfig['media-uploads'] = trim(sf_filter_title_save(trim($_POST['media-uploads'])), '/');
	}
	if (!empty($_POST['file-uploads'])) {
		$sfconfig['file-uploads'] = trim(sf_filter_title_save(trim($_POST['file-uploads'])), '/');
	}
	if (!empty($_POST['hooks'])) {
		$sfconfig['hooks'] = trim(sf_filter_title_save(trim($_POST['hooks'])), '/');
	}
	if (!empty($_POST['pluggable'])) {
		$sfconfig['pluggable'] = trim(sf_filter_title_save(trim($_POST['pluggable'])), '/');
	}
	if (!empty($_POST['filters'])) {
		$sfconfig['filters'] = trim(sf_filter_title_save(trim($_POST['filters'])), '/');
	}
	if (!empty($_POST['help'])) {
		$sfconfig['help'] = trim(sf_filter_title_save(trim($_POST['help'])), '/');
	}
	if (!empty($_POST['custom-icons'])) {
		$sfconfig['custom-icons'] = trim(sf_filter_title_save(trim($_POST['custom-icons'])), '/');
	}
	if (!empty($_POST['policies'])) {
		$sfconfig['policies'] = trim(sf_filter_title_save(trim($_POST['policies'])), '/');
	}

	sf_update_option('sfconfig', $sfconfig);

	return $mess;
}

function sfa_save_config_options()
{
    check_admin_referer('forum-adminform_sfsupport', 'forum-adminform_sfsupport');

	$sfsupport = array();
	$sfsupport['sfusinglinking']		= $_POST['sfusinglinking'];
	$sfsupport['sfusinglinkcomments']	= $_POST['sfusinglinkcomments'];
	$sfsupport['sfusingwidgets']		= $_POST['sfusingwidgets'];
	$sfsupport['sfusinggeneraltags']	= $_POST['sfusinggeneraltags'];
	$sfsupport['sfusingavatartags']		= $_POST['sfusingavatartags'];
	$sfsupport['sfusingliststags']		= $_POST['sfusingliststags'];
	$sfsupport['sfusinglinkstags']		= $_POST['sfusinglinkstags'];
	$sfsupport['sfusingpmtags']			= $_POST['sfusingpmtags'];
	$sfsupport['sfusingtagstags']		= $_POST['sfusingtagstags'];
	$sfsupport['sfusingstatstags']		= $_POST['sfusingstatstags'];
	$sfsupport['sfusingpagestags']		= $_POST['sfusingpagestags'];
	sf_update_option('sfsupport', $sfsupport);

	$mess = __("Options Updated", "sforum");

	return $mess;
}

?>