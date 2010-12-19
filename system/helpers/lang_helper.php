<?php
	/**
	 * lang_helper.php
	 *
	 * funzioni di internazionalizzazione
	 *
	 * @author    Phelipe de Sterlich
	 * @copyright Moorea Software | Elco Sistemi srl
	 * @package   peanut
	 **/

	class lang
	{
		/**
		 * classe lang
		 *
		 * funzioni di internazionalizzazione
		 *
		 * @author    Phelipe de Sterlich
		 * @copyright Moorea Software | Elco Sistemi srl
		 * @package   peanut
		 */

		protected static $lang = "it-it";

		protected static $cache = array();

		public static function setLang($lang = "")
		{
			if ($lang != "") lang::$lang = $lang;
		}

		public static function getLang()
		{
			return lang::$lang;
		}

		public static function get($testo, $lang = "")
		{
			if ($lang == "") $lang = lang::$lang;

			if (!isset(lang::$cache[$lang])) {
				lang::load($lang);
			}

			return (isset(lang::$cache[$lang][$testo])) ? lang::$cache[$lang][$testo] : $testo;
		}

		protected static function load($lang)
		{
			lang::$cache[$lang] = (file_exists(APP.DS."i18n".DS."{$lang}.php")) ? utils::loadFile(APP.DS."i18n".DS."{$lang}.php") : array();
			if (file_exists(SYSTEM.DS."i18n".DS."{$lang}.php")) lang::$cache[$lang] = array_merge(lang::$cache[$lang], utils::loadFile(SYSTEM.DS."i18n".DS."{$lang}.php"));
		}

	}

?>