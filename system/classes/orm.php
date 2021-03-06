<?php
	/** peanut **
	 * the small php framework
	 * (c) Phelipe de Sterlich | Moorea Software | Elco Sistemi srl
	 * -----
	 * file: system/classes/controller.php
	 * classe base controller
	 **/

	class Orm extends base
	{
		/**
		 * array per cache query eseguite
		 *
		 * @var array
		 * @access protected
		 **/
		protected static $queryCache = array();

		/**
		 * identificativo record
		 *
		 * @var integer
		 **/
		protected $id = 0;

		/**
		 * nome tabella
		 *
		 * @var string
		 **/
		protected $tableName = "";

		/**
		 * nome campo chiave
		 *
		 * @var string
		 **/
		protected $idFieldName = "id";

		/**
		 * array contenente campi e valori del record attuale
		 *
		 * @var    array
		 * @access protected
		 **/
		protected $fields = array(); // array contenente campi e valori del record

		/**
		 * array contenente campi e valori del record attuale, non modificati (per confronto)
		 *
		 * @var    array
		 * @access protected
		 **/
		protected $originalFields = array();

		/**
		 * array contenente informazioni sui tipi record
		 *
		 * @var    array
		 * @access protected
		 *
		 * esempi:
		 * nome_campo = tipo
		 * nome_campo = array("tipo" => tipo_campo, "cipher" => true/false)
		 **/
		protected $fieldTypes = array(); // array contenente informazioni sui tipi record

		/**
		 * elenco oggetti collegati in relazione uno (molti) a uno
		 * per ogni tabella collegata, l'array deve contenere un elemento cosi' impostato:
		 * nome => array("modelName" => modelName, "sourceField" => nomeCampoTabellaOrigine)
		 *
		 * @var array
		 *
		 * esempio: $hasOne = array("project" => array("modelName" => "ProjectsModel", "sourceField" => "project_id"));
		 **/
		protected $hasOne = array();

		/**
		 * funzione factory
		 * crea e ritorna un nuovo modello
		 *
		 * @concatenabile
		 * @param  string nome modello
		 * @param  mixed  identificativo record
		 * @return object (Orm)
		 */
		public static function factory($model, $id = 0)
		{
			// imposta il nome della classe del modello
			$model = to_camel_case($model, true)."Model";
			// crea il modello e lo ritorna
			return new $model($id);
		}

		/**
		 * funzione _initializeHasOne
		 * inizializzazione modelli collegato in relazione uno (molti) a uno
		 *
		 * @return void
		 * @access private
		 * @author Phelipe de Sterlich
		 **/
		function _initializeHasOne()
		{
			// per ogni record in $hasOne
			foreach ($this->hasOne as $object => &$data) {
				// legge il nome del modello
				$modelName = $data["modelName"];
				// crea un nuovo oggetto, assegnandogli il modello generato dinamicamente
				$data["model"] = new $modelName($this->$data["sourceField"]);
			}
		}

		/**
		 * funzione __construct
		 * inizializzazione class
		 *
		 * @param $id       integer identificativo record
		 * @param $useCache boolean definisce se caricare il record dalla cache (se presente) o se forzare il caricamento da database
		 * @return void
		 * @author Phelipe de Sterlich
		 **/
		function __construct($id = 0, $useCache = true)
		{
			// richiama la procedura di inizializzazione della classe padre
			parent::__construct();
			// se il nome della tabella non è specificato, lo imposta al nome del modello (meno la parola "Model")
			if ($this->tableName == "") $this->tableName = from_camel_case(str_replace("Model", "", get_class($this)));
			// carica il record di cui è passato l'identificativo
			$this->load($id, $useCache);
			// richiama la funzione di inizializzazione oggetti collegati
			$this->_initializeHasOne();
		}

		/**
		 * funzione __get
		 * magic get per ottenere le proprietà dell'oggetto
		 *
		 * @param  string $var nome della proprietà da ottenere
		 * @return mixed
		 * @author Phelipe de Sterlich
		 **/
		function __get($var)
		{

			// se esiste una proprietà col nome indicato
			if (isset($this->$var)) {
				// ritorno il valore della proprietà
				return $this->$var;
			// se esiste un campo col nome indicato
			} else if (array_key_exists($var, $this->fields)) {
				// ritorno il valore del campo (eventualmente convertito per la visualizzazione)
				return $this->getField($var);
			// se esiste un oggetto hasOne col nome indicato
			} else if (array_key_exists($var, $this->hasOne)) {
				// ritorno il modello collegato all'oggetto $hasOne
				return $this->hasOne[$var]["model"];
			}
		}

		/**
		 * funzione __set (magic set)
		 * imposta il valore di una proprietà dell'oggetto
		 *
		 * @param  mixed $var   (string) nome della proprietà da impostare
		 *                       (array) array associativo nome => valore
		 * @param  mixed $value valore da impostare (se $var è una stringa)
		 * @return void
		 * @author Phelipe de Sterlich
		 */
		function __set($var, $value = "")
		{
			// se esiste una proprietà col nome indicato
			if (isset($this->$var)) {
				// ne imposto il valore
				$this->$var = $value;
			// se esiste un campo col nome indicato
			} else if (array_key_exists($var, $this->fields)) {
				// ne imposto il valore
				$this->setField($var, $value);
			}
		}

		/**
		 * funzione set
		 * imposta il valore di una o piu' proprietà dell'oggetto
		 *
		 * @concatenabile
		 * @param  mixed $var   (array) array associativo nome => valore
		 *                      (string) nome della proprietà da impostare
		 * @param  mixed $value (opzionale) se $var è una stringa, $value rappresenta il valore da impostare sulla proprietà
		 * @return object (Orm)
		 * @author Phelipe de Sterlich
		 */
		function set($var, $value = "")
		{
			// inizializza l'array $vars
			$vars = array();
			// se $var è un array lo copia su $vars, altrimenti aggiunge a $vars il nome ed il valore della proprietà da impostare
			if (is_array($var)) {
				$vars = $var;
			} else {
				$vars[$var] = $value;
			}

			// per ogni coppia proprietà => valore presente nell'array
			foreach ($vars as $varName => $varValue) {
				// se esiste una proprietà col nome indicato
				if (isset($this->$varName)) {
					// ne imposto il valore
					$this->$varName = $varValue;
				// se esiste un campo col nome indicato
				} else if (array_key_exists($varName, $this->fields)) {
					// ne imposto il valore
					$this->setField($varName, $varValue);
				}
			}
			// ritorna l'oggetto Orm (per concatenazione)
			return $this;
		}

		function getRecord()
		{
			/** orm **
			 * funzione getRecord
			 * ritorna il record gestito dall'oggetto
			 * -- output --
			 * (array) array associativo campo = valore
			 **/

			// inizializzo l'array da ritornare
			$result = array();
			// per ogni campo della tabella
			foreach ($this->fields as $key => $value) {
				// aggiungo all'array l'associazione campo => valore (eventualmente convertito per la visualizzazione)
				$result[$key] = $this->getField($key);
			}
			// ritorno l'array
			return $result;
		}

		/**
		 * funzione getField
		 * ritorna il valore del campo specificato, formattandolo per la visualizzazione se previsto
		 *
		 * @param  string $field    nome del campo per cui ritornare il valore
		 * @param  bool   $original se true, legge il valore dal record originale (non modificato), altrimenti dal record attivo
		 * @return mixed            (in base al nome del campo)
		 * @author Phelipe de Sterlich
		 */
		function getField($field, $original = false)
		{
			// inizializza il valore letto ad una stringa vuota
			$value = "";
			// se deve leggere dal record originale
			if ($original) {
				// se il campo non esiste ritorna una stringa vuota, altrimenti legge il valore del campo
				if (!array_key_exists($field, $this->originalFields)) {
					return "";
				} else {
					$value = stripslashes($this->originalFields[$field]);
				}
			// se invece deve leggere dal record attivo
			} else {
				// se il campo non esiste ritorna una stringa vuota, altrimenti legge il valore del campo
				if (!array_key_exists($field, $this->fields)) {
					return "";
				} else {
					$value = stripslashes($this->fields[$field]);
				}
			}
			// rende disponibile la variabile globale $cipher
			global $cipher;
			// se il campo è presente nell'array dei tipi campo
			if (array_key_exists($field, $this->fieldTypes)) {
				// imposto i valori di default
				$tipo = ""; // tipo campo
				$bCipher = false; // flag campo cifrato
				// se il tipo di informazione sul campo é un'array
				if (is_array($this->fieldTypes[$field])) {
					// leggo il tipo di campo
					if (isset($this->fieldTypes[$field]["tipo"])) $tipo = $this->fieldTypes[$field]["tipo"];
					// leggo il flag campo cifrato
					if (isset($this->fieldTypes[$field]["cipher"])) $tipo = $this->fieldTypes[$field]["cipher"];
				// se invece è una stringa
				} else {
					// leggo il tipo di campo
					$tipo = $this->fieldTypes[$field];
				}

				// in base al tipo di campo
				switch ($tipo) {
					// data: converto il valore in formato visualizzabile (tramite apposita funzione)
					case "date": $value = ($value == "") ? date("Y-m-d") : utils::sql2date($value); break;
					// data (anche vuota): converto il valore in formato visualizzabile (tramite apposita funzione)
					case "date_empty": $value = utils::sql2date($value); break;
					// data / ora: converto il valore in formato visualizzabile (tramite apposita funzione)
					case "datetime": $value = utils::sql2datetime($value); break;
					// ora: converto il valore in formato visualizzabile (tramite apposita funzione)
					case "time": $value = strftime("%H:%M", strtotime($value)); break;
				}

				// se il campo è cifrato lo decifro
				if ($bCipher) $value = $cipher->decrypt($value);
			}
			// ritorno il valore del campo
			return $value;
		}

		function setField($field, $value)
		{
			/** orm **
			 * funzione setField
			 * imposta il valore di un campo, eventualmente convertendolo per il salvataggio
			 * -- input --
			 * $field (string) nome del campo per cui ottenere il valore
			 * $value (string) valore del campo
			 **/

			// se esiste un campo col nome indicato
			if (array_key_exists($field, $this->fields)) {
				global $cipher;

				// inizializzo il flag di scrittura valore
				$bWrite = true;

				// se il campo è presente nell'array dei tipi campo
				if (array_key_exists($field, $this->fieldTypes)) {
					// imposto i valori di default
					$tipo = ""; // tipo campo
					$bCipher = false; // flag campo cifrato
					// se il tipo di informazione sul campo é un'array
					if (is_array($this->fieldTypes[$field])) {
						// leggo il tipo di campo
						if (isset($this->fieldTypes[$field]["tipo"])) $tipo = $this->fieldTypes[$field]["tipo"];
						// leggo il flag campo cifrato
						if (isset($this->fieldTypes[$field]["cipher"])) $tipo = $this->fieldTypes[$field]["cipher"];
					// se invece è una stringa
					} else {
						// leggo il tipo di campo
						$tipo = $this->fieldTypes[$field];
					}

					// in base al tipo di campo
					switch ($tipo) {
						//  data, data (anche vuota): converto il valore per scriverlo su database (tramite apposita funzione)
						case "date":
						case "date_empty": $value = utils::date2sql($value); break;
						// password: genero l'hash della password se è compilata, ignoro il campo se è vuota
						case "password":
							if ($value == "") {
								$bWrite = false;
							} else {
								$value = security::hash($value);
							}
						// case "time": $value = strftime("%H:%M", strtotime($value)); break;
					}

					// e il campo è cifrato lo codifico
					if ($bCipher) $value = $cipher->encrypt($value);
				}

				// se il flag di scrittura è true scrivo il valore del campo
				if ($bWrite) $this->fields[$field] = $value;
			}
		}

		function load($id = false, $useCache = true)
		{
			/** orm **
			 * funzione load
			 * carica il record dal database, se non presente carica la struttura della tabella
			 * -- input --
			 * $id (integer, string o array) identificativo univoco del record (o array associativo campo => valore da usare per la ricerca)
			 * $useCache (bool)
			 **/

			// se è passato un identificativo (e non un array), lo imposto 
			if (($id) AND (!is_array($id))) $this->id = $id;

			// inizializzo l'array campi / valori
			$this->fields = array();

			// se l'identificativo record è specificato (o sono specificati i criteri per la ricerca)
			if (($this->id) OR (is_array($id))) {
				// imposta l'array delle condizioni where
				$where = (is_array($id)) ? $id : array($this->idFieldName => $this->id);
				// ottiene il record
				$this->fields = Database::select($this->tableName, array("where" => $where), "array");
				// copia i dati del record nell'array originalFields
				$this->originalFields = $this->fields;

				// se è passato un array, imposta l'identificativo del record
				if (is_array($id)) $this->id = $this->fields["id"];
			}
			// se l'identificativo non è passato, oppure non ci sono campi ottenuti dalla precedente query
			if ((count($this->fields) == 0) OR ($this->fields == null))
			{
				// imposta la query di lettura struttura tabella
				$query = "SHOW COLUMNS FROM {$this->tableName}";
				// esegue la query e ottiene la descrizione delle colonne dalla tabella
				$fields = Database::query($query, null, "records");
				// per ogni colonna trovata
				foreach ($fields as $field) {
					// aggiungo la colonna all'array campi / valori
					$this->fields[$field["Field"]] = "";
					// aggiungo la colonna all'array campi / valori originali
					$this->originalFields[$field["Field"]] = "";
				}
			}
		}

		/**
		 * funzione find
		 *
		 * carica dalla tabella del modello uno o più records corrispondenti
		 * alle impostazioni di ricerca, eventualmente ordinandoli
		 * è possibile inoltre limitare il numero di record risultanti
		 *
		 * @access public
		 * @param  $options array parametri della ricerca record (vedi database::select -> $options)
		 * @return array          record ottenuti
		 */
		function find($options = array())
		{
			// ritorno i record ottenuti dalla query
			return Database::select($this->tableName, $options, "records");
		}

		/**
		 * funzione findAll
		 * ritorna tutti i record
		 *
		 * @param $fields array / string elenco dei campi da ritornare
		 * @param $sort  array / string elenco dei campi per l'ordinamento
		 * @return array
		 * @author Phelipe de Sterlich
		 **/
		function findAll($fields = null, $sort = null)
		{
			$options = array();
			if ($fields != null) $options["fields"] = $fields;
			if ($sort != null) $options["sort"] = $sort;
			// ritorno i record ottenuti dalla query
			return Database::select($this->tableName, $options, "records");
		}

		/**
		 * funzione fromPost
		 * valorizza i campi del record in base ai dati passati via POST
		 *
		 * @concatenabile
		 * @param  string $header (opzionale) eventuale prefisso al nome dei campi utilizzato nel POST
		 * @return object (Orm)
		 * @author Phelipe de Sterlich
		 */
		function fromPost($header = "")
		{
			// ciclo su tutti i campi del record
			foreach ($this->fields as $key => $value) {
				// se il campo non è l'identificativo record e nel post esiste un input con lo stesso nome del campo (con l'eventuale prefisso)
				if (($key != $this->idFieldName) AND (isset($_POST[$header.$key]))) {
					// imposta il valore del campo al valore dell'elemento nel post
					$this->setField($key, $_POST[$header.$key]);
				}
			}

			// ritorna l'oggetto Orm (per concatenazione)
			return $this;
		}

		function save()
		{
			/** orm **
			 * funzione save
			 * salva il record nella tabella
			 **/

			$fields = $this->fields;
			unset($fields[$this->idFieldName]);

			if (!$this->id) {
				// se esiste, imposta il valore del campo "created" alla data / ora corrente
				if (array_key_exists('created', $fields)) $fields['created'] = date("Y-m-d H:i:s");
				// se esiste, imposta il valore del campo "modified" alla data / ora corrente
				if (array_key_exists('modified', $fields)) $fields['modified'] = date("Y-m-d H:i:s");

				$this->id = Database::insert($this->tableName, $fields);
				$this->_initializeHasOne();
			} else {
				// se esiste, imposta il valore del campo "modified" alla data / ora corrente
				if (array_key_exists('modified', $fields)) $fields['modified'] = date("Y-m-d H:i");

				Database::update($this->tableName, $fields, array($this->idFieldName => $this->id));
			}
		}

		function insert()
		{
			/** orm **
			 * funzione insert
			 * inserisce un nuovo record nella tabella, indipendentemente dal fatto che il record attuale esista (id != 0) o meno
			 **/

			// forzo l'identificativo del record a 0
			$this->id = 0;
			// salvo il record
			$this->save();
		}

		/**
		 * funzione delete
		 * elimina uno o piu' record
		 *
		 * @param $where     array  array associativo campo => valore contenente il campo (o i campi) da usare come condizioni where
		 * @param $whereSql  string testo sql da usare come condizione where (se non presente viene fatto un and di tutti i campi presenti nell'array $where)
		 * @return void
		 * @author Phelipe de Sterlich
		 **/
		function delete($where = null, $whereSql = "")
		{
			// elimina il record dalla tabella
			if ($where == null) $where = array($this->idFieldName => $this->id);
			Database::delete($this->tableName, $where, $whereSql); // elimina il record (o i record, se e' specificato il parametro $where) dalla tabella
		}

		/**
		 * funzione count
		 * ritorna il numero di records presenti nella tabella
		 *
		 * @param $where     array  array associativo campo => valore contenente il campo (o i campi) da usare come condizioni where
		 * @param $whereSql  string testo sql da usare come condizione where (se non presente viene fatto un and di tutti i campi presenti nell'array $where)
		 * @return int          numero di records presenti
		 * @author Phelipe de Sterlich
		 **/
		function count($where = null, $whereSql = "")
		{
			// crea l'elenco dei campi / parametri per le condizioni where
			if (($whereSql == "") AND ($where != null) AND (is_array($where))) {
				foreach ($where as $key => $value) {
					$whereSql .= (($whereSql == "") ? "" : " AND ") . $key . " = :" . $key;
				}
			}

			// crea la stringa sql di aggiornamento
			$sql = "SELECT COUNT(*) totale FROM {$this->tableName}";
			if ($whereSql != "") $sql .= " WHERE " . $whereSql;

			// eseguo la query e leggo il risultato
			$result = Database::query($sql, $where, "array");

			// ritorno il numero di records presenti
			return $result["totale"];
		}

		/**
		 * funzione getComboValues
		 * ritorna un array id => descrizione dei record da utilizzare in una combo
		 *
		 * @param  string $valueField nome del campo contenente l'identificativo del record
		 * @param  string $descField  nome del campo contenente la descrizione del record
		 * @param  array  $options    parametri
		 * @param  array  $prepend    (opzionale) eventuali elementi da aggiungere in testa all'array
		 * @return array
		 * @author Phelipe de Sterlich
		 **/
		function getComboValues($valueField, $descField, $options = array(), $prepend = null)
		{
			// imposto i campi da estrapolare per la ricerca
			$options["fields"] = array($valueField, $descField);

			// inizializza l'array dei risultati
			$result = array();
			// se è definito un array di elementi da aggiungere in testa al risultato
			if (($prepend != null) AND (is_array($prepend))) {
				// per ogni elemento dell'array
				foreach ($prepend as $key => $value) {
					// aggiunge l'elemento al risultato
					$result[$key] = $value;
				}
			}
			// ottiene i record che corrispondono ai criteri di ricerca
			$records = $this->find($options);
			// cicla sui record ottenuti
			foreach ($records as $record) {
				// aggiunge all'array dei risultati la coppia id > descrizione
				$result[$record[$valueField]] = $record[$descField];
			}
			// ritorna l'array dei risultati
			return $result;
		}

		/**
		 * funzione getModifiedFields
		 * ritorna un array contenente i campi del record che sono stati modificati rispetto al loro valore originale
		 *
		 * @param  $asText     bool    se true, l'aray viene ritornato come stringa
		 * @param  $outputType integer flag per attivazione scrittura in modalità standard (0), textile (1)
		 * @return mixed array "nome_campo" => array("old" => "valore_originale", "new" => "valore_modificato")
		 *               false se non ci sono campi modificati
		 * @author Phelipe de Sterlich
		 **/
		function getModifiedFields($asText = false, $outputType = 0)
		{
			// inizializza l'array risultato
			$result = array();
			// per ogni campo presente
			foreach ($this->fields as $fieldName => $fieldValue) {
				// se il valore originale del campo è diverso dal calore corrente
				if ($this->originalFields[$fieldName] != $fieldValue) {
					// aggiunge il campo e i due valori all'array
					$result[$fieldName] = array("old" => $this->getField($fieldName, true), "new" => $this->getField($fieldName));
				}
			}
			// se non ci sono record modificati
			if (count($result) == 0) {
				// ritorna false
				return false;
			// se è impostato l'apposito flag
			} else if ($asText) {
				// ritorna i campi modificati come stringa
				$result_s = "";
				foreach ($result as $key => $value) {

					switch ($outputType) {
						case 1: // textile
							$oldValue = ($value["old"] == "") ? "&nbsp;" : $value["old"];
							$newValue = ($value["new"] == "") ? "&nbsp;" : $value["new"];
							$result_s .= sprintf("|_. %s|%s|%s|\n", $key, $oldValue, $newValue);
							break;
						default:
							$result_s .= sprintf("campo = %s\nold = %s\nnew = %s\n-----\n", $key, $value["old"], $value["new"]);
							break;
					}
				}
				return $result_s;
			} else {
				// ritorna l'array con i campi modificati
				return $result;
			}
		}

		/**
		 * funzione asText
		 * ritorna il record in versione testuale
		 *
		 * @param $outputType integer flag per attivazione scrittura in modalità standard (0), textile (1)
		 * @return void
		 * @author Phelipe de Sterlich
		 **/
		function asText($outputType = 0)
		{
			$result = "";

			foreach ($this->fields as $key => $value) {
				switch ($outputType) {
					case 1: // textile
						$value = ($key == $this->idFieldName) ? $this->id : $this->getField($key);
						$value = ($value == "") ? "&nbsp;" : $value;
						$result .= sprintf("|_. %s|%s|\n", $key, $value);
						break;
					default:
						$result .= sprintf("%s = %s\n", $key, ($key == $this->idFieldName) ? $this->id : $this->getField($key));
						break;
				}
			}
			return $result;
		}

		/**
		 * funzione locateFieldValue
		 * ottiene il valore del campo richiesto per il record il cui identificativo corrisponde a quello passato
		 *
		 * @param  string $fieldName    nome del campo da leggere
		 * @param  mixed  $idFieldValue valore del campo chiave
		 * @param  string $idFieldName  nome del campo chiave (opzionale, se non specificato viene letto il campo chiave default)
		 * @return mixed
		 * @author Phelipe de Sterlich
		 **/
		function locateFieldValue($fieldName, $idFieldValue, $idFieldName = "")
		{
			// se non specificato da parametro, legge il nome del campo chiave
			if ($idFieldName == "") $idFieldName = $this->idFieldName;
			// ottiene il record corrispondente al campo chiave
			$record = Database::query("SELECT {$fieldName} FROM {$this->tableName} WHERE {$idFieldName} = '$idFieldValue'", null, "array");
			// ritorna il valore del campo richiesto
			return stripslashes($record[$fieldName]);
		}

		/**
		 * funzione fieldExists
		 * verifica se un campo esiste nella tabella
		 *
		 * @return bool
		 * @author Phelipe de Sterlich
		 **/
		function fieldExists($fieldName)
		{
			return array_key_exists($fieldName, $this->fields);
		}

	}

?>
