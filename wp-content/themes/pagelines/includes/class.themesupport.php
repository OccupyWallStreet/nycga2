<?php 
/**
 *
 *  PageLines Theme Support
 *
 */

class PageLinesThemeSupport {
	
	private $base_color = null;
	

	/**
	*
	* @TODO document
	*
	*/
	function __construct() {}
	
	public function Integration( $args ) {}
	
	public function SetBaseColor( $hex ){
		
		global $pagelines_base_color;
		
		$this->base_color = $hex;
		
		$pagelines_base_color = $this->base_color;
	
	}
	
	public static function BaseColor( ){
		global $pagelines_base_color;
		
		return (isset($pagelines_base_color)) ? $pagelines_base_color : false;
		
	}
	
	public function DisableCoreColor(){
		
		$this->Disable( array( 'panel' => 'color_control', 'keep' => false ) );
		
	}
	
	public function DisableCoreType(){
		
		$this->Disable( array( 'panel' => 'typography', 'keep' => false ) );
		
	}
	
	public function Disable( $args ){
		
		global $disabled_settings;

		$defaults = array(
			'option_id'	=> false,
			'panel'		=> '', 
			'keep'		=> false
		);
		
		$args = wp_parse_args( $args, $defaults );
		
		$disabled_settings[ $args['panel'] ] = $args;
		
	}
	
}

/**
*
* @TODO do
*
*/
function pl_is_disabled( $what ){
	
	global $disabled_settings;
	
	if(isset($disabled_settings[$what]))
		return true;
	else
		return false;
}
