<div class="message updated fade">
	<h3><?php echo $label ?></h3>
	<?php echo $msg ?>
	<?php if( isset( $button ) ) : ?>
		<div><input type="button" class="button <?php echo $button->class ?>" value="<?php echo $button->value ?>" /></div>
		<p></p>
	<?php endif ?>
</div>
