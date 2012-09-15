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
		 * variabile $_id
		 * identificativo record
		 *
		 * @var integer
		 **/
		protected static $_id = 0;

		/**
		 * funzione init
		 * inizializzazione classe, lettura e impostazione controller, action e id
		 *
		 * @return void
		 * @author Phelipe de Sterlich
		 **/
		public static function init()
		{
			// leggo il nome del controller
			if (isset($_GET["controller"])) {
				self::$_controller = $_GET["controller"];
			} elseif (isset($_POST["controller"])) {
				self::$_controller = $_POST["controller"];
			} else {
				self::$_controller = Configure::read("routes.controller");
			}

			// leggo il nome dell'action
			if (isset($_GET["action"])) {
				self::$_action = $_GET["action"];
			} elseif (isset($_POST["action"])) {
				self::$_action = $_POST["action"];
			} else {
				self::$_action = Configure::read("routes.action");
			}

			// leggo l'identificativo record
			if (isset($_GET["id"])) {
				self::$_id = $_GET["id"];
			} elseif (isset($_POST["id"])) {
				self::$_id = $_POST["id"];
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

	} // END class Router

?>