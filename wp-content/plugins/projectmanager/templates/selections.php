<?php
/**
Template page for selections. Loaded by do_action('projectmanager_selections')

The following variables are usable:

	$orderby: contains array of possible order options
	$order: contains array of possible directions of ordering (ascending, descending)
	$category: controls category dropdown, either false or contains category
	$selected_cat: currently selected category
	
	You can check the content of a variable when you insert the tag <?php var_dump($variable) ?>
*/
?>
<?php if ( $category || $orderby || $order ) : ?>

<div class='projectmanager_selections'>
<form action='<?php the_permalink() ?>' method='get'>
<div>
	<input type='hidden' name='page_id' value='<?php the_ID() ?>' />
	<?php if ( $category ) : ?>
	<?php wp_dropdown_categories(array('echo' => 1, 'hide_empty' => 0, 'hide_if_empty' => 1, 'name' => 'cat_id', 'orderby' => 'name', 'selected' => $selected_cat, 'hierarchical' => true, 'child_of' => $category, 'show_option_all' => __('View all categories'))); ?>
	<?php endif; ?>
	<?php if ( $orderby ) : ?>
	<select size='1' name='orderby'>
		<?php foreach ( $orderby AS $key => $value ) : ?>
		<?php $orderby_request = isset($_GET['orderby']) ? $_GET['orderby'] : '' ?>
		<option value='<?php echo $key ?>' <?php if ($orderby_request == $key) echo ' selected="selected"' ?>><?php echo $value ?></option>
		<?php endforeach; ?>
	</select>
	<?php endif; ?>
	<?php if ( $order ) : ?>
	<select size='1' name='order'>
		<?php foreach ( $order AS $key => $value ) : ?>
		<?php $order_request = isset($_GET['order']) ? $_GET['order'] : '' ?>
		<option value='<?php echo $key ?>' <?php if ($order_request == $key) echo ' selected="selected"' ?>><?php echo $value ?></option>
		<?php endforeach; ?>
	</select>
	<?php endif; ?>
	<input type='submit' value='<?php _e( 'Apply' ) ?>' class='button' />
</div>
</form>
</div>

<?php endif; ?>
