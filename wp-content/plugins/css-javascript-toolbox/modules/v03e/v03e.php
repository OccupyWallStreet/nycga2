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
class CJTV03Error extends CJTModuleBase{
	
	/**
	* How many blocks with errors?
	* 
	* @var integer
	*/
	private $processedBlocks = 0;
	
	/**
	* Error types keys and display names.
	* 
	* @var array
	*/
	protected static $errorTypes = array(
		'error' => array(
			'displayName' => 'Error',
			'description' => 'error system is 100% positive that there is at least one error in the code, and would therefore need manual correcting (or Clean up).',
			'correctionRoutine' => 'v03e_fix_error',
			'detectionRoutine' => 'check_error',
		),
		'warning' => array(
			'displayName' => 'Warning',
			'description' => 'error system is not 100% positive if the detected error(s) is actually an error or not, and would therefore need help from you.',
			'correctionRoutine' => 'v03e_fix_warning',
			'detectionRoutine' => 'check_warning',
		),
	);
	
	/**
	* Create CJTV03Error object.
	* 
	* @param array Modul info struct.
	* @return void
	*/
	public function __construct($moduleInfo) {
		parent::__construct($moduleInfo, true);
		add_action('cjt_upgraded', array($this, 'upgrade'), 0, 9);
		add_action('cjt_install', array($this, 'install'));
	}
	
	/**
	* Override cssJSToolbox::admin_print_scripts method.
	*/
	public function admin_print_scripts() {
		// Enqueue cssJSToolbox scripts.
		$this->cjToolbox->admin_print_scripts();
		// Enquque additional scripts.
		wp_enqueue_script(CJTOOLBOX_NAME . '-v03e-edc', $this->getJSFileURL('edc.js'));
		$strings = array(
			'confirmCleanup' =>					__('After cleaning up this code block, you will not be able to scan the code again.') . "\n\n" .
																	__('Would you like to clean up the current code block?') . "\n\n" .
																	__('Nothing is saved until you click the "Save Changes" button.'),
			'CleanupScanIsOutdated' => 	__('The scan is outdated.') . "\n\n" .
																	__('This is may be a result of changing the block code.') . "\n\n" .
																	__('Please rescan the code and then click cleanUp.'),
			'confirmDismiss' => 				__('After dismissing this message you\'ll never be able to scan/clean this block again') . "\n\n" .
																	__('Are you sure?'),
			'codeCleaned' => 						__('Block code is now clean') . "\n\n" .
																	__('Nothing saved untill "Save all changes" button is clicked.'),
			'errorBoxTitle' =>					__('Current code block contains errors!!! Click here to show or hide the details'),
			'rescanOutdated' =>					__('Block code has been changed, scan is outdated!!!') . "\n\n" .
																	__('Would you like to rescan the code?'),
		);
		wp_localize_script(CJTOOLBOX_NAME . '-v03e-edc', 'CJTv03eLocalization', $strings);
	}

	/**
	* Override cssJSToolbox::admin_print_scripts method.
	*/
	public function admin_print_styles() {
		// Enqueue cssJSToolbox scripts.
		$this->cjToolbox->admin_print_styles();
		// Enquque additional scripts.
		wp_enqueue_style(CJTOOLBOX_NAME . '-v03e-edc', $this->getCSSFileURL('style.css'));
	}
	
	/**
	* Callback for wp_ajax_cjtoolbox_v03e_checkCode hook.
	*/
	public function ajaxCheckCode()	{
		$response = array('has' => false);
		// Make sure this is a legal call.
		$this->doAjax();
		// Get post vars.
		$code = filter_input(INPUT_POST, 'code', FILTER_UNSAFE_RAW);
		$errorTypeName = $_POST['errorType'];
		$blockId = $_POST['blockId'];
		// Get error type object.
		$detectedErrorType = self::$errorTypes[$errorTypeName];
		$detectedErrorType['id'] = $errorTypeName;
		// Prepare parameters for the view.
		$checker = $detectedErrorType['detectionRoutine'];
		$matches = $this->$checker($code);
		// If its not array mean its clean.
		if (is_array($matches)) {
		  $errorDetails['_list'] = $matches;
		  $response['has'] = true;
		  $response['message'] = $this->getView('scan-message', compact('detectedErrorType', 'errorDetails', 'blockId'));
		}
		// Output view.
		header('Content-Type: text/plain');
		die(json_encode($response));
	}
	
