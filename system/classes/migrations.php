<?php 

/**
 * classe Migrations
 * gestione migrazioni per creazione / aggiornamento database
 *
 * deve esistere un file full.php con lo script di creazione completo del database (sempre aggiornato)
 * deve esistere un file 1.php (sono permessi anche uno o piu' zeri prima del numero, esempio 0001.php) anche vuoto (non viene comunque mai utilizzato)
 * possono esistere uno o piu' files nominati con numero in serie (2.php, 3.php ecc.) contenente le successive migrazioni
 * tutti questi files devono essere file php contenenti due variabili:
 * $description = descrizione della migrazione
 * $migrations = array contenente le singole query da eseguire
 *
 * @package Peanut
 * @author  Phelipe de Sterlich
 **/
class Migrations extends base
{

	/**
	 * currentVersion
	 * versione corrente del database (default: 0)
	 *
	 * @var integer
	 **/
	protected $currentVersion = 0;

	/**
	 * availableVersion
	 * versione disponibile dai files di migrazione (default: 0)
	 *
	 * @var integer
	 **/
	protected $availableVersion = 0;

	/**
	 * migrationFiles
	 * elenco files migrazioni (con numero migrazione)
	 *
	 * @var array (migrationNum => migrationFile)
	 **/
	protected $migrationFiles = array();

	/**
	 * defines
	 * elenco definizioni per sostituzione in query
	 *
	 * @var array
	 **/
	protected $defines = array();

	/**
	 * definesFilePath
	 * percorso completo del file delle definizioni
	 *
	 * @var string
	 **/
	public $definesFilePath = "";

	/**
	 * migrationsTableName
	 * nome della tabella dove vengono registrati i dati relativi alla migrazione corrente
	 *
	 * @var string
	 **/
	public $migrationsTableName = "{table_prefix}sys_migrations";

	/**
	 * migrationsPath
	 * percorso dei file delle migrazioni
	 *
	 * @var string
	 **/
	public $migrationsPath = "";

	/**
	 * migrationFullFile
	 * nume del file contenente la creazione completa del database
	 *
	 * @var string
	 **/
	public $migrationFullFile = "full.php";

	/**
	 * output
	 * contiene gli output dei file eseguiti
	 *
	 * @var array
	 **/
	public $output = array();

	/**
	 * funzione getCurrentVersion
	 * legge la versione corrente del database, crea se necessario la tabella di gestione delle migrazioni
	 *
	 * @return void
	 * @author Phelipe de Sterlich
	 **/
	protected function getCurrentVersion()
	{
		if (mysql_num_rows(database::query("SHOW TABLES LIKE '{$this->migrationsTableName}'"))) {
			$record = database::query("SELECT * FROM {$this->migrationsTableName} ORDER BY id", "array");
			dump ($record);
			$this->currentVersion = $record["current"];
		} else {
			database::query("CREATE TABLE {$this->migrationsTableName} (id INTEGER AUTO_INCREMENT PRIMARY KEY, current INTEGER DEFAULT 0)");
			database::query("INSERT INTO {$this->migrationsTableName} (current) VALUES (0)");
		}
	}

	/**
	 * funzione setAvailableVersion
	 * imposta la versione disponibile come corrente
	 *
	 * @return void
	 * @author Phelipe de Sterlich
	 **/
	protected function setAvailableVersion()
	{
		database::query("UPDATE {$this->migrationsTableName} SET current = {$this->availableVersion}");
	}

	/**
	 * funzione findMigrationFiles
	 * crea la lista dei files delle migrazioni
	 *
	 * @return void
	 * @author Phelipe de Sterlich
	 **/
	protected function findMigrationFiles()
	{
		// cerca tutti i files php nella directory impostata per le migrazioni, per ogni file trovato
		foreach (glob($this->migrationsPath.DS."*.php") as $file) {
			// ottiene il nome del file senza estensione
			$fileName = str_replace(".php", "", $file);
			// se il file è un numero
			if (is_numeric($fileName)) {
				// ottiene il numero di migrazione
				$migrationNum = intval($fileName);
				// aggiunge il file all'array dei files delle migrazioni
				$this->migrationFiles[$migrationNum] = $this->migrationsPath.DS.$file;
				// se il numero di migrazione è superiore alla versione attuale la aggiorna
				if ($migrationNum > $this->availableVersion) $this->availableVersion = $migrationNum;
			}
		}
	}

	/**
	 * funzione loadDefines
	 * carica le definizioni per le sostituzioni in query
	 *
	 * @return void
	 * @author Phelipe de Sterlich
	 **/
	protected function loadDefines()
	{
		if (file_exists($this->definesFilePath)) {
			// rimuove l'eventuale riferimento alla variabile $defines
			if (isset($defines)) unset($defines);
			// include il file delle definizioni
			include $this->definesFilePath;
			// aggiunge le definizioni all'array interno delle definizioni
			foreach ($defines as $key => $value) {
				$this->defines[$key] = $value;
			}
		}
	}

	/**
	 * funzione executeFile
	 * esegue le migrazioni contenute in un file
	 *
	 * @return void
	 * @author Phelipe de Sterlich
	 **/
	protected function executeFile($file)
	{
		dump ("processo file: ".$file);

		// rimuove i riferimenti alle variabili $description e $migrations
		if (isset($description)) unset($description);
		if (isset($migrations)) unset($migrations);

		// include il file delle migrazioni
		include $file;

		// aggiunge all'output la descrizione della migrazione
		if (isset($descrizione)) $this->output[] = $description;

		// se è presente l'array delle query
		if ((isset($migrations)) && (is_array($migrations))) {
			// per ogni query presente nell'array
			foreach ($migrations as $query) {
				// esegue le eventuali sostituzioni delle definizioni
				foreach ($this->defines as $key => $value) {
					$query = str_replace($key, $value, $query);
				}
				// esegue la query
				database::query($query);
			}
		}
	}

	/**
	 * funzione execute
	 * esegue le migrazioni sul database
	 *
	 * @return void
	 * @author Phelipe de Sterlich
	 **/
	function execute()
	{
		// inizializza, se serve, il percorso delle migrazioni
		if ($this->migrationsPath == "") $this->migrationsPath = APP.DS."migrations";
		// inizializza, se serve, il percorso delle definizioni
		if ($this->definesFilePath == "") $this->definesFilePath = $this->migrationsPath.DS."defines.php";

		// ottiene la versione corrente sul database
		$this->getCurrentVersion();
		// ottiene la lista dei files di migrazione e la versione disponibile
		$this->findMigrationFiles();
		// se la versione corrente non esiste (database vuoto)
		dump ($this->currentVersion);
		if ($this->currentVersion == 0) {
			// esegue il file completo di creazione database
			$this->executeFile($this->migrationsPath.DS.$this->migrationFullFile);
		// se invece la versione disponibile è superiore alla versione corrente
		} else if ($this->availableVersion > $this->currentVersion) {
			// per ogni migrazione presente
			for ($i = ($this->currentVersion + 1); $i <= $this->availableVersion; $i++) { 
				// esegue la migrazione
				$this->executeFile($this->migrationFiles[$i]);
			}
		}
	}
}

?>