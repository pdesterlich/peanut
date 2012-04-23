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
	 * flag stato applicazione (produzione)
	 * definisce lo stato in cui si trova l'applicazione: false = sviluppo, true = produzione
	 * utile per attivare / disattivare determinate funzionalità del programma per la fase di sviluppo
	 *
	 * @var bool
	 **/
	$config["production"] = false;

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
	
	$config["layout"]["title"] = "Peanut!";

	$config["database"]["enabled"] = false;
	$config["database"]["host"] = "";
	$config["database"]["name"] = "";
	$config["database"]["username"] = "";
	$config["database"]["password"] = "";

	/**
	 * prefisso tabelle in database - istanza da sostituire
	 * se specificato, viene sostituito con il valore del prefisso (indicato in prefix_replace)
	 *
	 * @var string
	 **/
	$config["database"]["prefix_search"] = "{table_prefix}";

	/**
	 * prefisso tabelle in database
	 * se specificato, viene sostituito a tutte le istanze di $config["database"]["prefix_search"] nelle query eseguite dall'helper database
	 *
	 * @var string
	 **/
	$config["database"]["prefix_replace"] = "";

	$config["security"]["salt"] = "cj9aht3lfj7hw3ac37thmu34f83jc2mhdzo7i853";
	$config["security"]["cipher"] = "h3x4tc347tf2gy3ih2xdq876293cjo182u3hgn2c";
	$config["security"]["disconnect"]["timeout"] = 30; // tempo in minuti dopo cui l'utente, se non ha attività, viene disconnesso
	$config["security"]["cleanup"]["probability"] = 5; // probabilità che venga eseguita la pulizia delle sessioni non attive (minimo: 1) (va letta come 1 su x)

	$config["sessions"]["session_id_name"] = "peanut_session_id";
?>