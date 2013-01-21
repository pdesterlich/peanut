<?php

	/**
	 * classe Router
	 * gestione indirizzamento pagine
	 *
	 * @package Peanut!
	 * @author Phelipe de Sterlich
	 **/
	class Router
	{

		/**
		 * variabile $_controller
		 * nome del controller
		 *
		 * @var string
		 **/
		protected static $_controller = "";

		/**
		 * variabile $_action
		 * nome dell'azione
		 *
		 * @var string
		 **/
		protected static $_action = "";

		/**
		 * variabile $_method
		 * metodo della richiesta
		 *
		 * @var string
		 **/
		protected static $_method = "";

		/**
		 * variabile $_id
		 * identificativo record
		 *
		 * @var integer
		 **/
		protected static $_id = 0;


		/**
		 * funzione controller
		 * ritorna il nome del controller
		 *
		 * @return string
		 * @author Phelipe de Sterlich
		 **/
		public static function controller()
		{
			return self::$_controller;
		}

		/**
		 * funzione action
		 * ritorna il nome dell'action
		 *
		 * @return string
		 * @author Phelipe de Sterlich
		 **/
		public static function action()
		{
			return self::$_action;
		}

		/**
		 * funzione method
		 * ritorna il metodo con cui è stata eseguita la richiesta
		 *
		 * @return string
		 * @author Phelipe de Sterlich
		 **/
		public static function method()
		{
			return self::$_method;
		}

		/**
		 * funzione id
		 * ritorna l'identificativo del record
		 *
		 * @return integer
		 * @author Phelipe de Sterlich
		 **/
		public static function id()
		{
			return self::$_id;
		}

		/**
		 * funzione init
		 * inizializzazione classe, lettura e impostazione controller, action e id
		 *
		 * @return void
		 * @author Phelipe de Sterlich
		 **/
		public static function init()
		{
			// leggo il metodo della richiesta
			self::$_method = $_SERVER["REQUEST_METHOD"];

			// leggo il nome del controller
			if (isset($_REQUEST["controller"])) {
				self::$_controller = $_REQUEST["controller"];
			} else {
				self::$_controller = Configure::read("routes.controller");
			}

			// leggo il nome dell'action
			if (isset($_REQUEST["action"])) {
				self::$_action = $_REQUEST["action"];
			} else {
				self::$_action = Configure::read("routes.action");
			}

			// leggo l'identificativo record
			if (isset($_REQUEST["id"])) {
				self::$_id = $_REQUEST["id"];
			} else {
				self::$_id = 0;
			}

			// se è impostato un request e non inizia con /index.php
			if ((isset($_SERVER["REQUEST_URI"])) AND (strtolower(substr($_SERVER["REQUEST_URI"], 0, 10)) != "/index.php"))
			{
				$pathInfo = $_SERVER["REQUEST_URI"];
				// rimuove, se presente, la parte di parametri GET
				if ((isset($_SERVER["QUERY_STRING"])) AND ($_SERVER["QUERY_STRING"] != "")) $pathInfo = str_replace("?".$_SERVER["QUERY_STRING"], "", $pathInfo);
				// rimuove gli eventuali slash iniziale e finale
				if (substr($pathInfo, 0, 1)  == "/") $pathInfo = substr($pathInfo, 1);
				if (substr($pathInfo, -1, 1) == "/") $pathInfo = substr($pathInfo, 0, -1);
				// se quello che resta è una stringa non vuota
				if (trim($pathInfo) != "") {
					$pathInfo = explode("/", $pathInfo);
					$pathInfoCount = count($pathInfo);
					// legge, se presenti, controller, action e id
					if ($pathInfoCount > 0) self::$_controller = $pathInfo[0];
					if ($pathInfoCount > 1) self::$_action     = $pathInfo[1];
					if ($pathInfoCount > 2) self::$_id         = $pathInfo[2];
				}
			}
		}

		/**
		 * funzione route
		 * carica il controller ed esegue l'azione corrispondente
		 *
		 * @return void
		 * @author Phelipe de Sterlich
		 **/
		public static function route()
		{
			// se abilitate, esegue la registrazione delle statistiche di accesso
			if (Configure::read("stats.local.enabled")) {
				Debug::start("registrazione statistiche accesso");
				Stats::log();
				Debug::stop("registrazione statistiche accesso");
			}

			Debug::start("inizializzazione controller");
			if (!controllerExists(self::$_controller."_controller")) {
				$controller = new StaticController();
			} else {
				$controllerClass = to_camel_case(self::$_controller."_controller", true);
				$controller = new $controllerClass(self::$_id); // creo un oggetto controller e, se presente, ne carico i parametri
			}
			Debug::stop("inizializzazione controller");

			// se l'azione non esiste nel controller, esce dall'applicazione mostrando il messaggio d'errore
			Debug::start("esecuzione azione");
			if (!method_exists($controller, self::$_action)) {
				if ((file_exists(APP.DS."views".DS.self::$_controller.DS.self::$_controller."_".self::$_action.".php")) OR (file_exists(SYSTEM.DS."views".DS.self::$_controller.DS.self::$_controller."_".self::$_action.".php"))) {
					// $controller = new StaticController();
					$controller->staticPage();
				} else {
					die (__("system.method_not_found", array(":controller" => self::$_controller, ":action" => self::$_action)));
				}
			} else {
				$action = self::$_action;
				$controller->$action();
			}
			Debug::stop();

			if ($controller->useTemplate) {
				Debug::start("rendering template");
				$layoutContent = $controller->template->render();
				Debug::stop("rendering template");
			} else {
				$layoutContent = $controller->layoutContent;
			}

			if ($controller->useLayout) {
				Debug::start("rendering layout");
				$controller->layout->set(array("layoutTitle" => $controller->layoutTitle, "layoutContent" => $layoutContent));
				echo $controller->layout->render("layouts");
				Debug::stop("rendering layout");
			} else {
				echo $layoutContent;
			}
		}

	} // END class Router

?>