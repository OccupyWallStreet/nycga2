<?php 
	global $bp;
	$type = bpcp_get_post_type_object();

	get_header(); 
?>

<div id = 'content'>
	<div class = 'padder'>
		<h3>
			<?php echo $type->labels->posts_directory; ?>
			<?php if ( is_user_logged_in() && current_user_can( $type->cap->publish_posts ) ) { ?>
				<a class = 'button' href = '<?php echo bp_get_root_domain() . '/' . $bp->current_component . '/create/'; ?>'><?php echo $type->labels->add_new_item; ?>
				</a>
			<?php } ?>
		</h3>
	
		<div id = '<?php echo $bp->current_component; ?>-dir-search' class = 'dir-search'>
			<?php bpcp_directory_search_form(); ?>
		</div><!-- /#current-component-dir-search -->

		<form action = '<?php echo $_SERVER['REQUEST_URI']; ?>' method = 'POST' id = '<?php echo $bp->current_component; ?>-directory-form' class = 'dir-form'>
		<div class = 'item-list-tabs'>
			<ul>
				<li class = 'selected' id = '<?php echo $bp->current_component; ?>-all'>
					<a href = '<?php echo bp_get_root_domain() . "/" . $bp->current_component; ?>' >
						<?php printf( $type->labels->all_posts, bpcp_get_total_count() ); ?>
					</a>
				</li>

				<?php if ( is_user_logged_in() && bpcp_get_user_count( bp_loggedin_user_id(), $type->id, 'readable' ) ) { ?>
				<li id = '<?php echo $bp->current_component; ?>-personal'>
					<a href = '<?php echo bp_loggedin_user_domain() . $bp->current_component . '/' . $type->slugs->my_posts . '/'; ?>'>
						<?php 	$stats = bpcp_get_user_count( bp_loggedin_user_id(), $type->id, 'readable' ); 
							printf( $type->labels->my_posts, $stats->publish ); ?>
					</a>
				</li>
				<?php } ?>
				<li id="<?php echo $bp->current_component; ?>-order-select" class="last filter">
					<?php _e( 'Order By:' ) ?>
					<select>
						<option value="active"><?php _e( 'Last Active', 'buddypress' ) ?></option>
						<option value="newest"><?php _e( 'Newly Created', 'buddypress' ) ?></option>
						<option value="alphabetical"><?php _e( 'Alphabetical', 'buddypress' ) ?></option>
						<?php do_action( 'bpcp_type_directory_options', $type->ID ); ?>
						<?php do_action( 'bpcp_' . $type->id . '_directory_options' ); ?>
					</select>
				</li>
			</ul>
		</div><!-- /.item-list-tabs -->

		<div id="<?php echo $bp->current_component; ?>-dir-list" class="<?php echo $bp->current_component; ?> dir-list">
			<?php bpcp_locate_template( Array( 'type/type-loop.php' ), true ); ?>
		</div><!-- #groups-dir-list --> 

		<?php wp_nonce_field( 'directory_' . $bp->current_component, '_wpnonce-' . $bp->current_component . '-filter' ) ?>

		</form><!-- /#current-component-directory-form -->
	</div><!-- /.padder -->
</div><!-- /content -->

<?php locate_template( Array( 'sidebar.php' ), true ); ?>

<?php get_footer(); ?>
