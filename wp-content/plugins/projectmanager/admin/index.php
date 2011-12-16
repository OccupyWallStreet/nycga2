<?php
if ( !current_user_can( 'view_projects' ) ) : 
     echo '<div class="error"><p style="text-align: center;">'.__("You do not have sufficient permissions to access this page.").'</p></div>';
else :

if ( isset($_POST['updateProjectManager']) AND !isset($_POST['deleteit']) ) {
	if ( 'project' == $_POST['updateProjectManager'] ) {
		check_admin_referer('projectmanager_manage-projects');
		$this->addProject( $_POST['project_title'] );
	}
	$this->printMessage();
}  elseif ( isset($_POST['doaction']) && isset($_POST['action']) ) {
	check_admin_referer('projectmanager_projects-bulk');
	if ( current_user_can('delete_projects') ) {
		if ( 'delete' == $_POST['action'] ) {
			foreach ( $_POST['project'] AS $project_id ) {
				$this->delProject( $project_id );
			}
		}
	} else {
		$this->setMessage(__("You don't have permission to perform this task", 'projectmanager'), true);
		$this->printMessage();
	}
}
?>
<div class="wrap">
	<h2><?php _e( 'Projectmanager', 'projectmanager' ) ?></h2>
	
	<div id="col-container">
	<div id="col-right">
	<div class="col-wrap">
		<form id="projects-filter" method="post" action="">
		<?php wp_nonce_field( 'projectmanager_projects-bulk' ) ?>
		
		<div class="tablenav">
			<div class="alignleft actions">
				<!-- Bulk Actions -->
				<select name="action" size="1">
					<option value="-1" selected="selected"><?php _e('Bulk Actions') ?></option>
					<option value="delete"><?php _e('Delete')?></option>
				</select>
				<input type="submit" value="<?php _e('Apply'); ?>" name="doaction" id="doaction" class="button-secondary action" />
			</div>
		</div>
		
		<table class="widefat" summary="<?php _e( 'List of Projects', 'projectmanager' ) ?>" title="<?php _e( 'Projectmanager', 'projectmanager' ) ?>">
		<thead>
			<tr>
				<th scope="col" class="check-column"><input type="checkbox" onclick="ProjectManager.checkAll(document.getElementById('projects-filter'));" /></th>
				<th scope="col" class="num"><?php _e('ID', 'projectmanager') ?></th>
				<th scope="col"><?php _e( 'Project', 'projectmanager' ) ?></th>
				<th scope="col" class="num"><?php _e( 'Datasets', 'projectmanager' ) ?></th>
				<th scope="col"><?php _e( 'Action', 'projectmanager' ) ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th scope="col" class="check-column"><input type="checkbox" onclick="ProjectManager.checkAll(document.getElementById('projects-filter'));" /></th>
				<th scope="col" class="num"><?php _e('ID', 'projectmanager') ?></th>
				<th scope="col"><?php _e( 'Project', 'projectmanager' ) ?></th>
				<th scope="col" class="num"><?php _e( 'Datasets', 'projectmanager' ) ?></th>
				<th scope="col"><?php _e( 'Action', 'projectmanager' ) ?></th>
			</tr>
		</tfoot>
		<tbody id="the-list">
		<?php if ( $projects = $projectmanager->getProjects() ) : ?>
		
		<?php foreach ( $projects AS $project ) : $class = ( !isset($class) || 'alternate' == $class ) ? '' : 'alternate'; ?>
			<tr class="<?php echo $class ?>">
				<th scope="row" class="check-column"><input type="checkbox" value="<?php echo $project->id ?>" name="project[<?php echo $project->id ?>]" /></th>
				<td class="num"><?php echo $project->id ?></td>
				<td><a href="admin.php?page=projectmanager&amp;subpage=show-project&amp;project_id=<?php echo $project->id ?>"><?php echo $project->title ?></a></td>
				<td class="num"><?php echo $projectmanager->getNumDatasets( $project->id ) ?></td>
				<td><a href="admin.php?page=projectmanager&amp;subpage=settings&amp;project_id=<?php echo $project->id ?>"><?php _e( 'Settings', 'projectmanager' ) ?></a> - <a href="admin.php?page=projectmanager&amp;subpage=dataset&amp;project_id=<?php echo $project->id ?>"><?php _e( 'Add Dataset', 'projectmanager' ) ?></a></td>
			</tr>
		<?php endforeach; ?>
	
		<?php endif; ?>
		</tbody>
		</table>

		</form>
	</div>
	</div><!-- /col-right -->
	
	<div id="col-left">
	<div class="col-wrap">
		<!-- Add New Project -->
		<div class="form-wrap">
		<form action="" method="post" style="margin-top: 3em;">
			<input type="hidden" name="project_id" value="" />
			<input type="hidden" name="updateProjectManager" value="project" />
			<?php wp_nonce_field( 'projectmanager_manage-projects' ) ?>
			
			<h3><?php _e( 'Add Project', 'projectmanager' ) ?></h3>
			<div class="form-field form-required">
				<label for="project_title"><?php _e( 'Title', 'projectmanager' ) ?></label>
				<input type="text" name="project_title" id="project_title" value="" size="30" style="margin-bottom: 1em;" />
			</div>
			
			<p class="submit"><input type="submit" value="<?php _e( 'Add Project', 'projectmanager' ) ?> &raquo;" class="button" /></p>
		</form>
		</div>
	</div>
	</div><!-- / col-left -->
</div>
</div>

<?php endif; ?>
