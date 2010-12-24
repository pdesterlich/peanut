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
		protected $id = 0; // identificativo record
		protected $tableName = ""; // nome tabella
		protected $idFieldName = "id"; // nome del campo chiave

		/**
		 * array contenente campi e valori del record attuale
		 *
		 * @var    array
		 * @access protected
		 **/
		protected $fields = array(); // array contenente campi e valori del record

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

		function __construct($id = 0) {
			/** orm **
			 * funzione __construct
			 * inizializzazione oggetto
			 * -- input --
			 * $id (integer) eventuale identificativo del record
			 **/

			// richiamo la procedura __construct della classe padre
			parent::__construct();

			// se il nome della tabella non è specificato, lo imposto al nome del modello (meno la parola "Model")
			if ($this->tableName == "") $this->tableName = strtolower(str_replace("Model", "", get_class($this)));

			// carico il record di cui è passato l'identificativo
			$this->load($id);
		}

		function __get($var) {
			/** orm **
			 * funzione __get
			 * magic get per ottenere le proprietà dell'oggetto
			 * -- input --
			 * $var (string) nome della proprietà da ritornare
			 * -- output --
			 * (variant) valore della proprietà
			 **/

			// se esiste una proprietà col nome indicato
			if (isset($this->$var)) {
				// ritorno il valore della proprietà
				return $this->$var;
			// se esiste un campo col nome indicato
			} else if (array_key_exists($var, $this->fields)) {
				// ritorno il valore del campo (eventualmente convertito per la visualizzazione)
				return $this->getField($var);
			}
		}

		function __set($var, $value) {
			/** orm **
			 * funzione __set
			 * magic set per impostare il valore di una proprietà dell'oggetto
			 * -- input --
			 * $var (string) nome della proprietà da modificare
			 * $value (variant) valore da impostare
			 **/

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

		function getRecord() {
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

		function getField($field) {
			/** orm **
			 * funzione getField
			 * ritorna il valore di un campo, eventualmente convertendolo per la visualizzazione
			 * -- input --
			 * $field (string) nome del campo per cui ottenere il valore
			 * -- output --
			 * (string) valore del campo
			 **/

			// se esiste un il campo passato come parametro
			if (array_key_exists($field, $this->fields)) {
				global $cipher;

				// leggo il valore del campo
				$value = $this->fields[$field];

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
			// se invece il campo non esiste
			} else {
				// ritorno una stringa vuota
				return "";
			}
		}

		function setField($field, $value) {
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

		function load($id = false) {
			/** orm **
			 * funzione load
			 * carica il record dal database, se non presente carica la struttura della tabella
			 * -- input --
			 * $id (integer o string) identificativo univoco del record
			 **/

			// se è passato un identificativo, lo imposto 
			if ($id) $this->id = $id;

			// inizializzo l'array campi / valori
			$this->fields = array();

			// se l'identificativo record è specificato
			if ($this->id) {
				// carico l'array associativo dei campi del record nella proprietà fields
				$this->fields = database::query("SELECT * FROM {$this->tableName} WHERE {$this->idFieldName} = '{$this->id}'", "array");
			}
			// se l'identificativo non è passato, oppure non ci sono campi ottenuti dalla precedente query
			if ((!$this->id) OR (count($this->fields) == 0) OR ($this->fields == null))
			{
				// carico la descrizione delle colonne dalla tabella
				$fields = database::query("SHOW COLUMNS FROM {$this->tableName}", "records");
				// per ogni colonna
				foreach ($fields as $field) {
					// aggiungo la colonna all'array campi / valori
					$this->fields[$field["Field"]] = "";
				}
			}
		}

		function find($fields = null, $where = null, $order = null, $limit = "") {
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

		function findAll($fields = null, $order = null) {
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

		function fromPost($header = "") {
			/** orm **
			 * funzione fromPost
			 * valorizza i campi del record in base ai dati passati via POST (in genere da una form)
			 * -- input --
			 * $header (string) eventuale prefisso al nome dei campi utilizzato nel POST
			 **/

			// ciclo su tutti i campi del record
			foreach ($this->fields as $key => $value) {
				// se il campo non è l'identificativo record
				if ($key != $this->idFieldName) {
					// se nel post esiste un input con lo stesso nome del campo
					if (isset($_POST[$header.$key])) {
						$this->setField($key, $_POST[$header.$key]);
					}
				}
			}
		}

		function save() {
			/** orm **
			 * funzione save
			 * salva il record nella tabella
			 **/

			$fields = $this->fields;
			unset($fields[$this->idFieldName]);

			if ($this->id === 0) {
				$this->id = database::insert($this->tableName, $fields, true);
			} else {
				database::update($this->tableName, $fields, array($this->idFieldName => $this->id));
			}
		}

		function insert() {
			/** orm **
			 * funzione insert
			 * inserisce un nuovo record nella tabella, indipendentemente dal fatto che il record attuale esista (id != 0) o meno
			 **/

			// forzo l'identificativo del record a 0
			$this->id = 0;
			// salvo il record
			$this->save();
		}

		function delete($where = null) {
			/** orm **
			 * funzione delete
			 * elimina il record dalla tabella
			 **/

			// elimina il record dalla tabella
			if ($where == null) $where = array($this->idFieldName => $this->id);
			database::delete($this->tableName, $where); // elimina il record (o i record, se e' specificato il parametro $where) dalla tabella
		}

		function count($where = null) {
			/** orm **
			 * funzione count
			 * conta i record nella tabella
			 * -- input --
			 * $where (string) condizioni di ricerca (WHERE)
			 *        (array) elenco dei campi / valori da utilizzare come condizioni di ricerca (WHERE)
			 * -- output --
			 * (integer) numero di record presenti
			 **/

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
		 * @return array
		 * @author Phelipe de Sterlich
		 **/
		function getComboValues($valueField, $descField, $where = null, $order = null)
		{
			// inizializza l'array dei risultati
			$result = array();
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