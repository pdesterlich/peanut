<?php
	/** peanut **
	 * the small php framework
	 * (c) Phelipe de Sterlich | Moorea Software | Elco Sistemi srl
	 * -----
	 * file: system/helpers/utils_helper.php
	 * helper per utility varie
	 **/

	class utils
	{

		public static function rand_str($length = 32, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890') {
			/** utils_helper **
			 * funzione rand_str
			 * generazione stringa casuale
			 * -- output --
			 * (string) stringa casuale
			 * -- source --
			 * codice originale di kyle dot florence - http://www.php.net/manual/en/function.rand.php#90773
			 */

			// Length of character list
			$chars_length = (strlen($chars) - 1);

			// Start our string
			$string = $chars{rand(0, $chars_length)};

			// Generate random string
			for ($i = 1; $i < $length; $i = strlen($string)) {
				// Grab a random character from our list
				$r = $chars{rand(0, $chars_length)};

				// Make sure the same two characters don't appear next to each other
				if ($r != $string{$i - 1}) $string .=  $r;
			}

			// Return the string
			return $string;
		}

		// --- funzioni di conversione ---

		public static function sql2date($data) {
			return (($data == "") OR ($data == "0000-00-00")) ? "" : date('d/m/Y', strtotime($data));
		}

		public static function sql2datetime($data, $advanced = false) {
			if ($data == "") {
				return "";
			} else if (!$advanced) {
				return date('d/m/Y H:i', strtotime($data));
			} else {
				$giorni = floor((mktime (0,0,0,date("m")  ,date("d")+1,date("Y")) - strtotime($data)) / (60*60*24));
				$format = "d/m H:i";
				switch ($giorni) {
					case 0:
						$format = "H:i";
						break;
					case 1:
						$format = "\i\e\\r\i H:i";
						break;
					default:
						break;
				}
				if (date("Y") != date("Y", strtotime($data))) $format = "d/m/Y H:i";
				return date($format, strtotime($data));
			}
		}

		public static function sql2time($time) {
			return strftime('%H:%M', strtotime($time));
		}

		public static function dateMath($dData, $value, $element = 'mday') {
			/**
			 * dateMath: aggiunge il valore indicato ad un timestamp
			 */
			$dData = getdate($dData);
			$dData[$element] = $dData[$element] + $value;
			return mktime($dData['hours'], $dData['minutes'], $dData['seconds'], $dData['mon'], $dData['mday'], $dData['year']);
		}

		public static function date2sql($data) {
			if ($data == "") {
				return "";
			} else {
				sscanf($data, "%d/%d/%d", $iGiorno, $iMese, $iAnno);
				return date("Y-m-d", mktime(0, 0, 0, $iMese, $iGiorno, $iAnno));
			}
		}

		public static function date2time($data) {
			sscanf($data, "%d/%d/%d", $iGiorno, $iMese, $iAnno);
			return mktime(0, 0, 0, $iMese, $iGiorno, $iAnno);
		}

		public static function loadFile($file) {
			return include $file;
		}

		/**
		 * funzione age
		 * calcola l'età partendo da una data (in formato yyyy-mm-dd)
		 *
		 * @param $data  date - data per cui calcolare l'età
		 * @param $isSql bool - true (default) se la data è in formato sql (yyyy-mm-dd), false se è in formato standard (dd/mm/yyyy)
		 * @return integer
		 * @author Phelipe de Sterlich
		 **/
		public static function age($data, $isSql = true)
		{
			if (!$isSql) $data = utils::date2sql($data);
			list($Y,$m,$d) = explode("-", $data);
    		return ( (date("md") < $m.$d) ? date("Y")-$Y-1 : date("Y")-$Y );
		}

		/**
		 * funzione intToStrTime
		 * converte un tempo da minuti a stringa
		 *
		 * @param $iTime integer tempo da convertire
		 * @return string
		 * @author Phelipe de Sterlich
		 **/
		public static function intToStrTime($iTime)
		{
			$ore = floor($iTime / 60);
			$minuti = $iTime % 60;
			return date("H:i", mktime($ore, $minuti, 0, 0, 0, 0));
		}

	}

?>