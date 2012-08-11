<?php 
global $post;
wp_nonce_field( 'os_update_slidemeta', 'orbit_slider_noncename' ); 

// Setup form data
$caption = get_post_meta($post->ID, '_sliderCaption', TRUE);
$url = get_post_meta($post->ID, '_sliderUrl', TRUE);
$target = get_post_meta($post->ID, '_sliderTarget', TRUE);
$selected = ' selected="selected"';
?>


<!-- Captions -->
  <p><?php _e('Caption text. This will be overridden if content is inserted in main editor above.', 'wp-orbit-slider'); ?></p>
  <p>
    <textarea type="text" id= "_sliderCaption" cols="71" rows="10" name="_sliderCaption"><?php if(!empty($caption)) echo $caption; ?></textarea>
  </p>
<p>As default, the slides will not link anywhere. If you wish to include a link ensure to include ( http:// )</p>    
<!-- URL Link -->
    <label for="_sliderUrl"><?php _e('URL Link:', 'wp-orbit-slider');?> </label>
    <input type="text" id= "_sliderUrl" name="_sliderUrl" value="<?php if(!empty($url)) echo $url; ?>" size="45" />

<!-- URL Target -->
    <label for="_sliderTarget"><?php _e('Target:', 'wp-orbit-slider');?> </label>
    <select name="_sliderTarget">
      <option value=""<?php if( empty($target) ) echo $selected; ?>><?php _e('-- Choose One --', 'wp-orbit-slider'); ?></option>
      <option value="_self"<?php if( $target == '_self' ) echo $selected; ?>><?php _e('Open link in same page', 'wp-orbit-slider'); ?></option>
      <option value="_blank"<?php if( $target == '_blank' ) echo $selected; ?>><?php _e('Open link in a new page', 'wp-orbit-slider'); ?></option>
    </select>



