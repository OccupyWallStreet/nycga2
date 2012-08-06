<?php
/**
* @version $Id ; v0.3e.php 16:33:02 07:02:2012 Ahmed Said ;
*/

/**
* Provide error Blocks CODE detection and correction
* for the old version plugin.
* 
* @author Ahmed Said
*/
class CJTTools extends CJTModuleBase{
	
	/**
	* Backups Wordpress option name.
	*/
	const BACKUPS_OPTION_NAME = 'cjtoolbox_tools_backups';
	
	/**
	* Allowed ajax action names.
	* 
	* @var array
	*/
	private static $actions = array(
		'api_getBackupInfo',
		'api_backup',
	);
	
	/**
	* Create CJTV03Error object.
	* 
	* @param array Modul info struct.
	* @return void
	*/
	public function __construct($moduleInfo) {
		parent::__construct($moduleInfo, true);
		add_action('cjt_upgraded', array($this, 'pluginUpgrade'));
	}
	
	/**
	* Override cssJSToolbox::admin_print_scripts method.
	*/
	public function admin_print_scripts() {
		// Enqueue cssJSToolbox scripts.
		$this->cjToolbox->admin_print_scripts();
		// Enquque additional scripts.
		wp_enqueue_script(CJTOOLBOX_NAME . '-tools-tools', $this->getJSFileURL('tools.js'));
		$strings = array(
			'backupConfirm' => 				__('Content change has been detected.') . "\n\n" .
																__('It seems that some changes made in the blocks code.') . "\n\n" .
																__('This changes won\'t be saved in the backup.') . "\n\n" .
																__('To save these changes click "Save Changes" and then click backup.') . "\n\n" .
																__('Would you to discard these changes from the backup?'),
			'couldNotBackup' =>				__('Could not backup the database, please try again.'),
			'serverNotResponsing' =>	__('Server not responding!!! Please try again.'),
			'noBackupAvailable' =>		__('No backup available!!!')
		);
		wp_localize_script(CJTOOLBOX_NAME . '-tools-tools', 'CJTToolsLocalization', $strings);
	}

	/**
	* Override cssJSToolbox::ajax_request_template method.
	*/	
	public function ajax_request_template() {
		// Make sure the requested template is belongs to this module.
		$templateName = $_GET['name'];
		$templateNameComponents = array();
		if (preg_match('/tools_(.+)/', $templateName, $templateNameComponents)) {
			// Remove module name from template name.
			$_GET['name'] = $templateNameComponents[1];
			$this->cjToolbox->ajax_request_template($this->getPath('views'), array('tools' => $this));
		}
		else {
			$this->cjToolbox->ajax_request_template();
		}
	}
	
	/**
	* Override cssJSToolbox::admin_print_scripts method.
	*/
	public function admin_print_styles() {
		// Enqueue cssJSToolbox scripts.
		$this->cjToolbox->admin_print_styles();
		// Enquque additional scripts.
		wp_enqueue_style(CJTOOLBOX_NAME . '-tools-tools', $this->getCSSFileURL('style.css'));
	}
	
	/**
	* Dispatch ajax request and prepare the response.
	* 
	* @return void
	*/
	public function api() {
		// Check if pemitted.
		check_ajax_referer('cjtoolbox-admin', 'security');
		// prepare the response in case dispatching failed.
		$response = array(
			'code' => 0x00,
			'message' => 'Dispatching',
			'response' => array(),
		);
		// Get method name from the action name.
		$action = current_filter();
		$actionComponents = array();
		if (preg_match('/_tools_(\w+)$/', $action, $actionComponents)) {
			$methodName = "api_{$actionComponents[1]}";
			// Restrict access.
			if (in_array($methodName, self::$actions)) {
				// Dispatch the call.
				$response['code'] = 0x01;
				$response['response'] = $this->$methodName($_POST);
			}
			else {
			  $response['code'] = 0xFF;
			}
		}
		header('Content-Type: text/plain');
		$responseJSON = json_encode($response);
		die($responseJSON);
	}
	
	/**
	* Ajax method for backup the database.
	* 
	* @return array Response array.
	*/
	protected function api_backup($name = null, $author = null) {
		global $current_user;
		// When called directely/internally
		// it may take the author as paramater.
		$author = $author ? $author : $current_user->display_name;
		$name = $name ? $name : trim($_GET['backupName']);
		$backup = array(
			'time' => date('h:i:s Y:M:d', time()),
			'name' => $name,
			'author' => $author,
			'data' => $this->cjToolbox->getData(),
		);
		// Wrap in array. We may alow multiple backups in the future.
		update_option(self::BACKUPS_OPTION_NAME, array($backup));
		// Response to the rquest.
		$response = array(
			'backName' => $_GET['backupName'],
			'success' => true,
		);
		return $response;
	}
	
	/**
	* Ajax method for get backup info.
	* 
	* @return array Response array.
	*/
	protected function api_getBackupInfo() {
		$backups = $this->getAvailableBackups();
		$response = array('has' => !empty($backups));
		return $response;
	}
	
	/**
	* Display tools bar.
	* Callback for cjt_post_body_before_blocks.
	*/
	public function displayToolBar() {
		$toolbar = $this->getView('tools-bar', array());
		echo $toolbar;
	}
	
	/**
	* Get available backups.
	* 
	*/
	public function getAvailableBackups() {
		$backups = get_option(self::BACKUPS_OPTION_NAME);
		return is_array($backups) ? $backups : array();
	}
	
	/**
	* Get module instance.
	*
	* @see __construct for parameters info.
	* @return CJTVTools Module instance.
	*/
	public static function getInstance($moduleInfo) {
		return new CJTTools($moduleInfo);
	}
	
	/**
	* Callback for cjt_upgrade hook.
	*/
	public function pluginUpgrade() {
		// Create a backup when the Plugin upgraded.
		$this->api_backup('Upgrading', 'system');
	}
	
	/**
	* Restoring blocks from the last backup.
	* 
	* Callback for cjt_blocks_data filter.
	* 
	* @return array Blocks data.
	*/
	public function restore($blocksData) {
		$backups = $this->getAvailableBackups();
		$blocksData = $backups[0]['data']; // For now we've only one backup.
		return $blocksData;
	}
	
	/**
	* Override cssJSToolbox::start_plugin method.
	*/
	public function start_plugin() {
		add_action('cjt_post_body_before_blocks', array($this, 'displayToolBar'));
		add_action('wp_ajax_cjtoolbox_tools_getBackupInfo', array($this, 'api'));
		add_action('wp_ajax_cjtoolbox_tools_backup', array($this, 'api'));
		// bind restore hook.
		if (isset($_GET['restore']) && $_GET['restore']) {
			add_filter('cjt_blocks_data', array($this, 'restore'));
		}
		$this->cjToolbox->start_plugin();
	}
	
} // End class.

// Put the module in action.
return CJTTools::getInstance($module);