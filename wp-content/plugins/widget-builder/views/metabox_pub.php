<?php
/**
 * Widget template. This template can be overriden using the "tribe_widget_builder_metabox_publish.php" filter.
 * See the readme.txt file for more info.
 */

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');

?><div class="submitbox" id="submitpost">

<div id="major-publishing-actions">
<?php do_action('post_submitbox_start'); ?>
<div id="delete-action">
<?php
if ( current_user_can( "delete_post", $post->ID ) ) {
	if ( !EMPTY_TRASH_DAYS )
		$delete_text = __( 'Delete Permanently', 'widget-builder' );
	else
		$delete_text = __( 'Move to Trash', 'widget-builder' );
	?>
<a class="submitdelete deletion" href="<?php echo get_delete_post_link($post->ID); ?>"><?php echo $delete_text; ?></a><?php
} ?>
</div>

<div id="publishing-action">
<img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" class="ajax-loading" id="ajax-loading" alt="" />
<?php
if ( !in_array( $post->post_status, array('publish', 'future', 'private') ) || 0 == $post->ID ) {
	if ( $can_publish ) : ?>
		<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e( 'Publish', 'widget-builder' ) ?>" />
		<?php submit_button( __( 'Publish', 'widget-builder' ), 'primary', 'publish', false, array( 'tabindex' => '5', 'accesskey' => 'p' ) ); ?>
<?php
	endif;
} else { ?>
		<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e( 'Update', 'widget-builder' ) ?>" />
		<input name="save" type="submit" class="button-primary" id="publish" tabindex="5" accesskey="p" value="<?php esc_attr_e( 'Update', 'widget-builder' ) ?>" />
<?php
} ?>
</div>
<div class="clear"></div>
</div>
</div>