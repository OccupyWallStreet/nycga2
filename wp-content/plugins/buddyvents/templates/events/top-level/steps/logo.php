<?php
/**
 * @package		WordPress
 * @subpackage	BuddyPress
 * @author		Boris Glumpler
 * @copyright	2011, ShabuShabu Webdesign
 * @link		http://shabushabu.eu
 * @license		http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */

wp_nonce_field( 'bpe_add_event_'. bpe_get_option( 'logo_slug' ) ) ?>

<?php if( bp_get_avatar_admin_step() == 'upload-image' ) : ?>

	<div class="left-menu">
		<?php bpe_new_event_avatar() ?>
	</div><!-- .left-menu -->

	<div class="main-column">
		<p><?php _e( 'Upload an image to use as a logo for this event.', 'events' ) ?></p>

		<p>
			<input type="file" name="file" id="file" />
			<input type="submit" name="upload" id="upload" value="<?php _e( 'Upload Image', 'events' ) ?>" />
			<input type="hidden" name="action" id="action" value="bp_avatar_upload" />
		</p>

		<p><?php _e( 'To skip the logo upload process, hit the "Next Step" button.', 'events' ) ?></p>
	</div><!-- .main-column -->

<?php elseif( bp_get_avatar_admin_step() == 'crop-image' ) : ?>

	<h3><?php _e( 'Crop Event Logo', 'events' ) ?></h3>

	<img src="<?php bp_avatar_to_crop() ?>" id="avatar-to-crop" class="avatar" alt="<?php _e( 'Logo to crop', 'events' ) ?>" />

	<div id="avatar-crop-pane">
		<img src="<?php bp_avatar_to_crop() ?>" id="avatar-crop-preview" class="avatar" alt="<?php _e( 'Logo preview', 'events' ) ?>" />
	</div>

	<input type="submit" name="avatar-crop-submit" id="avatar-crop-submit" value="<?php _e( 'Crop Image', 'events' ) ?>" />

	<input type="hidden" name="image_src" id="image_src" value="<?php bp_avatar_to_crop_src() ?>" />
	<input type="hidden" name="upload" id="upload" />
	<input type="hidden" id="x" name="x" />
	<input type="hidden" id="y" name="y" />
	<input type="hidden" id="w" name="w" />
	<input type="hidden" id="h" name="h" />

<?php endif; ?>