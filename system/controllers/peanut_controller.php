<?php
	/** peanut **
	 * the small php framework
	 * (c) Phelipe de Sterlich | Moorea Software
	 * -----
	 * file: system/controllers/peanut_controller.php
	 * controller default
	 **/

	class PeanutController extends Controller
	{

		public $useModel = false; // disabilito l'uso del modello

		function __construct($id = 0)
		{
			parent::__construct($id);
			$this->layout->set("appname", "Peanut!");
			$this->template->set("appname", "Peanut!");
		}

		function index() {

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