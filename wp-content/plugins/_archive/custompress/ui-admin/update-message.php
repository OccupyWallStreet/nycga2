<?php if (!defined('ABSPATH')) die('No direct access allowed!'); ?>

<?php if ( isset( $_GET['updated'] ) ): ?>
<div class="updated below-h2" id="message">
	<p><?php _e( 'Content Type Updated', $this->text_domain ); ?></p>
</div>
<?php endif; ?>