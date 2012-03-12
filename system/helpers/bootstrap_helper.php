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
		public static function formElement($itemId, $label, $value, $width = "span5")
		{
			$result = "";
			$result .= "<div class='control-group'>";
			$result .= "<label class='control-label' for='{$itemId}'>{$label}</label>";
			$result .= "<div class='controls'>";
			$result .= "<input type='text' class='{$width}' id='{$itemId}' name='{$itemId}' value='{$value}'>";
			$result .= "</div>";
			$result .= "</div>";

			return $result;
		}
	}

?>