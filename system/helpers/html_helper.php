<?php
	/**
	 * html_helper.php
	 *
	 * funzioni di generazione codice html
	 *
	 * @author    Phelipe de Sterlich
	 * @copyright Moorea Software | Elco Sistemi srl
	 * @package   peanut
	 **/

	/**
	 * classe html
	 *
	 * funzioni di generazione codice html
	 *
	 * @author    Phelipe de Sterlich
	 * @copyright Moorea Software | Elco Sistemi srl
	 * @package   peanut
	 */
	class html
	{

		/**
		 * funzione url
		 *
		 * genera un url dai parametri passati
		 *
		 * @param  string $controller nome del controller per cui generare il link
		 * @param  string $action     nome dell'azione per cui generare il link
		 * @param  mixed  $params     parametri aggiuntivi
		 * @return string             url generato
		 */
		public static function url($controller = "", $action = "", $params = null)
		{
			// inizializzo il risultato
			$result = "";
			// se definito, aggiungo il controller
			if ($controller != "") $result .= "&controller={$controller}";
			// se definito, aggiungo l'action
			if ($action != "") $result .= "&action={$action}";
			// se è presente un ulteriore parametro
			if ($params != null) {
				// se il parametro è un array
				if (is_array($params)) {
					// per ogni elemento dell'array
					foreach ($params as $key => $value) {
						// lo aggiungo come parametro GET
						$result .= "&{$key}={$value}";
					}
				// se il parametro non è un array
				} else {
					// lo considero come identificativo record e lo aggiungo come parametro GET
					$result .= "&id={$params}";
				}
			}
			// se il risultato è diverso da vuoto, rimuovo il primo carattere (&) e lo sostituisco con ?
			if ($result != "") $result = "?" . substr($result, 1);
			// aggiungo la pagina base
			$result = "index.php" . $result;
			// ritorno l'url generato
			return $result;
		}

		public static function location($controller = "", $action = "", $params = null, $exit = true)
		{
			/**
			 * funzione location
			 *
			 * genera un header "location" verso l'url dai parametri passati
			 *
			 * @param  string $controller nome del controller per cui generare il link
			 * @param  string $action     nome dell'azione per cui generare il link
			 * @param  mixed  $params     parametri aggiuntivi
			 * @param  bool   $exit       flag per eseguire l'uscita dall'esecuzione del codice php
			 * @return string             url generato
			 */

			// genero l'header e lo invio al browser
			header("Location: ".html::url($controller, $action, $params));
			if ($exit) exit;
		}

		/**
		 * funzione locationDirect
		 * genera un header "location" verso la location passata (interna all'applicazione)
		 *
		 * @param  string $location location verso cui fare il redirect
		 * @param  bool   $exit     flag per eseguire l'uscita dall'esecuzione del codice php
		 * @return void
		 * @author Phelipe de Sterlich
		 **/
		public static function locationDirect($location, $exit = true)
		{
			// se la location NON è una stringa vuota, gli aggiunge in testa il carattere ?
			if ($location != "") $location = "?".$location;
			// genera l'header verso la location e lo invia al browser
			header("Location: index.php".$location));
			// se previsto, esce dall'applicazione
			if ($exit) exit;
		}

		public static function link($caption, $controller = "", $action = "", $params = null, $attr = null)
		{
			/**
			 * funzione link
			 *
			 * genera un link "<a href ..." verso l'url dai parametri passati
			 *
			 * @param  string $caption    testo del link
			 * @param  string $controller nome del controller per cui generare il link
			 * @param  string $action     nome dell'azione per cui generare il link
			 * @param  mixed  $params     parametri aggiuntivi
			 * @param  array  $attr       attributi opzionali del link
			 * @return string             url generato
			 */

			$attributes = arrays::attributes($attr);
			return "<a href='".html::url($controller, $action, $params)."' {$attributes}>{$caption}</a>";
		}

		public static function ext_link($caption, $url, $attr = null)
		{
			/**
			 * funzione ext_link
			 *
			 * genera un link "<a href ..." verso l'url indicato
			 *
			 * @param  string $caption testo del link
			 * @param  string $url     indirizzo per cui generare il link
			 * @param  array  $attr    attributi opzionali del link
			 * @return string          url generato
			 */

			$attributes = arrays::attributes($attr);
			return "<a href='{$url}' {$attributes}>{$caption}</a>";
		}

		public static function js_link($caption, $action, $attr = null)
		{
			/**
			 * funzione js_link
			 *
			 * genera un link "<a onClick ..." che esegue il codice javascript indicato
			 *
			 * @param  string $caption testo del link
			 * @param  string $action  codice javascript da eseguire
			 * @param  array  $attr    attributi opzionali del link
			 * @return string          url generato
			 */

			$attributes = arrays::attributes($attr);
			return "<a href='#' onClick='{$action}' {$attributes}>{$caption}</a>";
		}

		public static function div($content, $id = "", $attr = null)
		{
			/**
			 * funzione div
			 *
			 * genera un tag <div>
			 *
			 * @param  string $content contenuto
			 * @param  string $id      identificativo (id="")
			 * @param  array  $attr    attributi opzionali
			 * @return string          div generato
			 */

			$attributes = arrays::attributes($attr);
			if ($id != "") $attributes .= " id='{$id}'";
			return "<div {$attributes}>{$content}</div>";
		}

		public static function img ($src, $alt = "", $attr = null)
		{
			$attributes = arrays::attributes($attr);
			if ($alt != "") $alt = "alt='{$alt}'";
			return "<img src='{$src}' {$alt} $attributes>";
		}

		/**
		 * genera uno o piu' tag html con apertura, contenuto e chiusura
		 *
		 * @param  string $tag     elemento html da generare
		 * @param  mixed  $content se stringa: contenuto del tag; se array: contenuti multipli (un tag per elemento)
		 * @param  string $id      identificativo (id="")
		 * @param  array  $attr    attributi opzionali
		 * @return string          codice html generato
		 * @author Phelipe de Sterlich
		 **/
		public static function element($tag, $content, $id = "", $attr = null)
		{
			// converte l'array degli attributi in una stringa
			$attributes = arrays::attributes($attr);
			// inizializza il codice html da ritornare
			$result = "";
			// se il contenuto è un array (generazione di piu' tag)
			if (is_array($content)) {
				// per ogni coppia tag > contenuto dell'array
				foreach ($content as $value) {
					// aggiunge al risultato il tag ed il relativo contentu
					$result .= "<{$tag} {$attributes}>{$value}</{$tag}>\n";
				}
			// altrimenti viene considerato come stringa (generazione tag singolo)
			} else {
				// se è specificato l'id lo aggiunge agli attributi
				if ($id != "") $attributes .= " id='{$id}'";
				// aggiunge al risultato il tag ed il relativo contentu
				$result .= "<{$tag} {$attributes}>{$content}</{$tag}>\n";
			}
			// ritorna il risultato
			return $result;
		}

		public static function mailto($address, $caption = "")
		{
			/**
			 * funzione mailto
			 *
			 * genera un link html a un'indirizzo email
			 *
			 * @param  string $address indirizzo email per cui generare il link
			 * @param  string $caption testo da mostrare (opzionale - se non specificato viene mostrato l'indirizzo stesso)
			 * @return string          codice html generato
			 */

			if ($caption == "") $caption = $address;
			return "<a href='mailto://{$address}'>{$caption}</a>";
		}

	}

?>