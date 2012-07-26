<?php
/**
 * Tribe Widget Factory enables the creation of multiple widgets using the same widget class.
 *
 * Using this simple class, you can have one widget class create several variations of widgets in the widget admin.
 *
 * Originally written by Timothy Wood ( @codearachnid ) for the Tribe Widget Builder
 *
 * Example:
 *
 * $params = array(
 * 	'title' => __( 'My Title' ),
 * 	'description' => __( 'This is a description of the widget.' )
 * );
 * $tribe_widget_factory = new Tribe_WP_Widget_Factory();
 * $tribe_widget_factory->register('My_Widget_Class', $params );
 *
 * class My_Widget_Class extends WP_Widget {
 *
 * 	function __construct( $params ) {
 * 		$id = 'my_widget_'.$params['type'];
 * 		$widget_ops = array(
 * 			'classname' => $id,
 * 			'description' => $params['description'],
 * 			'data' => $params // pass any additional params to the widget instance.
 * 		);
 * 		$control_ops = array( 'id_base' => $id );
 * 		parent::__construct( $id, $params['title'], $widget_ops, $control_ops );
 * 	}
 * }
 */

class Tribe_WP_Widget_Factory extends WP_Widget_Factory {
	/** @var Tribe_WP_Widget_Factory */
	private static $instance = NULL;

	/**
	 * Extend register($widget_class) with ability to pass parameters into widgets
	 *
	 * @param string $widget_class Class of the new Widget
	 * @param array|null $params parameters to pass through to the widget
	 */
	function register($widget_class, $params = null) {
		$key = $widget_class;
		if ( !empty($params) ) {
			$key .= md5(maybe_serialize($params));
		}
		$this->widgets[$key] = new $widget_class($params);
	}


	/**
	 * Get (and instantiate, if necessary) the instance of the class
	 * @static
	 * @return Shared_Sidebars
	 */
	public static function get_instance() {
		if ( !is_a( self::$instance, __CLASS__ ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	final public function __clone() {
		trigger_error( "No cloning allowed!", E_USER_ERROR );
	}

	final public function __sleep() {
		trigger_error( "No serialization allowed!", E_USER_ERROR );
	}

	protected function __construct() {
		parent::__construct();
	}

}