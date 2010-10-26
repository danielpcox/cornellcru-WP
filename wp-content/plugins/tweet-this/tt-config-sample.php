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
 * This file is for special options not needed by 99% of Tweet This users.
 * Rename this file from tt-config-sample.php to tt-config.php to use it.
 * If you are on WPMU, this file should be in /wp-content/plugins/tweet-this/.
 * Options specified here apply for all blogs on your WPMU installation.
 */


/**
 * Sample usage of the following three definitions: if you have activated
 * multiple social networking services and want to put the links in an
 * unordered list, set TT_PREFIX to "<ul><li>", TT_SUFFIX to "</li></ul>",
 * and TT_SEPARATOR to "</li><li>". Note that if you use this configuration or
 * several others, the links will not display correctly unless Twitter is one
 * of the active social networking services.
 */


/**
 * The text that comes before the Tweet This link(s). Alignment is left, right,
 * or center, depending on what the user has specified in the options.
 */
define('TT_PREFIX', '<div class="tweetthis" style="text-align:' .
	tt_option('tt_alignment') . ';"><p>');


/**
 * The text that comes after the Tweet This link(s).
 */
define('TT_SUFFIX', '</p></div>');


/**
 * The text that separates the links of different social networking services.
 */
define('TT_SEPARATOR', ' ');


/**
 * True / false. Ignores the options in database and uses whatever is
 * specified as TT_SPECIAL_OPTIONS below.
 */
define('TT_OVERRIDE_OPTIONS', false);


/**
 * True / false. Hides the option menu completely; recommended if
 * TT_OVERRIDE_OPTIONS is true. Otherwise, you can still make changes in the
 * options menu but they will be ignored.
 */
define('TT_HIDE_MENU', false);


/**
 * The options to be overriden. TT_OVERRIDE_OPTIONS must be true for these
 * to be used. To use this feature, go to the options menu and change
 * everything as needed, click "Save Options," click "Import / Export Options,"
 * and copy the text from the export text area. Then paste it below,
 * overwriting everything between the quote marks between the blank lines.
 * Included below are the default options.
 */
