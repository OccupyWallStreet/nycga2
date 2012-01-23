<?php 

if ( !class_exists('q2w3_post_order', false) ) die(); // loads only inside a plugin

require_once('table-filters.php');

echo '<h3>';
		  	
if ($post_type) {
	
	$pt = get_post_type_object($post_type);
	
	echo $pt->labels->name;
	
} elseif ($tax_name) {

	echo q2w3_post_order_term_name($term_id, $tax_name) .' <a href="?page='. $_GET['page'] .'&amp;tax_name='. $tax_name .'" style="text-decoration: none" title="'. __('Return to terms list', self::ID) .'">[...]</a>';
	
}

echo '</h3>'.PHP_EOL;

?>
<p><strong><?php _e('Sorted', self::ID) ?>:</strong></p>
<form method="post" action="<?php echo self::action_url() ?>">
<input type="hidden" name="action" value="update_sorted" />
<input type="hidden" name="term_id" value="<?php echo $term_id ?>" />
<input type="hidden" name="wp_nonce" value="<?php echo wp_create_nonce(self::ID.'_post') ?>"/>
<table class="widefat">
	<thead>
      <tr>
        <th scope="col" style="width: 55px"><?php _e('Position', self::ID) ?></th>
        <th scope="col" style="width: 55px; text-align: center"><?php _e('ID', self::ID) ?></th>
        <th scope="col"><?php _e('Title', self::ID) ?></th>
        <th scope="col" style="width: 100px"><?php _e('Change position', self::ID) ?></th>
      </tr>
    </thead>
    <tbody id="asc-list">
<?php $exclude_ids = self::list_posts(self::get_sorted_posts($term_id, $tax_name, $post_type), $post_type, $tax_name); ?>
	</tbody>
</table>
<br/>
<input type="submit" value="<?php _e('Update Sorted', self::ID) ?>" class="button-primary" />
</form>

<br/>

<p><strong><?php _e('Unsorted', self::ID) ?>:</strong></p>
<div class="tablenav">
<?php 
echo q2w3_post_order_table_search::controls();

if (!$settings['rows_per_page']) $rpp = 20; else $rpp = $settings['rows_per_page'];
$total_posts = self::get_unsorted_posts($term_id, $tax_name, $post_type, $exclude_ids, true);
$total_posts = $total_posts[0]['total'];
echo q2w3_post_order_table_paging::controls($total_posts, $rpp) 
?>
</div>
<form method="post" action="<?php echo self::action_url() ?>">
<input type="hidden" name="action" value="update_unsorted" />
<input type="hidden" name="term_id" value="<?php echo $term_id ?>" />
<input type="hidden" name="wp_nonce" value="<?php echo wp_create_nonce(self::ID.'_post') ?>"/>
<table class="widefat">
	<thead>
      <tr>
        <th scope="col" style="width: 55px"><?php _e('Position', self::ID) ?></th>
        <th scope="col" style="width: 55px; text-align: center"><?php _e('ID', self::ID) ?></th>
        <th scope="col"><?php _e('Title', self::ID) ?></th>
        <th scope="col" style="width: 100px"><?php _e('Change position', self::ID) ?></th>
      </tr>
    </thead>
    <tbody id="asc-list">
<?php self::list_posts(self::get_unsorted_posts($term_id, $tax_name, $post_type, $exclude_ids), $post_type, $tax_name); ?>
    </tbody>
</table>
<div class="tablenav">
<?php echo q2w3_post_order_table_paging::controls($total_posts, $rpp) ?>
</div>
<input type="submit" value="<?php _e('Update Unsorted', self::ID) ?>" class="button-primary" />
</form>

<?php 

function q2w3_post_order_term_name($term_id, $tax_name) {
	
	$terms_array = q2w3_post_order_term_array($term_id, $tax_name);
	
	return implode(' &bull; ', $terms_array);

}

function q2w3_post_order_term_array($term_id, $tax_name) {

	$term = get_term($term_id, $tax_name);
		
	if ($term_id == $_GET['term_id']) {
			
		$link = $term->name;
			
	} else {
			
		$link = '<a href="?page='. $_GET['page'] .'&amp;tax_name='. $tax_name .'&amp;term_id='. $term_id .'" style="text-decoration: none">'. $term->name .'</a>';
			
	}
		
	$array[] = $link;
	
	if ($term->parent) $array = array_merge(q2w3_post_order_term_array($term->parent, $tax_name), $array);
	
	return $array;

}

?>