<?php
if ( !current_user_can( 'edit_datasets' ) && !current_user_can( 'projectmanager_user') ) : 
	echo '<p style="text-align: center;">'.__("You do not have sufficient permissions to access this page.").'</p>';
else :
$project_id = $projectmanager->getProjectID();
$project = $projectmanager->getCurrentProject();

$options = get_option('projectmanager');
if ( isset($_GET['edit']) ) {
	$edit = true;
	$form_title = __('Edit Dataset','projectmanager');
	$dataset_id = $_GET['edit'];
	$dataset = $projectmanager->getDataset( $dataset_id );
	
	$cat_ids = $projectmanager->getSelectedCategoryIDs($dataset);
	$dataset_meta = $projectmanager->getDatasetMeta( $dataset_id );
	
	$name = htmlspecialchars(stripslashes_deep($dataset->name), ENT_QUOTES);
	
	$img_filename = $dataset->image;
	$meta_data = array();
	foreach ( $dataset_meta AS $meta ) {
		if ( is_string($meta_data[$meta->form_field_id] ) )
			$meta_data[$meta->form_field_id] = htmlspecialchars(stripslashes_deep($meta->value), ENT_QUOTES);
		else
			$meta_data[$meta->form_field_id] = stripslashes_deep($meta->value);
	}
}  else {
	$edit = false;
	$form_title = __('Add Dataset','projectmanager');
	$dataset_id = ''; $cat_ids = array(); $img_filename = ''; $name = ''; $meta_data = array();
}
$is_profile_page = false;
$page = ($_GET['page'] == 'projectmanager') ? 'projectmanager&subpage=show-project&project_id='.$project_id : 'project_'.$project_id;

// Try to create image directory
if ( 1 == $project->show_image && !wp_mkdir_p( $projectmanager->getFilePath() ) )
	echo "<div class='error'><p>".sprintf( __( 'Unable to create directory %s. Is its parent directory writable by the server?' ), $projectmanager->getFilePath() )."</p></div>";
?>

<?php if ( current_user_can('edit_other_datasets') && !$edit ) : ?>
<div id="wp_users" style="display: none; overflow: auto;"><div class="thickbox_content">
	<form>
		<div class="aligncenter;"><?php wp_dropdown_users( array('name' => 'wp_user_id') ) ?></div>

		<div class="buttonbar"><input type="button" value="<?php _e('Insert') ?>" class="button-secondary" onclick="ProjectManager.addWPUser();return false;" />&#160;<input type="button" value="<?php _e('Cancel') ?>" class="button" onclick="tb_remove();" /></div>
	</form>
</div></div>
<?php endif; ?>

<?php $projectmanager->loadTinyMCE() ?>

<form name="post" id="post" action="admin.php?page=<?php echo $page ?>" method="post" enctype="multipart/form-data">
	
<?php wp_nonce_field( 'projectmanager_edit-dataset' ) ?>
	
<div class="wrap">
	<?php $this->printBreadcrumb( $form_title ) ?>

	<h2><?php echo $form_title ?></h2>
	
	<?php include( 'dataset-form.php' ) ?>
	
	<input type="hidden" name="project_id" value="<?php echo $project_id ?>" />
	<input type="hidden" name="dataset_id" value="<?php echo $dataset_id ?>" />
	<input type="hidden" name="user_id" id="user_id"  value="<?php echo $dataset->user_id ?>" />
	<input type="hidden" name="updateProjectManager" value="dataset" />
			
	<p class="submit"><input type="submit" name="addportrait" value="<?php echo $form_title ?> &raquo;" class="button" /></p>
</div>
</form>

<?php endif; ?>
