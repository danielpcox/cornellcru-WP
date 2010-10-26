=== Tweet This ===
Contributors: richardxthripp
Plugin URI: http://richardxthripp.thripp.com/tweet-this/
Author URI: http://richardxthripp.thripp.com/
Donate Link: http://richardxthripp.thripp.com/donate/
Tags: admin, api, automatic, automatically, bebo, bit.ly, bookmarking, buzz, community, connect, delicious, digg, diigo, facebook, google, google buzz, integrate, integration, is.gd, link, linkedin, links, microblogging, mixx, myspace, networking, notify, oauth, page, pages, ping, plugin, plurk, post, posts, reddit, redirect, scheduling, sharing, shortener, sidebar, slashdot, squidoo, snipurl, social, social bookmarking, social media, stumbleupon, su.pr, technorati, th8.us, tinyurl, tweet, tweeting, tweets, twitter, update, updates, urls, widget, widgets, yahoo, yahoo buzz
Requires at least: 2.7
Tested up to: 3.0.1
Stable tag: 1.8

Popular Twitter plugin inserts "Tweet This" links so your readers can share
posts with one click. Automatically tweets new posts via OAuth.

== Description ==

Popular Twitter plugin inserts "Tweet This" links so your readers can share
posts with one click. Can automatically tweet new posts via OAuth.
Allows you to publish and schedule tweets from a new "Write Tweet" page.
Supports 10 URL shorteners including Bit.ly, Su.pr, and TinyURL.
Includes options for 20 social networks including Facebook, Bebo, and MySpace.
Includes the Wickett Twitter Widget for your sidebar and many other options.

`Tweet This 1.8 is a major release with many new features and improvements.
* Added Bebo, Diigo, FriendFeed, Gmail, Google Buzz, LinkedIn, Mixx, Slashdot,
  Squidoo, and Technorati to the Extended Services, bringing the total to 20.
* Added "Public Display Order" field to the Extended Services section, so you
  can change the order in which the services are displayed.
* Updated the auto-tweet options on the Write Post screen to use add_meta_box()
  so you can move the box around. Changed the default position from "advanced"
  to "side" and changed the text input to a textarea.
* You can now use [AUTHOR], [CATEGORY], [DATE], [TIME], [EXCERPT], and
  [BLOG_TITLE] shortcodes in Twitter and Extended Services text fields.
* Scheduled tweets can now be deleted from the Write Tweet page.
* The icons directory has been organized into directories by language and
  service. Your options and icon display will be updated automatically.
  However, if you want the icons to continue to be accessible at the old URLs
  (i.e. for Feedburner email newsletter readers), please merge the new icons
  directory with the old directory instead of overwriting it.
* Added contrast and brightened some icons, and improved transparency.
* Added [tweet_this] shortcode which you can use in any post or page to display
  the Tweet This div including all links. You will have to add a custom field
  titled tweet_this_hide with the value of "true" to stop duplicate insertion.
* Added three buttons to the save section of the options page: "Reset All
  Options Except Keys," "Flush URL Cache," and "Completely Uninstall Tweet
  This," which deletes all plugin options, custom fields, and database tables.
* Tweet This links now have the target="_blank" and rel="nofollow" tags added
  by default. This can be turned off in the advanced options.
* The "Don't shorten URLs under 30 characters" advanced option is now "Use full
  permalinks unless Tweet/Plurk Text exceeds 140 characters" and it's behavior
  has changed accordingly. This also applies to automatic tweets.
* Replaced tt-twitter2.png with the new official Twitter favicon.
* Added a JavaScript clock to the Write Tweet page based on the server's time.
* Added "button-primary" class to the save and import buttons in the options
  and the tweet button on the Write Tweet page, so the buttons are blue now.
* Increased font size on /icons/tt-su-big2.png from 7 to 9 and moved arrow up.
* Increased the add_action priority on tweet_this_css, tt_shortlink_wp_head,
  and tt_html_comment to 9 so they have priority over other plugins.
* Updated the icon selection and extended services section in the options to
  display properly at 1024x768 resolution on WordPress 3.0.
* Changed default CSS to include a 2-pixel margin on the left side of icons.
* Added @package and @since PHPdoc tags to Tweet This functions and classes.
* The number of options modified and the number of cached URLs flushed is now
  displayed after saving the options.
* You can now rename the /tweet-this/ directory and tweet-this.php file.
* Bugfix: Selecting "Share Links" for Twitter did not work at all in 1.7.7.
* Bugfix: get_tweet_this_url() now wraps URLs in urlencode() for Plurk to
  prevent http:// from becoming http:. Plurk has been completely unusable in
  all previous Tweet This versions for some time.
* Bugfix: Fixed Yahoo Buzz links which did not work in all previous versions.
* Bugfix: URLs containing a subdirectory with an underscore followed by another
  subdirectory would be shortened incorrectly on the Write Tweet page. Fixed.
* Bugfix: Each instance of the Twitter "Editable text box" now uses the post ID
  in HTML element IDs and JavaScript functions, to prevent invalid HTML and
  allow multiple text boxes to be displayed on the same page without conflict.
* Bugfix: get_tt_shortlink($post_id) is no longer affected by the option "Use
  'www.' instead of 'http://' in shortened URLs." It always uses http:// now.
* Bugfix: In 1.7.4 - 1.7.7, I accidently included the line
  "remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);"
  which removed the canonical link and shortlink from wp_head on all posts.`

