	<?php 
		$site_message = get_option('dev_buddydaily_header_message');
	?>
	<div id="information-bar">
		<div class="alignright"><?php echo stripslashes($site_message); ?></div>
		<?php echo date_i18n( get_option('date_format')) ?> 	<?php echo date_i18n( get_option('time_format')) ?> 
	</div>