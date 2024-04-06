<?php

namespace TYPO3\AbavoMaps\User;
 
/*
 * abavo_maps
 *
 * @copyright   2014 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */
 
/**
 * Description of Geocode
 * Geocoding class
 */
class Geocode {
	
	/*
	 * Geocode by address
	 * 
	 * @return array
	 */
	public function geoCodeAdress( $address='' , $apiKey='', $quotaUser='' ){
 
		$address = str_replace (" ", "+", urlencode( strip_tags($address) ));
		$details_url = "https://maps.googleapis.com/maps/api/geocode/json?address=".$address."&sensor=false"
			.(($apiKey!='') ? '&key='.$apiKey : '')
			.(($quotaUser!='') ? '&quotaUser='.$quotaUser : '');

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $details_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$response = json_decode(curl_exec($ch), true, 512, JSON_THROW_ON_ERROR);

		// If Status Code is ZERO_RESULTS, OVER_QUERY_LIMIT, REQUEST_DENIED or INVALID_REQUEST
		if ($response['status'] != 'OK') {
			return null;
		}else{
			return ['longitude' => (float) $response['results'][0]['geometry']['location']['lng'], 'latitude' => (float) $response['results'][0]['geometry']['location']['lat']];
		}

	}
	
	
	/*
	 * Geocode by IP method
	 * returns a imprecise location (for country use only)
	 * 
	 * @return array
	 */
	public function geoCodeIp( $ip='' ){
		
		$request_url = 'http://freegeoip.net/json/'.strip_tags($ip);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $request_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$response = json_decode(curl_exec($ch), true, 512, JSON_THROW_ON_ERROR);
		
		if (!is_array($response)){
			return null;
		}else{			
			return ['longitude' => $response['longitude'], 'latitude' => $response['latitude']];
		}
	}
}
