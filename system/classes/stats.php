<?php

	/**
	 * classe Stats
	 * gestione statistiche utilizzo
	 *
	 * ATTENZIONE!
	 * l'utilizzo di questa classe necessita della presenza di un database
	 * se il database non è presente la classe termina senza generare errori
	 * è consigliabile impostare l'opzione di configurazione
	 * $config["cookies"]["prefix"]
	 * ad un valore specifico per l'applicazione
	 * 
	 * @package Peanut
	 * @author Phelipe de Sterlich
	 **/
	class Stats
	{

		/**
		 * identificatore univoco visitatore
		 *
		 * @var string
		 **/
		protected static $uniqueId = "";

		/**
		 * funzione log
		 * registra l'accesso
		 *
		 * @return void
		 * @author Phelipe de Sterlich
		 **/
		function log()
		{
			$uniqueId = "";
			if (Configure::read("database.enabled")) {
				$cookieName = Configure::read("cookies.prefix")."_".Configure::read("stats.local.cookie_name");
				$uniqueId   = request::read($cookieName, "", true);
				if ($uniqueId == "") {
					$uniqueId = utils::rand_str();
					request::setCookie($cookieName, $uniqueId, "never");
				}

				$now = date_create();
				// impostazione dati connessione
				$connection = new StatsConnectionsModel(array("unique_id" => $uniqueId));
				if ($connection->id == 0) {
					loadClass("", "ua-parser".DS."UAparser");
					// UA::get();
					$ua = UA::parse();
					$connection->unique_id        = $uniqueId;
					$connection->date             = $now->format("Y-m-d");
					$connection->ip_address       = $_SERVER["REMOTE_ADDR"];
					$connection->start            = $now->format("Y-m-d H:i:s");
					$connection->time_spent       = "00:00:00";
					$connection->browser_family   = $ua->browser;
					$connection->browser_full     = $ua->browserFull;
					$connection->os_family        = $ua->os;
					$connection->os_full          = $ua->osFull;
					$connection->is_mobile        = $ua->isMobile;
					$connection->is_mobile_device = $ua->isMobileDevice;
					$connection->is_tablet        = $ua->isTablet;
					$connections->is_spider       = $ua->isSpider;
					$connection->is_computer      = $ua->isComputer;
				} else {
					$start = date_create_from_format("Y-m-d H:i:s");
					$time_spent = $now->diff($start)->format("%H:%I:%S");
				}
				$connection->end = $now->format("Y-m-d H:i:s");
				$connection->save();

				// impostazione dettagli richiesta
				$details = new StatsConnectionsDetailsModel();
				$details->connection_id  = $connection->id;
				$details->date_time      = $now->format("Y-m-d H:i:s");
				$details->request        = $_SERVER["REQUEST_URL"];
				$details->request_method = $_SERVER["REQUEST_METHOD"];
				$details->save();
			}
		}

	} // END class Stats

?>