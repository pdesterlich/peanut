<?php
	/** peanut **
	 * the small php framework
	 * (c) Phelipe de Sterlich | Moorea Software | Elco Sistemi srl
	 **/

	$config["debug"] = false;

	// indirizzo base dell'applicazione (esempio: http://tikehau.mooreasoft.ch/)
	$config["url"]["base"] = "";

	$config["routes"]["controller"] = "peanut";
	$config["routes"]["action"] = "index";

	$config["controller"]["layout"] = "default.php";

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