Tweeting a post on Twitter takes up a lot of space, because URLs quickly eat up
your 140 characters. While your readers might copy the permalink, go to Bit.ly
or TinyURL, shorten and copy the new URL, go to Twitter, and paste it into the
status box, this plugin merges all that into one step.

This plugin makes short URLs like http:/example.com/?p=1234, then displays a
link to Twitter for each post, with an optional icon (20 choices). This is
done automatically for each post as needed. You can choose a URL shortener
including Adjix.com, B2l.me, Bit.ly, Is.gd, Metamark.net, SnipURL.com, Su.pr,
Th8.us, TinyURL.com, and Tweetburner.com. Each shortened URL is cached as a
custom field in the postmeta table to keep load times fast. The cached records
are updated or deleted as needed when you edit a post's permalink, delete a
post, change your site's permalink structure, or change URL services. In WP 3.0
or later, Tweet This hooks the short URLs into the get_shortlink filter.

This plugin can also tweet new blog posts automatically, if you provide
your Twitter credentials in the options. Then a "Send to Twitter" checkbox
appears when writing a new post, along with a text box so you can change the
tweet text for that specific blog post. As of 1.7, OAuth is used.

Unlike Tweetmeme, ShareThis, and other Twitter plugins, Tweet This inserts
links without JavaScript, iFrames, or third-party dependencies. An example:
http://twitter.com/home/?status=Example+Post+http://example.com/?p=1234

Copyright 2008 - 2010  Richard X. Thripp  (email: richardxthripp@thripp.com)
Released under Version 2 of the GNU General Public License as published by
the Free Software Foundation, or, at your option, any later version.

== Installation ==

Before you begin, please make sure your server has PHP 5 and Curl enabled.
While you can use Tweet This on PHP 4, all OAuth functions require PHP 5.
Tweet This requires WordPress 1.5 minimum, with the following exceptions:

1. Importing exported options requires WP 2.0.
2. Automatic tweeting requires WP 2.7.
3. The Twitter Updates widget requires WP 2.8.
4. Adding short URLs to the get_shortlink filter requires WP 3.0.

If you are installing Tweet This for the first time, follow these steps:

1. Upload the `tweet-this` folder to `/wp-content/plugins/`.
2. If you're using WordPress MU and want this plugin active for all blogs,
	move `tweet-this.php` to `/wp-content/mu-plugins/` at this point.
