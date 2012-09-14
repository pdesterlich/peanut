<?php

	/**
	 * classe Debug
	 * gestione dati debug applicazione
	 *
	 * @package Peanut!
	 * @author Phelipe de Sterlich
	 **/
	class Debug
	{

		/**
		 * $items
		 * elementi di debug
		 *
		 * @var array
		 **/
		protected static $items = array();

		/**
		 * funzione start
		 * inizia la registrazione di un elemento nel debug
		 *
		 * @return void
		 * @author Phelipe de Sterlich
		 **/
		public static function start($element)
		{
			$time = microtime(true);
			self::$items[$element]["start"] = $time;
			self::$items[$element]["stop"]  = $time;
			self::$items[$element]["time"]  = 0;
		}

		/**
		 * funzione stop
		 * termina la registrazione di un elemento nel debug
		 *
		 * @return void
		 * @author Phelipe de Sterlich
		 **/
		public static function stop($element = "", $options = array())
		{
			if ($element == "") {
				end(self::$items);
				$element = key(self::$items);
			}

			self::$items[$element]["stop"] = microtime(true);
			self::$items[$element]["time"] = self::$items[$element]["stop"] - self::$items[$element]["start"];
			foreach ($options as $key => $value) {
				self::$items[$element]["data"][$key] = $value;
			}
		}

		/**
		 * funzione add
		 * aggiunge una elemento di debug senza registrazione dei tempi
		 *
		 * @return void
		 * @author Phelipe de Sterlich
		 **/
		public static function add($element, $options = array())
		{
			self::$items[$element]["time"] = 0;
			foreach ($options as $key => $value) {
				self::$items[$element]["data"][$key] = $value;
			}
		}

		/**
		 * funzione get
		 * ritorna l'array con le voci di debug
		 *
		 * @return array
		 * @author Phelipe de Sterlich
		 **/
		public static function get()
		{
			return self::$items;
		}

	} // END class Debug

?>