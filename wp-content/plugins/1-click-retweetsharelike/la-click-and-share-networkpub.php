<?php

define('LACANDSNW_YOU_HAVE_NOT_ADDED_ANY_API_KEY',      __('You have not added any API Key'));
define('LACANDSNW_API_KEY_ADDED',        				__('API Key has been added successfully'));
define('LACANDSNW_ERROR_LOADING_API_KEYS',        		__('Error occured while trying to load the API Keys. Please try again later'));
define('LACANDSNW_CURRENTLY_PUBLISHING',        		__('You are currently Publishing your Blog to'));
define('LACANDSNW_SOCIAL_NETWORKS',        				__('Networks'));
define('LACANDSNW_SOCIAL_NETWORK',        				__('Network'));
define('LACANDSNW_PLUGIN_NAME',        					__('cs'));
define('LACANDSNW_PLUGIN_VERSION', 						'4.0.0');


function lacandsnw_networkping($id) {
	if(!$id) {
		return FALSE;
	}
	$options = get_option(LAECHONW_WIDGET_NAME_INTERNAL);
	if(empty($options['lacandsnw_id']) or empty($options['api_key'])) {
		return;
	}
	$link = 'http://www.linksalpha.com/a/ping?id='.$options['lacandsnw_id'];
	$response_full = lacandsnw_networkpub_http($link);
	return;
}

function lacandsnw_convert($id) {
	if(!$id) {
		return;
	}
	$options = get_option(LAECHONW_WIDGET_NAME_INTERNAL);
	if(!empty($options['id_2'])) {
		return;
	}
	if(empty($options['lacandsnw_id']) or empty($options['api_key'])) {
		return;
	}
	// Build Params
	$link = 'http://www.linksalpha.com/a/networkpubconvert';
	$params = array('id'=>$options['lacandsnw_id'],
					'api_key'=>$options['api_key'],
					'plugin'=>LACANDSNW_PLUGIN_NAME,
					);
	//HTTP Call
	$response_full = lacandsnw_networkpub_http_post($link, $params);
	$response_code = $response_full[0];
	if ($response_code != 200) {
		return;
	}
	$response = lacandsnw_networkpub_json_decode($response_full[1]);
	if ($response->errorCode > 0) {
		return;
	}
	//Update options
	$options['id_2'] = $response->results;
	//Save
	update_option(LAECHONW_WIDGET_NAME_INTERNAL, $options);
	return;
}


function lacandsnw_post($post_id) {
	//Network keys
	$options = get_option(LAECHONW_WIDGET_NAME_INTERNAL);
	if (empty($options['api_key']) or empty($options['id_2'])) {
		return;
	}
	$id = $options['id_2'];
	$api_key = $options['api_key'];
	//Post Published?
	$post_data = get_post( $post_id, ARRAY_A );
	if(in_array($post_data['post_status'], array('future', 'publish'))) {
		//Post data: id, content and title
		$post_title = $post_data['post_title'];
		$post_content = $post_data['post_content'];
		//Post data: Permalink
		$post_link = get_permalink($post_id);
		//Post data: Categories
		$post_categories_array = array();
		$post_categories_data = get_the_category( $post_id );
		foreach($post_categories_data as $category) {
			$post_categories_array[] = $category->cat_name;
		}
		$post_categories = implode(",", $post_categories_array);
		//Post tags
		$post_tags_array = array();
		$post_tags_data = wp_get_post_tags( $post_id );
		foreach($post_tags_data as $tag) {
			$post_tags_array[] = $tag->name;
		}
		$post_tags = implode(",", $post_tags_array);
		//Post Geo
		if(function_exists('get_wpgeo_latitude')) {
			if(get_wpgeo_latitude( $post_id ) and get_wpgeo_longitude( $post_id )) {
				$post_geotag = get_wpgeo_latitude( $post_id ).' '.get_wpgeo_longitude( $post_id );
			}
		}
		if(!isset($post_geotag)) {
			$post_geotag = '';
		}
		// Build Params
		$link = 'http://www.linksalpha.com/a/networkpubpost';
		$params = array('id'=>$id,
						'api_key'=>$api_key,
						'post_id'=>$post_id,
						'post_link'=>$post_link,
						'post_title'=>$post_title,
						'post_content'=>$post_content,
						'plugin'=>LACANDSNW_PLUGIN_NAME,
						'plugin_version'=>lacandsnw_version(),
						'post_categories'=>$post_categories,
						'post_tags'=>$post_tags,
						'post_geotag'=>$post_geotag
						);
		//Featured Image
		$post_image = lacandsnw_thumbnail_link( $post_id );
		if($post_image) {
			$params['post_image'] = $post_image;
		}
		//HTTP Call
		$response_full = lacandsnw_networkpub_http_post($link,$params);
	}
	return;
}


