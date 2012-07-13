<?php
/**
 * Link Embedded Media Functions
 */

// load embed service classes
require_once 'bp-links-embed-classes.php';
require_once 'bp-links-embed-services.php';

/************************************************
 * General Functions
 */

function bp_links_embed_upload_from_url( $url ) {

	// build up our $files array.
	// its the same format as returned by wp_handle_upload()
	$files['file'] = null;
	$files['url'] = null;
	$files['type'] = null;
	$files['error'] = 'File upload failed!';

	// get path from URL
	$url_parts = parse_url( $url );

	if ( $url_parts['path'] ) {

		// nice, have the path
		$url_path = $url_parts['path'];
		
		// get path info
		$path_parts = pathinfo( $url_parts['path'] );

		if ( $path_parts['basename'] && $path_parts['extension'] ) {
			$file_name = $path_parts['basename'];
			$file_extension = $path_parts['extension'];
		} else {
			// error
			return $files;
		}
	} else {
		// error
		return $files;
	}

	// grab remote file
	$response = wp_remote_get( $url );

	// only use data from a successful request
	if ( 200 == wp_remote_retrieve_response_code( $response ) ) {
		$remote_file = wp_remote_retrieve_body( $response );
	} else {
		// assume any code besides 200 is an error
		return $files;
	}

	// make sure we got it
	if ( $remote_file ) {

		// get upload dir info
		add_filter( 'upload_dir', 'bp_links_avatar_upload_dir', 10, 0 );
		$upload_dir = wp_upload_dir();

		// make sure upload dir exists and is writable
		if ( !is_writable( $upload_dir['path'] ) ) {
			$files['error'] = 'Upload dir is not writable!';
			return $files;
		}

		// set local file path and url path
		$files['file'] = sprintf( '%s/remote_upload_%s.%s', $upload_dir['path'], md5( $file_name ), $file_extension );
		$files['url'] = sprintf( '%s/%s', $upload_dir['url'], $file_name );

		// try to write the file
		$bytes_written = file_put_contents( $files['file'], $remote_file );

		if ( $bytes_written ) {

			// get mime type
			$mimes = array(
				'jpg|jpeg|jpe' => 'image/jpeg',
				'gif' => 'image/gif',
				'png' => 'image/png'
			);

			$wp_filetype = wp_check_filetype( $files['file'], $mimes );

			if ( $wp_filetype['type'] ) {
				$files['type'] = $wp_filetype['type'];
				$files['error'] = false;
			} else {
				$files['error'] = 'Only JPEG, GIF and PNG files are supported!';
			}
		}
	}

	return $files;
}

function bp_links_embed_download_avatar( $url ) {
	global $bp;

	require_once( ABSPATH . '/wp-admin/includes/image.php' );

	$bp->avatar_admin->original = bp_links_embed_upload_from_url( $url );

	// Move the file to the correct upload location.
	if ( !empty( $bp->avatar_admin->original['error'] ) ) {
		bp_core_add_message( sprintf( '%1$s %2$s', __( 'Upload Failed! Error was:', 'buddypress-links' ), $bp->avatar_admin->original['error'] ), 'error' );
		return false;
	}

	// Resize the image down to something manageable and then delete the original
	if ( getimagesize( $bp->avatar_admin->original['file'] ) > BP_AVATAR_ORIGINAL_MAX_WIDTH ) {
		$bp->avatar_admin->resized = wp_create_thumbnail( $bp->avatar_admin->original['file'], BP_AVATAR_ORIGINAL_MAX_WIDTH );
	}

	$bp->avatar_admin->image = new stdClass;

	// We only want to handle one image after resize.
	if ( empty( $bp->avatar_admin->resized ) )
		$bp->avatar_admin->image->dir = $bp->avatar_admin->original['file'];
	else {
		$bp->avatar_admin->image->dir = $bp->avatar_admin->resized;
		@unlink( $bp->avatar_admin->original['file'] );
	}

	/* Set the url value for the image */
	$bp->avatar_admin->image->url = str_replace( BP_AVATAR_UPLOAD_PATH, BP_AVATAR_URL, $bp->avatar_admin->image->dir );

	return true;
}

