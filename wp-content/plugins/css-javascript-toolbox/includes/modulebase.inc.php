<?php
/**
* 
*/

/**
*
*/
abstract class CJTModuleBase {
	
	/**
	* instance to cjToolbox object.
	* 
	* @var cssJSToolbox
	*/
	protected $cjToolbox = null;
	
	/**
	* Is this module is to extend the cssJSToolbox
	* object method.
	* 
	* @var boolean
	*/
	protected $extendCJToolbox = true;
	
	/**
	* Module info struct passed by modules::processAll method.
	* 
	* @var array
	*/
	protected $moduleInfo = null;
	
	/**
	* If the method is not implemented in $this
	* object delegate cssJSToolbox method.
	* 
	* @param string Method name.
	* @param array Parameters.
	* @return mixed.
	*/
	public function __call($method, $parameters) {
		if ($this->extendCJToolbox) {
			return call_user_func_array(array($this->cjToolbox, $method), $parameters);
		}
		else {
		  trigger_error("Module method '{$method}' is not found", E_USER_ERROR);
		}
	}

	/**
	* Module base constructor.
	* 
	* This method should be called manually from the derivded classed
	*                                           
	* @param array Module info.
	* @param boolean Is to extended cssJSToolbox object methods. 
	* @return void
	*/
	protected function __construct($moduleInfo, $extendCJToolbox = false) {
		$this->moduleInfo = $moduleInfo;
		$this->extendCJToolbox = $extendCJToolbox;
		if ($extendCJToolbox) {
			// Extended cssJSToolbox class functionaity.
			$this->cjToolbox = cssJSToolbox::$instance;
			cssJSToolbox::$instance = $this;
		}
	}
	
	/**
	* If the property is not implemented in $this
	* object delegate cssJSToolbox property.
	* 
	* @param string Property name.
	* @return mixed
	*/
	public function __get($property) {
		if ($this->extendCJToolbox) {
			return $this->cjToolbox->$property;
		}
		else {
		  trigger_error("Module property '{$property}' is not found", E_USER_ERROR);
		}
	}
	
	/**
	* If the property is not implemented in $this
	* object delegate cssJSToolbox property.
	* 
	* @param string Property name.
	* @param array Parameters.
	* @return void
	*/
	public function __set($property, $value) {
		if ($this->extendCJToolbox) {
			$this->cjToolbox->$property = $value;
		}
		else {
		  trigger_error("Module property '{$property}' is not found", E_USER_ERROR);
		}
	}

	/**
	* Get CSS file URL for the module.
	* 
	* @param string CSS file.
	* @return string CSS file URL
	*/	
	protected function getCSSFileURL($file) {
	  return $this->getURL("public/css/{$file}");
	}
	
	/**
	* Get Javascript file URL for the module.
	* 
	* @param string Javascript file.
	* @return string JS file URL
	*/
	protected function getJSFileURL($file) {
		return $this->getURL("public/js/{$file}");
	}
	
	/**
	* Get path to module file.
	* 
	* @param string 
	* @return string File path.
	*/
	protected function getPath($file = '') {
	  $file = $file ? "/{$file}" : '';
		$path = "{$this->moduleInfo['path']}{$file}";
		return $path;
	}
	
	/**
	* Get module file URL.
	* 
	* @param 
	* @param string File to get the URL to or module
	* URL if omitted.
	* @return string File URL.
	*/
	protected function getURL($file = '') {
		$file = $file ? "/{$file}" : '';
		$url = "{$this->moduleInfo['url']}{$file}";
		return $url;
	}
	
	/**
	* put your comment there...
	* 
	* @param mixed $view
	* @param mixed $parameters
	*/
	public function getView($view, $parameters) {
		$viewPath = $this->getPath("views/{$view}.html.tmpl");
		// Make parameters visible to the view.
		extract($parameters);
		// Get the content.
		ob_start();
		require $viewPath;
		$content = ob_get_clean();
		return $content;
	}
	
	/**
	* put your comment there...
	* 
	*/
	public function leaveCJToolboxObject() {
		cssJSToolbox::$instance = $this->cjToolbox;
	}
	
} // End class.