3. Else, activate the plugin through the 'Plugins' menu in WordPress.
4. Tweet This icons should automatically appear on every post and page!
	Go to Settings > Tweet This to change settings and set up auto-tweets.
5. Optionally, delete readme.txt and the screenshots folder to save space.

If you are upgrading from Tweet This 1.7.7 or older, follow these steps:

1. Deactivate Tweet This.
2. Back up your /tweet-this/tt-config.php file if you are using it.
3. Delete the /tweet-this/ folder.
4. Upload the new /tweet-this/ folder to your plugins folder.
5. Restore your /tweet-this/tt-config.php file if you are using it.
6. Activate Tweet This 1.8.

== Frequently Asked Questions ==

= What are the minimum requirements for Tweet This? =

WordPress 2.7, PHP 5.0.0, and Curl. Read the Installation section for details.

= How do I make Tweet This show on posts, but NOT on pages? =

Go to Settings > Tweet This, click "Advanced Options,"
check "Hide Tweet This on pages," and click "Save Options."

= Does Tweet This provide a widget of my latest tweets? =

Yes, on WordPress 2.8 or later a Twitter Updates widget is available,
which functions like the Wickett Twitter Widget plugin by Automattic Inc.

= How does OAuth automatic tweeting work? =

Register for Twitter OAuth and enter your application keys in Settings > Tweet
This > Automatic Tweeting. There are also instructions there. Then, look for
the box titled Tweet This on the Write Post screen and check "Send to Twitter."
If you want this enabled by default, check "'Send to Twitter' defaults to
checked on unpublished posts" in Settings > Tweet This > Automatic Tweeting.

= Why is automatic tweeting not working? =

Make sure you have entered your OAuth keys correctly. Click "Test Twitter
OAuth" in Settings > Tweet This > Automatic Tweeting to verify. On your Twitter
application settings, "Default Access type" must be set to "Read & Write."
Also, make sure you have checked "Send to Twitter" on the Write Post screen.

= How does the "Write Tweet" page work? =

After you enter your OAuth keys, a new submenu titled "Write Tweet" will appear
under the Pages menu. From this page, you can not only write tweets, but
schedule future tweets to a MySQL table `wp_tweet_this_scheduled`. URLs
entered here are automatically shortened if the tweet text exceeds 140
characters. A preview function is included, and you can see a list of up to 100
of your latest scheduled tweets.

= How do scheduled tweets work? =

The schedule tweet function on the Write Tweet page adds a row to the
`wp_tweet_this_scheduled` MySQL table with the scheduled date, the tweet text,
and the status set to "future". When the first tweet is scheduled, an option is
added to the `wp_options` table titled `tweet_this_last_cron` with the value of
(time() - 86400). On every page load, (time() - 600) is compared to this
option; if it is greater, it has been more than ten minutes since the last
scheduled tweets were processed, so `tweet_this_init` attempts to publish all
the tweets with a status of "future" and a scheduled date in the past. If this
succeeds, the tweet's status is set to "publish"; if it fails, it is set to
"fail". Scheduled tweeting requires that someone actually visits your blog
after the scheduled date. The scheduled tweet is not triggered until a page is
loaded. Automatic tweets on scheduled posts are much simpler: the function to
publish the tweet is simply attached to the `publish_post` hook.

= What's the difference between Web Links and Share Links? =

Web links are what Tweet This has always used. They take the form
http://twitter.com/home/?status=Your+message and will display "via web" when
tweeted. Share links are like http://bit.ly/asEAtj (long URL) and use the
same API as the official Tweet button, but as a regular link instead of a
JavaScript popup. They use Twitter's official URL shortener, T.co, allow you to
specify up to two related users to be suggested after the tweet is made, and
will display "via Tweet Button" when tweeted.

= How does the cache work? =

