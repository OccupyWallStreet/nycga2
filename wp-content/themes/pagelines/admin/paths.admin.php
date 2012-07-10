<?php



/* 
 * Admin Paths 
 */
class PLAdminPaths {
	
	
	
	static function account($vars = '', $hash = '#Your_Account'){
		
		return self::make_url('admin.php?page='.PL_MAIN_DASH, $vars, $hash);
		
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function make_url( $string = '', $vars = '', $hash = '' ){
		
		return admin_url( $string.$vars.$hash );
		
	}	
}
