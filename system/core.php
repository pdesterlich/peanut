<?php

	/**
	 * core.php
	 *
	 * funzioni base
	 *
	 * @author    Phelipe de Sterlich
	 * @copyright Moorea Software | Elco Sistemi srl
	 * @package   peanut
	 **/

	function debugItem($item, $time, $details = "") {
		/** peanut **
		 * funzione debugItem
		 * aggiunge le informazioni passate all'array di debug
		 * -- input --
		 * $item (string) oggetto del debug
		 * $time ($float) tempo di esecuzione
		 * $details (string) eventuali informazioni aggiuntive
		 **/

		global $debug, $config;
		// se il sistema è in modalità di debug, aggiungo le informazioni all'array di debug
		if ($config["debug"]) $debug[] = array("item" => $item, "time" => sprintf("%.6f", $time), "details" => $details);
	}

	function dump($var, $return = false) {
		/** peanut **
		 * funzione dump
		 * mostra il dump della variabile passata, formattandolo opportunamente per la corretta visualizzazione
		 * -- input --
		 * $var (variant) variabile da visualizzare
		 * $return (boolean) (opzionale) se True il debug viene ritornato come stringa e non mandato in output
		 * -- output --
		 * (string) (se $return = True) dump della variabile passata
		 **/

		// ottengo il dump della variabile, incluso in un tag html <pre>
		$result = "<pre>".print_r($var, true)."</pre>";
		// ritorno il risultato o lo mando in output a seconda del valore di $return
		if ($return) {
			return $result;
		} else {
			echo $result;
		}
	}

	function from_camel_case($str) {
		/**
		* Translates a camel case string into a string with underscores (e.g. firstName -&gt; first_name)
		* @param    string   $str    String in camel case format
		* @return    string            $str Translated into underscore format
		**/
		$str[0] = strtolower($str[0]);
		$func = create_function('$c', 'return "_" . strtolower($c[1]);');
		return preg_replace_callback('/([A-Z])/', $func, $str);
	}

	function to_camel_case($str, $capitalise_first_char = false) {
		/**
		* Translates a string with underscores into camel case (e.g. first_name -&gt; firstName)
		* @param    string   $str                     String in underscore format
		* @param    bool     $capitalise_first_char   If true, capitalise the first char in $str
		* @return   string                              $str translated into camel caps
		**/
		if($capitalise_first_char) {
			$str[0] = strtoupper($str[0]);
		}
		$func = create_function('$c', 'return strtoupper($c[1]);');
		return preg_replace_callback('/_([a-z])/', $func, $str);
	}

	function __autoload($class_name) {
		$className = from_camel_case($class_name);
		// modelli
		if (file_exists(APP.DS."models".DS."{$className}.php")) { require_once APP.DS."models".DS."{$className}.php"; }
		else if (file_exists(SYSTEM.DS."models".DS."{$className}.php")) { require_once SYSTEM.DS."models".DS."{$className}.php"; }
		// controller
		else if (file_exists(APP.DS."controllers".DS."{$className}.php")) { require_once APP.DS."controllers".DS."{$className}.php"; }
		else if (file_exists(SYSTEM.DS."controllers".DS."{$className}.php")) { require_once SYSTEM.DS."controllers".DS."{$className}.php"; }
		// helpers
		else if (file_exists(APP.DS."helpers".DS."{$className}_helper.php")) { require_once APP.DS."helpers".DS."{$className}_helper.php"; }
		else if (file_exists(SYSTEM.DS."helpers".DS."{$className}_helper.php")) { require_once SYSTEM.DS."helpers".DS."{$className}_helper.php"; }
		// altre classi
		else if (file_exists(APP.DS."classes".DS."{$className}.php")) { require_once APP.DS."classes".DS."{$className}.php"; }
		else if (file_exists(SYSTEM.DS."classes".DS."{$className}.php")) { require_once SYSTEM.DS."classes".DS."{$className}.php"; }
		else { die (__("system.file_not_found", array(":filename" => $className))); }
	}

	/**
	 * funzione loadClass
	 * crea e ritorna un'istanza di una classe, eventualmente caricandone anche la relativa libreria
	 *
	 * @param  className string nome della classe da creare
	 * @param  libName   string (opzionale) nome della libreria da caricare prima di creare la classe
	 * @return object
	 * @author Phelipe de Sterlich
	 **/
	function loadClass($className, $libName = "")
	{
		if ($libName != "") {
			if (file_exists(APP.DS."libs".DS."{$libName}.php")) { require_once APP.DS."libs".DS."{$libName}.php"; }
			else if (file_exists(SYSTEM.DS."libs".DS."{$className}.php")) { require_once SYSTEM.DS."libs".DS."{$className}.php"; }
		}
		return new $className();
	}

	function getBasicVars() {
		global $config, $controllerName, $actionName, $idValue;

		// leggo il nome del controller
		if (isset($_GET["controller"])) {
			$controllerName = $_GET["controller"];
		} elseif (isset($_POST["controller"])) {
			$controllerName = $_POST["controller"];
		} else {
			$controllerName = $config["routes"]["controller"];
		}
		// leggo il nome dell'action
		if (isset($_GET["action"])) {
			$actionName = $_GET["action"];
		} elseif (isset($_POST["action"])) {
			$actionName = $_POST["action"];
		} else {
			$actionName = $config["routes"]["action"];
		}
		// leggo l'identificativo record
		if (isset($_GET["id"])) {
			$idValue = $_GET["id"];
		} elseif (isset($_POST["id"])) {
			$idValue = $_POST["id"];
		} else {
			$idValue = 0;
		}
		if (isset($_SERVER["REQUEST_URI"]))
		{
			$pathInfo = $_SERVER["REQUEST_URI"];
			if (substr($pathInfo, 0, 1) == "/") $pathInfo = substr($pathInfo, 1);
			if (substr($pathInfo, -1, 1) == "/") $pathInfo = substr($pathInfo, 0, -1);
			if (trim($pathInfo) != "") {
				$pathInfo = explode("/", $pathInfo);
				$pathInfoCount = count($pathInfo);
				if ($pathInfoCount > 0) $controllerName = $pathInfo[0];
				if ($pathInfoCount > 1) $actionName = $pathInfo[1];
				if ($pathInfoCount > 2) $idValue = $pathInfo[2];
			}
		}
	}

	function __($testo, $params = null, $lang = "")
	{
		/**
		 * funzione __
		 *
		 * ritorna la traduzione di un testo, eventualmente sostituendone
		 * alcune parti con i parametri passati
		 * il testo non viene "tradotto", ma viene cercata una corrispondenza
		 * nell'array della lingua
		 *
		 * @access public
		 * @param  string $testo   testo da tradurre
		 * @param  array  $params  parametri di sostituzione ("cerca" => "sostituisci", "cerca" => "sostituisci", ...)
		 * @param  string $lang    lingua da utilizzare (se non specificata viene utilizzata quella di default)
		 * @return string          testo tradotto
		 */

		$testo = lang::get($testo, $lang); // cerca la traduzione del testo
		return ($params == null) ? $testo : strtr($testo, $params); // ritorna il testo tradotto, eventualmente sostituendo i parametri
	}

	function ___($testo, $params = null, $lang = "")
	{
		/**
		 * funzione ___
		 *
		 * manda in output la traduzione di un testo, eventualmente sostituendone
		 * alcune parti con i parametri passati
		 * il testo non viene "tradotto", ma viene cercata una corrispondenza
		 * nell'array della lingua
		 *
		 * @access public
		 * @param  string $testo   testo da tradurre
		 * @param  array  $params  parametri di sostituzione ("cerca" => "sostituisci", "cerca" => "sostituisci", ...)
		 * @param  string $lang    lingua da utilizzare (se non specificata viene utilizzata quella di default)
		 * @return                 manda in output il testo tradotto
		 */

		echo __($testo, $params, $lang);
	}
?>