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

		function __construct($id = 0) {
			global $controllerName, $actionName, $config;

			// verifico se la richiesta è stata fatta via ajax
			if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') $this->isAjax = true;
			// se la richiesta è fatta via ajax
			if ($this->isAjax) {
				// disabilito l'uso del layout
				$this->useLayout = false;
				// disabilito il debug
				$config["debug"] = false;
			}

			// imposto l'identificativo dell'oggetto
			if ($id) $this->id = $id;

			// creo il layout
			$this->layout = new Template();
			// imposto il nome del layout in base alla configurazione
			$this->layout->templateName = $config["controller"]["layout"];
			// imposto il titolo del layout in base alla configurazione
			$this->layoutTitle = $config["layout"]["title"];

			// creo il template
			$this->template = new Template();
			// imposto il nome del template in base al nome del controller
			$this->template->templateName = $controllerName.DS.$controllerName."_".$actionName.".php";
			
			// se è abilitato l'utilizzo del modello
			if ($this->useModel) {
				$timerStart = microtime(true);
				// se il nome del modello non è assegnato, lo imposto in base al nome del controller
				if ($this->modelName == "") $this->modelName = str_replace("Controller", "Model", get_class($this));
				// leggo il nome del modello
				$modelName = $this->modelName;
				// creo il modello
				$this->model = new $modelName($this->id);
				$timerStop = microtime(true);
				if ($config["debug"]) debugItem("inizializzazione modello controller", $timerStop - $timerStart);

				// carico il record corrente (se presente) nel template
				if ($id) $this->template->set("record", $this->model->getRecord());
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

	}

	/*
	protected $modelName;
	protected $model = false;
	protected $oldModel = false;
	protected $id = 0;
	protected $currentUser = array();
	protected $controllerName;

	function __construct($id = 0) {
		if ($id) $this->id = $id;
		$this->modelName = str_replace("Controller", "Model", get_class($this));
		$this->model = new $this->modelName($this->id);
		$this->oldModel = new $this->modelName($this->id);
		$this->currentUser = database::select('utenti', '*', array('username' => $_SESSION["username"]));

		// inizializzazione classe zend gdata
		require_once 'Zend/Loader.php';
		Zend_Loader::loadClass('Zend_Gdata');
		// Zend_Loader::loadClass('Zend_Gdata_AuthSub');
		Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
		Zend_Loader::loadClass('Zend_Gdata_Calendar');
	}
	
	*/
?>