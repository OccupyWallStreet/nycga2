<h2><?php echo esc_attr( $this->name ); ?></h2>
		
		<p>Current Function: gait_info_tab::edit_screen()</p>
		<input type="submit" name="save" value="save" />
		
		<?php
		wp_nonce_field( 'groups_edit_save_' . $this->slug );

