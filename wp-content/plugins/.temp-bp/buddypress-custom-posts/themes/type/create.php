<?php
get_header();

//Setup globals for the edit form.
global $bp, $post_ID, $post, $post_type, $post_type_object;

$post_ID = 0;
$post_type_object = bpcp_get_post_type_object();
$post_type = $bp->{$bp->active_components[$bp->current_component]}->id;
$post = false;

?>
	<div id = 'content'>
		<div class = 'padder'>
			<h3>
				<?php bpcp_create_post_title(); ?>
				<a class = 'button' href = '<?php echo bp_get_root_domain() . '/' . $bp->current_component . '/'; ?>'><?php echo $post_type_object->labels->posts_directory; ?></a>
			</h3>
			<div class = 'item-body'>
				<?php bpcp_locate_template( Array( 'type/includes/edit-form.php' ), true ); ?>
			</div> <!-- /item-body -->
		</div> <!-- /.padder -->

	</div> <!-- /#content -->	

	<?php locate_template( Array( 'sidebar.php' ), true );

get_footer();
