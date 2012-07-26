<?php
/**
 * @package		WordPress
 * @subpackage	BuddyPress
 * @author		Boris Glumpler
 * @copyright	2011, ShabuShabu Webdesign
 * @link		http://shabushabu.eu
 * @license		http://www.opensource.org/licenses/gpl-2.0.php GPL License
 */
 
// No direct access is allowed
if( ! defined( 'ABSPATH' ) ) exit;

/**
 * Determine the upload folder
 * 
 * @package	Core
 * @since 	1.0
 */
function bpe_upload_path()
{
	global $bpe;
	
	if( is_multisite() )
	{
		$path = ABSPATH . get_blog_option( Buddyvents::$root_blog, 'upload_path' );
	}
	else
	{
		$upload_path = get_option( 'upload_path' );
		$upload_path = trim( $upload_path );
		
		if( empty( $upload_path ) || $upload_path == 'wp-content/uploads' ) 
		{
			$path = WP_CONTENT_DIR . '/uploads';
		}
		else
		{
			$path = $upload_path;
			if( 0 !== strpos( $path, ABSPATH ) )
				$path = path_join( ABSPATH, $path );
		}
	}
	
	$path .= '/events/';

	return apply_filters( 'bpe_upload_path', $path );
}

/**
 * Determine the upload url
 * 
 * @package	Core
 * @since 	1.0
 */
function bpe_upload_url( $root = false )
{
	global $bp, $bpe;
	
	if( is_multisite() )
	{
		$url = get_site_url() .'/'. get_blog_option( Buddyvents::$root_blog, 'upload_path' );
	}
	else
	{
		$upload_url = get_option( 'upload_path' );
		$upload_url = trim( $upload_url );
		
		if( empty( $upload_url ) || $upload_url == 'wp-content/uploads' ) 
		{
			$url = WP_CONTENT_URL . '/uploads';
		}
		else
		{
			$url = $upload_url;
			if( 0 !== strpos( $url, get_site_url() ) )
				$url = path_join( get_site_url(), $url );
		}
	}
	
	$url .= '/events/';
	
	if( $root )
		$url = str_replace( bp_get_root_domain(), '', $url );

	return apply_filters( 'bpe_upload_url', $url );
}

/**
 * Upload an image
 * 
 * @package	Core
 * @since 	1.0
 */
function bpe_upload_image( $field = 'img', $w = 150, $h = 150, $w_min = 50, $h_min = 50, $crop = true )
{
	global $bp;
	
	$image = '';
	$errors = array();
	
	// upload the image
	if( $_FILES[$field]['error'] == 0 )
	{
		$file = $_FILES[$field];
	
		if( is_array( $file ) )
		{
			$upload1 = bpe_upload_path();
			$upload2 = bp_loggedin_user_id() . '/';
			
			if( ! is_dir( $upload1 . $upload2 ) )
			{
				if( ! wp_mkdir_p( $upload1 . $upload2 ) )
					$errors[] = __( 'The directory to store your images could not be created.', 'events' );
			}
					
			$image = sanitize_file_name( $file['name'] );
					
			$parts = pathinfo( $image );
						
			$ext = array( 'jpeg', 'jpg', 'gif', 'png' );
			$ext = apply_filters( 'bpe_allowed_img_extensions', $ext );

			$extension = strtolower( $parts['extension'] );
			 
			if( ! in_array( $extension, $ext ) )
				$errors[] = sprintf( __( 'The file %s has a forbidden extension.', 'events' ), $image );
				
			list( $width, $height, $type, $attr ) = getimagesize( $file['tmp_name'] );
			
			if( filesize( $file['tmp_name'] ) > BP_AVATAR_ORIGINAL_MAX_FILESIZE )
				$errors[] = sprintf( __( 'The file you uploaded is too big. Please upload a file under %s', 'events' ), size_format( BP_AVATAR_ORIGINAL_MAX_FILESIZE ) );
			
			if( ! $errors )
			{
				$new_name = time() .'-'. strtolower( $image );
				
				if( ! @move_uploaded_file( $file['tmp_name'], $upload1 . $upload2 . $new_name ) )
					$errors[] = sprintf( __( 'The file %s could not be uploaded.', 'events' ), $image );
				else
				{
					if( $w && $h )
					{
						$keep = false;
						
						$mid = image_resize( $upload1 . $upload2 . $new_name, $w, $h, true, 'mid' );
						
						if( is_wp_error( $mid ) )
						{
							$mid = $upload1 . $upload2 . $new_name;
							$keep = true;
						}
	
						if( $w_min && $h_min )
						{
							$mini = image_resize( $upload1 . $upload2 . $new_name, $w_min, $h_min, $crop, 'mini' );
							
							if( is_wp_error( $mini ) )
							{
								$mini = $mid = $upload1 . $upload2 . $new_name;
								$keep = true;
							}
						}
						
						// delete the original image
						if( ! $keep )
							@unlink( $upload1 . $upload2 . $new_name );
						
						$mid = str_replace( ABSPATH, '/', $mid );
						$mid = str_replace( $_SERVER['DOCUMENT_ROOT'] .'/', '', $mid );
						
						if( $w_min && $h_min )
						{
							$mini = str_replace( ABSPATH, '/', $mini );
							$mini = str_replace( $_SERVER['DOCUMENT_ROOT'] .'/', '', $mini );
							
							$image = array( 'mid' => $mid, 'mini' => $mini );
							$image = serialize( $image );
						}
						else
							$image = $mid;
					}
					else
					{
						$full = $upload1 . $upload2 . $new_name;
						
						list( $width, $height, ,  ) = getimagesize( $full );
	
						$full = str_replace( ABSPATH, '/', $full );
						$full = str_replace( $_SERVER['DOCUMENT_ROOT'] .'/', '', $full );

						$image = array();
						$image['url'] = $full;
						$image['width'] = $width;
						$image['height'] = $height;
					}
				}
			}
		}
	}
	
	$imgs = array( 'url' => $image, 'errors' => $errors );
	
	return apply_filters( 'bpe_upload_image', $imgs, $errors );
}
?>