define('TT_SPECIAL_OPTIONS',

'a:123:{s:5:"tt_30";s:5:"false";s:14:"tt_url_service";s:5:"local";s:20:"tt_admin_url_service";s:6:"bit.ly";s:12:"tt_alignment";s:4:"left";s:18:"tt_limit_to_single";s:5:"false";s:17:"tt_limit_to_posts";s:5:"false";s:10:"tt_url_www";s:5:"false";s:9:"tt_footer";s:5:"false";s:6:"tt_ads";s:5:"false";s:13:"tt_new_window";s:4:"true";s:11:"tt_nofollow";s:4:"true";s:16:"tt_img_css_class";s:7:"nothumb";s:17:"tt_link_css_class";s:2:"tt";s:6:"tt_css";s:26:"img.[IMG_CLASS]{border:0;}";s:19:"tt_shortlink_filter";s:4:"true";s:16:"tt_adjix_api_key";s:0:"";s:8:"tt_ad_vu";s:4:"true";s:7:"tt_j_mp";s:5:"false";s:17:"tt_bitly_username";s:0:"";s:16:"tt_bitly_api_key";s:0:"";s:19:"tt_snipurl_username";s:0:"";s:18:"tt_snipurl_api_key";s:0:"";s:16:"tt_supr_username";s:0:"";s:15:"tt_supr_api_key";s:0:"";s:17:"tt_snipurl_domain";s:11:"snipurl.com";s:21:"tt_custom_url_service";s:47:"http://tinyurl.com/api-create.php?url=[LONGURL]";s:21:"tt_auto_tweet_display";s:4:"true";s:27:"tt_auto_tweet_display_pages";s:4:"true";s:13:"tt_auto_tweet";s:5:"false";s:19:"tt_auto_tweet_pages";s:5:"false";s:18:"tt_auto_tweet_text";s:28:"New blog post: [TITLE] [URL]";s:19:"tt_app_consumer_key";s:0:"";s:22:"tt_app_consumer_secret";s:0:"";s:14:"tt_oauth_token";s:0:"";s:21:"tt_oauth_token_secret";s:0:"";s:15:"tt_textbox_size";s:2:"60";s:15:"tt_auto_display";s:4:"true";s:13:"tt_tweet_text";s:13:"[TITLE] [URL]";s:12:"tt_link_text";s:15:"Tweet This Post";s:13:"tt_title_text";s:15:"Post to Twitter";s:15:"tt_twitter_icon";s:25:"en/twitter/tt-twitter.png";s:16:"tt_twitter_share";s:5:"false";s:21:"tt_twitter_share_text";s:13:"[TITLE_SHARE]";s:20:"tt_twitter_share_via";s:15:"tweetthisplugin";s:20:"tt_twitter_share_rel";s:30:"richardxthripp,tweetthisplugin";s:8:"tt_plurk";s:5:"false";s:13:"tt_plurk_text";s:13:"[TITLE] [URL]";s:18:"tt_plurk_link_text";s:15:"Plurk This Post";s:19:"tt_plurk_title_text";s:13:"Post to Plurk";s:13:"tt_plurk_icon";s:21:"en/plurk/tt-plurk.png";s:7:"tt_bebo";s:5:"false";s:17:"tt_bebo_link_text";s:12:"Post to Bebo";s:18:"tt_bebo_title_text";s:12:"Post to Bebo";s:12:"tt_bebo_icon";s:19:"en/bebo/tt-bebo.png";s:7:"tt_buzz";s:5:"false";s:17:"tt_buzz_link_text";s:14:"Buzz This Post";s:18:"tt_buzz_title_text";s:18:"Post to Yahoo Buzz";s:12:"tt_buzz_icon";s:19:"en/buzz/tt-buzz.png";s:12:"tt_delicious";s:5:"false";s:22:"tt_delicious_link_text";s:17:"Post to Delicious";s:23:"tt_delicious_title_text";s:17:"Post to Delicious";s:17:"tt_delicious_icon";s:29:"en/delicious/tt-delicious.png";s:7:"tt_digg";s:5:"false";s:17:"tt_digg_link_text";s:14:"Digg This Post";s:18:"tt_digg_title_text";s:12:"Post to Digg";s:12:"tt_digg_icon";s:19:"en/digg/tt-digg.png";s:8:"tt_diigo";s:5:"false";s:18:"tt_diigo_link_text";s:13:"Post to Diigo";s:19:"tt_diigo_title_text";s:13:"Post to Diigo";s:13:"tt_diigo_icon";s:21:"en/diigo/tt-diigo.png";s:11:"tt_facebook";s:5:"false";s:21:"tt_facebook_link_text";s:16:"Post to Facebook";s:22:"tt_facebook_title_text";s:16:"Post to Facebook";s:16:"tt_facebook_icon";s:27:"en/facebook/tt-facebook.png";s:5:"tt_ff";s:5:"false";s:15:"tt_ff_link_text";s:18:"Post to FriendFeed";s:16:"tt_ff_title_text";s:18:"Post to FriendFeed";s:10:"tt_ff_icon";s:15:"en/ff/tt-ff.png";s:8:"tt_gbuzz";s:5:"false";s:18:"tt_gbuzz_link_text";s:19:"Post to Google Buzz";s:19:"tt_gbuzz_title_text";s:19:"Post to Google Buzz";s:13:"tt_gbuzz_icon";s:21:"en/gbuzz/tt-gbuzz.png";s:8:"tt_gmail";s:5:"false";s:18:"tt_gmail_link_text";s:10:"Send Gmail";s:19:"tt_gmail_title_text";s:10:"Send Gmail";s:13:"tt_gmail_icon";s:21:"en/gmail/tt-gmail.png";s:11:"tt_linkedin";s:5:"false";s:21:"tt_linkedin_link_text";s:16:"Post to LinkedIn";s:22:"tt_linkedin_title_text";s:16:"Post to LinkedIn";s:16:"tt_linkedin_icon";s:27:"en/linkedin/tt-linkedin.png";s:7:"tt_mixx";s:5:"false";s:17:"tt_mixx_link_text";s:14:"Mixx This Post";s:18:"tt_mixx_title_text";s:14:"Mixx This Post";s:12:"tt_mixx_icon";s:19:"en/mixx/tt-mixx.png";s:10:"tt_myspace";s:5:"false";s:20:"tt_myspace_link_text";s:15:"Post to MySpace";s:21:"tt_myspace_title_text";s:15:"Post to MySpace";s:15:"tt_myspace_icon";s:25:"en/myspace/tt-myspace.png";s:7:"tt_ping";s:5:"false";s:17:"tt_ping_link_text";s:14:"Ping This Post";s:18:"tt_ping_title_text";s:15:"Post to Ping.fm";s:12:"tt_ping_icon";s:19:"en/ping/tt-ping.png";s:9:"tt_reddit";s:5:"false";s:19:"tt_reddit_link_text";s:14:"Post to Reddit";s:20:"tt_reddit_title_text";s:14:"Post to Reddit";s:14:"tt_reddit_icon";s:23:"en/reddit/tt-reddit.png";s:11:"tt_slashdot";s:5:"false";s:21:"tt_slashdot_link_text";s:16:"Post to Slashdot";s:22:"tt_slashdot_title_text";s:16:"Post to Slashdot";s:16:"tt_slashdot_icon";s:27:"en/slashdot/tt-slashdot.png";s:10:"tt_squidoo";s:5:"false";s:20:"tt_squidoo_link_text";s:15:"Post to Squidoo";s:21:"tt_squidoo_title_text";s:15:"Post to Squidoo";s:15:"tt_squidoo_icon";s:25:"en/squidoo/tt-squidoo.png";s:5:"tt_su";s:5:"false";s:15:"tt_su_link_text";s:17:"Stumble This Post";s:16:"tt_su_title_text";s:19:"Post to StumbleUpon";s:10:"tt_su_icon";s:15:"en/su/tt-su.png";s:13:"tt_technorati";s:5:"false";s:23:"tt_technorati_link_text";s:18:"Post to Technorati";s:24:"tt_technorati_title_text";s:18:"Post to Technorati";s:18:"tt_technorati_icon";s:31:"en/technorati/tt-technorati.png";s:16:"tt_service_order";s:152:"twitter, plurk, bebo, buzz, delicious, digg, diigo, facebook, ff, gbuzz, gmail, linkedin, mixx, myspace, ping, reddit, slashdot, squidoo, su, technorati";}'

); // ***** END SPECIAL OPTIONS ***** //

?>