function lacandsnw_networkpub_add($api_key) {
	if (!$api_key) {
		$errdesc = lacandsnw_error_msgs('invalid key');
		echo $errdesc;
		return;
	}
	$url = get_bloginfo('url');
	if (!$url) {
		$errdesc = lacandsnw_error_msgs('invalid url');
		echo $errdesc;
		return;
	}
	$desc = get_bloginfo('description');
	$options = get_option(LAECHONW_WIDGET_NAME_INTERNAL);
	if(!empty($options['lacandsnw_id'])) {
		$id = $options['lacandsnw_id'];
	} elseif (!empty($options['id_2'])) {
		$id = $options['id_2'];
	} else {
		$id = '';
	}
	
	$url_parsed = parse_url($url);
	$url_host = $url_parsed['host'];
	if( substr_count($url, 'localhost') or strpos($url_host, '192.168.') === 0 or strpos($url_host, '127.0.0') === 0 or (strpos($url_host, '172.') === 0 and (int)substr($url_host, 4, 2) > 15 and (int)substr($url_host, 4, 2) < 32 ) or strpos($url_host, '10.') === 0 ) {
		$errdesc = lacandsnw_error_msgs('localhost url');
		echo $errdesc;
		return FALSE;
	}
	$link   = 'http://www.linksalpha.com/a/networkpubaddone';
	// Build Params
	$params = array('url'=>urlencode($url),
					'key'=>$api_key,
					'plugin'=>LACANDSNW_PLUGIN_NAME,
					'id'=>$id);
	//HTTP Call
	$response_full = lacandsnw_networkpub_http_post($link,$params);
	$response_code = $response_full[0];
	if ($response_code != 200) {
		$errdesc = lacandsnw_error_msgs($response_full[1]);
		echo $errdesc;
		return FALSE;
	}
	$response = lacandsnw_networkpub_json_decode($response_full[1]);
	if ($response->errorCode > 0) {
		$errdesc = lacandsnw_error_msgs($response->errorMessage);
		echo $errdesc;
		return FALSE;
	}
	//Update options - Site id
	$options['id_2'] = $response->results->id;
	//Update options - Network Keys
	if(empty($options['api_key'])) {
		$options['api_key'] = $response->results->api_key;	
	} else {
		$option_api_key_array = explode(',', $options['api_key']);
		$option_api_key_new = $response->results->api_key;
		$option_api_key_new_array = explode(',', $option_api_key_new);
		foreach($option_api_key_new_array as $key=>$val) {
			if(!in_array($val, $option_api_key_array)) {
				$options['api_key'] = $options['api_key'].','.$val;
			}
		}
	}
	//Save
	update_option(LAECHONW_WIDGET_NAME_INTERNAL, $options);
	//Return
	echo '<div class="updated fade" style="width:94%;margin-left:5px;padding:5px;text-align:center">API Key has been added successfully</div>';
	return;
}


