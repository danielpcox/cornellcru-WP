<?php
/*
Plugin Name: Tweet This
Version: 1.8
Description: Popular Twitter plugin inserts "Tweet This" links so your readers can share posts with one click. Automatically tweets new posts via OAuth.
Contributors: richardxthripp
Author: Richard X. Thripp
Author URI: http://richardxthripp.thripp.com/
Plugin URI: http://richardxthripp.thripp.com/tweet-this/
Donate Link: http://richardxthripp.thripp.com/donate/
Update Server: http://richardxthripp.thripp.com/files/plugins/tweet-this.zip
Disclaimer: No warranty is provided. PHP 5 and Curl are required.
Requires at least: 2.7
Tested up to: 3.0.1
Stable tag: 1.8
License: GPLv2
Text Domain: tweet-this
Domain Path: /languages
*/


/**
 * Tweet This is a plugin for WordPress 2.7 - 3.0.1 and WordPress MU.
 * Copyright 2008 - 2010  Richard X. Thripp  (email: richardxthripp@thripp.com)
 * Released under Version 2 of the GNU General Public License as published by
 * the Free Software Foundation, or, at your option, any later version.
 */


/**
 * This is Tweet This v1.8, build 034, released 2010-10-02.
 */


/**
 * Definitions.
 */
if(!defined('WP_CONTENT_URL'))
	define('WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
if(!defined('WP_CONTENT_DIR'))
	define('WP_CONTENT_DIR', ABSPATH . 'wp-content');
if(!defined('WP_PLUGIN_URL'))
	define('WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins');
if(!defined('WP_PLUGIN_DIR'))
	define('WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins');
if(!defined('WPMU_PLUGIN_URL'))
	define('WPMU_PLUGIN_URL', WP_CONTENT_URL. '/mu-plugins');
if(!defined('WPMU_PLUGIN_DIR'))
	define('WPMU_PLUGIN_DIR', WP_CONTENT_DIR . '/mu-plugins');


/**
 * Same as plugin_basename($file), but we can't use that because:
 * 1. Plugin_basename is defined too late in WP 1.5.
 * 2. Wrapping plugin_basename in if(!function_exists) fails.
 * @package tweet-this
 * @since 1.8
 */
function tt_plugin_basename($file) {
	$file = str_replace('\\', '/', $file);
	$file = preg_replace('|/+|', '/', $file);
	$plugin_dir = str_replace('\\', '/', WP_PLUGIN_DIR);
	$plugin_dir = preg_replace('|/+|', '/', $plugin_dir);
	$mu_plugin_dir = str_replace('\\', '/', WPMU_PLUGIN_DIR);
	$mu_plugin_dir = preg_replace('|/+|', '/', $mu_plugin_dir);
	$file = preg_replace('#^' . preg_quote($plugin_dir, '#') . '/|^' .
		preg_quote($mu_plugin_dir, '#') . '/#', '', $file);
	$file = trim($file, '/');
	return $file;
}


/**
 * Definitions, continued.
 */
define('TT_DIR', str_replace(array('/' . basename(__FILE__), '.php'),
	array('', ''), tt_plugin_basename(__FILE__)));
define('TT_ABSPATH', WP_PLUGIN_DIR . '/' . TT_DIR . '/');
define('TT_URLPATH', WP_PLUGIN_URL . '/' . TT_DIR . '/');
define('TT_VERSION', '1.8');
define('TT_BUILD', '034');
define('TT_DB_VERSION', '1.0');
define('TT_CONFIG', TT_ABSPATH . 'tt-config.php');
define('TT_OPTIONS', TT_ABSPATH . 'lib/tt-options.php');
define('TT_OAUTH', TT_ABSPATH . 'lib/twitteroauth.php');
define('TT_OAUTH_BASE', TT_ABSPATH . 'lib/OAuth.php');
define('TT_OAUTH_SPECIAL', TT_ABSPATH . 'lib/twitteroauth-special.php');
define('TT_OAUTH_BASE_SPECIAL', TT_ABSPATH . 'lib/OAuth-special.php');
define('TT_JSON', TT_ABSPATH . 'lib/wp/class-json.php');
define('TT_WP_JSON', ABSPATH . WPINC . '/class-json.php');
define('TT_JQUERY_ABSPATH', TT_ABSPATH . 'lib/js/jquery-1.4.2.min.js');
define('TT_JQUERY_URLPATH', TT_URLPATH . 'lib/js/jquery-1.4.2.min.js');
define('TT_API_POST_STATUS', 'http://twitter.com/statuses/update.json');
define('TT_ADJIX_LEN', 21);
define('TT_B2LME_LEN', 20);
define('TT_BITLY_LEN', 20);
define('TT_CUSTOM_LEN', 26);
define('TT_ISGD_LEN', 18);
define('TT_LOCAL_LEN', strlen(str_replace('https://', 'http://',
	get_bloginfo('url'))) + 8);
define('TT_METAMARK_LEN', 20);
define('TT_SNIPURL_LEN', 25);
define('TT_SUPR_LEN', 19);
define('TT_TCO_LEN', 20);
define('TT_TH8US_LEN', 19);
define('TT_TINYURL_LEN', 26);
define('TT_TWEETBURNER_LEN', 22);
if(version_compare($GLOBALS['wp_version'], '2.0', '<')) {
	global $wpdb, $table_prefix;
	$wpdb->rxtt = $table_prefix . 'tweet_this_scheduled';
	define('TT_FILE_LOC', '?page=' . TT_DIR . '/' . basename(__FILE__));}
else {	global $wpdb; $wpdb->rxtt = $wpdb->prefix . 'tweet_this_scheduled';
	define('TT_FILE_LOC', '?page=' . basename(__FILE__));}
if(file_exists(TT_CONFIG)) require_once(TT_CONFIG);


/**
 * Do not edit these here, but rather, in the tt-config.php file.
 */
if(!defined('TT_PREFIX'))
	define('TT_PREFIX', '<div class="tweetthis" style="text-align:' .
	tt_option('tt_alignment') . ';"><p>');
if(!defined('TT_SUFFIX'))
	define('TT_SUFFIX', '</p></div>');
if(!defined('TT_SEPARATOR'))
	define('TT_SEPARATOR', ' ');
if(!defined('TT_OVERRIDE_OPTIONS'))
	define('TT_OVERRIDE_OPTIONS', true);
if(!defined('TT_HIDE_MENU'))
	define('TT_HIDE_MENU', false);
if(!defined('TT_SPECIAL_OPTIONS'))
	define('TT_SPECIAL_OPTIONS', '');


/**
 * JSON functions.
 * @package tweet-this
 * @since 1.7
 */
if(!class_exists('Services_JSON')) {
	if(version_compare($GLOBALS['wp_version'], '2.9', '<'))
		require_once(TT_JSON);
	else require_once(TT_WP_JSON);}
if(!function_exists('json_encode')) {
	function json_encode($data) {
		$json = new Services_JSON();
		return($json->encode($data));}}
if(!function_exists('json_decode')) {
	function json_decode($data) {
		$json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
		return($json->decode($data));}}


/**
 * Backward compatible version of mb_substr uses substr on PHP 4.0.0 - 4.0.5.
 * @package tweet-this
 * @since 1.8
 */
function tt_mb_substr($str = '', $start = 0, $length = 70) {
	if(function_exists('mb_substr'))
		return mb_substr($str, $start, $length);
	else return substr($str, $start, $length);
}


/**
 * Access to serialized Tweet This options with mapping.
 * @package tweet-this
 * @since 1.3
 */
function tt_option($key) {
	if(defined('TT_OVERRIDE_OPTIONS') && defined('TT_SPECIAL_OPTIONS') &&
		TT_OVERRIDE_OPTIONS == true && TT_SPECIAL_OPTIONS != '' &&
		unserialize(TT_SPECIAL_OPTIONS) != false)
			$options = unserialize(TT_SPECIAL_OPTIONS);
	else $options = get_option('tweet_this_settings');
	if($key == 'nw') {
		if($options['tt_new_window'] == 'true')
			return 'target="_blank" ';
		else return '';}
	elseif($key == 'nf') {
		if($options['tt_nofollow'] == 'true')
			return 'rel="nofollow" ';
		else return '';}
	elseif($key == 'tt_alignment') {
		if($options['tt_alignment'] == '' ||
			($options['tt_alignment'] != 'left' &&
			$options['tt_alignment'] != 'right' &&
			$options['tt_alignment'] != 'center'))
				return 'left';
		else return $options['tt_alignment'];}
	elseif($key == 'tt_twitter')
		return stripslashes(htmlentities($options['tt_auto_display']));
	elseif($key == 'tt_twitter_text')
		return stripslashes(htmlentities($options['tt_tweet_text']));
	elseif($key == 'tt_twitter_link_text')
		return stripslashes(htmlentities($options['tt_link_text']));
	elseif($key == 'tt_twitter_title_text')
		return stripslashes(htmlentities($options['tt_title_text']));
	elseif($key == 'tt_css' && ($options['tt_css'] ==
		'img.[IMG_CLASS]{border:0;}' || $options['tt_css'] ==
		'a.[LINK_CLASS]{text-decoration:none;border:0;}'))
			return	'img.[IMG_CLASS]{border:0;margin:0 0 0 2px ' .
				'!important;}';
	elseif($key == 'tt_textbox_size' && strlen($options[$key]) > 4)
		return '60';
	elseif($key == 'tt_service_order' && strlen($options[$key]) < 4)
		return	'twitter, plurk, bebo, buzz, delicious, digg, ' .
			'diigo, facebook, ff, gbuzz, gmail, linkedin, ' .
			'mixx, myspace, ping, reddit, slashdot, squidoo, ' .
			'su, technorati';
	// Voodoo to convert pre-1.8 icon paths to 1.8 paths on the fly.
	elseif(substr($key, -5) == '_icon' && (substr($options[$key], 0, 3)
		== 'tt-' || substr($options[$key], 0, 6) == 'de/tt-'))
		return str_replace('tt-', 'en/', substr($options[$key], 0,
		3)) . substr($key, 3, -5) . '/' . str_replace('-de.png',
		'.png', $options[$key]);
	else return stripslashes(htmlentities($options[$key]));
}


/**
 * Same as tt_option($key).
 * @package tweet-this
 * @since 1.7
 */
function get_tt_option($key) {
	return tt_option($key);
}


/**
 * Reads an external URL by file_get_contents($url) or Curl.
 * @package tweet-this
 * @since 1.3.5
 */
function tt_read_file($url) {
	if(ini_get('allow_url_fopen') == 1 ||
		strtolower(ini_get('allow_url_fopen')) == 'on') {
		$file = @file_get_contents($url);
		if($file == false) {
			$handle = @fopen($url, 'r');
			$file = @fread($handle, 4096);
			@fclose($handle);}}
	else {	if(function_exists('curl_init')) {
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
			$file = curl_exec($ch);
			curl_close($ch);}}
	if($file != false && $file != '') return $file;
}


/**
 * Same as tt_read_file($url).
 * @package tweet-this
 * @since 1.7.6
 */
function get_tt_read_file($url) {
	return tt_read_file($url);
}


/**
 * Creates the shortened URL for the current post within the loop.
 * @package tweet-this
 * @since 1.1
 */
function get_tweet_this_short_url($longurl = '', $post_id = '',
	$return = true, $admin = false, $service = '', $force = false) {
	if($admin != true) {
		global $id, $post;
		if($post_id == '') $post_id = $id;
		$cached_url = get_post_meta($post_id, 'tweet_this_url', true);
		$post_status = $post->post_status;}
	if($longurl != '') $purl = str_replace('t9WGb5', '_', $longurl);
	else $purl = get_permalink();
	$purl = str_replace('https://', 'http://', $purl);
	$sn_purl = rawurlencode($purl);
	$purl = urlencode($purl);
	if($cached_url && $cached_url != 'getnew' && $admin != true)
		return $cached_url;
	elseif($post_status && $post_status != 'publish' &&
		tt_option('tt_url_service') != 'local' &&
		tt_option('tt_url_service') != '' && $force == false)
			return;
	else {	if($admin == true) {
			$get_service = tt_option('tt_admin_url_service');
			if($get_service == 'same') $get_service =
				tt_option('tt_url_service');}
		else $get_service = tt_option('tt_url_service');
		if($service != '') $u = $service;
		else $u = str_replace('snurl', 'snipurl', $get_service);
		if((ini_get('allow_url_fopen') != 1 &&
		strtolower(ini_get('allow_url_fopen')) != 'on' &&
		!function_exists('curl_init')) ||
		($u == 'adjix' && tt_option('tt_adjix_api_key') == '') ||
		($u == 'snipurl' && (!function_exists('curl_init') ||
		tt_option('tt_snipurl_username') == '' ||
		tt_option('tt_snipurl_api_key') == '')) ||
		($u == 'tweetburner' && !function_exists('curl_init')))
			$u = 'local';
		if(($u == '' || $u == 'same' || $u == 'local') &&
			$admin == true) $u = 'bit.ly';
		switch($u) {
		case 'adjix': {
			if(tt_option('tt_ad_vu') != 'false')
				$ad_vu = '&ultraShort=y';
			$url = tt_read_file('http://api.adjix.com/shrinkLink' .
				'?url=' . $purl . '&partnerID=' .
				tt_option('tt_adjix_api_key') . $ad_vu);}
			break;
		case 'b2l.me':
			$url = tt_read_file('http://b2l.me/api.php?alias=' .
				'&url=' . $purl);
			break;
		case 'bit.ly': {
			if(tt_option('tt_bitly_username') != '' &&
			tt_option('tt_bitly_api_key') != '') {
				$decoded = json_decode(tt_read_file('http://' .
				'api.bit.ly/v3/shorten?longUrl=' . $purl .
				'&login=' . tt_option('tt_bitly_username') .
				'&apiKey=' . tt_option('tt_bitly_api_key') .
				'&format=json'), 'true');
				if($decoded['status_code'] == '200')
					$url = $decoded['data']['url'];
				else $url = tt_read_file('http://bit.ly/' .
					'api?url=' . $purl);}
			else $url = tt_read_file('http://bit.ly/api?url=' .
				$purl);
			if(tt_option('tt_j_mp') == 'true')
				$url = str_replace('http://bit.ly/',
				'http://j.mp/', $url);}
			break;
		case 'is.gd':
			$url = tt_read_file('http://is.gd/api.php?longurl=' .
				$purl);
			break;
		case 'metamark':
			$url = tt_read_file('http://metamark.net/api/rest/' .
				'simple?long_url=' . $purl);
			break;
		case 'snipurl': {
			$sn_api = 'http://snipurl.com/site/getsnip';
			$sn_post = 'sniplink=' . $sn_purl . '&snipuser=' .
			tt_option('tt_snipurl_username') . '&snipapi=' .
			tt_option('tt_snipurl_api_key') . '&snipformat=simple';
			$ch = curl_init($sn_api);
			curl_setopt($ch, CURLOPT_URL, $sn_api);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $sn_post);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$url = curl_exec($ch); curl_close($ch);
			if(tt_option('tt_snipurl_domain') == 'snipr.com')
				$url = str_replace('http://snipurl.com/',
				'http://snipr.com/', $url);
			elseif(tt_option('tt_snipurl_domain') == 'snurl.com')
				$url = str_replace('http://snipurl.com/',
				'http://snurl.com/', $url);
			elseif(tt_option('tt_snipurl_domain') == 'sn.im')
				$url = str_replace('http://snipurl.com/',
				'http://sn.im/', $url);
			elseif(tt_option('tt_snipurl_domain') == 'cl.lk')
				$url = str_replace('http://snipurl.com/',
				'http://cl.lk/', $url);}
			break;
		case 'su.pr': {
			if(tt_option('tt_supr_username') != '' &&
				tt_option('tt_supr_api_key') != '')
				$supr_api = '&login=' .
				tt_option('tt_supr_username') . '&apiKey=' .
				tt_option('tt_supr_api_key');
			$url = tt_read_file('http://su.pr/api/simpleshorten?' .
				'url=' . $purl . $supr_api);}
			break;
		case 'th8.us':
			$url = tt_read_file('http://th8.us/api.php?url=' .
				$purl . '&client=Tweet+This+v' . TT_VERSION .
				'&format=simple');
			break;
		case 'tinyurl':
			$url = tt_read_file('http://tinyurl.com/' .
				'api-create.php?url=' . $purl);
			break;
		case 'tweetburner': {
			$tw_post = 'link[url]=' . $purl;
			$ch = curl_init('http://tweetburner.com/links');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $tw_post);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
			$url = curl_exec($ch); curl_close($ch);}
			break;
		case 'local':
		default:
			$url = get_bloginfo('url') . '/?p=' . $post_id;
			break;
		case 'custom':
			$url = tt_read_file(str_replace('[LONGURL]',
			$purl, tt_option('tt_custom_url_service')));
			break;}
	// Tweetburner URLs cannot be accessed with the www subdomain.
	// It's not safe to replace http:// with www. outright because if
	// the blog already has the www subdomain you will get www.www.
		if(tt_option('tt_url_www') == 'true' && $u != 'tweetburner') {
			$url =	str_replace('http://', 'www.',
				str_replace('http://www.', 'www.', $url));}
	// If short URL > 30 characters, is error, or is malformed, discard.
		if($u != 'local' && $u != 'custom' && $admin != true &&
		(strlen($url) > 30 || strtolower($url) == 'error' ||
		$url == 'www.' || $url == 'http://' || $url == ''))
				$url = get_bloginfo('url') . '/?p=' . $post_id;
		if($admin != true) {
			if($cached_url == 'getnew')
				update_post_meta($post_id, 'tweet_this_url',
				$url, 'getnew');
			else add_post_meta($post_id, 'tweet_this_url',
			$url, true);}
		if($return != false) return $url;}
}


/**
 * Echoes the above function for convenience.
 * @package tweet-this
 * @since 1.1
 */
function tweet_this_short_url($longurl = '', $post_id = '',
	$return = true, $admin = false, $service = '', $force = false) {
	echo	get_tweet_this_short_url($longurl, $post_id, $return, $admin,
		$service, $force);
}


/**
 * Gets a post by ID with backward compatibility for WP 1.5.
 * Always returns an associative array.
 * @package tweet-this
 * @since 1.7.7
 */
function tt_get_post($post_id = '') {
	if($post_id == '') {global $id; $post_id = $id;}
	$postdata = null; if(function_exists('get_post'))
		$postdata = get_post($post_id, ARRAY_A);
	else {	$postdata = get_postdata($post_id);
		$postdata['post_author'] = $postdata['Author_ID'];
		$postdata['post_date'] = $postdata['Date'];
		$postdata['post_content'] = $postdata['Content'];
		$postdata['post_excerpt'] = $postdata['Excerpt'];
		$postdata['post_title'] = $postdata['Title'];
		$postdata['post_category'] = $postdata['Category'];}
	return	$postdata;
}


/**
 * Backward compatible function to get the author nickname in the loop.
 * @package tweet-this
 * @since 1.8
 */
function tt_get_the_author($post_id = '') {
	if($post_id == '') {global $id; $post_id = $id;}
	// tt_get_post($post_id) will not work here, for unknown reasons.
	if(function_exists('get_post')) {
		$gp = get_post($post_id);
		$author_id = $gp->post_author;}
	else {	$gp = get_postdata($post_id);
		$author_id = $gp['Author_ID'];}
	if(function_exists('get_the_author_meta'))
		return get_the_author_meta('display_name', $author_id);
	else return get_the_author_nickname();
}


/**
 * Returns the first category of the current post in the loop.
 * @package tweet-this
 * @since 1.8
 */
function tt_get_the_category($post_id = '') {
	if($post_id == '') {global $id; $post_id = $id;}
	$category = null; $categories = get_the_category($post_id);
	if($categories > 0) $category = $categories[0]->cat_name;
	return $category;
}


/**
 * Backward compatible function to strip shortcodes.
 * @package tweet-this
 * @since 1.8
 */
function tt_strip_shortcodes($text) {
	if(function_exists('strip_shortcodes'))
		return strip_shortcodes($text);
	else return $text;
}


/**
 * Returns the post excerpt, truncated with "..." if it exceeds $chars.
 * @package tweet-this
 * @since 1.8
 */
function tt_get_the_excerpt($post_id = '', $chars = 70) {
	if($post_id == '') {global $id; $post_id = $id;}
	// tt_get_post($post_id) will not work here, for unknown reasons.
	if(function_exists('get_post')) {
		$gp = get_post($post_id);
		$content = $gp->post_content;
		$excerpt = $gp->post_excerpt;}
	else {	$gp = get_postdata($post_id);
		$content = $gp['Content'];
		$excerpt = $gp['Excerpt'];}
	if(trim($excerpt) != '')
		$pxcr = strip_tags(tt_strip_shortcodes($excerpt));
	else $pxcr = strip_tags(tt_strip_shortcodes($content));
	if(strlen($pxcr) > $chars)
		$pxcr = tt_mb_substr($pxcr, 0, ($chars - 3)) . '...';
	return $pxcr;
}


