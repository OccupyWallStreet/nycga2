<?php if (!defined('ABSPATH')) die('No direct access allowed!'); ?>

<?php
$post_types = get_post_types('','names');

?>
<div style="clear: both">
	<h3><?php _e('Add Custom Field', $this->text_domain); ?></h3>
	<form action="#" method="post" class="ct-custom-fields">
		<div class="ct-wrap-left">
			<div class="ct-table-wrap">
				<div class="ct-arrow"><br></div>
				<h3 class="ct-toggle"><?php _e('Field Title', $this->text_domain) ?></h3>
				<table class="form-table <?php do_action('ct_invalid_field_title'); ?>">
					<tr>
						<th>
							<label for="field_title"><?php _e('Field Title', $this->text_domain) ?> <span class="ct-required">( <?php _e('required', $this->text_domain); ?> )</span></label>
						</th>
						<td>
							<input type="text" name="field_title" value="<?php if ( isset( $_POST['field_title'] ) ) echo $_POST['field_title']; ?>" />
							<br /><span class="description"><?php _e('The title of the custom field.', $this->text_domain); ?></span>
						</td>
					</tr>
					<tr>
						<th>
							<label for="field_required"><?php _e('Required Field', $this->text_domain) ?></label>
						</th>
						<td>
							<input type="checkbox" name="field_required" value="2" <?php checked( isset( $_POST['field_required'] ) ); ?> />
							<span class="description"><?php _e('Make this a Required Field.', $this->text_domain); ?></span>
						</td>
					</tr>
					<tr>
						<th>
							<label for="field_message"><?php _e('Required Field Error Prompt', $this->text_domain) ?></label><br />
						</th>
						<td>
							<input type="text" id="field_message" name="field_message" size="55" value="<?php if ( isset( $_POST['field_message'] ) ) echo $_POST['field_message']; ?>" />
							<br /><span class="description"><?php _e('Custom Required Field Error prompt for this field or leave blank for default.', $this->text_domain) ?></span><br />
						</td>
					</tr>
					<tr>
						<th>
							<label for="field_wp_allow"><?php _e('Allow for WP/plugins', $this->text_domain) ?> 
							<br /><span class="ct-required">(<?php _e("can't be changed", $this->text_domain) ?>)</span></label>
						</th>
						<td>
							<input type="checkbox" name="field_wp_allow" value="2" <?php checked( isset( $_POST['field_wp_allow'] ) ); ?> />
							<span class="description"><?php _e('The WP and other plugins can use this custom field.', $this->text_domain); ?></span>
						</td>
					</tr>
				</table>
			</div>
			<div class="ct-table-wrap">
				<div class="ct-arrow"><br></div>
				<h3 class="ct-toggle"><?php _e('Field Type', $this->text_domain) ?></h3>
				<table class="form-table <?php do_action('ct_invalid_field_options'); ?>">
					<tr>
						<th>
							<label for="field_type"><?php _e('Field Type', $this->text_domain) ?> <span class="ct-required">( <?php _e('required', $this->text_domain); ?> )</span></label>
						</th>
						<td>
							<select name="field_type">
								<option value="text" <?php selected( isset( $_POST['field_type'] ) && $_POST['field_type'] == 'text' ); ?>><?php _e('Text Box', $this->text_domain); ?></option>
								<option value="textarea" <?php selected( isset( $_POST['field_type'] ) && $_POST['field_type'] == 'textarea' ); ?>><?php _e('Multi-line Text Box', $this->text_domain); ?></option>
								<option value="radio" <?php selected( isset( $_POST['field_type'] ) && $_POST['field_type'] == 'radio' ); ?>><?php _e('Radio Buttons', $this->text_domain); ?></option>
								<option value="checkbox" <?php selected( isset( $_POST['field_type'] ) && $_POST['field_type'] == 'checkbox' ); ?>><?php _e('Checkboxes', $this->text_domain); ?></option>
								<option value="selectbox" <?php selected( isset( $_POST['field_type'] ) && $_POST['field_type'] == 'selectbox' ); ?>><?php _e('Drop Down Select Box', $this->text_domain); ?></option>
								<option value="multiselectbox" <?php selected( isset( $_POST['field_type'] ) && $_POST['field_type'] == 'multiselectbox' ); ?>><?php _e('Multi Select Box', $this->text_domain); ?></option>
								<option value="datepicker" <?php selected( isset( $_POST['field_type'] ) && $_POST['field_type'] == 'datepicker' ); ?>><?php _e('Date Picker', $this->text_domain); ?></option>
							</select>
							<br /><span class="description"><?php _e('Select type of the custom field.', $this->text_domain); ?></span>

							<div class="ct-text-type-options">
								<h4><?php _e('Fill in the options for this field', $this->text_domain); ?>:</h4>
								<p>
									<label for="field_regex"><?php _e('Regular Expression Validation', $this->text_domain) ?></label>
									<br />
									<textarea name="field_regex" rows="2" cols="50" ><?php if ( isset( $_POST['field_regex'] ) ) echo esc_textarea($_POST['field_regex']); ?></textarea>
