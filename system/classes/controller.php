<?php
	/** peanut **
	 * the small php framework
	 * (c) Phelipe de Sterlich | Moorea Software | Elco Sistemi srl
	 * -----
	 * file: system/classes/controller.php
	 * classe base controller
	 **/

	class Controller extends base
	{
		public $useLayout = true; // flag per abilitare l'utilizzo di layout
		public $layout; // layout per l'output

		public $useTemplate = true; // flag per abilitare l'utilizzo di un template
		public $template; // template per il render

		protected $useModel = true; // flag per abilitare l'utilizzo di un model
		protected $modelName = ""; // nome del modello da utilizzare (default vuoto, viene generato a runtime)

		public $layoutContent = ""; // testo generato dal controller, da utilizzare se non viene usato un template
		public $layoutTitle = ""; // titolo della pagina, utilizzato nel layout

		protected $model; // modello per l'accesso ai dati
		protected $id = 0; // identificativo record
		protected $isAjax = false; // flag tipo richiesta

		/**
		 * controllerName
		 * nome del controller
		 *
		 * @var string
		 **/
		protected $controllerName = "";

		function __construct($id = 0) {
			global $controllerName, $actionName;

			// imposta la proprietà controllerName del controller
			$this->controllerName = $controllerName;

			// verifico se la richiesta è stata fatta via ajax
			if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') $this->isAjax = true;
			// se la richiesta è fatta via ajax
			if ($this->isAjax) {
				// disabilito l'uso del layout
				$this->useLayout = false;
				// disabilito il debug
				Configure::write("debug", false);
			}

			// imposto l'identificativo dell'oggetto
			if ($id) $this->id = $id;

			// creo il layout
			$this->layout = new Template();
			// imposto il nome del layout in base alla configurazione
			$this->layout->templateName = Configure::read("controller.layout");
			// imposto il titolo del layout in base alla configurazione
			$this->layoutTitle = Configure::read("layout.title");

			// creo il template
			$this->template = new Template();
			// imposto il nome del template in base al nome del controller
			$this->template->templateName = $controllerName.DS.$controllerName."_".$actionName.".php";
			// imposta nomie controller e action nel template
			$this->template->set(array(
				"controllerName" => $controllerName,
				"actionName" => $actionName,
				));

			// se è abilitato l'utilizzo del modello
			if ($this->useModel) {
				Debug::start("inizializzazione modello controller");
				// se il nome del modello non è assegnato, lo imposto in base al nome del controller
				if ($this->modelName == "") $this->modelName = str_replace("Controller", "Model", get_class($this));
				// leggo il nome del modello
				$modelName = $this->modelName;
				// creo il modello
				$this->model = new $modelName($this->id);
				Debug::stop("inizializzazione modello controller");

				// carico il record corrente (se presente) nel template
				if ($id) {
					$this->template->set("record", $this->model->getRecord());
				} else {
					$this->template->set("record", array());
				}
			}
		}

		/**
		 * funzione index
		 * visualizzazione elenco record - template generico, in caso di specificità viene sovrascritta da una funzione specifica nel controller
		 *
		 * @return void
		 * @author Phelipe de Sterlich
		 **/
		function index()
		{

		}

		/**
		 * funzione view
		 * visualizzazione record - template generico, in caso di specificità viene sovrascritta da una funzione specifica nel controller
		 *
		 * @return void
		 * @author Phelipe de Sterlich
		 **/
		function view()
		{

		}

		/**
		 * funzione staticPage
		 * visualizzazione pagina statica
		 *
		 * @return void
		 * @author Phelipe de Sterlich
		 */
		function staticPage() {
			global $controllerName, $actionName;
			// imposta il nome del template in base a controller / action specificati
			$this->template->templateName = $controllerName.DS.$controllerName."_".$actionName.".php";
		}

	}
?>