<?php
/**
 * @package WordPress
 * @subpackage PulsePress
 */

// set default post type
$post_type = pulse_press_get_posting_type();
?>
<div id="postbox">
		<div class="avatar">
			<?php pulse_press_user_avatar( 'size=48' ); ?>
			<?php if(pulse_press_get_option( 'show_anonymous' )): ?>
			<label id="anonymous"><input type="checkbox"  value="1" id="post-anonymous" name="anonymous" /> shy <div id="shy-tooltip">posting this way will hide your identity on the font end, but the admistaror will still be able to find out who posted</div></label>
			<?php endif; ?>
		</div>
		<div id="inputarea">
			<form id="new_post" name="new_post" method="post" action="<?php echo site_url(); ?>/">
				<?php if ( 'status' == pulse_press_get_posting_type() || '' == pulse_press_get_posting_type() ) : ?>
				<label for="posttext">
					<?php pulse_press_user_prompt(); ?>
				</label>
				
				<?php endif; ?>
				<?php if ( current_user_can( 'upload_files' ) && pulse_press_get_option( 'allow_fileupload')): ?>
				<div id="media-buttons" class="hide-if-no-js">
					<?php pulse_press_media_buttons(); ?>
				</div>
				<?php endif; 
				if(pulse_press_get_option( 'show_twitter' ) ):?>
					<textarea class="expand70-200" name="posttext" id="posttext" tabindex="1" rows="4" cols="60"></textarea>
				<?php else: ?>
					<textarea class="expand70-200" name="posttext" id="posttext" tabindex="1" rows="4"  cols="60"></textarea>
				<?php endif; ?>
				<label class="post-error" for="posttext" id="posttext_error"></label>
				
				<div class="postrow">
					<?php if(pulse_press_get_option( 'show_tagging' )): ?>
					<input id="tags" name="tags" type="text" tabindex="2" autocomplete="off"
						value="<?php esc_attr_e( 'Tag it', 'pulse_press' ); ?>"
						onfocus="this.value=(this.value=='<?php echo esc_js( __( 'Tag it', 'pulse_press' ) ); ?>') ? '' : this.value;"
						onblur="this.value=(this.value=='') ? '<?php echo esc_js( __( 'Tag it', 'pulse_press' ) ); ?>' : this.value;" /> <br />
						<em><?php _e('Separate tags with commas', 'pulse_press'); ?></em>
					<?php endif; ?>
						<?php 
						// anonomouse is commneted out for now
						?>
						
					<input id="submit" type="submit" tabindex="3" value="<?php esc_attr_e( 'Post it', 'pulse_press' ); ?>" />
					<?php 
					if(pulse_press_get_option( 'show_twitter' ) ): 
						if(pulse_press_get_option( 'bitly_user') && pulse_press_get_option( 'bitly_api')): ?>
						<a href="#" id="shorten-url">shorten url</a>
						<?php endif; ?>
						<span id="post-count">140</span>
					<?php endif; ?>
				</div>
				<span class="progress" id="ajaxActivity">
					<img src="<?php echo str_replace( WP_CONTENT_DIR, content_url(), locate_template( array( 'i/indicator.gif' ) ) ); ?>"
						alt="<?php esc_attr_e( 'Loading...', 'pulse_press' ); ?>" title="<?php esc_attr_e( 'Loading...', 'pulse_press' ); ?>"/>
				</span>
				<input type="hidden" name="action" value="post" />
				<?php wp_nonce_field( 'new-post' ); ?>
			</form>

		</div>

		<div class="clear"></div>

</div> <!-- // postbox -->
