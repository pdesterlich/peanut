<?php 

	/**
	 * helper Bootstrap
	 * funzioni di utilità per framework css bootstrap
	 *
	 * @package Peanut
	 * @author Phelipe de Sterlich
	 **/
	class bootstrap
	{

		/**
		 * funzione pagination
		 * generazione codice html per paginazione
		 *
		 * @return string
		 * @author Phelipe de Sterlich
		 **/
		public static function pagination($pageCurrent, $pageCount, $controller, $action, $id, $pageName, $additionalClass = "")
		{
			$result = "<div class='pagination {$additionalClass}'><ul>";
			if ($pageCurrent > 1) $result .= "<li>" . html::link("&larr;", $controller, $action, array("id" => $id, $pageName => ($pageCurrent - 1))) . "</li>";
			for ($i=1; $i <= $pageCount; $i++) { 
				$result .= "<li " . (($pageCurrent == $i) ? "class='active'" : "") . ">" . html::link($i, $controller, $action, array("id" => $id, $pageName => $i)) . "</li>";
			}
			if ($pageCurrent < $pageCount) $result .= "<li>" . html::link("&rarr;", $controller, $action, array("id" => $id, $pageName => ($pageCurrent + 1))) . "</li>";
			$result .= "</ul></div>";

			return $result;
		}

		/**
		 * funzione carousel
		 * generazione codice html per carousel
		 *
		 * @return string
		 * @author Phelipe de Sterlich
		 **/
		public static function carousel($divId, $images)
		{
			$result = "<div id='{$divId}' class='carousel'>";
			$result .= "<div class='carousel-inner'>";
			$i = 0;
			foreach ($images as $image => $title) {
				$result .= "<div class='item ".(($i == 0) ? "active'" : "'").">";
				$result .= "<img src='{$image}' alt=''>";
				$result .= "<div class='carousel-caption'>";
				$result .= "<p>{$title}</p>";				
				$result .= "</div>";
				$result .= "</div>";
				$i++;
			}
			$result .= "</div>";
            $result .= "<a class='left carousel-control' href='#{$divId}' data-slide='prev'>‹</a>";
            $result .= "<a class='right carousel-control' href='#{$divId}' data-slide='next'>›</a>";
			$result .= "</div>";

			return $result;
		}

		/**
		 * funzione formElement
		 * genera il codice html per un elemento della form
		 *
		 * @return void
		 * @author Phelipe de Sterlich
		 **/
		public static function formElement($itemId, $label, $value, $width = "span5", $itemType = "input", $options = null)
		{
			$labelClass = "control-label";
			if (is_array($options)) {
				if (array_key_exists('labelClass', $options)) {
					$labelClass .= " ".$options["labelClass"];
				}
			}
			$result = "";
			$result .= "<div class='control-group'>";
			$result .= "<label class='{$labelClass}' for='{$itemId}'>{$label}</label>";
			$result .= "<div class='controls'>";
			switch ($itemType) {
				case 'input':
					$result .= "<input type='text' class='{$width}' id='{$itemId}' name='{$itemId}' value='{$value}'>";
					break;
				case 'textarea':
					$result .= "<textarea class='{$width}' id='{$itemId}' name='{$itemId}' rows='6'>{$value}</textarea>";
					break;
				case 'select':
					$result .= "<select class='{$width} id='$itemId' name='{$itemId}'>";					
					if (is_array($options)) {
						if (array_key_exists('selectOptions', $options)) {
							if (is_array($options["selectOptions"])) {
								foreach ($options["selectOptions"] as $optionKey => $optionValue) {
									$result .= "<option value='{$optionKey}' ".(($optionKey == $value) ? "selected" : "").">{$optionValue}</option>";
								}
							}
						}
					}
					$result .= "</select>";
					break;
			}
			$result .= "</div>";
			$result .= "</div>";

			return $result;
		}

		/**
		 * funzione breadcrumb
		 * genera il codice html per un elemento breadcrumb
		 *
		 * @param $items array elementi del breadcrumb
		 * @return string
		 * @author Phelipe de Sterlich
		 **/
		public static function breadcrumb($items)
		{
			$count = count($items);
			$i = 0;
			$result = "<ul class='breadcrumb'>";
			foreach ($items as $item) {
				$i++;
				$result .= "<li>".$item;
				if ($i < $count) $result .= "<span class='divider'>/</span>";
				$result .= "</li>";
			}
			$result .= "</ul>";

			return $result;
		}

		/**
		 * funzione alert
		 * genera il codice html per un elemento alert
		 *
		 * @param $testo      string testo dell'alert
		 * @param $alertClass string classe dell'alert (per determinare il colore della visualizzazione)
		 * @param $showClose  bool   abilita la visualizzazione del pulsante di chiusura
		 * @return string
		 * @author Phelipe de Sterlich
		 **/
		public static function alert($testo, $alertClass = "", $showClose = true)
		{
			$result = "<div class='alert {$alertClass}'>";
			if ($showClose) $result .= "<a class='close' data-dismiss='alert'>&times;</a>";
			$result .= $testo;
			$result .= "</div>";

			return $result;
		}

	}

?>