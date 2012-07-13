<?php
/**
 * BP Links admin list categories
 */
?>

<?php if ( isset( $message ) ) { ?>
	<div id="message" class="<?php echo $message_type ?> fade">
		<p><?php echo $message ?></p>
	</div>
<?php } ?>


<div class="wrap nosubsub buddypress-links-admin-content" style="position: relative">
	<div id="icon-edit" class="icon32"><br /></div>
	<h2><?php _e( 'BuddyPress Links', 'buddypress-links' ) ?>: <?php _e( 'Edit Categories', 'buddypress-links' ) ?></h2>

	<form id="search-form" method="post" action="">
		<p class="search-box">
			<label class="screen-reader-text" for="link-category-search-input">Search Categories:</label>
			<input type="text" id="link-category-search-input" value="<?php echo attribute_escape( stripslashes( $_REQUEST['s'] ) ); ?>" name="s" />
			<input id="submit" class="button" type="submit" value="<?php _e( 'Search Categories', 'buddypress-links' ) ?>" />
		</p>
	</form>

	<?php if ( bp_has_links_categories( 'type=recently-active&per_page=10' ) ) : ?>
		<form id="bp-category-admin-list" method="post" action="">
			<div class="tablenav">
				<div class="tablenav-pages">
					<?php bp_links_categories_pagination_count() ?> <?php bp_links_categories_pagination_links() ?>
				</div>
				<div class="alignleft">
					<input class="button-secondary delete" type="submit" name="categories_admin_delete" value="<?php _e( 'Delete', 'buddypress-links' ) ?>" onclick="if ( !confirm('<?php _e( 'Are you sure?', 'buddypress-links' ) ?>') ) return false"/>
					<?php wp_nonce_field('bp-links-categories-admin') ?>
					<br class="clear"/>
				</div>
			</div>

			<br class="clear"/>

			<?php if ( isset( $_REQUEST['s'] ) && $_REQUEST['s'] != '' ) { ?>
				<p><?php echo sprintf( '%1$s &quot;%2$s&quot;', __( 'Categories matching:', 'buddypress-links' ), $_REQUEST['s'] ) ?></p>
			<?php } ?>


			<table class="widefat" cellspacing="3" cellpadding="3">
				<thead>
					<tr>
						<th class="check-column" scope="col">
							<input id="category_check_all" type="checkbox" value="0" name="category_check_all" onclick="if ( jQuery(this).attr('checked') ) { jQuery('#category-list input[@type=checkbox]').attr('checked', 'checked'); } else { jQuery('#category-list input[@type=checkbox]').attr('checked', ''); }" />
						</th>
						<th scope="col">
								ID
						</th>
						<th scope="col">
								<?php _e( 'Name', 'buddypress-links' ) ?>
						</th>
						<th scope="col">
								<?php _e( 'Description', 'buddypress-links' ) ?>
						</th>
						<th scope="col">
								<?php _e( 'Slug', 'buddypress-links' ) ?>
						</th>
						<th scope="col">
								<?php _e( 'Priority', 'buddypress-links' ) ?>
						</th>
						<th scope="col">
								<?php _e( 'Links', 'buddypress-links' ) ?>
						</th>
						<th scope="col">
								<?php _e( 'Created', 'buddypress-links' ) ?>
						</th>
						<th scope="col">
								<?php _e( 'Updated', 'buddypress-links' ) ?>
						</th>
						<th scope="col">
						</th>
					</tr>
				</thead>
				<tbody id="category-list" class="list:categories category-list">
				<?php $counter = 0 ?>
				<?php while ( bp_links_categories() ) : bp_links_categories_category(); ?>
					<tr<?php if ( 1 == $counter % 2 ) { ?> class="alternate"<?php }?>>
						<th class="check-column" scope="row">
							<input id="category_<?php bp_links_categories_category_id() ?>" type="checkbox" value="<?php bp_links_categories_category_id() ?>" name="allcategories[<?php bp_links_categories_category_id() ?>]" />
						</th>
						<td><?php bp_links_categories_category_id() ?></td>
						<td><?php bp_links_categories_category_name() ?></td>
						<td><?php bp_links_categories_category_description() ?></td>
						<td><?php bp_links_categories_category_slug() ?></td>
						<td><?php bp_links_categories_category_priority() ?></td>
						<td><?php bp_links_categories_category_link_count() ?></td>
						<td><?php bp_links_categories_category_date_created() ?></td>
						<td><?php bp_links_categories_category_date_updated() ?></td>
						<td><a href="?page=buddypress-links-admin-cats&category_id=<?php bp_links_categories_category_id() ?>"><?php _e( 'Edit', 'buddypress-links') ?></a></td>
					</tr>
					<?php $counter++ ?>
				<?php endwhile; ?>
				</tbody>
			</table>

	<?php else: ?>

		<div id="message" class="info">
			<p><?php _e( 'No categories found.', 'buddypress-links' ) ?></p>
		</div>

	<?php endif; ?>

	<?php bp_links_categories_hidden_fields() ?>
	</form>

	<form action="admin.php" method="get">
		<p class="submit">
			<input type="hidden" name="page" value="buddypress-links-admin-cats" />
			<input type="hidden" name="category_id" value="" />
			<input type="submit" class="button" value="<?php _e( 'New Category','buddypress-links' ) ?>" />
		</p>
	</form>
</div>