<?php

$root = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));

if (file_exists($root.'/wp-load.php')) {
	// WP 2.6
	require_once($root.'/wp-load.php');
} else {
	// Before 2.6
	if (!file_exists($root.'/wp-config.php'))  {
		echo "Could not find wp-config.php";	
		die;	
	}// stop when wp-config is not there
	require_once($root.'/wp-config.php');
}

require_once(ABSPATH.'/wp-admin/admin.php');

// check for rights
if(!current_user_can('edit_posts')) die;

global $wpdb;

?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php _e('Projectmanager', 'projectmanager') ?></title>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
	<?php wp_register_script( 'projectmanager', PROJECTMANAGER_URL.'/admin/js/functions.js', array( 'colorpicker', 'sack' ), PROJECTMANAGER_VERSION ); wp_register_script ('projectmanager_ajax', PROJECTMANAGER_URL.'/admin/js/ajax.js', array( 'projectmanager' ), PROJECTMANAGER_VERSION ); wp_print_scripts( 'projectmanager_ajax'); ?>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo PROJECTMANAGER_URL ?>/admin/tinymce/tinymce.js"></script>
	<script type="text/javascript">
	//<![CDATA[
	ProjectManagerAjaxL10n = {
		blogUrl: "<?php bloginfo( 'wpurl' ); ?>", pluginPath: "<?php echo PROJECTMANAGER_PATH; ?>", pluginUrl: "<?php echo PROJECTMANAGER_URL; ?>", requestUrl: "<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php", imgUrl: "<?php echo PROJECTMANAGER_URL; ?>/images", Edit: "<?php _e("Edit"); ?>", Post: "<?php _e("Post"); ?>", Save: "<?php _e("Save"); ?>", Cancel: "<?php _e("Cancel"); ?>", pleaseWait: "<?php _e("Please wait..."); ?>", Revisions: "<?php _e("Page Revisions"); ?>", Time: "<?php _e("Insert time"); ?>", Options: "<?php _e("Options", "projectmanager") ?>", Delete: "<?php _e('Delete', 'projectmanager') ?>"
	}
	//]]>
	</script>
	<base target="_self" />
	
