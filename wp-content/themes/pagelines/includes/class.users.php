<?php 
/**
 * 
 *
 *  API for Working with WordPress Users
 *
 *
 *  @package PageLines Framework
 *  @since 2.0.b16
 *
 */
class UserEngine {


	/**
	*
	* @TODO document
	*
	*/
	function __construct( array $settings = array() ) {}


	/**
	*
	* @TODO document
	*
	*/
	function get_users( $args ){
		$additional_defaults = array(

			'meta_order'	=> true,
			'order_field'	=> 'pl_profile_priority'
			
		);

		$this->args = wp_parse_args($args, $additional_defaults);

		$users = get_users($this->args);

		if($this->args['meta_order'])
			uasort($users, array(&$this, 'order'));
		
		return $users;
	}


	/**
	*
	* @TODO document
	*
	*/
	function order($a, $b){

		$ap = (pl_um($this->args['order_field'], $a->ID)) ? pl_um($this->args['order_field'], $a->ID) : 10;
		$bp = (pl_um($this->args['order_field'], $b->ID)) ? pl_um($this->args['order_field'], $b->ID) : 10;

		if ( $ap == $bp )
			return 0;

		return ($ap < $bp) ? -1 : 1;
		

	}

}


/**
*
* @TODO do
*
*/
function pagelines_get_users( $args ){
	
	$users = new UserEngine();

	$u = $users->get_users($args);
	
	return $u;
}
