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

	}

?>