<?php

	/**
	 * classe Configure
	 * lettura configurazione
	 *
	 * @package Peanut!
	 * @author Phelipe de Sterlich
	 **/
	class Configure
	{

		/**
		 * variabile $_loaded
		 * flag configurazione caricata
		 *
		 * @var bool
		 **/
		protected static $_loaded = false;

		/**
		 * variabile $_config
		 *
		 * @var array
		 **/
		protected static $_config = array();

		/**
		 * funzione _loadConfig
		 * caricamento configurazione
		 *
		 * @return void
		 * @author Phelipe de Sterlich
		 **/
		protected static function _loadConfig()
		{
			global $config;
			self::$_config = $config;
			self::$_loaded = true;
		}

		/**
		 * funzione read
		 * legge un valore dalla configurazione
		 * - $config["nome"] deve essere letto con Configure::read("nome")
		 * - $config["nome"]["parametro"] deve essere letto con Configure::read("nome.parametro")
		 *
		 * @return variant
		 * @author Phelipe de Sterlich
		 * -----
		 * codice originale da CakePHP(tm) : Rapid Development Framework (http://cakephp.org) - parzialmente adattato
		 **/
		public static function read($path, $default = null)
		{
			if (!self::$_loaded) {
				self::_loadConfig();
			}

			$result = $default;

			$data = self::$_config;
			if (empty($data) || empty($path)) {
				$result = $default;
			}
			if (is_string($path)) {
				$parts = explode('.', $path);
			} else {
				$parts = $path;
			}
			foreach ($parts as $key) {
				if (is_array($data) && isset($data[$key])) {
					$data =& $data[$key];
				} else {
					$result = $default;
				}

			}

			$result = $data;
			return $result;
		}

		/**
		 * funzione write
		 * scrive un valore nella configurazione
		 *
		 * @return void
		 * @author Phelipe de Sterlich
		 **/
		public static function write($path, $value)
		{
			if (!self::$_loaded) {
				self::_loadConfig();
			}

			if (array_key_exists($path, self::$_config)) {
				self::$_config[$path] = $value;
			}
		}

	} // END class Configur
?>