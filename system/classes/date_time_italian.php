<?php

	/**
	 * classe DateTimeItalian
	 * personalizzazione classe DateTime con lingua in italiano
	 *
	 * @package Peanut
	 * @author Phelipe de Sterlich
	 **/
	class DateTimeItalian extends DateTime
	{

		/**
		 * ritorna la data in base al formato richiesto, usando la lingua italiana
		 *
		 * @return string
		 * @author Phelipe de Sterlich
		 **/
		public function format($format)
		{
			$english = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'Semptember', 'October', 'November', 'December');
			$italian = array('Lunedi', 'Martedi', 'Mercoledi', 'Giovedi', 'Venerdi', 'Sabato', 'Domenica', 'Gennaio', 'Febbraio', 'Marzo', 'Aprile', 'Maggio', 'Giugno', 'Luglio', 'Agosto', 'Settembre', 'Ottobre', 'Novembre', 'Dicembre');

			return str_replace($english, $italian, parent::format($format));
		}

		/**
		 * imposta la data in base al parametro passato
		 *
		 * @return void
		 * @author Phelipe de Sterlich
		 **/
		public function fromDate($date)
		{
			list($giorno, $mese, $anno) = explode("/", $date);
			$this->setDate($anno, $mese, $giorno);
		}

	} // END class DateTimeItalian extends DateTime

?>