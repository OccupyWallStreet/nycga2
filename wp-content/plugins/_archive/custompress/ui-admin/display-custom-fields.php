<?php if (!defined('ABSPATH')) die('No direct access allowed!'); ?>

<?php
global $post;

if ( $type == 'local' )
$custom_fields = $this->custom_fields;
elseif ( $type == 'network' )
$custom_fields = get_site_option('ct_custom_fields');

$output = false;

?>

<div class="form-wrap">
	<div class="form-field form-required ct-form-field">
		<input type="hidden" id="ct_custom_fields_form" value="" />

		<table class="form-table">

			<?php
			if(is_array($custom_fields)) :
			foreach ( $custom_fields as $key => $custom_field ) :
			if( in_array($post->post_type, $custom_field['object_type'] ) ){
				$output = true;
			} else {
				unset($custom_fields[$key]); //Filter out unused ones for Validation rules later
			}

			$prefix = ( empty( $custom_field['field_wp_allow'] ) ) ? '_ct_' : 'ct_';

			//Field ID to use
			$fid = $prefix . $custom_field['field_id'];

			$custom_field['field_title'] .= (empty($custom_field['field_required'])) ? '' : '*'; //required field class

			if ( $output ):
			?>
			<tr>
				<th>
					<label for="<?php echo $fid; ?>"><?php echo ( $custom_field['field_title'] ); ?></label>
				</th>
				<td>

					<?php
					switch ( $custom_field['field_type'] ){

						case 'text' : { // text fields
							?>
							<input type="text" name="<?php echo $fid; ?>" id="<?php echo $fid; ?>" value="<?php echo ( get_post_meta( $post->ID, $fid, true )); ?>" />
							<p><?php echo ( $custom_field['field_description'] ); ?></p>
							<?php
							break;
						}

						case 'textarea' : {	//textarea fields
							?>
							<textarea name="<?php echo $fid; ?>" id="<?php echo $fid; ?>" rows="5" cols="40"   ><?php echo ( get_post_meta( $post->ID, $fid, true )); ?></textarea>
							<p><?php echo ( $custom_field['field_description'] ); ?></p>
							<?php
							break;
						}

						case 'radio' : {	//radio fields
							if ( get_post_meta( $post->ID, $fid, true )){
								foreach ( $custom_field['field_options'] as $key => $field_option ): ?>
								<label>
									<input type="radio" name="<?php echo $fid; ?>" id="<?php echo ( $fid . '_' . $key ); ?>" value="<?php echo ( $field_option ); ?>" <?php if ( get_post_meta( $post->ID, $fid, true ) == $field_option ) echo ( 'checked="checked"' ); ?>   />
									<?php echo ( $field_option ); ?>
								</label>
								<?php
								endforeach;
							} else {
								foreach ( $custom_field['field_options'] as $key => $field_option ): ?>
								<label>
									<input type="radio" name="<?php echo $fid; ?>" id="<?php echo ( $fid . '_' . $key ); ?>" value="<?php echo ( $field_option ); ?>" <?php if ( $custom_field['field_default_option'] == $key ) echo ( 'checked="checked"' ); ?>   />
									<?php echo ( $field_option ); ?>
								</label>
								<?php endforeach;
							} ?>
							<p><?php echo ( $custom_field['field_description'] ); ?></p>
							<?php
							break;
						}

						case 'checkbox' : {	//checkbox fields
							if ( get_post_meta( $post->ID, $fid, true )){
								$field_values = get_post_meta( $post->ID, $fid, true );
								foreach ( $custom_field['field_options'] as $key => $field_option ): ?>
								<label>
									<input type="checkbox" name="<?php echo $fid; ?>[]" id="<?php echo ( $fid . '_' . $key ); ?>" value="<?php echo ( $field_option ); ?>"
									<?php checked( is_array($field_values) && array_search($field_option, $field_values) !== false ); ?>   />
									<?php echo ( $field_option ); ?>
								</label>
								<?php endforeach;
							}
							else
							{
								foreach ( $custom_field['field_options'] as $key => $field_option ): ?>
								<label>
									<input type="checkbox" name="<?php echo $fid; ?>[]" id="<?php echo ( $fid . '_' . $key); ?>" value="<?php echo ( $field_option ); ?>" <?php checked( $custom_field['field_default_option'] == $key ); ?>   />
									<?php echo ( $field_option ); ?>
								</label>
								<?php endforeach;
							} ?>
							<p><?php echo ( $custom_field['field_description'] ); ?></p>
							<?php
							break;
						}

						case 'selectbox' : {	//selectbox fields
							?>
							<select name="<?php echo $fid; ?>" id="<?php echo $fid; ?>" >
								<?php
								if ( get_post_meta( $post->ID, $fid, true )) {
									foreach ( $custom_field['field_options'] as $key => $field_option ): ?>
									<option value="<?php echo ( $field_option ); ?>" <?php if ( get_post_meta( $post->ID, $fid, true ) == $field_option ) echo ( 'selected="selected"' ); ?> ><?php echo ( $field_option ); ?></option>
									<?php
									endforeach;
								} else {
									foreach ( $custom_field['field_options'] as $key => $field_option ): ?>
									<option value="<?php echo ( $field_option ); ?>" <?php if ( $custom_field['field_default_option'] == $key ) echo ( 'selected="selected"' ); ?> ><?php echo ( $field_option ); ?></option>
									<?php
									endforeach;
								} ?>
							</select>
							<p><?php echo ( $custom_field['field_description'] ); ?></p>
							<?php
							break;
						}

						case 'multiselectbox' : {	//multiselectbox fields
							?>
							<select name="<?php echo $fid; ?>[]" id="<?php echo $fid; ?>" multiple="multiple" class="ct-select-multiple" >
								<?php
								if ( get_post_meta( $post->ID, $fid, true )){
									foreach ( $custom_field['field_options'] as $key => $field_option ):
									$multiselectbox_values = get_post_meta( $post->ID, $fid, true );
									$multiselectbox_values = (is_array($multiselectbox_values)) ? $multiselectbox_values : (array)$multiselectbox_values;
									?>
									<option value="<?php echo ( $field_option ); ?>"<?php selected(in_array($field_option, $multiselectbox_values) ); ?> ><?php echo ( $field_option ); ?></option>
									<?php
									endforeach;
								} else {
									foreach ( $custom_field['field_options'] as $key => $field_option ): ?>
									<option value="<?php echo ( $field_option ); ?>" <?php if ( $custom_field['field_default_option'] == $key ) echo ( 'selected="selected"' ); ?> ><?php echo ( $field_option ); ?></option>
									<?php endforeach;
								}
								?>
							</select>
							<p><?php echo ( $custom_field['field_description'] ); ?></p>
							<?php
							break;
						}

						case 'datepicker' : {	//datepicker fields
							?>
							<?php echo $this->jquery_ui_css(); ?>
							<input type="text" class="pickdate" name="<?php echo $fid; ?>" id="<?php echo $fid; ?>" value="<?php echo ( get_post_meta( $post->ID, $fid, true )); ?>" />
							<script type="text/javascript">
								jQuery(document).ready(function(){
									jQuery('#<?php echo $fid; ?>').datepicker({ dateFormat : '<?php echo $custom_field['field_date_format']; ?>' });
								});
							</script>
							<p><?php echo ( $custom_field['field_description'] ); ?></p>
							<?php
							break;
						}
					}  //switch
					?>

				</td>
			</tr>
			<?php
			endif; $output = false;
			endforeach;
			endif; //is_array($custom_fields)
			?>

		</table>
	</div>
	<script type="text/javascript">
		<?php echo $this->validation_rules($custom_fields); ?>
	</script>
</div>