Cached short URLs are saved to the postmeta table when a visitor views posts.
For any future pageloads, those URLs are loaded, instead of pinging the Th8.us
API (or Bit.ly, TinyURL, etc.). As long as the post's permalink doesn't change,
the short URL from the third-party service doesn't change.

The cache is invalidated by setting the existing short URLs in the postmeta
table to "getnew" as needed. By reusing the old fields instead of replacing
them, I don't bump up the `meta_id` counter needlessly. When the next person
visits that post, the `get_tweet_this_short_url` function in Tweet This
sees this and gets a new short URL.

What triggers a cached URL as invalid? When you save a post (including editing
and publishing), the cache is invalidated in case you changed the permalink.
Secondly, when you change URL services under Settings > Tweet This or change
permalink structures under Options > Permalinks, all the cached URLs are set
to "getnew". Finally, if you change "Use 'www.' instead of 'http://' in
shortened URLs" or "Don't shorten URLs under 30 characters," or import
new settings, the cache is invalidated. If you move your blog to a different
directory or domain name, click the "Flush URL Cache" button in the options.

When you deactivate the plugin, all the cached URLs are deleted.

= How does importing and exporting options work? =

In the options menu, there is a section titled "Import / Export Options." This
is as simple as can be: the export is a raw dump of the `tweet_this_settings`
row from the `wp_options` table, and to import, you just paste that dump in the
import text area. Click "Import Options," and your current options will be
replaced. Your OAuth keys are included, so don't share it with anyone.

= How does setting a custom short URL service work? =

You enter the API's path with [LONGURL] as the long URL. For TinyURL,
for example, you would enter "http://tinyurl.com/api-create.php?url=[LONGURL]".
Then Tweet This uses that service for all its short URLs. The API must accept
HTTP GET requests (not POST), and it must output a plain-text short URL
(no HTML, XML, or arrays) with the http prefix.

= How does the editable Tweet This text box work? =

One of the options for the Twitter icon in the Tweet This Options is
"Editable text box." This gives your readers a text box with character counter
so they can change the tweet on your site before going to Twitter. When you
click the submit button, an interstitial is loaded which parses and sends the
new tweet text, forwarding the reader to Twitter. Then it is the same as the
regular options: the reader can edit the tweet further on Twitter, or submit.

= How does the tt-config.php file work? =

Tweet This ships with the file named as `tt-config-sample.php`. This way,
if you rename it to tt-config.php to use it, you can still upload future
versions of Tweet This right over the old directory, because your customized
tt-config.php file will not be overwritten. Once you rename it, there are
several options you can set in it that are too advanced or cannot be included
in the regular options menu.

= Can I use the Tweet This functions in my theme? =

Yes. Within the loop, these functions are available:

`tweet_this_manual() : Echoes all the Tweet This links and disables regular
output. Useful for moving the Tweet This links above each post. If you want to
insert Tweet This below where it is normally inserted, you must add the line
"remove_filter('the_content', 'insert_tweet_this');" before this function.

tweet_this($service, $tweet_text, $link_text, $title_text, $icon_file,
$a_class, $img_class, $img_alt) : Echoes a Tweet This link. This is only
useful if you disable automatic insertion in the settings.  You can leave
the arguments blank like '' to use your settings from the options page.
These values are permitted for the $services argument: 'twitter', 'plurk',
'buzz', 'delicious', 'digg', 'facebook', 'myspace', 'ping', 'reddit', 'su'.
The $icon_file argument is for the filename of an image from the
/tweet-this/icons/ folder. Example: tweet_this('twitter',
'@richardxthripp [TITLE] [URL]', '[BLANK]', 'Share on Twitter [[URL]]',
'de/tt-twitter-big3-de.png', 'tweet-this', 'tt-image', 'Post to Twitter').
$icon_file can be set to "noicon" for a text-only link.

tweet_this_url($tweet_text, $service) : Echoes the Tweet This URL, which is
like http://twitter.com/home/?status=Tweet+This+http://37258.th8.us by
default. Optional tweet_text argument overrides "Tweet Text" from the
options menu. $service can be 'twitter' or 'plurk', or omitted for Twitter.
Sample usage: tweet_this_url('@richardxthripp [TITLE] [URL]', 'twitter').

tt_shortlink() : Just echoes the short URL for the post (Local, Th8.us,
TinyURL, etc.), cached if possible. Creates the short URL on-the-fly if needed.

tweet_this_trim_title() : URL-encodes get_the_title(), truncates it at the
nearest word if it's overly long, and echoes.

tt_option($key) : like get_option(), but specifically for Tweet This
settings. Useful for retrieving settings from the database.
Example: tt_option('tt_url_service'). Does not echo.`

