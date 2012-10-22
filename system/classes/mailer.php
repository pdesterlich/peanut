<?php

	/**
	 * classe Mailer
	 * gestione invio messaggi
	 *
	 * @package peanut
	 * @author Phelipe de Sterlich
	 **/
	class Mailer extends base
	{
		/**
		 * soggetto della mail
		 *
		 * @var    string
		 * @access protected
		 **/
		protected $subject = "";

		/**
		 * destinatario / destinatari
		 *
		 * @var    mixed (string) indirizzo email a cui inviare il messaggio
		 *               (array)  array email => nome dei destinatari del messaggio
		 * @access protected
		 **/
		protected $to = "";

		/**
		 * corpo del messaggio
		 *
		 * @var string
		 * @access protected
		 **/
		protected $body = "";

		/**
		 * headers del messaggio
		 *
		 * @var string
		 * @access protected
		 **/
		protected $headers = "";

		/**
		 * funzione factory
		 * crea e ritorna una nuova istanza della classe
		 *
		 * @concatenabile
		 * @return object (Mail)
		 */
		public static function factory()
		{
			// crea la nuova istanza della classe Mail e la ritorna
			return new Mailer();
		}

		/**
		 * funzione send
		 * invia il messaggio
		 *
		 * @return void
		 * @author Phelipe de Sterlich
		 **/
		function send()
		{
			// converte, se necessario, l'array dei destinatari in un'unica stringa (indirizzi separati da virgola)
			$to = (is_array($this->to)) ? implode(",", array_keys($this->to)) : $this->to;
			// invia il messaggio e ritorna il risultato
			return mail($to, $this->subject, $this->body, $this->headers);
		}
	}

?>