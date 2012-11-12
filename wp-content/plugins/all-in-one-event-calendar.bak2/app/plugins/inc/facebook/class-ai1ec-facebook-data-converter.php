<?php

/** 
 * This class converts Facebook data so that it can be used by our plugin
 * 
 * @author The Seed Network
 * 
 * 
 */
class Ai1ec_Facebook_Data_Converter {
	/**
	 * check if a key in an array is set and then returns the value or an empty string
	 * 
	 * @param array $array The array whose key must be checked
	 * 
	 * @param string $key the key that must be checked
	 * 
	 * @return string return either the value if the key is set otherwise an empty string
	 */
	public static function return_empty_or_value_if_set( array $array, $key ) {
		return isset( $array[$key] ) ? $array[$key] : '';
	}
}