/**
 * Truncates the post title with "..." if we are over 140 characters.
 * @package tweet-this
 * @since 1.1
 */
function get_tweet_this_trim_title($title = '', $tweet_text = '',
	$service = 'twitter', $encode = true) {
	if($title == '') $title = get_the_title();
	if($tweet_text == '') {
		if($service == 'twitter-share')
			$tweet_text = tt_option('tt_twitter_share_text');
		else	$tweet_text = tt_option('tt_tweet_text');}
	$tweet_text = str_replace(array('[AUTHOR]', '[CATEGORY]', '[DATE]',
		'[TIME]', '[EXCERPT]', '[BLOG_TITLE]'), array('', '', '', '',
		'', ''), $tweet_text);
	$tweet_text = preg_replace("/(\040)+/", " ", $tweet_text);
	$title = @html_entity_decode($title, ENT_COMPAT, 'UTF-8');
	if($service == 'twitter-share') {
		if(tt_option('tt_twitter_share_via') == '[BLANK]')
			$via_len = 0;
		else $via_len = strlen(tt_option('tt_twitter_share_via'));
		if($via_len != 0) $via_len += 6;}
	else $via_len = 0;
	$tt_len = max((strlen($tweet_text) - 12), 1);
	if($service == 'twitter-share') $url_len = constant('TT_TCO_LEN');
	else $url_len = get_tt_url_service_len();
	$lens = $tt_len + $url_len + $via_len;
	if(strlen($title) > (140 - $lens))
		$title = tt_mb_substr($title, 0, (137 - $lens)) . '...';
	if($encode == true) $title = urlencode($title);
	return $title;
}


/**
 * Echoes the above function for convenience.
 * @package tweet-this
 * @since 1.1
 */
function tweet_this_trim_title($title = '', $tweet_text = '',
	$service = 'twitter', $encode = true) {
	echo get_tweet_this_trim_title($title, $tweet_text, $service, $encode);
}


/**
 * Performs replacements on a string for the current post in the loop.
 * @package tweet-this
 * @since 1.7.7
 */
function get_tt_parse_string($text = '', $url = '', $space = ' ',
	$service = 'twitter', $title = '') {
	if($url == '') $url = get_tweet_this_short_url();
	if($title == '') $title = get_the_title();
	$matches = array('[TITLE]', '[TITLE_SHARE]', '[AUTHOR]', '[CATEGORY]',
		'[DATE]', '[TIME]', '[EXCERPT]', '[BLOG_TITLE]', '  ');
	$replacements = array(get_tweet_this_trim_title($title, $text,
		'twitter', false), get_tweet_this_trim_title($title, $text,
		'twitter-share', false), tt_get_the_author(),
		tt_get_the_category(), get_the_time(get_option('date_format')),
		get_the_time(get_option('time_format')), tt_get_the_excerpt(),
		get_bloginfo('name'), ' ');
	$final_text = str_replace($matches, $replacements, $text);
	if($service != 'twitter-share' && strpos($final_text, '[URL]') !==
	false && (strlen($final_text) > (145 - get_tt_url_service_len()))) {
		if(strpos($final_text, '[URL]') >
		(135 - get_tt_url_service_len()))
			$final_text = tt_mb_substr(preg_replace("/(\040)+/",
				" ", str_replace('[URL]', '', $final_text)), 0,
				(136 - get_tt_url_service_len())) . '... ' .
				$url;
		else	$final_text = tt_mb_substr(preg_replace("/(\040)+/",
				" ", $final_text), 0,
				(142 - get_tt_url_service_len())) . '...';}
	elseif($service != 'twitter-share' && strpos($final_text, '[URL]') ===
	false && strlen($final_text) > 140)
		$final_text = tt_mb_substr($final_text, 0, 137) . '...';
	$final_text = str_replace('[URL]', $url, $final_text);
	return str_replace(' ', $space, $final_text);
}


/**
 * Echoes the above function for convenience.
 * @package tweet-this
 * @since 1.7.7
 */
function tt_parse_string($text = '', $url = '', $space = ' ',
	$service = 'twitter', $title = '') {
	echo	get_tt_parse_string($text, $url, $space, $service, $title);
}


/**
 * Returns the link text array or the title/alt text array.
 * @package tweet-this
 * @since 1.7.7
 */
function tt_service_array($array = 'link') {
	$services = array('twitter' => __('Tweet This Post', 'tweet-this'),
		'plurk' => __('Plurk This Post', 'tweet-this'),
		'bebo' => __('Post to Bebo', 'tweet-this'),
		'buzz' => __('Buzz This Post', 'tweet-this'),
		'delicious' => __('Post to Delicious', 'tweet-this'),
		'digg' => __('Digg This Post', 'tweet-this'),
		'diigo' => __('Post to Diigo', 'tweet-this'),
		'facebook' => __('Post to Facebook', 'tweet-this'),
		'ff' => __('Post to FriendFeed', 'tweet-this'),
		'gbuzz' => __('Post to Google Buzz', 'tweet-this'),
		'gmail' => __('Send Gmail', 'tweet-this'),
		'linkedin' => __('Post to LinkedIn', 'tweet-this'),
		'mixx' => __('Mixx This Post', 'tweet-this'),
		'myspace' => __('Post to MySpace', 'tweet-this'),
		'ping' => __('Ping This Post', 'tweet-this'),
		'reddit' => __('Post to Reddit', 'tweet-this'),
		'slashdot' => __('Post to Slashdot', 'tweet-this'),
		'squidoo' => __('Post to Squidoo', 'tweet-this'),
		'su' => __('Stumble This Post', 'tweet-this'),
		'technorati' => __('Post to Technorati', 'tweet-this'));
	if($array == 'link' || $array == '') return $services;
	elseif($array == 'title' || $array == 'alt') {
		$services['twitter'] = __('Post to Twitter', 'tweet-this');
		$services['plurk'] = __('Post to Plurk', 'tweet-this');
		$services['buzz'] = __('Post to Yahoo Buzz', 'tweet-this');
		$services['digg'] = __('Post to Digg', 'tweet-this');
		$services['mixx'] = __('Post to Mixx', 'tweet-this');
		$services['ping'] = __('Post to Ping.fm', 'tweet-this');
		$services['su'] = __('Post to StumbleUpon', 'tweet-this');
		return $services;}
	else return false;
}


/**
 * Same as tt_service_array($array).
 * @package tweet-this
 * @since 1.7.7
 */
function get_tt_service_array($array) {
	return tt_service_array($array);
}


/**
 * Returns the link text, which can be set in Settings > Tweet This.
 * @package tweet-this
 * @since 1.7.6
 */
function get_tweet_this_link_text($link_text = '', $service = 'twitter') {
	if($link_text == '') {
	if($service == 'twitter-share' || $service == '') $service = 'twitter';
	$services = tt_service_array('link');
	foreach($services as $name => $display) {
		if($service == $name) {
			if(tt_option('tt_' . $name . '_link_text') == '')
				$link_text = $display;
			else	$link_text = tt_option('tt_' . $name .
				'_link_text');}}}
	return get_tt_parse_string($link_text, '', ' ', 'twitter-share');
}


/**
 * Echoes the above function for convenience.
 * @package tweet-this
 * @since 1.2
 * @notes 1.7.6: Behavior changed from return to echo.
 */
function tweet_this_link_text($link_text = '', $service = 'twitter') {
	echo	get_tweet_this_link_text($link_text, $service);
}


/**
 * Returns the popup title text, which can be set in Settings > Tweet This.
 * @package tweet-this
 * @since 1.7.6
 */
function get_tweet_this_title_text($title_text = '', $service = 'twitter') {
	if($title_text == '') {
	if($service == 'twitter-share' || $service == '') $service = 'twitter';
	$services = tt_service_array('title');
	foreach($services as $name => $display) {
		if($service == $name) {
			if(tt_option('tt_' . $name . '_title_text') == '')
				$title_text = $display;
			else	$title_text = tt_option('tt_' . $name .
				'_title_text');}}}
	return get_tt_parse_string($title_text, '', ' ', 'twitter-share');
}


/**
 * Echoes the above function for convenience.
 * @package tweet-this
 * @since 1.2
 * @notes 1.7.6: Behavior changed from return to echo.
 */
function tweet_this_title_text($title_text = '', $service = 'twitter') {
	echo	get_tweet_this_title_text($title_text, $service);
}


/**
 * Determines whether the Tweet This link can be displayed on the current post.
 * If true, this function returns $item. If false, it returns blank.
 * @package tweet-this
 * @since 1.3
 */
function tt_display_limits($item) {
	global $preview, $post;
	if((!$preview && $post->post_status != 'draft') ||
	tt_option('tt_url_service') == 'local') {
		if(tt_option('tt_limit_to_posts') == 'true') {
			if(tt_option('tt_limit_to_single') == 'true') {
				if(is_single()) return $item;}
			else {	if(!is_page()) return $item;}}
		if(tt_option('tt_limit_to_single') == 'true') {
			if(tt_option('tt_limit_to_posts') == 'true') {
				if(is_single()) return $item;}
			else {	if(is_singular()) return $item;}}
	if(tt_option('tt_limit_to_posts') != 'true' &&
	tt_option('tt_limit_to_single') != 'true')
		return $item;}
}


/**
 * Same as tt_display_limits($item).
 * @package tweet-this
 * @since 1.7.6
 */
function get_tt_display_limits($item) {
	return tt_display_limits($item);
}


/**
 * Generates the direct URL to tweet a post to Twitter or Plurk.
 * @package tweet-this
 * @since 1.1
 */
function get_tweet_this_url($tweet_text = '', $service = '', $longurl = '',
	$title = '', $post_id = '', $auto = false) {
	if($service == 'twitter')
		$path = 'http://twitter.com/home/?status=';
	elseif($service == 'twitter-share')
		$path = 'http://twitter.com/share?url=';
	elseif($service == 'plurk')
		$path = 'http://plurk.com/?status=';
	else	$path = 'http://twitter.com/home/?status=';
	if($tweet_text == '') {
		if($service == 'twitter') {
			if(tt_option('tt_tweet_text') == '')
				$tweet_text = '[TITLE] [URL]';
			else $tweet_text = tt_option('tt_tweet_text');}
		elseif($service == 'twitter-share') {
			if(tt_option('tt_twitter_share_text') == '')
				$tweet_text = '[TITLE_SHARE]';
			else $tweet_text = tt_option('tt_twitter_share_text');}
		elseif($service == 'plurk') {
			if(tt_option('tt_plurk_text') == '')
				$tweet_text = '[TITLE] [URL]';
			else $tweet_text = tt_option('tt_plurk_text');}
		else {	if(tt_option('tt_tweet_text') == '')
				$tweet_text = '[TITLE] [URL]';
			else $tweet_text = tt_option('tt_tweet_text');}}
	if($auto == true) $force = true; else $force = false;
	$title_trim = get_tweet_this_trim_title($title, $tweet_text, $service,
		false);
	if($service == 'twitter-share' || (tt_option('tt_30') == 'true' &&
		(strlen(get_permalink()) <
		(139 - strlen(get_tt_parse_string($tweet_text, '1', ' ',
		$service, $title_trim)))))) $url = get_permalink();
	else	$url = get_tweet_this_short_url($longurl, $post_id, true,
		false, '', $force);
	$tweet_text = urlencode(get_tt_parse_string($tweet_text, $url, ' ',
		$service, $title_trim));
	if($service == 'twitter-share') {
		$getvia = tt_option('tt_twitter_share_via');
		$getrel = tt_option('tt_twitter_share_rel');
		if($getvia == '[BLANK]') {$via = ''; $vialen = 0;}
		elseif($getvia != '') {$via = '&via=' . $getvia;
			$vialen = strlen($getvia) + 6;}
		else {$via = '&via=tweetthisplugin'; $vialen = 21;}
		if($getrel == '[BLANK]') $rel = '';
		elseif($getrel != '') $rel = '&related=' . str_replace(
			array(' ', ','), array('%20', '%2C'), $getrel);
		else $rel = '&related=richardxthripp%2Ctweetthisplugin';
		if(strlen($tweet_text) > (144 - TT_TCO_LEN - $vialen))
			$tweet_text = tt_mb_substr($tweet_text, 0,
				(141 - TT_TCO_LEN - $vialen)) . '...';
		if($tweet_text == '[BLANK]' || $tweet_text == false)
			$text = '';
		elseif($tweet_text != '')
			$text = '&text=' . $tweet_text;
		else $text = '';
		$final = $path . $url . $text . $via . $rel;}
	else	$final = $path . $tweet_text;
	if($auto == true) return $tweet_text;
	else	return tt_display_limits(str_replace('#', '%23', $final));
}


/**
 * Echoes the above function for convenience.
 * @package tweet-this
 * @since 1.1
 */
function tweet_this_url($tweet_text = '', $service = '', $longurl = '',
	$title = '', $post_id = '', $auto = false) {
	echo	get_tweet_this_url($tweet_text, $service, $longurl, $title,
		$post_id, $auto);
}


/**
 * Core function returns the complete link to a service.
 * @package tweet-this
 * @since 1.2
 */
function get_tweet_this($service = 'twitter', $tweet_text = '',
	$link_text = '', $title_text = '', $icon_file = '', $a_class = '',
	$img_class = '', $img_alt = '') {
	if($a_class == '') {
		if(tt_option('tt_link_css_class') == '')
			$a_class = 'tt';
		else $a_class = tt_option('tt_link_css_class');}
	if($img_class == '') {
		if(tt_option('tt_img_css_class') == '')
			$img_class = 'nothumb';
		else $img_class = tt_option('tt_img_css_class');}
	$url = get_tweet_this_url($tweet_text, $service);
	$get_title = get_tweet_this_title_text($title_text, $service);
	$pttl = urlencode(@html_entity_decode(get_the_title(), ENT_COMPAT,
		'UTF-8'));
	$purl = get_permalink();
	$pbtl = get_bloginfo('name');
	$pxcr = urlencode(tt_get_the_excerpt('', 150));
	if($service == 'twitter-share' || $service == '') $service = 'twitter';
	if($get_title == '[BLANK]') $title = '';
	else $title = ' title="' . $get_title . '"';
	$link = get_tweet_this_link_text($link_text, $service);
	// TODO: Use a loop here.
	if($service == 'twitter') {
		if($img_alt == '')
			$img_alt = __('Post to Twitter', 'tweet-this');
		if($icon_file == '') {
			if(tt_option('tt_twitter_icon') == '')
				$icon_file = 'en/twitter/tt-twitter.png';
			else $icon_file = tt_option('tt_twitter_icon');}}
	if($service == 'plurk') {
		if($img_alt == '')
			$img_alt = __('Post to Plurk', 'tweet-this');
		if($icon_file == '') {
			if(tt_option('tt_plurk_icon') == '')
				$icon_file = 'en/plurk/tt-plurk.png';
			else $icon_file = tt_option('tt_plurk_icon');}}
	if($service == 'bebo') {
		if($img_alt == '')
			$img_alt = __('Post to Bebo', 'tweet-this');
		if($icon_file == '') {
			if(tt_option('tt_bebo_icon') == '')
				$icon_file = 'en/bebo/tt-bebo.png';
			else $icon_file = tt_option('tt_bebo_icon');}
		$url =	'http://www.bebo.com/c/share?Url=' . $purl .
			'&amp;Title=' . $pttl;}
	if($service == 'buzz') {
		if($img_alt == '')
			$img_alt = __('Post to Yahoo Buzz', 'tweet-this');
		if($icon_file == '') {
			if(tt_option('tt_buzz_icon') == '')
				$icon_file = 'en/buzz/tt-buzz.png';
			else $icon_file = tt_option('tt_buzz_icon');}
		$url =	'http://buzz.yahoo.com/buzz?targetUrl=' .
			$purl . '&amp;headline=' . str_pad($pttl, 10, '.');}
	if($service == 'delicious') {
		if($img_alt == '')
			$img_alt = __('Post to Delicious', 'tweet-this');
		if($icon_file == '') {
			if(tt_option('tt_delicious_icon') == '')
				$icon_file = 'en/delicious/tt-delicious.png';
			else $icon_file = tt_option('tt_delicious_icon');}
		$url =	'http://delicious.com/post?url=' . $purl .
			'&amp;title=' . $pttl;}
	if($service == 'digg') {
		if($img_alt == '')
			$img_alt = __('Post to Digg', 'tweet-this');
		if($icon_file == '') {
			if(tt_option('tt_digg_icon') == '')
				$icon_file = 'en/digg/tt-digg.png';
			else $icon_file = tt_option('tt_digg_icon');}
		$url =	'http://digg.com/submit?url=' . $purl . '&amp;title=' .
			$pttl;}
	if($service == 'diigo') {
		if($img_alt == '')
			$img_alt = __('Post to Diigo', 'tweet-this');
		if($icon_file == '') {
			if(tt_option('tt_diigo_icon') == '')
				$icon_file = 'en/diigo/tt-diigo.png';
			else $icon_file = tt_option('tt_diigo_icon');}
		$url =	'http://www.diigo.com/post?url=' . $purl .
			'&amp;title=' . $pttl . '&amp;desc=' . $pttl;}
	if($service == 'facebook') {
		if($img_alt == '')
			$img_alt = __('Post to Facebook', 'tweet-this');
		if($icon_file == '') {
			if(tt_option('tt_facebook_icon') == '')
				$icon_file = 'en/facebook/tt-facebook.png';
			else $icon_file = tt_option('tt_facebook_icon');}
		$url =	'http://www.facebook.com/share.php?u=' . $purl .
			'&amp;t=' . $pttl;}
	if($service == 'ff') {
		if($img_alt == '')
			$img_alt = __('Post to FriendFeed', 'tweet-this');
		if($icon_file == '') {
			if(tt_option('tt_ff_icon') == '')
				$icon_file = 'en/ff/tt-ff.png';
			else $icon_file = tt_option('tt_ff_icon');}
		$url =	'http://www.friendfeed.com/share?title=' . $pttl .
			'&amp;link=' . $purl;}
	if($service == 'gbuzz') {
		if($img_alt == '')
			$img_alt = __('Post to Google Buzz', 'tweet-this');
		if($icon_file == '') {
			if(tt_option('tt_gbuzz_icon') == '')
				$icon_file = 'en/gbuzz/tt-gbuzz.png';
			else $icon_file = tt_option('tt_gbuzz_icon');}
		$url =	'http://www.google.com/buzz/post?url=' . $purl .
			'&amp;imageurl=';}
	if($service == 'gmail') {
		if($img_alt == '')
			$img_alt = __('Send Gmail', 'tweet-this');
		if($icon_file == '') {
			if(tt_option('tt_gmail_icon') == '')
				$icon_file = 'en/gmail/tt-gmail.png';
			else $icon_file = tt_option('tt_gmail_icon');}
		$url =	'https://mail.google.com/mail/?ui=2&amp;view=cm&amp;' .
			'fs=1&amp;tf=1&amp;su=' . $pttl . '&amp;body=Link:+' .
			$purl . '%0D%0A%0D%0A----%0D%0A+' . $pxcr;}
	if($service == 'linkedin') {
		if($img_alt == '')
			$img_alt = __('Post to LinkedIn', 'tweet-this');
		if($icon_file == '') {
			if(tt_option('tt_linkedin_icon') == '')
				$icon_file = 'en/linkedin/tt-linkedin.png';
			else $icon_file = tt_option('tt_linkedin_icon');}
		$url =	'http://www.linkedin.com/shareArticle?mini=true&amp;' .
			'url=' . $purl . '&amp;title=' . $pttl .
			'&amp;summary=' . $pxcr . '&amp;source=' . $pbtl;}
	if($service == 'mixx') {
		if($img_alt == '')
			$img_alt = __('Mixx This Post', 'tweet-this');
		if($icon_file == '') {
			if(tt_option('tt_mixx_icon') == '')
				$icon_file = 'en/mixx/tt-mixx.png';
			else $icon_file = tt_option('tt_mixx_icon');}
		$url =	'http://www.mixx.com/submit?page_url=' . $purl .
			'&amp;title=' . $pttl;}
	if($service == 'myspace') {
		if($img_alt == '')
			$img_alt = __('Post to MySpace', 'tweet-this');
		if($icon_file == '') {
			if(tt_option('tt_myspace_icon') == '')
				$icon_file = 'en/myspace/tt-myspace.png';
			else $icon_file = tt_option('tt_myspace_icon');}
		$url =	'http://www.myspace.com/Modules/PostTo/Pages/' .
			'?l=3&amp;u=' . $purl . '&amp;t=' . $pttl;}
	if($service == 'ping') {
		if($img_alt == '')
			$img_alt = __('Post to Ping.fm', 'tweet-this');
		if($icon_file == '') {
			if(tt_option('tt_ping_icon') == '')
				$icon_file = 'en/ping/tt-ping.png';
			else $icon_file = tt_option('tt_ping_icon');}
		$url =	'http://ping.fm/ref/?method=microblog&amp;title=' .
			$pttl . '&amp;link=' . $purl;}
	if($service == 'reddit') {
		if($img_alt == '')
			$img_alt = __('Post to Reddit', 'tweet-this');
		if($icon_file == '') {
			if(tt_option('tt_reddit_icon') == '')
				$icon_file = 'en/reddit/tt-reddit.png';
			else $icon_file = tt_option('tt_reddit_icon');}
		$url =	'http://reddit.com/submit?url=' . $purl .
			'&amp;title=' . $pttl;}
	if($service == 'slashdot') {
		if($img_alt == '')
			$img_alt = __('Post to Slashdot', 'tweet-this');
		if($icon_file == '') {
			if(tt_option('tt_slashdot_icon') == '')
				$icon_file = 'en/slashdot/tt-slashdot.png';
			else $icon_file = tt_option('tt_slashdot_icon');}
		$url =	'http://slashdot.org/bookmark.pl?url=' . $purl .
			'&amp;title=' . $pttl;}
	if($service == 'squidoo') {
		if($img_alt == '')
			$img_alt = __('Post to Squidoo', 'tweet-this');
		if($icon_file == '') {
			if(tt_option('tt_squidoo_icon') == '')
				$icon_file = 'en/squidoo/tt-squidoo.png';
			else $icon_file = tt_option('tt_squidoo_icon');}
		$url =	'http://www.squidoo.com/lensmaster/bookmark?' . $purl;}
	if($service == 'su') {
		if($img_alt == '')
			$img_alt = __('Post to StumbleUpon', 'tweet-this');
		if($icon_file == '') {
			if(tt_option('tt_su_icon') == '')
				$icon_file = 'en/su/tt-su.png';
			else $icon_file = tt_option('tt_su_icon');}
		$url =	'http://stumbleupon.com/submit?url=' . $purl .
			'&amp;title=' . $pttl;}
	if($service == 'technorati') {
		if($img_alt == '')
			$img_alt = __('Post to Technorati', 'tweet-this');
		if($icon_file == '') {
			if(tt_option('tt_technorati_icon') == '')
				$icon_file = 'en/technorati/tt-technorati.png';
			else $icon_file = tt_option('tt_technorati_icon');}
		$url =	'http://technorati.com/faves?add=' . $purl;}
	$icon = TT_URLPATH . 'icons/' . $icon_file;
	if($icon_file != 'noicon')
		$item = '<a ' . tt_option('nw') . tt_option('nf') .
			'class="' . $a_class . '" href="' . $url .
			'"' . $title . '><img class="' .
			$img_class . '" src="' . $icon . '" alt="' .
			$img_alt . '"' . $style . ' /></a>';
	if($link != '[BLANK]')
		$item .= ' <a ' . tt_option('nw') . tt_option('nf') .
			'class="' . $a_class . '" href="' . $url .
			'"' . $title . '>' . $link . '</a>';
	if($service == 'twitter' && $icon_file == 'textbox') {
		global $id; $textbox = tt_option('tt_textbox_size');
		if($textbox == '') $textbox = '60';
		if($link == '[BLANK]')
			$link = __('Tweet This Post', 'tweet-this');
		$item =	'<form action="' . get_bloginfo('wpurl') .
		'/index.php" method="post" id="tt_twitter_box_' . $id .
		'"><fieldset><input type="text" id="tt_twitter_box_text_' .
		$id . '" name="tt_twitter_box_text" class="tt_twitter_box" ' .
		'size="' . $textbox . '" value="' . urldecode(
		html_entity_decode((str_replace('http://twitter.com/home/' .
		'?status=', '', $url)))) . '" onchange="ttCharCount' . $id .
		'();" onclick="ttCharCount' . $id . '();" onkeyup=' .
		'"ttCharCount' . $id . '();" maxlength="140" /><script type=' .
		'"text/javascript">function ttCharCount' . $id . '() {var ' .
		'count' . $id . ' = document.getElementById("tt_twitter_box_' .
		'text_' . $id . '").value.length; document.getElementById' .
		'("tt_char_count_' . $id . '").innerHTML = "<strong>" + (140' .
		' - count' . $id. ') + "</strong>" + "' . __(' character(s) ' .
		'remaining', 'tweet-this') . '";} setTimeout("ttCharCount' .
		$id . '();", 500); document.getElementById("tt_twitter_box_' .
		$id . '").setAttribute("autocomplete", "off");</script>' .
		'<br /><input type="submit" id="tt_twitter_box_submit_' . $id .
		'" name="tt_twitter_box_submit" value="' . $link . '" class=' .
		'"button-primary" style="margin-top:5px;margin-bottom:-5px;"' .
		' /> <span id="tt_char_count_' . $id . '"></span></fieldset>' .
		'<input type="hidden" name="ttaction" value="tt_twitter_box"' .
		' /></form>';}
	return tt_display_limits($item);
}


