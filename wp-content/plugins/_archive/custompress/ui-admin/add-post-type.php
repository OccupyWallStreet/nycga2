<?php if (!defined('ABSPATH')) die('No direct access allowed!'); ?>

<h3><?php _e('Add Post Type', $this->text_domain); ?></h3>
<form action="#" method="post" class="ct-post-type">
	<div class="ct-wrap-left">
		<div class="ct-table-wrap">
			<div class="ct-arrow"><br></div>
			<h3 class="ct-toggle"><?php _e('Post Type', $this->text_domain) ?></h3>
			<table class="form-table <?php do_action('ct_invalid_field_post_type'); ?>" />
				<tr>
					<th>
						<label><?php _e('Post Type', $this->text_domain) ?> <span class="ct-required">( <?php _e('required', $this->text_domain); ?> )</span></label>
					</th>
					<td>
						<input type="text" name="post_type" value="<?php if ( isset( $_POST['post_type'] ) ) echo $_POST['post_type']; elseif ( isset( $_GET['ct_edit_post_type'] ) ) echo $_GET['ct_edit_post_type']; ?>" />
						<br /><span class="description"><?php _e('The new post type system name ( max. 20 characters ). Alphanumeric lower-case characters and underscores only. Min 2 letters. Once added the post type system name cannot be changed.', $this->text_domain); ?></span>
					</td>
				</tr>
			</table>
		</div>
		<div class="ct-table-wrap">
			<div class="ct-arrow"><br></div>
			<h3 class="ct-toggle"><?php _e('Supports', $this->text_domain) ?></h3>
			<table class="form-table supports">
				<tr>
					<th>
						<label><?php _e('Supports', $this->text_domain) ?></label>
					</th>
					<td>
						<p><span class="description"><?php _e('Register support of certain features for a post type.', $this->text_domain); ?></span></p>
						<label>
							<input type="checkbox" name="supports[title]" value="title" <?php checked(empty($_POST['supports']) || ! empty($_POST['supports']) && in_array('title', $_POST['supports']) ); ?> />
							<span class="description"><strong><?php _e('Title', $this->text_domain) ?></strong></span>
						</label>
						<br />
						<label>
							<input type="checkbox" name="supports[editor]" value="editor" <?php checked(empty($_POST['supports']) || ! empty($_POST['supports']) && in_array('editor', $_POST['supports']) ); ?> />
							<span class="description"><strong><?php _e('Editor', $this->text_domain) ?></strong> - <?php _e('Content', $this->text_domain) ?></span>
						</label>
						<br />
						<label>
							<input type="checkbox" name="supports[author]" value="author" <?php checked( ! empty($_POST['supports']) && in_array('author', $_POST['supports']) ); ?> />
							<span class="description"><strong><?php _e('Author', $this->text_domain) ?></strong></span>
						</label>
						<br />
						<label>
							<input type="checkbox" name="supports[thumbnail]" value="thumbnail" <?php checked( ! empty($_POST['supports']) && in_array('thumbnail', $_POST['supports']) ); ?> />
							<span class="description"><strong><?php _e('Thumbnail', $this->text_domain) ?></strong> - <?php _e('Featured Image - current theme must also support post-thumbnails.', $this->text_domain) ?></span>
						</label>
						<br />
						<label>
							<input type="checkbox" name="supports[excerpt]" value="excerpt" <?php checked( ! empty($_POST['supports']) && in_array('excerpt', $_POST['supports']) ); ?> />
							<span class="description"><strong><?php _e('Excerpt', $this->text_domain) ?></strong></span>
						</label>
						<br />
						<label>
							<input type="checkbox" name="supports[trackbacks]" value="trackbacks" <?php checked( ! empty($_POST['supports']) && in_array('trackbacks', $_POST['supports']) ); ?> />
							<span class="description"><strong><?php _e('Trackbacks', $this->text_domain) ?></strong></span>
						</label>
						<br />
						<label>
							<input type="checkbox" name="supports[custom_fields]" value="custom-fields" <?php checked( ! empty($_POST['supports']) && in_array('custom-fields', $_POST['supports']) ); ?> />
							<span class="description"><strong><?php _e('Custom Fields', $this->text_domain) ?></strong></span>
						</label>
						<br />
						<label>
							<input type="checkbox" name="supports[comments]" value="comments" <?php checked( ! empty($_POST['supports']) && in_array('comments', $_POST['supports']) ); ?> />
							<span class="description"><strong><?php _e('Comments', $this->text_domain) ?></strong> - <?php _e('Also will see comment count balloon on edit screen.', $this->text_domain) ?></span>
						</label>
						<br />
						<label>
							<input type="checkbox" name="supports[revisions]" value="revisions" <?php checked( ! empty($_POST['supports']) && in_array('revisions', $_POST['supports']) ); ?> />
							<span class="description"><strong><?php _e('Revisions', $this->text_domain) ?></strong> - <?php _e('Will store revisions.', $this->text_domain) ?></span>
						</label>
						<br />
						<label>
							<input type="checkbox" name="supports[page_attributes]" value="page-attributes" <?php checked( ! empty($_POST['supports']) && in_array('page-attributes', $_POST['supports']) ); ?> />
							<span class="description"><strong><?php _e('Page Attributes', $this->text_domain) ?></strong> - <?php _e('Template and menu order - Hierarchical must be true!', $this->text_domain) ?></span>
						</label>
						<br />
						<label>
							<input type="checkbox" name="supports[post_formats]" value="post-formats" <?php checked( ! empty($_POST['supports']) && in_array('post-formats', $_POST['supports']) ); ?> />
							<span class="description"><strong><?php _e('Post Formats', $this->text_domain) ?></strong> - <?php _e('Add post formats.', $this->text_domain) ?></span>
						</label>
					</td>
				</tr>
			</table>
		</div>
		<div class="ct-table-wrap">
			<div class="ct-arrow"><br></div>
			<h3 class="ct-toggle"><?php _e('Support Regular Taxonomies', $this->text_domain) ?></h3>
			<table class="form-table supports">
				<tr>
					<th>
						<label><?php _e('Support Regular Taxonomies', $this->text_domain) ?></label>
					</th>
					<td>
						<p><span class="description"><?php _e('Assign regular taxonomies to this Post Type.', $this->text_domain); ?></span></p>
						<?php if ( taxonomy_exists( 'category' ) ): ?>
						<label>
							<input type="checkbox" name="supports_reg_tax[category]" value="1" <?php checked(empty($_POST['supports_reg_tax']['category']), false); ?> />
							<span class="description"><strong><?php _e( 'Categories', $this->text_domain ) ?></strong></span>
						</label>
						<?php endif; ?>
						<br />
						<?php if ( taxonomy_exists( 'post_tag' ) ): ?>
						<label>
							<input type="checkbox" name="supports_reg_tax[post_tag]" value="1" <?php checked(empty($_POST['supports_reg_tax']['post_tag']), false); ?> />
							<span class="description"><strong><?php _e( 'Tags', $this->text_domain ) ?></strong></span>
						</label>
						<?php endif; ?>
					</td>
				</tr>
			</table>
		</div>
		<div class="ct-table-wrap">
			<div class="ct-arrow"><br></div>
			<h3 class="ct-toggle"><?php _e('Capability Type', $this->text_domain) ?></h3>
			<table class="form-table">
				<tr>
					<th>
						<label><?php _e('Capability Type', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="capability_type" value="post">
						<label>
							<input type="checkbox" name="capability_type_edit" value="1" />
							<span class="description ct-capability-type-edit"><strong><?php _e('Edit' , $this->text_domain); ?></strong> (<?php _e('advanced' , $this->text_domain); ?>)</span>
						</label>
						<br /><span class="description"><?php _e('The post type to use for checking read, edit, and delete capabilities. Default: "post".' , $this->text_domain); ?></span>
					</td>
				</tr>
			</table>
		</div>
		
		<div class="ct-table-wrap">
			<div class="ct-arrow"><br></div>
			<h3 class="ct-toggle"><?php _e('Map Meta Capabilities', $this->text_domain) ?></h3>
			<table class="form-table">
				<tr>
					<th>
						<label><?php _e('Map Meta Capabilities', $this->text_domain) ?></label>
					</th>
					<td>
						<p><span class="description"><?php _e('Use the internal default meta capability handling.', $this->text_domain); ?></span></p>
						<label>
							<input type="radio" name="map_meta_cap" value="1" <?php checked( isset($post_type['map_meta_cap']) && $_POST['map_meta_cap'] === true); ?> />
							<span class="description"><strong><?php _e('TRUE', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<label>
							<input type="radio" name="map_meta_cap" value="0" <?php checked(! isset($post_type['map_meta_cap']) || $_POST['map_meta_cap'] === false); ?> />
							<span class="description"><strong><?php _e('FALSE', $this->text_domain); ?></strong> (default)</span>
						</label>
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
						<label><?php _e('Name', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[name]" value="<?php if ( isset( $_POST['labels']['name'] ) ) echo $_POST['labels']['name']; ?>" />
						<br /><span class="description"><?php _e('General name for the post type, usually plural.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label><?php _e('Singular Name', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[singular_name]" value="<?php if ( isset( $_POST['labels']['singular_name'] ) ) echo $_POST['labels']['singular_name']; ?>" />
						<br /><span class="description"><?php _e('Name for one object of this post type. Defaults to value of name.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label><?php _e('Add New', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[add_new]" value="<?php if ( isset( $_POST['labels']['add_new'] ) ) echo $_POST['labels']['add_new']; ?>" />
						<br /><span class="description"><?php _e('The add new text. The default is Add New for both hierarchical and non-hierarchical types.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label><?php _e('Add New Item', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[add_new_item]" value="<?php if ( isset( $_POST['labels']['add_new_item'] ) ) echo $_POST['labels']['add_new_item']; ?>" />
						<br /><span class="description"><?php _e('The add new item text. Default is Add New Post/Add New Page.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label><?php _e('Edit Item', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[edit_item]" value="<?php if ( isset( $_POST['labels']['edit_item'] ) ) echo $_POST['labels']['edit_item']; ?>" />
						<br /><span class="description"><?php _e('The edit item text. Default is Edit Post/Edit Page.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label><?php _e('New Item', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[new_item]" value="<?php if ( isset( $_POST['labels']['new_item'] ) ) echo $_POST['labels']['new_item']; ?>" />
						<br /><span class="description"><?php _e('The new item text. Default is New Post/New Page.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label><?php _e('View Item', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[view_item]" value="<?php if ( isset( $_POST['labels']['view_item'] ) ) echo $_POST['labels']['view_item']; ?>" />
						<br /><span class="description"><?php _e('The view item text. Default is View Post/View Page.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label><?php _e('Search Items', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[search_items]" value="<?php if ( isset( $_POST['labels']['search_items'] ) ) echo $_POST['labels']['search_items']; ?>" />
						<br /><span class="description"><?php _e('The search items text. Default is Search Posts/Search Pages.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label><?php _e('Not Found', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[not_found]" value="<?php if ( isset( $_POST['labels']['not_found'] ) ) echo $_POST['labels']['not_found']; ?>" />
						<br /><span class="description"><?php _e('The not found text. Default is No posts found/No pages found.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label><?php _e('Not Found In Trash', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[not_found_in_trash]" value="<?php if ( isset( $_POST['labels']['not_found_in_trash'] ) ) echo $_POST['labels']['not_found_in_trash']; ?>" />
						<br /><span class="description"><?php _e('The not found in trash text. Default is No posts found in Trash/No pages found in Trash.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label><?php _e('Parent Item Colon', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[parent_item_colon]" value="<?php if ( isset( $_POST['labels']['parent_item_colon'] ) ) echo $_POST['labels']['parent_item_colon']; ?>" />
						<br /><span class="description"><?php _e('The parent text. This string isn\'t used on non-hierarchical types. In hierarchical ones the default is Parent Page', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th>
						<label><?php _e('Custom Fields block', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="labels[custom_fields_block]" value="<?php if ( isset( $_POST['labels']['custom_fields_block'] ) ) echo $_POST['labels']['custom_fields_block']; ?>" />
						<br /><span class="description"><?php _e('Title of Custom Fields block.', $this->text_domain); ?></span>
					</td>
				</tr>
			</table>
		</div>
		<div class="ct-table-wrap">
			<div class="ct-arrow"><br></div>
			<h3 class="ct-toggle"><?php _e('Description', $this->text_domain) ?></h3>
			<table class="form-table">
				<tr>
					<th>
						<label><?php _e('Description', $this->text_domain) ?></label>
					</th>
					<td>
						<textarea class="ct-field-description" name="description" rows="3"><?php if ( isset( $_POST['description'] ) ) echo $_POST['description']; ?></textarea>
						<br /><span class="description"><?php _e('A short descriptive summary of what the post type is.', $this->text_domain); ?></span>
					</td>
				</tr>
			</table>
		</div>
		<div class="ct-table-wrap">
			<div class="ct-arrow"><br></div>
			<h3 class="ct-toggle"><?php _e('Menu Position', $this->text_domain) ?></h3>
			<table class="form-table">
				<tr>
					<th>
						<label><?php _e('Menu Position', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="menu_position" value="<?php if ( isset( $_POST['menu_position'] ) ) echo $_POST['menu_position']; elseif ( !isset( $_POST['menu_position'] ) ) echo '50'; ?>" />
						<br /><span class="description"><?php _e('5 - below Posts; 10 - below Media; 20 - below Pages; 60 - below first separator; 100 - below second separator', $this->text_domain); ?></span>
					</td>
				</tr>
			</table>
		</div>
		<div class="ct-table-wrap">
			<div class="ct-arrow"><br></div>
			<h3 class="ct-toggle"><?php _e('Menu Icon', $this->text_domain) ?></h3>
			<table class="form-table">
				<tr>
					<th>
						<label><?php _e('Menu Icon', $this->text_domain) ?></label>
					</th>
					<td>
						<input type="text" name="menu_icon" value="<?php if ( isset( $_POST['menu_icon'] ) ) echo $_POST['menu_icon']; ?>" />
						<br /><span class="description"><?php _e('The url to the icon to be used for this menu.', $this->text_domain); ?></span>
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
						<label><?php _e('Public', $this->text_domain) ?></label>
					</th>
					<td>
						<span class="description"><?php _e('Meta argument used to define default values for publicly_queriable, show_ui, show_in_nav_menus and exclude_from_search.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<label>
							<input type="radio" name="public" value="1"  <?php checked( ! isset($_POST['public']) ||  (isset($_POST['public']) && $_POST['public'] === '1')); ?> />
							<span class="description"><strong><?php _e('TRUE', $this->text_domain); ?></strong>
							</label>
							<br />
							<?php _e('Display a user-interface for this "post_type"', $this->text_domain);?><br /><code>( show_ui = TRUE )</code><br /><br />
							<?php _e('Show "post_type" for selection in navigation menus', $this->text_domain); ?><br /><code>( show_in_nav_menus = TRUE )</code><br /><br />
							<?php _e('"post_type" queries can be performed from the front-end', $this->text_domain); ?><br /><code>( publicly_queryable = TRUE )</code><br /><br />
							<?php _e('Exclude posts with this post type from search results', $this->text_domain); ?><br /> <code>( exclude_from_search = FALSE )</code>
						</span>
						<br /><br />
						<label>
							<input type="radio" name="public" value="0" <?php checked( isset($_POST['public'])  && $_POST['public'] === '0'); ?> />
							<span class="description"><strong><?php _e('FALSE', $this->text_domain); ?></strong>
							</label>
							<br />
							<?php _e('Don not display a user-interface for this "post_type"', $this->text_domain);?><br /><code>( show_ui = FALSE )</code><br /><br />
							<?php _e('Hide "post_type" for selection in navigation menus', $this->text_domain); ?><br /><code>( show_in_nav_menus = FALSE )</code><br /><br />
							<?php _e('"post_type" queries cannot be performed from the front-end', $this->text_domain); ?><br /><code>( publicly_queryable = FALSE )</code><br /><br />
							<?php _e('Exclude posts with this post type from search results', $this->text_domain); ?><br /> <code>( exclude_from_search = TRUE )</code>
						</span>
						<br /><br />
						<label>
							<input type="radio" name="public" value="advanced" <?php checked(isset($_POST['public']) && $_POST['public'] =='advanced'); ?> />
							<span class="description"><strong><?php _e('Advanced', $this->text_domain); ?></strong>
							</label>
							<br />
						<?php _e('You can set each component manualy.', $this->text_domain); ?></span>
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
						<label><?php _e('Show UI', $this->text_domain) ?></label>
					</th>
					<td>
						<span class="description"><?php _e('Whether to generate a default UI for managing this post type.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<label>
							<input type="radio" name="show_ui" value="1" <?php checked( isset( $_POST['public'] ) && $_POST['public'] == 'advanced' && isset( $_POST['show_ui'] ) && $_POST['show_ui'] === '1'); ?> />
							<span class="description"><strong><?php _e('TRUE', $this->text_domain); ?></strong>
							</label>
							<br />
						<?php _e('Display a user-interface (admin panel) for this post type.', $this->text_domain); ?></span>
						<br />
						<label>
							<input type="radio" name="show_ui" value="0" <?php checked( isset( $_POST['public'] ) && $_POST['public'] == 'advanced' && isset( $_POST['show_ui'] ) && $_POST['show_ui'] === '0' ); ?> />
							<span class="description"><strong><?php _e('FALSE', $this->text_domain); ?></strong>
							</label>
							<br />
						<?php _e('Do not display a user-interface for this post type.', $this->text_domain); ?></span>
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
						<label><?php _e('Show In Nav Menus', $this->text_domain) ?></label>
					</th>
					<td>
						<span class="description"><?php _e('Whether post_type is available for selection in navigation menus.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<label>
							<input type="radio" name="show_in_nav_menus" value="1" <?php checked( isset( $_POST['public'] ) && $_POST['public'] == 'advanced' && isset( $_POST['show_in_nav_menus'] ) && $_POST['show_in_nav_menus'] === '1' ); ?> />
							<span class="description"><strong><?php _e('TRUE', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<label>
							<input type="radio" name="show_in_nav_menus" value="0" <?php checked( isset( $_POST['public'] ) && $_POST['public'] == 'advanced' && isset( $_POST['show_in_nav_menus'] ) && $_POST['show_in_nav_menus'] === '0' ); ?> />
							<span class="description"><strong><?php _e('FALSE', $this->text_domain); ?></strong></span>
						</label>
					</td>
				</tr>
			</table>
		</div>
		<div class="ct-table-wrap">
			<div class="ct-arrow"><br></div>
			<h3 class="ct-toggle"><?php _e('Publicly Queryable', $this->text_domain) ?></h3>
			<table class="form-table public-queryable">
				<tr>
					<th>
						<label><?php _e('Publicly Queryable', $this->text_domain ) ?></label>
					</th>
					<td>
						<span class="description"><?php _e('Whether post_type queries can be performed from the front end.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<label>
							<input type="radio" name="publicly_queryable" value="1" <?php checked( isset( $_POST['public'] ) && $_POST['public'] == 'advanced' && isset( $_POST['publicly_queryable'] ) && $_POST['publicly_queryable'] === '1' ); ?> />
							<span class="description"><strong><?php _e('TRUE', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<label>
							<input type="radio" name="publicly_queryable" value="0" <?php checked( isset( $_POST['public'] ) && $_POST['public'] == 'advanced' && isset( $_POST['publicly_queryable'] ) && $_POST['publicly_queryable'] === '0' ); ?> />
							<span class="description"><strong><?php _e('FALSE', $this->text_domain); ?></strong></span>
						</label>
					</td>
				</tr>
			</table>
		</div>
		<div class="ct-table-wrap">
			<div class="ct-arrow"><br></div>
			<h3 class="ct-toggle"><?php _e('Exclude From Search', $this->text_domain) ?></h3>
			<table class="form-table exclude-from-search">
				<tr>
					<th>
						<label><?php _e('Exclude From Search', $this->text_domain) ?></label>
					</th>
					<td>
						<span class="description"><?php _e('Whether to exclude posts with this post type from search results.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<label>
							<input type="radio" name="exclude_from_search" value="1" <?php checked( isset( $_POST['public'] ) && $_POST['public'] == 'advanced' && isset( $_POST['exclude_from_search'] ) && $_POST['exclude_from_search'] === '1' ); ?> />
							<span class="description"><strong><?php _e('TRUE', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<label>
							<input type="radio" name="exclude_from_search" value="0" <?php checked( isset( $_POST['public'] ) && $_POST['public'] == 'advanced' && isset( $_POST['exclude_from_search'] ) && $_POST['exclude_from_search'] === '0' ); ?> />
							<span class="description"><strong><?php _e('FALSE', $this->text_domain); ?></strong></span>
						</label>
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
						<label><?php _e('Hierarchical', $this->text_domain) ?></label>
					</th>
					<td>
						<span class="description"><?php _e('Whether the post type is hierarchical. Allows Parent to be specified.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<label>
							<input type="radio" name="hierarchical" value="1" <?php checked( ! empty($_POST['hierarchical']) || (isset( $_POST['hierarchical'] ) && $_POST['hierarchical'] === '1') ); ?> />
							<span class="description"><strong><?php _e('TRUE', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<label>
							<input type="radio" name="hierarchical" value="0" <?php checked( empty($_POST['hierarchical']) || (isset( $_POST['hierarchical'] ) && $_POST['hierarchical'] === '0') ); ?> />
							<span class="description"><strong><?php _e('FALSE', $this->text_domain); ?></strong></span>
						</label>
					</td>
				</tr>
			</table>
		</div>
		<div class="ct-table-wrap">
			<div class="ct-arrow"><br></div>
			<h3 class="ct-toggle"><?php _e('Has Archive', $this->text_domain) ?></h3>
			<table class="form-table">
				<tr>
					<th>
						<label><?php _e('Has Archive', $this->text_domain) ?></label>
					</th>
					<td>
						<span class="description"><?php _e('Enables post type archives. Will use string as archive slug. Will generate the proper rewrite rules if rewrite is enabled.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<label>
							<input type="radio" name="has_archive" value="1" <?php checked( ! empty($_POST['has_archive']) || (isset( $_POST['has_archive'] ) && $_POST['has_archive'] === '1') ); ?> />
							<span class="description"><strong><?php _e('TRUE', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<label>
							<input type="radio" name="has_archive" value="0" <?php checked( empty($_POST['has_archive']) || (isset( $_POST['has_archive'] ) && $_POST['has_archive'] === '0') ); ?> />
							<span class="description"><strong><?php _e('FALSE', $this->text_domain); ?></strong></span>
						</label>
						<br /><br />
						<span class="description"><strong><?php _e('Custom Slug', $this->text_domain); ?></strong></span>
						<br />

						<input type="text" name="has_archive_slug" value="<?php if ( ! empty( $_POST['has_archive_slug'] ) ) echo $_POST['has_archive_slug']; ?>" />
						<br />
						<span class="description"><?php _e('Custom slug for post type archive.', $this->text_domain); ?></span>
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
						<label><?php _e('Rewrite', $this->text_domain) ?></label>
					</th>
					<td>
						<span class="description"><?php _e('Rewrite permalinks with this format.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<label>
							<input type="radio" name="rewrite" value="1" <?php checked(empty($_POST['rewrite']) || (isset( $_POST['rewrite'] ) && $_POST['rewrite'] === '1') ); ?> />
							<span class="description"><strong><?php _e('TRUE', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<span class="description"><?php _e('Default will use post type.', $this->text_domain); ?></span>
						<br />
						<label>
							<input type="radio" name="rewrite" value="0" <?php checked( isset( $_POST['rewrite'] ) &&  $_POST['rewrite'] === '0' ); ?> />
							<span class="description"><strong><?php _e('FALSE', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<span class="description"><?php _e('Prevent rewrite.', $this->text_domain); ?></span>
						<br /><br />

						<span class="description"><strong><?php _e('Custom Slug', $this->text_domain); ?></strong></span>
						<br />
						<input type="text" name="rewrite_slug" value="<?php if ( ! empty($_POST['rewrite_slug'])) echo $_POST['rewrite_slug']; ?>" />
						<br />
						<span class="description"><?php _e('Prepend posts with this slug. If empty default will be used.', $this->text_domain); ?></span>
						<br /><br />
						<label>
							<input type="checkbox" name="rewrite_with_front" value="1" <?php checked( ! isset($_POST['rewrite_with_front']) || ! empty( $_POST['rewrite_with_front'] )); ?> />
							<span class="description"><strong><?php _e('Allow Front Base', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<span class="description"><?php _e('Allowing permalinks to be prepended with front base.', $this->text_domain); ?></span>
						<br /><br />
						<label>
							<input type="checkbox" name="rewrite_feeds" value="1" <?php checked( ! empty( $_POST['rewrite_feeds'] ) ); ?> />
							<span class="description"><strong><?php _e('Feeds', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<span class="description"><?php _e('Default will use has_archive.', $this->text_domain); ?></span>
						<br />
						<label>
							<input type="checkbox" name="rewrite_pages" value="1" <?php checked( ! isset($_POST['rewrite_pages']) || ! empty( $_POST['rewrite_pages'] ) ); ?> />
							<span class="description"><strong><?php _e('Pages', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<span class="description"><?php _e('Defaults to true.', $this->text_domain); ?></span>
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
						<label><?php _e('Query var', $this->text_domain) ?></label>
					</th>
					<td>
						<span class="description"><?php _e('Can queries be performed on this post_type.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<label>
							<input type="radio" name="query_var" value="1" <?php checked( !isset($_POST['query_var']) || ! empty($_POST['query_var'])); ?> />
							<span class="description"><strong><?php _e('TRUE', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<label>
							<input type="radio" name="query_var" value="0" <?php checked(isset($_POST['query_var']) && empty($_POST['query_var'])); ?> />
							<span class="description"><strong><?php _e('FALSE', $this->text_domain); ?></strong></span>
						</label>
					</td>
				</tr>
			</table>
		</div>
		<div class="ct-table-wrap">
			<div class="ct-arrow"><br></div>
			<h3 class="ct-toggle"><?php _e('Can Export', $this->text_domain) ?></h3>
			<table class="form-table">
				<tr>
					<th>
						<label><?php _e('Can Export', $this->text_domain) ?></label>
					</th>
					<td>
						<span class="description"><?php _e('Can this post_type be exported.', $this->text_domain); ?></span>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<label>
							<input type="radio" name="can_export" value="1" <?php checked( !isset($_POST['can_export']) || ! empty($_POST['can_export'])); ?> />
							<span class="description"><strong><?php _e('TRUE', $this->text_domain); ?></strong></span>
						</label>
						<br />
						<label>
							<input type="radio" name="can_export" value="0" <?php checked(isset($_POST['can_export']) && empty($_POST['can_export'])); ?> />
							<span class="description"><strong><?php _e('FALSE', $this->text_domain); ?></strong></span>
						</label>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<p class="submit">
		<?php wp_nonce_field('submit_post_type'); ?>
		<input type="submit" class="button-primary" name="submit" value="<?php _e('Add Post Type', $this->text_domain); ?>" />
	</p>
	<br /><br /><br /><br />
</form>
