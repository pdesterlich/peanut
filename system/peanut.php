<?php
	/** peanut **
	 * the small php framework
	 * (c) Phelipe de Sterlich | Moorea Software | Elco Sistemi srl
	 * -----
	 * file: system/peanut.php
	 * peanut core
	 **/

	session_start(); // inizializzo la sessione

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

	Debug::start("generazione pagina (complessivo)");

	Debug::start("inizializzazione cipher");
	$cipher = new cipher(); // inizializzo la classe per codifica / decodifica
	Debug::stop();

	// se è abilitata la connessione al database
	if (Configure::read("database.enabled")) {
		// esegue la connessione al database
		Database::connect();
	}

	Router::init();

	/*
		TODO : rendere utf-8 configurabile da opzioni
	*/
	header('Content-type: text/html; charset=utf-8');

	Router::route();

	Debug::stop("generazione pagina (complessivo)");

	if (Configure::read("debug")) {
		$debugTemplate = new Template("debug.php");
		$debugTemplate->set("debug", Debug::get());
		echo $debugTemplate->render();
	}

?>
