<?php
	/**
	 * helper html
	 *
	 * funzioni di generazione codice html
	 *
	 * @package   system > helpers
	 * @copyright Moorea Software
	 */

	class html
	{

		/**
		 * funzione url
		 * genera un url dai parametri passati
		 *
		 * @param  string $controller nome del controller per cui generare il link
		 * @param  string $action     nome dell'azione per cui generare il link
		 * @param  mixed  $params     (array) parametri aggiuntivi: array associativo nome => valore
		 *                            (string) parametri aggiuntivi: 
		 * @return string             url generato
		 * @author Phelipe de Sterlich
		 */
		public static function url($controller = "", $action = "", $params = null)
		{
			global $config;

			$result = "";

			// legge l'url base e aggiunge, se necessario, lo slash finale
			$basePath = $config["url"]["base"];
			if (($basePath != "") AND (substr($basePath, -1) != "/")) $basePath .= "/";

			// legge l'impostazione del parametro short url
			$shortUrl = $config["url"]["short"];

			// aggiunge il controller
			if ($controller != "") $result .= ($shortUrl) ? "/{$controller}" : "&controller={$controller}";

			// aggiunge l'action
			if ($action != "") $result .= ($shortUrl) ? "/{$action}" : "&action={$action}";

			if ($params != null)
			{
				if (is_numeric($params))
				{
					// lo considera come identificativo record e lo aggiunge all'url
					$result .= ($shortUrl) ? "/{$params}" : "&id={$params}";
				}
				else if (is_array($params))
				{
					// aggiunge ogni parametro presente nell'array
					foreach ($params as $key => $value) $result .= "&{$key}={$value}";
				}
				else
				{
					// lo aggiunge cosi' com'è all'url
					$result .= "&".$params;
				}
			}

			// se il risultato è diverso da vuoto, rimuovo il primo carattere (&) e lo sostituisco con ?
			// sostituisce la prima occorrenza di & con ?
			$result = preg_replace('/&/', '?', $result, 1);
			// aggiunge protocollo, path base e la pagina base
			$result = $basePath . (($shortUrl) ? "" : "index.php") . $result;
			$result = (($basePath == "") ? "" : $config["url"]["protocol"] . "://") . str_replace("//", "/", $result);

			// ritorna l'url generato, rimuovendo eventuali doppi slash
			return $result;
		}

		/**
		 * funzione location
		 * genera un header "location" verso l'url dai parametri passati
		 *
		 * @param  string $controller nome del controller per cui generare il link
		 * @param  string $action     nome dell'azione per cui generare il link
		 * @param  mixed  $params     parametri aggiuntivi
		 * @param  bool   $exit       flag per eseguire l'uscita dall'esecuzione del codice php
		 * @return string             url generato
		 * @author Phelipe de Sterlich
		 */
		public static function location($controller = "", $action = "", $params = null, $exit = true)
		{

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
			// genera l'header verso la location e lo invia al browser
			header("Location: ".$location);
			// se previsto, esce dall'applicazione
			if ($exit) exit;
		}

		/**
		 * funzione link
		 * genera un link "<a href ..." verso l'url dai parametri passati
		 *
		 * @param  string $caption    testo del link
		 * @param  string $controller nome del controller per cui generare il link
		 * @param  string $action     nome dell'azione per cui generare il link
		 * @param  mixed  $params     parametri aggiuntivi
		 * @param  array  $attr       attributi opzionali del link
		 * @return string             url generato
		 * @author Phelipe de Sterlich
		 */
		public static function link($caption, $controller = "", $action = "", $params = null, $attr = null)
		{
			$attributes = arrays::attributes($attr);
			return "<a href='".html::url($controller, $action, $params)."' {$attributes}>{$caption}</a>";
		}

		/**
		 * funzione ext_link
		 *
		 * genera un link "<a href ..." verso l'url indicato
		 *
		 * @param  string $caption testo del link
		 * @param  string $url     indirizzo per cui generare il link
		 * @param  array  $attr    attributi opzionali del link
		 * @return string          url generato
		 * @author Phelipe de Sterlich
		 */
		public static function ext_link($caption, $url, $attr = null)
		{
			$attributes = arrays::attributes($attr);
			return "<a href='{$url}' {$attributes}>{$caption}</a>";
		}

		/**
		 * funzione js_link
		 *
		 * genera un link "<a onClick ..." che esegue il codice javascript indicato
		 *
		 * @param  string $caption testo del link
		 * @param  string $action  codice javascript da eseguire
		 * @param  array  $attr    attributi opzionali del link
		 * @return string          url generato
		 * @author Phelipe de Sterlich
		 */
		public static function js_link($caption, $action, $attr = null)
		{
			$attributes = arrays::attributes($attr);
			return "<a href='#' onClick='{$action}' {$attributes}>{$caption}</a>";
		}

		/**
		 * funzione div
		 * genera un tag <div>
		 *
		 * @param  string $content contenuto
		 * @param  string $id      identificativo (id="")
		 * @param  array  $attr    attributi opzionali
		 * @return string          div generato
		 * @author Phelipe de Sterlich
		 */
		public static function div($content, $id = "", $attr = null)
		{
			$attributes = arrays::attributes($attr);
			if ($id != "") $attributes .= " id='{$id}'";
			return "<div {$attributes}>{$content}</div>";
		}

		/**
		 * funzione img
		 * genera un tag <img>
		 *
		 * @param  string $src  origine immagine
		 * @param  string $alt  testo alternativo
		 * @param  array  $attr attributi opzionali
		 * @return string       img generato
		 * @author Phelipe de Sterlich
		 */
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

		/**
		 * funzione mailto
		 *
		 * genera un link html a un'indirizzo email
		 *
		 * @param  string $address indirizzo email per cui generare il link
		 * @param  string $caption testo da mostrare (opzionale - se non specificato viene mostrato l'indirizzo stesso)
		 * @return string          codice html generato
		 * @author Phelipe de Sterlich
		 */
		public static function mailto($address, $caption = "")
		{
			if ($caption == "") $caption = $address;

			return ($address == "") ? "" : "<a href='mailto://{$address}'>{$caption}</a>";
		}

		/**
		 * funzione tr
		 *
		 * genera una riga di una tabella
		 *
		 * @param  array  $values  valori da aggiungere come colonne (<td>)
		 * @param  array  $attr    attributi della riga
		 * @param  string $colType tipo elemento per dato colonna (default: td)
		 * @return string          codice html generato
		 * @author Phelipe de Sterlich
		 */
		public static function tr($values, $attr = null, $colType = "td")
		{
			// apre la riga
			$result = "<tr ".arrays::attributes($attr).">";
			// per ogni valore della riga
			foreach ($values as $item => $data) {
				if (!is_array($data)) {
					if ($data == "") $data = "&nbsp;";
					$result .= "<{$colType}>{$data}</{$colType}>";
				} else {
					if ($item == "") $item = "&nbsp;";
					$result .= "<{$colType} ".arrays::attributes($data).">{$item}</{$colType}>";
				}
			}
			// chiude la riga
			$result .= "</tr>";
			
			return $result;
		}

	}

?>