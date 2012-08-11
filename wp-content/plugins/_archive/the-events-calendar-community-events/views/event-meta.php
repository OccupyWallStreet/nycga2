<?php

// Don't load directly
if ( !defined( 'ABSPATH' ) ) {
   die('-1');
}

/**
 * The Event Meta Form for editing custom fields
 * You can customize this view by putting a replacement file of the same name (delete.php) in the events/community/ directory of your theme.
 *
 * @package TribeCommunityEvents
 * @since  1.0
 * @author Modern Tribe Inc.
 */


if ( empty( $customFields ) || !is_array( $customFields ) ) {
	return;
}?>
<table id='event-meta' class='eventtable'>
   <tbody>
      <tr><td colspan='2' class='snp_sectionheader'><h4><?php _e( 'Event Custom Fields', 'tribe-events-community' ); ?></h4></td></tr>
      <?php
      foreach ( $customFields as $customField ) : ?>
         <?php
         if ( isset($_POST[ $customField['name'] ]) ) {
         	$val = $_POST[ $customField['name'] ];
         } else {
         	$val = get_post_meta( get_the_id(), $customField['name'], true );
         }
         ?>
         <tr>
            <td><?php echo esc_html( stripslashes( $customField['label'] ) ) ?></td>
            <td>
               <?php $options = explode( "\n", $customField['values'] ) ?>
               <?php if($customField['type'] == 'text'): ?>
                  <input type='text' name='<?php echo esc_attr( $customField['name'] ) ?>' value='<?php echo esc_attr( $val ) ?>'/>
               <?php elseif ( $customField['type'] == 'radio') : ?>
                  <?php foreach ( $options as $option ) : ?>
                     <div><label><input type='radio' name='<?php echo esc_attr( stripslashes( $customField['name'] ) ) ?>' value='<?php echo esc_attr( $option ) ?>' <?php checked( trim( $val ), trim( $option ) ) ?>/> <?php echo esc_html( stripslashes( $option ) ) ?></label></div>
                  <?php endforeach ?>
               <?php elseif ($customField['type'] == 'checkbox') : ?>
                  <?php foreach ( $options as $option ) : ?>
                     <?php $values = explode( '|', $val ); ?>
                     <div><label><input type='checkbox' value='<?php echo esc_attr( trim( $option ) ) ?>' <?php checked( in_array( trim( $option ), $values ) ) ?> name='<?php echo esc_attr( stripslashes( $customField['name'] ) ) ?>[]'/> <?php echo esc_html( stripslashes( $option ) ) ?></label></div>
                  <?php endforeach ?>
               <?php elseif($customField['type'] == 'dropdown'): ?>
                  <select name='<?php echo $customField['name']?>'>
                     <?php $options = explode( "\n", $customField['values'] ) ?>
                     <?php foreach ($options as $option): ?>
							<option value='<?php echo esc_attr( $option ) ?>' <?php selected( trim( $val ), trim( $option ) ) ?>><?php echo esc_html( stripslashes( $option ) ) ?></option>
                     <?php endforeach ?>
                  </select>
               <?php elseif ( $customField['type'] == 'textarea') : ?>
                  <textarea name='<?php echo esc_attr( $customField['name'] ) ?>'><?php echo esc_textarea( stripslashes( $val ) ) ?></textarea>
               <?php endif; ?>
           </td>
         </tr>
      <?php endforeach; ?>
   </tbody>
</table>
