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

		public static function element($element, $content, $id = "", $attr = null)
		{
			/**
			 * funzione element
			 *
			 * genera un tag html (specificato in $element) con apertura, contenuto e chiusura
			 *
			 * @param  string $element elemento html da generare
			 * @param  string $content contenuto
			 * @param  string $id      identificativo (id="")
			 * @param  array  $attr    attributi opzionali
			 * @return string          codice html generato
			 */

			$attributes = arrays::attributes($attr);
			if ($id != "") $attributes .= " id='{$id}'";
			return "<{$element} {$attributes}>{$content}</{$element}>";
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