function bp_links_embed_handle_upload( BP_Links_Link $link, $embed_code ) {

	if ( !empty( $embed_code ) ) {

		try {
			// try to locate a service to handle this html code
			$service = BP_Links_Embed::FromHtml( $embed_code );
			
			// did we find a service?
			if ( $service instanceof BP_Links_Embed_Service ) {
				// download image for avatar creation, attach embed service
				if ( bp_links_embed_download_avatar( $service->image_url() ) && $link->embed_attach( $service ) ) {
					// set status to partial and save link
					if ( $link->embed_status_set_partial(true) ) {
						return true;
					} else {
						// failed to save link
						bp_core_add_message( sprintf( '%s %s', __( 'There was an error saving link avatar.', 'buddypress-links' ), __( 'Please try again.', 'buddypress-links' ) ), 'error' );
					}
				} else {
					// failed to download remote image
					bp_core_add_message( sprintf( '%s %s', __( 'Downloading image from remote website failed.', 'buddypress-links' ), __( 'Please try again.', 'buddypress-links' ) ), 'error' );
				}
			} else {
				// no service could handle the html code
				bp_core_add_message( __( 'The embedding code you entered was not recognized.', 'buddypress-links' ), 'error' );
			}
		} catch ( BP_Links_Embed_User_Exception $e ) {
			bp_core_add_message( $e->getMessage(), 'error' );
		} catch ( BP_Links_Embed_Fatal_Exception $e ) {
			throw ($e);
		}
	}

	return false;
}

function bp_links_embed_handle_crop( BP_Links_Link $link ) {
	$link->embed_status_set_enabled( true );
}


/************************************************
 * Template Helper Functions
 */

function bp_link_embed_is_enabled() {
	global $links_template;
	return $links_template->link->embed_status_enabled();
}

function bp_link_embed_has_html() {
	global $links_template;

	if ( true === bp_link_embed_is_enabled() ) {
		return ( $links_template->link->embed() instanceof BP_Links_Embed_Has_Html );
	}
	
	return false;
}

function bp_link_embed_html() {
	echo bp_get_link_embed_html();
}
	function bp_get_link_embed_html() {
		global $links_template;

		if ( true === bp_link_embed_is_enabled() ) {
			return $links_template->link->embed()->html();
		} else {
			return null;
		}
	}


/**********************************************************
 * Rich Media URL Template Helper Functions
 */

function bp_links_auto_embed_panel( $embed_service = null, $display = false ) {
	echo bp_get_links_auto_embed_panel( $embed_service, $display );
}
	function bp_get_links_auto_embed_panel( $embed_service = null, $display = false ) {

		$attr_display = ( $display === true ) ? null : ' style="display: none;"';

		$clear_html =
			sprintf(
				'<span id="link-url-embed-clear"%1$s><a href="#clear">%2$s</a></span>%4$s
				<input type="submit" name="link-url-embed-enter" value="%3$s" onclick="return false;">%4$s',
				$attr_display, // arg 1
				__( 'Clear', 'buddypress-links' ), // arg 2
				__( 'Fetch Page Details', 'buddypress-links' ), // arg 3
				PHP_EOL // arg 4
			);

		$wrapper_html =
			sprintf(
				'<div id="link-url-embed"%1$s>
					%2$s
				</div>',
				$attr_display, // arg 1
				bp_get_links_auto_embed_panel_content( $embed_service ) // arg 2
			);

		return $clear_html . $wrapper_html;
	}

