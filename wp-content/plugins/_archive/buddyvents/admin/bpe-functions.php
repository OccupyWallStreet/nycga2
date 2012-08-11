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
 * Delete the avatars
 * 
 * @package Admin
 * @since 	1.2.3
 */
function bpe_delete_default_avatars( $link = false )
{
	if( is_array( $link ) )
	{
		foreach( $link as $img )
		{
			if( ! empty( $img ) )
				@unlink( ABSPATH . $img );
		}
	}
	else
		@unlink( ABSPATH . $link );
}

/**
 * Export an array/object into JSON format
 * 
 * @package Admin
 * @since 	2.1
 */
function bpe_export_to_json( $arr )
{
	$json = array();

	foreach( (array)$arr as $key => $value )
	{
		if( is_array( $value ) || is_object( $value ) )
		{
			if( is_object( $value ) )
				$value = get_object_vars( $value );
			
			$json[$key] = bpe_export_to_json( $value );
		}
		else
			$json[$key] = $value;
	}
	
	return json_encode( $json );
}

/**
 * Import a settings file
 * 
 * @package Admin
 * @since 	2.1
 */
function bpe_import_settings_file()
{
	global $bpe;
	
	if( $_FILES['import']['error'] == 0 )
	{
		$file = $_FILES['import'];
		$error = false;
	
		if( is_array( $file ) )
		{
			$js_file = sanitize_file_name( $file['name'] );
					
			$parts = pathinfo( $js_file );

			$json = json_decode( file_get_contents( $file['tmp_name'] ) );
			 
			if( strtolower( $parts['extension'] ) != 'js' )
				$error = sprintf( __( 'The file %s is not a Javascript file.', 'events' ), $js_file );
			
			elseif( ! isset( $json->bpe_export_file ) )
				$error = sprintf( __( 'The file %s is not a settings file.', 'events' ), $js_file );
			
			if( $error ) :
				return array( 'message' => $error, 'errors' => 1 );
			endif;
			
			foreach( $json as $name => $option ) :			
				if( in_array( substr( $option, 0, 1 ), array( '[', '{' ) ) )
					$option = (array) json_decode( $option );
				
				$bpe->options->{$name} = $option;
			endforeach;

			update_blog_option( Buddyvents::$root_blog, 'bpe_options', $bpe->options );
		}
	}
}

/**
 * Add an admin notice
 * 
 * @package Admin
 * @since 	2.1
 */
function bpe_admin_add_notice( $notice, $type = 'updated' )
{
	if( empty( $notice ) )
		return false;
	
	update_blog_option( Buddyvents::$root_blog, 'bpe_admin_notice', array( 'content' => $notice, 'type' => $type ) );
}

/**
 * Show an admin notice
 * 
 * @package Admin
 * @since 	2.1
 */
function bpe_show_admin_notices( $notice = array() )
{
	if( count( (array) $notice ) <= 0 )
		$notice = (array) get_blog_option( Buddyvents::$root_blog, 'bpe_admin_notice' );
	
	if( ! empty( $notice['content'] ) ) :
	
		if( empty( $notice['type'] ) )
			$notice['type'] = 'updated';
		?>
		<div id="message" class="<?php echo $notice['type'] ?> fade">
			<p><?php echo $notice['content'] ?></p>
		</div>
		<?php
	endif;
	
	delete_blog_option( Buddyvents::$root_blog, 'bpe_admin_notice' );
}
add_action( 'admin_notices', 'bpe_show_admin_notices' );
?>