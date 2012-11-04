<?php 

	/**
	 * helper request
	 * lettura parametri GET / POST / COOKIE
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
		 * @param $paramName   string  nome del parametro da leggere
		 * @param $default     variant valore di default da ritornare se il parametro non viene trovato (default: "")
		 * @param $readCookies boolean flag per cercare il parametro nei cookies (default: false)
		 * @return variant
		 * @author Phelipe de Sterlich
		 **/
		public static function read($paramName, $default = "", $readCookies = false)
		{
			$result = $default;
			if (isset($_REQUEST[$paramName])) {
				$result = $_REQUEST[$paramName];
			} else if ($readCookies == true) {
				$paramName = Configure::read("cookies.prefix")."_".$paramName;
				if (isset($_COOKIE[$paramName])) {
					$result = $_COOKIE[$paramName];
				}
			}
			return $result;
		}

		/**
		 * funzione setCookie
		 * imposta un cookie nel browser
		 *
		 * @param $paramName string  nome del parametro da scrivere
		 * @param $value     variant valore da scrivere
		 * @param $expire    variant data/ora di scadenza del cookie
		 *                   0 - il cookie scade al termine della sessione (ovvero quando si chiude il browser)
		 *                   "never" - il cookie scade 20 anni dopo la data/ora corrente
		 *                   unix timestamp - data/ora esatta di scadenza del cookie
		 * @return void
		 * @author Phelipe de Sterlich
		 **/
		public static function setCookie($paramName, $value = "", $expire = 0, $path = "/", $domain = "")
		{
			$paramName = Configure::read("cookies.prefix")."_".$paramName;
			if ($domain == "") $domain = Configure::read("url.base");
			if ($expire == "never") $expire = time() + (20 * 365 * 24 * 60 * 60);
			setcookie($paramName, $value, $expire, $path, $domain);
		}

		/**
		 * funzione exists
		 * controlla l'esistenza di un parametro nella richiesta
		 *
		 * @param $paramName   string  nome del parametro da controllare
		 * @param $readCookies boolean flag per cercare il parametro nei cookies (default: false)
		 * @return bool
		 * @author Phelipe de Sterlich
		 **/
		public static function exists($paramName, $readCookies = false)
		{
			$result = false;
			if (isset($_REQUEST[$paramName])) {
				$result = true;
			} else if ($readCookies == true) {
				if (isset($_COOKIE[Configure::read("cookies.prefix")."_".$paramName])) {
					$result = true;
				}
			}
			return $result;
		}
	}

?>