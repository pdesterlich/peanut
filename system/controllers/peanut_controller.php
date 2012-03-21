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

	}
	

?>