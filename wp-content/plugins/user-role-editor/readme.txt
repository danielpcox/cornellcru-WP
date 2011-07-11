=== User Role Editor ===
Contributors: shinephp
Donate link: http://www.shinephp.com/donate/
Tags: user, role, editor, security, access, permission, capability
Requires at least: 3.0
Tested up to: 3.1
Stable tag: 3.0.1

User Role Editor WordPress plugin makes the role capabilities changing easy. You can change any standard WordPress user role (except administrator).

== Description ==

User Role Editor WordPress plugin makes the role capabilities changing easy. You can change any standard WordPress user role (except administrator) with a few clicks.
Just turn on check boxes of capabilities you wish to add to the selected role and click "Update" button to save your changes. That's done. In case you made some unneccessary change you always have the "Reset" button to restore roles state from the automatically made backup copy.
Add new roles and customize its capabilities according to your needs. Unnecessary self-made role can be deleted if there are no users whome such role is assigned.
Role assigned every new created user by default can be changed too.
To read more about 'User Role Editor' visit this link at <a href="http://www.shinephp.com/user-role-editor-wordpress-plugin/" rel="nofollow">shinephp.com</a>


== Installation ==

Installation procedure:

Attention! Starting from version 2.2 plugin works with WordPress 3.0 and higher only. For earlier WordPress versions use plugin version 2.1.10 from http://downloads.wordpress.org/plugin/user-role-editor.2.1.10.zip
1. Deactivate plugin if you have the previous version installed.
2. Extract "user-role-editor.x.x.x.zip" archive content to the "/wp-content/plugins/user-role-editor" directory.
3. Activate "User Role Editor" plugin via 'Plugins' menu in WordPress admin menu. 
4. Go to the "Settings"-"User Role Editor" menu item and change your WordPress standard roles capabilities according to your needs.

== Frequently Asked Questions ==
- Does it work with WordPress 3.1 in multi-site environment?
Yes, it works with WordPress 3.1 multi-site. By default plugin works for every blog from your multi-site network as for locally installed blog.
To update selected role globally for the Network you should turn on the "Apply to All Sites" checkbox.


== Screenshots ==
1. screenshot-1.png User Role Editor main form
1. screenshot-2.png User Role Editor main form under multi-site environment


