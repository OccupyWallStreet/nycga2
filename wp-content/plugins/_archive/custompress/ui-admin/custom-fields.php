<?php if (!defined('ABSPATH')) die('No direct access allowed!'); ?>

<?php

$custom_fields = array();
if(is_multisite()) {

	if($this->display_network_content || is_network_admin() ){
		$cf = get_site_option('ct_custom_fields');
		$custom_fields['net'] = (empty($cf)) ? array() : $cf;
	}
	if($this->enable_subsite_content_types && ! is_network_admin() ){
		$cf = get_option('ct_custom_fields');
		$custom_fields['local'] = (empty($cf)) ? array() : $cf;
	}
} else {
	$cf = get_option('ct_custom_fields');
	$custom_fields['local'] = (empty($cf)) ? array() : $cf;
}

//Nonce for reorder
$nonce = wp_create_nonce('reorder_custom_fields');

?>

<?php $this->render_admin('update-message'); ?>

<div class="ct-wrap-left">

	<div class="embed-code-wrap">
		<h3><?php _e('Embedding Custom Fields', $this->text_domain); ?></h3>
		<span class="description"><?php _e( '<b>Embed codes</b> are used in templates to return the value of the custom fields of the current post. Codes may be for individual fields using the Embed code links below for each field <br />or you can display the entire block of custom fields for a listing using the embed code:', $this->text_domain ); ?></span>
		<br />
		<code><span style="color:red">&lt;?php</span> echo <strong>do_shortcode('[custom_fields_block]')</strong>; <span style="color:red">?&gt;</span></code>
		<br /><br />
		<span class="description"><?php _e( '<b>Shortcodes</b> are used in post, pages and widgets to return the value of the custom fields of the current post. Use inside the loop in Posts and Widgets', $this->text_domain ); ?></span>
		<br />
		<code><strong>[custom_fields_block]</strong></code>
	</div>
</div>

<div class="ct-wrap-right">

	<h3><?php _e('Attributes for the [custom_fields_block]', $this->text_domain); ?></h3>
	<strong><?php _e( 'Attributes which may be used for the block embed and shortcode:', $this->text_domain); ?></strong>
	<br /><span class="description"><?php _e( 'wrap        = Wrap the fields in either a "table", a "ul" or a "div" structure.', $this->text_domain ) ?></span>
	<br /><strong><?php _e( 'The default wrap attributes may be overriden using the following individual attributes:', $this->text_domain); ?></strong>
	<br /><span class="description"><?php _e( 'open = HTML to begin the block with', $this->text_domain ) ?></span>
	<br /><span class="description"><?php _e( 'close = HTML to end the block with', $this->text_domain ) ?></span>
	<br /><span class="description"><?php _e( 'open_line = HTML to begin a line with', $this->text_domain ) ?></span>
	<br /><span class="description"><?php _e( 'close_line = HTML to end a line with', $this->text_domain ) ?></span>
	<br /><span class="description"><?php _e( 'open_title = HTML to begin the title with', $this->text_domain ) ?></span>
	<br /><span class="description"><?php _e( 'close_title = HTML to end the title with', $this->text_domain ) ?></span>
	<br /><span class="description"><?php _e( 'open_value = HTML to begin the value with', $this->text_domain ) ?></span>
	<br /><span class="description"><?php _e( 'close_value = HTML to end the value with', $this->text_domain ) ?></span>
</div>
<div class="ct-clear"></div>

<form action="#" method="post" class="ct-form-single-btn">
	<input type="submit" class="button-secondary" name="redirect_add_custom_field" value="<?php _e('Add Custom Field', $this->text_domain); ?>" />