You can prefix these functions with `get_` to return the
data without echoing, for further manipulation by PHP.

= How much memory does Tweet This use? =

Tweet This is nearly 5000 lines of PHP and stores over 120 options in a
serialized array in the wp_options table. A typical Tweet This installation
uses about 1MB of RAM per WordPress instance. Even if you are not using Tweet
Tweet, you should use a caching plugin such as W3 Total Cache because WordPress
is a resource hog.

= Can I disable Tweet This on a specific post or page? =

Yes: add a custom field titled `tweet_this_hide` with the value of "true".

= Can I just use the Tweet This functions without it adding icons to my blog? =

Sure! Activate the plugin, go to Settings > Tweet This,
uncheck "Insert Tweet This," and click "Save Options."

= If I change URL services, will the old URLs continue to work? =

Yes. The short URLs are on third-party servers (Bit.ly, Su.pr, TinyURL, etc.),
and they should never delete them. Local URLs are built into WordPress and will
continue to work.

= Does Tweet This let me write new tweets from my dashboard? =

Yes: from the "Write Tweet" page under the "Pages" menu.

= Can Tweet This support many authors and their Twitter accounts on one blog? =

No. Automatic tweeting only works with one account per blog.

= Can I use variables such as [AUTHOR], [CATEGORY], [DATE], and [TIME]? =

Yes: a complete list is displayed in the options below the Twitter icons.

= Can I set Tweet This to fetch short URLs on demand, instead of in advance? =

No, but you can use Share Links which use Twitter's official URL shortener.

= Does Tweet This provide click stats? =

No, but you can use Bit.ly and enter your API key to get stats from Bit.ly.

= Can I change the order of the extended services? =

Yes: at the bottom of the Extended Services section in the options, there is a
comma delimited field titled "Public Display Order" to do just that.

= Can I use short URLs from an external plugin such as YOURLS or Pretty Link? =

No.

= Can I change the tweet text on a post-by-post basis? =

No.

= Can I use full-length URLs instead of URLs that end with "/?p=1234"? =

Yes: in the Advanced Options section in the options, check the box titled
"Use full permalinks unless Tweet/Plurk Text exceeds 140 characters."

== Changelog ==

