<?php
	/** peanut **
	 * the small php framework
	 * (c) Phelipe de Sterlich | Moorea Software | Elco Sistemi srl
	 * -----
	 * file: system/helpers/database_helper.php
	 * helper per gestione database
	 **/

	class Database
	{

		/**
		 * connessione database
		 *
		 * @var resource
		 **/
		protected static $db = false;

		/**
		 * array per cache query eseguite
		 *
		 * @var array
		 * @access protected
		 **/
		protected static $queryCache = array();

		/**
		 * funzione connect
		 * esegue la connessione al database
		 *
		 * @return void
		 * @author Phelipe de Sterlich
		 **/
		public static function connect()
		{
			// debug: avvio timer
			Debug::start("apertura database");

			switch (Configure::read("database.library")) {
				case 'pdo':

					// imposta il dsn per la connessione
					$dsn = sprintf("%s:host=%s;dbname=%s", Configure::read("database.pdo_driver"), Configure::read("database.host"), Configure::read("database.name"));

					// esegue la connessione al server mysql
					try {
						self::$db = new PDO($dsn, Configure::read("database.username"), Configure::read("database.password"));
					} catch (PDOException $e) {
						die (__("system.pdo_connect_fail", array(":server" => Configure::read("database.host"), ":database" => Configure::read("database.name"), ":errore" => $e->getMessage())));
					}

					// imposto il charset a utf-8
					$charset = Configure::read("database.charset", "");
					if ($charset != "") {
						// imposta il charset
						try {
							self::$db->query("SET CHARACTER SET " . $charset);
						} catch (PDOException $e) {
							// mostra un messaggio di errore in caso non riesca a connettersi al database
							die (__("system.pdo_set_charset_fail", array(":server" => Configure::read("database.host"), ":database" => Configure::read("database.name"), ":errore" => $e->getMessage())));
						}
					}

					break;
				case 'mysql':

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

					break;
			}

			Debug::stop("apertura database");
		}

		/**
		 * funzione query
		 * esegue una query, ritornando un valore differente a seconda delle impostazioni
		 *
		 * @param $sql      string query da eseguire
		 * @param $params   array  array associativo dei parametri della query con i relativi valori
		 * @param $fetch    string tipo variabile da ritornare
		 * @param $useCache bool   flag per abilitare o meno il caricamento dei dati dalla cache interna
		 * @return resource - se $fetch = "query", ritorna la risorsa ottenuta
		 *         array    - se $fetch = "array", ritorna il singolo record (il primo se la query prevede più record come risultato) in forma di array
		 *         object   - se $fetch = "object", ritorna il singolo record (il primo se la query prevede più record come risultato) in forma di oggetto
		 *         array    - se $fetch = "records", ritorna un array dei record ottenuti dalla query, associando i campi per nome
		 *         int      - se $fetch = "records_num", ritorna un array dei record ottenuti dalla query, associando i campi per indice
		 * @author Phelipe de Sterlich
		 **/
		public static function query($sql, $params = null, $fetch = "query", $useCache = true)
		{
			// TODO : migliorare gestione errori su esecuzione query

			// inizializzo il timer (per debug)
			Debug::start("esecuzione query");

			// imposta, se previsto, il prefisso delle tabelle
			$sql = str_replace(Configure::read("database.prefix_search"), Configure::read("database.prefix_replace"), $sql);

			// rimuovo eventuali spazi presenti in testa e in coda alla stringa sql
			$sql = trim($sql);

			// ottiene il tipo di operazione che la query deve eseguire (prima parola della query)
			list($queryOperation) = explode(" ", $sql);
			$queryOperation = strtoupper($queryOperation);

			$numRec = 0;
			$result = false;

			// TODO: verificare l'utilizzo della cache legato ai parametri
			/*
			if (($useCache) AND (Configure::read("database.cache"))) {
				return self::$queryCache[$fetch][$sql];
			}
			*/

			switch (Configure::read("database.library")) {
				case 'pdo':

					// preparo la query per l'esecuzione sul database
					$query = self::$db->prepare($sql);
					// eseguo la query
					try {
						if ($params == null) {
							$query->execute();
						} else {
							$query->execute($params);
						}
					} catch (PDOException $e) {
						die (__("system.query_fail", array(":sql" => $sql, ":errore" => $e->getMessage())));
					}

					// leggo il numero di record oggetto della query
					$numRec = $query->rowCount();

					// in base al valore di $fetch
					switch ($fetch) {
						// se "query" ritorno l'oggetto query
						case 'query':
							$result = $query;
							break;
						// se "array" ritorno un array con il primo record ottenuto dalla query
						case 'array':
							$result = $query->fetch(PDO::FETCH_ASSOC);
							break;
						// se "object" ritorno un oggetto con il primo record ottenuto dalla query
						case 'object':
							$result = $query->fetch(PDO::FETCH_OBJ);
							break;
						// se "records" ritorno un array con tutti i records ottenuti dalla query, associando i campi per nome
						case 'records':
							$result = $query->fetchAll(PDO::FETCH_ASSOC);
							break;
						// se "records_num" ritorno un array con tutti i records ottenuti dalla query, associando i campi per indice
						case 'records_num':
							$result = $query->fetchAll(PDO::FETCH_NUM);
							break;
					}

					break;
				case 'mysql':

					// imposto i parametri
					if (($params != null) AND (is_array($params))) {

						uksort($params, create_function('$a,$b', 'return strlen($a) < strlen($b);'));

						foreach ($params as $key => $value) {
							$sql = str_replace(":" . $key, "'" . mysql_real_escape_string($value) . "'", $sql);
						}
					}

					// eseguo la query sul database
					$query = mysql_query($sql) or die (__("system.query_fail", array(":sql" => $sql, ":errore" => mysql_error())));

					// se la query è una select
					if ($queryOperation == "SELECT") {
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

					break;
			}

			// aggiungo il risultato alla cache
			/*
			if (Configure::read("database.cache")) {
				self::$queryCache[$fetch][$query] = $result;
			}
			*/

			//  fermo il timer (per debug)
			$timeStop = microtime(true);

			// aggiungo al debug le informazioni sull'esecuzione della query
			Debug::stop("esecuzione query", array("query" => $sql, "records" => $numRec));

			// ritorno il risultato
			return $result;
		}
	
		/**
		 * funzione select
		 * ritorna uno o piu' record da una tabella
		 *
		 * @param $tableName string nome della tabella in cui aggiornare i record
		 * @param $options   array  parametri della query
		 *         fields    array  array contenente i nomi dei campi da visualizzare
		 *                   string stringa contenente i nomi dei campi da visualizzare (separati da virgola)
		 *         where     array  array associativo campo => valore contenente il campo (o i campi) da usare come condizioni where
		 *         whereSql  string testo sql da usare come condizione where (se non presente viene fatto un and di tutti i campi presenti nell'array $where)
		 *         sort      string testo sql da usare come ordinamento
		 *                   array  array contenente i nomi dei campi da usare come ordinamento
		 *         limit     string limitatore record ottenuti
		 * @param $fetch     string tipo variabile da ritornare (vedi parametro fetch di database::query)
		 * @return resource  (vedi database::query)
		 * @author Phelipe de Sterlich
		 **/
		public static function select($tableName, $options = array(), $fetch = "records")
		{
			// inizializzazione variabili
			$params = null;

			// comando query
			$sql = "SELECT ";

			// campi da estrapolare
			if (!array_key_exists("fields", $options)) {
				$sql .= "*";
			} else {
				if (is_array($options["fields"])) {
					$sql .= implode(",", $options["fields"]);
				} else {
					$sql .= $options["fields"];
				}
			}

			// origine dati
			$sql .= " FROM " . $tableName;

			// condizioni di ricerca
			$whereSql = "";
			if (array_key_exists("where", $options)) {
				$params = $options["where"];
				if (array_key_exists("whereSql", $options)) {
					$sql .= " WHERE " . $options["whereSql"];
				} else {
					foreach ($options["where"] as $key => $value) {
						$whereSql .= (($whereSql == "") ? "" : " AND ") . $key . " = :" . $key;
					}
				}
			}
			if ($whereSql != "") $sql .= " WHERE " . $whereSql;

			// ordinamento
			if (array_key_exists("sort", $options)) {
				if (is_array($options["sort"])) {
					$sql .= " ORDER BY " . implode(",", $options["sort"]);
				} else {
					$sql .= " ORDER BY " . $options["sort"];
				}
			}

			// limiti
			if (array_key_exists("limit", $options)) {
				$sql .= " LIMIT " . $options["limit"];
			}

			// esegue la query di estrapolazione
			return self::query($sql, $params, $fetch);
		}

		/**
		 * funzione insert
		 * inserisce un nuovo record in una tabella
		 *
		 * @param $tableName string nome della tabella in cui aggiungere il record
		 * @param $fields    array  array associativo campo => valore contenente i campi da aggiungere e i relativi valori
		 * @return integer          identificativo del record aggiunto
		 * @author Phelipe de Sterlich
		 **/
		public static function insert($tableName, $fields)
		{
			// crea gli elenchi dei nomi campi e nomi parametri
			$fieldNames = "";
			$valueNames = "";
			foreach ($fields as $key => $value) {
				$fieldNames .= (($fieldNames == "") ? "" : ", ") . $key;
				$valueNames .= (($valueNames == "") ? "" : ", ") . ":" . $key;
			}

			// crea la stringa sql di inserimento
			$sql = sprintf("INSERT INTO %s (%s) VALUES (%s)", $tableName, $fieldNames, $valueNames);

			// esegue la query di inserimento
			self::query($sql, $fields);

			// ritorna l'id del record inserito
			switch (Configure::read("database.library")) {
				case 'pdo': return self::$db->lastInsertId(); break;
				case 'mysql': return mysql_insert_id(); break;
			}
		}

		/**
		 * funzione update
		 * aggiorna uno o piu' record in una tabella
		 *
		 * @param $tableName string nome della tabella in cui aggiornare i record
		 * @param $fields    array  array associativo campo => valore contenente i campi da aggiornare e i relativi valori
		 * @param $where     array  array associativo campo => valore contenente il campo (o i campi) da usare come condizioni where
		 * @param $whereSql  string testo sql da usare come condizione where (se non presente viene fatto un and di tutti i campi presenti nell'array $where)
		 * @return void
		 * @author Phelipe de Sterlich
		 **/
		public static function update($tableName, $fields, $where = null, $whereSql = "")
		{
			// crea l'elenco dei campi / parametri da aggiornare
			$updateFields = "";
			foreach ($fields as $key => $value) {
				$updateFields .= (($updateFields == "") ? "" : ", ") . $key . " = :" . $key;
			}

			// crea l'elenco dei campi / parametri per le condizioni where
			if (($whereSql == "") AND ($where != null) AND (is_array($where))) {
				foreach ($where as $key => $value) {
					$whereSql .= (($whereSql == "") ? "" : " AND ") . $key . " = :" . $key;
				}
			}

			// crea la stringa sql di aggiornamento
			$sql = sprintf("UPDATE %s SET %s", $tableName, $updateFields);
			if ($whereSql != "") $sql .= " WHERE " . $whereSql;

			// combina i valori dei parametri where con quelli dei campi
			if ($where != null) $fields = array_merge($fields, $where);

			// esegue la query di aggiornamento
			self::query($sql, $fields);
		}

		/**
		 * funzione delete
		 * elimina uno o piu' record da una tabella
		 *
		 * @param $tableName string nome della tabella in cui aggiornare i record
		 * @param $where     array  array associativo campo => valore contenente il campo (o i campi) da usare come condizioni where
		 * @param $whereSql  string testo sql da usare come condizione where (se non presente viene fatto un and di tutti i campi presenti nell'array $where)
		 * @return void
		 * @author Phelipe de Sterlich
		 **/
		public static function delete($tableName, $where = null, $whereSql = "")
		{
			// crea l'elenco dei campi / parametri per le condizioni where
			if (($whereSql == "") AND ($where != null) AND (is_array($where))) {
				foreach ($where as $key => $value) {
					$whereSql .= (($whereSql == "") ? "" : " AND ") . $key . " = :" . $key;
				}
			}

			// crea la stringa sql di aggiornamento
			$sql = "DELETE FROM " . $tableName;
			if ($whereSql != "") $sql .= " WHERE " . $whereSql;

			// esegue la query di eliminazione
			self::query($sql, $where);
		}

	}

?>