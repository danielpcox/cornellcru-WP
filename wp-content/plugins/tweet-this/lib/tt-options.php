<?php
/**
 * Tweet This is a plugin for WordPress 2.7 - 3.0.1 and WordPress MU.
 * Copyright 2008 - 2010  Richard X. Thripp  (email: richardxthripp@thripp.com)
 * Released under Version 2 of the GNU General Public License as published by
 * the Free Software Foundation, or, at your option, any later version.
 */


/**
 * This file is part of Tweet This v1.8, build 034, released 2010-10-02.
 * http://richardxthripp.thripp.com/tweet-this/
 */


/**
 * This file is required for user-control of the Tweet This options.
 */


	if(TT_OVERRIDE_OPTIONS == true && TT_SPECIAL_OPTIONS != '')
		echo	'<br /><div id="override" class="error" ' .
			'style="width:800px;"><p>' . __('WARNING: ' .
			'TT_OVERRIDE_OPTIONS is set to true in the ' .
			'tt-config.php file in the tweet-this directory. ' .
			'Any options set here will be disregarded; only the ' .
			'options specified in the tt-config.php will be ' .
			'used. Please set TT_OVERRIDE_OPTIONS to false, or ' .
			'set TT_HIDE_MENU to true to remove the options ' .
			'form entirely.', 'tweet-this') . '</p></div>';
	if(isset($_REQUEST['reset']) &&
		version_compare($GLOBALS['wp_version'], '2.0', '>='))
			echo	'<br /><div id="message" class="updated ' .
			'fade"><p>' . __('All Tweet This options reset. URL ' .
			'cache flushed.', 'tweet-this') . '</p></div>';
	if(isset($_REQUEST['partreset']) &&
		version_compare($GLOBALS['wp_version'], '2.0', '>='))
			echo	'<br /><div id="message" class="updated ' .
			'fade"><p>' . __('All Tweet This options reset ' .
			'except API keys. URL cache flushed.', 'tweet-this') .
			'</p></div>';
	if(isset($_REQUEST['import']) &&
		substr($_POST['import_content'], 0, 1) == 'a' &&
		version_compare($GLOBALS['wp_version'], '2.5', '>='))
			echo	'<br /><div id="message" class="updated ' .
			'fade"><p>' . __('Tweet This options imported. URL ' .
			'cache flushed.', 'tweet-this') . '</p></div>';
	$u = str_replace('snurl', 'snipurl', tt_option('tt_url_service'));
	$u2 = str_replace('snurl', 'snipurl',
		tt_option('tt_admin_url_service'));
	$s = ' selected="selected"'; $v = ' checked="checked"'; global $wpdb;
	$export_options = $wpdb->get_var("SELECT option_value FROM
		$wpdb->options WHERE option_name = 'tweet_this_settings'");
	$count1 = number_format($wpdb->get_var("SELECT COUNT(*)
		FROM $wpdb->posts WHERE post_status = 'publish'"));
	$count2 = number_format($wpdb->get_var("SELECT COUNT(*) FROM
		$wpdb->postmeta WHERE meta_key = 'tweet_this_url' AND
		meta_value != 'getnew'"));
	if($count2 > $count1) $count2 = $count1;
	echo	'<script type="text/javascript">function ttTestOAuth() {var ' .
		'result = jQuery(\'#tt_oauth_test_result\'); result.show().' .
		'addClass(\'tt_oauth_result_wait\').html(\' <strong>' .
		__('Testing...', 'tweet-this') . '</strong>\'); jQuery.post(' .
		'"' . get_bloginfo('wpurl') . '/index.php", {ttaction: ' .
		'\'tt_oauth_test\', tt_app_consumer_key: jQuery(\'#tt_app_' .
		'consumer_key\').val(), tt_app_consumer_secret: jQuery(\'#' .
		'tt_app_consumer_secret\').val(), tt_oauth_token: jQuery(\'#' .
		'tt_oauth_token\').val(), tt_oauth_token_secret: jQuery(\'#' .
		'tt_oauth_token_secret\').val()}, function(data) {result.' .
		'html(data).removeClass(\'tt_oauth_result_wait\'); ' .
		'setTimeout(\'ttTestOAuthResult();\', 60000);});} function ' .
		'ttTestOAuthResult() {jQuery(\'#tt_oauth_test_result\').' .
		'fadeOut(\'slow\');} var lastDiv = ""; function showDiv' .
		'(divName) {if(lastDiv) {document.getElementById(lastDiv).' .
		'className = "hiddenDiv";} if(divName && document.getElement' .
		'ById(divName)) {document.getElementById(divName).className ' .
		'= "visibleDiv"; lastDiv = divName;}} var lastDivAdmin = "";' .
		' function showDivAdmin(divNameAdmin) {if(lastDivAdmin) {' .
		'document.getElementById(lastDivAdmin).className = "hidden' .
		'DivAdmin";} if(divNameAdmin && document.getElementById' .
		'(divNameAdmin)) {document.getElementById(divNameAdmin).' .
		'className = "visibleDivAdmin"; lastDivAdmin = divNameAdmin;' .
		'}} var enablepersist = \'on\'; var collapseprevious = \'no' .
		'\'; if(document.getElementById) {document.write(\'<style ' .
		'type="text/css">\'); document.write(\'.switchcontent{' .
		'display:none;}\'); document.write(\'</style>\');} function ' .
		'getElementbyClass(classname) {ccollect = new Array(); var ' .
		'inc = 0; var alltags = document.all ? document.all : ' .
		'document.getElementsByTagName("*"); for(i = 0; i<alltags.' .
		'length; i++) {if(alltags[i].className == classname) ' .
		'ccollect[inc++] = alltags[i];}} function contractcontent' .
		'(omit) {var inc = 0; while(ccollect[inc]) {if(ccollect[inc]' .
		'.id != omit) ccollect[inc].style.display = \'none\'; inc++;' .
		'}} function expandcontent(cid) {if(typeof ccollect != ' .
		'\'undefined\') {if(collapseprevious=="yes") contractcontent' .
		'(cid); document.getElementById(cid).style.display = ' .
		'(document.getElementById(cid).style.display != \'block\') ' .
		'? \'block\' : \'none\'}} function revivecontent() {contract' .
		'content(\'omitnothing\'); selectedItem = getselectedItem();' .
		' selectedComponents = selectedItem.split(\'|\'); for(i = 0;' .
		' i<selectedComponents.length-1; i++); document.getElementBy' .
		'Id(selectedComponents[i]).style.display = \'block\'} ' .
		'function get_cookie(Name) {var search = Name + \'=\'; var ' .
		'returnvalue = \'\'; if(document.cookie.length > 0) {offset ' .
		'= document.cookie.indexOf(search); if(offset != -1) {' .
		'offset += search.length; end = document.cookie.indexOf' .
		'(\';\', offset); if(end == -1) end = document.cookie.length' .
		'; returnvalue = unescape(document.cookie.substring(offset, ' .
		'end));}} return returnvalue;} function getselectedItem() {' .
		'if(get_cookie(window.location.pathname) != \'\') {selected' .
		'Item = get_cookie(window.location.pathname); return ' .
		'selectedItem;} else return \'\';} function saveswitchstate' .
		'() {var inc = 0; selectedItem = \'\'; while (ccollect[inc])' .
		' {if(ccollect[inc].style.display == \'block\') selectedItem' .
		' += ccollect[inc].id+\'|\'; inc++;} document.cookie = ' .
		'window.location.pathname+\'=\'+selectedItem} function ' .
		'do_onload() {uniqueidn = window.location.pathname+\'first' .
		'timeload\'; getElementbyClass(\'switchcontent\'); if(enable' .
		'persist == \'on\' && typeof ccollect != \'undefined\') {' .
		'document.cookie = (get_cookie(uniqueidn) == \'\') ? unique' .
		'idn+\'=1\' : uniqueidn+\'=0\'; firsttimeload = (get_cookie' .
		'(uniqueidn) == 1) ? 1 : 0; if(!firsttimeload) revivecontent' .
		'();}} if(window.addEventListener) window.addEventListener' .
		'(\'load\', do_onload, false); else if(window.attachEvent) ' .
		'window.attachEvent(\'onload\', do_onload); else if(document' .
		'.getElementById) window.onload = do_onload; if(enable' .
		'persist == \'on\' && document.getElementById) window.' .
		'onunload = saveswitchstate;</script><style type="text/css">' .
		'label.in {padding-bottom:3px;} .hiddenDiv {display:none;} ' .
		'.hiddenDivAdmin {display:none;} .visibleDiv {display:block;' .
		'} .visibleDivAdmin {display:block;} .option {padding:3px 0 ' .
		'3px 0;} .option label {display:block;float:left;width:210px' .
		';} .optsnip {padding:3px 0 3px 0;} .optsnip label {display:' .
		'block; float:left; width:230px;} .opttwt {padding:2px 0 2px' .
		' 0;} .opttwt label {display:inline-block; width:375px;} ' .
		'.opttwtins {padding:2px 0 2px 0;} .opttwtins label.ins ' .
		'{display:inline-block; width:375px;}</style>' .
		'<div class="wrap"><h2>';
	printf(__('<a target="_blank" href="http://richardxthripp.thripp.com' .
		'/tweet-this/">Tweet This</a> <a target="_blank" href=' .
		'"http://richardxthripp.thripp.com/tweet-this-version-' .
		'history/">v%1$s</a> Options', 'tweet-this'), TT_VERSION);
	echo	'</h2>';
	if(version_compare($GLOBALS['wp_version'], '1.5', '<'))
		echo	'<div id="php" class="error" style="width:800px;">' .
			'<p>' . __('WARNING: Please deactivate Tweet This ' .
			'and upgrade to WordPress 1.5 or newer. Tweet This ' .
			'does not have exception handling for versions of ' .
			'WordPress below 1.5 so it will cause fatal errors ' .
			'on your blog. If you ARE using WordPress 1.5 or ' .
			'newer, note that this message is only being shown ' .
			'because $GLOBALS[\'wp_version\'] is reporting a ' .
			'number below 1.5.', 'tweet-this') . '</p></div>';
	if(version_compare($GLOBALS['wp_version'], '2.7', '<'))
		echo	'<div id="php" class="error" style="width:800px;">' .
			'<p>' . __('WARNING: Automatic Tweeting requires ' .
			'WordPress 2.7 or newer. You can still enter your ' .
			'OAuth keys under "Automatic Tweeting" so that you ' .
			'can publish or schedule tweets from the "Write ' .
			'Tweet" page, but do NOT check any of the boxes as ' .
			'they may cause tweets to be published even when ' .
			'"Send to Twitter" is unchecked on the Write Post ' .
			'screen.', 'tweet-this') . '</p></div>';
	if(version_compare(PHP_VERSION, '5.0.0', '<'))
		echo	'<div id="php" class="error" style="width:800px;">' .
			'<p>' . __('WARNING: Please upgrade to PHP 5.0.0 or ' .
			'newer. Tweet This uses OAuth libraries which ' .
			'require PHP 5. You can still use Tweet This, but ' .
			'do NOT enter your OAuth keys under "Automatic ' .
			'Tweeting" or check any of the boxes as they will ' .
			'cause fatal errors on your blog.', 'tweet-this') .
			'</p></div>';
	if((ini_get('allow_url_fopen') == 0 ||
	strtolower('allow_url_fopen') == 'off') &&
	!function_exists('curl_init'))
		echo	'<div id="curl" class="error" style="width:800px;">' .
			'<p>' . __('Allow_url_fopen and Curl are disabled in' .
			' your PHP configuration. All URLs will be served ' .
			'locally, regardless of your chosen URL service. To ' .
			'fix this, try adding these lines to your <a href=' .
			'"http://www.washington.edu/computing/web/publishing' .
			'/php-ini.html">php.ini file</a>: `extension = curl.' .
			'so` and `allow_url_fopen = on`.', 'tweet-this') .
			'</p></div>';
	echo	'<p>';
	printf(__('You have <strong>%1$s' . '</strong> published posts and ' .
		'pages. Tweet This has short URLs for <strong>%2$s</strong> ' .
		'of them. URLs are cached as needed.', 'tweet-this'),
		$count1, $count2);
	echo	'</p><form id="tweet-this" name="tweet-this" method="post" ' .
		'action="">';
	if(function_exists('wp_nonce_field'))
		wp_nonce_field('update-options');
	echo	'<p><span style="width:325px;display:inline-block;"><strong>' .
		__('URL Service to use on posts and auto-tweets:',
		'tweet-this') . '</strong></span> <select name=' .
		'"tt[tt_url_service]" id="tt[tt_url_service]" onchange=' .
		'"showDiv(this.value);" onclick="showDiv(this.value);" ' .
		'onkeyup="showDiv(this.value);">';
	tt_url_service('adjix', 'Adjix.com');
	tt_url_service('b2l.me', 'B2l.me');
	tt_url_service('bit.ly', 'Bit.ly');
	tt_url_service('is.gd', 'Is.gd');
	tt_url_service('metamark', 'Metamark.net [xrl.us]');
	tt_url_service('snipurl', 'SnipURL.com');
	tt_url_service('su.pr', 'Su.pr');
	tt_url_service('th8.us', 'Th8.us');
	tt_url_service('tinyurl', 'TinyURL.com');
	tt_url_service('tweetburner', 'Tweetburner.com [twurl.nl]');
	tt_url_service('local', __('Local, i.e.'), 'default');
	echo	'<option value="custom"';
	if($u == 'custom')
		echo	$s;
	echo	'>' . __('Custom', 'tweet-this') . '</option></select></p>' .
		'<p><span style="width:325px;display:inline-block;"><strong>' .
		__('URL service to use on the Write Tweet page:',
		'tweet-this') . '</strong></span> <select name=' .
		'"tt[tt_admin_url_service]" id="tt[tt_admin_url_service]" ' .
		'onchange="showDivAdmin(this.value);" ' .
		'onclick="showDivAdmin(this.value);" ' .
		'onkeyup="showDivAdmin(this.value);">';
	tt_url_service('adjix', 'Adjix.com', 'admin');
	tt_url_service('b2l.me', 'B2l.me', 'admin');
	tt_url_service('bit.ly', 'Bit.ly', 'admindefault');
	tt_url_service('is.gd', 'Is.gd', 'admin');
	tt_url_service('metamark', 'Metamark.net [xrl.us]', 'admin');
	tt_url_service('snipurl', 'SnipURL.com', 'admin');
	tt_url_service('su.pr', 'Su.pr', 'admin');
	tt_url_service('th8.us', 'Th8.us', 'admin');
	tt_url_service('tinyurl', 'TinyURL.com', 'admin');
	tt_url_service('tweetburner', 'Tweetburner.com [twurl.nl]', 'admin');
	echo	'<option value="same"';
	if($u2 == 'same')
		echo	$s;
	echo	'>' . __('Same as above', 'tweet-this') . '</option>' .
		'<option value="custom"';
	if($u2 == 'custom')
		echo	$s;
	echo	'>' . __('Custom', 'tweet-this') . '</option></select></p>' .
		'<div id="adjix"';
	if($u != 'adjix' && $u2 != 'adjix')
		echo	' class="hiddenDiv"';
	echo	'><label for="tt[tt_adjix_api_key]">' . __('Adjix Partner ' .
		'ID (<strong>Mandatory</strong>)', 'tweet-this') .
		': </label><input type="text" name="tt[tt_adjix_api_key]" ' .
		'id="tt[tt_adjix_api_key]" size="50" value="' .
		tt_option('tt_adjix_api_key') . '" /><p style="padding-bot' .
		'tom:3px;"><label><input type="checkbox" name="tt[tt_ad_vu]"';
	if(tt_option('tt_ad_vu') != 'false')
		echo	$v;
		echo	' /> ';
	printf(__('Use shorter Ad.vu URLs (%1$s Characters)', 'tweet-this'),
		(TT_ADJIX_LEN - 4));
	echo	'</label></p></div><div id="bit.ly"';
	if($u != 'bit.ly' && $u2 != 'bit.ly')
		echo	' class="hiddenDiv"';
	echo	'><div class="option"><label for="tt[tt_bitly_username]">' .
		__('Bit.ly Username (Optional)', 'tweet-this') . ':</label>' .
		'<input type="text" name="tt[tt_bitly_username]" ' .
		'id="tt[tt_bitly_username]" size="50" value="' .
		tt_option('tt_bitly_username') . '" /></div>' .
		'<div class="option"><label for="tt[tt_bitly_api_key]">' .
		__('Bit.ly API Key (Optional)', 'tweet-this') . ':</label>' .
		'<input type="text" name="tt[tt_bitly_api_key]" ' .
		'id="tt[tt_bitly_api_key]" size="50" value="' .
		tt_option('tt_bitly_api_key') . '" /></div><p style=' .
		'"padding-bottom:3px;"><label><input type="checkbox" ' .
		'name="tt[tt_j_mp]"';
	if(tt_option('tt_j_mp') == 'true')
		echo	$v;
		echo	' /> ';
	printf(__('Use shorter J.mp URLs (%1$s Characters)', 'tweet-this'),
		(TT_BITLY_LEN - 2));
	echo	'</label></p></div><div id="custom"';
	if($u != 'custom' && $u2 != 'custom')
		echo	' class="hiddenDiv"';
	echo	'><label for="tt[tt_custom_url_service]">' .
		__('URL of API', 'tweet-this') . ': </label>' .
		'<input type="text" name="tt[tt_custom_url_service]" ' .
		'id="tt[tt_custom_url_service]" size="80" value="';
	if(tt_option('tt_custom_url_service') == '')
		echo	'http://tinyurl.com/api-create.php?url=[LONGURL]';
	else echo	tt_option('tt_custom_url_service');
	echo	'" /><p style="width:800px;padding-bottom:4px;">' .
		__('The URL service must allow HTTP GET requests, meaning ' .
		'that the long URL is passed to the API as a parameter in ' .
		'the URL itself. HTTP POST is not supported. The output of ' .
		'the API must be a plain-text short URL including the http ' .
		'prefix, <a href="http://th8.us/api.php?url=http://' .
		'richardxthripp.thripp.com/tweet-this/">like this</a>. The ' .
		'only valid variable is "[LONGURL]". Please check your blog ' .
		'after specifying the API\'s URL because its validity will ' .
		'not be confirmed. <a href="http://richardxthripp.thripp.com' .
		'/tweet-this-custom-url-services">A list of various URL ' .
		'shortening services is available</a>, including the seven ' .
		'removed in Tweet This 1.6. Note that Tweet This requests a ' .
		'short URL for each post on your blog as your posts are ' .
		'viewed, even if they are never tweeted. This will add ' .
		'hundreds of URLs to the service\'s database if you have ' .
		'hundreds of posts. Caching will prevent the same URLs from ' .
		'being requested again and again.', 'tweet-this') .
		'</p></div><div id="snipurl"';
	if($u != 'snipurl' && $u2 != 'snipurl')
		echo	' class="hiddenDiv"';
	echo	'><div class="optsnip"><label for="tt[tt_snipurl_username]">' .
		__('SnipURL Username (<strong>Mandatory</strong>)',
		'tweet-this') . ':</label><input type="text" name=' .
		'"tt[tt_snipurl_username]" id="tt[tt_snipurl_username]" ' .
		'size="50" value="' . tt_option('tt_snipurl_username') .
		'" /></div><div class="optsnip"><label for="tt[tt_snipurl_' .
		'api_key]">' . __('SnipURL API Key (<strong>Mandatory' .
		'</strong>)', 'tweet-this') . ':</label><input type="text" ' .
		'name="tt[tt_snipurl_api_key]" id="tt[tt_snipurl_api_key]" ' .
		'size="50" value="' . tt_option('tt_snipurl_api_key') .
		'" /></div><p style="padding-bottom:3px;">';
	if(tt_option('tt_snipurl_domain') == 'snipurl.com' ||
		tt_option('tt_snipurl_domain') == '') $sn_snipurl = $v;
	if(tt_option('tt_snipurl_domain') == 'snipr.com') $sn_snipr = $v;
	if(tt_option('tt_snipurl_domain') == 'snurl.com') $sn_snurl = $v;
	if(tt_option('tt_snipurl_domain') == 'sn.im') $sn_im = $v;
	if(tt_option('tt_snipurl_domain') == 'cl.lk') $sn_lk = $v;
	echo	'<input type="radio" name="tt[tt_snipurl_domain]" id=' .
		'"snipurl-01" value="snipurl.com"' . $sn_snipurl . ' /> ' .
		'<label for="snipurl-01">';
	printf(	__('SnipURL.com (%1$s Chars)', 'tweet-this'),
		TT_SNIPURL_LEN);
	echo	'</label> <input type="radio" name="tt[tt_snipurl_domain]" ' .
		'id="snipurl-02" value="snipr.com"' . $sn_snipr . ' /> ' .
		'<label for="snipurl-02">';
	printf(	__('Snipr.com (%1$s Chars)', 'tweet-this'),
		(TT_SNIPURL_LEN - 2));
	echo	'</label> <input type="radio" name="tt[tt_snipurl_domain]" ' .
		'id="snipurl-03" value="snurl.com"' . $sn_snurl .
		' /> <label for="snipurl-03">';
	printf(	__('SnURL.com (%1$s Chars)', 'tweet-this'),
		(TT_SNIPURL_LEN - 2));
	echo	'</label> <input type="radio" name="tt[tt_snipurl_domain]" ' .
		'id="snipurl-04" value="sn.im"' . $sn_im . ' /> <label for=' .
		'"snipurl-04">';
	printf(	__('Sn.im (%1$s Chars)', 'tweet-this'),
		(TT_SNIPURL_LEN - 6));
	echo	'</label> <input type="radio" name="tt[tt_snipurl_domain]" ' .
		'id="snipurl-05" value="cl.lk"' . $sn_lk . ' /> <label for=' .
		'"snipurl-05">';
	printf(	__('Cl.lk (%1$s Chars)', 'tweet-this'),
		(TT_SNIPURL_LEN - 6));
	echo	'</label></p></div><div id="su.pr"';
	if($u != 'su.pr' && $u2 != 'su.pr')
		echo	' class="hiddenDiv"';
	echo	'><div class="option"><label for="tt[tt_supr_username]">' .
		__('Su.pr Username (Optional)', 'tweet-this') . ':</label>' .
		'<input type="text" name="tt[tt_supr_username]" ' .
		'id="tt[tt_supr_username]" size="50" value="' .
		tt_option('tt_supr_username') . '" /></div><div class=' .
		'"option" style="padding-bottom:5px;"><label for=' .
		'"tt[tt_supr_api_key]">' . __('Su.pr API Key (Optional)',
		'tweet-this') . ':</label><input type="text" name=' .
		'"tt[tt_supr_api_key]" id="tt[tt_supr_api_key]" size="50" ' .
		'value="' . tt_option('tt_supr_api_key') . '" /></div></div>' .
		'<div id="th8.us"';
	if($u != 'th8.us' && $u2 != 'th8.us')
		echo ' class="hiddenDiv"'; echo ' style="width:800px;' .
		'padding-bottom:6px;">' . __('Please note that <a href="' .
		'http://th8.us/">Th8.us</a> is owned by the creator of Tweet' .
		' This and includes ads above your website in an iFrame (<a ' .
		'href="http://yd7ch.th8.us/">example</a>). If you want your ' .
		'website removed from the Th8.us database, change URL ' .
		'services and email me at <a href="mailto:richardxthripp@' .
		'thripp.com">richardxthripp@thripp.com</a>.', 'tweet-this') .
		'</div><div id="tweetburner"';
	if($u != 'tweetburner' && $u2 != 'tweetburner')
		echo ' class="hiddenDiv"'; echo ' style="width:800px;' .
		'padding-bottom:6px;">' . __('Do not enable "Use "www." ' .
		'instead of "http://" in shortened URLs" because ' .
		'Tweetburner does not work with the www subdomain; your ' .
		'URLs will just redirect to the Tweetburner home page.',
		'tweet-this') . '</div>';
	echo	'<div style="width:800px;padding-top:0px;' .
		'padding-bottom:3px;"><hr /></div>';
	if(tt_option('tt_twitter_icon') == 'textbox')
		echo	'<p><em>' . __('For the editable text box, "Link ' .
			'Text" serves as the submit button\'s text. "Tweet ' .
			'This Post" will be used if it is set to "[BLANK]".',
			'tweet-this') . '</em></p>';
	echo	'<div class="opttwtins"><label for="tt[tt_auto_display]" ' .
		'class="ins"><input type="checkbox" ' .
		'name="tt[tt_auto_display]" id="tt[tt_auto_display]"';
	if(tt_option('tt_auto_display') != 'false')
		echo	$v;
	echo	' /> ' . __('Insert Twitter Tweet This links using:',
		'tweet-this') . '</label>';
	if(tt_option('tt_twitter_share') == 'true') {
		$tws_true = $v; $tws_false = '';}
	else {	$tws_false = $v; $tws_true = '';}
	echo	' <input type="radio" name="tt[tt_twitter_share]" id=' .
		'"tws-01" value="web"' . $tws_false . ' /> ' .
		'<label for="tws-01">' . __('Web Links', 'tweet-this') .
		'</label> ' . __('(<a href="http://twitter.com/home/?status=' .
		'Tweet+This+Plugin+for+WordPress+http://bit.ly/bNL4WB" ' .
		'target="_blank">Example</a>)', 'tweet-this') . ' &nbsp;' .
		' <input type="radio" name="tt[tt_twitter_share]" id=' .
		'"tws-02" value="share"' . $tws_true . ' /> <label for=' .
		'"tws-02">' . __('Share Links', 'tweet-this') . '</label> ' .
		__('(<a href="http://twitter.com/share?url=http://' .
		'richardxthripp.thripp.com/tweet-this/&text=Tweet+This+' .
		'Plugin+for+WordPress&via=tweetthisplugin&related=' .
		'richardxthripp%2Ctweetthisplugin" target="_blank">Example' .
		'</a>)', 'tweet-this') . '</div><div class="opttwt"><label ' .
		'for="tt[tt_tweet_text]">' . __('WEB Tweet Text (Can use # ' .
		'and @ symbols):', 'tweet-this') . '</label><input type=' .
		'"text" name="tt[tt_tweet_text]" id="tt[tt_tweet_text]" ' .
		'size="50" value="';
	if(tt_option('tt_tweet_text') == '')
		echo	'[TITLE] [URL]';
	else echo	tt_option('tt_tweet_text');
	echo	'" /></div><div class="opttwt"><label for="tt[tt_link_' .
		'text]">' . __('WEB/SHARE Link Text or [BLANK] for none:',
		'tweet-this') . '</label><input type="text" name=' .
		'"tt[tt_link_text]" id="tt[tt_link_text]" size="50" value="';
	if(tt_option('tt_link_text') == '')
		echo	__('Tweet This Post', 'tweet-this');
	else echo	tt_option('tt_link_text');
	echo	'" /></div><div class="opttwt"><label for="tt[tt_title_text]' .
		'">' . __('WEB/SHARE Link Title Attribute or [BLANK] for ' .
		'none:', 'tweet-this') . '</label><input type="text" name=' .
		'"tt[tt_title_text]" id="tt[tt_title_text]" size="50" value="';
	if(tt_option('tt_title_text') == '')
		echo	__('Post to Twitter', 'tweet-this');
	else echo	tt_option('tt_title_text');
	echo	'" /></div><div class="opttwt"><label for="tt[tt_twitter_' .
		'share_text]">' . __('SHARE Tweet Text (Before URL) or ' .
		'[BLANK] for none:', 'tweet-this') . '</label><input type=' .
		'"text" name="tt[tt_twitter_share_text]" id=' .
		'"tt[tt_twitter_share_text]" size="50" value="';
	if(tt_option('tt_twitter_share_text') == '')
		echo	'[TITLE_SHARE]';
	else echo	tt_option('tt_twitter_share_text');
	echo	'" /></div><div class="opttwt"><label for="tt[tt_twitter_' .
		'share_via]">' . __('SHARE Via Twitter User or [BLANK] for ' .
		'none:', 'tweet-this') . '</label><input type="text" name=' .
		'"tt[tt_twitter_share_via]" id="tt[tt_twitter_share_via]" ' .
		'size="50" value="';
	if(tt_option('tt_twitter_share_via') == '')
		echo	'tweetthisplugin';
	else echo	tt_option('tt_twitter_share_via');
	echo	'" /></div><div class="opttwt"><label for="tt[tt_twitter_' .
		'share_rel]">' . __('SHARE Related User(s) (2 max) or ' .
		'[BLANK] for none:', 'tweet-this') . '</label><input type=' .
		'"text" name="tt[tt_twitter_share_rel]" id=' .
		'"tt[tt_twitter_share_rel]" size="50" value="';
	if(tt_option('tt_twitter_share_rel') == '')
		echo	'richardxthripp,tweetthisplugin';
	else echo	tt_option('tt_twitter_share_rel');
	echo	'" /></div>';
	tt_image_selection('twitter');
	echo	'<div><p><strong>' . __('Shortcodes available in Twitter ' .
		'and Extended Services text fields:', 'tweet-this') .
		'</strong></p><p>[TITLE] [TITLE_SHARE] (SHARE Tweet Text ' .
		'only) [URL] [AUTHOR] [CATEGORY] [DATE] [TIME] [EXCERPT] ' .
		'[BLOG_TITLE]</p></div><div style="width:800px;"><hr /></div>';
	echo	'<p onclick="expandcontent(\'s0\')" style="cursor:hand;' .
		'cursor:pointer;"><u><strong>' . __('Automatic Tweeting',
		'tweet-this') . '</strong></u></p><div id="s0" class=' .
		'"switchcontent"><div style="width:800px;">';
	// OAuth replaced Basic Auth in v1.7.
		printf(__('<p><em>To use automatic tweeting, you must ' .
		'register an application with Twitter.</em></p>' .
		'<p><strong><em>Step 1</em></strong>: <a href="http://dev.' .
		'twitter.com/apps/new" target="_blank"><strong>Click here' .
		'</strong></a> to open the form in a new window and fill out' .
		' the application as follows:</p>' .
		'<table border="3" cellspacing="3" cellpadding="3"><tbody>' .
		'<tr><td>Application&nbsp;Name:&nbsp;&nbsp;</td><td><input ' .
		'type="text" id="tto1" value="%1$s" onfocus="this.select()" ' .
		'readonly="readonly" size="50" />&nbsp;&nbsp;</td><td>[OK' .
		'&nbsp;to&nbsp;change]</td></tr><tr><td>Description:&nbsp;' .
		'&nbsp;</td><td><input type="text" id="tto2" value="%2$s" ' .
		'onfocus="this.select()" readonly="readonly" size="50" />' .
		'&nbsp;&nbsp;</td><td>[OK&nbsp;to&nbsp;change]</td></tr>' .
		'<tr><td>Application&nbsp;Website:&nbsp;&nbsp;</td><td>' .
		'<input type="text" id="tto3" value="%2$s" onfocus="this.' .
		'select()" readonly="readonly" size="50" />&nbsp;&nbsp;</td>' .
		'<td>[OK&nbsp;to&nbsp;change]</td></tr><tr><td>Application' .
		'&nbsp;Type:&nbsp;&nbsp;</td><td><input type="text" ' .
		'id="tto4" value="Browser" onfocus="this.select()" readonly=' .
		'"readonly" size="50" />&nbsp;&nbsp;</td><td>[the&nbsp;' .
		'default]</td></tr><tr><td>Callback&nbsp;URL:&nbsp;&nbsp;' .
		'</td><td><input type="text" id="tto5" value="%2$s" onfocus=' .
		'"this.select()" readonly="readonly" size="50" />&nbsp;' .
		'&nbsp;</td><td>[OK&nbsp;to&nbsp;change]</td></tr><tr><td>' .
		'Default&nbsp;Access&nbsp;type:&nbsp;&nbsp;</td><td><input ' .
		'type="text" id="tto6" value="Read &amp; Write" onfocus=' .
		'"this.select()" readonly="readonly" size="50" />&nbsp;' .
		'&nbsp;</td><td>[<strong>NOT&nbsp;the&nbsp;default</strong>]' .
		'</td></tr></tbody></table>' .
		'<p><strong><em>Step 2</em></strong>: Enter the CAPTCHA, ' .
		'click Register, and agree to the terms.</p>' .
		'<p><strong><em>Step 3</em></strong>: Copy and paste ' .
		'<strong>Consumer key</strong> and <strong>Consumer secret' .
		'</strong> below.</p>' .
		'<p><strong><em>Step 4</em></strong>: Click "My Access ' .
		'Token" on the right, and then, copy and paste <strong>' .
		'Access Token</strong> and <strong>Access Token Secret' .
		'</strong> below.</p>' .
		'<p><strong><em>Step 5</em></strong>: Click <strong>Test ' .
		'Twitter OAuth</strong> below. If you get "Authentication ' .
		'succeeded", click <strong>Save Options</strong> at the ' .
		'bottom.</p>', 'tweet-this'), str_replace('http://', '',
		get_bloginfo('url')), get_bloginfo('url'));
		echo '</div><div class="option"><label for="tt_app_consumer_' .
		'key">' . __('Twitter Consumer Key', 'tweet-this') .
		':</label><input type="text" name="tt_app_consumer_key" ' .
		'id="tt_app_consumer_key" size="65" value="' .
		tt_option('tt_app_consumer_key') . '" autocomplete="off" />' .
		'</div><div class="option"><label for="tt_app_consumer_' .
		'secret">' . __('Twitter Consumer Secret', 'tweet-this') .
		':</label><input type="text" name="tt_app_consumer_secret" ' .
		'id="tt_app_consumer_secret" size="65" value="' .
		tt_option('tt_app_consumer_secret') . '" autocomplete="off" ' .
		'/></div><div class="option"><label for="tt_oauth_token">' .
		__('Twitter Access Token', 'tweet-this') . ':</label><input ' .
		'type="text" name="tt_oauth_token" id="tt_oauth_token" ' .
		'size="65" value="' . tt_option('tt_oauth_token') .
		'" autocomplete="off" /></div><div class="option"><label ' .
		'for="tt_oauth_token_secret">' .
		__('Twitter Access Token Secret', 'tweet-this') . ':</label>' .
		'<input type="text" name="tt_oauth_token_secret" id="tt_' .
		'oauth_token_secret" size="65" value="' .
		tt_option('tt_oauth_token_secret') . '" autocomplete="off" ' .
		'/></div><div class="option"><label for="tt[tt_auto_tweet_' .
		'text]">' . __('Default Auto Tweet Text', 'tweet-this') .
		':</label><input type="text" name="tt[tt_auto_tweet_text]" ' .
		'id="tt[tt_auto_tweet_text]" size="65" value="';
	if(tt_option('tt_auto_tweet_text') == '')
		echo	__('New blog post', 'tweet-this') . ': [TITLE] [URL]';
	else echo	tt_option('tt_auto_tweet_text');
	echo	'" /></div><p><input type="button" class="button" name="tt_' .
		'oauth_test" id="tt_oauth_test" value="' .
		__('Test Twitter OAuth', 'tweet-this') . '" onclick="' .
		'ttTestOAuth(); return false;" /><span id="tt_oauth_test_' .
		'result"></span></p><p><label><input type="checkbox" ' .
		'name="tt[tt_auto_tweet_display]"';
	if(tt_option('tt_auto_tweet_display') != 'false')
		echo	$v;
	echo	' /> ' . __('Enable automatic tweeting on posts',
		'tweet-this') . '</label></p><p><label><input type=' .
		'"checkbox" name="tt[tt_auto_tweet_display_pages]"';
	if(tt_option('tt_auto_tweet_display_pages') != 'false')
		echo	$v;
	echo	' /> ' . __('Enable automatic tweeting on pages',
		'tweet-this') . '</label></p><p><label><input type="' .
		'checkbox" name="tt[tt_auto_tweet]"';
	if(tt_option('tt_auto_tweet') == 'true')
		echo	$v;
	echo	' /> ' . __('"Send to Twitter" defaults to checked on ' .
		'unpublished posts', 'tweet-this') . '</label></p><p><label>' .
		'<input type="checkbox" name="tt[tt_auto_tweet_pages]"';
	if(tt_option('tt_auto_tweet_pages') == 'true')
		echo	$v;
	echo	' /> ' . __('"Send to Twitter" defaults to checked on ' .
		'unpublished pages', 'tweet-this') . '</label></p>' .
		'<div style="width:800px;padding-top:0;padding-bottom:0;' .
		'"><p style="width:800px;"><em>' . __('After entering your ' .
		'OAuth keys, "Write Tweet" will appear as a menu item under ' .
		'the "Posts" section (this may take two pageloads).',
		'tweet-this') . '</em></p><hr /></div></div>' .
		'<p onclick="expandcontent(\'s1\')" style="cursor:' .
		'hand;cursor:pointer;"><u><strong>' . __('Advanced Options',
		'tweet-this') . '</strong></u></p><div id="s1" class=' .
		'"switchcontent"><p><label class="in">' .
		__('Alignment of Links', 'tweet-this') .
		': </label><select name="tt[tt_alignment]" ' .
		'id="tt[tt_alignment]"><option value="left"';
	if(tt_option('tt_alignment') == 'left')
		echo	$s;
	echo	'>' . __('Left', 'tweet-this') . '</option>' .
		'<option value="right"';
	if(tt_option('tt_alignment') == 'right')
		echo	$s;
	echo	'>' . __('Right', 'tweet-this') . '</option>' .
		'<option value="center"';
	if(tt_option('tt_alignment') == 'center')
		echo	$s;
	echo	'>' . __('Center', 'tweet-this') . ' &nbsp; </option>' .
		'</select> ' . __('Image CSS Class', 'tweet-this') .
		': <input type="text" name="tt[tt_img_css_class]" ' .
		'id="tt[tt_img_css_class]" size="15" value="';
	if(tt_option('tt_img_css_class') == '')
		echo	'nothumb';
	else echo	tt_option('tt_img_css_class');
	echo	'" /> ' . __('Link CSS Class', 'tweet-this') .
		': <input type="text" name="tt[tt_link_css_class]" ' .
		'id="tt[tt_link_css_class]" size="10" value="';
	if(tt_option('tt_link_css_class') == '')
		echo	'tt';
	else echo	tt_option('tt_link_css_class');
	echo	'" /></p><p>' . __('CSS to insert or [BLANK] for none, or ' .
		'[IMG_CLASS] and [LINK_CLASS] for the variables above.',
		'tweet-this') . '</p><p><input type="text" ' .
		'name="tt[tt_css]" id="tt[tt_css]" size="100" value="';
	if(tt_option('tt_css') == '') echo 'img.[IMG_CLASS]{border:0;' .
		'margin:0 0 0 2px !important;}';
	else echo	tt_option('tt_css');
	echo	'" /></p><p><label>' .
		'<input type="checkbox" name="tt[tt_shortlink_filter]"';
	if(tt_option('tt_shortlink_filter') != 'false')
		echo	$v;
	echo	' /> ' . __('Pass shortened URLs to WordPress via the ' .
		'get_shortlink filter if the URL service is not set to ' .
		'Local.<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(This will only ' .
		'add a rel="shortlink" link to wp_head in WordPress 2.9.2 ' .
		'or earlier.)', 'tweet-this') . '</label></p><p><label>' .
		'<input type="checkbox" name="tt[tt_limit_to_single]"';
	if(tt_option('tt_limit_to_single') == 'true')
		echo	$v;
	echo	' /> ' . __('Only show Tweet This when viewing single posts ' .
		'or pages.', 'tweet-this') . '</label></p><p><label>' .
		'<input type="checkbox" name="tt[tt_limit_to_posts]"';
	if(tt_option('tt_limit_to_posts') == 'true')
		echo	$v;
	echo	' /> ' . __('Hide Tweet This on pages (will override ' .
		'checkbox above).', 'tweet-this') . '</label></p><p><label>' .
		'<input type="checkbox" name="tt[tt_url_www]"';
	if(tt_option('tt_url_www') == 'true')
		echo	$v;
	echo	' /> ' . __('Use "www." instead of "http://" in shortened ' .
		'URLs.', 'tweet-this') . '</label></p><p><label>' .
		'<input type="checkbox" name="tt[tt_30]"';
	if(tt_option('tt_30') == 'true')
		echo	$v;
	echo	' /> ' . __('Use full permalinks unless Tweet/Plurk Text ' .
		'exceeds 140 characters.', 'tweet-this') . '</label></p><p>' .
		'<label><input type="checkbox" name="tt[tt_new_window]"';
	if(tt_option('tt_new_window') == 'true')
		echo	$v;
	echo	' /> ' . __('Add target="_blank" attribute to links so they ' .
		'open in new windows.', 'tweet-this') . '</label></p><p>' .
		'<label><input type="checkbox" name="tt[tt_nofollow]"';
	if(tt_option('tt_nofollow') == 'true')
		echo	$v;
	echo	' /> ' . __('Add rel="nofollow" attribute to links so ' .
		'search engines exclude them from page ranking.',
		'tweet-this') . '</label></p><p><label><input type=' .
		'"checkbox" name="tt[tt_footer]"';
	if(tt_option('tt_footer') == 'true')
		echo	$v;
	echo	' /> ' . __('Insert Tweet This message in footer.',
		'tweet-this') . '</label></p><p><label>' .
		'<input type="checkbox" name="tt[tt_ads]"';
	if(tt_option('tt_ads') == 'true')
		echo	$v;
	echo	' /> ' . __('Insert Google AdSense ads to support Tweet This.',
		'tweet-this') . '</label></p><p><em>' . __('Some advanced ' .
		'options can only be set in the tt-config.php file in the ' .
		'tweet-this directory.', 'tweet-this') . '</em></p>' .
		'<div style="width:800px;padding-top:0;padding-bottom:0;' .
		'"><hr /></div></div><p onclick="expandcontent(\'s2\')" ' .
		'style="cursor:hand;cursor:pointer;"><u><strong>' .
		__('Extended Services', 'tweet-this') . '</strong></u></p>' .
		'<div id="s2" class="switchcontent"><p><label class="in">' .
		'<input type="checkbox" name="tt[tt_plurk]"';
	if(tt_option('tt_plurk') == 'true')
		echo	$v;
	echo	' /> ' . __('Insert Plurk This', 'tweet-this') .
		'</label> | ' . __('Plurk Text', 'tweet-this') .
		': <input type="text" name="tt[tt_plurk_text]" ' .
		'id="tt[tt_plurk_text]" size="24" value="';
	if(tt_option('tt_plurk_text') == '')
		echo	'[TITLE] [URL]';
	else echo	tt_option('tt_plurk_text');
	echo	'" /> ' . __('Link', 'tweet-this') . ': <input type="text" ' .
		'name="tt[tt_plurk_link_text]" id="tt[tt_plurk_link_text]" ' .
		'size="25" value="';
	if(tt_option('tt_plurk_link_text') == '')
		echo	__('Plurk This Post', 'tweet-this');
	else echo	tt_option('tt_plurk_link_text');
	echo	'" /> ' . __('Title', 'tweet-this') . ': ' .
		'<input type="text" name="tt[tt_plurk_title_text]" ' .
		'id="tt[tt_plurk_title_text]" size="22" ' .
		'value="';
	if(tt_option('tt_plurk_title_text') == '')
		echo	__('Post to Plurk', 'tweet-this');
	else echo	tt_option('tt_plurk_title_text');
	echo	'" /></p>';
	tt_image_selection('plurk');
	echo	'<p><label class="in"><input type="checkbox" ' .
		'name="tt[tt_bebo]"';
	if(tt_option('tt_bebo') == 'true')
		echo	$v;
	echo	' /> ' . __('Insert Bebo', 'tweet-this') .
		'</label> | ' . __('Link', 'tweet-this') .
		': <input type="text" name="tt[tt_bebo_link_text]" ' .
		'id="tt[tt_bebo_link_text]" size="49" value="';
	if(tt_option('tt_bebo_link_text') == '')
		echo	__('Post to Bebo', 'tweet-this');
	else echo	tt_option('tt_bebo_link_text');
	echo	'" /> ' . __('Title', 'tweet-this') . ': ' .
		'<input type="text" name="tt[tt_bebo_title_text]" ' .
		'id="tt[tt_bebo_title_text]" size="44" ' .
		'value="';
	if(tt_option('tt_bebo_title_text') == '')
		echo	__('Post to Bebo', 'tweet-this');
	else echo	tt_option('tt_bebo_title_text');
	echo	'" /></p>';
	tt_image_selection('bebo');
	echo	'<p><label class="in"><input type="checkbox" ' .
		'name="tt[tt_buzz]"';
	if(tt_option('tt_buzz') == 'true')
		echo	$v;
	echo	' /> ' . __('Insert Buzz This (Yahoo)', 'tweet-this') .
		'</label> | ' . __('Link', 'tweet-this') .
		': <input type="text" name="tt[tt_buzz_link_text]" ' .
		'id="tt[tt_buzz_link_text]" size="42" value="';
	if(tt_option('tt_buzz_link_text') == '')
		echo	__('Buzz This Post', 'tweet-this');
	else echo	tt_option('tt_buzz_link_text');
	echo	'" /> ' . __('Title', 'tweet-this') . ': ' .
		'<input type="text" name="tt[tt_buzz_title_text]" ' .
		'id="tt[tt_buzz_title_text]" size="38" ' .
		'value="';
	if(tt_option('tt_buzz_title_text') == '')
		echo	__('Post to Yahoo Buzz', 'tweet-this');
	else echo	tt_option('tt_buzz_title_text');
	echo	'" /></p>';
	tt_image_selection('buzz');
	echo	'<p><label class="in"><input type="checkbox" ' .
		'name="tt[tt_delicious]"';
	if(tt_option('tt_delicious') == 'true')
		echo	$v;
	echo	' /> ' . __('Insert Delicious', 'tweet-this') .
		'</label> | ' . __('Link', 'tweet-this') .
		': <input type="text" name="tt[tt_delicious_link_text]" ' .
		'id="tt[tt_delicious_link_text]" size="46" value="';
	if(tt_option('tt_delicious_link_text') == '')
		echo	__('Post to Delicious', 'tweet-this');
	else echo	tt_option('tt_delicious_link_text');
	echo	'" /> ' . __('Title', 'tweet-this') . ': ' .
		'<input type="text" name="tt[tt_delicious_title_text]" ' .
		'id="tt[tt_delicious_title_text]" size="43" ' .
		'value="';
	if(tt_option('tt_delicious_title_text') == '')
		echo	__('Post to Delicious', 'tweet-this');
	else echo	tt_option('tt_delicious_title_text');
	echo	'" /></p>';
	tt_image_selection('delicious');
	echo	'<p><label class="in"><input type="checkbox" ' .
		'name="tt[tt_digg]"';
	if(tt_option('tt_digg') == 'true')
		echo	$v;
	echo	' /> ' . __('Insert Digg This', 'tweet-this') .
		'</label> | ' . __('Link', 'tweet-this') .
		': <input type="text" name="tt[tt_digg_link_text]" ' .
		'id="tt[tt_digg_link_text]" size="46" value="';
	if(tt_option('tt_digg_link_text') == '')
		echo	__('Digg This Post', 'tweet-this');
	else echo	tt_option('tt_digg_link_text');
	echo	'" /> ' . __('Title', 'tweet-this') . ': ' .
		'<input type="text" name="tt[tt_digg_title_text]" ' .
		'id="tt[tt_digg_title_text]" size="43" ' .
		'value="';
	if(tt_option('tt_digg_title_text') == '')
		echo	__('Post to Digg', 'tweet-this');
	else echo	tt_option('tt_digg_title_text');
	echo	'" /></p>';
	tt_image_selection('digg');
	echo	'<p><label class="in"><input type="checkbox" ' .
		'name="tt[tt_diigo]"';
	if(tt_option('tt_diigo') == 'true')
		echo	$v;
	echo	' /> ' . __('Insert Diigo', 'tweet-this') .
		'</label> | ' . __('Link', 'tweet-this') .
		': <input type="text" name="tt[tt_diigo_link_text]" ' .
		'id="tt[tt_diigo_link_text]" size="49" value="';
	if(tt_option('tt_diigo_link_text') == '')
		echo	__('Post to Diigo', 'tweet-this');
	else echo	tt_option('tt_diigo_link_text');
	echo	'" /> ' . __('Title', 'tweet-this') . ': ' .
		'<input type="text" name="tt[tt_diigo_title_text]" ' .
		'id="tt[tt_diigo_title_text]" size="44" ' .
		'value="';
	if(tt_option('tt_diigo_title_text') == '')
		echo	__('Post to Diigo', 'tweet-this');
	else echo	tt_option('tt_diigo_title_text');
	echo	'" /></p>';
	tt_image_selection('diigo');
	echo	'<p><label class="in"><input type="checkbox" ' .
		'name="tt[tt_facebook]"';
	if(tt_option('tt_facebook') == 'true')
		echo	$v;
	echo	' /> ' . __('Insert Facebook', 'tweet-this') .
		'</label> | ' . __('Link', 'tweet-this') .
		': <input type="text" name="tt[tt_facebook_link_text]" ' .
		'id="tt[tt_facebook_link_text]" size="45" value="';
	if(tt_option('tt_facebook_link_text') == '')
		echo	__('Post to Facebook', 'tweet-this');
	else echo	tt_option('tt_facebook_link_text');
	echo	'" /> ' . __('Title', 'tweet-this') . ': ' .
		'<input type="text" name="tt[tt_facebook_title_text]" ' .
		'id="tt[tt_facebook_title_text]" size="43" ' .
		'value="';
	if(tt_option('tt_facebook_title_text') == '')
		echo	__('Post to Facebook', 'tweet-this');
	else echo	tt_option('tt_facebook_title_text');
	echo	'" /></p>';
	tt_image_selection('facebook');
	echo	'<p><label class="in"><input type="checkbox" ' .
		'name="tt[tt_ff]"';
	if(tt_option('tt_ff') == 'true')
		echo	$v;
	echo	' /> ' . __('Insert FriendFeed', 'tweet-this') .
		'</label> | ' . __('Link', 'tweet-this') .
		': <input type="text" name="tt[tt_ff_link_text]" ' .
		'id="tt[tt_ff_link_text]" size="45" value="';
	if(tt_option('tt_ff_link_text') == '')
		echo	__('Post to FriendFeed', 'tweet-this');
	else echo	tt_option('tt_ff_link_text');
	echo	'" /> ' . __('Title', 'tweet-this') . ': ' .
		'<input type="text" name="tt[tt_ff_title_text]" ' .
		'id="tt[tt_ff_title_text]" size="41" ' .
		'value="';
	if(tt_option('tt_ff_title_text') == '')
		echo	__('Post to FriendFeed', 'tweet-this');
	else echo	tt_option('tt_ff_title_text');
	echo	'" /></p>';
	tt_image_selection('ff');
	echo	'<p><label class="in"><input type="checkbox" ' .
		'name="tt[tt_gbuzz]"';
	if(tt_option('tt_gbuzz') == 'true')
		echo	$v;
	echo	' /> ' . __('Insert Google Buzz', 'tweet-this') .
		'</label> | ' . __('Link', 'tweet-this') .
		': <input type="text" name="tt[tt_gbuzz_link_text]" ' .
		'id="tt[tt_gbuzz_link_text]" size="45" value="';
	if(tt_option('tt_gbuzz_link_text') == '')
		echo	__('Post to Google Buzz', 'tweet-this');
	else echo	tt_option('tt_gbuzz_link_text');
	echo	'" /> ' . __('Title', 'tweet-this') . ': ' .
		'<input type="text" name="tt[tt_gbuzz_title_text]" ' .
		'id="tt[tt_gbuzz_title_text]" size="40" ' .
		'value="';
	if(tt_option('tt_gbuzz_title_text') == '')
		echo	__('Post to Google Buzz', 'tweet-this');
	else echo	tt_option('tt_gbuzz_title_text');
	echo	'" /></p>';
	tt_image_selection('gbuzz');
	echo	'<p><label class="in"><input type="checkbox" ' .
		'name="tt[tt_gmail]"';
	if(tt_option('tt_gmail') == 'true')
		echo	$v;
	echo	' /> ' . __('Insert Gmail', 'tweet-this') .
		'</label> | ' . __('Link', 'tweet-this') .
		': <input type="text" name="tt[tt_gmail_link_text]" ' .
		'id="tt[tt_gmail_link_text]" size="49" value="';
	if(tt_option('tt_gmail_link_text') == '')
		echo	__('Send Gmail', 'tweet-this');
	else echo	tt_option('tt_gmail_link_text');
	echo	'" /> ' . __('Title', 'tweet-this') . ': ' .
		'<input type="text" name="tt[tt_gmail_title_text]" ' .
		'id="tt[tt_gmail_title_text]" size="43" ' .
		'value="';
	if(tt_option('tt_gmail_title_text') == '')
		echo	__('Send Gmail', 'tweet-this');
	else echo	tt_option('tt_gmail_title_text');
	echo	'" /></p>';
	tt_image_selection('gmail');
	echo	'<p><label class="in"><input type="checkbox" ' .
		'name="tt[tt_linkedin]"';
	if(tt_option('tt_linkedin') == 'true')
		echo	$v;
	echo	' /> ' . __('Insert LinkedIn', 'tweet-this') .
		'</label> | ' . __('Link', 'tweet-this') .
		': <input type="text" name="tt[tt_linkedin_link_text]" ' .
		'id="tt[tt_linkedin_link_text]" size="47" value="';
	if(tt_option('tt_linkedin_link_text') == '')
		echo	__('Post to LinkedIn', 'tweet-this');
	else echo	tt_option('tt_linkedin_link_text');
	echo	'" /> ' . __('Title', 'tweet-this') . ': ' .
		'<input type="text" name="tt[tt_linkedin_title_text]" ' .
		'id="tt[tt_linkedin_title_text]" size="42" ' .
		'value="';
	if(tt_option('tt_linkedin_title_text') == '')
		echo	__('Post to LinkedIn', 'tweet-this');
	else echo	tt_option('tt_linkedin_title_text');
	echo	'" /></p>';
	tt_image_selection('linkedin');
	echo	'<p><label class="in"><input type="checkbox" ' .
		'name="tt[tt_mixx]"';
	if(tt_option('tt_mixx') == 'true')
		echo	$v;
	echo	' /> ' . __('Insert Mixx This', 'tweet-this') .
		'</label> | ' . __('Link', 'tweet-this') .
		': <input type="text" name="tt[tt_mixx_link_text]" ' .
		'id="tt[tt_mixx_link_text]" size="47" value="';
	if(tt_option('tt_mixx_link_text') == '')
		echo	__('Mixx This Post', 'tweet-this');
	else echo	tt_option('tt_mixx_link_text');
	echo	'" /> ' . __('Title', 'tweet-this') . ': ' .
		'<input type="text" name="tt[tt_mixx_title_text]" ' .
		'id="tt[tt_mixx_title_text]" size="42" ' .
		'value="';
	if(tt_option('tt_mixx_title_text') == '')
		echo	__('Post to Mixx', 'tweet-this');
	else echo	tt_option('tt_mixx_title_text');
	echo	'" /></p>';
	tt_image_selection('mixx');
	echo	'<p><label class="in"><input type="checkbox" ' .
		'name="tt[tt_myspace]"';
	if(tt_option('tt_myspace') == 'true')
		echo	$v;
	echo	' /> ' . __('Insert Myspace', 'tweet-this') .
		'</label> | ' . __('Link', 'tweet-this') .
		': <input type="text" name="tt[tt_myspace_link_text]" ' .
		'id="tt[tt_myspace_link_text]" size="46" value="';
	if(tt_option('tt_myspace_link_text') == '')
		echo	__('Post to MySpace', 'tweet-this');
	else echo	tt_option('tt_myspace_link_text');
	echo	'" /> ' . __('Title', 'tweet-this') . ': ' .
		'<input type="text" name="tt[tt_myspace_title_text]" ' .
		'id="tt[tt_myspace_title_text]" size="43" ' .
		'value="';
	if(tt_option('tt_myspace_title_text') == '')
		echo	__('Post to MySpace', 'tweet-this');
	else echo	tt_option('tt_myspace_title_text');
	echo	'" /></p>';
	tt_image_selection('myspace');
	echo	'<p><label class="in"><input type="checkbox" ' .
		'name="tt[tt_ping]"';
	if(tt_option('tt_ping') == 'true')
		echo	$v;
	echo	' /> ' . __('Insert Ping This', 'tweet-this') .
		'</label> | ' . __('Link', 'tweet-this') .
		': <input type="text" name="tt[tt_ping_link_text]" ' .
		'id="tt[tt_ping_link_text]" size="46" value="';
	if(tt_option('tt_ping_link_text') == '')
		echo	__('Ping This Post', 'tweet-this');
	else echo	tt_option('tt_ping_link_text');
	echo	'" /> ' . __('Title', 'tweet-this') . ': ' .
		'<input type="text" name="tt[tt_ping_title_text]" ' .
		'id="tt[tt_ping_title_text]" size="43" ' .
		'value="';
	if(tt_option('tt_ping_title_text') == '')
		echo	__('Post to Ping.fm', 'tweet-this');
	else echo	tt_option('tt_ping_title_text');
	echo	'" /></p>';
	tt_image_selection('ping');
	echo	'<p><label class="in"><input type="checkbox" ' .
		'name="tt[tt_reddit]"';
	if(tt_option('tt_reddit') == 'true')
		echo	$v;
	echo	' /> ' . __('Insert Reddit', 'tweet-this') .
		'</label> | ' . __('Link', 'tweet-this') .
		': <input type="text" name="tt[tt_reddit_link_text]" ' .
		'id="tt[tt_reddit_link_text]" size="47" value="';
	if(tt_option('tt_reddit_link_text') == '')
		echo	__('Post to Reddit', 'tweet-this');
	else echo	tt_option('tt_reddit_link_text');
	echo	'" /> ' . __('Title', 'tweet-this') . ': ' .
		'<input type="text" name="tt[tt_reddit_title_text]" ' .
		'id="tt[tt_reddit_title_text]" size="45" ' .
		'value="';
	if(tt_option('tt_reddit_title_text') == '')
		echo	__('Post to Reddit', 'tweet-this');
	else echo	tt_option('tt_reddit_title_text');
	echo	'" /></p>';
	tt_image_selection('reddit');
	echo	'<p><label class="in"><input type="checkbox" ' .
		'name="tt[tt_slashdot]"';
	if(tt_option('tt_slashdot') == 'true')
		echo	$v;
	echo	' /> ' . __('Insert Slashdot', 'tweet-this') .
		'</label> | ' . __('Link', 'tweet-this') .
		': <input type="text" name="tt[tt_slashdot_link_text]" ' .
		'id="tt[tt_slashdot_link_text]" size="48" value="';
	if(tt_option('tt_slashdot_link_text') == '')
		echo	__('Post to Slashdot', 'tweet-this');
	else echo	tt_option('tt_slashdot_link_text');
	echo	'" /> ' . __('Title', 'tweet-this') . ': ' .
		'<input type="text" name="tt[tt_slashdot_title_text]" ' .
		'id="tt[tt_slashdot_title_text]" size="41" ' .
		'value="';
	if(tt_option('tt_slashdot_title_text') == '')
		echo	__('Post to Slashdot', 'tweet-this');
	else echo	tt_option('tt_slashdot_title_text');
	echo	'" /></p>';
	tt_image_selection('slashdot');
	echo	'<p><label class="in"><input type="checkbox" ' .
		'name="tt[tt_squidoo]"';
	if(tt_option('tt_squidoo') == 'true')
		echo	$v;
	echo	' /> ' . __('Insert Squidoo', 'tweet-this') .
		'</label> | ' . __('Link', 'tweet-this') .
		': <input type="text" name="tt[tt_squidoo_link_text]" ' .
		'id="tt[tt_squidoo_link_text]" size="48" value="';
	if(tt_option('tt_squidoo_link_text') == '')
		echo	__('Post to Squidoo', 'tweet-this');
	else echo	tt_option('tt_squidoo_link_text');
	echo	'" /> ' . __('Title', 'tweet-this') . ': ' .
		'<input type="text" name="tt[tt_squidoo_title_text]" ' .
		'id="tt[tt_squidoo_title_text]" size="42" ' .
		'value="';
	if(tt_option('tt_squidoo_title_text') == '')
		echo	__('Post to Squidoo', 'tweet-this');
	else echo	tt_option('tt_squidoo_title_text');
	echo	'" /></p>';
	tt_image_selection('squidoo');
	echo	'<p><label class="in"><input type="checkbox" name="tt[tt_su]"';
	if(tt_option('tt_su') == 'true')
		echo	$v;
	echo	' /> ' . __('Insert StumbleUpon', 'tweet-this') .
		'</label> | ' . __('Link', 'tweet-this') .
		': <input type="text" name="tt[tt_su_link_text]" ' .
		'id="tt[tt_su_link_text]" size="44" value="';
	if(tt_option('tt_su_link_text') == '')
		echo	__('Stumble This Post', 'tweet-this');
	else echo	tt_option('tt_su_link_text');
	echo	'" /> ' . __('Title', 'tweet-this') . ': ' .
		'<input type="text" name="tt[tt_su_title_text]" ' .
		'id="tt[tt_su_title_text]" size="40" ' .
		'value="';
	if(tt_option('tt_su_title_text') == '')
		echo	__('Post to StumbleUpon', 'tweet-this');
	else echo	tt_option('tt_su_title_text');
	echo	'" /></p>';
	tt_image_selection('su');
	echo	'<p><label class="in"><input type="checkbox" ' .
		'name="tt[tt_technorati]"';
	if(tt_option('tt_technorati') == 'true')
		echo	$v;
	echo	' /> ' . __('Insert Technorati', 'tweet-this') .
		'</label> | ' . __('Link', 'tweet-this') .
		': <input type="text" name="tt[tt_technorati_link_text]" ' .
		'id="tt[tt_technorati_link_text]" size="46" value="';
	if(tt_option('tt_technorati_link_text') == '')
		echo	__('Post to Technorati', 'tweet-this');
	else echo	tt_option('tt_technorati_link_text');
	echo	'" /> ' . __('Title', 'tweet-this') . ': ' .
		'<input type="text" name="tt[tt_technorati_title_text]" ' .
		'id="tt[tt_technorati_title_text]" size="41" ' .
		'value="';
	if(tt_option('tt_technorati_title_text') == '')
		echo	__('Post to Technorati', 'tweet-this');
	else echo	tt_option('tt_technorati_title_text');
	echo	'" /></p>';
	tt_image_selection('technorati');
	echo	'<p>' . __('<strong>Public Display Order</strong> [<em>Keep ' .
		'this comma delimited and do not delete services even if ' .
		'you are not using them.</em>]<br />[<strong>Legend:' .
		'</strong> &nbsp;buzz = Yahoo Buzz; ff = FriendFeed; gbuzz' .
		' = Google Buzz; su = StumbleUpon</em>]', 'tweet-this') .
		'</p><p><textarea type="text" cols="104" rows="2" name=' .
		'"tt[tt_service_order]" id="tt[tt_service_order]">' .
		tt_option('tt_service_order') . '</textarea></p>';
	// This seems pointless, but the Codex recommends it.
	echo	'</div><div style="width:800px;padding-top:0;padding-' .
		'bottom:2px;"><hr /></div><input type="hidden" name="action"' .
		' value="update" /><input type="hidden" name="page_options" ' .
		'value="tt[tt_30], tt[tt_url_service], ' .
		'tt[tt_admin_url_service], tt[tt_custom_url_service], ' .
		'tt[tt_alignment], tt[tt_limit_to_single], ' .
		'tt[tt_limit_to_posts], tt[tt_auto_tweet_display], ' .
		'tt[tt_auto_tweet_display_pages], tt[tt_auto_tweet], ' .
		'tt[tt_auto_tweet_pages], tt[tt_auto_tweet_text], ' .
		'tt_app_consumer_key, tt_app_consumer_secret, ' .
		'tt_oauth_token, tt_oauth_token_secret, ' .
		'tt[tt_textbox_size], tt[tt_url_www], tt[tt_footer], ' .
		'tt[tt_ads], tt[tt_new_window], tt[tt_nofollow], ' .
		'tt[tt_img_css_class], tt[tt_link_css_class], tt[tt_css], ' .
		'tt[tt_shortlink_filter], tt[tt_adjix_api_key], ' .
		'tt[tt_ad_vu], tt[tt_bitly_username], ' .
		'tt[tt_bitly_api_key], tt[tt_j_mp], ' .
		'tt[tt_snipurl_username], tt[tt_snipurl_api_key], ' .
		'tt[tt_snipurl_domain], tt[tt_supr_username], ' .
		'tt[tt_supr_api_key], ' .
		'[tt_auto_display], tt[tt_tweet_text], tt[tt_link_text], ' .
			'tt[tt_title_text], tt[tt_twitter_icon], ' .
			'tt[tt_twitter_share], tt[tt_twitter_share_text], ' .
			'tt[tt_twitter_share_via], ' .
			'tt[tt_twitter_share_rel], ' .
		'tt[tt[tt_plurk], tt[tt_plurk_text], ' .
			'tt[tt_plurk_link_text], tt[tt_plurk_title_text], ' .
			'tt[tt_plurk_icon], ' .
		'tt[tt_bebo], tt[tt_bebo_link_text], ' .
			'tt[tt_bebo_title_text], tt[tt_bebo_icon], ' .
		'tt[tt_buzz], tt[tt_buzz_link_text], ' .
			'tt[tt_buzz_title_text], tt[tt_buzz_icon], ' .
		'tt[tt_delicious], tt[tt_delicious_link_text], ' .
			'tt[tt_delicious_title_text], ' .
			'tt[tt_delicious_icon], ' .
		'tt[tt_digg], tt[tt_digg_link_text], ' .
			'tt[tt_digg_title_text], tt[tt_digg_icon], ' .
		'tt[tt_diigo], tt[tt_diigo_link_text], ' .
			'tt[tt_diigo_title_text], tt[tt_diigo_icon], ' .
		'tt[tt_facebook], tt[tt_facebook_link_text], ' .
			'tt[tt_facebook_title_text], tt[tt_facebook_icon], ' .
		'tt[tt_ff], tt[tt_ff_link_text], ' .
			'tt[tt_ff_title_text], tt[tt_ff_icon], ' .
		'tt[tt_gbuzz], tt[tt_gbuzz_link_text], ' .
			'tt[tt_gbuzz_title_text], tt[tt_gbuzz_icon], ' .
		'tt[tt_gmail], tt[tt_gmail_link_text], ' .
			'tt[tt_gmail_title_text], tt[tt_gmail_icon], ' .
		'tt[tt_linkedin], tt[tt_linkedin_link_text], ' .
			'tt[tt_linkedin_title_text], tt[tt_linkedin_icon], ' .
		'tt[tt_mixx], tt[tt_mixx_link_text], ' .
			'tt[tt_mixx_title_text], tt[tt_mixx_icon], ' .
		'tt[tt_myspace], tt[tt_myspace_link_text], ' .
			'tt[tt_myspace_title_text], tt[tt_myspace_icon], ' .
		'tt[tt_ping], tt[tt_ping_link_text], ' .
			'tt[tt_ping_title_text], tt[tt_ping_icon], ' .
		'tt[tt_reddit], tt[tt_reddit_link_text], ' .
			'tt[tt_reddit_title_text], tt[tt_reddit_icon], ' .
		'tt[tt_slashdot], tt[tt_slashdot_link_text], ' .
			'tt[tt_slashdot_title_text], tt[tt_slashdot_icon], ' .
		'tt[tt_squidoo], tt[tt_squidoo_link_text], ' .
			'tt[tt_squidoo_title_text], tt[tt_squidoo_icon], ' .
		'tt[tt_su], tt[tt_su_link_text], tt[tt_su_title_text], ' .
			'tt[tt_su_icon], ' .
		'tt[tt_technorati], tt[tt_technorati_link_text], ' .
			'tt[tt_technorati_title_text], ' .
			'tt[tt_technorati_icon], tt[tt_service_order]" />' .
		'<p class="submit" ' .
		'style="padding-top:0;padding-bottom:0;"><input ' .
		'type="submit" name="submit" class="button-primary" value="' .
		__('Save All Options', 'tweet-this') . '" title="' .
		__('If you do not want to save, leave this page without ' .
		'clicking this button.', 'tweet-this') . '" /> <input type=' .
		'"submit" name="reset" value="' . __('Reset All Options',
		'tweet-this') . '" title="' . __('This WILL flush the URL ' .
		'cache but will NOT delete scheduled tweets or postmeta.',
		'tweet-this') . '" onclick="return confirm (\'' .
		__('Are you sure you want to reset Tweet This to its ' .
		'default options? WARNING: Your OAuth keys and your ' .
		'Adjix.com, Bit.ly, SnipURL.com, and Su.pr logins will be ' .
		'reset.', 'tweet-this') . '\');" /> <input type="submit" ' .
		'name="partreset" value="' . __('Reset All Options Except ' .
		'Keys', 'tweet-this') . '" title=\'' . __('Same as "Reset ' .
		'All Options," except this preserves your OAuth and URL ' .
		'service keys.', 'tweet-this') . '\' onclick="return confirm' .
		' (\'' . __('Are you sure you want to reset Tweet This to ' .
		'its default options? Your OAuth keys and your Adjix.com, ' .
		'Bit.ly, SnipURL.com, and Su.pr logins will be preserved.',
		'tweet-this') . '\');" /> <input type="submit" name=' .
		'"flush" value="' . __('Flush URL Cache', 'tweet-this') .
		'" title="' . __('Only flushes the URL cache. Nondestructive.',
		'tweet-this') . '" onclick="return confirm (\'' .
		__('Are you sure you want to flush the URL cache? This is ' .
		'normally unnecessary because the cache is automatically ' .
		'flushed when you change URL services or related options. ' .
		'WARNING: If you have unsaved options, save them first ' .
		'because this will reload the page without saving your ' .
		'options.', 'tweet-this') . '\');" /> <input type="submit" ' .
		'name="uninstall" value="' . __('Completely Uninstall Tweet ' .
		'This', 'tweet-this') . '" title="' . __('Erases all ' .
		'options, postmeta, and scheduled tweets, and deactivates ' .
		'Tweet This.', 'tweet-this') . '" onclick="return confirm ' .
		'(\'' .	__('Are you sure you want to completely uninstall ' .
		'and deactivate Tweet This? WARNING: All plugin options, ' .
		'API keys, postmeta (custom fields), and scheduled tweets ' .
		'will be permanently erased.', 'tweet-this') . '\');" /></p>' .
		'<div style="width:800px;padding-top:2px;padding-bottom:0;">' .
		'<hr /></div><p onclick="expandcontent(\'s3\')" style=' .
		'"cursor:hand;cursor:pointer;"><u><strong>' .
		__('Import / Export Options', 'tweet-this') . '</strong>' .
		'</u></p><div id="s3" class="switchcontent"><p><em>' .
		__('EXPORT', 'tweet-this') . '</em>: ' . __('Save the ' .
		'content below to a text file. Includes your API keys, so ' .
		'don\'t share this with anyone.', 'tweet-this') . '</p>' .
		'<textarea name="export_content" rows="12" cols="100" ' .
		'onfocus="this.select()" readonly="readonly">' .
		$export_options . '</textarea><br /><p><em>' .
		__('IMPORT', 'tweet-this') . '</em>:</p>';

	if(version_compare($GLOBALS['wp_version'], '2.0', '>='))
		echo	'<textarea type="text" name="import_content" ' .
			'rows="12" cols="100" onfocus="this.select()">' .
			__('Paste your options here and click "Import ' .
			'Options." Whatever you paste here will be added ' .
			'as-is to the wp_options table for the ' .
			'tweet_this_options row. Importing options from a ' .
			'previous or future version of Tweet This will ' .
			'cause no harm.', 'tweet-this') . '</textarea>' .
			'<p class="submit" style="padding-top:6px;' .
			'padding-bottom:0;"><input type="submit" ' .
			'name="import" class="button-primary" value="' .
			__('Import Options', 'tweet-this') . '" title="' .
			__('Erases the old options and imports the options ' .
			'pasted above.', 'tweet-this') . '" onclick="return ' .
			'confirm (\'' . __('Are you sure you want to import ' .
			'these options? WARNING: Your current options will ' .
			'be permanently overwritten.', 'tweet-this') .
			'\');" /></p>';
	else echo	'<p style="width:750px">' . __('Importing options ' .
			'requires WordPress 2.0 or newer. <a href="http://' .
			'codex.wordpress.org/Upgrading_WordPress">Please ' .
			'upgrade</a>. Alternately, open up phpMyAdmin, ' .
			'select your WordPress database, click the ' .
			'"wp_options" table, click "Browse," find the ' .
			'"tweet_this_options" row, click the edit icon, ' .
			'paste your options dump into the "option_value" ' .
			'field, and finally, click "Go."', 'tweet-this') .
			'</p>';
	echo	'<div style="width:800px;padding-top:4px;padding-bottom:0;">' .
		'<hr /></div></div></form><p style="padding-top:5px;">' .
		__('If you like Tweet This, help write <a target="_blank" ' .
		'href="http://thripp.org/wiki/Tweet_This">the wiki</a>. ' .
		'Consider donating <a target="_blank" href="https://www.' .
		'paypal.com/cgi-bin/webscr?cmd=_xclick&amp;business=' .
		'richardxthripp@thripp.com&amp;currency_code=USD&amp;amount=' .
		'5.00&amp;return=&amp;item_name=Donations+for+Tweet+This+' .
		'plugin">$5.00</a>, <a target="_blank" href="https://www.' .
		'paypal.com/cgi-bin/webscr?cmd=_xclick&amp;business=' .
		'richardxthripp@thripp.com&amp;currency_code=USD&amp;amount=' .
		'10.00&amp;return=&amp;item_name=Donations+for+Tweet+This+' .
		'plugin">$10.00</a>, or <a target="_blank" href="https://' .
		'www.paypal.com/cgi-bin/webscr?cmd=_xclick&amp;business=' .
		'richardxthripp@thripp.com&amp;currency_code=USD&amp;amount=' .
		'&amp;return=&amp;item_name=Donations+for+Tweet+This+' .
		'plugin">another amount</a>. <a target="_blank" href=' .
		'"http://richardxthripp.thripp.com/tweet-this/#respond">' .
		'Report bugs here</a>', 'tweet-this') . ' (<a target=' .
		'"_blank" href="mailto:richardxthripp@thripp.com?subject=' .
		'Tweet+This+' . constant('TT_VERSION') . '+Bug+Report&amp;' .
		'body=I+am+running+WP+' . $GLOBALS['wp_version'] . '+on+PHP+' .
		constant('PHP_VERSION') . '+and+am+having+the+following+' .
		'problems:">' . __('email', 'tweet-this') . '</a>).</p></div>';

?>