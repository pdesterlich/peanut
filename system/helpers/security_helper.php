<?php
	/**
	 * security_helper.php
	 *
	 * funzioni di utilità specifiche per la sicurezza
	 *
	 * @author    Phelipe de Sterlich
	 * @copyright Moorea Software | Elco Sistemi srl
	 * @package   moorea
	 **/

	class security
	{
		/**
		 * classe security
		 *
		 * funzioni di utilità specifiche per la sicurezza
		 *
		 * @author    Phelipe de Sterlich
		 * @copyright Moorea Software | Elco Sistemi srl
		 * @package   moorea
		 */

		public static function uuid() {
			/**
			 * funzione uuid
			 *
			 * generazione uuid (universally unique identifier)
			 *
			 * @return string uuid generato
			 * @access public
			 * @since  0.0.1
			 *
			 * codice originale di Andrew Moore - http://www.php.net/manual/en/function.uniqid.php#94959
			 */

			// genero lo uuid
			return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X',
				// 32 bits for "time_low"
				mt_rand(0, 0xffff), mt_rand(0, 0xffff),
				// 16 bits for "time_mid"
				mt_rand(0, 0xffff),
				// 16 bits for "time_hi_and_version",
				// four most significant bits holds version number 4
				mt_rand(0, 0x0fff) | 0x4000,
				// 16 bits, 8 bits for "clk_seq_hi_res",
				// 8 bits for "clk_seq_low",
				// two most significant bits holds zero and one for variant DCE1.1
				mt_rand(0, 0x3fff) | 0x8000,
				// 48 bits for "node"
				mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
				);
		}

		public static function hash($str) {
			/**
			 * funzione hash
			 *
			 * generazione di un hash del testo passato come parametro
			 *
			 * @param  string $str testo di cui generare l'hash
			 * @return string      hash generato
			 * @access public
			 * @since  0.0.1
			 *
			 * basato su http://www.php.net/manual/en/function.md5.php#98314
			 */

			global $config;

			$str .= md5($config["security"]["salt"]); // aggiungo al testo il salt (stringa pseudo casuale per rendere più complesso il testo)
			return md5($str); // ritorno l'hash md5 del testo ottenuto
		}

	}

?>