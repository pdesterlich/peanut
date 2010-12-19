<?php
	/** peanut **
	 * the small php framework
	 * (c) Phelipe de Sterlich | Moorea Software | Elco Sistemi srl
	 * -----
	 * file: system/helpers/database_helper.php
	 * helper per gestione database
	 **/

	class database
	{

		public static function query($sql, $fetch = "query") {
			/** database_helper **
			 * funzione query
			 * esegue una query sul database
			 * -- input --
			 * $sql (string) query da eseguire
			 * $fetch (string) (opzionale) tipo di risultato da ottenere
			 * -- output --
			 * $fetch = "query" -> viene ritornato l'oggetto mysql_query (da utilizzare in genere per query di insert / update / delete)
			 * $fetch = "array" -> ritorna il singolo record (il primo se la query prevede più record come risultato) in forma di array
			 * $fetch = "object" -> ritorna il singolo record (il primo se la query prevede più record come risultato) in forma di oggetto
			 * $fetch = "records" -> ritorna un array dei record ottenuti dalla query, associando i campi per nome
			 * $fetch = "records_num" -> ritorna un array dei record ottenuti dalla query, associando i campi per indice
			 **/

			/*
				TODO : migliorare gestione errori su esecuzione query
			*/

			// inizializzo il timer (per debug)
			$timeStart = microtime(true);

			// rimuovo eventuali spazi presenti in testa e in coda alla stringa sql
			$sql = trim($sql);

			// eseguo la query sul database
			$query = mysql_query($sql) or die (__("system.query_fail", array(":sql" => $sql, ":errore" => mysql_error())));

			// se la query è una select
			if (strtolower(substr($sql, 0, 6)) == "select") {
				// leggo il numero di record ottenuti
				$numRec = mysql_num_rows($query);
			// altrimenti
			} else {
				// leggo il numero di record oggetto della query
				$numRec = mysql_affected_rows();
			}

			// in base al valore di $fetch
			switch ($fetch) {
				// se "query" ritorno l'oggetto mysql_query
				case 'query':
					$result = $query;
					break;
				// se "array" ritorno un array con il primo record ottenuto dalla query
				case 'array':
					$result = mysql_fetch_array($query, MYSQL_ASSOC);
					break;
				// se "object" ritorno un oggetto con il primo record ottenuto dalla query
				case 'object':
					$result = mysql_fetch_object($query);
					break;
				// se "records" ritorno un array con tutti i records ottenuti dalla query, associando i campi per nome
				case 'records':
					$result = array();
					while ($row = mysql_fetch_array($query, MYSQL_ASSOC)) {
						$result[] = $row;
					}
					break;
				// se "records_num" ritorno un array con tutti i records ottenuti dalla query, associando i campi per indice
				case 'records_num':
					$result = array();
					while ($row = mysql_fetch_array($query, MYSQL_NUM)) {
						$result[] = $row;
					}
					break;
			}

			//  fermo il timer (per debug)
			$timeStop = microtime(true);

			// aggiungo al debug le informazioni sull'esecuzione della query
			debugItem("esecuzione query", $timeStop - $timeStart, "<div class='query'>".$sql."</div>numero record: ".$numRec);

			// ritorno il risultato
			return $result;
		}

		public static function insert($tableName, $fields, $returnId = false) {
			/** database_helper **
			 * funzione insert
			 * esegue una query di inserimento sul database
			 * -- input --
			 * $tableName (string) tabella in cui inserire i dati
			 * $fields (array) array associativo contenente i campi da aggiungere e i relativi valori
			 * $returnId (boolean) (opzionale) se True, la funzione ritorna l'id (campo autoinc) del record inserito
			 * -- output --
			 * (integer) se $returnId = True, il valore ritornato è l'id (campo autoinc) del record inserito
			 **/

			// creo la stringa della query di inserimento
			$sql = sprintf("INSERT INTO %s (%s) VALUES ('%s')", 
				// nome della tabella
				$tableName,
				// nomi dei campi
				implode(", ", array_map('mysql_real_escape_string', array_keys($fields))), 
				// valori da inserire
				implode("', '", array_map('mysql_real_escape_string', $fields))
				);

			// eseguo la query di inserimento
			database::query($sql);

			// 
			if ($returnId) return mysql_insert_id();
		}

		public static function update($tableName, $fields, $where = null) {
			/** database_helper **
			 * funzione update
			 * esegue una query di aggiornamento sul database
			 * -- input --
			 * $tableName (string) tabella su cui aggiornare i dati
			 * $fields (array) array associativo contenente i campi da modificare e i relativi valori
			 * $where (array) array associativo contenente le condizioni " AND campo = valore"
			 *        (string) stringa da utilizzare come condizione where
			 **/

			// creo la stringa della query di aggiornamento
			$sql = "UPDATE {$tableName} SET ".arrays::implode($fields, " = ", ", ", "'", "mysql_real_escape_string");

			// se sono presenti le condizioni
			if ($where != null) {
				// se le condizioni sono un array
				if (is_array($where)) {
					// trasforma l'array in stringa e lo aggiunge alla query
					$sql .= " WHERE ".arrays::implode($where, " = ", " AND ", "'", "mysql_real_escape_string");
				// altrimenti
				} else {
					// aggiunge le condizioni alla query
					$sql .= " WHERE {$where}";
				}
			}

			// esegue la query di aggiornamento
			return database::query($sql);
		}

		public static function delete($tableName, $where = null) {
			/** database_helper **
			 * funzione delete
			 * esegue una query di eliminazione sul database
			 * -- input --
			 * $tableName (string) tabella da cui eliminare i dati
			 * $where (array) array associativo contenente le condizioni " AND campo = valore"
			 *        (string) stringa da utilizzare come condizione where
			 **/

			// imposta la query di eliminazione
			$sql = "DELETE FROM {$tableName}";

			// se sono presenti le condizioni
			if ($where != null) {
				// se le condizioni sono un array
				if (is_array($where)) {
					// trasforma l'array in stringa e lo aggiunge alla query
					$sql .= " WHERE ".arrays::implode($where, " = ", " AND ", "'", "mysql_real_escape_string");
				// altrimenti
				} else {
					// aggiunge le condizioni alla query
					$sql .= " WHERE {$where}";
				}
			}

			// esegue la query di eliminazione
			return database::query($sql);
		}
	}

?>