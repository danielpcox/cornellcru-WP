<?php
/*
Plugin Name: 1-click Retweet/Share/Like
Plugin URI: http://wwww.linksalpha.com/publish
Description: Adds Facebook Like, Facebook Share, Twitter, LinkedIn Share, Facebook Recommendations. Automatic publishing of content to 30+ Social Networks.
Author: LinksAlpha
Author URI: http://www.linksalpha.com/publish
Version: 4.0.0
*/

/*
    Copyright (C) 2010 LinksAlpha.

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a  copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

require("la-click-and-share-utility-fns.php");
require("la-click-and-share-networkpub.php");

define('LACANDS_PLUGIN_URL', 						lacands_get_plugin_dir());
define('LAECHONW_WIDGET_NAME', 						__('1-Click Retweet/Share/Like'));
define('LACANDS_FB_RECOMMENDATIONS_ID',  			'LACANDS_Facebook_Recommendations');
define('LACANDS_FB_RECOMMENDATIONS_NAME',  			__('Facebook Recommendations'));
define('LACANDSNW_WIDGET_NAME_INTERNAL', 			'lacandsnw_networkpub');
define('LACANDSNW_WIDGET_PREFIX',        			'lacandsnw_networkpub');
define('LACANDSNW_NETWORKPUB', 						__('Automatically publish your blog posts to 30+ Social Networks including Facebook, Twitter, LinkedIn, Yahoo, MySpace...'));
define('LACANDSNW_ERROR_INTERNAL',       			'internal error');
define('LACANDSNW_ERROR_INVALID_URL',    			'invalid url');
define('LACANDSNW_ERROR_INVALID_KEY',    			'invalid key');
define('LACANDSNW_WIDGET_NAME_POSTBOX', 			'Postbox');
define('LACANDSNW_WIDGET_NAME_POSTBOX_INTERNAL', 	'networkpubpostbox');
define('LACANDS_SETTINGS_SAVED', 					__('Settings saved for 1-click Retweet/Share/Like'));
define('LACANDS_DISPLAYING', 						__('Displaying'));
define('LACANDS_DONE', 								__('Done'));
define('LACANDS_PENDING', 							__('Pending'));
define('LACANDS_DISABLE_THIS_MESSAGE', 				__('To disable this message, go to Settings'));
define('LACANDS_DISABLE_THIS_MESSAGE2', 			__("'Auto Publish on Social Networks' and check the 'Warning box' and save changes"));
define('LACANDS_PLUGIN_IS_ALMOST_READY', 			__("plugin is almost ready"));
define('LACANDS_TO', 								__("To"));
define('LACANDS_YOU_MUST', 							__("you must"));
define('LACANDS_ENTER_API_KEY', 					__("enter API key"));
define('LACANDS_ENTER_API_KEY_UPPER', 				__("Enter API key"));
define('LACANDS_UNDER_SETTINGS', 					__("under Settings"));
define('LACANDS_AUTO_PUBLISH_ON_NETWORKS', 			__("Auto Publish on Social Networks"));
define('LACANDS_PLUGIN_ADMIN_URL', 					__("options-general.php?page=1-click-retweetsharelike/la-click-and-share.php"));
define('LACANDS_DEFAULT', 							__("Default"));
define('LACANDS_SHOW', 								__("Show"));
define('LACANDS_DONT_SHOW', 						__("Don't Show"));

$lacandsnw_networkpub_settings['api_key'] 	= array('label'=>__('API Key:'), 'type'=>'text', 'default'=>'');
$lacandsnw_networkpub_settings['id']      	= array('label'=>__('id'), 'type'=>'text', 'default'=>'');
$lacandsnw_options                        	= get_option(LACANDSNW_WIDGET_NAME_INTERNAL);
$lacands_version_number 					= '4.0.0';


function lacands_init() {
	global $lacands_version_number;
	$lacands_version_number_db = get_option('lacands-html-version-number');
	if($lacands_version_number != $lacands_version_number_db) {
		update_option('lacands-html-version-number', $lacands_version_number);
		lacands_writeOptionsValuesToWPDatabase('default');
	}
}


function lacands_readOptionsValuesFromWPDatabase() {
	global $lacands_opt_widget_counters_location, $lacands_widget_disable_cntr_display;
	global $lacands_opt_widget_margin_top, $lacands_opt_widget_margin_right, $lacands_opt_widget_margin_bottom, $lacands_opt_widget_margin_left;
	global $lacands_opt_cntr_font_color, $lacands_opt_widget_fb_like, $lacands_opt_widget_font_style;
	global $lacands_display_pages, $lacands_like_layout, $lacandsnw_opt_warning_msg;
	global $lacands_opt_widget_fb_ref, $lacands_opt_widget_fb_like_lang, $lacands_opt_widget_twitter_lang, $lacands_opt_widget_twitter_mention, $lacands_opt_widget_twitter_related1, $lacands_opt_widget_twitter_related2, $lacands_opt_widget_twitter_counter, $lacands_opt_widget_linkedin_button;
	global $lacands_opt_widget_fb_share_counter, $lacands_opt_widget_fb_like_show, $lacands_opt_widget_linkedin_counter, $lacands_opt_widget_fb_share_lang;
	global $lacands_opt_widget_buzz_counter, $lacands_opt_widget_digg_counter, $lacands_opt_widget_stumble_counter;
	global $lacands_opt_widget_fb_reco_width, $lacands_opt_widget_fb_reco_height, $lacands_opt_widget_fb_reco_header, $lacands_opt_widget_fb_reco_color, $lacands_opt_widget_fb_reco_font, $lacands_opt_widget_fb_reco_border;
	global $lacands_opt_widget_fb_reco_margin_top, $lacands_opt_widget_fb_reco_margin_right, $lacands_opt_widget_fb_reco_margin_bottom, $lacands_opt_widget_fb_reco_margin_left;
	global $lacands_opt_widget_fb_reco_title;

	$lacands_opt_widget_counters_location     	= get_option('lacands-html-widget-counters-location');
	$lacands_opt_widget_margin_top            	= get_option('lacands-html-widget-margin-top');
	$lacands_opt_widget_margin_right          	= get_option('lacands-html-widget-margin-right');
	$lacands_opt_widget_margin_bottom         	= get_option('lacands-html-widget-margin-bottom');
	$lacands_opt_widget_margin_left           	= get_option('lacands-html-widget-margin-left');
	$lacands_widget_disable_cntr_display      	= get_option('lacands-html-widget-disable-cntr-display');
	$lacands_opt_cntr_font_color              	= get_option('lacands-html-cntr-font-color');
	$lacands_opt_widget_fb_like               	= get_option('lacands-html-widget-fb-like');
	$lacands_opt_widget_font_style            	= get_option('lacands-html-widget-font-style');
	$lacands_opt_widget_fb_ref            	  	= get_option('lacands-html-widget-fb-ref');
	$lacands_opt_widget_fb_like_lang          	= get_option('lacands-html-widget-fb-like-lang');
	$lacands_opt_widget_fb_share_counter      	= get_option('lacands-html-widget-fb-share-counter');
	$lacands_opt_widget_fb_share_lang      		= get_option('lacands-html-widget-fb-share-lang');
	$lacands_opt_widget_fb_like_show      		= get_option('lacands-html-widget-fb-like-show');
	$lacands_opt_widget_twitter_lang          	= get_option('lacands-html-widget-twitter-lang');
	$lacands_opt_widget_twitter_mention       	= get_option('lacands-html-widget-twitter-mention');
	$lacands_opt_widget_twitter_related1      	= get_option('lacands-html-widget-twitter-related1');
	$lacands_opt_widget_twitter_related2      	= get_option('lacands-html-widget-twitter-related2');
	$lacands_opt_widget_twitter_counter      	= get_option('lacands-html-widget-twitter-counter');
	$lacands_opt_widget_linkedin_button	  		= get_option('lacands-html-widget-linkedin-button');
	$lacands_opt_widget_linkedin_counter      	= get_option('lacands-html-widget-linkedin-counter');
	$lacands_opt_widget_fb_reco_width	  		= get_option('lacands-html-widget-fb-reco-width');
	$lacands_opt_widget_fb_reco_height	  		= get_option('lacands-html-widget-fb-reco-height');
	$lacands_opt_widget_fb_reco_header	  		= get_option('lacands-html-widget-fb-reco-header');
	$lacands_opt_widget_fb_reco_color	  		= get_option('lacands-html-widget-fb-reco-color');
	$lacands_opt_widget_fb_reco_font	  		= get_option('lacands-html-widget-fb-reco-font');
	$lacands_opt_widget_fb_reco_border	  		= get_option('lacands-html-widget-fb-reco-border');
	$lacands_opt_widget_fb_reco_margin_top      = get_option('lacands-html-widget-fb-reco-margin-top');
	$lacands_opt_widget_fb_reco_margin_right    = get_option('lacands-html-widget-fb-reco-margin-right');
	$lacands_opt_widget_fb_reco_margin_bottom   = get_option('lacands-html-widget-fb-reco-margin-bottom');
	$lacands_opt_widget_fb_reco_margin_left     = get_option('lacands-html-widget-fb-reco-margin-left');
	$lacands_opt_widget_fb_reco_title			= get_option('lacands-html-widget-fb-reco-title');
	$lacands_opt_widget_stumble_counter      	= get_option('lacands-html-widget-stumble-counter');
	$lacands_opt_widget_buzz_counter      		= get_option('lacands-html-widget-buzz-counter');
	$lacands_opt_widget_digg_counter      		= get_option('lacands-html-widget-digg-counter');
	$lacands_display_pages            	  		= get_option('lacands-html-display-pages');
	$lacands_like_layout            	  		= get_option('lacands-html-like-layout');
	$lacandsnw_opt_warning_msg                	= get_option('lacandsnw-html-warning-msg');
}


function lacands_writeOptionsValuesToWPDatabase($option) {
	global $lacands_display_pages;
	global $lacands_version_number;

	if($option == 'default') {
		$lacands_eget = get_bloginfo('admin_email'); $lacands_uget = get_bloginfo('url'); $lacands_nget = get_bloginfo('name');
		$lacands_dget = get_bloginfo('description'); $lacands_cget = get_bloginfo('charset'); $lacands_vget = get_bloginfo('version');
		$lacands_lget = get_bloginfo('language'); $link='http://www.linksalpha.com/a/bloginfo';
		$lacands_bloginfo = array('email'=>$lacands_eget, 'url'=>$lacands_uget, 'name'=>$lacands_nget, 'desc'=>$lacands_dget, 'charset'=>$lacands_cget, 'version'=>$lacands_vget, 'lang'=>$lacands_lget, 'plugin'=>'cs');
		lacands_http_post($link, $lacands_bloginfo);
		$lacands_display_pages = array('single' => '1','home' => '1','archive' => '1', 'category'=>'1', 'tags'=>'1', 'date'=>'1', 'author'=>'1', 'page'=>'1');
		add_option('lacands-html-widget-counters-location', 'beforeAndafter');
		add_option('lacands-html-widget-margin-top',    				'5');
		add_option('lacands-html-widget-margin-right',  				'0');
		add_option('lacands-html-widget-margin-bottom', 				'5');
		add_option('lacands-html-widget-margin-left',   				'0');
		add_option('lacands-html-widget-disable-cntr-display-after', 	'0');
		add_option('lacands-html-cntr-font-color', 						'333333');
		add_option('lacands-html-widget-fb-like', 						'like');
		add_option('lacands-html-widget-font-style', 					'arial');
		add_option('lacands-html-widget-fb-ref', 						'facebook');
		add_option('lacands-html-widget-fb-like-lang', 					'en_US');
		add_option('lacands-html-widget-fb-share-counter', 				'1');
		add_option('lacands-html-widget-fb-share-lang', 				'en');
		add_option('lacands-html-widget-fb-like-show', 					'1');
		add_option('lacands-html-widget-twitter-lang', 					'en');
		add_option('lacands-html-widget-twitter-mention', 				'en');
		add_option('lacands-html-widget-twitter-related1', 				'en');
		add_option('lacands-html-widget-twitter-related2', 				'en');
		add_option('lacands-html-widget-twitter-counter', 				'1');
		add_option('lacands-html-widget-linkedin-button', 				'noshow');
		add_option('lacands-html-widget-linkedin-counter', 				'1');
		add_option('lacands-html-widget-fb-reco-width', 				'300');
		add_option('lacands-html-widget-fb-reco-height', 				'300');
		add_option('lacands-html-widget-fb-reco-header', 				'true');
		add_option('lacands-html-widget-fb-reco-color', 				'light');
		add_option('lacands-html-widget-fb-reco-font', 					'arial');
		add_option('lacands-html-widget-fb-reco-border', 				'#AAAAAA');
		add_option('lacands-html-widget-fb-reco-margin-top',    		'5');
		add_option('lacands-html-widget-fb-reco-margin-right',  		'0');
		add_option('lacands-html-widget-fb-reco-margin-bottom', 		'5');
		add_option('lacands-html-widget-fb-reco-margin-left',   		'0');
		add_option('lacands-html-widget-fb-reco-title',   				'');
		add_option('lacands-html-widget-stumble-counter', 				'1');
		add_option('lacands-html-widget-buzz-counter', 					'1');
		add_option('lacands-html-widget-digg-counter', 					'1');
		add_option('lacands-html-display-pages', 						$lacands_display_pages);
		add_option('lacands-html-like-layout', 							'button_count');
		update_option('lacands-html-version-number', 					$lacands_version_number);
		add_option('lacandsnw-html-warning-msg', 						'0');
	
	} else if ($option == 'update') {
		
		if(!empty($_POST['lacands-html-widget-counters-location'])) {
			update_option('lacands-html-widget-counters-location', 		$_POST['lacands-html-widget-counters-location']);
		}

		if($_POST['lacands-html-widget-margin-top'] != NULL) {
			update_option('lacands-html-widget-margin-top',    			(string)$_POST['lacands-html-widget-margin-top']);
		} else {
			update_option('lacands-html-widget-margin-top',    			'0');
		}

		if($_POST['lacands-html-widget-margin-right'] != NULL) {
			update_option('lacands-html-widget-margin-right',  			(string)$_POST['lacands-html-widget-margin-right']);
		} else {
			update_option('lacands-html-widget-margin-right',    		'0');
		}

		if($_POST['lacands-html-widget-margin-bottom'] != NULL) {
			update_option('lacands-html-widget-margin-bottom', 			(string)$_POST['lacands-html-widget-margin-bottom']);
		} else {
			update_option('lacands-html-widget-margin-bottom',    		'0');
		}

		if($_POST['lacands-html-widget-margin-left'] != NULL) {
			update_option('lacands-html-widget-margin-left',   			(string)$_POST['lacands-html-widget-margin-left']);
		} else {
			update_option('lacands-html-widget-margin-left',    		'0');
		}

		if(!empty($_POST['lacands-html-widget-disable-cntr-display'])) {
			update_option('lacands-html-widget-disable-cntr-display',   (string)$_POST['lacands-html-widget-disable-cntr-display']);
		} else {
			update_option('lacands-html-widget-disable-cntr-display', 	'0');
		}

		if(!empty($_POST['lacands-html-cntr-font-color'])) {
			update_option('lacands-html-cntr-font-color',				(string)$_POST['lacands-html-cntr-font-color']);
		} else {
			update_option('lacands-html-cntr-font-color', 				'333333');
		}

		if(!empty($_POST['lacands-html-widget-fb-like'])) {
			update_option('lacands-html-widget-fb-like',				(string)$_POST['lacands-html-widget-fb-like']);
		} else {
			update_option('lacands-html-widget-fb-like', 				'Like');
		}

		if(!empty($_POST['lacands-html-widget-font-style'])) {
			update_option('lacands-html-widget-font-style',				(string)$_POST['lacands-html-widget-font-style']);
		} else {
			update_option('lacands-html-widget-font-style', 			'Like');
		}
		
		if(!empty($_POST['lacands-html-widget-fb-ref'])) {
			update_option('lacands-html-widget-fb-ref',					(string)$_POST['lacands-html-widget-fb-ref']);
		} else {
			update_option('lacands-html-widget-fb-ref', 				'facebook');
		}
		
		if(!empty($_POST['lacands-html-widget-fb-like-lang'])) {
			update_option('lacands-html-widget-fb-like-lang',			(string)$_POST['lacands-html-widget-fb-like-lang']);
		} else {
			update_option('lacands-html-widget-fb-like-lang', 			'Like');
		}
		
		if(!empty($_POST['lacands-html-widget-fb-share-counter'])) {
			update_option('lacands-html-widget-fb-share-counter',		(string)$_POST['lacands-html-widget-fb-share-counter']);
		} else {
			update_option('lacands-html-widget-fb-share-counter', 		'0');
		}
		
		if(!empty($_POST['lacands-html-widget-fb-share-lang'])) {
			update_option('lacands-html-widget-fb-share-lang',			(string)$_POST['lacands-html-widget-fb-share-lang']);
		} else {
			update_option('lacands-html-widget-fb-share-lang', 			'Share');
		}
		
		if(!empty($_POST['lacands-html-widget-fb-like-show'])) {
			update_option('lacands-html-widget-fb-like-show',			(string)$_POST['lacands-html-widget-fb-like-show']);
		} else {
			update_option('lacands-html-widget-fb-like-show', 			'0');
		}
		
		if(!empty($_POST['lacands-html-widget-twitter-lang'])) {
			update_option('lacands-html-widget-twitter-lang',			(string)$_POST['lacands-html-widget-twitter-lang']);
		} else {
			update_option('lacands-html-widget-twitter-lang', 			'Like');
		}

		if(!empty($_POST['lacands-html-widget-twitter-mention'])) {
			update_option('lacands-html-widget-twitter-mention',		(string)$_POST['lacands-html-widget-twitter-mention']);
		} else {
			update_option('lacands-html-widget-twitter-mention', 		'');
		}
		
		if(!empty($_POST['lacands-html-widget-twitter-related1'])) {
			update_option('lacands-html-widget-twitter-related1',		(string)$_POST['lacands-html-widget-twitter-related1']);
		} else {
			update_option('lacands-html-widget-twitter-related1', 		'');
		}
		
		if(!empty($_POST['lacands-html-widget-twitter-related2'])) {
			update_option('lacands-html-widget-twitter-related2',		(string)$_POST['lacands-html-widget-twitter-related2']);
		} else {
			update_option('lacands-html-widget-twitter-related2', 		'');
		}
		
		if(!empty($_POST['lacands-html-widget-twitter-counter'])) {
			update_option('lacands-html-widget-twitter-counter',		(string)$_POST['lacands-html-widget-twitter-counter']);
		} else {
			update_option('lacands-html-widget-twitter-counter', 		'0');
		}
		
		if(!empty($_POST['lacands-html-widget-linkedin-button'])) {
			update_option('lacands-html-widget-linkedin-button',		(string)$_POST['lacands-html-widget-linkedin-button']);
		} else {
			update_option('lacands-html-widget-linkedin-button', 		'');
		}
	    
		if(!empty($_POST['lacands-html-widget-linkedin-counter'])) {
			update_option('lacands-html-widget-linkedin-counter',		(string)$_POST['lacands-html-widget-linkedin-counter']);
		} else {
			update_option('lacands-html-widget-linkedin-counter', 		'0');
		}
		
		if(!empty($_POST['lacands-html-widget-stumble-counter'])) {
			update_option('lacands-html-widget-stumble-counter',		(string)$_POST['lacands-html-widget-stumble-counter']);
		} else {
			update_option('lacands-html-widget-stumble-counter', 		'0');
		}
		
		if(!empty($_POST['lacands-html-widget-buzz-counter'])) {
			update_option('lacands-html-widget-buzz-counter',			(string)$_POST['lacands-html-widget-buzz-counter']);
		} else {
			update_option('lacands-html-widget-buzz-counter', 			'0');
		}
		
		if(!empty($_POST['lacands-html-widget-digg-counter'])) {
			update_option('lacands-html-widget-digg-counter',			(string)$_POST['lacands-html-widget-digg-counter']);
		} else {
			update_option('lacands-html-widget-digg-counter', 			'0');
		}
		
		if(!empty($_POST['lacands-html-display-page-home'])) {
			$lacands_display_pages['home'] = '1';
		} else {
			$lacands_display_pages['home'] = '0';
		}

		if(!empty($_POST['lacands-html-display-page-archive'])) {
			$lacands_display_pages['archive'] = '1';
		} else {
			$lacands_display_pages['archive'] = '0';
		}
		    
		if(!empty($_POST['lacands-html-display-page-page'])) {
			$lacands_display_pages['page'] = '1';
		} else {
			$lacands_display_pages['page'] = '0';
		}
		    
		if(!empty($_POST['lacands-html-display-page-date'])) {
			$lacands_display_pages['date'] = '1';
		} else {
			$lacands_display_pages['date'] = '0';
		}
		    
		if(!empty($_POST['lacands-html-display-page-category'])) {
			$lacands_display_pages['category'] = '1';
		} else {
			$lacands_display_pages['category'] = '0';
		}
		    
		if(!empty($_POST['lacands-html-display-page-tag'])) {
			$lacands_display_pages['tag'] = '1';
		} else {
			$lacands_display_pages['tag'] = '0';
		}
		    
		if(!empty($_POST['lacands-html-display-page-author'])) {
			$lacands_display_pages['author'] = '1';
		} else {
			$lacands_display_pages['author'] = '0';
		}
		
		if(!empty($_POST['lacands-html-display-page-page'])) {
			$lacands_display_pages['page'] = '1';
		} else {
			$lacands_display_pages['page'] = '0';
		}
		
		update_option('lacands-html-display-pages', $lacands_display_pages);

		if(!empty($_POST['lacands-html-like-layout'])) {
		    update_option('lacands-html-like-layout', 					(string)$_POST['lacands-html-like-layout']);
		}
	    
		if (isset($_POST['warning_msg'])) {
			if(!empty($_POST['lacandsnw-html-warning-msg'])) {
				update_option('lacandsnw-html-warning-msg',  			(string)$_POST['lacandsnw-html-warning-msg']);
			} else {
				update_option('lacandsnw-html-warning-msg', 			'0');
			}
		}	    
	} 
}


function lacands_wp_filter_post_content ( $related_content ) {
	global $lacands_opt_widget_counters_location;
	global $lacands_widget_disable_cntr_display;
	
	$lacands_widget_disable_cntr_display  = get_option('lacands-html-widget-disable-cntr-display');
	$lacands_opt_widget_counters_location = get_option('lacands-html-widget-counters-location');
	$lacands_display_pages = get_option('lacands-html-display-pages');
	
	if($lacands_widget_disable_cntr_display == '0') {
		if($lacands_opt_widget_counters_location == "beforeAndafter") {
			$related_content_beforeAndafter = lacands_wp_filter_content_widget(FALSE);
			if ((is_tag()  && ($lacands_display_pages['tag'])) || (is_category()  && ($lacands_display_pages['category'])) || (is_author() && ($lacands_display_pages['author'])) || (is_date()  && ($lacands_display_pages['date'])) || (is_page()  && ($lacands_display_pages['page']))) {
				echo $related_content_beforeAndafter;
			} else {
				$related_content = $related_content_beforeAndafter.$related_content.$related_content_beforeAndafter;
			}
		} else if($lacands_opt_widget_counters_location == "before") {
			if ((is_tag()  && ($lacands_display_pages['tag'])) || (is_category()  && ($lacands_display_pages['category'])) || (is_author() && ($lacands_display_pages['author'])) || (is_date()  && ($lacands_display_pages['date']))) {
				echo lacands_wp_filter_content_widget(FALSE);
			} else {
				$related_content = lacands_wp_filter_content_widget(FALSE).$related_content;
			}
		} else if($lacands_opt_widget_counters_location == "after") {
			if ((is_tag()  && ($lacands_display_pages['tag'])) || (is_category()  && ($lacands_display_pages['category'])) || (is_author() && ($lacands_display_pages['author'])) || (is_date()  && ($lacands_display_pages['date']))) {
				echo lacands_wp_filter_content_widget(FALSE);
			} else {
				$related_content = $related_content.lacands_wp_filter_content_widget(FALSE);
			}
		}
	}
	return ($related_content);
}


function lacands_wp_filter_content_widget ($show=TRUE) {
	global $lacands_opt_widget_counters_location, $lacands_widget_disable_cntr_display;
	global $lacands_opt_widget_margin_top, $lacands_opt_widget_margin_right, $lacands_opt_widget_margin_bottom, $lacands_opt_widget_margin_left;
	global $lacands_opt_cntr_font_color, $lacands_opt_widget_fb_like, $lacands_opt_widget_font_style;
	global $lacands_display_pages, $lacands_like_layout;
	global $lacands_opt_widget_fb_ref, $lacands_opt_widget_fb_like_lang, $lacands_opt_widget_twitter_lang, $lacands_opt_widget_twitter_mention, $lacands_opt_widget_twitter_related1, $lacands_opt_widget_twitter_related2, $lacands_opt_widget_twitter_counter, $lacands_opt_widget_linkedin_button;
	global $lacands_opt_widget_fb_share_counter, $lacands_opt_widget_fb_like_show, $lacands_opt_widget_linkedin_counter, $lacands_opt_widget_fb_share_lang;
	global $lacands_opt_widget_buzz_counter, $lacands_opt_widget_digg_counter, $lacands_opt_widget_stumble_counter;
	global $post;
	global $lacands_opt_widget_linkedin_button;

	$p = $post;
	lacands_readOptionsValuesFromWPDatabase();
	$position = '';
	if( $lacands_widget_disable_cntr_display == '0') {
		$position = 'padding-top:'.$lacands_opt_widget_margin_top.'px;padding-right:'.$lacands_opt_widget_margin_right.'px;padding-bottom:'.$lacands_opt_widget_margin_bottom.'px;padding-left:'.$lacands_opt_widget_margin_left.'px;';
	}
	
	if ($show || is_single() ||  (is_home() && ($lacands_display_pages['home'])) || (is_archive() && ($lacands_display_pages['archive'])) || (is_category()  && ($lacands_display_pages['category'])) || (is_tag()  && ($lacands_display_pages['tag'])) || (is_author()  && ($lacands_display_pages['author'])) || (is_date()  && ($lacands_display_pages['date'])) || (is_feed()  && ($lacands_display_pages['feed'])) || (is_page()  && ($lacands_display_pages['page']))) {
		$link1 = urlencode(urldecode(get_permalink($p)));
		$lacands_opt_cntr_font_color = 	str_replace('#', '', $lacands_opt_cntr_font_color);
		$lacands_opt_cntr_font_color = 	trim($lacands_opt_cntr_font_color);
		$args = array();
		$args['blog'] =				get_bloginfo('name');
		$args['link'] = 			htmlentities($link1);
		$args['title'] = 			substr(strip_tags($post->post_title), 0, 120);
		$args['desc'] = 			substr(strip_tags($post->post_content), 0, 200);
		$args['fc'] = 				$lacands_opt_cntr_font_color;
		$args['fs'] = 				$lacands_opt_widget_font_style;
		$args['fblname'] = 			$lacands_opt_widget_fb_like;
		$args['fblref'] = 			$lacands_opt_widget_fb_ref;
		$args['fbllang'] = 			$lacands_opt_widget_fb_like_lang;
		$args['fblshow'] = 			$lacands_opt_widget_fb_like_show;
		$args['fbsctr'] = 			$lacands_opt_widget_fb_share_counter;
		$args['fbslang'] =			$lacands_opt_widget_fb_share_lang;
		$args['twlang'] = 			$lacands_opt_widget_twitter_lang;
		$args['twmention'] = 		$lacands_opt_widget_twitter_mention;
		$args['twrelated1'] = 		$lacands_opt_widget_twitter_related1;
		$args['twrelated2'] = 		$lacands_opt_widget_twitter_related2;
		$args['twctr'] = 			$lacands_opt_widget_twitter_counter;
		$args['lnkdshow'] = 		$lacands_opt_widget_linkedin_button;
		$args['lnkdctr'] = 			$lacands_opt_widget_linkedin_counter;
		$args['buzzctr'] = 			$lacands_opt_widget_buzz_counter;
		$args['diggctr'] = 			$lacands_opt_widget_digg_counter;
		$args['stblctr'] = 			$lacands_opt_widget_stumble_counter;
		$args_data = http_build_query($args);
		$lacands_widget_display_cntrs = '<div style="'.$position.';">
							<iframe
								style="height:25px !important; border:none !important; overflow:hidden !important; width:450px !important;" frameborder="0" scrolling="no" allowTransparency="true"
								src="http://www.linksalpha.com/social?'.$args_data.'">
							</iframe>
						</div>';
		if($show) {
			echo $lacands_widget_display_cntrs;
			return;
		}
		return $lacands_widget_display_cntrs;
	}
	return;
}


function lacands_wp_admin_options_settings () {
	global $lacands_opt_widget_counters_location, $lacands_widget_disable_cntr_display;
	global $lacands_opt_widget_margin_top, $lacands_opt_widget_margin_right, $lacands_opt_widget_margin_bottom, $lacands_opt_widget_margin_left;
	global $lacands_opt_cntr_font_color, $lacands_opt_widget_fb_like, $lacands_opt_widget_font_style;
	global $lacands_display_pages, $lacands_like_layout;
	global $lacandsnw_networkpub_settings;
	global $lacandsnw_opt_warning_msg;
	global $lacands_opt_widget_fb_ref, $lacands_opt_widget_fb_like_lang, $lacands_opt_widget_twitter_lang, $lacands_opt_widget_twitter_mention, $lacands_opt_widget_twitter_related1, $lacands_opt_widget_twitter_related2, $lacands_opt_widget_twitter_counter, $lacands_opt_widget_linkedin_button;
	global $lacands_opt_widget_fb_share_counter, $lacands_opt_widget_fb_like_show, $lacands_opt_widget_linkedin_counter, $lacands_opt_widget_fb_share_lang;
	global $lacands_opt_widget_buzz_counter, $lacands_opt_widget_digg_counter, $lacands_opt_widget_stumble_counter;
	global $lacands_opt_widget_linkedin_button;

	if (isset($_POST['lacands_widget_update'])) {
		if ( function_exists('current_user_can') && !current_user_can('manage_options') ) {
			die(__('Cheatin&#8217; uh?'));
		}
		lacands_writeOptionsValuesToWPDatabase('update');
		echo '<div id="message" class="updated fade" style="width:1000px;"><p><strong>'.LACANDS_SETTINGS_SAVED.'</strong></p></div>';
		echo '</strong></p></div>';
	}
	
	if (isset($_POST['AddAPIKey'])) {
		if ( function_exists('current_user_can') && !current_user_can('manage_options') ) {
			die(__('Cheatin&#8217; uh?'));
		}
		$field_name = sprintf('%s_%s', LACANDSNW_WIDGET_PREFIX, 'api_key');
		$value = strip_tags(stripslashes($_POST[$field_name]));
		if($value) {
			$networkadd = lacandsnw_networkpub_add($value);
		}
	}
	
	if (isset($_POST['warning_msg'])) {
		if ( function_exists('current_user_can') && !current_user_can('manage_options') ) {
			die(__('Cheatin&#8217; uh?'));
		}
		if(!empty($_POST['lacandsnw-html-warning-msg'])) {
			update_option('lacandsnw-html-warning-msg',  (string)$_POST['lacandsnw-html-warning-msg']);
		} else {
			update_option('lacandsnw-html-warning-msg', '0');
		}
		echo '<div id="message" class="updated fade" style="width:1000px;"><p><strong>'.LACANDS_SETTINGS_SAVED.'</strong></p></div>';
		echo '</strong></p></div>';
	}
	
	$options    = get_option(LACANDSNW_WIDGET_NAME_INTERNAL);
	$curr_field = 'api_key';
	$field_name = sprintf('%s_%s', LACANDSNW_WIDGET_PREFIX, $curr_field);
	lacands_readOptionsValuesFromWPDatabase();
	$lacands_combo_iconWidget = '<img border="0" style="vertical-align:middle; border:1px solid #C0C0C0  " src="'.LACANDS_PLUGIN_URL.'widget.png">';
	require("la-click-and-share-comboAdmin.html");
	
}


function lacands_wp_admin() {
	if (function_exists('add_options_page')) {
	    add_options_page('1-click Retweet/Share/Like', '1-click Retweet/Share/Like', 'manage_options', __FILE__, 'lacands_wp_admin_options_settings');
	}
}


function lacands_pages() {
	if ( function_exists('add_submenu_page') ) {
		if(!lacandsnw_networkpubcheck()) {
			$page = add_submenu_page('edit.php', 	LACANDSNW_WIDGET_NAME_POSTBOX, LACANDSNW_WIDGET_NAME_POSTBOX, 'manage_options', LACANDSNW_WIDGET_NAME_POSTBOX_INTERNAL, 'lacandsnw_postbox');
		}
	}	
}


function lacands_activate() {
	lacands_writeOptionsValuesToWPDatabase('default');
}


function lacands_deactivate() {
	lacands_writeOptionsValuesToWPDatabase('delete');
}


function lacands_warning() {
	$options 		= get_option(LAECHONW_WIDGET_NAME_INTERNAL);
	$show_warning_msg 	= get_option('lacandsnw-html-warning-msg');

	if( ($show_warning_msg == 1) || (!empty($options['api_key']) ) ) {
		return;
	} else {
		echo $options['api_key'];
		echo "	<div class='updated fade' style='width:94%;margin-left:5px;padding:5px;'>
			<div style='padding:5px;'>
				<strong><a href=\"http://wordpress.org/extend/plugins/1-click-retweetsharelike/\" target=\"_blank\">".LAECHONW_WIDGET_NAME."</a>&nbsp;".LACANDS_PLUGIN_IS_ALMOST_READY."</strong>
			</div>
			<ol>";

		if(empty($options['api_key'])) {
			if (!isset($_POST['AddAPIKey'])) {
			    echo "<li>".sprintf('<div style="font-size:11px"><span style=color:#d12424;"><b>'.LACANDS_PENDING.':</b></span>&nbsp;'.LACANDS_TO.'&nbsp;'.LACANDSNW_NETWORKPUB.'&nbsp;'.LACANDS_YOU_MUST.'<a href="%1$s">&nbsp;'.LACANDS_ENTER_API_KEY.'&nbsp;</a>&nbsp;('.LACANDS_UNDER_SETTINGS.'->'.LAECHONW_WIDGET_NAME.'->\''.LACANDS_AUTO_PUBLISH_ON_NETWORKS.'\')</div>',
				  LACANDS_PLUGIN_ADMIN_URL)."</li>";
			}
		}

		if(!empty($options['api_key'])) {
			    echo "<li>".sprintf('<div style="font-size:11px"><span style=color:#006633;"><b>'.LACANDS_DONE.':</b></span>
						 <span style="color:#808080;">'.LACANDSNW_NETWORKPUB.'</span></div>',
						 LACANDS_PLUGIN_ADMIN_URL)."</li>";
		}

		echo "<li><div style='color: #006633;font-size:11px'><b>".LACANDS_DONE.":</b><span style='color:#808080;'>&nbsp;".LACANDS_DISPLAYING."&nbsp;".LAECHONW_WIDGET_NAME."</span></div></li></ol>";
		echo "<div style='color:#808080; font-size:11px;padding-left:5px;'>".LACANDS_DISABLE_THIS_MESSAGE."->1-click Retweet/Share/Like->".LACANDS_DISABLE_THIS_MESSAGE2."</div></div>";
	}
}


function lacands_la_langs() {
	$langs = array();
	$response_full = lacands_http_post("http://www.facebook.com/translations/FacebookLocales.xml", array());
	$response_code = $response_full[0];
	if ($response_code == 200) {
		preg_match_all('/<locale>\s*<englishName>([^<]+)<\/englishName>\s*<codes>\s*<code>\s*<standard>.+?<representation>([^<]+)<\/representation>/s', utf8_decode($response_full[1]), $langslist, PREG_PATTERN_ORDER);
		foreach ($langslist[1] as $key=>$val) {
			$langs[$langslist[2][$key]] = $val;
		}
	} else {
		$langs['default'] = "Default";
	}
	return $langs;
}


function lacands_fbs_langs() {
	$langs = array();
	$response_full = lacands_http_post("http://www.linksalpha.com/a/translate", array('type'=>'share'));
	$response_code = $response_full[0];
	if ($response_code == 200) {
		$response = lacandsnw_networkpub_json_decode($response_full[1]);
		foreach($response->results as $key=>$val) {
			$langs[$key] = $val;
		}
	} else {
		$langs['en'] = "English";
	}
	return $langs;
}


function lacands_fb_recommendations() {
	global $lacands_opt_widget_fb_reco_width, $lacands_opt_widget_fb_reco_height, $lacands_opt_widget_fb_reco_header, $lacands_opt_widget_fb_reco_color, $lacands_opt_widget_fb_reco_font, $lacands_opt_widget_fb_reco_border;
	global $lacands_opt_widget_fb_reco_margin_top, $lacands_opt_widget_fb_reco_margin_right, $lacands_opt_widget_fb_reco_margin_bottom, $lacands_opt_widget_fb_reco_margin_left;
	global $lacands_opt_widget_fb_reco_title;
	lacands_readOptionsValuesFromWPDatabase();
	$args = array('site'=>get_bloginfo('url'),'width'=>$lacands_opt_widget_fb_reco_width, 'height'=>$lacands_opt_widget_fb_reco_height, 'header'=>$lacands_opt_widget_fb_reco_header, 'colorscheme'=>$lacands_opt_widget_fb_reco_color, 'font'=>$lacands_opt_widget_fb_reco_font, 'border_color'=>$lacands_opt_widget_fb_reco_border);
	$args_data = http_build_query($args, '', '&amp;');
	$html  = '<div style="margin:'.$lacands_opt_widget_fb_reco_margin_top.'px '.$lacands_opt_widget_fb_reco_margin_right.'px '.$lacands_opt_widget_fb_reco_margin_bottom.'px '.$lacands_opt_widget_fb_reco_margin_left.'px">';
	if($lacands_opt_widget_fb_reco_title) {
		$html .= '<h3 class="widget-title">'.$lacands_opt_widget_fb_reco_title.'</h3>';
	}
	$html .= '<iframe src="http://www.facebook.com/plugins/recommendations.php?'.$args_data.'" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:300px; height:300px;" allowTransparency="true"></iframe>';
	$html .= '</div>';
	echo $html;
	return;
}


function lacands_fb_recommendations_settings($data) {
	$fb_reco_options = array('lacands-html-widget-fb-reco-width'=>'300', 'lacands-html-widget-fb-reco-height'=>'300', 'lacands-html-widget-fb-reco-header'=>'true', 'lacands-html-widget-fb-reco-color'=>'light', 'lacands-html-widget-fb-reco-font'=>'arial', 'lacands-html-widget-fb-reco-border'=>'#AAAAAA', 'lacands-html-widget-fb-reco-margin-top'=>'10', 'lacands-html-widget-fb-reco-margin-right'=>'0', 'lacands-html-widget-fb-reco-margin-bottom'=>'10', 'lacands-html-widget-fb-reco-margin-left'=>'0', 'lacands-html-widget-fb-reco-title'=>'');
	foreach($fb_reco_options as $key=>$val) {
		if(!get_option($key)) {
			add_option($key, $val);
		}
	}
	global $lacands_opt_widget_fb_reco_width, $lacands_opt_widget_fb_reco_height, $lacands_opt_widget_fb_reco_header, $lacands_opt_widget_fb_reco_color, $lacands_opt_widget_fb_reco_font, $lacands_opt_widget_fb_reco_border;
	global $lacands_opt_widget_fb_reco_margin_top, $lacands_opt_widget_fb_reco_margin_right, $lacands_opt_widget_fb_reco_margin_bottom, $lacands_opt_widget_fb_reco_margin_left;
	global $lacands_opt_widget_fb_reco_title;
	lacands_readOptionsValuesFromWPDatabase();
	foreach($fb_reco_options as $key=>$val) {
		if($key != 'lacands-html-widget-fb-reco-title') {
			if(!empty($_POST[$key])) {
				update_option($key, (string)$_POST[$key]);
			}
		} else {
			if(!empty($_POST[$key])) {
				update_option($key, (string)$_POST[$key]);
			} else {
				update_option($key, '');
			}
		}
	}
	require("la-click-and-share-fb-recommendation.html");
}


function lacands_main() {
	lacands_init();
	$dims = array('width' => 250, 'height' => 300);
	$widget_ops = array('description' => LACANDS_FB_RECOMMENDATIONS_NAME);
	register_activation_hook( __FILE__, 'lacands_activate' );
	if ( is_admin() ) {
		wp_enqueue_style('thickbox');
		wp_enqueue_script('jquery');
		wp_enqueue_script('thickbox');
		wp_register_script('postmessagejs', LACANDS_PLUGIN_URL .'jquery.ba-postmessage.min.js');
		wp_enqueue_script('postmessagejs');
		wp_register_script('lacandsjs', LACANDS_PLUGIN_URL.'la-click-and-share.js');
		wp_enqueue_script ('lacandsjs');
		wp_register_style ('lacandsnetworkpubcss', LACANDS_PLUGIN_URL.'la-click-and-share-networkpub.css');
		wp_enqueue_style  ('lacandsnetworkpubcss');
		add_action ( 'admin_menu',  'lacands_wp_admin');
		add_action ( 'admin_menu',  'lacands_pages');
		add_action ( 'admin_notices', 'lacands_warning');
		add_action ( 'init', 'lacandsnw_networkpub_ajax');
		add_action ( 'activate_{$plugin}', 'lacandsnw_pushpresscheck');
		add_action ( 'activated_plugin', 'lacandsnw_pushpresscheck');
		wp_register_widget_control(LACANDS_FB_RECOMMENDATIONS_ID, LACANDS_FB_RECOMMENDATIONS_NAME, 'lacands_fb_recommendations_settings', $dims, $widget_ops);
	}
	add_filter ( 'the_content', 'lacands_wp_filter_post_content');
	wp_register_sidebar_widget(LACANDS_FB_RECOMMENDATIONS_ID, LACANDS_FB_RECOMMENDATIONS_NAME, 'lacands_fb_recommendations', $widget_ops);
	register_deactivation_hook( __FILE__, 'lacands_deactivate' );
}


add_action ( 'init', 								'lacandsnw_networkpub_remove');
add_action ( '{$new_status}_{$post->post_type}',	'lacandsnw_networkping');
add_action ( 'publish_post',                    	'lacandsnw_networkping');
add_action ( 'future_to_publish', 					'lacandsnw_networkping');
add_action ( '{$new_status}_{$post->post_type}', 	'lacandsnw_post');
add_action ( 'publish_post', 						'lacandsnw_post');
add_action ( 'future_to_publish', 					'lacandsnw_post');
add_action ( '{$new_status}_{$post->post_type}', 	'lacandsnw_convert');
add_action ( 'publish_post', 						'lacandsnw_convert');
add_action ( 'future_to_publish', 					'lacandsnw_convert');

lacands_main();

?>