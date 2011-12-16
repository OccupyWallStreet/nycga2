<?php 
$is_profile_page = false;
if ( 1 == $project->show_image && !wp_mkdir_p( $projectmanager->getFilePath() ) ) : ?>
  <div class="error"><p><?php printf( __( 'Unable to create directory %s. Is its parten directory writable by the server?' ), $projectmanager->getFilePath() ) ?></p></div>
<?php endif; ?>

<?php $page = 'wp-admin/admin.php?page=projectmanager&subpage=show-project&project_id='.$project->id; ?>

<form name="datasetform" id="datasetform"  action="<?php echo $page ?>" method="post" enctype="multipart/form-data">
<?php wp_nonce_field('projectmanager_edit-dataset'); ?>


	<label for="name"><?php _e( 'Name', 'projectmanager' ) ?></label><input type="text" name="name" id="name" value="<?php echo $name ?>" size="30" /><br />
	<?php if ( 1 == $project->show_image ) : ?>
	<label for="projectmanager_image"><?php _e( 'Image', 'projectmanager' ) ?></label>
	<?php if ( '' != $img_filename ) : ?>
	<div class="alignright">
		<img src="<?php echo $projectmanager->getFileURL('tiny.'.$img_filename)?>" />
		<p style="text-align: center;"><input type="checkbox" id="del_old_image" name="del_old_image" value="1" style="margin-left: 1em;" />&#160;<label for="del_old_image"><?php _e( 'Delete', 'projectmanager' ) ?></label></p>
	</div>
	<?php endif; ?>
	<input type="file" name="projectmanager_image" id="projectmanager_image" size="25" />
	<?php if ( '' != $img_filename ) : ?>
		<p class="alignleft"><label for="overwrite_image"><?php _e( 'Overwrite existing image', 'projectmanager' ) ?></label><input type="checkbox" id="overwrite_image" name="overwrite_image" value="1" style="margin-left: 1em;" /></p>
		<input type="hidden" name="image_file" value="<?php echo $img_filename ?>" />
	<?php endif; ?>
	<?php endif; ?><br />
	<?php if ( $form_fields = $projectmanager->getFormFields() ) : ?>
		<?php foreach ( $form_fields AS $form_field ) : ?>
		
			<label for="form_field_<?php echo $form_field->id ?>"><?php echo $form_field->label ?></label>
			
			<?php if ( 'text' == $form_field->type || 'email' == $form_field->type || 'uri' == $form_field->type || 'numeric' == $form_field->type || 'currency' == $form_field->type ) : ?>
				<input type="text" name="form_field[<?php echo $form_field->id ?>]" id="form_field_<?php echo $form_field->id ?>" value="<?php echo $meta_data[$form_field->id] ?>" size="30" />
				<?php elseif ( 'textfield' == $form_field->type || 'tinymce' == $form_field->type ) : ?>
				<div style="width: 80%;">
					<textarea <?php if ( 'tinymce' == $form_field->type ) echo 'class="theEditor"' ?> name="form_field[<?php echo $form_field->id ?>]" id="form_field_<?php echo $form_field->id ?>" cols="70" rows="15"><?php echo $meta_data[$form_field->id] ?></textarea>
				</div>
				<?php elseif ( 'date' == $form_field->type ) : ?>
				<select size="1" name="form_field[<?php echo $form_field->id ?>][day]">
					<option value=""><?php _e( 'Day', 'projectmanager' ) ?></option>
					<option value="">&#160;</option>
					<?php for ( $day = 1; $day <= 31; $day++ ) : ?>
						<option value="<?php echo str_pad($day, 2, 0, STR_PAD_LEFT) ?>"<?php selected ( $day, intval(substr($meta_data[$form_field->id], 8, 2)) ); ?>><?php echo $day ?></option>
					<?php endfor; ?>
				</select>
				<select size="1" name="form_field[<?php echo $form_field->id ?>][month]">
					<option value=""><?php _e( 'Month', 'projectmanager' ) ?></option>
					<option value="">&#160;</option>
					<?php foreach ( $projectmanager->getMonths() AS $key => $month ) : ?>
						<option value="<?php echo str_pad($key, 2, 0, STR_PAD_LEFT) ?>"<?php selected ( $key, intval(substr($meta_data[$form_field->id], 5, 2)) ); ?>><?php echo $month ?></option>
					<?php endforeach; ?>
				</select>
				<select size="1" name="form_field[<?php echo $form_field->id ?>][year]">
					<option value="0000"><?php _e('Year', 'projectmanager') ?></option>
					<option value="0000">&#160;</option>
					<?php for ( $year = 1970; $year <= date('Y')+10; $year++ ) : ?>
						<option value="<?php echo $year ?>"<?php selected ( $year, substr($meta_data[$form_field->id], 0, 4) ); ?>><?php echo $year ?></option>
					<?php endfor; ?>
				</select>
				<?php elseif ( 'time' == $form_field->type ) : ?>
				<select size="1" name="form_field[<?php echo  $form_field->id ?>][hour]">
					<?php for ( $hour = 0; $hour <= 23; $hour++ ) : ?>
					<option value="<?php echo str_pad($hour, 2, 0, STR_PAD_LEFT) ?>"<?php selected( $hour, intval(substr($meta_data[$form_field->id], 0, 2)) ) ?>><?php echo str_pad($hour, 2, 0, STR_PAD_LEFT) ?></option>
					<?php endfor; ?>
				</select>
				<select size="1" name="form_field[<?php echo $form_field->id ?>][minute]">
					<?php for ( $minute = 0; $minute <= 59; $minute++ ) : ?>
					<option value="<?php  echo str_pad($minute, 2, 0, STR_PAD_LEFT) ?>"<?php selected( $minute, intval(substr($meta_data[$form_field->id], 3, 2)) ) ?>><?php echo str_pad($minute, 2, 0, STR_PAD_LEFT) ?></option>
					<?php endfor; ?>
				</select>
				<?php elseif ( 'file' == $form_field->type || 'image' == $form_field->type || 'video' == $form_field->type ) : ?>
					<input type="file" name="form_field[<?php echo $form_field->id ?>]" id="form_field_<?php echo $form_field->id ?>" size="40" />
					<input type="hidden" name="form_field[<?php echo $form_field->id ?>][current]" value="<?php echo $meta_data[$form_field->id] ?>" />
					<?php if (!empty($meta_data[$form_field->id])) : ?>
					<p>
						<?php if ( 'file' == $form_field->type ) : ?>
							<?php _e( 'Current File', 'projectmanager' ) ?>: <a href="<?php echo $projectmanager->getFileURL($meta_data[$form_field->id]) ?>"><?php echo $meta_data[$form_field->id] ?></a>&#160;
						<?php elseif( 'image' == $form_field->type ) : ?>
							<img src="<?php echo $projectmanager->getFileURL('tiny.'.$meta_data[$form_field->id])?>" class="alignright" style="margin-top: -1em;" />
						<?php elseif ( 'video' == $form_field->type ) : ?>
							<embed src="<?php $projectmanager->getFileURL($meta_data[$form_field->id]) ?>" width="150" class="alignright" style="margin-top: -1em;" />
						<?php endif; ?>
						<input type="checkbox" name="form_field[<?php echo $form_field->id ?>][del]" value="1" id="delete_file_<?php echo $form_field->id ?>">&#160;<label for="delete_file_<?php echo $form_field->id ?>"><strong><?php _e( 'Delete File', 'projectmanager' ) ?></strong></label>&#160;
						<input type="checkbox" name="form_field[<?php echo $form_field->id ?>][overwrite]" value="1" id="overwrite_file_<?php echo $form_field->id ?>">&#160;<label for="overwrite_file_<?php echo $form_field->id ?>"><strong><?php _e( 'Overwrite File', 'projectmanager' ) ?></strong></label>
					</p>
					<?php endif; ?>
				<?php elseif ( 'wp_user' == $form_field->type ) : wp_dropdown_users( array('name' => 'form_field['.$form_field->id.']', 'selected' => $meta_data[$form_field->id]) ); ?>	
				<?php elseif ( 'project' == $form_field->type ) : echo $projectmanager->getDatasetCheckboxList($options['form_field_options'][$form_field->id], 'form_field['.$form_field->id.'][]', $meta_data[$form_field->id]); ?>
				<?php elseif ( 'select' == $form_field->type ) : $projectmanager->printFormFieldDropDown($form_field->id, $meta_data[$form_field->id], $dataset_id, "form_field[".$form_field->id."]"); ?>
				<?php elseif ( 'checkbox' == $form_field->type ) : $projectmanager->printFormFieldCheckboxList($form_field->id, $meta_data[$form_field->id], $dataset_id, "form_field[".$form_field->id."][]"); ?>
				<?php elseif ( 'radio' == $form_field->type ) : $projectmanager->printFormFieldRadioList($form_field->id, $meta_data[$form_field->id], $dataset_id, "form_field[".$form_field->id."]"); ?>
				<?php elseif ( !empty($form_field->type) && is_array($projectmanager->getFormFieldTypes($form_field->type)) ) : ?>
					<?php $field = $projectmanager->getFormFieldTypes($form_field->type); ?>
					<?php if ( isset($field->type['input_callback']) ) :
						$args = array ( 'dataset_id' => $dataset_id, 'form_field' => $form_field, 'data' => $meta_data[$form_field->id], 'name' => 'form_field['.$form_field->id.']' );
						$field['args'] = array_merge( $args, (array)$field['args'] );
						call_user_func_array($field['input_callback'], $field['args']);
					else : ?>
					<p>
						<input type="hidden" name="form_field[<?php echo $form_field->id ?>]" id="form_field_<?php echo $form_field->id ?>" value="" />
						<?php echo $field['msg'] ?>
					</p>
					<?php endif; ?>
				<?php endif; ?>
				<br />
		<?php endforeach; ?>
	<?php endif; ?>


<input type="hidden" name="project_id" value="<?php echo $project->id ?>" />
<input type="hidden" name="dataset_id" value="<?php echo $dataset->id ?>" />
<input type="hidden" name="user_id" value="<?php echo $dataset->user_id ?>" />
<input type="hidden" name="updateProjectManager" value="dataset" />

<p class="submit"><input type="submit" name="adddataset" value="<?php echo $form_title ?> &raquo;" class="button" /></p>
</form>
