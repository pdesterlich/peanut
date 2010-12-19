<?php
	/** peanut **
	 * the small php framework
	 * (c) Phelipe de Sterlich | Moorea Software
	 **/

	define('DS', DIRECTORY_SEPARATOR);
	define('ROOT', realpath(dirname(__FILE__).DS.".."));
	define('SYSTEM', ROOT.DS."system");
	define('APP', ROOT.DS."app");

	include SYSTEM.DS."peanut.php";
?>