</head>
<body id="link" onload="tinyMCEPopup.executeOnLoad('init();');document.body.style.display='';" style="display: none">
<!-- <form onsubmit="insertLink();return false;" action="#"> -->
	<form name="ProjectManagerTinyMCE" action="#">
	<div class="tabs">
		<ul>
			<li id="project_tab" class="current"><span><a href="javascript:mcTabs.displayTab('project_tab', 'project_panel');" onmouseover="return false;"><?php _e( 'Project', 'projectmanager' ); ?></a></span></li>
			<!--<li id="gallery_tab"><span><a href="javascript:mcTabs.displayTab('gallery_tab', 'gallery_panel');" onmouseover="return false;"><?php _e( 'Gallery', 'projectmanager' ); ?></a></span></li>-->
			<li id="dataset_tab"><span><a href="javascript:mcTabs.displayTab('dataset_tab', 'dataset_panel');" onmouseover="return false;"><?php _e( 'Dataset', 'projectmanager' ); ?></a></span></li>
			<li id="search_tab"><span><a href="javascript:mcTabs.displayTab('search_tab', 'search_panel');" onmouseover="return false;"><?php _e('Search Form','projectmanager') ?></a></span></li>
			<li id="datasetform_tab"><span><a href="javascript:mcTabs.displayTab('datasetform_tab', 'datasetform_panel');" onmouseover="return false;"><?php _e('Dataset Form','projectmanager') ?></a></span></li>
		</ul>
	</div>
	<div class="panel_wrapper">
		
	<!-- project panel -->
	<div id="project_panel" class="panel current">
	<table style="border: 0;">
	<tr>
		<td><label for="projects"><?php _e("Project", 'projectmanager'); ?></label></td>
		<td>
		<select id="projects" name="projects" style="width: 200px">
        	<option value="0"><?php _e("No Project", 'projectmanager'); ?></option>
		<?php
			$projects = $wpdb->get_results("SELECT * FROM {$wpdb->projectmanager_projects} ORDER BY id ASC");
			if( ($projects) ) {
				foreach( $projects as $project )
					echo '<option value="'.$project->id.'" >'.$project->title.'</option>'."\n";
			}
		?>
        	</select>
		</td>
	</tr>
	<tr>
		<td nowrap="nowrap" valign="top"><label for="template"><?php _e( 'Template', 'projectmanager' ) ?></label></td>
		<td>
		<input type="radio" name="project_template" id="project_template_table" value="table" checked="ckecked" /><label for="project_template_table"><?php _e( 'Table', 'projectmanager' ) ?></label><br />
		<input type="radio" name="project_template" id="project_template_gallery" value="gallery" /><label for="project_template_gallery"><?php _e( 'Gallery', 'projectmanager' ) ?></label><br />
		</td>
	</tr>
	<tr>
		<td nowrap="nowrap" valign="top"><label for="cat_id"><?php _e( 'Category', 'projectmanager' ) ?></label></td>
		<td><?php wp_dropdown_categories(array( 'hide_empty' => 0, 'name' => 'cat_id', 'orderby' => 'name', 'hierarchical' => true, 'show_option_all' => __('Display all Datasets', 'projectmanager'))); ?></td>
	</tr>
	<tr>
		<td nowrap="nowrap" valign="top"><label for="orderby"><?php _e( 'Order By', 'projectmanager' ) ?></label></td>
		<td>
			<select size="1" name="orderby" id="orderby">
				<option value=""><?php _e( 'Default', 'projectmanager') ?></option>
				<option value="name"><?php _e('Name', 'projectmanager') ?></option>
				<option value="id"><?php _e('ID', 'projectmanager') ?></option>
				<option value="formfields"><?php _e( 'Formfields', 'projectmanager') ?></option>
			</select>
			<input type="text" size="3" name="formfield_id" id="formfield_id" />
		</td>
	</tr>
	<tr>
		<td nowrap="nowrap" valign="top"><label for="order"><?php _e( 'Order', 'projectmanager' ) ?></label></td>
		<td>
			<select size="1" name="order" id="order">
				<option value=""><?php _e( 'Default', 'projectmanager') ?></option>
				<option value="asc"><?php _e('Ascending', 'projectmanager') ?></option>
				<option value="desc"><?php _e('Descending', 'projectmanager') ?></option>
			</select>
	</table>
	</div>
	
	<!-- dataset panel -->
	<div id="dataset_panel" class="panel">
	<table style="border: 0;" cellpadding="5">
	<tr>
		<td><label for="datasets"><?php _e("Dataset", 'projectmanager'); ?></label></td>
		<td>
		<select id="datasets" name="datasets" style="width: 200px">
		<option value="0"><?php _e("No Dataset", 'projectmanager'); ?></option>
		<?php
			$datasets = $wpdb->get_results("SELECT * FROM {$wpdb->projectmanager_dataset} ORDER BY id ASC");
			if( ($datasets) ) {
				foreach( $datasets as $dataset )
					echo '<option value="'.$dataset->id.'" >'.$dataset->name.'</option>'."\n";
			}
		?>
        	</select>
		</td>
	</tr>
	</table>
	</div>
	
	<!-- search panel -->
	<div id="search_panel" class="panel">
	<table style="border: 0;">
	<tr>
		<td><label for="search_projects"><?php _e("Project", 'projectmanager'); ?></label></td>
		<td>
		<select id="search_projects" name="search_projects" style="width: 200px">
		<option value="0"><?php _e("No Project", 'projectmanager'); ?></option>
		<?php
			$projects = $wpdb->get_results("SELECT * FROM {$wpdb->projectmanager_projects} ORDER BY id ASC");
			if( ($projects) ) {
				foreach( $projects as $project )
					echo '<option value="'.$project->id.'" >'.$project->title.'</option>'."\n";
			}
		?>
        	</select>
		</td>
	</tr>
	<tr>
		<td nowrap="nowrap" valign="top"><label><?php _e( 'Display', 'projectmanager' ) ?></label></td>
		<td>
			<input type="radio" name="search_display" id="search_display_extend" value="extend" checked="ckecked" /><label for="search_display_extended"><?php _e( 'Extended Version', 'projectmanager' ) ?></label><br />
			<input type="radio" name="search_display" id="search_display_compact" value="compact" /><label for="search-display_compact"><?php _e( 'Compact Version', 'projectmanager' ) ?></label><br />
		</td>
	</tr>
	</table>
	</div>
	
	<!-- datast form panel -->
	<div id="datasetform_panel" class="panel">
	<table style="border: 0;">
	<tr>
		<td><label for="datasetform_projects"><?php _e("Project", 'projectmanager'); ?></label></td>
		<td>
		<select id="datasetform_projects" name="datasetform_projects" style="width: 200px">
		<option value="0"><?php _e("No Project", 'projectmanager'); ?></option>
		<?php
			$projects = $wpdb->get_results("SELECT * FROM {$wpdb->projectmanager_projects} ORDER BY id ASC");
			if( ($projects) ) {
				foreach( $projects as $project )
					echo '<option value="'.$project->id.'" >'.$project->title.'</option>'."\n";
			}
		?>
        	</select>
		</td>
	</tr>
	</table>
	</div>

	</div>
	
	<div class="mceActionPanel">
		<div style="float: left">
			<input type="button" id="cancel" name="cancel" value="<?php _e("Cancel", 'projectmanager'); ?>" onclick="tinyMCEPopup.close();" />
		</div>

		<div style="float: right">
			<input type="submit" id="insert" name="insert" value="<?php _e("Insert", 'projectmanager'); ?>" onclick="ProjectManagerInsertLink();" />
		</div>
	</div>

</form>
</body>
</html>
