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
				$connection = new StatsConnectionsModel();

				// cerca delle connessioni precedenti con lo stesso unique_id
				$records = $connection->find("id, ip_address, end_time", array("unique_id" => $uniqueId), "id DESC", 1);
				// se ci sono connessioni precedenti
				if (count($records) > 0) {
					// se l'indirizzo ip è lo stesso
					if ($_SERVER["REMOTE_ADDR"] == $records[0]["ip_address"]) {
						// verifico data / ora ultima connessione
						$lastConnection = date_create_from_format("Y-m-d H:i:s", $records[0]["end_time"]);
						$timeDiff = $now->diff($lastConnection);
						$minuti = ($timeDiff->y * 365 * 24 * 60) + ($timeDiff->m * 30 * 24 * 60) + ($timeDiff->d * 24 * 60) + ($timeDiff->h * 60) + $timeDiff->i;
						$timeOut = Configure::read("stats.local.timeout");
						// se l'ultima connessione è stata effettuata entro il limite previsto
						if (($minuti <= $timeOut) || ($timeOut == 0)) {
							// carico i dati della connessione
							$connection->load($records[0]["id"]);
						}
					}
				}
				if ($connection->id == 0) {
					loadClass("", "ua-parser".DS."UAParser");
					// UA::get();
					$ua = UA::parse();
					$connection->unique_id        = $uniqueId;
					$connection->connection_date  = $now->format("Y-m-d");
					$connection->ip_address       = $_SERVER["REMOTE_ADDR"];
					if (isset($_SERVER["HTTP_REFERER"])) {
						$connection->referer          = $_SERVER["HTTP_REFERER"];
					}
					$connection->start_time       = $now->format("Y-m-d H:i:s");
					$connection->time_spent       = "00:00:00";
					$connection->browser_family   = $ua->browser;
					$connection->browser_major    = $ua->major;
					$connection->browser_minor    = $ua->minor;
					$connection->browser_build    = $ua->build;
					$connection->browser_revision = $ua->revision;
					$connection->browser_full     = $ua->browserFull;
					$connection->os_family        = $ua->os;
					$connection->os_major         = $ua->osMajor;
					$connection->os_minor         = $ua->osMinor;
					$connection->os_build         = $ua->osBuild;
					$connection->os_revision      = $ua->osRevision;
					$connection->os_full          = $ua->osFull;
					$connection->is_mobile        = $ua->isMobile;
					$connection->is_mobile_device = $ua->isMobileDevice;
					$connection->is_tablet        = $ua->isTablet;
					$connection->is_spider        = $ua->isSpider;
					$connection->is_computer      = $ua->isComputer;
				} else {
					$start                  = date_create_from_format("Y-m-d H:i:s", $connection->start_time);
					$connection->time_spent = $now->diff($start)->format("%H:%I:%S");
				}
				$connection->end_time = $now->format("Y-m-d H:i:s");
				$connection->save();

				// impostazione dettagli richiesta
				$details = new StatsConnectionsDetailsModel();
				$details->connection_id  = $connection->id;
				$details->detail_date    = $now->format("Y-m-d H:i:s");
				$details->request        = $_SERVER["REQUEST_URI"];
				$details->request_method = $_SERVER["REQUEST_METHOD"];
				if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
					$details->ajax = 1;
				}
				$details->save();
			}
		}

	} // END class Stats

?>