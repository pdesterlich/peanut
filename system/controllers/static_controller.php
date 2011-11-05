<?php
	/** peanut **
	 * the small php framework
	 * (c) Phelipe de Sterlich | Moorea Software
	 * -----
	 * file: system/controllers/static_controller.php
	 * controller Static
	 **/

	class StaticController extends Controller
	{

		public $useModel = false; // disabilito l'uso del modello

		function index() {
			global $controllerName, $actionName;
			$this->template->templateName = $controllerName.DS.$controllerName."_".$actionName.".php";
		}

	}
	

?>