<?php  

function lacands_get_plugin_dir() {
	if ( version_compare($wp_version, '2.8', '<') ) {
		$path = dirname(plugin_basename(__FILE__));
		if ( $path == '.' )
		$path = '';
		$plugin_path = trailingslashit( plugins_url( $path ) );
	} 
	else {
		$plugin_path = trailingslashit( plugins_url( '', _FILE_) );
	}	
	return $plugin_path;
}

function lacands_http_post($link, $body) {
	if (!$link) {
		return array(500, 'Invalid Link');
	}
	//Try using WP_Http
	if( !class_exists( 'WP_Http' ) ) {
		include_once( ABSPATH . WPINC. '/class-http.php' );
	}
	if (class_exists('WP_Http')) {
		$request = new WP_Http;
		$response_full = $request->request( $link, array( 'method' => 'POST', 'body' => $body, 'headers'=>$headers) );
		if(isset($response_full->errors)) {			
			return array(500, 'Unknown Error');				
		}
		$response_code = $response_full['response']['code'];
			
		if ($response_code === 200) {
			$response = $response_full['body'];
			return array($response_code, $response);
		}
		$response_msg = $response_full['response']['message'];
		return array($response_code, $response_msg);
	}
	//Try using snoopy
	require_once(ABSPATH.WPINC.'/class-snoopy.php');
	$snoop = new Snoopy;
	if($snoop->submit($link, $body)){
		if (strpos($snoop->response_code, '200')) {
			$response = $snoop->results;
			return array(200, $response);
		}
	}
	return array(500, 'internal error');
}

function lacands_http_process($response_full) {
	if ($response_full[0] != 200) {
		return FALSE;
	}
	$response = lacands_json_decode($response_full[1]);
	if($response->errorCode) {
		return FALSE;
	}
	return $response->results;
}

function lacands_json_decode($str) {
	if (function_exists("json_decode")) {
		return json_decode($str);
	} 
	else {
		if (!class_exists('Services_JSON')) {
		require_once("JSON.php");
		}
	
		$json = new Services_JSON();
	
		return $json->decode($str);
	}
}

?>