<?php
	/**
	 * Peanut Controller
	 *
	 * controller per gestione pagine esempio / default
	 *
	 * @package system > controllers
	 * @copyright Moorea Software
	 **/

	class PeanutController extends Controller
	{

		// disabilita l'uso del modello
		public $useModel = false;

		/**
		 * funzione __construct
		 * inizializzazione controller
		 *
		 * @return void
		 * @author Phelipe de Sterlich
		 **/
		function __construct($id = 0)
		{
			parent::__construct($id);
			// imposta il nome dell'applicazione per layout e template
			$this->layout->set("appname", "Peanut!");
			$this->template->set("appname", "Peanut!");
		}

		function testpage() {
			global $cipher;
			$encoded = $cipher->encrypt("testo da codificare");
			$this->template->set(array(
				"uuid" => security::uuid(),
				"hash" => security::hash("testo da codificare"),
				"implode" => arrays::implode(array("campo1" => "valore1", "campo2" => "valore2"), "=", " AND ", "'", "mysql_real_escape_string"),
				"cipher_encrypt" => $encoded,
				"cipher_decrypt" => $cipher->decrypt($encoded),
				));
		}

		function ajaxhashtest() {
			$this->useTemplate = false;
			echo security::hash($_POST["testo"]);
		}
	}
	

?>