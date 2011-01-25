<?php
	/** peanut **
	 * the small php framework
	 * (c) Phelipe de Sterlich | Moorea Software
	 * -----
	 * file: system/classes/base.php
	 * classe base
	 **/
	
	class base
	{

		function __construct() {
			/**
			 * __construct: inizializzazione classe
			 */
			# code...
		}

		public function __set($var, $value) {
			/**
			 * magic __set method: imposta il valore del parametro indicato da $var a $value
			 */
			if (isset($this->$var))
			{
				$this->$var = $value;
			}
		}

		public function __get($var) {
			/**
			 * magic __get method: ritorna il valore del parametro indicato da $var
			 */
			if (isset($this->$var))
			{
				return $this->$var;
			}
		}

		/**
		 * funzione set
		 * imposta il valore di una o piu' proprietà dell'oggetto
		 *
		 * @concatenabile
		 * @param  mixed $var (array) array associativo nome => valore
		 *                    (string) nome della proprietà da impostare
		 * @param  mixed $val (opzionale) se $var è una stringa, $value rappresenta il valore da impostare sulla proprietà
		 * @return object
		 * @author Phelipe de Sterlich
		 */
		public function set($var, $val = null)
		{
			if (is_array($var))
			{
				foreach ($var as $key => $value) {
					$this->__set($key, $value);
				}
			}
			else
			{
				$this->__set($var, $val);
			}
			// ritorna l'oggetto (per concatenazione)
			return $this;
		}
	}
?>