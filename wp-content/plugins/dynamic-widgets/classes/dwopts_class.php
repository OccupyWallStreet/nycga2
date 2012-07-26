<?php
/**
 * DWOpts class
 *
 * @version $Id$
 * @copyright 2011 Jacco Drabbe
 */

	class DWOpts {
		public  $act;
		public  $checked = 'checked="checked"';
		public  $count;
		public  $default;
		private $type;

		/**
		 * DWOpts::__construct() Create DWOpts object
		 *
		 * @param object $result SQL object of DWOpt query
		 * @param string $type Name of Opts
		 */
		public function __construct($result, $type) {
			$this->act = array();
			$this->count = count($result);
			$this->type = $type;
			if ( $this->count > 0 ) {
				foreach ( $result as $condition ) {
					if ( $condition->maintype == $this->type ) {
						if ( $condition->name == 'default' || empty($condition->name) ) {
							$this->default = $condition->value;
						} else {
							$this->act[ ] = $condition->name;
						}
					}
				}
			} else {
				$this->default = '1';
			}

			// in some cases the default is (still) null
			if ( is_null($this->default) ) {
				$this->default = '1';
				$this->count = 0;
			}
		}

		/**
		 * DWOpts::selectNo() Checks if radiobutton 'No' should be selected
		 *
		 * @return boolean
		 */
		public function selectNo() {
			if ( $this->default == '0' ) {
				return TRUE;
			}
			return FALSE;
		}

		/**
		 * DWOpts::selectYes() Checks if radiobutton 'Yes' should be selected
		 *
		 * @return boolean
		 */
		public function selectYes() {
			if ( $this->default == '1' ) {
				return TRUE;
			}
			return FALSE;
		}
	}
?>