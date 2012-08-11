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

<div id="galleries">
	<?php if( bp_has_galleries( array( 'owner_id' => bpe_get_displayed_event( 'id' ), 'owner_type' => 'buddyvents' ) ) ) : ?>
		<?php while( bp_galleries() ) : bp_the_gallery(); ?>
			<div class='bp-gallery gallery-type-<?php bp_gallery_type(); ?>' id="gallery_<?php bp_gallery_id(); ?>">
				<div class='gallery-content'>
					<h3 class='gallery-title'><a href="<?php bp_gallery_permalink(); ?>"><?php bp_gallery_title(); ?></a></h3>
					<div class='gallery-cover'><a href="<?php bp_gallery_permalink(); ?>"><?php bp_gallery_cover_image( 'mini' ); ?></a></div>

					<br class="clear" />

					<div class='gallery-actions'>
	                    <?php if( user_can_delete_gallery() ): ?>
							<?php bp_gallery_add_media_link(); ?>
							<a href="<?php bp_gallery_edit_link(); ?>" class='edit'>[<?php _e( 'Edit', 'bp-gallery' ); ?>]</a>
							<a href='<?php bp_gallery_delete_link(); ?>' class='delete'>[x]<?php _e( 'remove', 'bp-gallery' ); ?></a>
						<?php else : ?>
                            <?php if( bpe_is_member( bpe_get_displayed_event() ) ):?>
 	                           <?php bp_gallery_add_media_link(); ?>
                            <?php endif;?>
                       <?php endif;?>
                    </div>
				</div>
			</div>
		<?php endwhile;?>
		<br class="clear" />

		<?php bp_gallery_pagination_count(); ?><br />
		<?php bp_gallery_pagination(); ?><br />
	<?php else:?>
		<p><?php bp_no_gallery_message(); ?></p>
		<?php bp_gallery_create_button(); ?>
	<?php endif;?>
	
	<br class="clear" />
</div>