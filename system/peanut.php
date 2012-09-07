<?php
	/** peanut **
	 * the small php framework
	 * (c) Phelipe de Sterlich | Moorea Software | Elco Sistemi srl
	 * -----
	 * file: system/peanut.php
	 * peanut core
	 **/

	session_start(); // inizializzo la sessione
	if (isset($_SESSION['lang'])) lang::setLang($_SESSION['lang']); // se previsto, imposto il linguaggio in base al dato indicato nella sessione

	$debug = array(); // inizializzo l'array per le informazioni di debugItem
	$mainTimerStart = microtime(true); // inizializzo il timer per il tempo di esecuzione complessivo

	// inizializzo le variabili per controller, action e id
	$controllerName = "";
	$actionName = "";
	$idValue = 0;

	// caricamento configurazione
	include SYSTEM.DS."config".DS."config.defaults.php"; // includo il file di configurazione di default
	if (file_exists(APP.DS."config".DS."config.application.php")) include APP.DS."config".DS."config.application.php"; // includo, se esiste, il file di configurazione dell'applicazione
	if (file_exists(APP.DS."config".DS."config.php")) include APP.DS."config".DS."config.php"; // includo, se esiste, il file di configurazione locale

	include "core.php";

	$timerStart = microtime(true);
	$cipher = new cipher(); // inizializzo la classe per codifica / decodifica
	$timerStop = microtime(true);
	if (Configure::read("debug")) debugItem("inizializzazione cipher", $timerStop - $timerStart);

	// se è abilitata la connessione al database
	if (Configure::read("database.enabled")) {
		// debug: avvio timer
		$timeStart = microtime(true);
		// eseguo la connessione al server mysql
		if (!mysql_connect(Configure::read("database.host"), Configure::read("database.username"), Configure::read("database.password"))) {
			// se non riesco, mostro un messaggio di errore
			die (__("system.mysql_server_connect_fail", array(":server" => Configure::read("database.host"), ":errore" => mysql_error())));
		}
		// eseguo la connessione al database
		if (!mysql_select_db(Configure::read("database.name"))) {
			// se non riesco, verifico se devo tentarne la creazione
			// se la creazione non è abilitata
			if (Configure::read("database.create") == false) {
				// mostro un messaggio di errore
				die (__("system.mysql_database_connect_fail", array(":database" => Configure::read("database.name"), ":errore" => mysql_error())));
			} else {
				if (!mysql_query("CREATE DATABASE " . Configure::read("database.name"))) {
					// mostro un messaggio di errore
					die (__("system.mysql_database_create_fail", array(":database" => Configure::read("database.name"), ":errore" => mysql_error())));
				} else {
					if (!mysql_select_db(Configure::read("database.name"))) {
						// mostro un messaggio di errore
						die (__("system.mysql_database_connect_fail", array(":database" => Configure::read("database.name"), ":errore" => mysql_error())));
					}
				}
			}
		}
		// imposto il charset a utf-8
		$charset = Configure::read("database.charset", "");
		if ($charset != "") {
			if (!mysql_query("SET CHARACTER SET " . $charset)) {
				// se non riesco, mostro un messaggio di errore
				die (__("system.mysql_set_charset_fail", array(":database" => Configure::read("database.name"), ":errore" => mysql_error())));
			}
		}
		// debug: stop timer
		$timeStop = microtime(true);
		// debug
		if (Configure::read("debug")) debugItem("apertura database", $timeStop - $timeStart);
	}

	getBasicVars();

	/*
		TODO : rendere utf-8 configurabile da opzioni
	*/
	header('Content-type: text/html; charset=utf-8');

	$timerStart = microtime(true);
	if (!controllerExists($controllerName."_controller")) {
		$controller = new StaticController();
	} else {
		$controllerClass = to_camel_case($controllerName."_controller", true);
		$controller = new $controllerClass($idValue); // creo un oggetto controller e, se presente, ne carico i parametri
	}
	$timerStop = microtime(true);
	if (Configure::read("debug")) debugItem("inizializzazione controller", $timerStop - $timerStart);

	// se l'azione non esiste nel controller, esce dall'applicazione mostrando il messaggio d'errore
	$timerStart = microtime(true);
	if (!method_exists($controller, $actionName)) {
		if ((file_exists(APP.DS."views".DS.$controllerName.DS.$controllerName."_".$actionName.".php")) OR (file_exists(SYSTEM.DS."views".DS.$controllerName.DS.$controllerName."_".$actionName.".php"))) {
			// $controller = new StaticController();
			$controller->staticPage();
		} else {
			die (__("system.method_not_found", array(":controller" => $controllerName, ":action" => $actionName)));
		}
	} else {
		$controller->$actionName();
	}
	$timerStop = microtime(true);
	if (Configure::read("debug")) debugItem("esecuzione azione", $timerStop - $timerStart);

	if ($controller->useTemplate) {
		$timerStart = microtime(true);
		$layoutContent = $controller->template->render();
		$timerStop = microtime(true);
		if (Configure::read("debug")) debugItem("rendering template", $timerStop - $timerStart);
	} else {
		$layoutContent = $controller->layoutContent;
	}

	if ($controller->useLayout) {
		$timerStart = microtime(true);
		$controller->layout->set(array("layoutTitle" => $controller->layoutTitle, "layoutContent" => $layoutContent));
		echo $controller->layout->render("layouts");
		$timerStop = microtime(true);
		if (Configure::read("debug")) debugItem("rendering layout", $timerStop - $timerStart);
	} else {
		echo $layoutContent;
	}

	$mainTimerStop = microtime(true);

	if (Configure::read("debug")) {
		debugItem("generazione pagina (tempo complessivo)", $mainTimerStop - $mainTimerStart);
		$debugTemplate = new Template("debug.php");
		$debugTemplate->set("debug", $debug);
		echo $debugTemplate->render();
	}

?>