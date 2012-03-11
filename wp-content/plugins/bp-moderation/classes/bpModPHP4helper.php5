<?php
/**
 * bpModPHP4Helper is a little abstract layer for some oop features between php4 and php5
 * Inspiration (and some code too) from cakephp Object, Overloadable and Overloadable2 classes
 */

/**
 * php5 natively support __construct and __destruct
 */
class bpModPHP4helper
{
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

	/**
	 * Magic method handler.
	 */
	function __call($method, $params)
	{
		if (!method_exists($this, 'call__')) {
			trigger_error(sprintf('Not possible to overload method %s: magic method handler call__ not defined in %s', $method, get_class($this)), E_USER_ERROR);
		}
		return $this->call__($method, $params);
	}

	/**
	 * Getter.
	 */
	function __get($name)
	{
		return $this->get__($name);
	}

	/**
	 * Setter.
	 */
	function __set($name, $value)
	{
		return $this->set__($name, $value);
	}
}

?>
