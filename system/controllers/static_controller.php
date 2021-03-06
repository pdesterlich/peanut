<?php
	/**
	 * Static Controller
	 *
	 * controller per gestione pagine statiche (senza controller / action specificata)
	 *
	 * @package system > controllers
	 * @copyright Moorea Software
	 **/

	class StaticController extends Controller
	{

		// disabilita l'uso del modello
		public $useModel = false;

		/**
		 * funzione index
		 * visualizzazione pagina statica
		 *
		 * @return void
		 * @author Phelipe de Sterlich
		 */
		function index() {
			// imposta il nome del template in base a controller / action specificati
			$this->template->templateName = Router::controller().DS.Router::controller()."_".Router::action().".php";
		}

	}
	

?>