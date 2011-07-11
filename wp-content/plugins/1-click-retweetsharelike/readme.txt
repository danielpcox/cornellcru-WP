=== Plugin Name ===

Contributors: LinksAlpha
Tags: like, facebook like, facebook, widget, plugin, twitter, retweet, tweet, images, social plugins, Post, google, admin, social posts, posts, shares, comments, sidebar, likes, page, image, social networks, buttons, counters, social media, social, links, comments, social networks, social, Blogger, Brightkite, Delicious, Diigo, Foursquare, Google Buzz, Plurk, Posterous, Sonico, Tumblr, Typepad, Windows Live, Yahoo, Yahoo Meme, Yammer, Status.net, socialcast, p2, tumblr, gowalla, basecamp, backpack, linkedin share, windows live
Requires at least: 2.0.2
Tested up to: 3.0.4
Stable tag: 4.0.0


== Description ==

Shows following 7 buttons on your blog posts:

1. Facebook Like
1. Facebook Share
1. Twitter button
1. LinkedIn Share
1. Google Buzz
1. Digg
1. StumbleUpon

AND, Facebook Recommendations in the Widgets Sidebar


Demo the plugin at - http://dev30.linksalpha.com/?p=8


Plugin also enables you to `Automatically Publish` or `Self Publish` your `Blog Posts to 30+ Networks`.

1. Facebook Profile/Wall
1. Facebook Pages
1. Facebook Application Page
1. Facebook Events
1. Facebook Groups
1. Twitter
1. LinkedIn
1. MySpace
1. Yammer
1. Yahoo
1. Identi.ca
1. Status.net
1. Google Buzz
1. Socialcast
1. Plurk
1. Sonico
1. Delicious
1. Diigo
1. Foursquare
1. Gowalla
1. Brightkite
1. Wordpress.com Blog
1. Wordpress.org Blog
1. Blogger
1. Tumblr
1. Typepad
1. Posterous
1. Yahoo Meme
1. Basecamp
1. Backpack
1. Windows Live


**Postbox**

* Postbox enables you to self/manually post to your Connected LinksAlpha.com Networks. This gives you more control over the content you want to publish
* To self post from your wordpress blog, click on `Postbox` menu option located under `Posts` in Admin Console 


**List of Features**

* 1-click Retweet
* 1-click Facebook Share
* 1-click Facebook Like
* Displays counts next to the buttons
* Offers consistent UI: aligned buttons and uniform color selection
* Gives visual indication after the blog post is Retweeted/Shared/Liked. See screenshot at http://cdn.linksalpha.com/static/1clickedwidget.png
* View Weekly Stats to track total number of Blog Posts, Tweets, Bitly Clicks, Facebook Comments, Facebook Likes, and Facebook Shares.
* View status of your blog posts to each network - whether the blog post has been published, when it was published, etc. 
* Using advanced features such as categories, automatically post selected blog posts to a subset of networks.


**Benefits**

* Enable your users to retweet, share and like your blog posts to their Twitter followers and Facebook friends.
* Keep your fans, followers, and connections automatically updated on your blog posts.
* Expand your blog reach and save time by letting the plugin publish your blog posts - automatically.


**Manual positioning**

For Manually positioning the 1-click Retweet/Share/Like on your blog you need to do the following two things:

1. Place the following code `<?php lacands_wp_filter_content_widget(); ?>` in index.php file or any other file as you see appropriate in themes folder (...\wordpress\wp-content\themes). Note: if you are using 'default' theme for the Wordpress plugin, then place the above code in single.php 
(...\wordpress\wp-content\themes\default\single.php)

2. On admin page for this plugin ('1-click Retweet/Share/Like'), check the box next to "Disable displaying 1-click Retweet/Share/Like (for Manual Option Only - see readme.txt)" under "1-Click Social Widget Position & Colors" and submit by clicking on 'Save Changes'.


**Admin Options**

* Option to show the buttons at top, bottom, or both top and bottom of the blog post
* Option to set margins for the buttons
* Option to place the buttons manually
* Select pages on which buttons should be visible: home and archive (default: single/home/archive)
* Select Font Styles for Retweet/Share/Like: arial, tahoma, lucida grande, segoe ui, trebuchet ms, verdana
* Select Counter Colors (any color) for Retweet and Facebook Share (For Facebook Like, option not available from Facebook)
* Option to change 'Like' text to 'Recommend'


**Misc**

* For getting support, email us at: post@linksalpha.com
* Note: We encourage you to download the latest version of the plugin as soon as it becomes available - as it may have additional extremely useful features for your blog.


== Installation ==
1. Upload la-click-and-share.zip to '/wp-content/plugins/' directory and unzip it.
2. Activate the Plugin from "Manage Plugins" window


== Frequently Asked Questions ==

= After plugin upgrade it doesn't work? =

Deactivate and then Activate the plugin. If by default it shows as 'activated', then click on 'deactivate' and then click on 'activate'. 

= What if I have more questions? =

Go to http://help.linksalpha.com/1-click-retweet-share-like/faqs for list of FAQs and corresponding answers.

= Question still not answered? =

Email us at post@linksalpha.com


== Screenshots ==
1. Social 1-click Retweet/Share/Like buttons
2. List of supported networks for automatic publishing


== Changelog ==

= 4.0.0 =
* Adds Support for Google Buzz, Digg, and StumbleUpon
* Users will now have to add their LinksAlpha user key, instead of network key

= 3.6.0 =
* Adds Support to self Post content to connected Networks. Submenu option under - Posts - in Admin console
* Added option to show/hide Twitter and Facebook Share counters
* Minor bug fixes

= 3.5.0 =

* Options added for Facebook Like button
* Options added for Twitter button
* Options added for Publishing

= 3.1.0 =

* Plugin now uses official Twitter button instead of ReTweet button from LinksAlpha.com
* Plugin now detects if user has PuSHPress plugin installed. Helps in faster publishing

= 3.0.0 =

* New functionality: ability to Automatically Publish your blog posts to 30 Networks!
