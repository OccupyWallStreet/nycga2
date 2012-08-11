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

if( ! function_exists( 'mapo_add_coords' ) ) :
	/**
	 * Add the coordinates
	 * 
	 * @package	Core
	 * @since 	1.2.3
	 */
	function mapo_add_coords( $id = null, $user_id, $group_id, $lat, $lng )
	{
		$ucoords = new MAPO_Coords( $id );
		
		$ucoords->user_id = $user_id;
		$ucoords->group_id = $group_id;
		$ucoords->lat = $lat;
		$ucoords->lng = $lng;
	
		if( $new_id = $ucoords->save() )
			return $new_id;
			
		return false;
	}
endif;

if( ! function_exists( 'mapo_get_id_by_user' ) ) :
	/**
	 * Get an id
	 * 
	 * @package	Core
	 * @since 	1.2.3
	 */
	function mapo_get_id_by_user( $user_id )
	{
		return MAPO_Coords::get_id_by_user( $user_id );
	}
endif;

if( ! function_exists( 'mapo_get_id_by_group' ) ) :
	/**
	 * Get an id
	 * 
	 * @package	Core
	 * @since 	1.2.3
	 */
	function mapo_get_id_by_group( $group_id )
	{
		return MAPO_Coords::get_id_by_group( $group_id );
	}
endif;

if( ! function_exists( 'mapo_format_address' ) ) :
	/**
	 * Format an address for google
	 * 
	 * @package	Core
	 * @since 	1.2.3
	 */
	function mapo_format_address( $address )
	{
		$space = array( '  ', '   ', '    ', '     ' );
		$address = str_replace( $space, ' ', $address );
		$address = str_replace( ' ', '+', $address );
		
		return $address;
	}
endif;

if( ! function_exists( 'mapo_save_user_coordinates' ) ) :
	/**
	 * Save a users coordinates
	 * 
	 * @package	Core
	 * @since 	1.2.3
	 */
	function mapo_save_user_coordinates( $user_id, $field = false )
	{
		if( ! $field )
			$field = $_POST['field_'. bpe_get_option( 'field_id' )];
		
		if( ! empty( $field ) )
		{
			$coords = mapo_get_coords( $field );
			
			if( $coords )
			{
				$id = mapo_get_id_by_user( $user_id );
				
				if( ! $id ) $id = null;
		
				mapo_add_coords( $id, $user_id, 0, $coords['lat'], $coords['lng'] );
				do_action( 'mapo_updated_location', $user_id, $coords );
			}
		}
	}
	add_action( 'xprofile_updated_profile', 'mapo_save_user_coordinates' );
endif;

if( ! function_exists( 'mapo_get_coords' ) ) :
	/**
	 * Get a users coordinates
	 * 
	 * @package	Core
	 * @since 	1.2.3
	 */
	function mapo_get_coords( $field )
	{
		if( defined( 'WP_LOCAL_DEV' ) && WP_LOCAL_DEV )
			return false;

		global $bpe, $wpdb;
	
		$addr = mapo_format_address( $field );
	
		$xml = wp_remote_get( 'http://maps.google.com/maps/api/geocode/xml?address='. $addr .'&sensor=false' );

		$data = new SimpleXMLElement( wp_remote_retrieve_body( $xml ) );
		
		if( $data->status == 'OK' )
		{
			$lat = (array)$data->result->geometry->location->lat;
			$lng = (array)$data->result->geometry->location->lng;
	
			$latitude = $lat[0];
			$longitude = $lng[0];
			
			$result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$bpe->tables->coords} WHERE lat = %d AND lng = %d AND user_id > 0", $latitude, $longitude ) );
	
			if( $result )
			{
				$suffix = 0.001;
				do {
					$latitude = $latitude + $suffix;
					$longitude = $longitude + $suffix;
					
					$result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$bpe->tables->coords} WHERE lat = %d AND lng = %d AND user_id > 0", $latitude, $longitude ) );
					
					$suffix = $suffix + 0.001;
				} while ( $result );
			}
			
			return array( 'lat' => $latitude, 'lng' => $longitude );
		}
		
		return false;
	}
endif;

if( ! function_exists( 'mapo_add_to_signup' ) ) :
	/**
	 * Add some usermeta
	 * 
	 * @package	Core
	 * @since 	1.2.3
	 */
	function mapo_add_to_signup( $usermeta )
	{
		if( ! empty( $_POST['field_'. bpe_get_option( 'field_id' )] ) )
			$usermeta['mapo_location'] = $_POST['field_'. bpe_get_option( 'field_id' )];
	
		return $usermeta;
	}
	add_filter( 'bp_signup_usermeta', 'mapo_add_to_signup' );
endif;

if( ! function_exists( 'mapo_user_activate_fields' ) ) :
	/**
	 * Save coordinates on registration
	 * 
	 * @package	Core
	 * @since 	1.2.3
	 */
	function mapo_user_activate_fields( $user_id, $user_login, $user_password, $user_email, $usermeta )
	{
		if( ! empty( $usermeta['mapo_location'] ) )
		{
			$coords = mapo_get_coords( $usermeta['mapo_location'] );
			
			if( $coords )
				mapo_add_coords( null, $user_id, 0, $coords['lat'], $coords['lng'] );
		}
		
		return $user_id;
	}
	add_action( 'bp_core_signup_user', 'mapo_user_activate_fields', 5, 5 );
endif;
?>