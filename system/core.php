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

	function _dump($var, $usePre) {
		/** peanut **
		 * funzione dump
		 * mostra il dump della variabile passata, formattandolo opportunamente per la corretta visualizzazione
		 * -- input --
		 * $var (variant) variabile da visualizzare
		 * $usePre (boolean) incapsula il dump in un blocco <pre>
		 * -- output --
		 * (string)
		 **/

		// ottengo il dump della variabile
		$result = print_r($var, true);

		// se necessario, aggiungo i tag <pre> per la visualizzazione html
		if ($usePre) $result = "<pre>".$result."</pre>";

		// ritorno il risultato
		return $result;
	}


	/**
	 * funzione dump
	 * mostra il dump della variabile passata, formattandolo opportunamente per la corretta visualizzazione
	 *
	 * @return string
	 * @author Phelipe de Sterlich
	 **/
	function dump($var, $return = false)
	{
		// ottengo il dump della variabile
		$result = _dump($var, true);

		if ($return) {
			return $result;
		} else {
			echo $result;
		}
	}

	/**
	 * funzione clDump
	 * dump variabile, da utilizzare nei tools da riga di comando
	 *
	 * @return string
	 * @author Phelipe de Sterlich
	 **/
	function clDump($var)
	{
		echo _dump($var, false)."\n";
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

	/**
	 * funzione findAlternateName
	 * ritorna il percorso alternativo per il nome passato, sostituendo tutte le occorrenze (tranne l'ultima) del _ con DS
	 **/
	function findAlternateName($originalName) {
		$result = $originalName;

		$isFirst = true;
		for ($i = strlen($result); $i >= 0; $i--) {
			if (substr($result, $i, 1) == "_") {
				if (!$isFirst) {
					$result = substr_replace($result, DS, $i, 1);
				}
				$isFirst = false;
			}
		}

		return $result;
	}

	function controllerExists($controllerName) {
		$altControllerName = findAlternateName($controllerName);
		return (file_exists(APP.DS."controllers".DS."{$controllerName}.php"))
			OR (file_exists(APP.DS."controllers".DS."{$altControllerName}.php"))
			OR (file_exists(SYSTEM.DS."controllers".DS."{$controllerName}.php"));
	}

	function __autoload($class_name) {
		$className = from_camel_case($class_name);
		$altClassName = findAlternateName($className);
		// modelli
		if (file_exists(APP.DS."models".DS."{$className}.php")) { require_once APP.DS."models".DS."{$className}.php"; }
		else if (file_exists(APP.DS."models".DS."{$altClassName}.php")) { require_once APP.DS."models".DS."{$altClassName}.php"; }
		else if (file_exists(SYSTEM.DS."models".DS."{$className}.php")) { require_once SYSTEM.DS."models".DS."{$className}.php"; }
		// controller
		else if (file_exists(APP.DS."controllers".DS."{$className}.php")) { require_once APP.DS."controllers".DS."{$className}.php"; }
		else if (file_exists(APP.DS."controllers".DS."{$altClassName}.php")) { require_once APP.DS."controllers".DS."{$altClassName}.php"; }
		else if (file_exists(SYSTEM.DS."controllers".DS."{$className}.php")) { require_once SYSTEM.DS."controllers".DS."{$className}.php"; }
		// helpers
		else if (file_exists(APP.DS."helpers".DS."{$className}_helper.php")) { require_once APP.DS."helpers".DS."{$className}_helper.php"; }
		else if (file_exists(SYSTEM.DS."helpers".DS."{$className}_helper.php")) { require_once SYSTEM.DS."helpers".DS."{$className}_helper.php"; }
		// altre classi
		else if (file_exists(APP.DS."classes".DS."{$className}.php")) { require_once APP.DS."classes".DS."{$className}.php"; }
		else if (file_exists(SYSTEM.DS."classes".DS."{$className}.php")) { require_once SYSTEM.DS."classes".DS."{$className}.php"; }
		else { die (__("system.file_not_found", array(":filename" => $className . " / " . $altClassName))); }
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
	function loadClass($className = "", $libName = "")
	{
		if ($libName != "") {
			if (file_exists(APP.DS."libs".DS."{$libName}.php")) { require_once APP.DS."libs".DS."{$libName}.php"; }
			else if (file_exists(SYSTEM.DS."libs".DS."{$libName}.php")) { require_once SYSTEM.DS."libs".DS."{$libName}.php"; }
		}
		if ($className != "") {
			return new $className();
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
