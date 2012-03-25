<?php
//
//  global-functions.php
//  all-in-one-event-calendar
//
//  Created by The Seed Studio on 2012-02-28.
//

/**
 * url_get_contents function
 *
 * @param string $url URL 
 *
 * @return string
 **/
function url_get_contents( $url ) {
	// holds the output
	$output = "";

	// If CURL is available, use it
	if( function_exists( 'curl_init' ) ) { 
		
		// initialize and configure curl for a request
		$c = curl_init();
		curl_setopt( $c, CURLOPT_URL, $url );
		curl_setopt( $c, CURLOPT_RETURNTRANSFER, true );
		// set user agent to PHP/PHP version number
		curl_setopt( $c, CURLOPT_USERAGENT, 'PHP/' . PHP_VERSION );
		curl_setopt ($c, CURLOPT_ENCODING, '' );

		// Add support for HTTPS
		if( strstr( $url, 'https' ) !== FALSE ) {
			curl_setopt( $c, CURLOPT_SSLVERSION, 3 );
			curl_setopt( $c, CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $c, CURLOPT_SSL_VERIFYHOST, 2 );
		}
		curl_setopt( $c, CURLOPT_COOKIESESSION, true );
		curl_setopt( $c, CURLOPT_HEADER, false );
		if( ! ini_get( 'safe_mode' ) ) {
			curl_setopt( $c, CURLOPT_FOLLOWLOCATION, true );
		}
		// get the contents of the url
		$output = curl_exec( $c );

		curl_close( $c );
	} else {
		// if CURL is unavailable, use file_get_contents
		$output = file_get_contents( $url );
	}

	// check if data is utf-8
	if( ! SG_iCal_Parser::_ValidUtf8( $output ) ) {
		// Encode the data in utf-8
		$output = utf8_encode( $output );
	}

	return $output;
}
