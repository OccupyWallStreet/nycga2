<?php

	global $bp;
	$type_object = get_post_type_object( $bp->active_components[ $bp->current_component ] );

?>

<div class = 'bpcp-container'>
	<h2 id = 'edit-item'><?php echo $type_object->labels->edit_item; ?></h2>
	<?php bpcp_locate_template( Array( 'type/includes/edit-form.php' ), true ); ?>
</div>
