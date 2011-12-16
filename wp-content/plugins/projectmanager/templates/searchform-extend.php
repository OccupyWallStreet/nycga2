<?php
/**
Template page for the searchform

The following variables are usable:
	
	$form_fields: contains all formfields of the selected project
	$search: holds the search request
	$search_option: contains the selected search option
	
	You can check the content of a variable when you insert the tag <?php var_dump($variable) ?>
*/
?>
<form class='search-form alignright' action='' method='post'>
<div>
	<input type='text' class='search-input' name='search_string' value='<?php echo $search ?>' />
	
	<select size='1' name='search_option'>
		<option value='0' <?php if ( 0 == $search_option ) echo " selected='selected'" ?>><?php _e( 'Name', 'projectmanager' ) ?></option>
		<?php foreach ( $form_fields AS $form_field ) : ?>
		<?php if ( $form_field->type != 'project' ) : ?>
		<option value='<?php echo $form_field->id ?>'<?php if ( $search_option == $form_field->id ) echo " selected='selected'" ?>><?php echo $form_field->label ?></option>
		<?php endif; ?>
		<?php endforeach; ?>
		<option value='-1' <?php if ( -1 == $search_option ) echo " selected='selected'" ?>><?php _e( 'Categories', 'projectmanager' ) ?></option>
	</select>
	
	<input type='submit' value='<?php _e('Search', 'projectmanager') ?> &raquo;' class='button' />
</div>
</form>
