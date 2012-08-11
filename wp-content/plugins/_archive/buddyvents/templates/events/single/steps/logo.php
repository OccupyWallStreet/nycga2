<?php
/**
 * @package		WordPress
 * @subpackage	BuddyPress
 * @author		Boris Glumpler
 * @copyright	2011, ShabuShabu Webdesign
 * @link		http://shabushabu.eu
 * @license		http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */

if( bp_get_avatar_admin_step() == 'upload-image' ) : ?>

    <p><?php _e( 'Upload an image to use as a logo for this event.', 'events' ) ?></p>
    
    <p>
        <input type="file" name="file" id="file" />
        <input type="submit" name="upload" id="upload" value="<?php _e( 'Upload Image', 'events' ) ?>" />
        <input type="hidden" name="action" id="action" value="bp_avatar_upload" />
    </p>
    
    <?php if( bpe_has_event_avatar() ) : ?>
    
        <p><?php _e( 'If you\'d like to remove the existing logo but not upload a new one, please use the delete logo button.', 'events' ) ?></p>
    
		<div id="delete-event-avatar-button">
			<a class="button" id="delete_event_avatar" title="<?php _e( 'Delete Logo', 'events' ) ?>" href="<?php echo bpe_get_event_avatar_delete_link() ?>"><?php _e( 'Delete Logo', 'events' ) ?></a>
		</div>
		
        <?php if( is_admin() ) : ?>
        	<p><?php bpe_event_image( array( 'event' => bpe_get_displayed_event() ) ) ?></p>
        <?php endif; ?> 
    
    <?php endif; ?>
    
    <?php wp_nonce_field( 'bp_avatar_upload' ) ?>

<?php elseif( bp_get_avatar_admin_step() == 'crop-image' ) : ?>

	<h3><?php _e( 'Crop Avatar', 'events' ) ?></h3>

	<img src="<?php bp_avatar_to_crop() ?>" id="avatar-to-crop" class="avatar" alt="<?php _e( 'Logo to crop', 'events' ) ?>" />

	<div id="avatar-crop-pane">
		<img src="<?php bp_avatar_to_crop() ?>" id="avatar-crop-preview" class="avatar" alt="<?php _e( 'Logo preview', 'events' ) ?>" />
	</div>

	<input type="submit" name="avatar-crop-submit" id="avatar-crop-submit" value="<?php _e( 'Crop Image', 'events' ) ?>" />

	<input type="hidden" name="image_src" id="image_src" value="<?php bp_avatar_to_crop_src() ?>" />
	<input type="hidden" id="x" name="x" />
	<input type="hidden" id="y" name="y" />
	<input type="hidden" id="w" name="w" />
	<input type="hidden" id="h" name="h" />

	<?php wp_nonce_field( 'bp_avatar_cropstore' ) ?>

<?php endif; ?>