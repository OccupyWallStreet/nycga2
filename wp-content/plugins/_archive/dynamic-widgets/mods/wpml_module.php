<?php
/**
 * WPML Module
 *
 * @version $Id: wpml_module.php 523481 2012-03-25 19:49:08Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	class DW_WPML extends DWModule {
		public static $icon;
		protected static $info = 'Using this option can override all other options.';
		protected static $except = 'Except the languages';
		public static $option = array( 'wpml' => 'Language' );
		protected static $overrule = TRUE;
		protected static $question = 'Show widget default on all languages?';
		public static $plugin = array( 'wpml' => FALSE );
		protected static $type = 'complex';

		public static function admin() {
			parent::admin();

			if ( self::detect() ) {
				$wpml_api = ICL_PLUGIN_PATH . DW_WPML_API;
				require_once($wpml_api);

				$list = array();
				$wpml_langs = wpml_get_active_languages();
				foreach ( $wpml_langs as $lang ) {
					$code = $lang['code'];
					$list[$code] = $lang['display_name'];
				}

				self::mkGUI(self::$type, self::$option[self::$name], self::$question, self::$info, self::$except, $list);
			}
		}

		public static function detect($update = TRUE) {
			$DW = &$GLOBALS['DW'];
			$DW->wpml = FALSE;

			if ( defined('ICL_PLUGIN_PATH') && file_exists(ICL_PLUGIN_PATH . DW_WPML_API) ) {
				self::checkOverrule('DW_WPML');
				if ( $update ) {
					$DW->wpml = TRUE;
				}
				self::$icon = '<img src="' . $DW->plugin_url . DW_WPML_ICON . '" alt="WMPL" title="Dynamic Widgets syncs with other languages of these pages via WPML" style="position:relative;top:2px;" />';
				return TRUE;
			}
			return FALSE;
		}

		public static function detectLanguage() {
			$DW = &$GLOBALS['DW'];

			if ( self::detect() ) {
				$wpml_api = ICL_PLUGIN_PATH . DW_WPML_API;
				if ( file_exists($wpml_api) ) {
					require_once($wpml_api);

					$wpmlang = wpml_get_default_language();
					$curlang = wpml_get_current_language();
					$DW->message('WPML language: ' . $curlang);

					if ( $wpmlang != $curlang ) {
						$DW->wpml = TRUE;
						$DW->message('WPML enabled');
					}

					return $curlang;
				}
			}
		}

		public static function getID($content_id, $content_type = 'post_page') {
			$language_code = wpml_get_default_language();
			$lang = wpml_get_content_translation($content_type, $content_id, $language_code);

			if ( is_array($lang) ) {
				$id = $lang[$language_code];
			} else {
				$id = 0;
			}

			return $id;
		}
	}
?>