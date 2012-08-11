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

require_once( EVENT_ABSPATH .'components/facebook/facebook/base_facebook.php' );

/**
 * Extends BaseFacebook and overrides 4 methods
 * 
 * @package Facebook
 * @since 	2.1
 */
class Facebook extends BaseFacebook
{
	protected static $supported_keys = array( 'state', 'code', 'access_token', 'user_id' );

	/**
	 * Initiate the FB class
	 * 
	 * @package Facebook
	 * @since 	2.1
	 * 
	 * @param 	array 	$config
	 */
	public function __construct( $config )
	{
		parent::__construct( $config );
	}

	/**
	 * Set any persistent data for a user
	 * 
	 * @package Facebook
	 * @since 	2.1
	 * 
	 * @param 	string 	$key
	 * @param	string	$value
	 */
	protected function setPersistentData( $key, $value )
	{
		if( ! in_array( $key, self::$supported_keys ) )
		{
			self::errorLog( 'Unsupported key passed to setPersistentData.' );
			return;
		}
		
		$meta_key = $this->get_meta_key( $key );

    	bp_update_user_meta( bp_loggedin_user_id(), $meta_key, $value );
	}

	/**
	 * Get any persistent data for a user
	 * 
	 * @package Facebook
	 * @since 	2.1
	 * 
	 * @param 	string 	$key
	 * @param	mixed	$default
	 */
	protected function getPersistentData( $key, $default = false )
	{
		if( ! in_array( $key, self::$supported_keys ) )
		{
			self::errorLog( 'Unsupported key passed to getPersistentData.' );
			return $default;
		}

		$meta_key = $this->get_meta_key( $key );
    	$value = bp_get_user_meta( bp_loggedin_user_id(), $meta_key, true );
    	
    	return ( ! empty( $value ) ) ? $value : $default;
  	}

	/**
	 * Clear specific persistent data for a user
	 * 
	 * @package Facebook
	 * @since 	2.1
	 * 
	 * @param 	string 	$key
	 */
	protected function clearPersistentData( $key )
	{
		if( ! in_array( $key, self::$supported_keys ) )
		{
			self::errorLog( 'Unsupported key passed to clearPersistentData.' );
			return $default;
		}

		$meta_key = $this->get_meta_key( $key );

		bp_delete_user_meta( bp_loggedin_user_id(), $meta_key );
	}

	/**
	 * Clear all persistent data for a user
	 * 
	 * @package Facebook
	 * @since 	2.1
	 */
	protected function clearAllPersistentData()
	{
		foreach( self::$supported_keys as $key )
			$this->clearPersistentData( $key );
	}

	/**
	 * Construct the meta_key for user meta
	 * 
	 * @package Facebook
	 * @since 	2.1
	 */
	protected function get_meta_key( $key )
	{
    	return implode( '_', array( 'fb', $key ) );
	}
}