`1.8: 2010-10-02: Major release with many new features and improvements.
* Added Bebo, Diigo, FriendFeed, Gmail, Google Buzz, LinkedIn, Mixx, Slashdot,
  Squidoo, and Technorati to the Extended Services, bringing the total to 20.
* Added "Public Display Order" field to the Extended Services section, so you
  can change the order in which the services are displayed.
* Updated the auto-tweet options on the Write Post screen to use add_meta_box()
  so you can move the box around. Changed the default position from "advanced"
  to "side" and changed the text input to a textarea.
* You can now use [AUTHOR], [CATEGORY], [DATE], [TIME], [EXCERPT], and
  [BLOG_TITLE] shortcodes in Twitter and Extended Services text fields.
* Scheduled tweets can now be deleted from the Write Tweet page.
* The icons directory has been organized into directories by language and
  service. Your options and icon display will be updated automatically.
  However, if you want the icons to continue to be accessible at the old URLs
  (i.e. for Feedburner email newsletter readers), please merge the new icons
  directory with the old directory instead of overwriting it.
* Added contrast and brightened some icons, and improved transparency.
* Added [tweet_this] shortcode which you can use in any post or page to display
  the Tweet This div including all links. You will have to add a custom field
  titled tweet_this_hide with the value of "true" to stop duplicate insertion.
* Added three buttons to the save section of the options page: "Reset All
  Options Except Keys," "Flush URL Cache," and "Completely Uninstall Tweet
  This," which deletes all plugin options, custom fields, and database tables.
* Tweet This links now have the target="_blank" and rel="nofollow" tags added
  by default. This can be turned off in the advanced options.
* The "Don't shorten URLs under 30 characters" advanced option is now "Use full
  permalinks unless Tweet/Plurk Text exceeds 140 characters" and it's behavior
  has changed accordingly. This also applies to automatic tweets.
* Replaced tt-twitter2.png with the new official Twitter favicon.
* Added a JavaScript clock to the Write Tweet page based on the server's time.
* Added "button-primary" class to the save and import buttons in the options
  and the tweet button on the Write Tweet page, so the buttons are blue now.
* Increased font size on /icons/tt-su-big2.png from 7 to 9 and moved arrow up.
* Increased the add_action priority on tweet_this_css, tt_shortlink_wp_head,
  and tt_html_comment to 9 so they have priority over other plugins.
* Updated the icon selection and extended services section in the options to
  display properly at 1024x768 resolution on WordPress 3.0.
* Changed default CSS to include a 2-pixel margin on the left side of icons.
* Added @package and @since PHPdoc tags to Tweet This functions and classes.
* The number of options modified and the number of cached URLs flushed is now
  displayed after saving the options.
* You can now rename the /tweet-this/ directory and tweet-this.php file.
* Bugfix: Selecting "Share Links" for Twitter did not work at all in 1.7.7.
* Bugfix: get_tweet_this_url() now wraps URLs in urlencode() for Plurk to
  prevent http:// from becoming http:. Plurk has been completely unusable in
  all previous Tweet This versions for some time.
* Bugfix: Fixed Yahoo Buzz links which did not work in all previous versions.
* Bugfix: URLs containing a subdirectory with an underscore followed by another
  subdirectory would be shortened incorrectly on the Write Tweet page. Fixed.
* Bugfix: Each instance of the Twitter "Editable text box" now uses the post ID
  in HTML element IDs and JavaScript functions, to prevent invalid HTML and
  allow multiple text boxes to be displayed on the same page without conflict.
* Bugfix: get_tt_shortlink($post_id) is no longer affected by the option "Use
  'www.' instead of 'http://' in shortened URLs." It always uses http:// now.
* Bugfix: In 1.7.4 - 1.7.7, I accidently included the line
  "remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);"
  which removed the canonical link and shortlink from wp_head on all posts.`

Older versions: http://richardxthripp.thripp.com/tweet-this-version-history/

== Other Notes ==

This section contains the acknowledgements and my to-do list.

= Acknowledgements =

Tweet This borrows code from these WordPress plugins: Twitter Tools by
Alex King, Wickett Twitter Widget by Automatic Inc., and WP to Twitter by Joe
Dolson. Tweet This uses these external libraries: JSON-PHP by Michal Migurski,
OAuth.php by Andy Smith, twitteroauth.php by Abraham Williams, Ext-Conv-Links
by Muhammad Arfeen, LinkifyURL by Jeff Roberson, and jQuery by John Resig.
Sascha Assbach created all the icons saying "Tweet This" or "Twitter Das," and
I used his icons as templates for the extended services. Graham Smith created
tt-twitter4.png and tt-twitter-micro2.png.

= Tweet This To-Do List [33 Items] =

