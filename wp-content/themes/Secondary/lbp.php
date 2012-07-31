<div class="log">
<?php if ( (is_home())  ) { ?>
<?php _e( 'Welcome' ) ?>
<?php } else { ?>
<?php wp_title( '|', false, 'right' ); bloginfo( 'name' ); ?>
<?php } ?>
</div>