function lacandsnw_networkpub_load() {
	$options = get_option(LAECHONW_WIDGET_NAME_INTERNAL);
	if (empty($options['api_key'])) {		
		$html = '<div class="msg_error">'.LACANDSNW_YOU_HAVE_NOT_ADDED_ANY_API_KEY.'</div>';
		echo $html;
		return;
	}
	$link = 'http://www.linksalpha.com/a/networkpubget';
	$body = array('key'=>$options['api_key'], 'version'=>2);	
	$response_full = lacandsnw_networkpub_http_post($link, $body);
	$response_code = $response_full[0];
	if ($response_code != 200) {
		$errdeschtml = lacandsnw_error_msgs($response_full[1]); 
		echo $errdeschtml;
		return;		
	}
	$response = lacandsnw_networkpub_json_decode($response_full[1]);
	if($response->errorCode > 0) {
		$html = '<div class="msg_error">'.LACANDSNW_ERROR_LOADING_API_KEYS.'.</div>';
		echo $html;
		return;
	}
	if(count($response->results_deleted)) {
		$option_api_key_array = explode(',', $options['api_key']);
		foreach($response->results_deleted as $row) {
			if(in_array($row, $option_api_key_array)) {
				$pos = $option_api_key_array[$row];
				unset($option_api_key_array[$pos]);
			}
		}
		$api_key = implode(",", $option_api_key_array);
		$options['api_key'] = $api_key;
		update_option(LAECHONW_WIDGET_NAME_INTERNAL, $options);
	}
	if(!count($response->results)) {
		return '<div class="msg_error">You have not added an API Key</div>';
	}
	if(count($response->results) == 1) {
		$html = '<div style="padding:0px 10px 10px 10px;">'.LACANDSNW_CURRENTLY_PUBLISHING.'&nbsp;'.count($response->results).'&nbsp;'.LACANDSNW_SOCIAL_NETWORK.'</div>';	
	} else {
		$html = '<div style="padding:0px 10px 10px 10px;">'.LACANDSNW_CURRENTLY_PUBLISHING.'&nbsp;'.count($response->results).'&nbsp;'.LACANDSNW_SOCIAL_NETWORKS.'</div>';
	}
	$html .= '<table class="networkpub_added"><tr><th>'.__('Network').'</th><th>'.__('Option').'</th><th>'.__('Remove').'</th></tr>';
	$i = 1;
	foreach($response->results as $row) {
		$html .= '<tr id="r_key_'.$row->api_key.'">';
		if($i&1) {
			$html .= '<td>';
		} else {
			$html .= '<td style="background-color:#F7F7F7;">';
		}
		$html .= '<a target="_blank" href="'.$row->profile_url.'">'.$row->name.'</a></td>';
		if($i&1) {
			$html .= '<td style="text-align:center;">';
		} else {
			$html .= '<td style="text-align:center;background-color:#F7F7F7;">';
		}
		$html .= '<a href="http://www.linksalpha.com/a/networkpuboptions?api_key='.$row->api_key.'&id='.$options['id_2'].'&version='.lacandsnw_version().'&KeepThis=true&TB_iframe=true&height=465&width=650" title="Publish Options" class="thickbox" type="button" />'.__('Options').'</a></td>';
		if($i&1) {
			$html .= '<td style="text-align:center;">';
		} else {
			$html .= '<td style="text-align:center;background-color:#F7F7F7;">';
		}
		$html .= '<a href="#" id="key_'.$row->api_key.'" class="lanetworkpubre">'.__('Remove').'</a></td>';
		$html .= '</tr>';
		$i++;
	}
	$html .= '</table>';
	echo $html;
	return;
}


function lacandsnw_networkpub_ajax() {
	if(!empty($_POST['type'])) {
		if(in_array($_POST['type'],array('remove','load'))) {
			if($_POST['type']=='remove') {
				lacandsnw_networkpub_remove($_POST['key']);				
			}					
			if($_POST['type']=='load') {
				lacandsnw_networkpub_load();				
			}			
		}		
	}
}


function lacandsnw_networkpub_remove() {
	$options = get_option(LAECHONW_WIDGET_NAME_INTERNAL);
	if (!empty($_POST['lacandsnw_networkpub_key'])) {
		$key_full = $_POST['lacandsnw_networkpub_key'];
		$key_only = trim(substr($key_full, 4));
		$link = 'http://www.linksalpha.com/a/networkpubremove';
		$body = array('id'=>$options['id_2'], 'key'=>$key_only);
		$response_full = lacandsnw_networkpub_http_post($link, $body);
		$response_code = $response_full[0];
		if ($response_code != 200) {
			$errdesc = lacandsnw_error_msgs($response_full[1]); 
			echo $errdesc;		
			return;
		}
		$api_key = $options['api_key'];
		$api_key_array = explode(',', $api_key);
		$loc = array_search($key_only, $api_key_array, True);
		if($loc !== FALSE) {
			unset($api_key_array[$loc]);
		}
		$api_key = implode(",", $api_key_array);
		$options['api_key'] = $api_key;
		update_option(LAECHONW_WIDGET_NAME_INTERNAL, $options);
		echo $key_full;
		return;
	}
}


