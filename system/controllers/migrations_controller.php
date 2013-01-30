<?php

	/**
	 * controller Migrations
	 * funzioni di default per gestione migrazioni
	 *
	 * @package Peanut
	 * @author Phelipe de Sterlich
	 **/
	class MigrationsController extends Controller
	{

		/**
		 * disabilita l'utilizzo del modello
		 *
		 * @var bool
		 **/
		protected $useModel = false;

		/**
		 * azione index
		 * esecuzione migrazioni
		 *
		 * @return void
		 * @author Phelipe de Sterlich
		 **/
		function index()
		{
			$migrations = new Migrations();
			$migrations->execute();
			$this->template->set(array(
				"results" => $migrations->output
				));
		}

	} // END class MigrationsController extends Controller

?>