<?php
	/** peanut **
	 * the small php framework
	 * (c) Phelipe de Sterlich | Moorea Software | Elco Sistemi srl
	 * -----
	 * file: system/helpers/arrays_helper.php
	 * helper per funzioni array
	 **/

	class arrays
	{

		public static function implode($array, $valueGlue, $itemGlue, $valueTerms = "", $callback = "") {
			/** arrays_helper **
			 * funzione implode
			 * processa un array per generare una stringa
			 * -- input --
			 * $array (array) array da processare
			 * $valueGlue (string) testo da inserire tra chiave e valore
			 * $itemGlue (string) testo da inserire tra un elemento e il successivo
			 * $valueTerms (string) terminatore da inserire prima e dopo ogni valore
			 * $callback (string) eventuale funzione di callback da utilizzare sull'array prima di processarlo
			 * -- output --
			 * (string) array processato
			 */

			if ($callback != "") $array = array_map($callback, $array);
			$result = "";
			foreach ($array as $key => $value) {
				if ($result != "") $result .= $itemGlue;
				$result .= $key.$valueGlue.$valueTerms.$value.$valueTerms;
			}

			return $result;
		}

		public static function attributes ($attr = null) {
			if (isset($attr) AND ($attr != null)) {
				if (!is_array($attr)) {
					return $attr;
				} else {
					return arrays::implode($attr, '=', " ", "'");
					/*
					$attributes = "";
					foreach ($attr as $key => $val) {
						$attributes .= $key.'="'.$val.'" ';
					}
					return $attributes;
					*/
				}
			}
		}

		public static function defaults($options, $defaults) {
			if ($options == null) $options = array();

			foreach ($defaults as $key => $value) {
				if (!array_key_exists($key, $options)) $options[$key] = $value;
			}

			return $options;
		}

		/**
		 * funzione getVal
		 * ritorna il valore di un elemento di un array
		 *
		 * @param  $array   array   insieme di elementi in cui effettuare la ricerca
		 * @param  $key     variant chiave da cercare
		 * @param  $default variant valore di default (se chiave non trovata)
		 * @return variant
		 * @author Phelipe de Sterlich
		 **/
		public static function getVal($array, $key, $default = "")
		{
			if (array_key_exists($key, $array)) {
				return $array[$key];
			} else {
				return $default;
			}
		}
	}

?>