</form>
<table class="widefat">
	<thead>
		<tr>
			<th><?php _e('Order', $this->text_domain); ?></th>
			<th><?php _e('Field Name', $this->text_domain); ?></th>
			<th><?php _e('Field ID', $this->text_domain); ?></th>
			<th><?php _e('WP/Plugins', $this->text_domain); ?></th>
			<th><?php _e('Field Type', $this->text_domain); ?></th>
			<th><?php _e('Description', $this->text_domain); ?></th>
			<th><?php _e('Post Types', $this->text_domain); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th><?php _e('Order', $this->text_domain); ?></th>
			<th><?php _e('Field Name', $this->text_domain); ?></th>
			<th><?php _e('Field ID', $this->text_domain); ?></th>
			<th><?php _e('WP/Plugins', $this->text_domain); ?></th>
			<th><?php _e('Field Type', $this->text_domain); ?></th>
			<th><?php _e('Description', $this->text_domain); ?></th>
			<th><?php _e('Post Types', $this->text_domain); ?></th>
		</tr>
	</tfoot>
	<tbody>

		<?php
		foreach($custom_fields as $source => $cf):

		$flag = ($source == 'net') && ! is_network_admin();
		$last = count($cf);
		$i = 0;
		foreach ( $cf as $custom_field ): ?>

		<?php

		$prefix = ( empty( $custom_field['field_wp_allow'] ) ) ? '_ct_' : 'ct_';
		$fid = $prefix . $custom_field['field_id'];
		?>

		<?php $class = ( $i % 2) ? 'ct-edit-row alternate' : 'ct-edit-row'; $i++; ?>
		<tr class="<?php echo ( $class ); ?>">
			<td>

				<?php if($flag): ?>
				<span class="description"><?php _e('network', $this->text_domain); ?></span>
				<?php endif; ?>
				<?php if($i != 1 && ! $flag): ?>
				<span class="ct-up"><a href="<?php echo( self_admin_url( 'admin.php?page=' . $_GET['page'] . "&ct_content_type=custom_field&direction=up&_wpnonce=$nonce&ct_reorder_custom_field=" . $custom_field['field_id'] )); ?>"><img src="<?php echo $this->plugin_url . 'ui-admin/images/up.png'; ?>" /></a> </span>
				<?php endif; ?>
				<?php if($i != $last && ! $flag): ?>
				<span class="ct-down"><a href="<?php echo( self_admin_url( 'admin.php?page=' . $_GET['page'] . "&ct_content_type=custom_field&direction=down&_wpnonce=$nonce&ct_reorder_custom_field=" . $custom_field['field_id'] )); ?>"><img src="<?php echo $this->plugin_url . 'ui-admin/images/down.png'; ?>" /></a></span>
				<?php endif; ?>
			</td>
			<td>
				<strong>
					<?php
					if($flag):
					echo( $custom_field['field_title'] );
					else:
					?>
					<a href="<?php echo( self_admin_url( 'admin.php?page=' . $_GET['page'] . '&ct_content_type=custom_field&ct_edit_custom_field=' . $custom_field['field_id'] )); ?>"><?php echo( $custom_field['field_title'] ); ?></a>
					<?php endif; ?>

				</strong>
				<div class="row-actions" id="row-actions-<?php echo $custom_field['field_id']; ?>" >
					<?php if(! $flag): ?>
					<span class="edit">
						<a title="<?php _e('Edit this custom field', $this->text_domain); ?>" href="<?php echo( self_admin_url( 'admin.php?page=' . $_GET['page'] . '&ct_content_type=custom_field&ct_edit_custom_field=' . $custom_field['field_id'] ) ); ?>"><?php _e( 'Edit', $this->text_domain ); ?></a> |
					</span>
					<?php endif; ?>
					<span>
						<a title="<?php _e('Show embed code', $this->text_domain); ?>" href="#" onclick="javascript:content_types.toggle_embed_code('<?php echo $custom_field['field_id']; ?>'); return false;"><?php _e('Embed Code', $this->text_domain); ?></a>
					</span>

					<?php if($flag): ?>
					<span class="description"><?php _e('Edit in Network Admin.', $this->text_domain); ?></span>
					<?php endif; ?>

					<?php if(! $flag): ?>
					<span class="trash">
						| <a class="submitdelete" href="#" onclick="javascript:content_types.toggle_delete('<?php echo $custom_field['field_id']; ?>'); return false;"><?php _e( 'Delete', $this->text_domain ); ?></a>
					</span>
					<?php endif; ?>
				</div>

				<form action="#" method="post" id="form-<?php echo $custom_field['field_id']; ?>" class="del-form">
					<?php wp_nonce_field('delete_custom_field'); ?>
					<input type="hidden" name="custom_field_id" value="<?php echo $custom_field['field_id']; ?>" />
					<input type="submit" class="button confirm" value="<?php _e( 'Field and values', $this->text_domain ); ?>" name="delete_cf_values" />
					<input type="submit" class="button cancel"  value="<?php _e( 'Cancel', $this->text_domain ); ?>" onClick="content_types.cancel('<?php echo $custom_field['field_id']; ?>'); return false;" />
					<input type="submit" class="button confirm" value="<?php _e( 'Only field', $this->text_domain ); ?>" name="submit" />
				</form>
			</td>
			<td><?php echo $prefix . $custom_field['field_id']; ?></td>
			<td><?php echo ( isset( $custom_field['field_wp_allow'] ) && 1 == $custom_field['field_wp_allow'] ) ? __( 'Allow', $this->text_domain ) : __( 'Deny', $this->text_domain ); ?></td>
			<td><?php echo( $custom_field['field_type'] ); ?></td>
			<td><?php echo( $custom_field['field_description'] ); ?></td>
			<td>
				<?php foreach( $custom_field['object_type'] as $object_type ): ?>
				<?php echo( $object_type ); ?>
				<?php endforeach; ?>
			</td>
		</tr>
		<tr id="embed-code-<?php echo $custom_field['field_id']; ?>" class="embed-code <?php echo ( $class ); ?>">
			<td colspan="10">
				<div class="embed-code-wrap">
					<span class="description"><?php _e( 'Embed code returns the values of the custom field with the specified key from the specified post. Property may be one of "title", "description" or "value". If property is not used "value" wil be returned. Use inside the loop in templates and PHP code ', $this->text_domain ); ?></span>
					<br />
					<code><span style="color:red">&lt;?php</span> echo <strong>do_shortcode('[ct id="<?php echo( $prefix . $custom_field['field_id'] ); ?>" property="title | description | value"]')</strong>; <span style="color:red">?&gt;</span></code>
					<br /><br />
					<span class="description"><?php _e( 'Shortcode returns the values of the custom field with the specified key from the specified post. Property may be one of "title", "description" or "value". If property is not used "value" wil be returned. Use inside the loop in Posts and Widgets', $this->text_domain ); ?></span>
					<br />
					<code><strong>[ct id="<?php echo $prefix . $custom_field['field_id'] ; ?>" property="title | description | value"]</strong></code>
				</div>
			</td>
		</tr>
		<?php endforeach; ?>
		<?php endforeach; ?>

	</tbody>
</table>
<form action="#" method="post" class="ct-form-single-btn">
	<input type="submit" class="button-secondary" name="redirect_add_custom_field" value="<?php _e('Add Custom Field', $this->text_domain); ?>" />
</form>
