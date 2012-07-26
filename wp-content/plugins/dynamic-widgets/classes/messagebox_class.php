<?php
/**
 * DWMessageBox class
 *
 * @version $Id$
 * @copyright 2011 Jacco Drabbe
 */

	class DWMessageBox {
		private static $leadtext;
		private static $message;
		public static  $type;

		/**
		 * DWMessageBox::create() Create and output a message box
		 *
		 * @param string $lead Lead of the messagebox
		 * @param string $msg Message
		 */
		public static function create($lead, $msg) {
			self::setlead($lead);
			self::setMessage($msg);
			self::output();
		}

		/**
		 * DWMessageBox::output() Output of messagebox
		 *
		 */
		public static function output() {
			switch ( self::$type ) {
				case 'error':
					$class = 'error';
					break;
				default:
					$class = 'updated fade';
			}

			echo '<div class="' . $class . '" id="message">';
			echo '<p>';
			if (! empty(self::$leadtext) ) {
				echo '<strong>' . self::$leadtext . '</strong> ';
			}
			echo self::$message;
			echo '</p>';
			echo '</div>';
		}

		/**
		 * DWMessageBox::setLead() Set lead of messagebox
		 *
		 * @param string $text Text of the lead
		 */
		public static function setLead($text) {
			self::$leadtext = $text;
		}

		/**
		 * DWMessageBox::setMessage() Set message of messagebox
		 *
		 * @param string $text Message
		 */
		public static function setMessage($text) {
			self::$message = $text;
		}

		/**
		 * DWMessageBox::setTypeMsg() Set type of messagebox
		 *
		 * @param string $type Type
		 */
		public static function setTypeMsg($type) {
			self::$type = $type;
		}
	}
?>