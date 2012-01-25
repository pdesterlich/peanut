<?php
	/**
	 * configurazione peanut framework
	 * valori di default
	 *
	 * @package peanut
	 * @author Phelipe de Sterlich
	 */

	/**
	 * flag attivazione informazioni di debug
	 *
	 * @var bool
	 **/
	$config["debug"] = false;

	/**
	 * indirizzo base dell'applicazione (senza il protocollo)
	 * (esempio: tikehau.mooreasoft.ch)
	 *
	 * @var string
	 **/
	$config["url"]["base"] = "";

	/**
	 * protocollo di comunicazione
	 * (esempio: http)
	 *
	 * @var string
	 **/
	$config["url"]["protocol"] = "http";

	/**
	 * flag attivazione url brevi
	 * (es. http://tikehau.mooreasoft.ch/projects/view/1 al posto di http://tikehau.mooreasoft.ch/index.php?controller=projects&action=view&id=1)
	 * è necessaria la presenza di un file .htaccess (vedi htaccess.sample in /htdocs)
	 *
	 * @var bool
	 **/
	$config["url"]["short"] = false;

	$config["routes"]["controller"] = "peanut";
	$config["routes"]["action"] = "index";

	$config["controller"]["layout"] = "peanut_layout.php";

	$config["database"]["enabled"] = false;
	$config["database"]["host"] = "";
	$config["database"]["name"] = "";
	$config["database"]["username"] = "";
	$config["database"]["password"] = "";

	$config["security"]["salt"] = "cj9aht3lfj7hw3ac37thmu34f83jc2mhdzo7i853";
	$config["security"]["cipher"] = "h3x4tc347tf2gy3ih2xdq876293cjo182u3hgn2c";
	$config["security"]["disconnect"]["timeout"] = 30; // tempo in minuti dopo cui l'utente, se non ha attività, viene disconnesso
	$config["security"]["cleanup"]["probability"] = 5; // probabilità che venga eseguita la pulizia delle sessioni non attive (minimo: 1) (va letta come 1 su x)

	$config["sessions"]["session_id_name"] = "peanut_session_id";
?>