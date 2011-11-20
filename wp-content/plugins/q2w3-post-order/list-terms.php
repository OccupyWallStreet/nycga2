<?php 

if ( !class_exists('q2w3_post_order', false) ) die(); // loads only inside a plugin

$taxonomy = get_taxonomy($tax_name);

?>
<!-- <form method="post" action="<?php echo self::action_url() ?>">
<input type="hidden" name="action" value="update_meta" />
<input type="hidden" name="tax_name" value="<?php echo $tax_name ?>" />
<input type="hidden" name="wp_nonce" value="<?php echo wp_create_nonce(self::ID.'_post') ?>"/>-->

<h3><?php echo $taxonomy->labels->name; ?></h3>

<table class="widefat">
  <thead>
	<tr>
	  <th scope="col"><?php _e('ID', self::ID) ?></th>
	  <th scope="col"><?php _e('Name', self::ID) ?></th>
	  <th scope="col" width="90" style="text-align: center"><?php _e('Sorted / Total', self::ID) ?></th>
	</tr>
  </thead>
  <tbody id="the-list">
  <?php self::list_terms($tax_name, '0', 0, $taxonomy->hierarchical); ?>
  </tbody>
</table>
<!-- <br/>
<input type="submit" value="<?php _e('Update Meta', self::ID) ?>" class="button-primary" />
</form> -->