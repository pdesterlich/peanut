<?php

	/**
	 * helper Session
	 * funzioni di supporto per sessioni
	 *
	 * @package peanut
	 * @author Phelipe de Sterlich
	 **/
	class session
	{
		/**
		 * funzione add
		 * aggiunge alla sessione uno o piu' valori
		 *
		 * @param  $vars array array chiave => valore dei parametri da impostare
		 * @return void
		 * @author Phelipe de Sterlich
		 **/
		public static function add($vars)
		{
			// per ogni coppia chiave => valore dell'array
			foreach ($vars as $key => $value) {
				// aggiunge la chiave alla sessione con il relativo valore
				$_SESSION[$key] = $value;
			}
		}

		/**
		 * funzione get
		 * legge un valore dalla sessione
		 *
		 * @param  string $var     nome del valore da ritornare
		 * @param  mixed  $default (opzionale) valore di default, in caso il parametro non sia presente (default: "")
		 * @return mixed
		 * @author Phelipe de Sterlich
		 **/
		public static function get($var, $default = "")
		{
			// se il parametro esiste nella sessione
			if (isset($_SESSION[$var])) {
				// ritorna il valore del parametro
				return $_SESSION[$var];
			// altrimenti
			} else {
				// ritorna il valore di default
				return $default;
			}
		}

		/**
		 * funzione remove
		 * rimuove dalla sessione uno o piu' valori
		 *
		 * @param  $vars array/string chiavi/chiave da rimuovere dalla sessione
		 * @return void
		 * @author Phelipe de Sterlich
		 **/
		public static function remove($vars)
		{
			// se il parametro passato non è un array
			if (!is_array($vars)) {
				// rimuove dalla sessione la chiave passata come parametri
				unset($_SESSION[$vars]);
			} else {
				// se invece è un array, per ogni chiave presente nell'array
				foreach ($vars as $key) {
					// rimuove dalla sessione la chiave
					unset($_SESSION[$key]);
				}
			}
		}
	}

?>