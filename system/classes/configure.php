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
		 * variabile $_config
		 *
		 * @var string
		 **/
		protected static $_config = array();

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
		public static function read($path)
		{
			if (empty(self::$_config)) {
				global $config;
				self::$_config = $config;
			}

			$data = self::$_config;
			if (empty($data) || empty($path)) {
				return null;
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
					return null;
				}

			}
			return $data;
		}

	} // END class Configur
?>