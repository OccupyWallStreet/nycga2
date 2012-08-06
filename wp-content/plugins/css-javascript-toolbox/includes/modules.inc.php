<?php
/**
* @version $Id ; modules.inc.php 16:08:23 07:02:2012 Ahmed Said ;
*/

/**
* Cover all modules functionality.
* 
* @author Ahmed Said.
*/
class CJTModulesEngine {
	/**
	* Modules directory.
	* 
	* @var string
	*/
	private $directory = '';
	
	/**
	* All created/processed modules instances.
	* 
	* @var array
	*/
	public $modules = array();
	
	/**
	* To process modules list.
	* 
	* @var array
	*/
	private $modulesList = array();
	
	/**
	* Wordpress option name for the modules list 
	* for the modules directory.
	* 
	* @var string
	*/
	private $modulesListOptionName = '';
	
	/**
	* Create new CJTModulesEngine object.
	* 
	* @param string Directory to search for modules.
	* @return void
	*/
	public function __construct($modulesDirectory = 'modules') {
		$this->directory = $modulesDirectory;
		$this->modulesListOptionName = MODULES_LIST_CACHE_VAR_PREFIX . "-{$this->directory}";;
		$this->readModulesList();
	}
	
	/**
	* Delete module by ID.
	* 
	* The deleted module won't never run again
	* until added again by the add method.
	* 
	* The module will stay running after deleted however
	* it won't running the next time.
	* 
	* @return boolean
	*/
	public function delete($moduleId) {
		if ($moduleId && is_scalar($moduleId)) {
			if (array_key_exists($moduleId, $this->modulesList)) {
				unset($this->modulesList[$moduleId]);
				update_option($this->modulesListOptionName, $this->modulesList);
			}
		}
	}
	
	/**
	* Create new CJTModulesEngine object.
	* @see __construct for parameters list.
	* @return CJTModulesEngine
	*/
	public static function getInstance($modulesDirectory = 'modules') {
		$instance = new CJTModulesEngine($modulesDirectory);
		return $instance;
	}
	
	/**
	* Process all available modules.
	* 
	* @return void
	*/
	public function processAll() {
		// We're that this variable will hold array @see readModulesList called from __construct.
		foreach ($this->modulesList as $moduleId => $module) {
			if (file_exists($module['file'])) {
				// If this method called twice don't desttoy the module 
				// object.
				$currentModuleObject = isset($this->modules[$moduleId]) ? $this->modules[$moduleId] : null;
				$mayBeObject = require_once $module['file'];
				$this->modules[$moduleId] = !is_bool($mayBeObject) ? $mayBeObject : $currentModuleObject;
				cssJSToolbox::printDebugMessage("Processing Module: {$moduleId}");
			}
		}
	}
	
	/**
	* Read modules list from cache or collect
	* from modules directory is not caches yet.
	* 
	* @return void
	*/
	public function readModulesList() {
		$cachedModulesList = get_option($this->modulesListOptionName);
		// Cache modules list only if not cached before.
		// Empty array is another case and it mean 
		// that this method is never called before.
		if (!is_array($cachedModulesList)) {
			cssJSToolbox::printDebugMessage("Caching Modules List");
			$cachedModulesList = array();
			$modulesDirPath = CJTOOLBOX_PATH . "/{$this->directory}";
			$modulesIterator = opendir($modulesDirPath);
			// Get all modules.
			while ($moduleDir = readdir($modulesIterator)) {
				// Build module main file path.
				$moduleFileName = "{$moduleDir}.php";
				$modulePath = "{$modulesDirPath}/{$moduleDir}";
			  if ($moduleDir != '.' && $moduleDir != '..' & is_dir($modulePath)) {
			  	$moduleFilePath = "{$modulePath}/{$moduleFileName}";
			  	$module = array(
			  		'id' => $moduleDir,
			  		'path' => $modulePath,
			  		'url' => CJTOOLBOX_URL . "/modules/{$moduleDir}",
			  		'file' => $moduleFilePath,
			  		'status' => 'active',
			  	);
			  	$cachedModulesList[$moduleDir] = $module;
				}
			}
			closedir($modulesIterator);
			// Update database modules list.
			update_option($this->modulesListOptionName, $cachedModulesList);
		} 
		$this->modulesList = $cachedModulesList;
	}
	
} // End class.