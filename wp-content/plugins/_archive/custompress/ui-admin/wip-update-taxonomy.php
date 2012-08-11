<?php if (!defined('ABSPATH')) die('No direct access allowed!'); ?>

<?php
$post_types = get_post_types('','names');

if ( isset( $_GET['ct_add_taxonomy'] ) ) {
	$action = 'add';
	$taxonomy = $_POST;
}
elseif ( isset ( $_GET['ct_edit_taxonomy'] ) ) {
	$action = 'edit';
	$taxonomy = $this->taxonomies[$_GET['ct_edit_taxonomy']]['args'];
	$taxonomy['taxonomy'] = $_GET['ct_edit_taxonomy'];
}

var_dump( $taxonomy );
?>

<h3><?php _e('Add Taxonomy', $this->text_domain); ?></h3>
<form action="#" method="post" class="ct-taxonomy">
	<div class="ct-wrap-left">
		<div class="ct-table-wrap">
			<div class="ct-arrow"><br></div>
			<h3 class="ct-toggle"><?php _e('Taxonomy', $this->text_domain) ?></h3>
			<table class="form-table <?php do_action('ct_invalid_field_taxonomy'); ?>">
				<tr>
					<th>
						<label for="post_type"><?php _e('Taxonomy', $this->text_domain) ?> <span class="ct-required">( <?php _e('required', $this->text_domain); ?> )</span></label>
					</th>
					<td>
						<input type="text" name="taxonomy" value="<?php if ( isset( $taxonomy['taxonomy'] ) ) echo $taxonomy['taxonomy']; ?>">
						<span class="description"><?php _e('The system name of the taxonomy. Alphanumeric characters and underscores only. Min 2 letters. Once added the taxonomy system name cannot be changed.', $this->text_domain); ?></span>
					</td>
				</tr>
			</table>
		</div>
		<div class="ct-table-wrap">
			<div class="ct-arrow"><br></div>
			<h3 class="ct-toggle"><?php _e('Post Type', $this->text_domain) ?></h3>
			<table class="form-table <?php do_action('ct_invalid_field_object_type'); ?>">
				<tr>
					<th>
						<label for="object_type"><?php _e('Post Type', $this->text_domain) ?> (<span class="ct-required"> <?php _e('required', $this->text_domain); ?> </span>)</label>
					</th>
					<td>
						<select name="object_type[]" multiple="multiple" class="ct-object-type">
							<?php if ( is_array( $post_types ) ): ?>
							<?php foreach( $post_types as $post_type ): ?>
							<option value="<?php echo ( $post_type ); ?>"
								<?php if ( $action == 'add' ): ?>
								<?php if ( isset( $taxonomy['object_type'] ) && is_array( $taxonomy['object_type'] ) ): ?>
								<?php foreach ( $taxonomy['object_type'] as $post_value ): ?>
								<?php if ( $post_value == $post_type ) echo 'selected="selected"'; ?>
								<?php endforeach; ?>
								<?php endif; ?>
								<?php elseif ( $action == 'edit' ): ?>
								<?php if ( is_object_in_taxonomy( $post_type, $_GET['ct_edit_taxonomy'] ) ) echo 'selected="selected"'; ?>
								<?php endif; ?>
							><?php echo( $post_type ); ?></option>
							<?php endforeach; ?>
							<?php endif; ?>
						</select>
						<span class="description"><?php _e('Select one or more post types to add this taxonomy to.', $this->text_domain); ?></span>
					</td>
				</tr>
			</table>
		</div>
		<div class="ct-table-wrap">
			<div class="ct-arrow"><br></div>
			<h3 class="ct-toggle"><?php _e('Labels', $this->text_domain) ?></h3>
			<table class="form-table">
				<tr>
					<th>
						<label for="name"><?php _e('Name', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[name]" value="<?php if ( isset( $taxonomy['labels']['name'] ) ) echo $taxonomy['labels']['name']; ?>">
						<span class="description"><?php _e('General name for the taxonomy, usually plural.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label for="singular_name"><?php _e('Singular Name', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[singular_name]" value="<?php if ( isset( $taxonomy['labels']['singular_name']  ) ) echo $taxonomy['labels']['singular_name']; ?>">
						<span class="description"><?php _e('Name for one object of this taxonomy. Defaults to value of name.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label for="add_new_item"><?php _e('Add New Item', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[add_new_item]" value="<?php if ( isset( $taxonomy['labels']['add_new_item'] ) ) echo $taxonomy['labels']['add_new_item']; ?>">
						<span class="description"><?php _e('The add new item text. Default is "Add New Tag" or "Add New Category".', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label for="new_item_name"><?php _e('New Item Name', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[new_item_name]" value="<?php if ( isset( $taxonomy['labels']['new_item_name'] ) ) echo $taxonomy['labels']['new_item_name']; ?>">
						<span class="description"><?php _e('The new item name text. Default is "New Tag Name" or "New Category Name".', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label for="edit_item"><?php _e('Edit Item', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[edit_item]" value="<?php if ( isset( $taxonomy['labels']['edit_item'] ) ) echo $taxonomy['labels']['edit_item']; ?>">
						<span class="description"><?php _e('The edit item text. Default is "Edit Tag" or "Edit Category".', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label for="update_item"><?php _e('Update Item', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[update_item]" value="<?php if ( isset( $taxonomy['labels']['update_item'] ) ) echo $taxonomy['labels']['update_item']; ?>">
						<span class="description"><?php _e('The update item text. Default is "Update Tag" or "Update Category".', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label for="search_items"><?php _e('Search Items', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[search_items]" value="<?php if ( isset( $taxonomy['labels']['search_items'] ) ) echo $taxonomy['labels']['search_items']; ?>">
						<span class="description"><?php _e('The search items text. Default is "Search Tags" or "Search Categories".', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label for="popular_items"><?php _e('Popular Items', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[popular_items]" value="<?php if ( isset( $taxonomy['labels']['popular_items'] ) ) echo $taxonomy['labels']['popular_items']; ?>">
						<span class="description"><?php _e('The popular items text. Default is "Popular Tags" or null.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label for="all_items"><?php _e('All Items', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[all_items]" value="<?php if ( isset( $taxonomy['labels']['all_items'] ) ) echo $taxonomy['labels']['all_items']; ?>">
						<span class="description"><?php _e('The all items text. Default is "All Tags" or "All Categories".', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label for="parent_item"><?php _e('Parent Item', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[parent_item]" value="<?php if ( isset( $taxonomy['labels']['parent_item'] ) ) echo $taxonomy['labels']['parent_item']; ?>">
						<span class="description"><?php _e('The parent item text. This string is not used on non-hierarchical taxonomies such as post tags. Default is null or "Parent Category".', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label for="parent_item_colon"><?php _e('Parent Item Colon', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[parent_item_colon]" value="<?php if ( isset( $taxonomy['labels']['parent_item_colon'] ) ) echo $taxonomy['labels']['parent_item_colon']; ?>">
						<span class="description"><?php _e('The same as parent_item, but with colon : in the end null, "Parent Category:".', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label for="add_or_remove_items"><?php _e('Add Or Remove Items', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[add_or_remove_items]" value="<?php if ( isset( $taxonomy['labels']['add_or_remove_items'] ) ) echo $taxonomy['labels']['add_or_remove_items']; ?>">
						<span class="description"><?php _e('The add or remove items text is used in the meta box when JavaScript is disabled. This string isn\'t used on hierarchical taxonomies. Default is "Add or remove tags" or null.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label for="separate_items_with_commas"><?php _e('Separate Items With Commas', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[separate_items_with_commas]" value="<?php if ( isset( $taxonomy['labels']['separate_items_with_commas'] ) ) echo $taxonomy['labels']['separate_items_with_commas']; ?>">
						<span class="description"><?php _e('The separate item with commas text used in the taxonomy meta box. This string isn\'t used on hierarchical taxonomies. Default is "Separate tags with commas", or null.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label for="choose_from_most_used"><?php _e('Choose From Most Used', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[choose_from_most_used]" value="<?php if ( isset( $taxonomy['labels']['choose_from_most_used'] ) ) echo $taxonomy['labels']['choose_from_most_used']; ?>">
						<span class="description"><?php _e('The choose from most used text used in the taxonomy meta box. This string isn\'t used on hierarchical taxonomies. Default is "Choose from the most used tags" or null.', $this->text_domain); ?></span>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<div class="ct-wrap-right">
		<div class="ct-table-wrap">
			<div class="ct-arrow"><br></div>
			<h3 class="ct-toggle"><?php _e('Public', $this->text_domain) ?></h3>
			<table class="form-table publica">
				<tr>
					<th>
						<label for="public"><?php _e('Public', $this->text_domain) ?></label>
					</th>
					<td>
						<span class="description"><?php _e('Should this taxonomy be exposed in the admin UI.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input type="radio" name="public" value="1" <?php if ( isset( $taxonomy['public'] ) && !empty( $taxonomy['public'] ) ) echo 'checked="checked"'; elseif ( !isset( $taxonomy['public'] ) ) echo( 'checked="checked"' ); ?>>
						<span class="description"><strong><?php _e('TRUE', $this->text_domain); ?></strong></span>
						<br />
						<input type="radio" name="public" value="0" <?php if ( isset( $taxonomy['public'] ) && $taxonomy['public'] === '0' ) echo( 'checked="checked"' ); ?>>
						<span class="description"><strong><?php _e('FALSE', $this->text_domain); ?></strong></span>
						<br />
						<input type="radio" name="public" value="advanced" <?php if ( isset( $taxonomy['public'] ) && $taxonomy['public'] == 'advanced' ) echo( 'checked="checked"' ); ?>>
						<span class="description"><strong><?php _e('ADVANCED', $this->text_domain); ?></strong></span>
					</td>
				</tr>
			</table>
		</div>
		<div class="ct-table-wrap">
			<div class="ct-arrow"><br></div>
			<h3 class="ct-toggle"><?php _e('Show UI', $this->text_domain) ?></h3>
			<table class="form-table show-ui">
				<tr>
					<th>
						<label for="show_ui"><?php _e('Show UI', $this->text_domain) ?></label>
					</th>
					<td>
						<span class="description"><?php _e('Whether to generate a default UI for managing this taxonomy.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input type="radio" name="show_ui" value="1" <?php if ( isset( $taxonomy['public'] ) && $taxonomy['public'] == 'advanced' && isset( $taxonomy['show_ui'] ) && $taxonomy['show_ui'] === '1' ) echo( 'checked="checked"' ); ?>>
						<span class="description"><strong><?php _e('TRUE', $this->text_domain); ?></strong></span>
						<br />
						<input type="radio" name="show_ui" value="0" <?php if ( isset( $taxonomy['public'] ) && $taxonomy['public'] == 'advanced' && isset( $taxonomy['show_ui'] ) && $taxonomy['show_ui'] === '0' ) echo( 'checked="checked"' ); ?>>
						<span class="description"><strong><?php _e('FALSE', $this->text_domain); ?></strong></span>
					</td>
				</tr>
			</table>
		</div>
		<div class="ct-table-wrap">
			<div class="ct-arrow"><br></div>
			<h3 class="ct-toggle"><?php _e('Show Tagcloud', $this->text_domain) ?></h3>
			<table class="form-table show_tagcloud">
				<tr>
					<th>
						<label for="show_tagcloud"><?php _e('Show Tagcloud', $this->text_domain) ?></label>
					</th>
					<td>
						<span class="description"><?php _e('Whether to show a tag cloud in the admin UI for this taxonomy.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input type="radio" name="show_tagcloud" value="1" <?php if ( isset( $taxonomy['public'] ) && $taxonomy['public'] == 'advanced' && isset( $taxonomy['show_tagcloud'] ) && $taxonomy['show_tagcloud'] === '1' ) echo( 'checked="checked"' ); ?>>
						<span class="description"><strong><?php _e('TRUE', $this->text_domain); ?></strong></span>
						<br />
						<input type="radio" name="show_tagcloud" value="0" <?php if ( isset( $taxonomy['public'] ) && $taxonomy['public'] == 'advanced' && isset( $taxonomy['show_tagcloud'] ) && $taxonomy['show_tagcloud'] === '0' ) echo( 'checked="checked"' ); ?>>
						<span class="description"><strong><?php _e('FALSE', $this->text_domain); ?></strong></span>
					</td>
				</tr>
			</table>
		</div>
		<div class="ct-table-wrap">
			<div class="ct-arrow"><br></div>
			<h3 class="ct-toggle"><?php _e('Show In Nav Menus ', $this->text_domain) ?></h3>
			<table class="form-table show-in-nav-menus">
				<tr>
					<th>
						<label for="show_in_nav_menus"><?php _e('Show In Nav Menus', $this->text_domain) ?></label>
					</th>
					<td>
						<span class="description"><?php _e('Whether to make this taxonomy available for selection in navigation menus.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input type="radio" name="show_in_nav_menus" value="1" <?php if ( isset( $taxonomy['public'] ) && $taxonomy['public'] == 'advanced' && isset( $taxonomy['show_in_nav_menus'] ) && $taxonomy['show_in_nav_menus'] === '1' ) echo( 'checked="checked"' ); ?>>
						<span class="description"><strong><?php _e('TRUE', $this->text_domain); ?></strong></span>
						<br />
						<input type="radio" name="show_in_nav_menus" value="0" <?php if ( isset( $taxonomy['public'] ) && $taxonomy['public'] == 'advanced' && isset( $taxonomy['show_in_nav_menus'] ) && $taxonomy['show_in_nav_menus'] === '0' ) echo( 'checked="checked"' ); ?>>
						<span class="description"><strong><?php _e('FALSE', $this->text_domain); ?></strong></span>
					</td>
				</tr>
			</table>
		</div>
		<div class="ct-table-wrap">
			<div class="ct-arrow"><br></div>
			<h3 class="ct-toggle"><?php _e('Hierarchical', $this->text_domain) ?></h3>
			<table class="form-table">
				<tr>
					<th>
						<label for="hierarchical"><?php _e('Hierarchical', $this->text_domain) ?></label>
					</th>
					<td>
						<span class="description"><?php _e('Whether the post type is hierarchical. Allows Parent to be specified.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input type="radio" name="hierarchical" value="1" <?php if ( isset( $taxonomy['hierarchical'] ) && !empty( $taxonomy['hierarchical'] ) ) echo 'checked="checked"'; ?>>
						<span class="description"><strong><?php _e('TRUE', $this->text_domain); ?></strong></span>
						<br />
						<input type="radio" name="hierarchical" value="0" <?php if ( isset( $taxonomy['hierarchical'] ) && empty( $taxonomy['hierarchical'] ) ) echo 'checked="checked"'; ?>>
						<span class="description"><strong><?php _e('FALSE', $this->text_domain); ?></strong></span>
					</td>
				</tr>
			</table>
		</div>
		<div class="ct-table-wrap">
			<div class="ct-arrow"><br></div>
			<h3 class="ct-toggle"><?php _e('Rewrite', $this->text_domain) ?></h3>
			<table class="form-table">
				<tr>
					<th>
						<label for="rewrite"><?php _e('Rewrite', $this->text_domain) ?></label>
					</th>
					<td>
						<span class="description"><?php _e('Set to false to prevent rewrite, or true to customize.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input type="radio" name="rewrite" value="1" <?php if ( isset( $taxonomy['rewrite'] ) && !empty( $taxonomy['rewrite'] ) ) echo 'checked="checked"'; ?>>
						<span class="description"><strong><?php _e('TRUE', $this->text_domain); ?></strong></span>
						<br />
						<span class="description"><?php _e('Default will use query var.', $this->text_domain); ?></span>
						<br />
						<input type="radio" name="rewrite" value="0" <?php if ( isset( $taxonomy['rewrite'] ) && empty( $taxonomy['rewrite'] ) ) echo 'checked="checked"'; ?>>
						<span class="description"><strong><?php _e('FALSE', $this->text_domain); ?></strong></span>
						<br />
						<span class="description"><?php _e('Prevent rewrite.', $this->text_domain); ?></span>
						<br /><br />

						<input type="checkbox" name="rewrite_use_slug" value="1" <?php if ( !empty( $taxonomy['rewrite_use_slug'] ) ) echo 'checked="checked"'; ?>>
						<span class="description"><strong><?php _e('Custom Slug', $this->text_domain); ?></strong></span>
						<br />
						<input type="text" name="rewrite_slug" value="<?php if ( isset( $taxonomy['rewrite_slug'] ) ) echo $taxonomy['rewrite_slug']; ?>" />
						<br />
						<span class="description"><?php _e('Prepend posts with this slug.', $this->text_domain); ?></span>
						<br /><br />
						<input type="checkbox" name="rewrite_disallow_with_front" value="1" <?php if ( !empty( $taxonomy['rewrite_disallow_with_front'] ) ) echo 'checked="checked"'; ?>>
						<span class="description"><strong><?php _e('Disallow Front Base', $this->text_domain); ?></strong></span>
						<br />
						<span class="description"><?php _e('Disallowing permalinks to be prepended with front base.', $this->text_domain); ?></span>
						<br /><br />
						<input type="checkbox" name="rewrite_hierarchical" value="1" <?php if ( !empty( $taxonomy['rewrite_hierarchical'] ) ) echo 'checked="checked"'; ?>>
						<span class="description"><strong><?php _e('Hierarchical URLs', $this->text_domain); ?></strong></span>
						<br />
						<span class="description"><?php _e('Allow hierarchical urls. Applicable for hierarchical taxonomies.', $this->text_domain); ?></span>
					</td>
				</tr>
			</table>
		</div>
		<div class="ct-table-wrap">
			<div class="ct-arrow"><br></div>
			<h3 class="ct-toggle"><?php _e('Query var', $this->text_domain) ?></h3>
			<table class="form-table">
				<tr>
					<th>
						<label for="query_var"><?php _e('Query var', $this->text_domain) ?></label>
					</th>
					<td>
						<span class="description"><?php _e('False to prevent queries. Default will use the taxonomy system name as query var.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input type="radio" name="query_var" value="1" <?php if ( isset( $taxonomy['query_var'] ) && !empty( $taxonomy['query_var'] ) ) { echo( 'checked="checked"' ); } ?>>
						<span class="description"><strong><?php _e('TRUE', $this->text_domain); ?></strong></span>
						<br />
						<input type="radio" name="query_var" value="0" <?php if ( isset( $taxonomy['query_var'] ) && empty( $taxonomy['query_var'] ) ) echo( 'checked="checked"' ); ?>>
						<span class="description"><strong><?php _e('FALSE', $this->text_domain); ?></strong></span>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<div class="clear"></div>
	<p class="submit">
		<?php wp_nonce_field('submit_taxonomy'); ?>
		<input type="submit" class="button-primary" name="submit" value="Add Taxonomy">
	</p>
	<br /><br /><br /><br />
</form>
