<?php
/**
 * QTranslate Module
 *
 * @version $Id: qtranslate_module.php 420121 2011-08-06 17:56:22Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	class DW_QT extends DWModule {
		protected static $except = 'Except the languages';
		protected static $info = 'Using this option can override all other options.';
		public static $option = array( 'qt'	=> 'Language (QTranslate)' );
		protected static $overrule = TRUE;
		protected static $question = 'Show widget default on all languages?';
		public static $plugin = array( 'qt' => FALSE );
		protected static $type = 'complex';

		public static function admin() {
			parent::admin();

			if ( self::detect() ) {
				$list = array();
				$qt_langs = get_option('qtranslate_enabled_languages');
				foreach ( $qt_langs as $code ) {
					$list[$code] = self::getQTLanguage($code);
				}

				self::mkGUI(self::$type, self::$option[self::$name], self::$question, self::$info, self::$except, $list);
			}
		}

		public static function detect($update = TRUE) {
			$DW = $GLOBALS['DW'];
			$DW->qt = FALSE;

			if ( defined('QTRANS_INIT') ) {
				self::checkOverrule('DW_QT');
				if ( $update ) {
					$DW->qt = TRUE;
				}
				return TRUE;
			}
			return FALSE;
		}

		public static function detectLanguage() {
			$DW = &$GLOBALS['DW'];

			if ( self::detect(FALSE) ) {
				$qtlang = get_option('qtranslate_default_language');
				$curlang = qtrans_getLanguage();
				$DW->message('QT language: ' . $curlang);

				if ( $qtlang != $curlang ) {
					$DW->qt = TRUE;
					$DW->message('QT enabled');
				}

				return $curlang;
			}
		}

		protected static function getQTLanguage($lang) {
			global $q_config;
			return $q_config['language_name'][$lang];
		}
	}
?>