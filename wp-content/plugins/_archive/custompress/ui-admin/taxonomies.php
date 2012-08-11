<?php if (!defined('ABSPATH')) die('No direct access allowed!'); ?>

<?php

$taxonomies = array();
if(is_multisite()) {

	if($this->display_network_content || is_network_admin() ){
		$cf = get_site_option('ct_custom_taxonomies');
		$taxonomies['net'] = (empty($cf)) ? array() : $cf;
	}
	if($this->enable_subsite_content_types && ! is_network_admin() ){
		$cf = get_option('ct_custom_taxonomies');
		$taxonomies['local'] = (empty($cf)) ? array() : $cf;
	}
} else {
	$cf = get_option('ct_custom_taxonomies');
	$taxonomies['local'] = (empty($cf)) ? array() : $cf;
}

?>

<?php $this->render_admin('update-message'); ?>

<form action="#" method="post" class="ct-form-single-btn">
	<input type="submit" class="button-secondary" name="redirect_add_taxonomy" value="<?php _e('Add Taxonomy', $this->text_domain); ?>" />
</form>
<table class="widefat">
	<thead>
		<tr>
			<th><?php _e('Taxonomy', $this->text_domain); ?></th>
			<th><?php _e('Name', $this->text_domain); ?></th>
			<th><?php _e('Post Types', $this->text_domain); ?></th>
			<th><?php _e('Public', $this->text_domain); ?></th>
			<th><?php _e('Hierarchical', $this->text_domain); ?></th>
			<th><?php _e('Rewrite', $this->text_domain); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th><?php _e('Taxonomy', $this->text_domain); ?></th>
			<th><?php _e('Name', $this->text_domain); ?></th>
			<th><?php _e('Post Types', $this->text_domain); ?></th>
			<th><?php _e('Public', $this->text_domain); ?></th>
			<th><?php _e('Hierarchical', $this->text_domain); ?></th>
			<th><?php _e('Rewrite', $this->text_domain); ?></th>
		</tr>
	</tfoot>
	<tbody>

		<?php
		foreach($taxonomies as $source => $tax):
		$flag = ($source == 'net') && ! is_network_admin();
		?>
		<?php $i = 0; foreach ( $tax as $name => $taxonomy ): ?>
		<?php $class = ( $i % 2) ? 'ct-edit-row alternate' : 'ct-edit-row'; $i++; ?>
		<tr class="<?php echo ( $class ); ?>">
			<td>
				<strong>
					<?php
					if($flag):
					echo $name;
					else:
					?>

					<a href="<?php echo self_admin_url('admin.php?page=' . $_GET['page'] . '&ct_content_type=taxonomy&ct_edit_taxonomy=' . $name); ?>"><?php echo( $name ); ?></a>
					<?php endif; ?>

				</strong>
				<div class="row-actions" id="row-actions-<?php echo $name; ?>">
					<?php if(! $flag): ?>

					<span class="edit">
						<a title="<?php _e('Edit this taxonomy', $this->text_domain); ?>" href="<?php echo self_admin_url( 'admin.php?page=' . $_GET['page'] . '&ct_content_type=taxonomy&ct_edit_taxonomy=' . $name ); ?>" ><?php _e('Edit', $this->text_domain); ?></a> |
					</span>
					<?php endif; ?>

					<span>
						<a title="<?php _e('Show embed code', $this->text_domain); ?>" href="" onclick="javascript:content_types.toggle_embed_code('<?php echo( $name ); ?>'); return false;"><?php _e('Embed Code', $this->text_domain); ?></a>
					</span>

					<?php if($flag): ?>
					<span class="description"><?php _e('Edit in Network Admin.', $this->text_domain); ?></span>
					<?php endif; ?>

					<?php if(! $flag): ?>
					<span class="trash">
						| <a class="submitdelete" href="" onclick="javascript:content_types.toggle_delete('<?php echo( $name ); ?>'); return false;"><?php _e('Delete', $this->text_domain); ?></a>
					</span>
					<?php endif; ?>
				</div>
				<form action="#" method="post" id="form-<?php echo( $name ); ?>" class="del-form">
					<?php wp_nonce_field('delete_taxonomy'); ?>
					<input type="hidden" name="taxonomy_name" value="<?php echo( $name ); ?>" />
					<input type="submit" class="button confirm" value="<?php _e( 'Confirm', $this->text_domain ); ?>" name="submit" />
					<input type="submit" class="button cancel"  value="<?php _e( 'Cancel', $this->text_domain ); ?>" onClick="content_types.cancel('<?php echo( $name ); ?>'); return false;" />
				</form>
			</td>
			<td><?php if ( isset( $taxonomy['args']['labels']['name'] ) ) echo $taxonomy['args']['labels']['name']; ?></td>
			<td>
				<?php foreach( $taxonomy['object_type'] as $object_type ): ?>
				<?php echo( $object_type ); ?>
				<?php endforeach; ?>
			</td>
			<td class="ct-tf-icons-wrap">
				<?php if ( $taxonomy['args']['public'] === null ): ?>
				<img class="ct-tf-icons" src="<?php echo esc_attr( $this->plugin_url . 'ui-admin/images/advanced.png' ); ?>" alt="<?php _e('Advanced', $this->text_domain); ?>" title="<?php _e('Advanced', $this->text_domain); ?>" />
				<?php elseif ( $taxonomy['args']['public'] ): ?>
				<img class="ct-tf-icons" src="<?php echo esc_attr( $this->plugin_url . 'ui-admin/images/true.png' ); ?>" alt="<?php _e('True', $this->text_domain); ?>" title="<?php _e('True', $this->text_domain); ?>" />
				<?php else: ?>
				<img class="ct-tf-icons" src="<?php echo esc_attr( $this->plugin_url . 'ui-admin/images/false.png' ); ?>" alt="<?php _e('False', $this->text_domain); ?>" title="<?php _e('False', $this->text_domain); ?>" />
				<?php endif; ?>
			</td>
			<td class="ct-tf-icons-wrap">
				<?php if ( $taxonomy['args']['hierarchical'] ): ?>
				<img class="ct-tf-icons" src="<?php echo esc_attr( $this->plugin_url . 'ui-admin/images/true.png' ); ?>" alt="<?php _e('True', $this->text_domain); ?>" title="<?php _e('True', $this->text_domain); ?>" />
				<?php else: ?>
				<img class="ct-tf-icons" src="<?php echo esc_attr( $this->plugin_url . 'ui-admin/images/false.png' ); ?>" alt="<?php _e('False', $this->text_domain); ?>" title="<?php _e('False', $this->text_domain); ?>" />
				<?php endif; ?>
			</td>
			<td class="ct-tf-icons-wrap">
				<?php if ( $taxonomy['args']['rewrite'] ): ?>
				<img class="ct-tf-icons" src="<?php echo esc_attr( $this->plugin_url . 'ui-admin/images/true.png' ); ?>" alt="<?php _e('True', $this->text_domain); ?>" title="<?php _e('True', $this->text_domain); ?>" />
				<?php else: ?>
				<img class="ct-tf-icons" src="<?php echo esc_attr( $this->plugin_url . 'ui-admin/images/false.png' ); ?>" alt="<?php _e('False', $this->text_domain); ?>" title="<?php _e('False', $this->text_domain); ?>" />
				<?php endif; ?>
			</td>
		</tr>
		<tr id="embed-code-<?php echo( $name ); ?>" class="embed-code <?php echo ( $class ); ?>">
			<td colspan="6">
				<div class="embed-code-wrap">
					<span class="description"><?php _e('Embed code returns an HTML string of taxonomy terms associated with a post and given taxonomy. <br />Terms are linked to their respective term listing pages. Use it in templates inside the Loop.', $this->text_domain ); ?></span>
					<br />
					<code><span style="color:red">&lt;?php</span> echo <strong>do_shortcode('[tax id="<?php echo( $name ); ?>" before="your text before " separator=", " after=" your text after"]')</strong>; <span style="color:red">?&gt;</span></code>
					<br /><br />
					<span class="description"><?php _e('Shortcode returns an HTML string of taxonomy terms associated with a post and given taxonomy. <br />Terms are linked to their respective term listing pages. Use it inside the Loop.', $this->text_domain ); ?></span>
					<br />
					<code><strong>[tax id="<?php echo( $name ); ?>" before="your text before: " separator=", " after=" your text after"]</strong></code>
				</div>
			</td>
		</tr>
		<?php endforeach; ?>
		<?php endforeach; ?>
	</tbody>
</table>
<form action="#" method="post" class="ct-form-single-btn">
	<input type="submit" class="button-secondary" name="redirect_add_taxonomy" value="<?php _e('Add Taxonomy', $this->text_domain); ?>" />
</form>