function lacandsnw_networkpub_json_decode($str) {
	if (function_exists("json_decode")) {
	    return json_decode($str);
	} else {
		if (!class_exists('Services_JSON')) {
			require_once("JSON.php");
		}
	    $json = new Services_JSON();
	    return $json->decode($str);
	}
}


function lacandsnw_networkpub_http($link) {
	if (!$link) {
		return array(500, 'invalid url');
	}
	if( !class_exists( 'WP_Http' ) ) {
		include_once( ABSPATH . WPINC. '/class-http.php' );
	}
	if (class_exists('WP_Http')) {
		$request = new WP_Http;
		$headers = array( 'Agent' => LAECHONW_WIDGET_NAME.' - '.get_bloginfo('url') );
		$response_full = $request->request( $link );
		$response_code = $response_full['response']['code'];
		if ($response_code === 200) {
			$response = $response_full['body'];
			return array($response_code, $response);
		}
		$response_msg = $response_full['response']['message'];
		return array($response_code, $response_msg);
	}
	require_once(ABSPATH.WPINC.'/class-snoopy.php');
	$snoop = new Snoopy;
	$snoop->agent = LAECHONW_WIDGET_NAME.' - '.get_bloginfo('url');
	if($snoop->fetchtext($link)){
		if (strpos($snoop->response_code, '200')) {
			$response = $snoop->results;
			return array(200, $response);
		}
	}
	return array(500, 'internal error');
}


function lacandsnw_networkpub_http_post($link, $body) {
	if (!$link) {
		return array(500, 'invalid url');
	}
	if( !class_exists( 'WP_Http' ) ) {
		include_once( ABSPATH . WPINC. '/class-http.php' );
	}
	if (class_exists('WP_Http')) {
		$request = new WP_Http;
		$headers = array( 'Agent' => LAECHONW_WIDGET_NAME.' - '.get_bloginfo('url') );
		$response_full = $request->request( $link, array( 'method' => 'POST', 'body' => $body, 'headers'=>$headers) );
		if(isset($response_full->errors)) {
			return array(500, 'internal error');
		}
		$response_code = $response_full['response']['code'];
		if ($response_code === 200) {
			$response = $response_full['body'];
			return array($response_code, $response);
		}
		$response_msg = $response_full['response']['message'];
		return array($response_code, $response_msg);
	}
	require_once(ABSPATH.WPINC.'/class-snoopy.php');
	$snoop = new Snoopy;
	$snoop->agent = LAECHONW_WIDGET_NAME.' - '.get_bloginfo('url');
	if($snoop->submit($link, $body)){
		if (strpos($snoop->response_code, '200')) {
			$response = $snoop->results;
			return array(200, $response);
		} 
	}	
	return array(500, 'internal error');
}


