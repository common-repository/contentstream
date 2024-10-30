=== ContentStream === 

Contributors: cfetechnology
Plugin Name: ContentStream
Plugin URI: https://www.cfemedia.com/technology/contentstream
Tags: ContentStream, Custom Post Type, Automated Content Retrieval, wp, php, cron
Author: CFE Media and Technology
Author URI: https://www.cfemedia.com/
Requires at least: 4.9.7
Tested up to: 4.9.8
Stable tag: 1.0.0
Version: 1.0.0
License: LGPLv3
License URI: http://www.gnu.org/licenses/lgpl-3.0-standalone.html

This plugin lets you quickly and easily retrieve articles from ContentStream for use in your CMS.

== Description ==

The ContentStream plugin is an extension of the product ContentStream, created by CFE Media and Technology, for use in Wordpress to download articles purchased in ContentStream directly into the CMS for immediate use on the user's site(s). The ContentStream plugin is meant for users of ContentStream who have active accounts so they can easily transfer content from ContentStream to their Wordpress site(s). The ContentStream Article Retriever plugin will not give the user access to ContentStream itself or allow users to make purchases within ContentStream.  In order for an user to access ContentStream, they must either contact a sales representative of CFE Media or register at the ContentStream website itself.

Once downloaded and activated this plugin will create a menu item in Wordpress's admin interface called ContentStream which has two submenu items, Articles and Settings, along with a one submenu item for each taxonomy list assoicated with your posts. Articles allows the user to view the articles they have downloaded and edit the info and taxonomy within the articles.  Settings allows the user to adjust the settings of plugin and determine what articles the plugin will download and often the plugin will download. The plugin requires the user to fill out the settings form to properly use the retrieval process. The user must provide their ContentStream login information and subscriber ID as well as telling the plugin how often they would like it to retrieve articles, how many of their searches they wish to use in this process, whether or not they want the article to be displayed using the plugin's builtin template, and whether or not they want the articles the plugin to be removed upon uninstallion. This information is stored within custom tables in the user's database so they only have to fill out the form once. These custom tables are removed upon uninstallation. The form also allows the user to manually download articles based on their settings if the user does not want to wait for the plugin to automatic download articles. Also on the ContentStream Settings page is a table that displays all the actions the plugin takes during the retrieval process so the user can see what exact the plugin is doing and if there are any errors in downloading their articles. 

[ContentStream Terms And Conditions](https://stream.cfetechnology.com/common/js/termsandconditions/views/termsandconditions.html)

== Installation == 

1. Register an account with ContentStream by contacting a sales representative with CFE Media. To request an account, please complete the form linked from this page: https://www.cfemedia.com/technology/contentstream/
2. Once registered, log into ContentStream and create a search to find the articles you wish to purchase
3. When you have finished searching and have selected your articles, activate the web service delivery method on your search on the delivery page
4. Note the subscriber id and search number on the delivery page for later use
5. Now in Wordpress, install and activate the ContentStream plugin from the plugin page, if you are using a multisite instance of Wordpress the plugin page is located under the Network Admin
6. Go to the newly created ContentStream Setting page and complete the form with the corresponding information
7. Save the form and either wait for the plugin to automatically download your articles based on your settings or click the 'Retrieve Articles Now' button to download the articles immediately

== Frequently Asked Questions ==

= What username and password should I use in the settings form? =
A: Use your ContentStream username and password, not your Wordpress credentials. The plugin needs that information to properly validate that the search and articles being requested are being requested by the registered user.

= I don't know my subscriber id or my search number, where do I find them? =
A: You can find your subscriber and search ids in ContentStream. They can be found on the delivery page of the search you wish to retrieve articles from.

= I don't want the plugin to automatically download articles, can I set it up to give myself manual control the downloading process? =
A: Yes, when filling out the form on the 'ContentStream Settings' page select the option 'Never' for question 4 of the retrieval settings and save. This will turn off the automatic retrieval process. Then you can use the 'Retrieve Articles Now' button on the same form to retrieve articles whenever you want.

= What is the custom template and what does it look like? =
A: The custom template is a page template that is included in the plugin itself that can be used to alter the appearance of the article on your site. The custom template is designed to look like the preview that can be found in ContentStream when searching for articles.

= I have downloaded some articles using this plugin and now I want to deactivate the plugin, what will happen to the articles I have downloaded? =
A: Nothing. If you are only deactivating the plugin the articles you have downloaded will appear in the 'Posts' section of Wordpress. If you intend to deactivate and uninstall the plugin, the articles you have downloaded will be removed from Wordpress if you selected 'Yes' for question 2 of the plugin settings on the 'Settings' page. Please make sure you the settings you have saved are what you want before deactivating/uninstalling the plugin.

= I was told I need to add a canonical tag to the articles I download from ContentStream, how do I do that? =
A: No need to worry the plugin does that for you automatically. All articles you download using this plugin will have their canonical tags already added, and if your build of Wordpress has the Yoast SEO plugin installed you will even be able to see the canonical url that will be used.

= I have answered yes to all the appearance questions on the settings form; how else can I get ContentStream articles on other pages of my site? =
A: The ContentStream plugin allows you to control flow of articles and where they go to a point, allowing you display them on your home page, in search results, and even generate a listing page for you. We strive to make it as easy as possible for you to make content appear on your site without affecting your site's other plugins and theme in unexpected and negivate ways. If you are looking to increase the number of pages ContentStream articles are appearing on you will need to involve a php developer and customize aspects of your site to better optimize the use of ContentStream articles. 

== Screenshots ==

1. Where to find your subscriber ID and search number in ContentStream
2. What the setting form and action log look like
3. Viewing the articles downloaded by the plugin
4. Editing an article downloaded by the plugin

== Changelog ==

= 1.0.0 = 
* Initial Release

== Upgrade Notice ==

= 1.0.0 = 
* Initial Release
