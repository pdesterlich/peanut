<?php

	/**
	 * modello StatsConnections
	 *
	 * @package Peanut
	 * @author Phelipe de Sterlich
	 **/
	class StatsConnectionsModel extends Orm
	{

		/**
		 * funzione __construct
		 * inizializzazione modello
		 *
		 * @param $id       integer identificativo record
		 * @param $useCache boolean definisce se caricare il record dalla cache (se presente) o se forzare il caricamento da database
		 * @return void
		 * @author Phelipe de Sterlich
		 **/
		function __construct($id = 0, $useCache = true)
		{
			// imposta il nome della tabella (leggendolo dalla configurazione)
			$this->tableName = Configure::read("stats.local.connections_table");
			// richiama la procedura di inizializzazione della classe padre
			parent::__construct($id, $useCache);
		}

	} // END class StatsConnectionsModel extends Orm

?>