function lacandsnw_error_msgs($errMsg) {

	$arr_errCodes  = explode(";", $errMsg);
	$errCodesCount = count($arr_errCodes);

	switch (trim($arr_errCodes[0])) {
	
		case 'internal error':
			$html = '<div class="msg_error">	
					<b>'.__('Please try again. Wait for sometime and try again').'</b>&nbsp;'.__('There was an unknown error. Please try again.
					You can also email us at').'&nbsp;<a href="mailto:post@linksalpha.com">post@linksalpha.com</a>&nbsp;'.__('with error description (your blog URL and the error)').'.
				</div>';
			return $html;		
			break;
	
		case 'invalid url':
			$html  = '<div class="msg_error"><b>'.__('Your blog URL is invalid').':</b>'.$arr_errCodes[$errCodesCount-1];			
			if($errCodesCount == 3) {
				$html .= '.&nbsp;'.__('Error Code').'&nbsp;='.$arr_errCodes[$errCodesCount-2];
			}			
			$html .= '<div>
					'.__('You can also').'&nbsp;<a href="http://www.linksalpha.com/user/siteadd" target="_blank">'.__('Click here').'</a>'.__(' to enter blog URL on LinksAlpha manually.
					  Also ensure that in ').'<b>'.__('Settings').'->'.__('General').'->"'.__('Blog address (URL)').'"</b> '.__('the URL is filled-in correctly').'.</div> 
					  <div>'.__('If you still face issues then email us at').'&nbsp;<a href="mailto:post@linksalpha.com">post@linksalpha.com</a>&nbsp;'.__('with error description').'.</div>';			
			return $html;
			break;
		
		case 'localhost url':
			$html  = '<div class="msg_error"><div><b>'.__('Website/Blog inaccessible').'</b></div>';
			$html .= '<div>'.__('You are trying to use the plugin on ').'<b>localhost</b> '.__('or behind a').' <b>'.__('firewall').'</b>, '.__('which is not supported. Please install the plugin on a Wordpress blog on a live server').'.</div>
				  </div>';
			return $html;
			break;
			
		case 'remote url error':		
			$html  = '<div class="msg_error"><div><b>'.__('Remote URL error').': </b>'.$arr_errCodes[$errCodesCount-1];
			if($errCodesCount == 3) {
				$html .= '. '.__('Error Code').'&nbsp;='.$arr_errCodes[$errCodesCount-2];
			}
			$html .= '</div>
					<div>
						<b>'.__('Description:').'</b>
						<b>'.__('Please try again').'. </b> '.__('Your site either did not respond (it is extremely slow) or it is not operational').'.
					</div>
					<div>
						'.__('You can also').' <a href="http://www.linksalpha.com/user/siteadd" target="_blank">'.__('Click here').'</a> '.__('to enter blog URL on LinksAlpha manually').'. 
						'.__('Also ensure that in').' <b>'.__('Settings').'->'.__('General').'->"'.__('Blog address (URL)').'"</b> '.__('the URL is filled-in correctly').'. 
					</div>
					<div>
						'.__('If you still face issues then email us at').' <a href="mailto:post@linksalpha.com">post@linksalpha.com</a> '.__('with error description').'.
					</div>
				</div>';
			return $html;		
			break;
			
		case 'feed parsing error':
			$html  = '<div class="msg_error"><div><b>'.__('Feed parsing error').': </b>'.$arr_errCodes[$errCodesCount-1];			
			if($errCodesCount == 3) {
				$html .= '. '.__('Error Code').'=&nbsp;'.$arr_errCodes[$errCodesCount-2];
			}
			$html .= '	</div>
					<div>
						<b>'.__('Description').': </b>
						'.__('Your RSS feed has errors. Pls go to').' <a href=http://beta.feedvalidator.org/ target="_blank">href=http://beta.feedvalidator.org/</a> '.__('to validate your RSS feed').'.
					</div>
					<div>
						'.__('If it comes out to be correct, try again and email as at ').'<a href="mailto:post@linksalpha.com">post@linksalpha.com</a> '.__('with your blog URL and error description').'.
					</div>
				</div>';			
			return $html;		
			break;

		case 'feed not found':
			$html ='<div class="msg_error">
					<div>
						<b>'.__('We could not find feed URL for your blog').'.</b>
					</div>
					<div>
						<a href="http://www.linksalpha.com/user/siteadd" target="_blank">'.__('Click here').'</a> '.__('to enter feed URL on LinksAlpha manually').'.
						'.__('Also ensure that in ').'<b>'.__('Settings').'->'.__('General').'->"'.__('Blog address (URL)').'"</b> '.__('the URL is filled-in correctly').'.
					</div>
					<div>
						'.__('If you still face issues then email us at ').'<a href="mailto:post@linksalpha.com">post@linksalpha.com</a> '.__('with error description').'
					</div>
				</div>';
			return $html;		
			break;
			
		case 'invalid key':
			$html = '<div class="msg_error">
					<div>
						<b>'.__('Invalid Key').': </b>'.__('the key that you entered is incorrect. Please try again').'.
					</div>
					<div>
						<span style="color:#d12424;">'.__('Getting Errors').'?</span> '.__('See help page').' <a href="http://help.linksalpha.com/errors" target="_blank">'.__('here').'</a>
					</div>
					<div>
						'.__('Or').', <a href="http://www.linksalpha.com/user/siteadd" target="_blank">'.__('Click here').'</a> '.__('to enter your blog URL on LinksAlpha manually').'.
						'.__('If you still face issues then email us at ').'<a href="mailto:post@linksalpha.com">post@linksalpha.com</a> '.__('with error description').'
					</div>
				</div>';			
			return $html;
			break;
			
		case 'subscription upgrade required':
			$html = '<div class="msg_error">
					<b>'.__('Upgrade account').'.</b> '.__('Please').' <a href="http://www.linksalpha.com/account" target="_blank">'.__('upgrade your subscription').'</a> '.__('to add more networks').'.
				</div>';
			return $html;
			break;
			
		default:
			$html = '<div class="msg_error">	
					'.__('Sorry we are undergoing maintenance at this time - this happens very rarely but is critical to ensure continued availability. We apologize for the inconvenience.').'
					'.__('This can take upto 2 hours maximum. Please try again after sometime and it is guaranteed to work').'.
					'.__('You can also email us at').' <a href="mailto:post@linksalpha.com">post@linksalpha.com</a> '.__('if issue persists. Thanks for your understanding').'
				</div>';
			return $html;		
			break;			
	}	
}


