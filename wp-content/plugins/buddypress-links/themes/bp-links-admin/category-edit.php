<?php
/**
 * BP Links admin edit categories
 */
?>

<div class="wrap nosubsub buddypress-links-admin-content">
	<div id="icon-edit" class="icon32"><br /></div>
	<h2><?php _e( 'BuddyPress Links', 'buddypress-links' ) ?>: <?php echo $heading_text ?></h2>

<?php echo $heading ?>

<?php if ( isset( $message ) ) { ?>
	<div id="message" class="<?php echo $message_type ?> fade">
		<p><?php echo $message ?></p>
	</div>
<?php } ?>

<form name="bp_links_category_form" id="bp_links_category_form" method="post" action="?page=buddypress-links-admin-cats" class="validate">
<?php
	do_action('bp_links_admin_edit_category_form_before', $category);
	wp_original_referer_field(true, 'previous');
	wp_nonce_field($nonce_action);
?>
	<input type="hidden" name="action" value="<?php echo esc_attr($action) ?>" />
	<input type="hidden" name="category_id" value="<?php echo esc_attr($category_id) ?>" />
	<table class="form-table">
		<tr class="form-field form-required">
			<th scope="row" valign="top"><label for="name"><?php _e( 'Link Category Name', 'buddypress-links' ) ?></label></th>
			<td><input name="name" id="name" type="text" value="<?php echo esc_attr($category_name); ?>" size="40" aria-required="true" /></td>
		</tr>
		<tr class="form-field">
			<th scope="row" valign="top"><label for="description"><?php _e( 'Description (optional)', 'buddypress-links' ) ?></label></th>
			<td><textarea name="description" id="description" rows="5" cols="50" style="width: 97%;"><?php echo $category_description; ?></textarea></td>
		</tr>
		<tr class="form-field">
			<th scope="row" valign="top"><label for="priority"><?php _e( 'Priority (optional)', 'buddypress-links' ) ?></label></th>
			<td><input name="priority" id="priority" type="text" value="<?php echo esc_attr($category_priority); ?>" size="5" aria-required="true" style="width: 50px;" /> (1 <?php _e( 'to','buddypress-links' ) ?> 100)</td>
		</tr>
		<?php do_action('bp_links_admin_edit_category_form_fields', $category); ?>
	</table>
	<p class="submit">
		<input type="submit" class="button-primary" name="submit" value="<?php echo esc_attr($submit_text) ?>" />
	</p>
<?php do_action('bp_links_admin_edit_category_form_after', $category); ?>
</form>
</div>

<?php include('admin-footer.php'); ?>