	/**
	* Check if the code contains errors.
	* 
	* @return array|null
	*/
	public function check_error($code) {
		$matchesList = array();
	  $expression = '/\\\\(\"|\\\')(?:\\\\\\\\\\1|.)*?\\\\\\1/';
	  if (preg_match_all($expression, $code, $matchesList, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
	  	return $this->cleanDetectorMatches($matchesList);
		}
	  return false;
	}
	
	/**
	* Check if the code contains warnings.
	* 
	* @param mixed $code
	*/
	public function check_warning($code) {
		$matchesList = array();
	  $expression = '/\\\\{2,}/';
	  if (preg_match_all($expression, $code, $matchesList, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
	  	return $this->cleanDetectorMatches($matchesList);
		}
	  return false;
	}
	
	/**
	* Cleaned matches resturne from regular expression
	* check to only contain the full match and the offset.
	* 
	* @param array Regular expression matches list.
	* @return array Cleaned matches.
	*/
	protected function cleanDetectorMatches($matchesList) {
		// Remove sub patterns matches.
		foreach ($matchesList as $index => $match) {
			$matchesList[$index] = array($match[0][1], $match[0][0]) ;
		}
		return $matchesList;
	}
	
	/**
	* If all blocks are cleaned delete myself.
	* 
	* Callback for cjt_manage_form_end action.
	* 
	* @return void
	*/
	public function deleteIfNoBlocks() {
		// If all blocks are cleaned, delete myself.
		if (!$this->processedBlocks) {
		  $this->install();
		}
	}
	
	/**
	* Check Ajax call nonce.
	* 
	* @return void
	*/
	protected function doAjax() {
		check_ajax_referer('cjtoolbox-admin', 'security');
	}
	
	/**
	* Get module instance.
	*
	* @see __construct for parameters info.
	* @return CJTV03Error Module instance.
	*/
	public static function getInstance($moduleInfo) {
		return new CJTV03Error($moduleInfo);
	}
	
	/**
	* Callback for cjt_install.
	*/
	public function install() {
		// Delete v03e module with new installation.
		cssJSToolbox::$modulesEngine->delete($this->moduleInfo['id']);
		// Don't extend cssJSToolbox class any more.
		$this->leaveCJToolboxObject();
	}
	
	/**
	* Output error message if the block contain errors.
	* 
	* Callback for cjt_block_after_code hook.
	* 
	* @return void
	*/
	public function noteMessage($blockId, $block) {
		// Work with only blocks those has the meta 
		if (isset($block['meta'][$this->moduleInfo['id']])) {
			$errorDetails = $block['meta'][$this->moduleInfo['id']];
			// If the blocks cleaned up the module meta is available
			// but not the error-type.
			if (isset($errorDetails['errorType']) && $errorDetails['errorType']) {
				$detectedErrorType = self::$errorTypes[$errorDetails['errorType']];
				$detectedErrorType['id'] = $errorDetails['errorType'];
				$noteMessage = $this->getView('error-note', compact('blockId', 'block', 'errorDetails', 'detectedErrorType'));
				$this->processedBlocks++;
				echo $noteMessage;				
			}
		}
	}
	
	/**
	* Recheck blocks data when data saved.
	* 
	* Callback for cjt_data_saved hook.
	* 
	* @return void
	*/
	public function recheckBlocks($blocks) {
		// Check blocks everytime blocks is updated.
		$this->upgrade_checkBlocks($blocks, false);
	}
	
	/**
	* Override cssJSToolbox::start_plugin method.
	*/
	public function start_plugin() {
		add_action('cjt_block_after_code', array($this, 'noteMessage'), 10, 2);
		add_action('cjt_manage_form_end', array($this, 'deleteIfNoBlocks'));
		add_action('wp_ajax_cjtoolbox_v03e_checkCode', array($this, 'ajaxCheckCode'));
		add_action('cjt_data_saved', array($this, 'recheckBlocks'));
		$this->cjToolbox->start_plugin();
	}
	
	/**
	* Callback for cjt_upgrade hook.
	*/
	public function upgrade() {
		$this->upgrade_cleanUp();
		$this->upgrade_checkBlocks();
	}
	
	/**
	* When the plugin upgraded check if there are any blocks
	* contain errors.
	* 
	* @return void
	*/
	protected function upgrade_checkBlocks($blocks = null) {
		$destroyMySelf = true;
		$moduleId = $this->moduleInfo['id'];
		// NOTE: As a result of upgrading the Plugin
		// This conditional should processed only once
		// in Plugin life cycle.
		$blocks = $blocks ? $blocks : $this->cjToolbox->getData();
		// Copy for each block contain error.
		$allErrorTypes = json_encode(array_keys(self::$errorTypes));
		foreach ($blocks as $blockId => $block) {
			foreach (self::$errorTypes as $errorKey => $errorTypeDetails) {
				$blockMeta = (array) $block['meta'][$moduleId];
				$dismissed = (isset($blockMeta["dismissed-{$errorKey}"]) && ($blockMeta["dismissed-{$errorKey}"]));
				if (!$dismissed) {
					$errorDetectorName = $errorTypeDetails['detectionRoutine'];
					$matchesList = $this->$errorDetectorName($block['code']);
					if (is_array($matchesList)) {
						$blockErrorData = array(
				  		'allErrorTypes' => $allErrorTypes,
				  		'errorType' => $errorKey,
				  		'_list' => $matchesList, // _ is hidden and isn't populated by the CJToolbox plugin.
						);
						$blocks[$blockId]['meta'][$moduleId] = array_merge($blockMeta, $blockErrorData);
						$destroyMySelf = false; // Setting this more than one has no effect.
						break; // WHen one error type if found don't check the others.
					}
					else { // Dismiss error when the block become clean.
						unset($blocks[$blockId]['meta'][$moduleId]);
					}
				} // !$dismissed
			}
		}
		if ($destroyMySelf) {
			// Same case like new installation.
			// Don't run anymore.
			$this->install();
		}
		else {
			// Save blocks meta data.
			$this->cjToolbox->saveData($blocks);
		}
	}
	
	/**
	* Cleanup blocks and template code from the extra slashes.
	* 
	* @return void
	*/
	protected function upgrade_cleanUp() {
		global $wpdb;
		$templateTable = "{$wpdb->prefix}cjtoolbox_cjdata";
		/// Clean up blocks code.
		$blocks = (array) get_option(cssJSToolbox::BLOCKS_OPTION_NAME);
		foreach ($blocks as $id => $block) {
			// Remove extra slahses introduced from the first version 
			// as a result of the magic quotes.
			$blocks[$id]['code'] = stripcslashes($block['code']);
		}
		// Save clean blocks.
		update_option(cssJSToolbox::BLOCKS_OPTION_NAME, $blocks);
		/// Clean up code templates.
		$query = "SELECT type, id, code FROM {$templateTable}";
		$templates = $wpdb->get_results($query, ARRAY_A);
		foreach ($templates as $template) {
			$cleanCode = stripcslashes($template['code']);
			// Leave only id and type as update keys.
			unset($template['code']);
			$wpdb->update($templateTable, array('code' => $cleanCode), $template);
		}
	}
	
} // End class.

// Put the module in action.
return CJTV03Error::getInstance($module);