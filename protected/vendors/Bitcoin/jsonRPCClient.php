<?php

/*
	jsonRPCClient - Modified for Bitcoin Webskin
*/
/*
					COPYRIGHT

Copyright 2007 Sergio Vaccaro <sergio@inservibile.org>

This file is part of JSON-RPC PHP.

JSON-RPC PHP is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

JSON-RPC PHP is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with JSON-RPC PHP; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
/**
 * The object of this class are generic jsonRPC 1.0 clients
 * http://json-rpc.org/wiki/specification
 *
 * @author sergio <jsonrpcphp@inservibile.org>
 */
 
class jsonRPCClient {
	
	private $url, $debug;

	public function __construct( $url, $debug = false ) {
		$this->url = $url;
		$this->debug = $debug;
	}
	
	public function __call( $method, $params ) {
	
		if( !is_scalar($method) ) { throw new Exception('Method name has no scalar value'); }
		if( !is_array($params)  ) { throw new Exception('Params must be given as array');   }
		
		$request = json_encode( array( 'method' => $method, 'params' => array_values($params), 'id' => 2 ) );
		$this->debug("&gt; $request");
		
		$opts = array ( 'http' => array ( 'method'  => 'POST', 'header'  => 'Content-type: application/json',
			'timeout'  => 5, 'ignore_errors' => 1, 'content' => $request ) );

		if( $fp = @fopen($this->url, 'r', false, stream_context_create($opts) ) ) {
			$response = '';
			while($row = fgets($fp)) { $response .= trim($row) . "\n"; }
			$this->debug("&lt $response");
			$response = json_decode($response,true);
		} else {
			$this->debug('Error: unable to connect to wallet');
			throw new Exception("Unable to connect to wallet.");
		}
		
		if( $response['id'] != 2 ) {
			$this->debug('Error: Incorrect response id from jsonRPC call');
			//throw new Exception('Incorrect response id (request id: '.$currentId.', response id: '.$response['id'].')');
		}
	
		if( !is_null($response['error']) ) {
			$this->debug('Error ' . $response['error']['code'] . ' : ' . $response['error']['message'] );
			$response['result'] = $response['error'];
		}
			
		return $response['result'];

	}

	public function debug($msg) {
		if( !$this->debug ) { return; }
		print "<pre style='margin:0'>DEBUG: "; print_r($msg); print '</pre>';
	}	
	
}