/**
 * Echoes the above function for convenience.
 * @package tweet-this
 * @since 1.1
 */
function tweet_this($service = 'twitter', $tweet_text = '', $link_text = '',
	$title_text = '', $icon_file = '', $a_class = '', $img_class = '',
	$img_alt = '') {
	echo	get_tweet_this($service, $tweet_text, $link_text, $title_text,
		$icon_file, $a_class, $img_class, $img_alt);
}


/**
 * Tweet This options on post edit screen. Requires WP 2.7 or newer.
 * @package tweet-this
 * @since 1.6
 */
function tt_post_options() {
	global $post;
	$tt_auto = get_post_meta($post->ID, 'tt_auto_tweet', true);
	$tt_auto_text = get_post_meta($post->ID, 'tt_auto_tweet_text', true);
	$tt_http_code = get_post_meta($post->ID, 'tt_http_code', true);
	$tt_tweeted = get_post_meta($post->ID, 'tt_tweeted', true);
	if(version_compare($GLOBALS['wp_version'], '2.5', '<'))
		echo	'<div id="tweetthis" class="postbox"><h3>' .
			__('Tweet This', 'tweet-this') .
			'</h3><div class="inside">';
	echo	'<p>';
	if(tt_option('tt_app_consumer_key') == '' ||
		tt_option('tt_app_consumer_secret') == '' ||
		tt_option('tt_oauth_token') == '' ||
		tt_option('tt_oauth_token_secret') == '') {
		echo '<strong>'; printf(__('Please enter your Twitter OAuth ' .
		'keys under <a target="_blank" href="%1$s/wp-admin/options-' .
		'general.php%2$s">Settings &gt; Tweet This &gt; Automatic ' .
		'Tweeting</a>, or uncheck "Enable automatic tweeting on ' .
		'posts" and "Enable automatic tweeting on pages" to remove ' .
		'this box.', 'tweet-this'), get_bloginfo('url'),
		constant('TT_FILE_LOC')); echo '</strong>';}
	elseif($tt_tweeted != 'false' && $tt_tweeted != '') {
		if(substr($tt_tweeted, 0, 7) == 'http://')
			$link = ': <a href="' . $tt_tweeted .
			'" target="_blank">' . str_replace('/', '',
			strrchr($tt_tweeted, '/')) . '</a>. ';
		else $link = '. ';
		echo '<input type="hidden" name="tt_auto_tweet" value=' .
		'"false" /><input type="hidden" name="tt_auto_tweet_text" ' .
		'value="' . htmlentities($tt_auto_text) . '" /><strong>' .
		'<font color="green">' . __('Tweet published',
		'tweet-this') . $link . __('To allow a retweet, delete the ' .
		'tt_tweeted custom field and resave the post.', 'tweet-this') .
		'</font></strong>';}
	elseif($post->post_status == 'private')
		echo	'<strong>' . __('Cannot tweet a private post.',
			'tweet-this') . '</strong>';
	else {	if(trim($tt_http_code) != '' && trim($tt_http_code) != '200') {
			echo '<strong>';
			printf(__('Tweet failed. Twitter returned <font ' .
			'color="red">HTTP %1$s</font>. Check your OAuth ' .
			'settings and re-save this post to try again.',
			'tweet-this'), $tt_http_code);
			echo '</strong></p><p>';}
		echo	'<script type="text/javascript">function ttCharCount' .
			'() {var count = document.getElementById("tt_auto_' .
			'tweet_text").value.length; var count_title = ' .
			'document.getElementById("title").value.length; ' .
			'document.getElementById("tt_char_count").innerHTML ' .
			'= "<strong>" + (140 - count - count_title - ' .
			get_tt_url_service_len() . ') + "</strong>&nbsp;" + ' .
			'"' . __('character(s)&nbsp;remaining', 'tweet-this') .
			'";} setTimeout("ttCharCount();", 500); document.get' .
			'ElementById("tt_twitter_box").setAttribute("auto' .
			'complete", "off"); ttCharCount();</script>' .
			'<label for="tt_auto_tweet" class="selectit">' .
			'<input type="hidden" name="tt_auto_tweet" ' .
			'value="false" /><input name="tt_auto_tweet" ' .
			'id="tt_auto_tweet" type="checkbox"';
		if($tt_auto == 'true' || (tt_option('tt_auto_tweet') == 'true'
			&& $tt_auto != 'false' && $post->post_type != 'page' &&
			$post->post_status != 'publish') ||
			(tt_option('tt_auto_tweet_pages') == 'true' &&
			$tt_auto != 'false' && $post->post_type == 'page' &&
			$post->post_status != 'publish'))
			echo	' checked="checked"';
		echo	' value="true" /> ' . __('Send to Twitter',
			'tweet-this') . '</label></p><p><textarea style=' .
			'"width:95%;" name="tt_auto_tweet_text" ' .
			'id="tt_auto_tweet_text" rows="2" cols="60" ' .
			'onchange="ttCharCount();" onclick="ttCharCount();" ' .
			'onkeyup="ttCharCount();">';
		if($tt_auto_text != '')
			echo	htmlentities($tt_auto_text);
		elseif(tt_option('tt_auto_tweet_text') != '')
			echo	htmlentities(tt_option('tt_auto_tweet_text'));
		else echo	__('New blog post', 'tweet-this') .
				': [TITLE] [URL]';
		echo	'</textarea></p><p><span id="tt_char_count"></span>' .
			'</p>';}
	if(version_compare($GLOBALS['wp_version'], '2.5', '<'))
		echo '</div></div>';
}


/**
 * Saves options on post edit screen. Requires WP 2.7 or newer.
 * @package tweet-this
 * @since 1.6
 */
function tt_store_post_options($post_id, $post = false) {
	$postdata = tt_get_post($post_id);
	if(!$postdata || ($postdata['post_type'] != 'page' &&
		tt_option('tt_auto_tweet_display') == 'false') ||
		($postdata['post_type'] == 'page' &&
		tt_option('tt_auto_tweet_display_pages') == 'false') ||
		tt_option('tt_app_consumer_key') == '' ||
		tt_option('tt_app_consumer_secret') == '' ||
		tt_option('tt_oauth_token') == '' ||
		tt_option('tt_oauth_token_secret' == ''))
			return false;
	$tt_auto_save = get_post_meta($post_id, 'tt_auto_tweet', true);
	if($tt_auto_save == '' && $postdata['post_type'] != 'page')
		$tt_auto_save = tt_option('tt_auto_tweet');
	elseif($tt_auto_save == '' && $postdata['post_type'] == 'page')
		$tt_auto_save = tt_option('tt_auto_tweet_pages');
	if($tt_auto_save == '') $tt_auto_save = 'false';
	if($_POST['tt_auto_tweet'] == 'true') $tt_auto_post = 'true';
	elseif($_POST['tt_auto_tweet'] == 'false') $tt_auto_post = 'false';
	else	$tt_auto_post = $tt_auto_save;
	update_post_meta($post_id, 'tt_auto_tweet', $tt_auto_post);
	$tt_auto_text_post = $_POST['tt_auto_tweet_text'];
	$tt_auto_text_save =
		get_post_meta($post_id, 'tt_auto_tweet_text', true);
	if($tt_auto_text_post == $tt_auto_text_save && $tt_auto_text_post != ''
		&& $tt_auto_text_save != '') $save_text = false;
	elseif($tt_auto_text_post == '') {
		if($tt_auto_text_save == '')
			$tt_auto_text_save = tt_option('tt_auto_tweet_text');
		$tt_auto_text_post = $tt_auto_text_save;
		$save_text = true;}
	elseif($tt_auto_text_save == '') $save_text = true;
	if($tt_auto_text_post == '') $tt_auto_text_post =
		__('New blog post', 'tweet-this') . ': [TITLE] [URL]';
	update_post_meta($post_id, 'tt_auto_tweet_text', $tt_auto_text_post);
}


/**
 * Handles automatic tweeting of posts. Requires WP 2.7 or newer.
 * @package tweet-this
 * @since 1.6
 */
function tt_auto_tweet($post_id) {
	$postdata = tt_get_post($post_id);
	$tt_auto = get_post_meta($post_id, 'tt_auto_tweet', true);
	$tt_auto_text = get_post_meta($post_id, 'tt_auto_tweet_text', true);
	$tt_tweeted = get_post_meta($post_id, 'tt_tweeted', true);
	if($tt_auto == 'true' || $tt_auto == 'false')
		$tweet_auto = $tt_auto;
	elseif(tt_option('tt_auto_tweet') != '')
		$tweet_auto = tt_option('tt_auto_tweet');
	else $tweet_auto = 'false';
	if($_POST['tt_auto_tweet'] == 'true')
		$tweet_auto = 'true';
	if($tt_auto_text != '')
		$tweet_text = $tt_auto_text;
	elseif(tt_option('tt_auto_tweet_text') != '')
		$tweet_text = tt_option('tt_auto_tweet_text');
	else $tweet_text = __('New blog post', 'tweet-this') .
		': [TITLE] [URL]';
	if(trim($_POST['tt_auto_tweet_text']) != '')
		$tweet_text = $_POST['tt_auto_tweet_text'];
	if(($tt_tweeted != 'false' && $tt_tweeted != '') ||
	$tweet_auto == 'false' || !$postdata ||
	tt_option('tt_app_consumer_key') == '' ||
	tt_option('tt_app_consumer_secret') == '' ||
	tt_option('tt_oauth_token') == '' ||
	tt_option('tt_oauth_token_secret') == '' ||
	($postdata['post_type'] != 'page' &&
	tt_option('tt_auto_tweet_display') == 'false') ||
	($postdata['post_type'] == 'page' &&
	tt_option('tt_auto_tweet_display_pages') == 'false'))
		return false;
	else {	if(tt_option('tt_app_consumer_key') != '' &&
		tt_option('tt_app_consumer_secret') != '' &&
		tt_option('tt_oauth_token') != '' &&
		tt_option('tt_oauth_token_secret') != '') {
			$status = urldecode(get_tweet_this_url($tweet_text,
				'twitter', get_permalink($post_id),
				$postdata['post_title'], $post_id, true));
			$connection = tt_oauth_connection();
			$connection->decode_json = true;
			$data = $connection->post(TT_API_POST_STATUS,
			array('status' => $status, 'source' => 'tweetthis'));
		if($connection->http_code == '200') {
			update_post_meta($post_id, 'tt_tweeted',
				'http://twitter.com/' .
				$data->user->screen_name . '/status/' .
				$data->id);
			delete_post_meta($post_id, 'tt_http_code');}
		else {	update_post_meta($post_id, 'tt_tweeted', 'false');
			update_post_meta($post_id, 'tt_http_code',
				$connection->http_code);}}}
}


/**
 * Inserts all links.
 * @package tweet-this
 * @since 1.1
 */
function insert_tweet_this($content = '', $force = false) {
	global $id, $preview, $post;
	$tweet_this_hide = get_post_meta($id, 'tweet_this_hide', true);
	if($force == false && (($tweet_this_hide && $tweet_this_hide
		!= 'false') || (tt_option('tt_url_service') != 'local' &&
		($preview || $post->post_status == 'draft'))))
			$content = $content;
	// TODO: Use a loop here.
	else {	$prefix = constant('TT_PREFIX');
		if(tt_option('tt_auto_display') == 'true' &&
			tt_option('tt_twitter_icon') == 'textbox')
				$prefix = str_replace('<p>', '', $prefix);
		if(	tt_option('tt_plurk') == 'true' ||
			tt_option('tt_bebo') == 'true' ||
			tt_option('tt_buzz') == 'true' ||
			tt_option('tt_delicious') == 'true' ||
			tt_option('tt_digg') == 'true' ||
			tt_option('tt_diigo') == 'true' ||
			tt_option('tt_facebook') == 'true' ||
			tt_option('tt_ff') == 'true' ||
			tt_option('tt_gbuzz') == 'true' ||
			tt_option('tt_gmail') == 'true' ||
			tt_option('tt_linkedin') == 'true' ||
			tt_option('tt_mixx') == 'true' ||
			tt_option('tt_myspace') == 'true' ||
			tt_option('tt_ping') == 'true' ||
			tt_option('tt_reddit') == 'true' ||
			tt_option('tt_slashdot') == 'true' ||
			tt_option('tt_squidoo') == 'true' ||
			tt_option('tt_su') == 'true' ||
			tt_option('tt_technorati') == 'true')
				$extended = true;
		else	$extended = false;
		if(tt_option('tt_auto_display') == 'true' || $extended == true)
			$content .= tt_display_limits($prefix);
		$order = explode(' ', preg_replace("/(\040)+/", " ",
			str_replace(',', ' ',
			trim(tt_option('tt_service_order')))));
		foreach($order as $s) {if(tt_option('tt_' . $s) == 'true') {
			if($s == 'twitter' && tt_option('tt_twitter_share') ==
				'true') $s = 'twitter-share';
			$content .= TT_SEPARATOR . get_tweet_this($s);}}
		if(tt_option('tt_auto_display') == 'true' || $extended == true)
			$content .= tt_display_limits(TT_SUFFIX);}
	return $content;
}


/**
 * Handles [tweet_this] shortcode replacement in content areas.
 * @package tweet-this
 * @since 1.8
 */
function tt_shortcode_handler() {
	return insert_tweet_this('', true);
}


/**
 * Echo this to insert all links into your theme manually.
 * @package tweet-this
 * @since 1.7
 */
function get_tweet_this_manual() {
	remove_filter('the_content', 'insert_tweet_this');
	return insert_tweet_this();
}


/**
 * Use this to insert all links into your theme manually.
 * @package tweet-this
 * @since 1.7
 */
function tweet_this_manual() {
	remove_filter('the_content', 'insert_tweet_this');
	echo insert_tweet_this();
}


/**
 * CSS inserted using the wp_head hook.
 * @package tweet-this
 * @since 1.7.6
 */
function get_tweet_this_css() {
	if(tt_option('tt_css') == '')
	return	'<style type="text/css">img.' . tt_option('tt_img_css_class') .
		'{border:0;margin:0 0 0 2px !important;}</style>';
	else return	'<style type="text/css">' .
			str_replace(array('[IMG_CLASS]', '[LINK_CLASS]'),
			array(tt_option('tt_img_css_class'),
			tt_option('tt_link_css_class')), tt_option('tt_css')) .
			'</style>';
}


/**
 * Echoes the above function for convenience.
 * @package tweet-this
 * @since 1.1
 * @notes 1.7.6: Behavior changed from return to echo.
 */
function tweet_this_css() {
	echo get_tweet_this_css() . "\n";
}


/**
 * Updates the database options when saved.
 * @package tweet-this
 * @since 1.1
 */
