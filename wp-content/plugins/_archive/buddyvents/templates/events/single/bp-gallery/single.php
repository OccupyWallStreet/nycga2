<?php
/**
 * @package		WordPress
 * @subpackage	BuddyPress
 * @author		Boris Glumpler
 * @copyright	2011, ShabuShabu Webdesign
 * @link		http://shabushabu.eu
 * @license		http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */

bpe_load_template( 'events/includes/event-header' );
?>
<div class="item-edit-tabs">
	<ul>
		<?php bpe_bp_gallery_admin_tabs(); ?>
	</ul>
</div>

<div class="gnav">
	<?php bp_gallery_bcomb(); ?>
</div>

<div id="galleries">
	<?php if( bp_has_galleries() ):?>
		<?php while( bp_galleries() ): bp_the_gallery(); ?>
			<?php if( user_can_view_gallery( bp_get_gallery_id() ) ) : ?>
				<div class="gallery-actions">
				<?php if( user_can_delete_gallery( bp_loggedin_user_id(), bp_get_gallery_id() ) ) : ?>
					<a href="<?php bp_gallery_edit_link(); ?>"><?php _e( 'Edit This gallery', 'bp-gallery' ); ?></a>|
				<?php endif;?>
				
				<?php if( bpe_is_member( bpe_get_displayed_event() ) ):?>
					<?php bp_gallery_add_media_link(); ?><br />
				<?php endif;?>
				</div>				
				
				<?php locate_template( array( '/gallery/single/media/'. bp_get_single_gallery()->gallery_type .'-loop.php', '/gallery/single/media/media-loop.php' ), true ); ?>
				
				<br class="clear" />	
		
			<?php else:?>			
				<p><?php printf( __( "This is a %s Gallery and You don't have adequate permissions to view them.", 'bp-gallery' ), bp_get_gallery_status() ); ?></p>
			<?php endif;?>	
		<?php endwhile;?>
			
		<?php bp_gallery_pagination_count(); ?><br />
		<?php bp_gallery_pagination(); ?><br />
	<?php else:?>
		<p><?php _e( "Perhaps the gallery does not exist or you don't have adequate permissions to view it.", 'bp-gallery' );?></p>
	<?php endif;?>
	<br class="clear" />
</div>