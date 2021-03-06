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
			$english = array(
				'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday',
				'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun',
				'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December',
				'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec',
				);
			$italian = array(
				'Lunedi', 'Martedi', 'Mercoledi', 'Giovedi', 'Venerdi', 'Sabato', 'Domenica',
				'Lun', 'Mar', 'Mer', 'Gio', 'Ven', 'Sab', 'Dom',
				'Gennaio', 'Febbraio', 'Marzo', 'Aprile', 'Maggio', 'Giugno', 'Luglio', 'Agosto', 'Settembre', 'Ottobre', 'Novembre', 'Dicembre',
				'Gen', 'Feb', 'Mar', 'Apr', 'Mag', 'Giu', 'Lug', 'Ago', 'Set', 'Ott', 'Nov', 'Dic',
				);

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

			// ritorno l'oggetto (per concatenazione)
			return $this;
		}

		/**
		 * imposta la data / ora in base al valore passato
		 * (record mysql datetime nel formato YYYY-MM-DD HH:MM:SS)
		 *
		 * @return void
		 * @author Phelipe de Sterlich
		 **/
		public function fromMysqlDateTime($datetime)
		{
			$delimiters = array("-", " ", ":");
			list($anno, $mese, $giorno, $ore, $minuti, $secondi) = explode($delimiters[0], str_replace($delimiters, $delimiters[0], $datetime));
			$this->setDate($anno, $mese, $giorno);
			$this->setTime($ore, $minuti, $secondi);

			return $this;
		}

	} // END class DateTimeItalian extends DateTime

?>