<br />
									<label for="field_regex_options"><?php _e('Options:', $this->text_domain) ?></label>
									<input type="text" id="field_regex_options" name="field_regex_options" size="3" value="<?php if ( isset( $_POST['field_regex_options'] ) ) echo $_POST['field_regex_options']; ?>" />
									<br /><span class="description"><?php _e('i = ignore case, g = global, m = multiline', $this->text_domain) ?></span>
									<br /><span class="description"><?php _e('Enter a regular expression to validate against or leave blank. Example for Email:', $this->text_domain) ?></span>
									<br /><span class="description"><?php _e('<code>^[\w.%+-]+@[\w.-]+\.[A-Z]{2,4}$</code> <code>i</code>', $this->text_domain) ?></span>
								</p>
								<p>
									<label for="field_regex_message"><?php _e('Regular Expression Validation Error Message', $this->text_domain) ?></label><br />
									<input type="text" id="field_message" name="field_regex_message" size="55" value="<?php if ( isset( $_POST['field_regex_message'] ) ) echo $_POST['field_regex_message']; ?>" /><br />
									<span class="description"><?php _e('Custom Regular Expression Validation Error message for this field or leave blank for default.', $this->text_domain) ?></span><br />
								</p>
							</div>

							<div class="ct-date-type-options">

								<?php

								$date_format = (empty($_POST['field_date_format'])) ? $this->get_options('date_format') : $_POST['field_date_format'];
								$date_format = (is_array($date_format)) ? 'mm/dd/yy' : $date_format;

								$this->jquery_ui_css(); //Load the current ui theme css

								?>
								<h4><?php _e('Fill in the options for this field', $this->text_domain); ?>:</h4>
								<p>
									<input type="text" id="field_date_format" name="field_date_format" size="38" value="<?php echo $date_format; ?>" onchange="jQuery('#datepicker').datepicker( 'option', 'dateFormat', this.value );"/>
									<br /><span class="description"><?php _e('Select Date Format option or type your own', $this->text_domain) ?></span>
									<br />
									<br />
									<input class="pickdate" id="datepicker" type="text" size="38" value="" /><br />
									<span class="description"><?php _e('Date picker sample', $this->text_domain) ?></span>
								</p>

							</div>
							<div class="ct-field-type-options">
								<h4><?php _e('Fill in the options for this field', $this->text_domain); ?>:</h4>
								<p>
									<?php _e('Order By', $this->text_domain); ?> :
									<select name="field_sort_order">
										<option value="default"><?php _e('Order Entered', $this->text_domain); ?></option>
										<?php /** @todo introduce the additional order options
										<option value="asc"><?php _e('Name - Ascending', $this->text_domain); ?></option>
										<option value="desc"><?php _e('Name - Descending', $this->text_domain); ?></option>
										*/ ?>
									</select>
								</p>

								<?php if ( isset( $_POST['field_options'] ) && is_array( $_POST['field_options'] )): ?>
								<?php foreach ( $_POST['field_options'] as $key => $field_option ): ?>
								<p>
									<?php _e('Option', $this->text_domain); ?> <?php echo( $key ); ?>:
									<input type="text" name="field_options[<?php echo( $key ); ?>]" value="<?php echo( $field_option ); ?>" />
									<input type="radio" value="<?php echo( $key ); ?>" name="field_default_option" <?php checked( $_POST['field_default_option'] == $key ); ?> />
									<?php _e('Default Value', $this->text_domain); ?>
									<?php if ( $key != 1 ): ?>
									<a href="#" class="ct-field-delete-option">[x]</a>
									<?php endif; ?>
								</p>
								<?php endforeach; ?>
								<?php else: ?>
								<p><?php _e('Option', $this->text_domain); ?> 1:
									<input type="text" name="field_options[1]" value="<?php if ( isset( $_POST['field_options'][1] ) ) echo $_POST['field_options'][1]; ?>" />
									<input type="radio" value="1" name="field_default_option" <?php checked( isset( $_POST['field_default_option'] ) && $_POST['field_default_option'] == '1' ); ?> />
									<?php _e('Default Value', $this->text_domain); ?>
								</p>
								<?php endif; ?>

								<div class="ct-field-additional-options"></div>
								<input type="hidden" value="1" name="track_number">
								<p><a href="#" class="ct-field-add-option"><?php _e('Add another option', $this->text_domain); ?></a></p>
							</div>
						</td>
					</tr>
				</table>
			</div>
			<div class="ct-table-wrap">
				<div class="ct-arrow"><br></div>
				<h3 class="ct-toggle"><?php _e('Field Description', $this->text_domain) ?></h3>
				<table class="form-table">
					<tr>
						<th>
							<label for="field_description"><?php _e('Field Description', $this->text_domain) ?></label>
						</th>
						<td>
							<textarea class="ct-field-description" name="field_description" rows="3" ><?php if ( isset( $_POST['field_description'] ) ) echo esc_textarea($_POST['field_description']); ?></textarea>
							<span class="description"><?php _e('Description for the custom field.', $this->text_domain); ?></span>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<div class="ct-wrap-right">
			<div class="ct-table-wrap">
				<div class="ct-arrow"><br></div>
				<h3 class="ct-toggle"><?php _e('Post Type', $this->text_domain) ?></h3>
				<table class="form-table <?php do_action('ct_invalid_field_object_type'); ?>">
					<tr>
						<th>
							<label for="object_type"><?php _e('Post Type', $this->text_domain) ?> <span class="ct-required">( <?php _e('required', $this->text_domain); ?> )</span></label>
						</th>
						<td>
							<select name="object_type[]" multiple="multiple" class="ct-object-type">
								<?php if ( is_array( $post_types )): ?>
								<?php foreach( $post_types as $post_type ): ?>
								<option value="<?php echo ( $post_type ); ?>" <?php if ( isset( $_POST['object_type'] ) && is_array( $_POST['object_type'] )) { foreach ( $_POST['object_type'] as $post_value ) { selected( $post_value == $post_type ); }} ?>><?php echo ( $post_type ); ?></option>
								<?php endforeach; ?>
								<?php endif; ?>
							</select>
							<br />
							<span class="description"><?php _e('Select one or more post types to add this custom field to.', $this->text_domain); ?></span>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<br style="clear: left" />

		<p class="submit">
			<?php wp_nonce_field( 'submit_custom_field' ); ?>
			<input type="submit" class="button-primary" name="submit" value="<?php _e('Add Custom Field', $this->text_domain); ?>" />
		</p>
		<br /><br /><br /><br />
	</form>
</div>

<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery('#datepicker').datepicker({ dateFormat : '<?php echo $date_format; ?>' });
		jQuery('#datepicker').attr('value', jQuery.datepicker.formatDate('<?php echo $date_format; ?>', new Date(), {}) );
	});
</script>
