<?php if( $edit ) : ?>
<tr class="form-field">
  <th scope="row" valign="top">
    <label for="tag-color">
  	  <?php _e( 'Category Color', AI1EC_PLUGIN_NAME ) ?>
  	</label>
  </th>
  <td>
    <div id="tag-color">
      <div id="tag-color-background"<?php echo $style ?>></div>
  	</div>
  	<input type="hidden" name="tag-color-value" id="tag-color-value" value="<?php echo $color ?>" />
  	<p class="description"><?php _e( 'Events in this category will be identified by this color', AI1EC_PLUGIN_NAME ) ?>.</p>
  </td>
</tr>
<?php else : ?>
  <div class="form-field">
  	<label for="tag-color">
  	  <?php _e( 'Category Color', AI1EC_PLUGIN_NAME ) ?>
  	</label>
  	<div id="tag-color">
      <div id="tag-color-background"></div>
  	</div>
  	<input type="hidden" name="tag-color-value" id="tag-color-value" value="" />
  	<p><?php _e( 'Events in this category will be identified by this color', AI1EC_PLUGIN_NAME ) ?>.</p>
  </div>
<?php endif; ?>