function lacandsnw_pushpresscheck() {
	$active_plugins = get_option('active_plugins');
	$pushpress_plugin = 'pushpress/pushpress.php';
	$this_plugin_key = array_search($pushpress_plugin, $active_plugins);
	if ($this_plugin_key) {
		$options = get_option(LAECHONW_WIDGET_NAME_INTERNAL);
		if(array_key_exists('lacandsnw_id', $options)) {
			if($options['lacandsnw_id']) {
				$link = 'http://www.linksalpha.com/a/pushpress';
				$body = array('id'=>$options['lacandsnw_id']);
				$response_full = lacandsnw_networkpub_http_post($link, $body);
				$response_code = $response_full[0];	
			}	
		}
	}
}


function lacandsnw_networkpubcheck() {
	$active_plugins = get_option('active_plugins');
	$pushpress_plugin = 'network-publisher/networkpub.php';
	$this_plugin_key = array_search($pushpress_plugin, $active_plugins);
	if ($this_plugin_key) {
		return True;
	}
	return False;
}


function lacandsnw_postbox_url() {
	if ( version_compare($wp_version, '3.0.0', '<') ) {
		$admin_url = get_bloginfo('url').'/wp-admin/edit.php?page='.LACANDSNW_WIDGET_NAME_POSTBOX_INTERNAL;	
	} else {
		$admin_url = get_admin_url().'/edit.php?page='.LACANDSNW_WIDGET_NAME_POSTBOX_INTERNAL;
	}
	return $admin_url;
}


function lacandsnw_postbox(){
	$html  = '<div class="wrap"><div class="icon32" id="lacands_laicon"><br /></div><h2>'.LAECHONW_WIDGET_NAME.' - '.LACANDSNW_WIDGET_NAME_POSTBOX.'</h2></div>';
	$html .= '<iframe id="networkpub_postbox" src="http://www.linksalpha.com/post?source=wordpress&sourcelink='.urlencode(lacandsnw_postbox_url()).'#'.urlencode(lacandsnw_postbox_url()).'" width="1050px;" height="700px;" scrolling="no" style="border:none !important;" frameBorder="0"></iframe>';
	$html .= '<div style="padding:10px 10px 6px 10px;background-color:#FFFFFF;margin-bottom:15px;margin-top:0px;border:1px solid #F0F0F0;width:1005px;">
			<div style="width:130px;float:left;font-weight:bold;">
				'.__('Share this Plugin').'
			</div>
			<div style="width:600px">
				<iframe style="height:25px !important; border:none !important; overflow:hidden !important; width:380px !important;" frameborder="0" scrolling="no" allowTransparency="true" src="http://www.linksalpha.com/social?link=http%253A%252F%252Fdev30.linksalpha.com%252F%253Fp%253D8&fc=333333&fs=lucida+grande&fblname=like&fblref=fb&fbllang=en_US&twitterlang=en&twittermention=vivekpuri&twitterrelated1=vivekpuri&twitterrelated12=twitfn&linkedinbutton=show"></iframe>
			</div>
		  </div>';
	echo $html;
	return;
}


function lacandsnw_thumbnail_link( $post_id ) {
	if(!function_exists('get_post_thumbnail_id')) {
		return False;
	}
	$src = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'full');
	if($src) {
		$src = $src[0];
		return $src;	
	} else {
		return False;
	}
}


function lacandsnw_version() {
	return LACANDSNW_PLUGIN_VERSION;
}


?>
