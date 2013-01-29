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
	 * reindirizzamento http > https
	 *
	 * @var string
	 **/
	$config["url"]["https_redirect_to"] = "";

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

	$config["security"]["salt"] = "cj9aht3lfj7hw3ac37thmu34f83jc2mhdzo7i853";
	$config["security"]["cipher"] = "h3x4tc347tf2gy3ih2xdq876293cjo182u3hgn2c";
	$config["security"]["disconnect"]["timeout"] = 30; // tempo in minuti dopo cui l'utente, se non ha attività, viene disconnesso
	$config["security"]["cleanup"]["probability"] = 5; // probabilità che venga eseguita la pulizia delle sessioni non attive (minimo: 1) (va letta come 1 su x)

	$config["sessions"]["session_id_name"] = "peanut_session_id";

	/**
	 * prefisso da utilizzare nella lettura / scrittura dei cookies (usato nelle funzioni dell'helper request)
	 *
	 * @var string
	 **/
	$config["cookies"]["prefix"] = "peanut";

	/**
	 * abilitazione statistiche locali
	 * default: false (non abilitate)
	 *
	 * @var boolean
	 **/
	$config["stats"]["local"]["enabled"] = false;

	/**
	 * nome del cookie da usare per identificare univocamente l'utente
	 * default: "stats_uniqueid"
	 *
	 * @var string
	 **/
	$config["stats"]["local"]["cookie_name"] = "stats_uniqueid";

	/**
	 * nome della tabella delle connessioni
	 * default: "stats_connections"
	 *
	 * @var string
	 **/
	$config["stats"]["local"]["connections_table"] = "stats_connections";

	/**
	 * nome della tabella dei dettagli
	 * default: "stats_connections_details"
	 *
	 * @var string
	 **/
	$config["stats"]["local"]["details_table"] = "stats_connections_details";

	/**
	 * tempo dall'ultima attività (in minuti) trascorso il quale viene considerata una nuova connessione
	 * default: 30
	 * se 0, non viene mai considerata una nuova connessione
	 *
	 * @var integer
	 **/
	$config["stats"]["local"]["timeout"] = 30;

	/**************************************************************************
	 * configurazione database
	 **************************************************************************/

	/**
	 * flag abilitazione database
	 *
	 * @var bool
	 **/
	$config["database"]["enabled"]  = false;

	/**
	 * libreria connessione database
	 * valori possibili:
	 *   mysql - utilizza la libreria mysql
	 *     pdo - utilizza la libreria pdo
	 *
	 * @var string
	 **/
	$config["database"]["library"]  = "mysql";

	/**
	 * driver pdo
	 * driver del database da utilizzare nella creazione della stringa DSN
	 * (solo con libreria pdo)
	 *
	 * @var string
	 **/
	$config["database"]["pdo_driver"] = "mysql";

	$config["database"]["host"]     = "";
	$config["database"]["name"]     = "";
	$config["database"]["username"] = "";
	$config["database"]["password"] = "";

	/**
	 * flag creazione database
	 * se abilitato, la procedura tenta di creare il database se questo non esiste
	 * (solo con libreria mysql)
	 *
	 * @var bool
	 **/
	$config["database"]["create"]   = false; // crea il database se non esiste

	/**
	 * charset da impostare sul database
	 * se specificato, viene eseguita la query "SET CHARACTER SET x"
	 *
	 * @var string
	 **/
	$config["database"]["charset"]  = "";

	/**
	 * abilita l'uso della cache locale
	 *
	 * @var bool
	 **/
	$config["database"]["cache"] = false;

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

?>