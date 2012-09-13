<?php
	/** peanut **
	 * the small php framework
	 * (c) Phelipe de Sterlich | Moorea Software | Elco Sistemi srl
	 * -----
	 * file: system/peanut.php
	 * peanut core
	 **/

	session_start(); // inizializzo la sessione

	// inizializzo le variabili per controller, action e id
	$controllerName = "";
	$actionName = "";
	$idValue = 0;

	// caricamento configurazione
	include SYSTEM.DS."config".DS."config.defaults.php"; // includo il file di configurazione di default
	if (file_exists(APP.DS."config".DS."config.application.php")) include APP.DS."config".DS."config.application.php"; // includo, se esiste, il file di configurazione dell'applicazione
	if (file_exists(APP.DS."config".DS."config.php")) include APP.DS."config".DS."config.php"; // includo, se esiste, il file di configurazione locale

	include "core.php";

	// se è definita la variabile di sessione "lang"
	if (session::exists("lang")) {
		// imposta il linguaggio in base a quanto impostato nella sessione
		lang::setLang(session::get("lang", ""));
	}

	Configure::write("debug", true);

	Debug::start("generazione pagina (complessivo)");
	Debug::start("inizializzazione cipher");
	$cipher = new cipher(); // inizializzo la classe per codifica / decodifica
	Debug::stop();

	// se è abilitata la connessione al database
	if (Configure::read("database.enabled")) {
		// debug: avvio timer
		Debug::start("apertura database");
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
		Debug::stop("apertura database");
	}

	getBasicVars();

	/*
		TODO : rendere utf-8 configurabile da opzioni
	*/
	header('Content-type: text/html; charset=utf-8');

	Debug::start("inizializzazione controller");
	if (!controllerExists($controllerName."_controller")) {
		$controller = new StaticController();
	} else {
		$controllerClass = to_camel_case($controllerName."_controller", true);
		$controller = new $controllerClass($idValue); // creo un oggetto controller e, se presente, ne carico i parametri
	}
	Debug::stop("inizializzazione controller");

	// se l'azione non esiste nel controller, esce dall'applicazione mostrando il messaggio d'errore
	Debug::start("esecuzione azione");
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

	Debug::stop("generazione pagina (complessivo)");

	if (Configure::read("debug")) {
		$debugTemplate = new Template("debug.php");
		$debugTemplate->set("debug", Debug::get());
		echo $debugTemplate->render();
	}

?>