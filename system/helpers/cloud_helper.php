<?php

	/**
	 * helper cloud
	 * funzioni di utilità per servizi cloud
	 *
	 * @package peanut
	 * @author Phelipe de Sterlich
	 **/
	class cloud
	{

		/**
		 * funzione gravatarGetImage
		 * ottiene l'immagine utente associata ad un'email da gravatar
		 *
		 * @param $email   string  The email address
		 * @param $size    integer Size in pixels, defaults to 80px [ 1 - 2048 ]
		 * @param $default string  Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
		 * @param $rating  string  Maximum rating (inclusive) [ g | pg | r | x ]
		 * @param $img     bool    True to return a complete IMG tag False for just the URL
		 * @param $attr    array   Optional, additional key/value attributes to include in the IMG tag
		 * @return         string  url dell'immagine o tag <img> completo
		 * @author Phelipe de Sterlich
		 **/
		public static function gravatarGetImage($email, $size = 80, $default = "mm", $rating = "r", $img = false, $attr = null)
		{
			$url = (Configure::read("url.protocol") == "https") ? "https://secure.gravatar.com/avatar/" : "http://www.gravatar.com/avatar/";
			$url .= md5( strtolower( trim( $email ) ) );
			$url .= sprintf("?s=%d&d=%s&r=%s", $size, $default, $rating);

			if ($img) $url = html::img($url, "", $attr);

			return $url;
		}

	} // END class cloud

?>