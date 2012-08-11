<?php
/* 
 * This file generates the default booking form fields. Events Manager Pro does not use this file.
 */
/* @var $EM_Event EM_Event */ 
//Here we have extra information required for the booking. 
?>
<?php if( !is_user_logged_in() && apply_filters('em_booking_form_show_register_form',true) ): ?>
	<?php //User can book an event without registering, a username will be created for them based on their email and a random password will be created. ?>
	<input type="hidden" name="register_user" value="1" />
	<p>
		<label for='user_name'><?php _e('Name','dbem') ?></label>
		<input type="text" name="user_name" id="user_name" class="input" <?php if(!empty($_REQUEST['user_name'])) echo "value='{$_REQUEST['user_name']}'"; ?> />
	</p>
	<p>
		<label for='dbem_phone'><?php _e('Phone','dbem') ?></label>
		<input type="text" name="dbem_phone" id="dbem_phone" class="input" <?php if(!empty($_REQUEST['dbem_phone'])) echo "value='{$_REQUEST['dbem_phone']}'"; ?> />
	</p>
	<p>
		<label for='user_email'><?php _e('E-mail','dbem') ?></label> 
		<input type="text" name="user_email" id="user_email" class="input" <?php if(!empty($_REQUEST['user_email'])) echo "value='{$_REQUEST['user_email']}'"; ?>  />
	</p>
	<?php do_action('register_form'); //careful if making an add-on, this will only be used if you're not using custom booking forms ?>					
<?php endif; ?>		
<p>
	<label for='booking_comment'><?php _e('Comment', 'dbem') ?></label>
	<textarea name='booking_comment'><?php echo !empty($_POST['booking_comment']) ? $_POST['booking_comment']:'' ?></textarea>
</p>	