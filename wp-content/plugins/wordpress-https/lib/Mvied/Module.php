<?php
/**
 * Module Class for a WordPress plugin.
 * 
 * Each Module in the project will extend this base Module class.
 * Modules can be treated as independent plugins. Think of them as sub-plugins.
 * 
 * @author Mike Ems
 * @package Mvied
 */
class Mvied_Module {

	/**
	 * Plugin object that this module extends
	 *
	 * @var Mvied
	 */
	protected $_plugin;

	/**
	 * Set Plugin
	 * 
	 * @param Mvied_Plugin $plugin
	 * @return object $this
	 * @uses Mvied_Plugin
	 */
	public function setPlugin( Mvied_Plugin $plugin ) {
		$this->_plugin = $plugin;		
		return $this;
	}
	
	/**
	 * Get Plugin
	 * 
	 * @param none
	 * @return Mvied_Plugin
	 */
	public function getPlugin() {
		if ( ! isset($this->_plugin) ) {
			die('Module ' . __CLASS__ . ' missing Plugin dependency.');
		}
		
		return $this->_plugin;
	}
	
}