`* Include caching controls for WP Super Cache and W3 Total Cache.
* Add the official Twitter widget (JavaScript).
* Integrate official Twitter JavaScript button and let user set language.
* Option to use custom icons for Twitter and Extended Services.
* Let user specify TT_PREFIX, TT_SUFFIX, and TT_SEPARATOR in the options
  instead of in tt-config.php.
* Allow user to set length of the URLs a custom URL service generates,
  or better yet, check them on a per-URL basis. Currently, all URLs generated
  by a custom URL service are assumed to be 26 characters.
* Option to place links at top of post, bottom of post, or both.
* Option to set max length for URLs and tweet text to a value lower than 140
  characters to allow for retweets.
* Add check boxes to display or hide Tweet This on posts, pages, the home page,
  RSS feeds, archives, search, excerpts, tag listings, and category listings.
* Add support for the plugins YOURLS, Pretty Link, and Twitter Friendly Links.
* Option to have Tweet This get it's short URLs from wp_get_shortlink() if
  available.
* Store permalink MD5 hashes in wp_postmeta and update short URLs only if the
  current hash does not match, eliminating the need for complicated rules.
* Add functionality to archive the user's tweets to a new database table
  (Twitter allows us to request the past 3200 tweets).
* Option to add Follow Author button to each author's posts based on their
  profile's Twitter username field.
* Option to add custom query strings to permalinks before they are sent to a
  URL shortener, primarily for Google Analytics campaign tracking.
* Support multiple authors with different Twitter accounts for auto-tweeting
  and on the Write Tweet page, with option to multicast to a centralized
  Twitter account.
* Allow site administrator to tweet to any author's account if that author has
  supplied Twitter OAuth keys. The UI for this will be a drop-down list on the
  Write Tweet page and in the Tweet This section on new posts. If you have
  multiple Twitter accounts, you will be able to create dummy WordPress
  accounts with your OAuth keys specified in the WordPress account's profile to
  add them to the drop-down list of Twitter accounts to choose from.
* Make Import / Export Options into a download / upload file option instead of
  exporting / importing plain text.
* Add option to only request short URLs when a Tweet This link is actually
  clicked instead of requesting them in advance.
* Add support for the WP_Http class and use it instead of Curl if available.
* Ability to set link and title text for all Extended Services at once.
* Option to set custom HTML "target" attribute on links instead of only
  providing the option of "_blank", and same for "rel" with "nofollow".
* Create a framework for child plugins (modules) and move all extended
  services here, enabled by default. This will allow users with limited server
  resources to disable advanced features.
* Integrate the "Referrer Detector" plugin (Twitter options only) and the
  "Tweetmeme Button" plugin into Tweet This.
* Integrate with Alex King's ShareThis by putting Tweet This links on the same
  line as ShareThis or within the pop-up menu, and replace the ShareThis
  Twitter function with Tweet This.
* Option to cache all short URLs at once (like askapache.com’s Crazy Cache for
  blog pages).
* Add WordPress Multi-User options: set / reset options for all blogs, disable
  certain features.
* Support "WPTouch iPhone Theme" plugin and optimize links for mobile browsers.
* Option to use Custom URL service with HTTP POST options, not just HTTP GET.
* Ability to exclude posts with certain categories, tags, or authors from being
  auto-tweeted or having the Tweet This links displayed.
* Ability to set custom tweet text or short URLs on posts or pages to override
  the defaults.
* Option to create daily, weekly, or per-tweet digests of your tweets as blog
  posts like Alex King's Twitter Tools.
* Implement Twitter's @Anywhere API and let commenters sign in with Twitter.`

== Screenshots ==

1. Tweet This options page: all sections closed.
2. Tweet This options page: all sections opened.
3. The Write Tweet page, having just published a tweet.
4. A post with Tweet This links; Twitter Web API and Share API.
5. Publishing a tweet alongside a new post.
6. The Twitter Updates widget included with Tweet This.

== Upgrade Notice ==

= 1.8 =
Major release with many new features and improvements.