== Special Thanks to ==
* [FullThrottle](http://fullthrottledevelopment.com/how-to-hide-the-adminstrator-on-the-wordpress-users-screen) - For the code to hide administrator role at admin backend.
* Marcin - For the code enhancement. This contribution allows to not lose new custom capability if it is added to other than 'Administrator' role.

= Translations =
* Belorussian: [Marsis G.](http://pc.de)
* Brasilian Portuguese: [Rafael Galdencio](http://www.arquiteturailustrada.com.br)
* Chinese: [Yackytsu](http://www.jackytsu.com)
* Dutch: [Rémi Bruggeman](http://www.remisan.be)
* French: [Whiler](http://blogs.wittwer.fr/whiler)
* German: [Peter](http://www.red-socks-reinbek.de)
* Hungarian: [István](http://www.blacksnail.hu)
* Italian: [Talksina](http://www.iadkiller.org), [Alessandro Mariani](http://technodin.org)
* Japanese: [Technolog.jp](http://technolog.jp)
* Persian: [Good Life](http://good-life.ir)
* Polish: [TagSite](http://www.tagsite.eu)
* Russian: [Vladimir Garagulya](http://shinephp.com)
* Spanish: [Dario Ferrer](http://www.darioferrer.com)
* Turkish: [Sadri Ercan](http://www.faydaliweb.com), [Can KAYA](http://www.kartaca.com)

Dear plugin User!
If you wish to help me with this plugin translation I very appreciate it. Please send your language .po and .mo files to vladimir[at-sign]shinephp.com email. Do not forget include you site link in order I can show it with greetings for the translation help at shinephp.com, plugin settings page and in this readme.txt file.
If you have better translation for some phrases, send it to me and it will be taken into consideration. You are welcome!
Share with me new ideas about plugin further development and link to your site will appear here.


== Changelog ==

= 3.0.1 =
* 27.02.2011
* Spanish translation is updated. Thanks to [Dario Ferrer](http://www.darioferrer.com). Other language translation wait for update too. You are welcome :).

= 3.0 =
* 06.02.2011
* Compatibility with WordPress 3.1 Release Candidate 3 and real multi-site feature are provided.
* Role capabilities list are sorted now in the alphabetical order. Easier to find - easier to manage.
* Code fix: allows to not lose new custom capability if it is added to other than 'Administrator' role. Thanks to Marcin for the contribution to the code of this plugin.
* Under multi-site environment:
* 1) URE has additional option 'Apply to All Sites' which allows you to apply updates to the selected role at all sites of your network. If some site has not such role, it will be added. You should know, that this option works for the role update only. All other actions as 'Add' or 'Delete' role still works for the currently selected blog/site only.
* 2) URE plugin settings page is available only to user with network superadministrator rights.

= 2.2.3 =
* 08.11.2010
* It is the security update. Old problem returned after 2.2.2 update and was discovered by saharusa. You can read this [thread](http://wordpress.org/support/topic/plugin-user-role-editor-editor-can-edit-admin).
Only user with Administrator role and superadmin user under multi-site environment have access to the User Role Editor Settings page now.

= 2.2.2 =
* 07.11.2010
* URE plugin Settings page was unavailable for some installations in multi-site environment. It is fixed by changing 'add_users' capability for administrator access to the 'edit_users'.
* Turkish translation is added.

= 2.2.1 =
* 09.10.2010
* Critical bug "Fatal error: Class 'SimplePie' not found in /" is fixed. This is a required update as URE plugin Settings page did not opened in previous version if you have not some of other my plugins installed :).

= 2.2 =
* 08.10.2010
* Technical update for WordPress 3.0 full compatibility. Staff deprecated since WordPress v.3.0 is excluded. If you use earlier WordPress versions, do not update URE plugin to v.2.2 or higher.
* Italian translation update. Thanks to [Alessandro Mariani](http://technodin.org).

= 2.1.10 =
* 21.09.2010
* German translation is updated. Thanks to [Peter](http://www.red-socks-reinbek.de).

= 2.1.9 =
* 17.09.2010
* Persian translation is added.

= 2.1.8 =
* 16.08.2010
* Compatibility issue with other plugins (like Flash Album Gallery), which use capabilities names with spaces inside (non valid JavaScript identifier), is fixed.
* Missed translation slots are added for some new WordPress 3.0 capabilities. Translators (former and new) are welcome to update correspondent language files.
* Brasilian Portuguese translation is added.

= 2.1.7 =
* 07.07.2010
* Chinese translation is added.

= 2.1.6 =
* 06.07.2010
* Dutch translation is added.

= 2.1.5 =
* 18.06.2010
* Hungarian translation is added.

= 2.1.4 =
* 08.05.2010
* Italian translation is added.
* Minor javascript bug (undefined parameter value was sent to the server) is fixed.

= 2.1.3 =
* 27.04.2010
* Japanese translation is updated.

= 2.1.2 =
* 26.04.2010
* Polish translation is added.

= 2.1.1 =
* 19.04.2010
* Form layout changed slightly to fit more long phrases in other languages
* Belorussian translation is added. Thanks to [Marsis G.](http://pc.de/).
* French, Japanese, Russian, Spanish translations are updated.

= 2.1 =
* 17.04.2010
* Two ways of capabilities names presentation are available for the user choice: standard WordPress name like 'edit_pages' and mouse pointer hint 'Edit pages', and vice versa - human readable form 'Edit pages' with mouse hint for WP standard name 'edit-pages'. Human readable form will be available in translated variant after correspondent translation file will be updated.
* Form layout changed slightly to fit more long phrases in other languages
* Russian, Spanish translations are updated.

= 2.0.3 =
* 14.04.2010
* Japanese translation is added. Thanks to [Technolog.jp](http://technolog.jp/).

= 2.0.2 =
* 11.04.2010
* German translation is verified and updated. Thanks to [Peter](http://www.red-socks-reinbek.de).

= 2.0.1 =
* 04.04.2010
* It is the critical update - security issue is fixed. Thanks to [Saharuza](http://wordpress.org/support/profile/2855662) for discover and telling me about it.
User with 'edit_users' permission could still use URL request with special parameters to remove Administrator role from Admin user or delete Admin user record. Check [this thread](http://wordpress.org/support/topic/383935) for more details.

= 2.0 =
* 04.04.2010
* Create New Role feature was added
* Delete self-made not used role feature was added.  You can not delete any WordPress standard role.
* Change default role for new user feature was added
* Administator role and users with Administrator role permission were hidden from "Users" and "Edit User" page. This is done in case of delegation of add_user, edit_user or delete_user capabilities to some role.

= 1.2 =
* 28.03.2010
* User Role Editor plugin menu item is moved to the Users menu
* Roles in the dropdown list are translated
* French translation is added

= 1.1 =
* 24.03.2010
* Critical bug is fixed. If you click 'Reset' button before any changes to the role data saved (that is click Update button) at least one time, you met with all roles data lost problem. Backup data created automatically before the 1st role data update. If no update - no backup. Special checking for that was added.
* German translation is added.
* Spanish translation is added.

= 1.0 =
* 22.03.2010
* 1st release.

== Additional Documentation ==

You can find more information about "User Role Editor" plugin at this page
http://www.shinephp.com/user-role-editor-wordpress-plugin/

I am ready to answer on your questions about plugin usage. Use ShinePHP forum at
http://shinephp.com/community/forum/user-role-editor/
or plugin page comments and site contact form for it please.