function update_tt_options() {
	if(isset($_REQUEST['tt']))
		$new_options = $_REQUEST['tt'];
	$booleans = array('tt_30', 'tt_limit_to_single', 'tt_limit_to_posts',
		'tt_url_www', 'tt_shortlink_filter', 'tt_auto_display',
		'tt_plurk', 'tt_bebo', 'tt_buzz', 'tt_delicious', 'tt_digg',
		'tt_diigo', 'tt_facebook', 'tt_ff', 'tt_gbuzz', 'tt_gmail',
		'tt_linkedin', 'tt_mixx', 'tt_myspace', 'tt_ping', 'tt_reddit',
		'tt_slashdot', 'tt_squidoo', 'tt_su', 'tt_technorati',
		'tt_ad_vu', 'tt_j_mp', 'tt_footer', 'tt_ads', 'tt_new_window',
		'tt_nofollow', 'tt_auto_tweet_display',
		'tt_auto_tweet_display_pages', 'tt_auto_tweet',
		'tt_auto_tweet_pages');
	$oauths = array('tt_app_consumer_key', 'tt_app_consumer_secret',
		'tt_oauth_token', 'tt_oauth_token_secret');
	$urlkeys = array('tt_adjix_api_key', 'tt_bitly_username',
		'tt_bitly_api_key', 'tt_snipurl_username',
		'tt_snipurl_api_key', 'tt_supr_username', 'tt_supr_api_key');
	$triggers = array('tt_url_service', 'tt_url_www', 'tt_ad_vu',
		'tt_j_mp', 'tt_adjix_api_key', 'tt_bitly_username',
		'tt_bitly_api_key', 'tt_snipurl_username',
		'tt_snipurl_api_key', 'tt_snipurl_domain', 'tt_supr_username',
		'tt_supr_api_key', 'tt_custom_url_service');
	foreach($booleans as $boolean) $new_options[$boolean] =
		$new_options[$boolean] ? 'true' : 'false';
	foreach($oauths as $oauth)
		$new_options[$oauth] = trim($_REQUEST[$oauth]);
	foreach($urlkeys as $urlkey)
		$new_options[$urlkey] = trim($new_options[$urlkey]);
	foreach($triggers as $trigger) {
		if(tt_option($trigger) != $new_options[$trigger]) {
			global $wpdb;
			$fcount = number_format($wpdb->get_var("SELECT COUNT(*)
				FROM $wpdb->postmeta WHERE meta_key =
				'tweet_this_url' AND meta_value != 'getnew'"));
			global_flush_tt_cache();
			$flush = ' ' . __("<strong>$fcount</strong> cached " .
				"URL(s) flushed.", 'tweet-this'); break;}}
	if($new_options['tt_twitter_share'] == 'share')
		$new_options['tt_twitter_share'] = 'true';
	else	$new_options['tt_twitter_share'] = 'false';
	// TODO: Use a loop here.
	if($new_options['tt_link_text'] == __('Tweet This Post', 'tweet-this')
	&& tt_option('tt_link_text') == __('Tweet This Post', 'tweet-this') &&
	$new_options['tt_twitter_icon'] != tt_option('tt_twitter_icon') &&
	$new_options['tt_twitter_icon'] != 'en/twitter/tt-twitter.png' &&
	$new_options['tt_twitter_icon'] != 'en/twitter/tt-twitter2.png' &&
	$new_options['tt_twitter_icon'] != 'en/twitter/tt-twitter3.png' &&
	$new_options['tt_twitter_icon'] != 'en/twitter/tt-twitter4.png' &&
	$new_options['tt_twitter_icon'] != 'noicon' &&
	$new_options['tt_twitter_icon'] != 'textbox')
		$new_options['tt_link_text'] = '[BLANK]';
	if($new_options['tt_plurk_link_text'] ==
	__('Plurk This Post', 'tweet-this') &&
	tt_option('tt_plurk_link_text') ==
	__('Plurk This Post', 'tweet-this') &&
	$new_options['tt_plurk_icon'] != tt_option('tt_plurk_icon') &&
	$new_options['tt_plurk_icon'] != 'en/plurk/tt-plurk.png' &&
	$new_options['tt_plurk_icon'] != 'noicon')
		$new_options['tt_plurk_link_text'] = '[BLANK]';
	if($new_options['tt_bebo_link_text'] ==
	__('Post to Bebo', 'tweet-this') && tt_option('tt_bebo_link_text') ==
	__('Post to Bebo', 'tweet-this') &&
	$new_options['tt_bebo_icon'] != tt_option('tt_bebo_icon') &&
	$new_options['tt_bebo_icon'] != 'en/bebo/tt-bebo.png' &&
	$new_options['tt_bebo_icon'] != 'noicon')
		$new_options['tt_bebo_link_text'] = '[BLANK]';
	if($new_options['tt_buzz_link_text'] ==
	__('Buzz This Post', 'tweet-this') && tt_option('tt_buzz_link_text') ==
	__('Buzz This Post', 'tweet-this') &&
	$new_options['tt_buzz_icon'] != tt_option('tt_buzz_icon') &&
	$new_options['tt_buzz_icon'] != 'en/buzz/tt-buzz.png' &&
	$new_options['tt_buzz_icon'] != 'noicon')
		$new_options['tt_buzz_link_text'] = '[BLANK]';
	if($new_options['tt_delicious_link_text'] ==
	__('Post to Delicious', 'tweet-this') &&
	tt_option('tt_delicious_link_text') ==
	__('Post to Delicious', 'tweet-this') &&
	$new_options['tt_delicious_icon'] != tt_option('tt_delicious_icon') &&
	$new_options['tt_delicious_icon'] != 'en/delicious/tt-delicious.png' &&
	$new_options['tt_delicious_icon'] != 'noicon')
		$new_options['tt_delicious_link_text'] = '[BLANK]';
	if($new_options['tt_digg_link_text'] ==
	__('Digg This Post', 'tweet-this') && tt_option('tt_digg_link_text') ==
	__('Digg This Post', 'tweet-this') &&
	$new_options['tt_digg_icon'] != tt_option('tt_digg_icon') &&
	$new_options['tt_digg_icon'] != 'en/digg/tt-digg.png' &&
	$new_options['tt_digg_icon'] != 'noicon')
		$new_options['tt_digg_link_text'] = '[BLANK]';
	if($new_options['tt_diigo_link_text'] ==
	__('Post to Diigo', 'tweet-this') && tt_option('tt_diigo_link_text') ==
	__('Post to Diigo', 'tweet-this') &&
	$new_options['tt_diigo_icon'] != tt_option('tt_diigo_icon') &&
	$new_options['tt_diigo_icon'] != 'en/diigo/tt-diigo.png' &&
	$new_options['tt_diigo_icon'] != 'noicon')
		$new_options['tt_diigo_link_text'] = '[BLANK]';
	if($new_options['tt_facebook_link_text'] ==
	__('Post to Facebook', 'tweet-this') &&
	tt_option('tt_facebook_link_text') ==
	__('Post to Facebook', 'tweet-this') &&
	$new_options['tt_facebook_icon'] != tt_option('tt_facebook_icon') &&
	$new_options['tt_facebook_icon'] != 'en/facebook/tt-facebook.png' &&
	$new_options['tt_facebook_icon'] != 'noicon')
		$new_options['tt_facebook_link_text'] = '[BLANK]';
	if($new_options['tt_ff_link_text'] ==
	__('Post to FriendFeed', 'tweet-this') &&
	tt_option('tt_ff_link_text') == __('Post to FriendFeed', 'tweet-this')
	&& $new_options['tt_ff_icon'] != tt_option('tt_ff_icon') &&
	$new_options['tt_ff_icon'] != 'en/ff/tt-ff.png' &&
	$new_options['tt_ff_icon'] != 'noicon')
		$new_options['tt_ff_link_text'] = '[BLANK]';
	if($new_options['tt_gbuzz_link_text'] ==
	__('Post to Google Buzz', 'tweet-this') &&
	tt_option('tt_gbuzz_link_text') ==
	__('Post to Google Buzz', 'tweet-this') &&
	$new_options['tt_gbuzz_icon'] != tt_option('tt_gbuzz_icon') &&
	$new_options['tt_gbuzz_icon'] != 'en/gbuzz/tt-gbuzz.png' &&
	$new_options['tt_gbuzz_icon'] != 'noicon')
		$new_options['tt_gbuzz_link_text'] = '[BLANK]';
	if($new_options['tt_gmail_link_text'] ==
	__('Send Gmail', 'tweet-this') &&
	tt_option('tt_gmail_link_text') ==
	__('Send Gmail', 'tweet-this') &&
	$new_options['tt_gmail_icon'] != tt_option('tt_gmail_icon') &&
	$new_options['tt_gmail_icon'] != 'en/gmail/tt-gmail.png' &&
	$new_options['tt_gmail_icon'] != 'noicon')
		$new_options['tt_gmail_link_text'] = '[BLANK]';
	if($new_options['tt_linkedin_link_text'] ==
	__('Post to LinkedIn', 'tweet-this') &&
	tt_option('tt_linkedin_link_text') ==
	__('Post to LinkedIn', 'tweet-this') &&
	$new_options['tt_linkedin_icon'] != tt_option('tt_linkedin_icon') &&
	$new_options['tt_linkedin_icon'] != 'en/linkedin/tt-linkedin.png' &&
	$new_options['tt_linkedin_icon'] != 'noicon')
		$new_options['tt_linkedin_link_text'] = '[BLANK]';
	if($new_options['tt_mixx_link_text'] ==
	__('Mixx This Post', 'tweet-this') &&
	tt_option('tt_mixx_link_text') ==
	__('Mixx This Post', 'tweet-this') &&
	$new_options['tt_mixx_icon'] != tt_option('tt_mixx_icon') &&
	$new_options['tt_mixx_icon'] != 'en/mixx/tt-mixx.png' &&
	$new_options['tt_mixx_icon'] != 'noicon')
		$new_options['tt_mixx_link_text'] = '[BLANK]';
	if($new_options['tt_myspace_link_text'] ==
	__('Post to MySpace', 'tweet-this') &&
	tt_option('tt_myspace_link_text') ==
	__('Post to MySpace', 'tweet-this') &&
	$new_options['tt_myspace_icon'] != tt_option('tt_myspace_icon') &&
	$new_options['tt_myspace_icon'] != 'en/myspace/tt-myspace.png' &&
	$new_options['tt_myspace_icon'] != 'noicon')
		$new_options['tt_myspace_link_text'] = '[BLANK]';
	if($new_options['tt_ping_link_text'] ==
	__('Ping This Post', 'tweet-this') && tt_option('tt_ping_link_text') ==
	__('Ping This Post', 'tweet-this') &&
	$new_options['tt_ping_icon'] != tt_option('tt_ping_icon') &&
	$new_options['tt_ping_icon'] != 'en/ping/tt-ping.png' &&
	$new_options['tt_ping_icon'] != 'noicon')
		$new_options['tt_ping_link_text'] = '[BLANK]';
	if($new_options['tt_reddit_link_text'] ==
	__('Post to Reddit', 'tweet-this') &&
	tt_option('tt_reddit_link_text') ==
	__('Post to Reddit', 'tweet-this') &&
	$new_options['tt_reddit_icon'] != tt_option('tt_reddit_icon') &&
	$new_options['tt_reddit_icon'] != 'en/reddit/tt-reddit.png' &&
	$new_options['tt_reddit_icon'] != 'noicon')
		$new_options['tt_reddit_link_text'] = '[BLANK]';
	if($new_options['tt_slashdot_link_text'] ==
	__('Post to Slashdot', 'tweet-this') &&
	tt_option('tt_slashdot_link_text') ==
	__('Post to Slashdot', 'tweet-this') &&
	$new_options['tt_slashdot_icon'] != tt_option('tt_slashdot_icon') &&
	$new_options['tt_slashdot_icon'] != 'en/slashdot/tt-slashdot.png' &&
	$new_options['tt_slashdot_icon'] != 'noicon')
		$new_options['tt_slashdot_link_text'] = '[BLANK]';
	if($new_options['tt_squidoo_link_text'] ==
	__('Post to Squidoo', 'tweet-this') &&
	tt_option('tt_squidoo_link_text') ==
	__('Post to Squidoo', 'tweet-this') &&
	$new_options['tt_squidoo_icon'] != tt_option('tt_squidoo_icon') &&
	$new_options['tt_squidoo_icon'] != 'en/squidoo/tt-squidoo.png' &&
	$new_options['tt_squidoo_icon'] != 'noicon')
		$new_options['tt_squidoo_link_text'] = '[BLANK]';
	if($new_options['tt_su_link_text'] ==
	__('Stumble This Post', 'tweet-this') &&
	tt_option('tt_su_link_text') ==
	__('Stumble This Post', 'tweet-this') &&
	$new_options['tt_su_icon'] != tt_option('tt_su_icon') &&
	$new_options['tt_su_icon'] != 'en/su/tt-su.png' &&
	$new_options['tt_su_icon'] != 'noicon')
		$new_options['tt_su_link_text'] = '[BLANK]';
	if($new_options['tt_technorati_link_text'] ==
	__('Post to Technorati', 'tweet-this') &&
	tt_option('tt_technorati_link_text') ==
	__('Post to Technorati', 'tweet-this') &&
	$new_options['tt_technorati_icon'] !=
	tt_option('tt_technorati_icon') &&
	$new_options['tt_technorati_icon'] !=
	'en/technorati/tt-technorati.png' &&
	$new_options['tt_technorati_icon'] != 'noicon')
		$new_options['tt_technorati_link_text'] = '[BLANK]';
	$count = 0;
	foreach($new_options as $option => $value)
		if(tt_option($option) != $value) $count++;
	update_option('tweet_this_settings', $new_options);
	echo	'<br /><div id="message" class="updated fade"><p>' .
		__("<strong>$count</strong> Tweet This option(s) " .
		"saved.", 'tweet-this') . $flush . '</p></div>';
}


/**
 * Outputs image selection for each service in the options.
 * @package tweet-this
 * @since 1.3
 */
function tt_image_selection($s) {
	// TODO: Use a loop here.
	$l = WP_PLUGIN_URL . '/' . TT_DIR . '/icons/';
	$c = ' checked="checked"';
	$z = '.png" /></label> <input type="radio" name="tt[tt_';
	echo	'<p><input type="radio" name="tt[tt_' . $s . '_icon]" id="' .
		$s . '-01" value="en/' . $s . '/tt-' . $s . '.png"';
	if(tt_option('tt_' . $s . '_icon') == 'en/' . $s . '/tt-' . $s . '.png'
		|| tt_option('tt_' . $s . '_icon') == '')
			echo	$c;
	echo	' /> <label for="' . $s . '-01"><img src="' . $l . 'en/' . $s .
		'/tt-' . $s . '.png" alt="en/' . $s . '/tt-' . $s . $z . $s .
		'_icon]" id="' . $s . '-02" value="en/' . $s . '/tt-' . $s .
		'-big1.png"';
	if(tt_option('tt_' . $s . '_icon') == 'en/' . $s . '/tt-' . $s .
		'-big1.png') echo $c;
	echo	' /> <label for="' . $s . '-02"><img src="' . $l . 'en/' . $s .
		'/tt-' . $s . '-big1.png" alt="en/' . $s . '/tt-' . $s .
		'-big1' . $z . $s . '_icon]" id="' . $s . '-03" value="en/' .
		$s . '/tt-' . $s . '-big2.png"';
	if(tt_option('tt_' . $s . '_icon') == 'en/' . $s . '/tt-' . $s .
		'-big2.png') echo $c;
	echo	' /> <label for="' . $s . '-03"><img src="' . $l . 'en/' . $s .
		'/tt-' . $s . '-big2.png" alt="en/' . $s . '/tt-' . $s .
		'-big2' . $z . $s . '_icon]" id="' . $s . '-04" value="en/' .
		$s . '/tt-' . $s . '-big3.png"';
	if(tt_option('tt_' . $s . '_icon') == 'en/' . $s . '/tt-' . $s .
		'-big3.png') echo $c;
	echo	' /> <label for="' . $s . '-04"><img src="' . $l . 'en/' . $s .
		'/tt-' . $s . '-big3.png" alt="en/' . $s . '/tt-' . $s .
		'-big3' . $z . $s . '_icon]" id="' . $s . '-05" value="en/' .
		$s . '/tt-' . $s . '-big4.png"';
	if(tt_option('tt_' . $s . '_icon') == 'en/' . $s . '/tt-' . $s .
		'-big4.png') echo $c;
	echo	' /> <label for="' . $s . '-05"><img src="' . $l . 'en/' . $s .
		'/tt-' . $s . '-big4.png" alt="en/' . $s . '/tt-' . $s .
		'-big4' . $z . $s . '_icon]" id="' . $s . '-06" value="en/' .
		$s . '/tt-' . $s . '-micro3.png"';
	if(tt_option('tt_' . $s . '_icon') == 'en/' . $s . '/tt-' . $s .
		'-micro3.png') echo $c;
	echo	' /> <label for="' . $s . '-06"><img src="' . $l . 'en/' . $s .
		'/tt-' . $s . '-micro3.png" alt="en/' . $s . '/tt-' . $s .
		'-micro3' . $z . $s . '_icon]" id="' . $s . '-07" value="en/' .
		$s . '/tt-' . $s . '-micro4.png"';
	if(tt_option('tt_' . $s . '_icon') == 'en/' . $s . '/tt-' . $s .
		'-micro4.png') echo $c;
	echo	' /> <label for="' . $s . '-07"><img src="' . $l . 'en/' . $s .
		'/tt-' . $s . '-micro4.png" alt="en/' . $s . '/tt-' . $s .
		'-micro4' . $z . $s . '_icon]" id="' . $s . '-08" ' .
		'value="noicon"';
	if(tt_option('tt_' . $s . '_icon') == 'noicon') echo $c;
	echo	' /> <label for="' . $s . '-08">' .
		__('None', 'tweet-this') . '</label></p>';
	if($s == 'twitter') {
		echo	'<p><input type="radio" name="tt[tt_twitter_icon]" ' .
			'id="twitter-09" value="en/twitter/tt-twitter2.png"';
		if(tt_option('tt_twitter_icon') ==
			'en/twitter/tt-twitter2.png') echo $c;
		echo	' /> <label for="twitter-09"><img src="' . $l .
			'en/twitter/tt-twitter2.png" alt="en/twitter/' .
			'tt-twitter2' . $z . 'twitter_icon]" id="twitter-10"' .
			' value="de/twitter/tt-twitter-big1.png"';
		if(tt_option('tt_twitter_icon') ==
			'de/twitter/tt-twitter-big1.png') echo $c;
		echo	' /> <label for="twitter-10"><img src="' . $l .
			'de/twitter/tt-twitter-big1.png" alt="de/twitter/' .
			'tt-twitter-big1' . $z . 'twitter_icon]" id=' .
			'"twitter-11" value="de/twitter/tt-twitter-big2.png"';
		if(tt_option('tt_twitter_icon') ==
			'de/twitter/tt-twitter-big2.png') echo $c;
		echo	' /> <label for="twitter-11"><img src="' . $l .
			'de/twitter/tt-twitter-big2.png" alt="de/twitter/' .
			'tt-twitter-big2' . $z . 'twitter_icon]" id=' .
			'"twitter-12" value="de/twitter/tt-twitter-big3.png"';
		if(tt_option('tt_twitter_icon') ==
			'de/twitter/tt-twitter-big3.png') echo $c;
		echo	' /> <label for="twitter-12"><img src="' . $l .
			'de/twitter/tt-twitter-big3.png" alt="de/twitter/' .
			'tt-twitter-big3' . $z . 'twitter_icon]" id=' .
			'"twitter-13" value="de/twitter/tt-twitter-big4.png"';
		if(tt_option('tt_twitter_icon') ==
			'de/twitter/tt-twitter-big4.png') echo $c;
		echo	' /> <label for="twitter-13"><img src="' . $l .
			'de/twitter/tt-twitter-big4.png" alt="de/twitter/' .
			'tt-twitter-big4' . $z . 'twitter_icon]" id="twitter' .
			'-14" value="de/twitter/tt-twitter-micro3.png"';
		if(tt_option('tt_twitter_icon') ==
			'de/twitter/tt-twitter-micro3.png') echo $c;
		echo	' /> <label for="twitter-14"><img src="' . $l .
			'de/twitter/tt-twitter-micro3.png" alt="de/twitter/' .
			'tt-twitter-micro3' . $z . 'twitter_icon]" id="' .
			'twitter-15" value="de/twitter/tt-twitter-micro4.png"';
		if(tt_option('tt_twitter_icon') ==
			'de/twitter/tt-twitter-micro4.png') echo $c;
		echo	' /> <label for="twitter-15"><img src="' . $l .
			'de/twitter/tt-twitter-micro4.png" alt="de/twitter/' .
			'tt-twitter-micro4.png" /></label></p>';
		echo	'<p><input type="radio" name="tt[tt_twitter_icon]" ' .
			'id="twitter-16" value="en/twitter/tt-twitter3.png"';
		if(tt_option('tt_twitter_icon') ==
			'en/twitter/tt-twitter3.png') echo $c;
		echo	' /> <label for="twitter-16"><img src="' . $l .
			'en/twitter/tt-twitter3.png" alt="en/twitter/' .
			'tt-twitter3' . $z . 'twitter_icon]" id="twitter-17"' .
			' value="en/twitter/tt-twitter4.png"';
		if(tt_option('tt_twitter_icon') ==
			'en/twitter/tt-twitter4.png') echo $c;
		echo	' /> <label for="twitter-17"><img src="' . $l .
			'en/twitter/tt-twitter4.png" alt="en/twitter/' .
			'tt-twitter4' . $z . 'twitter_icon]" id="twitter-21"' .
			' value="en/twitter/tt-twitter5.png"';
		if(tt_option('tt_twitter_icon') ==
			'en/twitter/tt-twitter5.png') echo $c;
		echo	' /> <label for="twitter-21"><img src="' . $l .
			'en/twitter/tt-twitter5.png" alt="en/twitter/' .
			'tt-twitter5' . $z . 'twitter_icon]" id="twitter-22"' .
			' value="en/twitter/tt-twitter6.png"';
		if(tt_option('tt_twitter_icon') ==
			'en/twitter/tt-twitter6.png') echo $c;
		echo	' /> <label for="twitter-22"><img src="' . $l .
			'en/twitter/tt-twitter6.png" alt="en/twitter/' .
			'tt-twitter6' . $z . 'twitter_icon]" id="twitter-18"' .
			' value="en/twitter/tt-twitter-micro1.png"';
		if(tt_option('tt_twitter_icon') ==
			'en/twitter/tt-twitter-micro1.png') echo $c;
		echo	' /> <label for="twitter-18"><img src="' . $l .
			'en/twitter/tt-twitter-micro1.png" alt="en/twitter/' .
			'tt-twitter-micro1' . $z . 'twitter_icon]" id="' .
			'twitter-19" value="en/twitter/tt-twitter-micro2.png"';
		if(tt_option('tt_twitter_icon') ==
			'en/twitter/tt-twitter-micro2.png') echo $c;
		echo	' /> <label for="twitter-19"><img src="' . $l .
			'en/twitter/tt-twitter-micro2.png" alt="en/twitter/' .
			'tt-twitter-micro2' . $z . 'twitter_icon]" ' .
			'id="twitter-20" value="textbox"';
		if(tt_option('tt_twitter_icon') == 'textbox')
			echo	$c;
		echo	' /> <label for="twitter-20">' . __('Editable text ' .
			'box', 'tweet-this') . '</label> <label class="in">[' .
			__('Size', 'tweet-this') . ': <input type="text" ' .
			'name="tt[tt_textbox_size]" id="tt[tt_textbox_size]"' .
			' size="3" maxlength="4" value="';
		if(tt_option('tt_textbox_size') == '')
			echo	'60';
		else echo	tt_option('tt_textbox_size');
		echo	'" />]</label></p>';}
}


/**
 * Helps construct the URL service drop-down in the options.
 * @package tweet-this
 * @since 1.3.3
 */
function tt_url_service($id = 'local', $title = 'Local', $special = '') {
	if($id == 'snurl') $id = 'snipurl';
	if(strtolower($title) == 'snurl.com') $title = 'SnipURL.com';
	if($special == 'admin' || $special == 'admindefault')
		$service = tt_option('tt_admin_url_service');
	else $service = tt_option('tt_url_service');
	echo	'<option value="' . $id . '"';
	if(str_replace('snurl', 'snipurl', $service) == $id
		|| (($special == 'default' || $special == 'admindefault') &&
		$service == '')) echo ' selected="selected"';
	echo	'>' . $title . ' ';
	if($id == 'local') {
		$local = str_replace('https://', 'http://',
			get_bloginfo('url')) . '/?p=1234';
		if(tt_option('tt_url_www') == 'true')
			$local = str_replace('http://', 'www.',
				str_replace('http://www.', 'www.', $local));
		echo	$local . ' ';}
	printf(__('(%1$s Characters)', 'tweet-this'),
		get_tt_url_service_len($id));
	if($id == 'local') echo ' &nbsp;';
	echo	'</option>';
}


/**
 * Gets the length of a URL service.
 * @package tweet-this
 * @since 1.7.4
 */
function get_tt_url_service_len($service = '') {
	if($service == 'snurl') $service = 'snipurl';
	if($service == '' || $service == 'same')
		$service = tt_option('tt_url_service');
	if($service == '' || $service == 'same') $service = 'local';
	$len = constant('TT_' . strtoupper(str_replace('.', '',
		$service)) . '_LEN');
	if(tt_option('tt_url_www') == 'true' && $service != 'tweetburner')
		$len -= 3;
	if($service == 'adjix' && tt_option('tt_ad_vu') != 'false')
		$len -= 4;
	elseif($service == 'bit.ly' && tt_option('tt_j_mp') == 'true')
		$len -= 2;
	elseif($service == 'snipurl' && (tt_option('tt_snipurl_domain') ==
		'snipr.com' || tt_option('tt_snipurl_domain') == 'snurl.com'))
		$len -= 2;
	elseif($service == 'snipurl' && (tt_option('tt_snipurl_domain') ==
		'sn.im' || tt_option('tt_snipurl_domain') == 'cl.lk'))
		$len -= 6;
	return $len;
}


/**
 * Echoes the above function for convenience.
 * @package tweet-this
 * @since 1.6
 * @notes 1.7.4: Behavior changed from return to echo.
 */
function tt_url_service_len($service = '') {
	echo get_tt_url_service_len($service);
}


/**
 * Connects to Twitter using OAuth.
 * Based in part on: http://wordpress.org/extend/plugins/twitter-tools/
 * Twitter Tools  Copyright 2008 - 2010  Alex King  http://alexking.org/
 * @package tweet-this
 * @since 1.7
 */
function tt_oauth_connection($key = '', $secret = '', $token = '',
	$token_sec = '') {
	if($key == '') $key = tt_option('tt_app_consumer_key');
	if($secret == '') $secret = tt_option('tt_app_consumer_secret');
	if($token == '') $token = tt_option('tt_oauth_token');
	if($token_sec == '') $token_sec = tt_option('tt_oauth_token_secret');
	$key = trim($key); $secret = trim($secret);
	$token = trim($token); $token_sec = trim($token_sec);
	if($key != '' && $secret != '' && $token != '' && $token_sec != '') {
		if(!class_exists('TwitterOAuth')) {
			require_once(TT_OAUTH);
			$connection = new TwitterOAuth($key, $secret, $token,
				$token_sec);}
		else {	require_once(TT_OAUTH_SPECIAL);
			$connection = new TweetThisTwitterOAuth($key, $secret,
				$token, $token_sec);}
		$connection->useragent = 'Tweet This ' .
			'http://richardxthripp.thripp.com/tweet-this/';
		return $connection;}
	else return false;
}


/**
 * Used in the options form to test the Twitter OAuth keys.
 * Based in part on: http://wordpress.org/extend/plugins/twitter-tools/
 * Twitter Tools  Copyright 2008 - 2010  Alex King  http://alexking.org/
 * @package tweet-this
 * @since 1.7
 */
function tt_oauth_test($key = '', $secret = '', $token = '', $token_sec = '') {
	if($key != '' && $secret != '' && $token != '' && $token_sec != '') {
		$connection =	tt_oauth_connection($key, $secret, $token,
				$token_sec);
		$data = 	$connection->get('account/verify_credentials');
		if($connection->http_code == '200') {
			$data = json_decode($data);
			return	' <strong><font color="green">' .
				__('Authentication succeeded, please save.',
				'tweet-this') . '</font></strong>';}
		else return	' <strong><font color="red">' .
				__('Authentication failed, make sure your ' .
				'keys are entered correctly.', 'tweet-this') .
				'</font></strong>';}
	else return	' <strong><font color="red"> ' . __('Authentication ' .
			'failed, please copy and paste all four keys.',
			'tweet-this') . '</font></strong>';
}


/**
 * Delimits URLs by adding a space on each side.
 * Based on: http://github.com/jmrware/LinkifyURL
 * LinkifyURL  Copyright 2010  Jeff Roberson  http://jmrware.com/
 * MIT License: http://www.opensource.org/licenses/mit-license.php
 * @package tweet-this
 * @since 1.7.4
 */
function tt_delimit_urls($text, $delimiter = ' ') {
    $url_pattern = '/# Match http & ftp URL that is not already linkified.
      # Alternative 1: URL delimited by (parentheses).
      (\()                     # $1  "(" start delimiter.
      ((?:ht|f)tps?:\/\/[a-z0-9\-._~!$&\'()*+,;=:\/?#[\]@%]+)  # $2: URL.
      (\))                     # $3: ")" end delimiter.
    | # Alternative 2: URL delimited by [square brackets].
      (\[)                     # $4: "[" start delimiter.
      ((?:ht|f)tps?:\/\/[a-z0-9\-._~!$&\'()*+,;=:\/?#[\]@%]+)  # $5: URL.
      (\])                     # $6: "]" end delimiter.
    | # Alternative 3: URL delimited by {curly braces}.
      (\{)                     # $7: "{" start delimiter.
      ((?:ht|f)tps?:\/\/[a-z0-9\-._~!$&\'()*+,;=:\/?#[\]@%]+)  # $8: URL.
      (\})                     # $9: "}" end delimiter.
    | # Alternative 4: URL delimited by <angle brackets>.
      (<|&(?:lt|\#60|\#x3c);)  # $10: "<" start delimiter (or HTML entity).
      ((?:ht|f)tps?:\/\/[a-z0-9\-._~!$&\'()*+,;=:\/?#[\]@%]+)  # $11: URL.
      (>|&(?:gt|\#62|\#x3e);)  # $12: ">" end delimiter (or HTML entity).
    | # Alternative 5: URL not delimited by (), [], {} or <>.
      (                        # $13: Prefix proving URL not already linked.
        (?: ^                  # Can be a beginning of line or string, or
        | [^=\s\'"\]]          # a non-"=", non-quote, non-"]", followed by
        ) \s*[\'"]?            # optional whitespace and optional quote;
      | [^=\s]\s+              # or a non-equals sign followed by whitespace.
      )                        # End $13. Non-prelinkified-proof prefix.
      ( \b                     # $14: Other non-delimited URL.
        (?:ht|f)tps?:\/\/      # Required http, https, ftp or ftps prefix.
        [a-z0-9\-._~!$\'()*+,;=:\/?#[\]@%]+ # All URI chars except "&".
        (?:                    # Either on a "&" or at the end of URI.
          (?!                  # Allow a "&" char only if not start of an...
            &(?:gt|\#0*62|\#x0*3e);                  # HTML ">" entity, or
          | &(?:amp|apos|quot|\#0*3[49]|\#x0*2[27]); # a [&\'"] entity if
            [.!&\',:?;]?        # followed by optional punctuation then
            (?:[^a-z0-9\-._~!$&\'()*+,;=:\/?#[\]@%]|$) # a non-URI char or EOS.
          ) &                  # If neg-assertion true, match "&" (special).
          [a-z0-9\-._~!$\'()*+,;=:\/?#[\]@%]* # More non-& URI chars.
        )*                     # Unroll-the-loop (special normal*)*.
        [a-z0-9\-_~$()*+=\/#[\]@%]  # Last char can\'t be [.!&\',;:?]
      )                        # End $14. Other non-delimited URL.
    /imx';
    $url_replace =	'$1$4$7$10$13' . $delimiter . '$2$5$8$11$14' .
			$delimiter . '$3$6$9$12';
    return	trim(preg_replace($url_pattern, $url_replace, $text));
}


/**
 * Shortens URLs in new tweets.
 * Based on: http://www.phpclasses.org/browse/package/6114.html
 * Ext-Conv-Links  Copyright 2010  Muhammad Arfeen  http://arfeen.net/
 * @package tweet-this
 * @since 1.7.3
 */
class tt_shorten_urls {
	function tt_shorten_urls($bitlyLogin, $bitlyAPIKey) {
		$this->_bitlyLogin = $bitlyLogin;
		$this->_bitlyAPIKey = $bitlyAPIKey;}
	function ExtractAndConvert($text = '') {
		$text = trim(tt_delimit_urls($text, ' '));
		$hyperlinksArray = array();
		preg_match_all('(((f|ht){1}(tp://|tps://))' .
			'[-a-zA-Z0-9@:%_+.~#?&//=]+)', $text,
			$hyperlinksArray);
		for($i = 0; $i < count($hyperlinksArray[0]); $i++) {
			$ShortLink = $this->_GetSortenLinkViaAPI(
				$hyperlinksArray[0][$i]);
			if(tt_option('tt_j_mp') == 'true')
				$ShortLink = str_replace('http://bit.ly/',
				'http://j.mp/', $ShortLink);
			$text = str_replace($hyperlinksArray[0][$i],
				$ShortLink, $text);} return $text;}
	function _GetSortenLinkViaAPI($URL) {
		$service = tt_option('tt_admin_url_service');
		if($service == 'same') $service = tt_option('tt_url_service');
		if($service == '' || $service == 'same' || $service == 'local')
			$service = 'bit.ly';
		if($this->_bitlyLogin != '' && $this->_bitlyAPIKey != '' &&
		$service == 'bit.ly')
			$BitlyXML = tt_read_file('http://api.bit.ly/shorten?' .
			'version=2.0.1&longUrl=' . urlencode(str_replace(
			't9WGb5', '_', $URL)) . '&login=' .
			$this->_bitlyLogin . '&apiKey=' . $this->_bitlyAPIKey .
			'&format=xml');
		else {	$geturl = get_tweet_this_short_url(str_replace('_',
				't9WGb5', $URL), '', true, true, '');
			$BitlyXML = '<bitly><errorCode>0</errorCode>' .
			'<errorMessage></errorMessage><results><nodeKeyVal>' .
			'<shortKeywordUrl></shortKeywordUrl><hash>null' .
			'</hash><userHash>null</userHash><nodeKey><![CDATA[' .
			'http://www.google.com/]]></nodeKey><shortUrl>' .
			$geturl . '</shortUrl><shortCNAMEUrl>' . $geturl .
			'</shortCNAMEUrl></nodeKeyVal></results><statusCode>' .
			'OK</statusCode></bitly>';}
		$XMLtoArray = $this->_ConvXMLtoArray($BitlyXML);
		if(isset($XMLtoArray['bitly']['results']['nodeKeyVal']
			['shortUrl']['_value_'])) return $XMLtoArray['bitly']
			['results']['nodeKeyVal']['shortUrl']['_value_'];}
	function _ConvXMLtoArray($contents, $get_attributes = 1) {
		if(!$contents || !function_exists('xml_parser_create'))
			return array();
		$parser = xml_parser_create();
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
		xml_parse_into_struct($parser, $contents, $xml_values);
		xml_parser_free($parser); if(!$xml_values) return;
		$xml_array = array(); $parents = array();
		$opened_tags = array(); $arr = array(); $current = &$xml_array;
		foreach($xml_values as $data) {unset($attributes, $value);
			extract($data); $result = ''; if($get_attributes) {
				$result = array();
				if(isset($value)) $result['_value_'] = $value;
				if(isset($attributes)) {
					foreach($attributes as $attr => $val)
					if($get_attributes == 1)
					$result['_attr_'][$attr] = $val;}}
			elseif(isset($value)) $result = $value;
			if($type == 'open') {
				$parent[$level - 1] = &$current;
				if(!is_array($current) || (!in_array($tag,
				array_keys($current)))) {
					$current[$tag] = $result;
					$current = &$current[$tag];} else {
					if(isset($current[$tag][0]))
					   array_push($current[$tag], $result);
					else $current[$tag] =
					   array($current[$tag], $result);
					$last = count($current[$tag]) - 1;
					$current = &$current[$tag][$last];}}
			elseif($type == 'complete') {if(!isset($current[$tag]))
				$current[$tag] = $result; else {
				if((is_array($current[$tag]) && $get_attributes
				== 0) || (isset($current[$tag][0]) &&
				is_array($current[$tag][0]) && $get_attributes
				== 1)) array_push($current[$tag], $result);
				else $current[$tag] =
					array($current[$tag], $result);}}
		elseif($type == 'close') $current = &$parent[$level - 1];}
	return($xml_array);}
}


/**
 * Backward-compatible function to enqueue jQuery.
 * @package tweet-this
 * @since 1.7.7
 */
function get_tt_enqueue_script($script = 'jquery') {
	if($script == 'jquery') {
		if(function_exists('wp_enqueue_script') &&
		version_compare($GLOBALS['wp_version'], '2.6', '>='))
			wp_enqueue_script('jquery');
		elseif(file_exists(TT_JQUERY_ABSPATH))
			return '<script type="text/javascript" src="' .
			constant('TT_JQUERY_URLPATH') . '"></script>';
		else	return '<script type="text/javascript" src="http://' .
			'code.jquery.com/jquery-1.4.2.min.js"></script>';}
	elseif($script != '' && function_exists('wp_enqueue_script'))
		wp_enqueue_script($script);
	else return;
}


/**
 * Echoes the above function for convenience.
 * @package tweet-this
 * @since 1.7.7
 */
function tt_enqueue_script($script = 'jquery') {
	echo get_tt_enqueue_script($script);
}


/**
 * Handles key verification, forwarding, and new tweets.
 * Based in part on: http://wordpress.org/extend/plugins/twitter-tools/
 * Twitter Tools  Copyright 2008 - 2010  Alex King  http://alexking.org/
 * @package tweet-this
 * @since 1.6
 */
function tt_request_handler() {
	if(isset($_REQUEST['ttaction'])) switch($_REQUEST['ttaction']) {
	case 'tt_oauth_test':
		$test = @tt_oauth_test($_POST['tt_app_consumer_key'],
			$_POST['tt_app_consumer_secret'],
			$_POST['tt_oauth_token'],
			$_POST['tt_oauth_token_secret']);
		die(__($test, 'tweet-this'));
	break;
	case 'tt_twitter_box':
		$link =	'http://twitter.com/home/?status=' . urlencode(
			@html_entity_decode(stripslashes(
			$_REQUEST['tt_twitter_box_text']), ENT_COMPAT,
			'UTF-8'));
		die('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 ' .
		'Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/' .
		'xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/' .
		'1999/xhtml" xml:lang="en" lang="en"><head><title>' .
		__('Sending you to Twitter...', 'tweet-this') . '</title>' .
		'<meta http-equiv="content-type" content="text/html; ' .
		'charset=utf-8" /><meta http-equiv="refresh" content="0;url=' .
		$link . '" /></head><body><p>' . __('Sending you to Twitter' .
		'...', 'tweet-this') . '<a href="' . $link . '">' .
		__('Click here if nothing happens.', 'tweet-this') .
		'</a></p></body></html>');
	break;
	case 'tt_write_tweet':
		if($_POST['tt_app_consumer_secret'] !=
		tt_option('tt_app_consumer_secret') &&
		trim(tt_option('tt_app_consumer_secret')) != '')
			die('&nbsp;&nbsp;<strong><font color="red">' .
			__('Cannot authenticate you as WordPress admin.',
			'tweet-this') . '</font></strong>');
		elseif(trim($_POST['tt_tweet_text']) == '')
			die('&nbsp;&nbsp;<strong><font color="red">' .
			__('Cannot publish a blank tweet.', 'tweet-this') .
			'</font></strong>');
		elseif(tt_option('tt_app_consumer_key') != '' &&
		tt_option('tt_app_consumer_secret') != '' &&
		tt_option('tt_oauth_token') != '' &&
		tt_option('tt_oauth_token_secret') != '') {
			$ECLObj = new tt_shorten_urls(
				tt_option('tt_bitly_username'),
				tt_option('tt_bitly_api_key'));
			$status = stripslashes(trim(str_replace(array("/^\s+/",
				"/\s{2,}/", "/\s+\$/"), array('', ' ', ''),
				$_POST['tt_tweet_text'])));
			if(strlen($status) > 140)
				$status = $ECLObj->ExtractAndConvert($status);
			$connection = tt_oauth_connection();
			$connection->decode_json = true;
			$data = $connection->post(TT_API_POST_STATUS,
				array('status' => $status, 'source' =>
				'tweetthis'));
			if($connection->http_code == '200')
				die('&nbsp;&nbsp;<strong><font color=' .
				'"green">' . __('Tweet published to Twitter ' .
				'successfully.', 'tweet-this') . ' (<a href=' .
				'"http://twitter.com/' . $data->user->screen_name .
				'/status/' . $data->id . '" target="_blank">' .
				$data->id . '</a>)</font></strong>');
			else die(printf('&nbsp;&nbsp;<strong><font color=' .
			'"red">' . __('Tweet failed: Twitter returned HTTP ' .
			'%1$s.', 'tweet-this') . '</font></strong>',
			$connection->http_code));}
	else	die('&nbsp;&nbsp;<strong><font color="red">' .
		__('Tweet failed: OAuth key(s) missing.', 'tweet-this') .
		'</font></strong>');
	break;
	case 'tt_shorten_urls':
		$ECLObj = new tt_shorten_urls(tt_option('tt_bitly_username'),
			tt_option('tt_bitly_api_key'));
		$status = stripslashes(trim(preg_replace("/(\040)+/", " ",
			$_POST['tt_tweet_text'])));
		if($_POST['tt_app_consumer_secret'] !=
		tt_option('tt_app_consumer_secret') &&
		trim(tt_option('tt_app_consumer_secret')) != '')
			die('&nbsp;&nbsp;<strong><font color="red">' .
			__('Cannot authenticate you as a WordPress admin.',
			'tweet-this') . '</font></strong>');
		elseif($status == '')
			die('&nbsp;&nbsp;<strong><font color="red">' .
			__('No tweet text to preview.', 'tweet-this') .
			'</font></strong>');
		else {	if(strlen($status) > 140) {
			$status = $ECLObj->ExtractAndConvert(str_replace('_',
				't9WGb5', $status));
				$lenmsg = '.'; $lenbr = '';}
			else {	$lenmsg = __(', or more if your tweet ' .
				'contains URLs.', 'tweet-this');
				$lenbr = '<br />';}
			if(strlen($status) < 141) {
				$count = '<strong><font color="green">' .
				strlen($status) . '</font></strong>';
				$remaining = '<strong><font color="green">' .
				(140 - strlen($status)) . '</font></strong>';
				$message = $lenbr . __('You can add ',
				'tweet-this') . $remaining .
				__(' character(s)', 'tweet-this') . $lenmsg;}
				else {$count = '<strong><font color="red">' .
				strlen($status) . '</font></strong>';
				$remaining = '<strong>' . (strlen($status) -
				140) . '</strong>';
				$message = '<font color="red">' .
				__('You must remove ', 'tweet-this') .
				$remaining . __(' character(s).',
				'tweet-this') . '</font>';}
		die('<div id="tt_tweet_preview_details" style="border:2px ' .
		'solid #4a4a4a;background-color:#f5f5f5;padding:0 10px 0 ' .
		'10px;margin:10px 0 10px 6px;width:750px;"><p>' .
		__('Your final tweet will contain ', 'tweet-this') . $count .
		__(' character(s).', 'tweet-this') . ' ' . $message .
		'</p></div><div id="tt_tweet_preview" style="border:2px ' .
		'solid #007f00;background-color:#e8ffe8;padding:0 10px 0 ' .
		'10px;margin-left:6px;width:750px;"><p>' .
		htmlentities($status) . '</p></div>');}
	break;
	case 'tt_schedule_tweet':
		$ECLObj = new tt_shorten_urls(tt_option('tt_bitly_username'),
			tt_option('tt_bitly_api_key'));
		$status = stripslashes(trim(str_replace(array("/^\s+/",
			"/\s{2,}/", "/\s+\$/"), array('', ' ', ''),
			$_POST['tt_tweet_text'])));
		$date = trim($_POST['tt_schedule_date']);
		if(strlen($date) == 10) $date .= ' 00:00:00';
		if(strlen($status) > 140)
			$status = $ECLObj->ExtractAndConvert($status);
		if($_POST['tt_app_consumer_secret'] !=
		tt_option('tt_app_consumer_secret') &&
		trim(tt_option('tt_app_consumer_secret')) != '')
			die('&nbsp;&nbsp;<strong><font color="red">' .
			__('Cannot authenticate you as a WordPress admin.',
			'tweet-this') . '</font></strong>');
		elseif($status == '')
			die('&nbsp;&nbsp;<strong><font color="red">' .
			__('No tweet text to schedule.', 'tweet-this') .
			'</font></strong>');
		elseif(strlen($status) > 140)
			die('&nbsp;&nbsp;<strong><font color="red">' .
			__('Tweet not scheduled because it is <strong>',
			'tweet-this') . strlen($status) . __('</strong> ' .
			'characters.', 'tweet-this') . '</font></strong>');
		elseif(time() > strtotime($date))
			die('&nbsp;&nbsp;<strong><font color="red">' .
			__('Tweet not scheduled because provided date is ' .
			'invalid or in the past.', 'tweet-this') .
			'</font></strong>');
		else {	global $wpdb;
			if(!get_option('tweet_this_db_version')) {
				tt_install_tables(); $table = 'true';}
			if($table == 'true') $table_text = __('Created ' .
				'database table `<strong>', 'tweet-this') .
				$wpdb->rxtt . __('</strong>` successfully.',
				'tweet-this') . '</font></p>' .
				'<p><font color="green">';
			global $user_ID;
			get_currentuserinfo();
			$dupe = $wpdb->get_row("SELECT ID, tt_modified FROM
				$wpdb->rxtt WHERE tt_date = '$date'");
			if($dupe->ID != '') {
				die('<div id="tt_schedule_details" style=' .
				'"border:2px solid #7f0000;background-color:' .
				'#ffe8e8;padding:0 10px 0 10px;margin:10px ' .
				'0 10px 6px;width:750px;"><p><font color=' .
				'"red">' . __('Tweet <strong>#',
				'tweet-this') . $dupe->ID . __('</strong>, ' .
				'added <strong>', 'tweet-this') .
				$dupe->tt_modified . __('</strong>, is also ' .
				'scheduled for <strong>', 'tweet-this') .
				$date . __('</strong>. Please change dates.',
				'tweet-this') . '</font></p></div>');}
			else {	$current_date = date("Y-m-d H:i:s");
				if(version_compare($GLOBALS['wp_version'],
					'2.0', '<')) $basefile = 'post.php';
				else	$basefile = 'edit.php';
				$blog_url = get_bloginfo('url');
				$plugin_file = constant('TT_FILE_LOC');
				$status = addslashes(stripslashes($status));
				$wpdb->query("INSERT INTO $wpdb->rxtt
					(tt_author, tt_date, tt_content,
					tt_status, tt_modified, tt_response)
					VALUES ('$user_ID', '$date', '$status',
					'future', '$current_date', '0')");
				die('<div id="tt_schedule_details" style=' .
				'"border:2px solid #005e00;background-color:' .
				'#f4fff4;padding:0 10px 0 10px;margin:10px ' .
				'0 10px 6px;width:750px;"><p><font color=' .
				'"green">' . $table_text . __("Tweet " .
				"scheduled for <strong>$date</strong> " .
				"successfully. <a href='$blog_url/wp-admin/" .
				"$basefile$plugin_file'>Click here</a> to " .
				"refresh.", 'tweet-this') .
				'</font></p></div>');}}
	break;}
}


/**
 * Displays the options form under Settings > Tweet This.
 * @package tweet-this
 * @since 1.1
 */
function print_tt_form() {
	if(file_exists(TT_OPTIONS)) {
		tt_enqueue_script('jquery');
		require(TT_OPTIONS);}
	else echo	'<p>' . __('The file that constructs the options ' .
			'form, /tweet-this/lib/tt-options.php, is missing.',
			'tweet-this') . '</p>';
}


/**
 * Array of the default options for installing Tweet This.
 * @package tweet-this
 * @since 1.8
 */
function get_tt_default_options($add_options = '') {
	if($add_options == '' || strlen($add_options) < 100)
	$add_options = array('tt_30' => 'false', 'tt_url_service' => 'local',
		'tt_admin_url_service' => 'bit.ly', 'tt_alignment' => 'left',
		'tt_limit_to_single' => 'false', 'tt_limit_to_posts' =>
		'false', 'tt_url_www' => 'false', 'tt_footer' => 'false',
		'tt_ads' => 'false', 'tt_new_window' => 'true',
		'tt_nofollow' => 'true', 'tt_img_css_class' => 'nothumb',
		'tt_link_css_class' => 'tt', 'tt_css' =>
		'img.[IMG_CLASS]{border:0;}', 'tt_shortlink_filter' => 'true',
		'tt_adjix_api_key' => '', 'tt_ad_vu' => 'true', 'tt_j_mp' =>
		'false', 'tt_bitly_username' => '', 'tt_bitly_api_key' => '',
		'tt_snipurl_username' => '', 'tt_snipurl_api_key' => '',
		'tt_supr_username' => '', 'tt_supr_api_key' => '',
		'tt_snipurl_domain' => 'snipurl.com','tt_custom_url_service' =>
		'http://tinyurl.com/api-create.php?url=[LONGURL]',
		'tt_auto_tweet_display' => 'true',
		'tt_auto_tweet_display_pages' => 'true',
		'tt_auto_tweet' => 'false', 'tt_auto_tweet_pages' => 'false',
		'tt_auto_tweet_text' =>	__('New blog post', 'tweet-this') .
		': [TITLE] [URL]', 'tt_app_consumer_key' => '',
		'tt_app_consumer_secret' => '',	'tt_oauth_token' => '',
		'tt_oauth_token_secret' => '', 'tt_textbox_size' => '60',
		'tt_auto_display' => 'true', 'tt_tweet_text' =>
			'[TITLE] [URL]', 'tt_link_text' =>
			__('Tweet This Post', 'tweet-this'), 'tt_title_text' =>
			__('Post to Twitter', 'tweet-this'),
			'tt_twitter_icon' => 'en/twitter/tt-twitter.png',
		'tt_twitter_share' => 'false', 'tt_twitter_share_text' =>
			'[TITLE_SHARE]', 'tt_twitter_share_via' =>
			'tweetthisplugin', 'tt_twitter_share_rel' =>
			'richardxthripp,tweetthisplugin',
		'tt_plurk' => 'false', 'tt_plurk_text' => '[TITLE] [URL]',
			'tt_plurk_link_text' => __('Plurk This Post',
			'tweet-this'), 'tt_plurk_title_text' =>
			__('Post to Plurk', 'tweet-this'), 'tt_plurk_icon' =>
			'en/plurk/tt-plurk.png',
		'tt_bebo' => 'false', 'tt_bebo_link_text' => __('Post to Bebo',
			'tweet-this'), 'tt_bebo_title_text' =>
			__('Post to Bebo', 'tweet-this'), 'tt_bebo_icon' =>
			'en/bebo/tt-bebo.png',
		'tt_buzz' => 'false', 'tt_buzz_link_text' =>
			__('Buzz This Post', 'tweet-this'),
			'tt_buzz_title_text' => __('Post to Yahoo Buzz',
			'tweet-this'), 'tt_buzz_icon' => 'en/buzz/tt-buzz.png',
		'tt_delicious' => 'false', 'tt_delicious_link_text' =>
			__('Post to Delicious', 'tweet-this'),
			'tt_delicious_title_text' => __('Post to Delicious',
			'tweet-this'), 'tt_delicious_icon' =>
			'en/delicious/tt-delicious.png',
		'tt_digg' => 'false', 'tt_digg_link_text' =>
			__('Digg This Post', 'tweet-this'),
			'tt_digg_title_text' => __('Post to Digg',
			'tweet-this'), 'tt_digg_icon' => 'en/digg/tt-digg.png',
		'tt_diigo' => 'false', 'tt_diigo_link_text' =>
			__('Post to Diigo', 'tweet-this'),
			'tt_diigo_title_text' => __('Post to Diigo',
			'tweet-this'), 'tt_diigo_icon' =>
			'en/diigo/tt-diigo.png',
		'tt_facebook' => 'false', 'tt_facebook_link_text' =>
			__('Post to Facebook', 'tweet-this'),
			'tt_facebook_title_text' => __('Post to Facebook',
			'tweet-this'), 'tt_facebook_icon' =>
			'en/facebook/tt-facebook.png',
		'tt_ff' => 'false', 'tt_ff_link_text' =>
			__('Post to FriendFeed', 'tweet-this'),
			'tt_ff_title_text' => __('Post to FriendFeed',
			'tweet-this'), 'tt_ff_icon' => 'en/ff/tt-ff.png',
		'tt_gbuzz' => 'false', 'tt_gbuzz_link_text' =>
			__('Post to Google Buzz', 'tweet-this'),
			'tt_gbuzz_title_text' => __('Post to Google Buzz',
			'tweet-this'), 'tt_gbuzz_icon' =>
			'en/gbuzz/tt-gbuzz.png',
		'tt_gmail' => 'false', 'tt_gmail_link_text' =>
			__('Send Gmail', 'tweet-this'),
			'tt_gmail_title_text' => __('Send Gmail',
			'tweet-this'), 'tt_gmail_icon' =>
			'en/gmail/tt-gmail.png',
		'tt_linkedin' => 'false', 'tt_linkedin_link_text' =>
			__('Post to LinkedIn', 'tweet-this'),
			'tt_linkedin_title_text' => __('Post to LinkedIn',
			'tweet-this'), 'tt_linkedin_icon' =>
			'en/linkedin/tt-linkedin.png',
		'tt_mixx' => 'false', 'tt_mixx_link_text' =>
			__('Mixx This Post', 'tweet-this'),
			'tt_mixx_title_text' => __('Mixx This Post',
			'tweet-this'), 'tt_mixx_icon' => 'en/mixx/tt-mixx.png',
		'tt_myspace' => 'false', 'tt_myspace_link_text' =>
			__('Post to MySpace', 'tweet-this'),
			'tt_myspace_title_text' =>
			__('Post to MySpace', 'tweet-this'),
			'tt_myspace_icon' => 'en/myspace/tt-myspace.png',
		'tt_ping' => 'false', 'tt_ping_link_text' =>
			__('Ping This Post', 'tweet-this'),
			'tt_ping_title_text' => __('Post to Ping.fm',
			'tweet-this'), 'tt_ping_icon' => 'en/ping/tt-ping.png',
		'tt_reddit' => 'false', 'tt_reddit_link_text' =>
			__('Post to Reddit', 'tweet-this'),
			'tt_reddit_title_text' => __('Post to Reddit',
			'tweet-this'), 'tt_reddit_icon' =>
			'en/reddit/tt-reddit.png',
		'tt_slashdot' => 'false', 'tt_slashdot_link_text' =>
			__('Post to Slashdot', 'tweet-this'),
			'tt_slashdot_title_text' => __('Post to Slashdot',
			'tweet-this'), 'tt_slashdot_icon' =>
			'en/slashdot/tt-slashdot.png',
		'tt_squidoo' => 'false', 'tt_squidoo_link_text' =>
			__('Post to Squidoo', 'tweet-this'),
			'tt_squidoo_title_text' => __('Post to Squidoo',
			'tweet-this'), 'tt_squidoo_icon' =>
			'en/squidoo/tt-squidoo.png',
		'tt_su' => 'false', 'tt_su_link_text' =>
			__('Stumble This Post', 'tweet-this'),
			'tt_su_title_text' => __('Post to StumbleUpon',
			'tweet-this'), 'tt_su_icon' => 'en/su/tt-su.png',
		'tt_technorati' => 'false', 'tt_technorati_link_text' =>
			__('Post to Technorati', 'tweet-this'),
			'tt_technorati_title_text' => __('Post to Technorati',
			'tweet-this'), 'tt_technorati_icon' =>
			'en/technorati/tt-technorati.png',
		'tt_service_order' => 'twitter, plurk, bebo, buzz, ' .
			'delicious, digg, diigo, facebook, ff, gbuzz, ' .
			'gmail, linkedin, mixx, myspace, ping, reddit, ' .
			'slashdot, squidoo, su, technorati');
	return $add_options;
}


/**
 * Same as tt_default_options($add_options).
 * @package tweet-this
 * @since 1.8
 */
function tt_default_options($add_options = '') {
	return get_tt_default_options($add_options);
}


/**
 * Deletes deprecated options left over from old versions of Tweet This.
 * Usage:  delete_tt_deprecated_options('alpha beta');
 * @package tweet-this
 * @since 1.8
 */
function delete_tt_deprecated_options($confirm = '') {
	if($confirm == 'alpha beta') {
	delete_option('tweet_this_password');
	delete_option('tt_add_title');
	delete_option('tt_big_icon');
	delete_option('tt_icon');
	delete_option('tt_small_icon');
	delete_option('tweet_this_import_key');}
	else return false;
}


/**
 * Adds the default Tweet This options to the database.
 * @package tweet-this
 * @since 1.3
 */
function tweet_this_install($add_options = '') {
	if($add_options == '')
		$add_options = get_tt_default_options($add_options);
	foreach($add_options as $key => $value) {
		if($old = get_option($key)) {
			$add_options[$key] = $old; delete_option($key);}}
	if(version_compare($GLOBALS['wp_version'], '2.7', '<') ||
	version_compare(PHP_VERSION, '5.0.0', '<')) {
		$add_options['tt_auto_tweet_display'] = 'false';
		$add_options['tt_auto_tweet_display_pages'] = 'false';}
	if(!get_option('tweet_this_settings'))
		add_option('tweet_this_settings', $add_options);
	elseif(strlen(serialize(get_option('tweet_this_settings'))) < 100)
		update_option('tweet_this_settings', $add_options);
	delete_tt_deprecated_options('alpha beta');
}


/**
 * Completely removes Tweet This.
 * Usage:  tweet_this_uninstall('alpha omega');
 * @package tweet-this
 * @since 1.8
 */
function tweet_this_uninstall($confirm = '') {
	if($confirm == 'alpha omega') {
		delete_option('tweet_this_settings');
		delete_option('widget_tweet-this');
		delete_tt_deprecated_options('alpha beta');
		tt_uninstall_tables('delta kappa');
		global $wpdb;
		$wpdb->query("DELETE FROM $wpdb->postmeta WHERE
			(meta_key = 'tweet_this_url' OR
			meta_key = 'tt_auto_tweet' OR
			meta_key = 'tt_auto_tweet_text' OR
			meta_key = 'tt_tweeted' OR
			meta_key = 'tweet_this_hide')");
		if(function_exists('unregister_widget'))
			unregister_widget('Tweet_This_Widget');
		return true;}
	else return false;
}


/**
 * Installs the MySQL table for scheduled tweets.
 * @package tweet-this
 * @since 1.7.3
 */
function tt_install_tables() {
	global $wpdb; $cc = '';
	if(version_compare(mysql_get_server_info(), '4.1.0', '>=')) {
		if(!empty($wpdb->charset))
			$cc .= " DEFAULT CHARACTER SET $wpdb->charset";
		if(!empty($wpdb->collate)) $cc .= " COLLATE $wpdb->collate";}
	$result = $wpdb->query("
		CREATE TABLE IF NOT EXISTS `$wpdb->rxtt` (
		`ID` bigint(20) unsigned NOT NULL auto_increment,
		`tt_author` bigint(20) unsigned NOT NULL default '0',
		`tt_date` datetime NOT NULL default '0000-00-00 00:00:00',
		`tt_content` longtext NOT NULL,
		`tt_status` varchar(20) NOT NULL default 'future',
		`tt_modified` datetime NOT NULL default '0000-00-00 00:00:00',
		`tt_response` varchar(20) NOT NULL default '0',
		PRIMARY KEY  (`ID`)
		) $cc");
	update_option('tweet_this_db_version', TT_DB_VERSION);
	update_option('tweet_this_last_cron', (time() - 86400));
}


/**
 * Drops the MySQL table for scheduled tweets.
 * Usage:  tt_uninstall_tables('delta kappa');
 * @package tweet-this
 * @since 1.8
 */
function tt_uninstall_tables($confirm = '') {
	if($confirm == 'delta kappa') {
		global $wpdb;
		$wpdb->query("DROP TABLE IF EXISTS $wpdb->rxtt");
		delete_option('tweet_this_db_version');
		delete_option('tweet_this_last_cron');
		return true;}
	else return false;
}


/**
 * Inserts the Options and Write Tweet pages.
 * @package tweet-this
 * @since 1.1
 */
function tweet_this_add_options() {
	// WP 1.5 compatibility.
	if(version_compare($GLOBALS['wp_version'], '2.0', '<')) {
		$filename = __FILE__;
		$postfile = 'post.php';
		global $user_level;
		if($user_level >= 5) {
			$manage = true; $publish = true;}}
	else {	$filename = basename(__FILE__); $postfile = 'post-new.php';
		if(current_user_can('manage_options')) $manage = true;
		if(current_user_can('publish_posts')) $publish = true;}
	if($manage == true && function_exists('add_options_page'))
		add_options_page(__('Tweet This Options', 'tweet-this'),
			__('Tweet This', 'tweet-this'), 8,
			$filename, 'tweet_this_options');
	if($publish == true && function_exists('add_submenu_page') &&
	tt_option('tt_app_consumer_key') != '' &&
	tt_option('tt_app_consumer_secret') != '' &&
	tt_option('tt_oauth_token') != '' &&
	tt_option('tt_oauth_token_secret') != '')
		add_submenu_page($postfile, __('Write Tweet',
			'tweet-this'), __('Write Tweet', 'tweet-this'), 2,
			$filename, 'tt_admin_tweet_form');
}


/**
 * Constructs the Write Tweet page.
 * @package tweet-this
 * @since 1.7.3
 */
function tt_admin_tweet_form() {
	tt_enqueue_script('jquery');
	echo	'<script type="text/javascript">function ttWriteTweet() {var' .
		' result = jQuery(\'#tt_write_tweet_result\');result.show().' .
		'addClass(\'tt_write_tweet_wait\').html(\'&nbsp;&nbsp;' .
		'<strong>' . __('Sending...', 'tweet-this') . '</strong>\');' .
		'jQuery.post("' . get_bloginfo('wpurl') . '/index.php", ' .
		'{ttaction: \'tt_write_tweet\', tt_app_consumer_secret: \'' .
		tt_option('tt_app_consumer_secret') . '\', tt_tweet_text: ' .
		'jQuery(\'#tt_tweet_text\').val()}, function(data) {result.' .
		'html(data).removeClass(\'tt_write_tweet_wait\');});} ' .
		'function ttScheduleTweet() {var result = jQuery(\'#tt_' .
		'schedule_tweet_result\');result.show().addClass(\'tt_' .
		'schedule_tweet_wait\').html(\'&nbsp;&nbsp;<strong>' .
		__('Scheduling...', 'tweet-this') . '</strong>\');' .
		'jQuery.post("' . get_bloginfo('wpurl') . '/index.php", ' .
		'{ttaction: \'tt_schedule_tweet\', tt_app_consumer_secret:' .
		' \'' . tt_option('tt_app_consumer_secret') . '\', tt_tweet_' .
		'text: jQuery(\'#tt_tweet_text\').val(), tt_schedule_date: ' .
		'jQuery(\'#tt_date_text\').val()}, function(data) {result.' .
		'html(data).removeClass(\'tt_schedule_tweet_wait\');});}' .
		'function ttShortenURLs() {var result = ' .
		'jQuery(\'#tt_shorten_urls_result\');result.show().' .
		'addClass(\'tt_shorten_urls_wait\').html(\'&nbsp;&nbsp;' .
		'<strong>' . __('Generating preview...', 'tweet-this') .
		'</strong>\');jQuery.post("' . get_bloginfo('wpurl') .
		'/index.php", {ttaction: \'tt_shorten_urls\', tt_app_' .
		'consumer_secret: \'' .tt_option('tt_app_consumer_secret') .
		'\', tt_tweet_text: jQuery(\'#tt_tweet_text\').val()}, ' .
		'function(data) {result.html(data).removeClass(\'tt_shorten_' .
		'urls_wait\');});} function ttWriteTweetResult() {jQuery(\'' .
		'#tt_write_tweet_result\');} function ttScheduleTweetResult' .
		'() {jQuery(\'#tt_schedule_tweet_result\');} function ' .
		'ttShortenURLsResult() {jQuery(\'#tt_shorten_urls_result\');' .
		'} var lastDiv = \'\'; var currenttime = \'' .
		date("F d, Y H:i:s", time()) . '\'; var montharray = new ' .
		'Array("01", "02", "03", "04", "05", "06", "07", "08", ' .
		'"09", "10", "11", "12"); var serverdate = new ' .
		'Date(currenttime); function padlength(what) {var output = ' .
		'(what.toString().length == 1) ? "0" + what : what; return ' .
		'output;} function displaytime() {serverdate.setSeconds' .
		'(serverdate.getSeconds()+1); var datestring = serverdate.' .
		'getFullYear() + "-" + montharray[serverdate.getMonth()] + ' .
		'"-" + padlength(serverdate.getDate()); var timestring = ' .
		'padlength(serverdate.getHours()) + ":" + padlength' .
		'(serverdate.getMinutes()) + ":" + padlength(serverdate.' .
		'getSeconds()); document.getElementById("servertime").' .
		'innerHTML = datestring + " " + timestring;} ' .
		'window.onload = function() {setInterval("displaytime()", ' .
		'985)}</script><div class="wrap"><h2>';
	printf(__('<a target="_blank" href="http://richardxthripp.thripp.com' .
		'/tweet-this/">Tweet This</a> <a target="_blank" href=' .
		'"http://richardxthripp.thripp.com/tweet-this-version-' .
		'history/">v%1$s</a>: Write Tweet', 'tweet-this'), TT_VERSION);
	echo	'</h2>';
	if(isset($_REQUEST['delete'])) {
		$admin = false; $deleted = false;
		if(version_compare($GLOBALS['wp_version'], '2.0', '<')) {
			global $user_level;
			if($user_level >= 5) $admin = true;}
		else {	if(current_user_can('publish_posts')) $admin = true;}
		if($admin == true) {
			global $wpdb; $delete_id = $_REQUEST['delete'];
			$delete_content = $wpdb->get_var("SELECT tt_content " .
				"FROM $wpdb->rxtt WHERE ID = '$delete_id'");
			$wpdb->query("DELETE FROM $wpdb->rxtt WHERE " .
				"ID = '$delete_id' LIMIT 1");
			if($delete_content != '') $deleted = true;}
		echo	'<div id="message" class="updated fade"><p>';
		if($admin == false) _e("Scheduled tweet <strong>#$delete_id" .
			"</strong> not deleted because you could not be " .
			"verified as an administrator.", 'tweet-this');
		elseif($deleted == true) _e("Scheduled tweet <strong>#" .
			"$delete_id</strong> deleted successfully.",
			'tweet-this');
		else _e("Scheduled tweet <strong>#$delete_id</strong> could " .
			"not be deleted because it does not exist.",
			'tweet-this');
		echo	'</p></div>';}
		echo '<p style="margin-bottom:5px;">' . __('This will ' .
		'create a new tweet in <a target="_blank" href="http://' .
		'twitter.com">Twitter</a> using your OAuth connection.',
		'tweet-this') . '</p><form action="' . get_bloginfo('wpurl') .
		'/index.php" method="post" id="tt_tweet_form"><fieldset><p>' .
		'<textarea type="text" cols="104" rows="5" id=' .
		'"tt_tweet_text" name="tt_tweet_text" onchange=' .
		'"ttCharCount();" onclick="ttCharCount();" onkeyup=' .
		'"ttCharCount();"></textarea></p><script type="text/java' .
		'script">function ttCharCount() {var count = document.get' .
		'ElementById("tt_tweet_text").value.length; document.get' .
		'ElementById("tt_char_count").innerHTML = "<strong>" + ' .
		'(count) + "</strong>" + "' . __(' character(s) before URL ' .
		'shortening.', 'tweet-this') . '";} setTimeout("ttCharCount' .
		'();", 500); document.getElementById("tt_tweet_text").set' .
		'Attribute("autocomplete", "off");</script><p style="margin-' .
		'top:0;">&nbsp;&nbsp;<input type="button" class="button-' .
		'primary" name="tt_write_tweet_final" id="tt_write_tweet_' .
		'final" value="' . __('Tweet This', 'tweet-this') .
		'" title="' . __('Click to publish Twitter status update.',
		'tweet-this') . '" onclick="ttWriteTweet(); return false;" ' .
		'/> <input type="button" class="button" name="tt_shorten_' .
		'urls_final" id="tt_shorten_urls_final" value="' .
		__('Preview This', 'tweet-this') . '" title="' .
		__('Click here if your Tweet contains URLs.', 'tweet-this') .
		'" onclick="ttShortenURLs(); return false;" /> <input type=' .
		'"button" class="button" name="tt_schedule_tweet_final" id=' .
		'"tt_schedule_tweet_final" value="' . __('Schedule This',
		'tweet-this') . '" title="' . __('Click to schedule future ' .
		'Twitter status update.', 'tweet-this') . '" onclick=' .
		'"ttScheduleTweet(); return false;" /> Date: <input type=' .
		'"text" name="tt_date_text" id="tt_date_text" size="21" ' .
		'maxlength="19" autocomplete="off" value="' .
		date("Y-m-d H:i:s", time() + 3600) . '" />&nbsp;&nbsp;' .
		'<span id="tt_char_count"></span></p></fieldset></form>' .
		'<div id="tt_write_tweet_result" style="margin-top:5px;' .
		'margin-bottom:5px;"></div><div id="tt_schedule_tweet_result' .
		'" style="margin-top:5px;margin-bottom:5px;"></div>' .
		'<div id="tt_shorten_urls_result"></div>';
	if(function_exists('wp_nonce_field'))
		wp_nonce_field('tt_new_tweet', '_wpnonce', true, false);
	if(function_exists('wp_referer_field')) wp_referer_field(false);
	echo	'<div id="tt_tweet_notes" style="border:2px solid ' .
		'#00b6bf;background-color:#e6fffe;padding:0 10px 0 10px;' .
		'margin-top:15px;margin-bottom:10px;margin-left:6px;width:' .
		'750px;">';
		$get_service = tt_option('tt_admin_url_service');
	if($get_service == 'same') $get_service = tt_option('tt_url_service');
	if($get_service == '' || $get_service == 'same' ||
		$get_service == 'local' || ($get_service == 'adjix' &&
		tt_option('tt_adjix_api_key') == '') ||
		($get_service == 'snipurl' &&
		(tt_option('tt_snipurl_username') == '' ||
		tt_option('tt_snipurl_api_key') == '')))
			$service = 'bit.ly';
	else	$service = $get_service;
	if(($get_service == 'adjix' && $service == 'bit.ly') ||
		($get_service == 'snipurl' && $service == 'bit.ly'))
			$notice = ', ' . __('because your chosen URL ' .
			'service requires an API key which you have not ' .
			'provided in the Tweet This options', 'tweet-this');
	else $notice = '';
	if($service == 'custom')
		$service = __('your custom URL service', 'tweet-this');
	else	$service = ucfirst($service);
	global $wpdb;
	printf(__('<p>Welcome to the Write Tweet page! The current server ' .
		'date is approximately <strong><span id="servertime"></span>' .
		'</strong>.</p>' .
		'<p>URLs will be shortened with <strong>%3$s</strong> if ' .
		'your tweet is over 140 characters%4$s. You can update URL ' .
		'services <a target="_blank" href="%1$s/wp-admin/options-' .
		'general.php%5$s">here</a>.</p>' .
		'<p>Click "Preview This" to see exactly what will be ' .
		'published to your Twitter account with an accurate ' .
		'character count.</p>' .
		'<p>Click "Schedule This" to schedule the tweet for the ' .
		'date specified in the box to the right. This defaults to ' .
		'one hour after you loaded this page. Make sure to use ' .
		'"YYYY-MM-DD&nbsp;HH:mm:SS" format. If you have not used ' .
		'tweet scheduling before, a table named `%2$s` will be ' .
		'created in your database.</p>' .
		'<p>An HTTP 401 error indicates incorrect OAuth keys. An ' .
		'HTTP 403 error can mean that you have exceeded 140 ' .
		'characters or that you are trying to publish a duplicate ' .
		'tweet. <a target="_blank" href="http://apiwiki.twitter.com/' .
		'HTTP-Response-Codes-and-Errors">Click here</a> for more ' .
		'information on HTTP response codes.</p>' .
		'<p>Note that scheduled tweets will be triggered up to ten ' .
		'minutes AFTER the scheduled time, and then only posted ' .
		'when someone loads a page on your blog. The last 100 ' .
		'scheduled tweets will be listed below from newest to ' .
		'oldest in order of date scheduled. You will have to reload ' .
		'the page to update the list. "Status" shows "future" for ' .
		'scheduled tweets, "publish" for successfully published ' .
		'tweets, and "fail" for scheduled tweets that could not be ' .
		'published. Deleting a published tweet from the list will ' .
		'not delete the tweet on Twitter.</p>',
		'tweet-this'), get_bloginfo('url'), $wpdb->rxtt, $service,
		$notice, constant('TT_FILE_LOC'));
	echo	'</div>'; global $wpdb;
	if(get_option('tweet_this_db_version') == '1.0')
		$tweets = $wpdb->get_col("SELECT ID FROM $wpdb->rxtt ORDER BY
		tt_date DESC LIMIT 100");
	if($tweets) {
		echo '<div id="tt_scheduled_tweets" style="border:2px solid ' .
		'#b6bf00;background-color:#fffee6;padding:5px;margin-top:' .
		'15px;margin-bottom:15px;margin-left:6px;width:760px;">' .
		'<table border="3" cellspacing="3" cellpadding="3"><tr><td>' .
		'<strong><u>' . __('ID', 'tweet-this') . '</u></strong>' .
		'&nbsp;</td><td>&nbsp;<strong><u>' . __('Scheduled Date',
		'tweet-this') . '</u></strong>&nbsp;&nbsp;</td><td>&nbsp;' .
		'&nbsp;<strong><u>' . __('Tweet Text', 'tweet-this') .
		'</u></strong>&nbsp;&nbsp;</td><td>&nbsp;&nbsp;<strong><u>' .
		__('Status', 'tweet-this') . '</u></strong>&nbsp;&nbsp;</td>' .
		'<td><strong><u>' . __('Delete', 'tweet-this') . '</td></tr>';
		if(version_compare($GLOBALS['wp_version'], '2.0', '<'))
			$basefile = 'post.php'; else $basefile = 'edit.php';
		foreach($tweets as $tweet) {
			$content = $wpdb->get_row("SELECT * FROM $wpdb->rxtt
				WHERE ID = '$tweet' LIMIT 1");
			$scheduled_id = $content->ID;
			$scheduled_date = $content->tt_date;
			echo	'<tr><td><strong>' . $content->ID .
				'</strong>' . '&nbsp;</td><td>&nbsp;<strong>' .
				str_replace(' ', '&nbsp;',
				$content->tt_date) . '</strong>&nbsp;&nbsp;' .
				'</td><td>' .
				htmlentities($content->tt_content) . '&nbsp;' .
				'&nbsp;</td><td>&nbsp;&nbsp;<strong>' .
				$content->tt_status . '</strong>&nbsp;&nbsp;' .
				'</td><td><strong><a href="' .
				get_bloginfo('url') . '/wp-admin/' .
				$basefile . constant('TT_FILE_LOC') .
				'&delete=' . $content->ID . '" onclick=' .
				'"return confirm (\'' . __("Are you sure " .
				"you want to delete tweet #$scheduled_id, " .
				"scheduled for $scheduled_date?",
				'tweet-this') . '\');">' . __('delete',
				'tweet-this') . '</a></strong></td></tr>';}
		echo	'</table></div>';}
	echo	'</div>';
}


/**
 * Constructs the options form and handles importing and resetting the options.
 * @package tweet-this
 * @since 1.1
 */
function tweet_this_options() {
	$show_form = 'true';
	if(isset($_REQUEST['submit'])) update_tt_options();
	elseif(isset($_REQUEST['reset'])) {
		delete_option('tweet_this_settings');
		global_flush_tt_cache();
		tweet_this_install();
		// Resetting the options requires a page refresh on WP 1.5.
		if(version_compare($GLOBALS['wp_version'], '2.0', '<')) {
			update_option('tweet_this_import_key', 'reset2');
			echo	'<br /><div id="message" class="updated fade">
				<p>' . __('Resetting Tweet This options. ' .
				'Please wait...', 'tweet-this') . '</p></div>
				<meta http-equiv="refresh" content="0">';
			$show_form = 'false';}}
	elseif(get_option('tweet_this_import_key') == 'reset2') {
		update_option('tweet_this_import_key', 'reset1');
		echo	'<br /><div id="message" class="updated fade"><p>' .
			__('All Tweet This options reset successfully. URL ' .
			'cache flushed.', 'tweet-this') . '</p></div>';}
	elseif(isset($_REQUEST['partreset'])) {
		$add_options = get_tt_default_options();
		$keepers = array('tt_app_consumer_key',
			'tt_app_consumer_secret', 'tt_oauth_token',
			'tt_oauth_token_secret', 'tt_adjix_api_key',
			'tt_bitly_username', 'tt_bitly_api_key',
			'tt_snipurl_username', 'tt_snipurl_api_key',
			'tt_supr_username', 'tt_supr_api_key');
		foreach($keepers as $keeper)
			$add_options[$keeper] = tt_option($keeper);
		delete_option('tweet_this_settings');
		global_flush_tt_cache();
		tweet_this_install($add_options);
		// Resetting the options requires a page refresh on WP 1.5.
		if(version_compare($GLOBALS['wp_version'], '2.0', '<')) {
			update_option('tweet_this_import_key', 'partreset2');
			echo	'<br /><div id="message" class="updated fade">
				<p>' . __('Resetting all Tweet This options ' .
				'except API keys. Please wait...',
				'tweet-this') . '</p></div><meta http-equiv=' .
				'"refresh" content="0">';
			$show_form = 'false';}}
	elseif(get_option('tweet_this_import_key') == 'partreset2') {
		update_option('tweet_this_import_key', 'partreset1');
		echo	'<br /><div id="message" class="updated fade"><p>' .
			__('All Tweet This options reset except API keys. ' .
			'URL cache flushed.', 'tweet-this') . '</p></div>';}
	elseif(isset($_REQUEST['flush'])) {
		global $wpdb;
		$count2 = number_format($wpdb->get_var("SELECT COUNT(*) FROM
			$wpdb->postmeta WHERE meta_key = 'tweet_this_url' AND
			meta_value != 'getnew'"));
		global_flush_tt_cache();
		echo	'<br /><div id="message" class="updated fade"><p>' .
			__("<strong>$count2</strong> cached URL(s) flushed " .
			"successfully.", 'tweet-this') . '</p></div>';}
	elseif(isset($_REQUEST['uninstall'])) {
		$tt_dir = constant('TT_DIR');
		update_option('tweet_this_import_key', 'uninstall2');
		echo	'<br /><div class="wrap"><p style="width:800px;">' .
			__("Are you sure? NOTE: You will have to delete the " .
			"/plugins/$tt_dir/ directory to complete the removal.",
			'tweet-this') . '</p><form id="tweet-this" name=' .
			'"tweet-this" method="post" action="">';
		if(function_exists('wp_nonce_field'))
			wp_nonce_field('update-options');
		echo	'<p class="submit"><input type="submit" name=' .
			'"uninstallfinal" value="' . __('COMPLETELY ' .
			'UNINSTALL AND DEACTIVATE TWEET THIS', 'tweet-this') .
			'" title="' . __('You can reactivate later, but you ' .
			'will lose your settings.', 'tweet-this') . '" /> ' .
			'<input type="submit" name="uninstallcancel" class=' .
			'"button-primary" value="' . __('Cancel and return ' .
			'to options page', 'tweet-this') . '" title="' .
			__('You will go back to the options form.',
			'tweet-this') . '" /></p></form></div>';
		$show_form = 'false';}
	elseif(isset($_REQUEST['uninstallcancel'])) {
		update_option('tweet_this_import_key', 'uninstall1');
		echo	'<br /><div id="message" class="updated ' .
			'fade"><p>' . __('Uninstallation aborted.',
			'tweet-this') . '</p></div>';}
	elseif(isset($_REQUEST['uninstallfinal']) &&
	get_option('tweet_this_import_key') == 'uninstall2') {
		echo	'<br /><div id="message" class="updated fade"><p>';
		if(function_exists('deactivate_plugins'))
			echo	__('Uninstalling and deactivating Tweet ' .
				'This. Please wait 3 seconds...',
				'tweet-this');
		else echo	__('Uninstalling Tweet This. Cannot ' .
				'deactivate because you are below WP 2.5. ' .
				'Please wait 3 seconds...', 'tweet-this');
		echo	'</p></div><meta http-equiv="refresh" content="3;url=' .
			get_bloginfo('url') . '/wp-admin/plugins.php';
		if(function_exists('deactivate_plugins')) echo '?deactivate=true';
		echo	'">';
		tweet_this_uninstall('alpha omega');
		if(function_exists('deactivate_plugins'))
			deactivate_plugins(__FILE__);
		$show_form = 'false';}
	elseif(isset($_REQUEST['uninstallfinal']) &&
	get_option('tweet_this_import_key') != 'uninstall2') {
		update_option('tweet_this_import_key', 'uninstall1');
		echo	'<br /><div id="message" class="updated fade"><p>' .
			__('Uninstallation aborted because ' .
			'tweet_this_import_key was not set to "uninstall2", ' .
			'which suggests tampering.', 'tweet-this') .
			'</p></div>';}
	elseif(isset($_REQUEST['import'])) {
		global $wpdb;
		$import_options = $_POST['import_content'];
		if(substr($import_options, 0, 1) == 'a') {
			$wpdb->query("UPDATE $wpdb->options SET option_value =
				'$import_options' WHERE option_name =
				'tweet_this_settings'");
				if(function_exists('wp_cache_flush'))
					wp_cache_flush();
				global_flush_tt_cache();
		// Importing options requires a page refresh before WP 2.5.
		if(version_compare($GLOBALS['wp_version'], '2.5', '<')) {
			update_option('tweet_this_import_key', 'import2');
			echo	'<br /><div id="message" class="updated fade">
				<p>' . __('Importing Tweet This options. ' .
				'Please wait...', 'tweet-this') . '</p></div>
				<meta http-equiv="refresh" content="0">';
			$show_form = 'false';}}
		else echo	'<br /><div id="message" class="error">
				<p>' . __('There is something wrong with the' .
				' provided options. Import aborted.',
				'tweet-this') . '</p></div>';}
	elseif(get_option('tweet_this_import_key') == 'import2') {
		update_option('tweet_this_import_key', 'import1');
		echo	'<br /><div id="message" class="updated fade"><p>' .
			__('Tweet This options imported successfully. URL ' .
			'cache flushed.', 'tweet-this') . '</p></div>';}
	if($show_form != 'false') print_tt_form();
}


/**
 * Sets one cached URL to "getnew".
 * @package tweet-this
 * @since 1.1
 */
function flush_tt_cache($post_id) {
	update_post_meta($post_id, 'tweet_this_url', 'getnew');
}


/**
 * Deletes one cached URL.
 * @package tweet-this
 * @since 1.1
 */
function delete_tt_cache() {
	global $id;
	delete_post_meta($id, 'tweet_this_url');
}


/**
 * Sets all cached URLs to "getnew".
 * @package tweet-this
 * @since 1.1
 */
function global_flush_tt_cache() {
	global $wpdb;
	$wpdb->query("UPDATE $wpdb->postmeta SET meta_value = 'getnew' WHERE
		meta_key = 'tweet_this_url'");
}


/**
 * Deletes all cached URLs.
 * @package tweet-this
 * @since 1.1
 */
function global_delete_tt_cache() {
	global $wpdb;
	$wpdb->query("DELETE FROM $wpdb->postmeta " .
		"WHERE meta_key = 'tweet_this_url'");
	delete_option('tweet_this_import_key');
}


/**
 * Deletes the custom fields of one post.
 * @package tweet-this
 * @since 1.7.2
 */
function delete_tt_postmeta() {
	global $id;
	delete_post_meta($id, 'tt_auto_tweet');
	delete_post_meta($id, 'tt_auto_tweet_text');
	delete_post_meta($id, 'tt_http_code');
	delete_post_meta($id, 'tt_tweeted');
	delete_post_meta($id, 'tweet_this_url');
}


/**
 * Loads the textdomain and checks for scheduled tweets every ten minutes.
 * @package tweet-this
 * @since 1.3
 */
function tweet_this_init() {
	if(function_exists('load_plugin_textdomain')) {
		if(version_compare($GLOBALS['wp_version'], '2.6', '<'))
			load_plugin_textdomain('tweet-this',
				'wp-content/plugins/' . TT_DIR . '/languages');
		else load_plugin_textdomain('tweet-this', false,
			TT_DIR . '/languages');
	}
	if(get_option('tweet_this_db_version') == '1.0') {
		global $wpdb;
		$last = get_option('tweet_this_last_cron', false);
		if(($last != false && $last > (time() - 600)) ||
		tt_option('tt_app_consumer_key') == '' ||
		tt_option('tt_app_consumer_secret') == '' ||
		tt_option('tt_oauth_token') == '' ||
		tt_option('tt_oauth_token_secret') == '')
			return;
		update_option('tweet_this_last_cron', time());
		$pending = $wpdb->get_col("SELECT ID FROM $wpdb->rxtt WHERE
			(tt_date > 0 && tt_date <= CURRENT_TIMESTAMP() AND
			tt_status = 'future')");
		if(!count($pending)) return;
		foreach($pending as $post){
			if(!$post) continue;
			$content = $wpdb->get_row("SELECT * FROM $wpdb->rxtt
				WHERE ID = '$post' LIMIT 1");
			$connection = tt_oauth_connection();
			$connection->decode_json = true;
			$data = $connection->post(TT_API_POST_STATUS,
				array('status' => $content->tt_content,
				'source' => 'tweetthis'));
			if($connection->http_code == '200') {
				$status = 'publish';
				$response = $data->id;}
			else {	$status = 'fail';
				$response = $connection->http_code;}
			$wpdb->query("UPDATE $wpdb->rxtt SET tt_status =
				'$status', tt_response = '$response' WHERE ID =
				'$post' AND tt_status = 'future' LIMIT 1");}}
}


/**
 * Returns the cached URL for a post or page.
 * @package tweet-this
 * @since 1.7.4
 */
function get_tt_shortlink($post_id = '') {
	global $post; if($post_id == '') $post_id = $post->ID;
	if($post_id != '' && ($post->post_status == 'publish' ||
	tt_option('tt_url_service') == 'local' ||
	tt_option('tt_url_service') == '')) {
		$get_cache = get_post_meta($post_id, 'tweet_this_url', true);
		if(trim($get_cache) != '' && $get_cache != 'getnew')
			$cache = $get_cache;
		else $cache = get_tweet_this_short_url(
			get_permalink($post_id), $post_id, true);
		return	str_replace('www.', 'http://',
			str_replace('http://www.', 'http://', $cache));}
	else return false;
}


/**
 * Echoes the above function for convenience.
 * @package tweet-this
 * @since 1.7.4
 */
function tt_shortlink($post_id = '') {
	global $post; if($post_id == '') $post_id = $post->ID;
	if($post_id != '' && ($post->post_status == 'publish' ||
		tt_option('tt_url_service') == 'local' ||
		tt_option('tt_url_service') == ''))
			echo get_tt_shortlink($post_id);
	else return false;
}


/**
 * Returns the cached URL HTML for insertion into wp_head.
 * @package tweet-this
 * @since 1.7.4
 */
function get_tt_shortlink_wp_head($post_id = '') {
	global $post; if($post_id == '') $post_id = $post->ID;
	if($post_id != '' && ($post->post_status == 'publish' ||
		tt_option('tt_url_service') == 'local' ||
		tt_option('tt_url_service') == ''))
			return '<link rel="shortlink" href="' .
			get_tt_shortlink($post_id) . '" />';
	else return false;	
}


/**
 * Echoes the above function for convenience.
 * @package tweet-this
 * @since 1.7.4
 */
function tt_shortlink_wp_head($post_id = '') {
	global $post; if($post_id == '') $post_id = $post->ID;
	if($post_id != '' && ($post->post_status == 'publish' ||
		tt_option('tt_url_service') == 'local' ||
		tt_option('tt_url_service') == ''))
			echo get_tt_shortlink_wp_head($post_id) . "\n";
	else return false;
}


/**
 * HTML comment inserted using the wp_head hook, including the version,
 * build number, and some debug info to help me answer support requests.
 * @package tweet-this
 * @since 1.7.7
 */
function get_tt_html_comment() {
	$mode = ' mode';
	if(version_compare($GLOBALS['wp_version'], '2.7', '<'))
	$mode .= ' gamma';
	if(version_compare(PHP_VERSION, '5.0.0', '<'))
	$mode .= ' delta';
	if($mode == ' mode') $mode .= ' alpha';
	if(trim(tt_option('tt_app_consumer_key')) != '')
	$mode .= ' zeta';
	return	'<!-- Tweet This v' . constant('TT_VERSION') .
		' b' . constant('TT_BUILD') . $mode . ' -->';
}


/**
 * Echoes the above function for convenience.
 * @package tweet-this
 * @since 1.7.7
 */
function tt_html_comment() {
	echo get_tt_html_comment() . "\n";
}


/**
 * Footer message must be enabled in Advanced Options.
 * @package tweet-this
 * @since 1.3
 */
function tweet_this_footer() {
	echo	'<div style="width:738px;margin:10px auto 10px auto;">' .
		'<p style="text-align:center;">';
	printf(__('Twitter links powered by <a target="_blank" href="http://' .
		'richardxthripp.thripp.com/tweet-this/">Tweet This v%1$s' .
		'</a>, a WordPress plugin for Twitter.', 'tweet-this'),
		TT_VERSION); echo '</p></div>';
}


/**
 * Ads must be enabled in Advanced Options.
 * @package tweet-this
 * @since 1.3.5
 */
function tweet_this_ad_body($content) {
	if(is_singular()) return $content .= '<p><script type="text/' .
		'javascript">google_ad_client = "pub-5149869439810473"; ' .
		'google_ad_slot = "1830968079"; google_ad_width = 336; ' .
		'google_ad_height = 280;</script><script type="text/' .
		'javascript" src="http://pagead2.googlesyndication.com/' .
		'pagead/show_ads.js"></script></p>';
	else return $content;
}


/**
 * Ads must be enabled in Advanced Options.
 * @package tweet-this
 * @since 1.3.5
 */
function tweet_this_ad_footer() {
	echo	'<div style="width:738px;margin:auto;"><p><script type="text' .
		'/javascript">google_ad_client = "pub-5149869439810473"; ' .
		'google_ad_slot = "6830530578"; google_ad_width = 728; ' .
		'google_ad_height = 90;</script><script type="text/' .
		'javascript" src="http://pagead2.googlesyndication.com/' .
		'pagead/show_ads.js"></script></p></div>';
}


/**
 * Converts an absolute date to a relative date.
 * Based on: http://wordpress.org/extend/plugins/wickett-twitter-widget/
 * Wickett Twitter Widget  Copyright 2009  Automattic Inc.
 * @package tweet-this
 * @since 1.7.6
 */
function tt_relative_time($original, $do_more = 0) {
	$chunks = array(array(31536000, 'year'), array(2592000, 'month'),
		array(604800, 'week'), array(86400, 'day'),
		array(3600, 'hour'), array(60, 'minute'));
	$since = time() - $original;
	for($i = 0, $j = count($chunks); $i < $j; $i++) {
		$seconds = $chunks[$i][0];
		$name = $chunks[$i][1];
		if(($count = floor($since / $seconds)) != 0) break;}
	$print = ($count == 1) ? '1 ' . $name : "$count {$name}s";
	if($i + 1 < $j) {
		$seconds2 = $chunks[$i + 1][0];
		$name2 = $chunks[$i + 1][1];
		if((($count2 = floor(($since - ($seconds * $count)) /
		$seconds2)) != 0) && $do_more)
			$print .= ($count2 == 1) ? ', 1 ' . $name2 :
			", $count2 {$name2}s";}
	return $print;
}


/**
 * Adds a Tweet This widget to Appearance > Widgets. Requires WP 2.8 or newer.
 * Based on: http://wordpress.org/extend/plugins/wickett-twitter-widget/
 * Wickett Twitter Widget  Copyright 2009  Automattic Inc.
 * @package tweet-this
 * @since 1.7.6
 */
if(version_compare($GLOBALS['wp_version'], '2.8', '>=')) :
class Tweet_This_Widget extends WP_Widget {
	function Tweet_This_Widget() {
		$widget_ops = array('classname' => 'tweet_this_widget',
			'description' => __('Displays your latest Twitter ' .
			'updates. Part of the Tweet This plugin.',
			'tweet-this'));
		$this->WP_Widget('tweet-this', __('Twitter Updates',
			'tweet-this'), $widget_ops);}
	function widget($args, $instance) {
		extract($args);
		$account = urlencode($instance['account']);
		if(empty($account)) return;
		$title = apply_filters('widget_title', $instance['title']);
		if(empty($title)) $title = __('Twitter Updates', 'tweet-this');
		$show = absint($instance['show']);
		$hidereplies = $instance['hidereplies'];
		$before_timesince = esc_html($instance['beforetimesince']);
		if(empty($before_timesince)) $before_timesince = ' ';
		$before_tweet = esc_html($instance['beforetweet']);
		echo	$before_widget . $before_title . '<a href="' .
			clean_url('http://twitter.com/' . $account) .
			'" target="_blank">' . $title . '</a>' . $after_title;
		if(!$tweets = wp_cache_get('tweet-this-widget-' .
		$this->number, 'widget')) {
			$twitter_json_url = clean_url("http://twitter.com/" .
			"statuses/user_timeline/$account.json", null, 'raw');
			$response = wp_remote_get($twitter_json_url,
				array('User-Agent' => 'Tweet This'));
			$response_code =
				wp_remote_retrieve_response_code($response);
			if($response_code == 200) {
				$tweets = wp_remote_retrieve_body($response);
				$tweets = json_decode($tweets);
				$expire = 900;
				if(!is_array($tweets) ||
				isset($tweets['error'])) {
					$tweets = 'error'; $expire = 300;}}
			else {	$tweets = 'error'; $expire = 300;
				wp_cache_add('tweet-this-widget-response-' .
					'code-' . $this->number,
					$response_code, 'widget', $expire);}
			wp_cache_add('tweet-this-widget' . $this->number,
				$tweets, 'widget', $expire);}
		if($tweets != 'error') {
			echo '<ul class="tweets">';
			$tweets_out = 0;
			foreach((array) $tweets as $tweet) {
			if($tweets_out >= $show) break;
			if(empty($tweet->text) || ($hidereplies &&
			!empty($tweet->in_reply_to_user_id))) continue;
			$text = preg_replace_callback('/(^|\s)#(\w+)/',
				array($this, '_tweet_this_widget_hashtag'),
				preg_replace_callback('/(^|\s)@(\w+)/',
				array($this, '_tweet_this_widget_username'),
			make_clickable(wp_specialchars($tweet->text))));
			$created_at = substr($tweet->created_at, 0, 10) .
				substr($tweet->created_at, 25, 5) .
				substr($tweet->created_at, 10, 15);
			echo	'<li>' . $before_tweet . $text .
				$before_timesince . '<a href="' .
				clean_url('http://twitter.com/' . $account .
				'/statuses/' . urlencode($tweet->id)) .
				'" class="timesince" target="_blank">' .
				str_replace(' ', '&nbsp;',
				tt_relative_time(strtotime($created_at))) .
				'&nbsp;ago</a></li>';
			$tweets_out++;} echo '</ul>';}
		else {	if(wp_cache_get('tweet-this-widget-response-code-' .
				$this->number, 'widget') == 401) echo '<p>' .
				__('HTTP 401 Error: Please make sure the ' .
				'Twitter account is <a href="http://help.' .
				'twitter.com/forums/10711/entries/14016" ' .
				'target="_blank">public</a>.', 'tweet-this') .
				'</p>';
			else echo '<p>' . __('Error: Twitter did not ' .
				'respond. Please wait a few minutes and ' .
				'refresh this page.', 'tweet-this') . '</p>';}
		echo $after_widget;}
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['account'] =
			strip_tags(stripslashes($new_instance['account']));
		$instance['account'] = str_replace('http://twitter.com/', '',
			$instance['account']);
		$instance['account'] = str_replace('/', '',
			$instance['account']);
		$instance['account'] = str_replace('@', '',
			$instance['account']);
		$instance['title'] =
			strip_tags(stripslashes($new_instance['title']));
		$instance['show'] = absint($new_instance['show']);
		$instance['hidereplies'] = isset($new_instance['hidereplies']);
		$instance['beforetimesince'] =
			$new_instance['beforetimesince'];
		wp_cache_delete('tweet-this-widget-' . $this->number,
			'widget');
		wp_cache_delete('tweet-this-widget-response-code-' .
			$this->number, 'widget');
		return $instance;}
	function form($instance) {
		$instance = wp_parse_args((array) $instance, array('account' =>
			'', 'title' => __('Twitter Updates', 'tweet-this'),
			'show' => '3', 'hidereplies' => false));
		$account = esc_attr($instance['account']);
		$title = esc_attr($instance['title']);
		$show = absint($instance['show']);
		if($show < 1 || 20 < $show) $show = '3';
		$hidereplies = (bool) $instance['hidereplies'];
		$before_timesince = esc_attr($instance['beforetimesince']);
		echo	'<p><label for="' . $this->get_field_id('title') .
			'">' . __('Title:', 'tweet-this') . '<input class=' .
			'"widefat" id="' . $this->get_field_id('title') .
			'" name="' . $this->get_field_name('title') .
			'" type="text" value="' . $title . '" /></label></p>' .
			'<p><label for="' . $this->get_field_id('account') .
			'">' . __('Twitter username:', 'tweet-this') .
			'<input class="widefat" id="' .
			$this->get_field_id('account') . '" name="' .
			$this->get_field_name('account') . '" type="text" ' .
			'value="' . $account . '" /></label></p><p><label ' .
			'for="' . $this->get_field_id('show') . '">' .
			__('Show latest ', 'tweet-this') .
			'<select id="' . $this->get_field_id('show') .
			'" name="' . $this->get_field_name('show') . '">';
		for($i = 1; $i <= 20; ++$i)
			echo	'<option value="' . $i . '"' .
				($show == $i ? ' selected="selected"' : '') .
				'>' . $i . '&nbsp;</option>';
		echo	'</select> ' . __('tweets', 'tweet-this') .
			'</label></p><p><label for="' .
			$this->get_field_id('hidereplies') . '"><input id="' .
			$this->get_field_id('hidereplies') . '" class=' .
			'"checkbox" type="checkbox" name="' .
			$this->get_field_name('hidereplies') . '"' .
			checked($hidereplies) . ' /> ' . __('Hide replies',
			'tweet-this') . ' </label></p><p><label for="' .
			$this->get_field_id('beforetimesince') . '">' .
			__('Text between tweet and time:', 'tweet-this') .
			'<input class="widefat" id="' .
			$this->get_field_id('beforetimesince') . '" name="' .
			$this->get_field_name('beforetimesince') . '" type=' .
			'"text" value="' . $before_timesince . '" /></label>' .
			'</p>';}
	function _tweet_this_widget_username($matches) {
		return $matches[1] . '@<a href="' .
		clean_url('http://twitter.com/' . urlencode($matches[2])) .
		'" target="_blank">' . $matches[2] . '</a>';}
	function _tweet_this_widget_hashtag( $matches ) {
		return $matches[1] . '<a href="' .
		clean_url('http://search.twitter.com/search?q=%23' .
		urlencode($matches[2])) . '" target="_blank">#' . $matches[2] .
		'</a>';}
} endif;


/**
 * Initializes the Tweet This widget. Requires WP 2.8 or newer.
 * @package tweet-this
 * @since 1.7.6
 */
function tweet_this_widget_init() {
	if(function_exists('register_widget') &&
	class_exists('Tweet_This_Widget'))
		register_widget('Tweet_This_Widget');
}


/**
 * Initializes Tweet This options on post edit screen in WP 2.5 or newer.
 * @package tweet-this
 * @since 1.8
 */
function tt_post_options_init() {
	if(version_compare($GLOBALS['wp_version'], '2.7', '>=')) {
		$context = 'side'; $priority = 'low';}
	else {	$context = 'normal'; $priority = 'high';}
	if(function_exists('add_meta_box')) {
		if(tt_option('tt_auto_tweet_display') != 'false') 
			add_meta_box('tweetthis', __('Tweet This',
			'tweet-this'), 'tt_post_options', 'post',
			$context, $priority);
		if(tt_option('tt_auto_tweet_display_pages') != 'false')
			add_meta_box('tweetthis', __('Tweet This',
			'tweet-this'), 'tt_post_options', 'page',
			$context, $priority);}
	else return false;
}


/**
 * Actions for updating the database, tweeting posts, and displaying things.
 */
add_action('init', 'tweet_this_init');
add_action('init', 'tt_request_handler');
add_action('publish_post', 'flush_tt_cache');
add_action('save_post', 'flush_tt_cache');
add_action('delete_post', 'delete_tt_postmeta');
add_action('generate_rewrite_rules', 'global_flush_tt_cache');
add_action('draft_post', 'tt_store_post_options', 1, 2);
add_action('save_post', 'tt_store_post_options', 1, 2);
add_action('publish_post', 'tt_store_post_options', 1, 2);
add_action('publish_post', 'tt_auto_tweet', 99);
add_action('publish_page', 'tt_auto_tweet', 99);
add_filter('the_content', 'insert_tweet_this');
if(function_exists('add_shortcode'))
	add_shortcode('tweet_this', 'tt_shortcode_handler');
if(version_compare($GLOBALS['wp_version'], '2.5', '>='))
	add_action('add_meta_boxes', 'tt_post_options_init');
else {	if(tt_option('tt_auto_tweet_display') != 'false')
		add_action('edit_form_advanced', 'tt_post_options');
	if(tt_option('tt_auto_tweet_display_pages') != 'false')
		add_action('edit_page_form', 'tt_post_options');}
if(TT_HIDE_MENU != true)
	add_action('admin_menu', 'tweet_this_add_options');
if(tt_option('tt_css') != '[BLANK]')
	add_action('wp_head', 'tweet_this_css', 9);
if(tt_option('tt_footer') == 'true')
	add_action('wp_footer', 'tweet_this_footer');
if(tt_option('tt_ads') == 'true') {
	add_action('the_content', 'tweet_this_ad_body');
	add_action('wp_footer', 'tweet_this_ad_footer');}
if(tt_option('tt_shortlink_filter') != 'false' && tt_option('tt_url_service')
	!= 'local' && tt_option('tt_url_service') != '') {
	if(version_compare($GLOBALS['wp_version'], '2.5', '<') ||
	(function_exists('has_action') &&
	!has_action('wp_head', 'wp_shortlink_wp_head')))
		add_action('wp_head', 'tt_shortlink_wp_head', 9);
	if(version_compare($GLOBALS['wp_version'], '3.0', '>='))
		add_filter('get_shortlink', 'get_tt_shortlink');}
if(function_exists('tt_html_comment'))
	add_action('wp_head', 'tt_html_comment', 9);
if(version_compare($GLOBALS['wp_version'], '2.8', '>='))
	add_action('widgets_init', 'tweet_this_widget_init');


/**
 * Activation / deactivation hooks.
 */
if(function_exists('register_activation_hook'))
	register_activation_hook(__FILE__, 'tweet_this_install');
else add_action('activate_' . __FILE__, 'tweet_this_install');
if(function_exists('register_deactivation_hook'))
	register_deactivation_hook(__FILE__, 'global_delete_tt_cache');
else add_action('deactivate_' . __FILE__, 'global_delete_tt_cache');

?>