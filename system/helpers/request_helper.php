<?php 

	/**
	 * helper request
	 * lettura parametri GET / POST
	 *
	 * @package peanut
	 * @author Phelipe de Sterlich
	 **/
	class request
	{

		/**
		 * funzione read
		 * legge il valore di un parametro eventualmente presente nella richiesta
		 *
		 * @return variant
		 * @author Phelipe de Sterlich
		 **/
		public static function read($valueName, $default = "")
		{
			$result = $default;
			if (isset($_REQUEST[$valueName])) $result = $_REQUEST[$valueName];
			return $result;
		}

	}

?>