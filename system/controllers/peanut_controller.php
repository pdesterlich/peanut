<?php
	/**
	 * Peanut Controller
	 *
	 * controller per gestione pagine esempio / default
	 *
	 * @package system > controllers
	 * @copyright Moorea Software
	 **/

	class PeanutController extends Controller
	{

		// disabilita l'uso del modello
		public $useModel = false;

		/**
		 * funzione __construct
		 * inizializzazione controller
		 *
		 * @return void
		 * @author Phelipe de Sterlich
		 **/
		function __construct($id = 0)
		{
			parent::__construct($id);
			// imposta il nome dell'applicazione per layout e template
			$this->layout->set("appname", "Peanut!");
			$this->template->set("appname", "Peanut!");

			/*
			loadClass("", "ua-parser".DS."UAparser");
			// UA::get();
			$ua = UA::parse();

			dump ($ua->browser);         // Chrome (can also use $ua->browser)
			dump ($ua->browserFull);    // Chrome 16.0.912

			dump ($ua->os);             // Mac OS X
			dump ($ua->osFull);         // Mac OS X 10.6.8

			// some other generic boolean options

			dump ($ua->isMobile);       // true or false
			dump ($ua->isMobileDevice); // true or false
			dump ($ua->isTablet);       // true or false
			dump ($ua->isSpider);       // true or false
			dump ($ua->isComputer);     // true or false
			dump ($ua->isUIWebview);    // true or false, iOS-only
			*/
		}

	}
	

?>