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

		public function set($var, $val = null) {
			/**
			 * set: impostazione variabili (utile per concatenazione)
			 */
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
			return $this;
		}
	}
?>