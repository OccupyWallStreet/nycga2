<?php if (!defined('ABSPATH')) die('No direct access allowed!'); ?>

<?php $msg = __( 'Settings Saved.', $this->text_domain ); ?>

<?php if ( isset( $_POST['save'] ) ): ?>
<div class="updated below-h2" id="message">
	<p><?php echo $msg; ?></p>
</div>
<?php endif; ?>