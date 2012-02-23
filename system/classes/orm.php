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
		 * array contenente campi e valori del record attuale, non modificati (per connfronto)
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
			$model = to_camel_case($model)."Model";
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
		 * @param  int $id identificativo record
		 * @return void
		 * @author Phelipe de Sterlich
		 **/
		function __construct($id = 0)
		{
			// richiama la procedura di inizializzazione della classe padre
			parent::__construct();
			// se il nome della tabella non è specificato, lo imposta al nome del modello (meno la parola "Model")
			if ($this->tableName == "") $this->tableName = from_camel_case(str_replace("Model", "", get_class($this)));
			// carica il record di cui è passato l'identificativo
			$this->load($id);
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

		function load($id = false)
		{
			/** orm **
			 * funzione load
			 * carica il record dal database, se non presente carica la struttura della tabella
			 * -- input --
			 * $id (integer o string) identificativo univoco del record
			 **/

			// se è passato un identificativo (e non un array), lo imposto 
			if (($id) AND (!is_array($id))) $this->id = $id;

			// inizializzo l'array campi / valori
			$this->fields = array();

			// se l'identificativo record è specificato (o sono specificati i criteri per la ricerca)
			if (($this->id) OR (is_array($id))) {
				// imposta la query di lettura record
				$query = "SELECT * FROM {$this->tableName}";
				if (is_array($id)) {
					$query .= " WHERE ".arrays::implode($where, " = ", " AND ", "'", "mysql_real_escape_string");
				} else {
					$query .= " WHERE {$this->idFieldName} = '{$this->id}'";	
				}

				// se la query è già presente nella cache
				if (isset(self::$queryCache[$query])) {
					// carica il risultato dalla cache
					$this->fields = self::$queryCache[$query];
				// altrimenti (query non in cache)
				} else {
					// esegue la query e ottiene la descrizione delle colonne dalla tabella
					$this->fields = database::query($query, "array");
					// copia i dati del record nell'array originalFields
					$this->originalFields = $this->fields;
					// salva il risultato della query in cache
					self::$queryCache[$query] = $this->fields;
				}
			}
			// se l'identificativo non è passato, oppure non ci sono campi ottenuti dalla precedente query
			if ((!$this->id) OR (count($this->fields) == 0) OR ($this->fields == null))
			{
				// imposta la query di lettura struttura tabella
				$query = "SHOW COLUMNS FROM {$this->tableName}";
				// se la query è già presente nella cache
				if (isset(self::$queryCache[$query])) {
					// carica il risultato dalla cache
					$fields = self::$queryCache[$query];
				// altrimenti (query non in cache)
				} else {
					// esegue la query e ottiene la descrizione delle colonne dalla tabella
					$fields = database::query($query, "records");
					// salva il risultato della query in cache
					self::$queryCache[$query] = $fields;
				}
				// per ogni colonna trovata
				foreach ($fields as $field) {
					// aggiungo la colonna all'array campi / valori
					$this->fields[$field["Field"]] = "";
					// aggiungo la colonna all'array campi / valori originali
					$this->originalFields[$field["Field"]] = "";
				}
			}
		}

		function find($fields = null, $where = null, $order = null, $limit = "")
		{
			/**
			 * funzione find
			 *
			 * carica dalla tabella del modello uno o più records corrispondenti
			 * alle impostazioni di ricerca, eventualmente ordinandoli
			 * è possibile inoltre limitare il numero di record risultanti
			 *
			 * @access public
			 * @param  mixed  $fields elenco dei campi da ritornare, stringa (se vuota ritorna tutti i campi) o array (campo, campo, ...)
			 * @param  mixed  $where  condizioni di ricerca (where), stringa o array (campo => valore, campo => valore, ...)
			 * @param  mixed  $order  impostazioni di ordinamento (order by), stringa o array (campo, campo, ...)
			 * @param  string $limit  limitatore di record
			 * @return array          record ottenuti
			 */

			$sql = "SELECT "; // inizializzo la query di selezione
			if (($fields == null) OR ($fields == "")) { // se non sono stati impostati i campi da ritornare
				$sql .= "* "; // ritorno tutti i campi
			} else {
				if (is_array($fields)) { // se le condizioni di ricerca sono un array
					$sql .= implode(",", $fields); // converto da array a stringa e aggiungo alla query
				} else { // se invece è una stringa
					$sql .= $fields; // aggiungo la stringa alla query
				}
			}

			$sql .= " FROM {$this->tableName}"; // aggiungo il nome della tabella

			// se sono impostate le condizioni di ricerca
			if ($where != null) {
				// se le condizioni di ricerca sono un array
				if (is_array($where)) {
					// converto da array a stringa e aggiungo alla query
					$sql .= " WHERE ".arrays::implode($where, " = ", " AND ", "'", "mysql_real_escape_string");
				// se invece è una stringa
				} else {
					// aggiungo la stringa alla query
					$sql .= " WHERE {$where}";
				}
			}

			// se sono impostate le condizioni di ordinamento
			if ($order != null) {
				// se le condizioni di ordinamento sono un array
				if (is_array($order)) {
					// converto da array a stringa e aggiungo alla query
					$sql .= " ORDER BY ".implode(",", $order);
				// se invece è una stringa
				} else {
					// aggiungo la stringa alla query
					$sql .= " ORDER BY {$order}";
				}
			}

			if ($limit != "") $sql .= " LIMIT {$limit}"; // se specificato, aggiungo il limitatore

			// ritorno i record ottenuti dalla query
			return database::query($sql, "records");
		}

		function findAll($fields = null, $order = null)
		{
			/** orm **
			 * funzione findAll
			 * ritorna tutti i record (shortcut per Orm->find)
			 * -- input --
			 * $order (string) ordinamento da utilizzare nella query
			 *        (array) elenco dei campi da utilizzare come ordinamento nella query
			 * -- output --
			 * (array) records della tabella
			 **/

			// richiama la funzione Orm->find per ottenere tutti i records
			return $this->find($fields, null, $order);
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

			if ($this->id === 0) {
				// se esiste, imposta il valore del campo "created" alla data / ora corrente
				if (array_key_exists('created', $fields)) $fields['created'] = date("Y-m-d H:i");
				// se esiste, imposta il valore del campo "modified" alla data / ora corrente
				if (array_key_exists('modified', $fields)) $fields['modified'] = date("Y-m-d H:i");

				$this->id = database::insert($this->tableName, $fields, true);
			} else {
				// se esiste, imposta il valore del campo "modified" alla data / ora corrente
				if (array_key_exists('modified', $fields)) $fields['modified'] = date("Y-m-d H:i");

				database::update($this->tableName, $fields, array($this->idFieldName => $this->id));
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

		function delete($where = null)
		{
			/** orm **
			 * funzione delete
			 * elimina il record dalla tabella
			 **/

			// elimina il record dalla tabella
			if ($where == null) $where = array($this->idFieldName => $this->id);
			database::delete($this->tableName, $where); // elimina il record (o i record, se e' specificato il parametro $where) dalla tabella
		}

		/**
		 * funzione count
		 * ritorna il numero di records presenti nella tabella
		 *
		 * @param  mixed $where (string) condizioni di ricerca (WHERE)
		 *                      (array) elenco dei campi / valori da utilizzare come condizioni di ricerca (WHERE)
		 * @return int          numero di records presenti
		 * @author Phelipe de Sterlich
		 **/
		function count($where = null)
		{
			// imposto la query di selezione
			$sql = "SELECT COUNT(*) totale FROM {$this->tableName}";
			// se sono impostate le condizioni di ricerca
			if ($where != null) {
				// se le condizioni di ricerca sono un array
				if (is_array($where)) {
					// converto da array a stringa e aggiungo alla query
					$sql .= " WHERE ".arrays::implode($where, " = ", " AND ", "'", "mysql_real_escape_string");
				// se invece è una stringa
				} else {
					// aggiungo la stringa alla query
					$sql .= " WHERE {$where}";
				}
			}

			// eseguo la query e leggo il risultato
			$result = database::query($sql, "array");

			// ritorno il numero di records presenti
			return $result["totale"];
		}

		/**
		 * funzione getComboValues
		 * ritorna un array id => descrizione dei record da utilizzare in una combo
		 *
		 * @param  string $valueField nome del campo contenente l'identificativo del record
		 * @param  string $descField  nome del campo contenente la descrizione del record
		 * @param  mixed  $where      (opzionale) eventuali condizioni where per l'estrapolazione dei records
		 * @param  mixed  $order      (opzionale) eventuali condizioni order by per l'estrapolazione dei records
		 * @param  array  $prepend    (opzionale) eventuali elementi da aggiungere in testa all'array
		 * @return array
		 * @author Phelipe de Sterlich
		 **/
		function getComboValues($valueField, $descField, $where = null, $order = null, $prepend = null)
		{
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
			$records = $this->find(array($valueField, $descField), $where, $order);
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
		 * @return mixed array "nome_campo" => array("old" => "valore_originale", "new" => "valore_modificato")
		 *               false se non ci sono campi modificati
		 * @author Phelipe de Sterlich
		 **/
		function getModifiedFields()
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
			// ritorna l'array dei campi modificati (o false se non ci sono campi modificati)
			if (count($result) == 0) {
				return false;
			} else {
				return $result;
			}
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
			$record = database::query("SELECT {$fieldName} FROM {$this->tableName} WHERE {$idFieldName} = '$idFieldValue'", "array");
			// ritorna il valore del campo richiesto
			return stripslashes($record[$fieldName]);
		}
	}

/*
class orm extends base
{
	// table: nome della tabella
	protected $table = 'tabella';
	// id_field: nome del campo id
	protected $id_field = 'id';
	// temp_id_field: nome del campo temp_id
	protected $temp_id_field = 'temp_id';
	// id: identificativo del record
	protected $id = 0;
	// fields: campi del record
	protected $fields = array();
	// date_fields: elenco dei campi in formato data
	protected $date_fields = array();
	// date_fields: elenco dei campi in formato data (default vuoto)
	protected $date_fields_empty = array();
	// time_fields: elenco dei campi in formato ora
	protected $time_fields = array();
	// encrypted_fields: elenco dei campi codificati
	protected $encrypted_fields = array();
	// datetime_fields: elenco dei campi in formato data ora
	protected $datetime_fields = array('insert_date', 'edit_date');

	// factory: creazione classe (per concatenazione)
	public static function factory($id = 0) {
		return new self($id);
	}

	// save: salva il record
	public function save() {
		global $utente;

		// genero un id temporaneo
		$temp_id = rand(1000,999999);

		// se id è a 0 (nuovo record)
		if ($this->id == 0)
		{
			// rimuovo l'id dall'elenco dei campi
			unset($this->fields[$this->id_field]);
			// imposto l'id temporaneo
			$this->fields[$this->temp_id_field] = $temp_id;
			// imposto, se presenti, i campi insert_date / insert_user
			if (array_key_exists('insert_date', $this->fields))
			{
				$this->fields['insert_date'] = date("Y-m-d H:i");
				$this->fields['insert_user'] = $utente;
				$this->fields['edit_date'] = date("Y-m-d H:i");
				$this->fields['edit_user'] = $utente;
			}

			if (array_key_exists('created', $this->fields)) $this->fields['created'] = date("Y-m-d H:i");
			if (array_key_exists('created_username', $this->fields)) $this->fields['created_username'] = $utente;
			if (array_key_exists('modified', $this->fields)) $this->fields['modified'] = date("Y-m-d H:i");
			if (array_key_exists('modified_username', $this->fields)) $this->fields['modified_username'] = $utente;

			// aggiungo il record
			database::insert($this->table, $this->fields);
			// recupero l'id del record inserito
			$this->id = database::select($this->table, $this->id_field, array($this->temp_id_field => $temp_id), $this->id_field.' DESC');
			// azzero l'id temporaneo
			database::update($this->table, array('temp_id' => 0), array($this->id_field => $this->id));
		} else {
			// aggiorno il record
			if (array_key_exists('edit_date', $this->fields)) $this->fields['edit_date'] = date("Y-m-d H:i");
			if (array_key_exists('edit_user', $this->fields)) $this->fields['edit_user'] = $utente;
			if (array_key_exists('modified', $this->fields)) $this->fields['modified'] = date("Y-m-d H:i");
			if (array_key_exists('modified_username', $this->fields)) $this->fields['modified_username'] = $utente;

			database::update($this->table, $this->fields, array($this->id_field => $this->id));
		}
	}

	*/

?>