function bp_links_auto_embed_panel_content( $embed_service = null ) {
	echo bp_get_links_auto_embed_panel_content( $embed_service );
}
	function bp_get_links_auto_embed_panel_content( $embed_service = null ) {

		if ( !$embed_service instanceof BP_Links_Embed_Service ) {
			return null;
		}

		// no js html or selected image index by default
		$js_html = null;
		$image_idx = null;
		$image_selected_idx = null;
		$image_selection_count = 0;
		$image_selection_diplay = null;

		// multiple images to select from?
		if ( $embed_service instanceof BP_Links_Embed_Has_Selectable_Image && count( $embed_service->image_selection() ) >= 1 ) {

			// body of javascript array
			$js_array = array();

			// user selected image index
			$image_idx = ( isset( $_POST['link-url-embed-thidx'] ) ) ? $_POST['link-url-embed-thidx'] : $embed_service->image_get_selected();

			// selected image index from data storage
			$image_selected_idx = $embed_service->image_get_selected();
			
			// count of images in selection
			$image_selection_count = count( $embed_service->image_selection() );

			foreach( $embed_service->image_selection() as $idx => $url ) {
				if ( is_int( $idx ) ) {
					$js_array[] = sprintf( '[%d,"%s"]', $idx, $url );
				} else {
					$js_array[] = sprintf( '["%s","%s"]', $idx, $url );
				}
			}

			$js_html =
				sprintf(
					'<script type="text/javascript">
						jQuery(document).ready( function() {
							jQuery("div#link-url-embed-thpick").data("images", [ %1$s ] );
							jQuery("div#link-url-embed-thpick").data("images_idx", %2$s );
							return;
						});
					</script>%3$s',
					join( ",", $js_array ), // arg 1
					( is_null( $image_idx ) ) ? 'null' : $image_idx, // arg 2
					PHP_EOL // arg 3
				);
		} else {
			// don't display the thumb picker
			$image_selection_diplay = ' style="display: none;"';
		}

		// determine output thumb url
		$service_thumb_url = $embed_service->image_thumb_url();

		if ( !empty( $service_thumb_url ) && bp_links_is_url_valid( $service_thumb_url ) ) {
			$thumb_url = $service_thumb_url;
		} else {
			// service or owner chose not to have a thumb
			$thumb_url = bp_links_default_avatar_uri();
		}

		return $js_html . sprintf(
			'<label style="clear: right;">
				%1$s %3$s
			</label>
			<div id="link-url-embed-content">
				<div id="link-url-embed-avatar">
					<img src="%4$s" class="avatar-current" alt="%5$s">
					<div id="link-url-embed-thpick"%12$s>
						<a href="#thprev" id="thprev">&lt;</a>
						<span id="thcurrent">%13$d</span>/<span id="thcount">%14$d</span> %15$s
						<a href="#thnext" id="thnext">&gt;</a>
						<div id="thnone">
							<input type="checkbox" name="link-url-embed-thskip" id="link-url-embed-thskip" value="1"%16$s /> %17$s
						</div>
					</div>
				</div>
				<a href="%7$s" target="_blank">%6$s</a>
				<p>%8$s</p>
				<div id="link-url-embed-options">
					<label for="link-url-embed-edit-text"><input type="checkbox" name="link-url-embed-edit-text" id="link-url-embed-edit-text" value="1"%9$s /> %2$s</label>
					<input type="hidden" name="link-url-embed-data" id="link-url-embed-data" value="%10$s" />
					<input type="hidden" name="link-url-embed-thidx" id="link-url-embed-thidx" value="%11$s" />
				</div>
			</div>',
			__( 'Rich Media Detected:', 'buddypress-links' ), // arg 1
			__( 'Edit Name and Description', 'buddypress-links' ), // arg 2
			esc_html( $embed_service->service_name() ), // arg 3
			esc_url( $thumb_url ), // arg 4
			esc_attr( $embed_service->title() ), // arg 5
			esc_html( $embed_service->title() ), // arg 6
			esc_url( $embed_service->url() ), // arg 7
			esc_html( $embed_service->description() ), // arg 8
			( !empty( $_POST['link-url-embed-edit-text'] ) ) ? ' checked="checked"' : null, // arg 9
			$embed_service->export_data(), // arg 10
			$image_selected_idx, // arg 11
			$image_selection_diplay,  // arg 12
			$image_idx + 1, // arg 13
			$image_selection_count, // arg 14
			__( 'Thumbs', 'buddypress-links' ), // arg 15
			( is_null( $image_idx ) ) ? ' checked="checked"' : null, // arg 16
			__( 'No Thumbnail', 'buddypress-links' ) // arg 17
		);
	}

function bp_links_auto_embed_panel_from_data( $embed_data = null ) {
	echo bp_get_links_auto_embed_panel_from_data( $embed_data );
}
	function bp_get_links_auto_embed_panel_from_data( $embed_data = null ) {

		if ( !empty( $embed_data ) ) {
			$service = BP_Links_Embed::LoadService( $embed_data );
			return bp_get_links_auto_embed_panel( $service, true );
		} else {
			return bp_get_links_auto_embed_panel();
		}
	}
?>
