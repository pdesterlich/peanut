<?php

	/**
	 * helper maps
	 * funzioni di utilità per gestione mappe
	 *
	 * @package peanut
	 * @author Phelipe de Sterlich
	 **/
	class maps
	{

		/**
		 * funzione geocode
		 * ritorna un array contenente le coordinate dell'indirizzo passato (o null in caso di errore)
		 *
		 * @return array
		 * @author Phelipe de Sterlich
		 **/
		function geocode($address)
		{
			$string      = str_replace (" ", "+", urlencode($address));
			$details_url = "http://maps.googleapis.com/maps/api/geocode/json?address=".$string."&sensor=false";

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $details_url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$response = json_decode(curl_exec($ch), true);

			// If Status Code is ZERO_RESULTS, OVER_QUERY_LIMIT, REQUEST_DENIED or INVALID_REQUEST
			if ($response['status'] != 'OK') {
				return null;
			}

			$geometry  = $response['results'][0]['geometry'];

			$array = array(
				'latitude'      => $geometry['location']['lat'],
				'longitude'     => $geometry['location']['lng'],
				'location_type' => $geometry['location_type'],
			);

			return $array;
		}

	} // END class maps

?>