<?php
/**
 * bpModPHP4Helper is a little abstract layer for some oop features between php4 and php5
 * Inspiration (and some code too) from cakephp Object, Overloadable and Overloadable2 classes
 */

/**
 * allows __construct and __destruct in PHP4
 */
class bpModPHP4helper
{

	/**
	 * PHP4 constructor that call descendant __costruct and set __destruct as a
	 * shutdown function if present
	 */
	function bpModPHP4helper()
	{

		/**
		 * right now __destruct isn't used anywhere
		 *
		if (method_exists($this, '__destruct')) {
		register_shutdown_function (array(&$this, '__destruct'));
		}/**/

		$args = func_get_args();
		call_user_func_array(array(&$this, '__construct'), $args);

	}

	/**
	 * Class constructor, overridden in descendant classes.
	 */
	function __construct()
	{
	}
}

/**
 * in child classes use get__ set__ and call__ as magic methods.
 */
class bpModOverloadable extends bpModPHP4helper
{

	function __construct()
	{
		overload(get_class($this));
	}

	/**
	 * Magic method handler.
	 */
	function __call($method, $params, &$return)
	{
		if (!method_exists($this, 'call__')) {
			trigger_error(sprintf(__('Magic method handler call__ not defined in %s', true), get_class($this)), E_USER_ERROR);
		}
		$return = $this->call__($method, $params);
		return true;
	}

	/**
	 * Getter.
	 */
	function __get($name, &$value)
	{
		$value = $this->get__($name);
		return true;
	}

	/**
	 * Setter.
	 */
	function __set($name, $value)
	{
		$this->set__($name, $value);
		return true;
	}
}

overload('bpModOverloadable');

?>
